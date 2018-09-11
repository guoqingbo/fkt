<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 消息提醒类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Basic_setting_base_model CLASS
 *
 * 基本设置管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
class Checknotice_base_model extends MY_Model
{
  /**
   * 基础设置表名称
   * @var string
   */
  private $_tbl = 'message_pop';

  /**
   * 获取最新未查看需要弹窗的消息
   * @broker_id  当前经纪人id
   * @返回值   消息数组
   */
  public function get_new_pop_message($broker_id)
  {
    $t = time();
    $start = mktime(0, 0, 0, date("m", $t), date("d", $t), date("Y", $t));
    $end = mktime(23, 59, 59, date("m", $t), date("d", $t), date("Y", $t));
    $sql = "select p.id,title,message,url from message_pop p 
                left join message_broker b on p.id = b.msg_id where p.is_pop_open = 0 and b.broker_id = {$broker_id} and "
      . "p.createtime < {$end} and p.createtime > {$start} order by p.createtime desc";
    return $this->dbback_city->query($sql)->result_array();
  }

  /**
   * 更新弹窗消息数据
   * @param array $update_data 更新的数据源数组
   * @param int $id 编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

}
