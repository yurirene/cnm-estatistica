<script>
    $(document).ready(function() {
        let link = "{{ route('dashboard.formularios-locais.export', ':id') }}";
        let val = link.replace(':id', $('#ano option:selected').text());
        $('#link_export').attr('href', val);
    })
    $('#responder').on('click', function() {
        $('#formulario_ump').show();
        $('#resumo-card').hide();
    });
    $('#ano').on('change', function() {
        let link = "{{ route('dashboard.formularios-locais.export', ':id') }}";
        let val = link.replace(':id', $('#ano option:selected').text());
        $('#link_export').attr('href', val);
    })
    $('#visualizar').on('click', function() {
        $('#formulario_ump').hide();
        $.ajax({
            type: "POST",
            url: '{{ route("dashboard.formularios-locais.view") }}',
            data: {
                _token: '{{ csrf_token() }}',
                id: $('#ano').val()
            },
            success: function(json) {
                feRenderResumo(json.data.resumo);
            },
        });
    });
</script>
