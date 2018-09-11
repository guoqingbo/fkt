

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery-1.8.3.min.js"></script>
<!--我的好友弹框开始-->
<script type="text/javascript" src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=jquery.nicescroll.js"></script>
<div class="add_myFriend" style='display:block'>
    <!--名片-->
    <dl class="add_myFriendTitle">
        <dd><img src="<?php echo ($broker_info['photo'])?$broker_info['photo']:MLS_SOURCE_URL.'/mls/images/v1.0/default.png'?>" /></dd>
        <dt>
            <span>
                <p class="pName"><?php echo $broker_info['truename']?></p>
                <p class="pManage"><?php echo $broker_info['company_name']?></p>
                <p class="pManageStore"><?php echo $broker_info['agency_name']?></p>
            </span>
            <b class="iconfont myFriendClose">&#xe609;</b>
        </dt>
    </dl>
    <!--朋友列表-->
    <div class="myFriendCont">
        <div class="myFriendContTab">
            <span><a href="/cooperate_friends/index">好友列表</a></span>
            <span class="MyCurTab"><a href="/cooperate_friends/index/1">添加好友</a></span>
        </div>
        <!--搜索好友-->
		<form name="search_form" id="search_form" method="post" action="" >
        <div class="myFriendSearch">
            <span class="myFriendInput">
                <input type="text" value="<?php echo ($post_param['search_name'])?$post_param['search_name']:'查找经纪人'?>" class="InputStyle" onfocus='if (this.value == "查找经纪人") { this.value = ""; }; '
            onblur='if (this.value == "") { this.value = "查找经纪人"; };' name="search_name"/>
            </span>
            <span class="myFriendBtn" onclick="$('#search_form').submit();">搜索</span>
        </div>
		</form>
        <!--好友列表-->
		<?php if(is_full_array($broker_all_info)){?>
        <div class="MyFriendList" id="MyFriendList">
            <ul>
				<?php foreach($broker_all_info as $key=>$vo){?>
                <li>
					<?php if($vo['photo']){?>
                    <img src="<?php echo $vo['photo']?>" />
					<?php }else{?>
					<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/default.png" />
					<?php } ?>
                    <span>
                        <p class="pName"><?php echo $vo['truename'];?></p>
                        <p class="pManage" title="<?php echo $vo['company_name']?>"><?php echo $vo['company_name']?></p>
                        <p class="pManageStore" title="<?php echo $vo['agency_name']?>"><?php echo $vo['agency_name']?></p>
						<?php if($vo['status'] == 1){?>
                        <a href="javascript:void(0);" class="MyFriendSearchAdd sendColor">已添加</a>
						<?php }elseif($vo['status'] == 2){?>
						<a href="javascript:void(0);" class="MyFriendSearchAdd sendColor">已发送申请</a>
						<?php }else{?>
						<a href="javascript:void(0);" class="MyFriendSearchAdd" onclick="add_friend(<?php echo $vo['broker_id']?>)" id="bid<?php echo $vo['broker_id']?>">添加好友</a>
						<?php } ?>
                    </span>
                </li>
                <?php } ?>
            </ul>

        </div>
		<?php }else{?>
			<!--搜索没有数据显示该部分-->
			<span class="MyFriendListNone"  style="display:block;">
				<p class="iconfont DateNone">&#xe660;</p>
				<p class="ListNoneText">未搜索到相关经纪人</p>

			</span>
		<?php } ?>

        <span class="MyFriendCopate"><a href="javascript:void(0);" onclick = "window.parent.to_url('friend');">去合作朋友圈 <img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/myfriend/jt_15.jpg" /></a></span>

        <!--删除部分-->
        <span class="MyFriendListMask"></span>
        <div class="MyFriendListDel">
            <p>是否确定删除该好友？</p>
            <dl>
                <dd>确定</dd>
                <dt>取消</dt>
            </dl>
        </div>
    </div>
    <!--确认删除-->

    <script type="text/javascript">
        $(function () {
            //美化滚动条
            function listCroll() {
                $('#MyFriendList').niceScroll({
                    cursorcolor: "#ccc",//#CC0071 光标颜色
                    cursoropacitymax: 1, //改变不透明度非常光标处于活动状态（scrollabar“可见”状态），范围从1到0
                    touchbehavior: false, //使光标拖动滚动像在台式电脑触摸设备
                    cursorwidth: "5px", //像素光标的宽度
                    cursorborder: "0", //     游标边框css定义
                    cursorborderradius: "5px",//以像素为光标边界半径
                    autohidemode: false //是否隐藏滚动条
                });
            }
            listCroll();
            //好友删除显示箭头
            $(".MyFriendList li").hover(function () {
                $(this).find("b").show();
            }, function () {
                $(this).find("b").hide();
            })
            //删除朋友列表
            $(".MyFriendListClose").live("click", function () {

                var thisParent = $(this).parents("li");
                $(".MyFriendListMask").show();
                $(".MyFriendListDel").show();

                //确认删除
                $(".MyFriendListDel dd").live("click", function () {

                    thisParent.remove();
                    $(".MyFriendListMask").hide();
                    $(".MyFriendListDel").hide();
                });
                //取消删除
                $(".MyFriendListDel dt").live("click", function () {
                    $(".MyFriendListMask").hide();
                    $(".MyFriendListDel").hide();
                })

                listCroll();

            })
            //显示朋友列表
            $("#mylistF").live("click", function () {
                $(".add_myFriend").show();
                listCroll();
            })
            //关闭朋友列表
            $(".myFriendClose").live("click", function () {
                //$(".add_myFriend").hide();
				$('#js_friends_pop', parent.document).css('display','none');
                //$("#js_friends_pop").css('display','none');
                //$("#js_pop_do_permission_none").hide();
            })
			$("#js_pop_do_permission_none").css('display','none');
        })

		function add_friend(broker_id_friend){
			$.ajax({
				type: "post",
				url: "/cooperate_friends/add_apply/",
				dataType:"json",
				data: {
					broker_id_friend: broker_id_friend
				},
				cache:false,
				error:function(){
				},
				success: function(data){
					if(data['status'] == 1){
						$("#bid"+data['broker_id_friend']).html('已发送申请');
						$("#bid"+data['broker_id_friend']).addClass('sendColor');
					}
				}
			});
		}
    </script>

</div>
    <!--我的好友弹框over-->


