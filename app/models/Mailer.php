<?php

namespace Core;

defined('ROOT') or die("Direct script access denied");

/**
 * Sends mail via configured SMTP (Settings: smtp_host etc.) when available,
 * falling back to PHP's native mail() so outgoing mail works with zero
 * configuration on hosts that already have local sendmail set up.
 */
class Mailer {
    public $error = '';

    public function send(string $toEmail, string $subject, string $htmlBody, string $textBody = ''): bool {
        $this->error = '';

        $fromEmail = (string) (setting('smtp_from_email') ?: setting('admin_email') ?: ('no-reply@' . $this->siteHost()));
        $fromName  = (string) (setting('smtp_from_name') ?: setting('site_name') ?: 'ThunderPHP');

        $smtpHost = trim((string) setting('smtp_host', ''));

        if ($smtpHost !== '') {
            $client = new SmtpClient([
                'host'       => $smtpHost,
                'port'       => (int) setting('smtp_port', 587),
                'encryption' => (string) setting('smtp_encryption', 'tls'),
                'username'   => (string) setting('smtp_username', ''),
                'password'   => (string) setting('smtp_password', ''),
            ]);

            $sent = $client->send($fromEmail, $fromName, $toEmail, $subject, $htmlBody, $textBody);

            if (!$sent) {
                $this->error = $client->error;
            }

            return $sent;
        }

        return $this->sendViaNativeMail($fromEmail, $fromName, $toEmail, $subject, $htmlBody);
    }

    protected function sendViaNativeMail(string $fromEmail, string $fromName, string $toEmail, string $subject, string $htmlBody): bool {
        $headers = implode("\r\n", [
            "From: {$fromName} <{$fromEmail}>",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
        ]);

        $sent = @mail($toEmail, $subject, $htmlBody, $headers);

        if (!$sent) {
            $this->error = "mail() failed to send to {$toEmail}";
        }

        return $sent;
    }

    protected function siteHost(): string {
        $host = parse_url((string) setting('site_url', ''), PHP_URL_HOST);
        return $host ?: 'localhost';
    }
}
