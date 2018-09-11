<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Finance extends My_Controller
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
  private $_limit = 9;

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
    $this->phone_400_tel = array('cd' => '028', 'km' => '0871', 'hz' => '0571', 'sz' => '0512');
  }

  /**
   * 金融
   */
  public function index()
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_menu'] = $this->user_menu;
    $user_arr = $this->user_arr;
    $data['group_id'] = $user_arr['group_id'];

    $data['city'] = $user_arr['city_spell'];
    //页面标题
    $data['page_title'] = '金融按揭贷款';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');

    $this->view('finance/index', $data);
  }

  /**
   *添加申请按揭客户资料
   */
  public function apply()
  {
    $data = $buy_photo_info = $sell_photo_info = array();

    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    //读取配置表
    $borrow_config = vpost("http://api-finance.house365.com/mortgage/get_picture?city_spell=" . $city, array());
    $data['borrow_config'] = json_decode($borrow_config, true);
    $data['new_borrow_config'] = $data['borrow_config']['data'];

    //买方，卖方图片数组信息
    $post_param = $this->input->post(NULL, TRUE);
    foreach ($data['borrow_config']['data']['buy_photo_info'] as $k => $v) {
      $buy_photo_info[$k] = $this->input->post("pic_buy" . $k);
      unset($post_param["pic_buy" . $k]);
    }
    foreach ($data['borrow_config']['data']['sell_photo_info'] as $k => $v) {
      $sell_photo_info[$k] = $this->input->post("pic_sell" . $k);
      unset($post_param["pic_sell" . $k]);
    }
    $post_param['buy_photo_info'] = json_encode($buy_photo_info);
    $post_param['sell_photo_info'] = json_encode($sell_photo_info);

    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      unset($post_param['submit_flag']);
      $add_data = vpost("http://api-finance.house365.com/mortgage/add?city_spell=" . $city . "&broker_id=" . $broker_id, $post_param);

      $data['add_data'] = json_decode($add_data, true);

      if ($data['add_data']['result'] == 1) {
        echo json_encode(array('result' => 'ok', "msg" => "添加成功"));
        exit;
      } else {
        echo json_encode(array('result' => 'no', "msg" => "添加失败"));
        exit;
      }
    }

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic_finance.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/move.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_finance.js');
    $this->view("finance/apply", $data);
  }

  public function apply_pledge()
  {
    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      unset($post_param['submit_flag']);

      $result = array('result' => 'no', "msg" => "添加失败");

      if (!empty($post_param['borrower']) && !empty($post_param['phone']) && !empty($post_param['intentional_money'])) {
        $add_data = vpost("http://api-finance.house365.com/pledge/apply?city_spell=" . $city . "&broker_id=" . $broker_id, $post_param);

        $data['add_data'] = json_decode($add_data, true);

        if ($data['add_data']['result'] == 1) {
          $result = array('result' => 'ok', "msg" => "申请成功，客户会第一时间联系您");
        }
      }
      die(json_encode($result));
    }

    /*
    $this->load->model('phone_info_400_model');
    $where = array(
        'city_id' => $user_arr['city_id'],
    );
    $info = $this->phone_info_400_model->get_phone($where,0,10);
    */
    $data['tel400'] = $this->config->item('tel400');

    $data['tel'] = isset($this->phone_400_tel[$city]) ? $this->phone_400_tel[$city] : '028';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/dy_detail.css'
    );
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/move.js,' . 'mls/js/v1.0/jquery.validate.min.js');
    $this->view("finance/apply_pledge", $data);
  }

  /**
   *修改申请按揭客户资料
   */
  public function modify($id)
  {
    $data = $buy_photo_info = $sell_photo_info = array();
    //获取对应修改客户的信息
    $info = vpost("http://api-finance.house365.com/mortgage/get_info?id=" . $id, array());

    $data['info'] = json_decode($info, true);
    $data['new_info'] = $data['info']['data'];

    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    //读取配置表
    $borrow_config = vpost("http://api-finance.house365.com/mortgage/get_picture?city_spell=" . $city, array());
    $data['borrow_config'] = json_decode($borrow_config, true);
    $data['new_borrow_config'] = $data['borrow_config']['data'];

    //买方，卖方图片数组信息
    $post_param = $this->input->post(NULL, TRUE);
    foreach ($data['borrow_config']['data']['buy_photo_info'] as $k => $v) {
      $buy_photo_info[$k] = $this->input->post("pic_buy" . $k);
      unset($post_param["pic_buy" . $k]);
    }
    foreach ($data['borrow_config']['data']['sell_photo_info'] as $k => $v) {
      $sell_photo_info[$k] = $this->input->post("pic_sell" . $k);
      unset($post_param["pic_sell" . $k]);
    }
    $post_param['buy_photo_info'] = json_encode($buy_photo_info);
    $post_param['sell_photo_info'] = json_encode($sell_photo_info);

    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "modify") {

      unset($post_param['submit_flag']);
      $modify_data = vpost("http://api-finance.house365.com/mortgage/modify?id=" . $id, $post_param);

      $data['modify_data'] = json_decode($modify_data, true);

      if ($data['modify_data']['result'] == 1) {
        echo json_encode(array('result' => 'ok', "msg" => "修改成功"));
        exit;
      } else {
        echo json_encode(array('result' => 'no', "msg" => "修改失败"));
        exit;
      }
    }

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic_finance.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/move.js,' . 'mls/js/v1.0/jquery.validate.min.js,' . 'mls/js/v1.0/verification_finance.js');
    $this->view("finance/modify", $data);
  }

  private function my_customer_mortgage()
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    $page = $post_param['page'] ? $post_param['page'] : 1;

    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    //获取我的客户信息
    $my_customer = vpost("http://api-finance.house365.com/mortgage/get_list?city_spell=" . $city . "&broker_id=" . $broker_id . "&page=" . $page . "&limit=" . $this->_limit, array());
    $my_customer = json_decode($my_customer, true);
    $data['customer_list'] = $my_customer['data'];

    //当前页
    $data['page'] = $page;

    //分页处理
    $params = array(
      'total_rows' => $data['customer_list']['total'], //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '金融按揭--我的客户';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');

    $this->view('finance/my_customer', $data);
  }

  private function my_customer_pledge()
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    $page = $post_param['page'] ? $post_param['page'] : 1;

    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    //获取我的客户信息
    $my_customer = vpost("http://api-finance.house365.com/pledge/get_list?city_spell=" . $city . "&broker_id=" . $broker_id . "&page=" . $page . "&limit=" . $this->_limit, array());
    $my_customer = json_decode($my_customer, true);
    $data['customer_list'] = $my_customer['data'];

    //当前页
    $data['page'] = $page;

    //分页处理
    $params = array(
      'total_rows' => $data['customer_list']['total'], //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '金融抵押--我的客户';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');

    $this->view('finance/my_customer_pledge', $data);
  }

  private function my_customer_rental()
  {
    //模板使用数据
    $data = array();
    //树型菜单
    $data['user_menu'] = $this->user_menu;

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    $page = $post_param['page'] ? $post_param['page'] : 1;

    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    //获取我的客户信息
    $my_customer = vpost("http://api-finance.house365.com/rental/customers?city_spell=" . $city . "&broker_id=" . $broker_id . "&page=" . $page . "&limit=" . $this->_limit, array());
    $my_customer = json_decode($my_customer, true);
    $data['customer_list'] = $my_customer['data'];

    //当前页
    $data['page'] = $page;

    //分页处理
    $params = array(
      'total_rows' => $data['customer_list']['total'], //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $page, //当前页数
      'list_rows' => $this->_limit, //每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '金融抵押--我的客户';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');

    $this->view('finance/my_customer_rental', $data);
  }

  /**
   * 金融按揭---我的客户
   * @param int
   */
  public function my_customer($type = 'pledge')
  {
    if ($type == 'pledge') {
      return $this->my_customer_pledge();
    } else if ($type == 'rental') {
      return $this->my_customer_rental();
    }
    return $this->my_customer_mortgage();
  }

  private function progress_mortgage($id)
  {
    //$id = $this->input->get('id', TRUE);
    //获取该条信息下按揭流水
    $my_progress = vpost("http://api-finance.house365.com/mortgage/progress?id=" . $id, array());
    $my_progress = json_decode($my_progress, true);
    $data['my_progress'] = $my_progress['data'];
    //echo '<pre>';print_r($data['my_progress']);die;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');
    $this->view('finance/check_progress', $data);
  }

  private function progress_pledge($id)
  {
    //$id = $this->input->get('id', TRUE);
    //获取该条信息下按揭流水

    $my_progress = vpost("http://api-finance.house365.com/pledge/progress?id=" . $id, array());
    $my_progress = json_decode($my_progress, true);
    $data['my_progress'] = $my_progress['data'];
    //echo '<pre>';print_r($data['my_progress']);die;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/main.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.2.min.js');
    $this->view('finance/check_progress_pledge', $data);
  }

  /**
   *查看进度
   */
  public function progress($type = 'mortgage')
  {
    $id = $this->input->get('id', TRUE);
    if ($type == 'pledge') {
      return $this->progress_pledge($id);
    }
    return $this->progress_mortgage($id);
  }

  public function apply_rental()
  {
    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    $broker_id = $user_arr['broker_id'];
    $post_param = $this->input->post(NULL, TRUE);
    $submit_flag = $post_param['submit_flag'];
    if ($submit_flag == "add") {
      unset($post_param['submit_flag']);

      $this->load->model('broker_sms_model');
      $validcode_id = $this->broker_sms_model->get_by_phone_validcode($post_param['tenant_phone'], $post_param['validcode']);
      if (false == $validcode_id) {
        die(json_encode(array('result' => 'no', "msg" => "验证码错误")));
      }

      $this->broker_sms_model->validcode_set_esta($validcode_id);

      $result = array('result' => 'no', "msg" => "提交失败");

      /*
            $post['tenant_cart'] = $post_param['tenant_cart'];
            $return = vpost('http://api-finance.house365.com/rental/customer_check?city_spell='.$city,$post);
            $return = json_decode($return,true);
            if($return['result'] == '1'){
                */
      $post = array(
        'broker_id' => $broker_id,
        'tenant_name' => $post_param['tenant_name'],
        'tenant_phone' => $post_param['tenant_phone'],
        'tenant_cart' => $post_param['tenant_cart'],
        'tenant_bank_id' => $post_param['tenant_bank_id'],
        'tenant_bank' => $post_param['tenant_bank'],
        'tenant_price' => $post_param['tenant_price'],
        'tenant_sex' => $post_param['tenant_sex'],
      );
      $return = vpost('http://api-finance.house365.com/rental/tenant_add?city_spell=' . $city, $post);
      $return = json_decode($return, true);
      if ($return['result'] == '1') {
        $result = array('result' => 'ok', "msg" => "提交成功");
      } else {
        $result = array('result' => 'no', "msg" => $return['msg']);
      }
      /*
          }else{
              $result = array('result'=>'no',"msg"=>"该用户在申请中，请勿重复提交");
          }
          */
      die(json_encode($result));
    }
    $data['tel'] = isset($this->phone_400_tel[$city]) ? $this->phone_400_tel[$city] : '028';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/dy_detail.css'
    );
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/move.js,' . 'mls/js/v1.0/jquery.validate.min.js');
    $this->view("finance/apply_rental", $data);
  }

  /**
   *借款人配置
   */
  public function get_config()
  {
    $user_arr = $this->user_arr;
    $city = $user_arr['city_spell'];
    //读取配置表
    $borrow_config = vpost("http://api-finance.house365.com/mortgage/get_picture?city_spell=" . $city, array());
    $new_borrow_config = json_decode($borrow_config, true);
    $new_borrow_config = $new_borrow_config['data'];
    echo json_encode(array('result' => 'ok', 'data' => $new_borrow_config));
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


  //ajax验证用户名和验证码
  public function ajaxValid()
  {
    $data = array();
    $phone = $this->input->post('phone');
    $this->load->model('broker_model');

    $result = $this->broker_model->get_by_phone($phone);
    if ($result && isset($result['id']) && $result['id'] > 0) {
      //die(json_encode(array('result'=>'no',"msg"=>"该手机号为经纪人帐号，不能申请")));

    }
    $this->load->model('broker_sms_model');
    $result = $this->broker_sms_model->send_sms($phone, 'rent_finance');
    if ($result) {
      die(json_encode(array('result' => 'ok', "msg" => "发送成功")));
    }
    die(json_encode(array('result' => 'no', "msg" => "发送失败")));
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
