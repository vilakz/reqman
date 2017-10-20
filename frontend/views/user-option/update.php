<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserOption */

$this->title = 'Редактировать пользовательскую опцию: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Пользовательские опции', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="user-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
