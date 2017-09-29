<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EntitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сущности';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (\Yii::$app->user->can('projectEdit')) { ?>
        <p>
            <?= Html::a('Добавить сущность', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>
    <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'path:ntext',
            [
                'attribute' => 'projectId',
                'value' => function($model) {
                    /** @var $model \common\models\Entity */
                    $Project = $model->project;
                    return ($Project ? $Project->name : 'вне проектов');
                },
            ],

            'description:ntext',
            'createdAt',
            // 'updatedAt',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons' => [
                ],
                'visibleButtons' => [
                    'view' => \Yii::$app->user->can('projectView'),
                    'update' => \Yii::$app->user->can('projectEdit'),
                    'delete' => \Yii::$app->user->can('projectEdit'),
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
