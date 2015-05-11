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
    height: 570px;
    line-height: 520px;
}
</style>
<script type="text/javascript">
function changebg(id)
{
    var div = "div";
    var input = "input";
    var obj  = document.getElementById(div+id);
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
        <li class="active"><a href="/picfeedback/check">图片</a></li>
        <li><a href="/feedback/check">文字</a></li>
        <li><a href="/picfeedback/his">图片历史</a></li>
        <li><a href="/feedback/his">文字历史</a></li>
    </ul>
</div>
<div class="picture-all">
    <div class="picture-row">
        <?php
        for ($id = 0; $id < 10; $id ++) {
            echo '<div class="picture-one" id="div'.$id.'" onclick="changebg('.$id.')">';
            echo '<img src="/image/96427251c02c9390b2f28b9c1174ceab.jpg" class="img-rounded" style="max-height: 487px;max-width: 330px;">';
            echo '<input type="hidden" name="pic['.$id.']" value="pass" id = "input'.$id.'" />'.
                '<p style="margin-top:-250px; margin-left:80px;float:left">'.$id.'</p></div>';
        }?>
    </div>
</div>

</body>
</html>
