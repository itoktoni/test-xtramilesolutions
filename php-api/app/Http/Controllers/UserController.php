<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = $this->service->getAllUsers();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = $this->validateCreateUser($request);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $user = $this->service->createUser($validator->validated());

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->service->findUser($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->service->findUser($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $validator = $this->validateUpdateUser($request, $id);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $updatedUser = $this->service->updateUser($user, $validator->validated());

        return response()->json([
            'id' => $updatedUser->id,
            'name' => $updatedUser->name,
            'email' => $updatedUser->email
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->service->findUser($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $this->service->deleteUser($user);

        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Validate create user request.
     */
    private function validateCreateUser(Request $request): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
        ]);
    }

    /**
     * Validate update user request.
     */
    private function validateUpdateUser(Request $request, int $id): \Illuminate\Validation\Validator
    {
        return Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $id,
        ]);
    }

    /**
     * Return validation error response.
     */
    private function validationErrorResponse(\Illuminate\Validation\Validator $validator): JsonResponse
    {
        return response()->json([
            'error' => 'Validation failed',
            'messages' => $validator->errors()
        ], 422);
    }
}