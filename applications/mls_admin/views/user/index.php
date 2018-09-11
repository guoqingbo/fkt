<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">用户列表</h1>
                    <h3><a href="<?php echo MLS_ADMIN_URL;?>/user/add/">添加</a></h3>
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
                                        <label>&nbsp&nbsp 城市
                                            <select name="city" aria-controls="dataTables-example" class="form-control input-sm" id="city">
                                                <option value="0">请选择</option>
                                                <?php foreach($city as $k => $v){?>
                                                <option value="<?php echo $v['id'];?>"<?php if(isset($city_id)){if($v['id']==$city_id){echo 'selected="selected"';}}?>><?php echo $v['cityname'];?></option>
                                                <?php }?>
                                            </select> 
                                        </label>
                                        <label>&nbsp&nbsp 用户组
                                            <select id="group" name="group" aria-controls="dataTables-example" class="form-control input-sm">
                                                <option value="0">请选择</option>
                                                <?php if(isset($user_group_arr) && !empty($user_group_arr)){ ?>
                                                    <?php foreach($user_group_arr as $key => $val){ ?>
                                                        <option value="<?php echo $val['id']; ?>" <?php if(isset($user_group_ids)){if($val['id']==$user_group_ids){echo 'selected="selected"';}}?> ><?php echo $val['name']; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select> 
                                        </label>
                                        <label>&nbsp&nbsp 状态
                                            <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                <option value="0">请选择</option>
                                                <option value="1" <?php if(1==$status){echo 'selected="selected"';} ?>>有效</option>
                                                <option value="2" <?php if(2==$status){echo 'selected="selected"';} ?>>失效</option>
                                            </select> 
                                        </label>
                                        <label>
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="pg" value="1">
                                                <input class="btn btn-primary" type="submit" value="查询">
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
                                            <th>序号</th>
                                            <th>联系方式</th>
                                            <th>用户名</th>
                                            <th>真实姓名</th>
                                            <th>备注</th>
                                            <th>客户经理</th>
                                            <th>状态</th>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
																			if(isset($user) && !empty($user)){
																				foreach($user as $key=>$value){
//																					if($_SESSION[WEB_AUTH]["purview"]!=32){
//																						//隐藏手机号部分数字
//																						$pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
//																						$replacement = "\$1*****\$3";
//																						$telno = preg_replace($pattern, $replacement, trim($value['telno']));
//																					}
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['uid'];?></td>
                                            <td><?php echo !empty($telno)?$telno:$value['telno'];?></td>
                                            <td><?php echo $value['username'];?></td>
                                            <td><?php echo $value['truename'];?></td>
											<td><?php echo $value['message'];?></td>
                                            <td><?php echo $value['am_cityid'] == 0 ? '否' : '<font color="red">是</font>';?></td>
                                            <td><?php echo $value['status'] == 1 ? '有效' : '<font color="red">失效</font>';?></td>
                                            <td>
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/user/modify/<?php echo $value['uid'];?>" >修改</a>
                                            	<a href="#" onclick="del(<?php echo $value['uid'];?>);">删除</a>
                                                <?php if($value['status']==1){ ?>
                                                    <a href="#" onclick="set_status(<?php echo $value['uid'];?> , 1);">设置失效</a>
                                                <?php }else{ ?>
                                                    <a href="#" onclick="set_status(<?php echo $value['uid'];?> , 2);">设置有效</a>
                                                <?php } ?>
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
<script>
function del(user_id){
    var is_del = confirm('确定删除该用户吗？');
    del_url = "<?php echo MLS_ADMIN_URL;?>/user/del/"+user_id;
    if(is_del){
        window.location.href = del_url;   
    }
}

function set_status(user_id , type){
    var text = '';
    if(1 == type){
        text = '确定设置失效吗？'
    }else{
        text = '确定设置有效吗？'
    }
    var is_del = confirm(text);
    del_url = "<?php echo MLS_ADMIN_URL;?>/user/set_status/"+user_id+"/"+type;
    if(is_del){
        window.location.href = del_url;   
    }
}
    
$(function(){
    //城市、用户组二级联动
    $('#city').change(function(){
        var city_id = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/user/find_group_by_cityid/'+city_id,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                    }
                }
                $('#group').empty();
                $('#group').append(str);
            }            
        });
    });
    
	$('#sel-all').click(function()
	{
        var aa = document.getElementsByName("rows_id");
        for (var i=0; i<aa.length; i++) { 
           aa[i].checked = document.getElementById('sel-all').checked;; 
        }
	});


   $("#addpush").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
        var city = 'nj';
	     $("#myModal").hide();
		 $(".modal-backdrop").remove();
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addpushlist',
            data: 'ids='+ids+'&city='+city,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				   $("#warning_text").html("成功添加"+msg+"个进入推送库！")
				   openWin('js_note1');
				 }else{
			       $("#warning_text").html("添加失败或没有用户符合要求！")
				   openWin('js_note1');
                 }
                }
            });
         }else{
			       $("#warning_text").html("请先勾选用户！")
				   openWin('js_note1');
         }
   });

      $("#addwhite").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
		 $("#myModal1").hide();
		 $(".modal-backdrop").remove();
		 var remark = $("#remark").val();
		 var kind = $("#kind").val();
		 var city = 'nj';
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addwhitelist',
            data: 'ids='+ids+'&city='+city+'&kind='+kind+'&remark='+remark,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				  $("#warning_text").html("成功添加"+msg+"个白名单！")
				  openWin('js_note1');
				 }else{
                  $("#warning_text").html("添加失败！")
				  openWin('js_note1');
                 }
                }
            });
         }else{
                  $("#warning_text").html("请先勾选用户！")
				  openWin('js_note1');
         }
   });
   
      $("#addalternatives").click(function(){
   		var arr = new Array();
		$(".gradeA").find("input:checked[name=rows_id]").each(function(i) {
		partten = /^1[0-9]{10}$/;
		if($(this).val()!=0 && partten.test($(this).val())){
		arr[i]=$(this).val();
		}
		});
		var ids = arr.join(",");
	     $("#myModal2").hide();
		 $(".modal-backdrop").remove();
         if(ids){
         $.ajax({
            type: 'post',
            url : '<?php echo MLS_ADMIN_URL;?>/user/addalternatives',
            data: 'ids='+ids,
            dataType:'html',
            success: function(msg){
			     if(msg>0)
			     {
				   $("#warning_text").html("成功添加"+msg+"个备选库！")
				   openWin('js_note1');
				 }else{
				   $("#warning_text").html("添加失败！")
				   openWin('js_note1');
                 }
                }
            });
         }else{
				   $("#warning_text").html("请先勾选用户！")
				   openWin('js_note1');
         }
   });
   
   
});
</script>
<?php require APPPATH.'views/footer.php'; ?>

