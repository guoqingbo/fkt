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
          <?php if($list){ foreach($list as $key => $val) {?>
         <div class="item">
            <p class="p"><?php echo $val['block_name'];?></p>
            <p class="p"><?php if($val['room']){echo $val['room']."室";}?>
                         <?php if($val['hall']){echo $val['hall']."厅";}?>
                         <?php if($val['toilet']){echo $val['toilet']."卫";}?></p>
            <p class="p"><?php echo $val['buildarea']; ?>㎡</p>
            <p class="p"><strong class="num"><?php echo $val['price']; ?></strong>万</p>
         </div>
          <?php }
          } else {?>
              <span class="no-data-tip">抱歉，没有找到符合条件的信息</span>
          <?php }?>
      </div>
   </div>
</div>

<script src="<?php echo MLS_SOURCE_URL;?>/min/?b=mls/js/v1.0&f=house.js,openWin.js,backspace.js"></script>
</body>
</html>
