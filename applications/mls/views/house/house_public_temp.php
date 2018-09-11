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
<!-- 新建模板弹窗 -->
<div class="pop_box_g"  style="display: block;border: 0; width: 640px; height: 375px;">
    <div class="hd">
        <div class="title">新建模板</div>

    </div>
    <div class="add_mb">
        <div class="add_xjmb">
            <form action="" method="post">
                <table>
                    <tbody>
                    <tr>
                        <td width="70" class="label">模板名称：</td>
                        <td width="230"><input class="mbmc check_tmp_name" type="text" value="" name="add_template_name" id="js_add_template_name"></td>
                        <td width="278"><span id="temp_name_num">您还可以输入8个字</span></td>
                    </tr>
                    <tr>
                        <td width="70" class="label align-top">模板内容：</td>
                        <td colspan="3" class="align-left ke-container-box">
                            <style>
                                .ke-container-box .ke-container-default{ width: 500px !important; height:199px;}
                            </style>
                            <textarea name="add_remark" id="add_remark" cols="0" rows="0" style="margin-top:5px;width:50px;height:30px;visibility:hidden;"></textarea>
							<p class="house-tip1"><img width="16" height="16" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/wh_an.gif"> 发布带有小区周边配套设施的房源描述模板，提高录入效率！建议1000字以内。</p>
                        </td>
                    </tr>
                    <tr class="bcBtn">
                        <td colspan="5"><input type="button" class="btn-lv1 btn-mid JS_Close" id="btn-save-tmp" value="保存"/></td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩 loading-->
<script>
    //添加弹窗编辑器
    var editor1;
    KindEditor.ready(function(K1) {
        editor1 = K1.create('#add_remark', {
            resizeType: 0,
            allowPreviewEmoticons: false,
            allowImageUpload: false,
            width : "512px", //编辑器的宽度为512px
            height : "210px", //编辑器的高度为210px
            items: ['fontname', 'fontsize', '|', 'forecolor',
            'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|',
            'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'wordpaste'],
            afterBlur: function() {
            this.sync();
            }
        });
    });

    //对模板名过滤验证
    $(".check_tmp_name").keyup(function(){
         var text=$("input[name='add_template_name']").val();
         var text_num=8-text.length;
         var more=text.length-8;
         if(text.length<=8){
         $('#temp_name_num').html('您还可以输入'+text_num+'个字');
         }
         if(text.length>8){
         $('#temp_name_num').html('<span style="color:red;">您已经超出了'+more+'个字</span>');
         }
    });


    //点击保存按钮
    $("#btn-save-tmp").click(function(){
        //将数据进行去除HTML标签过滤。
        var template_name = delHtmlTag($("input[name='add_template_name']").val());
        var remark = editor1.html();
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
            $(".span_msg").html("模板内容不能为空！");
            $(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png");
            openWin("js_pop_msg");
            return;
        }
        $.post("/sell/save_new_tmp",{template_name:template_name, remark:remark},function(data){
            if(data.status == 1) {
				$(".img_msg").attr("src","<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/r_ico.png");
                $(".span_msg").html("添加成功！");
                openWin('js_pop_msg');
				var gl_num = ($(".glmb_num",parent.document).length);
                //点击关闭消息弹窗的
                $(".msg_iconfont_close").click(function(){
                    //在"管理模板"按钮前面静态加上相应模板数据
                    $("#btn-add-tmp",parent.document).after('<a href="javascript:void(0);" class="mobanBtn_N mobanBtn_N_'+data.template_id+'" data-id="'+data.template_id+'">' +
                    '<input type="hidden" name="hid_remark_'+data.template_id+'" value="'+ data.remark +'"><span>' + template_name + '</span></a>');

                    //显示‘管理模板’
                    $('#btn-manage-tmp',parent.document).css('display','block');

                    //在管理模板弹窗"添加模板"按钮前面加上相应数据
                    $(".manage_add_tmp",parent.document).before('<dl class="glmb_num glmb_dl glmb_dl_'+data.template_id+'"><dt><span>'+data.template_name+'</span></dt>' +
                    '<dd class="glmb_dd"><a href="javascript:void(0);" class="left modify_tmp modify_tmp_'+data.template_id+'" data-id="'+data.template_id+'">修改' +
                    '<input type="hidden" name="hid_manage_tmp_id" value="'+data.template_id+'">' +
                    '<input type="hidden" name="hid_manage_tmp_name" value="'+data.template_name+'">' +
                    '<input type="hidden" name="hid_manage_tmp_remark" value="'+data.remark+'"></a>' +
                    '<a href="javascript:void(0);"" class="right remove_tmp remove_tmp_'+data.template_id+'" data-id="'+data.template_id+'">删除' +
                    '<input type="hidden" name="hid_manage_tmp_id" value="'+data.template_id+'">' +
                    '<input type="hidden" name="hid_manage_tmp_name" value="'+data.template_name+'">' +
                    '<input type="hidden" name="hid_manage_tmp_remark" value="'+data.remark+'"></a></dd></dl>');

                   //将父页面的iframe弹窗关闭
                    $("#new_moban",parent.document).hide();
                    $("#GTipsCovernew_moban",parent.document).remove();
					if(gl_num == 9){
						$(".manage_add_tmp",parent.document).remove();
					};
                });
                //去除页面中的‘还没有设置模板哦’提示
                $('#no_tmps_message',parent.document).attr('style','display:none;');
            }
        },'json');
    });

    //去掉所有的html标记的方法
    function delHtmlTag(str){
        return str.replace(/<[^>]+>/g,"");//去掉所有的html标记
    }

</script>
