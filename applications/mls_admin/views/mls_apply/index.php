<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper" style="min-height: 337px;">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $title;?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body" >
                        <table id="dataTables-example" class="table table-striped table-bordered table-hover">
                            <thead>
                                 <tr>
                                    <th>序号</th>
                                    <th>应用名称</th>
									<th>版本</th>
                                    <th>强制更新</th>
									<th>版本类型</th>
									<th>应用宝地址</th>
                                    <th><a href=<?php echo MLS_ADMIN_URL.'/mls_apply/add';?>>新增</a></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									if($list){
										foreach($list as $key =>$val){
											if($val['type'] == 1){
								?>
                                <tr class="gradeA">
                                    <td><?=$val['id']?></td>
                                    <td><?=$val['apply_name']?></td>
									<td><?=$val['version']?></td>
									<?php if($val['is_forced'] == 1){?>
									<td><?php echo "是";?></td>
									<?php }else{?>
									<td><?php echo '否';?></td>
									<?php }?>
									<?php if($val['version_type'] == 1){?>
									<td><?php echo "iOS";?></td>
									<?php }elseif($val['version_type'] == 2){?>
									<td><?php echo 'Android';?></td>
									<?php }else{?>
									<td><?php echo 'PC';?></td>
									<?php }?>
									<td><?=$val['update_url']?></td>
                                    <td><a href="<?php echo MLS_ADMIN_URL;?>/mls_apply/edit/<?php echo $val['id'];?>">修改</a></td>
                                </tr>
                                <?php }}}?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
