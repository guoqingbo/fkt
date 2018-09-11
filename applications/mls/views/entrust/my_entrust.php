<script type="text/javascript">
window.parent.addNavClass(17);
</script>
<body>
<div class="tab_box" id="js_tab_box">
    <p class="right"><img width="16" height="16" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico1.png"> 您的房源描述会在前台展示，网友会根据房源的描述质量判断您的专业度，完善的信息会给您带来更多来电及成交机会！</p>
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>

<div class="search_box clearfix"  id="js_search_box_02">
    <form name="search_form" id="search_form" method="post" action="">
        <input type="hidden" name="page" value="1">
    </form>
</div>

<div class="table_all">
    <div class="inner shop_inner" id="js_inner">
        <table class="table table2">
            <?php
            if($my_entrust_list){
                foreach ($my_entrust_list as $key=>$value){
            ?>
            <tr>
				<td width="2%">
				    <div class="info" style="text-align:right;">
				    <?php if($value['pics']){?>
                        <span title="此房源有图片" class="iconfont ts"></span>
                    <?php }?>
				    </div>
			    </td>
				<td width="9.5%">
					<div class="info">
                        <div class="tit-1"><?=$value['blockname'];?></div>
                    </div>
				</td>
                <td width="19%"><div class="info" style="text-align:left;"><?=$value['district_street']?> | <?=$value['housetype']?> | <?=strip_end_0($value['buildarea'])?>平米</div></td>
                <td width="8%"><div class="info"><em class="num-16"><?=strip_end_0($value['price'])?></em> 万</div></td>
                <td width="15%"><div class="info"><?=date('Y-m-d H:i:s', $value['dateline']) ?></div></td>
                <td width="14.5%"><div class="info">已抢委托经纪人（<em class="num-12"><?=$value['num']?></em>）</div></td>
                <td width="11%"><div class="info">房源评价（<em class="num-12"><?=$value['appraise_total']?></em>）</div></td>
  				<td><div class="info">
  				<?php
                if($value['status'] == 1){
                ?>
  				<a class="btn-jia" href="/entrust/entrust_detail/<?=$value['houseid']?>"><span>房源评价</span></a>
  				<?php
                }else{
  				?>
  				<a class="btn-hui btn-hui2"><span>房源已下架</span></a>
  				<?php }?>
  				</div></td>
            </tr>
			<?php
                }
            }else{
                echo '<tr><td colspan="6">暂无已抢房源</td></tr>';
			}
			?>
        </table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>

<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
