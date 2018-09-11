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
 * Personnel_log_base_model CLASS
 *
 * 日志信息基础类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class My_notebook_base_model extends MY_Model
{

  /**
   * 日志表名
   * @var string
   */
  private $_tbl = 'my_notebook';

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
   * 添加日志
   * @return string
   */
  public function add_info($data_info)
  {
    $this->db_city->insert($this->_tbl, $data_info);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * 保存修改的记事本
   * @return int
   */
  public function save_modify($id, $data_info)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $data_info);
    return $this->db_city->affected_rows();
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
   * 获取个人记事本列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条个人记录组成的二维数组
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

  /**
   * 根据查询条件返回一条个人记事本的记录
   * @param int $id 公司编号
   * @return array 返回一条一维数组的个人记事本记录
   */
  public function get_by_id($id)
  {
    //查询字段
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 删除
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_id($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 删除多条
   *
   * @param int $ids
   * @return int
   */
  public function del_by_ids($ids)
  {
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }
}

/* End of file personnel_log_base_model.php */
/* Location: ./application/models/personnel_log_base_model.php */
