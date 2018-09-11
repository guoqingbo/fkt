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
    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');
    $this->load->model('buy_customer_model');
    $this->load->model('rent_customer_model');
    $this->load->model('cooperate_model');
    $this->load->model('agency_model');
    $this->load->model('operate_log_model');
  }

  //获取系统默认的角色
  public function index()
  {
    $data = array();
    $agency_id = $this->user_arr['agency_id'];
    $company_id = $this->user_arr['company_id'];
    $role_level = $this->user_arr['role_level'];
    $city_id = $this->user_arr['city_id'];
    $data['city_id'] = $city_id;
    $data['now_agency_id'] = $agency_id;

    //判断当前经济人角色，是否在门店以上
    if (isset($role_level) && intval($role_level) < 6) {
      //当前公司下所有的一级二级门店
      $company_info = $this->agency_model->get_children_by_company_id($company_id);
    } else {
      //当前门店
      $company_info = $this->agency_model->get_by_id_one($agency_id);
    }

    //当前公司下所有门店
    $all_company_info = array();
    if (is_full_array($company_info)) {
      foreach ($company_info as $k => $v) {
        //判断门店下是否有下属门店
        $where_cond = array('agency_id' => $v['id']);
        $is_has_agency = '0';
        $next_agency_data = $this->agency_model->get_all_by($where_cond);
        if (is_full_array($next_agency_data)) {
          $is_has_agency = '1';
        }
        $company_info[$k]['is_has_agency'] = $is_has_agency;
      }
      foreach ($company_info as $k => $v) {
        //一级门店追加
        if (0 == $v['agency_id']) {
          $all_company_info[] = $v;
        }
      }
      //二级门店追加
      foreach ($company_info as $k => $v) {
        if ($v['agency_id'] != 0) {
          foreach ($all_company_info as $key => $val) {
            if ($v['agency_id'] == $val['id']) {
              $all_company_info[$key]['next_agency_data'][] = $v;
            }
          }
        }
      }
    }
    $data['all_company_info'] = $all_company_info;

    //form 表单提交的数据
    $submit_flag = $this->input->post("submit_flag", true);

    //获取系统默认基本设置
    $default_base_data = $this->agency_basic_setting_model->get_default_data();
    //获取当前门店基本设置
    $agency_base_data = $this->agency_basic_setting_model->get_data_by_agency_id($agency_id);

    //工作日
    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['work_day'] = explode(',', $agency_base_data["0"]['work_day']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['work_day'] = explode(',', $default_base_data["0"]['work_day']);
      $agency_setting = $default_base_data["0"];
    }
    //出售房源列表自定义
    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['sell_house_field'] = explode(',', $agency_base_data["0"]['sell_house_field']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['sell_house_field'] = explode(',', $default_base_data["0"]['sell_house_field']);
      $agency_setting = $default_base_data["0"];
    }
    //出去房源列表自定义
    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['rent_house_field'] = explode(',', $agency_base_data["0"]['rent_house_field']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['rent_house_field'] = explode(',', $default_base_data["0"]['rent_house_field']);
      $agency_setting = $default_base_data["0"];
    }

    //求购客源列表自定义
    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['buy_customer_field'] = explode(',', $agency_base_data["0"]['buy_customer_field']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['buy_customer_field'] = explode(',', $default_base_data["0"]['buy_customer_field']);
      $agency_setting = $default_base_data["0"];
    }
    //求租客源列表自定义
    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['rent_customer_field'] = explode(',', $agency_base_data["0"]['rent_customer_field']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['rent_customer_field'] = explode(',', $default_base_data["0"]['rent_customer_field']);
      $agency_setting = $default_base_data["0"];
    }

    $data['base_setting'] = $default_base_data["0"];
    $data['company_setting'] = $agency_setting;
    $data['setResult'] = $setResult;

    //导航栏
    $data['user_menu'] = $this->user_menu;

    //页面标题
    $data['page_title'] = '基本设置';

    //加载css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/cal.css,'
      . 'mls/css/v1.0/personal_center.css,'
      . 'mls/css/v1.0/guest_disk.css');

    //加载js
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'mls/js/v1.0/cal.js,'
      . 'mls/js/v1.0/shuifei.js');

    //加载底部js
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,mls/js/v1.0/backspace.js,mls/js/v1.0/calculate.js');
    $this->view('agency/base/index', $data);
  }

  public function save_button_submit()
  {
    $agency_arr = $this->input->post('agency_access_area', TRUE);//门店id

    //系统自动防护时间参数
    $guard_time = $this->input->post("guard_time2", true);
    if ($guard_time == "") {
      $guard_time = "0";
    }

    //信息录入进行黑名单校验参数
    $is_blacklist_check = $this->input->post("is_blacklist_check2", true);

    //跟进内容参数
    $follow_text_num = $this->input->post("follow_text_num2", true);
    if ($follow_text_num == "") {
      $follow_text_num = "0";
    }

    //楼盘名称只能选择录入参数
    $is_property_publish = $this->input->post("is_property_publish2", true);

    //房客源列表默认排序规则参数
    $house_list_order_field = $this->input->post("house_list_order_field2", true);

    //保密信息查看次数上限参数
    $secret_view_num = $this->input->post("secret_view_num2", true);
    if ($secret_view_num == "") {
      $secret_view_num = "0";
    }

    //登录时有提醒任务是否自动打开参数
    $is_remind_open = $this->input->post("is_remind_open2", true);

    //楼盘字典同步变化参数
    $is_community_modify_house = $this->input->post("is_community_modify_house2", true);

    //出租自动变公盘参数
    $rent_house_nature_public = $this->input->post("rent_house_nature_public2", true);
    if ($rent_house_nature_public == "") {
      $rent_house_nature_public = "0";
    }

    //房源变成没有归属人的公共房源
    $house_public_time = $this->input->post("house_public_time2", true);
    if ($house_public_time == "") {
      $house_public_time = "0";
    }
    //客源变成没有归属人的公共客源
    $customer_public_time = $this->input->post("customer_public_time2", true);
    if ($customer_public_time == "") {
      $customer_public_time = "0";
    }

    //求购自动变公客参数
    $buy_customer_nature_public = $this->input->post("buy_customer_nature_public2", true);
    if ($buy_customer_nature_public == "") {
      $buy_customer_nature_public = "0";
    }
    //出售自动变公盘参数
    $sell_house_nature_public = $this->input->post("sell_house_nature_public2", true);
    if ($sell_house_nature_public == "") {
      $sell_house_nature_public = "0";
    }
    //求租自动变公客参数
    $rent_customer_nature_public = $this->input->post("rent_customer_nature_public2", true);
    if ($rent_customer_nature_public == "") {
      $rent_customer_nature_public = "0";
    }
    //出售信息默认查询时间参数
    $sell_house_query_time = $this->input->post("sell_house_query_time2", true);

    //求购信息默认查询时间参数
    $buy_customer_query_time = $this->input->post("buy_customer_query_time2", true);

    //出租信息默认查询时间参数
    $rent_house_query_time = $this->input->post("rent_house_query_time2", true);

    //求租信息默认查询时间参数
    $rent_customer_query_time = $this->input->post("rent_customer_query_time2", true);

    //出租信息登记时间参数
    $rent_house_check_time = $this->input->post("rent_house_check_time2", true);
    if ($rent_house_check_time == "") {
      $rent_house_check_time = "0";
    }

    //求购信息登记时间参数
    $buy_customer_check_time = $this->input->post("buy_customer_check_time2", true);
    if ($buy_customer_check_time == "") {
      $buy_customer_check_time = "0";
    }

    //出售信息登记参数
    $sell_house_check_time = $this->input->post("sell_house_check_time2", true);
    if ($sell_house_check_time == "0") {
      $sell_house_check_time = "0";
    }

    //求租信息登记时间参数
    $rent_customer_check_time = $this->input->post("rent_customer_check_time2", true);
    if ($rent_customer_check_time == "") {
      $rent_customer_check_time = "0";
    }

    //两次客源跟进间隔参数
    $customer_follow_spacing_time = $this->input->post("customer_follow_spacing_time2", true);
    if ($customer_follow_spacing_time == "") {
      $customer_follow_spacing_time = "0";
    }

    //两次房源跟进间隔参数
    $house_follow_spacing_time = $this->input->post("house_follow_spacing_time2", true);
    if ($house_follow_spacing_time == "") {
      $house_follow_spacing_time = "0";
    }
    //房源默认显示范围
    $sell_house_indication_range = $this->input->post("sell_house_indication_range2", true);

    //房源默认显示范围
    $rent_house_indication_range = $this->input->post("rent_house_indication_range2", true);

    //是否开启合作中心
    $open_cooperate = $this->input->post("open_cooperate2", true);
    //是否开启合作中心旧数据
    $old_open_cooperate = $this->input->post("old_open_cooperate2", true);
    //是否开启合作中心旧数据
    $old_check_cooperate = $this->input->post("old_check_cooperate2", true);
    //查看房源密信息必须写跟进
    $old_is_secret_follow = $this->input->post("old_is_secret_follow2", true);
    //是否开启合作审核
    $check_cooperate = $this->input->post("check_cooperate2", true);

    //新增房源默认私盘
    $is_house_private = $this->input->post("is_house_private2", true);
    //新增客源默认私盘
    $is_customer_private = $this->input->post("is_customer_private2", true);
    //求购客源是否开启去重
    $buy_customer_unique = $this->input->post("buy_customer_unique2", true);
    //求租客源是否开启去重
    $rent_customer_unique = $this->input->post("rent_customer_unique2", true);
    //栋座单元门牌归属保密信
    $is_secrecy_information = $this->input->post("is_secrecy_information2", true);
    //查看房源密信息必须写跟进
    $is_secret_follow = $this->input->post("is_secret_follow2", true);
    //房源必须同步
    $is_fang100_insert = $this->input->post("is_fang100_insert2", true);
    //出售房源最后跟进日
    $sell_house_follow_last_time1 = $this->input->post("sell_house_follow_last_time1_form", true);
    //出售房源最后跟进日
    $sell_house_follow_last_time2 = $this->input->post("sell_house_follow_last_time2_form", true);
    //出租房源最后跟进日
    $rent_house_follow_last_time1 = $this->input->post("rent_house_follow_last_time1_form", true);
    //出租房源最后跟进日
    $rent_house_follow_last_time2 = $this->input->post("rent_house_follow_last_time2_form", true);
    //求购客源最后跟进日
    $buy_customer_follow_last_time1 = $this->input->post("buy_customer_follow_last_time1_form", true);
    //求购客源最后跟进日
    $buy_customer_follow_last_time2 = $this->input->post("buy_customer_follow_last_time2_form", true);
    //求租客源最后跟进日
    $rent_customer_follow_last_time1 = $this->input->post("rent_customer_follow_last_time1_form", true);
    //求租客源最后跟进日
    $rent_customer_follow_last_time2 = $this->input->post("rent_customer_follow_last_time2_form", true);

    //是否早晚打卡
    $is_check_work = $this->input->post("is_check_work2", true);
    //工作日
    $work_day = $this->input->post("work_day2", true);
    //出售房源列表自定义
    $sell_house_field = $this->input->post("sell_house_field2", true);
    //出租房源列表自定义
    $rent_house_field = $this->input->post("rent_house_field2", true);
    //求购客源列表自定义
    $buy_customer_field = $this->input->post("buy_customer_field2", true);
    //求租客源列表自定义
    $rent_customer_field = $this->input->post("rent_customer_field2", true);
    //上班时间
    $work_day_up_time = $this->input->post("work_day_up_time2", true);
    //下班时间
    $work_day_down_time = $this->input->post("work_day_down_time2", true);
    //查看出售业主信息
    $sell_house_secrecy_time = $this->input->post("sell_house_secrecy_time2", true);
    //查看出租业主信息
    $rent_house_secrecy_time = $this->input->post("rent_house_secrecy_time2", true);
    //查看求购业主信息
    $buy_customer_secrecy_time = $this->input->post("buy_customer_secrecy_time2", true);
    //查看求租业主信息
    $rent_customer_secrecy_time = $this->input->post("rent_customer_secrecy_time2", true);
    //每人的出售私盘数量
    $sell_house_private_num = $this->input->post("sell_house_private_num2", true);
    //每人的出租私盘数量
    $rent_house_private_num = $this->input->post("rent_house_private_num2", true);
    //每人的求购私客数量
    $buy_customer_private_num = $this->input->post("buy_customer_private_num2", true);
    //每人的求租私客数量
    $rent_customer_private_num = $this->input->post("rent_customer_private_num2", true);
    //房客源制
    $house_customer_system = $this->input->post("house_customer_system2", true);
    //是否锁盘
    $is_lock_cmt = $this->input->post("is_lock_cmt2", true);
    //是否锁盘武汉
    $is_lock_cmt_wh = $this->input->post("is_lock_cmt_wh2", true);
    //群发他人房源
    $publish_other_house = $this->input->post("publish_other_house2", true);

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
      "house_public_time" => $house_public_time,
      "customer_public_time" => $customer_public_time,
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
      "house_follow_spacing_time" => $house_follow_spacing_time,
      "open_cooperate" => $open_cooperate,
      "check_cooperate" => $check_cooperate,
      "is_house_private" => $is_house_private,
      "is_customer_private" => $is_customer_private,
      "buy_customer_unique" => $buy_customer_unique,
      "rent_customer_unique" => $rent_customer_unique,
      "is_secrecy_information" => $is_secrecy_information,
      "is_secret_follow" => $is_secret_follow,
      "is_fang100_insert" => $is_fang100_insert,
      "sell_house_follow_last_time1" => $sell_house_follow_last_time1,
      "sell_house_follow_last_time2" => $sell_house_follow_last_time2,
      "rent_house_follow_last_time1" => $rent_house_follow_last_time1,
      "rent_house_follow_last_time2" => $rent_house_follow_last_time2,
      "buy_customer_follow_last_time1" => $buy_customer_follow_last_time1,
      "buy_customer_follow_last_time2" => $buy_customer_follow_last_time2,
      "rent_customer_follow_last_time1" => $rent_customer_follow_last_time1,
      "rent_customer_follow_last_time2" => $rent_customer_follow_last_time2,
      "is_check_work" => $is_check_work,
      "work_day" => $work_day,
      "sell_house_field" => $sell_house_field,
      "rent_house_field" => $rent_house_field,
      "buy_customer_field" => $buy_customer_field,
      "rent_customer_field" => $rent_customer_field,
      "work_day_up_time" => $work_day_up_time,
      "work_day_down_time" => $work_day_down_time,
      "sell_house_secrecy_time" => $sell_house_secrecy_time,
      "rent_house_secrecy_time" => $rent_house_secrecy_time,
      "buy_customer_secrecy_time" => $buy_customer_secrecy_time,
      "rent_customer_secrecy_time" => $rent_customer_secrecy_time,
      "sell_house_private_num" => $sell_house_private_num,
      "rent_house_private_num" => $rent_house_private_num,
      "buy_customer_private_num" => $buy_customer_private_num,
      "rent_customer_private_num" => $rent_customer_private_num,
      "house_customer_system" => $house_customer_system,
      "publish_other_house" => $publish_other_house,
      "sell_house_indication_range" => $sell_house_indication_range,
      "rent_house_indication_range" => $rent_house_indication_range
    );
    //是否开启锁盘 逻辑不同于其他节点
    //（如果开启锁盘，直营店全改，加盟店从勾选中选取；关闭锁盘，所有的门店变成不锁盘）
    $paramArray_lock_cmt = array(
      "is_lock_cmt" => $is_lock_cmt,
      "is_lock_cmt_wh" => $is_lock_cmt_wh,
    );

    if (is_full_array($agency_arr)) {
      foreach ($agency_arr as $k => $v) {
        $agency_id = intval($v);
        $company_id = intval($this->user_arr['company_id']);
        $paramArray['company_id'] = $company_id;
        $setResult = $this->agency_basic_setting_model->judge_by_id($paramArray, $agency_id);
        if (is_int($setResult) && $setResult > 0) {
          //开启合作中心，从是变成否，房客源数据处理
          if ('1' == $old_open_cooperate && '0' == $open_cooperate) {
            $this->deal_data_cooperate(intval($v));
          }
          //开启合作审核，从是变成否，房客源数据处理
          if ('1' == $old_check_cooperate && '0' == $check_cooperate) {
            $this->deal_data_cooperate2(intval($v));
          }
          //查看房源密信息必须写跟进,从是变成否，数据处理
          if ('1' == $old_is_secret_follow && '0' == $is_secret_follow) {
            $this->deal_data_secret_follow(intval($v));
          }
        }
      }
    }

    //是否锁盘数据处理
    if ('1' == $is_lock_cmt || '1' == $is_lock_cmt_wh) {
      //获得当前公司下的所有直营店
      $this->agency_model->set_select_fields(array('id'));
      $all_agency_type_1 = $this->agency_model->get_children_by_company_id_type($company_id, 1);
      if (is_full_array($all_agency_type_1)) {
        foreach ($all_agency_type_1 as $k => $v) {
          $agency_id = intval($v['id']);
          $setResult_2 = $this->agency_basic_setting_model->judge_by_id($paramArray_lock_cmt, $agency_id);
        }
      }
      //获得勾选的门店中所有的加盟店
      if (is_full_array($agency_arr)) {
        foreach ($agency_arr as $k => $v) {
          $agency_id = intval($v);
          $this->agency_model->set_select_fields(array('id', 'agency_type'));
          $agency_data = $this->agency_model->get_by_id($agency_id);
          if (is_full_array($agency_data)) {
            if ('2' == $agency_data['agency_type']) {
              $setResult_2 = $this->agency_basic_setting_model->judge_by_id($paramArray_lock_cmt, $agency_id);
            }
          }
        }
      }
    } else {
      //获取当前公司下的所有门店
      $this->agency_model->set_select_fields(array('id'));
      $all_agency = $this->agency_model->get_children_by_company_id($company_id);
      if (is_full_array($all_agency)) {
        foreach ($all_agency as $k => $v) {
          $agency_id = intval($v['id']);
          $setResult_2 = $this->agency_basic_setting_model->judge_by_id($paramArray_lock_cmt, $agency_id);
        }
      }
    }

    //操作日志
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 31;
    $add_log_param['text'] = '修改基本设置';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();

    $this->operate_log_model->add_operate_log($add_log_param);

    if ($setResult === 1 || $setResult_2 === 1) {
      echo 'success';
    } else {
      echo 'failed';
    }
    exit;
  }


  private function deal_data_cooperate($agency_id = 0)
  {
    if (!empty($agency_id)) {
      //1）当前公司的以下合作房客源数据，合作字段is_share变为0
      //a）未进入合作流程的房客源数据 b）合作流程结束的房客源数据 c） 发起了申请还未接受的房客源数据

      //出售房源
      $all_sell_house_id = $this->sell_house_model->get_house_id_esta_4_7();
      if (is_full_array($all_sell_house_id)) {
        //变更相关房源的isshare字段
        $this->sell_house_model->change_is_share_by_not_house_id($all_sell_house_id, $agency_id);
      }

      //出租房源
      $all_rent_house_id = $this->rent_house_model->get_house_id_esta_4_7();
      if (is_full_array($all_rent_house_id)) {
        //变更相关房源的isshare字段
        $this->rent_house_model->change_is_share_by_not_house_id($all_rent_house_id, $agency_id);
      }

      //求购客源
      $all_buy_customer_id = $this->buy_customer_model->get_customer_id_esta_4_7();
      if (is_full_array($all_buy_customer_id)) {
        //变更相关客源的isshare字段
        $this->buy_customer_model->change_is_share_not_customer_id($all_buy_customer_id, $agency_id);
      }

      //求租客源
      $all_rent_customer_id = $this->rent_customer_model->get_customer_id_esta_4_7();
      if (is_full_array($all_rent_customer_id)) {
        //变更相关客源的isshare字段
        $this->rent_customer_model->change_is_share_not_customer_id($all_rent_customer_id, $agency_id);
      }

      //2)发起了申请还未接受的房客源数据,合作流程改为甲方拒绝
      $sell_house_cooperate_id = $this->cooperate_model->get_sell_house_cooperate_id_esta_1_by_agency_id($agency_id);
      $rent_house_cooperate_id = $this->cooperate_model->get_rent_house_cooperate_id_esta_1_by_agency_id($agency_id);
      $buy_customer_id_cooperate_id = $this->cooperate_model->get_buy_customer_cooperate_id_esta_1_by_agency_id($agency_id);
      $rent_customer_id_cooperate_id = $this->cooperate_model->get_rent_customer_cooperate_id_esta_1_by_agency_id($agency_id);
      $all_cooperate_id = array_merge($sell_house_cooperate_id, $rent_house_cooperate_id, $buy_customer_id_cooperate_id, $rent_customer_id_cooperate_id);

      $refuse_arr = array('step' => 1, 'type' => 4, 'reason' => '公司已关闭合作功能');
      $this->cooperate_model->change_esta_by_cooperate_id($all_cooperate_id, $refuse_arr);
    }
  }

  private function deal_data_cooperate2($agency_id = 0)
  {
    if (!empty($agency_id)) {
      //当前公司下所有的合作待审核房客源数据，改成合作房源。
      //出售房源
      $status_2_sell_house_id = $this->sell_house_model->get_isshare_2_house_id_by_agency_id(intval($agency_id));
      if (is_full_array($status_2_sell_house_id)) {
        //变更相关房源的isshare字段
        $this->sell_house_model->change_is_share_by_house_id($status_2_sell_house_id, 2);
      }
      //出租房源
      $status_2_rent_house_id = $this->rent_house_model->get_isshare_2_house_id_by_agency_id(intval($agency_id));
      if (is_full_array($status_2_rent_house_id)) {
        //变更相关房源的isshare字段
        $this->rent_house_model->change_is_share_by_house_id($status_2_rent_house_id, 2);
      }
      //求购客源
      $status_2_buy_customer_id = $this->buy_customer_model->get_isshare_2_customer_id_by_agency_id(intval($agency_id));
      if (is_full_array($status_2_buy_customer_id)) {
        //变更相关房源的isshare字段
        $this->buy_customer_model->change_is_share_by_customer_id($status_2_buy_customer_id, 2);
      }
      //求租客源
      $status_2_rent_customer_id = $this->rent_customer_model->get_isshare_2_customer_id_by_agency_id(intval($agency_id));
      if (is_full_array($status_2_rent_customer_id)) {
        //变更相关房源的isshare字段
        $this->rent_customer_model->change_is_share_by_customer_id($status_2_rent_customer_id, 2);
      }
    }

  }

  private function deal_data_secret_follow($agency_id = 0)
  {
    if (!empty($agency_id)) {
      //当前门店下所有的保密信息跟进进程，状态改成已结束。
      $this->load->model('secret_follow_process_model');
      $where_cond = array(
        'agency_id' => $agency_id,
        'type' => 1
      );
      $query_result = $this->secret_follow_process_model->get($where_cond);
      if (is_full_array($query_result)) {
        $update_arr = array(
          'status' => 2
        );
        foreach ($query_result as $k => $v) {
          $this->secret_follow_process_model->update(intval($v['id']), $update_arr);
        }
      }
    }

  }

}

