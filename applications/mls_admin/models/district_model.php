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
 * district_model CLASS
 *
 * 区属数据模型类
 *
 * @package         mls
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */

load_m('District_base_model');

class District_model extends District_base_model
{

  public function __construct()
  {
    parent::__construct();

    //设置区属表名称
    parent::set_district_tbl('district');

    //设置板块表名称
    parent::set_street_tbl('street');
  }

  /**
   * 获得区属
   */
  public function get_district($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_district_tbl, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 获得板块
   */
  public function get_street($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_street_tbl, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 获取区属总数
   */
  function get_district_num($where, $database = 'dbback_city')
  {
    $node = $this->get_data(array('form_name' => $this->_district_tbl, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 获取板块总数
   */
  function get_street_num($where, $database = 'dbback_city')
  {
    $node = $this->get_data(array('form_name' => $this->_street_tbl, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加区属
   */
  function add_district($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->_district_tbl);
    return $result;
  }

  /**
   * 添加板块
   */
  function add_street($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->_street_tbl);
    if ($result && !empty($paramlist['dist_id'])) {
      //清除缓存
      $mem_key = $this->_mem_key . '_get_street_bydist_' . $paramlist['dist_id'];
      $this->mc->delete($mem_key);
    }
    return $result;
  }

  /**
   * 根据区属名获得区属详情
   */
  public function get_district_by_district($district = '', $database = 'dbback_city')
  {
    $wherecond = array('district' => $district);
    $userData = $this->get_data(array('form_name' => $this->_district_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 根据ID获得区属详情
   */
  public function get_district_by_id($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->_district_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 根据板块名获得板块详情
   */
  public function get_street_by_street($street = '', $database = 'dbback_city')
  {
    $wherecond = array('streetname' => $street);
    $userData = $this->get_data(array('form_name' => $this->_street_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 根据ID获得板块详情
   */
  public function get_street_by_id($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->_street_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 修改区属
   */
  function modify_district($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_district_tbl);
    return $result;
  }

  /**
   * 修改板块
   */
  function modify_street($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_street_tbl);
    if ($result && !empty($paramlist['dist_id'])) {
      //清除缓存
      $mem_key = $this->_mem_key . '_get_street_bydist_' . $paramlist['dist_id'];
      $this->mc->delete($mem_key);
    }
    return $result;
  }

}

/* End of file district_model.php */
/* Location: ./applications/mls_admin/models/district_model.php */
