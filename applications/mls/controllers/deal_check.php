<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 成交查询 Class
 *
 * 成交查询
 *
 * @package      mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class Deal_check extends MY_Controller
{
  /**
   * 城市参数
   *
   * @access private
   * @var string
   */
  protected $_city = 'sh';


  /**
   * 录入经纪人id
   *
   * @access private
   * @var int
   */
  private $_boker_id = 0;

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
    $this->load->model('house_deal_model');
  }


  /**
   * 通知管理首页---通知消息列表页
   * @access public
   * @return void
   * date 2015-01-14
   * author angel_in_us
   */
  public function index()
  {
    //模板使用数据
    $data = array();

    $data['user_menu'] = $this->user_menu;
    $data['user_func_menu'] = $this->user_func_menu;

    //查询房源条件
    $cond_where = "";

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = isset($this->user_func_permission['area']) ? $this->user_func_permission['area'] : 1;
    $data['func_area'] = $func_area;

    if ($func_area == 1) {
      $broker_id = $this->user_arr['broker_id'];
      //权限条件
      $cond_where .= "`house_deal`.broker_id = {$broker_id}";
    } else if ($func_area == 2) {
      $agency_id = $this->user_arr['agency_id'];
      //权限条件
      $cond_where .= "`house_deal`.agency_id = {$agency_id}";

      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    } else if ($func_area == 3) {
      $company_id = $this->user_arr['company_id'];

      //获取所有分公司数组
      $this->load->model('api_broker_model');
      $agencys = $this->api_broker_model->get_agencys_by_company_id($company_id);
      $data['agencys'] = $agencys;

      //所有分公司id数组
      $agency_ids = array();
      foreach ($agencys as $k => $v) {
        $agency_ids[] = $v['agency_id'];
      }
      if (is_full_array($agency_ids)) {
        $agency_ids_str = implode(",", $agency_ids);
        //权限条件
        $cond_where .= "`house_deal`.agency_id in({$agency_ids_str})";
      }
    }

    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;

    //门店
    $agency_id = isset($post_param['agency_id']) ? intval($post_param['agency_id']) : 0;
    if ($agency_id) {
      //获取经纪人列表数组
      $this->load->model('api_broker_model');
      $brokers = $this->api_broker_model->get_brokers_agency_id($agency_id);
      $data['brokers'] = $brokers;
    }


    // 分页参数
    $page = isset($post_param['page']) ? intval($post_param['page']) : intval($this->_current_page);
    $this->_init_pagination($page);


    //表单提交参数组成的查询条件
    $cond_where_ext = $this->_get_cond_str($post_param);
    $cond_where .= $cond_where_ext;

    //清除条件头尾多余的“AND”和空格
    $cond_where = trim($cond_where);
    $cond_where = trim($cond_where, "AND");
    $cond_where = trim($cond_where);

    //符合条件的总行数
    $this->_total_count =
      $this->house_deal_model->count_by($cond_where);


    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->house_deal_model->get_list_by($cond_where, $this->_offset, $this->_limit);
    foreach ($list as $key => $val) {
      $list[$key]['house_info'] = unserialize($val['house']);
    }
    $data['list'] = $list;

    //分页处理
    $params = array(
      'total_rows' => $this->_total_count, //总行数
      'method' => 'post', //URL提交方式 get/html/post
      'now_page' => $this->_current_page,//当前页数
      'list_rows' => $this->_limit,//每页显示个数
    );
    //加载分页类
    $this->load->library('page_list', $params);
    //调用分页函数（不同的样式不同的函数参数）
    $data['page_list'] = $this->page_list->show('jump');


    //页面标题
    $data['page_title'] = '成交查询';
    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'mls/third/iconfont/iconfont.css,'
      . 'mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,'
      . 'mls/js/v1.0/house.js,mls/js/v1.0/backspace.js');
    //底部JS2
    $data['fuck_js'] = load_js('common/third/My97DatePicker/WdatePicker.js');

    //加载发布页面模板
    $this->view('office/deal_check', $data);
  }

  /**
   * 出售列表条件
   * 根据表单提交参数，获取查询条件
   */
  private function _get_cond_str($form_param)
  {
    $cond_where = '';
    //员工
    $broker_id = isset($form_param['broker_id']) ? intval($form_param['broker_id']) : 0;
    if ($broker_id) {
      $cond_where .= " AND `house_deal`.broker_id = '" . $broker_id . "'";
    }
    //时间条件
    date_default_timezone_set('PRC');
    if (isset($form_param['start_time']) && $form_param['start_time']) {
      $start_time = strtotime($form_param['start_time'] . " 00:00");
      $cond_where .= " AND `house_deal`.createtime >= '" . $start_time . "'";
    }

    if (isset($form_param['end_time']) && $form_param['end_time']) {
      $end_time = strtotime($form_param['end_time'] . " 23:59");
      $cond_where .= " AND `house_deal`.createtime <= '" . $end_time . "'";
    }
    if (isset($start_time) && isset($end_time) && $start_time > $end_time) {
      $this->jump(MLS_URL . '/key/', '您查询的开始时间不能大于结束时间！');
      exit;
    }
    return $cond_where;
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

  public function details($id)
  {
    $data = array();

    $data_info = $this->house_deal_model->get_by_id($id);
    $data_info['house_info'] = unserialize($data_info['house']);

    $data['data_info'] = $data_info;

    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    //获取出售信息基本配置资料
    $data['config'] = $this->house_config_model->get_config();

    //页面标题
    $data['page_title'] = '成交房源详情';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/personal_center.js,'
      . 'mls/js/v1.0/jquery.validate.min.js');

    $this->view('office/deal_details', $data);
  }
}

/* End of file deal_check.php */
/* Location: ./application/mls/controllers/deal_check.php */
