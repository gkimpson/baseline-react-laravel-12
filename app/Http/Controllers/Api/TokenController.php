<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TokenController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $tokens = $request->user()->tokens->map(function ($token) {
            return [
                'id' => $token->id,
                'name' => $token->name,
                'abilities' => is_array($token->abilities) ? $token->abilities : json_decode($token->abilities, true) ?? ['*'],
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ];
        });

        return response()->json([
            'tokens' => $tokens,
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string',
        ]);

        $abilities = $request->input('abilities', ['*']);

        $token = $request->user()->createToken($request->name, $abilities);

        return response()->json([
            'token' => [
                'id' => $token->accessToken->id,
                'name' => $token->accessToken->name,
                'abilities' => is_array($token->accessToken->abilities) ? $token->accessToken->abilities : json_decode($token->accessToken->abilities, true) ?? ['*'],
                'plain_text_token' => $token->plainTextToken,
                'created_at' => $token->accessToken->created_at,
            ],
        ], 201);
    }

    public function destroy(Request $request, $tokenId): \Illuminate\Http\JsonResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (! $token) {
            throw ValidationException::withMessages([
                'token' => ['Token not found.'],
            ]);
        }

        if ($token->id === $request->user()->currentAccessToken()->id) {
            throw ValidationException::withMessages([
                'token' => ['You cannot delete your current access token.'],
            ]);
        }

        $token->delete();

        return response()->json([
            'message' => 'Token deleted successfully.',
        ]);
    }

    public function destroyAll(Request $request): \Illuminate\Http\JsonResponse
    {
        $currentTokenId = $request->user()->currentAccessToken()->id;

        $deletedCount = $request->user()->tokens()
            ->where('id', '!=', $currentTokenId)
            ->delete();

        return response()->json([
            'message' => "Successfully deleted {$deletedCount} tokens.",
            'deleted_count' => $deletedCount,
        ]);
    }
}
