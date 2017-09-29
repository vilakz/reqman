<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Entity */

$this->title = 'Редактирование сущности: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Сущности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="entity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
