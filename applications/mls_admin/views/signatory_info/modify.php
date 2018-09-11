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
            $('#department_id').empty();
            $('#department_id').append(str);
        }
    });
}
$(function() {

  var regExp_pic = /(.jpg|.JPG|.png|.PNG)$/;
	$("#department_id").select2();
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
	var department_id = $("#department_id").val();
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
			department_id:department_id,qq:qq,expiretime:expiretime,idno:idno,email:email,photopic:photopic,idnopic:idnopic,cardpic:cardpic,idcard:idcard,package_id:package_id,status:status,group_id:group_id,area_id:area_id,register_id:register_id,master_id:master_id,is_reset_password:is_reset_password};
	if(register_id){

        $.ajax({
        	type: "POST",
            url: "/signatory_info/modify/"+id+'/'+register_id,
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
	    console.log(123)
        $.ajax({
            type: "POST",
            url: "/signatory_info/modify/"+id,
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
                                                员工姓名<font color="red">*</font>&nbsp;&nbsp;<input type="search" name="truename" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['truename']?>">
                                            </label>
                                            <label>
                                               &nbsp&nbsp出生日期&nbsp;&nbsp;&nbsp;<input type="search" name="birthday" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['birthday']?>">
                                            </label>
                                        </div>
                                        <!--                                        <img style="position:absolute; top:20px; right: 33px;" width="125" height="125" src="-->
                                        <?php //echo $code_img_url;?><!--">-->
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                手机号码<font color="red">*</font>&nbsp;&nbsp;<input type="search" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['phone']?>" readonly>
												<input type="hidden" name="phone" value="<?=$signatory_info['phone']?>">
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
                                                <input name="company_name" id="company_name" value="<?=$signatory_info['company_name']?>" class="input_text input_text_r w150 form-control input-sm" type="text" placeholder="输入汉字筛选" style="height:30px; line-height: 30px;" >
                                                <input type="hidden" name="company_id" id="company_id" value="<?=$signatory_info['company_id']?>">
                                            </label>
                                            <label>&nbsp;公司部门&nbsp;&nbsp;
                                                <select name="department_id"  id="department_id" aria-controls="dataTables-example">
                                                    <option value="0">请选择</option>
                                                    <?php foreach ($signatory_info['departments'] as $k => $v) { ?>
                                                        <option value="<?php echo $v['id'] ?>"<?php if($v['id']==$signatory_info['department_id']){echo 'selected="selected"';}?>><?php echo $v['name'] ?></option>
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
                                               业务QQ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="qq" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['qq']?>">
                                            </label>
                                            <label>
                                               &nbsp&nbsp注册时间<font color="red">*</font>&nbsp;&nbsp;<input type="text" id="end_time" name="end_time" readonly class="form-control input-sm" value="<?php echo date('Y-m-d H:i:s', $signatory_info['register_time'])?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                                  到期时间&nbsp;&nbsp;<input type="text" id="expiretime" name="expiretime" class="form-control input-sm" aria-controls="dataTables-example" value="<?php echo $signatory_info['expiretime'] == 0 ? '' : date('Y-m-d', $signatory_info['expiretime'])?>" onclick="WdatePicker()">
                                            </label>
                                            <label>
                                               &nbsp;&nbsp;身份证&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="search" name="idno" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['idno']?>">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" style="width:100%">
                                        <div class="dataTables_length" id="dataTables-example_length">
                                            <label>
                                               电子邮件&nbsp;&nbsp;&nbsp;<input type="search" name="email" class="form-control input-sm" aria-controls="dataTables-example" value="<?=$signatory_info['email']?>">
                                            </label>
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
                                                帐号有效性&nbsp;&nbsp;&nbsp;&nbsp;
                                            </label>
                                            <?php foreach($where_config['status'] as $k => $v) { ?>
                                                <label>
                                                    <input type="radio" name="status" value="<?=$k?>" <?php if($k==$signatory_info['status']){?> checked='checked' <?php }?> > <?=$v?>
                                                </label>
                                            <?php } ?>
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
                                            <a class="btn btn-primary" href="#" onclick="submit('modify',<?=$signatory_info['id']?>)">提交</a>
                                            <?php } ?>
                                            <?php if($register_id) { ?>
                                                <a class="btn btn-primary" href="/register_signatory_info/index">返回</a>
                                            <?php } else {?>
                                                <a class="btn btn-primary" href="/signatory_info/index">返回</a>
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
