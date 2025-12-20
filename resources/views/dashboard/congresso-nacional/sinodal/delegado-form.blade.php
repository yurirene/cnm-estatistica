@if(!empty($delegado->id))
    {!! Form::model(
        $delegado,
        [
            'url' => route('dashboard.cn.sinodal.delegado.update', $delegado->id),
            'method' => 'PUT',
            'files' => true
        ]
    ) !!}
@else
    {!! Form::open([
        'url' => route('dashboard.cn.sinodal.delegado.store'),
        'method' => 'POST',
        'files' => true
    ]) !!}
@endif

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('nome', 'Nome') !!}
            {!! Form::text(
                'nome',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o nome completo'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('cpf', 'CPF') !!}
            {!! Form::text(
                'cpf',
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => 'Digite o CPF'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('telefone', 'Telefone') !!}
            {!! Form::text(
                'telefone',
                null,
                [
                    'class' => 'form-control isTelefone',
                    'required' => true,
                    'autocomplete' => 'off',
                    'placeholder' => '(99) 99999-9999'
                ]
            ) !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('cpf', 'Oficial') !!}
            {!! Form::select(
                'oficial',
                [
                    '0' => 'Não',
                    '1' => 'Diácono',
                    '2' => 'Presbítero'
                ],
                null,
                [
                    'class' => 'form-control',
                    'required' => true,
                    'autocomplete' => 'off',
                    'readonly' => !empty($delegado->id) && $delegado->credencial == 1
                ]
            ) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('comissoes', 'Preferencias de Comissões') !!}
            {!! Form::select(
                'comissoes[]',
                [
                    'relatorios_gestao' => 'Relatórios da Gestão (Diretoria e Secretariado)',
                    'planejamento_estrategico' => 'Planejamento Estratégico',
                    'gtsi' => 'GTSI',
                    'atas' => 'Registro de Atos'
                ],
                !empty($delegado->comissoes) ? $delegado->comissoes : null,
                [
                    'class' => 'form-control isSelect2 select2-comissoes',
                    'multiple' => true,
                    'id' => 'comissoes'
                ]
            ) !!}
            <small class="form-text text-muted">Selecione no máximo 2 opções</small>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('credencial_file', 'Credencial (PDF ou Imagem)') !!}
            {!! Form::file(
                'credencial_file',
                [
                    'class' => 'form-control',
                    'accept' => 'application/pdf,image/*'
                ]
            ) !!}
            <small class="form-text text-muted">Apenas para novos uploads. Formatos aceitos: PDF, JPG, PNG (máx. 2MB)</small>
        </div>
    </div>
</div>

<div class="row">

    @if(!empty($delegado->path_credencial))
        <div class="col-md-6">
            <a href="/{{ $delegado->path_credencial }}" target="_blank" class="link mt-3">
                <i class="fas fa-eye"></i> Ver credencial atual
            </a>
        </div>
    @endif
</div>
<div class="row mt-3">
    <div class="col-md-6">
        <div class="form-group">
            <button class="btn btn-success">
                {{ !empty($delegado->id) ? 'Atualizar Delegado' : 'Cadastrar Delegado' }}
            </button>
            <a href="{{ route('dashboard.cn.sinodal.index') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </div>
    </div>
</div>
{!! Form::close() !!}

@push('js')
<script>
    $(document).ready(function() {
        var $comissoes = $('#comissoes');
        
        $comissoes.on('select2:select', function (e) {
            var selectedValues = $(this).val() || [];
            
            if (selectedValues.length > 2) {
                // Remove a última seleção adicionada
                selectedValues.pop();
                $(this).val(selectedValues).trigger('change');
                
                if (typeof iziToast !== 'undefined') {
                    iziToast.warning({
                        title: 'Atenção!',
                        message: 'Você pode selecionar no máximo 2 opções.',
                        position: 'topRight'
                    });
                } else {
                    alert('Você pode selecionar no máximo 2 opções.');
                }
            }
        });
    });
</script>
@endpush
