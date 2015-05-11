<?php
use yii\widgets\LinkPager;
?>
<div class="container" >
  <h2>Deal列表</h2>
  <div class="list-group" >

<?php foreach($models as $model){
    //var_dump($model);
    echo '<a href="/deal/feedback?dealid='. $model['id']. '" class="list-group-item">';
    echo '<h4 class="list-group-item-heading">' . $model['dealtitle'] . '</h4>';
    echo '<p class="list-group-item-text">共xxx条评价</p></a>';
}
?>
  </div>
</div>
<?= LinkPager::widget(['pagination' => $pages]) ?>
