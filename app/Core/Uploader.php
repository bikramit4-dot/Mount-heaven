<?php

namespace App\Core;

class Uploader
{
    /**
     * Handle an image upload for the given form field.
     * Returns the stored file name, keeps $old when no new file was uploaded.
     *
     * @throws \RuntimeException on any validation problem
     */
    public static function image(string $field, string $folder, ?string $old = null): ?string
    {
        if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $old;
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('The file upload failed (error code ' . $file['error'] . ').');
        }

        $max = (int) config('uploads.max_size');
        if (($file['size'] ?? 0) > $max) {
            throw new \RuntimeException('File is too large. Maximum size is ' . round($max / 1048576) . ' MB.');
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, config('uploads.allowed'), true)) {
            throw new \RuntimeException('Allowed file types: ' . implode(', ', config('uploads.allowed')));
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mime, $allowedMimes, true)) {
            throw new \RuntimeException('The uploaded file is not a valid image.');
        }

        if (@getimagesize($file['tmp_name']) === false) {
            throw new \RuntimeException('The uploaded file is not a valid image.');
        }

        $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $ext;
        $dir  = BASE_PATH . '/public/uploads/' . trim($folder, '/');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!@move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            throw new \RuntimeException('Could not save the uploaded file.');
        }
        @chmod($dir . '/' . $name, 0644);

        if ($old) {
            // $old may be a bare file name (legacy data) or a "folder/name" path.
            self::delete(str_contains($old, '/') ? $old : trim($folder, '/') . '/' . $old);
        }

        return trim($folder, '/') . '/' . $name;
    }

    /** Delete an uploaded file safely (path traversal protected). */
    public static function delete(string $relative): void
    {
        $base = realpath(BASE_PATH . '/public/uploads');
        if (!$base) {
            return;
        }
        $real = realpath(BASE_PATH . '/public/uploads/' . ltrim($relative, '/'));
        if ($real && str_starts_with($real, $base) && is_file($real)) {
            @unlink($real);
        }
    }
}
