<?php require APPPATH . 'views/header.php'; ?>
<style>
    span{text-align: right;display: inline-block;width:60px}
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>
        <?php if ($result == '') {?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <form name="search_form" method="post" action="">
                                    <input type="hidden" name="submit_flag" value="save">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>委托编号:&nbsp&nbsp</span><input value="<?php echo $list['id'];?>" disabled class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选">
                                                    <input type="hidden" name="id" value="<?php echo $list['id'];?>">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>用户姓名:&nbsp&nbsp</span><input value="<?php echo $list['realname'];?>" disabled class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>小区:&nbsp&nbsp</span><input value="<?php echo $list['comt_name'];?>" disabled class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <span>公司:&nbsp&nbsp</span><input id="company_name"  value="" class="input_text input_text_r w150 form-control input-sm" style="width:180px;display: inline-block;height:30px; line-height: 30px" type="text" placeholder="输入汉字筛选">
                                                                                 <input type="hidden" id="company_id" name="company_id" value="">
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                 <label>
                                                   <span>分店:&nbsp&nbsp</span>
                                                    <select name="agency_id"  id="agency_id" >
                                                        <option value="">请选择</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                 <label>
                                                   <span>经纪人:&nbsp&nbsp</span>
                                                    <select name="broker_id"  id="broker_id" >
                                                        <option value="">请选择</option>
                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                        <script>
                                         $("#company_name").change(function(){
                                             var company_name= $(this).val();
                                             $.ajax({
                                                type: 'get',
                                                url : '<?php echo MLS_ADMIN_URL; ?>/entrust_sell/get_agency/',
                                                dataType:'json',
                                                data:{
                                                    name:company_name
                                                },
                                                success: function(msg){
                                                    var str = '';
                                                    if(msg.length===0){
                                                        str = '<option value="">请选择</option>';
                                                        $('#agency_id').empty();
                                                        $('#agency_id').append(str);
                                                        $('#broker_id').empty();
                                                        $('#broker_id').append(str);
                                                    }else{
                                                        for(var i=0;i<msg.length;i++){
                                                            str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                                                            $("#company_id").val(msg[i].company_id);
                                                        }
                                                        $('#agency_id').empty();
                                                        $('#agency_id').append(str);
                                                        var agency_id= $("#agency_id").first().val();
                                                        get_broker(agency_id);
                                                    }
                                                }
                                             });
                                         });
                                         $("#agency_id").change(function(){
                                             var agency_id= $(this).val();
                                             get_broker(agency_id)
                                         });
                                         function get_broker(agency_id){
                                            $.ajax({
                                                type: 'get',
                                                url : '<?php echo MLS_ADMIN_URL; ?>/entrust_sell/get_broker/',
                                                dataType:'json',
                                                data:{
                                                    agency_id:agency_id
                                                },
                                                success: function(msg){
                                                    var str = '';
                                                    if(msg.length===0){
                                                        str = '<option value="">请选择</option>';
                                                    }else{
                                                        for(var i=0;i<msg.length;i++){
                                                            str +='<option value="'+msg[i].id+'">'+msg[i].truename+'</option>';
                                                        }
                                                    }
                                                    $('#broker_id').empty();
                                                    $('#broker_id').append(str);
                                                }
                                             });
                                         }

                                    </script>
                                        <div class="col-sm-6" style="width:100%">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <input type="hidden" name="status" value="1">
                                                <input class="btn btn-primary" type="submit" value="保存">
                                                <a class="btn btn-primary" href="/entrust_sell/index">返回</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        <?php } else if (0 === $result) { ?>
            <div><h1><b>添加失败</b><h1></div>
        <?php } else { ?>
            <div><h1><b>添加成功</b><h1></div>
        <?php } ?>
    </div>
</div>
<?php if($result!==""){?>
<script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL . '/entrust_sell/index/'; ?>";
            }, 1000);
        });
</script>
<?php }?>
