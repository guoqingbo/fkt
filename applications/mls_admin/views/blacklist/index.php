<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">中介黑名单列表</h1>
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
							     <div class="col-sm-6" style="width:100%">
								    电话：&nbsp;<input type="text" maxlength="11" name="tel" id="tel" style="width:183px" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['tel'])){echo $_POST['tel'];}else{echo "请输入要查询的中介号码...";}?>" onchange="check_tel(this.value)" onclick="delwords()"><span style="color:red;font-size:20px;">&nbsp;&nbsp;电话具有唯一性，请与<b>日期</b>分开查询！</span>
									</div>
								</div>
								<div class="row">
						        <div class="col-sm-6" style="width:100%;">
									日期：
									  <label>
									   <input style="width:183px" type="text" name="start_time" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()"> 到 <input style="width:183px" type="text" id="start_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
									  </label>
									   <input type="hidden" name="black" value="blacklist">
									   <input type="hidden" name="pg" value="1">
									   <input class="btn btn-primary" type="submit" value="查询"><br>
								</div>
								</div>
                               </form>
								</div>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>序号</th>

                                            <th>联系方式</th>
                                            <th>站点名称</th>
                                            <th>功能</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
																			if(isset($blacklist) && !empty($blacklist)){
																				foreach($blacklist as $key=>$value){
//																					if($_SESSION[WEB_AUTH]["purview"]!=32){
//																						//隐藏手机号部分数字
//																						$pattern = "/(1\d{1,2})\d\d(\d{0,3})/";
//																						$replacement = "\$1*****\$3";
//																						$telno = preg_replace($pattern, $replacement, trim($value['telno']));
//																					}
                                    ?>
                                        <tr class="gradeA">
                                            <td><?php echo $value['id'];?></td>

                                            <td><?php echo $value['tel'];?></td>
                                            <td><?php echo $value['store'];?></td>
                                            <td>
                                            	<!--<a href="<?php echo MLS_ADMIN_URL;?>/blacklist/modify/<?php echo $value['id'];?>" >修改</a>-->
                                            	<a href="<?php echo MLS_ADMIN_URL;?>/blacklist/del/<?php echo $value['id'];?>"  onclick="return checkdel()">删除</a>
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
                                    <b>共查到<?php echo $blacklist_num;?>条数据</b>
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
    function check_tel(text) {
		var _emp = /^\s*|\s*$/g;
        text = text.replace(_emp, "");
        var _d = /^1[3456789][0-9]\d{8}$/g;
        if (_d.test(text)) {
			return true;
        }else{
            alert("请输入11位有效手机号码！");
        }
    }
	function delwords(){
		var obj = document.getElementById("tel");
		obj.value = "";
	}
</script>
<script>
function checkdel(){
	if(confirm("确实要删除吗？"))
    {
		return true;
	}
     else
    {	return false;
	}
}

$(function(){
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
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
