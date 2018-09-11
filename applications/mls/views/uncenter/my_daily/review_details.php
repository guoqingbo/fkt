<!--我的日报详情页-->
    <div class="pop_box_g zws_my_report_pop" id="js_pop_box_g" style="display:block;">
        <div class="hd">
            <div class="title">日报详情</div>
        </div>
        <div class="mod">
            <div class="tab_pop_mod clear zws_my_report_popH">
               <dl class="zws_report_dl">
               		<dd>日报标题：</dd>
               		<dt>
               			<input type="text" value="<?=$daily['title']?>" class="zws_report_input" disabled="disabled"/>
               		</dt>
               </dl>
               <dl class="zws_report_dl">
               		<dd>工作内容：</dd>
               		<dt>
               			<textarea class="zws_report_textarea" disabled="disabled"><?=$daily['content']?></textarea>
               		</dt>
               </dl>
               <dl class="zws_report_dl">
               		<dd>问题反馈：</dd>
               		<dt>
               			<textarea class="zws_report_textarea" disabled="disabled"><?=$daily['promble']?></textarea>
               		</dt>
               </dl>
               <dl class="zws_report_dl">
               		<dd>填写日期：</dd>
               		<dt class="zws_font"><?php echo date('Y', $daily['create_time']) . '年' . date('m', $daily['create_time']) . '月' . date('d', $daily['create_time']) . '日';?></dt>
               </dl>
               <dl class="zws_report_dl">
               		<dd class="zws_font_color">经理点评：
                    <?php if ($daily['comment_broker_id']) { ?>
                        <b>(<?=$daily['broker']['truename']?>)</b>
                    <?php } ?>
                    </dd>
               		<dt>
               			<textarea class="zws_report_textarea" id="daily_comment" name = "comment"
                        <?php if ($daily['comment']) {echo 'disabled="disabled"';} ?>><?=$daily['comment']?></textarea>
                        <p><strong>&nbsp;</strong></p>
               		</dt>
               </dl>
               <div class="btn-pane center">
                    <?php if ($daily['comment'] == '') { ?>
                	<a class="btn-lv btn-left" href="javascript:void(0)"><span class="btn_inner" style="padding-right: 10px;"  id ="review_daily">确定</span></a>
                    <?php } ?>
                    <button type="button" class="btn-hui1 JS_Close">关闭</button>
                    <input type="hidden" name="daily_id" id="daily_id" value="<?=$daily['id']?>">
			    </div>
            </div>
 
        </div>
    </div>
    <div  class="pop_box_g pop_see_inform pop_no_q_up" style=" display:none;" id='work_end_sucess'>
        <div class="hd">
            <div class="title" id='work_title'></div>
            <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
        </div>
        <div class="dakaisSucc">
            <dl class class="clearfix">
                <dt class="left"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/dakacg.gif"></dt>
                <dd class="left" id='work_contents'></dd>
            </dl>
        </div>
    </div>
    <script type="text/javascript">
	$(function () {
        $('#review_daily').click(function(){
            var daily_comment = $.trim($('#daily_comment').val());
            if (daily_comment == '') {
                $('#daily_comment').parent("dt").find("p").html("请输入点评");
                return false;
            }
            $.ajax({
                type: 'post',
                url : '/daily_review/review/',
                dataType:'json',
                data: {'id' : $('#daily_id').val(), 'comment' : daily_comment},
                success: function(data){
                    if(data['status'] == 1) {
                        openWin('work_end_sucess');
                        $("#work_title").html('工作日报');
                        $("#work_contents").html('成功提交点评');
                        //刷新页面
                        window.parent.location.reload();
                    }
                }            
            });
        });

        $(".btn-hui1").live("click",function(){
            $(".js_GTipsCoverWxr").hide();
            $(window.parent.document).find("#js_find_daily_pop").hide();
            $(window.parent.document).find(".js_GTipsCoverWxr").hide();        
        });
    });
    </script>
