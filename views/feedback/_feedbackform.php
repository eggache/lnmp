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
    <?= $form->field($feedback, 'score')->textInput(['value'   => 1]) ?>
    <?= $form->field($feedback, 'comment')->textInput(['maxlength' => 512, 'value' => '本来我们团购的！进去前台说不能团，正说走了就假装说给我们打折（不让人团购）醉了，按摩房那狗屎颜色！我tm差点吐，靠，说他好的要不就是亲戚要不就是托吧，按了30分钟（本来90分钟）果断受不了，找另一家了，真的不来了']) ?>
    <?= Html::activeHiddenInput($feedback, 'has_pic', ['value' => 1]) ?>
    <?= Html::activeHiddenInput($feedback, 'picids', ['value' => 1]) ?>
    <?= Html::fileInput('pic') ?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Upload', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>
