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
 * City_model CLASS
 *
 * 城市业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("City_base_model");

class City_model extends City_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->city = 'city';
  }

  /**
   * 获得城市
   */
  public function get_city($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback')
  {
    $data = $this->get_data(array('form_name' => $this->city, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $data;
  }

  /**
   * 根据ID获得详情
   */
  public function get_city_by_id($id = '', $database = 'dbback')
  {
    $wherecond = array('id' => $id);
    $userData = $this->get_data(array('form_name' => $this->city, 'where' => $wherecond), $database);
    return $userData;

  }

  /**
   * 获取城市总数
   */
  function get_city_num($where, $database = 'dbback')
  {
    $node = $this->get_data(array('form_name' => $this->city, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $node[0]['num'];
  }

  /**
   * 添加城市
   */
  function add_city($paramlist = array(), $database = 'db')
  {
    $result = $this->add_data($paramlist, $database, $this->city);
    return $result;
  }

  /**
   * 修改城市
   */
  function modify_city($id, $paramlist = array(), $database = 'db')
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, $database, $this->city);
    return $result;
  }

  /**
   * 删除城市
   */
  function del_city($id = '')
  {
    $result = $this->del(array('id' => $id), 'db', $this->city);
    return $result;
  }


}

/* End of file City_model.php */
/* Location: ./app/models/City_model.php */
