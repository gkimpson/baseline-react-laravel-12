<?php

namespace Tests\Concerns;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

trait InteractsWithTokens
{
    protected function createTokenForUser(User $user, string $name = 'test-device', array $abilities = ['*']): NewAccessToken
    {
        return $user->createToken($name, $abilities);
    }

    protected function authenticateWithToken(User $user, string $tokenName = 'test-device', array $abilities = ['*']): array
    {
        $token = $this->createTokenForUser($user, $tokenName, $abilities);

        return [
            'token' => $token,
            'headers' => [
                'Authorization' => 'Bearer '.$token->plainTextToken,
            ],
        ];
    }

    protected function assertTokenExists(User $user, string $tokenName): void
    {
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => $tokenName,
        ]);
    }

    protected function assertTokenDeleted(NewAccessToken $token): void
    {
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    protected function assertTokenNotDeleted(NewAccessToken $token): void
    {
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    protected function revokeToken(NewAccessToken $token): void
    {
        $token->accessToken->delete();
    }

    protected function expireToken(NewAccessToken $token): void
    {
        $token->accessToken->update([
            'expires_at' => now()->subHour(),
        ]);
    }

    protected function createExpiredToken(User $user, string $name = 'expired-token'): NewAccessToken
    {
        $token = $this->createTokenForUser($user, $name);
        $this->expireToken($token);

        return $token;
    }
}
