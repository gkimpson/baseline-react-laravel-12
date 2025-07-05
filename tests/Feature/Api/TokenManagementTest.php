<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('API Token Management', function () {
    test('authenticated user can list their tokens', function () {
        $user = User::factory()->create();
        $token1 = $user->createToken('Device 1');
        $token2 = $user->createToken('Device 2', ['read']);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'tokens' => [
                    '*' => ['id', 'name', 'abilities', 'last_used_at', 'created_at'],
                ],
            ]);

        $tokens = $response->json('tokens');
        expect($tokens)->toHaveCount(2);

        $tokenNames = collect($tokens)->pluck('name')->toArray();
        expect($tokenNames)->toContain('Device 1');
        expect($tokenNames)->toContain('Device 2');
    });

    test('unauthenticated user cannot list tokens', function () {
        $response = $this->getJson('/api/tokens');

        $response->assertStatus(401);
    });

    test('authenticated user can create a new token', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tokens', [
            'name' => 'New Device',
            'abilities' => ['read', 'write'],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token' => [
                    'id',
                    'name',
                    'abilities',
                    'plain_text_token',
                    'created_at',
                ],
            ]);

        expect($response->json('token.name'))->toBe('New Device');
        expect($response->json('token.abilities'))->toBe(['read', 'write']);
        expect($response->json('token.plain_text_token'))->toBeString();

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'New Device',
            'tokenable_id' => $user->id,
        ]);
    });

    test('authenticated user can create a token with default abilities', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tokens', [
            'name' => 'Default Device',
        ]);

        $response->assertStatus(201);
        expect($response->json('token.abilities'))->toBe(['*']);
    });

    test('token creation validation requires name', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tokens', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    test('token creation validation validates abilities array', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/tokens', [
            'name' => 'Test Device',
            'abilities' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['abilities']);
    });

    test('authenticated user can delete a specific token', function () {
        $user = User::factory()->create();
        $token1 = $user->createToken('Device 1');
        $token2 = $user->createToken('Device 2');

        Sanctum::actingAs($user, [], 'sanctum');

        $response = $this->deleteJson("/api/tokens/{$token1->accessToken->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Token deleted successfully.']);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token1->accessToken->id,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token2->accessToken->id,
        ]);
    });

    test('user cannot delete non-existent token', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson('/api/tokens/999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    });

    test('user cannot delete another users token', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token1 = $user1->createToken('Device 1');
        $token2 = $user2->createToken('Device 2');

        Sanctum::actingAs($user1);

        $response = $this->deleteJson("/api/tokens/{$token2->accessToken->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    });

    test('user cannot delete their current access token', function () {
        $user = User::factory()->create();
        $token = $user->createToken('Current Device');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ])->deleteJson("/api/tokens/{$token->accessToken->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    });

    test('authenticated user can delete all other tokens', function () {
        $user = User::factory()->create();
        $currentToken = $user->createToken('Current Device');
        $token1 = $user->createToken('Device 1');
        $token2 = $user->createToken('Device 2');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentToken->plainTextToken,
        ])->deleteJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully deleted 2 tokens.',
                'deleted_count' => 2,
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token1->accessToken->id,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token2->accessToken->id,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $currentToken->accessToken->id,
        ]);
    });

    test('delete all tokens returns correct count when no other tokens exist', function () {
        $user = User::factory()->create();
        $currentToken = $user->createToken('Current Device');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$currentToken->plainTextToken,
        ])->deleteJson('/api/tokens');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully deleted 0 tokens.',
                'deleted_count' => 0,
            ]);
    });

    test('unauthenticated user cannot manage tokens', function () {
        $responses = [
            $this->postJson('/api/tokens', ['name' => 'Test']),
            $this->deleteJson('/api/tokens/1'),
            $this->deleteJson('/api/tokens'),
        ];

        foreach ($responses as $response) {
            $response->assertStatus(401);
        }
    });

    test('tokens have correct abilities when created', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $readOnlyToken = $this->postJson('/api/tokens', [
            'name' => 'Read Only',
            'abilities' => ['read'],
        ]);

        $writeToken = $this->postJson('/api/tokens', [
            'name' => 'Write Access',
            'abilities' => ['write'],
        ]);

        $fullAccessToken = $this->postJson('/api/tokens', [
            'name' => 'Full Access',
            'abilities' => ['*'],
        ]);

        expect($readOnlyToken->json('token.abilities'))->toBe(['read']);
        expect($writeToken->json('token.abilities'))->toBe(['write']);
        expect($fullAccessToken->json('token.abilities'))->toBe(['*']);
    });
});
