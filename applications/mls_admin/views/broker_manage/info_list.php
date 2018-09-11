<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">经纪人诚信管理</h1>
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
							<form name="search_form" method="post" action="" >
						 	 <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
							  	<div class="row">
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;电话号码：&nbsp;<input type="text"  name="phone" id="phone" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['phone'])){echo $_POST['phone'];}?>"  onclick="add_content1()"><span id='reminder1' style='font-weight:bold;color:red;'></span>
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;姓名：&nbsp;<input type="text"  name="truename" id="truename" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['truename'])){echo $_POST['truename'];}?>"  onclick="add_content2()"><span id='reminder2' style='font-weight:bold;color:red;'></span>
								</div>
								<br>
								<div class="row">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;信用等级：
									<select name='level' style="width:183px;height:33px;">
										<option value="<?php if(isset($_POST['level']) && !empty($_POST['level'])){echo $_POST['level'];}else{echo '';}?>">
											<?php if(isset($_POST['level']) && !empty($_POST['level'])){echo $name_icon;}else{echo "全部";}?>
										</option>
										<?php if(isset($level_list) && !empty($level_list)){
											foreach ($level_list as $key => $value) { ?>
											<option value="<?php echo $value['id'];?>"><?php echo $value['name_icon'];?></option>

										<?php }}?>
									</select>
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;排序：&nbsp;
									<select name='order_name' style="width:90px;height:33px;">

										<option value="<?php if(isset($_POST['order_name']) && !empty($_POST['order_name'])){echo $_POST['order_name'];}else{echo '';}?>">
											<?php if(isset($_POST['order_name']) && !empty($_POST['order_name']) && $_POST['order_name'] == 'trust'){echo '信用分';}else if(isset($_POST['order_name']) && !empty($_POST['order_name']) && $_POST['order_name'] == 'infomation'){echo "信息真实度";}else if(isset($_POST['order_name']) && !empty($_POST['order_name']) && $_POST['order_name'] == 'attitude'){echo "合作满意度";}else if(isset($_POST['order_name']) && !empty($_POST['order_name']) && $_POST['order_name'] == 'business'){echo "业务专业度";}else{echo "请选择";}?>
										</option>
										<option value='trust'>信用分</option>
										<option value='infomation'>信息真实度</option>
										<option value='attitude'>合作满意度</option>
										<option value='business'>业务专业度</option>
									</select>
									<select name='order_way' style="width:90px;height:33px;">

										<option value="<?php if(isset($_POST['order_way']) && !empty($_POST['order_way']) && $_POST['order_way'] == 'asc'){echo 'asc';}else if(isset($_POST['order_way']) && !empty($_POST['order_way']) && $_POST['order_way'] == 'desc'){echo 'desc';}else{echo '';}?>">
											<?php if(isset($_POST['order_way']) && !empty($_POST['order_way']) && $_POST['order_way'] == 'asc'){echo '由低到高';}else if(isset($_POST['order_way']) && !empty($_POST['order_way']) && $_POST['order_way'] == 'desc'){echo "由高到低";}else{echo "请选择";}?>
										</option>

										<option value=''>不限</option>
										<option value='desc'>由高到低</option>
										<option value='asc'>由低到高</option>
									</select>
								</div>
								<br>
							   <input type="hidden" name="angela_wen" value="angel_in_us">
							   <input type="hidden" name="pg" value="1">
							   <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href='/broker_trust_manage/'" value="重置">
                            </form>
								</div>
								<br>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>电话号码</th>
                                            <th>姓名</th>
                                            <th>信用等级</th>
                                            <th>信用分</th>
                                            <th>好评率</th>
                                            <th>合作成功率</th>
                                            <th>信息真实度</th>
                                            <th>合作满意度</th>
                                            <th>业务专业度</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
										if(isset($info_list) && !empty($info_list)){
											foreach($info_list as $key=>$value){
												$value = (array)$value;
											//echo "<pre>";print_r($value);die;?>
											<tr class="gradeA">
												<td><?php echo $value['phone'];?></td>
												<td><?php if($value['truename'] != ''){ echo $value['truename'];}else{echo '暂无资料';}?></td>
												<td><?php echo $value['level'];?></td>
												<td><?php echo $value['trust'];?></td>
												<td><?php echo $value['good_rate']."%";?></td>
                                                <td><?php echo $value['cop_suc_ratio'] . "%"; ?></td>
												<td><?php echo $value['infomation'];?></td>
												<td><?php echo $value['attitude'];?></td>
												<td><?php echo $value['business'];?></td>
												<td>
													<a href="/broker_trust_manage/info_detail/<?=$value['broker_id']?>" >明细</a>
												</td>
											</tr>
										<?php }}else{echo "<tr class='gradeA'><td colspan=10 style='text-align:center;color:red;font-weight:bold;'>很抱歉，暂无您查询的经纪人诚信相关数据~！</td></tr>";}?>
                                    </tbody>
                                </table>

                                <div class="row">
                                  <div class="col-sm-6" style='display:none;'>
                                   <div class="dataTables_info" id="dataTables-example_info" role="alert" aria-live="polite" aria-relevant="all"><input type="checkbox" id="sel-all">&nbsp;&nbsp;全选 &nbsp;&nbsp;<a href="javascript:void(0)"  data-target="#myModal1" data-toggle="modal">加入白名单</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal" data-toggle="modal" >标记到推送库</a> &nbsp;&nbsp;<a href="javascript:void(0)" data-target="#myModal2" data-toggle="modal">标记到备选库</a>
                                   </div>
                                  </div>
                                  <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $info_num;?>&nbsp;条数据！</b></span>
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
						<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">标记到推送库</h4>
                                        </div>
                                        <div class="modal-body">确定加入推送库？
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addpush">添加推送</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal1" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">白名单</h4>
                                        </div>
                                        <div class="modal-body">白名单原因:
                                        <select class="input-sm" aria-controls="dataTables-example" id="kind" name="kind">
										  	<option value="1">公司内部人士</option>
										  	<option value="2">经纪人</option>
								        </select>
										 备注
										 <input type="search" name="remark" id="remark" value="">
                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addwhite">确定</button>
                                        </div>
                                    </div>
                                </div>
                             </div>
							 <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal2" class="modal fade" style="display: none;">
                                <div class="modal-dialog"  style="margin:200px auto">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                            <h4 id="myModalLabel" class="modal-title">备选库</h4>
                                        </div>
                                        <div class="modal-body">确定加入备选库？

                                        </div>
                                        <div class="modal-footer">
                                            <button data-dismiss="modal" class="btn btn-default" type="button">取消</button>
                                            <button class="btn btn-primary" type="button" id="addalternatives">确定</button>
                                        </div>
                                    </div>
                                </div>
                             </div>
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
<script type="text/javascript">
	function add_content1(){
		$('#reminder1').html('&nbsp;&nbsp;请输入要查询的电话号码!');
	}
	function add_content2(){
		$('#reminder2').html('&nbsp;&nbsp;请输入要查询的经纪人姓名!');
	}
	function add_content3(){
		$('#reminder3').html('&nbsp;&nbsp;请输入要查询的经纪人姓名!');
	}
	function add_content4(){
		$('#reminder4').html('&nbsp;&nbsp;请输入要查询的经纪人编号!');
	}

	function change_price(id,rand){
		var cid =  'real_price'+rand;
		var obj = $("#"+cid);
		var real_price = obj.val().replace(/\s+/g, "");  //获取值并去空格

		if(real_price == ""){
			alert('请输入真实成交总价~！');
			$("#"+cid).focus();
			return;
		}
		else if(isNaN(real_price)){
			alert('真实成交总价必须为数字~！');
			$("#"+cid).focus();
			return;
		}
		else
		{
			//ajax 改变 cooperate 表里的 real_price
			$.ajax({
				type: 'get',
				url : '<?=MLS_ADMIN_URL?>/sell_house_sold/change_real_price',
				data :{'id':id,'real_price':real_price},
				dataType:'json',
				success: function(msg){
					if(msg == '123'){
						alert('改动失败，请稍后重试~！');
						location.href='<?=MLS_ADMIN_URL?>/sell_house_sold/';
						return;
					}else{
						location.href='<?=MLS_ADMIN_URL?>/sell_house_sold/';
						return;
					}
				}
			});
		}
	}
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<style>
	tr {text-align:center;}
</style>

