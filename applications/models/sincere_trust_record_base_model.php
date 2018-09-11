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
 * Sincere_trust_record_base_model CLASS
 *
 * 经纪人信用操作记录功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_trust_record_base_model extends MY_Model
{

  /**
   * 信用记录表
   * @var string
   */
  private $_tbl = 'sincere_trust_record';


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
   * 获取信用记录列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'ASC')
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

  /**
   * 获取某个分值信用值所对应的等级
   * @param int $trust
   */
  public function get_level_by_trust($trust)
  {
    //根据分值获取等级
    $this->load->model('sincere_trust_level_base_model');
    $trust_level = $this->sincere_trust_level_base_model->get_level_by_trust($trust['trust']);
    return $trust_level['name'];
  }

  /**
   * 根据查询条件返回一条的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的信用表记录
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
   * 通过经纪人编号获取信用记录
   * @param int $broker_id 信用编号
   * @return array 信用记录组成的一维数组
   */
  public function get_by_broker_id($broker_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('broker_id', broker_id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入信用数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的信用id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }
}

/* End of file Sincere_trust_record_base_model.php */
/* Location: ./applications/models/Sincere_trust_record_base_model.php */
