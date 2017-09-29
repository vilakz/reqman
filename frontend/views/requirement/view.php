<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Requirement */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Требования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requirement-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (\Yii::$app->user->can('projectEdit')) { ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить это требование ?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Добавить требование', ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'projectId',
                'value' => function ($model) {
                    return $model->project->name;
                }
            ],
            'body:ntext',
            'createdAt',
            'updatedAt',
        ],
    ]) ?>

</div>
