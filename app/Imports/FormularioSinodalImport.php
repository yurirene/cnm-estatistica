<?php

namespace App\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FormularioSinodalImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $federacoes = [];
        if (request()->filled('federacoes')) {
            foreach (request()->federacoes as $key => $federacao) {
                $federacoes[$key] = $federacao['federacao_id'];
            }
        }
        foreach ($rows as $row) {
            $dados = $this->formatarDados($row, $federacoes);
            $this->data[] = $dados;
        }
    }

    public function formatarDados(Collection $row, array $federacoes = []) 
    {
        try {
            $retorno = [
                'ano_referencia' => $row["ano_de_referencia"],
                'info_federacao' => [
                    'id' => $row["id"],
                    'sigla' => '',
                    'presbiterio' => $row["nome_do_presbiterio"],
                    'data_organizacao' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row["data_de_organizacao_da_federacao"])->format('d/m/Y'),
                    'midias_sociais' => $row["informacoes_de_contatos"],
                ],
                'estrutura' => [
                    'ump_organizada' => $row["quantidade_de_umps_organizadas_na_federacao"],
                    'ump_nao_organizada' => $row["quantidade_de_igrejas_do_presbiterio_sem_umps_organizadas"],
                ],
                'aci' => [
                    'repasse' => $row["a_federacao_fez_o_repasse_da_aci_para_a_sinodal"],
                    'ump_repassaram' => $row["quantidade_de_umps_que_fizeram_o_repasse_da_aci_para_a_federacao"],
                    'ump_nao_repassaram' => $row["quantidade_de_umps_que_nao_fizeram_o_repasse_da_aci_para_a_federacao"],
                ],
                'perfil' => [
                    'ativos' => $row["quantidade_de_socios_ativos"],
                    'cooperadores' => $row["quantidade_de_socios_cooperadores"],
                    'menor19' => $row["quantidade_de_socios_com_menos_de_19_anos"],
                    'de19a23' => $row["quantidade_de_socios_entre_19_23_anos"],
                    'de24a29' => $row["quantidade_de_socios_entre_24_29_anos"],
                    'de30a35' => $row["quantidade_de_socios_entre_30_35_anos"],
                    'homens' => $row["quantidade_de_socios_homens"],
                    'mulheres' => $row["quantidade_de_socios_mulheres"],
                ],
                'deficiencias' => [
                    'surdos' => $row["quantidade_de_socios_surdos"],
                    'auditiva' => $row["quantidade_de_socios_com_deficiencia_auditiva"],
                    'cegos' => $row["quantidade_de_socios_cegos"],
                    'baixa_visao' => $row["quantidade_de_socios_com_baixa_visao"],
                    'fisica_inferior' => $row["quantidade_de_socios_com_deficiencia_fisicamotora_em_membros_inferiores"],
                    'fisica_superior' => $row["quantidade_de_socios_com_deficiencia_fisicamotora_em_membros_superiores"],
                    'neurologico' => $row["quantidade_de_socios_com_algum_transtorno_neurologico"],
                    'intelectual' => $row["quantidade_de_socios_com_deficiencia_intelectual"],
                    'outras' => $row["ha_socios_com_deficiencias_ou_necessidade_de_acessibilidade_nao_mencionadas"],
                ],
                'estado_civil' => [
                    'solteiros' => $row["quantidade_de_socios_solteiros"],
                    'casados' => $row["quantidade_de_socios_casados"],
                    'divorciados' => $row["quantidade_de_socios_divorciados"],
                    'viuvos' => $row["quantidade_de_socios_viuvos"],
                    'filhos' => $row["quantidade_de_socios_com_filhos"],
                ],
                'escolaridade' => [
                    'fundamental' => $row["quantidade_de_socios_que_tem_ate_o_ensino_fundamental"],
                    'medio' => $row["quantidade_de_socios_que_tem_ate_o_ensino_medio"],
                    'tecnico' => $row["quantidade_de_socios_que_tem_ate_o_ensino_tecnico"],
                    'superior' => $row["quantidade_de_socios_que_tem_ate_o_ensino_superior"],
                    'pos' => $row["quantidade_de_socios_que_tem_ate_a_pos_graduacao"],
                    'desempregado' => $row["quantos_socios_desempregados"],
                ],
                'programacoes_locais' => [
                    'social' => $row["quantidade_de_programacoes_de_cunho_social"],
                    'evangelistico' => $row["quantidade_de_programacoes_de_cunho_evangelistico_e_missional"],
                    'espiritual' => $row["quantidade_de_programacoes_de_cunho_espiritual"],
                    'recreativo' => $row["quantidade_de_programacoes_de_cunho_recreativo"],
                    'oracao' => $row["quantidade_de_programacoes_de_reunioes_de_oracao_e_vigilias"],
                ],
                'programacoes' => [
                    'social' => $row["quantidade_de_programacoes_de_cunho_social2"],
                    'evangelistico' => $row["quantidade_de_programacoes_de_cunho_evangelistico_e_missional2"],
                    'espiritual' => $row["quantidade_de_programacoes_de_cunho_espiritual2"],
                    'recreativo' => $row["quantidade_de_programacoes_de_cunho_recreativo2"],
                    'oracao' => $row["quantidade_de_programacoes_de_reunioes_de_oracao_e_vigilias2"],
                ]
            ];

            if (!empty($federacoes) && isset($federacoes[$row["id"]])) {
                $retorno['federacao_id'] = $federacoes[$row["id"]];
            }
            return $retorno;
            
        } catch (\Throwable $th) {
            throw new Exception("Erro ao Importar Planilha da Sinodal", 1);
            
        }
    }
}
