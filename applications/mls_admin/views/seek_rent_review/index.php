<?php require APPPATH.'views/header.php'; ?>
<link href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
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
    <div id="page-wrapper" style="min-height: 337px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                </div>
				<form action="" method="post" name="search_form">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th style="width:45px;text-align:center">编号</th>
                                    <th style="width:75px;text-align:center">联系人</th>
                                    <th style="width:105px;text-align:center">联系电话</th>
                                    <th style="width:90px;text-align:center">意向区属</th>
                                    <th style="width:75px;text-align:center">面积(㎡)</th>
                                    <th style="width:90px;text-align:center">期望租金(元/月)</th>
                                    <th style="width:90px;text-align:center">创建时间</th>
                                    <th style="width:90px;text-align:center">委托状态</th>
                                    <th style="width:90px;text-align:center">审核状态</th>
                                    <th style="width:200px;text-align:center;text-align:center">操作</th>
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
                                    <td><?php echo $val['larea'];?>-<?php echo $val['larea'];?></td>
                                    <td><?php echo $val['lprice'];?>-<?php echo $val['hprice'];?></td>
                                    <td><?php echo date("Y-m-d",$val['ctime']);?></td>
                                    <td><?php switch($val['status']){case 1:echo "<font color='green'>已委托";break;case 2:echo "已下架</font>";break;}?></td>
									<td><?php switch($val['is_check']){case 1:echo "未审核";break;case 2:echo "<font color='green'>已审核</font>";break;case 3:echo "<font color='red'>审核不通过</font>";break;}?></td>
                                    <td>
									<?php if($val['is_check'] == 1){?>
										<a href='/seek_rent_review/edit_entrust_review/<?=$val['id']?>'>查看</a> | <a href='/seek_rent_review/edit_entrust/<?=$val['id']?>'>审核</a>
									<?php }else{?>
										<a href='/seek_rent_review/edit_entrust_review/<?=$val['id']?>'>查看</a>
									<?php }?>
									</td>
                                </tr>
                                <?php }}else{
                                       echo "<tr class='gradeA'><td colspan=15 style='text-align:center;color:red;font-weight:bold;'>暂无您查询的出售委托数据~！</td></tr>";
                                }?>
                            </tbody>
                        </table>
						<input type="hidden" name="pg" value="1">
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
				</form>
            </div>
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
		url: '/entrust_sell_review/del_pop/'+id,
		//data: {broker_id:broker_id},
		dataType: 'json',
		success: function(data){
			$("#ent_id").val(data);
			//$("#js_del").show();
			openWin("js_del");
		}/*,
		error: function(xhr, type){
			alert('Ajax error!');
			me.resetload();
		}*/
	});

}
function del(){
	var ent_id = $("#ent_id").val();
	var del_reason = $("#del_reason option:selected").val();
	$.ajax({
		type: 'GET',
		url: '/entrust_sell_review/del/'+ent_id,
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




