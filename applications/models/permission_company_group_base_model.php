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
class Permission_company_group_base_model extends MY_Model
{

  /**
   * 公司权限角色表
   * @var string
   */
  private $_tbl = 'permission_company_group';

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
    $this->_mem_key = $city . '_permission_company_role_base_model_';
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
  public function get_all_by($where)
  {
    $time = time();
    $sql = "select b.truename,a.name as store_name,s.name as name,b.role_id,s.id as sid from broker_info b
                left join agency a on a.id = b.agency_id
                left join permission_company_group p on b.role_id = p.id
                left join permission_system_group s on s.id = p.system_group_id 
                     where expiretime > " . $time . " and b.status=1 " . $where . " order by role_id ASC";
    $info = $this->dbback_city->query($sql)->result_array();
    return $info;
  }

  //下拉列表框数据
  public function get_agency_norepeat($where)
  {
    $sql = "select id,name as store_name from agency {$where}";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  public function get_system_group_id($role_id)
  {
    $sql = "select system_group_id from {$this->_tbl} where id = {$role_id}";
    $result = $this->dbback_city->query($sql)->row_array();
    return $result['system_group_id'];
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
    $info = array();
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    $result = $this->dbback_city->get($this->_tbl)->row_array();
    $result_new = unserialize($result['func_auth']);
    foreach ($result_new as $key => $val) {
      $sql = "select name from permission_modules where id= " . $key . ' and status = 1';
      $info = $this->dbback_city->query($sql)->row_array();
      $result_array[$key]['name'] = $info['name'];
      foreach ($val as $k => $v) {
        $sql = "select pname from permission_list where pid = " . $v . " and status = 1";
        $re = $this->dbback_city->query($sql)->row_array();
        $result_array[$key]['pname'][] = $re['pname'];
      }
    }
    return $result_array;
  }

  /**
   * 通过公司权限角色编号获取权限
   * @param int $id 编号
   * @return array 记录组成的一维数组
   */
  public function get_func_auth_by_id($id)
  {
    $info = array();
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $id);
    $result = $this->dbback_city->get($this->_tbl)->row_array();
    $result_new = unserialize($result['func_auth']);
    $result_func_auth = array();
    if (is_array($result_new) && !empty($result_new)) {
      foreach ($result_new as $key => $value) {
        foreach ($value as $k => $v) {
          $result_func_auth[] = $v;
        }
      }
    }
    return $result_func_auth;
  }

  //获取菜单栏权限
  public function get_menu_by($id, $cid)
  {
    $id = $id;
    $company_id = $cid;
    $sql = "select func_auth from permission_company_group where id = " . $id . " and company_id = " . $company_id;
    $result = $this->dbback_city->query($sql)->row_array();

    if (!empty($result)) {
      $result_new = unserialize($result['func_auth']);
      foreach ($result_new as $key => $val) {
        $menu_auth[] = $key;
      }
      return $menu_auth;
    } else {
      $sql = "select func_auth from permission_system_group where id = " . $id;
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
    $company_id = $cid;
    $sql = "select func_auth from permission_company_group where id = " . $id . " and company_id = " . $company_id;
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
      $sql = "select func_auth from permission_system_group where id = " . $id;
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

  //保存功能
  public function save_group_func($equipment, $id)
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
    $sql = "update permission_company_group set func_auth = '" . $func_auth . "' where id ='" . $id . "'";
    $this->db_city->query($sql);
    $result = $this->db_city->affected_rows();
    return $result;

  }


  public function get_func_by_id($id)
  {
    $sql = "select func_auth from permission_company_group where id = " . $id;
    $result = $this->db_city->query($sql)->row_array();
    $func = $result['func_auth'];
    $func_new = unserialize($func);
    return $func_new;
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
   * 获取该表中的所有公司
   * @param int $company_id 公司编号
   */
  public function get_all_company_id()
  {
    $result_arr = false;

    $sql = "SELECT DISTINCT company_id FROM `permission_company_group`";
    $query = $this->dbback_city->query($sql);
    $result_arr = $query->result_array();
    return $result_arr;
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
   * 更新公司权限角色数据
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
}

/* End of file Permission_company_role_base_model.php */
/* Location: ./applications/models/Permission_company_role_base_model.php */
