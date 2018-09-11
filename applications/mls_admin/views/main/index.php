<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
</head>
<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<frameset rows="89,*" cols="*" frameborder="no" border="0" framespacing="0">
    <frame src="main/top" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame">
    <frameset cols="220,*" frameborder="no" border="0" framespacing="0">
        <frame src="main/left" name="leftFrame" scrolling="yes" width="100%" frameborder="0" noresize="noresize"
               id="leftFrame" title="leftFrame">
        <frame src="user/index" name="rightFrame" id="rightFrame" title="rightFrame">
    </frameset>
</frameset>
<noframes>
    <body></body>
</noframes>
</html>
