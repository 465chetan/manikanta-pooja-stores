<?php
// ============================================================
// SRI MANIKANTA POOJA STORES â€” INPUT VALIDATOR
// ============================================================

class Validator {
    private array $errors = [];
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public static function make(array $data, array $rules): self {
        $v = new self($data);
        foreach ($rules as $field => $ruleStr) {
            $v->applyRules($field, $ruleStr);
        }
        return $v;
    }

    private function applyRules(string $field, string $ruleStr): void {
        $rules = explode('|', $ruleStr);
        $value = $this->data[$field] ?? null;
        $label = ucwords(str_replace('_', ' ', $field));

        foreach ($rules as $rule) {
            [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

            switch ($ruleName) {
                case 'required':
                    if ($value === null || $value === '' || $value === []) {
                        $this->errors[$field][] = "$label is required.";
                        return; // Skip further rules for this field
                    }
                    break;
                case 'string':
                    if ($value !== null && !is_string($value)) {
                        $this->errors[$field][] = "$label must be a string.";
                    }
                    break;
                case 'min':
                    if ($value !== null && strlen((string)$value) < (int)$param) {
                        $this->errors[$field][] = "$label must be at least $param characters.";
                    }
                    break;
                case 'max':
                    if ($value !== null && strlen((string)$value) > (int)$param) {
                        $this->errors[$field][] = "$label must not exceed $param characters.";
                    }
                    break;
                case 'email':
                    if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = "$label must be a valid email address.";
                    }
                    break;
                case 'mobile':
                    $clean = preg_replace('/\D/', '', (string)$value);
                    if ($value !== null && !preg_match('/^[6-9]\d{9}$/', $clean)) {
                        $this->errors[$field][] = "$label must be a valid 10-digit Indian mobile number.";
                    }
                    break;
                case 'numeric':
                    if ($value !== null && !is_numeric($value)) {
                        $this->errors[$field][] = "$label must be a number.";
                    }
                    break;
                case 'integer':
                    if ($value !== null && !filter_var($value, FILTER_VALIDATE_INT)) {
                        $this->errors[$field][] = "$label must be an integer.";
                    }
                    break;
                case 'min_val':
                    if ($value !== null && (float)$value < (float)$param) {
                        $this->errors[$field][] = "$label must be at least $param.";
                    }
                    break;
                case 'pincode':
                    if ($value !== null && !preg_match('/^\d{6}$/', (string)$value)) {
                        $this->errors[$field][] = "$label must be a valid 6-digit Indian pincode.";
                    }
                    break;
                case 'in':
                    $allowed = explode(',', $param);
                    if ($value !== null && !in_array($value, $allowed, true)) {
                        $this->errors[$field][] = "$label must be one of: " . implode(', ', $allowed) . '.';
                    }
                    break;
                case 'password':
                    if ($value !== null && strlen($value) < 8) {
                        $this->errors[$field][] = "$label must be at least 8 characters.";
                    }
                    break;
            }
        }
    }

    public function fails(): bool {
        return !empty($this->errors);
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(): string {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }
        return 'Validation failed.';
    }
}

/**
 * Sanitize a string against XSS
 */
function sanitize(string $str): string {
    return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize mobile number: keep only digits, strip country code
 */
function sanitizeMobile(string $mobile): string {
    $clean = preg_replace('/\D/', '', $mobile);
    if (strlen($clean) === 12 && str_starts_with($clean, '91')) {
        $clean = substr($clean, 2);
    }
    return $clean;
}

