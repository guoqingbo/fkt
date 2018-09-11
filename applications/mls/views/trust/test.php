<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>公盘公客</title>
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
<link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/guest_disk.css " rel="stylesheet" type="text/css">
<script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body >
	<!--描述：导航栏开始 -->
<div class="tab_box" id="js_tab_box">
	<a href="公盘公客.html" class="link link_on"><span class="iconfont">&#xe62e;</span>公盘公客</a>
	<a href="公盘公客--我的订阅.html" class="link"><span class="iconfont">&#xe60b;</span>我的订阅</a>
	<a href="公盘公客--我的收藏.html" class="link"><span class="iconfont">&#xe613;</span>我的收藏</a>
</div>
<!--描述：导航栏结束-->
<!--描述：分类导航栏开始-->
<div id="js_search_box" class="shop_tab_title">
			<a href="#" class="link">出售<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link">出租<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link">求购<span class="iconfont hide">&#xe607;</span></a>
   <a href="#" class="link link_on">求组<span class="iconfont hide">&#xe607;</span></a>
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
        <div class="fg"> <a href="javascript:void(0)" class="btn" ><span class="btn_inner">搜索</span></a> </div>
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
                <td class="c2">
                    <div class="info">
                        <input type="checkbox" id="js_checkbox">
                    </div>
                </td>
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
                	<div class="info">
                        <a href="javascript:void(0)" class="i_text i_down">报价<br>(W)</a>
                    </div>
                </td>
                <td class="c6">
                	<div class="info">
                        <a href="javascript:void(0)" class="i_text i_up">单价<br> (元/㎡)</a>
                    </div>
                </td>
                <td class="c7"><div class="info">委托<br>经纪人</div></td>
                <td class="c7"><div class="info">联系方式</div></td>
                <td class="c4"><div class="info">好评率</div></td>
                <td class="c4"><div class="info">合作<br>成功率</div></td>
                <td class="c6"><div class="info">跟进时间</div></td>
                <td colspan="3"><div class="info">操作</div></td>
            </tr>
        </table>
    </div>
    <div  id="js_inner" style="overflow-y: scroll;">
        <table class="inner table" id="js_table_box_Sincerity">
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
                <td class="c7 js_info" data-brokerId="7"><div class="info">张晓明<span class="onper onper_none iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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
                <td class="c7"><div class="info">张晓明<span class="onper iconfont">&#xe616;</span></div></td>
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

    <!--

    	描述：我要申诉
    -->
    <li onClick="openWin('js_woyaoshensu')">我要申诉</li>
</ul>
<img src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/loading.gif" id="mainloading" ><!--遮罩 loading-->
<!--描述：右击弹出列表页面*END*-->
<!--弹出框列表*STARTING*-->
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
                        <th width="87">报价(W)</th>
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
            <a class="btn-lv1 btn-mid" href="javascript:void(0)">保存</a>
		</div>
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
    		为了共同打造真实可靠的合作平台，举报经核实后将奖励您一定的积分、成长值
    		<span class="tipsicon iconfont">&#xe614;<div class="reicon_tips">积分、成长值有什么用？</div></span>
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
    		<input type="submit" class="btn-lv1 btn-mid" value="举报" />
    	</form>
    </div>
</div>


<!--HOVER 弹出信息框-->

<div class="box_Sincerity" id="js_box_Sincerity">
</div>
</body>
</html>
