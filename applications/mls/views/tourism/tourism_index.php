<script>
    window.parent.addNavClass(23);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="oversea_main">
	<form action='/tourism/index' method='post' id='search_form' name='search_form'>
        <!--筛选-->
        <span class="oversea_choice">
             
            <dl class="oversea_dl">
                <dd class="oversea_dl_f">城市：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='province' style='cursor:pointer;'>全部</p>
                    <?php foreach($province as $val){?>
                    <p class="oversea_check_bj <?=isset($post_param['province_id']) && in_array($val['id'],$post_param['province_id'])?'check_bjOn':'';?>"><b class="oversea_check oversea_check_opacity"><input type="checkbox"  name='province_id[]' value="<?php echo $val['id']?>" class="oversea_check_dis" <?=isset($post_param['province_id']) && in_array($val['id'],$post_param['province_id'])?'checked':'';?> style='display:inline'/></b><?=$val['province_name']?></p>
					<?php }?>
                </dt>
            </dl> 
             
            <dl class="oversea_dl">
                <dd class="oversea_dl_f">特点：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='feature' style='cursor:pointer;'>全部</p>
					<?php foreach($config['tourism_house_feature'] as $key=>$val){?>
						<p class="oversea_check_bj <?=isset($post_param['feature']) && in_array($key,$post_param['feature'])?'check_bjOn':'';?> "><b class="oversea_check oversea_check_opacity"><input type="checkbox" name='feature[]' value="<?=$key?>" class="oversea_check_dis" <?=isset($post_param['feature']) && in_array($key,$post_param['feature'])?'checked':'';?> style='display:inline'/></b><?=$val?></p>
					<?php }?>
                </dt>
            </dl>  
			<dl class="oversea_dl oversea_dl_f">
                <dd class="oversea_dl_f">均价：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='avg_price' style='cursor:pointer;'>全部</p>
					<?php foreach($config['tourism_price'] as $key=>$val){?>
						<p class="oversea_radio_bj <?=isset($post_param['avg_price']) && $key == $post_param['avg_price']?'radio_bjOn':'';?>"><b class="oversea_check oversea_radio_opacity"><input type="radio" name='avg_price' value="<?=$key?>" class="oversea_check_dis" <?=isset($post_param['avg_price']) && $key == $post_param['avg_price']?'checked':'';?> style='display:inline'/></b><?=$val?></p>
					<?php }?>
                </dt>
            </dl>  
        </span>
		<script>
			$(function(){
				$("input[type=checkbox]").bind("click",function(){
					$("#search_form").submit();
				});
				$("input[type=radio]").bind("click",function(){
					$("#search_form").submit();
				});
				$("#province").bind("click",function(){
					$("input[name='province_id[]']").attr('checked',false);
					$("#search_form").submit();
				});
				$("#feature").bind("click",function(){
					$("input[name='feature[]']").attr('checked',false);
					$("#search_form").submit();
				});
				$("#avg_price").bind("click",function(){
					$("input[name='avg_price']").attr('checked',false);
					$("#search_form").submit();
				});
				
				$(".oversea_check_bj").click(function(){
					$(this).addClass("check_bjOn");
				})
				$(".check_bjOn").click(function(){
					$(this).removeClass("check_bjOn");
					
				})
				
				$(".oversea_radio_bj").click(function(){
					$(this).addClass("radio_bjOn");
				})
				$(".radio_bjOn").click(function(){
					$(this).removeClass("radio_bjOn");
					
				})
			});
		</script>
        <div class="oversea_list_sort">
            <p>默认排序</p>
            <span class="span">共为您找到相关楼盘 <b><?=$total_count;?></b>套</span>
        </div>
        <!--列表页-->
        <div class="oversea_list">
            <ul>
			<?php
				if($list){
					foreach($list as $val){
			?>
                <li>
                    <span class="oversea_list_img">
                        <a href="/tourism/detail/<?=$val['id']?>"><img src="<?=changepic($val['pic'])?>" alt="<?=$val['house_type']?>"></a>
                        
                    </span>
                    <div class="oversea_list_detial">
                        <!--信息部分-->
                        <span class="oversea_list_detial_left">
                            <h2 class="oversea_dl_f"><a href="/tourism/detail/<?=$val['id']?>" title="<?=$val['block_name']?>   【<?=$val['province_name']?> <?=$val['city_name']?>】 "><?=$val['block_name']?>   【<?=$val['province_name']?> <?=$val['city_name']?>】 </a></h2>
                            
                            <p class="oversea_list_detial_lef_p oversea_dl_f">楼盘地址：<?=$val['address']?></p>
                            <span class="oversea_list_detial_left_tag oversea_dl_f">
							<?php 
								if($val['feature']){
									foreach($val['feature'] as $v){
							?>
                                <p class="oversea_dl_f"><?=$v;?></p>
							<?php }}?>
                            </span>
                        </span>
                        <div class="oversea_list_detial_right">
                            <span class="oversea_list_detial_right_sf oversea_dl_f">价格：<p><b><?=intval($val['avg_price'])?></b>元/m²</p></span>
                            <!--<span class="oversea_list_detial_right_sf oversea_dl_f">佣金：<?php if($val['brokerage_type'] == 1){?><p><b><?=$val['brokerage']?></b>元</p><?php }else{?><p><b><?=$val['brokerage']?></b>成交价</p><?php }?></span>-->
                            <span class="oversea_list_detial_right_sf oversea_dl_f" style="float: right"><a href="/tourism/detail/<?=$val['id']?>"><strong>查看详情</strong></a></span>
                        </div>
                    </div>
                </li>
			<?php }}?>
            </ul>

        </div>

        <!--分页-->
        <div class="over_sea_page oversea_dl_f">
          <div style='float:right'><?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?></div>

        </div>
	</form>
    </div>
    <script type="text/javascript">
        $(function () {
            function reWidth() {

              var aLi_W = $(".oversea_list li").width()*0.98;
				var aBody_H = $(window).height()-40;
				$(".oversea_main").css("height",aBody_H+"px");
				$(".oversea_main").css("overflow-y","auto");
                //console.log(aLi_W);
                $(".oversea_list_detial").css("width", (aLi_W - 218) + "px");
                $(".oversea_list_detial_right").css("padding-top", (150 - $(".oversea_list_detial_right").height()) / 2 + "px");
            }
            reWidth();

            $(window).resize(function () {
                reWidth();
            })

        })
        

    </script>



</body>
</html>