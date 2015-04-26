<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
foreach ($deallist as $deal) {
    ActiveForm::begin();
    echo Html::hiddenInput('dealid', $deal->id);
    echo Html::hiddenInput('userid', 1);
    echo $deal->dealtitle;
    echo $deal->money / 10 ;
    echo Html::submitButton('买一个');
    ActiveForm::end();
}
