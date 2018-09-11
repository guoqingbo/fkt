<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * mls-job
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 导入个人房源
 *
 *
 * @package         mls-job
 * @subpackage      Models
 * @category        Models
 * @author          angel_in_us
 */
class Collect_view_model extends MY_Model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->stat_collect_view = 'stat_collect_view';//每日查看量数据表
    $this->agent_house_judge = 'agent_house_judge';//经纪人采集房源查看判断表
  }

  /**
   * @param type $where
   * @return 出售查看量
   */
  public function get_sell_view_num($where)
  {
    $where .= ' and tbl_name = "sell_house_collect"';
    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->agent_house_judge);
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }

  /**
   * @param type $where
   * @return 出租查看量
   */
  public function get_rent_view_num($where)
  {
    $where .= ' and tbl_name = "rent_house_collect"';
    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->agent_house_judge);
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }

  /**
   * @param type $data
   * @return false | 插入id
   */
  public function add_daily_count($data)
  {
    $this->db_city->insert($this->stat_collect_view, $data);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }

  /**
   * @param type $where_check
   * @return 查询结果
   */
  public function check_view_exist($where_check)
  {
    $data = array();
    $this->dbback_city->select('id');
    $this->dbback_city->where($where_check);
    $this->dbback_city->from($this->stat_collect_view);
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }

  /**
   * @param type $data
   * @param type $where_up
   * @return 所影响的行数
   */
  public function update_daily_count($data, $where_up)
  {
    $this->dbback_city->update($this->stat_collect_view, $data, $where_up);
    $rows = $this->dbback_city->affected_rows();
    return $rows;
  }
}

?>
