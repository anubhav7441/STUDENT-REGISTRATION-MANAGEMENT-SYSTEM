<?php

/**
 * ImageUploader
 *
 * Handles secure profile-image uploads.
 * Validates MIME type (via finfo — cannot be spoofed), extension,
 * and file size before persisting the file with a unique name.
 */
class ImageUploader
{
    private string $uploadDir;
    private array  $allowedMimes;
    private array  $allowedExtensions;
    private int    $maxSize;
    private ?string $error = null;

    // ── Constructor ───────────────────────────────────────────
    public function __construct()
    {
        $this->uploadDir         = UPLOAD_DIR;
        $this->allowedMimes      = ALLOWED_TYPES;
        $this->allowedExtensions = ALLOWED_EXT;
        $this->maxSize           = MAX_FILE_SIZE;

        // Ensure upload directory exists and is writable
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                error_log('[ImageUploader] Cannot create upload directory: ' . $this->uploadDir);
            }
        }
    }

    // ── Public API ────────────────────────────────────────────

    /**
     * Process an uploaded file from $_FILES.
     *
     * @param  array       $file  e.g. $_FILES['profile_image']
     * @return string|null        Saved filename on success, null on failure or no upload
     */
    public function upload(array $file): ?string
    {
        $this->error = null;

        // No file selected — not an error; caller decides if it's mandatory
        if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // PHP-level upload error
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->error = $this->resolveUploadError($file['error']);
            return null;
        }

        // File size guard
        if ($file['size'] > $this->maxSize) {
            $this->error = 'File size must not exceed 2 MB.';
            return null;
        }

        // Extension check (case-insensitive)
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions, true)) {
            $this->error = 'Only JPG, JPEG, PNG, and WEBP images are allowed.';
            return null;
        }

        // MIME-type verification via finfo (reads the actual file bytes)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedMimes, true)) {
            $this->error = 'Invalid image file. Please upload a genuine image.';
            return null;
        }

        // Generate collision-resistant filename — no original name kept (prevents path tricks)
        $filename    = sprintf('img_%s_%s.%s', time(), bin2hex(random_bytes(8)), $ext);
        $destination = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->error = 'Failed to save the uploaded file. Check directory permissions.';
            return null;
        }

        return $filename;
    }

    /**
     * Safely delete an uploaded file by its stored filename.
     */
    public function delete(?string $filename): void
    {
        if (empty($filename)) {
            return;
        }
        // basename() prevents any directory-traversal attack
        $path = $this->uploadDir . basename($filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    // ── Private Helpers ───────────────────────────────────────

    private function resolveUploadError(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the maximum allowed size.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded. Please try again.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server error: missing temporary directory.',
            UPLOAD_ERR_CANT_WRITE => 'Server error: failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'File upload was blocked by a server extension.',
            default               => 'An unknown upload error occurred (code ' . $code . ').',
        };
    }
}
