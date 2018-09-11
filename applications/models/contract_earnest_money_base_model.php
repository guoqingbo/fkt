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
 * contract_earnest_money_base_model CLASS
 *
 * 合同诚意金、添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
class Contract_earnest_money_base_model extends MY_Model
{

  /**
   * 合同表
   * @var string
   */
  private $_tbl = 'contract_earnest_money';

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
  }

  /**
   * 获取相关字段配置信息
   */
  public function get_config()
  {
    $config = array(
      'trade_type' => array('1' => '出售', '2' => '出租'),
      'sell_type' => array('1' => '住宅', '2' => '别墅', '3' => '商铺', '4' => '写字楼', '5' => '厂房', '6' => '车库'),
      'collect_type' => array('1' => '现金', '2' => '支票', '3' => '转帐', '4' => '汇款'),
      'refund_type' => array('1' => '现金', '2' => '支票', '3' => '转帐', '4' => '汇款'),
      'status' => array('1' => '已收', '2' => '转定', '3' => '入库', '4' => '已退'),
    );
    return $config;
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
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 符合条件的诚意金
   * @param string $where 查询条件
   * @return int
   */
  public function sum_earnest_price_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->select_sum("earnest_price");//总值
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 获取合同列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  public function insert($data_info)
  {
    $this->db_city->insert($this->_tbl, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
  }

  /**
   * 更新房源
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $update_data);
    return $this->db_city->affected_rows();
  }

  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {

    if ($this->_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->_tbl);

    return $this->db_city->affected_rows();
  }

  /**
   * 根据查询条件返回一条合同业绩分成表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }
}

/* End of file Contract_earnest_money_base_model.php */
/* Location: ./applications/models/Contract_earnest_money_base_model.php */
