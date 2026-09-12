<script>
(function($) {
    function feInt(name) {
        var el = document.querySelector('#formulario_ump [name="' + name + '"]');
        return parseInt(el && el.value, 10) || 0;
    }

    function feTotalSocios() {
        return feInt('perfil[ativos]') + feInt('perfil[cooperadores]');
    }

    function feIconOk() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>';
    }

    function feIconBad() {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>';
    }

    function feSetBox(id, ok, okMsg, badMsg) {
        var box = document.getElementById(id);
        if (!box) {
            return;
        }
        box.classList.toggle('is-bad', !ok);
        box.innerHTML = (ok ? feIconOk() : feIconBad()) + '<span>' + (ok ? okMsg : badMsg) + '</span>';
    }

    window.feCheckConsistency = function() {
        if (!document.querySelector('#formulario_ump [name="perfil[ativos]"]')) {
            return;
        }

        var total = feTotalSocios();
        var idade = feInt('perfil[menor19]') + feInt('perfil[de19a23]') + feInt('perfil[de24a29]') + feInt('perfil[de30a35]');
        var genero = feInt('perfil[homens]') + feInt('perfil[mulheres]');
        var escolaridade = feInt('escolaridade[fundamental]') + feInt('escolaridade[medio]') + feInt('escolaridade[tecnico]') + feInt('escolaridade[superior]') + feInt('escolaridade[pos]');
        var estadoCivil = feInt('estado_civil[solteiros]') + feInt('estado_civil[casados]') + feInt('estado_civil[divorciados]') + feInt('estado_civil[viuvos]');
        var filhos = feInt('estado_civil[filhos]');

        feSetBox(
            'fe-consist-idade',
            idade === total,
            'Total por faixa etária confere com o total de sócios (' + idade + '/' + total + ')',
            'Total por faixa etária (' + idade + ') não confere com o total de sócios (' + total + ')'
        );
        feSetBox(
            'fe-consist-genero',
            genero === total,
            'Total por gênero confere com o total de sócios (' + genero + '/' + total + ')',
            'Total por gênero (' + genero + ') não confere com o total de sócios (' + total + ')'
        );
        feSetBox(
            'fe-consist-escolaridade',
            escolaridade === total,
            'Total por escolaridade confere com o total de sócios (' + escolaridade + '/' + total + ')',
            'Total por escolaridade (' + escolaridade + ') não confere com o total de sócios (' + total + ')'
        );
        feSetBox(
            'fe-consist-estado-civil',
            estadoCivil === total,
            'Total por estado civil confere com o total de sócios (' + estadoCivil + '/' + total + ')',
            'Total por estado civil (' + estadoCivil + ') não confere com o total de sócios (' + total + ')'
        );
        feSetBox(
            'fe-consist-filhos',
            filhos <= total,
            'Quantidade de sócios com filhos dentro do limite (' + filhos + '/' + total + ')',
            'Sócios com filhos (' + filhos + ') ultrapassa o total de sócios (' + total + ')'
        );
    };

    $(document).on('click', '#formulario_ump .fe-stepper-btn', function(e) {
        e.preventDefault();
        var $input = $(this).closest('.fe-stepper').find('input');
        if ($input.prop('readonly') || $input.prop('disabled')) {
            return;
        }
        var delta = parseInt($(this).data('fe-step'), 10) || 0;
        var val = parseInt($input.val(), 10) || 0;
        val = Math.max(0, val + delta);
        $input.val(val).trigger('input').trigger('change');
    });

    $(document).on('input change', '#formulario_ump input[type="number"]', function() {
        if (typeof window.feCheckConsistency === 'function') {
            window.feCheckConsistency();
        }
    });

    $(document).on('click', '#formulario_ump .fe-hint', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = $(this);
        $('#formulario_ump .fe-hint').not($this).removeClass('is-open');
        $this.toggleClass('is-open');
    });

    $(document).on('click', function(e) {
        if ($(e.target).closest('#formulario_ump .fe-hint').length) {
            return;
        }
        $('#formulario_ump .fe-hint').removeClass('is-open');
    });

    function feInitWizard() {
        var $wizard = $('#formulario_ump .fe-wizard');
        if (!$wizard.length) {
            return;
        }

        var $panels = $wizard.find('.fe-step-panel');
        var $steps = $wizard.find('.fe-wstep');
        var total = $panels.length;
        var current = parseInt($wizard.data('fe-start'), 10) || 0;
        if (current < 0 || current >= total) {
            current = 0;
        }

        function goStep(i) {
            if (i < 0 || i >= total) {
                return;
            }
            current = i;
            $panels.removeClass('is-active').eq(i).addClass('is-active');
            $steps.removeClass('is-active is-done').each(function(idx) {
                var $step = $(this);
                if (idx < i) {
                    $step.addClass('is-done');
                    $step.find('.fe-num').text('✓');
                } else {
                    $step.find('.fe-num').text(idx + 1);
                }
                if (idx === i) {
                    $step.addClass('is-active');
                }
            });
            $('#fe-step-num').text(i + 1);
            $('#fe-step-total').text(total);
            $('#fe-progress-fill').css('width', Math.round(((i + 1) / total) * 100) + '%');
            var last = i === total - 1;
            $('#fe-next').toggleClass('fe-hidden', last);
            $('#fe-submit').toggleClass('fe-hidden', !last);
            $('#fe-prev').prop('disabled', i === 0);
        }

        $steps.on('click', function() {
            goStep(parseInt($(this).data('fe-step-index'), 10));
        });
        $('#fe-next').on('click', function() {
            goStep(current + 1);
        });
        $('#fe-prev').on('click', function() {
            goStep(current - 1);
        });

        goStep(current);
        window.feCheckConsistency();
    }

    $(feInitWizard);
})(jQuery);
</script>
