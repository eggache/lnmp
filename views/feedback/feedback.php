<div>
<?php
use yii\helpers\Html;
?>
<?= $this->render('_feedbackform', [
        'feedback'  => $feedback,
        'picform'   => $picform,
        'couponid'  => $couponid,
        'dealid'    => $dealid,
        'userid'    => $userid,
    ]);
?>
</div>
