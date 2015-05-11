<html>
  <head>
    <link href="/date/css/bootstrap-combined.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="screen"
     href="/date/bootstrap-datetimepicker.min.css">
  </head>
  <body>
    <div id="datetimepicker" class="input-append date">
      <input type="text"></input>
      <span class="add-on">
        <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
      </span>
    </div>
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
      $('#datetimepicker').datetimepicker({
        format: 'MM/dd/yyyy hh:mm',
        language: 'en',
        pickDate: true,
        pickTime: true,
        hourStep: 1,
        minuteStep: 15,
        secondStep: 30,
        inputMask: true
      });
    </script>
  </body>
<html>

