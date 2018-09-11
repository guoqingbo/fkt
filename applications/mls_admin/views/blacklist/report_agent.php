<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<style>
	table{border:1px solid grey;width:400px;height:280px;text-align:center;}
	tr td{border:1px solid grey;}
	td th{padding:2px;}
	#do{text-align:center;}
	.left{text-align:right;width:40%;}
</style>
<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">中介举报审核操作</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<form name="search_form" method="post" action="" >
			<table>
				<tr><th colspan=2 id="do">操作</th></tr>
                <?php if (is_full_array($house_detail)) { ?>
                <tr><td class="left" style="height:100px;">房源基本信息：</td><td>楼盘名称：<?php echo $house_detail[0]['house_name'];?>
                <br>来源：<?php if($house_detail[0]['source_from']==0){echo '赶集网';}else if($house_detail[0]['source_from']== 1 ){echo '58同城';}else if($house_detail[0]['source_from']== 2 ){echo '房天下';}else if($house_detail[0]['source_from']== 5 ){echo '吴江房产网';}else if($house_detail[0]['source_from']== 6 ){echo '亿房网';}else if($house_detail[0]['source_from']== 7 ){echo '联合网';}else if($house_detail[0]['source_from']== 8 ){echo '小鱼网';}?>
                <br>面积：<?php echo $house_detail[0]['buildarea'];?>
                <br>价格：<?php echo $house_detail[0]['price'];?>
                <?php echo $report_agent[0]['tbl'] == 1 ? '万' : '元/月';?>
                </td></tr>
                <?php } ?>
				<tr><td class="left">待审核号码：</td><td><?php echo $report_agent[0]['r_tel'];?></td></tr>
				<tr><td class="left">举报原因：</td><td><?php echo $report_agent[0]['r_reason'];?></td></tr>
				<tr><td class="left">举报人：</td><td><?php echo $report_agent[0]['r_person'];?></td></tr>
				<tr><td class="left"><label for="blacklist"><input type="radio" value="blacklist" id="blacklist" name="action[]" <?php if($report_agent[0]['r_status']==2){echo "checked='checked'";}?> <?php if($report_agent[0]['r_status']==1){?>disabled<?php }?>/><?php if($report_agent[0]['r_status']==1){?><span style="color:grey"><?php }else{?><span style="color:red"><?php }?><b>加入黑名单</b></span></label></td><td><label for="reject"><input type="radio" value="reject" id="reject" name="action[]" <?php if($report_agent[0]['r_status']==1){echo "checked='checked'";}?><?php if($report_agent[0]['r_status']==2){?>disabled<?php }?>/><?php if($report_agent[0]['r_status']==2){?><span style="color:grey"><?php }else{?><span style="color:red"><?php }?><b>拒绝</b></span></label></td></tr>
				<tr><td class="left">备注：</td><td><textarea  rows="5" cols="20" name="r_comment" id='r_comment'><?php if(!empty($report_agent[0]['r_comment'])){echo $report_agent[0]['r_comment'];}?></textarea></td></tr>
				<tr><td colspan=2 ><input type="submit" name="submit" id="submit" value="确定">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="cancel" id="cancel" value="取消" onclick="goback()"></td></tr>
				<input type="hidden" name="checkout" value="angel_in_us">
				<input type="hidden" name="r_tel" value="<?php echo $report_agent[0]['r_tel'];?>">
				<input type="hidden" name="r_reason" value="<?php echo $report_agent[0]['r_reason'];?>">
				<input type="hidden" name="broker_id" value="<?php echo $report_agent[0]['broker_id'];?>">
				<input type="hidden" name="r_id" value="<?php echo $report_agent[0]['r_id'];?>">
			</table>
			</form>
        </div>
        <!-- /#page-wrapper -->

    </div>
<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL ?>/index.php/blacklist/reportlist";
	}
</script>
<?php require APPPATH.'views/footer.php'; ?>
