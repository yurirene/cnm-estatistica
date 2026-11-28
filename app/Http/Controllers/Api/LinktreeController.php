<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LinkTree;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LinktreeController extends Controller
{
    /**
     * Valida e-mail e senha e emite token Sanctum para diretoria.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (
            $user === null
            || !Hash::check($validated['password'], $user->password)
            || !$user->status
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas ou usuário inativo.',
            ], 401);
        }

        if ($user->role->name !== User::ROLE_DIRETORIA) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso permitido apenas para usuários da diretoria.',
            ], 403);
        }

        $user->tokens()->where('name', 'linktree')->delete();
        $token = $user->createToken('linktree')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login válido para diretoria.',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function links(Request $request): JsonResponse
    {
        $this->ensureDiretoria($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Links encontrados com sucesso.',
            'data' => LinkTree::all()->map(function ($link) {
                return [
                    'id' => $link->id,
                    'name' => $link->name,
                    'url' => $link->url,
                ];
            }),
        ]);
    }

    public function addLink(Request $request): JsonResponse
    {
        $this->ensureDiretoria($request->user());

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'url' => ['required', 'string'],
        ]);
        LinkTree::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Link adicionado com sucesso.',
        ]);
    }

    public function removerLink(Request $request, LinkTree $link): JsonResponse
    {
        $this->ensureDiretoria($request->user());

        $link->delete();

        return response()->json([
            'success' => true,
            'message' => 'Link removido com sucesso.',
        ]);
    }

    private function ensureDiretoria(?User $user): void
    {
        if ($user === null || $user->role->name !== User::ROLE_DIRETORIA) {
            abort(403, 'Acesso permitido apenas para usuários da diretoria.');
        }
    }
}
