<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 群发站点 城市 配置
 */
class CitySite
{
  //成都、重庆、合肥、杭州、昆明、南京、上海、哈尔滨、
  //苏州、西安、惠州、泰州、廊坊、中山、珠海、无锡、
  //武汉、贵阳、厦门、泉州、福州、漳州、松原、北京、
  //青岛、济南、海口、广州、深圳、南昌、长春、呼和浩特
  //天津、太原、郑州、兰州、长沙、沈阳、南宁、乌鲁木齐
  //拉萨、银川、西宁、淮安、潍坊、宁德、温州、石家庄
  //柳州、萍乡

  //赶集 城市
  public function ganji_city($city)
  {
    $data = array(
      'cd' => 'cd', 'cq' => 'cq', 'hf' => 'hf', 'hz' => 'hz', 'km' => 'km', 'nj' => 'nj', 'sh' => 'sh', 'hrb' => 'hrb',
      'sz' => 'su', 'xa' => 'xa', 'huizhou' => 'huizhou', 'taizhou' => 'jstaizhou', 'langfang' => 'langfang', 'zhongshan' => 'zhongshan', 'zhuhai' => 'zhuhai', 'wuxi' => 'wx',
      'wuhan' => 'wh', 'guiyang' => 'gy', 'xiamen' => 'xm', 'quanzhou' => 'quanzhou', 'fuzhou' => 'fz', 'zhangzhou' => 'zhangzhou', 'songyuan' => 'songyuan', 'beijing' => 'bj',
      'qingdao' => 'qd', 'jinan' => 'jn', 'haikou' => 'hn', 'guangzhou' => 'gz', 'shenzhen' => 'sz', 'nanchang' => 'nc', 'changchun' => 'cc', 'huhehaote' => 'nmg',
      'tianjin' => 'tj', 'taiyuan' => 'ty', 'zhengzhou' => 'zz', 'lanzhou' => 'lz', 'changsha' => 'cs', 'shenyang' => 'sy', 'nanning' => 'nn', 'wulumuqi' => 'xj',
      'lasa' => 'xz', 'yinchuan' => 'yc', 'xining' => 'xn', 'huaian' => 'huaian', 'weifang' => 'weifang', 'ningde' => 'ningde', 'wenzhou' => 'wenzhou', 'shijiazhuang' => 'sjz',
      'liuzhou' => 'liuzhou', 'pingxiang' => 'pingxiang', 'zhangjiakou' => 'zhangjiakou'
    );
    if (empty($data[$city])) {
      return false;
    } else {
      return $data[$city];
    }
  }

  //58 城市
  public function wuba_city($city)
  {
    $data['spell'] = array(
      'cd' => 'cd', 'cq' => 'cq', 'hf' => 'hf', 'hz' => 'hz', 'km' => 'km', 'nj' => 'nj', 'sh' => 'sh', 'hrb' => 'hrb',
      'sz' => 'su', 'xa' => 'xa', 'huizhou' => 'huizhou', 'taizhou' => 'taizhou', 'langfang' => 'lf', 'zhongshan' => 'zs', 'zhuhai' => 'zh', 'wuxi' => 'wx',
      'wuhan' => 'wh', 'guiyang' => 'gy', 'xiamen' => 'xm', 'quanzhou' => 'qz', 'fuzhou' => 'fz', 'zhangzhou' => 'zhangzhou', 'songyuan' => 'songyuan', 'beijing' => 'bj',
      'qingdao' => 'qd', 'jinan' => 'jn', 'haikou' => 'haikou', 'guangzhou' => 'gz', 'shenzhen' => 'sz', 'nanchang' => 'nc', 'changchun' => 'cc', 'huhehaote' => 'hu',
      'tianjin' => 'tj', 'taiyuan' => 'ty', 'zhengzhou' => 'zz', 'lanzhou' => 'lz', 'changsha' => 'cs', 'shenyang' => 'sy', 'nanning' => 'nn', 'wulumuqi' => 'xj',
      'lasa' => 'lasa', 'yinchuan' => 'yinchuan', 'xining' => 'xn', 'huaian' => 'ha', 'weifang' => 'wf', 'ningde' => 'nd', 'wenzhou' => 'wz', 'shijiazhuang' => 'sjz',
      'liuzhou' => 'liuzhou', 'pingxiang' => 'px', 'zhangjiakou' => 'zjk'
    );
    $data['id'] = array(
      'cd' => 102, 'cq' => 37, 'hf' => 837, 'hz' => 79, 'km' => 541, 'nj' => 172, 'sh' => 2, 'hrb' => 202,
      'sz' => 5, 'xa' => 483, 'huizhou' => 722, 'taizhou' => 693, 'langfang' => 772, 'zhongshan' => 771, 'zhuhai' => 910, 'wuxi' => 93,
      'wuhan' => 158, 'guiyang' => 2015, 'xiamen' => 606, 'quanzhou' => 291, 'fuzhou' => 304, 'zhangzhou' => 710, 'songyuan' => 2315, 'beijing' => 1,
      'qingdao' => 122, 'jinan' => 265, 'haikou' => 2053, 'guangzhou' => 3, 'shenzhen' => 4, 'nanchang' => 669, 'changchun' => 319, 'huhehaote' => 811,
      'tianjin' => 18, 'taiyuan' => 740, 'zhengzhou' => 342, 'lanzhou' => 952, 'changsha' => 414, 'shenyang' => 188, 'nanning' => 845, 'wulumuqi' => 984,
      'lasa' => 2055, 'yinchuan' => 2054, 'xining' => 2052, 'huaian' => 968, 'weifang' => 362, 'ningde' => 7951, 'wenzhou' => 330, 'shijiazhuang' => 241,
      'liuzhou' => 7133, 'pingxiang' => 2248, 'zhangjiakou' => 3328
    );
    if (empty($data['spell'][$city])) {
      return false;
    } else {
      return array('spell' => $data['spell'][$city], 'id' => $data['id'][$city]);
    }
  }

  //安居客 城市
  public function anjuke_city($city)
  {
    $data = array(
      'cd' => 'chengdu', 'cq' => 'chongqing', 'hf' => 'hf', 'hz' => 'hangzhou', 'km' => 'km', 'nj' => 'nanjing', 'sh' => 'shanghai', 'hrb' => 'heb',
      'sz' => 'suzhou', 'xa' => 'xa', 'huizhou' => 'huizhou', 'taizhou' => 'taizhou', 'langfang' => 'langfang', 'zhongshan' => 'zs', 'zhuhai' => 'zh', 'wuxi' => 'wx',
      'wuhan' => 'wuhan', 'guiyang' => 'gy', 'xiamen' => 'xm', 'quanzhou' => 'quanzhou', 'fuzhou' => 'fz', 'zhangzhou' => 'zhangzhou', 'songyuan' => 'songyuan', 'beijing' => 'beijing',
      'qingdao' => 'qd', 'jinan' => 'jinan', 'haikou' => 'haikou', 'guangzhou' => 'guangzhou', 'shenzhen' => 'shenzhen', 'nanchang' => 'nc', 'changchun' => 'cc', 'huhehaote' => 'huhehaote',
      'tianjin' => 'tianjin', 'taiyuan' => 'ty', 'zhengzhou' => 'zhengzhou', 'lanzhou' => 'lanzhou', 'changsha' => 'cs', 'shenyang' => 'sy', 'nanning' => 'nanning', 'wulumuqi' => 'wulumuqi',
      'lasa' => 'lasa', 'yinchuan' => 'yinchuan', 'xining' => 'xining', 'huaian' => 'huaian', 'weifang' => 'weifang', 'ningde' => 'ningde', 'wenzhou' => 'wenzhou', 'shijiazhuang' => 'sjz',
      'liuzhou' => 'liuzhou', 'pingxiang' => 'pingxiang', 'zhangjiakou' => 'zhangjiakou'
    );
    return $data[$city];
  }

  //百姓网 城市
  public function baixing_city($city)
  {
    $data = array(
      'pingxiang' => 'pingxiang', 'nj' => 'pingxiang'
    );
    return empty($data[$city]) ? '' : $data[$city];
  }

  //安居客 房屋类型 装修类型
  public function anjuke_house($city, $act)
  {
    //type 房屋类型: 1住宅 2公寓 3商住楼 4平房
    //fit  装修类型: 1毛培 2简装 3中装 4精装 5豪装 6婚装
    //face 朝向：1东 2东南 3南 4西南 5西 6西北 7北 8东北 9东西 10南北
    if ($act == 'sell') {
      $default = array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4));
      $face = array(1 => 0, 2 => 4, 3 => 1, 4 => 6, 5 => 2, 6 => 7, 7 => 3, 8 => 5, 9 => 9, 10 => 8);
      $data = array(
        'nj' => array('type' => array(1 => 25, 2 => 26, 3 => 26, 4 => 12, 'bs' => 27), 'fit' => array(1 => 25, 2 => 26, 3 => 123, 4 => 27, 5 => 28, 6 => 28)),     //南京
        'sh' => array('type' => array(1 => 9, 2 => 1, 3 => 1, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),         //上海
        'hrb' => array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),       //哈尔滨
        'km' => array('type' => array(1 => 113, 2 => 115, 3 => 115, 4 => 12, 'bs' => 114), 'fit' => array(1 => 119, 2 => 120, 3 => 123, 4 => 121, 5 => 122, 6 => 122)),   //昆明
        'sz' => array('type' => array(1 => 33, 2 => 34, 3 => 34, 4 => 12, 'bs' => 35), 'fit' => array(1 => 35, 2 => 36, 3 => 123, 4 => 37, 5 => 38, 6 => 38)),    //苏州
        'hz' => array('type' => array(1 => 29, 2 => 30, 3 => 30, 4 => 12, 'bs' => 31), 'fit' => array(1 => 30, 2 => 31, 3 => 123, 4 => 32, 5 => 33, 6 => 33)),    //杭州
        'xa' => array('type' => array(1 => 93, 2 => 95, 3 => 95, 4 => 12, 'bs' => 94), 'fit' => array(1 => 99, 2 => 100, 3 => 123, 4 => 101, 5 => 102, 6 => 102)), //西安
        'cq' => array('type' => array(1 => 41, 2 => 42, 3 => 42, 4 => 12, 'bs' => 43), 'fit' => array(1 => 45, 2 => 46, 3 => 123, 4 => 47, 5 => 48, 6 => 48)),    //重庆
        'cd' => array('type' => array(1 => 21, 2 => 22, 3 => 22, 4 => 12, 'bs' => 23), 'fit' => array(1 => 20, 2 => 21, 3 => 123, 4 => 22, 5 => 23, 6 => 23)),    //成都
        'langfang' => array('type' => array(1 => 9, 2 => 7, 3 => 7, 4 => 12, 'bs' => 8), 'fit' => array(1 => 5, 2 => 6, 3 => 123, 4 => 7, 5 => 8, 6 => 8)),       //廊坊
        'taizhou' => array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),      //泰州
        'wuhan' => array('type' => array(1 => 49, 2 => 50, 3 => 50, 4 => 12, 'bs' => 51), 'fit' => array(1 => 55, 2 => 56, 3 => 123, 4 => 57, 5 => 58, 6 => 58)),//武汉
        'lanzhou' => array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),      //兰州
        'guiyang' => array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),      //贵阳
        'quanzhou' => array('type' => array(1 => 9, 2 => 71, 3 => 71, 4 => 12, 'bs' => 2), 'fit' => array(1 => 1, 2 => 2, 3 => 123, 4 => 3, 5 => 4, 6 => 4)),     //泉州
        'jinan' => array('type' => array(1 => 54, 2 => 53, 3 => 53, 4 => 12, 'bs' => 55), 'fit' => array(1 => 60, 2 => 61, 3 => 123, 4 => 62, 5 => 63, 6 => 63)), //济南
        'fuzhou' => array('type' => array(1 => 109, 2 => 111, 3 => 111, 4 => 12, 'bs' => 110), 'fit' => array(1 => 115, 2 => 116, 3 => 123, 4 => 117, 5 => 118, 6 => 118)) //福州
      );
    } else {
      $default = array('type' => array(1 => 8, 2 => 1, 3 => 10, 4 => 9, 'bs' => 4),
        'fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修'));
      $face = array(1 => '东', 2 => '东南', 3 => '南', 4 => '西南', 5 => '西', 6 => '西北', 7 => '北', 8 => '东北', 9 => '东西', 10 => '南北');
      $data = array(
        'nj' => array('fit' => array(1 => '25|毛坯', 2 => '26|简单装修', 3 => '123|中等装修', 4 => '27|精装修', 5 => '28|豪华装修', 6 => '28|豪华装修')), //南京
        'sh' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),      //上海
        'hrb' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),      //哈尔滨
        'km' => array('fit' => array(1 => '119|毛坯', 2 => '120|简单装修', 3 => '123|中等装修', 4 => '121|精装修', 5 => '122|豪华装修', 6 => '122|豪华装修')), //昆明
        'sz' => array('fit' => array(1 => '35|毛坯', 2 => '36|简单装修', 3 => '123|中等装修', 4 => '37|精装修', 5 => '38|豪华装修', 6 => '38|豪华装修')),    //苏州
        'hz' => array('fit' => array(1 => '30|毛坯', 2 => '31|简单装修', 3 => '123|中等装修', 4 => '32|精装修', 5 => '33|豪华装修', 6 => '33|豪华装修')),    //杭州
        'xa' => array('fit' => array(1 => '99|毛坯', 2 => '100|简单装修', 3 => '123|中等装修', 4 => '101|精装修', 5 => '102|豪华装修', 6 => '102|豪华装修')),//西安
        'cq' => array('fit' => array(1 => '45|毛坯', 2 => '46|简单装修', 3 => '123|中等装修', 4 => '47|精装修', 5 => '48|豪华装修', 6 => '48|豪华装修')),    //重庆
        'cd' => array('fit' => array(1 => '20|毛坯', 2 => '21|简单装修', 3 => '123|中等装修', 4 => '22|精装修', 5 => '23|豪华装修', 6 => '23|豪华装修')),    //成都
        'langfang' => array('fit' => array(1 => '5|毛坯', 2 => '6|简单装修', 3 => '123|中等装修', 4 => '7|精装修', 5 => '8|豪华装修', 6 => '8|豪华装修')),    //廊坊
        'taizhou' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),    //泰州
        'wuhan' => array('fit' => array(1 => '55|毛坯', 2 => '56|简单装修', 3 => '123|中等装修', 4 => '57|精装修', 5 => '58|豪华装修', 6 => '58|豪华装修')), //武汉
        'lanzhou' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),     //兰州
        'guiyang' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),     //贵阳
        'quanzhou' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),    //泉州
        'jinan' => array('fit' => array(1 => '60|毛坯', 2 => '61|简单装修', 3 => '123|中等装修', 4 => '62|精装修', 5 => '63|豪华装修', 6 => '63|豪华装修')),  //济南
        'fuzhou' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修')),      //福州
        'zhangjiakou' => array('fit' => array(1 => '1|毛坯', 2 => '2|简单装修', 3 => '123|中等装修', 4 => '3|精装修', 5 => '4|豪华装修', 6 => '4|豪华装修'))       //张家口
      );
      foreach ($data as &$val) {
        $val['type'] = $default['type'];
      }
      $data['sh']['type'] = array(1 => 5, 2 => 1, 3 => 10, 4 => 9, 'bs' => 4);
    }
    //租房 配套设施
    $equipment = array(
      19 => 4, //床
      10 => 9, //电视
      3 => 6,  //空调
      5 => 10, //冰箱
      4 => 11, //洗衣机
      6 => 12, //热水器
      17 => 7, //宽带
      22 => 15, //可做饭
      23 => 16, //独立卫生间
      24 => 17  //阳台
    );

    $result = empty($data[$city]) ? $default : $data[$city];
    $result['face'] = $face;
    $result['equipment'] = $equipment;
    return $result;
  }


}
