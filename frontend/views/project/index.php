<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Проекты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php if (\Yii::$app->user->can('projectAdmin')) { ?>
            <?= Html::a('Добавить проект', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
                'headerOptions' => ['class'=>'kartik-sheet-style'] ,
                'expandOneOnly' => true,
                'expandIcon' => '<span class="glyphicon glyphicon-info-sign"></span>',
                'expandTitle' => "Показать пользователей",
                'expandAllTitle' => "Показать пользователей для всех",

                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_users', ['model' => $model]);
                },
            ],
//            'createdAt',
//            'updatedAt',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{add-user} {view} {update} {delete}',
                'buttons' => [
                    'add-user' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-plus"></span>', ['add', 'id' => $model->id], [
                            'title' => 'Добавить пользователя',
                            'aria-label' => 'Добавить пользователя',
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'add-user' => \Yii::$app->user->can('projectAdmin'),
                    'view' => \Yii::$app->user->can('projectView'),
                    'update' => \Yii::$app->user->can('projectAdmin'),
                    'delete' => \Yii::$app->user->can('projectAdmin'),
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<?php
$js = <<< JSVAL
$(document).on('click', '.js-project-delete-user', function(event){
    event.preventDefault();
    var el = $(event.target).parent();
    $.ajax({
        url: '/project/unset-user?' + 'id=' + el.data('projectid') + '&email=' + el.data('useremail'),
        method: 'post',
    })
    .done(function(data){
        if (data.result) {
            el.parent().parent().remove();
        } else if (data.message) {
            alert('Ошибка, не удалось удалить пользователя.' + data.message);
        } else {
            alert('Ошибка, не удалось удалить пользователя.');
        }
    })
    .fail(function(){
        alert('Ошибка, не удалось удалить пользователя');
    })
    ;
});
JSVAL;
$this->registerJs($js);
?>