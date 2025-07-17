<?php

namespace App\Models\Produtos;

use App\Casts\DateCast;
use App\Casts\FileCast;
use App\Casts\MoneyCast;
use App\Services\Produtos\AuditoriaProdutosService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FluxoCaixa extends Model
{
    public string $nomeTabela = 'Fluxo Caixa';

    protected $table = 'produtos_fluxo_caixa';
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'valor' => MoneyCast::class,
        'comprovante' => FileCast::class,
        'data_lancamento' => DateCast::class
    ];
    protected $dates = ['created_at', 'updated_at'];
    public string $caminho = 'public/produtos/comprovantes';


    public function getNomeTabelaFormatada(): string
    {
        return $this->nomeTabela;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
        // create a event to happen on updating
        static::updating(function ($table) {
            $acao = "Alterado o registro #{$table->getKey()} em {$table->getNomeTabelaFormatada()}";
            AuditoriaProdutosService::store(
                $table,
                auth()->id() ?? null,
                $acao,
                session()->get('login_externo') ?? null
            );
        });

        // create a event to happen on saving
        static::created(function ($table) {
            $acao = "Criado o registro #{$table->getKey()} em {$table->getNomeTabelaFormatada()}";
            AuditoriaProdutosService::store(
                $table,
                auth()->id() ?? null,
                $acao,
                session()->get('login_externo') ?? null
            );
        });

        // create a event to happen on deleting
        static::deleting(function ($table) {
            $acao = "Apagado o registro #{$table->getKey()} em {$table->getNomeTabelaFormatada()}";
            AuditoriaProdutosService::store(
                $table,
                auth()->id() ?? null,
                $acao,
                session()->get('login_externo') ?? null
            );
        });
    }

   /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

   /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    public const SALDO_INICIAL = 0;
    public const ENTRADA = 1;
    public const SAIDA = 2;

    public const LABELS_TIPOS = [
        self::SALDO_INICIAL => 'info',
        self::ENTRADA => 'success',
        self::SAIDA => 'danger'
    ];

    public const TIPOS = [
        self::SALDO_INICIAL => 'Saldo Inicial',
        self::ENTRADA => 'Entrada',
        self::SAIDA => 'Saída'
    ];

    public const TIPOS_ATIVOS = [
        self::ENTRADA => 'Entrada',
        self::SAIDA => 'Saída'
    ];

}
