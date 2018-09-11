<!--我要举报-->
<div class="pop_box_g report_box pop_box_g_border_none" id="js_woyaojubao" style="display:block">
	<div class="hd">
        <div class="title">我要举报</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>

    <div class="report_box">
    	<div class="report_tips">
    		为了共同打造真实可靠的合作平台，举报经核实后将奖励您一定的积分。
    	</div>
    	<form name="fileform_head" id="fileform_head" action="<?php echo MLS_URL;?>/cooperate/add_report/" enctype="multipart/form-data" target="filepost_iframe" method="post">
    		<table class="table report_table">
    			<tr class="retrbg">
    				<td class="retdname" >举报类型：</td>
    				<td>
    					<select name="report_type" id="report_obj" name="report_type">
							<option value="3">不按协议履行合同</option>
							<option value="4">其他</option>
    					</select>
    				</td>
					<input type="hidden" value="<?php echo $ct_id ?>" name="ct_id"/>

					<input type="hidden"  name="photo_name"/>
					<input type="hidden" value="2" name="cooperate_type"/>
					<input type="hidden" value="1" name="cooperate_style"/>
    			</tr>
    			<tr class="retrbg">
    				<td class="retdname">举报原因：</td>
    				<td>
    					<textarea name="report_text" id="text_uid" placeholder="请详细说明举报理由"></textarea>
    				</td>
    			</tr>
       <tr class="retrbg">
    				<td class="retdname">上传证据：</td>
    				<td>

					<input name="headfile" type="file" class="file_input" id="headfile">
    				</td>
					<input type="hidden" name="action" value="headfile" />
			        <input type="hidden" name="div_id" value="headpic_replace" />
    		</tr>

    		</table>
    		<input  type="submit" class="report_btn" value="举报" id="repot" onclick="getname()" />
    	</form>
    </div>
</div>
<script>
$(function() {
	$("#headfile").live("change",function(){
		var file = $(this).val();
		alert(file);
		if(file != "")
		{
			var patrn=/(.jpg|.JPG|.bmp|.BMP|.png|.PNG)$/;
			if (patrn.exec(file))
			{
				$("#fileform_head").submit();
			}
			else
			{
				alert("图片格式不正确");
				$("#head_red").css("color","#F00");
				return false;
			}
		}
	});

</script>
<script>
function getname(){
	var a=$("input[name='headfile']").val();
	var pos1 = a.lastIndexOf("\\");
    var pos2=a.substring(pos1+1);
     $("input[name='photo_name']").val(pos2);


}
</script>
