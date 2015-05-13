<?php
use app\controllers\PictureCheckController;
?>
<!DOCTYPE HTML>
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
<link href="/date/css/bootstrap-combined.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="/date/bootstrap-datetimepicker.min.css">
<link href="assets/css/docs.css" rel="stylesheet">
</head>

<body>

<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <li><a href="/picfeedback/check">图片</a></li>
        <li><a href="/feedback/check">文字</a></li>
        <li class="active"><a href="/picfeedback/his">图片历史</a></li>
        <li><a href="/feedback/his">文字历史</a></li>
    </ul>
</div>
<form method="get" action="<?= $url ?>">
    <div class="row">
        <div class="span1"> 
            <label style="text-align:center; margin-top:5px; float:right;">审核人:</label>
        </div>
        <div class="span2">
            <select style="max-width:100px;">
            <?php
                $selected = isset($checkperson) && $checkperson ? "" : 'selected="selected"';
                echo '<option '. $selected . 'value = "0">人工审核</option>';
                foreach($userlist as $id => $name) {
                    $selected = isset($checkperson) && $checkperson == $id;
                    $selected = $selected ? 'selected="selected"' : "";
                    echo '<option '. $selected . 'value="'.$id.'">'.$name.'</option>';
                }    
            ?>
            </select>
        </div>
        <div class="span1">
            <label style="text-align:center; margin-top:5px; float:right;">开始时间:</label>
        </div>
        <div class="span2">
            <div id="begintime" class="input-append date">
                <input type="text" name="begintime" style="max-width:125px; height:30px;"></input>
                <span class="add-on" style="height:30px;">
                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                </span>
            </div>
        </div>
        <div class="span1">
            <label style="text-align:center; margin-top:5px; float:right;">结束时间:</label>
        </div>
        <div class="span2">
            <div id="endtime" class="input-append date">
                <input type="text" name="endtime" style="max-width:125px; height:30px;"></input>
                <span class="add-on" style="height:30px;">
                    <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
                </span>
            </div>
        </div>
        <div class="span1">
            <input class="btn" type="submit" value="查询"/>
        </div>
    </div>
</form>
<table class="table table-hover">
    <thead>
        <tr>
            <th style="text-align: center;">图片</th>
            <th style="width:90px; text-align: center;">审核操作</th>
            <th style="width:90px; text-align: center;">审核人</th>
            <th style="width:150px; text-align: center;">审核时间</th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($list as $value) {
        extract($value);
        $pass = $status == PictureCheckController::STATUS_PASS ? true : false;
        $bg = $pass ? "#fff" : "#ffe4e5";
        $status = $pass ? "审核通过" : "审核禁止";
        $person = isset($userlist[$checkperson]) ? $userlist[$checkperson] : "机器审核";
        $html = '<tr><td style="text-align:center; background:'.$bg.'"><img style="max-height: 500px; min-height: 300px;" class="img-rounded" src="'.$url.'"></td>'.
                '<td style="text-align: center;">'.$status.'</td>'.
                '<td style="text-align: center;">'.$person.'</td>'.
                '<td style="text-align: center;">'.date('Y-m-d H:i:s',$checktime).'</td></tr>';
        echo $html;
    }?>
    </tbody>
</table>
</body>
<script type="text/javascript"
    src="/date/jquery.min.js">
</script>
<script type="text/javascript"
    src="/date/bootstrap.min.js">
</script>
<script type="text/javascript"
    src="/date/bootstrap-datetimepicker.min.js">
</script>
<script type="text/javascript">
    $('#begintime').datetimepicker({
        format: 'yyyy-MM-dd hh:mm',
        language: 'zh-CN',
        pickDate: true,
        pickTime: true,
        hourStep: 1,
        minuteStep: 15,
        secondStep: 30,
        inputMask: true
    });
    $('#endtime').datetimepicker({
        format: 'yyyy-MM-dd hh:mm',
        language: 'zh-CN',
        pickDate: true,
        pickTime: true,
        hourStep: 1,
        minuteStep: 15,
        secondStep: 30,
        inputMask: true
    });
</script>
</html>
