<div class="pop_box_g" id="js_genjin" style="display:block">
    <div class="hd">
        <div class="title">房源跟进</div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="clearfix pop_fg_fun_box">
                <div class="text left text_title">跟进明细</div>
                <div class="get_page"><span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to03">跳转</a>
                    <div id="js_f_input03" class="f_input hide"> <span class="tex">跳转到第</span>
                        <input class="input" type="text">
                        <span class="tex">页</span> <a class="b_link" href="javascript:void(0)">确定</a> </div>
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
                        <td><?php echo $type_list[$val['follow_way']]['follow_name'];?></td>
                        <td><?php echo $val['text'];?></td>
                        <td><?php echo $customer_list[$val['customer_id']]['truename'];?></td>
                        <td><?php echo $broker_name;?></td>
                    </tr>
					<?php }
				   }else{
					?>
					<tr><td colspan="24">抱歉，该房源还没有添加跟进信息</td></tr>
					<?php }?>
                </table>
            </div>
            <h3 class="title"> 房源跟进<span class="text">(房源010202)</span></h3>

            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进日期：</p>
                    <p class="i_text"><?php echo $time?></p>
                    <p class="t_text">跟进方式：</p>
                    <div class="i_text">
                        <label class="label">
                            <input type="radio"value="1" name="radio01">
                            看房</label>
                        <label class="label">
                            <input type="radio" value="2"  name="radio01">
                            修改</label>
                        <label class="label">
                            <input type="radio" value="3"  name="radio01">
                            电话</label>
                        <label class="label">
                            <input type="radio"  value="4"name="radio01">
                            磋商</label>
                        <label class="label">
                            <input type="radio"  value="5" name="radio01">
                            带看</label>
                        <label class="label">
                            <input type="radio"  value="6" name="radio01">
                            其它</label>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">带看员工：</p>
                    <p class="i_text"><?php echo $broker_name?></p>
                    <div class="left">
                        <p class="t_text">客户类型：</p>
                        <div class="i_text">

                               <?php
							   if($house_type==1){
								   echo ' 出售';
							   }elseif($house_type==2){
								   echo '出租';
							   }
							   ?>
                        </div>
                    </div>
                    <div class="left">
                        <p class="t_text">客户姓名：</p>
                        <input type="text" class="k_input" id="kputid" value="">
                    </div>
                </div>
				 <input type="hidden" value="<?php echo $house_id?>" id="house_id"/>
				 <input type="hidden"  id="cn_id">
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进内容：</p>
                    <textarea class="textarea" id="textid"></textarea>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_label">
                        <label>
                            <input type="checkbox">
                            提醒</label>
                    </p>
                </div>
                <div class="inner_in">
                    <div class="in_fg clearfix">
                        <p class="text_t">提醒日期：</p>
                        <input class="input_text w90" type="text">
                    </div>
                    <div class="in_fg clearfix">
                        <p class="text_t">提醒内容：</p>
                        <input class="input_text w570"  type="text">
                    </div>
                </div>
            </div>

            <a class="save_btn" id="save_sub">保存</a> </div>
    </div>
	</div>
	<script type="text/javascript">
	$(function(){
		$("#save_sub").click(function(){
	    var follow_type=$("input[name=radio01]:checked").val();//跟进方式
		var text=$("#textid").val();//跟进内容
		var cname=$("#cn_id").val();//客户id
		var house_id=$("#house_id").val();//房源id
		var addata={
		'follow_type':follow_type,
        'text':text,
        'cname':cname,
		'house_id':house_id
		};


		$.ajax({
         url:'<?php echo MLS_URL;?>/sell/addfollow',
         type:'get',
         data:addata,
         success:function(data){

			 if(data =1){

				 $("#dialog_do_itp").html("添加成功");
				  openWin('js_pop_do_success');
				  //$(window.parent.document).find("#js_genjin").hide();

			 }else if(data = 2){

				$("#dialog_do_itp").html("添加失败");
				 openWin('js_pop_do_success');
			 }
		 }
		});
		});
	});

	//查看个人的客源
	$(function(){
        $("#kputid").focus(function(){
        var _url = '<?php echo MLS_URL;?>/sell/source';
        if(_url)
        {
         $("#js_keyuan .iframePop").attr("src",_url);
        }
        openWin('js_keyuan');
		});
	});
</script>
<!--客户信息弹框-->
<div id="js_keyuan" class="iframePopBox" style=" width:505px; height:345px; ">
 <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
<iframe frameborder="0" scrolling="no" width="505" height="345" class='iframePop' src=""></iframe>
</div>

<!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>
