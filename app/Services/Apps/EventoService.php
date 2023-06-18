<?php

namespace App\Services\Apps;

use App\Helpers\FormularioEventoHelper;
use App\Models\Apps\Site\Evento;
use App\Models\Apps\Site\EventoInscrito;
use App\Models\Sinodal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Service responsável por manipular as configurações do evento dentro
 * do app Site
 */
class EventoService
{

    /**
     * Criar Evento se não houver cadastrado
     * Utilizado para o primeiro acesso ao APP Site
     *
     * @param string $sinodalId
     * @return Evento|null
     */
    public static function criarEvento(Sinodal $sinodal): ?Evento
    {
        try {
            return Evento::create([
                'sinodal_id' => $sinodal->id,
                'path_arte_1' => 'https://placehold.co/1995x525',
                'form' => [
                    0 => [
                        'tipo' => 'text',
                        'campo' => 'Nome',
                        'input' => str_replace('%_nome_%', 'nome', FormularioEventoHelper::INPUTS['text'])
                    ]
                ]
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Método para atualizar as configurações da página de eventos
     *
     * @param string $eventoId
     * @param array $request
     * @return void
     */
    public static function atualizarConfig(string $eventoId, array $request)
    {
        try {
            $evento = Evento::find($eventoId);
            $pathArte1 = $evento->path_arte_1 ?? null;

            $pathArte1 = self::salvarArte($request, $pathArte1, 'arte_evento_principal', $evento->sinodal_id);
            $form = self::processarForm($request['form']);
            $evento->update([
                "nome" => $request['nome'],
                "data_inicio" => $request['data_inicio'],
                "data_fim" => $request['data_fim'],
                "descricao" => $request['descricao'],
                "form" => $form,
                "path_arte_1" => $pathArte1,
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Método para salvar a imagem enviada e apagar a cadastrada se houver
     *
     * @param array $request
     * @param [type] $pathArte
     * @param [type] $chave
     * @param [type] $sinodalId
     * @return void
     */
    public static function salvarArte(array $request, $pathArte, $chave, $sinodalId)
    {
        if (!isset($request[$chave])) {
            return $pathArte;
        }
        if (!is_null($pathArte)) {
            Storage::delete(
                str_replace(
                    'storage',
                    'public',
                    $pathArte
                )
            );
        }
        $nome = time().'.'.$request[$chave]->getClientOriginalExtension();
        $path = $request[$chave]->storeAs("/public/sinodais/{$sinodalId}/eventos", $nome);

        return str_replace(
            'public',
            'storage',
            $path
        );
    }

    /**
     * Monta os tipos de campos para salvar a config de form
     *
     * @param array $request
     * @return void
     */
    public static function processarForm(array $request)
    {
        $form = [];
        foreach ($request as $item) {
            if ($item['tipo'] == FormularioEventoHelper::REMOVER) {
                continue;
            }
            $form[] = [
                'tipo' => $item['tipo'],
                'campo' => $item['campo'],
                'option' => $item['option'],
                'input' => FormularioEventoHelper::mount(
                    $item['tipo'],
                    $item['campo'],
                    $item['option']
                )
            ];
        }
        return $form;
    }

    /**
     * Método responsável por salvar a inscrição no evento da sinodal
     *
     * @param Evento $evento
     * @param array $request
     * @return void
     */
    public static function inscricao(Evento $evento, array $request)
    {
        EventoInscrito::create([
            'evento_id' => $evento->id,
            'informacoes' => $request
        ]);
    }

    /**
     * Atualizar status do evento
     *
     * @param string $eventoId
     * @return void
     */
    public static function updateStatus(string $eventoId)
    {
        $evento = Evento::find($eventoId);
        $evento->update([
            'status' => !$evento->status
        ]);
    }

    public static function limparConfig(string $eventoId)
    {
        $evento = Evento::find($eventoId);
        $evento->update([
            'status' => 0,
            'path_arte_1' => 'https://placehold.co/1995x525',
            'nome' => null,
            'data_inicio' => null,
            'data_fim' => null,
            'descricao' => null,
            'form' => [
                0 => [
                    'tipo' => 'text',
                    'campo' => 'Nome',
                    'input' => str_replace('%_nome_%', 'nome', FormularioEventoHelper::INPUTS['text'])
                ]
            ]
        ]);
    }

    /**
     * Método para gerar a lista de inscritos com as informações
     * de acordo com os campos atuais do formulário
     *
     * @param string $eventoId
     * @return void
     */
    public static function dataTableInscritos(string $eventoId)
    {
        try {

        $inscritos = EventoInscrito::where('evento_id', $eventoId);
        $forms =  data_get(Evento::find($eventoId)->form, '*.campo');
        $cabecalhos = array_map(function ($item) {
            return FormularioEventoHelper::formatName($item);
        }, $forms);

        return $inscritos->get()
            ->map(function ($item) use ($cabecalhos) {
                $dados = [];
                $dados['id'] = $item->id;
                foreach ($cabecalhos as $c) {
                    $dados[$c] = isset($item['informacoes'][$c])
                        ? $item['informacoes'][$c]
                        : 'Não preenchido';
                }
                $dados['status'] = $item->status_formatado;
                return $dados;
            })
            ->toArray();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Método para inverter o status do inscrito
     *
     * @param string $eventoId
     * @param integer $inscritoId
     * @return string
     */
    public static function statusInscrito(string $eventoId, int $inscritoId): string
    {
        $inscrito = EventoInscrito::where('evento_id', $eventoId)
            ->where('id', $inscritoId)
            ->first();
        $inscrito->update([
            'status' => !$inscrito->status
        ]);

        return $inscrito->status_formatado;
    }

    /**
     * Método para remover o inscrito
     *
     * @param string $eventoId
     * @param integer $inscritoId
     * @return void
     */
    public static function removerInscrito(string $eventoId, int $inscritoId)
    {
        EventoInscrito::where('evento_id', $eventoId)
            ->where('id', $inscritoId)
            ->first()
            ->delete();
    }

    public static function limparLista(string $eventoId)
    {
        $inscritos = EventoInscrito::where('evento_id', $eventoId)->get();
        foreach ($inscritos as $inscrito) {
            $inscrito->delete();
        }
    }

}
