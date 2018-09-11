<?php require APPPATH.'views/header.php';date_default_timezone_set("PRC"); ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript">

function get_agency(companyId)
{
    $.ajax({
        type: 'get',
        url : '<?php echo MLS_ADMIN_URL; ?>/agency/get_agency_ajax/'+companyId,
        dataType:'json',
        success: function(msg){
            var str = '';
            if(msg===''){
                str = '<option value="">请选择</option>';
            }else{
                str = '<option value="">请选择</option>';
                for(var i=0;i<msg.length;i++){
                    str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                }
            }
            $('#agency_id').empty();
            $('#agency_id').append(str);
        }
    });
}
$(function() {
    $("#company_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/company/get_company_by_kw/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data[0]['id'] != '0'){
                        response(data);
                    }else{
                        response(data);
                    }
                }
            });
        },
        minLength: 1,
        removeinput: 0,
        select: function(event,ui) {
            if(ui.item.id > 0){
                var company_name = ui.item.label;
                var id = ui.item.id;
                //操作
                $("#company_id").val(id);
                $("#company_name").val(company_name);
                get_agency(id);
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#company_id").val("");
                $("#company_name").val("");
            }
        }
    });
});


</script>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">出售合作订单</h1>
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
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>状态
                                                        <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($status_arr as $k => $v){?>
                                                            <option value="<?php echo $k;?>" <?php if(!empty($where_cond['esta'])){if($k==$where_cond['esta']){echo 'selected="selected"';}}?>><?php echo $v;?></option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        &nbsp&nbsp 交易编号<input type='search' aria-controls="dataTables-example" size='12' name='order_sn' value="<?php if((!empty($where_cond['order_sn']))){echo $where_cond['order_sn'];}?>"/>
                                                    </label>
                                                    <label>
                                                        &nbsp&nbsp 经纪人姓名<input type='search' aria-controls="dataTables-example" size='12' name='broker_name' value="<?php if((!empty($where_cond['broker_name_a']))){echo $where_cond['broker_name_a'];}?>"/>
                                                    </label>
													<label>&nbsp;所属公司&nbsp;
														<input name="company_name" id="company_name" value="<?=$where_cond['company_name']?>" class="input_text input_text_r w150 form-control input-sm" type="text" placeholder="输入汉字筛选" style="height:30px; line-height: 30px;" >
														<input type="hidden" name="company_id" id="company_id" value="<?=$where_cond['company_id']?>">
													</label>
													<label>&nbsp;所属门店&nbsp;
														<select name="agency_id"  id="agency_id" aria-controls="dataTables-example" class="form-control input-sm">
															<option value="0">请选择</option>
															<?php if ($agencys) {
																	foreach ($agencys as $v) { ?>
																		<option value="<?=$v['id']?>"  <?php if((!empty($where_cond['agency_id']) && $where_cond['agency_id'] == $v['id'])){echo 'selected="selected"';}?>><?=$v['name']?></option>
																	<?php }?>
															<?php } ?>
														</select>
													</label>
													<label>
														<div class="dataTables_length" id="dataTables-example_length">
															&nbsp;时间筛选&nbsp;<input type="text" name="time_s" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onclick="WdatePicker()"/>
															&nbsp;至&nbsp;
															<input type="text" name="time_e" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onclick="WdatePicker()"/>
														</div>
													</label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/cooperate_order/sell/'" value="重置">
                                                        </div>
                                                    </label>
													<label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            &nbsp;&nbsp;<a class="btn btn-primary" href='/cooperate_order/exportReport/0/<?php if(isset($where_cond['order_sn']) || isset($where_cond['broker_name_a']) ||  isset($where_cond['company_id']) || isset($where_cond['agency_id']) || isset($where_cond['esta']) || (isset($_POST['time_e']) && isset($_POST['time_s']))){?><?php echo '?';?><?php }?>
														<?php
															if(isset($where_cond['esta'])){
																echo 'status='.$where_cond['esta'];
															}
															if(isset($where_cond['order_sn'])){
																echo '&order_sn='.$where_cond['order_sn'];
															}
															if(isset($where_cond['broker_name_a'])){
																echo '&broker_name='.$where_cond['broker_name_a'];
															}
															if(isset($where_cond['company_id'])){
																echo '&company_id='.$where_cond['company_id'];
															}
															if(isset($where_cond['agency_id'])){
																echo '&agency_id='.$where_cond['agency_id'];
															}
															if(isset($_POST['time_s']) && isset($_POST['time_e'])){
																echo '&time_s='.$_POST['time_s'].'&time_e='.$_POST['time_e'];
															}
														?>'>导出</a>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" style="text-align:center;vertical-align:middle" >交易编号</th>
                                            <th colspan="4" style="text-align:center">甲方</th>
                                            <th colspan="4" style="text-align:center">乙方</th>
                                            <th rowspan="2" style="text-align:center;vertical-align:middle">状态更新时间</th>
                                            <th rowspan="2" style="text-align:center;vertical-align:middle">状态</th>
                                            <th rowspan="2" style="text-align:center;vertical-align:middle">评价</th>
                                            <th rowspan="2" style="text-align:center;vertical-align:middle">操作</th>
                                        </tr>
                                        <tr>
                                            <th style="text-align:center">经纪人</th>
                                            <th style="text-align:center">门店</th>
                                            <th style="text-align:center">公司</th>
                                            <th style="text-align:center">手机</th>
                                            <th style="text-align:center">经纪人</th>
                                            <th style="text-align:center">门店</th>
                                            <th style="text-align:center">公司</th>
                                            <th style="text-align:center">手机</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if(isset($cooperate_order2) && !empty($cooperate_order2)){
                                            foreach($cooperate_order2 as $key=>$value){
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['order_sn'];?></td>
                                            <?php if(2==$value['apply_type']){?>
                                            <td><?php echo $value['broker_name_b'];?></td>
                                            <td><?php echo $value['agent_b_name'];?></td>
                                            <td><?php echo $value['company_b_name'];?></td>
                                            <td><?php echo $value['phone_b'];?></td>
                                            <td><?php echo $value['broker_name_a'];?></td>
                                            <td><?php echo $value['agent_a_name'];?></td>
                                            <td><?php echo $value['company_a_name'];?></td>
                                            <td><?php echo $value['phone_a'];?></td>
                                            <?php }else{?>
                                            <td><?php echo $value['broker_name_a'];?></td>
                                            <td><?php echo $value['agent_a_name'];?></td>
                                            <td><?php echo $value['company_a_name'];?></td>
                                            <td><?php echo $value['phone_a'];?></td>
                                            <td><?php echo $value['broker_name_b'];?></td>
                                            <td><?php echo $value['agent_b_name'];?></td>
                                            <td><?php echo $value['company_b_name'];?></td>
                                            <td><?php echo $value['phone_b'];?></td>
                                            <?php }?>
                                            <td><?php echo date('Y-m-d',$value['dateline']);?></td>
                                            <td><?php echo $status_arr[$value['esta']];?></td>
                                            <td><?php echo $value['appraise_str'];?></td>
                                            <td>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/cooperate_order/detail/<?php echo $value['id'];?>" >详情</a>
                                                <?php if($value['esta']==10){?>
                                                已冻结
                                                <?php }else{?>
                                                <a href="<?php echo MLS_ADMIN_URL;?>/cooperate_order/modify/<?php echo $value['id'];?>" onclick="return confirm('确定冻结此合作？');">冻结</a>
                                                <?php }?>
                                            </td>
                                        </tr>
                                    <?php }}?>


                                    </tbody>
                                </table>
                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

										<ul class="pagination" style="margin:-8px 0;padding-left:20px">
											<?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
										</ul>
                                    </div>
                                  </div>
                                <div style="color:blue;position:absolute;right:33px;">
                                    <b>共查到<?php echo $cooperate_num;?>条数据</b>
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
<script>
$(function(){
    $('#company_id').change(function(){
        var companyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/company/get_agency_ajax/'+companyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                    }
                }
                $('#agency_id').empty();
                $('#agency_id').append(str);
            }
        });
    });
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

