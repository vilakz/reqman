<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserOption */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-option-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if (Yii::$app->user->can('administrator')) { ?>
    <?= $form->field($model, 'userId')->dropDownList(\common\models\User::getUsersList(), ['prompt' => 'Выберите пользователя']) ?>
    <?php } ?>

    <?= $form->field($model, 'projectId')->dropDownList(Yii::$app->user->identity->getProjectUserList(), ['prompt'=>'Проект (опционально)']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?= '' //$form->field($model, 'createdAt')->textInput(['maxlength' => true]) ?>

    <?= '' //$form->field($model, 'updatedAt')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
