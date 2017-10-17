<?php

return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
        'POST projects/add/<id:\d+>' => 'project/add',
        'POST projects/unset-user/<id:\d+>' => 'project/unset-user',
        ['class' => 'yii\rest\UrlRule', 'controller' => 'project'],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'requirement'],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'entity'],
        'POST entities/select-path' => 'entity/select-path',
        '/' => 'site/index',
        'POST login' => 'site/token',
    ],
];