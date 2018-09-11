<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title ?></h1>
            </div>
        </div>
        <div class="row">
            <form name="search_form" method="post" action="" >
                <input type="hidden" name="pg" value="1">
            </form>
            <ul class="shop_tab_title">
                <a class="btn btn-primary link <?php if (!$type) {
                    echo 'link_on';
                } ?>" href="/broker_trust_manage/info_detail/<?php echo $broker_id ?>/?type=0">来自合作方的评价<span
                            class="iconfont hide">&#xe607;</span></a>&nbsp;&nbsp;
                <a class="btn btn-primary link <?php if ($type === 1) {
                    echo 'link_on';
                } ?>" href="/broker_trust_manage/info_detail/<?php echo $broker_id ?>/?type=1">我给合作方的评价<span
                            class="iconfont hide">&#xe607;</span></a>&nbsp;&nbsp;
                <a class="btn btn-primary link <?php if ($type === 2) {
                    echo 'link_on';
                } ?>" href="/broker_trust_manage/info_punish/<?php echo $broker_id ?>/?type=2">处罚记录<span
                            class="iconfont hide">&#xe607;</span></a>
        	</ul>

        	<table class="table table-striped table-bordered table-hover">
                <thead>
            		<tr>
            			<th rowspan="2" style="text-align:center;vertical-align:middle" >交易编号</th>
            			<th rowspan="2" style="text-align:center;vertical-align:middle" >合作房源</th>
            			<th rowspan="2" style="text-align:center;vertical-align:middle" >整体评价</th>
            			<th colspan="3" style="text-align:center" >细节评价</th>
            			<th rowspan="2" style="text-align:center;vertical-align:middle" >评价内容</th>
            			<th rowspan="2" style="text-align:center;vertical-align:middle" >评价时间</th>
                        <th rowspan="2" style="text-align:center;vertical-align:middle">合作方</th>
            		</tr>
                    <tr>
                        <th style="text-align:center" >信息真实度</th>
                        <th style="text-align:center" >态度满意度</th>
                        <th style="text-align:center" >业务专业度</th>
                    </tr>
                </thead>
                <tbody>
            		<?php
                    if($cooperate_info){
                        foreach ($cooperate_info as $key=>$value){
                    ?>
                    <tr>
                        <td><?php echo $value['transaction_id'] ?></td>
                        <td>
                        <?php
                        $house_info = unserialize($value['house_info']);
                        $price_unit = $house_info['tbl'] == 'rent' ? '元/月' : 'w';
                        echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                        $house_info['blockname'].' '.$house_info['room'].'室'.$house_info['hall'].'厅'.
                            $house_info['toilet'] . '卫 ' . $house_info['fitment'] . ' ' . $house_info['forward'] . ' ' . $house_info['buildarea'] . ' ㎡ ' . $house_info['price'] . $price_unit;

                        ?></td>
                        <td><?php echo $value['trust_name'] ?></td>
                        <td><?php echo $value['info_star'] ?></td>
                        <td><?php echo $value['atti_star'] ?></td>
                        <td><?php echo $value['busi_star'] ?></td>
                        <td><?php echo $value['content'] ?></td>
                        <td><?php echo date('Y-m-d H:i:s', $value['create_time']) ?></td>
                        <td><?php echo $value['truename'] ?><br><?php echo $value['broker_level']['level']; ?></td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
        	</table>
        </div>
        <div class="row">
           <div class="col-sm-6">
             <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                    <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/info_detail/');?>
                </ul>
             </div>
           </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function modify(id){
	var data = {id:id};
	$.ajax({
		type: "POST",
		url: "/broker_trust_manage/modify/",
		data:data,
		cache:false,
		error:function(){
			alert("系统错误");
			return false;
		},
		success: function(data){
			alert(data);
            window.location.href = "/broker_trust_manage/info_detail/<?php echo $broker_id ?>?type=<?php echo $type?>";
		}
	});
}
</script>
<?php require APPPATH . 'views/footer.php'; ?>
