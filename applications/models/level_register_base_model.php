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
 * level_register_base_model CLASS
 *
 * 查找证书是否奖励过等级分值的状态
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class level_register_base_model extends MY_Model
{

  /**
   * 认证奖励记录表
   * @var string
   */
  private $_tbl = 'register_record';

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
   * 通过证书编号获取证书记录
   * @param int $broker_id 经纪人编号
   * @return array 证书记录组成的一维数组
   */
  public function get_by_broker_id($broker_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback->select($this->_select_fields);
    }
    //查询条件
    $this->dbback->where('broker_id', $broker_id);
    $register = $this->dbback->get($this->_tbl)->row_array();
    return $register;
  }

  /**
   * 插入证书数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的证书id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db->insert_batch($this->_tbl, $insert_data)) {
        return $this->db->insert_id();
      }
    } else {
      //单条插入
      if ($this->db->insert($this->_tbl, $insert_data)) {
        return $this->db->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新证书数据
   * @param array $update_data 更新的数据源数组
   * @param int $broker_id 经纪人编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_broker_id($update_data, $broker_id)
  {
    $this->db->where('broker_id', $broker_id);
    $this->db->update($this->_tbl, $update_data);
    return $this->db->affected_rows();
  }
}

/* End of file level_register_base_model.php */
/* Location: ./applications/models/level_register_base_model.php */
