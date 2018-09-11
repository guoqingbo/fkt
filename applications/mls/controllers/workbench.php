<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 工作台
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Workbench extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $this->load->model('agency_model');
    $this->load->model('cityprice_model');
    $this->load->model('district_model');
    $this->load->model('auth_review_model');
    $this->load->model('api_broker_sincere_model');
    $this->load->model('agency_basic_setting_model');

    //出售房源
    $this->load->model('sell_house_model');

    //出租房源
    $this->load->model('rent_house_model');

    //求购客源
    $this->load->model('buy_customer_model');

    //求租客源
    $this->load->model('rent_customer_model');

    //采集
//		$this->load->model('collections_model');

    //跟进任务
    $this->load->model('task_model');

    // 合作申请
    $this->load->model('cooperate_model');

    //消息、公告模型类
    $this->load->model('message_model');

    //事件提醒
    $this->load->model('remind_model');

    //采集房源总数量（好房看看）
    $this->load->model('collections_model_new');

    //帮助中心
    $this->load->model('help_center_model');
    //公司公告
    $this->load->model('company_notice_model');
    //等级分值
    $this->load->model('api_broker_level_base_model');
      $this->load->model('signing_notice_model');//消息、公告模型类

  }


  public function index()
  {
    $data = array();
    $broker_info = $this->user_arr;
    $data['group_id'] = $broker_info['group_id'];
    $data['level'] = $this->api_broker_level_base_model->get_level($broker_info['level']);
    //获取悬赏总金额
    $reward_sum_arr = $this->sell_house_model->get_sum_cooperate_reward();
    if (isset($reward_sum_arr[0]['reward_sum']) && !empty($reward_sum_arr[0]['reward_sum'])) {
      $reward_sum = intval($reward_sum_arr[0]['reward_sum']);
      if ($reward_sum > 10000) {
        $reward_sum_big = strip_end_0($reward_sum / 10000, 1);
      }
      $data['reward_sum'] = $reward_sum;
      $data['reward_sum_big'] = $reward_sum_big;
    } else {
      $data['reward_sum'] = 0;
    }

    //获得最高悬赏
    $reward_max_arr = $this->sell_house_model->get_max_cooperate_reward();
    if (isset($reward_max_arr[0]['reward_max']) && !empty($reward_max_arr[0]['reward_max'])) {
      $reward_max = intval($reward_max_arr[0]['reward_max']);
      if ($reward_max > 10000) {
        $reward_max_big = strip_end_0($reward_max / 10000, 1);
      }
      $data['reward_max'] = $reward_max;
      $data['reward_max_big'] = $reward_max_big;
    } else {
      $data['reward_max'] = 0;
    }

    $broker_id = $broker_info['broker_id'];
    $data['broker_id'] = $broker_id;
    //获取动态评分（信息真实度、态度满意度、业务满意度）
    $appraise_and_avg = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);
    //信息真实度是否高于平均
    $result1 = $appraise_and_avg['infomation']['score'] - $appraise_and_avg['infomation']['avg'];
    if ($result1 > 0) {
      $info_up_down = 'up';
    } else if ($result1 < 0) {
      $info_up_down = 'down';
    } else {
      $info_up_down = 'fair';
    }
    $appraise_and_avg['infomation']['up_down'] = $info_up_down;

    //态度满意度是否高于平均
    $result2 = $appraise_and_avg['attitude']['score'] - $appraise_and_avg['attitude']['avg'];
    if ($result2 > 0) {
      $attitude_up_down = 'up';
    } else if ($result2 < 0) {
      $attitude_up_down = 'down';
    } else {
      $attitude_up_down = 'fair';
    }
    $appraise_and_avg['attitude']['up_down'] = $attitude_up_down;

    //业务满意度是否高于平均
    $result3 = $appraise_and_avg['business']['score'] - $appraise_and_avg['business']['avg'];
    if ($result3 > 0) {
      $business_up_down = 'up';
    } else if ($result3 < 0) {
      $business_up_down = 'down';
    } else {
      $business_up_down = 'fair';
    }
    $appraise_and_avg['business']['up_down'] = $business_up_down;
    $broker['appraise_and_avg'] = $appraise_and_avg;

    //获取好评率
    $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
    $broker['good_rate'] = $good_avg_rate['good_rate'];
    //好评率比平均值高
    $broker['good_rate_avg_high'] = $good_avg_rate['good_rate_avg_high'];

    //合作成功率平均值
    $this->load->model('cooperate_suc_ratio_base_model');
    $data['avg_cop_suc_ratio'] = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    $data['cop_succ_ratio_info'] = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($this->user_arr['broker_id']);


    //获取信用值和等级
    $trust_level = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    $broker['trust_level'] = $trust_level;

    $cond_where = array();
    $order_cond = 'updatetime';

    //采集出售新增数量
    $collect_sell_house_num = $this->collections_model_new->get_new_sell_num();
    //采集出租新增数量
    $collect_rent_house_num = $this->collections_model_new->get_new_rent_num();
    //采集房源新增数量
    $data['all_collect_house_num'] = $collect_sell_house_num + $collect_rent_house_num;
    //房源客源跟进新任务数量
    $cond_where_task_info = 'id <> 0 and status = 2 and run_broker_id = "' . $broker_id . '"';
    $task_num = $this->task_model->count_by($cond_where_task_info);
    $data['task_num'] = $task_num;

    //发起的合作申请
    $cond_where_send['brokerid_b'] = $broker_id;
    $cond_where_in = array();
    $cond_where_like = array();
    $wait_do_count = 'wait_do_b';
    $primary_postfix = '_b';
    //总数、待处理申请(1)、待评价合作(2)总数量（不随搜索条件变化）
    $data['send']['all_estas_num'] = $this->cooperate_model->get_cooperate_statistics_by_cond('all', $cond_where_send, $cond_where_in, $cond_where_like);
    $data['send']['all_estas_num1'] = $this->cooperate_model->get_cooperate_statistics_by_cond($wait_do_count, $cond_where_send, $cond_where_in, $cond_where_like);
    $data['send']['all_estas_num2'] = $this->cooperate_model->get_cooperate_statistics_by_cond('wait_appraise', $cond_where_send, $cond_where_in, $cond_where_like, $primary_postfix);

    //收到的合作申请
    $cond_where_accept['brokerid_a'] = $broker_id;
    $wait_do_count = 'wait_do_a';
    $primary_postfix = '_a';

    //总数、待处理申请(1)、待评价合作(2)总数量（不随搜索条件变化）
    $data['accept']['all_estas_num'] = $this->cooperate_model->get_cooperate_statistics_by_cond('all', $cond_where_accept, $cond_where_in, $cond_where_like);
    $data['accept']['all_estas_num1'] = $this->cooperate_model->get_cooperate_statistics_by_cond($wait_do_count, $cond_where_accept, $cond_where_in, $cond_where_like);
    $data['accept']['all_estas_num2'] = $this->cooperate_model->get_cooperate_statistics_by_cond('wait_appraise', $cond_where_accept, $cond_where_in, $cond_where_like, $primary_postfix);

    //首页轮播
    $cond_where_slider = array('type' => 5, 'slider' => 1, 'broker_id' => $broker_id);
    $data['slider_list'] = $this->message_model->get_row_by_cond($cond_where_slider);

    //系统公告
    $cond_where_message = array('type' => 5, 'broker_id' => $broker_id);
    $data['message_list'] = $this->message_model->get_system_by_cond($cond_where_message, 0, 6);

    //公司公告
    $company_id_notice = intval($broker_info['company_id']);
    $cond_where_notice = array('company_id' => $company_id_notice);
    $data['company_notice_list'] = $this->company_notice_model->get_company_notice_by($cond_where_notice, 0, 6);
    foreach ($data['company_notice_list'] as $k => $vo) {
      $data['company_notice_list'][$k]['title'] = mb_substr($vo['title'], 0, 15, 'utf-8');
      if (mb_strlen($vo['title']) > 15) {
        $data['company_notice_list'][$k]['title'] .= '...';
      }
    }
      //签约公告
      $data['signing_notice_list'] = [];
      $agency_info = $this->agency_model->get_one_by("id = " . $this->user_arr['agency_id']);
      if (!empty($agency_info)) {
          $cond_where = "company_id = 1 ";
          //收件对象为所有
          $cond_where .= " AND receipt_object_type='all'";
          //收件对象为区域
          $cond_where .= " OR receipt_object_district=" . $agency_info['dist_id'];
          //收件对象为门店
          $cond_where .= " OR receipt_object_agency=" . $this->user_arr['agency_id'];
          $data['signing_notice_list'] = $this->signing_notice_model->get_signing_notice_by($cond_where, 0, 6);
      }



    //我的消息数量
//    $cond_where_message2 = array('from' => 1, 'broker_id' => $broker_id);
//    $data['message2_num'] = $this->message_model->get_count_by_cond($cond_where_message2);

      //我的消息数量
      $cond_where_message3 = array('from' => 0, 'broker_id' => $broker_id, 'is_read' => 0);
      $data['message3_num'] = $this->message_model->get_count_by_cond($cond_where_message3);

    //事件提醒数量
    $where_cond_remind = "id != 0 ";
    $data['remind_num'] = $this->remind_model->get_remind_num($where_cond_remind);

    //最近更新房源个数(最近三天)
    $recent_hosue_num = $this->collections_model_new->get_recent_hosue_num();
    $data['recent_hosue_num'] = $recent_hosue_num;

    //最近七天被查看的房源个数
    $recent_brower_hosue_num = $this->collections_model_new->get_recent_brower_hosue_num();
    $data['recent_brower_hosue_num'] = $recent_brower_hosue_num;

    //获取系统默认基本设置
    $default_base_data = $this->agency_basic_setting_model->get_default_data();
    //获取当前门店基本设置
    $agency_base_data = $this->agency_basic_setting_model->get_data_by_agency_id($broker_info['agency_id']);

    if (!empty($agency_base_data["0"])) {
      $agency_base_data["0"]['work_day'] = explode(',', $agency_base_data["0"]['work_day']);
      $agency_setting = $agency_base_data["0"];
    } else {
      $default_base_data["0"]['work_day'] = explode(',', $default_base_data["0"]['work_day']);
      $agency_setting = $default_base_data["0"];
    }
    //是否早晚打卡
    $data['is_check_work'] = $agency_setting['is_check_work'];
    //上下班时间
    $data['work_day_up_time'] = $agency_setting['work_day_up_time'];
    $data['work_day_down_time'] = $agency_setting['work_day_down_time'];

    /*
     * 获取个人真实姓名
     */
    $broker['truename'] = $broker_info['truename'];
    /*
     * 获取城市表个人数据
     */
    $broker['photo'] = $broker_info['photo'];
    $broker['ident_auth'] = $broker_info['ident_auth'];
    $broker['quali_auth'] = $broker_info['quali_auth'];
    $broker['agency_id'] = $broker_info['agency_id'];
    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
    $broker['ident_auth_status'] = $ident_info['status'];
    $broker['credit'] = $broker_info['credit'];
    $broker['city'] = $broker_info['city_spell'];
    $data['broker'] = $broker;
    //print_r($broker);

    //页面标题
    $data['page_title'] = '工作台';
    if (!empty($broker_info['city_spell'])) {
      $api_url = 'http://api.house365.com/esf/web/get_city_month_averageprice.php?conf_cityflag=' . $broker_info['city_spell'];
    } else {
      $api_url = 'http://api.house365.com/esf/web/get_city_month_averageprice.php?conf_cityflag=hf';
    }

    $memkey = $broker_info['city_spell'] . "_get_city_month_averageprice";
    $house_price_cache = $this->mc->get($memkey);
    if ($house_price_cache['isok'] == 1) {
      $house_price_obj = $house_price_cache['data'];
    } else {
      $house_price_str = curl_get_contents($api_url);
      $house_price_obj = json_decode($house_price_str);
      $this->mc->add($memkey, array('isok' => 1, 'data' => $house_price_obj), 14400);
    }

    if (!empty($house_price_obj) && is_object($house_price_obj)) {
      $house_price_data = $house_price_obj->data->infoData;
      $house_price_arr = explode(',', $house_price_data);

      $min_price = 0;
      foreach ($house_price_arr as $k => $v) {
        $min_price = $min_price == 0 ? $v : $min_price;
        $min_price = $min_price > $v ? $v : $min_price;
      }
      $data['min_price'] = $min_price;

      $house_month_data = $house_price_obj->data->infoMonth;

      //$data['y_data'] = '['.$house_price_data.']';
      //$data['x_data'] = '['.$house_month_data.']';
    } else {
      $data['min_price'] = 14000;
      //$data['y_data'] = '[17407,17380,17370,17263,17316,17258,17143,17225,17238,17226,17036,17151]';
      //$data['x_data'] = "['201405','201406','201407','201408','201409','201410','201411','201412','201501','201502','201503','201504']";
    }

    $data['x_data'] = $this->cityprice_model->get_month();

    $data['y_data'] = $this->cityprice_model->get_price();
    $data['y_data_xf'] = $this->cityprice_model->get_price_xf();

    $data['min_price'] = $this->cityprice_model->get_min_price();
    //print_r($data['min_price']);die();

    $this->load->model('broker_login_log_model');
    $data['count_day'] = $this->broker_login_log_model->get_count_day_login($broker_id);

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/myStyle.css,mls/css/v1.0/style_p.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/personal_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,mls/js/v1.0/highcharts.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js,mls/js/v1.0/house.js');

    $this->view('workbench/index3', $data);
  }

}
/* End of file my_info.php */
/* Location: ./applications/mls/controllers/my_info.php */
