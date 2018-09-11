<!DOCTYPE html>
<html>
<head>
  <script>
    if (window.parent.document.getElementById('GTipsCovermainloading')) {
      window.parent.showHide();
    }
  </script>

  <script>
    MLS_URL = '<?php echo MLS_URL;?>';
    MLS_SOURCE_URL = '<?php echo MLS_SOURCE_URL;?>';
  </script>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
  <title><?php echo isset($page_title) && $page_title != '' ? $page_title . '_' : ''; ?>MLS经纪人系统</title>
  <?php
  if (isset($js) && $js != '') {
    echo $js;
  }
  if (isset($css) && $css != '') {
    echo $css;
  }
  ?>

</head>
