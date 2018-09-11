<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?f=mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/house_new.css">
<script type="text/javascript" src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js"></script><script src="<?=MLS_SOURCE_URL ?>/min/?f=mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js" type="text/javascript"></script>
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
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>角色</th>
                                    <th>等级</th>
                                    <th>设置权限</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($permission_role) && !empty($permission_role)){
                                foreach ($permission_role as $key => $value) { ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['name']; ?></td>
                                        <td><?php echo $value['level']; ?></td>
                                        <td><a href="/permission/set_role_func/<?=$value['id']?>">权限设置</a> |
                                            <a href="javascript:void(0);" onclick="get_group(<?php echo $value['id']; ?>);">修改信息</a> |
                                            <a href="javascript:void(0);" onclick="del_group(<?php echo $value['id']; ?>);">删除</a></td>
                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <input type="submit" class="btn btn-primary" name="add" value="增加" onclick="openWin('js_remark');">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="">
     <!--添加框-->
    <div style="width:350px; height:250px; display:block;border-color: #3E444B" id="js_remark" class="pop_box_g">
        <div class="hd" style="background: #3E444B;">
            <div class="title">用户组添加</div>
            <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
        </div>
        <div class="mod">
            <div class="create_newb_wrapall paddiing_down">

                <div class="create_newb_wrap create_newblack clearfix" >
                    <div class="name fl">用户组：</div>
                    <input type="text" class="loupan fl" name="name" id="name">
                </div>

                <div class="create_newb_wrap create_newblack clearfix">
                    <div class="name fl">等级：</div>
                    <input type="text" class="loupan fl" name="level" id="level">
                </div>
            </div>
            <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
                <button class="btn-lv1 btn-left JS_Close" onclick="add_group();" type="button" style="float:left;">添加</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
        </div>
    </div>
     <!--更新信息框-->
    <div style="width:350px; height:250px; display:block;border-color: #3E444B" id="js_remarks" class="pop_box_g">
        <input type="hidden" name="id" id="update_id">
        <div class="hd" style="background: #3E444B;">
            <div class="title">用户组修改</div>
            <div class="close_pop"><a class="JS_Close iconfont" title="关闭" href="javascript:void(0);"></a></div>
        </div>
        <div class="mod">
            <div class="create_newb_wrapall paddiing_down">

                <div class="create_newb_wrap create_newblack clearfix" >
                    <div class="name fl">用户组：</div>
                    <input type="text" class="loupan fl" name="name" id="update_name">
                </div>

                <div class="create_newb_wrap create_newblack clearfix">
                    <div class="name fl">等级：</div>
                    <input type="text" class="loupan fl" name="level" id="update_level">
                </div>
            </div>
            <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
                <button class="btn-lv1 btn-left JS_Close" onclick="update_group();" type="button" style="float:left;">保存</button>
                <button class="btn-hui1 JS_Close" type="button">取消</button>
            </div>
        </div>
    </div>
        <!--操作结果弹出提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" style="border-color: #3E4">
    <div class="hd" style="background: #3E444B;">
        <div class="title" >提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();location=location;" title="关闭" class="JS_Close iconfont"></a>
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
</form>
</div>
<?php require APPPATH . 'views/footer.php'; ?>
<script>
function add_group(){
    var group_name = $("#name").val();
    var description = $("#description").val();
    var level = $("#level").val();
    var data = {
        'name':group_name,
        'description':description,
        'level':level
    };
    $.ajax({
        type:"POST",
        url:"/permission/addgroup",
        data:data,
        cache:false,
        error:function(){
            alert("系统错误");
            return false;
        },
        success:function(return_data){
            if(return_data>0){
                $('#dialog_do_itp').html('添加成功');
                openWin('js_pop_do_success');
            }else{
                $('#dialog_do_itp').html('添加失败');
                openWin('js_pop_do_success');
            }
        }
    })
}
//获得当前用户组基本信息
function get_group(id){
    $.ajax({
        url: "<?php echo MLS_ADMIN_URL;?>/permission/get_group/"+id,
        type:"GET",
        dataType:"json",
        data:{
            isajax:1
        },
        success:function(data){
            if(data['errorCode'] == '401')
            {
                login_out();
            }
            else if(data['errorCode'] == '403')
            {
                permission_none();
            }else if(data['result'] == 'ok')
                {
                    var list=data['list'];
                    $("#update_id").val(list['id']);
                    $("#update_name").val(list['name']);
                    $("#update_description").val(list['description']);
                    $("#update_level").val(list['level']);
                    openWin('js_remarks');
                }
        }
    });
}

//更新用户组信息
function update_group(){
    var id = $("#update_id").val();
    var group_name = $("#update_name").val();
    var description = $("#update_description").val();
    var level = $("#update_level").val();
    var data ={
        'id': id,
        'name':group_name,
        'description': description,
        'level': level,
    };
    $.ajax({
        type:"POST",
        url:"/permission/update_group",
        data:data,
        cache:false,
        error:function(){
            alert("系统错误");
            return false;
        },
        success:function(return_data){
            if(1==return_data){
                $('#dialog_do_itp').html('保存成功');
                openWin('js_pop_do_success');
            }else{
                $('#dialog_do_itp').html('保存失败');
                openWin('js_pop_do_success');
            }
        }
    });
}
//删除用户组
function del_group(id){
    $.ajax({
        url: "<?php echo MLS_ADMIN_URL;?>/permission/del_group/"+id,
        type:"GET",
        dataType:"json",
        data:{
            isajax:1
        },
        success:function(return_data){
            if(1==return_data){
                $('#dialog_do_itp').html('删除成功');
                openWin('js_pop_do_success');
            }else{
                $('#dialog_do_itp').html('删除失败');
                openWin('js_pop_do_success');
            }
        }
    });
}
</script>
