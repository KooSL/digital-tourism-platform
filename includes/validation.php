<?php


/**
 * Collects field validators and their error messages. Usage:
 *
 *   $v = new Validator();
 *   $v->required('name', $name, 'Full name is required.');
 *   $v->email('email', $email, 'Please enter a valid email address.');
 *   if ($v->fails()) { ... }
 */
class Validator
{
    private array $errors = [];

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function addError(string $field, string $message): void
    {
        // Only keep the first error per field - showing "name is required"
        // AND "name is too short" for the same empty field is just noise.
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function required(string $field, $value, string $message): self
    {
        if ($value === null || trim((string)$value) === '') {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function email(string $field, $value, string $message, bool $requiredField = true): self
    {
        $value = trim((string)$value);
        if ($value === '' && !$requiredField) {
            return $this;
        }
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function phone(string $field, $value, string $message, bool $requiredField = true): self
    {
        $value = trim((string)$value);
        if ($value === '' && !$requiredField) {
            return $this;
        }
        // Allows an optional leading + and 7-15 digits, covers most
        // international formats without being so strict it rejects real
        // numbers.
        if (!preg_match('/^\+?[0-9]{7,15}$/', $value)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function minLength(string $field, $value, int $min, string $message): self
    {
        if (mb_strlen(trim((string)$value)) < $min) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function maxLength(string $field, $value, int $max, string $message): self
    {
        if (mb_strlen(trim((string)$value)) > $max) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function inArray(string $field, $value, array $allowed, string $message): self
    {
        if (!in_array($value, $allowed, true)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function integerRange(string $field, $value, int $min, int $max, string $message): self
    {
        $intVal = filter_var($value, FILTER_VALIDATE_INT);
        if ($intVal === false || $intVal < $min || $intVal > $max) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function dateNotPast(string $field, $value, string $message): self
    {
        $value = trim((string)$value);
        $date = DateTime::createFromFormat('Y-m-d', $value);
        $today = new DateTime('today');
        if (!$date || $date->format('Y-m-d') !== $value || $date < $today) {
            $this->addError($field, $message);
        }
        return $this;
    }

    /**
     * Simple honeypot check for anti-spam: a hidden field named
     * "website_url" (or similar) that's invisible to real users via CSS,
     * but that unsophisticated bots often fill in automatically. If it has
     * any value, treat the whole submission as spam.
     */
    public function honeypotEmpty(string $field, $value): self
    {
        if (trim((string)$value) !== '') {
            $this->addError($field, 'Spam detected.');
        }
        return $this;
    }
}

/**
 * Store validation errors + the user's submitted (non-sensitive) input in
 * the session, then redirect back to the form. The form page reads these
 * back with getFlashErrors()/oldInput() so the user doesn't have to
 * retype everything after a validation failure.
 */
function redirectWithErrors(string $path, array $errors, array $oldInput = []): void
{
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old_input'] = $oldInput; // NEVER put passwords in here
    $separator = strpos($path, '?') === false ? '?' : '&';
    header("Location: $path{$separator}error=validation");
    exit;
}

/**
 * Reads and clears the flashed validation errors. Call once per page load.
 */
function getFlashErrors(): array
{
    $errors = $_SESSION['form_errors'] ?? [];
    unset($_SESSION['form_errors']);
    return $errors;
}

/**
 * Reads (without clearing) a single old-input value, for repopulating a
 * form field after a validation failure. Falls back to $default (e.g. a
 * signed-in user's saved name/email) when there's no flashed value.
 */
function oldInput(string $key, string $default = ''): string
{
    return htmlspecialchars($_SESSION['form_old_input'][$key] ?? $default);
}

/**
 * Call after successfully reading old input on a GET request, so a page
 * refresh doesn't keep re-showing stale form values forever.
 */
function clearOldInput(): void
{
    unset($_SESSION['form_old_input']);
}

/**
 * Renders the flashed validation error list using the same .error-box
 * markup pattern already used across the site for success/error banners,
 * so it looks consistent with the existing design.
 */
function renderValidationErrors(): void
{
    $errors = getFlashErrors();
    if (empty($errors)) {
        return;
    }
    echo '<div class="error-box" id="errorBox"><strong>Please fix the following:</strong><ul>';
    foreach ($errors as $message) {
        echo '<li>' . htmlspecialchars($message) . '</li>';
    }
    echo '</ul></div>';
}
