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
 * Suggest_base_model CLASS
 *
 * 经纪人信息基础类 提供挂靠公司的关系
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Suggest_base_model extends MY_Model
{

  /**
   * 任务表名
   * @var string
   */
  private $_tbl = 'feedback';

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
      $this->dbback->where($where);
    }
    return $this->dbback->count_all_results($this->_tbl);
  }

  /**
   * 获取意见列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条任务记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    //排序条件
    $this->dbback->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback->limit($limit, $start);
    }
    //返回结果
    return $this->dbback->get($this->_tbl)->result_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 公司编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    //查询条件
    $this->dbback->where('id', $id);
    return $this->dbback->get($this->_tbl)->row_array();
  }


  /**
   * 插入反馈意见
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function insert($insert_data)
  {
    if ($this->db->insert($this->_tbl, $insert_data)) {
      return $this->db->insert_id();
    }
    return false;
  }

}

/* End of file task_base_model.php */
/* Location: ./application/models/task_base_model.php */
