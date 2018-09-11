<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">用户列表</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
							<form name="search_form" method="post" action="" style='display:none;'>
						 	 <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
							  <div class="row">
							     <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>设定时间查询
									    <select name="cond1" aria-controls="dataTables-example" class="form-control input-sm">
										  <?php
										  foreach( $search['cond_arr_1'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1) && $cond1==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									    </select>
									   </label>
									   <label>
									    介于
									   <select name="cond1_down_year" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['year_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_down_year) && $cond1_down_year==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>年
									   </label>
									   <label>
									   <select name="cond1_down_month" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['month_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_down_month) && $cond1_down_month==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>月
									   </label>
									   <label>
									   <select name="cond1_down_day" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['day_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_down_day) && $cond1_down_day==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>日
								   	   </label>
									   <label>至
									   <select name="cond1_up_year" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['year_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_up_year) && $cond1_up_year==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>年
								   	   </label>
									   <label>
									   <select name="cond1_up_month" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['month_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_up_month) && $cond1_up_month==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>月
								   	   </label>
									   <label>
									   <select name="cond1_up_day" aria-controls="dataTables-example" class="form-control input-sm">
									      <?php foreach( $search['day_arr'] as $key => $value){ ?>
										  <option value="<?php echo $key;?>" <?php if(isset($cond1_up_day) && $cond1_up_day==$key){ ?>selected <?php } ?>><?php echo $value;?></option>
										  <?php } ?>
									   </select>日
								   	   </label>
									  </div>
									</div>
								</div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">

									  <label>
									   电话号码:<input type="search" name="cond_telno" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($cond_telno)){echo $cond_telno;}?>">
									  </label>
									   <input type="hidden" name="pg" value="1">
									   <input class="btn btn-primary" type="submit" value="提交">
									  </div>
									</div>
                               </form>
								</div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>
                                            <th>联系方式</th>
                                            <th>用户名</th>
                                            <th>真实姓名</th>
                                            <th>备注</th>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($user) && !empty($user)){
                                            foreach($user as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['uid'];?></td>
                                            <td><?php echo !empty($telno)?$telno:$value['telno'];?></td>
                                            <td><?php echo $value['username'];?></td>
                                            <td><?php echo $value['truename'];?></td>
                                            <td><?php echo $value['message'];?></td>
                                            <td>
                                                <?php if($value['role']==3 || $value['role']==4){?>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/manager/set/cancel/<?php echo $value['uid'];?>" >取消</a>
                                                <?php }else{?>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/manager/set/site/<?php echo $value['uid'];?>" >设置管理员</a>
                                                &nbsp;&nbsp;&nbsp;
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/manager/set/site_city/<?php echo $value['uid'];?>" >设置分站管理员</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6" style='display:none;'>
                                   <div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all"><input type="checkbox" id="sel-all">&nbsp;&nbsp;全选 &nbsp;&nbsp;<a href="javascript:void(0)"  data-target="#myModal1" data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal" data-toggle="modal" >标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal2" data-toggle="modal">标记到备选库</a>
                                   </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                       <ul class="pagination" style="margin:-8px 0;padding-left:20px">
									   										<?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
									   									 </ul>
                                    </div>
                                  </div>
                                </div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->

                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
				 <div class="col-lg-4" style="display:none" id="js_note1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            提示框
							<button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="panel-body">
                            <p id="warning_text"></p>
                        </div>
                    </div>
                </div>
<?php require APPPATH.'views/footer.php'; ?>

