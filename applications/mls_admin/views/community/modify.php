<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4&ak=s4xTcbCABxjTGG3EfdZpQxaT"></script>
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
                    <h1 class="page-header">修改楼盘</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$modifyResult){; ?>
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
                                            <input type='hidden' name='submit_flag' value='modify'/>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘名称<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="cmt_name" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['cmt_name']?>">
                                                    </label>
                                                    <label>
                                                        拼音:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="name_spell" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['name_spell']?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘别名:&nbsp&nbsp&nbsp&nbsp<input type="text" name="alias" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['alias']?>">
                                                    </label>
                                                    <label>
                                                        别名拼音:&nbsp&nbsp&nbsp<input type="text" name="alias_spell" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['alias_spell']?>">
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘类型:&nbsp;&nbsp;<input type="radio" value="1" name="type"<?php if(strchr($comm['type'], '1')){?>checked='checked'<?php } ?>>住宅&nbsp;&nbsp;
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="2" name="type" <?php if(strchr($comm['type'], '2')){?>checked='checked'<?php } ?>>别墅
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="4" name="type" <?php if(strchr($comm['type'], '4')){?>checked='checked'<?php } ?>>写字楼
                                                    </label>
                                                    <label>
                                                        <input type="radio" value="3" name="type" <?php if(strchr($comm['type'], '3')){?>checked='checked'<?php } ?>>商铺
                                                    </label>
													<label>
                                                        <input type="radio" value="5" name="type" <?php if(strchr($comm['type'], '5')){?>checked='checked'<?php } ?>>厂房
                                                    </label>
													<label>
                                                        <input type="radio" value="6" name="type" <?php if(strchr($comm['type'], '6')){?>checked='checked'<?php } ?>>仓库
                                                    </label>
													<label>
                                                        <input type="radio" value="7" name="type" <?php if(strchr($comm['type'], '7')){?>checked='checked'<?php } ?>>车库
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        区属<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select id="district" name="dist_id" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php foreach ($district as $k => $v) { ?>
                                                                <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$comm['dist_id']){echo 'selected="selected"';}?>><?php echo $v['district'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        板块<font color="red">*</font>:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                        <select id="street" name="streetid" aria-controls="dataTables-example" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php foreach ($comm['street_arr'] as $k => $v) { ?>
                                                                <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$comm['streetid']){echo 'selected="selected"';}?>><?php echo $v['streetname'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘地址<font color="red">*</font>:&nbsp&nbsp&nbsp<input type="search" name="address" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['address']?>" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        建筑年代:&nbsp&nbsp&nbsp&nbsp
                                                        <select id="build_date" name="build_date" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <?php for($i=1970;$i<2021;$i++){?>
                                                            <option value="<?php echo $i;?>" <?php if($i==$comm['build_date']){echo 'selected="selected"';}?>><?php echo $i;?>年</option>
                                                            <?php }?>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        建筑面积:&nbsp&nbsp&nbsp&nbsp<input type="search" name="buildarea" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['buildarea']?>">
                                                    </label>
                                                    平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        交付日期:&nbsp&nbsp&nbsp&nbsp<input type="search" name="deliver_date" onclick="WdatePicker()" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['deliver_date']?>">
                                                    </label>
                                                    <label>
                                                        均价:&nbsp&nbsp&nbsp&nbsp<input type="search" name="averprice" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['averprice']?>">
                                                    </label>
                                                    元/平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        产权年限:&nbsp&nbsp&nbsp&nbsp
                                                        <select id="build_date" name="property_year" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <option value="40" <?php if(40==$comm['property_year']){echo 'selected="selected"';}?>>40年</option>
                                                            <option value="50" <?php if(50==$comm['property_year']){echo 'selected="selected"';}?>>50年</option>
                                                            <option value="70" <?php if(70==$comm['property_year']){echo 'selected="selected"';}?>>70年</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        占地面积:&nbsp&nbsp&nbsp&nbsp<input type="search" name="coverarea" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['coverarea']?>">
                                                    </label>
                                                    平方米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        物业公司:&nbsp&nbsp&nbsp&nbsp<input type="search" name="property_company" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['property_company']?>">
                                                    </label>
                                                    <label>
                                                        开发商:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="developers" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['developers']?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        停车位:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="parking" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['parking']?>">
                                                    </label>
                                                    <label>
                                                        绿化率:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="green_rate" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['green_rate']*100?>">%
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        容积率:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="plot_ratio" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['plot_ratio']?>">
                                                    </label>
                                                    <label>
                                                        物业费:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="property_fee" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['property_fee']?>">
                                                    </label>
                                                    元/月·平米
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        总栋数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="build_num" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['build_num']?>">
                                                    </label>
                                                    <label>
                                                        总户数:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="total_room" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['total_room']?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼层状况:&nbsp&nbsp&nbsp&nbsp<input type="search" name="floor_instruction" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['floor_instruction']?>" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘简介:&nbsp&nbsp&nbsp&nbsp<input type="search" name="introduction" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['introduction']?>" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
													<label>
                                                        物业业态:&nbsp;&nbsp;<input type="checkbox" value="住宅" name="build_type[]"<?php if(strchr($comm['build_type'], '住宅')){?>checked='checked'<?php } ?>>住宅&nbsp;&nbsp;
                                                    </label>
													<label>
                                                        <input type="checkbox" value="别墅" name="build_type[]" <?php if(strchr($comm['build_type'], '别墅')){?>checked='checked'<?php } ?>>别墅
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="写字楼" name="build_type[]" <?php if(strchr($comm['build_type'], '写字楼')){?>checked='checked'<?php } ?>>写字楼
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="商铺" name="build_type[]" <?php if(strchr($comm['build_type'], '商铺')){?>checked='checked'<?php } ?>>商铺
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="厂房仓库" name="build_type[]" <?php if(strchr($comm['build_type'], '厂房')){?>checked='checked'<?php } ?>>厂房
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="厂房仓库" name="build_type[]" <?php if(strchr($comm['build_type'], '仓库')){?>checked='checked'<?php } ?>>仓库
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" value="厂房仓库" name="build_type[]" <?php if(strchr($comm['build_type'], '车库')){?>checked='checked'<?php } ?>>车库
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        周边配套:&nbsp&nbsp&nbsp&nbsp<input type="search" name="facilities" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['facilities']?>" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        公交:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="bus_line" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['bus_line']?>" size="62">&nbsp;&nbsp;
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        地铁:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="subway" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['subway']?>" size="62">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        对应小学:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="primary_school" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['primary_school']?>">
                                                    </label>
                                                    <label>
                                                        对应中学:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="search" name="high_school" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $comm['high_school']?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼盘状态:&nbsp;&nbsp;<input type="radio" name="status" value="1" <?php if('1'==$comm['status']){?> checked='checked' <?php }?>/> 临时小区
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="2" <?php if('2'==$comm['status']){?> checked='checked' <?php }?>/> 正式小区
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="status" value="3" <?php if('3'==$comm['status']){?> checked='checked' <?php }?>/> 待审核小区
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        显示上传图片按钮:&nbsp;&nbsp;<input type="radio" name="is_upload_pic" value="0" <?php if('0'==$comm['is_upload_pic']){?> checked='checked' <?php }?>/> 否
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="is_upload_pic" value="1" <?php if('1'==$comm['is_upload_pic']){?> checked='checked' <?php }?>/> 是
                                                    </label>
                                                </div>
                                            </div>
											<div class="col-sm-6" style="width:100%">
										<div class="dataTables_length" id="dataTables-example_length">
										  <label>
										   请输入地址<font color="red">*</font>:&nbsp;&nbsp;&nbsp;&nbsp;
										   <input type="text" id="txtCity" value="<?php echo $comm['cmt_name'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;" >
											&nbsp;&nbsp;&nbsp;&nbsp;
                                              <span class="text1"><font color="red">*</font>经度:</span>
                                              <input type="text" value="<?php echo $comm['b_map_x']; ?>"
                                                     aria-controls="dataTables-example" class="form-control input-sm "
                                                     style="width:90px;display: inline-block;" name="b_map_x"
                                                     id="b_map_x">
                                              <!--<input type="search" name="b_map_x" class="form-control input-sm" aria-controls="dataTables-example" value=""/>-->
											&nbsp;&nbsp;&nbsp;&nbsp;
                                              <span class="text1"><font color="red">*</font>纬度:</span>
                                              <input type="text" value="<?php echo $comm['b_map_y']; ?>"
                                                     aria-controls="dataTables-example" class="form-control input-sm "
                                                     style="width:90px;display: inline-block;" name="b_map_y"
                                                     id="b_map_y">
                                              <!--<input type="search" name="b_map_y" class="form-control input-sm" aria-controls="dataTables-example" value=""/>-->
											 &nbsp;&nbsp;
										  </label>
                                            <a class="btn btn-sm btn-success btn_map_xy">更新经纬度</a>
										 </div>
									</div>
									<div class="col-sm-6" style="width:100%;">
										<span style="float:left;font-weight:bold">百&nbsp;度&nbsp;地&nbsp;图：</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<span id="l-map"></span>
										<span id="r-result" style="position:relative;"></span>
									</div>
									<script type="text/javascript">
										var lng = "<?php echo $comm['b_map_x'] > 0 ? $comm['b_map_x'] : $lng;?>";
										var lat = "<?php echo $comm['b_map_y'] > 0 ? $comm['b_map_y'] : $lat;?>";
										// 百度地图API功能
										var map = new BMap.Map("l-map");            // 创建Map实例
										var point = new BMap.Point(lng, lat);     // 创建点坐标
										map.centerAndZoom(point,16);
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

										var newpoint=new BMap.Point(lng, lat);
										var marker = new BMap.Marker(newpoint);        // 创建标注
										map.addOverlay(marker);                     // 将标注添加到地图中

										map.addEventListener("click", function(e){
											map.removeOverlay(marker);
											newpoint=new BMap.Point(e.point.lng, e.point.lat);
											marker = new BMap.Marker(newpoint);        // 创建标注
											map.addOverlay(marker);                     // 将标注添加到地图中
											$("#b_map_x").val(e.point.lng);
											$("#b_map_y").val(e.point.lat);
										});

										$('#txtCity').bind('input txtCity', function() {
											var city = document.getElementById("txtCity").value;
												local.search(city);

										});
                                        //                                        // 创建地址解析器实例
                                        //                                        var myGeo = new BMap.Geocoder();
                                        //                                        // 将地址解析结果显示在地图上,并调整地图视野
                                        //                                        myGeo.getPoint("<?php //echo $comm['address']?>//", function(point){
                                        //                                            if (point) {
                                        //                                                map.centerAndZoom(point, 16);
                                        //                                                map.addOverlay(new BMap.Marker(point));
                                        //                                                console.log(point);
                                        //                                                console.log("<?php //echo $comm['cmt_name'];?>//")
                                        //                                            }else{
                                        //                                                alert("您选择地址没有解析到结果!");
                                        //                                            }
                                        //                                        }, "杭州市");
									 </script>
											<?php if (!empty($mess_error)) { ?>
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <font color='red'><?php echo $mess_error; ?></font>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input class="btn btn-primary" type="submit" value="提交">
                                                    <input class="btn btn-primary" type="button" value="取消" onclick="window.history.go(-1);">
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
            <?php }else if(0===$modifyResult){ ?>
            	<div>更新失败</div>
            <?php }else{?>
            	<div>更新成功</div><br>
            <input class="btn btn-primary" type="button" value="关闭" onclick="close_window();">
            <?php }?>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

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
function close_window()
{
    var userAgent = navigator.userAgent;
    if (userAgent.indexOf("Firefox") != -1 || userAgent.indexOf("Presto") != -1) {
        window.location.replace("about:blank");
    } else {
        window.opener = null;
        window.open("", "_self");
        window.close();
    }
}
$(function(){
    $('#district').change(function(){
        var districtID = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/community/find_street_bydis/'+districtID,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg.result=='no result'){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].streetname+'</option>';
                    }
                }
                $('#street').empty();
                $('#street').append(str);
            }
        });
    });

});
</script>
<script src="<?php echo MLS_SOURCE_URL;?>/common/third/My97DatePicker/WdatePicker.js"></script>
<?php require APPPATH.'views/footer.php'; ?>
