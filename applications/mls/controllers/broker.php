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
    $this->load->model('broker_info_model');
    $this->load->model('broker_model');
    $this->load->model('agency_model');
    $this->load->model('permission_company_role_model');
    $this->load->model('broker_view_secrecy_model');
    $this->load->model('sell_house_model');
    $this->load->model('rent_house_model');
    $this->load->model('buy_customer_model');
    $this->load->model('rent_customer_model');
  }

  //员工管理页
  public function index()
  {
    $data_view = array();
    $data_view['broker_id'] = $this->user_arr['broker_id'];
    $data_view['user_menu'] = $this->user_menu;
    $data_view['user_func_menu'] = $this->user_func_menu;

    $where = 'status = 1';
    $where .= ' and company_id = ' . $this->user_arr['company_id'];

    $this->agency_model->set_select_fields(array('id', 'name'));
    $data_view['agencys'] = $this->agency_model->get_all_by('status = 1 and company_id = ' . $this->user_arr['company_id']);
    $pg = $this->input->post('page');

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->broker_info_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $pg,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data_view['page_list'] = $this->page_list->show('jump');
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
        if (is_full_array($agency_info)) {
          $broker_info[$key]['agency_name'] = $agency_info['name'];
        } else {
          $broker_info[$key]['agency_name'] = '';
        }
      }
      if ($value['role_id']) {
        $role_info = $this->permission_company_role_model->get_by_id($value['role_id']);
        if (is_full_array($role_info)) {
          $broker_info[$key]['role_name'] = $role_info['name'];
        } else {
          $broker_info[$key]['role_name'] = '';
        }
      }
      //身份认证信息

      $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $value['broker_id'], 0, 1);
      if (is_full_array($ident_info)) {
        $broker_info[$key]['auth_ident_status'] = $ident_info['status'];
      } else {
        $broker_info[$key]['auth_ident_status'] = 0;
      }
      //资质认证信息
      $quali_info = $this->auth_review_model->get_new("type = 2 and broker_id = " . $value['broker_id'], 0, 1);
      if (is_full_array($quali_info)) {
        $broker_info[$key]['auth_quali_status'] = $quali_info['status'];
      } else {
        $broker_info[$key]['auth_quali_status'] = 0;
      }
    }
    //var_dump($broker_info);exit;
    $data_view['agency'] = $broker_info;


    //页面标题
    $data_view['page_title'] = '员工资料管理';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');

    $this->view("agency/personnel/broker", $data_view);
  }

  //添加
  public function add()
  {
    $agency_id = $this->input->post('agency_id');
    $agency_id = ($agency_id == 0) ? $this->user_arr['agency_id'] : $agency_id;

    $this->permission_company_role_model->set_select_fields(array('id'));
    $roles = $this->permission_company_role_model->get_by_company_id_package_id($this->user_arr['company_id'], 2);
    $role_id = $roles['id'];

    $phone = $this->input->post('phone');
    $password = $this->input->post('password');
    $code = $this->input->post('code');

    $this->load->model('broker_sms_model');
    $this->broker_sms_model->type = 'register';
    $code_id = $this->broker_sms_model->get_by_phone_validcode($phone, $code);//判断验证码是否输入正确
    if (!$code_id) {
      echo '{"status":"failed","msg":"验证码输入不正确"}';
      exit;
    }

    $city_id = $this->user_arr['city_id'];

    $insert_id = $this->broker_model->add_user($city_id, $phone, $password);//插入公表返回id
    $this->broker_info_model->init_broker($insert_id, $phone, $agency_id, $role_id);

    $this->broker_sms_model->validcode_set_esta($code_id);//把验证过后的验证设为已验证状态
    echo '{"status":"success","msg":"添加经纪人成功"}';
  }

  //修改密码
  public function modify_password()
  {
    $this_user = $this->user_arr;
    $broker_id = $this_user['broker_id'];
    $old_password = $this->input->post('old_password');
    $new_password = $this->input->post('new_password');
    $equal_password = $this->input->post('equal_password');
    $modify_data = $this->broker_model->modify_password($broker_id, $old_password, $new_password, $equal_password);
      echo json_encode(array("result" => $modify_data));
  }


  //修改
  public function modify()
  {
    $data_view = array();
    $broker_id = $this->input->post('broker_id');

    $data_view['broker_id'] = $broker_id;
    $purpose = $this->input->post('purpose');
    if ($purpose == 'get_info') {
      $broker_info1 = $this->broker_model->get_by_id($broker_id);//获取共表数据
      $data_view['phone'] = $broker_info1['phone'];
      $data_view['status'] = $broker_info1['status'];
      $this->broker_info_model->set_select_fields(array());
      $broker_info2 = $this->broker_info_model->get_by_broker_id($broker_id);//获取城市表数据
      $data_view['truename'] = $broker_info2['truename'];
      $data_view['idno'] = $broker_info2['idno'];
      $data_view['birthday'] = $broker_info2['birthday'];
      $data_view['qq'] = $broker_info2['qq'];
      /*$data_view['ident_auth'] = $broker_info2['ident_auth'];
      $data_view['quali_auth'] = $broker_info2['quali_auth'];*/
      $data_view['agency_id'] = $broker_info2['agency_id'];
      $data_view['role_id'] = $broker_info2['role_id'];
      if ($broker_info2['role_id']) {
        $role_info = $this->permission_company_role_model->get_by_id($data_view['role_id']);
        if (is_full_array($role_info)) {
          $data_view['role_name'] = $role_info['name'];
        } else {
          $data_view['role_name'] = '';
        }
      } else {
        $data_view['role_name'] = '';
      }


      //获取身份认证信息
      /*if($data_view['ident_auth'] == 1){
          $this->load->model('ident_cert_model');
          $ident_info = $this->ident_cert_model->get_by_broker_id($broker_id);
          $data_view['headshots_photo'] = $ident_info['headshots_photo'];
          $data_view['idno_photo'] = $ident_info['idno_photo'];
      }else{*/
      $this->load->model('auth_review_model');
      $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $broker_id, 0, 1);
      if (is_full_array($ident_info) && $ident_info['status'] == 2) {
        $data_view['auth_ident_status'] = $ident_info['status'];
        $data_view['headshots_photo'] = $ident_info['photo'];
        $data_view['idno_photo'] = $ident_info['photo2'];
      } else {
        $data_view['auth_ident_status'] = '';
        $data_view['headshots_photo'] = '';
        $data_view['idno_photo'] = '';
      }
      //}
      //获取资质认证信息
      /*if($data_view['quali_auth'] == 1){
          $this->load->model('quali_cert_model');
          $quali_info = $this->quali_cert_model->get_by_broker_id($broker_id);
          $data_view['card_photo'] = $quali_info['card_photo'];
          $data_view['agency_photo'] = $quali_info['agency_photo'];
      }else{*/
      $quali_info = $this->auth_review_model->get_new("type = 2 and broker_id = " . $broker_id, 0, 1);
      if (is_full_array($quali_info) && $quali_info['status'] == 2) {
        $data_view['auth_quali_status'] = $quali_info['status'];
        $data_view['card_photo'] = $quali_info['photo'];
        $data_view['agency_photo'] = $quali_info['photo2'];
      } else {
        $data_view['auth_quali_status'] = '';
        $data_view['card_photo'] = '';
        $data_view['agency_photo'] = '';
      }
      //}

      /*
       * 获取城市表局域/街道数据
       */
      $this->load->model('district_model');
      $this->agency_model->set_select_fields(array());
      $agency_info = $this->agency_model->get_by_id($data_view['agency_id']);
      if (is_full_array($agency_info)) {
        $company_id = $agency_info['company_id'];
        if ($company_id == 0) {
          $company_id = $agency_info['id'];
        }
        //获取部门
        $data_view['agencys'] = $this->agency_model->get_children_by_company_id($company_id);
        $data_view['company_roles'] = $this->permission_company_role_model->get_by_company_id($company_id);
        $data_view['agency_name'] = $agency_info['name'];
        $data_view['dist_name'] = $this->district_model->get_distname_by_id($agency_info['dist_id']);
      } else {
        $data_view['agencys'] = '';
        $data_view['company_roles'] = '';
        $data_view['agency_name'] = '';
        $data_view['dist_name'] = '';
      }
      echo json_encode($data_view);
    } elseif ($purpose == 'base_add') {
      $truename = $this->input->post('truename');
      $birthday = $this->input->post('birthday');
      //$phone = $this->input->post('phone');
      $idno = $this->input->post('idno');
      $qq = $this->input->post('qq');
      $agency_id = $this->input->post('agency_id');
      $role = $this->input->post('role');

      /*$this->broker_model->set_select_fields(array('phone'));
      $oldphone = $this->broker_model->get_by_id($broker_id);
      if($phone != $oldphone['phone']){
          $exist_phone = $this->broker_model->is_exist_by_phone($phone);
          if($exist_phone){echo '手机号码已存在，请重输入！';exit;}
      }*/
      //$update_data = array('phone'=>$phone,'truename'=>$truename,'idno'=>$idno,'birthday'=>$birthday,'qq'=>$qq);
      //$this->broker_model->update_by_id($update_data,$broker_id);
      $update_data = array('truename' => $truename, 'idno' => $idno, 'birthday' => $birthday, 'qq' => $qq, 'agency_id' => $agency_id, 'role_id' => $role);

      $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
      echo '修改成功';

    } elseif ($purpose == 'ident_add') {
      $this->load->model('auth_review_model');
      $photo = $this->input->post('photo');
      $photo2 = $this->input->post('photo2');
      $insert_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'type' => 1, 'status' => 1, 'updatetime' => time());
      $this->auth_review_model->insert($insert_data);
      echo '身份认证申请成功，请等待审核';

    } elseif ($purpose == 'quali_add') {
      $this->broker_info_model->update_by_broker_id(array('quali_auth' => 0), $broker_id);
      $this->load->model('auth_review_model');
      $photo = $this->input->post('photo');
      $photo2 = $this->input->post('photo2');

      $auth_info = $this->auth_review_model->get_by_broker_id($broker_id);
      if (is_full_array($auth_info)) {
        $insert_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'type' => 2, 'status' => 1, 'updatetime' => time());
        $this->auth_review_model->insert($insert_data);
        echo '资质认证申请成功，请等待审核';
      } else {
        $reason = $this->input->post('reason');
        $ident_info = $this->auth_review_model->get_new("type = 2 and broker_id = " . $broker_id, 0, 1);
        $au_id = $ident_info['id'];
        $update_data = array('photo' => $photo, 'photo2' => $photo2, 'status' => 1, 'reason' => $reason, 'updatetime' => time());
        $this->auth_review_model->update_by_id($update_data, $au_id);
        echo '资质认证更换成功，请等待审核';
      }
    }
  }

  //删除
  public function delete()
  {
    $wei = 1;
    $arr_id = array();
    $broker_id = $this->input->post("broker_id");
    $brokers = $this->broker_info_model->get_by_broker_id($broker_id);
    foreach ($brokers as $k => $v) {
      if (is_array($v)) {
        $arr_id[$k] = $v['broker_id'];
      } else {
        $wei = 2;
        break;
      }
    }
    if ($wei == 2) {
      $arr_id = $brokers['broker_id'];
    }
    if (is_full_array($arr_id)) {
      $this->broker_info_model->update_by_broker_id(array('status' => 2), $arr_id);
      $this->broker_model->update_by_id(array('status' => 2), $arr_id);
      echo '{"status":"success","msg":"删除经济人成功"}';
    } else {
      $this->redirect_permission_none();
      exit;
    }
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
   * 保密信息次数验证
   *
   * @access public
   * @param  int $type
   * @param  int $row_id
   * @return void
   */
  public function check_baomi_time($type = 0, $row_id = 0)
  {
    //获取当前经济人所在公司的基本设置信息
    $company_basic_data = $this->company_basic_arr;
    //出售房源保密次数
    $sell_house_secrecy_time = intval($company_basic_data['sell_house_secrecy_time']);
    //出租房源保密次数
    $rent_house_secrecy_time = intval($company_basic_data['rent_house_secrecy_time']);
    //出售客源保密次数
    $buy_customer_secrecy_time = intval($company_basic_data['buy_customer_secrecy_time']);
    //出租客源保密次数
    $rent_customer_secrecy_time = intval($company_basic_data['rent_customer_secrecy_time']);

    //查看类型：1，出售；2，出租；3，求购；4，求租
    if (!empty($type)) {
      $type = intval($type);
    }

    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $secrecy_num = 0;

    //判断查看数据是否属于本人，是否已查看过
    //出售
    if (1 == $type) {
      $secrecy_num = $sell_house_secrecy_time;
      $this->sell_house_model->set_search_fields(array('broker_id'));
      $this->sell_house_model->set_id($row_id);
      $owner_arr = $this->sell_house_model->get_info_by_id();
    } else if (2 == $type) {
      $secrecy_num = $rent_house_secrecy_time;
      $this->rent_house_model->set_search_fields(array('broker_id'));
      $this->rent_house_model->set_id($row_id);
      $owner_arr = $this->rent_house_model->get_info_by_id();
    } else if (3 == $type) {
      $secrecy_num = $buy_customer_secrecy_time;
      $this->buy_customer_model->set_search_fields(array('broker_id'));
      $this->buy_customer_model->set_id($row_id);
      $owner_arr = $this->buy_customer_model->get_info_by_id();
    } else if (4 == $type) {
      $secrecy_num = $rent_customer_secrecy_time;
      $this->rent_customer_model->set_search_fields(array('broker_id'));
      $this->rent_customer_model->set_id($row_id);
      $owner_arr = $this->rent_customer_model->get_info_by_id();
    }

    $where_cond = array(
      'broker_id' => $broker_id,
      'view_type' => $type,
      'row_id' => $row_id
    );
    $query_result = $this->broker_view_secrecy_model->get_one_by($where_cond);

    $insert_data = array(
      'broker_id' => $broker_id,
      'row_id' => $row_id,
      'view_type' => $type,
      'view_time' => time()
    );

    //当前经纪人当天查看总次数
    //今天的凌晨时间戳
    $today_time = strtotime(date('Y-m-d'));
    //明天的凌晨时间戳
    $tomorrow_time = strtotime(date("Y-m-d", strtotime("+1 day")));
    $where_cond = 'broker_id = "' . $broker_id . '" and view_type = "' . $type . '" and view_time > "' . $today_time . '" and view_time < "' . $tomorrow_time . '"';
    $broker_data = $this->broker_view_secrecy_model->get_broker_totay_view_num($where_cond);

    $read_secrecy_num = intval($broker_data['num']);
      $success = array();
    if ($secrecy_num > 0) {
      if ($read_secrecy_num < $secrecy_num) {
        //添加记录
        $result = $this->broker_view_secrecy_model->insert($insert_data);
        $success["success"] = true;
        echo json_encode($success);
      } else {
          $success["success"] = false;
          echo json_encode($success);
      }
    } else {
      //添加记录
      $result = $this->broker_view_secrecy_model->insert($insert_data);
        $success["success"] = true;
        echo json_encode($success);
    }
  }

}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
