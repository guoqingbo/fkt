<?php require APPPATH . 'views/header.php'; ?>
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/house_new.css">
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
<style>
    .paddiing_down{padding-bottom: 5px}
    td{text-align: center}
    th{text-align: center}
    html, body {
    height: 100%;
    overflow: auto;
    width: 100%;
	}
    h1{font-weight: bold;font-size:25px}
</style>
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
                    <div class="panel-head">
                        <button id="btn_add" class="btn btn-primary" style="margin-left: 15px;margin-top: 15px;">新增步骤</button>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th style="width:10%;text-align:center">编号</th>
                                    <th style="width:70%;text-align:center">流程步骤</th>
                                    <th style="width:20%;text-align:center">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($stages) && !empty($stages)) {
                                foreach ($stages as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['stage_name']; ?></td>
                                        <td>
                                            <a href="javascript:void(0);" onclick="edit_step(<?=$value['id']?>,'<?=$value['stage_name']?>');">修改</a>
                                            <!--<a href="#" onclick="$('#stage_id').val(<?=$value['id']?>);openWin('js_delstep');">删除</a>-->
                                        </td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--添加框-->
<div style="width:280px; height:175px; display:block;border-color: #3E444B;display: none" id="js_addstep" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">新增步骤</div>
	<div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label>步骤名称：</label>
		<input class="form-control input-sm " type="text" id="stage_name" maxlength="20" style="width:150px;display: inline-block;" aria-controls="dataTables-example" value="">
	    </div>

	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1 btn-left JS_Close" onclick="add_step();" type="button" style="float:left;">确定</button>
		<button class="btn-hui1 JS_Close" type="button">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--修改框-->
<div style="width:280px; height:165px; display:block;border-color: #3E444B;display: none" id="js_editstep" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">修改步骤</div>
	<div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label>步骤名称：</label>
		<input type="hidden" id="stage_id">
        <input class="form-control input-sm " type="text" id="stage_name2" maxlength="20" style="width:150px;display: inline-block;" aria-controls="dataTables-example" value="">
	    </div>

	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1 btn-left JS_Close" onclick="save_step();" type="button" style="float:left;">确定</button>
		<button class="btn-hui1 JS_Close" type="button">取消</button>
	    </div>
	</div>

    </div>
</div>

<!--删除框-->
<div style="width:280px; height:150px; display:block;border-color: #3E444B;display: none" id="js_delstep" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">提示</div>
	<div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label>是否确定删除该步骤？</label>
	    </div>

	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1 btn-left JS_Close" onclick="del_step();" type="button" style="float:left;">确定</button>
		<button class="btn-hui1 JS_Close" type="button">取消</button>
	    </div>
	</div>

    </div>
</div>

<!--提示框-->
<div style="width:280px; height:150px; display:block;border-color: #3E444B;display: none" id="js_prompt" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">提示</div>
	<div class="close_pop"><a class="iconfont" title="关闭" href="javascript:void(0);" onclick="location.reload();"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label id="prompt" style=""></label>
	    </div>

	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1" style="margin-left: 30px;"onclick="location.reload();">确定</button>
	    </div>
	</div>
    </div>
</div>
<script>

    $("#btn_add").click(function(){
        openWin('js_addstep');
    });

    function add_step() {
	$.post("<?php echo MLS_ADMIN_URL;?>/contract/add_data",{stage_name:$('#stage_name').val()},function(data){
	    if(data.status == 1){
		$("#prompt").text('添加步骤成功！');
		openWin('js_prompt');
	    } else {
		$("#prompt").text('添加步骤失败！');
		openWin('js_prompt');
	    }
	},"json");
    }

    function edit_step(id,name) {
	$('#stage_id').val(id);
	$('#stage_name2').val(name);
	openWin('js_editstep');
    }

    function save_step() {
	$.post("<?php echo MLS_ADMIN_URL;?>/contract/save_data",{id:$('#stage_id').val(),stage_name:$('#stage_name2').val()},function(data){
	    if(data.status == 1){
		$("#prompt").text('权证步骤修改成功！');
		openWin('js_prompt');
	    } else {
		$("#prompt").text('权证步骤修改失败！');
		openWin('js_prompt');
	    }
	},"json");
    }

    function del_step() {
	$.post("<?php echo MLS_ADMIN_URL;?>/contract/del_data",{id:$('#stage_id').val()},function(data){
	    if(data.status == 1){
		$("#prompt").text('权证步骤已删除！');
		openWin('js_prompt');
	    } else {
		$("#prompt").text('权证步骤删除失败！');
		openWin('js_prompt');
	    }
	},"json");
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>
