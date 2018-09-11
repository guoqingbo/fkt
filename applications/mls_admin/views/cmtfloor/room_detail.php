<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">修改房间</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php if(''==$modifyResult){; ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                        <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                            <div class="row">
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type='hidden' name='submit_flag' value='modify'/>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        楼号:&nbsp&nbsp&nbsp<?php echo $room_data['floor_num']?>
                                                    </label>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <label>
                                                        房号:&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" name="room_num" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $room_data['room_num'];?>">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        户型:&nbsp&nbsp&nbsp&nbsp<input type="text" name="room_count" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $room_data['room_count'];?>">室
                                                        <input type="text" name="hall_count" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $room_data['hall_count'];?>">厅
                                                    </label>								 
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        建筑面积:&nbsp&nbsp&nbsp&nbsp<input type="text" name="area" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $room_data['area'];?>"> ㎡
                                                    </label>							 
                                                </div>
                                            </div>
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <label>
                                                        物业类别:&nbsp&nbsp&nbsp&nbsp
                                                        <select name="build_type" class="form-control input-sm" style="width:168px">
                                                            <option value="">请选择</option>
                                                            <option value="写字楼" <?php if('写字楼'==$room_data['build_type']){echo 'selected="selected"';}?>>写字楼</option>
                                                            <option value="住宅" <?php if('住宅'==$room_data['build_type']){echo 'selected="selected"';}?>>住宅</option>
                                                            <option value="别墅" <?php if('别墅'==$room_data['build_type']){echo 'selected="selected"';}?>>别墅</option>
                                                            <option value="商铺" <?php if('商铺'==$room_data['build_type']){echo 'selected="selected"';}?>>商铺</option>
                                                            <option value="厂房" <?php if('厂房'==$room_data['build_type']){echo 'selected="selected"';}?>>厂房</option>
                                                            <option value="车库" <?php if('车库'==$room_data['build_type']){echo 'selected="selected"';}?>>车库</option>
                                                        </select>    
                                                    </label>							 
                                                </div>
                                            </div>
									  <?php if (!empty($mess_error)) { ?>
                                                <div class="col-sm-6" style="width:100%">
                                                    <div class="dataTables_length" id="dataTables-example_length">
                                                        <font color='red'><?php echo $mess_error; ?></font>
                                                    </div>
                                                </div>
                                            <?php } ?>									  								  
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                    <input class="btn btn-primary" type="submit" value="提交">
                                                    <input class="btn btn-primary" type="button" value="返回" onclick="window.history.go(-1);">
                                                </div>
                                            </div>		
                                        </div>
                                    </form>
								</div>
                               </div>
                               </div>
                              </div>
                        <!-- /.panel-body -->
                        
                    </div>
            <?php }else if(0===$modifyResult){ ?>
            	<div>更新失败</div>
            <?php }else{?>
            	<div>更新成功</div>
                <script type="text/javascript">
                    window.history.go(-2);
                </script>
            <?php }?>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
<?php require APPPATH.'views/footer.php'; ?>
