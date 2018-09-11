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
 * @date      2014-12-28
 * @author          angel_in_us
 */
class Notice_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->notice = 'notice';
  }

  /**
   * 获取notice表里的全部通知信息
   * @date      2015-01-14
   * @author       angel_in_us
   */
  function get_notice($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 10, $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->notice, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 根据id获取notice表里的详细通知信息
   * @date      2015-01-14
   * @author       angel_in_us
   */
  function get_notice_byid($where = array(), $database = 'db_city')
  {
    $result = $this->get_data(array('form_name' => $this->notice, 'where' => $where), $database);
    return $result;
  }

  /**
   * 获取notice表里的通知信息的数量
   * @date      2015-01-14
   * @author       angel_in_us
   */
  function get_notice_num($where = array(), $like = array(), $database = 'db_city')
  {
    $notice_num = $this->get_data(array('form_name' => $this->notice, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), $database);
    return $notice_num[0]['num'];
  }


  /**
   * 根据id来删除单个通知信息
   * @date      2015-01-14
   * @author       angel_in_us
   */
  function del_notice_byid($arr = array())
  {
    $result = $this->del($arr, 'db_city', $this->notice);
    return $result;
  }


  /**
   * 把用户所发布的通知消息入库到 notice 表里
   * @date      2015-01-15
   * @author       angel_in_us
   */
  public function add_notice($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->notice);
    return $result;
  }
}

/* End of file notice_model.php */
/* Location: ./application/mls/models/notice_model.php */
