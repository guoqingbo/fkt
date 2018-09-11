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
    $this->load->model('credit_record_model');
    $this->load->model('head_review_model');
    $this->load->model('api_broker_level_model');
    $this->load->model('house_config_model');
    $this->load->model('operate_log_model');
  }

  public function index()
  {
    $broker = array();
    $broker['user_menu'] = $this->user_menu;

    $broker_info = $this->user_arr;
    $broker_id = $broker_info['broker_id'];
    $broker['weidian_url'] = MLS_MOBILE_URL . '/' . $broker_info['city_spell'] . "/broker_info/broker_details/" . $broker_id;
    /*
     * 获取城市表个人数据
     */
    $this->broker_info_model->set_select_fields(array('idno', 'birthday', 'qq', 'phone', 'truename', 'agency_id', 'photo', 'master_id', 'level', 'work_time', 'weixin', 'businesses'));
    $broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);

    $broker['idno'] = $broker_info_city['idno'];
    $broker['birthday'] = $broker_info_city['birthday'];
    $broker['qq'] = $broker_info_city['qq'];
    $broker['phone'] = $broker_info_city['phone'];
    $broker['truename'] = $broker_info_city['truename'];
    $broker['agency_id'] = $broker_info_city['agency_id'];
    $broker['photo'] = $broker_info_city['photo'];
    $broker['level'] = $broker_info_city['level'];
    $broker['work_time'] = $broker_info_city['work_time'];
    //获取基本配置资料
    $config = $this->house_config_model->get_config();
    $broker['work_time_string'] = $config['work_time'][$broker_info_city['work_time']];
    if ($broker_info_city['weixin']) {
      $broker['weixin'] = $broker_info_city['weixin'];
    }
    if ($broker_info_city['businesses']) {
      $broker['businesses'] = $broker_info_city['businesses'];
    }

    //查询客户经理
    if ($broker_info_city['master_id'] > 0) {
      $master_info = $this->broker_info_model->get_master_info($broker_info_city['master_id']);
      $broker['master'] = array(
        'master_id' => $broker_info_city['master_id'],
        'master_name' => $master_info['truename'],
        'master_telno' => $master_info['telno'],
      );
    } else {
      $broker['tel400'] = $this->config->item('tel400');
    }

    $this->broker_info_model->set_select_fields(array('credit'));
    $broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);
    $broker['credit'] = $broker_info_city['credit'];
    /*$broker['ident_auth'] = $broker_info_city['ident_auth'];
    $broker['quali_auth'] = $broker_info_city['quali_auth'];*/

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
    } else {
      $broker['ident_auth_status'] = 0;
      $broker['headshots_photo'] = '';
      $broker['idno_photo'] = '';
      $broker['card_photo'] = '';
      $broker['idno'] = '';
      $broker['ident_remark'] = '';
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
    $broker['dist_id'] = $agency_info['dist_id'];
    $broker['street_id'] = $agency_info['street_id'];
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
    //获取经纪人的信用值
    $broker['trust_level'] = $this->api_broker_sincere_model->get_trust_level_by_broker_id($broker_id);
    //获取经纪人的成长值
    $broker['level'] = $this->api_broker_level_model->get_level_app($broker['level']);
    //获取成长值获取方式规则
    $way = $this->api_broker_level_model->get_way_app();
    $broker['level_way'] = $way;
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

    $this->result("1", "查询个人信息成功", $broker);
    return;
  }


  /*
     * 获取身份资质认证信息
     */
  public function ident_info()
  {
    $data = array();
    $broker_id = $this->user_arr['broker_id'];
    $ident_info = $this->auth_review_model->get_new("type = 1 and broker_id = " . $broker_id, 0, 1);
    //$ident_info['photo'] = str_replace('thumb/','',$ident_info['photo']);
    //$ident_info['photo2'] = str_replace('thumb/','',$ident_info['photo2']);
    $ident_info['photo'] = changepic($ident_info['photo']);
    $ident_info['photo2'] = changepic($ident_info['photo2']);
    $ident_info['photo3'] = changepic($ident_info['photo3']);
    if (is_full_array($ident_info)) {
      //获取头像审核信息
      $head_info = $this->head_review_model->get_new("broker_id = " . $broker_id, 0, 1);
      //print_r($head_info);
      $head_auth = array();
      if (is_full_array($head_info)) {
        $head_auth['head_auth_status'] = $head_info['status'];
        $head_auth['head_info_pic'] = $head_info['headpic'];
      } else {
        $head_auth['head_auth_status'] = 0;
        $head_auth['head_info_pic'] = '';
      }
      $data['ident_info'] = $ident_info;
      $data['head_auth'] = $head_auth;
      $this->result(1, '身份资质认证信息获取成功', $data);
      return;
    } else {
      $this->result(0, '无身份认证信息，请申请');
      return;
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
    $fileurl = $this->pic_model->common_upload();
    echo
      //echo "<script>alert('".$fileurl."')</script>";exit;
      /*if($filename == 'photofile_add'){
          $broker_id = $this->user_arr['broker_id'];
          $update_data = array('photo'=>$fileurl);
          $this->broker_info_model->update_by_broker_id($update_data,$broker_id);
          echo "<script>window.parent.changePhoto('".$fileurl."')</script>";
      }elseif($filename == 'photofile_modify'){
          $broker_id = $this->user_arr['broker_id'];
          $update_data = array('photo'=>$fileurl);
          $this->broker_info_model->update_by_broker_id($update_data,$broker_id);
          $div_id= $this->input->post('div_id');
          echo "<script>window.parent.changePic('".$fileurl."','".$div_id."')</script>";
      }else{*/
    $div_id = $this->input->post('div_id');
    $upload_photo = "<script>window.parent.changePic('" . $fileurl . "','" . $div_id . "')</script>";
    $this->result("1", "上传图片信息", $upload_photo);
    return;

    //}

  }

  /*
   * 身份认证申请
   */
  public function ident_auth()
  {
    $broker_id = $this->user_arr['broker_id'];
    $photo = $this->input->post('photo');
    $photo2 = $this->input->post('photo2');
    $photo3 = $this->input->post('photo3');
    $idno = $this->input->post('idno');//echo "1";die();
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);
    if (empty($photo)) {
      $this->result(2, '请上传标准照片！');
      return;
    }
    if (empty($photo2)) {
      $this->result(3, '请上传身份证照片！');
      return;
    }
    if (empty($photo3)) {
      $this->result(4, '请上传个人名片照片！');
      return;
    }
    if (empty($idno)) {
      $this->result(5, '请输入身份证号！');
      return;
    }
    $insert_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'photo3' => $photo3, 'type' => 1, 'status' => 1, 'updatetime' => time(), 'idcard' => $idno);
    $num = $this->auth_review_model->insert($insert_data);
    if ($num >= 0) {
      //操作日志
      $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 40;
      $add_log_param['text'] = $broker_info['phone'] . ' ' . $broker_info['truename'] . ' 提交审核';
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

      $this->result(1, '身份资质认证申请成功，请等待审核');
      return;
    } else {
      $this->result(0, '身份资质认证申请失败，请重新提交');
      return;
    }

  }

  /*
    * 身份认证更换
    */
  public function quali_modify_auth()
  {
    $broker_id = $this->user_arr['broker_id'];
    $photo = $this->input->post('photo');
    $photo2 = $this->input->post('photo2');
    $photo3 = $this->input->post('photo3');
    $idno = $this->input->post('idno');//echo "1";die();
    $devicetype = $this->input->post('api_key', TRUE);
    $deviceid = $this->input->post('deviceid', TRUE);

    $ident_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);
    $au_id = $ident_info['id'];

    $update_data = array('broker_id' => $broker_id, 'photo' => $photo, 'photo2' => $photo2, 'photo3' => $photo3, 'type' => 1, 'status' => 1, 'updatetime' => time(), 'idcard' => $idno);
    $num = $this->auth_review_model->update_by_id($update_data, $au_id);
    if ($num >= 0) {
      //操作日志
      $broker_info = $this->broker_info_model->get_by_broker_id($this->user_arr['broker_id']);
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 40;
      $add_log_param['text'] = $broker_info['phone'] . ' ' . $broker_info['truename'] . ' 重新审核';
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
      $this->result(1, '身份认证更换成功，请等待审核');
      return;
    } else {
      $this->result(0, '身份认证更换失败，请重新认证');
      return;
    }
  }

  /*
     * 我的积分
     */
  public function integration()
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //我的剩余总积分
    $all_score = $broker_info['credit'];
    //共消耗积分数
    $where_score = 'broker_id = ' . $broker_id . ' AND score < 0';
    $lose_score = $this->credit_record_model->sum_score_by($where_score);
    //共获取积分数
    $where_score = 'broker_id = ' . $broker_id . ' AND score > 0';
    $get_score = $this->credit_record_model->sum_score_by($where_score);

    $post_param = $this->input->post(NULL, TRUE);
    $method = isset($post_param['method']) ? $post_param['method'] : '';
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $pagesize = isset($post_param['pagesize']) ? intval($post_param['pagesize']) : 0;
    $this->_init_pagination($page, $pagesize);
    $where = '';
    //全部明细、积分获取、积分消耗
    if ('all' == $method) {
      $where = 'broker_id = ' . $broker_id;
    } else if ('get' == $method) {
      $where = 'broker_id = ' . $broker_id . ' AND score > 0';
    } else if ('lose' == $method) {
      $where = 'broker_id = ' . $broker_id . ' AND score < 0';
    } else {
      $where = 'broker_id = ' . $broker_id;
    }
    $select = array('id', 'create_time', 'action_id', 'score');

    $this->credit_record_model->set_select_fields($select);
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    if (is_full_array($credit_info)) {
      $this->load->model('credit_way_model');
      foreach ($credit_info as $key => $value) {
        $alias_info = $this->credit_way_model->get_by_id($value['action_id']);
        $credit_info[$key]['create_time'] = date('Y-m-d H:i:s', $value['create_time']);
        $credit_info[$key]['alias_name'] = $alias_info['name'];
        unset($credit_info[$key]['action_id']);
      }
    }
    $data = array(
      'all_score' => $all_score,
      'lose_score' => $lose_score,
      'get_score' => $get_score,
      'details' => $credit_info
    );
    $this->result(1, '获积分信息成功', $data);
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

  /*
   * 账户权限
   */
  public function competence()
  {
    $broker_info = $this->user_arr;
    $broker_id = intval($broker_info['broker_id']);
    //有效日期
    $expiretime = $broker_info['expiretime'];
    $expire_date = date('Y-m-d', $expiretime);
    //剩余天数
    //$expire_time = strtotime($expire_date);
    //$today_time = strtotime(date('Y-m-d',time()));
    $surplus_days = ceil(($expiretime - time()) / 60 / 60 / 24);
    $data = array(
      'competence_name' => '精英版',
      'expire_date' => $expire_date,
      'surplus_days' => $surplus_days,
      'share_message_num' => '不限',
    );
    $data['collection_message_num'] = $broker_info['group_id'] == 2 ? '不限' : '40';
    $this->result(1, '获得账户权限成功', $data);
  }

  /*
     * 头像更换审核
     */
  public function head_modify_auth()
  {
    $this->load->model('pic_model');
    $input_name = 'headpic';
    $this->pic_model->set_filename($input_name);
    $headpic = $this->pic_model->upload("common");
    $broker_id = $this->user_arr['broker_id'];
    $insert_data = array('broker_id' => $broker_id, 'headpic' => $headpic, 'status' => 1, 'updatetime' => time());
    $this->head_review_model->insert($insert_data);
    $this->result(1, '修改成功', array('headpic' => $headpic));
  }

  /*
     * 修改个人信息
     */
  /*public function base_modify()
  {
      $broker_id = $this->user_arr['broker_id'];

      $submit_flag = $this->input->post('submit_flag');
      //echo $submit_flag;exit;
      if($submit_flag == 'modify'){
          //获取参数
          $truename = $this->input->post('truename');
          $birthday = $this->input->post('birthday');
          //$phone = $this->input->post('phone');
          $idno = $this->input->post('idno');
          $qq = $this->input->post('qq');
          $photo = $this->input->post('photo');
          if(!$truename){
              $this->result(2,'请输入员工姓名！');
              return;
          }

          $update_data = array('truename'=>$truename,'idno'=>$idno,'birthday'=>$birthday,'qq'=>$qq,'photo'=>$photo);
          //$this->broker_model->update_by_id($update_data,$broker_id);
          $num = $this->broker_info_model->update_by_broker_id($update_data,$broker_id);
          if($num >= 0){
              $this->result(1,'修改成功');
              return;
          }else{
              $this->result(0,'修改失败');
              return;
          }
      }else{


          //获取公表个人数据

          $this->broker_model->set_select_fields(array('phone'));
          $broker_info = $this->broker_model->get_by_id($broker_id);
          $phone = $broker_info['phone'];


          //获取城市表个人数据

          $this->broker_info_model->set_select_fields(array('truename','idno','birthday','qq','photo','agency_id'));
          $broker_info_city = $this->broker_info_model->get_by_broker_id($broker_id);
          $truename = $broker_info_city['truename'];
          $idno = $broker_info_city['idno'];
          $birthday = $broker_info_city['birthday'];
          $qq = $broker_info_city['qq'];
          $photo = $broker_info_city['photo'];
          $agency_id = $broker_info_city['agency_id'];//门店id

          //获取城市表门店/局域/街道数据

          $this->agency_model->set_select_fields(array('dist_id','street_id','name','company_id'));//指定获取区域和街道
          $dist_street = $this->agency_model->get_by_id($agency_id);
          $dist_name = $this->district_model->get_distname_by_id($dist_street['dist_id']);
          $street_name = $this->district_model->get_streetname_by_id($dist_street['street_id']);

          $agency_name = $dist_street['name'];
          $company_id = $dist_street['company_id'];//公司id

          //当存在有公司获取公司名称否则公司名称从门店名称
          if($company_id != 0){

              $this->agency_model->set_select_fields(array('name'));
              $company = $this->agency_model->get_by_id($company_id);
          }

          $data_info = "
          <div class='rz_base_info clearfix'>
              <div class='item'>
                  <span class='text'>员工姓名：</span>
                  <input id='truename' class='input_text' type='text' value='".$truename."'>
              </div>
              <div class='item'>
                  <span class='text'>出生日期：</span>
                  <input id='birthday' class='input_text' type='text' value='".$birthday."'>
              </div>
              <div class='item'>
                  <span class='text'>服务区属：</span>
                  <input class='input_text w50 input_readonly' type='text' readonly value='".$dist_name."'>
                  <span class='left' style='padding:0 8px; line-height:26px'>—</span>
                  <input class='input_text w50 input_readonly' type='text' readonly value='".$street_name."'>
              </div>
              <div class='item'>
                  <span class='text'>身份证号：</span>
                  <input id='idno' class='input_text w130' type='text' value='".$idno."'>
              </div>
              <div class='item'>
                  <span class='text'>手机号码：</span>
                  <input id='phone' class='input_text input_readonly' type='text' readonly value='".$phone."'>
              </div>
              <div class='item'>
                  <span class='text'>业务QQ：</span>
                  <input id='qq' class='input_text' type='text' value='".$qq."'>
              </div>
              <div class='item'>
                  <span class='text'>公司门店：</span>
                  <input id='agency_name' class='input_text w150 input_readonly' type='text' readonly value='".$agency_name."'>
              </div>
              <div class='item'>
                  <span class='text'>所属公司：</span>
                  <input id='company_name' class='input_text w130 input_readonly' type='text' readonly value='".$company['name']."'>
              </div>
          </div>
          <div class='btn_wrap clearfix'>
              <a class='btn' onclick='base_save()'><span class='btn_inner'>保存资料</span></a>
          </div>";
          $this->result("1","查询个人信息",$data_info);
          return;

      }

  }*/

  //修改个人资料
  public function modify_person()
  {
    $work_time = $this->input->post('work_time');
    $weixin = $this->input->post('weixin');
    $businesses = $this->input->post('businesses');
    if ($work_time == $this->user_arr['work_time'] && $weixin == $this->user_arr['weixin'] && $businesses == $this->user_arr['businesses']) {
      $this->result("2", "资料未修改，请更改后再提交！");
      return;
    }
    if (mb_strlen($weixin) > 20) {
      $this->result("2", "微信号最多填写20个字！");
      return;
    }
    if (mb_strlen($businesses) > 50) {
      $this->result("2", "擅长领域最多填写50个字！");
      return;
    }
    $update_data = array('work_time' => $work_time, 'weixin' => $weixin, 'businesses' => $businesses);
    $modify_slave_phone = $this->broker_info_model->update_by_broker_id($update_data, $this->user_arr['broker_id']);
    if ($modify_slave_phone) {
      $this->result("1", "更改成功！");
      return;
    } else {
      $this->result("0", "更新失败，请重新提交！");
      return;
    }
  }
}
/* End of file my_info.php */
/* Location: ./applications/mls/controllers/my_info.php */
