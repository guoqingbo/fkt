<script type="text/javascript">
$(function() {
	$("#photofile").live("change",function(){
		var file = $(this).val();
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_photo").submit();
			}
			else
			{
				return false;
			}
		}
	});
});
</script>
<iframe name="filepost_iframe" id="filepost_iframe" style="width:1px;height:1px;display:none;"></iframe>
<body class="personal_center_scroll">
<div class="tab_box" id="js_tab_box">
    <?php if(isset($user_menu) && $user_menu != ''){ echo $user_menu;}?>
</div>
<div class="personal_box">
    <div class="personal_fg">
        <div class="hd">
            <h3 class="h3">基本资料</h3>
        </div>
        <div class="mod clearfix">
            <div class="froms_p">
                <div class="item clearfix">
                    <label class="label"><span class="text">员工姓名：</span>
                        <input id="truename" class="input_text" type="text" value="<?=$truename ?>">
                    </label>
                    <label class="label"><span class="text">出生日期：</span>
                        <input id="birthday" class="input_text" type="text" value="<?=$birthday ?>">
                    </label>
                    <label class="label"><span class="text">服务区属：</span>
                        <input id="" class="input_text input_readonly" type="text" readonly value="<?=$dist_name.'-'.$street_name ?>">
                    </label>
                    <label class="label"><span class="text">手机号码：</span>
                        <input id="phone" class="input_text input_readonly" type="text" readonly value="<?=$phone ?>">
                    </label>
                </div>
                <div class="item clearfix">
                    <label class="label"><span class="text">身份证：</span>
                        <input id="idno" class="input_text" type="text" value="<?=$idno ?>">
                    </label>
                    <label class="label"><span class="text">所属公司：</span>
                        <input id="company_name" class="input_text input_readonly" type="text" readonly value="<?=$company['name'] ?>">
                    </label>
                    <label class="label"><span class="text">业务QQ：</span>
                        <input id="qq" class="input_text" type="text" value="<?=$qq ?>">
                    </label>
                    <label class="label"><span class="text">公司门店：</span>
                        <input id="agency_name" class="input_text input_readonly" type="text" readonly value="<?=$agency_name ?>">
                    </label>
                </div>
                <button type="button" class="btn" onclick="base_save()">保存资料</button>
            </div>

            <div class="personal_photo personal_photo_t personal_photo_s">
                <div class="inner">
                <form name="fileform_photo" id="fileform_photo" action="/my_info/upload_photo" enctype="multipart/form-data" target="filepost_iframe" method="post">
                    <div id="photo_replace">
                    <?php if(empty($photo)){ ?>
                        <p class="iconfont">&#xe608;</p>
                        <p class="tex">点击上传头像</p>
                        <input name="photofile" type="file" class="file_input" id="photofile">
                    <?php }else{?>
                        <img src="<?=$photo ?>" width="100" height="100">
                        <div class="del_amend">
                            <a href="javascript:void(0)"><input name="photofile" type="file" class="file_input" id="photofile">修改</a>
                            <a href="javascript:void(0)" onclick="remove_photofile()">删除</a>
                        </div>
                   <?php }?>
                    </div>
                    <input type="hidden" name="action" value="photofile" />
                    <input type="hidden" name="div_id" value="photo_replace" />
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function changePic(fileurl,div_id,filename){
    	$("#"+div_id).html("<img src='"+fileurl+"' width='100' height='100'><div class='del_amend'><a href='javascript:void(0)'><input name='"+filename+"' type='file' class='file_input' id='"+filename+"'>修改</a><a href='javascript:void(0)' onclick='remove_"+filename+"()'>删除</a></div>");
    }

    function remove_photofile()
    {
    	var photo_addHtml = "<p class='iconfont'>&#xe608;</p>";
    	photo_addHtml += "<p class='tex'>点击上传头像</p>";
    	photo_addHtml += "<input type='file' name='photofile' class='file_input' id='photofile'>";
    	$("#photo_replace").html(photo_addHtml);
    }

    function base_save(){
        var photo = "";
        if($("#photo_replace").find("img").length){
     	   photo = $("#photo_replace img").attr("src");
      	}
    	var truename = $("#truename").val();
    	var birthday = $("#birthday").val();
    	/*var phone = $("#phone").val(); */
    	var idno = $("#idno").val();
    	var qq = $("#qq").val();

    	if(!truename){
 		   alert("姓名不能为空");
 		   return false;
    	}

    	var data = {submit_flag:"modify",truename:truename,birthday:birthday,idno:idno,qq:qq,photo:photo};
    	$.ajax({
    		type: "POST",
    		url: "/my_info/base_modify",
    		data:data,
    		cache:false,
    		error:function(){
    			alert("系统错误");
    			return false;
    		},
    		success: function(data){
    			alert(data);
    			window.location.href = "/my_info/";
    		}
    	});

    }
</script>
