<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-事件提醒
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_remind extends MY_Controller
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


  public function __construct()
  {
    parent::__construct();
    $this->load->model('remind_model');
    $this->load->model('event_receiver_model');
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
   * 事件提醒列表
   *
   * @access  public
   * @param  void
   */
  public function index()
  {
    //根据当前经纪人id（接受者id）获得对应的事件id
    $receiver_id = $this->user_arr['broker_id'];
    $event_id_data = $this->event_receiver_model->get_event_by_receiver($receiver_id);
    $event_id_arr = array();
    foreach ($event_id_data as $k => $v) {
      $event_id_arr[] = $v['event_id'];
    }
    $where_in = array();
    if (!empty($event_id_arr)) {
      $where_in = array('id', $event_id_arr);
    }
    $data = array();
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    $remind_list2 = array();
    $where_cond = "id != 0 ";
    $data['like_code'] = array();
    $where_param = $this->input->post(NULL, TRUE);
    if (!empty($where_param['min_create_time'])) {
      $where_cond .= "AND create_time >= " . strtotime($where_param['min_create_time']) . " ";
    }
    if (!empty($where_param['max_create_time'])) {
      $where_cond .= "AND create_time <= " . strtotime($where_param['max_create_time']) . " ";
    }
    //所在公司的分店信息
    $company_id = intval($this->user_arr['company_id']);
    $this->load->model('api_broker_model');
    $company_id = $this->user_arr['company_id'];
    $data['agencys'] = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //分页开始
    $data['remind_num'] = $this->remind_model->get_remind_num($where_cond, $where_in, $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['remind_num'] ? ceil($data['remind_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_total_count = $data['remind_num'];
    $this->_init_pagination($page);

    $remind_list = $this->remind_model->get_remind($where_cond, $where_in, $data['like_code'], $this->_offset, $this->_limit);
    foreach ($remind_list as $k => $v) {
      $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
      $v['notice_time'] = date('Y-m-d H:i:s', $v['notice_time']);
      $remind_list2[] = $v;
    }
    $data['remind_list'] = $remind_list2;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post/ajax
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');

    //页面标题
    $data['page_title'] = '事件提醒';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    $this->view('uncenter/my_remind/my_remind', $data);
  }

  /**
   * 添加事件提醒
   *
   * @access  public
   * @param  void
   */
  public function add_remind()
  {
    $post_param = $this->input->post(NULL, TRUE);//获得提交参数
    //数据处理
    if (is_array($post_param) && !empty($post_param)) {
      $post_param['agency_id'] = $this->user_arr['agency_id'];//发布人门店id
      $post_param['broker_id'] = $this->user_arr['broker_id'];//发布人id
      $post_param['broker_name'] = $this->user_arr['truename'];//发布人姓名
      $post_param['notice_time'] = intval(strtotime($post_param['notice_time']));//提醒时间
      $post_param['create_time'] = time();//发布时间
    }
    //事件表添加数据
    $add_result = $this->remind_model->add_remind($post_param);
    if (is_int($add_result) && !empty($add_result)) {
      //事件接收者表数据
      $receiver_data = array(
        'receiver_id' => $this->user_arr['broker_id'],
        'event_id' => $add_result,
      );
      $add_result2 = $this->event_receiver_model->add_receiver($receiver_data);
    }
    if (is_int($add_result2) && !empty($add_result2)) {
      echo 'add_success';
    } else {
      echo 'add_failed';
    }
  }

  /**
   * 事件处理
   *
   * @access  public
   * @param  void
   */
  public function deal_remind()
  {
    $remind_id = $this->input->get('id');
    $deal_result = $this->remind_model->set_status($remind_id, array('status' => 1));
    if ($deal_result === 1) {
      echo 'deal_success';
    } else {
      echo 'deal_failed';
    }
  }

  /**
   * 事件忽略
   *
   * @access  public
   * @param  void
   */
  public function ignore_remind()
  {
    $remind_id = $this->input->get('id');
    $deal_result = $this->remind_model->set_status($remind_id, array('status' => 2));
    if ($deal_result === 1) {
      echo 'ignore_success';
    } else {
      echo 'ignore_failed';
    }
  }

  /**
   * 事件删除
   *
   * @access  public
   * @param  void
   */
  public function del_remind()
  {
    $remind_id = $this->input->get('id');
    $del_result = $this->remind_model->del_remind_byid($remind_id);
    if ($del_result === 1) {
      echo 'del_success';
    } else {
      echo 'del_failed';
    }
  }


  /**
   * 事件批量动作（处理、忽略、删除）
   *
   * @access  public
   * @param  void
   */
  public function batch_action()
  {
    $param_data = $this->input->get(null, '');
    $action_method = $param_data['method'];
    $remind_ids = $param_data['remind_ids'];
    $action_result = '';
    //批量处理
    if ('deal' == $action_method) {
      foreach ($remind_ids as $k => $v) {
        $deal_result = $this->remind_model->set_status($v, array('status' => 1));
      }
      if ($deal_result === 1) {
        $action_result = 'success';
      }
    } else if ('ignore' == $action_method) {
      foreach ($remind_ids as $k => $v) {
        $ignore_result = $this->remind_model->set_status($v, array('status' => 2));
      }
      if ($ignore_result === 1) {
        $action_result = 'success';
      }
    } else if ('del' == $action_method) {
      foreach ($remind_ids as $k => $v) {
        $del_result = $this->remind_model->del_remind_byid($v);
      }
      if ($del_result === 1) {
        $action_result = 'success';
      }
    }
    echo $action_result;
  }

}
