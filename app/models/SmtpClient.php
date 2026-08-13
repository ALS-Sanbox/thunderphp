<?php

namespace Core;

defined('ROOT') or die("Direct script access denied");

/**
 * Minimal, dependency-free SMTP client. Supports implicit TLS (port 465),
 * STARTTLS (port 587), plain (port 25), and AUTH LOGIN.
 */
class SmtpClient {
    public $error = '';

    protected string $host;
    protected int $port;
    protected string $encryption;
    protected string $username;
    protected string $password;
    protected int $timeout;

    /** @var resource|null */
    protected $socket;

    public function __construct(array $config) {
        $this->host       = $config['host'] ?? '';
        $this->port       = (int) ($config['port'] ?? 587);
        $this->encryption = $config['encryption'] ?? 'tls';
        $this->username   = $config['username'] ?? '';
        $this->password   = $config['password'] ?? '';
        $this->timeout    = (int) ($config['timeout'] ?? 10);
    }

    public function send(string $fromEmail, string $fromName, string $toEmail, string $subject, string $htmlBody, string $textBody = ''): bool {
        $this->error = '';

        if (!$this->connect()) {
            return false;
        }

        $localHost = $_SERVER['SERVER_NAME'] ?? 'localhost';

        if (!$this->sendCommand("EHLO {$localHost}", ['250'])) {
            $this->disconnect();
            return false;
        }

        if ($this->encryption === 'tls') {
            if (!$this->sendCommand("STARTTLS", ['220'])) {
                $this->disconnect();
                return false;
            }

            if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->error = "Failed to negotiate TLS with {$this->host}";
                $this->disconnect();
                return false;
            }

            // The SMTP spec requires re-issuing EHLO after STARTTLS.
            if (!$this->sendCommand("EHLO {$localHost}", ['250'])) {
                $this->disconnect();
                return false;
            }
        }

        if ($this->username !== '') {
            if (!$this->sendCommand("AUTH LOGIN", ['334'])
                || !$this->sendCommand(base64_encode($this->username), ['334'])
                || !$this->sendCommand(base64_encode($this->password), ['235'])) {
                $this->disconnect();
                return false;
            }
        }

        if (!$this->sendCommand("MAIL FROM:<{$fromEmail}>", ['250'])
            || !$this->sendCommand("RCPT TO:<{$toEmail}>", ['250', '251'])
            || !$this->sendCommand("DATA", ['354'])) {
            $this->disconnect();
            return false;
        }

        $message = $this->buildMessage($fromEmail, $fromName, $toEmail, $subject, $htmlBody, $textBody);

        if (!$this->writeRaw($message . "\r\n.\r\n")) {
            $this->error = "Failed to send message body to {$this->host}";
            $this->disconnect();
            return false;
        }

        $response = $this->readResponse();
        if (!$this->responseHasCode($response, ['250'])) {
            $this->error = "Server rejected message: {$response}";
            $this->disconnect();
            return false;
        }

        $this->sendCommand("QUIT", ['221']);
        $this->disconnect();

        return true;
    }

    protected function connect(): bool {
        $target = $this->encryption === 'ssl'
            ? "ssl://{$this->host}:{$this->port}"
            : "tcp://{$this->host}:{$this->port}";

        $this->socket = @stream_socket_client($target, $errno, $errstr, $this->timeout);

        if (!$this->socket) {
            $this->error = "Could not connect to {$this->host}:{$this->port} ({$errstr})";
            return false;
        }

        stream_set_timeout($this->socket, $this->timeout);

        $greeting = $this->readResponse();
        if (!$this->responseHasCode($greeting, ['220'])) {
            $this->error = "Unexpected greeting from {$this->host}: {$greeting}";
            $this->disconnect();
            return false;
        }

        return true;
    }

    protected function sendCommand(string $command, array $expectedCodes): bool {
        if (!$this->writeRaw($command . "\r\n")) {
            $this->error = "Failed to send command to {$this->host}";
            return false;
        }

        $response = $this->readResponse();

        if (!$this->responseHasCode($response, $expectedCodes)) {
            $this->error = "Unexpected response to '{$command}': {$response}";
            return false;
        }

        return true;
    }

    protected function writeRaw(string $data): bool {
        return $this->socket ? (fwrite($this->socket, $data) !== false) : false;
    }

    protected function readResponse(): string {
        if (!$this->socket) {
            return '';
        }

        $response = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $response .= $line;

            // Multi-line responses use a hyphen after the code (e.g. "250-");
            // the final line uses a space (e.g. "250 ").
            if (preg_match('/^\d{3} /', $line)) {
                break;
            }
        }

        return $response;
    }

    protected function responseHasCode(string $response, array $expectedCodes): bool {
        foreach ($expectedCodes as $code) {
            if (str_starts_with($response, $code)) {
                return true;
            }
        }

        return false;
    }

    protected function buildMessage(string $fromEmail, string $fromName, string $toEmail, string $subject, string $htmlBody, string $textBody): string {
        $headers = [
            "From: " . $this->encodeHeader($fromName) . " <{$fromEmail}>",
            "To: <{$toEmail}>",
            "Subject: " . $this->encodeHeader($subject),
            "Date: " . date('r'),
            "MIME-Version: 1.0",
        ];

        if ($textBody !== '') {
            $boundary = 'thunderphp-' . bin2hex(random_bytes(8));
            $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

            $body = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
                . $this->dotStuff($textBody) . "\r\n"
                . "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n"
                . $this->dotStuff($htmlBody) . "\r\n"
                . "--{$boundary}--";
        } else {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "Content-Transfer-Encoding: 8bit";
            $body = $this->dotStuff($htmlBody);
        }

        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    protected function dotStuff(string $body): string {
        return preg_replace('/^\./m', '..', $body);
    }

    protected function encodeHeader(string $value): string {
        return preg_match('/[^\x20-\x7E]/', $value)
            ? '=?UTF-8?B?' . base64_encode($value) . '?='
            : $value;
    }

    protected function disconnect(): void {
        if ($this->socket) {
            fclose($this->socket);
            $this->socket = null;
        }
    }
}
