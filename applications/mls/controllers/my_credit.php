<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-我的成长-我的积分记录
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_credit extends MY_Controller
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
    $this->load->model('credit_record_model');
  }

  //所有记录
  public function index()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_id = $this->user_arr['broker_id'];
    $pg = $this->input->post('page');

    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id);

    $where = 'broker_id = ' . $broker_id;
    //页面标题
    $data['page_title'] = '我的积分-所有记录';

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->credit_record_model->count_by($where);
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
    $data['page_list'] = $this->page_list->show('jump');
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    if (is_full_array($credit_info)) {
      foreach ($credit_info as $key => $value) {
        $credit_info[$key]['credit_way'] = $credit_way[$value['type']];
      }
    }
    $data['credit_info'] = $credit_info;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/integral.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');
    $this->view('my_credit/index', $data);
  }

  /**
   * 使用记录
   *
   */
  public function use_credit()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_id = $this->user_arr['broker_id'];
    $pg = $this->input->post('page');

    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id);

    $where = 'broker_id = ' . $broker_id . ' and score < 0';
    //页面标题
    $data['page_title'] = '我的积分-使用记录';

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->credit_record_model->count_by($where);
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
    $data['page_list'] = $this->page_list->show('jump');
    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    if (is_full_array($credit_info)) {
      $this->load->model('credit_way_model');
      foreach ($credit_info as $key => $value) {
        $credit_info[$key]['credit_way'] = $credit_way[$value['type']];
      }
    }
    $data['credit_info'] = $credit_info;
    //echo '<pre>';print_r($data['credit_info']);die;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/integral.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');
    $this->view('my_credit/use_credit', $data);
  }

  /**
   * 使用记录
   *
   */
  public function obtain_credit()
  {
    $data = array();
    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;
    $broker_id = $this->user_arr['broker_id'];
    $pg = $this->input->post('page');

    $this->load->model('broker_info_model');
    $data['credit_total'] = $this->broker_info_model->get_credit_by_broker_id($broker_id);

    $where = 'broker_id = ' . $broker_id . ' and score > 0';
    //页面标题
    $data['page_title'] = '我的积分-获取记录';

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->credit_record_model->count_by($where);
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
    $data['page_list'] = $this->page_list->show('jump');
    //任务信息
    $credit_info = $this->credit_record_model->get_all_by($where, $this->_offset, $this->_limit, 'id', 'desc');
    //引入积分模型
    $this->load->model('api_broker_credit_model');
    $credit_way = $this->api_broker_credit_model->get_way();
    if (is_full_array($credit_info)) {
      $this->load->model('credit_way_model');
      foreach ($credit_info as $key => $value) {
        $credit_info[$key]['credit_way'] = $credit_way[$value['type']];
      }
    }
    $data['credit_info'] = $credit_info;
    //echo '<pre>';print_r($data['credit_info']);die;
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/guest_disk.css,mls/css/v1.0/myStyle.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/integral.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js');
    $this->view('my_credit/obtain_credit', $data);
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

}

/* End of file my_growing_credit.php */
/* Location: ./application/mls/controllers/my_growing_credit.php */
