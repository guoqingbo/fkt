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
 * Stat_broker_app_model CLASS
 *
 * 统计APP数据
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Stat_broker_app_model extends MY_Model
{
  private $_sbad_tbl = 'stat_broker_app_daily';
  private $_sbac_tbl = 'stat_broker_app_count';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function count_total_by_ymd_cityid($ymd, $cityid)
  {
    $total_num = $android_num = $ios_num = 0;

    $sql = "SELECT devicetype,count(id) as num FROM `mls`." . $this->_sbad_tbl . " WHERE ymd = '" . $ymd . "' AND city = '" . $cityid . "' GROUP BY devicetype";
    $data = $this->query($sql);
    if (is_full_array($data)) {
      foreach ($data as $value) {
        if ($value['devicetype'] == 0) {
          $android_num = intval($value['num']);
        } else if ($value['devicetype'] == 1) {
          $ios_num = intval($value['num']);
        }
      }
    }

    $total_num = intval($android_num + $ios_num);

    return array('android_num' => $android_num, 'ios_num' => $ios_num, 'total_num' => $total_num);
  }

  public function add_broker_app_count($ymd, $cityid)
  {
    $count_data = $this->count_total_by_ymd_cityid($ymd, $cityid);

    $ymdarr = explode('-', $ymd);
    $y = $ymdarr[0];
    $m = $ymdarr[1];
    $d = $ymdarr[2];

    $insert_data = array('y' => $y, 'm' => $m, 'd' => $d, 'city' => $cityid, 'total' => $count_data['total_num'], 'android' => $count_data['android_num'], 'iphone' => $count_data['ios_num']);
    $this->db_mls->insert($this->_sbac_tbl, $insert_data);
    return $this->db_mls->insert_id();
  }
}

/* End of file stat_broker_app_model.php */
/* Location: ./applications/mls_job/models/stat_broker_app_model.php */
