<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//58同城采集城市
$config['58_city'] = array(
  'nj' => 178,         //南京
  'km' => 541,         //昆明
  'sz' => 5,           //苏州
  'hz' => 79,          //杭州
  'xa' => 483,         //西安
  'cq' => 37,          //重庆
  'hrb' => 202,        //哈尔滨
  'cd' => 102,         //成都
  'taizhou' => 23550,  //泰州
  'langfang' => 774,   //廊坊
  'zhongshan' => 771,  //中山
  'zhuhai' => 910,     //珠海
  'huizhou' => 722,    //惠州
  'wuxi' => 93,        //无锡
  'wuhan' => 158,      //武汉
  'guiyang' => 2015,   //贵阳
  'xiamen' => 606,     //厦门
  'quanzhou' => 291,   //泉州
  'zhangzhou' => 710,  //漳州
  'fuzhou' => 304,     //福州
  'songyuan' => 2315,  //松原
  'shanghai' => 2,     //上海
  'beijing' => 1,      //北京
  'taiyuan' => 740,    //太原
  'wulumuqi' => 984,   //乌鲁木齐
  'haikou' => 2053,    //海口
  'guangzhou' => 3,    //广州
  'shenzhen' => 4,     //深圳
  'changchun' => 319,  //长春
  'jinan' => 265,      //济南
  'tianjin' => 18,     //天津
  'shijiazhuang' => 241,        //石家庄
  'huhehaote' => 811,       //呼和浩特
  'nanning' => 845,    //南宁
  'lanzhou' => 952,    //兰州
  'changsha' => 414,   //长沙
  'shenyang' => 188,   //沈阳
  'nanchang' => 669,   //南昌
  'lasa' => 2055,      //拉萨
  'zhengzhou' => 342,  //郑州
  'qingdao' => 122,    //青岛
  'yinchuan' => 2054,   //银川
  'xining' => 2052     //西宁
);

//赶集网采集城市
$config['ganji_city'] = array(
  'nj' => 'nj',                //南京
  'km' => 'km',                //昆明
  'sz' => 'su',                //苏州
  'hz' => 'hz',                //杭州
  'xa' => 'xa',                //西安
  'cq' => 'cq',                //重庆
  'hrb' => 'hrb',              //哈尔滨
  'cd' => 'cd',                //成都
  'taizhou' => 'jstaizhou',    //泰州
  'langfang' => 'langfang',    //廊坊
  'zhongshan' => 'zhongshan',  //中山
  'zhuhai' => 'zhuhai',        //珠海
  'huizhou' => 'huizhou',      //惠州
  'wuxi' => 'wx',              //无锡
  'wuhan' => 'wh',             //武汉
  'guiyang' => 'gy',           //贵阳
  'xiamen' => 'xm',            //厦门
  'quanzhou' => 'quanzhou',    //泉州
  'zhangzhou' => 'zhangzhou',  //漳州
  'fuzhou' => 'fz',            //福州
  'songyuan' => 'songyuan',    //松原
  'shanghai' => 'sh',                //上海
  'beijing' => 'bj',           //北京
  'taiyuan' => 'ty',           //太原
  'wulumuqi' => 'xj',          //乌鲁木齐
  'haikou' => 'hn',            //海口
  'guangzhou' => 'gz',         //广州
  'shenzhen' => 'sz',          //深圳
  'changchun' => 'cc',         //长春
  'jinan' => 'jn',             //济南
  'tianjin' => 'tj',           //天津
  'shijiazhuang' => 'sjz',              //石家庄
  'huhehaote' => 'nmg',              //呼和浩特
  'nanning' => 'nn',           //南宁
  'lanzhou' => 'lz',           //兰州
  'changsha' => 'cs',          //长沙
  'shenyang' => 'sy',          //沈阳
  'nanchang' => 'nc',          //南昌
  'lasa' => 'xz',            //拉萨
  'zhengzhou' => 'zz',         //郑州
  'qingdao' => 'qd',           //青岛
  'yinchuan' => 'yc',   //银川
  'xining' => 'xn'      //西宁
);

//58采集小区城市
$config['58_xiaoqu'] = array(
  'nj' => 'nj',                //南京
  'km' => 'km',                //昆明
  'sz' => 'su',                //苏州
  'hz' => 'hz',                //杭州
  'xa' => 'xa',                //西安
  'cq' => 'cq',                //重庆
  'hrb' => 'hrb',              //哈尔滨
  'cd' => 'cd',                //成都
  'taizhou' => 'taizhou',      //泰州
  'langfang' => 'lf',          //廊坊
  'zhongshan' => 'zs',         //中山
  'zhuhai' => 'zh',            //珠海
  'huizhou' => 'huizhou',      //惠州
  'wuxi' => 'wx',              //无锡
  'wuhan' => 'wh',             //武汉
  'guiyang' => 'gy',           //贵阳
  'xiamen' => 'xm',            //厦门
  'quanzhou' => 'qz',          //泉州
  'zhangzhou' => 'zhangzhou',  //漳州
  'fuzhou' => 'fz',            //福州
  'songyuan' => 'songyuan',    //松原
  'shanghai' => 'sh',                //上海
  'beijing' => 'bj',           //北京
  'taiyuan' => 'ty',           //太原
  'wulumuqi' => 'xj',          //乌鲁木齐
  'haikou' => 'haikou',        //海口
  'guangzhou' => 'gz',         //广州
  'shenzhen' => 'sz',          //深圳
  'changchun' => 'cc',         //长春
  'jinan' => 'jn',             //济南
  'tianjin' => 'tj',           //天津
  'shijiazhuang' => 'sjz',              //石家庄
  'huhehaote' => 'hu',              //呼和浩特
  'nanning' => 'nn',           //南宁
  'lanzhou' => 'lz',           //兰州
  'changsha' => 'cs',          //长沙
  'shenyang' => 'sy',          //沈阳
  'nanchang' => 'nc',          //南昌
  'lasa' => 'lasa',            //拉萨
  'zhengzhou' => 'zz',         //郑州
  'qingdao' => 'qd',           //青岛
  'yinchuan' => 'yinchuan',   //银川
  'xining' => 'xn'            //西宁
);


//58采集小区城市
$config['soufang_xiaoqu'] = array(
  'nj' => 'nanjing',           //南京
  'km' => 'km',                //昆明
  'sz' => 'suzhou',            //苏州
  'hz' => 'hz',                //杭州
  'xa' => 'xian',              //西安
  'cq' => 'cq',                //重庆
  'hrb' => 'hrb',              //哈尔滨
  'cd' => 'cd',                //成都
  'taizhou' => 'taizhou',      //泰州
  'langfang' => 'lf',          //廊坊
  'zhongshan' => 'zs',         //中山
  'zhuhai' => 'zh',            //珠海
  'huizhou' => 'huizhou',      //惠州
  'wuxi' => 'wuxi',            //无锡
  'wuhan' => 'wuhan',          //武汉
  'guiyang' => 'gy',           //贵阳
  'xiamen' => 'xm',            //厦门
  'quanzhou' => 'qz',          //泉州
  'zhangzhou' => 'zhangzhou',  //漳州
  'songyuan' => 'songyuan',    //松原
  'fuzhou' => 'fz',            //福州
  'shanghai' => 'sh',                //上海
  'beijing' => '',             //北京
  'taiyuan' => 'taiyuan',      //太原
  'wulumuqi' => 'xj',          //乌鲁木齐
  'haikou' => 'hn',            //海口
  'guangzhou' => 'gz',         //广州
  'shenzhen' => 'sz',          //深圳
  'changchun' => 'changchun',  //长春
  'jinan' => 'jn',             //济南
  'tianjin' => 'tj',           //天津
  'shijiazhuang' => 'sjz',              //石家庄
  'huhehaote' => 'nm',              //呼和浩特
  'nanning' => 'nn',           //南宁
  'lanzhou' => 'lz',           //兰州
  'changsha' => 'cs',          //长沙
  'shenyang' => 'sy',          //沈阳
  'nanchang' => 'nc',          //南昌
  //'lasa'=>'lasa',          //拉萨
  'zhengzhou' => 'zz',         //郑州
  'qingdao' => 'qd',           //青岛
  'yinchuan' => 'yinchuan',   //银川
  'xining' => 'xn',           //西宁
);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

