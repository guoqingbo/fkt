<?php require APPPATH . 'views/header.php'; ?>
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
                            <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>设置查询条件
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">请选择</option>
                                                        <option value="phone" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'phone')){echo 'selected="selected"';}?>>手机号码</option>
                                                        <option value="truename" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'truename')){echo 'selected="selected"';}?>>经纪人姓名</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    包含<input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                </label>
                                                <label>状态
                                                      <select name="search_status"  class="form-control input-sm">
                                                        <option value="99">请选择</option>
                                                        <option value="1" <?php if($where_cond['search_status'] == 1) { ?>selected<?php }?>>待审核</option>
                                                        <option value="2" <?php if($where_cond['search_status'] == 2) { ?>selected<?php }?>>通过</option>
                                                        <option value="3" <?php if($where_cond['search_status'] == 3) { ?>selected<?php }?>>驳回</option>
                                                        <option value="4" <?php if($where_cond['search_status'] == 4) { ?>selected<?php }?>>注销失效</option>
                                                        <option value="5" <?php if($where_cond['search_status'] == 5) { ?>selected<?php }?>>非法状态</option>
                                                    </select>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" name="pg" value="1">
                                                        <input class="btn btn-primary" type="submit" value="查询">
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
                                    <!--<th>认证内容</th>-->
                                    <th>状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($auth_info) && !empty($auth_info))
                            {
                                foreach ($auth_info as $key => $value) {
                                    /*if ($value['type']==1) {
                                        $auth_type = '身份认证';
                                    }else{
                                        $auth_type = '资质认证';
                                    }*/
                                    switch ($value['status'])
                                    {
                                        case 1:
                                            $auth_status = '待审核';
                                            break;
                                        case 2:
                                            $auth_status = '通过';
                                            break;
                                        case 3:
                                            $auth_status = '驳回';
                                            break;
										case 4:
                                            $auth_status = '注销失效';
                                            break;
                                        default:
                                            $auth_status = '非法状态';
                                    }
                            ?>
                                    <tr class="gradeA">
                                        <td><?php echo $value['id']; ?></td>
                                        <td><?php echo $value['phone']; ?></td>
                                        <td><?php echo $value['truename']; ?></td>
                                        <!--<td><?php echo $auth_type; ?></td>-->
                                        <td><?php echo $auth_status; ?></td>
                                        <td>
                                            <a href="<?php echo MLS_ADMIN_URL; ?>/auth_review/modify/<?php echo $value['id']; ?>" >修改</a>
                                        </td>
                                    </tr>
                            <?php
                                }
                           }
                            ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/auth_review/');?>
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
    function checkdel(id)
    {
         if (confirm('确定要删除此公司？')) {
             return true;
         } else {
             return false;
         }
    }
</script>
<?php require APPPATH . 'views/footer.php'; ?>
