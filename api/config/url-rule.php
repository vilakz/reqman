<?php

return [
    'POST projects/add/<id:\d+>' => 'project/add',
    'POST projects/unset-user/<id:\d+>' => 'project/unset-user',
    ['class' => 'yii\rest\UrlRule', 'controller' => 'project'],
    ['class' => 'yii\rest\UrlRule', 'controller' => 'requirement'],
    ['class' => 'yii\rest\UrlRule', 'controller' => 'entity'],
    'POST entities/select-path/<id:\d+>' => 'entity/select-path',
    '/' => 'site/index',
    'POST login' => 'site/token',
];