<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 初始化数据
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Init extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  //初始化公司权限
  public function company_permission()
  {
    die();
    //查找总公司名称
    $this->load->model('agency_model');
    $company = $this->agency_model->get_company_by();
    if (is_full_array($company)) {
      foreach ($company as $v) {
        $this->agency_model->init_company_permission($v['id']);
      }
    }
  }

  public function broker_role()
  {
    die();
    $this->load->model('broker_info_model');
    $broker_infos = $this->broker_info_model->get_all_by('', 0, 0);
    //获取权限
    $this->load->model('permission_company_role_model');
    $this->permission_company_role_model->set_select_fields(array('id'));
    //查找总公司名称
    $this->load->model('agency_model');
    foreach ($broker_infos as $v) {
      $package_id = $v['package_id'];
      $agency_id = $v['agency_id'];
      $agency = $this->agency_model->get_by_id($agency_id);
      $company_id = $agency['company_id'];
      $role_info = $this->permission_company_role_model->get_by_company_id_package_id($company_id, $package_id);
      $update_data = array('role_id' => $role_info['id']);
      $this->broker_info_model->update_by_id($update_data, $v['id']);
    }
  }

}
