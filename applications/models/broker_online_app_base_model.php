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
 * Broker_online_app_base_model CLASS
 *
 * 登录在线通信业务逻辑类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models broker_online_pc_state
 * @author          fisher
 */
class Broker_online_app_base_model extends MY_Model
{

  private $_tbl = 'broker_online_app';
  private $_tbl_member = 'member_online_app';
  private $_tbl_baal = 'broker_app_access_log';
  private $_tbl_sbad = 'stat_broker_app_daily';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function add_access_log($insert_data)
  {
    $this->db->insert($this->_tbl_baal, $insert_data);
    return $this->db->insert_id();
  }

  public function add_broker_app_daily($insert_data)
  {
    $this->db->where('deviceid', $insert_data['deviceid']);
    $data = $this->db->get($this->_tbl_sbad)->result_array();
    if (!is_full_array($data)) {
      $this->db->insert($this->_tbl_sbad, $insert_data);
      return $this->db->insert_id();
    } else {
      return 0;
    }
  }

  public function add_login_record($insert_data)
  {
    $this->delete($insert_data);
    $this->db->insert($this->_tbl, $insert_data);
    return $this->db->insert_id();
  }

  public function get_by_broker_id($broker_ids)
  {
    if (is_array($broker_ids)) {
      $this->db->where_in('broker_id', $broker_ids);
    } else if (intval($broker_ids) > 0) {
      $this->db->where('broker_id', $broker_ids);
    }
    return $this->db->get($this->_tbl)->result_array();
  }

  public function get_by_member_uid($member_uids)
  {
    if (is_array($member_uids)) {
      $this->db->where_in('uid', $member_uids);
    } else if (intval($member_uids) > 0) {
      $this->db->where('uid', $member_uids);
    }
    return $this->db->get($this->_tbl_member)->result_array();
  }

  public function update_token_by_broker_id($broker_id, $token)
  {
    $update_data = array('token' => $token);
    $this->db->where('broker_id', $broker_id);
    $this->db->update($this->_tbl, $update_data);
  }

  public function delete($data)
  {
    $this->db->where('deviceid', $data['deviceid']);
    $this->db->or_where('broker_id', $data['broker_id']);
    return $this->db->delete($this->_tbl);
  }

  public function get_all_by_city($city)
  {
    $this->db->where('city', $city);
    return $this->db->get($this->_tbl)->result_array();
  }
}

/* End of file Broker_online_app_base_model.php */
/* Location: ./app/models/Broker_online_app_base_model.php */
