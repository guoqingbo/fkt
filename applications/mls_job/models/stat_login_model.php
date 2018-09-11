<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls_job
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * @package         mls_job
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Stat_login_model extends MY_Model
{

  private $stat_login_tbl = 'stat_login';
  private $login_log_tbl = 'login_log';

  public function __construct()
  {
    parent::__construct();
  }

  //获取昨天登录量
  public function login_log_count_by($where = '')
  {
    $starttime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
    $endtime = $starttime + 86399;

    if ($where) {
      $where .= ' and ';
    }
    $where .= 'dateline >= ' . $starttime . ' and dateline <= ' . $endtime;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results($this->login_log_tbl);
  }

  //记录登录量表
  public function set_stat_login($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->stat_login_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->stat_login_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->stat_login_tbl);
  }
}

/* End of file stat_login_model.php */
/* Location: ./application/mls_job/models/autocollect_model.php */
