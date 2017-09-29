<?php
return [
    'language'       => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'name' => 'Управление требованиями',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache',
            'fileMode'  => 0664,
        ],
        'assetManager' => [
            'linkAssets' => true,
            //append time stamps to assets for cache busting
            'appendTimestamp' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
    ],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module'
            // enter optional module parameters below - only if you need to
            // use your own export download action or custom translation
            // message source
            // 'downloadAction' => 'gridview/export/download',
            // 'i18n' => []
        ]
    ],
];
