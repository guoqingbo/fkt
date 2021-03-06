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
 * Auth_review_base_model CLASS
 *
 * 经纪人证书审核功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Auth_review_base_model extends MY_Model
{

  /**
   * 证书申请表
   * @var string
   */
  private $_tbl = 'auth_review';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 证书审核状态
   * @var array
   */
  public $status = array(
    '队列', '通过', '拒绝'
  );

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
   * 获取证书申请列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条证书申请记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = '', $order_by = '')
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
    if ($order_key == '' || $order_by == '') {
      $this->dbback_city->order_by('status asc, updatetime desc');
    }
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 通过编号获取记录
   * @param int $id 记录编号
   * @return array 证书申请记录组成的一维数组
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
   * 通过经纪人编号获取记录
   * @param int $broker_id 经济人id
   * @return array 证书申请记录组成的一维数组
   */
  public function get_by_broker_id($broker_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('broker_id', $broker_id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 根据查询条件返回一条证书申请表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的证书申请表记录
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

  /*
   *获取最新记录
   */
  public function get_new($where, $start = -1, $limit = 20,
                          $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->db_city->where($where);
    }
    //排序条件
    $this->db_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->db_city->limit($limit, $start);
    }
    //返回结果
    return $this->db_city->get($this->_tbl)->row_array();
  }

  /**
   * 插入证书申请数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的证书申请id 失败 false
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
   * 更新证书申请数据
   * @param array $update_data 更新的数据源数组
   * @param array $agency_id 证书申请编号数组
   * @param int $company_id 总证书申请编号
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

  public function get_boss_broker($company_id)
  {
    $this->dbback_city->where('company_id', $company_id);
    $this->dbback_city->where('system_group_id', 1);
    $this->dbback_city->select('id');
    $result = $this->dbback_city->get('permission_company_group')->row_array();
    $this->dbback_city->where('role_id', $result['id']);
    $this->dbback_city->select('id,truename');
    return $this->dbback_city->get('broker_info')->row_array();
  }
}

/* End of file pay_applay_base_model.php */
/* Location: ./applications/models/pay_applay_base_model.php */
