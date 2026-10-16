<?php

namespace App\Http\Controllers;

use App\Exceptions\MeetApiException;
use App\Models\MeetSala;
use App\Models\User;
use App\Services\MeetService;
use App\Support\MeetClient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class MeetController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $recusa = $this->recusarPagina($request);

        if ($recusa !== null) {
            return $recusa;
        }

        $salas = MeetSala::query()
            ->with(['solicitante', 'para'])
            ->orderByDesc('inicia_em')
            ->paginate(15);

        return view('dashboard.meet.index', [
            'meetNaoConfigurado' => ! MeetClient::configurado(),
            'usuarios' => $this->usuariosPermitidos(),
            'salas' => $salas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $recusa = $this->recusarPagina($request);

        if ($recusa !== null) {
            return $recusa;
        }

        if (! MeetClient::configurado()) {
            return redirect()->route('dashboard.meet.index')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'O Meet ainda não foi configurado no sistema.',
                ],
            ]);
        }

        $dados = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'para_user_id' => ['required', 'uuid', 'exists:users,id'],
            'inicia_em' => ['required', 'date'],
            'termina_em' => ['required', 'date'],
        ]);

        $fuso = (string) config('app.timezone');

        try {
            $sala = MeetService::solicitar(
                $request->user(),
                $dados['para_user_id'],
                trim($dados['titulo']),
                Carbon::parse($dados['inicia_em'], $fuso),
                Carbon::parse($dados['termina_em'], $fuso)
            );
        } catch (ValidationException $th) {
            throw $th;
        } catch (MeetApiException $th) {
            return redirect()->back()->withInput()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => $th->getMessage(),
                ],
            ]);
        } catch (Throwable $th) {
            MeetService::registrarErro($th);

            return redirect()->back()->withInput()->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Não foi possível criar a sala.',
                ],
            ]);
        }

        return redirect()->route('dashboard.meet.index')->with([
            'mensagem' => [
                'status' => true,
                'texto' => 'Sala criada: ' . $sala->meet_uri,
            ],
        ]);
    }

    public function calendario(Request $request): JsonResponse
    {
        if (! MeetService::podeAcessar($request->user())) {
            return response()->json([
                'message' => 'Você não tem permissão para ver o calendário.',
            ], 403);
        }

        $dados = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $fuso = (string) config('app.timezone');
        $inicio = Carbon::parse($dados['start'], $fuso);
        $fim = Carbon::parse($dados['end'], $fuso);

        $eventos = MeetSala::query()
            ->with(['solicitante', 'para'])
            ->where('inicia_em', '<', $fim)
            ->where('termina_em', '>', $inicio)
            ->orderBy('inicia_em')
            ->get()
            ->map(function (MeetSala $sala) {
                return [
                    'id' => $sala->id,
                    'title' => $sala->titulo,
                    'start' => $sala->inicia_em?->toIso8601String(),
                    'end' => $sala->termina_em?->toIso8601String(),
                    'url' => $sala->meet_uri,
                ];
            })
            ->values();

        return response()->json($eventos);
    }

    private function recusarPagina(Request $request): ?RedirectResponse
    {
        if (! MeetService::podeAcessar($request->user())) {
            return redirect()->route('dashboard.home')->with([
                'mensagem' => [
                    'status' => false,
                    'texto' => 'Você não tem permissão para acessar a Sala Meet.',
                ],
            ]);
        }

        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    private function usuariosPermitidos()
    {
        return User::query()
            ->whereHas('role', function ($consulta) {
                $consulta->whereIn('name', User::ROLES_MEET);
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
