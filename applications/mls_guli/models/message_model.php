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
 * @date            2015-01-19
 * @author          angel_in_us
 */
class Message_model extends MY_Model
{

  private $message;

  public function __construct()
  {
    parent::__construct();
    $this->message = 'message';
    $this->message_signatory = 'message_signatory';
    $this->load->model('message_base_model');//消息基类model

    $this->_tbl1 = 'detailed_follow';
    $this->_tbl2 = 'event_remind';
    $this->company_notice = 'company_notice'; //公司公告
    $this->company_notice_signatory = 'company_notice_signatory'; //公司公告经纪人
  }

  /**
   * 获取符合条件的信息条数
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_count_by_cond($where = array(), $database = 'dbback_city')
  {
    $count_num = 0;
    //查询条件
    if ($where != '') {
      $this->dbselect($database);
      $this->db->from($this->message);
      $this->db->join($this->message_signatory, $this->message_signatory . '.msg_id = ' . $this->message . '.id');
      $this->db->where($where);
      $result = $this->db->get();
      $count_num = count($result->result());
    }

    return intval($count_num);
  }

  /**
   * 获取符合跟进提醒条件的信息条数
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_count_by_cond_smessage($where = array(), $database = 'dbback_city')
  {
    $count_num = 0;
    //查询条件
    if ($where != '') {
      $this->dbselect($database);
      $this->db->select("*");
      $this->db->from($this->_tbl2);
      $this->db->where($where);
      $result = $this->db->get();
      $count_num = count($result->result());
    }

    return intval($count_num);
  }

  /**
   * 获取符合提醒条件的信息具体内容
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_smessage_by($where, $start = -1, $limit = 20,
                                  $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl2);
    //$this->dbback_city->join($this->_tbl1,$this->_tbl2.'.detail_id = '.$this->_tbl1.'.id');

    //排序条件
    $this->dbback_city->order_by($this->_tbl2 . '.' . $order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 获取符合跟进条件的信息具体内容
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_detail_by($where)
  {
    //查询字段
    $this->dbback_city->select("*");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    $this->dbback_city->from($this->_tbl1);
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 通过id获取符合提醒条件的信息具体内容
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_event_by_id($id)
  {
    /* //查询字段
     $this->dbback_city->select("*");

     //查询条件
     $this->dbback_city->where("id=".$id);
     $this->dbback_city->from($this->_tbl1);
     //返回结果
     return $this->dbback_city->get()->row_array();
     */
    $sql = "select * from event_remind where id =" . $id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;
  }

  /**
   * 通过id获取符合跟进条件的信息具体内容
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的信息条数
   */
  public function get_detail_by_id($id)
  {
    $sql = "select * from detailed_follow where id =" . $id;
    $result = $this->dbback_city->query($sql)->row_array();
    return $result;
  }


  /**
   * 设为已完成跟进信息
   *
   * @access    protected
   * @param    string $id 参数
   * @return    array   结果
   */
  public function update_event_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->_tbl2;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);

    return $this->db_city->affected_rows();
  }

  public function update_event_by_ids($ids, $update_arr, $escape = TRUE)
  {
    $update_num = 0;

    if (!empty($ids) && is_array($update_arr) && !empty($update_arr)) {
      if (is_array($ids)) {
        $id_str = implode(',', $ids);
        $cond_where = "id IN (" . $id_str . ") ";
        $update_num = $this->update_event_by_cond($update_arr, $cond_where, $escape);
      } else {
        $cond_where = "id = " . $ids;
        $update_num = $this->update_event_by_cond($update_arr, $cond_where, $escape);
      }
    }

    return $update_num;
  }

  /**
   * 获取符合条件的公告对应的信息
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @param    int $offset 偏移数,默认值为0
   * @param    int $limit 每次取的条数，默认值为10
   * @param    string $order_key 排序字段，默认值
   * @param    string $order_by 升序、降序，默认降序排序
   * @return    array   信息-经纪人列表
   */

  function get_row_by_cond($where = array(), $offset = 0, $limit = 10,
                           $order_key = 'createtime', $order_by = 'DESC', $database = 'dbback_city')
  {
    $this->dbselect($database);
    $this->db->from($this->message);
    $this->db->join($this->message_signatory, $this->message_signatory . '.msg_id = ' . $this->message . '.id');
    $this->db->where($where);
    $this->db->order_by($this->message_signatory . '.' . $order_key, $order_by);
    $this->db->limit($limit, $offset); //开始位置，数量
    $result = $this->db->get();
    return $result->result_array();
  }

  /**
   * 获取符合条件的系统公告对应的信息
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @param    int $offset 偏移数,默认值为0
   * @param    int $limit 每次取的条数，默认值为10
   * @return    array   信息-经纪人列表
   */
  public function get_system_by_cond($where = array(), $offset = 0, $limit = 10,
                                     $database = 'dbback_city')
  {
    $this->dbselect($database);
    $this->db->from($this->message);
    $this->db->join($this->message_signatory, $this->message_signatory . '.msg_id = ' . $this->message . '.id');
    $this->db->where($where);
    $this->db->order_by("{$this->message}.is_top DESC,{$this->message_signatory}.createtime DESC");
    //echo "{$this->message_signatory}.is_top DESC,{$this->message_signatory}.createtime DESC";die();
    $this->db->limit($limit, $offset); //开始位置，数量
    $result = $this->db->get();
    return $result->result_array();
  }

  /**
   * 获取系统信息详情
   *
   * @access    protected
   * @param    string $id 参数
   * @return    array   结果
   */
  function get_result($id, $database = 'dbback_city')
  {
    $this->dbselect($database);
    $this->db->from($this->message);
    $this->db->where(array('id' => $id));
    $result = $this->db->get();
    return $result->row_array();
  }


  /**
   * 删除message_signatory
   *
   * @access    protected
   * @param    string $id 参数
   * @return    array   结果
   */
  function message_signatory_del($where = array(), $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->delete($this->message_signatory, $where);
  }


  /**
   * 设为已读message_signatory
   *
   * @access    protected
   * @param    string $id 参数
   * @return    array   结果
   */
  function message_signatory_update($where = array(), $data, $database = 'db_city')
  {
    $this->dbselect($database);
    return $this->db->update($this->message_signatory, $where, $data);

  }

  public function update_bulletin_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->message_signatory;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);

    return $this->db_city->affected_rows();
  }

  public function update_bulletin_by_ids($ids, $update_arr, $escape = TRUE)
  {
    $update_num = 0;

    if (!empty($ids) && is_array($update_arr) && !empty($update_arr)) {
      if (is_array($ids)) {
        $id_str = implode(',', $ids);
        $cond_where = "id IN (" . $id_str . ") ";
        $update_num = $this->update_bulletin_by_cond($update_arr, $cond_where);
      } else {
        $cond_where = "id = " . $ids;
        $update_num = $this->update_bulletin_by_cond($update_arr, $cond_where);
      }
    }

    return $update_num;
  }

  public function get_all_type()
  {
    $this->dbback_city->select('module');
    $result = $this->dbback_city->get('message_open_pop')->result_array();
    foreach ($result as $key => $val) {
      $new_arr[$key + 1] = $val['module'];
    }
    return $new_arr;
  }
}

/* End of file message_model.php */
/* Location: ./application/mls_guli/models/message_model.php */
