<script>
    window.parent.addNavClass(22);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="oversea_main">
	<form action='/abroad/index' method='post' id='search_form' name='search_form'>
        <!--筛选-->
        <span class="oversea_choice">
             <dl class="oversea_dl">
                 <dd class="oversea_dl_f">国家：</dd>
                 <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='country' style='cursor:pointer;'>全部</p>
                    <?php foreach($country as $val){?>
                    <p class="oversea_check_bj <?=isset($post_param['country_id']) && in_array($val['id'],$post_param['country_id'])?'check_bjOn':'';?>"><b class="oversea_check oversea_check_opacity"><input type="checkbox"  name='country_id[]' value="<?php echo $val['id']?>" class="oversea_check_dis" <?=isset($post_param['country_id']) && in_array($val['id'],$post_param['country_id'])?'checked':'';?> style='display:inline'/></b><?=$val['country_name']?></p>
					<?php }?>
                 </dt>
             </dl>
            <dl class="oversea_dl">
                <dd class="oversea_dl_f">城市：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='city' style='cursor:pointer;'>全部</p>
					<?php foreach($city as $val){?>
                    <p class="oversea_check_bj <?=isset($post_param['city_id']) && in_array($val['id'],$post_param['city_id'])?'check_bjOn':'';?>"><b class="oversea_check oversea_check_opacity "><input type="checkbox" name='city_id[]' value="<?=$val['id']?>" class="oversea_check_dis" <?=isset($post_param['city_id']) && in_array($val['id'],$post_param['city_id'])?'checked':'';?> style='display:inline'/></b><?=$val['city_name']?></p>
					<?php }?>
                </dt>
            </dl>
			<dl class="oversea_dl oversea_dl_f">
                <dd class="oversea_dl_f">类型：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='house_type' style='cursor:pointer;'>全部</p>
					<?php foreach($config['abroad_house_type'] as $key=>$val){?>
						<p class="oversea_check_bj <?=isset($post_param['house_type']) && in_array($key,$post_param['house_type'])?'check_bjOn':'';?>"><b class="oversea_check oversea_check_opacity"><input type="checkbox" name='house_type[]' value="<?=$key?>" class="oversea_check_dis" <?=isset($post_param['house_type']) && in_array($key,$post_param['house_type'])?'checked':'';?> style='display:inline'/></b><?=$val?></p>
					<?php }?>
                </dt>
            </dl> 
            <dl class="oversea_dl oversea_dl_f">
                <dd class="oversea_dl_f">总价：</dd>
                <dt class="oversea_dl_f">
                    <p class="oversea_c_fff" id='price' style='cursor:pointer;'>全部</p>
					<?php foreach($config['abroad_price'] as $key=>$val){?>
						<p class="oversea_radio_bj <?=isset($post_param['price']) && $key== $post_param['price']?'radio_bjOn':'';?>"><b class="oversea_check oversea_radio_opacity"><input type="radio" name='price' value="<?=$key?>" class="oversea_check_dis" <?=isset($post_param['price']) && $key== $post_param['price']?'checked':'';?> style='display:inline'/></b><?=$val?></p>
					<?php }?>
                </dt>
            </dl>     
            
        </span>
        <div class="oversea_list_sort">
            <p>默认排序</p>
            <span class="span">共为您找到相关楼盘 <b><?=$total_count;?></b>套</span>
        </div>
		<script>
			$(function(){
				$("input[type=checkbox]").bind("click",function(){
					
					$("#search_form").submit();
					
				});
				$("input[type=radio]").bind("click",function(){
					$("#search_form").submit();
					
				});
				$("#country").bind("click",function(){
					$("input[name='country_id[]']").attr('checked',false);
					
					$("#search_form").submit();
					
				});
				$("#city").bind("click",function(){
					$("input[name='city_id[]']").attr('checked',false);
					$("#search_form").submit();
					
				});
				$("#house_type").bind("click",function(){
					$("input[name='house_type[]']").attr('checked',false);
					$("#search_form").submit();
					
				});
				$("#price").bind("click",function(){
					$("input[name='price']").attr('checked',false);
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
        <!--列表页-->
        <div class="oversea_list">
            <ul>
			<?php if($list){
					foreach($list as $val){
			?>
                <li>
                    <span class="oversea_list_img">
                        <a href="/abroad/detail/<?=$val['id']?>"><img src="<?=changepic($val['pic'])?>" alt="<?=$val['house_type']?>" ></a>
                        <p><?=$val['house_type']?></p>
                    </span>
                    <div class="oversea_list_detial">
                        <!--信息部分-->
                        <span class="oversea_list_detial_left">
                            <h2 class="oversea_dl_f"><a href="/abroad/detail/<?=$val['id']?>" title="<?=$val['block_name']?>"><?=$val['block_name']?></a></h2>
                            <p class="oversea_list_detial_left_map oversea_dl_f"><?=$val['country_name']?> - <?=$val['city_name']?></p>
                            <p class="oversea_list_detial_lef_p oversea_dl_f">楼盘地址：<?=$val['address']?></p>
                            <span class="oversea_list_detial_left_tag oversea_dl_f">
                                <p class="oversea_dl_f"><?=$val['feature']?></p>
                            </span>
                        </span>
                        <div class="oversea_list_detial_right">
                            <span class="oversea_list_detial_right_sf oversea_dl_f">首付：
								<p>
									<b><?php if(strlen($val['first_pay']) <= 10){?><?=strip_end_0($val['first_pay'])?><?php }else{?><?php echo substr_for_string($val['first_pay'],10)?><?php }?></b>
									万<?=$val['money_unit']?>起
								</p>
							</span>
                            <span class="oversea_list_detial_right_sf oversea_dl_f">佣金：<?php if($val['brokerage_type'] == 1){?><p><b><?=$val['brokerage']?></b>元</p><?php }else{?><p><b><?=$val['brokerage']?></b>成交价</p><?php }?></span>
                            <span class="oversea_list_detial_right_sf oversea_dl_f"><a href="/abroad/detail/<?=$val['id']?>"><strong>查看详情</strong></a></span>
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
				var aBody_H = $(window).height()-60;
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