<?php header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pramga: no-cache"); ?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>无标题文档</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body style="overflow:auto;">

<div class="b_y_pic_box">
    <div class="inner">
        <div class="pic"><img alt="" src="<?php echo MLS_SOURCE_URL;?>/mls/images/v1.0/w_pic.png"></div>
        <div class="d_box">
            <table class="table">
                <tr>
                    <th>楼盘：</th>
                    <td><?php echo $list['block_name'];?></td>
                    <th>户型：</th>
                    <td><?php if($list['room']){echo $list['room']."室";}?>
                        <?php if($list['hall']){echo $list['hall']."厅";}?>
                        <?php if($list['toilet']){echo $list['toilet']."卫";}?>
                    </td>
                </tr>
                <tr>
                    <th>面积：</th>
                    <td><strong class="num"><?php echo $list['buildarea']; ?></strong>㎡</td>
                    <th>售价：</th>
                    <td><strong  class="num"><?php echo $list['price']; ?></strong>万</td>
                </tr>
                <tr>
                    <th>所在楼层：</th>
                    <td><?php echo $list['floor']; ?><?php if($list['floor_type']==2){ echo "-".$list['subfloor'];}?></td>
                    <th>总楼层：</th>
                    <td><?php echo $list['totalfloor']; ?></td>
                </tr>
                <tr>
                    <th>房龄：</th>
                    <td><?php echo $list['buildyear'];?>年</td>
                    <th>装修：</th>
                    <td><?php echo $config['fitment'][$list['fitment']]; ?></td>
                </tr>
                <tr>
                    <th>朝向：</th>
                    <td><?php echo $config['forward'][$list['forward']];?></td>
                    <th>房源编号：</th>
                    <td>CS<?php echo $list['id'];?></td>
                </tr>
                <tr>
                    <th>联系人：</th>
                    <td><?php echo $list['broker_name']; ?></td>
                    <th>联系电话：</th>
                    <td><?php echo $list['telno1']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=house.js,openWin.js,backspace.js"></script>
</body>
</html>
