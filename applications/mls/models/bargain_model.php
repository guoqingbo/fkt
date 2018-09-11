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
 * bargain_model CLASS
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lalala
 */
load_m("Bargain_base_model");

class Bargain_model extends Bargain_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->_tbl1 = 'bargain';
    $this->_tbl2 = 'broker_info';
    $this->_tbl3 = 'agency';
  }

  /**
   * 详情
   */
  public function get_info_by_id($id)
  {
    //查询字段
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl1)->row_array();
  }

  //更改成交审核状态
  public function change_status($id, $status)
  {
    $this->db_city->where('id', $id);
    $this->db_city->set('status', $status, false);
    $this->db_city->update($this->_tbl1);
    return $this->db_city->affected_rows();
  }

  //更改房源状态
  public function change_house_status($type, $h_id)
  {
    if ($type == 1) {
      $this->db_city->where('id', $h_id);
      $this->db_city->set('status', '4', false);
      $this->db_city->update('sell_house');
    } else {
      $this->db_city->where('id', $h_id);
      $this->db_city->set('status', '4', false);
      $this->db_city->update('rent_house');
    }
    return $this->db_city->affected_rows();
  }

  //总计数据
  public function get_total($where)
  {
    //查询字段
    $this->dbback_city->select("SUM(commission_total) AS commission_total_total");

    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    $this->dbback_city->from($this->_tbl1);
    //返回结果
    return $this->dbback_city->get()->row_array();
  }

  //根据角色层次获取访问范围权限
  public function get_range_menu_by_role_level($user_arr, $post_agency_id = 0)
  {
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    $agencys = array(); //门店
    $brokers = array(); //经纪人
    if ($user_arr['role_level'] < 6) //公司
    {
      //获取该公司下的所有门店
      $agencys = $this->agency_model->get_children_by_company_id($user_arr['company_id']);
      array_unshift($agencys, array('id' => '', 'name' => '请选择'));
      if ($post_agency_id > 0) {
        $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
        $brokers = $this->broker_info_model->get_by_agency_id($post_agency_id);
        array_unshift($brokers, array('broker_id' => '', 'truename' => '请选择'));
      } else {
        $brokers = array(array('broker_id' => '', 'truename' => '请选择'));
      }
    } else if ($user_arr['role_level'] < 8) //门店
    {
      $agencys = array(
        array('id' => $user_arr['agency_id'],
          'name' => $user_arr['agency_name'])
      );
      $this->broker_info_model->set_select_fields(array('broker_id', 'truename'));
      $brokers = $this->broker_info_model->get_by_agency_id($user_arr['agency_id']);
      array_unshift($brokers, array('broker_id' => '', 'truename' => '请选择'));
    } else //查看自己的。目前没有此权限设置
    {
      $agencys = array(
        array('id' => $user_arr['agency_id'],
          'name' => $user_arr['agency_name'])
      );
      $brokers = array(
        array('broker_id' => $user_arr['broker_id'],
          'truename' => $user_arr['truename'])
      );
    }
    return array('agencys' => $agencys, 'brokers' => $brokers);
  }
}

/* End of file bargain_model.php */
/* Location: ./app/models/bargain_model.php */
