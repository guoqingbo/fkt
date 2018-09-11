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
 * Import_person_house_base CLASS
 *
 * 用于导入365个人房源统计的记录
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Import_person_house_base_model extends MY_Model
{

  /**
   * 用于导入365个人房源统计的记录
   * @var string
   */
  private $_tbl = 'import_person_house';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_last_time_by_type($type)
  {
    $this->db_city->where('type', $type);
    $person_house_count = $this->db_city->get($this->_tbl)->row_array();
    return isset($person_house_count) ? $person_house_count['count_last_time'] : 0;
  }

  /**
   * 更新导入房源后的记数器
   * @param string $type 类型
   * @param int $last_time 最后统计时间
   * @return int 受影响的行数
   */
  public function update_by_type_and_last_time($type, $last_time)
  {
    $this->db_city->where('type', $type);
    $update_data = array(
      'count_last_time' => $last_time,
      'update_time' => time(),
    );
    $this->db_city->update($this->_tbl, $update_data);
    return $this->db_city->affected_rows();
  }
}
