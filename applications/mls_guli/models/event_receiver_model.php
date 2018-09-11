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
 * Event_receiver_model CLASS
 *
 * 事件接收人模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date            2014-12-28
 * @author          angel_in_us
 */
class Event_receiver_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->event_receiver = 'event_receiver';
  }

  /**
   * 根据事件接受者id获取事件id
   *
   * @access    public
   * @param    array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return    array
   */
  function get_event_by_receiver($receiver_id, $database = 'dbback_city')
  {
    $where = array('receiver_id' => $receiver_id);
    $select = array('event_id');
    $result = $this->get_data(array('select' => $select, 'form_name' => $this->event_receiver, 'where' => $where), $database);
    return $result;
  }

  /**
   * 添加
   *
   * @access    public
   * @param    array $data 添加数据,string $database 数据库
   * @return    int
   */
  public function add_receiver($data = array(), $database = 'db_city')
  {
    $result = $this->add_data($data, $database, $this->event_receiver);
    return $result;
  }

}

/* End of file notice_model.php */
/* Location: ./application/mls_guli/models/notice_model.php */
