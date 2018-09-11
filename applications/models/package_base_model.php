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
 * Group_base_model CLASS
 *
 * 经纪人用户套餐
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Package_base_model extends MY_Model
{

  /**
   * 用户组
   * @var string
   */
  private $_tbl = 'package';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取配置文件
   * @return array
   */
  public function get_config()
  {
    $package = $this->get_all_by();
    return change_to_key_array($package, 'id');
  }

  public function get_all_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback->where($where);
    }
    //返回结果
    return $this->dbback->get($this->_tbl)->result_array();
  }

  //清空数据库
  public function truncate()
  {
    $this->db->from($this->_tbl);
    $this->db->truncate();
  }

  /**
   * 插入用数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司id 失败 false
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
}

/* End of file package_base_model.php */
/* Location: ./applications/models/package_base_model.php */
