<?php

function uploads_get_root(): string
{
    $path = realpath(__DIR__ . '/../uploads');
    if ($path === false) {
        $path = __DIR__ . '/../uploads';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
    return $path;
}

function uploads_ensure_subdir(array $segments): ?string
{
    $path = uploads_get_root();
    foreach ($segments as $segment) {
        $clean = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$segment);
        if ($clean === '') {
            continue;
        }
        $path .= DIRECTORY_SEPARATOR . $clean;
        if (!is_dir($path) && !mkdir($path, 0755, true)) {
            return null;
        }
    }
    return $path;
}

function uploads_relative_path(string $absolute): ?string
{
    $root = uploads_get_root();
    $absolutePath = realpath($absolute) ?: $absolute;

    if (strpos($absolutePath, $root) !== 0) {
        return null;
    }

    $relative = substr($absolutePath, strlen($root));
    $relative = str_replace('\\', '/', $relative);
    return ltrim($relative, '/');
}

function uploads_validate_image(array $file): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid upload parameters'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload failed with error code ' . $file['error']];
    }

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Temporary upload not found'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File size must be 5MB or less'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed, true)) {
        return ['success' => false, 'message' => 'Unsupported image type'];
    }

    $mime = @mime_content_type($file['tmp_name']);
    if ($mime === false || strpos($mime, 'image/') !== 0) {
        return ['success' => false, 'message' => 'Invalid image file'];
    }

    return ['success' => true, 'extension' => $ext];
}

function uploads_store_image(array $file, int $userId, array $subSegments, ?string $baseName = null): array
{
    $validation = uploads_validate_image($file);
    if (!$validation['success']) {
        return $validation;
    }

    $ext = $validation['extension'];
    $destinationDir = uploads_ensure_subdir(array_merge(['u' . $userId], $subSegments));
    if ($destinationDir === null) {
        return ['success' => false, 'message' => 'Failed to prepare upload directory'];
    }

    if ($baseName === null || $baseName === '') {
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $sanitized = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalName);
        $sanitized = $sanitized !== '' ? $sanitized : 'image';
        $baseName = $sanitized . '_' . uniqid();
    }

    $fileName = $baseName . '.' . $ext;
    $absolutePath = $destinationDir . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        return ['success' => false, 'message' => 'Unable to save uploaded file'];
    }

    $relativePath = uploads_relative_path($absolutePath);
    if ($relativePath === null) {
        return ['success' => false, 'message' => 'Failed to resolve upload path'];
    }

    return ['success' => true, 'path' => $relativePath, 'absolute' => $absolutePath, 'extension' => $ext];
}

function uploads_move_within(string $currentRelative, array $destinationSegments, ?string $newBaseName = null): array
{
    $root = uploads_get_root();
    $relativeNormalized = str_replace(['\\', '..'], ['/', ''], $currentRelative);
    $relativeNormalized = ltrim($relativeNormalized, '/');

    $currentPath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativeNormalized);

    if (!file_exists($currentPath)) {
        return ['success' => false, 'message' => 'Source file not found'];
    }

    $destinationDir = uploads_ensure_subdir($destinationSegments);
    if ($destinationDir === null) {
        return ['success' => false, 'message' => 'Failed to prepare destination directory'];
    }

    $ext = strtolower(pathinfo($currentPath, PATHINFO_EXTENSION));
    $newBaseName = $newBaseName !== null && $newBaseName !== ''
        ? preg_replace('/[^A-Za-z0-9_-]/', '_', $newBaseName)
        : pathinfo($currentPath, PATHINFO_FILENAME);

    $destinationPath = $destinationDir . DIRECTORY_SEPARATOR . $newBaseName . '.' . $ext;

    if (!rename($currentPath, $destinationPath)) {
        return ['success' => false, 'message' => 'Unable to move uploaded file'];
    }

    $relativePath = uploads_relative_path($destinationPath);
    if ($relativePath === null) {
        return ['success' => false, 'message' => 'Failed to resolve destination path'];
    }

    return ['success' => true, 'path' => $relativePath, 'absolute' => $destinationPath];
}

function uploads_normalize_relative(string $path): string
{
    if (strpos($path, 'uploads/') === 0) {
        $path = substr($path, strlen('uploads/'));
    }

    $path = str_replace(['..', '\\'], ['', '/'], $path);
    return ltrim($path, '/');
}

function uploads_delete(string $path): void
{
    $relative = uploads_normalize_relative($path);
    if ($relative === '') {
        return;
    }

    $root = uploads_get_root();
    $absolute = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_file($absolute)) {
        @unlink($absolute);
    }
}

?>

