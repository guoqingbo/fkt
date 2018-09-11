<div class="pop_box_g" id="js_genjin" style="display:block; border:none;">
    <div class="hd">
        <div class="title">房源跟进明细</div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="clearfix pop_fg_fun_box">
                <div class="text left text_title">房源跟进明细</div>
                <div class="get_page">
                     <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
                </div>
            </div>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="w160">跟进日期</th>
                        <th class="w110">类别</th>
                        <th class="w240">内容</th>
                        <th class="w130">带看/成交客户</th>
                        <th width="127">跟进人</th>
                    </tr>
                  
                   <?php if($follow_lists){
					   
					   foreach($follow_lists as $key=>$val){
					   ?>
                    
                    <tr class="bg">
                        <td><?php echo $val['date'];?></td>
                        <td><?php echo $follow_config['follow_way'][$val['follow_way']];?></td>
                        <td><?php echo $val['text'];?></td>
                        <td><?php 
						if($val['customer_id']){
							echo $customer_list[$val['customer_id']]['truename'];
						}
						?></td>
                        <td><?php 
						if(empty($val['broker_id'])){
							echo '系统管理员';
						}else{
							echo $broker_name;	
						}
						?></td>
                    </tr>
					<?php }
				   }else{
					?>
					<tr><td colspan="24">抱歉，该房源还没有添加跟进信息</td></tr>
					<?php }?>
                </table>
            </div>
           
			
            </div>
    </div>
	</div>
	