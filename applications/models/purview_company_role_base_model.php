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
 * Permission_company_role_base_model CLASS
 *
 * 公司权限角色添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Purview_company_role_base_model extends MY_Model
{

  /**
   * 公司权限角色表
   * @var string
   */
  private $_tbl = 'purview_company_role';

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
    $this->_mem_key = $city . '_purview_company_role_base_model_';
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
   * 获取公司权限角色列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
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
   * 根据查询条件返回一条表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的表记录
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
   * 通过公司权限角色编号获取记录
   * @param int $id 编号
   * @return array 记录组成的一维数组
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
   * 获取公司下的角色编号
   * @param int $company_id 公司编号
   */
  public function get_by_company_id($company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据公司编号和用户组编号获取相应的权限
   * @param int $company_id 公司编号
   * @param int $package_id 套餐编号
   */
  public function get_by_company_id_package_id($company_id, $package_id)
  {
    $mem_key = $this->_mem_key . 'get_by_company_id_package_id_'
      . $company_id . '_' . $package_id;
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $role = $cache['data'];
    } else {
      //查询条件
      $this->dbback_city->where('company_id', $company_id);
      $this->dbback_city->where('package_id', $package_id);
      $role = $this->dbback_city->get($this->_tbl)->row_array();
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $role), 3600);
    }
    return $role;

  }

  /**
   * 根据公司编号和角色编号获取相应的权限
   * @param int $company_id 公司编号
   * @param int $role_id 权色编号
   */
  public function get_by_company_id_role_id($company_id, $role_id)
  {
    $mem_key = $this->_mem_key . 'get_by_company_id_package_id_'
      . $company_id . '_' . $role_id;
    //$this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $role = $cache['data'];
    } else {
      //查询条件
      $this->dbback_city->where('company_id', $company_id);
      $this->dbback_city->where('id', $role_id);
      $role = $this->dbback_city->get($this->_tbl)->row_array();
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $role), 3600);
    }
    return $role;
  }

  /**
   * 插入公司权限角色数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
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
   * 更新公司权限角色数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id, $company_id = 0)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if ($company_id != 0) {
      $mem_key = $this->_mem_key . 'get_by_company_id_package_id_'
        . $company_id . '_' . $id;
      $this->mc->delete($mem_key);
    }
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 根据公司编号删除公司权限角色数据
   * @param int $company_id 公司编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_company_id($company_id, $package_id = '')
  {
    $this->db_city->where('company_id', $company_id);
    if ($package_id !== '') {
      $this->db_city->where('package_id', $package_id);
    }
    $this->db_city->delete($this->_tbl);
    return $this->db_city->affected_rows() > 0 ? true : false;
  }

  /**
   * 删除公司权限角色数据
   * @param int $id 编号
   * @param int $company_id 公司编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_id($id, $company_id = '')
  {
    //多条删除
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    if ($company_id) {
      $this->db_city->where('company_id', $company_id);
    }
    $this->db_city->where('package_id !=', 1); //总店长
    $this->db_city->where('package_id !=', 2); //经纪人
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

/* End of file purview_company_role_base_model.php */
/* Location: ./applications/models/purview_company_role_base_model.php */
