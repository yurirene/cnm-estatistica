@if($formularioComplementarSinodal->formulario != null)
<h3>Formulário Complementar Sinodal</h3>
<div class="row">
    <div class="col">
        <div id="formulario-renderizado-sinodal"></div>
    </div>
</div>
@endif

@if($formularioComplementarFederacao->formulario != null)
<h3>Formulário Complementar Federação</h3>
<div class="row">
    <div class="col">
        <div id="formulario-renderizado-federacao"></div>
    </div>
</div>
@endif

@push('js')
<script>
    jQuery(function($) {
        @if($formularioComplementarSinodal->formulario != null)
        $('#formulario-renderizado-sinodal').formRender({
            dataType: 'json',
            formData: JSON.parse(@json($formularioComplementarSinodal->formulario))
        });
            @if($formularioComplementarSinodal->resposta != null)
            let respostasSinodal = JSON.parse(@json($formularioComplementarSinodal->resposta));

            Object.keys(respostasSinodal).forEach(function(key) {
                let input = document.querySelector(`input[name="${key}"]`);

                if (input) {
                    input.value = respostasSinodal[key];
                }
            });

            @endif
        @endif

        @if($formularioComplementarFederacao != null)
        $('#formulario-renderizado-federacao').formRender({
            dataType: 'json',
            formData: JSON.parse(@json($formularioComplementarFederacao->formulario))
        });
            @if($formularioComplementarFederacao->resposta != null)
                let respostaFederacao = JSON.parse(@json($formularioComplementarFederacao->resposta));

                Object.keys(respostaFederacao).forEach(function(key) {
                    let input = document.querySelector(`input[name="${key}"]`);

                    if (input) {
                        input.value = respostaFederacao[key];
                    }
                });
            @endif
        @endif
    });
</script>
@endpush