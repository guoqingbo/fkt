<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Broker_log_model extends MY_Model
{

  /**
   * 统计日志表
   * @var string
   */
  private $_tbl = 'c_broker_log';

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
   * 获取统计日志列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条统计日志记录组成的二维数组
   */
  public function get_all_by($where, $start = 0, $limit = 20,
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
   * 根据查询条件返回一条统计日志的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的公司审核表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    //查询条件
    $this->dbback->where($where);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 统计日志编号
   * @return array 统计日志记录组成的一维数组
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
   * 插入数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司审核id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db->insert_batch($this->_tbl, $insert_data)) {
        return $this->db->insert_id();
      }
    } else {
      //单条插入
      if ($this->db->insert($this->_tbl, $insert_data)) {
        return $this->db->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db->update_batch($this->_tbl, $update_data);
    } else {
      $this->db->update($this->_tbl, $update_data);
    }
    return $this->db->affected_rows();
  }
}

/* End of file Count_log_base_model.php */
/* Location: ./applications/models/Count_log_base_model.php */
