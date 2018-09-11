<div id="js_pop_publish_house_now" class="pop_box_g pop_see_inform pop_publish_house_now "  style='display: block;'>
    <div class="hd">
        <div class="title">群发房源</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="iconfont JS_Close" ></a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
         	<div class="house_list">
             	<div class="item">
					<table class="p_table">
                        <?php foreach($list as $key => $val){ ?>
                    	<tr>
                          <td class="house_id"><?php echo $val['id']; ?></td>
                          <td>
                            <dl>
                                <?php foreach($siteinfo as $k=>$v){ ?>
                                <??>
                                <dd><span class="name"><?php echo $v['name'];?></span><span id='publish_<?php echo $v['id'];?>'>正在发布...</span></dd>
                                <?php } ?>
                            </dl>
                          </td>
                       </tr>
                       <?php }?>
                    </table>
                </div>
             </div>
         </div>
    </div>
</div>


<div id="js_del_z_pop" class="pop_box_g pop_see_inform pop_no_q_up pop_add_block_zd">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="iconfont" onclick="fun_goon(0);$('#js_del_z_pop').hide();$('#GTipsCoverjs_del_z_pop').remove();" >&#xe60c;</a></div>
    </div>
    <div class="mod">
    	<div class="inform_inner">
            <div class="up_inner">																				
                <p class="text_zd"><span id="site_name"></span>楼盘名称:</p>
                <div class="clearfix text_input_zd_b">
                    <input type="text" id="block_name" name="block_name" class="text_input_zd">
                    <input type="hidden" id="block_id" name="block_id" class="text_input_zd">
                    <input type="hidden" id="address" name="address" class="text_input_zd">
                    <input type="hidden" id="district" name="district" class="text_input_zd">
                    <input type="hidden" id="street" name="street" class="text_input_zd">
                    <input type="hidden" id="ajax_url" value="">
                </div>
                <button class="btn-lv1" type="button" onclick="fun_goon();$('#js_del_z_pop').hide();$('#GTipsCoverjs_del_z_pop').remove();">确定</button>
                <button class="btn-hui1" onclick="fun_goon(0);$('#js_del_z_pop').hide();$('#GTipsCoverjs_del_z_pop').remove();" type="button">取消</button>
             </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var house_id = <?php echo $house_id; ?>;
    var sell_type = <?php echo $sell_type; ?>;
    var fun_goon = '';
    var fun_next = '';
    var fun_num = 0;
    var fun_max_num = <?php echo $fun_max_num;?>;
    var control = '<?php echo $control; ?>';
    
    
    var fun_arr_sort = new Array();
    <?php echo $fun_arr_sort;?>
    
    var fun_anjuke =  function anjuke(_house_id){
        if(_house_id == 0 ){
            $("#publish_2").addClass("r");
            $("#publish_2").html('发布失败');  
            fun_num +=  1;
            if(fun_num < fun_max_num){
                fun_arr[fun_num](house_id);
            }
        }else{
            _house_id = house_id;        
            var block_id = $("#block_id").val();
            $.ajax({
                type: 'get',
                url : '/'+control+'/sending_anjuke/'+_house_id+'/'+block_id,
                dataType:'json',
                success: function(msg){
                    if(msg['flag'] == 1){
                        $("#publish_2").addClass("s");
                        $("#publish_2").html('发布成功');
                        fun_num = + 1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }else if(msg['flag'] == 2){
                        $("#ajax_url").val("/sell/get_ajk_by_keyword/");
                        fun_goon = fun_anjuke;
                        $("#block_name").val("");
                        $("#block_id").val("");
                        $("#site_name").html("安居客");
                        openWin('js_del_z_pop');
                    }else{
                        $("#publish_2").addClass("r");
                        $("#publish_2").html('发布失败');
                        fun_num +=  1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }
                }            
            });
        }
    };
    var fun_soufang = function soufang(_house_id){
        if(_house_id == 0 ){
            $("#publish_3").addClass("r");
            $("#publish_3").html('发布失败');  
            fun_num +=  1;
            if(fun_num < fun_max_num){
                fun_arr[fun_num](house_id);
            }
        }else{
            _house_id = house_id;        
            var block_name = $("#block_id").val();
            $.ajax({
                type: 'get',
                url : '/'+control+'/sending_soufang/'+_house_id,
                dataType:'json',
                data: {
                    block_name: $("#block_id").val(),
                    address: $("#address").val(),
                    district: $("#district").val(),
                    street: $("#street").val()
                },
                success: function(msg){
                    if(msg['flag'] == 1){
                        $("#publish_3").addClass("s");
                        $("#publish_3").html('发布成功');
                        fun_num = + 1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }else if(msg['flag'] == 2){
                        $("#ajax_url").val("/sell/get_sf_by_keyword/");                        
                        fun_goon = fun_soufang;
                        $("#block_name").val("");
                        $("#block_id").val("");
                        $("#site_name").html("搜房帮");
                        openWin('js_del_z_pop');
                    }else{
                        $("#publish_3").addClass("r");
                        $("#publish_3").html('发布失败');
                        fun_num += 1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }
                }            
            });
        }
    };
    
    var fun_zsb =  function zsb(_house_id){
        if(_house_id == 0 ){
            $("#publish_2").addClass("r");
            $("#publish_2").html('发布失败');  
            fun_num +=  1;
            if(fun_num < fun_max_num){
                fun_arr[fun_num](house_id);
            }
        }else{
            _house_id = house_id;        
            var block_id = $("#block_id").val();
            $.ajax({
                type: 'get',
                url : '/'+control+'/sending_zsb/'+_house_id+'/'+block_id,
                dataType:'json',
                success: function(msg){
                    if(msg['flag'] == 1){
                        $("#publish_1").addClass("s");
                        $("#publish_1").html('发布成功');
                        fun_num = + 1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }else if(msg['flag'] == 2){ //无此选项
                        $("#ajax_url").val("/sell/get_ajk_by_keyword/");
                        fun_goon = fun_anjuke;
                        $("#block_name").val("");
                        $("#block_id").val("");
                        $("#site_name").html("租售宝");
                        openWin('js_del_z_pop');
                    }else{
                        $("#publish_1").addClass("r");
                        $("#publish_1").html('发布失败');
                        fun_num +=  1;
                        if(fun_num < fun_max_num){
                            fun_arr[fun_num](house_id);
                        }
                    }
                }            
            });
        }
    };
    

    $(function(){
        $("#block_name").autocomplete({                    
            source: function( request, response ) {
                var term = request.term;
                var ajax_url = $("#ajax_url").val();
                $.ajax({
                    url: ajax_url,
                    type: "GET",
                    dataType: "json",
                    data: {
                        keyword: term,
                        sell_type: sell_type
                    },
                    success: function(data) {
                        //判断返回数据是否为空，不为空返回数据。
                        if( data[0]['id'] != '0'){
                            response(data);
                        }else{
                            response(data);
                        }	                        
                    }
                });
            },
            minLength: 1,
            removeinput: 0,
            select: function(event,ui) {
                if(ui.item.id){
                    $("#block_id").val(ui.item.id);
                    if(ui.item.address){
                        $("#address").val(ui.item.address);
                    }
                    if(ui.item.address){
                        $("#district").val(ui.item.district);
                    }
                    if(ui.item.address){
                        $("#street").val(ui.item.street);
                    }
                    removeinput = 2;
                }else{
                    removeinput = 1;
                }
            },	       
            close: function(event) {
                if(typeof(removeinput)=='undefined' || removeinput == 1){
                    $("#block_name").val("");
                    $("#block_id").val("");
                }
            }
        });
    });
    
    var fun_arr = Array(<?php echo $fun_arr;?>);
    fun_arr[0](house_id);
</script>