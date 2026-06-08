<?php

/**
 * Global View Helper Functions
 *
 * Loaded once by index.php — available in all views.
 * Keeps views clean and eliminates duplicate function definitions.
 */

// ── Output Helpers ────────────────────────────────────────────

/**
 * Return a safe HTML-encoded field value for form pre-fill.
 * Priority: $old (repopulate after validation error) → $student (edit) → ''
 */
function fval(string $key, array $old, ?array $student): string
{
    $v = $old[$key] ?? $student[$key] ?? '';
    // If the value is an array (e.g. skills[] from POST), join it
    if (is_array($v)) {
        $v = implode(', ', $v);
    }
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/**
 * Return the HTML-escaped validation error for a field, or ''.
 */
function ferr(string $key, array $errors): string
{
    return htmlspecialchars($errors[$key] ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Return 'invalid', 'valid', or '' CSS class based on form state.
 * Returns '' when the form is pristine (never submitted).
 */
function fcls(string $key, array $errors, array $old): string
{
    if (empty($old)) return '';
    return isset($errors[$key]) ? 'invalid' : 'valid';
}

// ── Dashboard / Table Helpers ─────────────────────────────────

/**
 * Build a URL preserving current safe GET params and merging overrides.
 */
function buildUrl(array $override = []): string
{
    $allowed = ['search', 'sort', 'order', 'page'];
    $base    = [];
    foreach ($allowed as $k) {
        if (isset($_GET[$k])) {
            $base[$k] = $_GET[$k];
        }
    }
    return '?' . http_build_query(array_merge($base, $override));
}

/**
 * Return an HTML sort-direction icon for a table column header.
 */
function sortIcon(string $col, string $curSort, string $curOrder): string
{
    if ($curSort !== $col) {
        return ' <span style="opacity:.3;font-size:.7em;">↕</span>';
    }
    return $curOrder === 'ASC'
        ? ' <span style="font-size:.75em;">↑</span>'
        : ' <span style="font-size:.75em;">↓</span>';
}

/**
 * Return full URL to an uploaded avatar image, or null if none.
 */
function avatarUrl(?string $filename): ?string
{
    return $filename
        ? htmlspecialchars(UPLOAD_URL . $filename, ENT_QUOTES, 'UTF-8')
        : null;
}

/**
 * Return 1–2 uppercase initials from a full name string.
 */
function initials(string $name): string
{
    $parts = explode(' ', trim($name));
    return strtoupper(
        substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
    );
}

/**
 * Return selected skills as a clean array from either a DB string or a POST array.
 */
function parseSkills(mixed $raw): array
{
    if (is_array($raw)) {
        return array_values(array_filter(array_map('trim', $raw)));
    }
    return array_values(array_filter(array_map('trim', explode(',', (string)$raw))));
}
