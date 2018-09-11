<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 营销中心-业主预约
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Check_work_center extends MY_Controller
{
  /**
   * 当前页码
   *
   * @access private
   * @var string
   */
  private $_current_page = 1;

  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 10;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  /**
   * 条目总数
   *
   * @access private
   * @var int
   */
  private $_total_count = 0;


  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->model('check_work_model');
    $this->load->model('company_employee_model');
    //表单验证
    $this->load->library('form_validation');
    //加载房源标题模板类
    $this->load->model('broker_model');
    $this->load->model('api_broker_model');
    $this->load->library('Verify');
    $this->load->model('agency_basic_setting_model');
    //权限
    if (is_full_array($this->user_arr)) {
      $this->load->model('broker_permission_model');
      $this->broker_permission_model->set_broker_id($this->user_arr['broker_id'], $this->user_arr['company_id']);
    }
  }

  /**
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param, $type = '')
  {
    /*if($type == 1){
            $cond_where = 'rent_house.status = 1';
        }else{
            $cond_where = 'sell_house.status = 1';
        }*/

    if (!empty($form_param['house_id']) && $form_param['house_id'] > 0) {
      $house_id = intval($form_param['house_id']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "house_id = '" . $house_id . "'";
    }


    //查看户型条件
    if (isset($form_param['room']) && !empty($form_param['room'])) {
      $room = intval($form_param['room']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "room = '" . $room . "'";
    } else if ($form_param['room'] == '0') {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "room IN (0,1,2,3,4,5,6)";
    }

    //区属
    $district_id = intval($form_param['dist_id']);
    //板块
    $street_id = intval($form_param['street_id']);
    if ($street_id) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      if ($type == 1) {
        $cond_where .= "rent_house.street_id = '" . $street_id . "'";
      } else {
        $cond_where .= "sell_house.street_id = '" . $street_id . "'";
      }
    } else if ($district_id) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "district_id = '" . $district_id . "'";
    }

    //楼盘ID出售出租
    if (!empty($form_param['block_name']) && $form_param['block_id'] > 0) {
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "block_id = '" . $form_param['block_id'] . "'";
    }

    //电话
    if (isset($form_param['phone']) && !empty($form_param['phone'])) {
      $phone = intval($form_param['phone']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "apnt.phone LIKE '%" . $phone . "%'";
    }


    //最小面积
    if (isset($form_param['buildarea1']) && !empty($form_param['buildarea1'])) {
      $buildarea1 = trim($form_param['buildarea1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "buildarea >= '" . $buildarea1 . "'";
    }

    //最大面积
    if (isset($form_param['buildarea2']) && !empty($form_param['buildarea2'])) {
      $buildarea2 = trim($form_param['buildarea2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "buildarea <= '" . $buildarea2 . "'";
    }

    //最小价格
    if (isset($form_param['price1']) && !empty($form_param['price1'])) {
      $price1 = trim($form_param['price1']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "price >= '" . $price1 . "'";
    }

    //最大价格
    if (isset($form_param['price2']) && !empty($form_param['price2'])) {
      $price2 = trim($form_param['price2']);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "price <= '" . $price2 . "'";
    }

    //最小时间
    if (isset($form_param['stimemin']) && !empty($form_param['stimemin'])) {
      $stimemin = strtotime(trim($form_param['stimemin'])) - 24 * 3600;
      $stimemin = date("Y-m-d", $stimemin);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "sdate > '" . $stimemin . "'";
    }

    //最大时间
    if (isset($form_param['stimemax']) && !empty($form_param['stimemax'])) {
      $stimemax = strtotime(trim($form_param['stimemax'])) + 24 * 3600;
      $stimemax = date("Y-m-d", $stimemax);
      $cond_where .= !empty($cond_where) ? ' AND ' : '';
      $cond_where .= "sdate < '" . $stimemax . "'";
    }

    return $cond_where;
  }


  /**
   * 初始化分页参数
   *
   * @access public
   * @param  int $current_page
   * @param  int $page_size
   * @return void
   */
  private function _init_pagination($current_page = 1, $page_size = 0)
  {
    /** 当前页 */
    $this->_current_page = ($current_page && is_numeric($current_page)) ?
      intval($current_page) : 1;

    /** 每页多少项 */
    $this->_limit = ($page_size && is_numeric($page_size)) ?
      intval($page_size) : $this->_limit;

    /** 偏移量 */
    $this->_offset = ($this->_current_page - 1) * $this->_limit;

    if ($this->_offset < 0) {
      redirect(base_url());
    }
  }

  public function index()
  {
    //模板使用数据
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);

    //当前用户的所有信息
    $company_info = $this->agency_model->get_by_id($broker_info['company_id']);
    $broker_info['company_name'] = $company_info['name'];
    $data['broker_info'] = $broker_info;
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //print_r($post_param);

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

    //工作日
    $data['workdays'] = $agency_setting['work_day'];
    foreach ($data['workdays'] as $key => $vo) {
      if ($vo == 7) {
        $data['workdays'][$key] = 0;
      }
    }
    //是否早晚打卡
    $data['is_check_work'] = $agency_setting['is_check_work'];
    //上下班时间
    $data['work_day_up_time'] = $agency_setting['work_day_up_time'];
    $data['work_day_down_time'] = $agency_setting['work_day_down_time'];


    //构建日历
    if ($post_param['year'] && $post_param['month']) {
      $year = $post_param['year'];
      $month = $post_param['month'];
    } else {
      $year = date('Y', time());
      $month = intval(date('m', time()));
    }
    $daysInMonth = date("t", mktime(0, 0, 0, $month, 1, $year)); //获得当月的总天数
    $firstDay = date("w", mktime(0, 0, 0, $month, 1, $year));  //获得每个月的第一天
    //$firstDay -= 1;
    $tempDays = $firstDay + $daysInMonth; //计算数组中的日历表格数
    $weeksInMonth = ceil($tempDays / 7); //获得表格行数
    //创建一个二维数组用来存放日期信息
    for ($j = 0; $j < $weeksInMonth; $j++) {
      for ($i = 0; $i < 7; $i++) {
        $counter++;
        $week[$j][$i] = $counter;
        //日期偏移量
        $week[$j][$i] -= $firstDay;
        if (($week[$j][$i] < 1) || ($week[$j][$i] > $daysInMonth)) {
          $week [$j] [$i] = "";
        }
      }
    }
    //print_r($week);cal_days_in_month()
    $data['year'] = $year;
    $data['month'] = $month;
    $data['week'] = $week;
    //当月实际已过日期和月
    if (date('Y', time()) != $year || intval(date('m', time())) != $month) {
      $data['date_past'] = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
      $data['date_past_month'] = date('Y-m', mktime(0, 0, 0, $month, 1, $year));
    } else {
      $data['date_past'] = date('Y-m-d', time() - 3600 * 24);
      $data['date_past_month'] = date('Y-m', time() - 3600 * 24);
    }
    $data['broker_id'] = $broker_id;

    //表单提交参数组成的查询条件
    $cond_where .= 'broker_id = ' . $broker_id . ' AND year = ' . $year . ' AND month = ' . $month;

    $day_data_new = array();   //具有数据的天
    //获取考勤打卡数据列表内容
    $list = $this->check_work_model->get_list_by($cond_where, 0, 0, 'createtime', 'ASC');
    foreach ($list as $key => $vo) {
      if ($vo['status'] == 1) {
        $data1[$vo['day']]['cktime1'] = date('H:i:s', $vo['clocktime']);
      } elseif ($vo['status'] == 2) {
        $data1[$vo['day']]['cktime2'] = date('H:i:s', $vo['clocktime']);
      } elseif ($vo['status'] == 5) {
        $data1[$vo['day']]['cktime5'] = date('H:i:s', $vo['clocktime']);
      } elseif ($vo['status'] == 6) {
        $data1[$vo['day']]['cktime6'] = date('H:i:s', $vo['clocktime']);
      } elseif ($vo['status'] == 3) {
        if (date('Y-m-d H:i:s', $vo['ltime_up']) > date('Y-m-d H:i:s', $vo['ltime_down'])) {
          $data1[$vo['day']]['lup3'] = $agency_setting['work_day_up_time'];
        } else {
          $data1[$vo['day']]['lup3'] = date('H:i:s', $vo['ltime_up']);
        }
        if (date('Y-m-d', $vo['ltime_down']) > date('Y-m-d', mktime(0, 0, 0, $vo['month'], $vo['day'], $vo['year']))) {
          $data1[$vo['day']]['ldown3'] = $agency_setting['work_day_down_time'];
        } else {
          $data1[$vo['day']]['ldown3'] = date('H:i:s', $vo['ltime_down']);
        }
      } elseif ($vo['status'] == 4) {
        $data1[$vo['day']]['lup4'] = date('H:i:s', $vo['ltime_up']);
        $data1[$vo['day']]['ldown4'] = date('H:i:s', $vo['ltime_down']);
      }
      $day_data_new[] = $vo['day'];
    }
    //print_r($data1);exit;
    $data['data1'] = $data1;
    $data['day_data_new'] = array_unique($day_data_new);

    //print_r($data['day_data_new']);

    //页面标题
    $data['page_title'] = '我的考勤表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/scrollPic.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house.js');

    $data['post_param'] = $post_param;


    $this->view('check_work_center/check_work_center', $data);
  }


  /**
   * 添加打卡
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function add_work($broker_id)
  {
    $post_param = $this->input->post(NULL, TRUE);
    $post_param['broker_id'] = $broker_id;
    $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
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

    $year = date('Y', time());
    $month = date('m', time());
    $day = date('d', time());

    //上下班时间并转化成当天时间戳
    $work_day_up_time = $agency_setting['work_day_up_time'];
    $work_day_up_time_array = explode(':', $work_day_up_time);
    $work_day_up_time = mktime($work_day_up_time_array[0], $work_day_up_time_array[1], $work_day_up_time_array[2], $month, $day, $year);
    $work_day_down_time = $agency_setting['work_day_down_time'];
    $work_day_down_time_array = explode(':', $work_day_down_time);
    $work_day_down_time = mktime($work_day_down_time_array[0], $work_day_down_time_array[1], $work_day_down_time_array[2], $month, $day, $year);
    //是否早晚打卡
    $is_check_work = $agency_setting['is_check_work'];


    //print_r($post_param );exit;
    $result = 0;
    //插入数据
    $add_data = array();
    $add_data['status'] = $post_param['type'];
    $add_data['broker_id'] = $broker_id;
    $add_data['createtime'] = time();
    $add_data['year'] = $year;
    $add_data['month'] = intval($month);
    $add_data['day'] = intval($day);
      $add_data['ip'] = get_ip();
    $where = 'broker_id =' . $broker_id . ' and year=' . $year . ' and month=' . $add_data['month'] . ' and day=' . $add_data['day'];
    if ($post_param['type'] == 1) {
      //判断是否已打卡
      $where_new = $where . ' and status in (1,5)';
      $work_info = $this->check_work_model->get_one_by($where_new);
      //查找打卡时间点是否有请假外出记录并覆盖到上班时间前
      $where_out = $where . ' and status in (3,4) and ltime_up<=' . $work_day_up_time . ' and ltime_down>=' . time();
      $work_info_out = $this->check_work_model->get_one_by($where_out);
      if (!empty($work_info) && is_full_array($work_info)) {
        echo json_encode(array('result' => 'once', 'type' => $post_param['type']));
        exit;
      }
      $add_data['clocktime'] = time();
      if (date('H:i:s', $add_data['clocktime']) > date('H:i:s', $work_day_up_time)) {
        //如打卡时间点在请假外出记录内则判定正常打卡并覆盖当天请假时间
        if (is_full_array($work_info_out) && !empty($work_info_out)) {
          $work_leave_update = $where . ' and status = 3';
          $work_leave_update_date = array('ltime_down' => $add_data['clocktime']);
          $this->check_work_model->update_work($work_leave_update, $work_leave_update_date);
        } else {
          $add_data['status'] = 5;
        }
      }
      $result = $this->check_work_model->add_work($add_data);
    } else if ($post_param['type'] == 2) {
      //判断是否已打卡
      $where_new = $where . ' and status in (2,6)';
      $work_info = $this->check_work_model->get_one_by($where_new);
      if (!empty($work_info) && is_full_array($work_info)) {
        echo json_encode(array('result' => 'once', 'type' => $post_param['type']));
        exit;
      }
      //判断上班是否已打卡
      $where_up = $where . ' and status in (1,5) ';
      $work_info_up = $this->check_work_model->get_one_by($where_up);
      if (empty($work_info_up)) {
        echo json_encode(array('result' => 'uplose', 'type' => $post_param['type']));
        exit;
      }
      //判断在下班前打卡时间是否有请假外出覆盖到下班时间段内
      if (time() < $work_day_down_time) {
        $where_down = $where . ' and status in (3,4) and ltime_down >= ' . $work_day_down_time . ' and ltime_up >= ' . time();
        $work_info_down = $this->check_work_model->get_one_by($where_down);
      } else {
        $work_info_down = array();
      }
      $add_data['clocktime'] = time();
      if (date('H:i:s', $add_data['clocktime']) < date('H:i:s', $work_day_down_time) && empty($work_info_down)) {
        $add_data['status'] = 6;
      }
      $result = $this->check_work_model->add_work($add_data);
    } else {
      $add_data['remark'] = $post_param['remark'];
      $ltime_up = strtotime($post_param['ltime_up']);
      $ltime_down = strtotime($post_param['ltime_down']);
      $add_data['ltime_up'] = $ltime_up;
      $add_data['ltime_down'] = $ltime_down;
      //判断起始时间必须小于终止时间
      if ($add_data['ltime_up'] > $add_data['ltime_down']) {
        echo json_encode(array('result' => 'timeout', 'type' => $post_param['type']));
        exit;
      }
      //判断是否有重复请假/外出时间
      $where_lup = 'status = ' . $post_param['type'] . ' and broker_id=' . $broker_id . ' and ltime_up<' . $ltime_up . ' and ltime_down>' . $ltime_up;
      $work_info_up = $this->check_work_model->get_one_by($where_lup);
      $where_ldown = 'status = ' . $post_param['type'] . ' and broker_id=' . $broker_id . ' and ltime_down<' . $ltime_down . ' and ltime_down>' . $ltime_down;
      $work_info_down = $this->check_work_model->get_one_by($where_ldown);
      //print_r($work_info_up);exit;
      if ((is_full_array($work_info_up) && !empty($work_info_up)) || (is_full_array($where_ldown) && !empty($where_ldown))) {
        echo json_encode(array('result' => 'timerepeat', 'type' => $post_param['type']));
        exit;
      }
      //判断请假时间跨度并分别计入数据库
      if ($post_param['type'] == 3) {
        $day_diff = intval((strtotime(date('Y-m-d', $ltime_down)) - strtotime(date('Y-m-d', $ltime_up))) / (24 * 3600));
        for ($i = $day_diff; $i >= 0; $i--) {
          $add_data['year'] = date('Y', $ltime_up);
          $add_data['month'] = intval(date('m', $ltime_up));
          $add_data['day'] = intval(date('d', $ltime_up));
          $ltime_up += 24 * 3600;
          if ($this->check_work_model->add_work($add_data)) {
            $result++;
          }
        }
      } else {
        $result = $this->check_work_model->add_work($add_data);
      }
    }
    if ($result) {
      echo json_encode(array('result' => 'ok', 'type' => $post_param['type']));
    } else {
      echo json_encode(array('result' => 'fail', 'type' => $post_param['type']));
    }
  }

  /**
   * 考勤详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  function work_detail()
  {
    $post_param = $this->input->post(NULL, TRUE);
    if ($post_param['is_check_work']) {
      $where = 'broker_id =' . $post_param['broker_id'] . ' and year=' . $post_param['year'] . ' and month=' . $post_param['month'] . ' and day=' . $post_param['day'];
    } else {
      $where = 'broker_id =' . $post_param['broker_id'] . ' and year=' . $post_param['year'] . ' and month=' . $post_param['month'] . ' and day=' . $post_param['day'] . ' and status in (1,3,4,5)';
    }
    $work_info = $this->check_work_model->get_list_by($where, 0, 0, 'createtime', 'ASC');
    foreach ($work_info as $key => $vo) {
      $work_info[$key]['createtime'] = date("H:i", $vo['createtime']);
      $work_info[$key]['ltime_up'] = date("Y-m-d H:i:s", $vo['ltime_up']);
      $work_info[$key]['ltime_down'] = date("Y-m-d H:i:s", $vo['ltime_down']);
    }
    $data['work_info'] = $work_info;
    $data['year'] = $post_param['year'];
    $data['month'] = $post_param['month'];
    $data['day'] = $post_param['day'];

    echo json_encode($data);
  }
}
/* End of file entrust.php */
/* Location: ./applications/mls/controllers/entrust.php */
