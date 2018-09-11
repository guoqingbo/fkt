<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 钥匙管理
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Key extends MY_Controller
{
  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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

  public function __construct()
  {
    parent::__construct();
    $this->load->model('key_model');
    $this->load->model('key_log_model');
    $this->load->model('agency_model');
    $this->load->model('operate_log_model');
  }

  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;


    $data['status_arr'] = array(
      0 => "全部",
      1 => "在店",
      2 => "外借",
      3 => "房主取走",
    );

    $company_id = $this->user_arr['company_id'];

    //根据数据范围，获得门店数据
    $this->load->model('agency_permission_model');
    $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
    $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_key');
    $all_access_agency_ids = '';
    if (is_full_array($access_agency_ids_data)) {
      foreach ($access_agency_ids_data as $k => $v) {
        $all_access_agency_ids .= $v['sub_agency_id'] . ',';
      }
      $all_access_agency_ids .= $this->user_arr['agency_id'];
      $all_access_agency_ids = trim($all_access_agency_ids, ',');
    } else {
      $all_access_agency_ids = $this->user_arr['agency_id'];
    }
    $data['agencys'] = $this->agency_model->get_all_by_agency_id($all_access_agency_ids);

    //查询房源条件
    $cond_where = "key.id > 0 AND key.agency_id in (" . $all_access_agency_ids . ")";

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //门店
    $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
    if ($agency_id) {
      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    }

    //楼盘
    $block_id = isset($post_param['block_id']) ? intval($post_param['block_id']) : 0;
    if ($block_id) {
      //获取钥匙栋座列表数组
      $list = array();
      $this->key_model->set_select_fields(array("dong"));
      $rs = $this->key_model->get_list_by(array("block_id" => $block_id), -1);
      foreach ($rs as $key => $val) {
        $list[] = $val['dong'];
      }
      $data['dongs'] = $list;
    }

    //楼盘
    $dong = isset($post_param['dong']) ? trim($post_param['dong']) : "";
    if ($block_id && $dong) {
      //获取钥匙单元列表数组
      $list = array();
      $this->key_model->set_select_fields(array("unit"));
      $rs = $this->key_model->get_list_by(array("block_id" => $block_id, "dong" => $dong), -1);
      foreach ($rs as $key => $val) {
        $list[] = $val['unit'];
      }
      $data['units'] = $list;
    }


    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);


    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    $cond_where = $cond_where == '' ? "broker_info.company_id = '" . $company_id . "' " : $cond_where . " AND broker_info.company_id = '" . $company_id . "' ";
    //符合条件的总行数
    $this->_total_count =
      $this->key_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->key_model->get_list_by($cond_where, $this->_offset, $this->_limit);
    if (is_full_array($list)) {
      foreach ($list as &$v) {
        $agency = $this->api_broker_model->get_by_agency_id($v['agency_id']);
        $v['agency_name'] = $agency['name'];
      }
    }
    $data['list'] = $list;
    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '钥匙列表';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');
    $this->view('house/key_list', $data);
  }

  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //房源编号
    $house_id = isset($form_param['house_id']) ? trim($form_param['house_id']) : "";
    if ($house_id) {
      $type = 0;
      if (stripos($house_id, "cs") !== false) {
        $type = 1;
      }
      if (stripos($house_id, "cz") !== false) {
        $type = 2;
      }
      if ($type) {
        $house_id = substr($house_id, 2);
        $cond_where .= " AND `key`.type = '" . $type . "' AND `key`.house_id = '" . $house_id . "'";
      }
    }
    //楼盘
    $block_id = isset($form_param['block_id']) ? intval($form_param['block_id']) : 0;
    if ($block_id) {
      $cond_where .= " AND `key`.block_id = '" . $block_id . "'";
    }
    //门店
    $agency_id = isset($form_param['agency_id']) ? intval($form_param['agency_id']) : 0;
    if ($agency_id) {
      $cond_where .= " AND `key`.agency_id = '" . $agency_id . "'";
    }
    //栋座
    $dong = isset($form_param['dong']) ? trim($form_param['dong']) : "";
    if ($dong) {
      $cond_where .= " AND `key`.dong = '" . $dong . "'";
    }
    //单元
    $unit = isset($form_param['unit']) ? trim($form_param['unit']) : "";
    if ($unit) {
      $cond_where .= " AND `key`.unit = '" . $unit . "'";
    }
    //钥匙编号
    $number = isset($form_param['number']) ? trim($form_param['number']) : "";
    if ($number) {
      $cond_where .= " AND `key`.number = '" . $number . "'";
    }
    //钥匙状态
    $status = isset($form_param['status']) ? intval($form_param['status']) : 0;
    if ($status) {
      $cond_where .= " AND `key`.status = '" . $status . "'";
    }
    //收钥匙人
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND `key`.broker_id = '" . $broker_id . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where .= " AND `key`.add_time >= '" . $start_time . "'";
    }

    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where .= " AND `key`.add_time <= '" . $end_time . "'";
    }
    if (isset($start_time) && isset($end_time) && $start_time > $end_time) {
      $this->jump(MLS_URL . '/key/', '您查询的开始时间不能大于结束时间！');
      exit;
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

  /**
   * 钥匙详情
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function details($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      //详情信息
      $list = $this->key_log_model->get_list_by(array("key_id" => $id), -1);
      foreach ($list as $key => $val) {
        $act_name = "";
        switch ($val['act']) {
          case 1:
            $act_name = "借钥匙";
            break;
          case 2:
            $act_name = "还钥匙";
            break;
          case 3:
            $act_name = "还业主";
            break;
        }
        $list[$key]['act_name'] = $act_name;
        $broker_info = array();
        $broker_info = $this->user_arr;
        $truename = $broker_info['truename'];
        $phone = $broker_info['phone'];
        $list[$key]['truename'] = $truename;
        $list[$key]['phone'] = $phone;
      }

      if ($list) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
      echo json_encode($result);
    }
  }

  //借钥匙
  public function borrow_key()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $this->add_key_logion($post_param);
  }

  //还钥匙
  public function also_key()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $this->add_key_logion($post_param);
  }

  //还业主
  public function also_owner()
  {
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $this->add_key_logion($post_param);
  }

  //添加钥匙日志
  public function add_key_logion($post_param = '')
  {
    $broker_info = array();
    $broker_info = $this->user_arr;
    $key_id = $post_param['key_id'];
    $act = $post_param['act'];
    //借出方的选择
    $company_status = intval($post_param['company_status']);

    if ((isset($post_param['company_status']) && $post_param['company_status'] == 1) || $company_status == '') {
      $agency_id = intval($post_param['agency_id']);
      $broker_id = intval($post_param['broker_id']);
      $borrow_person = $broker_info['truename'];
      $borrow_telephone = $broker_info['phone'];
      $borrow_company = $broker_info['agency_name'];
    } else {
      //如果是其他公司借的所对应的信息
      $broker_id = intval($broker_info['broker_id']);
      $agency_id = intval($broker_info['agency_id']);
      $borrow_person = $post_param['borrow_person'];
      $borrow_telephone = $post_param['borrow_telephone'];
      $borrow_company = $post_param['borrow_company'];
    }
    $time = $post_param['time'];

    if ($company_status == '') {
      $reason = '还钥匙';
    } else {
      $reason = trim($post_param['reason']);
    }

    //钥匙状态信息
    $this->key_model->set_select_fields(array("status", "num"));
    $info = $this->key_model->get_by_id($key_id);
    $status = $info["status"];

    //在店状态，无法执行还钥匙操作
    if ($status == 1 && $act == 2) {
      $this->jump(MLS_URL . '/key/', '钥匙为在店状态，无法执行还钥匙操作');
      exit;
    }

    //外借状态，无法执行借钥匙或还业主操作
    if ($status == 2 && in_array($act, array(1, 3))) {
      $this->jump(MLS_URL . '/key/', '钥匙为外借状态，无法执行借钥匙或还业主操作');
      exit;
    }

    //房主取走状态
    if ($status == 3) {
      $this->jump(MLS_URL . '/key/', '钥匙为房主取走状态，无法执行操作');
      exit;
    }


    $datainfo['key_id'] = $key_id;
    $datainfo['act'] = $act;
    $datainfo['time'] = $time;
    $datainfo['company_status'] = $company_status;//借出方的选择

    $datainfo['agency_id'] = $agency_id;
    $datainfo['broker_id'] = $broker_id;

    $datainfo['borrow_person'] = $borrow_person;
    $datainfo['borrow_telephone'] = $borrow_telephone;
    $datainfo['borrow_company'] = $borrow_company;

    if ($reason) {
      $datainfo['reason'] = $reason;
    }
    $rs = $this->key_log_model->add_info($datainfo);
    //echo $re;
    if ($rs) {
      //操作日志
      $key_log_data = $this->key_log_model->get_by_id(intval($rs));
      $add_log_text = '';
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 38;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $update = array();
      if ($act == 1) {
        $update['status'] = 2;//借钥匙操作，钥匙状态改为外借
        $update['num'] = $info["num"] + 1;//借用次数+1
        if (is_full_array($key_log_data)) {
          $add_log_text .= $key_log_data['borrow_company'] . ' ' . $key_log_data['borrow_person'] . ' 借用钥匙编号为' . $key_log_data['key_id'] . '的钥匙 ' . '时间到 ' . $key_log_data['time'];
        }
        $add_log_param['text'] = $add_log_text;
        $this->operate_log_model->add_operate_log($add_log_param);
      } elseif ($act == 2) {
        $update['status'] = 1;//还钥匙操作，钥匙状态改为在店
        if (is_full_array($key_log_data)) {
          $add_log_text .= $key_log_data['borrow_company'] . ' ' . $key_log_data['borrow_person'] . ' 归还钥匙编号为' . $key_log_data['key_id'] . '的钥匙 ';
        }
        $add_log_param['text'] = $add_log_text;
        $this->operate_log_model->add_operate_log($add_log_param);
      } elseif ($act == 3) {
        $update['status'] = 3;//还业主操作，钥匙状态改为房主取走
      }
      $this->key_model->update_by_id($update, $key_id);
      // $this->jump(MLS_URL.'/key/', '操作成功');
      echo json_encode(array('result' => 'ok'));
      exit;
    } else {
      // $this->jump(MLS_URL.'/key/', '操作失败');
      echo json_encode(array('result' => 'fail'));
      exit;
    }
  }

  //验证钥匙编号
  public function check_number()
  {
    $house_id = $this->input->post('house_id', TRUE);
    $number = $this->input->post('number', TRUE);
    $info = $this->key_model->get_one_by(array('number' => $number,
      'company_id' => $this->user_arr['company_id'], 'status !=' => '3'));
    if (empty($info)) {
      echo("true");
    } else {
      if ($info['house_id'] == $house_id) {
        echo("true");
      } else {
        echo("false");
      }
    }
  }

  /**
   * 钥匙栋座列表
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function get_dong_list()
  {
    $block_id = $this->input->get('block_id', TRUE);
    if ($block_id) {
      //钥匙栋座列表信息
      $list = array();
      $this->key_model->set_select_fields(array("dong"));
      $rs = $this->key_model->get_list_by(array("block_id" => $block_id), -1);
      foreach ($rs as $key => $val) {
        $list[] = $val['dong'];
      }

      if (is_full_array($list)) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
    } else {
      $result = array('result' => 'no');
    }
    echo json_encode($result);
  }

  /**
   * 钥匙单元列表
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function get_unit_list()
  {
    $block_id = $this->input->get('block_id', TRUE);
    $dong = $this->input->get('dong', TRUE);
    if ($block_id && $dong) {
      //钥匙单元列表信息
      $list = array();
      $this->key_model->set_select_fields(array("unit"));
      $rs = $this->key_model->get_list_by(array("block_id" => $block_id, "dong" => $dong), -1);
      foreach ($rs as $key => $val) {
        $list[] = $val['unit'];
      }

      if (is_full_array($list)) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
    } else {
      $result = array('result' => 'no');
    }
    echo json_encode($result);
  }
}
