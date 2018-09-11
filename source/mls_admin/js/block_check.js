/*小区审核页面的相应操作*/

//审核操作
function change_esta (blockid) {
	if(blockid){
		var esta = "";
		var waring = "";
			var estaobj = document.getElementsByName('esta');
			for(var i = 0;i < estaobj.length ; i++){
				if( estaobj[i].checked){
					esta = estaobj[i].value;
				}
			} 

		if(esta == 0){
			waring = "确定删除小区相关信息吗？";
		}else if(esta == 1){
			waring = "确定把小区变成临时小区吗？";
		}else if(esta == 2){
			waring = "确定把小区变成正式小区吗？";
		}
		if(confirm(waring)){
			$.ajax({
				type: "post",
				url: "/community/checkaction/check/"+blockid+"/"+esta,
				dataType:"json",
				contentType: "application/x-www-form-urlencoded; charset=utf-8", 
                                success: function(data){	
                                    if(data == 0){
                                            alert('删除小区成功!');
                                    }else if(data == 1){
                                            alert('改成临时小区操作成功!');
                                    }else if(data == 2){
                                            alert('改成正式小区操作成功!');
                                    }else if(data == 10){
                                            alert('更新小区关键字失败!');
                                    }else{
                                            alert('操作失败!');
                                    }
                                }
			});

			//framereload('top');
		}
	}else{
		alert('参数错误!');
		resme_block ();
	}
}


//查询操作
function search_block(type , this_cmt_id) {
	//关键字
	var querylen = 0; 

	if(type == 1){
		var blockname  = $('#blockname').val().replace(/\s+/g,"");
		querylen = blockname.length;
		var query = "/search_name/"+blockname+"/"+this_cmt_id;
		var classname = "#blockname_waring";
		var waring = "小区名称不能为空!";
		var tip = "暂无结果";
	}else if(type == 2){
		var blockaddress  = $('#blockaddress').val().replace(/\s+/g,"");
		querylen = blockaddress.length ;
		var query = "/search_address/"+blockaddress+"/"+this_cmt_id;
		var classname = "#blockaddress_waring";
		var waring = "小区地址不能为空!";
		var  tip = "暂无结果";
	}
		
	if( querylen >0 ){
		$.ajax({
			type: "post",
			url: "/community/searchaction"+query,
			dataType:"html",
			success: function(data){
                            //返回操作结果
                            var response = eval('('+data+')');
                            //从小区（待审核、临时楼盘）
                            if(response.bak != 'nodata') {
                                $("#show_details tr:not(:first)").remove();
                                $("#checkbox").css({'display':'','text-align':'center','font-weight':'bold'});
                                for(var i = 0; i < response.bak.length; i++) {	
                                    $("<tr><td align='center'><input type='checkbox' class='bak' name='id' value='"+response.bak[i]['id']+"'/></td><td align='center'>"+response.bak[i]['cmt_name']+"</td><td align='center'>"+response.bak[i]['dist_name']+"</td><td align='center'>"+response.bak[i]['street_name']+"</td><td align='center'>"+response.bak[i]['address']+"</td><tr>").insertAfter($("#show_details tr:eq("+i+")"));
                                }
                            }else{
                                $("#show_details tr:not(:first)").remove();
                                $("<tr><td colspan='4' style='text-align:center;font-weight:bold;'>暂无小区信息</td></tr>").insertAfter($("#show_details tr:eq(0)"));
                            }
                            //主小区（正式楼盘）
                            if(response.main != 'nodata') {
                                $("#show_details2 tr:not(:first)").remove();
                                $("#checkbox2").css({'display':'','text-align':'center','font-weight':'bold'});
                                for(var i = 0; i < response.main.length; i++) {	
                                    $("<tr><td align='center'><input type='radio' class='main' name='id' value='"+response.main[i]['id']+"'/></td><td align='center'>"+response.main[i]['cmt_name']+"</td><td align='center'>"+response.main[i]['dist_name']+"</td><td align='center'>"+response.main[i]['street_name']+"</td><td align='center'>"+response.main[i]['address']+"</td><tr>").insertAfter($("#show_details2 tr:eq("+i+")"));
                                }
                            }else{
                                $("#show_details2 tr:not(:first)").remove();
                                $("<tr><td colspan='4' style='text-align:center;font-weight:bold;'>暂无小区信息</td></tr>").insertAfter($("#show_details2 tr:eq(0)"));
                            }
			}
		});
	}else{
		show_tip(classname,waring);//为空处理
	}
}

//合并操作
function hb_block () {
    var mainblock = $('#mainblock').val().replace(/\s+/g,"");
    var bakblock = '';
    var blocks = [];
    $('input[name="id"]:checked').each(function(){
        blocks.push($(this).val());
    });
    if('' == mainblock || blocks.length != 2){
        alert('所选小区个数必须为2/请填写目标小区');
    }else{
        if(blocks.indexOf(mainblock)==-1){
            alert('目标小区编号填写错误');
        }else{
            for(var i=0;i<blocks.length;i++){
                if(mainblock!=blocks[i]){
                    bakblock = blocks[i];
                }
            }
            var query = '/'+mainblock+'/'+bakblock;
            $.ajax({
                type: "post",
                url: "/community/merge"+query,
                dataType:"html",
                success: function(data){
                    //返回操作结果
                    if(data == '1') {
                        alert('合并成功');
                    }else{
                        alert('合并失败');
                    }
                }
            });
        }
    }
}

function h_tip(type) {
	message = "";
	if(type == "blockname"){
		classname = "#blockname_waring";
	}else if(type == "blockaddress"){
		classname = "#blockaddress_waring";
	}
	
	hidden_tip(classname,message);
}


//取消操作
function resme_block () {
	window.history.go(-1);
}

//显示提示警告
function show_tip(classname,message){
	$(classname).show();
	$(classname).text(message);
}

//隐藏提示和警告
function hidden_tip(classname,message){
	$(classname).text();
	$(classname).hide();
}

//最终确定操作
function last_action(){
    var is_do;
    var waring = '';
    var ajax_url = '';
    var action_type = $('input[name="action_type"]:checked').val();
    var this_comm_id = $('#cmt_id').val();
    //所有选中楼盘id
    var comm_id = [];
    var i = 0;
    $('input[name="id"]:checked').each(function(){
        comm_id[i] = $(this).val();
        i++;
    });
    //正式楼盘作为主楼盘id
    var main_comm_id = [];
    var i = 0;
    $('input[name="id"][class="main"]:checked').each(function(){
        main_comm_id[i] = $(this).val();
        i++;
    });
    //删除楼盘
    if(action_type==0){
        waring = "确定将该楼盘删除吗？";
        ajax_url = "/community/checkaction/check/0/";
    //正式楼盘
    }else if(action_type==2){
        waring = "确定把小区变成正式楼盘吗？";
        ajax_url = "/community/checkaction/check/2";
    //合并楼盘
    }else if(action_type==1){
        if(0==main_comm_id.length){
            waring = "必须选择正式小区";
        }else{
            waring = "确定合并楼盘吗？";
            ajax_url = "/community/checkaction/merge/";
        }
    }
    is_do =  confirm(waring);
    //设置正式楼盘和删除楼盘
    if(is_do){
        $.ajax({
                type: "post",
                url: ajax_url,
                data:{
                    'comm_id':comm_id,
                    'main_comm_id':main_comm_id,
                    'this_comm_id':this_comm_id
                },
                dataType:"json",
                contentType: "application/x-www-form-urlencoded; charset=utf-8", 
                success: function(data){
                    if(data == 3){
                            alert('操作成功!');
                            window.location.href = '../check_list';
                    }else if(data == 1){
                            alert('改成临时小区操作成功!');
                            window.location.href = '../check_list';
                    }else if(data == 2){
                        var is_result = confirm('成功添加正式小区！是否马上去填写小区其他信息？');
                        if(is_result){
                            window.location.href = '../modify/'+this_comm_id;
                        }else{
                            window.location.href = '../check_list';
                        }
                    }else if(data == 10){
                            alert('更新小区关键字失败!');
                    }else if(data == 5){
                            alert('合并成功!');
                            window.location.href = '../check_list';
                    }else{
                            alert('操作失败!');
                            window.location.href = '../check_list';
                    }
                }
        });
    }
}
