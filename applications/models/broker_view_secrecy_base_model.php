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
 * Broker_view_secrecy_base_model CLASS
 *
 * 经纪人查看保密信息记录
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Broker_view_secrecy_base_model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl = 'broker_view_secrecy';

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
   * 获取表名
   * @return string
   */
  public function get_tbl()
  {
    return $this->_tbl;
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
   * 根据查询条件返回一条记录
   * @param string $where 查询条件
   * @return array
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

  /**
   * 根据查询条件返回当前经纪人当天的浏览次数
   * @param string $where 查询条件
   * @return array
   */
  public function get_broker_totay_view_num($where = '')
  {
    $this->dbback_city->select('count(*) as num');
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function insert($insert_data)
  {
    //单条插入
    if ($this->db_city->insert($this->_tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    return false;
  }

}

/* End of file broker_info_base_model.php */
/* Location: ./application/models/broker_info_base_model.php */
