<?php require APPPATH . 'views/header.php'; ?>
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/house_new.css">
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/openWin.js" type="text/javascript"></script>
<style>
    h1{font-weight: bold;font-size:25px}
    .paddiing_down{padding-bottom: 5px}
    td{text-align: center}
    th{text-align: center}
    html, body {
    height: 100%;
    overflow: auto;
    width: 100%;
	}
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
			<button id="btn_add" class="btn btn-primary" style="margin-left: 15px;margin-top: 5px;">新增步骤</button>
		    </div>
		    <div class="panel-body">
			<input type = 'hidden' id='temp_id'>
			<table class="table table-striped table-bordered table-hover" id="dataTables-example">
			    <thead>
			    <tr>
				<th>步骤</th>
				<th>流程阶段</th>
				<th>操作</th>
			    </tr>
			    </thead>
			    <tbody>
			    <?php if (isset($sys_temp['steps']) && !empty($sys_temp['steps'])) {
				foreach ($sys_temp['steps'] as $key => $val) { ?>
				    <tr class="gradeA">
					<td><?php echo $stage_conf[$val['step_id']]['text'];?></td>
					<td><?php echo $val['stage_name'];?></td>
					<td>
					    <a href="javascript:void(0);" onclick="$('#step_error1').text('');edit_step(<?=$val['id']?>);">修改</a>
					    <a href="javascript:void(0);" onclick="$('#temp_id').val(<?=$val['id']?>);openWin('js_delstep');">删除</a>
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
<div style="width:600px; height:350px; display:block;border-color: #3E444B;display: none" id="js_addstep" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">新增权证流程系统模板</div>
	<div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;">权证流程模板：</label>
		<label>系统默认模板</label>
	    </div>

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;">步骤：</label>
		<label id="step"></label>
	    </div>

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;float:left"><font color="red">*</font>流程阶段：</label>
		<div style="margin-left:110px;"><?php if($stage){foreach($stage as $key=>$val){?>
		    <div style="width:140px;heigh:20px;float:left;">
                <input type="hidden" id="template_id">
                <input type="checkbox" name="stage[]" value="<?=$val['id']?>" id="stage<?=$val['id']?>">&nbsp;
                <label for="stage<?=$val['id']?>"><?=$val['stage_name'];?></label>
                </div>
                <?php }}?>
            </div>
	    </div>
        <div style="margin-left:110px"><font color="red" id="step_error"></font></div>
	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1 btn-left" onclick="add_step();" type="button" style="float:left;">确定</button>
		<button class="btn-hui1 JS_Close" type="button">取消</button>
	    </div>
	</div>
    </div>
</div>

<!--修改框-->
<div style="width:600px; height:350px; display:block;border-color: #3E444B;display:none;" id="js_editstep" class="pop_box_g">
    <div class="hd" style="background: #3E444B;">
	<div class="title">修改权证流程系统模板</div>
	<div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;">权证流程模板：</label>
		<label>系统默认模板</label>
	    </div>

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;">步骤：</label>
		<label id="step1"></label>
	    </div>

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label style="width:100px;text-align:right;float:left"><font color="red">*</font>流程阶段：</label>
		<div style="margin-left:110px;"><?php if($stage){foreach($stage as $key=>$val){?>
		    <div style="width:140px;heigh:20px;float:left;">
                <input type="checkbox" name="stage1[]" value="<?=$val['id']?>" id="edit_stage<?=$val['id']?>">&nbsp;
                <label for="edit_stage<?=$val['id']?>"><?=$val['stage_name'];?></label>
                </div>
                <?php }}?>
            </div>
	    </div>
        <div style="margin-left:110px"><font color="red" id="step_error1"></font></div>
	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1 btn-left" onclick="save_step();" type="button" style="float:left;">确定</button>
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
	<div class="close_pop"><a class="iconfont" title="关闭" href="javascript:void(0);" onclick="location=location;return false;"></a></div>
    </div>
    <div class="mod">
	<div class="create_newb_wrapall paddiing_down">

	    <div class="create_newb_wrap create_newblack clearfix" >
		<label id="prompt" style=""></label>
	    </div>

	    <div style="width:120px; margin:10px auto; height:auto; overflow:hidden; zoom:1;">
		<button class="btn-lv1" style="margin-left: 30px;"onclick="location=location;return false;">确定</button>
	    </div>
	</div>
    </div>
</div>
<script>

    $("#btn_add").click(function(){
        $("#step_error").text('');
        $.post("<?php echo MLS_ADMIN_URL;?>/contract/add_step",{},function(data){
            if(data['next_step']<=10){
                $("#template_id").val(data['template_id']);
                $("#step").text(data['stage_conf'][data['next_step']]['text']);
                openWin('js_addstep');
            }else{
                $("#prompt").text('最多添加十步！');
                openWin('js_prompt');
            }
        },"json");
    });

    function add_step() {
        var step = new Array;
        $("input[name='stage[]']:checked").each(function(index,item){
            step.push($(this).val());
        });
        if(step.length>0){
            if(step.length<=3){
                $.post("<?php echo MLS_ADMIN_URL;?>/contract/save_add_step",{step:step,template_id:$("#template_id").val()},function(data){
                    if(data['status'] == 1){
                        $("#js_addstep").hide();
                        $("#GTipsCover").remove();
                        $("#prompt").text('权证步骤添加成功！');
                        openWin('js_prompt');
                    } else {
                        $("#prompt").text('权证步骤添加失败！');
                        openWin('js_prompt');
                    }
                },"json");
            }else{
                $("#step_error").text('最多选三个步骤！');
            }
        }else{
            $("#step_error").text('最少选一个步骤！');
        }
    }

    function edit_step(id) {
	$("input[name='stage1[]']").removeAttr('checked');
	$('#temp_id').val(id);
	$.post("<?php echo MLS_ADMIN_URL;?>/contract/modify_step",{id:id},function(data){
	    var list = data['list'];
	    for(var i in list){
		$("#edit_stage"+list[i]).attr('checked',true);
	    }
        $("#step1").text(data['key']);
	},"json");
	openWin('js_editstep');
    }

    function save_step() {
        var step = new Array;
        $("input[name='stage1[]']:checked").each(function(index,item){
            step.push($(this).val());
        });
        if(step.length>0){
            if(step.length<=3){
                $.post("<?php echo MLS_ADMIN_URL;?>/contract/save_modify_step",{id:$('#temp_id').val(),step:step},function(data){
                    if(data['status'] == 1){
                        $("#js_editstep").hide();
                        $("#GTipsCover").remove();
                        $("#prompt").text('权证步骤修改成功！');
                        openWin('js_prompt');
                    } else {
                        $("#prompt").text('权证步骤修改失败！');
                        openWin('js_prompt');
                    }
                },"json");
            }else{
                $("#step_error1").text('最多选三个步骤！');
            }
        }else{
            $("#step_error1").text('最少选一个步骤！');
        }
    }

    function del_step() {
	$.post("<?php echo MLS_ADMIN_URL;?>/contract/del_step",{id:$('#temp_id').val()},function(data){
	    if(data['status'] == 1){
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
