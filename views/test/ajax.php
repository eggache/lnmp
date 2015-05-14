<?php
$script = <<<JS
jQuery('#1').change(function(){
        alert(111);
    var zipId = $(this).val();
    jQuery.get('/test/get',function(data){
        var data = jQuery.parseJSON(data);
        alert(data);
    });
 
});
JS;
$this->registerJs($script);
?>

<select id=1>
    <option> 1 </option>
    <option> 2 </option>
</select>
