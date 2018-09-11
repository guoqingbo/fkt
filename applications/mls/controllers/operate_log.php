<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统控制器
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * Operate_log Controller CLASS
 *
 * 操作日志相关功能 控制器
 *
 * @package         MLS
 * @subpackage      Controllers
 * @category        Controllers
 * @author          yuan
 */
class Operate_log extends MY_Controller
{

  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'hz';

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
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('operate_log_model');//操作日志列
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
   * 操作日志
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function index()
  {
    $data['this_user'] = $this->user_arr;
    $this_company_id = $data['this_user']['company_id'];
    $this_agency_id = $data['this_user']['agency_id'];
    $this_role_level = $data['this_user']['role_level'];
    $data['this_role_level'] = intval($this_role_level);
    //页面菜单
    $data['user_menu'] = $this->user_menu;
    $data['where_cond'] = array();
    $data['like_code'] = array();
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    //角色判断，是否店长以下
    if (isset($this_role_level) && intval($this_role_level) > 5) {
      $post_param['agency_id'] = $this_agency_id;
    }
    //门店id
    if (!empty($post_param['agency_id'])) {
      $data['where_cond']['agency_id'] = $post_param['agency_id'];
      //根据门店id，获得经纪人
      $this->load->model('api_broker_model');
      $broker_arr = $this->api_broker_model->get_brokers_agency_id(intval($post_param['agency_id']));
      $data['broker_arr'] = $broker_arr;
    }
    //经纪人id
    if (!empty($post_param['broker_id'])) {
      $data['where_cond']['broker_id'] = $post_param['broker_id'];
    }
    //操作类型
    if (!empty($post_param['type'])) {
      $data['where_cond']['type'] = $post_param['type'];
    }
    //操作内容
    if (!empty($post_param['text'])) {
      $data['like_code']['text'] = $post_param['text'];
    }
    //操作日期
    if (!empty($post_param['start_time'])) {
      $data['where_cond']['start_time'] = $post_param['start_time'];
    }
    if (!empty($post_param['end_time'])) {
      $data['where_cond']['end_time'] = $post_param['end_time'];
    }

    //门店数据
    $this->load->model('agency_model');
    //角色判断，店长以下
    if (isset($this_role_level) && intval($this_role_level) > 5) {
      $this_agency_data = $this->agency_model->get_by_id($this_agency_id);
      $data['agency_list'][0] = $this_agency_data;
    } else if (isset($this_role_level) && intval($this_role_level) < 6) {
      $data['agency_list'] = $this->agency_model->get_children_by_company_id($this->user_arr['company_id']);
    }

    //操作类型
    $data['type'] = $this->operate_log_model->get_base_conf();

    //post参数
    $post_param = $this->input->post(NULL, TRUE);

    //查询房源条件
    $cond_where = "id > 0 ";
    if (intval($this_company_id) > 0) {
      $cond_where .= " AND company_id = '" . $this_company_id . "'";
    }
    if (isset($data['where_cond']['agency_id']) && !empty($data['where_cond']['agency_id'])) {
      $cond_where .= " AND agency_id = '" . intval($data['where_cond']['agency_id']) . "' ";
    }
    if (isset($data['where_cond']['broker_id']) && !empty($data['where_cond']['broker_id'])) {
      $cond_where .= " AND broker_id = '" . intval($data['where_cond']['broker_id']) . "' ";
    }
    if (isset($data['where_cond']['type']) && !empty($data['where_cond']['type'])) {
      $cond_where .= " AND type = '" . intval($data['where_cond']['type']) . "' ";
    }
    if (isset($data['where_cond']['start_time']) && !empty($data['where_cond']['start_time'])) {
      $start_time = strtotime($data['where_cond']['start_time']);
      $cond_where .= " AND time >= '" . $start_time . "' ";
    }
    if (isset($data['where_cond']['end_time']) && !empty($data['where_cond']['end_time'])) {
      $end_time = strtotime($data['where_cond']['end_time']) + 24 * 3600;
      $cond_where .= " AND time <= '" . $end_time . "' ";
    }

    /** 分页参数 */
    $page = isset($post_param['page']) ? intval($post_param['page']) : 1;
    $this->_total_count = $this->operate_log_model->get_operate_log_num($cond_where, $data['like_code']);
    $this->_init_pagination($page);
    $data['operate_log'] = $this->operate_log_model->get_operate_log($cond_where, $data['like_code'], $this->_offset, $this->_limit);
    foreach ($data['operate_log'] as $k => $v) {
      //门店名
      $agency_data = $this->agency_model->get_by_id_one(intval($v['agency_id']));
      if (is_full_array($agency_data)) {
        $v['agency_name'] = $agency_data[0]['name'];
      }
      //类型名
      $v['type_name'] = $data['type'][$v['type']];
      //来源系统
      $v['from_system_name'] = '';
      if ('1' == $v['from_system']) {
        $v['from_system_name'] = 'PC';
      } else if ('2' == $v['from_system']) {
        $v['from_system_name'] = 'Andorid';
      } else if ('3' == $v['from_system']) {
        $v['from_system_name'] = 'ios';
      }
      $data['operate_log2'][] = $v;
    }

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
    $data['page_title'] = '操作日志';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/house_new.css,'
      . 'mls/css/v1.0/guest_disk.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/swf/swfupload.js,mls/js/v1.0/cmt_uploadpic.js');
    //底部JS
    $data['footer_js'] = load_js('common/third/My97DatePicker/WdatePicker.js,mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js,mls/js/v1.0/disk.js,mls/js/v1.0/backspace.js');

    $this->view('operate_log/index', $data);
  }

  //根据门店id获取经纪人
  public function broker_list()
  {
    $broker_info = $this->user_arr;
    $agency_id = $this->input->get('agency_id', TRUE);
    $agency_id = intval($agency_id);
    $this->load->model('api_broker_model');
    $agency_arr = $this->api_broker_model->get_brokers_agency_id($agency_id);
    echo json_encode($agency_arr);
  }


}

/* End of file operate_log.php */
/* Location: ./applications/mls/controllers/operate_log.php */
