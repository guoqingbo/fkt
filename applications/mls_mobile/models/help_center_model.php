<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * help_center_model MODEL CLASS
 *
 * 区属板块管理 控制器
 *
 * @package         MLS
 * @subpackage      MODEL
 * @category        MODEL
 * @author          lu
 */
class Help_center_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /** 查询主菜单 */
  public function get_all_parents()
  {
    $this->dbback_city->select('id, title');
    $this->dbback_city->from('help_center');
    $this->dbback_city->where('is_parent = 1');

    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 获取子菜单内容 */
  public function get_children($id)
  {
    $where = "parent_id = " . $id;
    $this->dbback_city->select('*');
    $this->dbback_city->from('help_center');
    $this->dbback_city->where($where);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  /** 根据ID查询主菜单标题*/
  public function get_parent_name($id)
  {
    $where = "id =" . $id;
    $this->dbback_city->select('title');
    $this->dbback_city->from('help_center');
    $this->dbback_city->where($where);
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /**
   * 首页控制台获取5条子菜单
   */
  public function get_chiledren_by_workbench()
  {
    $where = "`is_parent` = 0";
    $this->dbback_city->select(array('id', 'title', 'parent_id'));
    $this->dbback_city->from('help_center');
    $this->dbback_city->where($where);
    $this->dbback_city->limit(5);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

}
