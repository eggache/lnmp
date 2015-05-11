<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Dealfeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= Html::activeHiddenInput($feedback, 'couponid', ['value' => $couponid]) ?>
    <?= Html::activeHiddenInput($feedback, 'dealid', ['value' => $dealid]) ?>
    <?= Html::activeHiddenInput($feedback, 'userid', ['value' => $userid]) ?>
    <?= $form->field($feedback, 'score')->textInput() ?>
    <?= $form->field($feedback, 'comment')->textInput(['maxlength' => 512 ]) ?>
    <?= Html::activeHiddenInput($feedback, 'has_pic', ['value' => 1]) ?>
    <?= Html::activeHiddenInput($feedback, 'picids', ['value' => 1]) ?>
    <?= Html::fileInput('pic') ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
