<div class="row">

    <div class="col-md-12">
        @if($mapeamento['campos'][$nome] == 'text')

        {!! Form::label($nome, $mapeamento['titulo'][$nome]) !!}
        <div class="input-group mb-3">
            {!! Form::text($nome, $campo, ['class' => 'form-control']) !!}
            <div class="input-group-append">
                <button class="btn btn-outline-success update"
                    data-chave="{{ $k }}"
                    data-id="{{ $nome }}"
                    type="button"
                >
                    <i class="fas fa-save"></i>
                    Atualizar
                </button>
            </div>
        </div>

        @endif
        @if($mapeamento['campos'][$nome] == 'rich')
            {!! Form::label($nome, $mapeamento['titulo'][$nome]) !!}
            {!! Form::textarea($nome, $campo, ['class' => 'form-control isSummernoteCampo', 'data-chave' => $k]) !!}
        @endif
        @if($mapeamento['campos'][$nome] == 'custom')
            {!! \App\Helpers\SiteHelper::$nome($sinodal_id, $campo, $k) !!}
        @endif
    </div>

</div>
