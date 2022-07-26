<script>
    var $fbEditor = $(document.getElementById('fb-editor'));
    var $formContainer = $(document.getElementById('fb-rendered-form'))
    var options = {
        i18n: {
            locale: 'pt-BR'
        },
        disabledAttrs: ["name", "access", "className"],
        disableFields: ['autocomplete', 'file', 'hidden', 'button'],
        disabledActionButtons: ['data', 'edit' , 'clear'],
        onSave: function(evt, formData) {
            $fbEditor.toggle();
            $formContainer.toggle();
            $('input[name=formulario]').val(JSON.stringify(formData));
        },
    };
    $('.edit-form').click(function() {
        $fbEditor.toggle();
        $formContainer.toggle();
    });

</script>