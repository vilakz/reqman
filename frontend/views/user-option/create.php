<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserOption */

$this->title = 'Добавить пользовательскую опцию';
$this->params['breadcrumbs'][] = ['label' => 'Пользовательские опции', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
