<?php

namespace App\Models;

use App\Helpers\FormHelper;
use Illuminate\Database\Eloquent\Model;

class ValorAciAno extends Model
{
    protected $table = 'valores_aci_ano';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'ano' => 'integer',
        'valor' => 'float',
    ];

    public static function valorPara(int $ano): float
    {
        $registro = self::query()->where('ano', $ano)->first();
        if ($registro !== null) {
            return (float) $registro->valor;
        }

        $anterior = self::query()
            ->where('ano', '<=', $ano)
            ->orderByDesc('ano')
            ->first();
        if ($anterior !== null) {
            return (float) $anterior->valor;
        }

        $parametro = Parametro::where('nome', 'valor_aci')->first();

        return $parametro !== null ? self::parseValor($parametro->valor) : 0.0;
    }

    public static function sincronizar(int $ano, mixed $valor): self
    {
        return self::query()->updateOrCreate(
            ['ano' => $ano],
            ['valor' => self::parseValor($valor)]
        );
    }

    public static function parseValor(mixed $valor): float
    {
        if (is_int($valor) || is_float($valor)) {
            return (float) $valor;
        }

        $texto = trim((string) $valor);
        if ($texto === '') {
            return 0.0;
        }

        if (is_numeric($texto)) {
            return (float) $texto;
        }

        return FormHelper::converterParaFloat($texto);
    }
}
