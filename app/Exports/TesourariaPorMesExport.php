<?php

namespace App\Exports;

use App\Helpers\FormHelper;
use App\Models\Apps\Tesouraria\Lancamento;
use App\Models\Pesquisas\Pesquisa;
use App\Services\Apps\TesourariaService;
use Carbon\Carbon;
use DateTime;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TesourariaPorMesExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithCustomStartCell,
    WithEvents,
    ShouldAutoSize
{
    protected DateTime $dataInicio;
    protected DateTime $dataFim;
    protected array $totalizadores;
    protected string $baseUrl;
    protected string $mes;
    protected bool $comComprovante;

    public function __construct(
        DateTime $dataInicio,
        DateTime $dataFim,
        string $mes,
        bool $comComprovante = false
    ) {
        $this->dataInicio = $dataInicio;
        $this->dataFim = $dataFim;
        $this->mes = $mes;
        $this->baseUrl = config('app.url');
        $this->comComprovante = $comComprovante;

        $this->totalizadores = TesourariaService::totalizadores([
            'dataInicio' => $dataInicio->format('d/m/Y'),
            'dataFim' => $dataFim->format('d/m/Y')
        ]);
    }

    public function map($lancamento): array
    {
        $valor = $lancamento['valor_cru'];
        $entrada = !empty($lancamento['tipo']) && $lancamento['tipo'] == Lancamento::TIPO_ENTRADA
            ? "R$ " . number_format($valor, 2, ',', '.')
            : '';
        $saida = !empty($lancamento['tipo']) && $lancamento['tipo'] == Lancamento::TIPO_SAIDA
            ? "R$ " . number_format(($valor * -1), 2, ',', '.')
            : '';
        $dataLancamento = !is_null($lancamento['data_lancamento'])
            ? Carbon::parse($lancamento['data_lancamento'])->format('d/m/Y')
            : '';

        $retorno = [
            $dataLancamento,
            $lancamento['descricao'],
            $entrada,
            $saida
        ];

        if ($this->comComprovante) {
            $retorno[] = $lancamento['url_comprovante'];
        }

        return $retorno;
    }

    public function collection()
    {
        $baseUrl = $this->baseUrl;
        $lancamentos = Lancamento::whereBetween('data_lancamento', [$this->dataInicio, $this->dataFim])
            ->orderBy('data_lancamento', 'asc')
            ->get()
            ->map(function ($item) use ($baseUrl) {
                $item->valor_cru = $item->getRawOriginal('valor');
                $item->url_comprovante = !empty($item->comprovante)
                    ? "{$baseUrl}/{$item->comprovante}"
                    : '';
                return $item;
            })
            ->toArray();

        if (count($lancamentos) < 10) {
            for ($i = 0; $i < 10; $i++) {
                $lancamentos[] = [
                    'data_lancamento' => null,
                    'descricao' => '',
                    'valor_cru' => '',
                    'url_comprovante' => '',
                    'tipo' => ''
                ];
            }
        }

        $lancamentos[-1] = [
            'data_lancamento'=> '',
            'descricao'=> 'SALDO INICIAL',
            'valor_cru'=> FormHelper::converterParaFloat($this->totalizadores['saldoInicial']),
            'url_comprovante'=> '',
            'tipo'=> Lancamento::TIPO_ENTRADA
        ];

        ksort($lancamentos);

        return collect($lancamentos);
    }

    public function headings(): array
    {
        $retorno = [
            'DATA',
            'HISTÓRICO',
            'ENTRADA',
            'SAÍDA'
        ];

        if ($this->comComprovante) {
            $retorno[] = 'COMPROVANTE';
        }

        return $retorno;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return ucfirst($this->mes);
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        $totalizadores = $this->totalizadores;

        return [
            AfterSheet::class => function(AfterSheet $event) use ($totalizadores) {
                $sheet = $event->sheet;

                $sheet->mergeCells("A1:D1");
                $sheet->setCellValue('A1', "MOVIMENTO DO CAIXA - {$this->mes}");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $styleArrayFinal = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ];

                $cellRange = "A1:D2"; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);


                $ultimaLinhaRegistro = $ultimaLinha = $sheet->getHighestRow();
                $event->sheet->getDelegate()->getStyle("C3:D{$ultimaLinha}")->applyFromArray($styleArrayFinal);

                $ultimaLinha++;
                $sheet->setCellValue("B{$ultimaLinha}", "Saldo do Mês   ");
                $sheet->setCellValue("C{$ultimaLinha}", "R$ {$totalizadores['entradas']}");
                $sheet->setCellValue("D{$ultimaLinha}", "R$ {$totalizadores['saidas']}");
                $event->sheet->getDelegate()
                    ->getStyle("B{$ultimaLinha}:D{$ultimaLinha}")
                    ->applyFromArray($styleArrayFinal);

                $ultimaLinha++;
                $sheet->setCellValue("B{$ultimaLinha}", "Saldo Anterior   ");
                $sheet->setCellValue("C{$ultimaLinha}", "R$ {$totalizadores['saldoInicial']}");
                $event->sheet->getDelegate()
                    ->getStyle("B{$ultimaLinha}:D{$ultimaLinha}")
                    ->applyFromArray($styleArrayFinal);
                $ultimaLinha++;
                $sheet->setCellValue("B{$ultimaLinha}", "Saldo Atual   ");
                $sheet->setCellValue("D{$ultimaLinha}", "R$ {$totalizadores['saldoFinal']}");
                $event->sheet->getDelegate()
                    ->getStyle("B{$ultimaLinha}:D{$ultimaLinha}")
                    ->applyFromArray($styleArrayFinal);


                $event->sheet->getStyle("A1:D{$ultimaLinhaRegistro}")
                    ->getBorders()
                    ->getOutline()
                    ->setBorderStyle(Border::BORDER_THICK);

                $primeiraLinhaTotalizador = $ultimaLinhaRegistro + 1;
                $event->sheet->getStyle("C{$primeiraLinhaTotalizador}:D{$ultimaLinha}")
                    ->getBorders()
                    ->getRight()
                    ->setBorderStyle(Border::BORDER_THICK);
                $event->sheet->getStyle("C{$primeiraLinhaTotalizador}:D{$ultimaLinha}")
                    ->getBorders()
                    ->getLeft()
                    ->setBorderStyle(Border::BORDER_THICK);


                $event->sheet->getStyle("C{$ultimaLinha}:D{$ultimaLinha}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_THICK);

                $event->sheet->getStyle("C{$ultimaLinhaRegistro}:D{$ultimaLinhaRegistro}")
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_NONE);

                $event->sheet->getDefaultRowDimension()->setRowHeight(20);

                $event->sheet->getStyle('A1:D1')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('0f243e');

                $event->sheet->getStyle('A1')
                    ->getFont()
                    ->getColor()
                    ->setARGB(Color::COLOR_WHITE);
            }
        ];
    }
}
