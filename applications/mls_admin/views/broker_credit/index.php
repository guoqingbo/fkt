<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script>
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
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" id="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">条件筛选</option>
														<option value="phone" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'phone')){echo 'selected="selected"';}?>>手机号码</option>
                                                        <option value="truename" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'truename')){echo 'selected="selected"';}?>>真实姓名</option>
                                                    </select>
                                                </label>
												<label>
                                                    <div><input type='search'  size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                    </div>
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
                                                    <div>
                                                        &nbsp&nbsp 积分获取/使用：&nbsp&nbsp<select id='score' name="score"  class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="">不限</option>
                                                        <option value="score1" <?php if((!empty($where_cond['score']) && $where_cond['score'] == 'score1')) {echo 'selected="selected"';}?>>积分获取</option>
                                                        <option value="score2" <?php if((!empty($where_cond['score']) && $where_cond['score'] == 'score2')) {echo 'selected="selected"';}?>>积分消耗</option>
                                                    </select>
                                                    </div>
                                                </label>
												<label>
                                                    <div>
                                                        &nbsp&nbsp 积分获取渠道：&nbsp&nbsp<select id='infofrom' name="infofrom"  class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="">不限</option>
                                                        <option value="2" <?php if((!empty($where_cond['infofrom']) && $where_cond['infofrom'] == '2')) {echo 'selected="selected"';}?>>APP</option>
                                                        <option value="1" <?php if((!empty($where_cond['infofrom']) && $where_cond['infofrom'] == '1')) {echo 'selected="selected"';}?>>PC端</option>
                                                    </select>
                                                    </div>
                                                </label>
												<label>
                                                    <div>
                                                        &nbsp&nbsp 行为：&nbsp&nbsp<select id='type' name="type"  class="form-control input-sm" aria-controls="dataTables-example">
                                                        <option value="">不限</option>
                                                        <?php foreach($credit_way as $v) { ?>
                                                        <option value="<?=$v['type']?>" <?php if((!empty($where_cond['type']) && $where_cond['type'] == $v['type'])) {echo 'selected="selected"';}?>><?=$v['action']?></option>
                                                        <?php }?>
                                                    </select>
                                                    </div>
                                                </label>
												<label>
                                                    <div>
														&nbsp&nbsp 时间查询：&nbsp&nbsp
														<input type="text" name="time_s" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onclick="WdatePicker()"/>
														&nbsp;至&nbsp;
														<input type="text" name="time_e" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onclick="WdatePicker()"/>
													</div>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">
														<a class="btn btn-primary" href='/broker_credit/exportReport/<?php if((isset($where_cond['search_where']) && isset($where_cond['search_value'])) || (isset($where_cond['company_id']) || isset($where_cond['agency_id'])) || isset($where_cond['score']) || isset($where_cond['infofrom']) || (isset($_POST['time_s']) && isset($_POST['time_e']))){?><?php echo '?';?><?php }?>
														<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo 'search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}
															if($where_cond['company_id']){
																echo '&company_id='.$where_cond['company_id'];
															}
															if($where_cond['agency_id']){
																echo '&agency_id='.$where_cond['agency_id'];
															}
															if(isset($where_cond['score'])){
																echo '&score='.$where_cond['score'];
															}
															if(isset($where_cond['infofrom'])){
																echo '&infofrom='.$where_cond['infofrom'];
															}
															if(isset($where_cond['type'])){
																echo '&type='.$where_cond['type'];
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
                                </div>
                           </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>手机号码</th>
                                    <th>真实姓名</th>
									<th>时间</th>
                                    <th>行为</th>
									<th>积分</th>
									<th>备注</th>
									<th>剩余积分</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($broker_credit) && !empty($broker_credit)) {
                                foreach ($broker_credit as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id'];?></td>
                                        <td><?php echo $value['phone'];?></td>
                                        <td><?php echo $value['truename'];?></td>
										<td><?php echo date("Y-m-d H:i:s",$value['create_time']);?></td>
                                        <td><?php echo $value['credit_way']['action']; ?></td>
										<td><?php if($value['score'] > 0){?><?php echo '+'.$value['score'];?><?php }else{?><?php echo $value['score'];?><?php }?></td>
										<td><?php echo $value['remark'];?></td>
										<td><?php echo $value['credit'];?></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">

								<p style="width:1124px;height:50px;">
									<span style="margin-right:50px">当前获取积分总计:<?php echo $score_get;?></span><span style="margin-right:50px">消耗积分:<?php echo $score_consume;?></span>
								</p>
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/broker_credit/');?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function checkdel(id)
    {
         if (confirm('确定要删除此礼品？')) {
             return true;
         } else {
             return false;
         }
    }
	function checkdown(id)
    {
         if (confirm('确定将改礼品下架？')) {
             return true;
         } else {
             return false;
         }
    }
	function checkup(id)
    {
         if (confirm('确定将改礼品上架？')) {
             return true;
         } else {
             return false;
         }
    }
    function uploadphoto(id)
    {
            layer.open({
                    type: 2,
                    title: '上传公司Logo ',
                    shadeClose: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['400px', '400px'],
                    content: '/company/uploadphoto/'+id,
                end: function(){
                            $("#search_form").submit();
                    }
            });
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>
