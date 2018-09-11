<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">出租成交房源</h1>
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
									<div class="col-sm-6" style="width:20%">
										房源编号：<input type="text"  name="house_id" id="house_id" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['house_id'])){echo $_POST['house_id'];}?>"  onclick="add_content1()"><span id='reminder1' style='font-weight:bold;color:red;'></span>
									</div>
									<div class="col-sm-6" style="width:20%">
										交易编号：<input type="text"  name="order_sn" id="order_sn" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['order_sn'])){echo $_POST['order_sn'];}?>"  onclick="add_content2()"><span id='reminder2' style='font-weight:bold;color:red;'></span>
									</div>
									<div class="col-sm-6" style="width:20%">
											经纪人姓名：<input type="text"  name="agent_name" id="agent_name" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['agent_name'])){echo $_POST['agent_name'];}?>"  onclick="add_content3()"><span id='reminder3' style='font-weight:bold;color:red;'></span>
									</div>
									<div class="col-sm-6" style="width:20%">
											经纪人编号：<input type="text"  name="agent_id" id="agent_id" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['agent_id'])){echo $_POST['agent_id'];}?>"  onclick="add_content4()"><span id='reminder4' style='font-weight:bold;color:red;'></span>
									</div><br>
								</div>
									   <input type="hidden" name="angela_wen" value="angel_in_us">
									   <input type="hidden" name="pg" value="1">
									   <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href='/rent_house_sold/'" value="重置">
                               </form>
								</div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>交易编号</th>
                                            <th>房源编号</th>
                                            <th>区属板块</th>
                                            <th>甲方经纪人</th>
                                            <th>乙方经纪人</th>
                                            <th>成交时间</th>
                                            <th>经纪人提交的价格(单位:元)</th>
                                            <th>真实成交价(单位:元)</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
										<?php
										if(isset($sold_list) && !empty($sold_list)){
											foreach($sold_list as $key=>$value){?>
											<tr class="gradeA">
												<td><?php echo $value['order_sn'];?></td>
												<td><?php echo $value['rowid'];?></td>
												<td><?php echo $value['house']['districtname'].'  '.$value['house']['streetname'];?></td>
												<td><?php echo $value['broker_name_a'];?></td>
												<td><?php echo $value['broker_name_b'];?></td>
												<td><?php date_default_timezone_set('PRC'); echo date('Y-m-d',$value['dateline']);?></td>
												<td><?php echo strip_end_0($value['price']);?></td>
												<td>
													<?php
														$price = intval($value['real_price']);
														if($price==0)
														{
															$rand1 = rand(1,10000).time();
															echo "<input type='text' style='width:70px;' name='real_price' id='real_price".$rand1."' cols=20 ><input type='button' onclick=\"change_price(".$value['id'].",".$rand1.")\" value='确定'>";
														}
														else
														{
															$rand2 = rand(1,10000).time();
															echo "<span style='color:red;font-weight:bold;'>".$price."&nbsp;&nbsp;</span><input type='text' name='real_price' id='real_price".$rand2."' cols=20><input type='button' onclick=\"change_price(".$value['id'].",".$rand2.")\" value='修改'>";
														}
													?>
												</td>
												<td style="width:120px;">
													<a href="<?php echo MLS_ADMIN_URL;?>/sell_house_sold/house_detail/<?php echo $value['id'];?>/<?php echo $value['tbl'];?>" >房源详情</a><br>
													<a href="<?php echo MLS_ADMIN_URL;?>/sell_house_sold/cooperation_detail/<?php echo $value['id'];?>/<?php echo $value['tbl'];?>">合作详情</a><br>
													<a href="<?php echo MLS_ADMIN_URL;?>/sell_house_sold/reward_real_price/<?php echo $value['id'];?>"  onclick="return checkdel()">价格真实奖励</a>
												</td>
											</tr>
										<?php }}else{echo "<tr class='gradeA'><td colspan=9 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的租房成交数据~！</td></tr>";}?>
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
                                    <div style="color:blue;position:absolute;right:33px;">
                                        <b>共查到<?php echo $sold_num;?>条数据</b>
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
		$('#reminder1').html('请输入要查询的房源编号!');
	}
	function add_content2(){
		$('#reminder2').html('请输入要查询的交易编号!');
	}
	function add_content3(){
		$('#reminder3').html('请输入要查询的经纪人姓名!');
	}
	function add_content4(){
		$('#reminder4').html('请输入要查询的经纪人编号!');
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
	/*table td{text-align:center;vertical-align:50%;}*/
</style>
