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
 * Broker_sms_base_model CLASS
 *
 * 经纪人短信验证码类，用于验证手机关联验证码
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Broker_sms_base_model extends MY_Model
{

  /**
   * 经纪人验证码表名
   * @var string
   */
  private $_tbl = 'broker_sms';

  /**
   * 用户获取短信类型
   * @var array
   */
  private $_arr_type = array(
    'register' => 3, 'login' => 1,
    'modify_data' => 2, 'findpw' => 4,
    'modify_phone' => 5
  );

  /**
   * 设置短信类型，默认为1
   * @var type
   */
  public $type = 'login';

  /**
   * 过期时长，秒为单位
   * @var int
   */
  public $expiretime = 300;

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 产生100000-999999之间的随机数
   * @return int
   */
  public function rand_num()
  {
    return rand(100000, 999999);
  }


  /**
   * 添加发送短信到手机的记录
   * @param int $phone 手机号码
   * @param int $validcode 验证码
   * @return boolean 成功插入编号
   */
  public function add($phone, $validcode)
  {
    $createtime = time();
    $expiretime = $createtime + $this->expiretime;
    $field_values = array(
      'phone' => $phone, 'type' => $this->_arr_type[$this->type],
      'validcode' => $validcode, 'esta' => 0,
      'createtime' => $createtime, 'expiretime' => $expiretime
    );
    //插入成功后返回相应id
    if ($this->db->insert($this->_tbl, $field_values)) {
      return $this->db->insert_id();
    }
    return false;
  }


  /**
   * 某个手机号码是否过期，可以再次获取
   * @param int $phone 手机号码
   * @param int $validcode 验证码
   * @return int 受影响的行数
   */
  public function is_expire_by_phone($phone)
  {
    $this->dbback->where('phone', $phone);
    $this->dbback->where('type', $this->_arr_type[$this->type]);
    $this->dbback->where('esta', 0);
    $this->dbback->where('createtime >=', time() - 60);
    return $this->dbback->count_all_results($this->_tbl);
  }


  /**
   * 把验证过后的验证设为已验证状态
   * @param int $validcode_id 验证码ID编号
   * @return int 更新状态
   */
  public function validcode_set_esta($validcode_id)
  {
    $this->db->where('id', $validcode_id);
    $update_field_values = array('esta' => 1);
    $this->db->update($this->_tbl, $update_field_values);
  }


  /**
   * 判断某个手机号在时间内获取某个类型的验证码是否准确
   * @param int $phone 手机号码
   * @param int $validcode 验证码
   * @return int 验证码编号
   */
  public function get_by_phone_validcode($phone, $validcode)
  {
    $this->dbback->select('id');
    $this->dbback->where('phone', $phone);
    $this->dbback->where('validcode', $validcode);
    $this->dbback->where('type', $this->_arr_type[$this->type]);
    $this->dbback->where('esta', 0);
    $this->dbback->where('expiretime >=', time());
    $this->dbback->limit(1);
    $result = $this->dbback->get($this->_tbl)->row_array();
    return isset($result['id']) ? $result['id'] : 0;
  }
}

/* End of file broker_sms_base_model.php */
/* Location: ./application/models/broker_sms_base_model.php */
