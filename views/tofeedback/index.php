<?php
use yii\helpers\Html;
?>
<div class="container" >
  <h2>待评价订单列表</h2>
  <div class="list-group" >

<?php foreach($tofeedback as $feedback){
    echo '<a href="/feedback/feedback?dealid='. $feedback['dealid'] . '&couponid=' . $feedback['couponid'] . '&userid=' . $feedback['userid'] . '" class="list-group-item">';
    echo '<h4 class="list-group-item-heading">' . $feedback['dealName'] . '</h4>';
    echo '<p class="list-group-item-text">你为本团购打几分？ 评价可得  <strong style="color: #b94a48">' . intval($feedback['point']/10) . '</strong>  积分</p></a>';
}
?>
  </div>
</div>
