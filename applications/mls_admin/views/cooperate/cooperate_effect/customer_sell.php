<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title ?></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div>
                         <div>
                             <span ><a href="<?=MLS_ADMIN_URL?>/cooperate_effect/index/1/1" >出售公盘</a></span>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span ><a href="<?=MLS_ADMIN_URL?>/cooperate_effect/index/2/1" >出租公盘</a></span>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span ><a href="<?=MLS_ADMIN_URL?>/cooperate_effrct/index/3/1">求购公客</a></span>
                             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span ><a href="<?=MLS_ADMIN_URL?>/cooperate_effect/index/4/1" >求租公客</a></span>
                        </div>
                        </div>
                        <div class="table-responsive">
						   <form name="search_form" method="post" action="<?=MLS_ADMIN_URL?>/cooperate_effect/index/3/1" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
    								<div class="row">
    								    <div class="col-sm-6" style="width:100%;">状态：
    								        <select name="status">
    								            <option value="0">全部</option>
    								            <option value="1" <?php if($post_param['status']==1){echo 'selected="selected"';}?>>待审核</option>
    								            <option value="2" <?php if($post_param['status']==2){echo 'selected="selected"';}?> >已通过</option>
    								            <option value="3" <?php if($post_param['status']==3){echo 'selected="selected"';}?>>未通过</option>
    								        </select>
                                            举报类型：
								            <select name="type">
    								            <option value="0" <?php if(empty($post_param['type'])){echo 'selected="selected"';}?>>全部</option>

    								            <option value="1"<?php if($post_param['type']==1){echo 'selected="selected"';}?> >房源虚假</option>

    								            <option value="2" <?php if($post_param['type']==2){echo 'selected="selected"';}?>>客源虚假</option>

    								            <option value="3"<?php if($post_param['type']==3){echo 'selected="selected"';}?> >不按协议履行合同</option>
    								            <option value="4" <?php if($post_param['type']==4){echo 'selected="selected"';}?>>其它</option>
    								        </select>
                                            举报人姓名：
    							         <input type="text" name="broker_name" value="<?php if($post_param['broker_name']){echo $post_param['broker_name'];}?>">
                                            被举报人姓名：
    							        <input type="text" name="brokered_name" value="<?php if($post_param['brokered_name']){echo $post_param['brokered_name']; }?>">
                                            <input type="hidden" name="pg" value="1">
                                            <input class="btn btn-primary" type="submit" value="查询">
                                        </div>

						            </div>
					            </div>
                            </form>
						</div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                                <tr>
                                    <th style="width:55px;">举报人</th>
                                    <th style="width:68px;">被举报人</th>
                                    <th style="width:68px;">客源编号</th>
                                    <th style="width:42px;">类型</th>
                                    <th style="width:74px;">面积(平方)</th>
                                    <th style="width:68px;">价格(万元)</th>
                                    <th style="width:68px;">客户电话</th>
                                    <th style="width:55px;">提交时间</th>
                                    <th style="width:68px;">举报类型</th>
                                    <th style="width:68px;">举报理由</th>
                                    <th style="width:42px;">证据</th>
                                    <th style="width:42px;">操作</th>
                                </tr>
                            </thead>
                             <tbody>
                                <?php if($lists){
								foreach($lists as $key=>$val){
								$house_info=unserialize($val['house_info']);
								?>
								<tr>
                                    <th><?php echo $val['broker_name'];?></th>
                                    <th><?php echo $val['brokered_name'];?></th>
                                    <th><?php echo $house_info['rowid'];?></th>

                                    <th>
                                 <?php if($val['house_type']=='sell')

									 {echo '求购';}else{
										 echo '求租';
									 }
								 ?>
                                    </th>

                                    <th>
                                   <?php echo $house_info['buildarea'];?>
                                    </th>

                                    <th>
                                   <?php echo $house_info['price'];?>
                                    </th>
                                    <th><?php echo $house_info['telno1']?></th>

                                    <th><?php
									echo date('Y-m-d H:i:s',$val['report_time']);?></th>
                                    <th>
                                   <?php if($val['report_type']==1){
									   echo '房源虚假';
								   }elseif($val['report_type']==2){
									   echo '客源虚假';
								   }elseif($val['report_type']==3){
									   echo '不按协议履行合同';
								   }elseif($val['report_type']==4){
									   echo '其它';
								   }?>
                                    </th>
                                    <th><?php echo $val['report_text']?></th>
                                    <th>
                                   <?php
									if($val['photo_url'] && $val['photo_name']){
										$img_url=explode(',',$val['photo_url']);
										$img_name=explode(',',$val['photo_name']);
										foreach($img_url as $key=>$value){
											echo '
											<a class="example-image-link" href="'.$value.'"  data-lightbox="example-2" data-title="Optional caption.">'.$img_name[$key].'</a>';
										}
									}
                                    ?>
                                    </th>

                                    <th>
                                     <select id="status<?php echo $val['id']?>">
                                        <option value="1" <?php if($val['status']==1){echo 'selected="selected"';}?> >待审核</option>
                                        <option value="2" <?php if($val['status']==2){echo 'selected="selected"';}?>>已通过</option>
                                        <option value="3" <?php if($val['status']==3){echo 'selected="selected"';}?>>未通过</option>
                                    </select>
                                    <button onclick="modify(<?php echo $val['id']?>)">确定</button>
                                    </th>
                                </tr>
							<?php }}?>


                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                   <ul class="pagination" style="margin:-8px 0;padding-left:20px">
								         <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/cooperate_effect/index/3/1/');?>
								    </ul>
                                </div>
                            </div>
                            <div style="color:blue;position:absolute;right:33px;">
                                <b>共查到<?php echo $user_num;?>条数据</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function modify(id){
	var status_modify = $("#status"+id).val();
	$.ajax({
        type: 'post',
        url: '/cooperate_effect/modify/',
        data:{id:id,status_modify:status_modify},
        success: function(data){
			if(data='成功'){
				alert('操作成功');
				window.location.href="/cooperate_effect/index/3/1";
			}else{
				alert('操作失败');
			}

        }
    });
}
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/report_img/css/lightbox.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/report_img/js/lightbox.min.js"></script>
