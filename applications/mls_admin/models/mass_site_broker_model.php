<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mass_site_broker_model
 * 经纪人群发网站关联
 * @author ccy
 */
class Mass_site_broker_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 数据列表
   */
  function select_mass_broker($id = '')
  {
    if ($id == '') {
      return $this->dbback_city->get(mass_site_broker)->result_array();
    } else {
      $this->dbback_city->where_in('id', $id);
      return $this->dbback_city->get(mass_site_broker)->result_array();
    }
  }

  /**
   * 群发站点
   */
  function site_list()
  {
    return $this->dbback_city->get(mass_site)->result_array();
  }
}
