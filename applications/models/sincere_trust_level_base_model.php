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
 * Sincere_trust_level_base_model CLASS
 *
 * 诚信信用等级 修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_trust_level_base_model extends MY_Model
{

  /**
   * 信用等级表
   * @var string
   */
  private $_tbl = 'sincere_trust_level';

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
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_sincere_trust_level_base_model_';
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
   * 获取所有信用等级
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by()
  {
    $mem_key = $this->_mem_key . 'get_all_by';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $trust_level = $cache['data'];
    } else {
      $this->dbback_city->order_by('id', 'desc');
      $trust_level = $this->dbback_city->get($this->_tbl)->result_array();
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $trust_level), 86400);
    }
    //返回结果
    return $trust_level;
  }

  /**
   * 通过编号获取记录
   * @param int $agency_id 编号
   * @return array 记录组成的一维数组
   */
  public function get_by_alias($alias)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('alias', $alias);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
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

  //清空数据库
  public function truncate()
  {
    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }

  /**
   * 更新数据
   * @param array $update_data 更新的数据源数组
   * @param array $agency_id 编号数组
   * @param int $company_id 总编号
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
   * 根据信用分
   * @param int $trust
   */
  public function get_level_by_trust($trust)
  {
    $all_trust = $this->get_all_by();
    if (is_full_array($all_trust)) {
      foreach ($all_trust as $value) {
        if (($trust >= $value['down'] && $trust <= $value['up'])
          || ($value['up'] == 0 && $trust >= $value['down'])
        ) {
          return $value;
        }
      }
    }
    return '';
  }
}

/* End of file Sincere_trust_level_base_model.php */
/* Location: ./applications/models/Sincere_trust_level_base_model.php */
