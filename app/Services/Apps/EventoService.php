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
     * @param integer $eventoId
     * @param array $request
     * @return void
     */
    public static function atualizarConfig(int $eventoId, array $request)
    {
        try {
            $evento = Evento::find($eventoId);
            $pathArte1 = $evento->path_arte_1 ?? null;
            $pathArte2 = $evento->path_arte_2 ?? null;

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

}
