<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统默认角色
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Base extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_basic_setting_model');

  }

  //获取系统默认的角色
  public function index()
  {
    $data = array();
    $agency_id = $this->user_arr['agency_id'];

    //获取系统公司基本设置
    $info = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);
    $company_setting = $info["0"];
    if (empty($company_setting)) {
      $result = $this->agency_basic_setting_model->get_default_data();
      $company_setting = $result["0"];
    }
    //获取系统默认基本设置
    $result = $this->agency_basic_setting_model->get_default_data();
    $base_setting = $result["0"];
    $data['base_setting'] = $base_setting;
    $data['company_setting'] = $company_setting;
    $this->result('1', $data);
  }

  //更新基本设置
  public function update()
  {
    $data = array();
    $agency_id = $this->user_arr['agency_id'];

    //信息录入进行黑名单校验参数
    $is_blacklist_check = $this->input->post("is_blacklist_check", true);

    //跟进内容参数
    $follow_text_num = $this->input->post("follow_text_num", true);
    if ($follow_text_num == "") {
      $follow_text_num = "0";
    }

    //保密信息查看次数上限参数
    $secret_view_num = $this->input->post("secret_view_num", true);
    if ($secret_view_num == "") {
      $secret_view_num = "0";
    }

    $paramArray = array(
      "is_blacklist_check" => $is_blacklist_check,
      "follow_text_num" => $follow_text_num,
      "secret_view_num" => $secret_view_num
    );
    $setResult = $this->agency_basic_setting_model->judge_by_id($paramArray, $agency_id);
    if ($setResult) {
      $this->result('1', '基本设置更新成功');
    } else {
      $this->result('0', '基本设置更新失败');
    }
  }
}

