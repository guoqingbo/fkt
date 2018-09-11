<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=common/third/My97DatePicker/WdatePicker.js" type="text/javascript"></script>
<script type="text/javascript">
window.parent.addNavClass(17);
</script>
<div class="tab_box" id="js_tab_box">
    <?php echo $user_menu;?>
</div>
<div class="search_box clearfix" id="js_search_box_02">
    <form name="search_form" id="subform" method="post" action="">
        <div class="fg_box">
            <p class="fg fg_tex">日期：</p>
            <div class="fg">
                <input type="text" class="fg-time" id="start_date_begin" name="start_date_begin" onclick="WdatePicker()" value="<?=$post_param['start_date_begin']?>">
            </div>
            <div class="fg fg_tex03">—</div>
            <div class="fg fg_tex03">
            <input type="text" class="fg-time" id="start_date_end" name="start_date_end" onclick="WdatePicker()" value="<?=$post_param['start_date_end']?>">
            <span style="font-weight:bold;color:red;" id="time_reminder"></span>
            </div>
        </div>
        <div class="fg_box">
            <p class="fg fg_tex">经理点评：</p>
            <div class="fg mr10" style="*padding-top:10px;">
                <select class="select" name="comment">
                    <option value="0" <?php if ($post_param['comment'] == '') {echo 'selected';}?>>全部</option>
                    <option value="1" <?php if ($post_param['comment'] == 1) {echo 'selected';}?>>未点评</option>
                    <option value="2" <?php if ($post_param['comment'] == 2) {echo 'selected';}?>>已点评</option>
                </select>
            </div>
        </div>
        <?php if(is_int($company_id) && $company_id>0){?>
        <div class="fg_box">
            <p class="fg fg_tex"> 范围：</p>
            <div class="fg">
                <select class="select" name="post_agency_id" onchange="chang('sell')">
                        <?php if($agency_list && $role_level != 6){?>
                            <option selected value='0'>不限</option>
                            <?php
                            foreach($agency_list as $key=>$val){
                            ?>
                            <option <?php if($val['agency_id'] == $post_param['post_agency_id'] || ($val['agency_id']==$agency_id && $post_param['post_broker_id'] == ''))
                                    echo "selected"; ?> value="<?php echo $val['agency_id'];?>"><?php echo $val['agency_name'];?></option>
                        <?php }}else{?>
                            <option value='<?php echo $agency_id;?>' selected><?php echo $agency_list;?></option>
                        <?php }?>
                    </select>
                </div>
            <div class="fg fg_tex fg_tex03" >
                <select class="select" name="post_broker_id" id="list_broker">
                    <option value='0'>不限</option>
                <?php if($broker_list){ ?>
                    <?php foreach($broker_list as $key=>$val){ ?>
                    <option  <?php if($val['broker_id'] == $post_param['post_broker_id'] ||($val['broker_id']==$broker_id && $post_param['post_broker_id'] == ''))
                            echo "selected"; ?> value='<?php echo $val['broker_id']?>'><?php echo $val['truename']?></option>
                <?php }}?>
                </select>
            </div>
        </div>
	    <?php }else{?>
            <?php if(!empty($register_info['corpname']) && !empty($register_info['storename'])){?>
                <div class="fg_box">
                        <p class="fg fg_tex"> 范围：</p>
                        <div class="fg">
                        <select class="select">
                            <option><?php echo $register_info['corpname'];?></option>
                        </select>
                        </div>
                        <div class="fg fg_tex fg_tex03" >
                            <select class="select">
                                <option><?php echo $register_info['storename'];?></option>
                            </select>
                        </div>
                </div>

            <?php }?>
     <?php }?>

            <!--获取经纪人信息-->
    <script>
    function chang(type){
     var agency_id=$("select[name='post_agency_id']").val();
     $.ajax({
        url: "<?php echo MLS_URL;?>/"+type+"/broker_list/",
        type: "GET",
        dataType: "json",
        data:{agency_id: agency_id},
        success:function(data_list){
            var str_html='<option value="0">不限</option>';
            if(agency_id>0){
                for(var i=0;i<data_list.length;i++){
                    str_html +='<option value='+data_list[i].broker_id+'>'+data_list[i].truename+'</option>';
                }
            }
            $("#list_broker").empty().html(str_html);
        }
     });

    }
    </script>
				<div class="fg_box">
                    <input type="hidden" name="page" value="1">
                    <input type="hidden" name="is_submit" value="1">
					<div class="fg">
                    <a href="javascript:void(0)" onclick="$('#subform :input[name=page]').val('1');form_submit();return false;" class="btn"><span class="btn_inner">搜索</span></a> </div>
					<div class="fg"> <a href="/daily_review/index/" class="reset">重置</a> </div>
				</div>
			</form>
		</div>

<div class="table_all report-form-wrap">
    <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c10">日期</td>
                <td class="c40">日报标题</td>
                <td class="c30">经理点评</td>
                <td class="c10">经纪人</td>
                <td class="c10">操作</td>
            </tr>
        </table>
    </div>

    <div class="inner" id="js_inner" style="height: 389px !important;">
        <table class="table list-table">
            <?php
                if($list){
                    foreach ($list as $key=>$value){
            ?>
            <tr <?php if ($key % 2 != 0) { echo 'class="bg"'; } ?>>
                <td class="c10"><?=date('Y-m-d H:i:s', $value['create_time'])?></td>
                <td class="c40"><?=$value['title']?></td>
                <td class="c30 zws_my_report"><?php if ($value['comment_broker_id'] > 0) {echo '已点评';} else {echo '未点评';}?></td>
                <td class="c10"><?=$value['truename']?></td>
                <td class="c10"><a href="javascript:void(0)" onclick="find_daily(<?=$value['id']?>)">查看</a></td>
            </tr>
            <?php }}else{?>
                    <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php }?>
        </table>
    </div>
</div>
<div class="fun_btn fun_btn_bottom clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
<!--编辑资料弹窗-->
<div id="js_find_daily_pop" class="iframePopBox" style="width:580px; height:510px;border-left:none;">
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1">&#xe60c;</a>
    <iframe frameborder="0" scrolling="auto" width="580px" height="510px" class='iframePop' src="" name="childIframe"></iframe>
</div>
<script type="text/javascript">
    function find_daily(id)
    {
        $('#js_find_daily_pop .iframePop').attr('src','/daily_review/find_daily/' + id);
        openWin('js_find_daily_pop');
    }
    //通过参数判断是否可以被提交
    function form_submit(){
        var is_submit = $("input[name='is_submit']").val();
        if(is_submit ==1){
            $('#subform').submit();
        }
    }
</script>
