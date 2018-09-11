<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Entrust_base_model CLASS
 *
 * 营销中心预约出售
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Appoint_center_base_model extends MY_Model
{

  private $_tbl_apnt = 'apnt';
  private $_tbl_sell_house = 'sell_house';
  private $_tbl_rent_house = 'rent_house';
  private $_tbl_broker = 'broker_info';
  private $_tbl_agency = 'agency';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_appoint_center_base_model_';
  }

  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }
    $this->_select_fields = $select_fields_str;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }


  /**
   * 出售预约符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by_sell($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_sell_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_sell_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_sell_house}.broker_id = {$this->_tbl_broker}.broker_id");
    // $this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");
    return $this->dbback_city->count_all_results();
  }

  /**
   * 出租预约符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by_rent($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_rent_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_rent_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_rent_house}.broker_id = {$this->_tbl_broker}.broker_id");
    //$this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");
    return $this->dbback_city->count_all_results();
  }


  //出售房源
  public function get_list_by_sell($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl_apnt}.*,{$this->_tbl_broker}.truename,{$this->_tbl_apnt}.id AS app_id,{$this->_tbl_sell_house}.id,{$this->_tbl_sell_house}.broker_id,{$this->_tbl_sell_house}.district_id,{$this->_tbl_sell_house}.street_id,{$this->_tbl_sell_house}.block_name,{$this->_tbl_sell_house}.room,{$this->_tbl_sell_house}.hall,{$this->_tbl_sell_house}.toilet,{$this->_tbl_sell_house}.buildarea,{$this->_tbl_sell_house}.price,{$this->_tbl_sell_house}.floor,{$this->_tbl_sell_house}.floor_type,{$this->_tbl_sell_house}.subfloor,{$this->_tbl_sell_house}.totalfloor");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_sell_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_sell_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_sell_house}.broker_id = {$this->_tbl_broker}.broker_id");
    //$this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl_apnt . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  //出租房源
  public function get_list_by_rent($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl_apnt}.*,{$this->_tbl_broker}.truename,{$this->_tbl_apnt}.id AS app_id,{$this->_tbl_rent_house}.id,{$this->_tbl_rent_house}.district_id,{$this->_tbl_rent_house}.street_id,{$this->_tbl_rent_house}.block_name,{$this->_tbl_rent_house}.room,{$this->_tbl_rent_house}.hall,{$this->_tbl_rent_house}.toilet,{$this->_tbl_rent_house}.buildarea,{$this->_tbl_rent_house}.price_danwei,{$this->_tbl_rent_house}.price,{$this->_tbl_rent_house}.floor,{$this->_tbl_rent_house}.floor_type,{$this->_tbl_rent_house}.subfloor,{$this->_tbl_rent_house}.totalfloor");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_rent_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_rent_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_rent_house}.broker_id = {$this->_tbl_broker}.broker_id");
    //$this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl_apnt . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取预约显示日期时间
   */
  public function apnt_date_time()
  {
    $apnt_date_time = array();
    $weekarray = array("日", "一", "二", "三", "四", "五", "六");
    for ($i = 0; $i < 9; $i++) {
      $apnt_date_time['sdate'][$i] = date("Y-m-d", strtotime(" +" . ($i - 1) . " day")) . ' 周' . $weekarray[date("w", strtotime(" +" . ($i - 1) . " day"))];
    }
    return $apnt_date_time;
  }


  /*******************************************************************************************/


}

/* End of file entrust_base_model.php */
/* Location: ./application/models/entrust_base_model.php */
