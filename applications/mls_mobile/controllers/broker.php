<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 员工增删改查
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Broker extends MY_Controller
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
  private $_limit = 20;

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
    $this->load->model('broker_info_model');
    $this->load->model('broker_model');
    $this->load->model('agency_model');
    $this->load->model('permission_company_role_model');
    $this->load->model('operate_log_model');
  }

  //员工管理页
  public function broker_list()
  {
    $data_view = array();
    $data_view['broker_id'] = $this->user_arr['broker_id'];

    $pg = $this->input->post('page');
    $pagesize = $this->input->post('pagesize');

    $where = 'status = 1';
    $where .= ' and company_id = ' . $this->user_arr['company_id'];
    $this->agency_model->set_select_fields(array('id', 'name'));
    $data_view['agencys'] = $this->agency_model->get_all_by($where);

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page, $pagesize);

    //员工信息
    $this->broker_info_model->set_select_fields(array());
    $broker_info = $this->broker_info_model->get_all_by($where, $this->_offset, $this->_limit);
    //var_dump($broker_info);exit;
    $this->load->model('auth_review_model');
    foreach ($broker_info as $key => $value) {
      $broker_info[$key]['agency_name'] = '';
      $broker_info[$key]['role_name'] = '';
      $this->agency_model->set_select_fields(array('name'));
      $this->permission_company_role_model->set_select_fields(array('name'));
      if ($value['agency_id']) {
        $agency_info = $this->agency_model->get_by_id($value['agency_id']);
        $broker_info[$key]['agency_name'] = $agency_info['name'];
      }
      if ($value['role_id']) {
        $role_info = $this->permission_company_role_model->get_by_id($value['role_id']);
        $broker_info[$key]['role_name'] = $role_info['name'];
      }
      //身份认证信息

      $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $value['broker_id'], 0, 1);
      if (is_full_array($ident_info)) {
        $broker_info[$key]['auth_ident_status'] = $ident_info['status'];
      } else {
        $broker_info[$key]['auth_ident_status'] = '';
      }
      //资质认证信息
      $quali_info = $this->auth_review_model->get_new("type = 2 and broker_id = " . $value['broker_id'], 0, 1);
      if (is_full_array($quali_info)) {
        $broker_info[$key]['auth_quali_status'] = $quali_info['status'];
      } else {
        $broker_info[$key]['auth_quali_status'] = '';
      }
    }
    $data_view['agency'] = $broker_info;
    $this->result(1, '获取成功', $data_view);
  }

  //根据门店id获取经纪人
  public function get_brokers()
  {
    $agency_id = $this->input->get('agency_id', TRUE);
    $agency_id = intval($agency_id);
    $this->load->model('api_broker_model');
    $brokered_list = $this->api_broker_model->get_brokers_agency_id($agency_id);
    $broker_lists = array();
    foreach ($brokered_list as $key => $val) {
      $broker_lists[$key]['broker_id'] = $val['broker_id'];
      $broker_lists[$key]['truename'] = $val['truename'];
    }
    $broker_list = array();
    $broker_list[] = array('broker_id' => '0', 'truename' => '不限');
    foreach ($broker_lists as $key => $val) {
      $broker_list[] = $val;
    }
    $this->result('1', '经纪人信息', $broker_list);

  }

  //修改密码
  public function modify_password()
  {
    $this_user = $this->user_arr;
    $broker_id = $this_user['broker_id'];
    $old_password = $this->input->post('old_password');
    $new_password = $this->input->post('new_password');
    $equal_password = $this->input->post('equal_password');
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    $modify_data = $this->broker_model->modify_password($broker_id, $old_password, $new_password, $equal_password);

    if ('password_not_true' == $modify_data) {
      $this->result(2, '您输入的原密码错误，请重新输入！');
      return;
    } elseif ('password_not_same' == $modify_data) {
      $this->result(3, '您两次输入的新密码不一致，请重新输入！');
      return;
    } else {
      //操作日志
      $broker_info = $this->broker_info_model->get_by_broker_id(intval($broker_id));
      $old_agency_info = $this->agency_model->get_by_id(intval($broker_info['agency_id']));
      $old_agency_name = '';
      if (is_full_array($old_agency_info)) {
        $old_agency_name = $old_agency_info['name'];
      }

      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 44;
      $add_log_param['text'] = '修改"' . $old_agency_name . '" "' . $broker_info['truename'] . '"密码';
      if ($devicetype == 'android') {
        $add_log_param['from_system'] = 2;
      } else {
        $add_log_param['from_system'] = 3;
      }
      $add_log_param['device_id'] = $deviceid;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->operate_log_model->add_operate_log($add_log_param);

      $this->result(1, '修改密码成功！');
      return;
    }
  }

  //经纪人签到
  public function sign()
  {

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

  //判断输入号码是否为黑名单
  public function check_blacklist()
  {
    $return_arr = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //录入数据，是否黑名单校验
    $is_blacklist_check = $company_basic_data['is_blacklist_check'];

    $telno = $this->input->get('telno', TRUE);
    $int_telno = trim($telno);
    $this->load->model('blacklist_model');
    $where_sql = ' where tel = "' . $int_telno . '"';
    $result_arr = $this->blacklist_model->get_all_by($where_sql);
    if ('1' == $is_blacklist_check && count($result_arr) > 0) {
      $return_arr['isok'] = '1';
      $return_arr['msg'] = '该号码在黑名单中，可能为虚假信息源';
    } else {
      $return_arr['isok'] = '0';
      $return_arr['msg'] = '号码非黑名单';
    }
    $return_arr['result'] = '1';
    echo json_encode($return_arr);
    exit;
  }

  //当前用户基本设置，是否进行黑名单校验
  public function is_check_blacklist()
  {
    $result_arr = array();
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    if (isset($company_basic_data['is_blacklist_check']) && '1' == $company_basic_data['is_blacklist_check']) {
      $result_arr['isok'] = '1';
      $result_arr['msg'] = '有黑名单校验';
    } else {
      $result_arr['isok'] = '0';
      $result_arr['msg'] = '无黑名单校验';
    }
    $result_arr['result'] = '1';
    echo json_encode($result_arr);
    exit;
  }

  //获取经纪人列表
  public function get_brokerinfo_list()
  {
    $broker_ids = $this->input->get('broker_ids');
    if ($broker_ids == '') {
      $this->result(0, '经纪人编号不能为空');
      return false;
    }
    //经纪人数组
    $broker_ids = explode(',', $broker_ids);
    if (count($broker_ids) > 20) {
      $this->result(0, '超过上限');
      return false;
    }
    $new_brokers = array();
    $this->load->model('api_broker_model');
    $brokers = $this->api_broker_model->get_by_broker_ids($broker_ids);
    if (is_full_array($brokers)) {
      foreach ($brokers as $v) {
        $new_brokers[] = array(
          'broker_id' => $v['broker_id'], 'broker_name' => $v['truename'],
          'photo' => $v['photo']
        );
      }
    }
    $this->result(1, '查询成功', $new_brokers);
  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
