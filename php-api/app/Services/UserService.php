<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class UserService
{
    protected UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all users.
     */
    public function getAllUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getAll();
    }

    /**
     * Get users since a given ID (for new data).
     */
    public function getUsersSinceId(int $sinceId, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getUsersSinceId($sinceId, $limit);
    }

    /**
     * Get users for update scanning.
     */
    public function getUsersForScan(int $scanId, int $lastCreatedId, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getUsersForScan($scanId, $lastCreatedId, $limit);
    }

    /**
     * Find user by ID.
     */
    public function findUser(int $id): ?\App\Models\User
    {
        return $this->repository->find($id);
    }

    /**
     * Create a new user.
     */
    public function createUser(array $data): \App\Models\User
    {
        return $this->repository->create($data);
    }

    /**
     * Update a user.
     */
    public function updateUser(\App\Models\User $user, array $data): \App\Models\User
    {
        return $this->repository->update($user, $data);
    }

    /**
     * Delete a user.
     */
    public function deleteUser(\App\Models\User $user): bool
    {
        return $this->repository->delete($user);
    }
}