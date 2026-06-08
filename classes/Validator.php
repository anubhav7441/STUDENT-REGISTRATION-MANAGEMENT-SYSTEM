<?php

/**
 * Validator
 *
 * Centralised, fluent, reusable server-side input validation.
 * Error messages match the PDF requirements exactly.
 */
class Validator
{
    /** @var array<string,string> Error messages keyed by field name */
    private array $errors = [];

    /** @var array Raw input data */
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // ── Rule Methods (fluent) ─────────────────────────────────

    /** Required — must not be empty after trimming. */
    public function required(string $field, string $label): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string)($this->data[$field] ?? ''));

        // Handle array fields (e.g. skills[])
        if (is_array($this->data[$field] ?? null) && empty($this->data[$field])) {
            $value = '';
        }

        if ($value === '') {
            // PDF-specified messages where applicable
            $this->errors[$field] = match ($field) {
                'full_name'     => 'Please enter your full name.',
                'email'         => 'Please enter a valid email address.',
                'country'       => 'Country must be selected.',
                'gender'        => 'Please select your gender.',
                'date_of_birth' => 'Please enter your date of birth.',
                default         => "{$label} is required.",
            };
        }

        return $this;
    }

    /** Valid e-mail address. */
    public function email(string $field, string $label): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Please enter a valid email address.';
        }
        return $this;
    }

    /** Phone: exactly 10 digits. */
    public function phone(string $field, string $label): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $digits = preg_replace('/\D/', '', (string)($this->data[$field] ?? ''));
        if ($digits !== '' && !preg_match('/^\d{10}$/', $digits)) {
            $this->errors[$field] = 'Phone number must contain 10 digits.';
        }
        return $this;
    }

    /** Value must be in an allowed whitelist. */
    public function inList(string $field, string $label, array $allowed): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = (string)($this->data[$field] ?? '');
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field] = "{$label} contains an invalid value.";
        }
        return $this;
    }

    /** Date string (Y-m-d) that must be in the past. */
    public function date(string $field, string $label): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }
        $ts = strtotime($value);
        if ($ts === false || date('Y-m-d', $ts) !== $value) {
            $this->errors[$field] = 'Please enter a valid date of birth.';
            return $this;
        }
        if ($ts >= time()) {
            $this->errors[$field] = 'Date of birth must be in the past.';
        }
        return $this;
    }

    /** Maximum character length. */
    public function maxLength(string $field, string $label, int $max): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }
        $value = (string)($this->data[$field] ?? '');
        if (mb_strlen($value) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    // ── Result Accessors ──────────────────────────────────────

    public function passes(): bool     { return empty($this->errors); }
    public function fails(): bool      { return !$this->passes(); }
    public function getErrors(): array { return $this->errors; }
    public function firstError(): string { return array_values($this->errors)[0] ?? ''; }

    // ── Static Sanitisation Helpers ───────────────────────────

    /** Strip HTML, trim, encode special chars — safe for single-line fields. */
    public static function sanitizeString(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Sanitise multi-line text — strips HTML but preserves newlines. */
    public static function sanitizeText(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Cast to integer safely. */
    public static function sanitizeInt(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
