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
 * Stat_group_publish_model CLASS
 *
 * 权限模块添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Stat_group_publish_model extends MY_Model
{
  private $tbl = 'group_publish_log';
  private $count_tbl = 'stat_group_publish';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_num_for_sell($where)
  {
    $data = array();
    $this->dbback_city->select('count(id) as all_num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->tbl);
    $data = $this->dbback_city->get()->row_array();
    return $data['all_num'];
  }

  public function get_num_for_rent($where)
  {
    $data = array();
    $this->dbback_city->select('count(id) as all_num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->tbl);
    $data = $this->dbback_city->get()->row_array();
    return $data['all_num'];
  }

  public function add_daily_count($data)
  {
    $this->db_city->insert($this->count_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
