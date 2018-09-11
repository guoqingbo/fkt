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
 * Broker_login_log_base_model CLASS
 *
 * 经纪人登录日志记录
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          fisher
 */
class Broker_login_log_base_model extends MY_Model
{

  private $_tbl = 'login_log';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  //登录日志
    public function insert_login_log($broker_id, $ip, $deviceid, $phone, $infofrom = 1, $agency_id, $agency_name)
  {
    $insert_data = array();
    $insert_data['broker_id'] = $broker_id;
    $insert_data['ip'] = $ip;
    $insert_data['deviceid'] = $deviceid;
    $insert_data['dateline'] = time();
    $insert_data['phone'] = $phone;
    $insert_data['infofrom'] = $infofrom;
      $insert_data['agency_id'] = $agency_id;
      $insert_data['agency_name'] = $agency_name;
    //单条插入
    if ($this->db_city->insert($this->_tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
    return false;
  }

  //获取当天登录次数
  public function get_count_day_login($broker_id, $infofrom = 1)
  {
    $start_time = date('Y-m-d') . ' 00:00:00';
    $end_time = date('Y-m-d') . ' 23:59:59';
    $this->dbback_city->where('broker_id', $broker_id);
    $this->dbback_city->where('infofrom', $infofrom);
    $this->dbback_city->where('dateline >=', strtotime($start_time));
    $this->dbback_city->where('dateline <=', strtotime($end_time));
    return $this->dbback_city->count_all_results($this->_tbl);
  }
}

/* End of file Broker_login_log_base_model.php */
/* Location: ./app/models/Broker_login_log_base_model.php */
