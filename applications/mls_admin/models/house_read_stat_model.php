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
 * Issue_msg_model CLASS
 *
 * 后台发布消息模型类
 *
 * @package       MLS
 * @subpackage    Models
 * @category      Models
 * @author        angel_in_us
 * @date          2015-08-05
 */
class House_read_stat_model extends MY_Model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->stat_collect_view = 'stat_collect_view';  //每日房源查看量
  }


  /**
   * 获取总数
   */
  function get_num($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->stat_collect_view, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $result[0]['num'];
  }

  /**
   * 获得所有查看详情
   */
  public function get_issue_msg($where = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->stat_collect_view, 'where' => $where, 'order_by' => 'ymd', 'limit' => $offset, 'offset' => $pagesize), $database);
    return $result;
  }
}

/* End of file house_read_stat_model.php */
