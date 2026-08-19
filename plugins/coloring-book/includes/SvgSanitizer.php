<?php
namespace ColoringBook;

defined('ROOT') or die("Direct script access denied");

/**
 * Sanitizes an untrusted SVG file before it's ever stored on disk. This is
 * the authoritative check - the client-side sanitizer in the
 * SVGColoringWidget library (Coloring-Book-App) protects the browser that
 * loaded a given SVG, but anyone can bypass a browser entirely and POST a
 * file straight to the upload endpoint, so this is what actually decides
 * what's safe to write to uploads/coloring-books/.
 *
 * Deliberately DOM-based (DOMDocument), not regex - regex can't reliably
 * reason about nested/malformed markup, comments splitting a tag apart,
 * or attribute values containing what looks like another attribute.
 */
class SvgSanitizer {
    private const DANGEROUS_TAGS = ['script', 'foreignObject', 'iframe', 'object', 'embed', 'link', 'meta'];
    private const URL_ATTRS = ['href', 'xlink:href', 'src'];

    /** @var string[] Human-readable reasons the last sanitize() call failed. */
    public array $errors = [];

    /**
     * Validates and sanitizes raw SVG file content.
     *
     * @return string|null Sanitized SVG markup, or null if the input isn't
     *                      a well-formed, safe-to-attempt SVG document at all
     *                      (malformed XML, wrong root element, or a DOCTYPE/
     *                      ENTITY declaration - which a legitimate coloring
     *                      page SVG never needs and which is the classic XXE
     *                      attack vector, so it's rejected outright rather
     *                      than trying to sanitize around it).
     */
    public function sanitize(string $svgContent): ?string {
        $this->errors = [];

        if (trim($svgContent) === '') {
            $this->errors[] = 'The file is empty.';
            return null;
        }

        if (strlen($svgContent) > 5 * 1024 * 1024) {
            $this->errors[] = 'The file is larger than the 5MB limit for coloring pages.';
            return null;
        }

        // Reject any DOCTYPE/ENTITY declaration before ever handing this to
        // the XML parser - modern libxml already disables external entity
        // loading by default, but a coloring page SVG has no legitimate
        // reason to declare one at all, so this is rejected outright rather
        // than relying solely on that default.
        if (preg_match('/<!DOCTYPE|<!ENTITY/i', $svgContent)) {
            $this->errors[] = 'SVG files with a DOCTYPE or ENTITY declaration are not allowed.';
            return null;
        }

        $previousErrorSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $doc = new \DOMDocument();
        // LIBXML_NONET: never resolve any external reference over the
        // network, even if something upstream of this check ever allowed a
        // DTD through. No LIBXML_NOENT/LIBXML_DTDLOAD - entities are never
        // expanded at all.
        $loaded = $doc->loadXML($svgContent, LIBXML_NONET);

        $xmlErrors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorSetting);

        if (!$loaded || !empty($xmlErrors)) {
            $this->errors[] = 'The file is not well-formed XML.';
            return null;
        }

        $root = $doc->documentElement;
        if ($root === null || strtolower($root->localName) !== 'svg') {
            $this->errors[] = 'The file is not an SVG (its root element is not <svg>).';
            return null;
        }

        $this->stripDangerousElements($doc);
        $this->stripDangerousAttributes($doc);

        return $doc->saveXML($root);
    }

    private function stripDangerousElements(\DOMDocument $doc): void {
        $xpath = new \DOMXPath($doc);

        foreach (self::DANGEROUS_TAGS as $tag) {
            // XPath's local-name() matches by the element's local name
            // regardless of namespace prefix - deliberately not
            // getElementsByTagName(), which (being the non-namespace-aware
            // DOM1 method) matches against the literal qualified tagName as
            // written, so an attacker prefixing the tag under a declared
            // namespace (<svg:script>, <x:script>, ...) could dodge it.
            $nodes = $xpath->query("//*[local-name()='{$tag}']");
            $toRemove = [];
            foreach ($nodes as $node) {
                $toRemove[] = $node;
            }
            foreach ($toRemove as $node) {
                $node->parentNode?->removeChild($node);
            }
        }
    }

    private function stripDangerousAttributes(\DOMDocument $doc): void {
        $xpath = new \DOMXPath($doc);
        // //* selects every element in the document regardless of
        // namespace.
        $allElements = $xpath->query('//*');

        foreach ($allElements as $element) {
            if (!($element instanceof \DOMElement)) continue;

            $toRemove = [];
            foreach ($element->attributes as $attr) {
                $name = strtolower($attr->nodeName);
                $value = $attr->nodeValue ?? '';

                // Every event-handler attribute.
                if (str_starts_with($name, 'on')) {
                    $toRemove[] = $attr->nodeName;
                    continue;
                }

                // href/xlink:href/src pointing at a dangerous scheme.
                // Internal #fragment references are left alone -
                // gradients/patterns/clip-paths depend on those.
                if (in_array($name, self::URL_ATTRS, true) && $this->isDangerousUrl($value)) {
                    $toRemove[] = $attr->nodeName;
                    continue;
                }

                // style attribute carrying a javascript: URL or the old
                // IE-only expression() CSS attack.
                if ($name === 'style' && preg_match('/(javascript:|expression\s*\()/i', $value)) {
                    $toRemove[] = $attr->nodeName;
                }
            }

            foreach ($toRemove as $attrName) {
                $element->removeAttribute($attrName);
            }
        }
    }

    private function isDangerousUrl(string $value): bool {
        return (bool) preg_match('/^\s*(javascript|vbscript|data:text\/html)/i', $value);
    }
}
