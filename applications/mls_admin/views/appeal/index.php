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
                                                <label>状态：
                                                    <select name="status" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">全部</option>
                                                        <option value="1">待审核</option>
                                                        <option value="2">已通过</option>
                                                        <option value="3">未通过</option>
                                                    </select>
                                                </label>
                                                <label>
                                                     合同编号：
                                                    <input type='search' class="form-control input-sm" size='12' name="appraise_id" value="<?=$appraise_id?>"/>
                                                </label>
                                                <label>
                                                                                                                                                                                                 申诉人：
                                                    <input type='search' class="form-control input-sm" size='12' name="broker_name" value="<?=$broker_name?>"/>
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
                                    <th style="width:40px;">交易编号</th>
                                    <th style="width:40px;">合作房源</th>
                                    <th style="width:40px;">整体评价</th>
                                    <th style="width:40px;">细节评价</th>
                                    <th style="width:40px;">评价内容</th>
                                    <th style="width:40px;">评价时间</th>
                                    <th style="width:40px;">评价方</th>
                                    <th style="width:40px;">申诉人</th>
                                    <th style="width:40px;">证据</th>
                                    <th style="width:40px;">申诉理由</th>
                                    <th style="width:40px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                           if (isset($appeal_info) && !empty($appeal_info))
                            {
                                foreach ($appeal_info as $key => $value) {
                            ?>
                                    <tr class="gradeA">
                                        <td><?=$value['transaction_id']?></td>
                                        <td>
                                        <?php
                                        $house_info = $value['house_info'];
                                        $unit = $house_info['tbl'] == 'sell' ? '万' : '元/月';
                                        echo $house_info['districtname'].'-'.$house_info['streetname'].' '.
                                        $house_info['blockname'].' '.$house_info['room'].'室'.$house_info['hall'].'厅'.
                                        $house_info['toilet'].'卫 '.$house_info['fitment_str'].' '.$house_info['forward_str'].' '.$house_info['buildarea'].' ㎡ '.$house_info['price'] . $unit;
                                        ?>
                                        </td>
                                        <td><?=$value['trust_name'] ?></td>
                                        <td>
                                            <div class="pjxj">
                                                <div class="pjxj_name">信息真实度</div>
                                                <div class="pjxj_dj"><?=$value['info_star'] ?></div>
                                            </div><br><br><br>
                                            <div class="pjxj">
                                                <div class="pjxj_name">态度满意度</div>
                                                <div class="pjxj_dj"><?=$value['atti_star'] ?></div>
                                            </div><br><br><br>
                                            <div class="pjxj">
                                                <div class="pjxj_name">业务专业度</div>
                                                <div class="pjxj_dj"><?=$value['busi_star'] ?></div>
                                            </div><br><br><br>
                                        </td>
                                        <td><?=$value['content'] ?></td>
                                        <td><?=date('Y-m-d H:i:s',$value['create_time'])?></td>
                                        <td><?=$value['cooperate_broker_name']?></td>
                                        <td><?=$value['broker_name']?></td>
                                        <td>
                                        <?php
                                        $photo_url=explode(',',$value['photo_url']);
                                        $photo_url_num=count($photo_url);
                                        for($i = 0;$i<$photo_url_num-1;$i++){
                                            if($photo_url[$i]){
                                                echo '<a href="'.changepic($photo_url[$i]).'" target="_blank">证据'.($i+1).'</a>';
                                            }
                                        }
                                        ?>
                                        </td>
                                        <td><?=$value['reason']?></td>
                                        <td>
                                        <?php
                                        switch ($value['status']){
                                            case 1:
                                                echo '<select id="status_modify'.$value['id'].'">
                                                        <option value="1">待审核</option>
                                                        <option value="2">已通过</option>
                                                        <option value="3">未通过</option>
                                                    </select>
                                                    <button onclick="modify('.$value['id'].')">确定</button>';break;
                                            case 2:echo '已通过';break;
                                            case 3:echo '未通过';break;
                                        }

                                        ?>

                                        </td>
                                    </tr>
                            <?php
                                }
                           }
                            ?>
                            </tbody>
                        </table>
                        <div class="row">
                           <div class="col-sm-6">
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
<script type="text/javascript">
function modify(id){
	var status = $("#status_modify"+id).val();
	var data = {id:id,status:status};
	$.ajax({
		type: "POST",
		url: "/appeal/modify/",
		data:data,
		cache:false,
		error:function(){
			alert("系统错误");
			return false;
		},
		success: function(data){
			alert(data);
			window.location.href = "/appeal/";
		}
	});
}

</script>
<?php require APPPATH . 'views/footer.php'; ?>
