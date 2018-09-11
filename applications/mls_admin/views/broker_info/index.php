<?php require APPPATH . 'views/header.php'; ?>
<link href="<?php echo MLS_SOURCE_URL;?>/mls/css/v1.0/select2.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL;?>/mls/js/v1.0/select2.js"></script>
<style type="text/css">
    .ui-menu {width: 180px !important;}
</style>
<script type="text/javascript">
function get_agency(companyId)
{
    $.ajax({
        type: 'get',
        url : '<?php echo MLS_ADMIN_URL; ?>/agency/get_agency_ajax/'+companyId,
        dataType:'json',
        success: function(msg){
            var str = '';
            if(msg===''){
                str = '<option value="">请选择</option>';
            }else{
                str = '<option value="">请选择</option>';
                for(var i=0;i<msg.length;i++){
                    str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                }
            }
            $('#agency_id').empty();
            $('#agency_id').append(str);
        }
    });
}
$(function() {
	$("#agency_id").select2();
    $("#company_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/company/get_company_by_kw/",
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
                var company_name = ui.item.label;
                var id = ui.item.id;
                //操作
                $("#company_id").val(id);
                $("#company_name").val(company_name);
                get_agency(id);
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#company_id").val("");
                $("#company_name").val("");
            }
        }
    });
});


</script>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?= $title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="search_form" id="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>设置查询条
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($where_config['search_where'] as $k => $v) { ?>
                                                        <option value="<?=$k?>" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == $k)){echo 'selected="selected"';}?>><?=$v?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                  包含<input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                </label>
                                                <label>帐号有效性
                                                  <select name="search_status"  class="form-control input-sm">
                                                      <option value="99">请选择</option>
                                                      <option value="1" <?php if($where_cond['search_status'] == 1) { ?>selected<?php }?>>有效</option>
                                                      <option value="2" <?php if($where_cond['search_status'] == 2) { ?>selected<?php }?>>失效</option>
                                                  </select>
                                                </label>
                                            </div>
                                         </div>
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>设置时间条件&nbsp;
                                                    <select name="search_time" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($where_config['search_time'] as $k => $v) { ?>
                                                        <option value="<?=$k?>" <?php if((!empty($where_cond['search_time']) && $where_cond['search_time'] == $k)){echo 'selected="selected"';}?>><?=$v?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>
                                                    &nbsp;介于&nbsp;<input type="text" name="start_time" style="width:183px" id="start_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['start_time'])){echo $_POST['start_time'];}?>" onclick="WdatePicker()">
                                                    &nbsp;至&nbsp;<input type="text" id="end_time" name="end_time" class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['end_time'])){echo $_POST['end_time'];}?>" onclick="WdatePicker()">
                                                </label>
                                            </div>
                                         </div>
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>&nbsp;用户组&nbsp;
                                                    <select name="group_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($where_config['group'] as $k => $v) { ?>
                                                        <option value="<?=$k?>" <?php if((!empty($where_cond['group_id']) && $where_cond['group_id'] == $k)){echo 'selected="selected"';}?>><?=$v?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>&nbsp;身份类型&nbsp;
                                                    <select name="package_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <?php foreach($where_config['package'] as $k => $v) { ?>
                                                        <option value="<?=$k?>" <?php if((!empty($where_cond['package_id']) && $where_cond['package_id'] == $k)){echo 'selected="selected"';}?>><?=$v?></option>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>&nbsp;所属公司&nbsp;
                                                    <input name="company_name" id="company_name" value="<?=$where_cond['company_name']?>" class="input_text_r form-control input-sm" type="text" placeholder="输入汉字筛选" style="height:30px; line-height: 30px; width:180px;" >
                                                    <input type="hidden" name="company_id" id="company_id" value="<?=$where_cond['company_id']?>">
                                                </label>
                                                <label>&nbsp;所属门店&nbsp;
                                                    <select name="agency_id"  id="agency_id" aria-controls="dataTables-example">
                                                        <option value="0">请选择</option>
                                                        <?php if ($agencys) {
                                                                foreach ($agencys as $v) { ?>
                                                                    <option value="<?=$v['id']?>"  <?php if((!empty($where_cond['agency_id']) && $where_cond['agency_id'] == $v['id'])){echo 'selected="selected"';}?>><?=$v['name']?></option>
                                                                <?php }?>
                                                        <?php } ?>
                                                    </select>
                                                </label>
                                                <label>&nbsp;客户经理&nbsp;
                                                    <?php if($is_user_manager){ ?>
                                                        <select name="master_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="<?php echo $this_user_id; ?>" selected="selected" ><?php echo $this_user_name; ?></option>
                                                        </select>
                                                    <?php }else{ ?>
                                                        <select name="master_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <?php foreach($masters as $k => $v) { ?>
                                                            <option value="<?=$v['uid']?>" <?php if((!empty($where_cond['master_id']) && $v['uid'] == $where_cond['master_id'])){echo 'selected="selected"';}?>><?=$v['truename']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    <?php }?>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary" onclick="$('#search_form').attr('action', '/broker_info/index/')" type="submit" value="查询">
                                                        <?php if(!$is_user_manager){ ?><a class="btn btn-primary" href='/broker_info/add'>添加</a><?php }?>
                                                        <?php if($role=='3' || $role=='4'){ ?>
														<input class="btn btn-primary" onclick="$('#search_form').attr('action', '/broker_info/exportReport/')" type="submit" value="导出">
                                                        <?php } ?>
                                                    </div>
                                                </label>
                                            </div>
                                         </div>
                                    </div>
                                </div>
                           </form>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>手机号码</th>
                                    <th>真实姓名</th>
                                    <th>门店名称</th>
                                    <th>公司名称</th>
                                    <th>成长等级</th>
                                    <th>积分</th>
                                    <th>用户组</th>
                                    <th>认证时间</th>
                                    <th>注册时间</th>
                                    <th>权限</th>
                                    <th>帐号有效性</th>
                                    <th>客户经理</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($broker_info) && !empty($broker_info)) {
                                foreach ($broker_info as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><a href="<?php echo MLS_ADMIN_URL; ?><?php echo $_SESSION[WEB_AUTH]["city"]; ?>/broker_info/broker_details/<?php echo $value['broker_id']; ?>" target="_blank"><?php echo $value['broker_id']; ?></a></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td><?php echo $value['truename']; ?></td>
                                        <td><?php echo $value['agency_name']; ?></td>
                                        <td><?php echo $value['company_name']; ?></td>
                                        <td>Lv <?php echo $value['level']['level']; ?></td>
                                        <td><?php echo $value['credit']; ?></td>
                                        <td><?php echo $value['group_str']; ?></td>
                                        <td><?php echo $value['auth_time'] == 0 ? '' : date('Y-m-d H:i:s', $value['auth_time']); ?></td>
                                        <td><?php echo $value['register_time'] == 0 ? '' : date('Y-m-d H:i:s', $value['register_time']); ?></td>
                                        <td><?php echo $value['package_str']; ?></td>
                                        <td><?php if($value['status'] == 1 && $value['expiretime'] >= time()) {echo '有效';} else {echo '<font style="color:red;">失效</font>';} ?></td>
                                        <!--<td>
                                        <?php
                                        if($value['auth_ident_status'] == 2){
                                            echo '已认证';
                                        }else{
                                            echo '未认证';
                                        }
                                        ?>
                                        </td>-->
                                        <td><?php echo $masters[$value['master_id']]['truename']; ?></td>
                                        <td>
                                            <?php if(!$is_user_manager){ ?>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/broker_info/modify/<?php echo $value['id']; ?>/<?php echo $type=0; ?>" target="_blank">修改</a> |
                                            <?php }else{ ?>
                                                <a href="<?php echo MLS_ADMIN_URL; ?>/broker_info/modify/<?php echo $value['id']; ?>/<?php echo $type=1; ?>" target="_blank">查看</a> |
                                            <?php } ?>
											<a onclick="openn_import_new('sell',<?php echo $value['broker_id']; ?>)" class="add_link" href='javascript:void(0);'>梵讯出售房源导入</a> |
											<a onclick="openn_import_new('rent',<?php echo $value['broker_id']; ?>)" class="add_link" href='javascript:void(0);'>梵讯出租房源导入</a>
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/broker_info/');?>
                                    </ul>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    $('#company_id').change(function(){
        var companyId = $(this).val();
        $.ajax({
            type: 'get',
            url : '<?php echo MLS_ADMIN_URL; ?>/company/get_agency_ajax/'+companyId,
            dataType:'json',
            success: function(msg){
                var str = '';
                if(msg===''){
                    str = '<option value="">请选择</option>';
                }else{
                    str = '<option value="">请选择</option>';
                    for(var i=0;i<msg.length;i++){
                        str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                    }
                }
                $('#agency_id').empty();
                $('#agency_id').append(str);
            }
        });
    });
});
</script>
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>


<!-- 出售房源入 -->
<div id="jss_pop_import" class="pop_box_g pop_see_inform" style=" display:none;" >
    <div class="hd">
        <div class="title" id="import_title">出售房源导入</div>
        <div class="close_pop"><a href="/broker_info/" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">

        <div class="up_m_b_tex">房源导入功能可以将外部房源直接导入系统中，省去手动录入的麻烦。<!--为保证您的房源顺利导入，请使用我们提供的标准模板。</br><a href="<?=MLS_SOURCE_URL ?>/xls/example5.xls">
                <img alt="" src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/page_white_excel.png">点击下载房源导入参考模板</a>-->
        </div>
        <style>
            .up_m_b_file .text{ float:left; line-height:26px;}
            .up_m_b_file .text_input{width:150px;height: 24px;line-height: 24px;padding: 0 10px;border: 1px solid #E9E9E9;float: left;}
            .up_m_b_file .f_btn{ margin-left:10px;_display:inline; float:left; background:url(<?=MLS_SOURCE_URL?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0; width:44px; height:26px; overflow:hidden; position:relative; overflow:hidden; text-align:center; line-height:26px; }
            .up_m_b_file .f_btn .file{cursor:pointer;font-size:50px;filter:alpha(opacity:0); opacity: 0; position:absolute; right:-5px; top:-5px;}
            .up_m_b_file .btn_up_b{ margin-left:10px; _display:inline; float:left; overflow:hidden; width:44px; height:26px; position:relative; line-height:26px; text-align:center;background:url(<?=MLS_SOURCE_URL?>/mls/images/v1.0/btn_qx_02.gif) no-repeat right 0;}
            .up_m_b_file .btn_up_b .btn_up{ cursor:pointer; font-size:100px; position:absolute;filter:alpha(opacity:0); opacity: 0; right:-5px; top:-5px;}
        </style>
        <div class="up_m_b_file clearfix" id='import_form'>
            <form action="/broker_info/import/1" enctype="multipart/form-data" target="new" method="post">
            <p class="text">上传导入文件：</p>
			<input type='hidden' id='broker_id' name='broker_id'>
            <input type="text" class="text_input" id="aa" name="aa">
            <div class="f_btn" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">浏览</div><input class="file" name="upfile" type="file" onchange="document.getElementById('aa').value=this.value"></div>
            <div class="btn_up_b" style=" background-position: 0 0; "><div style="width: 44px; position: absolute; left:0; top: 0;">上传</div><input class="btn_up" type="submit" name="sub" value="上传"></div>
            </form>
        </div>
        <iframe allowtransparency="true" src="<?php echo MLS_ADMIN_URL;?>/blank.php" frameborder="0" scrolling="no" name="new" id="xx1x" height="34" width="393" style="bac"></iframe>
        <div style="text-align:center;" id='openn_sure'><a class="btn-lv" href="javascript:void(0)" onclick="openn_sure_new(1)"><span>确认导入</span></a></div>
    </div>
</div>
<script>
function see_reason(){
	var xxx = $(document.getElementById('xx1x').contentWindow.document.body).html();
	xxx = xxx.replace(/<p .*?>(.*?)<\/p>/g," ");
	xxx = xxx.replace(/<P .*?>(.*?)<\/P>/g," "); //为了兼容ie6
	xxx = xxx.replace(/display:none/g,"display:block");
	xxx = xxx.replace(/DISPLAY: none/g,"DISPLAY: block"); //为了兼容ie6
	//alert(xxx);
	$("#js_pop_msg_excel .up_inner").html(xxx);
	openWin('js_pop_msg_excel');
}

function openn_import_new(type,broker_id)
{
	if(type == 'rent'){
		$("#import_title").html('出租房源导入');
		$('#import_form form').attr("action", "/broker_info/import/2");
		$('#openn_sure').html("<a class='btn-lv' href='javascript:void(0);' onclick='openn_sure_new(2);'><span>确认导入</span></a>");
	}
    //先清空上传文本框
    $("input[name='upfile']").val('');
    $("#aa").val('');
    $("#broker_id").val(broker_id);
    openWin('jss_pop_import');
}
//确认导入
function openn_sure_new(type)
{
     var id = $("#xx1x").contents().find("#tmp_id").val();
     var broker_id = $("#broker_id").val();
     if(id > 0){
     $("#xx1x").contents().find("body").empty();
     openWin('jss_pop_sure',ajax_import_new(id,type,broker_id));
     }else{
        openWin('jss_pop_error');
    }
}

function ajax_import_new(id,type,broker_id)
{
    var url = "/broker_info/sure/"+type;

     $.ajax({
           url: url,
           type: "POST",
           dataType: "json",
           data: {id:id,broker_id:broker_id},
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
</script>

<img src="<?php echo MLS_SOURCE_URL; ?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩
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
<!-- 导入表格错误提示框 -->
<div class="pop_box_g pop_see_inform pop_no_q_up" id="js_pop_msg_excel" style="margin-left:-200px;width:400px">
    <div class="hd">
        <div class="title">失败列表</div>
        <div class="close_pop">
            <a class=" iconfont msg_iconfont_close" onclick="$('#js_pop_msg_excel').hide();$('#GTipsCover').hide();" title="关闭" href="javascript:void(0)"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner" style="height:150px;overflow-x:hidden;overflow-y:auto">
            <div class="up_inner" style="padding:0px">
                <p class="text"><img class="img_msg" style="margin-right:10px;" src="<?=MLS_SOURCE_URL?>/mls/images/v1.0/r_ico.png">
                    <span class="span_msg"></span><!-- id="dialog_do_itp"-->
                </p>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.onload=function(){
    $(function(){
        $("#wrapper").css({"height":($("body").height())+"px","overflow-y":"auto"});
        $("#page-wrapper").css("min-height","auto");
       $(window).resize(function(){
           $("#wrapper").css({"height":($("body").height())+"px","overflow-y":"auto"});
           $("#page-wrapper").css("min-height","auto");
       })

    })
    }
</script>
