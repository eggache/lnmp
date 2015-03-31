<?php
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<input type='file' name='UploadForm[file]' id=1/>
<button>Submit</button>

<?php ActiveForm::end() ?>

<script>
function postImage(){
     
}
</script>
