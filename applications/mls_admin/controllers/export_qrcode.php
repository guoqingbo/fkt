<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);

/**
 * 导出二维码
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Export_qrcode extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
  }

  public function agency()
  {
    //查询满足条件的门店数据
    $this->load->model('agency_model');
    $where = 'status = 1  AND company_id <> 0';
    $agencys = $this->agency_model->get_all_by($where, 0, -1);
    $base_url = MLS_ADMIN_URL . '/' . $_SESSION[WEB_AUTH]["city"] . '/broker_info/agency_house/';
    foreach ($agencys as $v) {
      $company = $this->agency_model->get_by_id($v['company_id']);
      $qrcode_name = $v['id'] . '_' . $company['name'] . '-' . $v['name'];
      export_get_qrcode($base_url . $v['id'], $_SESSION[WEB_AUTH]["city"], 'H', 6, $qrcode_name);
    }
  }
}
