<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-个人资料
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_info extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //$this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $this->load->model('agency_model');
    $this->load->model('district_model');
    $this->load->model('auth_review_model');
    $this->load->model('head_review_model');
    $this->load->model('permission_agency_group_model');
    //$this->load->model('newhouse_sync_account_base_model');
    $this->load->model('api_broker_level_base_model');
    $this->load->model('house_config_model');
    $this->load->model('operate_log_model');
    $this->load->model('message_base_model');
    $this->load->model('phone_info_400_model');
  }

  public function index()
  {

    $broker = array();
    $broker['tel400'] = $this->config->item('tel400');

    $broker['user_menu'] = $this->user_menu;

    $html_array = explode('</a>',$broker['user_menu']);
    foreach ($html_array as $key => $value) {
      $html_array[$key] = trim($value);
      if (strpos($html_array[$key], 'attendance')) {
        $html_array[$key] = substr_replace($html_array[$key], ' id="attendance_app"', 2, 0);
      }
    }
    // print_r($html_array);die;
    $broker['user_menu'] = implode('</a>', $html_array);
    $attendance_app_per = $this->broker_permission_model->check('147');
    $broker['attendance_app_auth'] = $attendance_app_per;
    // print_r($attendance_app_per);die;

    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $broker['level'] = $this->api_broker_level_base_model->get_level($broker_info['level']);
    //获取基本配置资料
    $config = $this->house_config_model->get_config();
    $broker['work_time'] = $config['work_time'];
    /*
     * 获取城市表个人数据
     */
    $this->broker_info_model->set_select_fields(array('idno', 'birthday', 'company_id', 'role_id', 'qq', 'phone', 'truename', 'agency_id', 'photo', 'credit', 'master_id', 'work_time', 'weixin', 'businesses'));
    $broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);
    $broker['city_spell'] = $broker_info['city_spell'];;
    $broker['idno'] = $broker_info_city['idno'];
    $broker['birthday'] = $broker_info_city['birthday'];
    $broker['qq'] = $broker_info_city['qq'];
    $broker['phone'] = $broker_info_city['phone'];
    $broker['truename'] = $broker_info_city['truename'];
    $broker['agency_id'] = $broker_info_city['agency_id'];
    $broker['company_id'] = $broker_info_city['company_id'];
    $broker['role_id'] = $broker_info_city['role_id'];
    $broker['photo'] = $broker_info_city['photo'];
    //图片url处理换成大图
    if (!empty($broker['photo'])) {
      $photo_str = '';
      $photo_arr = changepic($broker['photo']);
      $photo_str .= $photo_arr;
      $broker['photo'] = $photo_str;
    }
    $broker['credit'] = $broker_info_city['credit'];
    //查询客户经理
    $broker['master_id'] = $broker_info_city['master_id'];
    if ($broker_info_city['master_id'] > 0) {
      $master_info = $this->broker_info_model->get_master_info($broker_info_city['master_id']);
      $broker['master_name'] = $master_info['truename'];
      $broker['master_telno'] = $master_info['telno'];
    } else {
      $broker['tel400'] = $this->config->item('tel400');
    }
    $role_id = $broker_info_city['role_id'];
    $company_id = $broker_info_city['company_id'];

    /*$broker['ident_auth'] = $broker_info_city['ident_auth'];
    $broker['quali_auth'] = $broker_info_city['quali_auth'];*/
    /*
        * 是否有权限重新认证
        * 0 没有
        */
    /*$permission_role_id = $this->permission_company_group_model->get_one_by("company_id = ".$company_id." and "."system_group_id = 1");
        if($permission_role_id['id'] < $role_id){
            $broker_info_auth = 0;
        }*/
    $broker_info_auth = $this->broker_info_model->get_all_by("company_id = " . $company_id);
    $broker['broker_info_auth'] = '';
    //print_r($broker_info_auth);
    $group = $this->permission_agency_group_model->get_one_by("id = " . $role_id);
    if (is_full_array($group)) {
      $role = $this->permission_agency_group_model->get_all_by(" and p.company_id = " . $company_id . " and p.system_group_id < " . $group['system_group_id']);
    }
    if (!empty($role)) {
      $broker_info_auth = 0;
    }
    /*print_r($role);
        foreach($broker_info_auth as $vo){
            if($vo['role_id'] == $role['role_id']){
                $broker_info_auth = 0;
            }
        }*/
    $broker['broker_info_auth'] = $broker_info_auth;
    //echo $auth;
    //$system_arr = $this->permission_company_group_model->get_one_by("id = ".$role_id);
    //$company_arr = $this->permission_company_group_model->get_by_company_id($company_id);
    //$system_group_id = $system_arr['system_group_id'];

    //$group_id=$system_group_id['system_group_id'];
    //echo "<script>alert('".$group_id."')</script>";exit;
    /*
     * 当是认证用户的时候 获取photo地址
     */
    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);

    if (is_full_array($ident_info)) {
      $broker['ident_auth_status'] = $ident_info['status'];
      $broker['headshots_photo'] = $ident_info['photo'];
      $broker['idno_photo'] = $ident_info['photo2'];
      $broker['card_photo'] = $ident_info['photo3'];
      $broker['idno'] = $ident_info['idcard'];
      $broker['ident_remark'] = $ident_info['remark'];

      //图片url处理换成大图
      if (!empty($broker['headshots_photo'])) {
        $photo_str = '';
        $photo_arr = changepic($broker['headshots_photo']);
        $photo_str .= $photo_arr;
        $broker['headshots_photo'] = $photo_str;
      }
      if (!empty($broker['idno_photo'])) {
        $photo_str = '';
        $photo_arr = changepic($broker['idno_photo']);
        $photo_str .= $photo_arr;
        $broker['idno_photo'] = $photo_str;
      }
      if (!empty($broker['card_photo'])) {
        $photo_str = '';
        $photo_arr = changepic($broker['card_photo']);
        $photo_str .= $photo_arr;
        $broker['card_photo'] = $photo_str;
      }
    } else {
      $broker['ident_auth_status'] = 0;
      $broker['headshots_photo'] = '';
      $broker['idno_photo'] = '';
      $broker['card_photo'] = '';
      $broker['idno'] = '';
      $broker['ident_remark'] = '';
    }
    //获取头像审核信息
    $head_info = $this->head_review_model->get_new("broker_id = " . $broker_id, 0, 1);
    if (is_full_array($head_info)) {
      $broker['head_auth_status'] = $head_info['status'];
      $broker['head_info_pic'] = $head_info['headpic'];
    } else {
      $broker['head_auth_status'] = 0;
      $broker['head_info_pic'] = '';
    }

    /*
     * 获取城市表门店/局域/街道数据
     */
    $agency_id = $broker['agency_id'];//门店id
    $agency_info = array();
    if ($agency_id > 0) {
      $this->agency_model->set_select_fields(array('dist_id', 'street_id', 'name', 'company_id'));//指定获取区域和街道
      $agency_info = $this->agency_model->get_by_id($agency_id);
    }

    if (is_full_array($agency_info)) {
      $dist_name = $this->district_model->get_distname_by_id($agency_info['dist_id']);
      $street_name = $this->district_model->get_streetname_by_id($agency_info['street_id']);
    } else {
      $dist_name = '';
      $street_name = '';
      $agency_info['name'] = '';
      $agency_info['company_id'] = 0;
    }
    $broker['dist_name'] = $dist_name;
    $broker['street_name'] = $street_name;
    $broker['agency_name'] = $agency_info['name'];
    $broker['company_id'] = $agency_info['company_id'];
    $company_id = $broker['company_id'];//公司id
    //当存在有公司获取公司名称否则公司名称从门店名称
    if ($company_id != 0) {
      $this->agency_model->set_select_fields(array('name'));
      $broker['company'] = $this->agency_model->get_by_id($company_id);
    } else {
      $broker['company'] = array('name' => '');
    }
    /*
     * 信用模块
     */
    $this->load->model('api_broker_sincere_model');
    //获取经纪人的信用值和等级
    $broker['trust_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    //获取好评率/总数/好评/中评/差评
    $broker['count_info'] = $this->api_broker_sincere_model->get_trust_appraise_count($broker_id);
    //好评率比平均值高
    $good_avg_rate = $this->api_broker_sincere_model->good_avg_rate($broker_id);
    $broker['diff_good_rate'] = $good_avg_rate['good_rate_avg_high'];

    //合作成功率平均值
    $this->load->model('cooperate_suc_ratio_base_model');
    $broker['avg_cop_suc_ratio'] = $this->cooperate_suc_ratio_base_model->get_avg_succ_ratio();
    $broker['cop_succ_ratio_info'] = $this->cooperate_suc_ratio_base_model->get_broker_cop_succ_ratio_info($broker_id);

    //获取经纪人动态评分基本统计信息
    $broker['appraise_avg_info'] = $this->api_broker_sincere_model->get_appraise_and_avg($broker_id);

    $this->load->model('cooperate_model');
    //统计收到别人发起的合作数量
    $broker['received'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_a = ' . $broker_id);
    //统计向别人发起的合作数量
    $broker['initiate'] = $this->cooperate_model->get_cooperate_num_by_cond('brokerid_b = ' . $broker_id);
    //统计收到别人发起的合作中我接受的数量
    $broker['accept'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 2 and brokerid_a = ' . $broker_id);
    //统计向别人发起的合作中被对方接受的数量
    $broker['accepted'] = $this->cooperate_model->get_cooperate_num_by_cond('esta = 2 and brokerid_b = ' . $broker_id);

    // 暂时禁用二维码 by alphabeta 20170405
    //微信二维码图片
    //$broker['wximg'] = get_qrcode(MLS_URL.'/' . $broker_info['city_spell'] . '/broker_info/broker_details/' . $broker_id, $broker_info['city_spell']);
    //$broker['wximg2'] = get_qrcode(MLS_URL.'/' . $broker_info['city_spell'] . '/broker_info/agency_house/' . $broker_info['agency_id'], $broker_info['city_spell']);

    //页面标题
    $broker['page_title'] = '店面部门管理';

    //页面标题
    $broker['ip'] = get_ip();

    //需要加载的css
    $broker['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css'
      . ',mls/css/v1.0/personal_new.css');
    //需要加载的JS
    $broker['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $broker['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('uncenter/my_info/my_info', $broker);
  }

  /*
   * 修改个人信息
   */
  public function base_modify()
  {
    $broker_id = $this->user_arr['broker_id'];

    $submit_flag = $this->input->post('submit_flag');
    //echo $submit_flag;exit;
    if ($submit_flag == 'modify') {
      //获取参数
      $truename = $this->input->post('truename');
      $birthday = $this->input->post('birthday');
      /*$phone = $this->input->post('phone');*/
      $idno = $this->input->post('idno');
      $qq = $this->input->post('qq');
      $photo = $this->input->post('photo');
      if (!$truename) {
        echo '{"status":"failed","msg":"员工姓名不能为空"}';
        exit;
      }
      /*$this->broker_model->set_select_fields(array('phone'));
      $oldphone = $this->broker_model->get_by_id($broker_id);
      if($phone != $oldphone['phone']){
          $exist_phone = $this->broker_model->is_exist_by_phone($phone);
          if($exist_phone){echo '手机号码已存在，请重输入！';exit;}
      }*/
      $update_data = array('truename' => $truename, 'idno' => $idno, 'birthday' => $birthday, 'qq' => $qq, 'photo' => $photo);
      //$this->broker_model->update_by_id($update_data,$broker_id);
      $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
      echo '{"status":"success","msg":"修改成功"}';
    } else {

      /*
       * 获取公表个人数据
      */
      $this->broker_model->set_select_fields(array('phone'));
      $broker_info = $this->broker_model->get_by_id($broker_id);
      $phone = $broker_info['phone'];

      /*
       * 获取城市表个人数据
       */
      $this->broker_info_model->set_select_fields(array('truename', 'idno', 'birthday', 'qq', 'photo', 'agency_id'));
      $broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);
      $truename = $broker_info_city['truename'];
      $idno = $broker_info_city['idno'];
      $birthday = $broker_info_city['birthday'];
      $qq = $broker_info_city['qq'];
      $photo = $broker_info_city['photo'];
      $agency_id = $broker_info_city['agency_id'];//门店id
      /*
       * 获取城市表门店/局域/街道数据
      */
      $this->agency_model->set_select_fields(array('dist_id', 'street_id', 'name', 'company_id'));//指定获取区域和街道
      $dist_street = $this->agency_model->get_by_id($agency_id);
      $dist_name = $this->district_model->get_distname_by_id($dist_street['dist_id']);
      $street_name = $this->district_model->get_streetname_by_id($dist_street['street_id']);

      $agency_name = $dist_street['name'];
      $company_id = $dist_street['company_id'];//公司id

      //当存在有公司获取公司名称否则公司名称从门店名称
      if ($company_id != 0) {

        $this->agency_model->set_select_fields(array('name'));
        $company = $this->agency_model->get_by_id($company_id);
      }

      $data_info = "
            <div class='rz_base_info clearfix'>
                <div class='item'>
                    <span class='text'>员工姓名：</span>
                    <input id='truename' class='input_text' type='text' value='" . $truename . "'>
                </div>
                <div class='item'>
                    <span class='text'>出生日期：</span>
                    <input id='birthday' class='input_text' type='text' value='" . $birthday . "'>
                </div>
                <div class='item'>
                    <span class='text'>服务区属：</span>
                    <input class='input_text w50 input_readonly' type='text' readonly value='" . $dist_name . "'>
                    <span class='left' style='padding:0 8px; line-height:26px'>—</span>
                    <input class='input_text w50 input_readonly' type='text' readonly value='" . $street_name . "'>
                </div>
                <div class='item'>
                    <span class='text'>身份证号：</span>
                    <input id='idno' class='input_text w130' type='text' value='" . $idno . "'>
                </div>
                <div class='item'>
                    <span class='text'>手机号码：</span>
                    <input id='phone' class='input_text input_readonly' type='text' readonly value='" . $phone . "'>
                </div>
                <div class='item'>
                    <span class='text'>业务QQ：</span>
                    <input id='qq' class='input_text' type='text' value='" . $qq . "'>
                </div>
                <div class='item'>
                    <span class='text'>公司门店：</span>
                    <input id='agency_name' class='input_text w150 input_readonly' type='text' readonly value='" . $agency_name . "'>
                </div>
                <div class='item'>
                    <span class='text'>所属公司：</span>
                    <input id='company_name' class='input_text w130 input_readonly' type='text' readonly value='" . $company['name'] . "'>
                </div>
            </div>
            <div class='btn_wrap clearfix'>
			    <a class='btn' onclick='base_save()'><span class='btn_inner'>保存资料</span></a>
	        </div>";
      echo $data_info;
    }

  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    //echo "<script>alert('".$filename."')</script>";exit;
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    if ($filename == 'idnofile') {
      $fileurl = $this->pic_model->common_upload(1, '仅供' . SOFTWARE_NAME . '认证使用');
    } else {
      $fileurl = $this->pic_model->common_upload();
    }
    //图片url处理换成大图
    if (!empty($fileurl)) {
      $photo_str = '';
      $photo_arr = changepic($fileurl);
      $photo_str .= $photo_arr;
      $fileurl = MLS_FILE_SERVER_URL . $photo_str;
    }

    $div_id = $this->input->post('div_id');
    echo "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";

  }

  /*
   * 认证申请
   */
  public function ident_auth()
  {
    $broker_id = $this->user_arr['broker_id'];
    $broker_name = $this->user_arr['truename'];
    $photo = $this->input->post('photo');
    $photo2 = $this->input->post('photo2');
    $photo3 = $this->input->post('photo3');

    $photo = changepic($photo);
    $photo2 = changepic($photo2);
    $photo3 = changepic($photo3);

    $idno = $this->input->post('idno');//echo "1";die();
    $insert_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'photo3' => $photo3, 'type' => 1, 'status' => 1, 'updatetime' => time(), 'idcard' => $idno);
    $this->auth_review_model->insert($insert_data);
    //$this->newhouse_sync_account_base_model->updatestatus($broker_id);
    $where_str = 'flag = 1 and status = 2 and city_id = ' . $this->user_arr['city_id'];
    $phone_info = $this->phone_info_400_model->get_phone($where_str);
    if (is_full_array($phone_info)) {
      $param['phone'] = $phone_info[0]['num_group'];
    }
    $this->message_base_model->add_message("8-53", $broker_id, $broker_name, '/my_info/index', $param);
    echo '身份认证申请成功，请等待审核';

    //$broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);
    //$company_id = $broker_info_city['company_id'];
    //$role_id = $this->permission_company_group_model->get_one_by("company_id = ".$company_id." and "."system_group_id = 9");
    //$this->broker_info_model->update_by_broker_id(array('group_id'=>1,'role_id'=>$role_id['id']),$broker_id);
    $this->broker_info_model->update_by_broker_id(array('group_id' => 1), $broker_id);

    //操作日志
    $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 40;
    $add_log_param['text'] = $broker_info['phone'] . ' ' . $broker_info['truename'] . ' 提交审核';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();
    $this->operate_log_model->add_operate_log($add_log_param);
  }

  /*
    *认证更换
    */
  public function quali_modify_auth()
  {
    $broker_id = $this->user_arr['broker_id'];
    $broker_name = $this->user_arr['truename'];
    $company_id = $this->user_arr['company_id'];
    //echo($company_id);die;
    //$role_id = $this->permission_company_group_model->get_one_by("company_id = ".$company_id." and "."system_group_id = 9");

    //$this->broker_info_model->update_by_broker_id(array('group_id'=>1,'role_id'=>$role_id['id']),$broker_id);
    $this->broker_info_model->update_by_broker_id(array('group_id' => 1), $broker_id);
    //$boss_info = $this->auth_review_model->get_boss_broker($company_id);
    $photo = $this->input->post('photo');
    $photo2 = $this->input->post('photo2');
    $photo3 = $this->input->post('photo3');
    $idno = $this->input->post('idno');//echo "1";die();

    $photo = changepic($photo);
    $photo2 = changepic($photo2);
    $photo3 = changepic($photo3);

    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
    $au_id = $ident_info['id'];

    $update_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'photo3' => $photo3, 'type' => 1, 'status' => 1, 'updatetime' => time(), 'idcard' => $idno);
    $result = $this->auth_review_model->update_by_id($update_data, $au_id);
    //获取400电话
    $where_str = 'flag = 1 and status = 2 and city_id = ' . $this->user_arr['city_id'];
    $phone_info = $this->phone_info_400_model->get_phone($where_str);
    if (is_full_array($phone_info)) {
      $param['phone'] = $phone_info[0]['num_group'];
    }
    //$this->newhouse_sync_account_base_model->updatestatus($broker_id);
    $this->message_base_model->add_message("8-53", $broker_id, $broker_name, '/my_info/index', $param);

    echo '身份认证更换成功，请等待审核';

    //操作日志
    $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 40;
    $add_log_param['text'] = $broker_info['phone'] . ' ' . $broker_info['truename'] . ' 重新审核';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();
    $this->operate_log_model->add_operate_log($add_log_param);
  }

  /*
     *资质照片更换发送消息
     */
  public function seed_message()
  {
    $broker_id = $this->user_arr['broker_id'];
    $broker_name = $this->user_arr['truename'];
    $company_id = $this->user_arr['company_id'];
    //echo($company_id);die;
    $boss_info = $this->auth_review_model->get_boss_broker($company_id);

    /*$broker_info_auth = $this->broker_info_model->get_all_by("company_id = ".$company_id);
        foreach($broker_info_auth as $vo){
            if($vo['role_id'] < $role_id){

            }
        }*/
    //发送消息
    if (is_full_array($boss_info)) {
      $params['name'] = $broker_name;
      $this->message_base_model->add_message('8-47', $boss_info['id'], $boss_info['truename'], '', $params);
    }

    echo 'success';
  }


  /*
     * 头像更换审核
     */
  public function head_modify_auth()
  {
    $broker_id = $this->user_arr['broker_id'];
    $headpic = $this->input->post('headpic');

    $headpic = changepic($headpic);
    //echo "1";die();

    //$head_info = $this->head_review_model->get_new("broker_id = ".$broker_id,0,1);
    //$head_id = $head_info['id'];
    //if($head_info['status'] == 2){
    $insert_data = array('broker_id' => $broker_id, 'headpic' => $headpic, 'status' => 1, 'updatetime' => time());
    $this->head_review_model->insert($insert_data);
    //}else{
    //	$update_data = array('broker_id'=>$broker_id,'headpic'=>$headpic,'status'=>1,'updatetime'=>time());
    //	$this->head_review_model->update_by_id($update_data,$head_id);
    //}
    echo '头像已提交成功，请等待审核';

    //操作日志
    $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
    $add_log_param = array();
    $add_log_param['company_id'] = $this->user_arr['company_id'];
    $add_log_param['agency_id'] = $this->user_arr['agency_id'];
    $add_log_param['broker_id'] = $this->user_arr['broker_id'];
    $add_log_param['broker_name'] = $this->user_arr['truename'];
    $add_log_param['type'] = 40;
    $add_log_param['text'] = $broker_info['phone'] . ' ' . $broker_info['truename'] . ' 重新审核';
    $add_log_param['from_system'] = 1;
    $add_log_param['from_ip'] = get_ip();
    $add_log_param['mac_ip'] = '127.0.0.1';
    $add_log_param['from_host_name'] = '127.0.0.1';
    $add_log_param['hardware_num'] = '测试硬件序列号';
    $add_log_param['time'] = time();
    $this->operate_log_model->add_operate_log($add_log_param);
  }

  public function modify_phone()
  {
    $phone = $this->input->get('phone');
    $validcode = $this->input->get('validcode');
    if (!trim($phone) || !trim($validcode)) {
      echo json_encode(array('status' => 0, 'msg' => '参数不合法'));
      return false;
    }
    //验证码是否正确，并且有效
    //引入用户SMS操作类，并初始化验证码的有效时长和类型
    $broker_sms = $this->broker_model->get_broker_sms('modify_phone');
    $validcode_id = $broker_sms->get_by_phone_validcode($phone, $validcode);
    if (!$validcode_id) //没有相关的验证码
    {
      echo json_encode(array('status' => 0, 'msg' => '验证码错误，请重新获取'));
      return false;
    }
      //同步金品app修改手机号
      $this->load->library('Curl');
      //生成加密签名
      $this->load->library('DES3');
      $time = time();
      $sign = $this->des3->encrypt($this->user_arr['broker_id'] . $time);
      $url = JINPIN_URL . '/broker/houseUpdatePhone';
      $params = [
          'brokerId' => $this->user_arr['broker_id'],
          'phoneNum' => $phone,
          'time' => $time,
          'sign' => $sign
      ];
      $output = $this->curl->httpRequstPost($url, http_build_query($params));
      $output = json_decode($output, true);
      $text = '金品生活同步修改手机号失败';
      if ($output['success']) {
          //添加推送日志
          $text = '金品生活同步修改手机号成功';
      } else {
          echo json_encode(array('status' => 0, 'msg' => $text));
          return false;
      }

    $this->load->model('broker_model');
    $this->load->model('broker_info_model');
    $update_data = array('phone' => $phone);
    $modify_slave_phone = $this->broker_info_model->update_by_broker_id($update_data, $this->user_arr['broker_id']);
    $modify_master_phone = $this->broker_model->update_by_id($update_data, $this->user_arr['broker_id']);
    if ($modify_slave_phone && $modify_master_phone) {
      //操作日志
      $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
      $old_agency_info = $this->agency_model->get_by_id(intval($this->user_arr['agency_id']));
      $old_agency_name = '';
      if (is_full_array($old_agency_info)) {
        $old_agency_name = $old_agency_info['name'];
      }
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 40;
        $add_log_param['text'] = $old_agency_name . ' ' . $broker_info['truename'] . ' 更换手机，并且' . $text;
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
      echo json_encode(array('status' => 1));
      return false;
    } else {
      echo json_encode(array('status' => 0, 'msg' => '更换失败，请重新再试'));
      return false;
    }
  }

  //修改个人资料
  public function modify_person()
  {
    $type = $this->input->get('type');
    if ($type == 'search') {
      $broker_info = $this->user_arr;
      $person['work_time'] = $broker_info['work_time'];
      $person['weixin'] = $broker_info['weixin'];
      $person['businesses'] = $broker_info['businesses'];
      echo json_encode($person);
      return false;
    }
    $work_time = $this->input->get('work_time');
    $weixin = $this->input->get('weixin');
    $businesses = $this->input->get('businesses');
    if ($work_time == $this->user_arr['work_time'] && $weixin == $this->user_arr['weixin'] && $businesses == $this->user_arr['businesses']) {
      echo json_encode(array('status' => 0, 'msg' => '资料未修改，请更改后再提交！'));
      return false;
    }
    if (mb_strlen($weixin) > 20) {
      echo json_encode(array('status' => 0, 'msg' => '微信号最多填写20个字！'));
      return false;
    }
    if (mb_strlen($businesses) > 50) {
      echo json_encode(array('status' => 0, 'msg' => '擅长领域最多填写50个字！'));
      return false;
    }
    $this->load->model('broker_info_model');
    $update_data = array('work_time' => $work_time, 'weixin' => $weixin, 'businesses' => $businesses);
    $modify_slave_phone = $this->broker_info_model->update_by_broker_id($update_data, $this->user_arr['broker_id']);
    if ($modify_slave_phone) {
      echo json_encode(array('status' => 1));
      return false;
    } else {
      echo json_encode(array('status' => 0, 'msg' => '更新失败，请重新提交！'));
      return false;
    }
  }

  public function pic_deal()
  {
    $broker_info = $this->user_arr;
    $agency_id = 0;
    if (is_full_array($broker_info)) {
      $agency_id = $broker_info['agency_id'];
    }
    $agency_scode_img = $this->input->get('agency_scode_img', TRUE);

    $this->load->model('pic_model');
    $fileurl = $this->pic_model->agency_print($agency_scode_img, $agency_id);
    //下载图片
    $pic_file_name = 'scode_' . $agency_id . '.jpg';
    if ('success' == $fileurl) {
      $file_path = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'source' . DIRECTORY_SEPARATOR . 'mls' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'v1.0' . DIRECTORY_SEPARATOR . 'agency_wei' . DIRECTORY_SEPARATOR . $pic_file_name;

      $filename = realpath($file_path); //文件名
      Header("Content-type:  application/octet-stream ");
      Header("Accept-Ranges:  bytes ");
      Header("Accept-Length: " . filesize($filename));
      header("Content-Disposition:  attachment;  filename= men_dian_wei_dian.jpg");
      readfile($filename);
    }
    exit;
  }

  public function broker_wei_down()
  {
    $broker_info = $this->user_arr;
    // 暂时禁用二维码 by alphabeta 20170405
    //$broker_wximg = get_qrcode(MLS_URL.'/' . $broker_info['city_spell'] . '/broker_info/broker_details/' . $broker_info['broker_id'], $broker_info['city_spell']);
    $last_str = '';
    $last_arr = array();
    if (!empty($broker_wximg) && is_string($last_str)) {
      $last_str = strstr($broker_wximg, 'source');
      if (!empty($last_str) && is_string($last_str)) {
        $last_arr = explode('/', $last_str);
      }
    }
    $file_path = '';
    if (is_full_array($last_arr)) {
      $file_path = dirname(dirname(dirname(dirname(__FILE__))));
      foreach ($last_arr as $key => $value) {
        $file_path .= DIRECTORY_SEPARATOR . $value;
      }
    }

    $filename = realpath($file_path); //文件名
    Header("Content-type:  application/octet-stream ");
    Header("Accept-Ranges:  bytes ");
    Header("Accept-Length: " . filesize($filename));
    header("Content-Disposition:  attachment;  filename= ge_ren_wei_dian.jpg");
    readfile($filename);
    exit;
  }

}
/* End of file my_info.php */
/* Location: ./applications/mls/controllers/my_info.php */
