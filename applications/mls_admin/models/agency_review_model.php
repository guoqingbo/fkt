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
 * Agency_model CLASS
 *
 * 门店审核业务逻辑类，提供对门店添加，删除审核过程
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Agency_review_base_model");

class Agency_review_model extends Agency_review_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取所有的审核记录
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @return array
   */
  public function get_all_by($where, $start = 0, $limit = 20)
  {
    $agency = parent::get_all_by($where, $start, $limit);
    if (is_array($agency) && !empty($agency)) {
      $this->load->model('agency_model');
      foreach ($agency as &$v) {
        $agency_info = $this->agency_model->get_by_id($v['agency_id']);
        $company_info = $this->agency_model->get_by_id($agency_info['company_id']);
        $v['company_name'] = $company_info['name'];
        $v['agency_name'] = $agency_info['name'];
        $v['telno'] = $agency_info['telno'];
        $v['address'] = $agency_info['address'];
        $v['status_str'] = $this->review_status[$v['status']];
        $v['action_str'] = $this->action[$v['action']];
      }
    }
    return $agency;
  }

  /**
   * 通过编号获取记录
   * @param int $id 记录编号
   * @return array 付费申请记录组成的一维数组
   */
  public function get_by_id($id)
  {
    $agency_review = parent::get_by_id($id);
    if ($agency_review) {
      $this->load->model('agency_model');
      $agency_info = $this->agency_model->get_by_id($agency_review['agency_id']);
      $company_info = $this->agency_model->get_by_id($agency_info['company_id']);
      $agency_review['company_name'] = $company_info['name'];
      $agency_review['agency_name'] = $agency_info['name'];
      $agency_review['telno'] = $agency_info['telno'];
      $agency_review['address'] = $agency_info['address'];
      $agency_review['applay_name'] = $agency_info['address'];
      $agency_review['action_str'] = $this->action[$agency_review['action']];
      $agency_review['status_arr'] = $this->review_status;
    }
    return $agency_review;
  }

  /**
   * 审核门店
   * @param int $id 审核编号
   * @param int $status 状态
   * @param string $remark 备注
   * @return int
   */
  public function review($id, $status, $remark)
  {
    $update_data = array(
      'status' => $status, 'remark' => $remark
    );
    return $this->update_by_id($update_data, $id);
  }
}

/* End of file Agency_review_model.php */
/* Location: ./app/models/Agency_review_model.php */
