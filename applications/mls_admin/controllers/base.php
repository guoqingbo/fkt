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
    //查看所有的模块
    $this->load->model('agency_basic_setting_model');

  }

  //获取系统默认的角色
  public function index()
  {
    $data = array();
    $setResult = "";
    $data['title'] = '基本设置';
    //form 表单提交的数据
    $submit_flag = $this->input->post("submit_flag", true);

    if ($submit_flag == "set") {
      //系统自动防护时间参数
      $guard_time = $this->input->post("guard_time", true);
      if ($guard_time == "") {
        $guard_time = "0";
      }

      //信息录入进行黑名单校验参数
      $is_blacklist_check = $this->input->post("is_blacklist_check", true);

      //跟进内容参数
      $follow_text_num = $this->input->post("follow_text_num", true);
      if ($follow_text_num == "") {
        $follow_text_num = "0";
      }

      //楼盘名称只能选择录入参数
      $is_property_publish = $this->input->post("is_property_publish", true);

      //房客源列表默认排序规则参数
      $house_list_order_field = $this->input->post("house_list_order_field", true);

      //保密信息查看次数上限参数
      $secret_view_num = $this->input->post("secret_view_num", true);
      if ($secret_view_num == "") {
        $secret_view_num = "0";
      }

      //登录时有提醒任务是否自动打开参数
      $is_remind_open = $this->input->post("is_remind_open", true);

      //楼盘字典同步变化参数
      $is_community_modify_house = $this->input->post("is_community_modify_house", true);

      //出租自动变公盘参数
      $rent_house_nature_public = $this->input->post("rent_house_nature_public", true);
      if ($rent_house_nature_public == "") {
        $rent_house_nature_public = "0";
      }
      //求购自动变公客参数
      $buy_customer_nature_public = $this->input->post("buy_customer_nature_public", true);
      if ($buy_customer_nature_public == "") {
        $buy_customer_nature_public = "0";
      }
      //出售自动变公盘参数
      $sell_house_nature_public = $this->input->post("sell_house_nature_public", true);
      if ($sell_house_nature_public == "") {
        $sell_house_nature_public = "0";
      }
      //求租自动变公客参数
      $rent_customer_nature_public = $this->input->post("rent_customer_nature_public", true);
      if ($rent_customer_nature_public == "") {
        $rent_customer_nature_public = "0";
      }
      //出售信息默认查询时间参数
      $sell_house_query_time = $this->input->post("sell_house_query_time", true);

      //求购信息默认查询时间参数
      $buy_customer_query_time = $this->input->post("buy_customer_query_time", true);

      //出租信息默认查询时间参数
      $rent_house_query_time = $this->input->post("rent_house_query_time", true);

      //求租信息默认查询时间参数
      $rent_customer_query_time = $this->input->post("rent_customer_query_time", true);

      //出租信息登记时间参数
      $rent_house_check_time = $this->input->post("rent_house_check_time", true);
      if ($rent_house_check_time == "") {
        $rent_house_check_time = "0";
      }

      //求购信息登记时间参数
      $buy_customer_check_time = $this->input->post("buy_customer_check_time", true);
      if ($buy_customer_check_time == "") {
        $buy_customer_check_time = "0";
      }

      //出售信息登记参数
      $sell_house_check_time = $this->input->post("sell_house_check_time", true);
      if ($sell_house_check_time == "0") {
        $sell_house_check_time = "0";
      }

      //求租信息登记时间参数
      $rent_customer_check_time = $this->input->post("rent_customer_check_time", true);
      if ($rent_customer_check_time == "") {
        $rent_customer_check_time = "0";
      }

      //两次客源跟进间隔参数
      $customer_follow_spacing_time = $this->input->post("customer_follow_spacing_time", true);
      if ($customer_follow_spacing_time == "") {
        $customer_follow_spacing_time = "0";
      }

      //两次房源跟进间隔参数
      $house_follow_spacing_time = $this->input->post("house_follow_spacing_time", true);
      if ($house_follow_spacing_time == "") {
        $house_follow_spacing_time = "0";
      }
      $paramArray = array(
        "guard_time" => $guard_time,
        "is_blacklist_check" => $is_blacklist_check,
        "follow_text_num" => $follow_text_num,
        "is_property_publish" => $is_property_publish,
        "house_list_order_field" => $house_list_order_field,
        "secret_view_num" => $secret_view_num,
        "is_remind_open" => $is_remind_open,
        "is_community_modify_house" => $is_community_modify_house,
        "rent_house_nature_public" => $rent_house_nature_public,
        "buy_customer_nature_public" => $buy_customer_nature_public,
        "sell_house_nature_public" => $sell_house_nature_public,
        "rent_customer_nature_public" => $rent_customer_nature_public,
        "sell_house_query_time" => $sell_house_query_time,
        "buy_customer_query_time" => $buy_customer_query_time,
        "rent_house_query_time" => $rent_house_query_time,
        "rent_customer_query_time" => $rent_customer_query_time,
        "rent_house_check_time" => $rent_house_check_time,
        "buy_customer_check_time" => $buy_customer_check_time,
        "sell_house_check_time" => $sell_house_check_time,
        "rent_customer_check_time" => $rent_customer_check_time,
        "customer_follow_spacing_time" => $customer_follow_spacing_time,
        "house_follow_spacing_time" => $house_follow_spacing_time
      );
      $setResult = $this->agency_basic_setting_model->update_default_data($paramArray);
    }
    //获取系统默认基本设置
    $base_setting = $this->agency_basic_setting_model->get_default_data();
    $data['base_setting'] = $base_setting[0];
    $data['setResult'] = $setResult;
    $this->load->view('base/index', $data);
  }
}

