<!DOCTYPE html>
<html lang="en">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">


    <script>
      MLS_ADMIN_URL = '<?php echo MLS_ADMIN_URL;?>';
      MLS_SOURCE_URL = '<?php echo MLS_SOURCE_URL;?>';
    </script>

    <title><?php echo $title?></title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Timeline CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/plugins/timeline.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/plugins/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <link href="<?php echo MLS_SOURCE_URL; ?>/mls_admin/css/trust_level.css" rel="stylesheet">

    <!-- jQuery Version 1.11.0 -->
    <script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/jquery-1.8.3.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php
    if ( isset($js) && $js != '')
    {
        echo $js;
    }

    if ( isset($css) && $css != '')
    {
        echo $css;
    }
    ?>
</head>

<body>
