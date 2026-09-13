<?php

return [
    'ai_provider' => env('AI_PROVIDER', 'template'),
    'ai_enabled' => (bool) env('AI_ENABLED', false),
    'catalogo_version' => '1.1',
    'modelo_template' => 'template-engine-v1',
    'tipo_analise' => 'diagnostico',
];
