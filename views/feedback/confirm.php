<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Dealfeedback */
/* @var $form yii\widgets\ActiveForm */
?>
<html>
<head>

<style type="text/css">

div.picture-all
{
    margin:auto;
}

div.picture-row
{
    margin: 0 auto;
    width: 1206px;
}
div.picture-one
{
    width: 30%;
    float: left;
    position: relative;
    border: solid 1px #999;
    text-align: center;
    height: 550px;
    line-height: 520px;
}
</style>
<script type="text/javascript">
function changebg(id)
{
    var td = "td";
    var input = "input";
    var obj  = document.getElementById(td+id);
    if (obj.style.background == "rgb(255, 228, 229)") {
        obj.style.background = "#fff";
        document.getElementById(input+id).value="pass";
    } else {
        obj.style.background="#ffe4e5";
        document.getElementById(input+id).value="ban";
    }
}
</script>
<link href="http://cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="http://cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="assets/css/docs.css" rel="stylesheet">
</head>

<body>

<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <li><a href="/picfeedback/confirm">图片</a></li>
        <li class="active"><a href="/feedback/confirm">文字</a></li>
    </ul>
</div>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th style="width:100px; text-align: center;">评价ID</th>
                <th style="width:150px; text-align: center;">项目标题</th>
                <th style="width:50px; text-align: center;">评分</th>
                <th style="text-align: center;">内容</th>
                <th style="width:150px; text-align: center;">关键词</th>
                <th style="width:90px; text-align: center;">时间</th>
            </tr>
        </thead>
        <tbody>
    <?php
        foreach ($list as $value) {
            extract($value);
            $html = '<tr><td style="text-align: center;">'.$id.'</td>'.
                    '<td>'.$title.'</td>'.
                    '<td style="text-align: center;">'.$score.'</td>'.
                    '<td onclick="changebg('.$id.')" id="td'.$id.'">'.$comment.'<input type="hidden" value="pass" name="check['.$id.']"id="input'.$id.'"/></td>'.
                    '<td style="text-align: center;">'.$keyword.'</td>'.
                    '<td style="text-align: center;">'.date('Y-m-d H:i:s',$addtime).'</td></tr>';
            echo $html;
        }?>
        </tbody>
    </table>
    <div class="span2 offset5">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
</form>
</body>
</html>
