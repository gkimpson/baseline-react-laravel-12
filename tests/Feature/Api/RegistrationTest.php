<?php

use App\Models\User;
use Tests\Concerns\CreatesApiUsers;

uses(CreatesApiUsers::class);

describe('API Registration', function () {
    test('user can register and receive verification email', function () {
        $registrationData = $this->getValidRegistrationData();

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'message',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'email_verified_at' => null,
        ]);

        expect($response->json('message'))->toContain('verify');
    });

    test('registered user cannot login without email verification', function () {
        $registrationData = $this->getValidRegistrationData();

        $this->postJson('/api/register', $registrationData);

        $loginData = $this->getValidLoginData(
            $registrationData['email'],
            $registrationData['password']
        );

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    test('registered user can login after email verification', function () {
        $registrationData = $this->getValidRegistrationData();

        $this->postJson('/api/register', $registrationData);

        $user = User::where('email', $registrationData['email'])->first();
        $user->markEmailAsVerified();

        $loginData = $this->getValidLoginData(
            $registrationData['email'],
            $registrationData['password']
        );

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);

        expect($response->json())->toHaveTokenStructure();
    });

    test('registration with duplicate email fails', function () {
        $existingUser = $this->createApiUser(['email' => 'existing@example.com']);

        $registrationData = $this->getValidRegistrationData([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    test('registration requires password confirmation', function () {
        $registrationData = $this->getValidRegistrationData([
            'password_confirmation' => 'different-password',
        ]);

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });

    test('registration requires minimum password length', function () {
        $registrationData = $this->getValidRegistrationData([
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });

    test('registration requires valid email format', function () {
        $registrationData = $this->getValidRegistrationData([
            'email' => 'invalid-email',
        ]);

        $response = $this->postJson('/api/register', $registrationData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    test('registration requires all fields', function () {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
                'device_name',
            ]);
    });
});
