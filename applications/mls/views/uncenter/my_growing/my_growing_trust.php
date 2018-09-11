<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div id="js_search_box">
    <div  class="shop_tab_title">
        <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
    </div>
</div>


<div class="table_all">
	<div class="title shop_title" id="js_title">
        <table class="table">
           <tr>
              	<td class="c20"><div class="info">时间</div></td>
                <td class="c15"><div class="info">操作</div></td>
                <td class="c20"><div class="info">当前信用分</div></td>
                <td class="c20"><div class="info"><span class="left">当前信用等级</span></div></td>
                <td ><div class="info">说明</div></td>
            </tr>
     	</table>
    </div>
    <div id="js_inner" class="inner shop_inner">
        <table class="table">
            <?php 
            if($trust_info){
               $num=0; 
                foreach ($trust_info as $key=>$value){
                    $num = $num+1;
            ?>
            <tr <?php if($num%2==0){echo 'class="bg"';}?>>
            
             	<td class="c20"><div class="info"><?=date('Y-m-d H:i:s', $value['create_time'])?></div></td>
                <td class="c15"><div class="info"><p class="s"><?=$value['score']?></p></div></td>
                <td class="c20"><div class="info"><?=$value['trust']?>分</div></td>
                <td class="c20"><div class="info"><?=$value['trust_name']?></div></td>
                <td ><div class="info"><p class="left"><?=$value['alias_name']?></p></div></td>
               
            </tr>
            <?php 
                    
                }
                $num++;
            }  
            ?>
            
         </table>
    </div>
</div>

<div id="js_fun_btn" class="fun_btn fun_btn_bottom clearfix">
    <div class="get_page">
        <form name="search_form" id="search_form" method="post" action="/my_growing_trust/" >
            <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?> 
        </form>				
    </div>
</div>

