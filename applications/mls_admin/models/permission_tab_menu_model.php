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
 * permission_tab_menu_model CLASS
 *
 * 权限功能菜单菜单添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("permission_tab_menu_base_model");

class permission_tab_menu_model extends permission_tab_menu_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'permission_tab';
    $this->_tbl2 = 'permission_module';
    $this->_tbl3 = 'permission_list';
  }

  public function get_list_by($where, $start = -1, $limit = 20,
                              $order_key = 'id', $order_by = 'ASC')
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl1}.*,{$this->_tbl2}.name AS module_name");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl1);
    $this->dbback_city->join($this->_tbl2, "{$this->_tbl1}.module_id = {$this->_tbl2}.id");
    //$this->dbback_city->join($this->_tbl3, "{$this->_tbl1}.pid = {$this->_tbl3}.pid");

    //排序条件
    $this->dbback_city->order_by($this->_tbl1 . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取全部权限节点
   */
  public function get_all_list()
  {
    //查询字段
    $this->dbback_city->select("{$this->_tbl3}.pid,{$this->_tbl3}.pname");
    $this->dbback_city->from($this->_tbl3);
    return $this->dbback_city->get()->result_array();

  }

  /**
   * 删除权限功能数据
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

}

/* End of file permission_tab_menu_base_model.php */
/* Location: ./app/models/permission_tab_menu_base_model.php */
