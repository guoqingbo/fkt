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
 * Rent_house_statistic_model CLASS
 *
 * 出租房源信息统计相关的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */

//加载父类文件
load_m('House_base_model');

class Rent_house_statistic_model extends House_base_model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_rent_house_tbl = 'rent_house';


  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_statistic_district_tbl = 'statistics_city_district';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    //初始化表名称
    $this->set_tbl($this->_rent_house_tbl);
  }


  //获取全城区属统计表
  public function get_statistic_district_tbl()
  {
    return $this->_statistic_district_tbl;
  }


  //获取全城-区属维度新增数据
  public function get_city_district_new_data($cond_where)
  {
    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //表名称
    $tbl_name = $this->get_tbl();

    //新增量
    $this->dbback_city->select('COUNT(id) AS add_num', FALSE);

    //新增面积
    $this->dbback_city->select('SUM(buildarea) AS add_area', FALSE);

    //单套总价
    $this->dbback_city->select('AVG(price) AS price', FALSE);

    //单套
    $this->dbback_city->select('AVG(price/buildarea) AS avgprice', FALSE);

    //查询
    $arr_data = $this->dbback_city->get($tbl_name)->row_array();

    return $arr_data;
  }


  /*
   * 新增统计数据
   * @param array $data_info 统计数据
   * @return int 新增函数ID ，插入失败返回0
   */
  public function add_city_district_data($data_info)
  {
    $tbl_name = $this->get_statistic_district_tbl();

    if ($tbl_name != "" && is_array($data_info) && !empty($data_info)) {
      $statistic_arr = array();
      $statistic_arr['dist_id'] = intval($data_info['dist_id']);
      $statistic_arr['type'] = 2;
      $statistic_arr['add_num'] = intval($data_info['add_num']);
      $statistic_arr['add_area'] = round($data_info['add_area']);
      $statistic_arr['price'] = round($data_info['price']);
      $statistic_arr['avgprice'] = round($data_info['avgprice']);
      $statistic_arr['creattime'] = time();

      $this->db_city->insert($tbl_name, $statistic_arr);
      return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : 0;
    }
  }
}
