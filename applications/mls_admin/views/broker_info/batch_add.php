<?php require APPPATH . 'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <?php if ($addResult == '') { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    公司id<font color="red">*</font>:&nbsp&nbsp<input type="search" id="c_id" class="form-control input-sm" aria-controls="dataTables-example" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                 <label>
                                                  <input type="text" style="display:none;">
                                                   门店id<font color="red">*</font>:&nbsp&nbsp<input type="text" id="a_id" class="form-control input-sm" aria-controls="dataTables-example" value="" autocomplete ="off">
                                                </label>
                                            </div>
                                        </div>
                                       <?php if (!empty($mess_error)) { ?>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <font color='red'><?php echo $mess_error; ?></font>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input class="btn btn-primary" type="button" value="批量上传" id="bulk_upload">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="submit_flag" value="add">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } else if (0 === $addResult) { ?>
            <div><h1><b>添加失败</b><h1></div>
            <a href="/broker_info/index">点此返回</a>
        <?php } else { ?>
            <div><h1><b>添加成功</b><h1></div>
            <a href="/broker_info/modify/<?php echo $addResult; ?>">继续完善资料</a>
<?php } ?>
        <!-- /.panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
</div>
</div>
<div class="col-lg-4" style="display:none" id="js_note1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            提示框
            <button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="panel-body">
            <p id="warning_text"></p>
        </div>
    </div>
</div>
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style=" display:none;" >
    <div class="hd">
        <div class="title" id="import_title">经纪人导入</div>
        <div class="close_pop"><a href="/broker_info/batch_add" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <style>
            .up_m_b_file .text{ float:left; line-height:26px;}
            .up_m_b_file .text_input{width:150px;height: 24px;line-height: 24px;padding: 0 10px;border: 1px solid #E9E9E9;float: left;}
            .up_m_b_file .f_btn{ margin-left:10px;_display:inline; float:left; background:url(<?=MLS_SOURCE_URL?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0; width:44px; height:26px; overflow:hidden; position:relative; overflow:hidden; text-align:center; line-height:26px; }
            .up_m_b_file .f_btn .file{cursor:pointer;font-size:50px;filter:alpha(opacity:0); opacity: 0; position:absolute; right:-5px; top:-5px;}
            .up_m_b_file .btn_up_b{ margin-left:10px; _display:inline; float:left; overflow:hidden; width:44px; height:26px; position:relative; line-height:26px; text-align:center;background:url(<?=MLS_SOURCE_URL?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;}
            .up_m_b_file .btn_up_b .btn_up{ cursor:pointer; font-size:100px; position:absolute;filter:alpha(opacity:0); opacity: 0; right:-5px; top:-5px;}
        </style>
        <div class="up_m_b_file clearfix" id='import_form'>
            <form action="/broker_info/batch_import" enctype="multipart/form-data" target="new" method="post">
            <p class="text">上传导入文件：</p>
            <input type="text" class="text_input" id="aa" name="aa">
            <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>
            <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_ADMIN_URL;?>/blank.php" frameborder="0" scrolling="no" name="new" id="xx1x" height="34" width="393" style="bac"></iframe>
        <div style="text-align:center;" id='openn_sure'><a class="btn-lv" href="javascript:void(0)" onclick="openn_sure_new(1)"><span>确认导入</span></a></div>
    </div>
</div>

<!--确认导入表格弹窗-->
<div id="jss_pop_sure" class="pop_box_g pop_see_inform pop_no_q_up stop_pop_box" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" onclick='location=location' title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod" style="_margin-top:-10px;">
    	<div class="inform_inner">
             <div class="up_inner">
				<p class="text" style="line-height:28px;"><br>
				   <img alt="" src="">
					<span></span>
				</p>
			</div>
        </div>
    </div>
</div>

<!--提示导入表格弹窗-->
<div id="jss_pop_error" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont"></a></div>
    </div>
    <div class="mod">
            <div class="inform_inner">
                <div class="up_inner">
                    <p class="text" style="line-height:28px;"><br>
                    <img alt="" src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/error_ico.png">
                    <span> 请上传表格！</span>
                    </p>
                </div>
            </div>
    </div>
</div>

<script>
//确认导入
function openn_sure_new(type)
{
    //公司、门店id
    var company_id = $('#c_id').val();
    var agency_id = $('#a_id').val();
    var id = $("#xx1x").contents().find("#tmp_id").val();
    var broker_id = $("#broker_id").val();
    if(id > 0){
       $("#xx1x").contents().find("body").empty();
       openWin('jss_pop_sure',ajax_import_new(id,company_id,agency_id));
    }else{
       openWin('jss_pop_error');
    }

}

function ajax_import_new(id,company_id,agency_id)
{
    var url = "/broker_info/broker_sure/";
     $.ajax({
           url: url,
           type: "POST",
           dataType: "json",
           data: {id:id,company_id:company_id,agency_id:agency_id},
           success: function(data) {
               if(data.status == 'ok')
               {
                   $('#jss_pop_sure .mod .inform_inner .text span').html(data.success);
                   $("#jss_pop_sure .mod .inform_inner .text img").attr("src", "<?=MLS_SOURCE_URL?>/mls/images/v1.0/r_ico.png");
               }else{
                   $('#jss_pop_sure .mod .inform_inner .text span').html(data.error);
                   $("#jss_pop_sure .mod .inform_inner .text img").attr("src", "<?=MLS_SOURCE_URL?>/mls/images/v1.0/error_ico.png");
               }
           }
        });
}

$(function(){
    $('#bulk_upload').live('click',function(){
        openWin('jss_pop_import');
    });
});
</script>
<?php require APPPATH . 'views/footer.php'; ?>

