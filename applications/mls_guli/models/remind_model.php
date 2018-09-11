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
 * collections_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date            2014-12-28
 * @author          angel_in_us
 */
class Remind_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->remind = 'event_remind';
  }

  /**
   * 获取事件提醒
   *
   * @access    public
   * @param    array $where筛选条件 , array $like模糊查询字段,int $offset偏移量,int $limit每页数量
   * @return    array
   */
  function get_remind($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 0, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->remind, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $limit, 'limit' => $offset), $database);
    return $result;
  }

  /**
   * 获取事件提醒
   *
   * @access    public
   * @param    array $where筛选条件
   * @return    array
   */
  function get_remind_order($where = array(), $order = '', $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->remind, 'where' => $where, 'order_by' => $order), $database);
    return $result;
  }

  /**
   * 根据条件获取事件提醒数量
   * @date            2015-01-14
   * @author       angel_in_us
   */
  function get_remind_num($where = array(), $where_in = array(), $like = array(), $database = 'dbback_city')
  {
    $remind_num = $this->get_data(array('form_name' => $this->remind, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'select' => array('count(*) as num')), $database);
    return $remind_num[0]['num'];
  }


  /**
   * 根据id来删除事件
   * @date            2015-01-14
   * @author       angel_in_us
   */
  function del_remind_byid($remind_id = '')
  {
    $result = $this->del(array('id' => $remind_id), 'db_city', $this->remind);
    return $result;
  }

  /**
   * 添加事件提醒
   *
   * @access    public
   * @param    array $data 添加数据,string $database 数据库
   * @return    int
   */
  public function add_remind($data = array(), $database = 'db_city')
  {
    $result = $this->add_data($data, $database, $this->remind);
    return $result;
  }

  /**
   * 设置事件状态位
   *
   * @access    public
   * @param    array $data 添加数据,string $database 数据库
   * @return    int
   */
  public function set_status($remind_id, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $remind_id), $paramlist, 'db_city', $this->remind);
    return $result;
  }


}

/* End of file notice_model.php */
/* Location: ./application/mls_guli/models/notice_model.php */
