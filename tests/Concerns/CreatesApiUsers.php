<?php

namespace Tests\Concerns;

use App\Models\User;

trait CreatesApiUsers
{
    protected function createApiUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function createUnverifiedApiUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email_verified_at' => null,
        ], $attributes));
    }

    protected function createUserWithCredentials(string $email = 'test@example.com', string $password = 'password'): User
    {
        return $this->createApiUser([
            'email' => $email,
            'password' => bcrypt($password),
        ]);
    }

    protected function getValidLoginData(string $email = 'test@example.com', string $password = 'password', string $deviceName = 'test-device'): array
    {
        return [
            'email' => $email,
            'password' => $password,
            'device_name' => $deviceName,
        ];
    }

    protected function getValidRegistrationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'device_name' => 'test-device',
        ], $overrides);
    }

    protected function getValidTokenData(string $name = 'test-token', array $abilities = ['*']): array
    {
        return [
            'name' => $name,
            'abilities' => $abilities,
        ];
    }
}
