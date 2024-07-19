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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TesourariaBalanceteExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithCustomStartCell,
    WithEvents,
    ShouldAutoSize
{
    protected int $ano;

    public function __construct(int $ano)
    {
        $this->ano = $ano;
    }


    public function collection()
    {
        $lancamentos = [];

        for($mes = 1; $mes <= 12; $mes++) {
            $dataInicio = Carbon::createFromFormat('d/m/Y', "01/{$mes}/{$this->ano}");
            $dataFim = clone $dataInicio;
            $lancamentos[$mes] = TesourariaService::totalizadores([
                'dataInicio' => $dataInicio->format('d/m/Y'),
                'dataFim' => $dataFim->lastOfMonth()->format('d/m/Y')
            ]);
            $lancamentos[$mes]['mes'] = ucfirst($dataFim->monthName);
        }

        return collect($lancamentos);
    }

    public function map($lancamento): array
    {
        return [
            $lancamento['mes'],
            'R$ ' . $lancamento['entradas'],
            'R$ ' . $lancamento['saidas'],
            'R$ ' . $lancamento['saldoFinal']
        ];
    }

    public function headings(): array
    {
        return [
            [
                'MÊS',
                'CRÉDITO',
                'DÉBITO',
                'SALDO'
            ]
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return "Balancete Geral";
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                /**
                 * @var Sheet $sheet
                 */
                $sheet = $event->sheet;
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', "BALANCETE GERAL");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                $styleArrayFinal = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ];

                $cellRange = 'A1:D2'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A3:D14')->applyFromArray($styleArrayFinal);

                $event->sheet->getStyle('A1:D1')
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('0f243e');

                $event->sheet->getStyle('A1')
                    ->getFont()
                    ->getColor()
                    ->setARGB(Color::COLOR_WHITE);

                $event->sheet->getDefaultRowDimension()->setRowHeight(20);
            }
        ];
    }
}
