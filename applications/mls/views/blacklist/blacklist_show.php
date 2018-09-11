<body >
<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>

	<div class="search_box clearfix" id="js_search_box_02">
		<form name="search_form" id="search_form" method="post" action="">

			<div class="fr"><a href="javascript:void(0)" class="btn-lan" onclick="reset_add()"><span>新增</span></a></div>
			<div class="fg_box">
				<p class="fg fg_tex">类别：</p>

				<div class="fg" style="*padding-top:10px;">
					<select class="select w90" name="cate" id="cate">
						<option value="0" <?php echo($cate == 0)?selected:""?>>不限</option>
						<option value="1" <?php echo($cate == 1)?selected:""?>>恶意竞争</option>
						<option value="2" <?php echo($cate == 2)?selected:""?>>广告推销</option>
						<option value="3" <?php echo($cate == 3)?selected:""?>>无效客户</option>
						<option value="4" <?php echo($cate == 4)?selected:""?>>私下成交</option>
					</select>
				</div>
			</div>

			<div class="fg_box">
				<p class="fg fg_tex">姓名： </p>

				<div class="fg">
					<input type="text" name="bname" id="bname"  class="input w110" value="<?php echo $bname; ?>">
				</div>
			</div>
			<div class="fg_box">
				<p class="fg fg_tex"> 电话：</p>

				<div class="fg">
					<input type="text" name="tel" id="tel"  class="input w110" value="<?php echo $tel; ?>">
				</div>
			</div>

			<div class="fg_box">
				<div class="fg"> <a href="javascript:void(0)" class="btn" onclick="$('#search_form').submit();return false;"><span class="btn_inner">搜索</span></a> </div>
				<div class="fg"> <a href="/blacklist/index/" class="reset">重置</a> </div>
			</div>
			<div class="get_page" style="display:none">
				<?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
			</div>
		</form>
	</div>

<!--前台显示页面-->
<div class="table_all report-form-wrap">

	<div class="title shop_title" id="js_title">
        <table class="table">
            <tr>
                <td class="c11">
                    <div class="info">类别</div>
                </td>
                <td class="c11">
                    <div class="info">姓名</div>
                </td>
                <td class="c12">
                    <div class="info">电话</div>
                </td>
                <td class="c15">
                    <div class="info">备注</div>
                </td>
                <td class="c11">
                    <div class="info">登记分店</div>
                </td>
                <td class="c11">
                    <div class="info">登记人</div>
                </td>
                <td class="c15">
                    <div class="info">登记时间</div>
                </td>
                <td>
                    <div class="info">操作</div>
                </td>
            </tr>
        </table>
    </div>

	<div class="inner shop_inner" id="js_inner">
        <table class="table">

			<?php if($list){
                    foreach($list as $key=>$val) { ?>
                        <tr <?php if($key % 2 == 1){ ?>class="bg" <?php }?> id="tr<?php echo $val['id'];?>">
                            <td class="c11" id="show_cate">
							<?php
								switch($val['cate']){
									case "0":
										echo "不限";
									break;
									case "1":
										echo "恶意竞争";
									break;
									case "2":
										echo "广告推销";
									break;
									case "3":
										echo "垃圾客户";
									break;
									case "4":
										echo "私下成交";
									break;
								}
							?>
							</td>
                            <td class="c11" id="show_bname"><?php echo $val['bname'];?></td>
                            <td class="c12" id="show_tel"><?php echo $val['tel'];?></td>
                            <td class="c15" id="show_remark"><?php echo $val['remark'];?></td>
                            <td class="c11"><?php echo $val['store_name'];?></td>
                            <td class="c11"><?php echo $val['truename'];?></td>
                            <td class="c15"><strong class="f60"><?php echo date("Y-m-d H:i:s",$val['addtime']);?></strong></td>
							<td>
								<div class="info"><a href="javascript:void(0)" onClick="modify_detail(<?php echo $val['id'];?>)">查看详情</a><span style="margin:0 13px;color:#b2b2b2;">|</span><a href="javascript:void(0)" onClick="modify_del_pop(<?php echo $val['id'];?>)">删除</a>
								</div>
							</td>
                        </tr>
                    <?php }
            } else { ?>
                <tr><td><span class="no-data-tip">抱歉，没有找到符合条件的信息</span></td></tr>
            <?php } ?>
        </table>
    </div>
</div>
<div class="fun_btn clearfix" id="js_fun_btn">
    <div class="get_page">
        <?php if(isset($page_list) && $page_list != ''){ echo $page_list;}?>
    </div>
</div>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading"><!--遮罩 loading-->
<!--新增页面-->
<div class="pop_box_g" id="js_modify_add" style="width:450px; height:301px; display:none;">
    <div class="hd">
        <div class="title">新增黑名单</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down ">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl"><span class="red_star">*</span>类别：</div>

                <select class="qushu fl" id="add_cate">
                    <option selected value="0">不限</option>
					<option value="1">恶意竞争</option>
					<option value="2">广告推销</option>
					<option value="3">垃圾客户</option>
					<option value="4">私下成交</option>
                </select>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
				<div class="name fl"><span class="red_star">*</span>姓名：</div>
                <input type="text" class="loupan fl" id="add_bname"  name="add_bname">
				<span class="add_bname"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl"><span class="red_star">*</span>电话：</div>
				<input type="text" class="loupan fl" id="add_tel"  name="add_tel">
				<span class="add_tel"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <span class="name fl">备注：</span>
                <textarea class="address remind fl" name="remark" id="remark" onkeyup="limit_num()"></textarea>
                <div class="address_num fl" id="limited">0/100</div>
            </div>


        </div>

        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button type="button" style="float:left;" class="btn-lv1 btn-left " onclick="modify_add();">确定</button>
            <button type="button" class="btn-hui1 JS_Close">取消</button>
        </div>
    </div>
</div>

<!--删除页面-->

<div class="pop_box_g" id="js_modify_del" style="width:300px; height:auto; background:#fff; display:none;">
	<input type='hidden' id='black_id' name='id' >
    <div class="hd">
        <div class="title">确认删除</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod mod-qf">
		<div class="center">
			 <img id="dialog_do_itp_src" style="margin-right:10px;" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/s_ico.png">
			 <span class="text" >你是否确认删除黑名单</span>
		</div>
		<div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button type="button" style="float:left;" class="btn-lv1 btn-left " onclick="modify_del();">确定</button>
            <button type="button" class="btn-hui1 JS_Close">取消</button>
        </div>
    </div>
</div>

<!--详情页面-->
<div class="pop_box_g" id="js_modify_detail" style="width:380px; height:360px; display:none;">
	<input type='hidden' id='detail_id' name='id' >
    <div class="hd">
        <div class="title">黑名单详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down ">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">类别：</div>
				<span id="detail_cate" style="border:none;margin-left:18px"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">姓名：</div>
				<input type="text" class="loupan fl" id="detail_bname" style="border:none;margin-left:10px" readonly>
                <span id=""></span>

            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">电话：</div>
				<input type="text" class="loupan fl" id="detail_tel" style="border:none;margin-left:10px" readonly>
                <span id=""></span>
            </div>
			<div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">登记分店：</div>
				<input type="text" class="loupan fl" id="detail_store_name" style="border:none;margin-left:10px" readonly>
                <span id=""></span>
            </div>
			<div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">登记人：</div>
				<input type="text" class="loupan fl" id="detail_truename" style="border:none;margin-left:10px" readonly>
                <span id=""></span>
            </div>
			<div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl" style="margin-left:40px">登记时间：</div>
				<input type="text" class="loupan fl" id="detail_addtime" style="border:none;margin-left:10px" readonly>
                <span id=""></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <span class="name fl" style="margin-left:40px">备注：</span>
				<div style="width:180px;height:50px;margin-left:18px;float:left;overflow:auto" id="detail_remark"></div>
            </div>
        </div>

        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button type="button" style="float:left;margin-left:30px" class="btn-lv1 btn-left JS_Close" onClick="modify_edit_pop()">编辑</button>
        </div>
    </div>
</div>


<!--编辑页面-->
<div class="pop_box_g" id="js_modify_edit" style="width:450px; height:301px; display:none;">
	<input type='hidden' id='edit_id' name='id' >
    <div class="hd">
        <div class="title">编辑黑名单</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="create_newb_wrapall paddiing_down ">

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl"><span class="red_star">*</span>类别：</div>

				<select class="qushu fl" id="edit_cate" style="padding-left:6px">
                    <option selected value="0">不限</option>
					<option value="1">恶意竞争</option>
					<option value="2">广告推销</option>
					<option value="3">垃圾客户</option>
					<option value="4">私下成交</option>
                </select>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl"><span class="red_star">*</span>姓名：</div>
                <input type="text" class="loupan fl" id="edit_bname">
				<span class="edit_bname"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <div class="name fl"><span class="red_star">*</span>电话：</div>
                <input type="text" class="loupan fl" id="edit_tel">
				<span class="edit_tel"></span>
            </div>

            <div class="create_newb_wrap create_newblack clearfix">
                <span class="name fl">备注：</span>
                <textarea class="address remind fl" id="edit_remark" onkeyup="limit_num1()" style="padding-left:8px"></textarea>

                <div class="address_num fl" id="limited1">0/100</div>
            </div>


        </div>

        <div style="width:120px; margin:10px auto 0; height:auto; overflow:hidden; zoom:1;">
            <button type="button" style="float:left;" class="btn-lv1 btn-left " onClick="modify_edit()">确定</button>
            <button type="button" class="btn-hui1 JS_Close">取消</button>
        </div>
    </div>
</div>

<!--操作结果提示框-->
<div id="js_pop_do_success" class="pop_box_g pop_see_inform pop_no_q_up" >
    <div class="hd">
        <div class="title">提示</div>
        <div class="close_pop">
            <a href="javascript:void(0);" onclick="sub_form();" title="关闭" class="JS_Close iconfont"></a>
        </div>
    </div>
    <div class="mod">
        <div class="inform_inner">
            <div class="up_inner">
                 <p class="text" id='dialog_do_itp'></p>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
	//var partten = /^[1][3578][0-9]{9}$/;
	//var partten = /^(((\d{3,4}-)?(\d{7,8}-)?\d{3,4}?)|([1][3578][0-9]{9}))$/;
	var partten = /^[0-9\-]{1,18}$/;
	$('#add_bname').change(function(){
		var bname = $('#add_bname').val();
		if(!bname){
			$('.add_bname').html("<font color='red'>请输入正确姓名</font>");
		}else{
			$('.add_bname').html("");
		}
	});
	$('#add_tel').change(function(){
		var tel = $('#add_tel').val();
		if(!partten.test(tel)){
			$('.add_tel').html("<font color='red'>请输入正确的电话号码</font>");
		}else{
			$('.add_tel').html("");
		}
	});
	$('#edit_bname').change(function(){
		var bname = $('#edit_bname').val();
		if(!bname){
			$('.edit_bname').html("<font color='red'>请输入正确姓名</font>");
		}else{
			$('.edit_bname').html("");
		}
	});
	$('#edit_tel').change(function(){
		var tel = $('#edit_tel').val();
		if(!partten.test(tel)){
			$('.edit_tel').html("<font color='red'>请输入正确的电话号码</font>");
		}else{
			$('.edit_tel').html("");
		}
	});
	//添加黑名单弹出框刷新
	function reset_add(){
		$('#add_bname').val("");
		$('#add_tel').val("");
		$('#remark').val("");
		$('#add_cate').val("0");
		$('.add_bname').html("");
		$('.add_tel').html("");
		openWin('js_modify_add');
	}
	//添加黑名单操作
	function modify_add(){
		var bname = $('#add_bname').val();
		var tel = $('#add_tel').val();
		var remark = $('#remark').val();
		var cate = $('#add_cate').val();
		if(!bname){
			$('.add_bname').html("<font color='red'>请输入正确姓名</font>");
			return false;
		}else{
			$('.add_bname').html("");
		}
		if(!partten.test(tel)){
			$('.add_tel').html("<font color='red'>请输入正确的电话号码</font>");
			return false ;
		}else{
			$('.add_tel').html("");
		}
		var data = {
			'bname':bname,
			'tel':tel,
			'remark':remark,
			'cate':cate
		};

		$.ajax({
			type: "POST",
			url: "/blacklist/add",
			data:data,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(return_data){
				//alert(return_data);
				if(1==return_data.status){
					//location = location;
					$('#js_modify_add').hide();
					$('#dialog_do_itp').html('添加成功');
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);

				}else{
					$('#js_modify_add').hide();
					$('#dialog_do_itp').html('添加失败');
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);
				}
			}
		});
	}
	//删除黑名单操作弹出框
	function modify_del_pop(id){
		$.ajax({
			type: "POST",
			url: "/blacklist/del/",
			data: "black_id="+id,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(data.id){
					$('#black_id').val(data['id']);
					openWin('js_modify_del');
				}else{
					openWin('js_modify_del');
				}
			}
		});

	}
	//删除黑名单
	function modify_del(){
		var id = $('#black_id').val();
		$.ajax({
			type: "POST",
			url: "/blacklist/del",
			data: "id="+id,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(1==data.status){
					$('#js_modify_del').hide();
					$('#dialog_do_itp').html('删除成功');
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);
				}else{
					$('#js_modify_del').hide();
					$('#dialog_do_itp').html('删除失败');
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);
				}
			}
		});

	}

	//单条黑名单详情
	function modify_detail(id){
		$.ajax({
			type: "POST",
			url: "/blacklist/detail",
			data: "detail_id="+id,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(data.id){
					$('#detail_id').val(data['id']);
					$('#detail_bname').val(data['bname']);
					$('#detail_tel').val(data['tel']);
					$('#detail_remark').html(data['remark']);
					$('#detail_store_name').val(data['store_name']);
					$('#detail_truename').val(data['truename']);
					$('#detail_addtime').val(data['addtime']);
					switch(data['cate']){
						case "0":
							$('#detail_cate').html("不限");
						break;
						case "1":
							$('#detail_cate').html("恶意竞争");
						break;
						case "2":
							$('#detail_cate').html("广告推销");
						break;
						case "3":
							$('#detail_cate').html("垃圾客户");
						break;
						case "4":
							$('#detail_cate').html("私下成交");
						break;
					}
					openWin('js_modify_detail');
				}else{
					openWin('js_modify_detail');
				}
			}
		});

	}

	//黑名单内容修改弹出框
	function modify_edit_pop(){
		var id = $('#detail_id').val();
		$.ajax({
			type: "POST",
			url: "/blacklist/edit",
			data: "edit_pop_id="+id,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(data.id){
					$('#edit_id').val(data['id']);
					$('#edit_bname').val(data['bname']);
					$('#edit_tel').val(data['tel']);
					$('#edit_remark').val(data['remark']);
					$('#edit_cate').val(data['cate']).selected = true;
					if(!data['remark'] == ""){
                           var num = data['remark'].length;
                           $("#limited1").html(num+"/100");
                       }else{
                           $("#limited1").html("0/100");
                       }
					openWin('js_modify_edit');
				}else{
					openWin('js_modify_edit');
				}
			}
		});
	}
	//黑名单内容修改
	function modify_edit(){

		var id = $('#edit_id').val();
		var bname = $('#edit_bname').val();
		var tel = $('#edit_tel').val();
		var remark = $('#edit_remark').val();
		var cate = $('#edit_cate').val();
		if(!bname){
			$('.edit_bname').html("<font color='red'>请输入正确姓名</font>");
			//openWin('js_pop_do_success');
			return false;
		}else{
			$('.edit_bname').html("");
		}
		if(!partten.test(tel)){
			$('.edit_tel').html("<font color='red'>请输入正确的电话号码</font>");
			//openWin('js_pop_do_success');
			return false ;
		}else{
			$('.edit_tel').html("");
		}

		var data = {
			'bname':bname,
			'tel':tel,
			'remark':remark,
			'cate':cate,
			'edit_id':id
		};
		$.ajax({
			type: "POST",
			url: "/blacklist/edit",
			data: data,
			dataType:"json",
			cache:false,
			error:function(){
				alert("系统错误");
				return false;
			},
			success: function(data){
				if(1==data.status){
					$('#dialog_do_itp').html('修改成功');
					$('#tr'+data.id).children("#show_bname").html(data['bname']);
					$('#tr'+data.id).children("#show_tel").html(data['tel']);
					$('#tr'+data.id).children("#show_remark").html(data['remark']);
					switch(data['cate']){
						case "0":
							$('#tr'+data.id).children("#show_cate").html("不限");
						break;
						case "1":
							$('#tr'+data.id).children("#show_cate").html("恶意竞争");
						break;
						case "2":
							$('#tr'+data.id).children("#show_cate").html("广告推销");
						break;
						case "3":
							$('#tr'+data.id).children("#show_cate").html("垃圾客户");
						break;
						case "4":
							$('#tr'+data.id).children("#show_cate").html("私下成交");
						break;
					}
					$('#js_modify_edit').hide();
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);
				}else{
					$('#js_modify_edit').hide();
					$('#dialog_do_itp').html('修改失败');
					openWin('js_pop_do_success');
					setTimeout("window.location.href='/blacklist/index'",1000);
				}
			}
		});

	}

	//弹出框备注信息限制字数
	function limit_num(){
		var maxnum = 100;  //最大字数
		var input = $("#remark").val().length;  //获取当前字数
		$("#limited").text(input+"/"+maxnum);
		if(input > maxnum){    //如果字数超过，将无法输入
			 $("#remark").val( $("#remark").val().substr(0, maxnum));
			 $("#limited").text(maxnum+"/"+maxnum);
		}
	}
	function limit_num1(){
		var maxnum = 100;  //最大字数
		var input = $("#edit_remark").val().length;  //获取当前字数
		$("#limited1").text(input+"/"+maxnum);
		if(input > maxnum){    //如果字数超过，将无法输入
			 $("#edit_remark").val( $("#edit_remark").val().substr(0, maxnum));
			 $("#limited1").text(maxnum+"/"+maxnum);
		}
	}




</script>
</body>
