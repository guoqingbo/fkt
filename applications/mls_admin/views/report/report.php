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
                            <span <?php if($style==1 ){echo 'class="on"';}?>><a href="/report/?style=1">出售公盘</a></span>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span <?php if($style==2 ){echo 'class="on"';}?>><a href="/report/?style=2">出租公盘</a></span>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span <?php if($style==3 ){echo 'class="on"';}?>><a href="/report/?style=3">求购公客</a></span>
                            &nbsp&nbsp&nbsp&nbsp&nbsp
                            <span <?php if($style==4 ){echo 'class="on"';}?>><a href="/report/?style=4">求租公客</a></span>
                        </div>
                        <div class="table-responsive">
						     <form name="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
    								<div class="row">
    								    <div class="col-sm-6" style="width:100%">
    								       <label>状态：
    								        <select name="status">
    								            <option value="0">全部</option>
    								            <option value="1" <?php if($status==1){echo 'selected="selected"';}?>>待审核</option>
    								            <option value="2" <?php if($status==2){echo 'selected="selected"';}?>>已通过</option>
    								            <option value="3" <?php if($status==3){echo 'selected="selected"';}?>>未通过</option>
    								        </select>
    								       </label>
    								       <label>举报类型：
							                <select name="type">
    								            <option value="0">全部</option>
    								            <?php if($style==1 or $style==2){?>
    								            <option value="1" <?php if($type==1){echo 'selected="selected"';}?>>房源虚假</option>
    								            <?php }else{?>
    								            <option value="2" <?php if($type==2){echo 'selected="selected"';}?>>客源虚假</option>
    								            <?php }?>
    								            <option value="3" <?php if($type==3){echo 'selected="selected"';}?>>已成交</option>
    								            <option value="4" <?php if($type==4){echo 'selected="selected"';}?>>其它</option>
    								        </select>
    								       </label>
    								       <label>举报人姓名：<input type="text" name="broker_name" value="<?=$broker_name?>"></label>
							               <label>被举报人姓名：<input type="text" name="brokered_name" value="<?=$brokered_name?>"></label>
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
                                    <?php if($style==1 or $style==2){?>
                                    <th style="width:68px;">房源编号</th>
                                    <?php }else{?>
                                    <th style="width:68px;">客源编号</th>
                                    <?php }?>
                                    <th style="width:42px;">类型</th>
                                    <th style="width:68px;">面积(平方)</th>
                                    <th style="width:68px;">价格(<?=$prize_danwei?>)</th>
                                    <?php if($style==1 or $style==2){?>
                                    <th style="width:68px;">业主电话</th>
                                    <?php }else{?>
                                    <th style="width:68px;">客户电话</th>
                                    <?php }?>
                                    <th style="width:55px;">提交时间</th>
                                    <th style="width:68px;">举报类型</th>
                                    <th style="width:68px;">举报理由</th>
                                    <th style="width:42px;">证据</th>
                                    <th style="width:42px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if($report_info){
                                foreach ($report_info as $key=>$value){
                                    $house_info='';
                                    if($value['house_info']){
                                        $house_info=unserialize($value['house_info']);
                                    }
                                ?>
                                <tr>
                                    <th><?=$value['broker_name']?></th>
                                    <th><?=$value['brokered_name']?></th>

                                    <th><?=$value['number']?></th>

                                    <th>
                                    <?php
                                    switch ($style)
                                    {
                                        case 1:echo '出售';break;
                                        case 2:echo '出租';break;
                                        case 3:echo '求购';break;
                                        case 4:echo '求租';break;
                                    }
                                    ?>
                                    </th>
                                    <th>
                                    <?php
                                    if($house_info){
                                       echo $house_info['buildarea'];
                                    }
                                    ?>
                                    </th>
                                    <th>
                                    <?php
                                    if($house_info){
                                        echo $house_info['price'];
                                    }
                                    ?>
                                    </th>

                                    <th><?=$value['phone']?></th>

                                    <th><?=date('Y-m-d H:i:s', $value['date_time'])?></th>
                                    <th>
                                    <?php
                                    switch ($value['type'])
                                    {
                                        case 1:echo '房源虚假';break;
                                        case 2:echo '客源虚假';break;
                                        case 3:echo '已成交';break;
                                        case 4:echo '其它';break;
                                    }
                                    ?>
                                    </th>
                                    <th><?=$value['content'] ?></th>
                                    <th>
                                    <?php
                                        $photo_url=explode(',',$value['photo_url']);
                                        $photo_url_num=count($photo_url);
                                        for($i = 0;$i<$photo_url_num-1;$i++){
                                            if($photo_url[$i]){
                                                echo '<a href="'.changepic($photo_url[$i]).'" target="_blank">证据'.($i+1).'</a>';
                                            }
                                        }
                                        ?>
                                    </th>
                                    <?php
                                    if($value['status'] == 2){
                                    ?>
                                    <th>已通过</th>
                                    <?php
                                    }elseif($value['status'] == 3){
                                    ?>
                                    <th>未通过</th>
                                    <?php
                                    }else{
                                    ?>
                                    <th>
                                    <select id="status_modify<?=$value['id'] ?>">
                                        <option value="1" <?php if($value['status']==1){echo 'selected="selected"';}?>>待审核</option>
                                        <option value="2" <?php if($value['status']==2){echo 'selected="selected"';}?>>已通过</option>
                                        <option value="3" <?php if($value['status']==3){echo 'selected="selected"';}?>>未通过</option>
                                    </select>
                                    <button onclick="modify(<?=$value['id'] ?>)">确定</button>
                                    </th>
                                    <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                                }}
                                ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                   <ul class="pagination" style="margin:-8px 0;padding-left:20px">
								         <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/report/index');?>
								    </ul>
                                </div>
                            </div>
                                    <div style="color:blue;position:absolute;right:33px;">
                                        <b>共查到<?php echo $count;?>条数据</b>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function checkdel(){
	if(confirm("确实要删除吗？"))
    {
		return true;
	}
     else
    {	return false;
	}
}
function modify(id){
	var status_modify = $("#status_modify"+id).val();
	$.ajax({
        type: 'post',
        url: '/report/modify/',
        data:{id:id,status_modify:status_modify},
        success: function(data){
            alert(data);
            window.location.href="/report/";
        }
    });
}
</script>
<?php require APPPATH.'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
