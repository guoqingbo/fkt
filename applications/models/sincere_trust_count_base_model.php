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
 * Sincere_trust_count_base_model CLASS
 *
 * 经纪人信用好评统计管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Sincere_trust_count_base_model extends MY_Model
{

  /**
   * 信用统计表
   * @var string
   */
  private $_tbl = 'sincere_trust_count';

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
    $this->_mem_key = $city . '_sincere_trust_count_base_model_';
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
   * 统计评价数据
   * @param int $broker_id 经纪人编号
   * @param string $count_fields 统计字段
   */
  public function count_appraise($broker_id, $count_fields)
  {
    $broker = $this->get_by_broker_id($broker_id);
    if (is_full_array($broker)) {
      if ($count_fields == 'good') {
        $good_count = $broker['good'] + 1;
        $total_count = $good_count + $broker['medium'] + $broker['bad'];
      } else if ($count_fields == 'medium') {
        $medium = $broker['medium'] + 1;
        $good_count = $broker['good'];
        $total_count = $broker['good'] + $medium + $broker['bad'];
      } else if ($count_fields == 'bad') {
        $bad = $broker['bad'] + 1;
        $good_count = $broker['good'];
        $total_count = $broker['good'] + $broker['medium'] + $bad;
      }
      $good_rate = round($good_count / $total_count * 100, 2);
      $this->db_city->set('good_rate', $good_rate);
      $this->db_city->set($count_fields, "{$count_fields} + 1", false);
      $this->db_city->where('broker_id', $broker_id);
      $this->db_city->update($this->_tbl);
      return $this->db_city->affected_rows();
    } else {
      $this->insert(array('broker_id' => $broker_id));
      return $this->count_appraise($broker_id, $count_fields);
    }
  }

  /**
   * 获取有效经纪人平均好评率
   */
  public function good_avg_rate()
  {
    $current_time = time();
    $this->load->model('broker_info_base_model');
    $join_tbl = $this->broker_info_base_model->get_tbl();
    $this->dbback_city->select('avg(good_rate) as good_rate');
    $this->dbback_city->where("$join_tbl.status = 1 and $join_tbl.expiretime > " . $current_time);
    $this->dbback_city->join($join_tbl, $this->_tbl . '.broker_id = ' . $join_tbl . '.broker_id ');
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过经纪人编号获取统计记录
   * @param int $broker_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_broker_id($broker_id)
  {
    $this->db_city->where('broker_id', $broker_id);
    return $this->db_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入公司数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司id 失败 false
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

/* End of file agency_base_model.php */
/* Location: ./applications/models/agency_base_model.php */
