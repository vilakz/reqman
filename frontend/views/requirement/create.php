<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Requirement */

$this->title = 'Создать требование';
$this->params['breadcrumbs'][] = ['label' => 'Требования', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requirement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
