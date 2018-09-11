<?php require APPPATH.'views/header.php'; ?>
    <div id="wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">审核楼盘</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">

                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="tableoutline" width="700" cellspacing="0" cellpadding="0" border="0" bgcolor="#DFDFDF" align="center">
                                    <tr>
                                        <td valign="bottom" height="30">
                                            <input id="cmt_id" type="hidden" value="<?php echo $comm['id'];?>"/>
                                            <span style="font-size:14px;font-weight:bold;">楼盘名称：</span><?php echo $comm['cmt_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span style="font-size:14px;font-weight:bold;">区属：</span><?php echo $comm['dist_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <span style="font-size:14px;font-weight:bold;">板块：</span><?php echo $comm['street_name'];?>
                                            <br>
                                            <span style="font-size:14px;font-weight:bold;">楼盘地址：</span><?php echo $comm['address'];?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center">
                                            <div height="24" style="width:500px;margin-top:15px;">
                                                <table width="100%" cellspacing="0" border="0" align="center">
                                                    <tbody>
                                                        <tr><td width="33%"><input type="radio" <?php if($comm['status']==2){?>checked=""<?php }?> value="2" name="action_type" <?php if($comm['status']!=3){?>disabled="true"<?php }?>>新增为正式小区</td></tr>
                                                        <tr><td width="33%"><input type="radio" <?php if($comm['status']==4){?>checked=""<?php }?> value="1" name="action_type" <?php if($comm['status']!='3'){?>disabled="true"<?php }?>>与已有小区合并</td></tr>
                                                        <tr><td width="40%"><input type="radio" <?php if($comm['status']==4){?>checked=""<?php }?> value="0" name="action_type" <?php if($comm['status']!=3){?>disabled="true"<?php }?>>删除楼盘</td></tr>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </td>
                                    </tr>

                                    <tr height="80">
                                        <td>
                                            <hr width="690px" align="center">
                                        </td>
                                    </tr>
                                </table>s
                                <table class="tableoutline" width="700" cellspacing="0" cellpadding="0" border="0" bgcolor="#DFDFDF" align="center">
                                    <tbody style="display:none;" id="cmt_merge">
                                        <tr>
                                            <td height="35">
                                                <div style="width:100%;margin-left:50px;margin-top:10px;">
                                                    <span style="font-size:14px;">小区名称：</span>
                                                    <span style="margin-left:10px;">
                                                        <input type="text" onfocus="h_tip('blockname')" style="height: 25px;line-height:25px;width:200px;" id="blockname" name="blockname" value="<?php echo $comm['cmt_name'];?>">
                                                        <input type="button" value="查  询" onclick="search_block(1,<?php echo $comm['id'];?>)" class="btn_search" name="search_blockname">
                                                    </span>
                                                    <span style="margin-left:20px;display:none;color:red" id="blockname_waring"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="35">
                                                <div style="width:100%;margin-left:50px;margin-top:10px;">
                                                    <span style="font-size:14px;">小区地址：</span>
                                                    <span style="margin-left:10px;">
                                                        <input type="text" onfocus="h_tip('blockaddress')" style="height: 25px;line-height:25px;width: 200px;" id="blockaddress" name="blockaddress" value="<?php echo $comm['address'];?>">
                                                        <input type="button" value="查  询" onclick="search_block(2,<?php echo $comm['id'];?>)" class="btn_search" name="search_blockname">
                                                    </span>
                                                    <span style="margin-left:20px;display:none;color:red" id="blockaddress_waring"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="20">&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td>
                                                待审核/临时楼盘
                                                <table width="600" cellspacing="0" cellpadding="0" border="1" align="center" style="line-height:24px;" id="show_details">
                                                    <tbody><tr bgcolor="#8E8E8E">
                                                            <td style="display:none;" id='checkbox'></td>
                                                            <td style="text-align:center;font-weight:bold;">小区名称</td>
                                                            <td style="text-align:center;font-weight:bold;">区属</td>
                                                            <td style="text-align:center;font-weight:bold;">板块</td>
                                                            <td style="text-align:center;font-weight:bold;">楼盘地址</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;font-weight:bold;" colspan="4">暂无小区信息</td>
                                                        </tr>
                                                    </tbody></table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                正式楼盘
                                                <table width="600" cellspacing="0" cellpadding="0" border="1" align="center" style="line-height:24px;" id="show_details2">
                                                    <tbody><tr bgcolor="#8E8E8E">
                                                            <td style="display:none;" id='checkbox2'></td>
                                                            <td style="text-align:center;font-weight:bold;">小区名称</td>
                                                            <td style="text-align:center;font-weight:bold;">区属</td>
                                                            <td style="text-align:center;font-weight:bold;">板块</td>
                                                            <td style="text-align:center;font-weight:bold;">楼盘地址</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="text-align:center;font-weight:bold;" colspan="4">暂无小区信息</td>
                                                        </tr>
                                                    </tbody></table>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td height="35">
                                                <div id="totalblock" style="display:none;">
                                                    <span style="font-size:14px;">目标小区编号<font color='red'>*</font>：</span>
                                                    <span style="margin-left:10px;">
                                                        <input type="text" style="height: 25px;line-height:25px;width: 200px;" id="mainblock" name="mainblock">
                                                    </span>
                                                    <span style="margin-left:20px;display:none;color:red" id="blockaddress_waring"></span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div id="merge" style="display:none;">
                                                    <input type="button" value=" 合 并 " title="合并" alt="" class="thickbox" name="bt_hb" onclick="hb_block()">
                                                </div>
                                            </td>
                                        </tr>

                                    </tbody>

                                </table>
                                <?php if('3'==$comm['status']){?>
                                    <input style="position:absolute; bottom:19px; left:413px;" type="button" value=" 确 定 " title="确定" alt="" class="btn btn-primary" name="bt_hb" onclick="last_action()">
                                <?php }?>
                                    <input type="button" onclick="window.location.href='../check_list';" value="返回" class="btn btn-primary" style="position:absolute; bottom:19px; left:500px;">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->



        </div>
        <!-- /#page-wrapper -->

    </div>
				 <div class="col-lg-4" style="display:none" id="js_note1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            提示框
							<button type="button" class="close JS_Close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="panel-body">
                            <p id="warning_text"></p>
                        </div>
                    </div>
                </div>
    <script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/mls_admin/js/block_check.js" charset="UTF-8"></script>
<?php require APPPATH.'views/footer.php'; ?>
    <script type="text/javascript">
        $(function(){
            $('input[name="action_type"][value="1"]').click(function(){
                $('#cmt_merge').show();
            });
            $('input[name="action_type"][value="2"]').click(function(){
                $('#cmt_merge').hide();
            });
            $('input[name="action_type"][value="0"]').click(function(){
                $('#cmt_merge').hide();
            });
        });
    </script>

