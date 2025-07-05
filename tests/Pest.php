<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

expect()->extend('toBeValidToken', function () {
    return $this->toBeString()->toMatch('/^\d+\|[a-zA-Z0-9]{40}$/');
});

expect()->extend('toHaveTokenStructure', function () {
    return $this->toHaveKeys(['user', 'token']);
});

expect()->extend('toHaveTokenListStructure', function () {
    return $this->toHaveKey('tokens')
        ->and($this->value['tokens'])->toBeArray();
});

expect()->extend('toHaveTokenResponseStructure', function () {
    return $this->toHaveKey('token')
        ->and($this->value['token'])->toHaveKeys(['id', 'name', 'abilities', 'plain_text_token', 'created_at']);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createUserWithToken(array $userAttributes = [], string $tokenName = 'test-device', array $abilities = ['*'])
{
    $user = \App\Models\User::factory()->create($userAttributes);
    $token = $user->createToken($tokenName, $abilities);

    return [
        'user' => $user,
        'token' => $token,
        'plainTextToken' => $token->plainTextToken,
    ];
}

function authenticateApi(?\App\Models\User $user = null, string $tokenName = 'test-device', array $abilities = ['*'])
{
    if (! $user) {
        $user = \App\Models\User::factory()->create();
    }

    $token = $user->createToken($tokenName, $abilities);

    return [
        'user' => $user,
        'token' => $token,
        'headers' => [
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ],
    ];
}

function createVerifiedUser(array $attributes = [])
{
    return \App\Models\User::factory()->create(array_merge([
        'email_verified_at' => now(),
    ], $attributes));
}

function createUnverifiedUser(array $attributes = [])
{
    return \App\Models\User::factory()->create(array_merge([
        'email_verified_at' => null,
    ], $attributes));
}
