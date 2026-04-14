<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DiretoriaLoginValidationController extends Controller
{
    /**
     * Valida e-mail e senha e confirma se o usuário pertence à diretoria.
     */
    public function __invoke(Request $request): JsonResponse
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

        return response()->json([
            'success' => true,
            'message' => 'Login válido para diretoria.',
        ]);
    }
}
