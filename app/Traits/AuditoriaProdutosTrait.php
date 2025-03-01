<?php

namespace App\Traits;

use App\Services\Produtos\AuditoriaProdutosService;

trait AuditoriaProdutosTrait
{
    public function getNomeTabelaFormatada(): string
    {
        return $this->nomeTabela;
    }

    public static function boot()
    {
        parent::boot();

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
}
