<script>
    window.parent.addNavClass(11);
</script>

<div class="tab_box" id="js_tab_box">
    <?php
    echo $user_menu;
    ?>
</div>
<div id="js_search_box" class="shop_tab_title">
    <?php if(isset($user_func_menu) && $user_func_menu != ''){ echo $user_func_menu;}?>
</div>
<div class="company-info-wrap">
    <table>
        <tbody>
            <tr>
                <td class="label">公司名称</td>
                <td><?php echo $name; ?></td>
                <td class="label">公司地址</td>
                <td><?php echo $address; ?></td>
            </tr>
            <tr class="bg">
                <td class="label">所在城市</td>
                <td><?php echo $city; ?></td>
                <td class="label">联系人</td>
                <td><?php echo $linkman; ?></td>
            </tr>
            <tr>
                <td class="label">联系电话</td>
                <td><?php echo $telno; ?></td>
                <td class="label">邮编</td>
                <td><?php echo $zip_code; ?></td>
            </tr>
            <tr class="bg">
                <td class="label">传真</td>
                <td><?php echo $fax; ?></td>
                <td class="label">邮箱</td>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <td class="label">网址</td>
                <td colspan="3"><?php echo $website; ?></td>
            </tr>
        </tbody>
    </table>
</div>
</body>
