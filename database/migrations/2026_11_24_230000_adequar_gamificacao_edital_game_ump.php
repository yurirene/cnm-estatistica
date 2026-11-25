<?php

use App\Models\Parametro;
use App\Models\Permission;
use App\Services\Gamificacao\GamificacaoConfiguracaoService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('gamificacao_placares')) {
            Schema::table('gamificacao_placares', function (Blueprint $table) {
                if (! Schema::hasColumn('gamificacao_placares', 'percentual_aci')) {
                    $table->decimal('percentual_aci', 8, 2)->default(0)->after('socios_ativos');
                }
                if (! Schema::hasColumn('gamificacao_placares', 'percentual_evangelismo')) {
                    $table->decimal('percentual_evangelismo', 8, 2)->default(0)->after('percentual_aci');
                }
            });

            DB::table('gamificacao_placares')->where('liga', 'ouro')->update(['liga' => 'simonton']);
            DB::table('gamificacao_placares')->where('liga', 'prata')->update(['liga' => 'blackford']);
            DB::table('gamificacao_placares')->where('liga', 'bronze')->update(['liga' => 'conceicao']);
        }

        if (Schema::hasTable('gamificacao_pilar_resultados')) {
            DB::table('gamificacao_pilar_resultados')->where('pilar', 'resgate')->delete();
            DB::table('gamificacao_pilar_resultados')->where('pilar', 'speed_run')->update(['pilar' => 'entrega_antecipada']);
            DB::table('gamificacao_pilar_resultados')->where('pilar', 'bonus_missionario')->update(['pilar' => 'conexao_missionaria']);
        }

        if (Schema::hasTable('gamificacao_conquistas')) {
            DB::table('gamificacao_conquistas')->where('tipo', 'resgate')->delete();
            DB::table('gamificacao_conquistas')->where('tipo', 'bonus_missionario')->update(['tipo' => 'conexao_missionaria']);
        }

        $this->renomearParametros([
            'prazo_speed_run.mes' => 'prazo_entrega_antecipada.mes',
            'prazo_speed_run.dia' => 'prazo_entrega_antecipada.dia',
            'ligas.sinodal.ouro.min' => 'ligas.sinodal.simonton.min',
            'ligas.sinodal.prata.min' => 'ligas.sinodal.blackford.min',
            'ligas.federacao.ouro.min' => 'ligas.federacao.simonton.min',
            'ligas.federacao.prata.min' => 'ligas.federacao.blackford.min',
            'pilares.speed_run.max' => 'pilares.entrega_antecipada.max',
            'pilares.bonus_missionario.max' => 'pilares.conexao_missionaria.max',
            'conquistas.bonus_missionario.pontos' => 'conquistas.conexao_missionaria.pontos',
        ]);

        Parametro::query()
            ->where('area', GamificacaoConfiguracaoService::AREA)
            ->where(function ($q) {
                $q->where('nome', 'like', 'pilares.resgate.%')
                    ->orWhere('nome', 'like', 'conquistas.resgate.%')
                    ->orWhereIn('nome', [
                        'prazo_speed_run.mes',
                        'prazo_speed_run.dia',
                        'ligas.sinodal.ouro.min',
                        'ligas.sinodal.prata.min',
                        'ligas.federacao.ouro.min',
                        'ligas.federacao.prata.min',
                        'pilares.speed_run.max',
                        'pilares.bonus_missionario.max',
                        'conquistas.bonus_missionario.pontos',
                    ]);
            })
            ->delete();

        $valores = [
            'pilares.aci.minimo_percentual' => ['80', 'ACI — % mínima do previsto'],
            'tetos.ano_inicial' => ['50', 'Teto anual — primeiro exercício (só Estatística + ACI)'],
            'tetos.sinodal' => ['110', 'Teto anual — Sinodal'],
            'tetos.federacao' => ['100', 'Teto anual — Federação'],
            'tetos.ciclo_sinodal' => ['380', 'Teto do ciclo — Sinodal'],
            'tetos.ciclo_federacao' => ['350', 'Teto do ciclo — Federação'],
            'descontos.sinodal.50' => ['209', 'Sinodal — 50%'],
            'descontos.sinodal.75' => ['266', 'Sinodal — 75%'],
            'descontos.sinodal.100' => ['323', 'Sinodal — 100%'],
            'descontos.federacao.50' => ['193', 'Federação — 50%'],
            'descontos.federacao.75' => ['245', 'Federação — 75%'],
            'descontos.federacao.100' => ['298', 'Federação — 100%'],
            'pilares.eventos.max' => ['10', 'Eventos — teto'],
            'pilares.eventos.esporadico_maximo_pontos' => ['4', 'Eventos esporádicos — teto de pontos'],
            'conquistas.pmf_oficial.pontos' => ['2', 'PMF Oficial'],
            'conquistas.pmf_parceria.pontos' => ['2', 'PMF em Parceria'],
            'conquistas.djp.pontos' => ['2', 'DJP'],
            'conquistas.esporadico.pontos' => ['2', 'Evento esporádico'],
            'pilares.entrega_antecipada.max' => ['10', 'Entrega Antecipada — máximo'],
            'pilares.conexao_missionaria.max' => ['10', 'Conexão Missionária — máximo'],
            'conquistas.conexao_missionaria.pontos' => ['10', 'Conexão Missionária'],
            'ligas.sinodal.simonton.min' => ['300', 'Mínimo Liga Simonton'],
            'ligas.sinodal.blackford.min' => ['150', 'Mínimo Liga Blackford'],
            'ligas.federacao.simonton.min' => ['85', 'Mínimo Liga Simonton'],
            'ligas.federacao.blackford.min' => ['45', 'Mínimo Liga Blackford'],
            'prazo_entrega_antecipada.mes' => ['1', 'Entrega antecipada — mês'],
            'prazo_entrega_antecipada.dia' => ['10', 'Entrega antecipada — dia'],
        ];

        foreach ($valores as $nome => [$valor, $descricao]) {
            Parametro::updateOrCreate(
                ['nome' => $nome, 'area' => GamificacaoConfiguracaoService::AREA],
                [
                    'descricao' => $descricao,
                    'valor' => $valor,
                    'tipo' => 'number',
                ]
            );
        }

        app(GamificacaoConfiguracaoService::class)->semearParametros();
        Cache::forget('gamificacao.config.overrides');

        $mapaPermissoes = [
            'dashboard.game-cnm.index' => ['dashboard.game-ump.index', 'Game UMP - Iniciar', 'secretaria-estatistica'],
            'dashboard.game-cnm.update' => ['dashboard.game-ump.update', 'Game UMP - Atualizar', 'secretaria-estatistica'],
            'dashboard.game-cnm.recalcular' => ['dashboard.game-ump.recalcular', 'Game UMP - Recalcular', 'secretaria-estatistica'],
            'dashboard.game-cnm.eventos.index' => ['dashboard.game-ump.eventos.index', 'Game UMP - Eventos', 'secretaria-estatistica'],
            'dashboard.game-cnm.eventos.store' => ['dashboard.game-ump.eventos.store', 'Game UMP - Cadastrar evento', 'secretaria-estatistica'],
            'dashboard.game-cnm.eventos.destroy' => ['dashboard.game-ump.eventos.destroy', 'Game UMP - Remover evento', 'secretaria-estatistica'],
            'dashboard.game-cnm.painel' => ['dashboard.game-ump.painel', 'Game UMP - Painel', 'game-ump-painel'],
        ];

        foreach ($mapaPermissoes as $slugAntigo => [$slugNovo, $nome, $resource]) {
            $permissao = Permission::where('slug', $slugAntigo)->first();
            if ($permissao) {
                $permissao->update([
                    'slug' => $slugNovo,
                    'name' => $nome,
                    'resource' => $resource,
                ]);
            } else {
                Permission::updateOrCreate(
                    ['slug' => $slugNovo],
                    [
                        'name' => $nome,
                        'slug' => $slugNovo,
                        'resource' => $resource,
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        // Adequação ao edital não é revertida automaticamente.
    }

    /**
     * @param  array<string, string>  $mapa
     */
    private function renomearParametros(array $mapa): void
    {
        foreach ($mapa as $antigo => $novo) {
            $parametro = Parametro::query()
                ->where('area', GamificacaoConfiguracaoService::AREA)
                ->where('nome', $antigo)
                ->first();

            if (! $parametro) {
                continue;
            }

            $existente = Parametro::query()
                ->where('area', GamificacaoConfiguracaoService::AREA)
                ->where('nome', $novo)
                ->first();

            if ($existente) {
                $existente->update(['valor' => $parametro->valor]);
                $parametro->delete();
            } else {
                $parametro->update(['nome' => $novo]);
            }
        }
    }
};
