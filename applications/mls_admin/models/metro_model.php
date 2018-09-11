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
 * 地铁线路数据模型类
 *
 * @package         mls
 * @subpackage      Models
 * @category        Models
 * @author          wang
 */
//load_m('Metro_base_model');
class Metro_model extends MY_Model
{
  /**
   * 地铁线路里表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_line_tbl = 'metro_line';

  /**
   * 地铁站点表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_site_tbl = 'metro_site';

  public function __construct()
  {
    parent::__construct();

    //设置地铁线路表名称
    // parent::set_metro_line_tbl('metro_line');

    //设置地铁站点表名称
    // parent::set_metro_site_tbl('metro_site');
  }

  /**
   * 获得地铁线路
   */
  public function get_metro_line($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_metro_line_tbl, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 获得地铁站点
   */
  public function get_metro_site($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $data = $this->get_data(array('form_name' => $this->_metro_site_tbl, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 获取地铁线路总数
   */
  function get_metro_line_num($where, $database = 'dbback_city')
  {
    $node = $this->get_data(array('form_name' => $this->_metro_line_tbl, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 获取地铁站点总数
   */
  function get_metro_site_num($where, $database = 'dbback_city')
  {
    $node = $this->get_data(array('form_name' => $this->_metro_site_tbl, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加地铁线路
   */
  function add_metro_line($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->_metro_line_tbl);
    return $result;
  }

  /**
   * 添加站点
   */
  function add_metro_site($paramlist = array(), $database = 'db_city')
  {
    $result = $this->add_data($paramlist, $database, $this->_metro_site_tbl);
    return $result;
  }

  /**
   * 根据ID获得地铁详情
   */
  public function get_metro_line_by_id($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->_metro_line_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 根据ID获得站点详情
   */
  public function get_metro_site_by_id($id = '', $database = 'dbback_city')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->_metro_site_tbl, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 修改地铁线路
   */
  function modify_metro_line($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_metro_line_tbl);
    return $result;
  }

  /**
   * 修改站点
   */
  function modify_metro_site($id, $paramlist = array(), $database = 'db_city')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->_metro_site_tbl);
    return $result;
  }

  /**
   * 删除站点
   * @param string $commid 站点ID
   * @return 0 or 1
   */
  function del_metro_site($id = '')
  {
    $result = $this->del(array('id' => $id), 'db_city', $this->_metro_site_tbl);
    return $result;
  }

}

/* End of file district_model.php */
/* Location: ./applications/mls_admin/models/district_model.php */
