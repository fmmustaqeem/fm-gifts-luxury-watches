<?php
namespace FMGifts\Control;

final class Logger
{
    private const SENSITIVE_KEY = '/(?:customer|user|name|email|phone|address|postcode|postal|ip|payment|transaction|note|token|secret|password|api[_-]?key|authorization|cookie|session)/i';

    public static function redact($value, string $key = '')
    {
        if ($key !== '' && preg_match(self::SENSITIVE_KEY, $key)) {
            return '[REDACTED]';
        }
        if (is_array($value)) {
            $safe = [];
            foreach ($value as $childKey => $childValue) {
                $safe[$childKey] = self::redact($childValue, (string) $childKey);
            }
            return $safe;
        }
        if (is_object($value)) {
            $properties = get_object_vars($value);
            if (!$properties) {
                return '[OBJECT REDACTED]';
            }
            $safe = [];
            foreach ($properties as $childKey => $childValue) {
                $safe[$childKey] = self::redact($childValue, (string) $childKey);
            }
            return $safe;
        }
        if (!is_string($value)) {
            return $value;
        }
        $value = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', '[REDACTED_EMAIL]', $value);
        $value = preg_replace('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', '[REDACTED_IP]', $value);
        $value = preg_replace('/(?<!\w)\+?\d[\d\s().-]{7,}\d(?!\w)/', '[REDACTED_PHONE]', $value);
        $value = preg_replace('/\b(?:Bearer|Basic)\s+[^\s]+/i', '[REDACTED_AUTH]', $value);
        return $value;
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        if (!function_exists('wc_get_logger')) {
            return;
        }
        $logger = wc_get_logger();
        if (!is_object($logger) || !method_exists($logger, 'log')) {
            return;
        }
        $logger->log(sanitize_key($level), (string) self::redact($message), [
            'source' => 'fm-gifts-control',
            'context' => self::redact($context),
        ]);
    }
}
