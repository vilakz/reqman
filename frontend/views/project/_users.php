<?php

/* @var $this yii\web\View */

use Yii;
use kartik\grid\GridView;
use yii\helpers\Html;

/* @var $model common\models\Project */

$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => $model->getUsers(),
    'sort' => false,
    'pagination' => [
        'pageSize' => 1000,
    ],
]);
$projectId = $model->id;
$gridId = "usersProject{$model->id}";
?>
<div>Пользователи :</div>
<?= GridView::widget([
    'id' => $gridId,
    'dataProvider' => $dataProvider,
    'summary' => false,
    'columns' => [
        [
            'attribute' => 'email',
        ],
        [
            'class' => 'kartik\grid\EditableColumn',
            'attribute' => 'projectRights',
            'label' => 'Доступ',
            'refreshGrid' => false,
            'content' => function ($modelUser) {
                $roles = Yii::$app->authManager->getRolesByUser($modelUser->id);
                return implode(';', \yii\helpers\ArrayHelper::getColumn($roles, 'name'));
            },
            'editableOptions' => [
                'placement' => 'top top-left',
                'inputType' => \kartik\editable\Editable::INPUT_RADIO_LIST,
                'data' => \common\models\User::getProjectTypeList(),
                'formOptions' => ['action' => ['/project/edit-user-rights']],
                'options' => ['class' => 'noIcheck'],
                'pjaxContainerId' => $gridId,
            ],
            'visible' => (Yii::$app->user->can('administartor') || Yii::$app->user->can('projectAdmin')),
        ],
        [
            'label' => 'Доступ',
            'value' => function($modelUser) {
                $roles = Yii::$app->authManager->getRolesByUser($modelUser->id);
                return implode(';', \yii\helpers\ArrayHelper::getColumn($roles, 'name'));
            },
            'visible' => !(Yii::$app->user->can('administartor') || Yii::$app->user->can('projectAdmin')),
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) use ($projectId, $gridId) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        [
                            '/project/unset-user',
                            'id' => $projectId,
                            'email' => $model->email,
                        ]
                        ,
                        [
                            'title' => 'Удалить пользователя из проекта',
                            'aria-label' => 'Удалить пользователя из проекта',
                            'data' => [
                                'pjax' => "0",
                                'projectId' => $projectId,
                                'userEmail' => $model->email,
                            ],
                            'class' => 'js-project-delete-user',
                        ]
                    );
                },
            ],
            'visibleButtons' => [
                'delete' => \Yii::$app->user->can('projectAdmin'),
            ],
        ],
    ],
])?>
