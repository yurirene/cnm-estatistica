<script>
    var $fbEditor = $(document.getElementById('fb-editor'));
    var $formContainer = $(document.getElementById('fb-rendered-form'))
    var options = {
        i18n: {
            override: {
                'en-US': {
                    addOption: 'Adicionar Opção +',
                    allFieldsRemoved: 'Todos os campos serão removidos.',
                    allowMultipleFiles: 'Permitir usuário para upload de multiplos arquivos',
                    autocomplete: 'Autocompletar',
                    button: 'Botão',
                    cannotBeEmpty: 'O campo não pode ser vazio',
                    checkboxGroup: 'Grupo de checkbox',
                    className: 'Classe',
                    clearAllMessage: 'Você tem certeza que deseja limpar todos os campos?',
                    clear: 'Remover tudo',
                    close: 'Fechar',
                    content: 'Conteúdo',
                    copy: 'Copiar para área de Transferência',
                    copyButton: '&#43;',
                    copyButtonTooltip: 'Copiar',
                    dateField: 'Selecionar Data',
                    description: 'Texto de Ajuda',
                    descriptionField: 'Descrição',
                    devMode: 'Modo de Desenvolvimento',
                    editNames: 'Editar nomes',
                    editorTitle: 'Elementos do Formulário',
                    editXML: 'Editar XML',
                    enableOther: 'Ativar &quot;Outros&quot;',
                    enableOtherMsg: 'Permitir usuários entrem em lista de opções',
                    fieldNonEditable: 'Esse campo não pode ser editado.',
                    fieldRemoveWarning: 'Are you sure you want to remove this field?',
                    fileUpload: 'File Upload',
                    formUpdated: 'Form Updated',
                    getStarted: 'Drag a field from the right to this area',
                    header: 'Headerz',
                    hide: 'Edit',
                    hidden: 'Hidden Input',
                    inline: 'Inline',
                    inlineDesc: 'Display {type} inline',
                    label: 'Label',
                    labelEmpty: 'Field Label cannot be empty',
                    limitRole: 'Limit access to one or more of the following roles:',
                    mandatory: 'Mandatory',
                    maxlength: 'Max Length',
                    minOptionMessage: 'This field requires a minimum of 2 options',
                    multipleFiles: 'Multiple Files',
                    name: 'Name',
                    no: 'No',
                    noFieldsToClear: 'There are no fields to clear',
                    number: 'Number',
                    off: 'Off',
                    on: 'On',
                    option: 'Option',
                    options: 'Options',
                    optional: 'optional',
                    optionLabelPlaceholder: 'Label',
                    optionValuePlaceholder: 'Value',
                    optionEmpty: 'Option value required',
                    other: 'Other',
                    paragraph: 'Paragraph',
                    placeholder: 'Placeholder',
                    'placeholder.value': 'Value',
                    'placeholder.label': 'Label',
                    'placeholder.text': '',
                    'placeholder.textarea': '',
                    'placeholder.email': 'Enter you email',
                    'placeholder.placeholder': '',
                    'placeholder.className': 'space separated classes',
                    'placeholder.password': 'Enter your password',
                    preview: 'Preview',
                    radioGroup: 'Radiohead',
                    radio: 'Radio',
                    removeMessage: 'Remove Element',
                    removeOption: 'Remove Option',
                    remove: '&#215;',
                    required: 'Required',
                    richText: 'Rich Text Editor',
                    roles: 'Access',
                    rows: 'Rows',
                    save: 'Save',
                    selectOptions: 'Options',
                    select: 'Fabulous Dropdown',
                    selectColor: 'Select Color',
                    selectionsMessage: 'Allow Multiple Selections',
                    size: 'Size',
                    'size.xs': 'Extra Small',
                    'size.sm': 'Small',
                    'size.m': 'Default',
                    'size.lg': 'Large',
                    style: 'Style',
                    styles: {
                        btn: {
                            'default': 'Default',
                            danger: 'Danger',
                            info: 'Info',
                            primary: 'Primary',
                            success: 'Success',
                            warning: 'Warning'
                        }
                    },
                    subtype: 'Type',
                    text: 'Text Field',
                    textArea: 'Text Area',
                    toggle: 'Toggle',
                    warning: 'Warning!',
                    value: 'Value',
                    viewJSON: '{  }',
                    viewXML: '&lt;/&gt;',
                    yes: 'Yes'
                }
            }
        },
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