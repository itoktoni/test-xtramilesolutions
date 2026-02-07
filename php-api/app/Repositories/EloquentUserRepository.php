<?php

namespace App\Repositories;

use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return User::orderBy('id', 'desc')->get();
    }

    /**
     * Get users since a given ID (for new data).
     * Query: SELECT * FROM users WHERE id > :since_id ORDER BY id ASC LIMIT :limit
     */
    public function getUsersSinceId(int $sinceId, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('id', '>', $sinceId)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get users for update scanning.
     * Query: SELECT * FROM users WHERE id > :scan_id AND id <= :last_created_id ORDER BY id ASC LIMIT :limit
     */
    public function getUsersForScan(int $scanId, int $lastCreatedId, int $limit = 100): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('id', '>', $scanId)
            ->where('id', '<=', $lastCreatedId)
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Find user by ID.
     */
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
        ]);
    }

    /**
     * Update a user.
     */
    public function update(User $user, array $data): User
    {
        if (isset($data['name'])) {
            $user->name = trim($data['name']);
        }

        if (isset($data['email'])) {
            $user->email = strtolower(trim($data['email']));
        }

        $user->save();

        return $user;
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
