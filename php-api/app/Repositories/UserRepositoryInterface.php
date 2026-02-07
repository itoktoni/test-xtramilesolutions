<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * Get all users.
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection;

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