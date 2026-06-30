<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:191'],
        ]);

        $user = User::query()
            ->activeEmployee()
            ->where(function ($query) use ($validated): void {
                $query
                    ->where('email', $validated['login'])
                    ->orWhere('work_email', $validated['login'])
                    ->orWhere('username', $validated['login']);
            })
            ->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => 'These credentials do not match an active employee account.',
            ]);
        }

        $token = Str::random(80);

        $user->forceFill([
            'remember_token' => $token,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Logged in successfully.',
            'token_type' => 'Bearer',
            'token' => $token,
            'employee' => $this->employeePayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'employee' => $this->employeePayload($request->user()->load(['company', 'branch', 'department', 'post'])),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->forceFill([
            'remember_token' => null,
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    private function employeePayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'work_email' => $user->work_email,
            'username' => $user->username,
            'employee_code' => $user->employee_code,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'status' => $user->status,
            'company' => $user->company,
            'branch' => $user->branch,
            'department' => $user->department,
            'post' => $user->post,
        ];
    }
}
