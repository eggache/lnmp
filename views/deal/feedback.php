<?php
use yii\widgets\LinkPager;
?>
<div class="container" >
  <h2>Deal列表</h2>
  <div class="list-group" >

<?php foreach($models as $model){
    $commentid = $model->commentid;
    $comment = $commentList[$commentid]->comment;
    echo '<h4 class="list-group-item-heading">' . $comment . '</h4>';
    echo '<p class="list-group-item-text">'. date("Y-m-d", $model->addtime) .'</p>';
}
?>
  </div>
</div>
<?= LinkPager::widget(['pagination' => $pages]) ?>
