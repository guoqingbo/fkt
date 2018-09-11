<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Buy_match_model CLASS
 *
 * 群发站点数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Mass_site_base_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->mass_site = 'mass_site';
  }

  /**
   * 获得所有群发站点
   */
  public function get_mass_site($offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $result;
  }

  /**
   * 根据群发站点ID获得详情
   */
  public function getinfo_byid($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->mass_site, 'where' => $wherecond), $database);
    return $result;
  }

  /**
   * 获取群发站点总数
   */
  function get_num($database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'select' => array('count(*) as num')), $database);
    return $result[0]['num'];
  }

  /**
   * 添加群发站点
   */
  function add($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->mass_site);
    return $result;
  }

  /**
   * 修改群发站点
   */
  function modify($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->mass_site);
    return $result;
  }

  /**
   * 删除群发站点
   */
  function del_mass_site($id = '')
  {
    $result = $this->del(array('id' => $id), 'db_city', $this->mass_site);
    return $result;
  }

  /**
   * 启用群发站点
   */
  function open($id, $paramlist = array('status' => 1), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->mass_site);
    return $result;
  }

  /**
   * 关闭群发站点
   */
  function close($id, $paramlist = array('status' => 0), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->mass_site);
    return $result;
  }

}

/* End of file mass_site_base_model.php */
/* Location: ./application/models/mass_site_base_model.php */
