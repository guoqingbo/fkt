<?php require APPPATH . 'views/header.php'; ?>
<link href="<?php echo MLS_SOURCE_URL;?>/mls/css/v1.0/select2.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL;?>/mls/js/v1.0/select2.js"></script>
<script type="text/javascript">

function get_agency(companyId)
{
    $.ajax({
        type: 'get',
        url : '<?php echo MLS_ADMIN_URL; ?>/agency/get_agency_ajax/'+companyId,
        dataType:'json',
        success: function(msg){
            var str = '';
            if(msg===''){
                str = '<option value="">请选择</option>';
            }else{
                str = '<option value="">请选择</option>';
                for(var i=0;i<msg.length;i++){
                    str +='<option value="'+msg[i].id+'">'+msg[i].name+'</option>';
                }
            }
            $('#agency_id').empty();
            $('#agency_id').append(str);
        }
    });
}
$(function() {

  var regExp_pic = /(.jpg|.JPG|.png|.PNG)$/;
	$("#agency_id").select2();
	$("#photofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;
			if (patrn.exec(file))
			{
				$("#fileform_photo").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	});

	$("#headfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;
			if (patrn.exec(file))
			{
				$("#fileform_head").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	});
	$("#idnofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;
			if (patrn.exec(file))
			{
				$("#fileform_idno").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	});
	$("#cardfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;
			if (patrn.exec(file))
			{
				$("#fileform_card").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	});
	/*$("#agencyfile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=regExp_pic;
			if (patrn.exec(file))
			{
				$("#fileform_agency").submit();
			}
			else
			{
				alert("图片格式不正确");
				return false;
			}
		}
	}); */

    $("#company_name").autocomplete({
        source: function( request, response ) {
            var term = request.term;
            $.ajax({
                url: "/company/get_company_by_kw/",
                type: "GET",
                dataType: "json",
                data: {
                    keyword: term
                },
                success: function(data) {
                    //判断返回数据是否为空，不为空返回数据。
                    if( data[0]['id'] != '0'){
                        response(data);
                    }else{
                        response(data);
                    }
                }
            });
        },
        minLength: 1,
        removeinput: 0,
        select: function(event,ui) {
            if(ui.item.id > 0){
                var company_name = ui.item.label;
                var id = ui.item.id;
                //操作
                $("#company_id").val(id);
                $("#company_name").val(company_name);
                get_agency(id);
                removeinput = 2;
            }else{
                removeinput = 1;
            }
        },
        close: function(event) {
            if(typeof(removeinput)=='undefined' || removeinput == 1){
                $("#company_id").val("");
                $("#company_name").val("");
            }
        }
    });

    $("input[name='package_id']").bind("click",function(){
        if ($(this).val() == 1)
        {
            $('#area_id').css('display', 'none');
        }
        else
        {
            $('#area_id').css('display', 'block');
        }
    });
});


function changePic(fileurl,div_id){
    $("#"+div_id).attr("src",fileurl);
}

function submit(submit_flag,id){
	var truename = $("input[name='truename']").val();
	var birthday = $("input[name='birthday']").val();
	var phone = $("input[name='phone']").val();
	var password = $("input[name='password']").val();
	var company_id = $("#company_id").val();
	var agency_id = $("#agency_id").val();
	var qq = $("input[name='qq']").val();
	var expiretime = $("input[name='expiretime']").val();
	var idno = $("input[name='idno']").val();
	var idcard = $("input[name='idcard']").val();
	var email = $("input[name='email']").val();
    var register_id = $("input[name='register_id']").val();

	var photopic = $("#photopic_replace").attr("src");
    if(photopic.substring(photopic.lastIndexOf("/")+1,photopic.lastIndexOf(".")) == "sfrz_bg"){
    	photopic = "";
    }
	/*var headpic = $("#headpic_replace").attr("src");
    if(headpic.substring(headpic.lastIndexOf("/")+1,headpic.lastIndexOf(".")) == "sfrz_bg"){
    	headpic = "";
    }*/
	var idnopic = $("#idnopic_replace").attr("src");
	if(idnopic.substring(idnopic.lastIndexOf("/")+1,idnopic.lastIndexOf(".")) == "sfrz_bg"){
		idnopic = "";
    }
	var cardpic = $("#cardpic_replace").attr("src");
    if(cardpic.substring(cardpic.lastIndexOf("/")+1,cardpic.lastIndexOf(".")) == "sfrz_bg"){
    	cardpic = "";
    }
	/*var agencypic = $("#agencypic_replace").attr("src");
	if(agencypic.substring(agencypic.lastIndexOf("/")+1,agencypic.lastIndexOf(".")) == "sfrz_bg"){
		agencypic = "";
    }*/

	var package_id = $('input[name="package_id"]:checked').val();
	var status = $('input[name="status"]:checked').val();
    var group_id = $('input[name="group_id"]:checked').val();
    var area_id = $('input[name="area_id"]:checked').val();
    var is_reset_password = $('input[name="is_reset_password"]:checked').val();
    var master_id = $('#master_id').val();
	var data = {submit_flag:submit_flag,truename:truename,phone:phone,birthday:birthday,password:password,company_id:company_id,
			agency_id:agency_id,qq:qq,expiretime:expiretime,idno:idno,email:email,photopic:photopic,idnopic:idnopic,cardpic:cardpic,idcard:idcard,package_id:package_id,status:status,group_id:group_id,area_id:area_id,register_id:register_id,master_id:master_id,is_reset_password:is_reset_password};
	if(register_id){
        $.ajax({
        	type: "POST",
            url: "/broker_info/modify/"+id+'/'+register_id,
        	data:data,
        	cache:false,
        	error:function(){
        		alert("系统错误");
        		return false;
        	},
        	success: function(data){
        		alert(data);
        	}
        });
    } else {
        $.ajax({
            type: "POST",
            url: "/broker_info/modify/"+id,
            data:data,
            cache:false,
            error:function(){
                alert("系统错误");
                return false;
            },
            success: function(data){
                alert(data);
            }
        });
    }

}
</script>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<div id="wrapper">
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$title?></h1>
            </div>
        </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="table-responsive">
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
                                                员工姓名<font color="red">*</font>&nbsp;&nbsp;<input type="search" name="truename" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['truename']?>">
                                            </label>
                                            <label>
                                               &nbsp&nbsp出生日期&nbsp;&nbsp;&nbsp;<input type="search" name="birthday" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['birthday']?>">
                                            </label>
                                        </div>
                                        <!--                                        <img style="position:absolute; top:20px; right: 33px;" width="125" height="125" src="-->
                                        <?php //echo $code_img_url;?><!--">-->
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                手机号码<font color="red">*</font>&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['phone']?>" readonly>
												<input type="hidden" name="phone" value="<?=$broker_info['phone']?>">
                                            </label>
                                            <label>
                                               &nbsp&nbsp;是否重置密码&nbsp;&nbsp;&nbsp;
                                               <!--
                                               <input type="password" name="password" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker['password']?>">
                                               -->
                                               <label>
                                                    <input type="radio" name="is_reset_password" value="1" />是
                                                </label>
                                               &nbsp&nbsp
                                               <label>
                                                    <input type="radio" name="is_reset_password" value="2" checked='checked' /> 否
                                                </label>
                                               （重置密码为：123456）
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                               所属公司&nbsp;
                                                <input name="company_name" id="company_name" value="<?=$broker_info['company_name']?>" class="input_text input_text_r w150 form-control input-sm" type="text" placeholder="输入汉字筛选" style="height:30px; line-height: 30px;" >
                                                <input type="hidden" name="company_id" id="company_id" value="<?=$broker_info['company_id']?>">
                                            </label>
                                            <label>&nbsp;公司门店&nbsp;&nbsp;
                                                <select name="agency_id"  id="agency_id" aria-controls="dataTables-example">
                                                    <option value="0">请选择</option>
                                                    <?php foreach ($broker_info['agencys'] as $k => $v) { ?>
                                                        <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$broker_info['agency_id']){echo 'selected="selected"';}?>><?php echo $v['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                        </div>
                                        <?php if(is_full_array($register_info)){ ?>
                                        <input type="hidden" name="register_id" id="register_id" value="<?php echo $register_info['id']; ?>">
                                        <div>
                                            该经纪人注册填写资料为：<b><?php echo $register_info['corpname']; ?></b>，门店为：<b><?php echo $register_info['storename']; ?></b>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                               业务QQ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="qq" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['qq']?>">
                                            </label>
                                            <label>
                                               &nbsp&nbsp注册时间<font color="red">*</font>&nbsp;&nbsp;<input type="text" id="end_time" name="end_time" readonly class="form-control input-sm" value="<?php echo date('Y-m-d H:i:s', $broker_info['register_time'])?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                  到期时间&nbsp;&nbsp;<input type="text" id="expiretime" name="expiretime" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $broker_info['expiretime'] == 0 ? '' : date('Y-m-d', $broker_info['expiretime'])?>" onclick="WdatePicker()">
                                            </label>
                                            <label>
                                               &nbsp;&nbsp;身份证&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="idno" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['idno']?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                               电子邮件&nbsp;&nbsp;&nbsp;<input type="search" name="email" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$broker_info['email']?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>头像</label>
                                            <label>
                                            <?php
                                            if($broker_info['photo']){
												$photo = $broker_info['photo'];
                                                $photo_big = changepic($broker_info['photo']);
                                            }else{
                                                $photo = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/sfrz_bg.gif';
                                            }
                                            ?>
                                            <a href="<?=$photo_big ?>" target="_blank"><img id="photopic_replace" src="<?=$photo ?>" width="130" height="170"/></a>
                                            </label>
                                            <!--
                                            <label>
                                                <form name="fileform_photo" id="fileform_photo" action="/broker_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                							        <input name="photofile" id="photofile" type="file" class="file_input mt10">
                                                    <input type='hidden' name='action' value='photofile' />
                                                    <input type='hidden' name='div_id' value='photopic_replace' />
                                                </form>
                                            </label>  -->
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>身份认证</label>
                                            <!--
                                            <label>
                                                <form name="fileform_head" id="fileform_head" action="/broker_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                							        <input name="headfile" id="headfile" type="file" class="file_input mt10">
                                                    <input type='hidden' name='action' value='headfile' />
                                                    <input type='hidden' name='div_id' value='headpic_replace' />
                                                </form>
                                            </label>-->
                                            <label>
											<?php
                                            if($idno_photo){
												$idno_photo_big = changepic($idno_photo);
                                            }else{
                                                $idno_photo = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/sfrz_bg.gif';
                                            }
                                            ?>
                                            <a href="<?=$idno_photo_big ?>" target="_blank"><img id="idnopic_replace" src="<?=$idno_photo ?>" width="242" height="152"/></a>
                                            </label>
                                            <!--
                                            <label>
                                                <form name="fileform_idno" id="fileform_idno" action="/broker_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                							        <input name="idnofile" id="idnofile" type="file" class="file_input mt10">
                                                    <input type='hidden' name='action' value='idnofile' />
                                                    <input type='hidden' name='div_id' value='idnopic_replace' />
                                                </form>
                                            </label>-->
											<label>
                                               认证身份证&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="idcard" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$idcard?>" disabled>
                                            </label>
                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>资质认证</label>
                                            <label>
                                            <?php
                                            if($auth_ident_status!=2){
                                                $card_photo_big = $card_photo = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/sfrz_bg.gif';
                                                $agency_photo = MLS_SOURCE_URL . '/mls/images/v1.0/grzx/sfrz_bg.gif';
                                            }
											else
											{
												$card_photo_big = changepic($card_photo);
											}
                                            ?>
                                            <a href="<?=$card_photo_big ?>" target="_blank"><img id="cardpic_replace" src="<?=$card_photo ?>" width="242" height="152"/></a>
                                            </label>
                                            <!--
                                            <label>
                                                <form name="fileform_card" id="fileform_card" action="/broker_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                							        <input name="cardfile" id="cardfile" type="file" class="file_input mt10">
                                                    <input type='hidden' name='action' value='cardfile' />
                                                    <input type='hidden' name='div_id' value='cardpic_replace' />
                                                </form>
                                            </label>-->

                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                身份组&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php foreach($where_config['package'] as $k => $v) { ?>
                                                <label>
                                                    <input type="radio" name="package_id" value="<?=$k?>" <?php if($k==$broker_info['package_id']){?> checked='checked' <?php }?>/> <?=$v?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
									 <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                角色权限组&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php if(is_full_array($permission_group)){
                                                        foreach($permission_group as $k => $v) {?>
                                                <label style="color:#b0b0b0">
                                                    <input
                                                            type="radio"
                                                            name="user_permission_groupid"
                                                            value="<?php echo $v['id']; ?>"
                                                        <?php if ($broker_role_level == $v['level']) { ?>
                                                            checked='checked'
                                                        <?php } ?> disabled/>
                                                    <?php echo $v['name']; ?>
                                                </label>
                                            <?php }} ?>
                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                用户组&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php foreach($where_config['group'] as $k => $v) { ?>
                                                <label style="color:#b0b0b0">
                                                    <input type="radio" name="group_id" value="<?=$k?>" <?php if($k==$broker_info['group_id']){?> checked='checked' <?php }?>  disabled /> <?=$v?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                帐号有效性&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php foreach($where_config['status'] as $k => $v) { ?>
                                                <label>
                                                    <input type="radio" name="status" value="<?=$k?>" <?php if($k==$broker_info['status']){?> checked='checked' <?php }?> > <?=$v?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%;display: <?php echo $broker_info['package_id'] == 2 ? 'block;' : 'none;'?>" id="area_id">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                范围&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php foreach($where_config['area'] as $k => $v) { ?>
                                                <label>
                                                    <input type="radio" name="area_id" value="<?=$k?>" <?php if($k==$broker_info['area_id']){?> checked='checked' <?php }?> > <?=$v?>
                                                </label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                     <div class="col-sm-6" style="width:100%;">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                客户经理&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <label>
                                                <select name="master_id"  id="master_id" aria-controls="dataTables-example" class="form-control input-sm">
                                                    <option value="0">请选择</option>
                                                   <?php foreach($masters as $v) { ?>
                                                        <option value="<?php echo $v['uid'] ?>"<?php if($v['uid']==$broker_info['master_id']){echo 'selected="selected"';}?>><?php echo $v['truename'] ?></option>
                                                    <?php } ?>
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
                                            <?php if($type!='1'){ ?>
                                            <a class="btn btn-primary" href="#" onclick="submit('modify',<?=$broker_info['id']?>)">提交</a>
                                            <?php } ?>
                                            <?php if($register_id) { ?>
                                                <a class="btn btn-primary" href="/register_broker_info/index">返回</a>
                                            <?php } else {?>
                                                <a class="btn btn-primary" href="/broker_info/index">返回</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
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
<?php require APPPATH . 'views/footer.php'; ?>
<link href="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/skin/WdatePicker.css" rel="stylesheet">
<script src="<?php echo MLS_SOURCE_URL; ?>/common/third/My97DatePicker/WdatePicker.js"></script>
