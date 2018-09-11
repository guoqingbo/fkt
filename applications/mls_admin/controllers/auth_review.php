<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 身份、资质审核
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Auth_review extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('auth_review_model');
    $this->load->model('broker_info_model');
    $this->load->helper('user_helper');
    //$this->load->model('newhouse_sync_account_base_model');
    $this->load->model('phone_info_400_model');
  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');//引入分页类
    $where = '';
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status) {
      $search_status = 99;
    }
    $pg = $this->input->post('pg');
    if ($search_where && $search_value) {

      //搜索查询条件值
      $this->broker_info_model->set_select_fields(array('broker_id'));
      $brokers = $this->broker_info_model->get_all_by($search_where . ' like ' . "'%$search_value%'");

      $broker_ids = $this->broker_info_model->format_brokers($brokers);

      if (is_full_array($broker_ids)) {
        $broker_ids = implode(',', $broker_ids);
        //var_dump($broker_ids);exit;
        $where = "broker_id in($broker_ids)";
      } else {
        $where = "broker_id in('')";
      }
    }

    if ($search_status != 99) {
      if ($where == '') {
        $where = "status = " . $search_status;
      } else {
        $where .= " and status = " . $search_status;
      }
    }

    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'search_status' => $search_status
    );

    //分页开始
    $data_view['count'] = $this->auth_review_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //申请列表
    $auth_info = $this->auth_review_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);


    foreach ($auth_info as $key => $value) {
      $this->broker_info_model->set_select_fields(array('truename', 'phone'));
      $broker_info = $this->broker_info_model->get_by_broker_id($value['broker_id']);
      //var_dump($broker_info);exit;
      $auth_info[$key]['truename'] = $broker_info['truename'];
      $auth_info[$key]['phone'] = $broker_info['phone'];
    }

    $data_view['auth_info'] = $auth_info;

    $data_view['title'] = '认证资料审核';
    $data_view['conf_where'] = 'index';
    $this->load->view('auth_review/index', $data_view);
  }

  /**
   * 处理图片
   * 2015.10.8
   * cc
   */
  function changepic($pic = '')
  {
    if ($pic != '') {
      if (strstr($pic, '_120x90')) {
        $picurl = str_replace('_120x90', '', $pic);
      } elseif (strstr($pic, '/thumb/')) {
        $picurl = str_replace('/thumb/', '/', $pic);
      } else {
        $picurl = $pic;
      }
    } else {
      $picurl = $pic;
    }
    return $picurl;
  }

  /**
   * 修改认证资料申请信息
   * @param int $auth_id 认证资料序号
   */
  public function modify($auth_id)
  {
    $data_view = array();
    //配置信息
    $data_view['where_config'] = $this->broker_info_model->get_where_config();
    //echo  $data_view['where_config'];die;
    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    $data_view['auth_id'] = $auth_id;
    $auth_review_info = $this->auth_review_model->get_by_id($auth_id);//获取信息
    $type = $auth_review_info['type'];
    $broker_id = $auth_review_info['broker_id'];
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
    $data_view['broker_info'] = $broker_info;
    if ($broker_info['company_id'] && $broker_info['agency_id']) {
      $this->load->model('agency_model');
      $agency_info = $this->agency_model->get_by_id($broker_info['agency_id']);
      $company_children = $this->agency_model->get_children_by_company_id($agency_info['company_id']);
      $broker_info['company_id'] = $agency_info['company_id'];
      $company_info = $this->agency_model->get_by_id($broker_info['company_id']);
      $broker_info['company_name'] = $company_info['name'];
      $broker_info['agencys'] = $company_children;
      $data_view['broker_info'] = $broker_info;
    }

    //如果原图存在
    $initialpic = str_replace('thumb', 'initial', $auth_review_info['photo']);
    if (preg_match("/404 Not Found/", curl_get_contents($initialpic))) {
      //图片url处理换成大图
      //print_r($auth_review_info['photo']);
      if (!empty($auth_review_info['photo'])) {
        $photo_str = '';
        $photo_arr = changepic($auth_review_info['photo']);
        $photo_str .= $photo_arr;
        $auth_review_info['photo'] = $photo_str;
      }
      //print_r($auth_review_info['photo']);
      if (!empty($auth_review_info['photo2'])) {
        $photo_str = '';
        $photo_arr = changepic($auth_review_info['photo2']);
        $photo_str .= $photo_arr;
        $auth_review_info['photo2'] = $photo_str;
      }
      if (!empty($auth_review_info['photo3'])) {
        $photo_str = '';
        $photo_arr = changepic($auth_review_info['photo3']);
        $photo_str .= $photo_arr;
        $auth_review_info['photo3'] = $photo_str;
      }
    }

    $data_view['auth_review_info'] = $auth_review_info;
    $data_view['title'] = '身份资质照片审核';
    $submit_flag = $this->input->post('submit_flag');
    //echo $broker_id;exit;
    if ($submit_flag == 'modify') {
      $ispass = 1;//同步到房管家的状态（默认为1）
      //获取参数
      $status = $this->input->post('status');
      $remark = $this->input->post('remark');
      $agency_id = $this->input->post('agency_id');
      $company_id = $this->input->post('company_id');
      $package_id = $this->input->post('package_id');
      $old_status = $this->input->post('old_status');

      if ($old_status == $status && $old_status == 2) {
        echo '修改成功！';
        exit;
      }

      if ('2' == $status && (empty($company_id) || empty($agency_id))) {
        //echo '修改失败，请选择公司门店！';
        //exit;
        $company_id = 0;
        $agency_id = 0;
      }
      //根据公司id获取权限身份列表
      /*$this->load->model('permission_company_group_model');
            $permission_company_group = $this->permission_company_group_model->get_by_company_id($company_id);
            foreach($permission_company_group as $vo){
                if($vo['system_group_id']==9){
                    $role_id = $vo['id'];
                }
            }*/
      //根据身份组赋予role_id
      //更换门店的时候，变成对应门店的角色权限id
      if (($agency_id != $broker_info['agency_id'] && $package_id == 2) || $package_id == 1) {
        $this->load->model('permission_agency_group_model');
        $this->load->model('permission_system_group_model');
        $per_where_cond = array('agency_id' => $agency_id);
        if ('1' == $package_id) {
          $per_where_cond['system_group_id'] = 1;
        } else {
          $per_where_cond['system_group_id'] = 8;
        }
        $role_info = $this->permission_agency_group_model->get_one_by($per_where_cond);
        //print_r($role_info);
        $role_id = $role_info['id'] > 0 ? $role_info['id'] : 1;
        //根据角色，获得角色level
        $system_role_data = $this->permission_system_group_model->get_by_id($per_where_cond['system_group_id']);
        if (is_full_array($system_role_data)) {
          $level = intval($system_role_data['level']) > 0 ? intval($system_role_data['level']) : 10;
        } else {
          $level = 10;
        }

        //查询公司消息的条件
        $time = time();
        $cond_where = "company_id = {$company_id} and expiretime >= {$time} ";
        //获取员工role_id列表
        $this->broker_info_model->set_select_fields(array('role_id'));
        $role_ids = $this->broker_info_model->get_all_by($cond_where, 0, 0);
        $role_id_arr = array();
        foreach ($role_ids as $vo) {
          $role_id_arr[] = $vo['role_id'];
        }
        $role_id_arr = array_unique($role_id_arr);
        //判断是否总店长(总经理)冲突
        $this->load->model('organization_base_model');
        $role_id_dz = $this->organization_base_model->get_role_id_by($company_id, 1);
        if ($broker_info['role_id'] != $role_id && $per_where_cond['system_group_id'] == 1) {
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
      } else {
        $role_id = $broker_info['role_id'];
        $level = $broker_info['role_level'];
      }
      $this->load->model('message_model');
      $broker_name = $broker_info['truename'];
      $group_id = $broker_info['group_id'];

      /*********************************需要修改！！！！！！！！！！！！！！******************************************/
      //获取400电话
      $where_str = 'flag = 1 and status = 2 and city_id = ' . $_SESSION[WEB_AUTH]['city_id'];
      $phone_info = $this->phone_info_400_model->get_phone($where_str);
      if ($status == 2) {//通过
        $ispass = 4;//同步到房管家的状态（通过修改为4）
        //发送推送消息
//        $this->load->model('push_func_model');
//        $this->push_func_model->send(1, 7, 1, 0, $broker_id);
        $new_agency_info = $this->agency_model->get_by_id($agency_id);
        //更改头像-挂靠客户经理
        $update_data = array('photo' => $auth_review_info['photo'], 'master_id' => $new_agency_info['master_id']);
        $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
        $this->message_model->add_message('8-45-1', $broker_id, $broker_name, '/my_info/');
        //增加积分
        $this->load->model('api_broker_credit_base_model');
        $this->api_broker_credit_base_model->set_broker_param(array('broker_id' => $broker_id), 1);
        $this->api_broker_credit_base_model->ident_cert();
        //增加等级分值
        $this->load->model('api_broker_level_base_model');
        $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $broker_id), 1);
        $this->api_broker_level_base_model->ident_cert();
        //无人站发送短信
        if (empty($phone_info)) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'auth_review_pass'), 'sms');
          $return = $this->sms->send($broker_info['phone']);
          $result['status'] = $return['success'] ? 1 : 0;
          $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
        }
      } elseif ($status == 3) {//拒绝
        $ispass = 3;//同步到房管家的状态（通过修改为3）
        $auth_review_info = $this->auth_review_model->get_by_broker_id($broker_id);
        $params['reason'] = $remark;
        $this->message_model->add_message('8-45-2', $broker_id, $broker_name, '/my_info/', $params);
        if (empty($phone_info)) {
          $this->load->library('Sms_codi', array('city' => 'hz', 'jid' => '2', 'template' => 'auth_review_fail'), 'sms');
          $return = $this->sms->send($broker_info['phone'], array('name' => $broker_name));
          $result['status'] = $return['success'] ? 1 : 0;
          $result['msg'] = $return['success'] ? '短信发送成功' : $return['errorMessage'];
        }
      }

      //修改经纪人所属公司、门店时，相应房客源数据跟上
      if ($broker_info['company_id'] != $company_id) {
        //出售
        $this->load->model('sell_house_model');
        $this->sell_house_model->change_company_id_by_borker_id($broker_id, $company_id);
        //出租
        $this->load->model('rent_house_model');
        $this->rent_house_model->change_company_id_by_borker_id($broker_id, $company_id);
        //求购
        $this->load->model('buy_customer_model');
        $this->buy_customer_model->update_private_customer_info_by_companyid($broker_id, $company_id);
        //求租
        $this->load->model('rent_customer_model');
        $this->rent_customer_model->update_private_customer_info_by_companyid($broker_id, $company_id);
      }
      if ($broker_info['agency_id'] != $agency_id) {
        //出售
        $this->load->model('sell_house_model');
        $this->sell_house_model->change_agency_id_by_borker_id($broker_id, $agency_id);
        //出租
        $this->load->model('rent_house_model');
        $this->rent_house_model->change_agency_id_by_borker_id($broker_id, $agency_id);
        //求购
        $this->load->model('buy_customer_model');

        $this->buy_customer_model->update_private_customer_info_by_brokerid($broker_id, $agency_id);
        //求租
        $this->load->model('rent_customer_model');
        $this->rent_customer_model->update_private_customer_info_by_brokerid($broker_id, $agency_id);
      }

      $this->auth_review_model->update_by_id(array('status' => $status, 'remark' => $remark, 'updatetime' => time()), $auth_id);
      if ($group_id != 3) {
        $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $broker_id, 0, 1);
        //$ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id,0,1);
        $ident_auth = (is_full_array($ident_info) && $ident_info['status'] == 2) ? 1 : 0;
        //资质认证信息
        //$quali_info = $this->auth_review_model->get_new("type = 2 and broker_id = " . $broker_id,0,1);
        //$quali_info = $this->auth_review_model->get_new("broker_id = " . $broker_id,0,1);
        //$quali_auth = (is_full_array($quali_info)&&$quali_info['status']==2) ? 1 : 0;
        if ($ident_auth) { //身份资质已认证
          $this->broker_info_model->update_by_broker_id(array('group_id' => 2, 'agency_id' => $agency_id, 'company_id' => $company_id, 'role_id' => $role_id, 'role_level' => $level, 'package_id' => $package_id, 'auth_time' => time()), $broker_id);
          //该经纪人的最新的头像审核通过。
          $this->load->model('head_review_model');
          $where_cond = 'broker_id = "' . $broker_id . '"';
          $head_pic_data = $this->head_review_model->get_new($where_cond);
          if (is_full_array($head_pic_data)) {
            $head_id = $head_pic_data['id'];
            $this->head_review_model->update_by_id(array('status' => 2, 'updatetime' => time()), $head_id);
          }
        } else {
          $this->broker_info_model->update_by_broker_id(array('group_id' => 1), $broker_id);
        }
      }
      echo '修改成功！';
      exit;
//      $xffxdata = array(
//        //'ag_id' => $broker_id,
//        'city' => $city = $_SESSION[WEB_AUTH]["city"],
//        'ks_id' => $agency_id,
//        'kcp_id' => $company_id,
//        'pic' => $auth_review_info['photo'],
//        'id_photo1' => $auth_review_info['photo2'],
//        'id_photo2' => $auth_review_info['photo3'],
//        'ag_status' => $ispass,
//        'update_time' => time()
//      );
      //11
      //$this->newhouse_sync_account_base_model->updateagency($xffxdata,$broker_id);
      /*
      $url = MLS_ADMIN_URL.'/fktdata/agency';
      $this->load->library('Curl');
      Curl::fktdata($url, $xffxdata);*/
//      return false;
    }
    $register_info = $this->broker_info_model->get_register_info_by_brokerid($broker_info['id']);
    $data_view['register_info'] = $register_info;
    //需要加载的JS
    $this->load->helper('common_load_source_helper');
    $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('auth_review/modify', $data_view);
  }
}

/* End of file auth_review.php */
/* Location: ./application/mls_admin/controllers/auth_review.php */
