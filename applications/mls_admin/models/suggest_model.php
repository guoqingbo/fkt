<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS-admin
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * suggest_model CLASS
 *
 * 后台意见和建议
 *
 * @package         MLS-admin
 * @subpackage      Models
 * @category        Models
 * @author          lujun
 */
class Suggest_model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  /** 列表显示 */
  public function get_all_suggest($city_id, $status, $offset = 0, $limit = 10)
  {
    if ($status == 99) {
      $where = "city_id = " . $city_id;
    } else {
      $where = "city_id = " . $city_id . " and status = " . $status;
    }
    $this->dbback->select('*');
    $this->dbback->from('feedback');
    $this->dbback->where($where);
    $this->dbback->order_by('status');
    $this->dbback->limit($limit, $offset);
    $data = $this->dbback->get()->result_array();
    return $data;
  }


  /** 查询建议详情*/
  public function get_info($where)
  {
    $this->dbback->select('*');
    $this->dbback->from('feedback');
    $this->dbback->where($where);
    $data = $this->dbback->get()->row_array();
    return $data;
  }

  /** 获取城市ID */
  public function get_city_id($city_py)
  {
    $where = "spell = " . "'" . $city_py . "'";
    $this->dbback->select('id');
    $this->dbback->from('city');
    $this->dbback->where($where);
    $data = $this->dbback->get()->row_array();
    return $data['id'];
  }

  /** 更改状态、添加管理员回复 */
  public function change_status($id, $status, $adminfeedback = '')
  {
    $sql = "update feedback set status = " . $status . ", adminfeedback = " . "'" . $adminfeedback . "'" . " where id = " . $id;
    $query = $this->db->query($sql);
    return $this->db->affected_rows();
  }

  /** 查询全部条数 */
  public function get_count_by_cond($city_id, $status)
  {
    if ($status == 99) {
      $where = "city_id = " . $city_id;
    } else {
      $where = "city_id = " . $city_id . " and status = " . $status;
    }
    $this->dbback->select('*');
    $this->dbback->from('feedback');
    $this->dbback->where($where);
    $this->dbback->order_by('status');
    $count_num = count($this->dbback->get()->result_array());
    return $count_num;
  }

  /** 根据手机号关联出提议者 */
  public function get_broker_by_phone($phone)
  {
    $where = "phone = " . "'" . $phone . "'";
    $this->dbback_city->select('*');
    $this->dbback_city->from('broker_info');
    $this->dbback_city->where($where);
    $data = $this->dbback_city->get()->row_array();
    return $data;
  }
}
