<!--我的日报详情页-->
    <div class="pop_box_g zws_my_report_pop" id="js_pop_box_g" style="display:block;">
        <div class="hd">
            <div class="title">日报详情</div>
            <!--<div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>-->
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
               			<textarea class="zws_report_textarea" id="text_num" disabled="disabled"><?=$daily['comment']?></textarea>
                        <p><strong>&nbsp;</strong></p>
               		</dt>
               </dl>
               <div class="btn-pane center">
                    <button type="button" class="btn-hui1 JS_Close">关闭</button>
			    </div>
            </div>
 
        </div>
    </div>
	<script type="text/javascript">
		$(function(){
			$(".btn-hui1").live("click",function(){
				$(".js_GTipsCoverWxr").hide();
				$(window.parent.document).find("#js_find_daily_pop").hide();
				$(window.parent.document).find(".js_GTipsCoverWxr").hide();
				
			})
			
		})
	</script>