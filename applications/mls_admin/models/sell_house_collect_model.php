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
 * Sell_house_collect_model CLASS
 *
 * 二手房采集内容数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Sell_house_collect_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->sell_house_collect = 'sell_house_collect';
  }

  /**
   * 获得所有二手房采集内容
   */
  public function get_collect($where = array(), $order_by, $offset = 0, $pagesize = 0, $select = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'order_by' => $order_by, 'limit' => $offset, 'offset' => $pagesize, 'select' => $select), $database);
    return $user;
  }

  /**
   * 获取二手房采集内容总数
   */
  function get_num($where = array(), $database = 'dbback')
  {
    $user = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $user[0]['num'];
  }
}

/* End of file sell_house_collect_model.php */
/* Location: ./application/models/sell_house_collect_model.php */
