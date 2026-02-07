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
        return User::orderBy('created_at', 'desc')->get();
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