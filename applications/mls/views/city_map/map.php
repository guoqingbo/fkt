<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<style type="text/css">
	#allmap {width: 100%;  overflow: hidden;margin:0;}
	#l-map{height:100%;width:78%;float:left;border-right:2px solid #bcbcbc;}
	#r-result{height:100%;width:20%;float:left;}
</style>
  <script src="<?php echo MLS_SOURCE_URL;?>/min/?f=mls/js/v1.0/jquery-1.8.3.min.js" type="text/javascript"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.5&ak=s4xTcbCABxjTGG3EfdZpQxaT"></script>
<title><?=$title?>业务工具--<?php echo $city_name.'市';?>地图</title>
</head>
<body>
    <div class="tab_box" id="js_tab_box">
        <?php
            echo $user_menu;
        ?>
    </div>
	<div>
		<div id="allmap"></div>
	</div>

	<script type="text/javascript">
		var lng = "<?php echo $lng;?>";
		var lat = "<?php echo $lat;?>";
		function buildMap(arrMapParameter)
		{
			var map = new BMap.Map("allmap");                // 创建Map实例
			var point = new BMap.Point(arrMapParameter.lng, arrMapParameter.lat);     // 创建点坐标
			map.centerAndZoom(point, arrMapParameter.zoom);                     // 初始化地图,设置中心点坐标和地图级别。
			map.enableScrollWheelZoom();                     //启用滚轮放大缩小
			map.addControl(new BMap.NavigationControl());
			map.addControl(new BMap.ScaleControl());
			map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_SATELLITE_MAP,BMAP_HYBRID_MAP ]}));
			map.addControl(new BMap.OverviewMapControl());              //添加默认缩略地图控件
			map.addControl(new BMap.OverviewMapControl({isOpen:true, anchor: BMAP_ANCHOR_BOTTOM_RIGHT}));   //右上角，打开

			return map;
		}
		$('document').ready(function(){
			function map_check(){
				var wH = $(window).height();
				var iH = wH - 58;
				$('#allmap').css('height',iH);
			}
			map_check();
			//生成地图
			buildMap({'lng' : lng, 'lat' : lat, 'zoom' : 12});
		});


	</script>
</body>
</html>
