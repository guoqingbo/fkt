<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 个人中心-跟进任务
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class My_task extends MY_Controller
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
    $this->load->model('task_model');
    $this->load->model('api_broker_model');
  }

  //任务管理页
  public function index()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $pg = $this->input->post('page');

    $where = 'run_broker_id = ' . $this->user_arr['broker_id'];

    $task_type = $this->input->post('task_type');
    $data_view['task_type'] = $task_type;
    if ($task_type) {
      $where .= ' and task_type = ' . $task_type;
    }
    $start_date_begin = $this->input->post('start_date_begin');

    $data_view['start_date_begin'] = $start_date_begin;
    if ($start_date_begin) {
      $where .= ' and over_date >= ' . strtotime($start_date_begin);
    }
    $start_date_end = $this->input->post('start_date_end');
    $data_view['start_date_end'] = $start_date_end;
    if ($start_date_end) {
      $where .= ' and over_date <= ' . strtotime($start_date_end);
    }
    $allot_agency = $this->input->post('allot_agency');
    $data_view['allot_agency'] = $allot_agency;

    $allot_broker = $this->input->post('allot_broker');
    $data_view['allot_broker'] = $allot_broker;
    if ($allot_broker) {
      $where .= ' and allot_broker_id = ' . $allot_broker;
    }
    $status = $this->input->post('status');
    $data_view['status'] = $status;
    if ($status == 3) {
      $where .= ' and over_date < ' . time();
    } else if ($status) {
      $where .= ' and over_date > ' . time();
      $where .= ' and status = ' . $status;
    }
    $run_agency = $this->input->post('run_agency');
    $data_view['run_agency'] = $run_agency;

    /*$run_broker = $this->input->post('run_broker');
    $data_view['run_broker']=$run_broker;
    if($run_broker){
        $where .= ' and run_broker_id = '.$run_broker;
    }*/
    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->task_model->count_by($where);
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;
    //任务信息
    $task_info = $this->task_model->get_all_by($where, $this->_offset, $this->_limit);

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
    //echo $this->db->last_query();
    //根据房源id/客源id 获取业主姓名/客户姓名

    //echo $value['expiretime'] == 0 ? '' : date('Y-m-d', $value['expiretime']);

    if (is_full_array($task_info)) {
      $this->load->model('sell_house_model');
      $this->load->model('rent_house_model');
      $this->load->model('buy_customer_model');
      $this->load->model('rent_customer_model');

      foreach ($task_info as $key => $value) {
        $task_type = $value['task_type'];
        $task_style = $value['task_style'];
        if ($task_type == 2) {//房源
          $house_id = $value['house_id'];
          if ($task_style == 1) {//出售
            $task_info[$key]['format_house_id'] = format_info_id($house_id, 'sell');
            $this->sell_house_model->set_tbl('sell_house');
            $this->sell_house_model->set_search_fields(array('owner'));
            $this->sell_house_model->set_id($house_id);
            $sell_house_info = $this->sell_house_model->get_info_by_id();
            $task_info[$key]['name'] = $sell_house_info['owner'];
          } elseif ($task_style == 2) {//出租
            $task_info[$key]['format_house_id'] = format_info_id($house_id, 'rent');
            $this->rent_house_model->set_tbl('rent_house');
            $this->rent_house_model->set_search_fields(array('owner'));
            $this->rent_house_model->set_id($house_id);
            $rent_house_info = $this->rent_house_model->get_info_by_id();
            $task_info[$key]['name'] = $rent_house_info['owner'];
          }
        } elseif ($task_type == 3) {//客源
          $custom_id = $value['custom_id'];

          if ($task_style == 3) {//求购
            $task_info[$key]['format_custom_id'] = format_info_id($custom_id, 'buy_customer');
            $this->buy_customer_model->set_tbl('buy_customer');
            $this->buy_customer_model->set_search_fields(array('truename'));
            $this->buy_customer_model->set_id($custom_id);
            $buy_customer_info = $this->buy_customer_model->get_info_by_id();
            $task_info[$key]['name'] = $buy_customer_info['truename'];
          } elseif ($task_style == 4) {//求租
            $task_info[$key]['format_custom_id'] = format_info_id($custom_id, 'rent_customer');
            $this->rent_customer_model->set_tbl('rent_customer');
            $this->rent_customer_model->set_search_fields(array('truename'));
            $this->rent_customer_model->set_id($custom_id);
            $rent_customer_info = $this->rent_customer_model->get_info_by_id();
            $task_info[$key]['name'] = $rent_customer_info['truename'];

          }
        } else {
          $task_info[$key]['name'] = '系统';
          switch ($task_style) {
            case 1:
              $task_info[$key]['format_house_id'] = format_info_id($value['house_id'], 'sell');
              break;
            case 2:
              $task_info[$key]['format_house_id'] = format_info_id($value['house_id'], 'rent');
              break;
            case 3:
              $task_info[$key]['format_custom_id'] = format_info_id($value['custom_id'], 'buy_customer');
              break;
            case 4:
              $task_info[$key]['format_custom_id'] = format_info_id($value['custom_id'], 'rent_customer');
              break;
          }
        }
        $allot_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($value['allot_broker_id']);//分配人信息
        $run_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($value['run_broker_id']);
        $task_info[$key]['allot_truename'] = $allot_broker_info['truename'];
        $task_info[$key]['run_truename'] = $run_broker_info['truename'];

        $allot_agency_info = $this->api_broker_model->get_by_agency_id($allot_broker_info['agency_id']);
        $run_agency_info = $this->api_broker_model->get_by_agency_id($run_broker_info['agency_id']);
        $task_info[$key]['allot_agencyname'] = $allot_agency_info['name'];
        $task_info[$key]['run_agencyname'] = $run_agency_info['name'];

      }
    }
    //获取部门
    $company_id = $this->user_arr['company_id'];
    if (empty($company_id)) {
      $company_id = 0;
    }
    $agency_info = $this->api_broker_model->get_agencys_by_company_id($company_id);
    //var_dump($agency_info);exit();
    $data_view['agency_info'] = $agency_info;
    $data_view['company_id'] = $company_id;
    $data_view['task_info'] = $task_info;
    //获取分配人
    if ($allot_agency) {
      $broker_info_allot = $this->api_broker_model->get_brokers_agency_id($allot_agency);
      $data_view['broker_info_allot'] = $broker_info_allot;
    }
    //获取执行人
    if ($run_agency) {
      $broker_info_run = $this->api_broker_model->get_brokers_agency_id($run_agency);
      $data_view['broker_info_run'] = $broker_info_run;
    }

    //页面标题
    $data_view['page_title'] = '跟进任务';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css ');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/customer_list.js,mls/js/v1.0/scrollPic.js');

    $this->view("uncenter/my_task/my_task", $data_view);
  }

  //添加
  public function add()
  {

  }

  //修改
  public function modify()
  {

  }

  //撤销
  public function revoke()
  {
    $id = $this->input->post("id");
    $reason = $this->input->post("reason");
    $params = array();
    $task_info = $this->task_model->get_by_id($id);
    $task_type = $task_info['task_type'];
    $run_broker_id = $task_info['run_broker_id'];
    $house_id = $task_info['house_id'];
    $custom_id = $task_info['custom_id'];
    $broker_id = $this->user_arr['broker_id'];

    $broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($broker_id);//分配人信息
    $run_broker_info = $this->api_broker_model->get_baseinfo_by_broker_id($run_broker_id);
    $truename = $broker_info['truename'];
    $run_truename = $run_broker_info['truename'];

    $this->task_model->update_by_id(array('status' => 1, 'start_date' => time(), 'reason' => $reason), $id);
    $params['name'] = $truename;
    $this->load->model('message_base_model');
    if ($task_type == 2) {
      $params['type'] = "f";
      $params['id'] = $house_id;
    } elseif ($task_type == 3) {
      $params['id'] = $custom_id;
    }
    $task_style = $task_info['task_style'];
    if ($task_style == 1) {
      $params['id'] = format_info_id($params['id'], 'sell');
    } elseif ($task_style == 2) {
      $params['id'] = format_info_id($params['id'], 'rent');
    } elseif ($task_style == 3) {
      $params['id'] = format_info_id($params['id'], 'buy_customer');
    } elseif ($task_style == 4) {
      $params['id'] = format_info_id($params['id'], 'rent_customer');
    }
    //33
    $this->message_base_model->add_message('7-42', $run_broker_id, $run_truename, '/my_task/', $params);
    echo '操作成功';
  }

  /**
   *部门触发获取经济人
   * @param type $agency_id
   */
  public function get_broker_ajax($agency_id)
  {
    $broker = $this->api_broker_model->get_brokers_agency_id($agency_id);
    $broker_info = array();
    if (is_full_array($broker)) {
      foreach ($broker as $v) {
        $broker_info[] = array('broker_id' => $v['broker_id'], 'truename' => $v['truename'], 'phone' => $v['phone']);
      }
    }
    echo json_encode($broker_info);
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

/* End of file my_task.php */
/* Location: ./application/mls/controllers/my_task.php */
