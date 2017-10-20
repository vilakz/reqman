<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserOption */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользовательские опции', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-option-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить эту пользовательскую опцию ?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'name',
            'value:ntext',
            'createdAt',
            'updatedAt',
        ],
    ]) ?>

</div>
