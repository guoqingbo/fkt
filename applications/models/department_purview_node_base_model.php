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
 * Agency_permission_node_base_model CLASS
 *
 * 权限模块添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Department_purview_node_base_model extends MY_Model
{
  /**
   * 权限模块表
   * @var string
   */
  private $_tbl = 'department_purview_father_node';
  private $_tbl_list = 'department_purview_node';

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
    $this->_mem_key = $city . '_department_purview_node_base_model_';
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
   * 获取所有的模块，用于缓存
   * @return array
   */
  public function get_all()
  {
    $mem_key = $this->_mem_key . 'get_all';
    $this->mc->delete($mem_key);
    $cache = $this->mc->get($mem_key);
    $whereAll = " l.mid = m.id where l.status = 1 order by l.pid ASC";
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $modules = $cache['data'];
    } else {
      $modules = $this->get_all_by($whereAll);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $modules), 86400);
    }
    return $modules;
  }

  /**
   * 通过是否需要权限字段来获取所有模块
   * @param int $init_auth 是否需要权限判断 1 需要权限 0 无需权限
   * @return array
   */
  public function get_all_by_init_auth($init_auth = 1)
  {
    $modules = $this->get_all();
    $new_modules = array();
    if (is_full_array($modules)) {
      foreach ($modules as &$v) {
        if ($v['init_auth'] == 1) {
          $new_modules[] = $v;
        }
      }
    }
    return $new_modules;
  }


  /**
   * 通过模块编号获取记录
   * @param int $id 模块编号
   * @return array 记录组成的一维数组
   */
  public function get_by_id($id)
  {
    $modules = $this->get_all();
    $new_modules = array();
    if (is_full_array($modules)) {
      foreach ($modules as &$v) {
        if ($v['pid'] == $id) {
          $new_modules = $v;
          break;
        }
      }
    }
    return $new_modules;
  }

  public function get_all_by_id($where = '', $start = -1, $limit = 20,
                                $order_key = 'order', $order_by = 'desc')
  {
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
    return $this->dbback_city->count_all_results($this->_tbl_list);
  }

  /**
   * 管理后台获取权限模块列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条记录组成的二维数组
   */
  public function get_all_by($where = '', $start = -1, $limit = 20)
  {
    //排序条件
    if ($start >= 0 && $limit > 0) {
      $where = $where . " limit " . $start . "," . $limit;
    }
    $sql = "select pid,name,pname,mid,l.status as status from department_purview_node l LEFT JOIN department_purview_father_node m on " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;

  }

  /**
   * 获取权限模块的所有内容
   *
   */
  public function get_all_by_modules()
  {
    $sql = "select * from department_purview_father_node ";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  public function get_modules_by($id)
  {
    $sql = "select id,name from department_purview_node where status=1 and id = '" . $id . "'";
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;
  }

  //获得所有的权限节点
  public function get_all_node_serialize()
  {
    $all_father_node = $this->get_all_by_modules();
    $all_child_node = $this->get_all();
    $result_arr = array();
    if (is_full_array($all_father_node) && is_full_array($all_child_node)) {
      foreach ($all_father_node as $k => $v) {
        $result_arr[$v['id']] = array();
      }
      foreach ($result_arr as $k => $v) {
        foreach ($all_child_node as $key => $value) {
          if ($value['mid'] == $k) {
            $result_arr[$k][] = $value['pid'];
          }
        }
      }
    }

    $result_str = '';
    if (is_full_array($result_arr)) {
      $result_str = serialize($result_arr);
    }
    return $result_str;
  }

  //根据权限节点，重构数据
  public function get_node_serialize_by_child_node($child_node = array())
  {
    $all_father_node = $this->get_all_by_modules();
    $all_child_node = $this->get_all();
    //根据设置的权限节点，获得数据。
    $set_child_node = array();
    if (is_full_array($child_node)) {
      foreach ($all_child_node as $k => $v) {
        foreach ($child_node as $key => $value) {
          if ($value == $v['pid']) {
            $set_child_node[] = $v;
          }
        }
      }
    }

    $result_arr = array();
    if (is_full_array($all_father_node) && is_full_array($set_child_node)) {
      foreach ($all_father_node as $k => $v) {
        $result_arr[$v['id']] = array();
      }
      foreach ($result_arr as $k => $v) {
        foreach ($set_child_node as $key => $value) {
          if ($value['mid'] == $k) {
            $result_arr[$k][] = $value['pid'];
          }
        }
      }
    }
    //去除值为空的键
    foreach ($result_arr as $k => $v) {
      if (empty($v)) {
        unset($result_arr[$k]);
      }
    }
    $result_str = '';
    if (is_full_array($result_arr)) {
      $result_str = serialize($result_arr);
    }
    return $result_str;
  }


  /**
   * 插入权限模块数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的权限组id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl_list, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl_list, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新权限模块数据
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
    $this->db_city->where_in('pid', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl_list, $update_data);
    } else {
      $this->db_city->update($this->_tbl_list, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除权限模块数据
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
      $this->db_city->delete($this->_tbl);
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
    $this->db_city->from($this->_tbl);
    $this->db_city->truncate();
  }
}

/* End of file department_purview_node_base_model.php */
/* Location: ./applications/models/department_purview_node_base_model.php */
