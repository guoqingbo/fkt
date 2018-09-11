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
 * bargain_divide_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
load_m('Transfer_base_model');

class Bargain_transfer_model extends Transfer_base_model
{

  /**
   * 成交表
   * @var string
   */
  private $_tbl = 'bargain_transfer_step';

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
   * 添加成交
   * @return string
   */
  public function add_info($data_info)
  {
    $this->db_city->insert($this->_tbl, $data_info);
    return $this->db_city->affected_rows() >= 1 ? $this->db_city->insert_id() : 0;
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
   * 获取成交权证列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by_cid($cid)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where("bargain_id", $cid);
    //排序条件
    $this->dbback_city->order_by('id', 'ASC');
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 编号
   * @return array 成交记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 编号
   * @return array 成交记录组成的一维数组
   */
  public function get_by_cond($where)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /** 添加数据并返回ID*/
  public function insert_data($data)
  {
    $this->db_city->insert($this->_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /** 修改数据并返回影响行数 */
  public function modify_data($id, $data)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $data);
    return $this->db_city->affected_rows();
  }

  /** 删除 */
  public function delete_data($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 根据编号更新成交的详细信息数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }

  /**
   * 根据cid删除
   *
   * @param int $id
   * @return 0 or 1
   */
  public function del_by_cid($cid)
  {
    $this->db_city->where('bargain_id', $cid);
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows();
  }
}

/* End of file bargain_divide_model.php */
/* Location: ./app/models/bargain_divide_model.php */
