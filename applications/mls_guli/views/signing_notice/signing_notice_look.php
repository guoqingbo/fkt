<script>
    window.parent.addNavClass(1);
</script>
<div class="tab_box" id="js_tab_box">
    <?php if (isset($user_menu) && $user_menu != '') {
        echo $user_menu;
    } ?>
</div>
<!--<div id="js_search_box">-->
<!--    <div  class="shop_tab_title">-->
<!--        --><?php //if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
<!--    </div>-->
<!--</div>-->
<!--<a href="javascript:void(0);" class="btn-lv" style="position:absolute; top:48px; right:20px;" onclick="add_notice_pop();"><span>发布公告</span></a>-->
<a href="javascript:void(0);" class="btn-lv" style="position:absolute; top:48px; right:20px;"
   onclick="location.href='/signing_notice/index/'"><span>返回</span></a>


<div class="inner shop_inner" id="js_inner" style="overflow: auto">
    <div class="table-notice">
        <table>
            <tr>
                <td class="td-left"><span style="width: 50px">分类：</span></td>
                <td class="">
                    <?= $config["notice_type"][$notice_detail['notice_type']]; ?>
                </td>
            </tr>
            <tr>
                <td class="td-left">收件对象：</td>
                <td>
                    <?= $notice_detail['receipt_object_name']; ?>
                </td>
            </tr>
            <tr>
                <td class="td-left">公告文号：</td>
                <td>
                    <?= $notice_detail['notice_number']; ?>
                </td>
            </tr>
            <tr>
                <td class="td-left">标题：</td>
                <td>
                    <?= $notice_detail['title']; ?>
                </td>
            </tr>
            <!--            <tr>-->
            <!--                <td class="td-left">置顶排序：</td>-->
            <!--                <td>-->
            <!--                    --><? //= $notice_detail['top_rank']; ?><!--"-->
            <!--                </td>-->
            <!--            </tr>-->
            <!--            <tr>-->
            <!--                <td class="td-left">置顶过期时间：</td>-->
            <!--                <td>--><? //= $notice_detail['top_rank_deadline']; ?><!--"-->
            <!--                </td>-->
            <!--            </tr>-->

            <tr>
                <td class="td-left">附件：</td>
                <td><a href="<?= $notice_detail['attachment']; ?>"
                       target="_blank"><?= $notice_detail['attachment_name']; ?></a>
                </td>
            </tr>
            <tr>
                <td class="td-left">内容：</td>
                <td><?= $notice_detail['contents']; ?>
                </td>
            </tr>

        </table>
    </div>
</div>
