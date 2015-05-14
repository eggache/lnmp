<?php
?>
<html>
<head>

<link href="http://cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap.min.css" rel="stylesheet">
<link href="http://cdn.bootcss.com/bootstrap/2.3.2/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href="/date/css/bootstrap-combined.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" media="screen" href="/date/bootstrap-datetimepicker.min.css">
<link href="assets/css/docs.css" rel="stylesheet">
</head>

<body>

<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <li><a href="/feedback/stat?type=1">工作量报表</a></li>
        <li class="active"><a href="/feedback/eff?type=3">效率报表</a></li>
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
            <th style="width:10%; text-align: center;">审核人</th>
            <th style="width:10%; text-align: center;">图片审核量</th>
            <th style="width:10%; text-align: center;">图片审核效率</th>
            <th style="width:10%; text-align: center;">文字审核量</th>
            <th style="width:10%; text-align: center;">文字审核效率</th>
            <th style="width:10%; text-align: center;">审核总量</th>
            <th style="width:10%; text-align: center;">审核效率</th>
        </tr>
    </thead>
        <?php
            foreach($list as $value) {
                extract($value);
                $picpercent = $piccnt ? number_format($pictime/$piccnt) : 0;
                $textpercent = $textcnt ? number_format($texttime/$textcnt) : 0;
                echo '<tr><td style="text-align: center;">'.$checkperson.'</td>',
                    '<td style="text-align: center;">'.$piccnt.'</td>',
                    '<td style="text-align: center;">'.$picpercent.'/秒</td>';
                echo '<td style="text-align: center;">'.$textcnt.'</td>',
                    '<td style="text-align: center;">'.$textpercent.'/秒</td>';
                echo '<td style="text-align: center;">'.($textcnt+$piccnt).'</td>',
                    '<td style="text-align: center;">'.number_format(($texttime+$pictime)/($textcnt+$piccnt), 2).'/秒</td></tr>';
            }
?>
    <tbody>
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
