<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Entity */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Сущности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (\Yii::$app->user->can('projectEdit')) { ?>
        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить эту сущность ?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Добавить сущность', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' => 'projectId',
                'value' => function ($model) {
                    /** @var $model \common\models\Entity */
                    $Project = $model->project;
                    return ($Project ? $Project->name : 'вне проектов');
                }
            ],
            'description:ntext',
            'createdAt',
            'updatedAt',
        ],
    ]) ?>

</div>
