<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?= $form->field($model, 'pic')->fileInput() ?>
    <input type="submit" />
<?php ActiveForm::end() ?>
<img src='../image/1.jpg'>
<script  type="text/javascript">
var x = document.createElement('input');
x.setAttribute('type', 'file');
x.setAttribute('onchange', 'fileChange()');

function fileChange()
{
    xmlhttp=new XMLHttpRequest();
    xmlhttp.open('GET', 'http://localhost/~trylen/lnmp/web/index.php?r=site/ajax', true);
    xmlhttp.send();
    var ret = xmlhttp.responseText;
    alert(xmlhttp.responseText);
}
</script>
