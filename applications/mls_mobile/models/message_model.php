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
 * Message_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2015-01-19
 * @author          angel_in_us
 */
class Message_model extends MY_Model
{

  private $message;

  public function __construct()
  {
    parent::__construct();
    $this->message = 'message';
    $this->message_broker = 'message_broker';
    $this->load->model('message_base_model');//消息基类model
    $this->company_notice = 'company_notice'; //公司公告
    $this->company_notice_broker = 'company_notice_broker'; //公司公告经纪人
  }

  /**
   * 获取符合条件的信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_count_by_cond($where = array(), $database = 'db_city')
  {
    $count_num = 0;
    //查询条件
    if ($where != '') {
      $this->dbselect($database);
      $this->db->from($this->message);
      $this->db->join($this->message_broker, $this->message_broker . '.msg_id = ' . $this->message . '.id');
      $this->db->where($where);
      $result = $this->db->get();
      $count_num = count($result->result());
    }

    return intval($count_num);
  }

  /**
   * 获取符合条件的公司公告条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_count_by_cond_company_notice($where = array(), $database = 'db_city')
  {
    $count_num = 0;
    //查询条件
    if ($where != '') {
      $this->dbselect($database);
      $this->db->from($this->company_notice);
      $this->db->join($this->company_notice_broker, $this->company_notice_broker . '.n_id = ' . $this->company_notice . '.id');
      $this->db->where($where);
      $result = $this->db->get();
      $count_num = count($result->result());
    }

    return intval($count_num);
  }

  /**
   * 获取符合条件的公告对应的信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   信息-经纪人列表
   */

  function get_row_by_cond($where = array(), $offset = 0, $limit = 10,
                           $order_key = 'createtime', $order_by = 'DESC', $database = 'db_city')
  {
    $this->dbselect($database);
    $this->db->from($this->message);
    $this->db->join($this->message_broker, $this->message_broker . '.msg_id = ' . $this->message . '.id');
    $this->db->where($where);
    $this->db->order_by($this->message_broker . '.' . $order_key, $order_by);
    $this->db->limit($limit, $offset); //开始位置，数量
    $result = $this->db->get();
    return $result->result();
  }

  /**
   * 获取信息详情
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function get_result($id, $database = 'db_city')
  {
    $this->dbselect($database);
    $this->db->from($this->message);
    $this->db->where(array('id' => $id));
    $result = $this->db->get();
    return $result->result();
  }


  /**
   * 删除message_broker
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function message_broker_del($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->delete($this->message_broker, $where);
  }


  /**
   * 设为已读message_broker
   *
   * @access  protected
   * @param  string $id 参数
   * @return  array   结果
   */
  function message_broker_update($where = array(), $data, $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->update($this->message_broker, $where, $data);

  }
}

/* End of file message_model.php */
/* Location: ./application/mls/models/message_model.php */
