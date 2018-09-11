<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <div class="row">
            <form name="search_form" method="post" action="" >
                <input type="hidden" name="pg" value="1">
            </form>
            <ul class="shop_tab_title">
        		<a class="btn btn-primary link <?php if(!$type){echo 'link_on';}?>" href="/broker_trust_manage/info_detail/<?=$broker_id?>/?type=0">来自合作方的评价<span class="iconfont hide">&#xe607;</span></a>&nbsp;&nbsp;
                <a class="btn btn-primary link <?php if($type===1){echo 'link_on';}?>"  href="/broker_trust_manage/info_detail/<?=$broker_id?>/?type=1">我给合作方的评价<span class="iconfont hide">&#xe607;</span></a>&nbsp;&nbsp;
                <a class="btn btn-primary link <?php if($type===2){echo 'link_on';}?>"  href="/broker_trust_manage/info_punish/<?=$broker_id?>/?type=2">处罚记录<span class="iconfont hide">&#xe607;</span></a>
        	</ul>
        	<table class="table partner_box">
        		<tr class="ctrbg">
        			<td class="pard1">交易编号</td>
        			<td class="pard2">合作房源</td>
        			<td class="pard3">举报人</td>
        			<td class="pard4">扣分类型</td>
        			<td class="pard5">详情</td>
        			<td class="pard6">生效时间</td>
        		</tr>
        		<?php
                if($punish_info){
                    foreach ($punish_info as $key=>$value){
                ?>
                <tr>
                    <td><?=$value['number']?></td>
                    <td>
                    <?php
                    $house_info = unserialize($value['house_info']);
                    echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                    $house_info['blockname'].' '.$house_info['room'].'室'.$house_info['hall'].'厅'.
                    $house_info['toilet'].'卫 '.$house_info['fitment'].' '.$house_info['forward'].' '.$house_info['buildarea'].' ㎡ '.$house_info['price'].'W';

                    ?>
                    </td>
                    <td><?=$value['brokered_name'] ?></td>
                    <td><?=$config_info['type'][$value['type']] ?></td>
                    <td><?=$value['description']?></td>
                    <td><?=date('Y-m-d H:i:s',$value['create_time'])?></td>
                </tr>
                <?php
                    }
                }
                ?>
        	</table>
        </div>
        <div class="row">
           <div class="col-sm-6">
             <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                    <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/info_punish/');?>
                </ul>
             </div>
           </div>
        </div>
    </div>
</div>
<?php require APPPATH . 'views/footer.php'; ?>
