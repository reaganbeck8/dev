<?php

namespace App\Models;

use Core\Model;

class MediaModel extends Model
{
    protected string $tableName = 'media';

    private const UPLOAD_BASE = 'storage/uploads/';

    private const TYPE_DIRS = [
        'image' => 'images/',
        'audio' => 'audio/',
        'video' => 'videos/',
        'document' => 'documents/',
    ];

    private const ALLOWED_MIMES = [
        'image'    => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'audio'    => ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'],
        'video'    => ['video/mp4', 'video/webm'],
        'document' => ['application/pdf'],
    ];

    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50 MB

    public function findById(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function getAll(string $fileType = null, int $limit = 50, int $offset = 0): array
    {
        if ($fileType) {
            return $this->fetchAll(
                "SELECT * FROM {$this->table()} WHERE file_type = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$fileType, $limit, $offset]
            );
        }
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function findByType(string $fileType): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE file_type = ? AND deleted_at IS NULL ORDER BY created_at DESC",
            [$fileType]
        );
    }

    /**
     * Handle file upload. Returns the new media ID or throws on failure.
     */
    public function upload(array $file, string $fileType, ?int $uploadedBy = null): int
    {
        // Validate file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload failed with error code: ' . $file['error']);
        }

        if ($file['size'] > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('File exceeds maximum size of 50 MB.');
        }

        $mime = $file['type'];
        if (!isset(self::ALLOWED_MIMES[$fileType]) || !in_array($mime, self::ALLOWED_MIMES[$fileType], true)) {
            throw new \RuntimeException('File type not allowed.');
        }

        // Generate stored name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $storedName = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
        $relPath = self::UPLOAD_BASE . self::TYPE_DIRS[$fileType] . $storedName;
        $absPath = BASE_PATH . '/' . $relPath;

        if (!move_uploaded_file($file['tmp_name'], $absPath)) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        $this->execute(
            "INSERT INTO {$this->table()} (original_name, stored_name, file_path, mime_type, file_type, file_size, uploaded_by, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $file['name'],
                $storedName,
                $relPath,
                $mime,
                $fileType,
                $file['size'],
                $uploadedBy,
            ]
        );

        return (int) $this->lastInsertId();
    }

    /**
     * Soft delete — also removes the physical file.
     */
    public function delete(int $id): void
    {
        $media = $this->findById($id);
        if (!$media) {
            return;
        }

        // Remove physical file
        $absPath = BASE_PATH . '/' . $media['file_path'];
        if (file_exists($absPath)) {
            unlink($absPath);
        }

        $this->execute(
            "UPDATE {$this->table()} SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    public function count(string $fileType = null): int
    {
        if ($fileType) {
            $row = $this->fetch(
                "SELECT COUNT(*) as total FROM {$this->table()} WHERE file_type = ? AND deleted_at IS NULL",
                [$fileType]
            );
        } else {
            $row = $this->fetch(
                "SELECT COUNT(*) as total FROM {$this->table()} WHERE deleted_at IS NULL"
            );
        }
        return (int) ($row['total'] ?? 0);
    }
}
