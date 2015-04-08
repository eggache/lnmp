<div>
<?php
use yii\helpers\Html;
?>
<?= $this->render('_feedbackform', [
        'feedback'  => $feedback,
        'picform'   => $picform,
    ]);
?>
</div>
