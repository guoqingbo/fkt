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
 * Stat_publish_model CLASS
 *
 * 统计房源发布数量
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Stat_publish_model extends MY_Model
{
  private $sell_tbl = 'sell_house';
  private $rent_tbl = 'rent_house';
  private $count_tbl = 'stat_publish';

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
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->sell_tbl);
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }

  public function get_num_for_rent($where)
  {
    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->rent_tbl);
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }

  public function add_daily_count($data)
  {
    $this->db_city->insert($this->count_tbl, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
