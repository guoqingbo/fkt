<script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/kindeditor-min.js'></script>
<script charset='utf-8'  src='<?php echo MLS_SOURCE_URL;?>/common/js/kindeditor-4.1.10/lang/zh_CN.js'></script>
<!-- 模板管理消息弹窗 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg">
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a class="JS_Close iconfont msg_iconfont_close" title="关闭" href="javascript:void(0);"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                <p class="text"><img class="img_msg" style="margin-right:10px;" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png">
                    <span id="dialog_do_itp" class="span_msg"></span>
                </p>
            </div>
        </div>
    </div>
</div>
<!-- 修改模板弹窗 -->
<div class="pop_box_g" id="upd_moban" style="display: block;border:0;width: 640px; height: 375px;">
    <div class="hd">
        <div class="title">修改模板</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="add_mb">
        <div class="add_xjmb">
            <form action="" method="post">
                <table>
                    <tbody>
                    <tr>
                        <input type="hidden" name="hid_id" value="<?php echo $temp['id'];?>">
                        <td width="70" class="label">模板名称：</td>
                        <td width="230"><input class="mbmc check_tmp_name" type="text" value="<?php echo $temp['template_name'];?>" name="upd_template_name"></td>
                        <td width="278"><span id="temp_name_num"></span></td>
                    </tr>
                    <tr>
                        <td width="70" class="label align-top">模板内容：</td>
                        <td colspan="3" class="align-left ke-container-box">
                            <style>
                                .ke-container-box .ke-container-default{ width: 450px !important; height:199px;}
                            </style>
                            <textarea name="upd_remark" id="upd_remark" cols="0" rows="0" style="margin-top:5px;width:50px;height:30px;visibility:hidden;"><?php echo $temp['remark'];?></textarea>
							<p class="house-tip1"><img width="16" height="16" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/wh_an.gif"> 发布带有小区周边配套设施的房源描述模板，提高录入效率！建议1000字以内。</p>
                        </td>
                    </tr>
                    <tr class="bcBtn">
                        <td colspan="5"><input type="button" class="btn-lv1 btn-mid JS_Close" id="btn-upd-tmp" value="保存"/></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
     //对模板名过滤验证
	$(function(){
		function ck_text(){
			var text=$("input[name='upd_template_name']").val();
			var text_num= 8 - text.length;
			var more=text.length - 8;
			if(text.length<=8){
				$('#temp_name_num').html('您还可以输入'+text_num+'个字');
			}
			if(text.length>8){
				$('#temp_name_num').html('<span style="color:red;">您已经超出了'+more+'个字</span>');

			}
		}
		$(".check_tmp_name").keyup(function(){
			ck_text();
		});
		ck_text();
	});

    var editor2;
    KindEditor.ready(function(K2) {
        editor2 = K2.create('#upd_remark', {
            resizeType: 0,
            allowPreviewEmoticons: false,
            allowImageUpload: false,
            width : "512px", //编辑器的宽度为512px
            height : "210px", //编辑器的高度为210px
            items: ['fontname', 'fontsize', '|', 'forecolor',
                'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                'insertunorderedlist', '|', 'wordpaste', '|', 'image'],
            afterBlur: function() {
                this.sync();
            }
        });
    });


    //点击保存按钮
    $("#btn-upd-tmp").click(function(){
        var id = $("input[name='hid_id']").val();
        var template_name = delHtmlTag($("input[name='upd_template_name']").val());
        var remark = editor2.html();
        if(!template_name) {
            $(".span_msg").html("模板名不能为空！");
            $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
            openWin("js_pop_msg");
            return;
        }
        if(template_name.length > 8)
        {
            $(".span_msg").html("模板名称最多8个字！");
            $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
            openWin("js_pop_msg");
            return;
        }
        if(!remark) {
            $(".span_msg").html("备注不能为空！");
            $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
            openWin("js_pop_msg");
            return;
        }
        $.post("/rent/save_tmp",{id:id,template_name:template_name, remark:remark},function(data){
            if(data.status == 1) {
                $(".span_msg").html("修改成功！");
				$(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png");
                openWin('js_pop_msg');
                //点击关闭消息弹窗的
                $(".msg_iconfont_close").click(function(){
                    //静态修改模板名字列表的值
                    $(".mobanBtn_N_"+data.template_id,parent.document).find("span").html(data.template_name);

                    //替换管理模板弹窗中的数据
                    $(".glmb_dl_"+data.template_id+" span",parent.document).html(data.template_name);

                    //将父页面的iframe弹窗关闭
                    $("#upd_moban",parent.document).hide();
                    $("#GTipsCovernew_moban",parent.document).remove();
                    $("#GTipsCoverupd_moban",parent.document).remove();
                    $("#GTipsCovergl_moban",parent.document).remove();
                });
            } else {
                //设置操作结果提示窗的一些值
                $(".span_msg").html("修改失败！");
                $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
                openWin("js_pop_msg");
            }

        },'json');
    });

    //去掉所有的html标记的方法
    function delHtmlTag(str){
        return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    }

</script>
