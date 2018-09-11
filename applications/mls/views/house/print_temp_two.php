<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>无标题文档</title>
    <link href="<?php echo MLS_SOURCE_URL;?>/min/?b=mls&f=css/v1.0/base.css,third/iconfont/iconfont.css,css/v1.0/house_manage.css " rel="stylesheet" type="text/css">
    <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js"></script>
</head>

<body style="overflow:auto;">

<div class="b_y_l_box">
<div class="m_box">
<div class="hd">
    <div class="left tex"><span class="text">公司名称：</span></div>
    <div class="left tex"><span class="text">联系电话：</span></div>
</div>
<div class="mod clearfix">
    <div class="table_box">
        <table class="table">
            <tr>
                <th>
                    <div class="info">
                        <p class="left w110">物业名称</p>
                        <p class="left w65">户型</p>
                        <p class="left w62">面积</p>
                        <p class="left w65">售价</p>
                    </div>
                </th>
                <th>
                    <div class="info">
                        <p class="left w110">物业名称</p>
                        <p class="left w65">户型</p>
                        <p class="left w62">面积</p>
                        <p class="left w65">售价</p>
                    </div>
                </th>
                <th>
                    <div class="info">
                        <p class="left w110">物业名称</p>
                        <p class="left w65">户型</p>
                        <p class="left w62">面积</p>
                        <p class="left w65">售价</p>
                    </div>
                </th>
            </tr>
            <?php foreach($list as $key => $val) {?>
                <?php if($key%3 == 0){?>
            <tr  <?php if($key%6 == 3){?>class="bg"<?php }?>>
                <td>
                    <div class="info">
                        <p class="left w110"><?php echo $val['block_name'];?></p>
                        <p class="left w65"><?php if($val['room']){echo $val['room']."室";}?>
                            <?php if($val['hall']){echo $val['hall']."厅";}?>
                            <?php if($val['toilet']){echo $val['toilet']."卫";}?></p>
                        <p class="left w62"><?php echo $val['buildarea']; ?>㎡</p>
                        <p class="left w65"><strong class="num"><?php echo $val['price']; ?></strong>万</p>
                    </div>
                </td>
                    <?php if($totalnum%3 == 1 && $key == $totalnum-1){?>
                        <td>
                            <div class="info">
                            </div>
                        </td>
                        <td>
                            <div class="info">
                            </div>
                        </td>
                        </tr>
                    <?php }?>
                <?php }?>
                <?php if($key%3 == 1){?>
                <td>
                    <div class="info">
                        <p class="left w110"><?php echo $val['block_name'];?></p>
                        <p class="left w65"><?php if($val['room']){echo $val['room']."室";}?>
                            <?php if($val['hall']){echo $val['hall']."厅";}?>
                            <?php if($val['toilet']){echo $val['toilet']."卫";}?></p>
                        <p class="left w62"><?php echo $val['buildarea']; ?>㎡</p>
                        <p class="left w65"><strong class="num"><?php echo $val['price']; ?></strong>万</p>
                    </div>
                </td>
                    <?php if($totalnum%3 == 2 && $key == $totalnum-1){?>
                        <td>
                            <div class="info">
                            </div>
                        </td>
                        </tr>
                    <?php }?>
                <?php }?>
                <?php if($key%3 == 2){?>
                <td>
                    <div class="info">
                        <p class="left w110"><?php echo $val['block_name'];?></p>
                        <p class="left w65"><?php if($val['room']){echo $val['room']."室";}?>
                            <?php if($val['hall']){echo $val['hall']."厅";}?>
                            <?php if($val['toilet']){echo $val['toilet']."卫";}?></p>
                        <p class="left w62"><?php echo $val['buildarea']; ?>㎡</p>
                        <p class="left w65"><strong class="num"><?php echo $val['price']; ?></strong>万</p>
                    </div>
                </td>
            </tr>
                <?php }?>

            <?php } ?>


        </table>

    </div>

</div>
</div>
</div>


<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=house.js,openWin.js,backspace.js"></script>
</body>
</html>
