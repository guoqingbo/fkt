<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 用户详细信息类
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Signatory_info extends MY_Controller
{
  /**
   * 每页条目数
   *
   * @access private
   * @var int
   */
  private $_limit = 5;

  /**
   * 偏移
   *
   * @access private
   * @var int
   */
  private $_offset = 0;

  public function __construct()
  {
    parent::__construct();
    $this->load->model('signatory_info_model');
    //$this->load->model('auth_review_model');
    $this->load->helper('user_helper');
    //$this->load->model('newhouse_sync_account_base_model');
   // $this->load->model('district_model');
   // $this->load->model('community_model');
  //  $this->load->model('api_broker_level_base_model');

  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');
    $pg = $this->input->post('pg');
      $data_view['title'] = '签约人管理';
    $data_view['conf_where'] = 'index';
      $nowtime = time();
    //设置查询条件
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status || $search_status == 99) {
      $search_status = 99;
      $where = 'status <> 0';
    } else if ($search_status == 1) {
      $where = 'status = ' . $search_status . ' and expiretime >= ' . $nowtime;
    } else {
      $where = 'status = ' . $search_status;
      //$where = 'status = '.$search_status.' or expiretime < '.$nowtime;
    }
    //引入经纪人基本类库
    $this->load->model('signatory_info_model');

    $search_broker_base = false;
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
      $search_broker_base = true;
    }
    //设置时间条件
    $search_time = $this->input->post('search_time');
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($search_time && $start_time && $end_time) {
      $start_time_format = strtotime($start_time);
      $end_time_format = strtotime($end_time) + 86399;
      $where .= ' and ' . "$search_time >= $start_time_format and " . "$search_time <= $end_time_format ";
      $search_broker_base = true;
    }


      //公司和部门
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
      $department_id = $this->input->post('department_id');
      if ($company_id || $department_id) {
      $this->load->model('department_model');
          $departments = $this->department_model->get_children_by_company_id($company_id);
    }
      if ($department_id) {
          $where .= ' and department_id = ' . $department_id;
          $data_view['departments'] = $departments;
    } else if ($company_id) {
          if (is_full_array($departments)) {
              $department_id = array();
              foreach ($departments as $v) {
                  $department_id[] = $v['id'];
        }
              $department_ids = implode(',', $department_id);
              $where .= ' and department_id in(' . $department_ids . ')';
      }
    }

    //记录搜索过的条件
    $data_view['where_cond'] = array(
        'search_where' => $search_where,
        'search_value' => $search_value,
        'search_time' => $search_time,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'company_id' => $company_id,
        'department_id' => $department_id,
        'search_status' => $search_status,
        'company_name' => $company_name,
    );
    //分页开始
    $data_view['count'] = 10;
    $data_view['pagesize'] = 50; //设定每一页显示的记录数
    $data_view['count'] = $this->signatory_info_model->count_by($where);
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //经纪人列表
    $signatory_info = $this->signatory_info_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);
    //echo "<pre>";
    //print_r($signatory_info);
    //die;
    //搜索配置信息
    //var_dump($signatory_info);exit;
    $data_view['where_config'] = $this->signatory_info_model->get_where_config();
    $this->load->helper('common_load_source_helper');
    //$data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
    $data_view['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    $data_view['signatory_info'] = $signatory_info;
    $this->load->view('signatory_info/index', $data_view);
  }

  public function add()
  {
    $data_view = array();
    $data_view['title'] = '用户管理-添加用户';
    $data_view['conf_where'] = 'index';
    $data_view['addResult'] = '';
    $submit_flag = $this->input->post('submit_flag');
    if ($submit_flag == 'add') {
      $this->load->library('form_validation');//表单验证
      $this->form_validation->set_rules('phone', 'Phone', 'required');
      $this->form_validation->set_rules('password', 'Password', 'required');
      if ($this->form_validation->run() === true) {
        //获取参数
        $phone = $this->input->post('phone');
        $password = ltrim($this->input->post('password'));
        if ($phone == '' || $password == '') //用户名和密码为空
        {
          echo '带 * 为必填字段';
          exit;
        } else if (strlen($phone) < 11) //手机长度小于11位
        {
          echo '<script type="text/javascript">alert("手机号码不合法");history.go(-1);</script>';
          exit;
        }
        //初始化经纪人类
        $this->load->model('signatory_model');
        //判断号码是否已经注册过
        $is_exist_phone = $this->signatory_model->is_exist_by_phone($phone);
        if ($is_exist_phone) //已注册
        {
          $data_view['mess_error'] = '此号码已经注册过，请重新请写';
        } else {
          //根据城市编号查找相应数据库标识符
          $this->load->model('city_model');
          $city_info = $this->city_model->get_city_by_spell($_SESSION[WEB_AUTH]["city"]);
          $city_id = $city_info['id'];
          $insert_id = $this->signatory_model->add_user($city_id, $phone, $password, '1');//插入数据返回id
          if ($insert_id > 0) {
            $data_view['addResult'] = $this->signatory_info_model->init_signatory_admin($insert_id, $phone);
            if (!$data_view['addResult']) {
              $data_view['mess_error'] = '注册失败';
            }
          } else {
            $data_view['mess_error'] = '注册失败';
          }
        }
      } else {
        $data_view['mess_error'] = '带 * 为必填字段';
      }
    }
    $this->load->view('signatory_info/add', $data_view);
  }

  public function batch_add()
  {
    //需要加载的JS
    $this->load->helper('common_load_source_helper');
    $data_view['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/guest_disk.css,'
      . 'mls/css/v1.0/myStyle.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'mls/js/v1.0/cooperate_common.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,'
      . 'mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/jquery.validate.min.js,'
      . 'mls/js/v1.0/house.js');
    $this->load->view('signatory_info/batch_add', $data_view);
  }


  public function modify($id, $type = 0)
  {
    $data_view = array();
    $data_view['title'] = '用户管理-修改用户';
    $data_view['conf_where'] = 'index';
    $data_view['type'] = $type;
    $submit_flag = $this->input->post('submit_flag');
    $signatory_info = $this->signatory_info_model->get_by_id($id);
    $this_role_level = $signatory_info['role_level'];

    if (!is_full_array($signatory_info)) {
      echo '无此经纪人';
      die();
    }
    //初始化经纪人类
    $this->load->model('signatory_model');
    $this->load->model('signatory_system_group_model');
    $broker = $this->signatory_model->get_by_id($signatory_info['signatory_id']);
    // 暂时禁用二维码 by alphabeta 20170405
    //$code_img_url = get_qrcode(MLS_URL . '/' . $_SESSION[WEB_AUTH]["city"] . '/signatory_info/broker_details/' . $signatory_info['signatory_id'], $_SESSION[WEB_AUTH]["city"]);
    if (!empty($code_img_url)) {
      $data_view['code_img_url'] = $code_img_url;
    } else {
      $data_view['code_img_url'] = '';
    }

    $register_id = $this->input->post('register_id');

    //print_r($broker);
    if ($submit_flag == 'modify') {
      //获取参数
      $phone = $this->input->post('phone');
      $truename = $this->input->post('truename');
      $birthday = $this->input->post('birthday');
      $qq = $this->input->post('qq');
      $idno = $this->input->post('idno');
      $idcard = $this->input->post('idcard');
      $expiretime = '2026-12-31 23:59:59';
      $company_id = $this->input->post('company_id');
      $department_id = $this->input->post('department_id');
      $package_id = $this->input->post('package_id');
      $status = $this->input->post('status');
      $email = $this->input->post('email');
      $master_id = $this->input->post('master_id');
      $is_reset_password = $this->input->post('is_reset_password');

      $group_id = $this->input->post('group_id');
      /***
       * $group_id = $this->input->post('group_id');
       * $area_id = $this->input->post('area_id');**/
      //if(!$truename || !$expiretime
        //    || !$company_id || !$department_id)
      if (!$truename) {
        echo '带 * 为必填字段';
        exit;
      }


      $broker_update_data = array(
        //'phone' => $phone,
        'expiretime' => strtotime($expiretime),
        'status' => $status
      );
      if ('1' === $is_reset_password) {
        $broker_update_data['password'] = md5('123456');
      }

      $this->signatory_model->update_by_id($broker_update_data, $signatory_info['signatory_id']);
      //获取权限
      $this->load->model('permission_agency_group_model');
      $this->load->model('permission_company_group_model');
        if ($department_id > 0) {
            $per_where_cond = array('department_id' => $department_id);
      } else {
        $per_where_cond = array('company_id' => $company_id);
      }

      if ('1' == $package_id) {
        $per_where_cond['system_group_id'] = 1;
      } else {
        $per_where_cond['system_group_id'] = 8;
      }
        if ($per_where_cond['department_id'] > 0) {
        $role_info = $this->permission_agency_group_model->get_one_by($per_where_cond);
      } else {
        $role_info = $this->permission_company_group_model->get_one_by($per_where_cond);
      }
      $role_id = $role_info['id'] > 0 ? $role_info['id'] : 1;
      //根据角色，获得角色level
      $system_role_data = $this->permission_system_group_model->get_by_id($per_where_cond['system_group_id']);
      if (is_full_array($system_role_data)) {
        $role_level = intval($system_role_data['level']);
      }

      //查询公司消息的条件
      $time = time();
      $cond_where = "company_id = {$company_id} and expiretime >= {$time} ";
      //获取员工role_id列表
      $this->signatory_info_model->set_select_fields(array('role_id'));
      $role_ids = $this->signatory_info_model->get_all_by($cond_where, 0, 0);
      $role_id_arr = array();
      foreach ($role_ids as $vo) {
        $role_id_arr[] = $vo['role_id'];
      }
      $role_id_arr = array_unique($role_id_arr);
      //判断是否总店长(总经理)冲突
      $this->load->model('organization_base_model');
      $role_id_dz = $this->organization_base_model->get_role_id_by($company_id, 1);
      if ($this_role_level != '1' && $signatory_info['role_id'] != $role_id && $per_where_cond['system_group_id'] == 1) {
        //找到公司下所有的门店下的总经理权限id，
        $where_cond = array(
          'company_id' => $company_id,
          'system_group_id' => 1
        );
        $company_system_1_data = $this->permission_agency_group_model->get_id_by_where_cond($where_cond);
        $company_system_1_id = array();
        if (is_full_array($company_system_1_data)) {
          foreach ($company_system_1_data as $k => $v) {
            $company_system_1_id[] = $v['id'];
          }
        }
        if (is_full_array($role_id_arr) && is_full_array($company_system_1_id)) {
          $check_result = array_intersect($role_id_arr, $company_system_1_id);
          if (is_full_array($check_result)) {
            echo "公司已有总店长！";
            exit();
          }
        }

      }
      $photo = $this->input->post('photopic');
      $signatory_info_update_data = array(
        //'phone' => $phone,
        'truename' => $truename,
        'birthday' => $birthday, 'qq' => $qq, 'status' => $status,
        'company_id' => $company_id, 'idno' => $idno,
          'department_id' => $department_id, 'package_id' => $package_id,
        'role_id' => $role_id, 'expiretime' => strtotime($expiretime),
        'email' => $email, 'role_level' => $role_level,
        'master_id' => $master_id
        /***'group_id' => $group_id, 'area_id' => $area_id,**/
      );

      $this->signatory_info_model->update_by_signatory_id($signatory_info_update_data, $signatory_info['signatory_id']);

      //修改经纪人所属公司、门店时，相应房客源数据跟上
      if ($signatory_info['company_id'] != $company_id) {
        //出售
        $this->load->model('sell_house_model');
        $this->sell_house_model->change_company_id_by_borker_id($signatory_info['signatory_id'], $company_id);
        //出租
        $this->load->model('rent_house_model');
        $this->rent_house_model->change_company_id_by_borker_id($signatory_info['signatory_id'], $company_id);
        //求购
        $this->load->model('buy_customer_model');
        $this->buy_customer_model->update_private_customer_info_by_companyid($signatory_info['signatory_id'], $company_id);
        //求租
        $this->load->model('rent_customer_model');
        $this->rent_customer_model->update_private_customer_info_by_companyid($signatory_info['signatory_id'], $company_id);
      }
        if ($signatory_info['department_id'] != $department_id) {
        //出售
        $this->load->model('sell_house_model');
            $this->sell_house_model->change_department_id_by_borker_id($signatory_info['signatory_id'], $department_id);
        //出租
        $this->load->model('rent_house_model');
            $this->rent_house_model->change_department_id_by_borker_id($signatory_info['signatory_id'], $department_id);
        //求购
        $this->load->model('buy_customer_model');
            $this->buy_customer_model->update_private_customer_info_by_brokerid($signatory_info['signatory_id'], $department_id);
        //求租
        $this->load->model('rent_customer_model');
            $this->rent_customer_model->update_private_customer_info_by_brokerid($signatory_info['signatory_id'], $department_id);
      }

      $register_id = $this->input->post('register_id');
      if ($register_id > 0) $this->signatory_info_model->update_register_broker_status($register_id);

      /***
       * if($package_id == '1'){
       * $this->signatory_info_model->update_agent_roleid($signatory_info['signatory_id'],$signatory_info['company_id']);
       * }**/
      echo '修改成功！';
      if ('2' == $group_id) {
        $ag_status = 4;
      } else {
        $ag_status = 1;
      }
      $this->load->model('department_model');
        $agency = $this->department_model->get_by_id($department_id);
      $dist_id = $agency['dist_id'];
      // echo $dist_id.'    '.$area_id;
      $signatory_id = $signatory_info['signatory_id'];
      $xffxdata = array(
        'ag_phone' => $phone,
        'city' => $_SESSION['esfdatacenter']['city'],
          'ks_id' => $department_id,//门店id
        'kcp_id' => $company_id,//经纪公司id
        'ag_dist' => $dist_id,//经纪人所在区属
        'ag_name' => $truename,// 经纪人姓名
        'update_time' => time()
      );
      if ('1' === $is_reset_password) {
        $xffxdata['password'] = '123456';
      }
      //11
      //$this->newhouse_sync_account_base_model->updateagency($xffxdata,$signatory_id);
      /*
      $url = MLS_ADMIN_URL.'/fktdata/agency';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/
    } else {
    //  $register_info = $this->signatory_info_model->get_register_info_by_signatoryid($signatory_info['id']);

      //根据分店找总公司id
        if ($signatory_info['department_id']) {
        //查找身份认证 - 资质认证
        $this->load->model('department_model');
            $agency_info = $this->department_model->get_by_id($signatory_info['department_id']);
        $company_children = $this->department_model->get_children_by_company_id($agency_info['company_id']);
        $signatory_info['company_id'] = $agency_info['company_id'];
        $company_info = $this->department_model->get_by_id($signatory_info['company_id']);
        $signatory_info['company_name'] = $company_info['name'];
            $signatory_info['departments'] = $company_children;
      } else {
        if ($signatory_info['company_id'] != 0) {
          //$this->load->model('department_model');
          //$company_info = $this->department_model->get_by_id($signatory_info['company_id']);
          $signatory_info['company_name'] = $company_info['name'];
          $signatory_info['company_id'] = $company_info['id'];
        //  $company_children = $this->department_model->get_children_by_company_id($company_info['id']);
            // $signatory_info['departments'] = $company_children;
        } else {
          $signatory_info['company_name'] = '';
          $signatory_info['company_id'] = 0;
        }
      }
      //身份认证信息
     // $ident_info = $this->auth_review_model->get_new("type = 1 and signatory_id = " . $signatory_info['signatory_id'], 0, 1);
      if (is_full_array($ident_info)) {
        $data_view['auth_ident_status'] = $ident_info['status'];
        $data_view['headshots_photo'] = $ident_info['photo'];
        $data_view['idno_photo'] = $ident_info['photo2'];
        $data_view['card_photo'] = $ident_info['photo3'];
        $data_view['idcard'] = $ident_info['idcard'];
      } else {
        $data_view['auth_ident_status'] = '';
        $data_view['headshots_photo'] = '';
        $data_view['idno_photo'] = '';
        $data_view['card_photo'] = '';
        $data_view['idcard'] = '';
      }
      /*//资质认证信息
      $quali_info = $this->auth_review_model->get_new("type = 2 and signatory_id = ".$signatory_info['signatory_id'],0,1);
      if(is_full_array($quali_info)){
          $data_view['auth_quali_status'] = $quali_info['status'];
          $data_view['card_photo'] = $quali_info['photo'];
          $data_view['agency_photo'] = $quali_info['photo2'];
      }else{
          $data_view['auth_quali_status'] = '';
          $data_view['card_photo'] = '';
          $data_view['agency_photo'] = '';
      }*/

      $data_view['register_id'] = $register_id;
      $data_view['register_info'] = $register_info;
      $broker_role_level = $signatory_info['role_level'];
      $data_view['broker_role_level'] = $broker_role_level;

      //获取权限组列表
      $permission_group = $this->signatory_info_model->get_purview_group();
      $data_view['permission_group'] = $permission_group;
      //配置信息
      $data_view['where_config'] = $this->signatory_info_model->get_where_config();
      //echo  $data_view['where_config'];die;
      $data_view['signatory_info'] = $signatory_info;
      //if($signatory_info['company_id']<>0){
      //    $data['permission_initialize_num'] = $this->signatory_info_model->permission_initialize_num($signatory_info['company_id']);
      //}
      $data_view['broker'] = $broker;
     // $this->load->model('user_model');
     // $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
      $data_view['masters'] = $masters;
      //需要加载的JS
      $this->load->helper('common_load_source_helper');
      $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
      //需要加载的JS
      $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
        . 'common/third/swf/swfupload.js,'
        . 'mls/js/v1.0/uploadpic.js,'
        . 'mls/js/v1.0/cooperate_common.js,'
        . 'common/third/jquery-ui-1.9.2.custom.min.js');
      $this->load->view('signatory_info/modify', $data_view);
    }

  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
    //echo "<script>alert('".$fileurl."')</script>";exit;

    $div_id = $this->input->post('div_id');
    echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
  }

  function redirect_post($url, array $data)
  {
    ?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <!--      --><?/*
      if ($encode != "GBK") {
        header('Content-Type: text/html; charset=utf-8');
        echo '<meta http-equiv="Content-Type" content="text/html; charset=$encode">';
      }
      */
      ?>
      <script type="text/javascript">
        document.write("Loading...");
        function closethisasap() {
          document.forms["redirectpost"].submit();
        }
      </script>
    </head>
    <body onload="closethisasap();">
    <form name="redirectpost" method="post" action="<?= $url ?>">
      <?
      if (!is_null($data)) {
        foreach ($data as $k => $v) {
          echo '<input type="hidden" name="' . $k . '" value="' . $v . '"> ';
        }
      }
      echo '<input type="hidden" name="login_fang" value="' . md5(MLS_NAME) . '"> ';
      ?>
    </form>
    </body>
    </html>
    <?php
    exit;
  }

  public function enter_pc($signatory_id)
  {
    //查找经纪人所属的城市
    $this->load->model('signatory_model');
    $result = $this->signatory_model->get_by_id($signatory_id);
    $post_url = MLS_ADMIN_URL . '/login/admin_signin';
    $this->redirect_post($post_url, $result);
  }


  //门店管理页
  public function exportReport()
  {
    $data_view = array();

    $nowtime = time();
    //设置查询条件
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status || $search_status == 99) {
      $search_status = 99;
      $where = 'status <> 0';
    } else if ($search_status == 1) {
      $where = 'status = ' . $search_status . ' and expiretime >= ' . $nowtime;
    } else {
      $where = 'status = ' . $search_status . ' or expiretime < ' . $nowtime;
    }
    //引入经纪人基本类库
    $this->load->model('signatory_info_model');

    $search_broker_base = false;
    if ($search_where && $search_value) {
      $where .= ' and ' . $search_where . ' like ' . "'%$search_value%'";
      $search_broker_base = true;
    }
    //设置时间条件
    $search_time = $this->input->post('search_time');
    $start_time = $this->input->post('start_time');
    $end_time = $this->input->post('end_time');

    if ($search_time && $start_time && $end_time) {
      $start_time_format = strtotime($start_time);
      $end_time_format = strtotime($end_time) + 86399;
      $where .= ' and ' . "$search_time >= $start_time_format and " . "$search_time <= $end_time_format ";
      $search_broker_base = true;
    }

    //用户组
    $group_id = $this->input->post('group_id');
    if ($group_id) {
      $where .= ' and group_id = ' . $group_id;
    }
    //套餐
    $package_id = $this->input->post('package_id');
    if ($package_id) {
      $where .= ' and package_id = ' . $package_id;
    }
    //公司和门店
    $company_id = $this->input->post('company_id');
    $company_name = $this->input->post('company_name');
      $department_id = $this->input->post('department_id');
      if ($company_id || $department_id) {
      $this->load->model('department_model');
          $departments = $this->department_model->get_children_by_company_id($company_id);
    }
      if ($department_id) {
          $where .= ' and department_id = ' . $department_id;
          $data_view['departments'] = $departments;
    } else if ($company_id) {
          if (is_full_array($departments)) {
              $department_id = array();
              foreach ($departments as $v) {
                  $department_id[] = $v['id'];
        }
              $department_ids = implode(',', $department_id);
              $where .= ' and department_id in(' . $department_ids . ')';
      }
    }
    //判断当前经纪人是否为客户经理
    $this->load->model('user_model');
    $this_user_id = intval($_SESSION[WEB_AUTH]['uid']);
    $data_view['this_user_id'] = $this_user_id;
    $data_view['this_user_name'] = $_SESSION[WEB_AUTH]['truename'];
    if ($this_user_id > 0) {
      $this_user_data = $this->user_model->getuserByid($this_user_id);
      if (is_full_array($this_user_data[0])) {
        $am_cityid = intval($this_user_data[0]['am_cityid']);
      }
    }
    if (isset($am_cityid) && $am_cityid > 0) {
      $data_view['is_user_manager'] = true;
      $where .= ' and master_id = ' . $this_user_id;
    } else {
      $data_view['is_user_manager'] = false;
      //客户经理
      $master_id = $this->input->post('master_id', true);
      if ($master_id == -1) {
        $where .= ' and master_id = 0';
      } else if ($master_id) {
        $where .= ' and master_id = ' . $master_id;
      }
    }
    //记录搜索过的条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value,
      'search_time' => $search_time, 'start_time' => $start_time,
      'end_time' => $end_time, 'group_id' => $group_id,
      'package_id' => $package_id, 'company_id' => $company_id,
        'department_id' => $department_id, 'search_status' => $search_status,
      'company_name' => $company_name,
    );
    //分页开始
    $data_view['pagesize'] = $this->signatory_info_model->count_by($where);
    //计算记录偏移量
    $data_view['offset'] = 0;
    //经纪人列表
    $signatory_info = $this->signatory_info_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);
    $data_view['where_config'] = $this->signatory_info_model->get_where_config();
    //查询这个城市的客户经理数据
    $this->load->model('user_model');
    $masters = $this->user_model->get_user_by_cityid($_SESSION[WEB_AUTH]["city_id"]);
    if (is_full_array($signatory_info)) {
      $group = $data_view['where_config']['group'];
      $package = $data_view['where_config']['package'];

      $this->load->model('broker_login_log_model');

      $permission_arr = array();
      $permission_group = $this->signatory_info_model->get_permission_group();
      foreach ($permission_group as $k => $v) {
        $permission_arr[$v['level']] = $v['name'];
      }

      foreach ($signatory_info as $key => $value) {
        $signatory_info[$key]['group_str'] = $group[$value['group_id']];
        $signatory_info[$key]['package_str'] = $permission_arr[$value['role_level']];

        if ($value['group_id'] == 1) {
          $register_info = $this->signatory_info_model->get_register_info_by_brokerid($value['id']);
          $signatory_info[$key]['company_name'] = $register_info['corpname'];
          $signatory_info[$key]['agency_name'] = $register_info['storename'];
        } else {
          //获取经纪人所属公司名称
          $company_one = $value['company_id'] > 0 ? $this->department_model->get_by_id($value['company_id']) : array();
          $signatory_info[$key]['company_name'] = is_full_array($company_one) ? $company_one['name'] : '';

          //获取经纪人所属门店名称
            $agency_one = $value['department_id'] > 0 ? $this->department_model->get_by_id($value['department_id']) : array();
          $signatory_info[$key]['agency_name'] = is_full_array($agency_one) ? $agency_one['name'] : '';
        }
        $signatory_info[$key]['status_str'] = $value['status'] == 1 ? '有效' : '无效';

        $signatory_info[$key]['regtime'] = date('Y-m-d H:i:s', $value['register_time']);

        $last_login = $this->broker_login_log_model->get_last_log($value['phone']);
        $signatory_info[$key]['last_login'] = $last_login[0]['dateline'] > 0 ? date('Y-m-d H:i:s', $last_login[0]['dateline']) : '尚未登录';

        $signatory_info[$key]['no_login_days'] = $last_login[0]['dateline'] > 0 ? floor(($nowtime - $last_login[0]['dateline']) / 86400) : '尚未登录';
        $signatory_info[$key]['infofrom'] = '';
        if ($last_login[0]['dateline'] > 0) {
          $signatory_info[$key]['infofrom'] = $last_login[0]['infofrom'] == 1 ? 'PC' : 'APP';
        }
        $signatory_info[$key]['master'] = $masters[$value['master_id']]['truename'];
        if ($value['auth_time'] > 0) {
          $signatory_info[$key]['authtime'] = date('Y-m-d H:i:s', $value['auth_time']);
        }
        $signatory_info[$key]['level'] = $this->api_broker_level_base_model->get_level($value['level']);
      }
    }

    //调用PHPExcel第三方类库
    $this->load->library('PHPExcel.php');
    $this->load->library('PHPExcel/IOFactory');
    //创建phpexcel对象
    $objPHPExcel = new PHPExcel();
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); // 用于 2007 格式
    $objWriter->setOffice2003Compatibility(true);

    //设置phpexcel文件内容
    $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
      ->setLastModifiedBy("Maarten Balliauw")
      ->setTitle("Office 2007 XLSX Test Document")
      ->setSubject("Office 2007 XLSX Test Document")
      ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
      ->setKeywords("office 2007 openxml php")
      ->setCategory("Test result file");

    //设置表格导航属性
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'ID');
    $objPHPExcel->getActiveSheet()->setCellValue('B1', '姓名');
    $objPHPExcel->getActiveSheet()->setCellValue('C1', '联系电话');
    $objPHPExcel->getActiveSheet()->setCellValue('D1', '所属公司');
    $objPHPExcel->getActiveSheet()->setCellValue('E1', '所属门店');
    $objPHPExcel->getActiveSheet()->setCellValue('F1', '角色权限组');
    $objPHPExcel->getActiveSheet()->setCellValue('G1', '认证情况');
    $objPHPExcel->getActiveSheet()->setCellValue('H1', '是否有效');
    $objPHPExcel->getActiveSheet()->setCellValue('I1', '注册时间');
    $objPHPExcel->getActiveSheet()->setCellValue('J1', '最后登录时间');
    $objPHPExcel->getActiveSheet()->setCellValue('K1', '距今未登录天数');
    $objPHPExcel->getActiveSheet()->setCellValue('L1', '登录来源');
    $objPHPExcel->getActiveSheet()->setCellValue('M1', '客户经理');
    $objPHPExcel->getActiveSheet()->setCellValue('N1', '认证时间');
    $objPHPExcel->getActiveSheet()->setCellValue('O1', '成长等级');
    $objPHPExcel->getActiveSheet()->setCellValue('P1', '积分');
    //设置表格的值
    for ($i = 2; $i <= count($signatory_info) + 1; $i++) {
      $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $signatory_info[$i - 2]['signatory_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $signatory_info[$i - 2]['truename']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $i, $signatory_info[$i - 2]['phone']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $signatory_info[$i - 2]['company_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $i, $signatory_info[$i - 2]['agency_name']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $i, $signatory_info[$i - 2]['package_str']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $i, $signatory_info[$i - 2]['group_str']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $i, $signatory_info[$i - 2]['status_str']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $i, $signatory_info[$i - 2]['regtime']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $i, $signatory_info[$i - 2]['last_login']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $i, $signatory_info[$i - 2]['no_login_days']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $i, $signatory_info[$i - 2]['infofrom']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $i, $signatory_info[$i - 2]['master']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $i, $signatory_info[$i - 2]['authtime']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $i, 'Lv ' . $signatory_info[$i - 2]['level']['level']);
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $i, $signatory_info[$i - 2]['credit']);
    }

    $fileName = strtotime(date('Y-m-d H:i:s')) . "_excel.xls";
    //$fileName = iconv("utf-8", "gb2312", $fileName);

    $objPHPExcel->getActiveSheet()->setTitle('stat_broker_nums');
    $objPHPExcel->setActiveSheetIndex(0);

    //header("Content-type: text/csv");//重要
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel;charset=utf-8');   //excel 2003
    //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');   //excel 2007
    //header('Content-Disposition: attachment;filename="求购客源.xls"');
    header("Content-Disposition: attachment;filename=\"$fileName\"");
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
  }


  //导入报表
  public function import($type)
  {
    if (!empty($_POST['sub'])) {
      $signatory_id = $this->input->post('signatory_id', true);
      $config['upload_path'] = str_replace("\\", "/", UPLOADS . DIRECTORY_SEPARATOR . 'temp');
      //目录不存在则创建目录
      if (!file_exists($config['upload_path'])) {
        $aryDirs = explode("/", substr($config['upload_path'], 0, strlen($config['upload_path'])));
        $strDir = "";
        foreach ($aryDirs as $value) {
          $strDir .= $value . "/";
          if (!@file_exists($strDir)) {
            if (!@mkdir($strDir, 0777)) {
              return "mkdirError";
            }
          }
        }
      }
      $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //上传的文件名称
        $signatory_info = $this->signatory_info_model->get_one_by(array('signatory_id' => $signatory_id));
        $this->load->model('read_model');
        if ($type == 1) {
          $result = $this->read_model->read_house('sell_model', $signatory_info, $data['upload_data'], 2, 1);
          unlink($data['upload_data']['full_path']); //删除文件
        } elseif ($type == 2) {
          $result = $this->read_model->read_house('rent_house_model', $signatory_info, $data['upload_data'], 2, 1);
          unlink($data['upload_data']['full_path']); //删除文件
        }
      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }
      echo $result;

    }
  }

  //经纪人批量导入
  public function batch_import()
  {
    if (!empty($_POST['sub'])) {
      $config['upload_path'] = './temp/';
      $config['file_name'] = date('YmdHis', time()) . rand(1000, 9999);
      $config['allowed_types'] = 'xlsx|xls';
      $config['max_size'] = "2000";
      $this->load->library('upload', $config);
      //打印成功或错误的信息
      if ($this->upload->do_upload('upfile')) {
        $data = array("upload_data" => $this->upload->data());
        //上传的文件名称
        $this->load->model('read_model');

        $result = $this->read_model->broker_read($data['upload_data'], 2);
        unlink($data['upload_data']['full_path']); //删除文件

      } else {
        $result = '<!DOCTYPE html><html><head lang="en"><meta charset="UTF-8"><title>空白页面</title><link type="text/css" rel="stylesheet" href="".MLS_SOURCE_URL."/min/?f=mls/css/v1.0/base.css"></head><body style="background:#F2F2F2;"><p class="up_m_b_date_up" style="text-align: center;"><span class="up_e">上传失败</span>，请选择文件上传</p></body></html>';
      }
      echo $result;

    }
  }

  /**
   * 确定导入
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function sure($type)
  {
    $data = array();
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    $sell_type = array();
    foreach ($data['config']['sell_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $nature = array();
    foreach ($data['config']['nature'] as $key => $k) { //性质类型
      $nature[$k] = $key;
    }
    $forward = array();
    foreach ($data['config']['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($data['config']['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    $taxes = array();
    foreach ($data['config']['taxes'] as $key => $k) { //税费类型
      $taxes[$k] = $key;
    }
    $entrust = array();
    foreach ($data['config']['entrust'] as $key => $k) { //委托类型
      $entrust[$k] = $key;
    }
    $id = $this->input->post('id', true);
    $signatory_id = $this->input->post('signatory_id', true);
    $signatory_info = $this->signatory_info_model->get_one_by(array('signatory_id' => $signatory_id));

    $data['where']['id'] = $id;
    $data['where']['signatory_id'] = $signatory_id;
    //print_r($data['where']);die;
    if ($type == 1) {
      $model = 'sell_model';
    } elseif ($type == 2) {
      $model = 'rent_house_model';
    }
    $this->load->model($model);
    $result = $this->$model->get_tmp($data['where'], '', '', '');
    $content = unserialize($result[0]['content']);
    //print_r($content);die;
    $res = array();
    $i = 0;
    $fail_num = '';
    $content_count = count($content);
    foreach ($content as $key => $k) {
      $res['signatory_id'] = $signatory_id;
      $res['broker_name'] = trim($signatory_info['truename']);
        $res['department_id'] = trim($signatory_info['department_id']); //门店ID
      $res['company_id'] = intval($signatory_info['company_id']);//获取总公司编号
      $where['cmt_name'] = $k[1];
      if ($type == 1) {
        $community_info = $this->$model->community_info($where);
      } elseif ($type == 2) {
        $community_info = $this->$model->community_info_new($where);
      }
      if (!$community_info[0]['id']) {
        //$k[20]$k[21]需要判断为空？
        $dist_arr = $this->district_model->get_district_id($k[18]);
        $street_arr = $this->district_model->get_street_id($k[19]);
        $paramArray = array(
          'cmt_name' => $k[1],//楼盘名称
          'dist_id' => trim($dist_arr['id']),//区属
          'streetid' => trim($street_arr['id']),//板块
          'address' => $k[20],//地址
          'status' => 3,
        );
        $add_result = $this->community_model->addcommunity($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $community_info = $this->$model->community_info($where);
        }
      }
      $res['block_id'] = $community_info[0]['id'];
      $res['block_name'] = $community_info[0]['cmt_name'];
      $res['district_id'] = $community_info[0]['dist_id'];
      $res['street_id'] = $community_info[0]['streetid'];
      $res['address'] = $community_info[0]['address'];
      $res['sell_type'] = $sell_type[$k[15]];  //物业类型
      $res['door'] = $k[22];
      $res['owner'] = $k[23];
      foreach (explode("/", $k[24]) as $vo => $v) {
        $res['telno' . ($vo + 1)] = $v;
      }
      $res['status'] = $status[$k[9]];
      $res['nature'] = $nature[$k[8]];
      $res['isshare'] = 0; //默认为不合作
      $house = explode("/", $k[5]);
      $res['room'] = $house[0] ? $house[0] : 0;
      $res['hall'] = $house[1] ? $house[1] : 0;
      $res['toilet'] = $house[2] ? $house[2] : 0;
      if (!in_array($res['sell_type'], array(5, 6, 7))) {
        $res['forward'] = $forward[$k[10]] ? $forward[$k[10]] : 0; //朝向类型
        $floor = explode("/", $k[11]);
        if (strpos($floor[0], "-") !== false) { //存在
          $res['floor_type'] = 2;
          $floor2 = explode("-", $floor[0]);
          $res['floor'] = $floor2[0];
          $res['subfloor'] = $floor2[1];
        } else {
          $res['floor_type'] = 1;
          $res['floor'] = $floor[0];
        }
        $res['totalfloor'] = $floor[1];
        $res['fitment'] = $fitment[$k[14]]; //装修类型
      }
      $res['buildyear'] = $k[7] ? $k[7] : 0;
      $res['buildarea'] = $k[4];
      $res['price'] = $k[2];
      if ($type == 1) {
        $res['avgprice'] = intval($res['price'] * 10000 / $res['buildarea']);
        $res['taxes'] = 3;//税费
        $res['entrust'] = $entrust[$k[30]]; //委托类型
      } elseif ($type == 2) {
        $res['rententrust'] = $entrust[$k[30]]; //委托类型
      }
      $res['keys'] = 0;

      $res['title'] = $k[43]; //标题
      $res['createtime'] = time();
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      $res['is_publish'] = 1; //默认群发房源
      //导入数据的唯一性判断
      $house_num = $this->check_house($res['block_id'], $res['door'], $res['signatory_id'], $type);
      if ($house_num == 0) {
        if ($type == 1) {
          if (($this->$model->add_data($res, 'db_city', 'sell_house')) > 0) {
            $i++;
          }
        } elseif ($type == 2) {
          if (($this->$model->add_data($res, 'db_city', 'rent_house')) > 0) {
            $i++;
          }
        }
      } else {
        $fail_num .= ($key + 2) . ',';
      }
      unset($res);
    }
    $fail_num = substr($fail_num, 0, -1);
    $fail_num .= '。';
    if ($i > 0 && $i == $content_count) {
      $res = array('signatory_id' => $signatory_id);
      $this->$model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。';
    } else if ($i > 0 && $i != $content_count) {
      $res = array('signatory_id' => $signatory_id);
      $this->$model->del($res, 'db_city', 'tmp_uploads');
      $result['status'] = 'ok';
      $result['success'] = '房源导入成功！<br>成功录入房源' . $i . '条。<br>重复录入房源' . ($content_count - $i) . '条。<br>重复录入表格行数为：' . $fail_num;
    } else {
      $result['status'] = 'error';
      $result['error'] = '房源导入失败！再试一次吧！<br>可能失败的原因：1.网络连接超时；2.重复导入房源。';
    }
    echo json_encode($result);
  }

  /**
   * 确定导入
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function broker_sure()
  {
    //初始化经纪人类
    $this->load->model('signatory_model');
    $this->load->model('signatory_info_model');

    $data = array();
    $i = 0;
    $id = $this->input->post('id', true);
    $company_id = intval($this->input->post('company_id', true));
      $department_id = intval($this->input->post('department_id', true));

    $data['where']['id'] = $id;
    $result = $this->signatory_info_model->get_tmp($data['where']);
    if (is_full_array($result)) {
      $content = unserialize($result[0]['content']);
      foreach ($content as $key => $value) {
        $phone = $value[1];
        $truename = $value[0];
        //判断号码是否已经注册过
        $is_exist_phone = $this->signatory_model->is_exist_by_phone($phone);
        if ($is_exist_phone == 0) //已注册
        {
          //根据城市编号查找相应数据库标识符
          $this->load->model('city_model');
          $city_info = $this->city_model->get_city_by_spell($_SESSION[WEB_AUTH]["city"]);
          $city_id = $city_info['id'];
          $password = '123456';
          $insert_id = $this->signatory_model->add_user($city_id, $phone, $password, '1');//插入数据返回id
          if ($insert_id > 0) {
            $role_id = 445;
            $role_level = 10;
              $add_result = $this->signatory_info_model->batch_init_broker($insert_id, $phone, $department_id, $role_id, $role_level, $truename);
            if (is_int($add_result) && $add_result > 0) {
              $i++;
            }
          }
        }
      }
    }
    $return_result['status'] = 'ok';
    $return_result['success'] = '经纪人导入成功！<br>成功录入经纪人' . $i . '条。';

    echo json_encode($return_result);
  }

  //判断房源是否重复
  public function check_house($block_id, $door, $signatory_id, $type)
  {
    //经纪人信息
    $signatory_info = $this->signatory_info_model->get_one_by(array('signatory_id' => $signatory_id));
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($signatory_info['company_id']);//获取总公司编号
    //获取全部分公司信息
    $this->load->model('api_signatory_model');
      $agency_list = $this->api_signatory_model->get_departments_by_company_id($company_id);
      $arr_department_id = array();
    foreach ($agency_list as $key => $val) {
        $arr_department_id[] = $val['department_id'];
    }
      $department_ids = implode(',', $arr_department_id);
    $cond_where = "status != 5 and block_id = '$block_id' and door = '$door' ";
      if ($department_ids) {
          $cond_where .= " and department_id in (" . $department_ids . ")";
    }
    if ($type == 1) {
      $tbl = "sell_house";
      $this->load->model('sell_house_model');
      $this->sell_house_model->set_tbl($tbl);
      $house_num = $this->sell_house_model->get_housenum_by_cond($cond_where);
    } elseif ($type == 2) {
      $tbl = "rent_house";
      $this->load->model('rent_house_model');
      $this->rent_house_model->set_tbl($tbl);
      $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);
    }
    return $house_num;
  }

  /*public function check_house($block_id,$door,$unit,$dong){
            //经纪人信息
            $signatory_info = $this->user_arr;
            //根据经济人总公司编号获取全部分店信息
            $company_id=intval($signatory_info['company_id']);//获取总公司编号
            //获取全部分公司信息
            $agency_list=$this->api_signatory_model->get_departments_by_company_id($company_id);
            $arr_department_id = array();
            foreach($agency_list as $key => $val){
                $arr_department_id[] = $val['department_id'];
            }
            $department_ids = implode(',',$arr_department_id);
            $cond_where = "status != 5 and department_id in (".$department_ids.") and block_id = $block_id and door = '$door' ";
            $tbl="rent_house";
            $this->rent_house_model->set_tbl($tbl);
            $house_num = $this->rent_house_model->get_housenum_by_cond($cond_where);

          return $house_num;
    }*/
}

/* End of file signatory_info.php */
/* Location: ./application/mls_admin/controllers/signatory_info.php */
