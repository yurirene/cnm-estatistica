@if(!is_null($formularioComplementarSinodal) && $formularioComplementarSinodal->formulario != null)
<h4 class="fe-section-title">Formulário Complementar Sinodal</h4>
<div class="fe-complementar">
    <div id="formulario-renderizado-sinodal"></div>
</div>
@endif

@if(!is_null($formularioComplementarFederacao) && $formularioComplementarFederacao->formulario != null)
<h4 class="fe-section-title">Formulário Complementar Federação</h4>
<div class="fe-complementar">
    <div id="formulario-renderizado-federacao"></div>
</div>
@endif

@push('js')
<script>
    jQuery(function($) {
        @if(!is_null($formularioComplementarSinodal) && $formularioComplementarSinodal->formulario != null)
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

        @if(!is_null($formularioComplementarFederacao) && $formularioComplementarFederacao->formulario != null)
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
