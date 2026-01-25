<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class TokensController extends Controller
{
    /**
     * Show the user's API tokens settings page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('settings/Tokens', [
            'tokens' => $request->user()->tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'created_at' => Carbon::parse($token->created_at)->format('Y-m-d H:i:s'),
                    'last_used_at' => $token->last_used_at ? Carbon::parse($token->last_used_at)->format('Y-m-d H:i:s') : null,
                    'expires_at' => $token->expires_at ? Carbon::parse($token->expires_at)->format('Y-m-d H:i:s') : null,
                ];
            }),
        ]);
    }

    /**
     * Create a new personal access token.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        $token = $request->user()->createToken($request->name, $request->abilities ?? ['*']);

        // Return Inertia response with the new token
        return Inertia::render('settings/Tokens', [
            'tokens' => $request->user()->tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'created_at' => Carbon::parse($token->created_at)->format('Y-m-d H:i:s'),
                    'last_used_at' => $token->last_used_at ? Carbon::parse($token->last_used_at)->format('Y-m-d H:i:s') : null,
                    'expires_at' => $token->expires_at ? Carbon::parse($token->expires_at)->format('Y-m-d H:i:s') : null,
                ];
            }),
            'newToken' => [
                'token' => $token->plainTextToken,
                'id' => $token->accessToken->id,
                'name' => $token->accessToken->name,
            ],
        ]);
    }

    /**
     * Delete a personal access token.
     */
    public function destroy(Request $request, string $tokenId)
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return back()->with('error', 'Token not found');
        }

        $token->delete();

        return back()->with('flash', [
            'message' => 'Token deleted successfully',
        ]);
    }
}