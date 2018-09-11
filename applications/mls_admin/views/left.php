<?php
    $this_user = $_SESSION[WEB_AUTH];
?>
<?php if($this_user['role']==1){?>
<dl class="navbar-default sidebar left_menu" role="navigation">
    <dt class="lefdt">超级管理</dt>
    <dd  style="display:none">
        <ul class="nav">
            <li><a class="active" href="<?php echo MLS_ADMIN_URL;?>/purview_father_node/index"><i class="fa fa-dashboard fa-fw"></i>权限根节点设置</a></li>
            <li><a class="active" href="<?php echo MLS_ADMIN_URL;?>/purview_node/index"><i class="fa fa-dashboard fa-fw"></i>权限节点设置</a></li>
        </ul>
    </dd>
    <dt class="lefdt">系统管理</dt>
    <dd  style="display:none">
        <ul class="nav">
        <li><a class="active" href="<?php echo MLS_ADMIN_URL;?>/user_group/index"><i class="fa fa-dashboard fa-fw"></i>用户组管理</a></li>
        </ul>
        <ul class="nav">
        <li><a class="active" href="<?php echo MLS_ADMIN_URL;?>/user/data_list"><i class="fa fa-dashboard fa-fw"></i>用户信息管理</a></li>
        </ul>
    </dd>
</dl>
<?php }else if($this_user['role']==2){?>
<dl class="navbar-default sidebar left_menu" role="navigation" id="purview">
</dl>
<?php }?>

<script type="text/javascript">
$(document).ready(function() {
    var _url = window.location.href;
    $.ajax({
        url: "/user/ajax_get_purview_node/",
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            if(data.result!='failed'){
                //根据返回的数据，组装左侧菜单html
                var left_html = '';
                for(var i=0;i<data.length;i++){
                    var is_this_url = false;
                    left_html += '<dt class="lefdt"><i class="fa fa-dashboard fa-fw"></i>'+data[i].p_name+'</dt>';
                    for(var j=0;j<data[i].purview_node_data.length;j++){
                        if('<?php echo MLS_ADMIN_URL;?>/'+data[i].purview_node_data[j].path==_url){
                            is_this_url = true;
                        }
                    }
                    if(is_this_url){
                        left_html += '<dd style="display:block;">';
                    }else{
                        left_html += '<dd style="display:none;">';
                    }
                    left_html += '<ul class="nav">';
                    for(var j=0;j<data[i].purview_node_data.length;j++){
                        left_html += '<li><a class="active" href="<?php echo MLS_ADMIN_URL;?>/'+data[i].purview_node_data[j].path+'">'+data[i].purview_node_data[j].name+'</a></li>';
                    }
                    left_html += '</ul>';
                    left_html += '</dd>';
                }
                $('#purview').empty().append(left_html);
            }else{
                $('#purview').empty().append('<code color="red">您尚未开通任何菜单权限<br>(请联系管理员)</code>');
            }
        }
    });

    //左侧菜单伸缩效果
    $(".left_menu .lefdt").live("click",function(){
        if($(this).next("dd").is(":hidden"))
        {
            $(".left_menu dd").slideUp(500);
            $(this).next("dd").slideDown(500)
        }
        else
        {
            $(".left_menu dd").slideUp(500);
        }
    })
});
</script>
<style type="text/css">
    .lefdt {color: #428bca; padding-left:8px; line-height:32px; font-weight: 100;}
</style>
