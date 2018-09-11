<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * City_statistic_model CLASS
 *
 * 全城统计相关的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class City_statistic_model extends MY_Model
{

  /**
   * 单项统计-按照城区统计数据表名
   *
   * @access private
   * @var string
   */
  private $_statistic_district_tbl = 'statistics_city_district';
  /**
   * 单项统计-按照城区统计数据表名
   *
   * @access private
   * @var string
   */
  private $_statistic_district_tb2 = 'statistics_city_build_type';


  /**
   * 趋势统计-按照出售房源统计数据表名
   *
   * @access private
   * @var string
   */
  private $_statistic_trend_sell_tbl = 'statistics_city_trend_sell';


  /**
   * 趋势统计-按照出租房源统计数据表名
   *
   * @access private
   * @var string
   */
  private $_statistic_trend_rent_tbl = 'statistics_city_trend_rent';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }


  //获取全城区属统计表
  public function get_statistic_district_tbl()
  {
    return $this->_statistic_district_tbl;
  }

  //获取全城物业类型统计表
  public function get_statistic_district_tb2()
  {
    return $this->_statistic_district_tb2;
  }

  //获取趋势统计出售房源数据表
  public function get_statistic_trend_sell_tbl()
  {
    return $this->_statistic_trend_sell_tbl;
  }


  //获取趋势统计出租房源数据表
  public function get_statistic_trend_rent_tbl()
  {
    return $this->_statistic_trend_rent_tbl;
  }


  /**
   * _tbl_exists
   *
   * 判断数据库表是否存在
   *
   * @access    private
   * @param    int $tbl_name 表名称
   * @return    boolean  true/false
   */
  private function _tbl_exists($tbl_name)
  {
    $sql = "SHOW TABLES LIKE '" . $tbl_name . "'";
    $tbl_info = $this->db_city->query($sql);

    if (is_array($tbl_info) && !empty($tbl_info)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }


  /**
   * _creat_tbl_by_copy
   *
   * 根据$tblname 生成新表 $tblname_new
   *
   * @access    private
   * @param    string $tblname_new 新表名称
   * @param    string $tblname 被复制的表的名称
   * @return    boolean true/false
   */
  private function _creat_tbl_by_copy($tblname_new, $tblname)
  {
    if (trim($tblname_new) == trim($tblname)) {
      return false;
    }

    // MYSQL5.0以上才支持,5.0之前用(CREATE TABLE 新表 SELECT * FROM 旧表 WHERE 1=2)
    $sql = " CREATE TABLE $tblname_new LIKE $tblname ";
    $result = $this->db_city->query($sql);

    if ($result) {
      return true;
    } else {
      return false;
    }
  }


  /*
   * 根据日期获取趋势统计数据表
   * @param string $year 格式2015
   * @param string $type 信息类型
   * @retrun string 表名称
   */
  public function get_statistic_trend_tbl_by_year($year, $type)
  {
    $tbl_name_new = '';
    $tbl_name = '';

    //表名称
    switch ($type) {
      case '1':
        $tbl_name = $this->get_statistic_trend_sell_tbl();
        break;
      case '2':
        $tbl_name = $this->get_statistic_trend_rent_tbl();
        break;
    }

    if ($tbl_name != '') {
      $tbl_name_new = $tbl_name . '_' . $year;

      //判断表名称是否已创建
      $is_created = $this->_tbl_exists($tbl_name_new);

      //没创建表名称需要重新创建
      if (!$is_created) {
        $this->_creat_tbl_by_copy($tbl_name_new, $tbl_name);
      }
    }

    return $tbl_name_new;
  }

  /**
   * 统计模块基础配置信息
   *
   * @access    public
   * @param    array $type 基本信息类型 buy-求购/rent-求租
   * @param    array $key 需求信息编号数组 配置信息Key值
   * @return    array 配置信息数组
   */
  public function get_base_conf($type = '', $key = '')
  {
    $config_arr = array();

    //配置数组信息
    $config = array(
      'unit' => array(1 => '按城区', 2 => '按类型'),
      'unit_trend' => array('month' => '按月', 'day' => '按日'),
      'type' => array(1 => '出售', 2 => '出租'),
      'fields' => array(
        'add_num' => '登记量', 'add_area' => '登记面积',
        'price' => '单套总价', 'avgprice' => '登记均价'
      ),
      'fields_rent' => array(
        'add_num' => '登记量', 'add_area' => '登记面积',
        'price' => '月均租金', 'avgprice' => '登记均价'
      ),
      'field_suffix' => array(
        '1' => array('add_num' => '套', 'add_area' => '㎡',
          'price' => '万元/套', 'avgprice' => '元/㎡'
        ),
        '2' => array('add_num' => '套', 'add_area' => '㎡',
          'price' => '元/月', 'avgprice' => '元/㎡/月'
        )
      ),
      'count_type' => array(
        'add_num' => array('name' => '总计', 'operate' => 'sum'),
        'add_area' => array('name' => '总计', 'operate' => 'sum'),
        'price' => array('name' => '平均', 'operate' => 'avg'),
        'avgprice' => array('name' => '平均', 'operate' => 'avg')
      ),
      'sell_buildare' => array('1' => '60m2以下', '2' => '60-90m2',
        '3' => '90-120m2', '4' => '120-150m2', '5' => '150-180m2',
        '6' => '180-240m2', '7' => '240-320m2', '8' => '320-600m2',
        '9' => '600m2以上'
      ),
      'sell_price' => array('1' => '30万以下', '2' => '30-50万',
        '3' => '50-70万', '4' => '70-90万', '5' => '90-120万',
        '6' => '120-150万', '7' => '150-200万', '8' => '200-400万',
        '9' => '400万以上'
      ),
      'rent_price' => array('1' => '500元以下', '2' => '800-1200元',
        '3' => '1200-1800元', '4' => '1800-2600元', '5' => '2600-3200元',
        '6' => '2600-3200元', '7' => '3200-4200元', '8' => '4200-8000元',
        '9' => '8000元以上'
      ),
      'room_type' => array('1' => '1室', '2' => '2室', '3' => '3室', '4' => '4室',
        '5' => '5室', '6' => '6室', '7' => '7室', '8' => '8室', '0' => '全部'),
      'sell_avgprice' => array('1' => '3000元/m2以下', '2' => '3000-4000元/m2',
        '3' => '4000-5000元/m2', '4' => '5000-6000元/m2', '5' => '6000-7000元/m2 ',
        '6' => '7000-9000元/m2', '7' => '9000-12000元/m2', '8' => '12000-20000元/m2',
        '9' => '20000元/m2以上'
      ),
      'rent_avgprice' => array('1' => '20元/m2以下', '2' => '20-50元/m2',
        '3' => '50-80元/m2', '4' => '80-110元/m2', '5' => '110-140元/m2 ',
        '6' => '140-170元/m2', '7' => '170-200元/m2', '8' => '200-400元/m2',
        '9' => '400元/m2以上'
      ),
    );

    //返回数据
    $config_arr = empty($key) ? $config : $config[$key];

    return $config_arr;
  }


  /*
   * 从房客源基础表中以相应维度获取新增数据
   * @param string $type sell-出售，rent-出租
   * @param string $cond_where 查询条件
   * @return array 统计数据
   */
  public function count_city_district_new_data($type, $cond_where)
  {
    $arr_data = array();

    $tbl_name = '';//表名称

    switch ($type) {
      case 'sell':
        $this->load->model('sell_house_model');
        $tbl_name = $this->sell_house_model->get_tbl();

        //查询统计字段
        $this->dbback_city->select('COUNT(id) AS add_num', FALSE);//新增量
        $this->dbback_city->select('SUM(buildarea) AS add_area', FALSE);//新增面积
        $this->dbback_city->select('AVG(price) AS price', FALSE);//单套总价
        $this->dbback_city->select('AVG(avgprice) AS avgprice', FALSE);//单套
        break;

      case 'rent':
        $this->load->model('rent_house_model');
        $tbl_name = $this->rent_house_model->get_tbl();

        //查询统计字段
        $this->dbback_city->select('COUNT(id) AS add_num', FALSE);//新增量
        $this->dbback_city->select('SUM(buildarea) AS add_area', FALSE); //新增面积
        $this->dbback_city->select('AVG(price) AS price', FALSE);//单套总价
        $this->dbback_city->select('AVG(price/buildarea) AS avgprice', FALSE);//单套均价
        break;
    }

    if ($tbl_name != "" && $cond_where != '') {
      $this->dbback_city->where($cond_where); //查询条件
      $arr_data = $this->dbback_city->get($tbl_name)->row_array();//查询
    }

    return $arr_data;
  }


  /*
   * 添加区属维度统计数据
   * @param  int $type 类型  1-出售，2-出租 , 3-求购 ， 4-求租
   * @param array $data_info 统计数据
   * @return int 新增函数ID ，插入失败返回0
   */
  public function add_city_district_data($type, $data_info)
  {
    //统计数据表名称
    $tbl_name = $this->get_statistic_district_tbl();

    if ($tbl_name != "") {
      $statistic_arr = array();
      $statistic_arr['dist_id'] = intval($data_info['dist_id']);
      $statistic_arr['type'] = intval($type);
      $statistic_arr['add_num'] = intval($data_info['add_num']);
      $statistic_arr['add_area'] = intval($data_info['add_area']);
      $statistic_arr['price'] = round($data_info['price']);
      $statistic_arr['avgprice'] = round($data_info['avgprice']);
      $statistic_arr['stattime'] = $data_info['stattime'];
      $statistic_arr['creattime'] = time();

      $this->db_city->flow($tbl_name, $statistic_arr);
      //echo $this->db_city->last_query().'<br>';
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }
  }

  /*
   * 添加物业类型维度统计数据
   * @param  int $type 类型  1-出售，2-出租 , 3-求购 ， 4-求租
   * @param array $data_info 统计数据
   * @return int 新增函数ID ，插入失败返回0
   */
  public function add_city_build_type_data($type, $data_info)
  {
    //统计数据表名称
    $tbl_name = $this->get_statistic_district_tb2();

    if ($tbl_name != "") {
      $statistic_arr = array();
      $statistic_arr['build_type'] = intval($data_info['build_type']);
      $statistic_arr['type'] = intval($type);
      $statistic_arr['add_num'] = intval($data_info['add_num']);
      $statistic_arr['add_area'] = intval($data_info['add_area']);
      $statistic_arr['price'] = round($data_info['price']);
      $statistic_arr['avgprice'] = round($data_info['avgprice']);
      $statistic_arr['stattime'] = $data_info['stattime'];
      $statistic_arr['creattime'] = time();

      $this->db_city->flow($tbl_name, $statistic_arr);
      //echo $this->db_city->last_query().'<br>';
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }
  }


  /*
   * 获取单项统计-按区属统计数据
   * @param int $type 类型 1-出售、2-出租、3-求购、4-求租
   * @param string $field 查询字段
   * @param string $start_day 开始日期
   * @param string $end_day 结束日期
   * @return 统计数据
   */
  public function get_city_statistic_dist_data($type, $field = '', $start_day = '', $end_day = '')
  {
    $data_arr = array();

    $type = intval($type);
    $field = !empty($field) ? strip_tags($field) : '';
    $start_day = !empty($start_day) ? strip_tags($start_day) : date('Y-m-d', strtotime("-1 day"));
    $end_day = !empty($end_day) ? strip_tags($end_day) : $start_day;

    //统计数据表
    $tbl_name = $this->get_statistic_district_tbl();

    if ($tbl_name != '') {
      if ($field != '') {
        $this->dbback_city->select("dist_id");
        $this->dbback_city->select($field);
      }

      $cond_where = "type = '" . $type . "' AND stattime >= '" . $start_day . "' AND stattime <= '" . $end_day . "'";
      $this->dbback_city->where($cond_where);
      $data_arr = $this->dbback_city->get($tbl_name)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $data_arr;
  }

  /*
   * 获取单项统计-按区属统计数据
   * @param int $type 类型 1-出售、2-出租、3-求购、4-求租
   * @param string $field 查询字段
   * @param string $start_day 开始日期
   * @param string $end_day 结束日期
   * @return 统计数据
   */
  public function get_build_type_statistic_dist_data($type, $field = '', $start_day = '', $end_day = '')
  {
    $data_arr = array();

    $type = intval($type);
    $field = !empty($field) ? strip_tags($field) : '';
    $start_day = !empty($start_day) ? strip_tags($start_day) : date('Y-m-d', strtotime("-1 day"));
    $end_day = !empty($end_day) ? strip_tags($end_day) : $start_day;

    //统计数据表
    $tbl_name = $this->get_statistic_district_tb2();
    if ($tbl_name != '') {
      if ($field != '') {
        $this->dbback_city->select("build_type");
        $this->dbback_city->select($field);
      }

      $cond_where = "type = '" . $type . "' AND stattime >= '" . $start_day . "' AND stattime <= '" . $end_day . "'";
      $this->dbback_city->where($cond_where);
      $data_arr = $this->dbback_city->get($tbl_name)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $data_arr;
  }

  /*
   * 获取单项统计-按物业类型统计数据
   * @param int $type 类型 1-出售、2-出租、3-求购、4-求租
   * @param string $field 查询字段
   * @param string $start_day 开始日期
   * @param string $end_day 结束日期
   * @return 统计数据
   */
  public function get_city_statistic_build_type_data($type, $field = '', $start_day = '', $end_day = '')
  {
    $data_arr = array();

    $type = intval($type);
    $field = !empty($field) ? strip_tags($field) : '';
    $start_day = !empty($start_day) ? strip_tags($start_day) : date('Y-m-d', strtotime("-1 day"));
    $end_day = !empty($end_day) ? strip_tags($end_day) : $start_day;

    //统计数据表
    $tbl_name = $this->get_statistic_district_tb2();

    if ($tbl_name != '') {
      if ($field != '') {
        $this->dbback_city->select("build_type");
        $this->dbback_city->select($field);
      }

      $cond_where = "type = '" . $type . "' AND stattime >= '" . $start_day . "' AND stattime <= '" . $end_day . "'";
      $this->dbback_city->where($cond_where);
      $data_arr = $this->dbback_city->get($tbl_name)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $data_arr;
  }


  /*
   * 添加出售房源趋势统计数据
   * @param array $data_info 统计数据
   * @return int 新增函数ID ，插入失败返回0
   */
  public function add_city_trend_statistic_sell($data_info)
  {
    //统计数据表名称
    $year = date("Y");
    $tbl_name = $this->get_statistic_trend_tbl_by_year($year, 1);

    if ($tbl_name != "") {
      $statistic_arr = array();
      $statistic_arr['rowid'] = intval($data_info['id']);
      $statistic_arr['signatory_id'] = intval($data_info['signatory_id']);
      $statistic_arr['department_id'] = intval($data_info['department_id']);
      $statistic_arr['district_id'] = intval($data_info['district_id']);
      $statistic_arr['street_id'] = intval($data_info['street_id']);
      $statistic_arr['infotype'] = intval($data_info['sell_type']);
      $statistic_arr['room'] = intval($data_info['room']);
      $statistic_arr['price'] = round($data_info['price']);
      $statistic_arr['avgprice'] = round($data_info['avgprice']);
      $statistic_arr['house_type'] = intval($data_info['house_type']);
      $statistic_arr['fitment'] = intval($data_info['fitment']);
      $statistic_arr['buildyear'] = strip_tags($data_info['buildyear']);
      $statistic_arr['buildarea'] = floatval($data_info['buildarea']);
      $statistic_arr['month'] = date('m');
      $statistic_arr['day'] = date('d');
      $statistic_arr['creattime'] = intval($data_info['createtime']);

      $this->db_city->flow($tbl_name, $statistic_arr);
      //echo $this->db_city->last_query();
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }
  }


  /*
   * 添加出租房源趋势统计数据
   * @param array $data_info 统计数据
   * @return int 新增函数ID ，插入失败返回0
   */
  public function add_city_trend_statistic_rent($data_info)
  {
    //统计数据表名称
    $year = date("Y");
    $tbl_name = $this->get_statistic_trend_tbl_by_year($year, 2);

    if ($tbl_name != "") {
      $statistic_arr = array();
      $statistic_arr['rowid'] = intval($data_info['id']);
      $statistic_arr['signatory_id'] = intval($data_info['signatory_id']);
      $statistic_arr['department_id'] = intval($data_info['department_id']);
      $statistic_arr['district_id'] = intval($data_info['district_id']);
      $statistic_arr['street_id'] = intval($data_info['street_id']);
      $statistic_arr['infotype'] = intval($data_info['sell_type']);//物业类型
      $statistic_arr['room'] = intval($data_info['room']);
      $statistic_arr['price'] = round($data_info['price']);
      $statistic_arr['avgprice'] = $data_info['buildarea'] > 0 ?
        round($data_info['price'] / $data_info['buildarea']) : 0;
      $statistic_arr['house_type'] = intval($data_info['house_type']);
      $statistic_arr['fitment'] = intval($data_info['fitment']);
      $statistic_arr['buildyear'] = strip_tags($data_info['buildyear']);
      $statistic_arr['buildarea'] = floatval($data_info['buildarea']);
      $statistic_arr['month'] = date('m');
      $statistic_arr['day'] = date('d');
      $statistic_arr['creattime'] = intval($data_info['createtime']);

      $this->db_city->flow($tbl_name, $statistic_arr);
      //echo $this->db_city->last_query();
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }
  }


  /*
   * 获取趋势统计-按月统计
   * @param int $type 类型 1-出售、2-出租、3-求购、4-求租
   * @param int $field 需要统计的字段
   * @param string $unit 统计维度
   * @param string $year 统计年份
   * @param string $cond_where 查询条件
   * @return 统计数据
   */
  public function get_city_statistic_trend_data($type, $field, $unit, $year, $cond_where = '')
  {
    $data_arr = array();

    $type = strip_tags($type);//类型
    $field = strip_tags($field);//统计字段
    $unit = strip_tags($unit);//统计维度
    $year = strip_tags($year);//统计年份

    //统计数据表名称
    $tbl_name = $this->get_statistic_trend_tbl_by_year($year, $type);

    if ($tbl_name != '') {
      switch ($field) {
        case 'add_num':
          $this->dbback_city->select('COUNT(rowid) AS add_num', FALSE);//登记量
          break;

        case 'add_area':
          $this->dbback_city->select('SUM(buildarea) AS add_area', FALSE);//登记面积
          break;

        case 'price':
          $this->dbback_city->select('AVG(avgprice) AS price', FALSE);//单套总价
          break;

        case 'avgprice':
          $this->dbback_city->select('AVG(avgprice) AS avgprice', FALSE);//登记均价
          break;

        default:
          $this->dbback_city->select('COUNT(rowid) AS add_num', FALSE);//登记量
          break;
      }

      $this->dbback_city->select($unit);

      if ($cond_where != '') {
        $this->dbback_city->where($cond_where);
      }

      $this->dbback_city->group_by($unit);
      $data_arr = $this->dbback_city->get($tbl_name)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $data_arr;
  }
}
