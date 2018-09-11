        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo MLS_ADMIN_URL."/user"?>"><?=$title?></a>
                <?php if(isset($_SESSION[WEB_AUTH]['role'])&&$_SESSION[WEB_AUTH]['role']!=1){?>
                <div style="float: left; padding:15px 0 0 15px;">
                    城市：
                    <select onchange="window.location.href=<?php echo "'".MLS_ADMIN_URL."/user/change_city/'"?>+this.value;">
                        <option value="sh" <?php if('sh'==$_SESSION[WEB_AUTH]["city"]){echo 'selected="selected"';}?>>上海</option>
                        <option value="hf" <?php if('hf'==$_SESSION[WEB_AUTH]["city"]){echo 'selected="selected"';}?>>合肥</option>
                        <option value="nj" <?php if('nj'==$_SESSION[WEB_AUTH]["city"]){echo 'selected="selected"';}?>>南京</option>
                    </select>
                </div>
                <?php }?>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="<?php echo FRAME_LOGOUT;?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

           <?php require APPPATH.'views/left.php'; ?>
            <!-- /.navbar-static-side -->
        </nav>
