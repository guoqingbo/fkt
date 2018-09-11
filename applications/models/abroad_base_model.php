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
 * sell_house_model CLASS
 *
 * 海外地产信息管理类,提供增加、修改、删除、查询 出售房源信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
class Abroad_base_model extends MY_Model
{

  /**
   * 表名
   *
   * @access private
   * @var string
   */
  private $_abroad_tbl = 'abroad_house';

  private $_tbl_country = 'abroad_country';

  private $_tbl_city = 'abroad_city';

  private $_search_fields = '';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
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
   * 符合条件托管合同的行数
   * @param string $where 查询条件
   * @return int
   */
  public function get_count_by_cond($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_abroad->where($where);
    }
    return $this->dbback_abroad->count_all_results($this->_abroad_tbl);
  }

  /**
   * 获取符合条件的房源需求信息列表
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   出售出租信息列表
   */
  public function get_list_by_cond($cond_where = '', $offset = 0, $limit = 10, $order_key2 = '', $order_by2 = '', $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_abroad->select($this->_select_fields);
    }
    //查询条件.
    if ($cond_where != '') {
      $this->dbback_abroad->where($cond_where);
    }
    if (!empty($order_key2) && !empty($order_by2)) {
      $this->dbback_abroad->order_by($order_key2, $order_by2);
    } else {
      $this->dbback_abroad->order_by($order_key, $order_by);
    }
    if ($offset >= 0 && $limit > 0) {
      $this->dbback_abroad->limit($limit, $offset);
    }
    $arr_data = $this->dbback_abroad->get($this->_abroad_tbl)->result_array();

    return $arr_data;
  }

  /**
   * 获取国家列表
   * @param array $data
   * @return array
   */
  public function get_country($data = '')
  {
    $this->dbback_abroad->where('status', 1);
    $country_list = $this->dbback_abroad->get($this->_tbl_country)->result_array();
    return $country_list;
  }

  /**
   * 通过门店编号获取经纪人记录
   * @param int $agency_id 门店编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_country_id($country_id)
  {
    $this->dbback_abroad->where('id', $country_id);
    //查询条件
    $this->dbback_abroad->where('status', 1);
    return $this->dbback_abroad->get($this->_tbl_country)->row_array();
  }

  /**
   * 获取城市列表
   * @param array $data
   * @return array
   */
  public function get_city($country_id = '')
  {
    $this->dbback_abroad->where('status', 1);
    if ($country_id) {
      $this->dbback_abroad->where_in('country_id', $country_id);
    }
    $city_list = $this->dbback_abroad->get($this->_tbl_city)->result_array();
    return $city_list;
  }

  public function get_by_city_id($city_id)
  {
    $this->dbback_abroad->where('id', $city_id);
    $this->dbback_abroad->where('status', 1);
    return $this->dbback_abroad->get($this->_tbl_city)->row_array();
  }

  /**
   * 通过ID获取记录
   * @param int $id 编号
   * @return array 合同记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_abroad->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_abroad->where('id', $id);
    return $this->dbback_abroad->get($this->_abroad_tbl)->row_array();
  }

  /**
   * 根据多个house_id查询多条房源信息
   * @param  house_id字段
   * @return array
   */

  public function get_all_house($house_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($house_id)) {
      $sql = " SELECT * FROM  `sell_house` WHERE id IN ($house_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  /**
   * 根据多个id查询多条房源合作的信息
   * @param  id字段
   * @return array
   */

  public function get_all_isshare_by_ids($id)
  {
    $this->dbselect('dbback_city');

    if (!empty($id)) {
      $sql = " SELECT isshare FROM  `sell_house` WHERE id IN ($id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  /**
   * 添加一条浏览记录
   * @param array $paramlist 添加字段
   * @return insert_id or 0
   */
  function add($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->browse_sell_mess_log);
    return $result;
  }
}

/* End of file sell_house_model.php */
/* Location: ./applications/mls/models/sell_house_model.php */
