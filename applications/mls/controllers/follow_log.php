<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店管理-办公管理-跟进日志
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Follow_log extends MY_Controller
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
    $this->load->model('follow_model');
    $this->load->model('api_broker_model');
  }

  public function index()
  {
    $data_view = array();
    $broker_info = $this->user_arr;
    $data_view['user_menu'] = $this->user_menu;
    $data_view['user_func_menu'] = $this->user_func_menu;
    $pg = $this->input->post('page');

    //权限条件($func_area  1 本人  2 门店  3公司)
    $func_area = isset($this->user_func_permission['area']) ? $this->user_func_permission['area'] : 1;
    if ($func_area == 1) {
      $where = 'broker_id = ' . $broker_info['broker_id'];
    } elseif ($func_area == 2) {
      $where = 'agency_id = ' . $broker_info['agency_id'];
      $data_view['agency_info'] = array(array('agency_id' => $broker_info['agency_id'], 'agency_name' => $broker_info['agency_name']));
    } else {
      $where = 'company_id = ' . $broker_info['company_id'];
      //获取部门
      $data_view['agency_info'] = $this->api_broker_model->get_agencys_by_company_id($this->user_arr['company_id']);
    }

    $follow_type = $this->input->post('follow_type');
    $data_view['follow_type'] = $follow_type;
    if ($follow_type) {
      $where .= ' and follow_type = ' . $follow_type;
    }
    $start_date_begin = $this->input->post('start_date_begin');
    $data_view['start_date_begin'] = $start_date_begin;
    if ($start_date_begin) {
      $where .= ' and date >= "' . $start_date_begin . '"';
    }
    $start_date_end = $this->input->post('start_date_end');
    $data_view['start_date_end'] = $start_date_end;
    if ($start_date_end) {
      $where .= ' and date <= "' . $start_date_end . '"';
    }

    $run_agency = $this->input->post('run_agency');
    $data_view['run_agency'] = $run_agency;
    if ($run_agency) {
      $where .= ' and agency_id = ' . $run_agency;
    }
    $run_broker = $this->input->post('run_broker');
    $data_view['run_broker'] = $run_broker;
    if ($run_broker) {
      $where .= ' and broker_id = ' . $run_broker;
    }
    //echo $where;exit();
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->follow_model->count_by($where);
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

    $follow_info = $this->follow_model->get_lists($where, $this->_offset, $this->_limit);

    if (is_full_array($follow_info)) {
      $this->load->model('sell_house_model');
      $this->load->model('rent_house_model');
      $this->load->model('buy_customer_model');
      $this->load->model('rent_customer_model');


      foreach ($follow_info as $key => $value) {
        $follow_type = $value['follow_type'];
        $type = $value['type'];

        if ($follow_type == 1) {//房源
          $house_id = $value['house_id'];
          if ($type == 1) {//出售

            $this->sell_house_model->set_tbl('sell_house');
            $this->sell_house_model->set_search_fields(array('owner'));
            $this->sell_house_model->set_id($house_id);
            $sell_house_info = $this->sell_house_model->get_info_by_id();
            $follow_info[$key]['name'] = $sell_house_info['owner'];
          } elseif ($type == 2) {//出租

            $this->rent_house_model->set_tbl('rent_house');
            $this->rent_house_model->set_search_fields(array('owner'));
            $this->rent_house_model->set_id($house_id);
            $rent_house_info = $this->rent_house_model->get_info_by_id();
            $follow_info[$key]['name'] = $rent_house_info['owner'];
          }
        } elseif ($follow_type == 2) {//客源
          $customer_id = $value['customer_id'];
          if ($type == 3) {//求购

            $this->buy_customer_model->set_tbl('buy_customer');
            $this->buy_customer_model->set_search_fields(array('truename'));
            $this->buy_customer_model->set_id($customer_id);
            $sell_house_info = $this->buy_customer_model->get_info_by_id();
            $follow_info[$key]['name'] = $sell_house_info['truename'];
          } elseif ($type == 4) {//求租

            $this->rent_customer_model->set_tbl('rent_customer');
            $this->rent_customer_model->set_search_fields(array('truename'));
            $this->rent_customer_model->set_id($customer_id);
            $sell_house_info = $this->rent_customer_model->get_info_by_id();
            $follow_info[$key]['name'] = $sell_house_info['truename'];

          }
        } else {
          $follow_info[$key]['name'] = '系统';
        }

        $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($value['broker_id']);

        $follow_info[$key]['truename'] = $broker_info['truename'];

        $agency_info = $this->api_broker_model->get_by_agency_id($broker_info['agency_id']);

        $follow_info[$key]['agencyname'] = $agency_info['name'];

      }
    }

    $data_view['follow_info'] = $follow_info;

    //获取执行人
    if ($run_agency) {
      $broker_info_run = $this->api_broker_model->get_brokers_agency_id($run_agency);
      $data_view['broker_info_run'] = $broker_info_run;
    }

    //页面标题
    $data_view['page_title'] = '店面部门管理';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/personal_center.css,mls/css/v1.0/house_manage.css ');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');


    $this->view("agency/office/follow_log", $data_view);
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

/* End of file follow_log.php */
/* Location: ./application/mls/controllers/follow_log.php */
