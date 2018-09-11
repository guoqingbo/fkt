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
 * Purview_department_role_base_model CLASS
 *
 * 门店权限角色添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Purview_department_group_base_model extends MY_Model
{

  /**
   * 公司权限角色表
   * @var string
   */
  private $_tbl = 'purview_department_group';

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
    $this->_mem_key = $city . '_purview_department_role_base_model_';
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
   * 获取门店权限角色列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where)
  {
//    $time = time();
//    $sql = "select b.truename,a.name as store_name,s.name as name,b.role_id,s.id as sid from signatory_info b
//                left join department a on a.id = b.department_id
//                left join purview_department_group p on b.role_id = p.id
//                left join purview_system_group s on s.id = p.system_group_id
//                     where expiretime > " . $time . " and b.status=1 " . $where . " order by role_id ASC";
//    $info = $this->dbback_city->query($sql)->result_array();
      //员工姓名 truename 门店名store_name 角色名name 角色id role_id 系统角色id sid

      $info = $this->dbback_city
          ->select("truename,role_id,role_level")
          ->from("signatory_info")
          ->where("status = 1 " . $where)
          ->get()
          ->result_array();
    return $info;
  }

  //下拉列表框数据
  public function get_department_norepeat($where)
  {
    $sql = "select id,name as store_name from department {$where}";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  public function get_system_group_id($role_id)
  {
      $system_group_id = [];
      $result = $this->dbback_city
          ->select("system_group_id")
          ->where_in("id", explode(",", $role_id))
          ->get($this->_tbl)
          ->result_array();
      if (!empty($result)) {
          foreach ($result as $key => $val) {
              $system_group_id[] = $val['system_group_id'];
          }
      }
      return $system_group_id;
  }

  public function get_func_by_id($id)
  {
//    $sql = "select func_auth from purview_department_group where id = " . $id;
//    $result = $this->db_city->query($sql)->row_array();
//    $func = $result['func_auth'];
//    $func_new = unserialize($func);
      $id = explode(",", $id);
      $result = $this->db_city
          ->select("func_auth")
          ->where_in("id", $id)
          ->get("purview_department_group")
          ->result_array();
      $func_new = [];
      if (!empty($result)) {
          foreach ($result as $key => $val) {
              $func_item = unserialize($val['func_auth']);
              foreach ($func_item as $key1 => $val1) {
                  if (is_array($func_new[$key1])) {
                      $func_new[$key1] = array_merge($func_new[$key1], $val1);
                  } else {
                      $func_new[$key1] = $val1;
                  }
                  $func_new[$key1] = array_unique($func_new[$key1]);
              }
          }
      }
    return $func_new;
  }

  //获取菜单栏权限
  public function get_menu_by($id, $cid)
  {
    $id = $id;
    $department_id = $cid;
    $sql = "select func_auth from purview_department_group where id = " . $id . " and department_id = " . $department_id;
    $result = $this->dbback_city->query($sql)->row_array();

    if (!empty($result)) {
      $result_new = unserialize($result['func_auth']);
      foreach ($result_new as $key => $val) {
        $menu_auth[] = $key;
      }
      return $menu_auth;
    } else {
      $sql = "select func_auth from purview_system_group where id = " . $id;
      $result = $this->dbback_city->query($sql)->row_array();
      $result_new = unserialize($result['func_auth']);
      foreach ($result_new as $key => $val) {
        $menu_auth[] = $key;
      }
      return $menu_auth;
    }
  }

  public function get_func_by($id, $cid)
  {
    $id = $id;
    $department_id = $cid;
    $sql = "select func_auth from purview_department_group where id = " . $id . " and department_id = " . $department_id;
    $result = $this->dbback_city->query($sql)->row_array();

    if (!empty($result)) {
      $result_new = unserialize($result['func_auth']);
      foreach ($result_new as $key => $val) {
        foreach ($val as $k => $v) {
          $func_auth[] = $v;
        }
      }
      return $func_auth;
    } else {
      $sql = "select func_auth from purview_system_group where id = " . $id;
      $result = $this->dbback_city->query($sql)->row_array();
      $result_new = unserialize($result['func_auth']);
      foreach ($result_new as $key => $val) {
        foreach ($val as $k => $v) {
          $func_auth[] = $v;
        }
      }
      return $func_auth;
    }
  }

  /**
   * 更新门店权限角色数据
   * @param array $update_data 更新的数据源数组
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
    $this->db_city->where_in('system_group_id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {

    if ($this->_tbl == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($this->_tbl);

    return $this->db_city->affected_rows();
  }

  //根据id保存
  public function save_group_func_by_id($equipment, $id)
  {
    $func_auth_new = array();//功能权限数组
    if (!empty($equipment)) {
      foreach ($equipment as $key => $val) {
        $k = reset(explode("/", $val));
        $v = end(explode("/", $val));
        $func_auth_new[$k][] = $v;
      }
    }
    //序列化数组
    $func_auth = serialize($func_auth_new);
    //print_r($func_auth_new);
    //print_R($func_auth);die();
    $sql = "update purview_department_group set func_auth = '" . $func_auth . "' where id ='" . $id . "'";
    $this->db_city->query($sql);
    $result = $this->db_city->affected_rows();
    return $result;

  }

  //变更某公司下的某角色权限
  public function save_group_func_by_company($equipment, $company_id, $system_group_id)
  {
    $func_auth_new = array();//功能权限数组
    if (!empty($equipment)) {
      foreach ($equipment as $key => $val) {
        $k = reset(explode("/", $val));
        $v = end(explode("/", $val));
        $func_auth_new[$k][] = $v;
      }
    }
    //序列化数组
    $func_auth = serialize($func_auth_new);
    $sql = "update purview_department_group set func_auth = '" . $func_auth . "' where company_id='" . $company_id . "' and system_group_id='" . $system_group_id . "'";
    $this->db_city->query($sql);
    $result = $this->db_city->affected_rows();
    return $result;

  }

  //变更某些门店下的某角色权限
  public function save_group_func_by_department($equipment, $department_arr, $system_group_id)
  {
    $department_str = '';
    if (is_full_array($department_arr)) {
        $department_str = trim(implode(',', $department_arr), ",");
    }
    $func_auth_new = array();//功能权限数组
    if (!empty($equipment)) {
      foreach ($equipment as $key => $val) {
        $k = reset(explode("/", $val));
        $v = end(explode("/", $val));
        $func_auth_new[$k][] = $v;
      }
    }
    //序列化数组
    $func_auth = serialize($func_auth_new);
    $sql = "update purview_department_group set func_auth = '" . $func_auth . "' where department_id in (" . $department_str . ") and system_group_id='" . $system_group_id . "'";
    $this->db_city->query($sql);
    $result = $this->db_city->affected_rows();
    return $result;

  }

  /**
   * 根据条件获取角色编号
   * @param int $company_id 公司编号
   */
  public function get_id_by_where_cond($where_cond)
  {
    $this->dbback_city->select('id');
    //查询条件
    $this->dbback_city->where($where_cond);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 通过门店权限角色编号获取权限
   * @param int $id 编号
   * @return array 记录组成的一维数组
   */
  public function get_func_auth_by_id($id)
  {
      $id = explode(',', $id);
    $info = array();
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
      $this->dbback_city->where_in('id', $id);
      $result = $this->dbback_city->get($this->_tbl)->result_array();

      $result_func_auth = [];
      if (!empty($result)) {
          foreach ($result as $key => $val) {
              $result_new_item = unserialize($val['func_auth']);
              if (is_array($result_new_item) && !empty($result_new_item)) {
                  foreach ($result_new_item as $key => $val) {
                      $result_func_auth = array_merge($result_func_auth, $val);
                  }
              }
          }
    }
      $result_func_auth = array_unique($result_func_auth);
    return $result_func_auth;
  }

  /**
   * 获取该表中的所有门店
   * @param int $company_id 公司编号
   */
  public function get_all_department_id()
  {
    $result_arr = false;

    $sql = "SELECT DISTINCT(department_id),company_id FROM `purview_department_group`";
    $query = $this->dbback_city->query($sql);
    $result_arr = $query->result_array();
    return $result_arr;
  }

}

/* End of file purview_department_role_base_model.php */
/* Location: ./applications/models/purview_department_role_base_model.php */
