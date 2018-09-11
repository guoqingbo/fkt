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
 * House_tmp_base_model CLASS
 *
 * 钥匙查询、添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class House_tmp_base_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
  }

  /** 根据条件查询数量 */
  public function get_count($where, $tab_name)
  {
    $this->dbback_city->where($where);

    //查询
    $count = $this->dbback_city->count_all_results($tab_name);
    return $count;
  }

  /** 添加数据并返回ID*/
  public function insert_data($tab_name, $data)
  {
    $this->db_city->insert($tab_name, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /** 修改数据并返回影响行数 */
  public function modify_data_rows($tab_name, $data, $where)
  {
    $this->db_city->update($tab_name, $data, $where);
    return $this->db_city->affected_rows();
  }

  /** 删除 */
  public function delete_data($where, $tab_name)
  {
    $this->db_city->where($where);
    $this->db_city->delete($tab_name);

    return $this->db_city->affected_rows();
  }
}
