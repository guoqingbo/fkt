<?php require APPPATH . 'views/header.php'; ?>
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css,css/v1.0/cal.css,css/v1.0/system_set.css,css/v1.0/personal_center.css">
<link type="text/css" rel="stylesheet" href="<?=MLS_SOURCE_URL ?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/guest_disk.css ">
<style>
 .set_basic_wra {
    background: #fbfbfb none repeat scroll 0 0;
    border: 1px solid #e6e6e6;
    width: 100%;
 }
.text{
    display: inline-block;
    color: #666;
    float: left;
    height: 24px;
    line-height: 24px;
    padding-left: 13px;
    text-align: right;
    width: 120px;
    }
.input_radio{
    width:20px;
    }
h1{
    font-weight: 500;
    font-size: 2em;
    margin: 0.67em 0;
    color: inherit;
    font-family: inherit;
    }
</style>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <h1 class="page-header"><?php echo $title; ?></h1>
        </div>
        <?php if ('' == $setinfo) {?>
        <form name="search_form" method="post" action="" >
            <input type='hidden' name='submit_flag' value='save'/>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div id="js_inner" class="forms forms_scroll h91" style="height: 547px;">
                        <div class="forms_details_fg forms_details_fg_bg clearfix">
                            <div class="clearfix">
				<h3 class="h3">房源信息</h3>
			    </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>房源名称：</div>
                                    <input type="text" value="<?php echo $list['name'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="name">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>联系电话：</div>
                                   <input type="text" value="<?php echo $list['phone'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:120px;display: inline-block;"  name="phone">
                                </div>
                            </div>
                            <div class="item_fg clearfix house_type2">
                                <div class="width_b left">
                                    <div class="text"><b class="red">*</b>区域：</div>
                                    <div class="left js_fields">
                                        <select name="district" class="select" id="district">
                                            <option value="" selected="">请选择</option>
                                            <?php if($district){
                                                foreach($district as $key =>$val){?>
                                             <option value="<?php echo $val['id'];?>" <?php echo $list['district_id']==$val['id']?"selected":"";?>><?php echo $val['district'];?></option>
                                            <?php }}?>
                                        </select>
                                    </div>

                                    <div class="text"><b class="red">*</b>板块：</div>
                                    <div class="left js_fields">
                                        <select id="street" name="street" class="select">
                                            <?php if($street)
                                            foreach($street as $key =>$val){?>
                                            <option value="<?php echo $val['id'];?>" <?php echo $list['street_id']==$val['id']?"selected":"";?>><?php echo $val['streetname'];?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <script>
                                        $("#district").change(function(){
                                            var val=$(this).val();
                                            $.ajax({
                                            url:"<?=MLS_ADMIN_URL?>/new_house/get_street",
                                            type:"GET",
                                            dataType:"json",
                                            data:{
                                               'val':val
                                            },
                                            success:function(data){
                                                var html="";
                                                if(data && data.length > 0){
                                                   for(var i in data){
                                                       html+="<option value='"+data[i]['id']+"'>"+data[i]['streetname']+"</option>";
                                                   }
                                                   $("#street").html(html);
                                                }else{
                                                      html+="<option value=''>请选择</option>";
                                                      $("#street").html(html);
                                                }
                                            }
                                          });
                                        });
                                    </script>
                                </div>
                                <div class="clearfix item_fg js_fields">
                                    <div class="text"><b class="red">*</b>物业类型：</div>
                                    <i><input type="radio" id="js_house_type_ZZ" checked="" value="1" class="input_radio" name="type" <?php echo $list['type']==1?"checked":"";?>>
                                        住宅</i>
                                    <i><input type="radio" id="js_house_type_BS" class="input_radio" value="2" name="type" <?php echo $list['type']==2?"checked":"";?> >
                                        别墅</i>
                                    <i> <input type="radio" id="js_house_type_SP" class="input_radio" value="3" name="type" <?php echo $list['type']==3?"checked":"";?>>
                                        商铺</i>
                                    <i><input type="radio" id="js_house_type_XZL" class="input_radio" value="4" name="type" <?php echo $list['type']==4?"checked":"";?>>
                                        写字楼</i>
                                    <i><input type="radio" id="js_house_type_CF" class="input_radio" value="5" name="type" <?php echo $list['type']==5?"checked":"";?>>
                                        厂房</i>
                                    <i><input type="radio" id="js_house_type_CK01" class="input_radio" value="6" name="type" <?php echo $list['type']==6?"checked":"";?>>
                                        仓库</i>
                                    <i><input type="radio" id="js_house_type_CK02" class="input_radio" value="7" name="type" <?php echo $list['type']==7?"checked":"";?>>
                                        车库</i>
                                    <i><input type="radio" id="js_house_type_JDS" class="input_radio" value="7"
                                              name="type" <?php echo $list['type'] == 7 ? "checked" : ""; ?>>
                                        酒店式公寓</i>
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="left width_b house_type2">
                                    <div class="text"><b class="red">*</b>装修情况：</div>
                                    <div class="left js_fields">
                                        <i><input type="radio" value="1" name="renovation" class="input_radio" <?php echo $list['renovation']==1?"checked":"";?>> 毛坯</i>
                                        <i><input type="radio" value="2" name="renovation" class="input_radio" <?php echo $list['renovation']==2?"checked":"";?>> 简装</i>
                                        <i><input type="radio" value="3" name="renovation" class="input_radio" <?php echo $list['renovation']==3?"checked":"";?>> 中装</i>
                                        <i><input type="radio" value="4" name="renovation" class="input_radio" <?php echo $list['renovation']==4?"checked":"";?>> 精装</i>
                                        <i><input type="radio" value="5" name="renovation" class="input_radio" <?php echo $list['renovation']==5?"checked":"";?>> 豪装</i>
                                        <i><input type="radio" value="6" name="renovation" class="input_radio" <?php echo $list['renovation']==6?"checked":"";?>> 婚装</i>
                                    </div>
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>绿化：</div>
                                    <input type="text" value="<?php echo $list['green'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="green">%
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>开盘时间：</div>
                                   <input type="text" value="<?php echo $list['open_time'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="green">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>交付时间：</div>
                                   <input type="text" value="<?php echo $list['give_time'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="green">
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>物业公司：</div>
                                   <input type="text" value="<?php echo $list['wy_company'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="wy_company">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>物业费：</div>
                                   <input type="text" value="<?php echo $list['property'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="property">元
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>开发商：</div>
                                   <input type="text" value="<?php echo $list['developers'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="developers">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>开发商官网：</div>
                                   <input type="text" value="<?php echo $list['devurl'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="devurl">
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>物业地址：</div>
                                   <input type="text" value="<?php echo $list['wy_addr'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="wy_addr">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>售楼地址：</div>
                                   <input type="text" value="<?php echo $list['address'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="address">
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>均价：</div>
                                   <input type="text" value="<?php echo $list['price'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="price">元
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>总楼层：</div>
                                   <input type="text" value="<?php echo $list['tfloor'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="tfloor">层
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>车位信息：</div>
                                   <input type="text" value="<?php echo $list['address'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="parking">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>占地面积：</div>
                                   <input type="text" value="<?php echo $list['covered'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:80px;display: inline-block;"  name="covered">㎡
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>工程进度：</div>
                                   <input type="text" value="<?php echo $list['speed'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="speed">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>产权年限：</div>
                                   <input type="text" value="<?php echo $list['chanquan'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="chanquan">年
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>总户数：</div>
                                   <input type="text" value="<?php echo $list['households'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="households">户
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>出售情况：</div>
                                   <input type="text" value="<?php echo $list['is_sell'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="is_sell">
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>项目特色：</div>
                                   <input type="text" value="<?php echo $list['shtick'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="shtick">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>主力户型：</div>
                                        <div class="y_fg">
                                            <div class="left js_fields">
                                                <select name="room" class="select">
                                                    <option value="1">1</option>
                                                    <option selected="" value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                    <option value="6">6</option>
                                                </select>
                                                <div class="errorBox clear"></div>
                                            </div>
                                            <span class="y_fg y_fg_p5">室</span>
                                            <div class="left js_fields">
                                                <select name="hall" class="select">
                                                    <option value="0">0</option>
                                                    <option value="1">1</option>
                                                    <option selected="" value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                                <div class="errorBox clear"></div>
                                            </div>
                                            <span class="y_fg y_fg_p5">厅</span>
                                            <div class="left js_fields">
                                                <select name="toilet" class="select">
                                                    <option value="0">0</option>
                                                    <option selected="" value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                                <div class="errorBox clear"></div>
                                            </div>
                                            <span class="y_fg y_fg_p5">卫</span>
                                            <div class="left js_fields">
                                                <select name="kitchen" class="select">
                                                    <option value="0">0</option>
                                                    <option selected="" value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                                <div class="errorBox clear"></div>
                                            </div>
                                            <span class="y_fg y_fg_p5">厨</span>
                                            <div class="left js_fields">
                                                <select name="balcony" class="select">
                                                    <option value="0">0</option>
                                                    <option selected="" value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                </select>
                                                <div class="errorBox clear"></div>
                                            </div>
                                            <span class="y_fg y_fg_p5">阳台</span>
                                        </div>
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>搜索关键字：</div>
                                   <input type="text" value="<?php echo $list['keywords'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="keywords">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>优惠信息：</div>
                                   <input type="text" value="<?php echo $list['prefer'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:180px;display: inline-block;"  name="prefer">
                                </div>
                            </div>
                            <div class="item_fg clearfix">
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>百度地图X坐标：</div>
                                   <input type="text" value="<?php echo $list['b_map_x'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="b_map_x">
                                </div>
                                <div class="width_b left js_fields">
                                    <div class="text"><b class="red">*</b>百度地图Y坐标：</div>
                                   <input type="text" value="<?php echo $list['b_map_y'];?>" aria-controls="dataTables-example" class="form-control input-sm " style="width:60px;display: inline-block;"  name="b_map_y">
                                </div>
                            </div>
                            <br>
                            <div class="js_s_h_info_house">
                                <div class="text"><b class="red">*</b>户源详情：</div>
                                <textarea style="margin-top: 5px; width: 400px; height: 180px;resize:none;"  name="detail"><?php echo $list['detail'];?></textarea>
                           </div>
                            <br>
                            <div class="js_s_h_info_house" style="margin-left:80px">
                                <div class="add_pic_house_box clearfix">
                                    <label>
                                        <b class="red">*</b>封面图:
                                        <div class="addBtn radius5">
                                            <span id="spanButtonPlaceholder2"></span>
                                        </div>
                                        <script type="text/javascript">
                                            var swfu2;
                                            $(function() {
                                            swfu2 = new SWFUpload({
                                                file_post_name: "file",
                                                upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                file_size_limit : "5 MB",
                                                file_types : "*.jpg;*.png",
                                                file_types_description : "JPG Images",
                                                file_upload_limit : "0",
                                                file_queue_limit : "5",
                                                custom_settings : {
                                                    upload_target : "jsPicPreviewBoxM2",
                                                    upload_limit  : 1,
                                                    upload_nail	  : "thumbnails2",
                                                    upload_infotype : 2
                                                },
                                                swfupload_loaded_handler : swfUploadLoaded,
                                                file_queue_error_handler : fileQueueError,
                                                file_dialog_start_handler : fileDialogStart,
                                                file_dialog_complete_handler : fileDialogComplete,
                                                upload_progress_handler : uploadProgress,
                                                upload_error_handler : uploadError,
                                                upload_success_handler : uploadSuccessNew,
                                                upload_complete_handler : uploadComplete,

                                                button_image_url : "",
                                                button_placeholder_id : "spanButtonPlaceholder2",
                                                button_width: 88,
                                                button_height: 28,
                                                button_cursor: SWFUpload.CURSOR.HAND,
                                                button_text:"浏览",
                                                flash_url : "<?=MLS_SOURCE_URL ?>/common/third/swfupload.swf"
                                            });

                                            });
                                        </script>
                                        <div id="jsPicPreviewBoxM2" style="display:none" ></div>
                                        <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails2">
                                        <?php if(!empty($list['face_img'])){
                                            foreach($list['face_img'] as $key=>$val){?>
                                            <div class="add_item_pic">
                                                <div class="pic">
                                                    <img src="<?php echo $val;?>" width="130px" height="100px">
                                                    <input type="hidden" name="p_filename2[]" value="<?php echo $val;?>" class="hidden_1">
                                                </div>
                                                <div class="fun">
                                                    <a onclick="prevOrNextFun(this)" class="label_pic" href="javascript:void(0);">设为首图</a>
                                                    <a onclick="fun_hide_p(this);swfu2.setButtonDisabled(false);thumbnails2.children().remove();" href="javascript:void(0);" class="del_pic">删除</a>
                                                    <a onclick="prevOrNextFun(this)" href="javascript:void(0);" class="del_left">左移</a>
                                                    <a onclick="prevOrNextFun(this)" href="javascript:void(0);" class="del_right">右移</a>
                                                    <p class="fun-bg">背景</p>
                                                </div>
                                                <span class="first-img"></span>
                                            </div>
                                        <?php }}?>
                                        </div>
                                    </label>
                                </div>
                                <div class="add_pic_house_box add_pic_house_box2 clearfix">
                                    <label>
                                        <b class="red">*</b>户型图(支持多张图片上传):
                                        <div class="addBtn radius5">
                                            <span id="spanButtonPlaceholder1"></span>
                                        </div>
                                        <script type="text/javascript">
                                            var swfu1;
                                            $(function() {
                                            swfu1 = new SWFUpload({
                                                file_post_name: "file",
                                                upload_url: "<?=JAVA_FILE_UPLOAD_URL?>",
                                                file_size_limit : "5 MB",
                                                file_types : "*.jpg;*.png",
                                                file_types_description : "JPG Images",
                                                file_upload_limit : "0",
                                                file_queue_limit : "5",
                                                custom_settings : {
                                                    upload_target : "jsPicPreviewBoxM1",
                                                    upload_limit  : 3,
                                                    upload_nail	  : "thumbnails1",
                                                    upload_infotype :  1
                                                },
                                                swfupload_loaded_handler : swfUploadLoaded,
                                                file_queue_error_handler : fileQueueError,
                                                file_dialog_start_handler : fileDialogStart,
                                                file_dialog_complete_handler : fileDialogComplete,
                                                upload_progress_handler : uploadProgress,
                                                upload_error_handler : uploadError,
                                                upload_success_handler : uploadSuccessNew,
                                                upload_complete_handler : uploadComplete,

                                                button_image_url : "",
                                                button_placeholder_id : "spanButtonPlaceholder1",
                                                button_width: 88,
                                                button_height: 28,
                                                button_cursor: SWFUpload.CURSOR.HAND,
                                                button_text:"浏览",
                                                flash_url : "<?=MLS_SOURCE_URL ?>/common/third/swfupload.swf"
                                            });

                                            });
                                        </script>
                                        <div id="jsPicPreviewBoxM1" style="display:none" ></div>
                                        <div class="picPreviewBoxM clearfix ui-sortable" id="thumbnails1">
                                        <?php if(!empty($list['hx_imgurl'])){
                                            foreach($list['hx_imgurl'] as $key=>$val){?>
                                            <div class="add_item_pic">
                                                <div class="pic">
                                                    <img src="<?php echo $val;?>" width="130px" height="100px">
                                                    <input type="hidden" name="p_filename1[]" value="<?php echo $val;?>" class="hidden_1">
                                                </div>
                                                <div class="fun">
                                                    <a onclick="prevOrNextFun(this)" class="label_pic" href="javascript:void(0);">设为首图</a>
                                                    <a onclick="fun_hide_p(this);swfu1.setButtonDisabled(false);thumbnails1.children().remove();" href="javascript:void(0);" class="del_pic">删除</a>
                                                    <a onclick="prevOrNextFun(this)" href="javascript:void(0);" class="del_left">左移</a>
                                                    <a onclick="prevOrNextFun(this)" href="javascript:void(0);" class="del_right">右移</a>
                                                    <p class="fun-bg">背景</p>
                                                </div>
                                                <span class="first-img"></span>
                                            </div>
                                            <?php }}?>

                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
		         <div style="height:61px;"></div>
                    </div>
                    <div id="dataTables-example_length" class="dataTables_length">
                        <input type="submit" value="保&nbsp;&nbsp;&nbsp;存" class="btn btn-primary" id="save">
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php } else if (0 === $setinfo) { ?>
            <div>设置失败</div>
        <?php } else{ ?>
            <div>设置成功</div>
        <?php } ?>
    </div>
</div>
<?php if ($setinfo != "") { ?>
    <script>
        $(function() {
            setTimeout(function() {
                window.location.href = "<?php echo MLS_ADMIN_URL;?>/new_house/index/";
            }, 1000);
        });
    </script>
<?php } ?>
<?php
if ( isset($js) && $js != '')
{
    echo $js;
}

if ( isset($css) && $css != '')
{
    echo $css;
}
?>
