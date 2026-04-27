<?php

namespace App\Models;

use Core\Model;

class ArtistModel extends Model
{
    protected string $tableName = 'artists';

    public function findById(int $id): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function findBySlug(string $slug): array|false
    {
        return $this->fetch(
            "SELECT * FROM {$this->table()} WHERE slug = ? AND deleted_at IS NULL",
            [$slug]
        );
    }

    public function getActive(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE status = 'active' AND deleted_at IS NULL ORDER BY sort_order, name"
        );
    }

    public function getAll(): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->table()} WHERE deleted_at IS NULL ORDER BY sort_order, name"
        );
    }

    public function create(array $data): int
    {
        $this->execute(
            "INSERT INTO {$this->table()} (name, slug, tagline, bio, profile_image_id, status, sort_order, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['slug'],
                $data['tagline'] ?? null,
                $data['bio'] ?? null,
                $data['profile_image_id'] ?? null,
                $data['status'] ?? 'active',
                $data['sort_order'] ?? 0,
            ]
        );
        return (int) $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = [];

        foreach (['name', 'slug', 'tagline', 'bio', 'profile_image_id', 'status', 'sort_order'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) {
            return;
        }

        $fields[] = "updated_at = NOW()";
        $params[] = $id;

        $this->execute(
            "UPDATE {$this->table()} SET " . implode(', ', $fields) . " WHERE id = ? AND deleted_at IS NULL",
            $params
        );
    }

    public function delete(int $id): void
    {
        $this->execute(
            "UPDATE {$this->table()} SET deleted_at = NOW() WHERE id = ?",
            [$id]
        );
    }

    // ─── Socials ───────────────────────────────────────────────────────────────

    public function getSocials(int $artistId): array
    {
        return $this->fetchAll(
            "SELECT * FROM {$this->db->table('artist_socials')} WHERE artist_id = ? ORDER BY platform",
            [$artistId]
        );
    }

    public function setSocials(int $artistId, array $socials): void
    {
        // Delete existing and re-insert
        $this->execute(
            "DELETE FROM {$this->db->table('artist_socials')} WHERE artist_id = ?",
            [$artistId]
        );

        foreach ($socials as $social) {
            if (empty($social['url'])) {
                continue;
            }
            $this->execute(
                "INSERT INTO {$this->db->table('artist_socials')} (artist_id, platform, url) VALUES (?, ?, ?)",
                [$artistId, $social['platform'], $social['url']]
            );
        }
    }
}
