<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RequirementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Требования';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requirement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (\Yii::$app->user->can('projectEdit')) { ?>
        <?= Html::a('Добавить требование', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            [
                'attribute' => 'projectId',
                'value' => function($model) {
                    return $model->project->name;
                },
            ],

            'body:ntext',
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
