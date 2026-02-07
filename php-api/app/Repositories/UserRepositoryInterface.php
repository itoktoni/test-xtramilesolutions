<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    /**
     * Get all users.
     */
    public function getAll(): Collection;

    /**
     * Get users since a given ID (for new data).
     */
    public function getUsersSinceId(int $sinceId, int $limit = 100): Collection;

    /**
     * Get users for update scanning.
     */
    public function getUsersForScan(int $scanId, int $lastCreatedId, int $limit = 100): Collection;

    /**
     * Find user by ID.
     */
    public function find(int $id): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User;

    /**
     * Delete a user.
     */
    public function delete(User $user): bool;
}