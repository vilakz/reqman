<?php

/* @var $this yii\web\View */
/* @var $addUser common\models\AddUser */
/* @var $Project common\models\Project */
/* @var $form yii\widgets\ActiveForm */
/* @var $addUserResult boolean|string */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = "Добавить пользователя к проекту '{$Project->name}''";
?>
<div class="add-user-form">

    <?php if (true === $addUserResult) { ?>

        <h2>Пользователь успешно добавлен</h2>

    <?php } else if (is_string($addUserResult)) { ?>

        <p>Есть проблемы при добавлении :</p>
        <p><?= $addUserResult ?></p>

    <?php } else { ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->dropDownList(\common\models\User::getProjectTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить' , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php } ?>
</div>
