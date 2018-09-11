<?php require APPPATH.'views/header.php'; ?>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><a href="/advert_app_manage/"><?php echo $title ?></a></h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <!-- /.panel-heading -->
                    <div class="panel-body">
                        <div class="table-responsive">
                            <form name="add_form" method="post" enctype="multipart/form-data" action="">
                                    <div role="grid" class="dataTables_wrapper form-inline" id="dataTables-example_wrapper">
                                        <div class="row">
                                            <div class="col-sm-6" style="width:100%">
                                                <div class="dataTables_length" id="dataTables-example_length">
                                                </div>
                                            </div>
                                        </div>
                                        <input type='hidden' name='submit_flag' value='add'/>
                                        <div class="col-sm-6" style="width:100%; text-align: center;">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    <font style="font-size: 16px; color:red;font-weight:bold;"><?=$news['title']?></font>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6" style="width:100%; ">
                                            <div class="dataTables_length" id="dataTables-example_length">
                                                <label>
                                                    &nbsp;<font style="font-size: 12px;"><?=$news['new_content']?></font>
                                                </label>
                                            </div>
                                        </div>	
                                    </div>
                                </form>
                            </div>
                           </div>
                           </div>
                          </div>
                </div>
            </div>
        </div>
<?php require APPPATH.'views/footer.php'; ?>