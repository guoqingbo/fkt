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
 * Permission_system_role_base_model CLASS
 *
 * 系统权限角色添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Permission_system_group_base_model extends MY_Model
{

  /**
   * 系统权限角色表
   * @var string
   */
  private $_tbl1 = 'permission_system_group';
  private $_tbl2 = 'permission_company_group';

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
   * 返回系统默认系统权限角色
   * @return array
   */
  public function get_role()
  {
    $this->dbback_city->select('id, name,level,func_auth');
    $this->dbback_city->order_by("level", "ASC");
    //返回结果
    return $this->dbback_city->get($this->_tbl1)->result_array();
  }

  //获取权限用户组
  public function get_group($rid, $cid)
  {
    $role_id = $rid;
    $agency_id = $cid;
    $sql = "select level from permission_system_group s
                 left join permission_agency_group c on s.id = c.system_group_id where c.id = " . $role_id . " and agency_id = " . $agency_id;
    $result = $this->dbback_city->query($sql)->row_array();
    if (!empty($result)) {
      $level = $result['level'];
        $sql = "select c.id as id,name,system_group_id, s.level from permission_system_group s
                              left join permission_agency_group c on s.id = c.system_group_id where agency_id = " . $agency_id . " and s.level >= " . $level . " order by s.level";
      $result_new = $this->dbback_city->query($sql)->result_array();
      return $result_new;
    }
  }

    //获取登录用户的权限从门店
    public function get_user_level($rid, $cid)
    {
        $role_id = $rid;
        $agency_id = $cid;
        $sql = "select level from permission_system_group s left join permission_agency_group c on s.id = c.system_group_id where c.id = " . $role_id . " and agency_id = " . $agency_id;
        $result = $this->dbback_city->query($sql)->row_array();
        $level = 0;
        if (!empty($result)) {
            $level = $result['level'];
        }
        return $level;
    }

  //获取权限用户组从公司
  public function get_group_company($rid, $cid)
  {
    $role_id = $rid;
    $company_id = $cid;
    $sql = "select level from permission_system_group s
                 left join permission_company_group c on s.id = c.system_group_id where c.id = " . $role_id . " and company_id = " . $company_id;
    $result = $this->dbback_city->query($sql)->row_array();
    if (!empty($result)) {
      $level = $result['level'];
        $sql = "select c.id as id,name,system_group_id, s.level from permission_system_group s
                              left join permission_company_group c on s.id = c.system_group_id where company_id = " . $company_id . " and s.level >= " . $level . " order by s.level";
      $result_new = $this->dbback_city->query($sql)->result_array();
      return $result_new;
    }
  }

    //获取登录用户的权限从公司
    public function get_user_company_level($rid, $cid)
    {
        $role_id = $rid;
        $company_id = $cid;
        $sql = "select level from permission_system_group s left join permission_company_group c on s.id = c.system_group_id where c.id = " . $role_id . " and company_id = " . $company_id;
        $result = $this->dbback_city->query($sql)->row_array();
        $level = 0;
        if (!empty($result)) {
            $level = $result['level'];
        }
        return $level;
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
   * 获取系统权限角色列表页
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
    return $this->dbback_city->get($this->_tbl1)->result_array();
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
    return $this->dbback_city->get($this->_tbl1)->row_array();
  }


  /**
   * 通过系统权限角色编号获取记录
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
    return $this->dbback_city->get($this->_tbl1)->row_array();
  }


  /**
   * 插入系统权限角色数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl1, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl1, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 插入系统权限角色数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功后返回受影响的行数
   */
  public function add_group($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl1, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl1, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新系统权限角色数据
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
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl1, $update_data);
    } else {
      $this->db_city->update($this->_tbl1, $update_data);
    }
    return $this->db_city->affected_rows();
  }


  /**
   * 删除系统权限角色数据
   * @param int $id 编号
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
      $this->db_city->delete($this->_tbl1);
    }
    if ($this->db_city->affected_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }

  //清空数据库
  public function truncate()
  {
    $this->db_city->from($this->_tbl1);
    $this->db_city->truncate();
  }

  public function get_func_by_id($id)
  {
    if ($id) {
      $sql = "select func_auth from permission_system_group where id = " . $id;
      $result = $this->db_city->query($sql)->row_array();
      $func = $result['func_auth'];
      $func_new = unserialize($func);
      return $func_new;
    }
  }

  public function get_func_by_id_2($id)
  {
    if ($id) {
      $sql = "select func_auth from permission_system_group where id = " . $id;
      $result = $this->db_city->query($sql)->row_array();
      $func = $result['func_auth'];
      $func_new = unserialize($func);
      $func_new2 = array();
      foreach ($func_new as $key => $value) {
        if (is_array($func_new) && !empty($func_new)) {
          foreach ($value as $k => $v) {
            $func_new2[] = $v;
          }
        }
      }
      return $func_new2;
    }
  }

}

/* End of file permission_system_role_base_model.php */
/* Location: ./applications/models/permission_system_role_base_model.php */
