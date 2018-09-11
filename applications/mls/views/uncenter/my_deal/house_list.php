<div id="js_add_house_p" class="pop_box_g pop_box_g03" style="width:540px">
    <div class="hd">
        <div class="title">添加成交房源</div>
    </div>
    <div class="mod">
        <div class="inner inner02">
            <form method='post' action='' id='search_form'>
            <div class="inner_ky_box">
                <div class="title">
                    房源编号：
                    <input type="text" class="input_t input_t_p" name="house_id" value="<?php echo $post_param['house_id']; ?>">
                    &nbsp;&nbsp;小区名称：
                    <input type="text" class="input_t input_t_p" name="block_name" id="block_name" value="<?php echo $post_param['block_name']; ?>">
                    <input name="block_id" id="block_id" value="<?php echo $post_param['block_id']?>" type="hidden">
                    
                    <script type="text/javascript">
                        $(function(){
							$.widget( "custom.autocomplete", $.ui.autocomplete, {
								_renderItem: function( ul, item ) {
									if(item.id>0){
										return $( "<li>" )
										.data( "item.autocomplete", item )  
										.append('<a class="ui-corner-all" tabindex="-1"><span class="ui_name">'+item.label+'</span><span class="ui_district">'+item.districtname+'</span><span class="ui_address">'+item.address+'</span></a>')
										.appendTo( ul );
									}else{
										return $( "<li>" )
										.data( "item.autocomplete", item )  
										.append('<a class="ui-corner-all" tabindex="-1">'+item.label+'</a>')
										.appendTo( ul );
									}
								}
							});
                            $("#block_name").autocomplete({                    
                                source: function( request, response ) {
                                    var term = request.term;
                                    $("#block_id").val("");
                                    $.ajax({
                                        url: "/community/get_cmtinfo_by_kw/",
                                        type: "GET",
                                        dataType: "json",
                                        data: {
                                            keyword: term
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
                                    if(ui.item.id > 0){
                                        var blockname = ui.item.label;
                                        var id = ui.item.id;
                                        var streetid = ui.item.streetid;
                                        var streetname = ui.item.streetname;
                                        var dist_id = ui.item.dist_id;
                                        var districtname = ui.item.districtname;
                                        var address = ui.item.address;

                                        //操作
                                        $("#block_id").val(id);                            
                                        $("#block_name").val(blockname);
                                        removeinput = 2;
                                    }else{
                                        openWin('js_pop_add_new_block');
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
                    </script>
                    
                    <button type="submit" class="btn-lv1" style="margin-left:7px;">查询</button>
                </div>
						<div class="clear">&nbsp;</div>
                <table class="table">
                    <?php 
                    if($list)
                    {
                        foreach($list as $key => $val)
                        {
                    ?>
                    <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> onclick="window.parent.open_js_pop_add_info_house('my_deal_<?php echo $type;?>',<?php echo $val['id']; ?>);" style="cursor:pointer;">
                        <td>
                            <div class="item">
                                <span class="id_house"><?php echo $val['id']; ?></span>
                                |
                                <span class="info_house">
                                    <?php echo $val['block_name']; ?>
                                    <?php echo $val['room']; ?>室<?php echo $val['hall']; ?>厅<?php echo $val['toilet']; ?>卫
                                    <?php echo strip_end_0($val['buildarea']); ?>㎡
                                    <?php echo $val['buildyear']; ?>
                                </span>
                                <span class="pirce_house"><?php echo $val['price']; ?><?php if($type == "sell"){echo "万";}elseif($type == "rent"){echo "元/月";}?></span>
                            </div>
                        </td>
                    </tr>
                    <?php        
                        }
                    }else{
                    ?>
                    <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
                    <?php }?>
                   
               </table>
            </div>
            <div class="clearfix pop_fg_fun_box">
                <div class="get_page">
                    <?php echo $page_list;?>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
$(function(){
    openWin('js_add_house_p');
});
</script>

