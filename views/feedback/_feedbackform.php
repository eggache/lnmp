<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Dealfeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($feedback, 'orderid')->textInput() ?>
    <?= $form->field($feedback, 'dealid')->textInput() ?>
    <?= $form->field($feedback, 'userid')->textInput() ?>
    <?= $form->field($feedback, 'comment')->textInput(['maxlength' => 512]) ?>
    <?= $form->field($picform, 'pic')->fileInput() ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
