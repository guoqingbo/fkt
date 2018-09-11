<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>二手房运营管理系统</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="<?php echo MLS_SOURCE_URL;?>/mls_admin/font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Sign In</h3>
                    </div>
                    <div class="panel-body">

                        <form action="" method="post" role="form" id="loginform">
                            <fieldset>
                                <div class="form-group">
                                    <input type="email" name="username" placeholder="Username" value="<?php if(!empty($_COOKIE['mls_admin_name'])){echo $_COOKIE['mls_admin_name'];}?>" size="20" class="form-control" autofocus/>
                                </div>
                                <div class="form-group">
									<input type="password" placeholder="Password" name="password" value="<?php if(!empty($_COOKIE['mls_admin_password'])){echo $_COOKIE['mls_admin_password'];}?>" size="20" class="form-control" />
                                </div>
                                <div class="login_message"><?php if(isset($mess_error)) echo $mess_error;?></div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me" <?php if(!empty($_COOKIE['mls_admin_name'])){?>checked="checked"<?php }?>>Remember Me
                                    </label>
                                </div>
                                <a href="javascript:void(0);" id="sub" class="btn btn-lg btn-success btn-block">Login</a>
								<input type="hidden" name="submit_flag" value="login" />
                            </fieldset>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery Version 1.11.0 -->
    <script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/jquery-1.8.3.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/plugins/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/sb-admin-2.js"></script>

	<script>
	$('#sub').click(function(){

	$('#loginform').submit();

	});
	</script>

</body>

</html>
