<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserOptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользовательские опции';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-option-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить пользовательскую опцию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'userId',
                'visible' => Yii::$app->user->can('administrator'),
                'value' => function ($model) {
                    /** @var \common\models\UserOption $model */
                    return $model->user->username . ' (' . $model->user->email . ')';
                }
            ],
            [
                'attribute' => 'projectId',
                'value' => function ($model) {
                    /** @var \common\models\UserOption $model */
                    return ($model->projectId ? $model->project->name : 'нет');
                }
            ],
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    /** @var \common\models\UserOption $model */
                    $humanName = $model->getHumanName();
                    return $model->name . ($humanName ? " ($humanName)" : '');
                }
            ],
            'value:ntext',
            // 'createdAt',
            // 'updatedAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
