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
 * Perssion_module_model CLASS
 *
 * 权限模块添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          LION
 */
load_m("Group_publish_base_model");

class Group_publish_model extends Group_publish_base_model
{
  private $group_sell_tbl = 'group_publish_sell';
  private $group_rent_tbl = 'group_publish_rent';
  private $mass_site = 'mass_site';
  private $anjuke_xiaoqu = 'anjuke_xiaoqu';
  private $anjuke_wuye = 'anjuke_wuye';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 添加出售群发需求信息
   *
   * @access  protected
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  public function add_sell_info($data_info)
  {
    $this->set_tbl($this->group_sell_tbl);
    return $this->add_info($data_info);
  }

  public function add_rent_info($data_info)
  {
    $this->set_tbl($this->group_rent_tbl);
    return $this->add_info($data_info);
  }


  //通过house_id ,broker_id 读取房源
  public function get_sell_publish($broker_id, $house_id)
  {
    $data = array();
    $this->dbback_city->select($this->group_sell_tbl . ".* ," . $this->mass_site . ".name");
    $where = $this->group_sell_tbl . ".broker_id = '$broker_id' and " . $this->group_sell_tbl . ".house_id = '$house_id'";
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->group_sell_tbl);
    $this->dbback_city->join($this->mass_site, "$this->group_sell_tbl.site_id =  $this->mass_site.id");
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  public function get_rent_publish($broker_id, $house_id)
  {
    $data = array();
    $this->dbback_city->select($this->group_sell_tbl . ".* ," . $this->mass_site . ".name");
    $where = $this->group_sell_tbl . ".broker_id = '$broker_id' and " . $this->group_sell_tbl . ".house_id = '$house_id'";
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->group_sell_tbl);
    $this->dbback_city->join($this->mass_site, "$this->group_sell_tbl.site_id =  $this->mass_site.id");
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  //通过house_id,broker_id,site_id 读取信息数量
  public function get_num_sell_publish($broker_id, $site_id, $house_id)
  {
    $data = array();
    $this->dbback_city->select("*");
    $where = "broker_id = '$broker_id' and house_id = '$house_id' and site_id = '$site_id'";
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->group_sell_tbl);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }


  //通过keyword 获取安居客小区信息
  public function get_ajk_xiaoqu($name)
  {
    $data = array();
    $this->dbback_city->select("*");
    $where = "name = '$name' ";
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->anjuke_xiaoqu);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

  //通过keyword 获取安居客小区信息
  public function get_ajk_wuye($name)
  {
    $data = array();
    $this->dbback_city->select("*");
    $where = "name = '$name' ";
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->anjuke_wuye);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }

}

/* End of file group_publish_model.php */
/* Location: ./applications/mls/models/group_publish_model.php */
