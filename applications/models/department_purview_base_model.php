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
 * Agency_purview_base_model CLASS
 *
 * 门店访问数据范围类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Department_purview_base_model extends MY_Model
{

  /**
   * 门店关联权限表
   * @var string
   */
  private $_tbl = 'department_purview';
  private $_tb2 = 'department';

  //当前经纪人所在门店ID
  private $_department_id = 0;
  //当前经纪人所在公司ID
  private $_company_id = 0;
  //当前经纪人角色等级
  private $_role_level = 0;

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

  //设置当前经纪人门店ID、公司、角色
  public function set_department_id($department_id = 0, $company_id = 0, $role_level = 0)
  {
    if ($department_id > 0) {
      $this->_department_id = intval($department_id);
    }
    if ($company_id > 0) {
      $this->_company_id = intval($company_id);
    }
    if ($role_level > 0) {
      $this->_role_level = intval($role_level);
    }
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
   * 根据主门店id、被关联门店id获取权限节点
   * @param int $main_department_id 主门店ID
   * @param int $sub_department_id 被关联门店ID
   * @return array 返回数据
   */
  public function get_func_area_by_main_sub_id($main_department_id, $sub_department_id)
  {
    //查询字段
    $this->dbback_city->select('func_auth');
    //查询条件
    $this->dbback_city->where(array('main_department_id' => $main_department_id, 'sub_department_id' => $sub_department_id));
    //返回结果
    $departmentarea = $this->dbback_city->get($this->_tbl)->row_array();
    if (is_full_array($departmentarea) && !empty($departmentarea['func_auth'])) {
      $func_area = $departmentarea['func_auth'];
    } else {
      $func_area = array();
    }
    return $func_area;
  }

  /**
   * 根据主门店id，要查询数据范围节点，获得被关联的门店id
   * @param int $main_department_id 主门店ID
   * @param string $access_str 待查询数据范围字段名
   * @return array 返回数据
   */
  public function get_department_id_by_main_id_access($main_department_id, $access_str = '')
  {
    //判断当前经纪人是否是总经理
    if (1 == $this->_role_level) {
      $this->dbback_city->select('id as sub_department_id');
      $this->dbback_city->where('status', 1);
      $this->dbback_city->where('company_id', $this->_company_id);
      $department_id_arr = $this->dbback_city->get($this->_tb2)->result_array();
    } else {
      //查询字段
      $this->dbback_city->select('sub_department_id');
      //查询条件
      $where_cond = array('main_department_id' => $main_department_id, 'is_effective' => 1, $access_str => 1);
      $this->dbback_city->where($where_cond);
      //返回结果
      $department_id_arr = $this->dbback_city->get($this->_tbl)->result_array();
    }
    return $department_id_arr;
  }

  /**
   * 根据主门店id，获得相关数据id
   * @param int $main_department_id 主门店ID
   * @param string $access_str 待查询数据范围字段名
   * @return array 返回数据
   */
  public function get_id_by_main_id($main_department_id)
  {
    //查询字段
    $this->dbback_city->select('id');
    //查询条件
    $where_cond = array('main_department_id' => $main_department_id);
    $this->dbback_city->where($where_cond);
    //返回结果
    $department_id_arr = $this->dbback_city->get($this->_tbl)->result_array();
    return $department_id_arr;
  }

  /**
   * 根据主门店id，获得所有数据
   * @param int $main_department_id 主门店ID
   * @param string $access_str 待查询数据范围字段名
   * @return array 返回数据
   */
  public function get_data_by_main_id($main_department_id)
  {
    //查询字段
    $this->dbback_city->select('*');
    //查询条件
    $where_cond = array('main_department_id' => $main_department_id);
    $this->dbback_city->where($where_cond);
    //返回结果
    $department_id_arr = $this->dbback_city->get($this->_tbl)->result_array();
    return $department_id_arr;
  }

  /**
   * 根据被关联门店id，获得相关数据id
   * @param int $sub_department_id 主门店ID
   * @param string $access_str 待查询数据范围字段名
   * @return array 返回数据
   */
  public function get_id_by_sub_id($sub_department_id)
  {
    //查询字段
    $this->dbback_city->select('id');
    //查询条件
    $where_cond = array('sub_department_id' => $sub_department_id);
    $this->dbback_city->where($where_cond);
    //返回结果
    $department_id_arr = $this->dbback_city->get($this->_tbl)->result_array();
    return $department_id_arr;
  }

  /**
   * 根据主门店id，数据归属门店id，判断是否有权限
   * @param string $main_department_id 主门店ID
   * @param array $owner_arr 数据归属
   * @param string $node_id 权限节点id
   * @return array 返回数据
   */
  public function check($main_department_id = '', $owner_arr = array(), $node_id = '')
  {
    $result = false;
    //判断登录经纪人是否是总经理角色
    if (1 == $this->_role_level) {
      $result = true;
    } else {
      $main_department_id = intval($main_department_id);
      //数据所属门店id
      if (isset($owner_arr['department_id']) && !empty($owner_arr['department_id'])) {
        $sub_department_id = intval($owner_arr['department_id']);
      }
      //判断是否是自己门店的房客源数据
      if (isset($sub_department_id) && $sub_department_id == $this->_department_id) {
        $result = true;
      } else {
        if (is_int($main_department_id) && is_int($sub_department_id) && !empty($main_department_id) && !empty($sub_department_id) && !empty($node_id)) {
          //查询字段
          $this->dbback_city->select('func_auth');
          //查询条件
          $where_cond = array('main_department_id' => $main_department_id, 'is_effective' => 1, 'sub_department_id' => $sub_department_id);
          $this->dbback_city->where($where_cond);
          //返回结果
          $func_result = $this->dbback_city->get($this->_tbl)->result_array();
          if (is_full_array($func_result)) {
            $func_auth_result = array();
            $func_auth = $func_result[0]['func_auth'];
            $func_arr = unserialize($func_auth);
            if (is_full_array($func_arr)) {
              foreach ($func_arr as $k => $v) {
                foreach ($v as $key => $value) {
                  $func_auth_result[] = $value;
                }
              }
            }
            if (is_full_array($func_auth_result)) {
              $result = in_array($node_id, $func_auth_result);
            }
          }
        }
      }
    }
    return $result;
  }

  /**
   * 根据主门店id、被关联门店id获取数据
   * @param int $main_department_id 主门店ID
   * @param int $sub_department_id 被关联门店ID
   * @return array 返回数据
   */
  public function get_data_by_main_sub_id($main_department_id, $sub_department_id)
  {
    //查询字段
    $this->dbback_city->select('*');
    //查询条件
    $this->dbback_city->where(array('main_department_id' => $main_department_id, 'sub_department_id' => $sub_department_id));
    //返回结果
    $return_data = $this->dbback_city->get($this->_tbl)->row_array();
    return $return_data;
  }

  /**
   * 插入数据
   * @param array $data 插入数据源数组
   * @return int 成功 返回插入成功后的id 失败 false
   */
  public function replace($data)
  {
    return $this->db_city->replace($this->_tbl, $data);
  }


  /**
   * 初始化门店关联权限
   * @param int $companyid 公司ID
   * @param int $main_department_id 主门店ID
   * @param array $department_id_arr 被关联门店id
   * @return array 返回多条公司记录组成的二维数组
   */
  public function init_department_area($company_id = 0, $main_department_id = 0, $department_id_arr = array(), $func_auth = '')
  {
    if (is_full_array($department_id_arr)) {
      foreach ($department_id_arr as $k => $v) {
        $insert_data = array(
          'company_id' => $company_id,
          'main_department_id' => $main_department_id,
          'sub_department_id' => intval($v),
          'func_auth' => $func_auth,
          'is_view_house' => 1,
          'is_house_match' => 1,
          'is_house_share_tasks' => 1,
          'is_house_allocate' => 1,
          'is_view_customer' => 1,
          'is_customer_match' => 1,
          'is_customer_share_tasks' => 1,
          'is_customer_allocate' => 1,
          'is_cooperation' => 1,
          'is_key' => 1,
          'is_employee' => 1,
          'is_blacklist' => 1,
          'is_work_count' => 1,
          'is_house_count' => 1,
          'is_customer_count' => 1,
          'is_effective' => 1,
        );
        $result = $this->replace($insert_data);
      }
      return $result;
    }
  }

  //根据主门店id和被关联门店id，插入或更新数据
  public function deal_into_data($param_arr = array())
  {
    $main_department_id = $param_arr['main_department_id'];
    $sub_department_id = $param_arr['sub_department_id'];

    $query_sql = 'select id from ' . $this->_tbl . ' where main_department_id = "' . $main_department_id . '" and sub_department_id = "' . $sub_department_id . '"';
    $query_result = $this->dbback_city->query($query_sql)->result_array();
    //更新数据
    if (is_full_array($query_result)) {
      $id = intval($query_result[0]['id']);
      $update_result = $this->update_by_id($id, $param_arr);
      return $update_result;
    } else {
      //添加数据
      $add_result = $this->replace_data($param_arr);
      return $add_result;
    }
  }

  //更新数据
  public function update_by_id($id, $update_data)
  {
    $this->db_city->where('id', $id);
    $this->db_city->update($this->_tbl, $update_data);
    return $this->db_city->affected_rows();
  }

  //添加数据
  public function replace_data($add_data)
  {
    return $this->db_city->replace($this->_tbl, $add_data);
  }

  //删除数据
  public function delete_data($where_cond = array(), $database = 'db_city')
  {
    if (is_full_array($where_cond)) {
      $del_result = $this->del($where_cond, $database, $this->_tbl);
    } else {
      $del_result = false;
    }
    return $del_result;
  }

}

/* End of file department_base_model.php */
/* Location: ./applications/models/department_base_model.php */
