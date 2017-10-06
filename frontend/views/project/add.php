<?php

/* @var $this yii\web\View */
/* @var $project \common\models\Project */
/* @var $result boolean */
/* @var $addUser \common\models\AddUser */
/* @var $form yii\widgets\ActiveForm */

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

$this->title = "Добавить пользователя к проекту '{$project->name}''";
?>
<div class="add-user-form">

    <?php if (true === $result) { ?>

        <h2>Пользователь успешно добавлен</h2>

    <?php } else if ($addUser->addUserMessage) { ?>

        <p>Есть проблемы при добавлении :</p>
        <p><?= $addUser->addUserMessage ?></p>

    <?php } else { ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($addUser, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($addUser, 'type')->dropDownList(\common\models\User::getProjectTypeList()) ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить' , ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php } ?>
</div>
