<?php

namespace App\Exports;

use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\Pesquisas\Pesquisa;
use App\Services\Apps\TesourariaService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TesourariaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected DateTime $dataInicio;
    protected DateTime $dataFim;
    protected array $totalizadores;

    public function __construct(array $filtros)
    {
        $datas = explode(' - ', $filtros['periodo']);
        $this->dataInicio = Carbon::createFromFormat('d/m/Y', $datas[0]);
        $this->dataFim = Carbon::createFromFormat('d/m/Y', $datas[1]);

        $this->totalizadores = TesourariaService::totalizadores([
            'dataInicio' => $datas[0],
            'dataFim' => $datas[1]
        ]);
    }

    /**
    * @var Lancamento $invoice
    */
    public function map($lancamento): array
    {
        $valor = $lancamento['valor_cru'];
        $entrada = $lancamento['tipo'] == Lancamento::TIPO_ENTRADA
            ? number_format($valor, 2, ',', '.')
            : '';
        $saida = $lancamento['tipo'] == Lancamento::TIPO_SAIDA
            ? number_format(($valor * -1), 2, ',', '.')
            : '';

        return [
            !is_null($lancamento['data_lancamento'])
                ? Carbon::parse($lancamento['data_lancamento'])->format('d/m/Y')
                : '',
            $lancamento['descricao'],
            $entrada,
            $saida,
            $lancamento['comprovante']
        ];
    }

    public function collection()
    {
        $lancamentos = Lancamento::whereBetween('data_lancamento', [$this->dataInicio, $this->dataFim])
            ->orderBy('data_lancamento', 'asc')
            ->get()
            ->map(function ($item) {
                $item->valor_cru = $item->getRawOriginal('valor');
                return $item;
            })
            ->toArray();

        $lancamentos[] = [
            'data_lancamento' => null,
            'descricao' => 'SALDO FINAL',
            'valor_cru' => FormHelper::converterParaFloat($this->totalizadores['saldoFinal']),
            'comprovante' => null,
            'tipo' => Lancamento::TIPO_ENTRADA
        ];

        return collect($lancamentos);
    }
    public function headings(): array
    {
        return [
            [
                'DATA',
                'HISTÓRICO',
                'ENTRADA',
                'SAIDA',
                'COMPROVANTE'
            ],
            [
                '',
                'SALDO INICIAL',
                $this->totalizadores['saldoInicial'],
                '',
                ''
            ]
        ];
    }

}
