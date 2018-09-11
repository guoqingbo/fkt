<!--页面部分-->
<body >
	<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
	<a href="<?php echo MLS_URL;?>/customer/manage_pub" class="link link_on"><span class="iconfont">&#xe62e;</span>公盘公客</a>
	<a href="/customer/collect" class="link"><span class="iconfont">&#xe613;</span>我的收藏</a>
</div>
<!--描述：导航栏结束-->
<!--描述：分类导航栏开始-->
<div id="js_search_box" class="shop_tab_title  scr_clear">
			<a href="#" class="link link_on">出售<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link">出租<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link">求购<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link">求组<span class="iconfont hide">&#xe607;</span></a>
</div>
<!--描述：分类导航栏结束-->
<!--描述：选择区域开始-->
<div class="search_box clearfix" id="js_search_box"> <a href="javascript:void(0)" class="s_h" onClick="show_hide_info(this)" data-h="0">展开<span class="iconfont">&#xe609;</span></a>
    <div class="fg_box">
        <p class="fg fg_tex">时间：</p>
        <div class="fg">
            <select class="select">
                <option>半年内</option>
                <option>一年内</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 状态：</p>
        <div class="fg">
            <select class="select">
                <option>有效</option>
                <option>无效</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 物业类型：</p>
        <div class="fg">
            <select class="select">
                <option>不限</option>
                <option>住宅</option>
                <option>住宅</option>
                <option>住宅</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 区属：</p>
        <div class="fg">
            <select class="select">
                <option>不限</option>
                <option>雨花台</option>
                <option>鼓楼</option>
                <option>雨花台</option>
                <option>鼓楼</option>
                <option>雨花台</option>
                <option>鼓楼</option>
                <option>雨花台</option>
                <option>鼓楼</option>
                <option>雨花台</option>
                <option>鼓楼</option>
                <option>雨花台</option>
                <option>鼓楼</option>
            </select>
        </div>
    </div>
      <div class="fg_box">
        <p class="fg fg_tex"> 板块：</p>
        <div class="fg">
            <select class="select">
                <option>不限</option>
                <option>新街口</option>
                <option>鼓楼</option>
                <option>湖南路</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <p class="fg fg_tex"> 楼盘：</p>
        <div class="fg">
            <input type="text" class="input w60">
        </div>
    </div>
    <div style="float:left;clear:left;" class="hide"></div>
    <div class="fg_box hide">
        <p class="fg fg_tex"> 户型：</p>
        <div class="fg">
            <select class="select">
                <option>不限</option>
                <option>三室</option>
                <option>三室</option>
                <option>三室</option>
            </select>
        </div>
    </div>
    <div class="fg_box hide">
        <p class="fg fg_tex"> 面积：</p>
        <div class="fg">
            <input type="text" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex03">平米</p>
    </div>
    <div class="fg_box hide">
        <p class="fg fg_tex"> 总价：</p>
        <div class="fg">
            <input type="text" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex02">—</p>
        <div class="fg">
            <input type="text" class="input w30">
        </div>
        <p class="fg fg_tex fg_tex03">万元</p>
    </div>
    <div class="fg_box hide">
        <p class="fg fg_tex"> 标签：</p>
        <div class="fg">
            <select class="select">
                <option>不限</option>
                <option>我爱我家华侨路分店</option>
                <option>我爱我家鼓楼分店</option>
                <option>湖南路</option>
            </select>
        </div>
    </div>
    <div class="fg_box">
        <div class="fg"> <a href="javascript:void(0)" class="btn" >搜索</a> </div>
        <div class="fg"> <a href="javascript:void(0)" class="reset">重置</a> </div>
    </div>
</div>
<!--描述：选择区域结束-->
<!--描述：内容选择项开始-->
<div class="fun_btn clearfix" id="js_fun_btn">
	<label class="btn btn_del"><input type="checkbox" id="js_checkbox">全选</label>
	<label class="btn btn_del"><input type="checkbox" id="js_zaixiangoutong">在线沟通</label>
    <a href="#" class="btn">智能匹配</a>
    <a href="#" class="btn">我要订阅</a>
    <div class="get_page"> <span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to">跳转</a>
        <div id="js_f_input" class="f_input hide"><span class="tex">跳转到第</span>
            <input class="input" type="text">
            <span  class="tex">页</span><a class="b_link" href="javascript:void(0)">确定</a></div>
    </div>
</div>
<!--描述：内容选择项结束-->
<!--描述：主要内容区域开始-->
<div class="table_all">
     <div class="title" id="js_title">
        <table class="table">
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" id="js_checkbox">
                    </div></td>
                <td class="c4"><div class="info">特色</div></td>
                <td class="c3"><div class="info">交易</div></td>
                <td class="c3"><div class="info">状态</div></td>
                <td class="c3"><div class="info">性质</div></td>
                <td class="c5"><div class="info">房源编号</div></td>
                <td class="c5"><div class="info">物业类型</div></td>
                <td class="c4"><div class="info">区属</div></td>
                <td class="c4"><div class="info">板块</div></td>
                <td class="c6"><div class="info">楼盘</div></td>
                 <td class="c4"><div class="info">户型</div></td>
                <td class="c5">
                	<div class="info">面积<br>(㎡)</div>
                </td>
                <td class="c5">
                	<div class="info"><a href="javascript:void(0)" class="i_text i_down">报价<br>(W)</a></div>
                </td>
                <td class="c6">
                	<div class="info"><a href="javascript:void(0)" class="i_text i_up">单价<br> (元/㎡)</a></div>
                </td>
                <td class="c7"><div class="info">委托<br>经纪人</div></td>
                <td class="c7"><div class="info">联系方式</div></td>
                <td class="c4"><div class="info">好评率</div></td>
                <td class="c4"><div class="info">合作<br>成功率</div></td>
                <td class="c6"><div class="info">跟进时间</div></td>
                <td colspan="3"><div class="info">操作控制</div></td>

            </tr>
        </table>
    </div>
    <div  id="js_inner" style="overflow-y: scroll;">
        <table class="table">
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr>
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span class="onper">&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
            <tr class="bg">
                <td class="c2"><div class="info">
                        <input type="checkbox" class="checkbox">
                    </div></td>
                <td class="c4"><div class="info"><span class="iconfont ts">&#xe606;</span><span class="iconfont ts ts02">&#xe60d;</span></div></td>
                <td class="c3"><div class="info">售</div></td>
                <td class="c3"><div class="info">有效</div></td>
                <td class="c3"><div class="info">私</div></td>
                <td class="c5"><div class="info">122222</div></td>
                <td class="c5"><div class="info">住宅</div></td>
                <td class="c4"><div class="info">雨花台</div></td>
                <td class="c4"><div class="info">新街口</div></td>
                <td class="c6"><div class="info">怡景花园</div></td>
                <td class="c4"><div class="info">3-2-1</div></td>
                <td class="c5"><div class="info">100</div></td>
                <td class="c5"><div class="info">200</div></td>
                <td class="c6"><div class="info">20000</div></td>
                <td class="c7"><div class="info">张晓明<span>&#xe616;</span></div></td>
                <td class="c7"><div class="info">182325155155</div></td>
                <td class="c4"><div class="info">90.35%</div></td>
                <td class="c4"><div class="info">100%</div></td>
                <td class="c6"><div class="info">2014-11-26<br>14:48:23</div></td>
                <td class="c5"><a href="javascript:void(0)" class="hezuo">合作申请</a></td>
                <td class="c5"><a href="javascript:void(0)" class="shcang">取消收藏</a></td>
                <td class="c3"><a href="javascript:void(0)" class="jubao" onclick="openWin('js_woyaojubao')">举报</a></td>
            </tr>
          </table>
    </div>
</div>
<!--描述：主要内容区域结束-->
<!--描述：右击弹出列表页面*START*-->
<ul id="openList">
    <!--右键菜单-->
    <li onClick="	openWin('js_pop_box_g');">查看详情</li>
    <li>修改信息</li>
    <li onClick="openWin('js_zhineng')">智能匹配</li>
    <li>共享房源</li>
    <li>取消共享</li>
    <li onClick="openWin('js_genjin')">写跟进</li>
    <li>查看跟进</li>
    <!--    <li>群发</li>-->
    <li onClick="openWin('js_fenpeirenwu')">分配任务</li>
    <!--
    	作者：516037580@qq.com
    	时间：2014-12-29
    	描述：我要申诉，我要举报，出售公盘
    -->
    <li onClick="openWin('js_woyaoshensu')">我要申诉</li>
    <li onClick="openWin('js_woyaojubao')">我要举报</li>
    <li onClick="openWin('js_sellhouse')">出售公盘</li>
</ul>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/009.gif" id="mainloading" ><!--遮罩 loading-->
<!--描述：右击弹出列表页面*END*-->
<!--弹出框列表*STARTING*-->
<!--房源信息-->
<div class="pop_box_g" id="js_pop_box_g">
    <div class="hd">
        <div class="title">房源详情</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl class="clearfix" id="js_tab_t01">
                <dd class="js_t item itemOn" title="房源详情">房源详情</dd>
                <dd class="js_t item" title="保密信息">保密信息</dd>
                <dd class="js_t item" title="房源图片">房源图片</dd>
                <dd class="js_t item" title="小区概况">小区概况</dd>
                <dd class="js_t item" title="合作统计">合作统计</dd>
            </dl>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b01">
            <div class="js_d inner" style="display:block;">
                <table class="table">
                    <tr>
                        <td class="w60 t_l">楼盘名称：</td>
                        <td class="w170">天润城第二街区</td>
                        <td class="w60 t_l">区属：</td>
                        <td class="w170">鼓楼</td>
                        <td class="w60 t_l">板块：</td>
                        <td>三牌楼</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">地址：</td>
                        <td class="w170">南京市浦口区柳洲东路9号区</td>
                        <td class="w60 t_l">状态：</td>
                        <td class="w170">有效</td>
                        <td class="w60 t_l">房源性质：</td>
                        <td>私盘</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">户型：</td>
                        <td class="w170">3室2厅1卫1厨1阳台</td>
                        <td class="w60 t_l">楼层：</td>
                        <td class="w170">9/14</td>
                        <td class="w60 t_l">朝向：</td>
                        <td>南</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">装修：</td>
                        <td class="w170">简装</td>
                        <td class="w60 t_l">房龄：</td>
                        <td class="w170">2010年</td>
                        <td class="w60 t_l">面积：</td>
                        <td>100平方米</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">售价：</td>
                        <td class="w170">200万元</td>
                        <td class="w60 t_l">单价：</td>
                        <td class="w170">20000元/平米</td>
                        <td class="w60 t_l">税费：</td>
                        <td>各付</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">委托协议：</td>
                        <td class="w170">已签</td>
                        <td class="w60 t_l">委托类型：</td>
                        <td class="w170">多家登记</td>
                        <td class="w60 t_l">类型：</td>
                        <td>多层</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">结构：</td>
                        <td class="w170">框架</td>
                        <td class="w60 t_l">产权：</td>
                        <td class="w170">商品房</td>
                        <td class="w60 t_l">物业费：</td>
                        <td>1.2元/月/m²</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">付款方式：</td>
                        <td class="w170">按揭</td>
                        <td class="w60 t_l">付佣方式：</td>
                        <td class="w170">给佣</td>
                        <td class="w60 t_l">证件：</td>
                        <td>无</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">现状：</td>
                        <td class="w170">空置房</td>
                        <td class="w60 t_l">看房：</td>
                        <td class="w170">提前预约</td>
                        <td class="w60 t_l">信息来源：</td>
                        <td>店面</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">房屋设施：</td>
                        <td colspan="5">水、电天然气、暖气、电话、电视、空调、家具、太阳能、洗衣机、热水器、油烟机、电冰箱、微波炉、橱柜、宽带、电梯、停车位、水、电天然气、暖气、电话、电视、空调、家具、太阳能、洗衣机、热水器、油烟机、电冰箱、微波炉、橱柜、宽带、电梯、停车位 </td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">周边环境：</td>
                        <td colspan="5">幼儿园、小学、中学、医院、银行、火车站、汽车站、地铁、停车场、超市、公园、菜场、商场、健身房、体育馆</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">交房时间：</td>
                        <td class="w170">2014-12-31</td>
                        <td class="w60 t_l">委托时间：</td>
                        <td class="w170">2014-12-31</td>
                        <td class="w60 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">备注：</td>
                        <td colspan="5">好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房位 </td>
                    </tr>
                </table>
            </div>
            <div class="js_d inner inner02">
                <div class="t_box">
                    <table class="table" id="js_table_ys">
                        <tr>
                            <td class="w60 t_l">楼盘名称：</td>
                            <td class="w170">天润城第二街区</td>
                            <td class="w60 t_l">区属：</td>
                            <td class="w170">鼓楼</td>
                            <td class="w60 t_l">板块：</td>
                            <td>三牌楼</td>
                        </tr>
                        <tr>
                            <td class="w60 t_l">地址：</td>
                            <td class="w170">南京市浦口区柳洲东路9号区</td>
                            <td class="w60 t_l">栋座：</td>
                            <td class="w170"><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">13栋</strong></td>
                            <td class="w60 t_l">单元：</td>
                            <td><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">3</strong></td>
                        </tr>
                        <tr>
                            <td class="w60 t_l">门牌：</td>
                            <td class="w170"><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">104</strong></td>
                            <td class="w60 t_l">业主姓名：</td>
                            <td class="w170"><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">张阿姨</strong></td>
                            <td class="w60 t_l">业主电话：</td>
                            <td><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">18005655452</strong></td>
                        </tr>
                        <tr>
                            <td class="w60 t_l">身份证号：</td>
                            <td class="w170">320122111111111111</td>
                            <td class="w60 t_l">面积：</td>
                            <td class="w170">100平方米</td>
                            <td class="w60 t_l">售价：</td>
                            <td> 200万元</td>
                        </tr>
                        <tr>
                            <td class="w60 t_l">底价：</td>
                            <td class="w170"><strong class="color js_y_hide">***</strong><strong class="color js_y_hide hide">180万元</strong></td>
                            <td class="w60 t_l">单价：</td>
                            <td class="w170">20000元/平米</td>
                            <td class="w60 t_l">书证号：</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="w60 t_l">丘地号：</td>
                            <td class="w170">&nbsp;</td>
                            <td class="w60 t_l">备案号：</td>
                            <td class="w170">&nbsp;</td>
                            <td class="w60 t_l">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                    <p class="link_btn_b"><a onClick="show_ys_inner(this)" href="javascript:void(0)"><span class="iconfont">&#xe610;</span>查看保密信息</a></p>
                    <!--
														<p class="link_btn_b">很遗憾，您无权查看相关保密信息。</p>
														-->

                </div>
                <div class="clearfix pop_fg_fun_box">
                    <div class="text left"><span class="fg">总查阅次数：<a href="#">8</a></span><span class="fg">今日查阅次数：<a href="#">2</a></span></div>
                    <div class="get_page"><span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to01">跳转</a>
                        <div id="js_f_input01" class="f_input hide"> <span class="tex">跳转到第</span>
                            <input class="input" type="text">
                            <span  class="tex">页</span> <a class="b_link" href="javascript:void(0)">确定</a> </div>
                    </div>
                </div>
                <div class="table_list_box">
                    <table class="table_list">
                        <tr>
                            <th class="w130">最近查阅时间</th>
                            <th class="w170">查阅门店</th>
                            <th class="w60">查阅人</th>
                            <th class="w90">总查阅次数</th>
                            <th class="w90">今日查阅次数</th>
                            <th>初次查阅时间</th>
                        </tr>
                        <tr>
                            <td>2014-11-21 15:23:34</td>
                            <td>21世纪不动产总店</td>
                            <td>厉小塔</td>
                            <td>4</td>
                            <td>1</td>
                            <td>2014-10-21  15:23:34</td>
                        </tr>
                        <tr class="bg">
                            <td>2014-11-21 15:23:34</td>
                            <td>21世纪不动产总店</td>
                            <td>厉小塔</td>
                            <td>4</td>
                            <td>1</td>
                            <td>2014-10-21  15:23:34</td>
                        </tr>
                        <tr>
                            <td>2014-11-21 15:23:34</td>
                            <td>21世纪不动产总店</td>
                            <td>厉小塔</td>
                            <td>4</td>
                            <td>1</td>
                            <td>2014-10-21  15:23:34</td>
                        </tr>
                        <tr  class="bg">
                            <td>2014-11-21 15:23:34</td>
                            <td>21世纪不动产总店</td>
                            <td>厉小塔</td>
                            <td>4</td>
                            <td>1</td>
                            <td>2014-10-21  15:23:34</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="js_d inner inner02">
                <div class="show_house_pic">
                    <p class="title">室内图</p>
                    <div class="pic"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="220" width="340"> </div>
                    <div class="small_pic">
                        <div class="prev"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <li class="item active"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                            </ul>
                        </div>
                        <div class="next iconfont"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
                <div class="show_house_pic_fg">&nbsp;</div>
                <div class="show_house_pic">
                    <p class="title">室内图</p>
                    <div class="pic"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="220" width="340"> </div>
                    <div class="small_pic">
                        <div class="prev prev_click"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <li class="item active"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                            </ul>
                        </div>
                        <div class="next iconfont"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
            </div>
            <div class="js_d inner">
                <table class="table">
                    <tr>
                        <td class="w60 t_l">楼盘名称：</td>
                        <td class="w170">天润城第二街区</td>
                        <td class="w60 t_l">区属：</td>
                        <td class="w170">鼓楼</td>
                        <td class="w60 t_l">板块：</td>
                        <td>三牌楼</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">地址：</td>
                        <td class="w170">南京市浦口区柳洲东路9号区</td>
                        <td class="w60 t_l">物业类型：</td>
                        <td class="w170">住宅</td>
                        <td class="w60 t_l">建筑年代：</td>
                        <td>2010年</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">产权年限：</td>
                        <td class="w170">70年</td>
                        <td class="w60 t_l">建筑面积：</td>
                        <td class="w170">123214平方米</td>
                        <td class="w60 t_l">占地面积：</td>
                        <td>123214平方米</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">物业公司：</td>
                        <td class="w170">中海物业</td>
                        <td class="w60 t_l">开发商：</td>
                        <td class="w170">中海地产</td>
                        <td class="w60 t_l">停车位：</td>
                        <td>地面车位充足 有1234地下车位</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">绿化率：</td>
                        <td class="w170">40%</td>
                        <td class="w60 t_l">容积率：</td>
                        <td class="w170">2.14</td>
                        <td class="w60 t_l">物业费：</td>
                        <td>2.2 元/月/m²</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">总栋数：</td>
                        <td class="w170">28</td>
                        <td class="w60 t_l">总户数：</td>
                        <td class="w170">1280</td>
                        <td class="w60 t_l">楼层状况：</td>
                        <td>1梯2户</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">楼盘简介：</td>
                        <td colspan="5">真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">周边环境：</td>
                        <td colspan="5">幼儿园、小学、中学、医院、银行、火车站、汽车站、地铁、停车场、超市、公园、菜场、商场、健身房、体育馆 </td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">图片：</td>
                        <td colspan="5"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="105" width="140">&nbsp;&nbsp;<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="105" width="140"></td>
                    </tr>
                </table>
            </div>
            <div class="js_d inner inner02">
                <div class="hz_inner_info">
                    <div class="title"><span class="fg">本房源被查看次数：12</span>本房源被查看人数：12</div>
                    <div class="info">
                        <div class="list">
                            <table class="table">
                                <tr>
                                    <th class="w90">查看人</th>
                                    <th class="w240">所属门店</th>
                                    <th class="w120">联系方式</th>
                                    <th class="w70">查看次数</th>
                                    <th>最近查看时间</th>
                                </tr>
                                <tr class="bg">
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>9</td>
                                    <td>2014-11-23  17:23:56</td>
                                </tr>
                                <tr>
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>9</td>
                                    <td>2014-11-23  17:23:56</td>
                                </tr>
                                <tr class="bg">
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>9</td>
                                    <td>2014-11-23  17:23:56</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="hz_inner_info">
                    <div class="title"><span class="fg">本房源被申请合作次数：12</span>本房源被申请合作人数：12</div>
                    <div class="info">
                        <div class="list">
                            <table class="table">
                                <tr>
                                    <th class="w90">申请人</th>
                                    <th class="w240">所属门店</th>
                                    <th class="w120">联系方式</th>
                                    <th class="w170">申请时间</th>
                                    <th>状态</th>
                                </tr>
                                <tr class="bg">
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>2014-11-23  17:23:56</td>
                                    <td><strong class="s_z">申请中</strong></td>
                                </tr>
                                <tr>
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>2014-11-23  17:23:56</td>
                                    <td><strong class="s_z">申请中</strong></td>
                                </tr>
                                <tr class="bg">
                                    <td>王大鹏</td>
                                    <td>我爱我家华侨路分店</td>
                                    <td>18293847283</td>
                                    <td>2014-11-23  17:23:56</td>
                                    <td><strong class="h_z">合作中</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab_pop_bd">
            <div><a class="btn btn_del" href="javascript:void(0);">编辑</a><a class="btn btn_del" href="javascript:void(0);">删除</a></div>
            <div> <a class="btn" href="javascript:void(0);">提醒</a> <a class="btn" href="javascript:void(0);">分配任务</a> <a class="btn" href="javascript:void(0);">查看跟进</a> <a class="btn" href="javascript:void(0);">写跟进</a> </div>
        </div>
    </div>
</div>
<!--智能匹配-->
<div class="pop_box_g pop_box_g02" id="js_zhineng">
    <div class="hd">
        <div class="title">智能匹配</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title"> 所选房源</h3>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c6">特色</th>
                        <th class="c4">交易</th>
                        <th class="c4">状态</th>
                        <th class="c4">性质</th>
                        <th class="c8">房源<br>
                            编号</th>
                        <th class="c4">物业<br>
                            类型</th>
                        <th class="c5">区属</th>
                        <th class="c4">板块</th>
                        <th class="c9">楼盘</th>
                        <th class="c4">房龄</th>
                        <th class="c4">户型</th>
                        <th class="c4">类型</th>
                        <th class="c4">朝向</th>
                        <th class="c4">楼层</th>
                        <th class="c4">装修</th>
                        <th class="c4">面积<br>
                            (㎡)</th>
                        <th class="c4">报价<br>
                            (W)</th>
                        <th class="c5">单价<br>
                            (元/㎡)</th>
                        <th class="c9">委托<br>
                            门店</th>
                        <th>委托<br>
                            经纪人</th>
                    </tr>
                    <tr>
                        <td><span class="iconfont ts"></span><span class="iconfont ts ts02"></span></td>
                        <td>售</td>
                        <td>有效</td>
                        <td>私</td>
                        <td>21242141</td>
                        <td>住宅</td>
                        <td>雨花台</td>
                        <td>板桥</td>
                        <td>金域华府</td>
                        <td>2012</td>
                        <td>3-2-1</td>
                        <td>高层</td>
                        <td>南</td>
                        <td>9/20</td>
                        <td>简装</td>
                        <td>90</td>
                        <td>100</td>
                        <td>12000</td>
                        <td>中原地产安江平阳店</td>
                        <td>王大鹏</td>
                    </tr>
                </table>
            </div>
            <h3 class="title"> 匹配条件筛选</h3>
            <div class="inner inner02 clearfix">
                <div class="fg_box">
                    <p class="fg fg_tex">范围：</p>
                    <div class="fg">
                        <select class="select">
                            <option>不限</option>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">时间：</p>
                    <div class="fg">
                        <select class="select">
                            <option>一个月</option>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 租金：</p>
                    <div class="fg">
                        <input type="text" class="input w30">
                    </div>
                    <p class="fg fg_tex fg_tex02">—</p>
                    <div class="fg">
                        <input type="text" class="input w30">
                    </div>
                    <p class="fg fg_tex fg_tex03">元/月</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 面积：</p>
                    <div class="fg">
                        <input type="text" class="input w30">
                    </div>
                    <p class="fg fg_tex fg_tex02">—</p>
                    <div class="fg">
                        <input type="text" class="input w30">
                    </div>
                    <p class="fg fg_tex fg_tex03">平米</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 物业类型：住宅</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 户型：3室</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">区属：鼓楼区</p>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex">板块：</p>
                    <div class="fg">
                        <select class="select">
                            <option>不限</option>
                        </select>
                    </div>
                </div>
                <div class="fg_box">
                    <p class="fg fg_tex"> 楼盘：</p>
                    <div class="fg">
                        <input type="text" class="input w175">
                    </div>
                </div>
                <div class="fg_box">
                    <div class="fg"> <a href="javascript:void(0)" class="btn">智能匹配</a> </div>
                </div>
            </div>
            <div class="clearfix pop_fg_fun_box">
                <div class="text left text_color">共123条客源符合匹配条件</div>
                <div class="get_page"><span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to02">跳转</a>
                    <div id="js_f_input02" class="f_input hide"> <span class="tex">跳转到第</span>
                        <input class="input" type="text">
                        <span class="tex">页</span> <a class="b_link" href="javascript:void(0)">确定</a> </div>
                </div>
            </div>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="c4">交易</th>
                        <th class="c4">状态</th>
                        <th class="c4">性质</th>
                        <th class="c8">房源编号</th>
                        <th class="c7">客户姓名</th>
                        <th class="c7">物业类型</th>
                        <th class="c7">意向区属</th>
                        <th class="c7">意向板块</th>
                        <th class="c9">意向楼盘</th>
                        <th class="c4">户型</th>
                        <th class="c4">装修</th>
                        <th class="c4">面积<br>
                            (㎡)</th>
                        <th class="c4">报价<br>
                            (W)</th>
                        <th class="c9">委托门店</th>
                        <th>委托<br>
                            经纪人</th>
                        <th>跟进时间</th>
                    </tr>
                    <tr>
                        <td>售</td>
                        <td>有效</td>
                        <td>私</td>
                        <td>21242141</td>
                        <td>张大妈</td>
                        <td>住宅</td>
                        <td>2012</td>
                        <td>板桥</td>
                        <td>金域华府</td>
                        <td>3-2-1</td>
                        <td>简装</td>
                        <td>100</td>
                        <td>120</td>
                        <td>中原地产安江平阳店</td>
                        <td>王大鹏</td>
                        <td>2014-11-26<br>
                            14:48:23</td>
                    </tr>
                    <tr class="bg">
                        <td>售</td>
                        <td>有效</td>
                        <td>私</td>
                        <td>21242141</td>
                        <td>张大妈</td>
                        <td>住宅</td>
                        <td>2012</td>
                        <td>板桥</td>
                        <td>金域华府</td>
                        <td>3-2-1</td>
                        <td>简装</td>
                        <td>100</td>
                        <td>120</td>
                        <td>中原地产安江平阳店</td>
                        <td>王大鹏</td>
                        <td>2014-11-26<br>
                            14:48:23</td>
                    </tr>
                    <tr>
                        <td>售</td>
                        <td>有效</td>
                        <td>私</td>
                        <td>21242141</td>
                        <td>张大妈</td>
                        <td>住宅</td>
                        <td>2012</td>
                        <td>板桥</td>
                        <td>金域华府</td>
                        <td>3-2-1</td>
                        <td>简装</td>
                        <td>100</td>
                        <td>120</td>
                        <td>中原地产安江平阳店</td>
                        <td>王大鹏</td>
                        <td>2014-11-26<br>
                            14:48:23</td>
                    </tr>
                    <tr class="bg">
                        <td>售</td>
                        <td>有效</td>
                        <td>私</td>
                        <td>21242141</td>
                        <td>张大妈</td>
                        <td>住宅</td>
                        <td>2012</td>
                        <td>板桥</td>
                        <td>金域华府</td>
                        <td>3-2-1</td>
                        <td>简装</td>
                        <td>100</td>
                        <td>120</td>
                        <td>中原地产安江平阳店</td>
                        <td>王大鹏</td>
                        <td>2014-11-26<br>
                            14:48:23</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!--房源跟进-->
<div class="pop_box_g" id="js_genjin">
    <div class="hd">
        <div class="title">房源跟进</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <div class="clearfix pop_fg_fun_box">
                <div class="text left text_title">跟进明细</div>
                <div class="get_page"><span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a href="javascript:void(0)" id="js_get_page_to03">跳转</a>
                    <div id="js_f_input03" class="f_input hide"> <span class="tex">跳转到第</span>
                        <input class="input" type="text">
                        <span class="tex">页</span> <a class="b_link" href="javascript:void(0)">确定</a> </div>
                </div>
            </div>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="w160">跟进日期</th>
                        <th class="w110">类别</th>
                        <th class="w240">内容</th>
                        <th class="w130">带看/成交客户</th>
                        <th>跟进人</th>
                    </tr>
                    <tr>
                        <td>2014-11-22 13:23:17</td>
                        <td>带看</td>
                        <td>带客户看房，房子不错</td>
                        <td>李阿姨</td>
                        <td>王大鹏</td>
                    </tr>
                    <tr class="bg">
                        <td>2014-11-22 13:23:17</td>
                        <td>带看</td>
                        <td>带客户看房，房子不错</td>
                        <td>李阿姨</td>
                        <td>王大鹏</td>
                    </tr>
                    <tr>
                        <td>2014-11-22 13:23:17</td>
                        <td>带看</td>
                        <td>带客户看房，房子不错</td>
                        <td>李阿姨</td>
                        <td>王大鹏</td>
                    </tr>
                    <tr class="bg">
                        <td>2014-11-22 13:23:17</td>
                        <td>带看</td>
                        <td>带客户看房，房子不错</td>
                        <td>李阿姨</td>
                        <td>王大鹏</td>
                    </tr>
                </table>
            </div>
            <h3 class="title"> 房源跟进<span class="text">(房源010202)</span></h3>
            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进日期：</p>
                    <p class="i_text">2014-11-22</p>
                    <p class="t_text">跟进方式：</p>
                    <div class="i_text">
                        <label class="label">
                            <input type="radio" name="radio01">
                            堪房</label>
                        <label class="label">
                            <input type="radio" name="radio01">
                            修改</label>
                        <label class="label">
                            <input type="radio" name="radio01">
                            电话</label>
                        <label class="label">
                            <input type="radio" name="radio01">
                            磋商</label>
                        <label class="label">
                            <input type="radio" name="radio01">
                            带看</label>
                        <label class="label">
                            <input type="radio" name="radio01">
                            其他</label>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">带看员工：</p>
                    <p class="i_text">王大鹏</p>
                    <div class="left">
                        <p class="t_text">客户类型：</p>
                        <div class="i_text">
                            <label class="label">
                                <input type="radio" name="radio02">
                                求购</label>
                            <label class="label">
                                <input type="radio" name="radio02">
                                求租</label>
                        </div>
                    </div>
                    <div class="left">
                        <p class="t_text">客户姓名：</p>
                        <input type="text" class="k_input" readonly onFocus="openWin('js_keyuan')">
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进内容：</p>
                    <textarea class="textarea"></textarea>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_label">
                        <label>
                            <input type="checkbox">
                            提醒</label>
                    </p>
                </div>
                <div class="inner_in">
                    <div class="in_fg clearfix">
                        <p class="text_t">提醒日期：</p>
                        <input class="input_text w90" type="text">
                    </div>
                    <div class="in_fg clearfix">
                        <p class="text_t">提醒内容：</p>
                        <input class="input_text w570"  type="text">
                    </div>
                </div>
            </div>
            <a class="save_btn" href="javascript:void(0)">保存</a> </div>
    </div>
</div>
<!--我的客源-->
<div class="pop_box_g pop_box_g03" id="js_keyuan">
    <div class="hd">
        <div class="title">我的客源</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="inner inner02">
            <div class="inner_ky_box">
                <div class="title">客户姓名：
                    <input type="text" class="input_t">
                    <button class="btn">查询</button>
                </div>
                <table class="table">
                    <tr>
                        <th class="w45">&nbsp;</th>
                        <th class="w60">交易</th>
                        <th class="w160">客户编号</th>
                        <th class="w70">客户姓名</th>
                        <th>价格范围</th>
                    </tr>
                    <tr>
                        <td><input type="radio" name="radio3"></td>
                        <td>买</td>
                        <td>012910</td>
                        <td>徐大牛</td>
                        <td>200-300万元</td>
                    </tr>
                    <tr class="bg">
                        <td><input type="radio" name="radio3"></td>
                        <td>买</td>
                        <td>012910</td>
                        <td>徐大牛</td>
                        <td>200-300万元</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="radio3"></td>
                        <td>买</td>
                        <td>012910</td>
                        <td>徐大牛</td>
                        <td>200-300万元</td>
                    </tr>
                    <tr class="bg">
                        <td><input type="radio" name="radio3"></td>
                        <td>买</td>
                        <td>012910</td>
                        <td>徐大牛</td>
                        <td>200-300万元</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="radio3"></td>
                        <td>买</td>
                        <td>012910</td>
                        <td>徐大牛</td>
                        <td>200-300万元</td>
                    </tr>
                </table>
            </div>
            <div class="clearfix pop_fg_fun_box">
                <div class="get_page"><span>2/10页</span><a href="javascript:void(0)">上一页</a><a href="javascript:void(0)">下一页</a><a id="js_get_page_to04" href="javascript:void(0)">跳转</a>
                    <div class="f_input hide" id="js_f_input04"> <span class="tex">跳转到第</span>
                        <input type="text" class="input">
                        <span class="tex">页</span> <a href="javascript:void(0)" class="b_link">确定</a> </div>
                </div>
            </div>
            <a class="inner_ky_save_btn" href="javascript:void(0)">保存</a> </div>
    </div>
</div>
<!--分配任务-->
<div class="pop_box_g" id="js_fenpeirenwu">
    <div class="hd">
        <div class="title">分配任务</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="mod_zn_inner">
            <h3 class="title">跟进对象</h3>
            <div class="inner">
                <table class="table">
                    <tr>
                        <th class="w90">房源编号</th>
                        <th class="w90">物业类型</th>
                        <th class="w80">区属</th>
                        <th class="w90">板块</th>
                        <th class="w170">楼盘</th>
                        <th class="w80">户型</th>
                        <th class="w80">面积(㎡)</th>
                        <th>报价(W)</th>
                    </tr>
                    <tr>
                        <td>00000001</td>
                        <td>住宅</td>
                        <td>鼓楼区</td>
                        <td>华侨路</td>
                        <td>怡景花园</td>
                        <td>3-2-1</td>
                        <td>100</td>
                        <td>300</td>
                    </tr>
                    <tr class="bg">
                        <td>00000001</td>
                        <td>住宅</td>
                        <td>鼓楼区</td>
                        <td>华侨路</td>
                        <td>怡景花园</td>
                        <td>3-2-1</td>
                        <td>100</td>
                        <td>300</td>
                    </tr>
                    <tr>
                        <td>00000001</td>
                        <td>住宅</td>
                        <td>鼓楼区</td>
                        <td>华侨路</td>
                        <td>怡景花园</td>
                        <td>3-2-1</td>
                        <td>100</td>
                        <td>300</td>
                    </tr>
                </table>
            </div>
            <div class="clear">&nbsp;</div>
            <div class="inner inner02">
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务分配人：</p>
                    <p class="i_text">21世纪不动产总店</p>
                    <p class="t_text">厉小塔</p>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务执行人：</p>
                    <p class="i_text">
                        <select class="select">
                            <option>21世纪不动产总店</option>
                        </select>
                    <p class="left">&nbsp;&nbsp;&nbsp;</p>
                    <select class="select">
                        <option>厉小塔</option>
                    </select>
                    </p>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">任务类型：</p>
                    <div class="i_text">
                        <label class="label">
                            <input type="radio" name="radio04">
                            房源跟进</label>
                        <label class="label">
                            <input type="radio" name="radio04">
                            客源跟进</label>
                    </div>
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">执行期限：</p>
                    <input type="text"  class="k_input">
                </div>
                <div class="item_fg_h clearfix">
                    <p class="t_text">跟进内容：</p>
                    <textarea class="textarea"></textarea>
                </div>
            </div>
            <a class="save_btn" href="javascript:void(0)">保存</a> </div>
    </div>
</div>
<!--我要申诉-->
<div class="pop_box_g appeal_bg" id="js_woyaoshensu">
	<div class="hd">
        <div class="title">我要申诉</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
   <div class="appeal_main">
    <div class="appeal_content">
    	<div class="appeal_top">
    		<table class="table appeal_detail">
    			<tr>
	    			<td class="apl_name">王晓仁</td>
	    			<td><div class="apl_zizhi">10年资质</div></td>
	    			<td>图标显示不了</td>
	    			<td>图标</td>
	    			<td>图标</td>
    			</tr>
			</table>
			<div class="appeal_adr">182390403456   21世纪不动产陆家嘴店</div>
    	</div>
    	<!--经纪人信用评价-->
    	<div class="credit_title">
    		<span>经纪人信用评价</span><div class="credit_rate">好评率：99.22%</div>
    	</div>
    	<table class="table ctrcenter">
    		<tr class="ctrbg">
    			<td class="cd200">总数</td><td class="cd200">好评</td><td class="cd200">中评</td><td class="cd200">差评</td>
    		</tr>
    		<tr>
    			<td class="cd200">120</td><td class="cd200 cblue">119</td><td class="cd200 cblue">1</td><td class="cd200 cblue">0</td>
    		</tr>
    		<tr class="ctrbg ctrleft">
    			<td colspan="4"><span>合作陈功率：</span><span class="cred">86.36%</span><span>图标&nbsp;&nbsp;</span><span>比平均值：</span><span class="cred">高9.15%</span></td>
    		 </tr>
    		 <tr>
    		   <td class="cd200">收到合作：<span class="cblue">12</span>次</td><td class="cd200">发起合作：<span class="cblue">12</span>次</td><td class="cd200">接受合作：<span class="cblue">12</span>次</td><td class="cd200">被接受合作：<span class="cblue">12</span>次</td>
    		</tr>
    	</table>
    	<!--经纪人动态评分-->
    	<div class="credit_title">经纪人动态评分</div>
    	<div class="broker_pf">
    		<ul class="pf_left">
    			<li class="pf_bg">
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span>4.5</span>分</div>
    				<div class="pf_bijiao"><span>比平均值</span><span class="high_low">高</span><span class="pf_bfb">9.15%</span></div>
    			</li>
    			<li>
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span>4.5</span>分</div>
    				<div class="pf_bijiao"><span>比平均值</span><span class="high_low">高</span><span class="pf_bfb">9.15%</span></div>
    			</li>
    			<li>
    				<div class="pf_taidu">信息真实度</div>
    				<div class="pf_socre">得分<span>4.5</span>分</div>
    				<div class="pf_bijiao"><span>比平均值</span><span class="high_low">高</span><span class="pf_bfb">9.15%</span></div>
    			</li>
    		</ul>
    		<div class="pf_right">
    			<div class="pf_con" style="display:block;">
    				<div class="pf_result"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span> 共245人</div>
    				<table class="table pf_score">
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td><td class="pfd20">5分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:90px;"></div>99%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></td><td class="pfd20">4分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:5px;"></div>5%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">3分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:3px;"></div>3%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">2分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:2px;"></div>2%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">1分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:1px;"></div>1%</td>
    					</tr>
    				</table>
    			</div>
 				<div class="pf_con">
    				<div class="pf_result"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span> （第二个）共245人</div>
    				<table class="table pf_score">
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td><td class="pfd20">5分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:90px;"></div>99%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></td><td class="pfd20">4分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:5px;"></div>5%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">3分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:3px;"></div>3%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">2分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:2px;"></div>2%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">1分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:1px;"></div>1%</td>
    					</tr>
    				</table>
    			</div>
    			<div class="pf_con">
    				<div class="pf_result"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span> （第三个）共245人</div>
    				<table class="table pf_score">
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span></td><td class="pfd20">5分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:90px;"></div>99%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></td><td class="pfd20">4分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:5px;"></div>5%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">3分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:3px;"></div>3%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">2分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:2px;"></div>2%</td>
    					</tr>
    					<tr>
    						<td class="pfd100"><span class="djicon dj100"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span><span class="djicon dj50"></span></td><td class="pfd20">1分</td>
    						<td class="pfd150"><div class="pf_bfbbg" style="width:1px;"></div>1%</td>
    					</tr>
    				</table>
    			</div>
    		</div>
    	</div>

    </div>
        	<!--来自合作方的评价-->
    	<ul class="partner_list">
    		<li calss="selbg">来自合作方的评价（123）</li>
    		<li>我给合作方的评价（456）</li>
    	</ul>
    	<table class="table partner_check">
    		<tr>
    			<td><input type="radio" name="pj">全部</td>
    			<td><input type="radio" name="pj">好评</td>
    			<td><input type="radio" name="pj">中评</td>
    			<td><input type="radio" name="pj">差评</td>
    		</tr>
    	</table>
    	<table class="table partner_box">
    		<tr calss="ctrbg">
    			<td class="pard1">交易编号</td>
    			<td class="pard2">合作房源</td>
    			<td class="pard3">整体评价</td>
    			<td class="pard4">细节评价</td>
    			<td class="pard5">评价内容</td>
    			<td class="pard6">评价时间</td>
    			<td class="pard7">评价人</td>
    		</tr>
    		<tr>
    			<td class="pard1">201411231212341234</td>
    			<td class="pard2">鼓楼区-三牌楼  天福园 3室2厅1卫精装 南 102㎡  200W</td>
    			<td class="pard3 good_pj">好评</td>
    			<td class="pard4">
    				<div class="pjxj">
    					<div class="pjxj_name">消息真实度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
    				<div class="pjxj">
    					<div class="pjxj_name">态度满意度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
    				<div class="pjxj">
    					<div class="pjxj_name">业务专业度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
				</td>
    			<td class="pard5">合作愉快，非常诚信下次再合作！</td>
    			<td class="pard6">2014-11-23 12:34:12</td>
    			<td class="pard7">评价人<br>
    				<span class="djicon pjper100"></span><span class="djicon pjper100"></span><span class="djicon pjper100"></span><span class="djicon pjper100"></span>
    			</td>
    		</tr>
    	    <tr calss="pardbg">
    			<td class="pard1">201411231212341234</td>
    			<td class="pard2">鼓楼区-三牌楼  天福园 3室2厅1卫精装 南 102㎡  200W</td>
    			<td class="pard3 good_pj">好评</td>
    			<td class="pard4">
    				<div class="pjxj">
    					<div class="pjxj_name">消息真实度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
    				<div class="pjxj">
    					<div class="pjxj_name">态度满意度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
    				<div class="pjxj">
    					<div class="pjxj_name">业务专业度</div>
    					<div class="pjxj_dj"><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj100"></span><span class="djicon dj50"></span></div>
    				</div>
				</td>
    			<td class="pard5">合作愉快，非常诚信下次再合作！</td>
    			<td class="pard6">2014-11-23 12:34:12</td>
    			<td class="pard7">评价人<br>
    				<span class="djicon pjper100"></span><span class="djicon pjper100"></span><span class="djicon pjper100"></span><span class="djicon pjper100"></span>
    			</td>
    		</tr>
    	</table>
</div>
   </div>
<!--我要举报-->
<div class="pop_box_g report_box" id="js_woyaojubao">
	<div class="hd">
        <div class="title">我要举报</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>

    <div class="report_box">
    	<div class="report_tips">
    		为了共同打造真实可靠的共享平台，举报经核实后将奖励您一定的积分、成长值
    		<span class="tipsicon">&#xe614;<div class="reicon_tips">积分、成长值有什么用？</div></span>
    	</div>
    	<form class="preort_form" action="" method="post">
    		<table class="table report_table">
    			<tr class="retrbg">
    				<td class="retdname">举报类型：</td>
    				<td>
    					<select name="jubaoleixing">
    						<option value="jubao1">举报类型一</option>
    						<option value="jubao2">举报类型二</option>
    					</select>
    				</td>
    			</tr>
    			<tr class="retrbg">
    				<td class="retdname">举报原因：</td>
    				<td>
    					<textarea name="" placeholder="请详细说明举报理由"></textarea>
    				</td>
    			</tr>
    			<tr><td>&nbsp;</td><td></td></tr>
    		</table>
    		<input type="submit" class="report_btn" value="举报" />
    	</form>
    </div>
</div>
<!--出售公盘-->
<div class="pop_box_g" id="js_sellhouse">
    <div class="hd">
        <div class="title">出售公盘</div>
        <div class="close_pop"><a href="javascript:void(0);" title="关闭" class="JS_Close iconfont">&#xe60c;</a></div>
    </div>
    <div class="mod">
        <div class="tab_pop_hd">
            <dl class="clearfix" id="js_tab_t02">
                <dd class="js_t item itemOn" title="房源详情">房源详情</dd>
                <dd class="js_t item" title="房源图片">房源图片</dd>
                <dd class="js_t item" title="小区概况">小区概况</dd>
            </dl>
        </div>
        <div class="tab_pop_mod clear" id="js_tab_b02">
            <div class="js_d inner" style="display:block;">
                <table class="table">
                    <tr>
                        <td class="w60 t_l">楼盘名称：</td>
                        <td class="w170">天润城第二街区</td>
                        <td class="w60 t_l">区属：</td>
                        <td class="w170">鼓楼</td>
                        <td class="w60 t_l">板块：</td>
                        <td>三牌楼</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">地址：</td>
                        <td class="w170">南京市浦口区柳洲东路9号区</td>
                        <td class="w60 t_l">状态：</td>
                        <td class="w170">有效</td>
                        <td class="w60 t_l">房源性质：</td>
                        <td>私盘</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">户型：</td>
                        <td class="w170">3室2厅1卫1厨1阳台</td>
                        <td class="w60 t_l">楼层：</td>
                        <td class="w170">9/14</td>
                        <td class="w60 t_l">朝向：</td>
                        <td>南</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">装修：</td>
                        <td class="w170">简装</td>
                        <td class="w60 t_l">房龄：</td>
                        <td class="w170">2010年</td>
                        <td class="w60 t_l">面积：</td>
                        <td>100平方米</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">售价：</td>
                        <td class="w170">200万元</td>
                        <td class="w60 t_l">单价：</td>
                        <td class="w170">20000元/平米</td>
                        <td class="w60 t_l">税费：</td>
                        <td>各付</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">委托协议：</td>
                        <td class="w170">已签</td>
                        <td class="w60 t_l">委托类型：</td>
                        <td class="w170">多家登记</td>
                        <td class="w60 t_l">类型：</td>
                        <td>多层</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">结构：</td>
                        <td class="w170">框架</td>
                        <td class="w60 t_l">产权：</td>
                        <td class="w170">商品房</td>
                        <td class="w60 t_l">物业费：</td>
                        <td>1.2元/月/m²</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">付款方式：</td>
                        <td class="w170">按揭</td>
                        <td class="w60 t_l">付佣方式：</td>
                        <td class="w170">给佣</td>
                        <td class="w60 t_l">证件：</td>
                        <td>无</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">现状：</td>
                        <td class="w170">空置房</td>
                        <td class="w60 t_l">看房：</td>
                        <td class="w170">提前预约</td>
                        <td class="w60 t_l">信息来源：</td>
                        <td>店面</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">房屋设施：</td>
                        <td colspan="5">水、电天然气、暖气、电话、电视、空调、家具、太阳能、洗衣机、热水器、油烟机、电冰箱、微波炉、橱柜、宽带、电梯、停车位、水、电天然气、暖气、电话、电视、空调、家具、太阳能、洗衣机、热水器、油烟机、电冰箱、微波炉、橱柜、宽带、电梯、停车位 </td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">周边环境：</td>
                        <td colspan="5">幼儿园、小学、中学、医院、银行、火车站、汽车站、地铁、停车场、超市、公园、菜场、商场、健身房、体育馆</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">交房时间：</td>
                        <td class="w170">2014-12-31</td>
                        <td class="w60 t_l">委托时间：</td>
                        <td class="w170">2014-12-31</td>
                        <td class="w60 t_l">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">备注：</td>
                        <td colspan="5">好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房位 </td>
                    </tr>
                </table>
            </div>
  			<div class="js_d inner inner02">
                <div class="show_house_pic">
                    <p class="title">室内图</p>
                    <div class="pic"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="220" width="340"> </div>
                    <div class="small_pic">
                        <div class="prev"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <li class="item active"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                            </ul>
                        </div>
                        <div class="next iconfont"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
                <div class="show_house_pic_fg">&nbsp;</div>
                <div class="show_house_pic">
                    <p class="title">室内图</p>
                    <div class="pic"> <img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="220" width="340"> </div>
                    <div class="small_pic">
                        <div class="prev prev_click"><span class="iconfont">&#xe607;</span></div>
                        <div class="list">
                            <ul class="clearfix">
                                <li class="item active"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                                <li class="item"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="54" width="72"></li>
                            </ul>
                        </div>
                        <div class="next iconfont"><span class="iconfont">&#xe607;</span></div>
                    </div>
                </div>
            </div>
            <div class="js_d inner">
                <table class="table">
                    <tr>
                        <td class="w60 t_l">楼盘名称：</td>
                        <td class="w170">天润城第二街区</td>
                        <td class="w60 t_l">区属：</td>
                        <td class="w170">鼓楼</td>
                        <td class="w60 t_l">板块：</td>
                        <td>三牌楼</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">地址：</td>
                        <td class="w170">南京市浦口区柳洲东路9号区</td>
                        <td class="w60 t_l">物业类型：</td>
                        <td class="w170">住宅</td>
                        <td class="w60 t_l">建筑年代：</td>
                        <td>2010年</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">产权年限：</td>
                        <td class="w170">70年</td>
                        <td class="w60 t_l">建筑面积：</td>
                        <td class="w170">123214平方米</td>
                        <td class="w60 t_l">占地面积：</td>
                        <td>123214平方米</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">物业公司：</td>
                        <td class="w170">中海物业</td>
                        <td class="w60 t_l">开发商：</td>
                        <td class="w170">中海地产</td>
                        <td class="w60 t_l">停车位：</td>
                        <td>地面车位充足 有1234地下车位</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">绿化率：</td>
                        <td class="w170">40%</td>
                        <td class="w60 t_l">容积率：</td>
                        <td class="w170">2.14</td>
                        <td class="w60 t_l">物业费：</td>
                        <td>2.2 元/月/m²</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">总栋数：</td>
                        <td class="w170">28</td>
                        <td class="w60 t_l">总户数：</td>
                        <td class="w170">1280</td>
                        <td class="w60 t_l">楼层状况：</td>
                        <td>1梯2户</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">楼盘简介：</td>
                        <td colspan="5">真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房真的是好房</td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">周边环境：</td>
                        <td colspan="5">幼儿园、小学、中学、医院、银行、火车站、汽车站、地铁、停车场、超市、公园、菜场、商场、健身房、体育馆 </td>
                    </tr>
                    <tr>
                        <td class="w60 t_l">图片：</td>
                        <td colspan="5"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="105" width="140">&nbsp;&nbsp;<img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/340_240.png" height="105" width="140"></td>
                    </tr>
                </table>
            </div>
		</div>
        <div class="tab_pop_bd">
            <div>
            	<a class="btn btn_del" href="javascript:void(0);"><span>&#xe616;</span>在线沟通</a>
            </div>
            <div>
            	<a class="btn btn_dell" href="javascript:void(0);">我要举报</a>
            	<a class="btn btn_dell" href="javascript:void(0);">智能匹配</a>
            	<a class="btn btn_dell" href="javascript:void(0);">查看跟进</a>
            	<a class="btn btn_dell" href="javascript:void(0);">我要收藏</a>
            	<a class="btn" href="javascript:void(0);">合作申请</a>
            </div>
        </div>
    </div>
</div>

<!--弹出框列表*ENDING*-->
<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=openWin.js,house.js,backspace.js "></script>
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/disk.js"></script>
</body>
</html>
