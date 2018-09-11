<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS-admin
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * buy_customer_info_model CLASS
 *
 * 后台帮助中心
 *
 * @package         MLS-admin
 * @subpackage      Models
 * @category        Models
 * @author          lujun
 */
class Help_center_model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  /** 查询主菜单 OK */
  public function get_all_parents()
  {
    $this->dbback->select('id, title');
    $this->dbback->from('help_center');
    $this->dbback->where('is_parent = 1');
    $this->dbback->order_by('orderby', 'desc');

    $data = $this->dbback->get()->result_array();
    return $data;
  }


  /** 根据ID查询主菜单标题*/
  public function get_parent_name($where)
  {
    $this->dbback->select('title, orderby');
    $this->dbback->from('help_center');
    $this->dbback->where($where);
    $data = $this->dbback->get()->row_array();
    return $data;
  }

  public function get_prev_parent_id($where)
  {
    $this->dbback->select('parent_id');
    $this->dbback->from('help_center');
    $this->dbback->where($where);
    $data = $this->dbback->get()->row_array();
    return $data['parent_id'];
  }

  /** 删除 */
  public function delete_data($where, $tab_name)
  {
    $this->db->where($where);
    $this->db->delete($tab_name);

    return $this->db->affected_rows();
  }

  /** 保存主菜单名字 */
  public function save_modify($tab_name, $data, $where)
  {
    $this->db->update($tab_name, $data, $where);
    return $this->db->affected_rows();
  }

  /** 获取子菜单内容 */
  public function get_children($id)
  {
    $where = "parent_id = " . $id;
    $this->dbback->select('*');
    $this->dbback->from('help_center');
    $this->dbback->where($where);
    $this->dbback->order_by('orderby', 'desc');
    $data = $this->dbback->get()->result_array();
    return $data;
  }

  /** 获取子项内容 */
  public function get_child_info($id)
  {
    $where = "id = " . $id;
    $this->dbback->select('*');
    $this->dbback->from('help_center');
    $this->dbback->where($where);

    $data = $this->dbback->get()->row_array();
    return $data;
  }

  /** 保存操作 */
  public function save_add($tab_name, $data)
  {
    $this->db->insert($tab_name, $data);
    return $this->db->affected_rows();
  }


}
