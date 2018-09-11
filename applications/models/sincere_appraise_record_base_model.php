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
 * Sincere_appraise_record_base_model CLASS
 *
 * 经纪人的动态评分记录功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_appraise_record_base_model extends MY_Model
{

  /**
   * 信用表
   * @var string
   */
  private $_tbl = 'sincere_appraise_record';

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

  //统计多长时间数据
  public function get_valid_time()
  {
    return strtotime('-3 months', time());
  }

  /**
   * 获取条件下均分
   * @return int
   */
  public function get_avg_by($where)
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->select('type_id');
    $this->dbback_city->select_avg('score');
    $this->dbback_city->group_by('type_id');
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 获取条件下均分
   * @return int
   */
  public function get_sum_by($where)
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->select('type_id');
    $this->dbback_city->select_sum('score');
    $this->dbback_city->group_by('type_id');
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 经纪人动态评分详细信息
   * @param unknown $broker_id
   * @param unknown $type_id
   */
  public function count_score_group_by($where)
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->select('score, count(*) as count');
    $this->dbback_city->group_by('score');
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 插入评价数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的评价id 失败 false
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

  /**
   * 更新评价数据
   * @param array $update_data 更新的数据源数组
   * @param array $broker_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_broker_id($update_data, $broker_id)
  {
    if (is_array($broker_id)) {
      $ids = $broker_id;
    } else {
      $ids[0] = $broker_id;
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
   * 删除评价数据
   * @param array or int $id 评价编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_id($id)
  {
    //多条删除
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    if ($ids) {
      $this->db_city->where_in('id', $ids);
      $this->db_city->delete($this->_tbl);
    }
    if ($this->db_city->affected_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }
}

/* End of file Sincere_appraise_record_base_model.php */
/* Location: ./applications/models/Sincere_appraise_record_base_model.php */
