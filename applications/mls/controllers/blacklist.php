<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Blacklist extends MY_Controller
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
  private $_limit = 15;

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
    $this->load->model('blacklist_model');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    //$this->load->model('company_employee_model');
    $this->load->model('operate_log_model');
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


  public function index()
  {
    $broker_id = $this->user_arr['broker_id'];

    $data['user_menu'] = $this->user_menu;
    $broker_info = $this->blacklist_model->get_broker_by_id($broker_id);
    $company_id = $broker_info['company_id'];//所属公司id


    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);

    //查询消息的条件
    if ($company_id != "0") {
      $cond_where = " where company_id = {$company_id} ";
      //根据数据范围，获得门店数据
      $this->load->model('agency_permission_model');
      $this->agency_permission_model->set_agency_id($this->user_arr['agency_id'], $this->user_arr['company_id'], $this->user_arr['role_level']);
      $access_agency_ids_data = $this->agency_permission_model->get_agency_id_by_main_id_access($this->user_arr['agency_id'], 'is_blacklist');
      $all_access_agency_ids = '';
      if (is_full_array($access_agency_ids_data)) {
        foreach ($access_agency_ids_data as $k => $v) {
          $all_access_agency_ids .= $v['sub_agency_id'] . ',';
        }
        $all_access_agency_ids .= $this->user_arr['agency_id'];
        $all_access_agency_ids = trim($all_access_agency_ids, ',');
      } else {
        $all_access_agency_ids = $this->user_arr['agency_id'];
      }
      $cond_where .= " and agency_id in (" . $all_access_agency_ids . ")";
    } else {
      $cond_where = " where broker_id = {$broker_id} ";
    }
    //表单提交参数组成的查询条件
    $cate = $this->input->post('cate', TRUE);
    if ($cate) {
      $cond_where .= " and cate LIKE '%" . $cate . "%'";
    }

    $bname = $this->input->post('bname', TRUE);
    if ($bname) {
      $cond_where .= " and bname LIKE '%" . $bname . "%'";
    }
    $tel = $this->input->post('tel', TRUE);
    if ($tel) {
      $cond_where .= " and tel LIKE '%" . $tel . "%'";
    }
    //符合条件的总行数
    $this->_total_count = $this->blacklist_model->count_by($cond_where);

    $cond_where = $cond_where . " order by id DESC ";
    //计算总页数
    //$pages  = $this->_total_count > 0 ? ceil( $this->_total_count / $this->_limit ) : 0;

    //获取列表内容
    $list = $this->blacklist_model->get_all_by($cond_where, $this->_offset, $this->_limit);


    $data['list'] = $list;
    $data['bname'] = $bname;
    $data['tel'] = $tel;
    $data['cate'] = $cate;

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
    $data['page_title'] = '黑名单';


    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/cal.css'
      . ',mls/css/v1.0/guest_disk.css,mls/css/v1.0/house_new.css');

    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js'
      . 'common/third/My97DatePicker/WdatePicker.js');


    $this->view('blacklist/blacklist_show', $data);
  }

  //黑名单添加
  public function add()
  {
    $black_info['bname'] = $this->input->post('bname', TRUE);
    $black_info['tel'] = $this->input->post('tel', TRUE);
    $black_info['remark'] = $this->input->post('remark', TRUE);
    $black_info['cate'] = $this->input->post('cate', TRUE);
    //echo "<hr/>";
    //获取经纪人信息
    $broker_id = $this->user_arr['broker_id'];
    $broker_info = $this->blacklist_model->get_broker_by_id($broker_id);
    //echo json_encode($broker_info);die();
    //获取门店列表内容
    $agency_id = $broker_info['agency_id'];//门店id
    //echo json_encode($agency_id);die();
    if ($agency_id) {
      $this->agency_model->set_select_fields(array('name'));//指定获取区域和街道
      $agency_info = $this->agency_model->get_by_id($agency_id);
      $black_info['store_name'] = $agency_info['name'];
    } else if ($agency_id == "0") {
      $agency_name = $this->blacklist_model->get_register_broker_by_id($broker_info['id']);
      //echo json_encode($agency_name);die();
      $black_info['store_name'] = $agency_name['storename'];
    }
    $company_id = $broker_info['company_id'];//所属公司id

    $black_info['truename'] = $broker_info['truename'];
    $black_info['addtime'] = time();
    $black_info['broker_id'] = $broker_id;
    $black_info['agency_id'] = $agency_id;
    $black_info['company_id'] = $company_id;//die();
    //echo json_encode($black_info);die();
    //print_r($black_info);exit;
    $num = $this->blacklist_model->add_blacklist($black_info);

    if ($num) {
      $info["status"] = 1;
      echo json_encode($info);

      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['agency_id'] = $this->user_arr['agency_id'];
      $add_log_param['broker_id'] = $this->user_arr['broker_id'];
      $add_log_param['broker_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 39;
      $add_log_param['text'] = '新增 ' . $black_info['bname'] . ' 的黑名单';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();
      $this->operate_log_model->add_operate_log($add_log_param);
    } else {
      $info["status"] = 0;
      echo json_encode($info);
    }
  }

  //黑名单删除
  public function del()
  {
    $black_id = $this->input->post('black_id', TRUE);
    $id = $this->input->post('id', TRUE);
    if ($black_id) {
      $data = $this->blacklist_model->get_by_black_id($black_id);
      if ($data['id']) {
        echo json_encode($data);
      }
    } else if ($id) {
      $black_info = $this->blacklist_model->get_by_black_id($id);
      $bname = '';
      if (is_full_array($black_info)) {
        $bname = $black_info['bname'];
      }

      $arr = array("id" => $id);
      $num = $this->blacklist_model->del_blacklist($arr);
      if ($num) {
        $info["status"] = 1;
        echo json_encode($info);
        //操作日志
        $add_log_param = array();
        $add_log_param['company_id'] = $this->user_arr['company_id'];
        $add_log_param['agency_id'] = $this->user_arr['agency_id'];
        $add_log_param['broker_id'] = $this->user_arr['broker_id'];
        $add_log_param['broker_name'] = $this->user_arr['truename'];
        $add_log_param['type'] = 39;
        $add_log_param['text'] = '删除 ' . $bname . ' 的黑名单';
        $add_log_param['from_system'] = 1;
        $add_log_param['from_ip'] = get_ip();
        $add_log_param['mac_ip'] = '127.0.0.1';
        $add_log_param['from_host_name'] = '127.0.0.1';
        $add_log_param['hardware_num'] = '测试硬件序列号';
        $add_log_param['time'] = time();
        $this->operate_log_model->add_operate_log($add_log_param);
      } else {
        $info["status"] = 0;
        echo json_encode($info);
      }
    }
  }

  //黑名单详情
  public function detail()
  {
    $detail_id = $this->input->post('detail_id', TRUE);
    $data = $this->blacklist_model->get_by_black_id($detail_id);
    if ($data['id']) {
      $data['addtime'] = date("Y-m-d H:i:s", $data['addtime']);
      echo json_encode($data);
    }

  }

  //黑名单修改
  public function edit()
  {
    $edit_pop_id = $this->input->post('edit_pop_id', TRUE);
    $edit_id = $this->input->post('edit_id', TRUE);
    $edit_info['bname'] = $this->input->post('bname', TRUE);
    $edit_info['tel'] = $this->input->post('tel', TRUE);
    $edit_info['remark'] = $this->input->post('remark', TRUE);
    $edit_info['cate'] = $this->input->post('cate', TRUE);
    $edit_info['id'] = $edit_id;
    if ($edit_pop_id) {
      $data = $this->blacklist_model->get_by_black_id($edit_pop_id);
      if ($data['id']) {
        echo json_encode($data);
      }
    } else if ($edit_id) {
      $arr = array("id" => $edit_id);
      $num = $this->blacklist_model->edit_blacklist($arr, $edit_info);
      if ($num) {
        $edit_info["status"] = 1;
        echo json_encode($edit_info);
      } else {
        $edit_info["status"] = 0;
        echo json_encode($edit_info);
      }
    } else {
      return false;
    }
  }


}
