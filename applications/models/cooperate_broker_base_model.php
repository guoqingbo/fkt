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
class Cooperate_broker_base_model extends MY_Model
{

  /**
   * 经纪人表名
   * @var string
   */
  private $_tbl = 'broker';

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
   * 注册用户
   * @param array $insert_data 注册数据
   * @return int 返回插入id
   */
  public function add($insert_data = array())
  {
    if (is_full_array($insert_data)) {
      //单条插入
      if ($this->db_cooperate->insert($this->_tbl, $insert_data)) {
        return $this->db_cooperate->insert_id();
      }
    }
    return false;
  }

  /**
   * 注册用户
   * @param int $city_id 城市编号
   * @param int $phone 手机号码
   * @param string $password 密码
   * @return int 成功插入ID
   */
  public function add_user($insert_data = array())
  {
    if (is_full_array($insert_data)) {
      $phone = $insert_data['phone'];
      //号码是否已经注册过
      $is_exist_phone = $this->is_exist_by_phone($phone);
      if ($is_exist_phone) //号码已经存在
      {
        return 'exist_phone';
      }
      return $this->add($insert_data);
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
    $this->db_cooperate->where('phone', $phone);
    return $this->db_cooperate->count_all_results($this->_tbl);
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
      $this->dbback_cooperate->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_cooperate->where('id', $id);
    return $this->dbback_cooperate->get($this->_tbl)->row_array();
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
      $this->dbback_cooperate->select($this->_select_fields);
    }

    //查询条件
    $this->dbback_cooperate->where($where);
    return $this->dbback_cooperate->get($this->_tbl)->row_array();
  }

}

/* End of file broker_base_model.php */
/* Location: ./application/models/broker_base_model.php */
