<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Entity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'projectId')->dropDownList(Yii::$app->user->identity->getProjectUserList(), ['prompt'=>'Выберите проект']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <? //= $form->field($model, 'createdAt')->textInput(['maxlength' => true]) ?>

    <? //= $form->field($model, 'updatedAt')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
