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
                    <h1 class="page-header">租房采集房源电话审核</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
			<form name="search_form" method="post" action="" >
			<table>
				<tr><th colspan=2 id="do">操作</th></tr>
				<tr>
					<td>待审核号码：</td>
					<td>
						<input type="text" name="tel" class="input-sm"  value="<?php echo $rent_house_collect['telno1'];?>" />
						<?php if(strlen($rent_house_collect['tel_url'])>15){?>
							<a alt='查看号码' target='_blank' href="<?php echo $rent_house_collect['tel_url'];?>">查看号码</a>
						<?php }else{?>
							<img src="<?php echo $rent_house_collect['tel_url'];?>"/>
						<?php }?>
					</td>
				</tr>
				<tr>
					<td><a target="_blank" href="<?php echo $rent_house_collect['oldurl'];?>" >查看原网址</a></td>
					<td><a target="_blank" href="http://www.baidu.com/s?wd=<?php echo $rent_house_collect['telno1'];?>" >人工查询</a></td>
				</tr>
				<tr>
				<td><label for="blacklist"><input type="radio" value="blacklist" id="blacklist" name="act"/><span style="color:red"><b>加入黑名单</b></span></label></td>
				<td><label for="pass"><input type="radio" value="pass" id="pass" name="act"/><span style="color:red"><b>通过</b></span></label></td>
				</tr>
				<tr><td colspan=2 ><input type="submit" name="submit" id="submit" value="确定">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="cancel" id="cancel" value="取消" onclick="goback()"></td></tr>
				<input type="hidden" name="checkout" value="angel_in_us" />
			</table>
			</form>
        </div>
        <!-- /#page-wrapper -->

    </div>
<script>
	function	goback(){
		location.href = "<?=MLS_ADMIN_URL?>/rent_house_collect/";
	}
</script>
<?php require APPPATH.'views/footer.php'; ?>
