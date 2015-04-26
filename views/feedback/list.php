<?php
use yii\helpers\Html;
echo $title;
?>
<table class="table table-hover">
<tr><th width=20%>userid</th><th>comment</th></tr>
<?php
foreach ($list as $feedback) {
    echo '<tr><td>' . $feedback['userid'] . '</td><td>' . $feedback['comment'] . '</td></tr>';
}
?>
</table>
