<script src="<?php echo MLS_SOURCE_URL; ?>/min/?f=mls/js/v1.0/json2.js" type="text/javascript"></script>
<script>
    window.parent.addNavClass(2);
</script>
<form method='post' action='' id='search_form' name="search_form">
    <div class="tab_box" id="js_tab_box">
        <a class="link link_on">配置发布房源字段</a>
    </div>
    <div class="table_all">
        <div class="inner" id="js_innerHouse" style="height:auto;">
            <table class="table table_q">
                <?php
                    foreach($list as $key => $val)
                    {
                        $tdclass = 1 == $key % 2 ? 'bg' : '';
                ?>
                <tr class="<?php echo $tdclass; ?>" date-url="/house/set_field/<?php echo $key; ?>">
                    <td class="c1">
                        <div class="info">
                            <input type="checkbox" name="items" value="<?php echo $key;?>" class="checkbox" style="display:none;">
                        </div>
                    </td>
                    <td class="c1"><div class="info"><?php echo $val; ?></div></td>
                    <td class="c4"><div class="info">配置</div></td>
                </tr>
                <?php
                    }
                ?>
            </table>
        </div>
    </div>
</form>
<!--配置房源字段弹框-->
<div id="js_pop_box_g" class="iframePopBox" style=" width:816px; height:540px; ">
    <input type="hidden" value="" id="window_min_name"/>
    <input type="hidden" value="" id="window_min_url"/>
    <input type="hidden" value="" id="window_min_id"/>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" style="right:46px;display:none;" id="window_min_click">一</a>
    <a class="JS_Close close_pop iconfont" href="javascript:void(0)" date-iframe="1" id="window_min_close">&#xe60c;</a>
    <iframe frameborder="0" name="detialIframe" scrolling="no" width="816" height="540" class='iframePop detialIframe' id="detialIframe" src=""></iframe>
</div>

<ul id="openList">
    <input type="hidden" id="right_id" class="js_input">
    <!--右键菜单-->
    <li onClick="editHouseField();" class="js_input_2">配置房源字段</li>
</ul>

<script>
function editHouseField() {
    var sell_type = $("#right_id").val();
    var _url = '/house/set_field/' + sell_type;
    if (_url) {
        $("#js_pop_box_g .iframePop").attr("src", _url);
    }
    openWin('js_pop_box_g');
}
$(function(){

})
</script>