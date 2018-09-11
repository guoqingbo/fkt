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
 * Broker_base_model CLASS
 *
 * 经纪人基础类 提供注册、登录、修改密码、查询等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Signatory_base_model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl = 'signatory';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

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
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }


  /**
   * 获取经纪人用户列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条用户记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'status', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    //查询条件
    $this->dbback->where($where);
    //排序条件
    $this->dbback->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback->limit($limit, $start);
    }
    //返回结果
    $data_info = $this->dbback->get($this->_tbl)->result_array();
    return $data_info;
  }


  /**
   * 根据查询条件返回一条用户表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的用户表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }

    //查询条件
    $this->dbback->where($where);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 根据手机号码获取记录
   * @param string $phone 手机号码
   * @return array
   */
  public function get_by_phone($phone)
  {
    //查询条件
    $this->dbback->where('phone', $phone);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 注册用户
   * @param int $city_id 城市编号
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return int 成功插入ID
   */
  public function add_user($city_id, $phone, $password, $status = 1)
  {

    //号码是否已经注册过
    $is_exist_phone = $this->is_exist_by_phone($phone);
    if ($is_exist_phone) //号码已经存在
    {
      return 'exist_phone';
    }
    return $this->add($city_id, $phone, $password, $status);
  }

  /**
   * 注册用户添加记录
   * @param int $city_id 城市编号
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return int 成功插入ID
   */
  public function add_user_record($broker_id)
  {

    //号码是否已经注册过
    $update_data = array('broker_id' => $broker_id, 'create_time' => time());
    return $this->db->insert('register_record', $update_data);
  }

  /**
   * 更新密码(用于找回密码)
   * @param int $phone 手机号码
   * @param string $password 密码
   * @param string $verify_password 确认密码
   * @return int 受影响的行数
   */
  public function update_password($phone, $password, $verify_password)
  {
    if ($password != $verify_password) {
      return 'password_not_same';
    }
    $is_exist_phone = $this->is_exist_by_phone($phone);
    if (!$is_exist_phone) {
      return 'non_exist_phone';
    }
    $update_data = array('password' => md5($password));
    $this->db->where('phone', $phone);
    $this->db->update($this->_tbl, $update_data);
    return $this->db->affected_rows();
  }

  /**
   * 修改密码
   * @param string $broker_id 当前经纪人id
   * @param string $old_password 原密码
   * @param string $new_password 新密码
   * @param string $verify_password 确认密码
   * @return int 受影响的行数
   */
  public function modify_password($broker_id, $old_password, $new_password, $verify_password)
  {
    $is_true_password = $this->is_true_password($broker_id, $old_password);
    if (!$is_true_password) {
      return 'password_not_true';
    }
    if ($new_password != $verify_password) {
      return 'password_not_same';
    }
    $update_data = array('password' => md5($new_password));
    $this->db->where('id', $broker_id);
    $this->db->update($this->_tbl, $update_data);
    return $this->dbback->affected_rows();
  }

  /**
   * 注册用户
   * @param int $city_id 城市编号
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return int 返回插入id
   */
  public function add($city_id, $phone, $password, $status)
  {
    $insert_data = array();
    $insert_data['phone'] = $phone;
    $insert_data['password'] = md5($password);
    $insert_data['expiretime'] = strtotime("next year");
    $insert_data['city_id'] = $city_id;
    $insert_data['status'] = $status;
    //单条插入
    if ($this->db->insert($this->_tbl, $insert_data)) {
      return $this->db->insert_id();
    }
    return false;
  }

  /**
   * 根据手机号码判断用户是否有效
   * @param int $phone 手机号码
   * @return int 受影响的行数
   */
  public function is_exist_by_phone($phone)
  {
    $this->dbback->where('phone', $phone);
    return $this->dbback->count_all_results($this->_tbl);
  }

  /**
   * 判断输入的密码是否正确
   * @param string $broker_id 经纪人id
   * @param string $password 密码
   * @return boolean 是否正确
   */
  public function is_true_password($broker_id, $password)
  {
    $broker_data = $this->get_one_by('id = ' . $broker_id);
    $true_password = $broker_data['password'];
    if ($true_password == md5($password)) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * 用户登录并返回行记录
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return array
   */
  public function login($phone, $password)
  {
    if ($phone == '' || $password == '') {
      return 'error_param';
    }
    $this->dbback->where('phone', $phone);
    $this->dbback->where('password', md5($password));
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 用户登录并返回行记录
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return array
   */
  public function md5login($phone, $password)
  {
    if ($phone == '' || $password == '') {
      return 'error_param';
    }
    $this->dbback->where('phone', $phone);
    $this->dbback->where('password', $password);
    return $this->dbback->get($this->_tbl)->row_array();
  }

  /**
   * 通过经纪人编号获取记录
   * @param int $id 公司编号
   * @return array 经纪人记录组成的一维数组
   */
  public function get_by_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->db->select($this->_select_fields);
    }
    //查询条件
    $this->db->where('id', $id);
    return $this->db->get($this->_tbl)->row_array();
  }

  /**
   * 更新经纪人基本数据
   * @param array $update_data 更新的数据源数组
   * @param array $id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }

    $this->db->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db->update_batch($this->_tbl, $update_data);
    } else {
      $this->db->update($this->_tbl, $update_data);
    }

    return $this->db->affected_rows();
  }

  /**
   * 删除经纪人
   * @param array $id 经济人编号
   * @return int 成功后返回受影响的行数
   */
  public function delete_by_id($id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db->where_in('id', $ids);
    return $this->db->delete($this->_tbl);
  }
}

/* End of file broker_base_model.php */
/* Location: ./application/models/broker_base_model.php */
