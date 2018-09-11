<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">楼盘图库</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>&nbsp&nbsp 图片类型
                                                        <select name="pic_type" aria-controls="dataTables-example" class="form-control input-sm">
                                                            <option value="0">请选择</option>
                                                            <option value="7" <?php if($pic_type==7){echo 'selected="selected"';}?>>未分类图片</option>
                                                            <option value="1" <?php if($pic_type==1){echo 'selected="selected"';}?>>户型图</option>
                                                            <option value="2" <?php if($pic_type==2){echo 'selected="selected"';}?>>小区正门</option>
                                                            <option value="3" <?php if($pic_type==3){echo 'selected="selected"';}?>>外景图</option>
                                                            <option value="4" <?php if($pic_type==4){echo 'selected="selected"';}?>>小区环境</option>
                                                            <option value="5" <?php if($pic_type==5){echo 'selected="selected"';}?>>内部设施</option>
                                                            <option value="6" <?php if($pic_type==6){echo 'selected="selected"';}?>>周边配套</option>
                                                        </select>
                                                    </label>
                                                    <label>
                                                        <div class="dataTables_length" id="dataTables-example_length">
                                                            <input type="hidden" name="pg" value="1">
                                                            <input class="btn btn-primary" type="submit" value="查询">
                                                            <a class="btn btn-primary" href='../add_cmt_img/<?php echo $commid;?>'>添加</a>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form action="../cmt_pic_manage_action" method="post" id="action_form">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                            <tbody>
                                                <?php
                                                for($i=1;$i<=$rows;$i++){
                                                  ?>
                                                <tr>
                                                    <?php for($j=0;$j<5;$j++){
                                                        if(!empty($all_image_data[5*($i-1)+$j])){
                                                    ?>
                                                    <td bgcolor="#eeeeee" align="center">
                                                        <table>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <a target="_blank" href="<?php echo $all_image_data[5*($i-1)+$j]['image'];?>">
                                                                            <img width="200" height="150" border="0" src="<?php echo $all_image_data[5*($i-1)+$j]['image'];?>">
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" value="<?php echo $all_image_data[5*($i-1)+$j]['id'];?>" name="rows_id[]">
                                                                        <input type="hidden" value="<?php echo $all_image_data[5*($i-1)+$j]['image'];?>" id="img_src">
                                                                        编号:<?php echo $all_image_data[5*($i-1)+$j]['id'];?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                            if(!empty($pic_type_arr[$all_image_data[5*($i-1)+$j]['pic_type']])){
                                                                                echo $pic_type_arr[$all_image_data[5*($i-1)+$j]['pic_type']];
                                                                            }else{
                                                                                echo '未分类';
                                                                            }
                                                                        ?>
                                                                        &nbsp;
                                                                        <a href='#' name='single_del' id="<?php echo $all_image_data[5*($i-1)+$j]['id'];?>">删除</a>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                       <?php
                                                                            if(!empty($all_image_data[5*($i-1)+$j]['title'])){
                                                                                echo $all_image_data[5*($i-1)+$j]['title'];
                                                                            }else{
                                                                                echo '无标题';
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                                    if($all_image_data[5*($i-1)+$j]['is_surface']==1){
                                                                        echo '<tr><td style="color:red;">(封面)</td></tr>';
                                                                    }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <?php }} ?>
                                                </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>

                                <table class="table table-striped table-bordered table-hover">
                                    <tbody>
                                        <tr bgcolor="#f1f1f1">
                                            <td width="10%"><input type="checkbox" onclick="checkall()" id="chkall">全选</td>
                                            <td>选中项：&nbsp;
                                                <input type="radio" value="multdel" name="actiontype">删除&nbsp;&nbsp;
                                                <input type="radio" value="setface" name="actiontype">封面&nbsp;&nbsp;
                                                <input type="radio" value="update_type" name="actiontype">修改图片类型&nbsp;&nbsp;
                                                <select name="pic_type2" id="pic_type2">
                                                    <option value="7">未分类图片</option>
                                                    <option value="1">户型图</option>
                                                    <option value="2">小区正门</option>
                                                    <option value="3">外景图</option>
                                                    <option value="4">小区环境</option>
                                                    <option value="5">内部设施</option>
                                                    <option value="6">周边配套</option>
                                                </select>
                                                <input type="button" onclick="" value="提交" name="submit" id="action_submit">
                                                <input type="hidden" value="1" id="get_col_num">
                                                <input type="hidden" value="" name="actionid">
                                                <input type="hidden" value="5" name="bi_blockid">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                </form>

                                <div class="row">
                                  <div class="col-sm-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">

                                       <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                            <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/user/index');?>
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
            </div>

        </div>
    </div>
    <!-- /#wrapper -->
<?php require APPPATH.'views/footer.php'; ?>
<script type="text/javascript">
$(function(){
    $('a[name="single_del"]').click(function(){//单独删除
            var imgarr = [];
            imgarr.push($(this).attr('id'));
            var data = {
                'actiontype':'multdel',
                'imgarr':imgarr,
            };
            var imgarrLeng = data.imgarr.length;//选中图片个数
            $.ajax({
                type: 'get',
                url : '<?php echo MLS_ADMIN_URL; ?>/cmtimage/cmt_pic_manage_action/<?php echo $commid;?>',
                data: data,
                success: function(msg){
                    if(msg=='delSuccess'){
                        alert('删除成功');
                    }else if(msg=='delFail'){
                        alert('删除失败');
                    }else if(msg=='surfaceSuccess'){
                        alert('设置封面成功');
                    }else if(msg=='surfaceFail'){
                        alert('设置封面失败');
                    }
                    //location.reload();
                }
            });
    });

    $("#action_submit").click(function(){
        var actiontype = $('input[name="actiontype"]:checked').val();
        var pic_type = $('#pic_type2 option:selected').val();
        var imgid = [];
        var imgsrc = [];
        $('input[name="rows_id[]"]:checked').each(function(){
            imgid.push($(this).val());
            imgsrc.push($(this).next().val());
        });
        var data = {
            'actiontype':actiontype,
            'imgarr':imgid,
            'imgsrc':imgsrc,
            'pic_type':pic_type
        };
        var imgarrLeng = data.imgarr.length;//选中图片个数
        if(imgarrLeng==0){
            alert('请至少选择一张图片进行操作');
        }else if('setface'==data.actiontype && imgarrLeng>1){//封面
            alert('只能选择一张图片作为该楼盘封面');
        }else if(typeof(actiontype)=='undefined'){
            alert('请选择操作选项');
        }else{
            $.ajax({
                type: 'get',
                url : '<?php echo MLS_ADMIN_URL; ?>/cmtimage/cmt_pic_manage_action/<?php echo $commid;?>',
                data: data,
                success: function(msg){
                    if(msg=='delSuccess'){
                        alert('删除成功');
                    }else if(msg=='delFail'){
                        alert('删除失败');
                    }else if(msg=='surfaceSuccess'){
                        alert('设置封面成功');
                    }else if(msg=='surfaceFail'){
                        alert('设置封面失败');
                    }else if(msg=='update_type_success'){
                        alert('修改类型成功');
                    }else if(msg=='update_type_fail'){
                        alert('修改类型失败');
                    }
                    //location.reload();
                }
            });
        }

    });
});

function checkall()
{
    var aa = document.getElementsByName('rows_id[]');
    for (var i=0; i<aa.length; i++)
    {
       aa[i].checked =document.getElementById('chkall').checked;;
    }

}


</script>
