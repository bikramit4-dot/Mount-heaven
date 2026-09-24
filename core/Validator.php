<?php

namespace App\Core;

class Validator
{
    private array $errors = [];

    public function __construct(private array $data) {}

    public function required(string $field, string $label): static
    {
        $v = trim((string) ($this->data[$field] ?? ''));
        if ($v === '') {
            $this->errors[$field][] = "{$label} is required.";
        }
        return $this;
    }

    public function email(string $field, string $label): static
    {
        $v = trim((string) ($this->data[$field] ?? ''));
        if ($v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    public function min(string $field, int $len, string $label): static
    {
        $v = trim((string) ($this->data[$field] ?? ''));
        if ($v !== '' && mb_strlen($v) < $len) {
            $this->errors[$field][] = "{$label} must be at least {$len} characters.";
        }
        return $this;
    }

    public function max(string $field, int $len, string $label): static
    {
        $v = trim((string) ($this->data[$field] ?? ''));
        if ($v !== '' && mb_strlen($v) > $len) {
            $this->errors[$field][] = "{$label} may not exceed {$len} characters.";
        }
        return $this;
    }

    public function same(string $field, string $other, string $label): static
    {
        if (($this->data[$field] ?? '') !== ($this->data[$other] ?? '')) {
            $this->errors[$field][] = "{$label} confirmation does not match.";
        }
        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /** Trimmed input data, safe for storage (drops _token etc.). */
    public function validated(): array
    {
        $out = [];
        foreach ($this->data as $k => $v) {
            if ($k === '_token' || is_array($v)) {
                continue;
            }
            $out[$k] = is_string($v) ? trim($v) : $v;
        }
        return $out;
    }
}
