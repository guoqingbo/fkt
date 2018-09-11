<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Check_for_updates_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->city = 'city'; //城市表
    $this->error_collect = 'error_collect'; //采集中断列表详情
  }

  //获取城市
  public function collect_city()
  {
    $where = array('is_run' => 0);
    $result = $this->get_data(array('form_name' => $this->city, 'where' => $where), 'db');
    return $result;
  }

  /**
   * 检测采集房源
   * $type 房源类型（sell：出售；rent：出租）；
   * $kind 采集job的类型（1：住宅别墅；2：商铺写字楼）；
   * $web 站点来源（58：58同城；ganji：赶集）
   * 2016.3.2
   * cc
   */
  public function check_for_updates($city)
  {
    $words = '';//提示
    $res = array();
    $this->dbselect('dbback');
    $begin_time = strtotime(date("Y-m-d 08:00:00", time()));//job开始时间
    $end_time = strtotime(date("Y-m-d 22:00:00", time()));//job结束时间
    $citylist = array(
      '30' => '成都',
      '29' => '重庆',
      '23' => '哈尔滨',
      '19' => '杭州',
      '9' => '昆明',
      '17' => '苏州',
      '21' => '西安',
      '3' => '南京',
      '31' => '泰州',
      '32' => '廊坊',
      '33' => '中山',
      '34' => '珠海',
      '35' => '惠州',
      '36' => '无锡',
      '37' => '武汉',
      '38' => '贵阳',
      '39' => '厦门',
      '40' => '泉州',
      '41' => '福州',
      '42' => '松原',
      '43' => '漳州',
      '44' => '上海',
      '45' => '北京',
      '46' => '青岛',
      '47' => '济南',
      '48' => '长春',
      '28' => '兰州',
      '50' => '广州',
      '51' => '深圳',
      '57' => '呼和浩特',
      '58' => '海口',
      '7' => '天津',
      '49' => '太原',
      '24' => '郑州',
      '54' => '南昌',
      '56' => '乌鲁木齐',
      '53' => '长沙',
      '22' => '石家庄',
      '25' => '沈阳',
      '52' => '南宁',
      '59' => '银川',
      '60' => '西宁',
      '61' => '淮安',
      '62' => '宁德'
    );
//        //58出售
//        $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=".$city." and source_from=1 ORDER BY id DESC LIMIT 0,1";
//        $query = $this->db->query($sql);
//        $result = $query->result_array();
//        $res['58_出售'] = $result[0];
//		//赶集出售
//        $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=".$city." and source_from=0 ORDER BY id DESC LIMIT 0,1";
//        $query = $this->db->query($sql);
//        $result = $query->result_array();
//        $res['赶集_出售'] = $result[0];
//		//58出租
//        $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=".$city." and source_from=1 ORDER BY id DESC LIMIT 0,1";
//        $query = $this->db->query($sql);
//        $result = $query->result_array();
//        $res['58_出租'] = $result[0];
//		//赶集出租
//        $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=".$city." and source_from=0 ORDER BY id DESC LIMIT 0,1";
//        $query = $this->db->query($sql);
//        $result = $query->result_array();
//        $res['赶集_出租'] = $result[0];

    //58住宅别墅出售
    $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=" . $city . " and source_from=1 and sell_type IN (1,2) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['58_住宅别墅_出售'] = $result[0];
    //58商铺写字楼出售
    $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=" . $city . " and source_from=1 and sell_type IN (3,4) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['58_商铺写字楼_出售'] = $result[0];
    //赶集住宅别墅出售
    $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=" . $city . " and source_from=0 and sell_type IN (1,2) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['赶集_住宅别墅_出售'] = $result[0];
    //赶集商铺写字楼出售
    $sql = " SELECT id,createtime,city FROM  sell_house_collect WHERE city=" . $city . " and source_from=0 and sell_type IN (3,4) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['赶集_商铺写字楼_出售'] = $result[0];
    //58住宅别墅出租
    $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=" . $city . " and source_from=1 and rent_type IN (1,2) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['58_住宅别墅_出租'] = $result[0];
    //58商铺写字楼出租
    $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=" . $city . " and source_from=1 and rent_type IN (3,4) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['58_商铺写字楼_出租'] = $result[0];
    //赶集住宅别墅出租
    $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=" . $city . " and source_from=0 and rent_type IN (1,2) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['赶集_住宅别墅_出租'] = $result[0];
    //赶集商铺写字楼出租
    $sql = " SELECT id,createtime,city FROM  rent_house_collect WHERE city=" . $city . " and source_from=0 and rent_type IN (3,4) ORDER BY id DESC LIMIT 0,1";
    $query = $this->db->query($sql);
    $result = $query->result_array();
    $res['赶集_商铺写字楼_出租'] = $result[0];

    if (time() > $begin_time && time() < $end_time) {
      foreach ($res as $key => $value) {
        if (!empty($value)) {
          $keyarr = explode('_', $key);
          if ($keyarr[1] == '住宅别墅') {
            $time = 3600 * 2;
          } else {
            $time = 3600 * 6;
          }
          if (time() - $value[createtime] > $time) {
            $words .= $citylist[$city] . "_" . $key . "*";
          }
        }
      }
    }
    return $words;
  }

  //采集失败（城市+站点+类型）
  public function add_error_collect($data = array())
  {
    $result = $this->add_data($data, 'db', $this->error_collect);
    return $result;
  }

  //获取失败信息
  public function get_error_collect($id)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->error_collect, 'where' => $where), 'db');
    return $result;
  }
}
