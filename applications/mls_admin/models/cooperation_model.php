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
 * blacklist_model CLASS
 *
 * 房源成交模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Cooperation_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->cooperate = 'cooperate';
    $this->cooperate_attached = 'cooperate_attached';
  }


  /**
   * 根据合作 id 查询房源信息
   * @date      2015-01-26
   * @author       angel_in_us
   */
  function get_house_info_byids($where_in = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->cooperate_attached, 'where_in' => $where_in, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 根据合作 id 查询合作信息
   * @date      2015-01-27
   * @author       angel_in_us
   */
  function get_cooperation_byids($where_in = array(), $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->cooperate, 'where_in' => $where_in, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }
}

/* End of file cooperation_model.php */
/* Location: ./application/mls_admin/models/cooperation_model.php */
