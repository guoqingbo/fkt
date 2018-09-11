<?php require APPPATH.'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_new.css " rel="stylesheet" type="text/css">
<script type="text/javascript"
		src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/upload_wei.js,mls/js/v1.0/cooperate_common.js,common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/openWin.js"></script>
<style>
    td{text-align: center}
     th{text-align: center}
	html, body {
    height: 100%;
    overflow: auto;
    width: 100%;
	}
</style>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;" >
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                </div>
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <div class="table-responsive">
                            <form action="" method="post" name="search_form">
                                <input type="hidden" name="submit_flag" value="search">
                                <div style="width:100%" class="col-sm-6">
                                    编号：&nbsp;<input type="text" value="<?=$param_array['id']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="id">
                                    用户姓名：&nbsp;<input type="text" value="<?=$param_array['uid']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="uid">
                                    电话：&nbsp;<input type="text" value="<?=$param_array['phone']?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:100px;display: inline-block;"  name="phone">
                                    &nbsp;&nbsp;委托状态：&nbsp;<select name="status">
                                                    <option value="">不限</option>
                                                    <option value="1" <?=($param_array['status']==1)?'selected':''?>>已委托</option>
                                                    <option value="2" <?=($param_array['status']==2)?'selected':''?>>已下架</option>
                                                </select>
									&nbsp;&nbsp;审核状态：&nbsp;<select name="is_check">
                                                    <option value="">不限</option>
                                                    <option value="1" <?=($param_array['is_check']==1)?'selected':''?>>未审核</option>
                                                    <option value="2" <?=($param_array['is_check']==2)?'selected':''?>>已审核</option>
													<option value="3" <?=($param_array['is_check']==3)?'selected':''?>>审核不通过</option>
                                                </select>
                                    <input type="hidden" name="pg" value="1">
                                    <input class="btn btn-primary" type="submit" value="查询" name="search">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/seek_rent/'" value="重置">
                                </div>
                            </form>
                        </div>
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px">编号</th>
                                    <th style="width:75px">用户姓名</th>
                                    <th style="width:105px">电话</th>
                                    <th style="width:90px;text-align:center">区属</th>
                                    <th style="width:75px">面积范围(㎡)</th>
                                    <th style="width:70px">价格范围(万元)</th>
                                    <th style="width:90px">创建时间</th>
                                    <th style="width:90px">抢拍情况</th>
                                    <th style="width:90px">委托状态</th>
                                    <th style="width:90px">审核状态</th>
                                    <th style="width:200px;text-align:center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($list) && !empty($list)) {
                                            foreach($list as $key =>$val){?>
                                <tr class="gradeA">
                                    <td><?php echo $val['id'];?></td>
                                    <td><?php echo $val['realname'];?></td>
                                    <td><?php echo $val['phone'];?></td>
                                    <td><?php echo $val['district'];?></td>
                                    <td><?php echo $val['larea'];?>-<?php echo $val['harea'];?></td>
                                    <td><?php echo $val['lprice'];?>-<?php echo $val['hprice'];?></td>
                                    <td><?php echo date("Y-m-d",$val['ctime']);?></td>
									<td><?=$val['grab_times']?>/10</td>
                                    <td><?php switch($val['status']){case 1:echo "<font color='green'>已委托";break;case 2:echo "已下架</font>";break;}?></td>
									<td><?php switch($val['is_check']){case 1:echo "未审核";break;case 2:echo "<font color='green'>已审核</font>";break;case 3:echo "<font color='red'>审核不通过</font>";break;}?></td>
                                    <td><?php switch($val['status']){
									case 1:echo "<a href='/seek_rent/edit_entrust/".$val['id']."'>修改委托</a> | <a href='javascript:void(0);' onclick='del_pop(".$val['id'].")'>下架</a> | <a href='/seek_rent/entrust_grab_details/".$val['id']."' >委托明细</a>";break;
									case 2:echo "<a href='/seek_rent/edit_entrust/".$val['id']."'>修改委托</a> | <a href='/seek_rent/entrust_grab_details/".$val['id']."' >委托明细</a>";break;}?></td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出售委托数据~！</td></tr>";
                                }?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                         <?php echo page_uri($page, $pages, MLS_ADMIN_URL . '/user/index'); ?>
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
        </div>
    </div>
</div>
<div class="pop_box_g" id="js_del" style="width:300px; height:200px; display:none;">
	<input type="hidden" id="ent_id">
    <div class="hd">
        <div class="title"></div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down ">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="width:100px"><span class="red_star">*</span>下架原因：</div>
            </div>
			<div class="create_newb_wrap create_newblack clearfix">
                <select class="qushu fl" id="del_reason">
					<option value="1">房源已经成交了</option>
					<option value="2">我不想卖了</option>
					<option value="3">其他</option>
                </select>
            </div>

        </div>

        <div class="center">
            <button type="button" class="btn-lv1 btn-left " onclick="del();">确定</button>
            <button type="button" class="btn-hui1 JS_Close">取消</button>
        </div>
    </div>
</div>
<?php
if ( isset($js) && $js != '')
{
    echo $js;
}

if ( isset($css) && $css != '')
{
    echo $css;
}
?>
<script>
function del_pop(id){
	$.ajax({
		type: 'GET',
		url: '/seek_sell/del_pop/'+id,
		//data: {broker_id:broker_id},
		dataType: 'json',
		success: function(data){
			$("#ent_id").val(data);
			//$("#js_del").show();
			openWin("js_del");
		}
	});

}
function del(){
	var ent_id = $("#ent_id").val();
	var del_reason = $("#del_reason option:selected").val();
	$.ajax({
		type: 'GET',
		url: '/seek_sell/del/'+ent_id,
		data: {del_reason:del_reason},
		dataType: 'json',
		success: function(data){
			if(data){
				window.location.reload();
			}
		}/*,
		error: function(xhr, type){
			alert('Ajax error!');
			me.resetload();
		}*/
	});
}




</script>
