<?php require APPPATH . 'views/header.php'; ?>
<script src="<?=MLS_SOURCE_URL ?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
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
                            <form name="search_form" id="search_form" method="post" action="" >
                                <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                    <div class="row">
                                         <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <select name="search_where" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">条件筛选</option>
														<option value="product_serial_num" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'product_serial_num')){echo 'selected="selected"';}?>>商品编号</option>
                                                        <option value="product_name" <?php if((!empty($where_cond['search_where']) && $where_cond['search_where'] == 'product_name')){echo 'selected="selected"';}?>>商品名称</option>
                                                    </select>
                                                </label>
												<label>
                                                    <div><input type='search' class="form-control input-sm" size='12' name="search_value" value="<?php if(!empty($where_cond['search_value'])) {echo $where_cond['search_value'];}?>"/>
                                                    </div>
                                                </label>
												<label>
                                                    <select name="search_where2" aria-controls="dataTables-example" class="form-control input-sm">
                                                        <option value="0">条件筛选</option>
														<option value="phone" <?php if((!empty($where_cond['search_where2']) && $where_cond['search_where2'] == 'phone')){echo 'selected="selected"';}?>>手机号码</option>
                                                        <option value="truename" <?php if((!empty($where_cond['search_where2']) && $where_cond['search_where2'] == 'truename')){echo 'selected="selected"';}?>>真实姓名</option>
                                                    </select>
                                                </label>
												<label>
                                                    <div><input type='search' class="form-control input-sm" size='12' name="search_value2" value="<?php if(!empty($where_cond['search_value2'])) {echo $where_cond['search_value2'];}?>"/>
                                                    </div>
                                                </label>
												<label>
                                                    <div>
														订单编号：<input type='search' class="form-control input-sm" size='12' name="order" value="<?php if(!empty($where_cond['order'])) {echo $where_cond['order'];}?>"/>
													</div>
                                                </label>
												<label>
                                                    <div>
														兑换时间：<input type="text" name="time_s" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_s'])) {echo $_POST['time_s'];}?>" onclick="WdatePicker()"/>
														&nbsp;至&nbsp;
														<input type="text" name="time_e" style="width:123px"  class="form-control input-sm" aria-controls="dataTables-example" value="<?php if(isset($_POST['time_e'])) {echo $_POST['time_e'];}?>" onclick="WdatePicker()"/>
													</div>
                                                </label>
                                                <label>
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <input type="hidden" id="pg" name="pg" value="<?=$page?>">
                                                        <input class="btn btn-primary" onclick="$('#pg').val('1');" type="submit" value="查询">&nbsp;&nbsp;&nbsp;<input class="btn btn-primary" type="button" onclick="javascript:location.href = '/gift_exchange_record/'" value="重置">&nbsp;&nbsp;&nbsp;
														<a class="btn btn-primary" href='/gift_exchange_record/exportReport/<?php if((isset($where_cond['search_where']) && isset($where_cond['search_value'])) || (isset($where_cond['search_where2']) && isset($where_cond['search_value2'])) || (isset($where_cond['search_where2']) && isset($where_cond['search_value2'])) || isset($where_cond['order']) || (isset($_POST['time_s']) && isset($_POST['time_e']))){?><?php echo '?';?><?php }?>
														<?php
															if(isset($where_cond['search_where']) && isset($where_cond['search_value'])){
																echo 'search_where='.$where_cond['search_where'].'&search_value='.$where_cond['search_value'];
															}
															if(isset($where_cond['search_where2']) && isset($where_cond['search_value2'])){
																echo '&search_where2='.$where_cond['search_where2'].'&search_value2='.$where_cond['search_value2'];
															}
															if(isset($where_cond['order'])){
																echo '&order='.$where_cond['order'];
															}
															if( isset($_POST['time_s']) && isset($_POST['time_e'])){
																echo '&time_s='.$_POST['time_s'].'&time_e='.$_POST['time_e'];
															}
															if( isset($type)){
																echo '&type='.$type;
															}
														?>'>导出</a>
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
									<th>订单号</th>
									<th>真实姓名</th>
									<th>手机号码</th>
                                    <th>商品编号</th>
                                    <th>商品名称</th>
									<?php if($type == 1){?>
									<th>兑换积分值</th>
									<th>兑换时间</th>
									<?php }else{?>
									<th>中奖时间</th>
									<?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($gift) && !empty($gift)) {
                                foreach ($gift as $key => $value) { ?>
                                    <tr class="gradeA">
										<td><?php echo $value['id'];?></td>
										<td><?php echo $value['order'];?></td>
                                        <td><?php echo $value['truename'];?></td>
                                        <td><?php echo $value['phone'];?></td>
                                        <td><?php echo $value['product_serial_num'];?></td>
										<td><?php echo $value['product_name'];?></td>
										<?php if($type == 1){?>
                                        <td><?php echo $value['score_record']; ?></td>
										<?php } ?>
										<td><?php echo date("Y-m-d H:i:s",$value['create_time']);?></td>


                                    </tr>
                            <?php }} ?>
                            </tbody>
                        </table>
                        <div class="row">
                            <div class="col-sm-6 clearfix" style="width:100%;">
                                <span style="float:right; color:blue;padding-right:20px" ><b>&nbsp;&nbsp;&nbsp;&nbsp;共查到&nbsp;<?php echo $count;?>&nbsp;条数据！</b></span>
                                <div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate">
                                    <ul class="pagination" style="margin:-8px 0;padding-left:20px">
                                        <?php echo page_uri($page,$pages,MLS_ADMIN_URL.'/gift_manage/');?>
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

<?php require APPPATH . 'views/footer.php'; ?>
