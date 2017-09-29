<?php

use kartik\typeahead\Typeahead;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Entity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="entity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'path')->widget(
        TypeAhead::className(),
        [
            'options' => [
                'placeholder' => $model->attributeLabels()['path'],
            ],
            'pluginOptions' => [
                'highlight' => true,
            ],
            'dataset' => [
                [
                    'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('path')",
                    'display' => 'path',
                    'remote' => [
                        'url' => Url::to(['select-path', 'id' => $model->id, 'word'=>'__q__']),
                        'wildcard' => '__q__',
                    ]
                ]
            ]
        ]
    ); ?>
    <?= $form->field($model, 'projectId')->dropDownList(Yii::$app->user->identity->getProjectUserList(), ['prompt'=>'Выберите проект']) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <? //= $form->field($model, 'createdAt')->textInput(['maxlength' => true]) ?>

    <? //= $form->field($model, 'updatedAt')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
