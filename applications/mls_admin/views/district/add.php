<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<style>
	#l-map{height:400px;width:600px;float:left;border:1px solid #bcbcbc;}
	#r-result{height:400px;width:230px;float:right;}
</style>
<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><?php echo $title;?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$addResult){; ?>
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
									  </div>
									</div>
								</div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
									   <input type='hidden' name='submit_flag' value='add'/>
                                       区属名<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="district" class="form-control input-sm" aria-controls="dataTables-example" value="">
									  </label>
									  </div>
									  </div>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									  <label>
									   排序:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="order" class="form-control input-sm" aria-controls="dataTables-example" value="">
									  </label>
									  </div>
									  </div>
						        <div class="col-sm-6" style="width:100%">
									<div class="dataTables_length" id="dataTables-example_length">
										<label>
										   是否展示<font color="red">*</font>:
										   <select name="is_show" class="form-control input-sm" aria-controls="dataTables-example">
											   <option value="1">是</option>
											   <option value="2">否</option>
										   </select>
										</label>
									</div>
								</div>
								<div class="col-sm-6" style="width:100%">
										<div class="dataTables_length" id="dataTables-example_length">
										  <label>
										   请输入地址<font color="red">*</font>:&nbsp;&nbsp;&nbsp;&nbsp;
										   <input type="text" id="txtCity" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;" >
											&nbsp;&nbsp;&nbsp;&nbsp;
											<span class="text1"><font color="red">*</font>经度:</span><input type="text" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:90px;display: inline-block;"  name="b_map_x" id="b_map_x"> <!--<input type="search" name="b_map_x" class="form-control input-sm" aria-controls="dataTables-example" value=""/>-->
											&nbsp;&nbsp;&nbsp;&nbsp;
											<span class="text1"><font color="red">*</font>纬度:</span><input type="text" value="" aria-controls="dataTables-example" class="form-control input-sm " style="width:90px;display: inline-block;"  name="b_map_y" id="b_map_y"><!--<input type="search" name="b_map_y" class="form-control input-sm" aria-controls="dataTables-example" value=""/>-->
											 &nbsp;&nbsp;
										  </label>
										 </div>
									</div>
									<div class="col-sm-6" style="width:100%;">
										<span style="float:left;font-weight:bold">百&nbsp;度&nbsp;地&nbsp;图：</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<span id="l-map"></span>
										<span id="r-result" style="position:relative;"></span>
									</div>
									<script type="text/javascript">
										var lng = "<?php echo $lng;?>";
										var lat = "<?php echo $lat;?>";
										// 百度地图API功能
										var map = new BMap.Map("l-map");            // 创建Map实例
										var point = new BMap.Point(lng, lat);     // 创建点坐标
										map.centerAndZoom(point,12);
										map.enableScrollWheelZoom();       // 初始化地图,设置城市和地图级别。
										map.addControl(new BMap.NavigationControl());
										map.addControl(new BMap.ScaleControl());
										map.addControl(new BMap.MapTypeControl({anchor: BMAP_ANCHOR_TOP_RIGHT}));
										map.addControl(new BMap.OverviewMapControl());              //添加默认缩略地图控件
										map.addControl(new BMap.OverviewMapControl({isOpen:true, anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));   //右上角，打开
										var local = new BMap.LocalSearch("全国", {
										  renderOptions: {
												map: map,
												panel : "r-result",
												autoViewport: true,
												selectFirstResult: false
										  }
										});
										map.addEventListener("click",function(e){
												$("#b_map_x").val(e.point.lng);
												$("#b_map_y").val(e.point.lat);
												var newpoint=new BMap.Point(e.point.lng,e.point.lat);
												var marker = new BMap.Marker(newpoint);        // 创建标注
										map.addOverlay(marker);                     // 将标注添加到地图中
												marker.enableDragging();                    //标注可拖拽
												marker.addEventListener("dragend", function(e){
												$("#b_map_x").val(e.point.lng);
												$("#b_map_y").val(e.point.lat);
											});
										});
										$('#txtCity').bind('input txtCity', function() {
											var city = document.getElementById("txtCity").value;
												local.search(city);

										});

									 </script>
								<?php if(!empty($mess_error)){?>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
											<font color='red'><?php echo $mess_error; ?></font>
									  </div>
									  </div>
									  <?php } ?>
						        <div class="col-sm-6" style="width:100%">
								    <div class="dataTables_length" id="dataTables-example_length">
									   <input class="btn btn-primary" type="submit" value="提交">
									  </div>
									  </div>
								</div>
              </form>
								</div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->

                    </div>
            <?php }else if(0===$addResult){ ?>
            	<div>插入失败</div>
            <?php }else{?>
            	<div>插入成功</div>
            <?php }?>
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

