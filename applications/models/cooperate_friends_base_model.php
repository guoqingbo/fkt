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
 * Cooperate_base_model CLASS
 *
 * 房客源合作基类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Cooperate_friends_base_model extends MY_Model
{

  /**
   * 合作朋友圈好友列表表名
   * @var string
   */
  protected $tbl_friends = 'cooperate_friends';

  /**
   * 合作朋友圈好友申请列表表名
   * @var string
   */
  protected $tbl_apply = 'cooperate_friends_apply';

  /**
   * 合作朋友圈提醒消息关联表
   * @var string
   */
  protected $tbl_message = 'message_friends';


  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';

    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }

    $this->_select_fields = $select_fields_str;
  }


  /**
   * 返回需要查询的字段
   * @param void
   * @return string 查询字段
   */
  public function get_search_fields()
  {
    return $this->_select_fields;
  }

  /**
   * 更新申请状态
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function get_apply_by_id($id)
  {
    $id = intval($id);
    $arr = array();

    if ($id > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->where(array('id' => $id));
      $this->dbback_city->from($this->tbl_apply);
      $arr = $this->dbback_city->get()->row_array();
    }

    return $arr;
  }

  /**
   * 添加合作朋友圈申请
   * @param void
   * @return string 查询字段
   */
  public function add_apply($arr)
  {
    $tbl_name = $this->tbl_apply;
    $insert_id = 0;

    if (is_array($arr) && !empty($arr)) {
      $this->db_city->insert($tbl_name, $arr);

      //如果插入成功，则返回插入的id
      if (($this->db_city->affected_rows()) >= 1) {
        $insert_id = $this->db_city->insert_id();
      }
    }

    return $insert_id;
  }

  /**
   * 更新申请状态
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function update_apply($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->tbl_apply;

    $up_num = 0;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }

  /**
   * 添加到合作朋友数据库
   * @param void
   * @return string 查询字段
   */
  public function add_friend($arr)
  {
    $tbl_name = $this->tbl_friends;
    $insert_id = 0;

    if (is_array($arr) && !empty($arr)) {
      $this->db_city->insert($tbl_name, $arr);

      //如果插入成功，则返回插入的id
      if (($this->db_city->affected_rows()) >= 1) {
        $insert_id = $this->db_city->insert_id();
      }
    }

    return $insert_id;
  }

  /**
   * 更新朋友状态
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function update_friend($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->tbl_friends;

    $up_num = 0;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }

  /**
   * 添加到合作朋友消息关联库
   * @param void
   * @return string 查询字段
   */
  public function add_friend_message($arr)
  {
    $tbl_name = $this->tbl_message;
    $insert_id = 0;

    if (is_array($arr) && !empty($arr)) {
      $this->db_city->insert($tbl_name, $arr);

      //如果插入成功，则返回插入的id
      if (($this->db_city->affected_rows()) >= 1) {
        $insert_id = $this->db_city->insert_id();
      }
    }

    return $insert_id;
  }

  /**
   * 更新朋友消息关联库
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function update_friend_message($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->tbl_message;

    $up_num = 0;

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return $up_num;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);
    $up_num = $this->db_city->affected_rows();

    return $up_num;
  }

  /**
   * 获取符合条件的朋友申请信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_apply_list_by_cond($cond_where, $offset = 0, $limit = 10,
                                         $order_key = 'createtime', $order_by = 'DESC')
  {
    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);//查询字段
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }
    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    //查询条件
    $this->dbback_city->from($this->tbl_apply);
    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }

  /*
   * 根据双方broker_id获取申请信息
   * @parames int $cid
   * @return array
   */
  public function get_apply_by_broker_id($broker_id, $broker_id_friend, $status = 0)
  {
    $broker_id = intval($broker_id);
    $broker_id_friend = intval($broker_id_friend);
    $arr = array();

    if ($broker_id > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->where(array('broker_id_send' => $broker_id, 'broker_id_receive' => $broker_id_friend, 'status' => $status));
      $this->dbback_city->from($this->tbl_apply);
      $arr = $this->dbback_city->get()->row_array();
    }

    return $arr;
  }

  /*
     * 根据双方broker_id获取申请信息
     * @parames int $cid
     * @return array
     */
  public function get_friend_by_broker_id($broker_id, $broker_id_friend, $status = 1)
  {
    $broker_id = intval($broker_id);
    $broker_id_friend = intval($broker_id_friend);
    $arr = array();

    if ($broker_id > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }
      $where = "status = " . $status . "  and ((broker_id =" . $broker_id . " and broker_id_friend =" . $broker_id_friend . ") or (broker_id =" . $broker_id_friend . " and broker_id_friend =" . $broker_id . "))";
      //查询条件
      //$this->dbback_city->where(array('broker_id'=>$broker_id,'broker_id_friend'=>$broker_id_friend,'status'=>$status));
      $this->dbback_city->where($where);
      $this->dbback_city->from($this->tbl_friends);
      $arr = $this->dbback_city->get()->result_array();
    }

    return $arr;
  }

  /**
   * 获取符合条件的朋友总数
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_friends_num_by_cond($cond_where)
  {
    //合作信息表
    $tbl_name = $this->tbl_friends;

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);//查询字段
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }
    //查询条件
    $this->dbback_city->from($this->tbl_friends);
    $this->dbback_city->join('broker_info', "broker_info.broker_id = $this->tbl_friends.broker_id_friend");
    $arr_data = $this->dbback_city->count_all_results();

    return $arr_data;
  }


  /**
   * 获取符合条件的朋友信息列表
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   合作列表数组
   */
  public function get_friends_list_by_cond($cond_where, $offset = 0, $limit = 10,
                                           $order_key = 'createtime', $order_by = 'DESC')
  {
    //合作信息表
    $tbl_name = $this->tbl_friends;

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $this->dbback_city->select($select_fields);//查询字段
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }
    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    //查询条件
    $this->dbback_city->from($this->tbl_friends);
    $this->dbback_city->join('broker_info', "broker_info.broker_id = $this->tbl_friends.broker_id_friend");
    $arr_data = $this->dbback_city->get()->result_array();

    return $arr_data;
  }


  /**
   * 获取信息关联表
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int 更新影响行数
   */
  public function get_message_by_id($id)
  {
    $id = intval($id);
    $arr = array();

    if ($id > 0) {
      //查询字段
      if ($this->_select_fields) {
        $this->dbback_city->select($this->_select_fields);
      }

      //查询条件
      $this->dbback_city->where(array('msg_id' => $id));
      $this->dbback_city->from($this->tbl_message);
      $arr = $this->dbback_city->get()->row_array();
    }

    return $arr;
  }

}

/* End of file cooperate_base_model.php */
/* Location: ./application/models/cooperate_base_model.php */
