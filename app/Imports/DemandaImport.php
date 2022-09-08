<?php

namespace App\Imports;

use App\Models\Demanda;
use App\Models\DemandaItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DemandaImport implements ToCollection, SkipsEmptyRows, WithHeadingRow
{
    /**
    * @param Collection $collection
    */

    public $informacoes;
    public $data;

    public function collection(Collection $collection)
    {
        $this->data = $collection;
        $this->informacoes = $collection->unique('atividade_para')->pluck('atividade_para');
    }

}
