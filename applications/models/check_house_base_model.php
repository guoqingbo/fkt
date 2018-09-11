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
 * permission_tab_menu_base_model CLASS
 *
 * 预约看房菜单菜单添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class check_house_base_model extends MY_Model
{

  /**
   * 权限功能菜单表
   * @var string
   */
  private $_tbl_apnt = 'apnt';
  private $_tbl_sell_house = 'sell_house';
  private $_tbl_rent_house = 'rent_house';
  private $_tbl_broker = 'broker_info';
  private $_tbl_agency = 'agency';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    //$city = $this->config->item('login_city');
    //$this->_mem_key = $city . '_check_house_base_model_';
    //加载dbback_jjr（查）
    // $this->load->library('My_DB','','mydbback_jjr');
    //$this->dbback_jjr = $this->mydbback_jjr->get_db_obj('dbback_jjr');
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
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by_sell($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl_apnt);
  }

  public function count_by_rent($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl_apnt);
  }

  public function count_by_new_house($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl_apnt);
  }

  //符合条件的预约房源信息
  //出售房源
  public function get_list_by_sell($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl_apnt}.*,{$this->_tbl_sell_house}.block_name,{$this->_tbl_sell_house}.address,{$this->_tbl_sell_house}.id AS house_id,{$this->_tbl_broker}.truename,{$this->_tbl_agency}.name AS agency_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_sell_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_sell_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_sell_house}.broker_id = {$this->_tbl_broker}.broker_id");
    $this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");

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
    $this->dbback_city->select("{$this->_tbl_apnt}.*,{$this->_tbl_rent_house}.block_name,{$this->_tbl_rent_house}.address,{$this->_tbl_rent_house}.id AS house_id,{$this->_tbl_broker}.truename,{$this->_tbl_agency}.name AS agency_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);
    $this->dbback_city->join($this->_tbl_rent_house, "{$this->_tbl_apnt}.house_id = {$this->_tbl_rent_house}.id");
    $this->dbback_city->join($this->_tbl_broker, "{$this->_tbl_rent_house}.broker_id = {$this->_tbl_broker}.broker_id");
    $this->dbback_city->join($this->_tbl_agency, "{$this->_tbl_broker}.agency_id = {$this->_tbl_agency}.id");

    //排序条件
    $this->dbback_city->order_by($this->_tbl_apnt . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  //新房预约表
  public function get_list_by_new_house_apnt($where, $start = -1, $limit = 20, $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl_apnt}.*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl_apnt);

    //排序条件
    $this->dbback_city->order_by($this->_tbl_apnt . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  //新房预约表
  public function get_list_by_new_house($where = array())
  {
    //查询字段
    $res = $this->dbback_jjr->where($where);
    $re = $this->dbback_jjr->select(array('lp_id', 'lp_name', 'lp_loc', 'city'))->get('keeper_loupan')->result_array();

    return $re;
  }

  //清空数据库
  public function truncate()
  {

    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }
}

/* End of file permission_tab_menu_base_model.php */
/* Location: ./applications/models/permission_tab_menu_base_model.php */
