<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Bank_account extends MY_Controller
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
  private $_limit = 20;

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
    $this->load->model('department_model');
    $this->load->model('signatory_info_model');

    $this->load->model('signatory_model');
    $this->load->model('company_employee_model');

    $this->load->model('bank_account_model');
    $this->load->model('dictionary_model');

    $this->load->model('signatory_operate_log_model');
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

  public function index($bank_account_id = "", $b_id = '')
  {
    $view_type = 'index';//用于区分首页还是搜索页
    if ($this->input->get('view_type')) {
      $view_type = $this->input->get('view_type');
    }

    $data['user_menu'] = $this->user_menu;

    $bank_where = array("status" => 1, "dictionary_type_id" => '1');

    $bank_info_list = $this->dictionary_model->get_all_by($bank_where);

    $data['bank_info_list'] = $bank_info_list;

    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $time = time();
    $this->_init_pagination($page);
    //查询消息的条件
    $cond_where = "status = 1";

    $bank_account_id_str = $this->input->get('search_bank_account_id');
    $keyword = $this->input->get('search_keyword');
    if ($keyword) {
      $keyword = urldecode($keyword);
      $cond_where .= " and (concat(`card_name`, `card_no`, `bank_name`, `bank_deposit`) LIKE '%" . $keyword . "%')";
    }
    if (!empty($bank_account_id_str)) {
      $bank_account_id_str = trim($bank_account_id_str, ',');
      $bank_account_id_str = explode(',', $bank_account_id_str);
      if (!empty($bank_account_id_str)) {
        $cond_where .= ' and id in (' . $bank_account_id_str . ')';
      }
    }

    if ($bank_account_id) {
      $cond_where .= ' and id = ' . $bank_account_id;
    }

    $data['search_keyword'] = $keyword;
//    $cond_where_bank_account = $cond_where;

    //符合条件的总行数
    $this->_total_count = $this->bank_account_model->count_by($cond_where);

    //获取数据列表内容
    $signatory_all_info = $this->bank_account_model->get_all_by($cond_where, $this->_offset, $this->_limit, 'id ', 'ASC');

    $data['signatory_all_info'] = $signatory_all_info;

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
    $data['page_title'] = '银行卡管理';
      $data['page_params'] = $params;
    //需要加载的css
      $data['css'] = load_css('mls_guli/third/iconfont/iconfont.css,mls_guli/css/v1.0/base.css'
      . ',mls_guli/css/v1.0/house_manage.css,mls_guli/css/v1.0/cal.css'
      . ',mls_guli/css/v1.0/guest_disk.css,mls_guli/css/v1.0/house_new.css'
      . ',mls_guli/css/v1.0/personal_center.css'
      . ',mls_guli/images/alphabeta/bank-logo/bank-logo.css');

    //需要加载的JS
    $data['js'] = load_js('mls_guli/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls_guli/js/v1.0/openWin.js,mls_guli/js/v1.0/house.js,'
      . 'mls_guli/js/v1.0/backspace.js,mls_guli/js/v1.0/house_list.js,mls_guli/js/v1.0/scrollPic.js'
    );


    $this->view('bank_account/bank_account_show', $data);
  }

  //添加数据字典
  public function add_bank_account()
  {
    $bank_id = $this->input->post('bank_id');
    $bank_id = $bank_id ? $bank_id : 0;
    $card_no = $this->input->post('card_no');
    $card_name = $this->input->post('card_name');
    $bank_deposit = $this->input->post('bank_deposit');

    if ($bank_id > 0) {
      $bank_info = $this->dictionary_model->get_by_id($bank_id);
      if ($bank_info) {
        $bank_account_id = $this->bank_account_model->add_bank_account($card_no, $card_name, $bank_info['id'], $bank_info['name'], $bank_deposit, 1);
        if (is_int($bank_account_id) && $bank_account_id > 0) {
          //操作日志
          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['department_id'] = $this->user_arr['department_id'];
          $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
          $add_log_param['signatory_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 28;
          $add_log_param['text'] = '添加银行卡' . $card_no . '资料';
          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();

          $this->signatory_operate_log_model->add_operate_log($add_log_param);

          echo '{"status":"success","msg":"添加银行卡成功"}';
        } else {
          echo '{"status":"failed","msg":"添加银行卡失败"}';
        }
      } else {
        echo '{"status":"failed","msg":"银行卡信息不正确"}';
        return;
      }
    } else {
      echo '{"status":"failed","msg":"银行卡信息不正确"}';
      return;
    }

  }

  //修改数据字典弹出框 传值bank_account_id
  public function modify_bank_account_pop()
  {
    $bank_account_id = $this->input->post('bank_account_id');
    if ($bank_account_id > 0) {
      $bank_account_info = $this->bank_account_model->get_by_id($bank_account_id);
      echo json_encode($bank_account_info);
    }
  }

  // 修改数据字典
  public function modify_bank_account()
  {

    $bank_account_id = $this->input->post("bank_account_id");
    $bank_id = $this->input->post('bank_id');
    $bank_id = $bank_id ? $bank_id : 0;
    $card_no = $this->input->post('card_no');
    $card_name = $this->input->post('card_name');
    $bank_deposit = $this->input->post('bank_deposit');

    if ($bank_id > 0) {
      $bank_info = $this->dictionary_model->get_by_id($bank_id);
      if ($bank_info) {
        $update_data = array('card_no' => $card_no, 'card_name' => $card_name, 'bank_deposit' => $bank_deposit, 'bank_id' => $bank_info["id"], 'bank_name' => $bank_info["name"], 'updatetime' => time());
        $update_result = $this->bank_account_model->update_by_id($update_data, $bank_account_id);

        if ($update_result) {
          //操作日志
          $add_log_param = array();
          $add_log_param['company_id'] = $this->user_arr['company_id'];
          $add_log_param['department_id'] = $this->user_arr['department_id'];
          $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
          $add_log_param['signatory_name'] = $this->user_arr['truename'];
          $add_log_param['type'] = 28;
          $add_log_param['text'] = '修改数据字典数据' . $bank_account_id . '资料';
          $add_log_param['from_system'] = 1;
          $add_log_param['from_ip'] = get_ip();
          $add_log_param['mac_ip'] = '127.0.0.1';
          $add_log_param['from_host_name'] = '127.0.0.1';
          $add_log_param['hardware_num'] = '测试硬件序列号';
          $add_log_param['time'] = time();

          $this->signatory_operate_log_model->add_operate_log($add_log_param);
          echo '{"status":"success","msg":"修改银行卡成功"}';
        } else {
          echo '{"status":"error","msg":"未作任何修改"}';
        }
      } else {
        echo '{"status":"failed","msg":"银行卡信息不正确"}';
        return;
      }
    } else {
      echo '{"status":"failed","msg":"银行卡信息不正确"}';
      return;
    }
  }

  //删除数据
  public function delete_bank_account()
  {
    $bank_account_id = $this->input->post('bank_account_id');
    $data_view = array();
    $data_view['deleteResult'] = '';
    $data_view['title'] = '银行卡-删除数据';
    $data_view['conf_where'] = 'index';

    //删除类型
    $deleteResult = $this->bank_account_model->update_by_id(array('status' => 2, 'updatetime' => time()), $bank_account_id);
    //1 删除成功 0 删除失败
    //删除门店，与该门店相关联的门店数据范围，设为无效。
    if (1 == $deleteResult) {
      //操作日志
      $add_log_param = array();
      $add_log_param['company_id'] = $this->user_arr['company_id'];
      $add_log_param['department_id'] = $this->user_arr['department_id'];
      $add_log_param['signatory_id'] = $this->user_arr['signatory_id'];
      $add_log_param['signatory_name'] = $this->user_arr['truename'];
      $add_log_param['type'] = 28;
      $add_log_param['text'] = '删除银行卡' . $bank_account_id . '资料';
      $add_log_param['from_system'] = 1;
      $add_log_param['from_ip'] = get_ip();
      $add_log_param['mac_ip'] = '127.0.0.1';
      $add_log_param['from_host_name'] = '127.0.0.1';
      $add_log_param['hardware_num'] = '测试硬件序列号';
      $add_log_param['time'] = time();

      $this->signatory_operate_log_model->add_operate_log($add_log_param);

      $data_view['msg'] = "删除成功";
    }

    echo json_encode($data_view);
  }


  /**
   * 根据关键词获取银行卡信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_bank_account_info_by_kw()
  {
    //当前经纪人等级
    $role_level = intval($this->user_arr['role_level']);
    //当前经纪人所在公司
    $company_id = intval($this->user_arr['company_id']);
    //根据角色，决定搜索范围
    $search_arr = array(
      'role_level' => $role_level,
      'company_id' => $company_id
    );

    $keyword = $this->input->get('keyword', TRUE);
    $select_fields = array('id', 'card_name', 'card_no', 'bank_name');
    $this->bank_account_model->set_select_fields($select_fields);
    $cmt_info = $this->bank_account_model->get_bank_account_info_by_kw($keyword, $search_arr, 10);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['bank_name'];
    }

    if (empty($cmt_info) || empty($search_arr['role_level']) || empty($search_arr['company_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无类型';
    }

    echo json_encode($cmt_info);
  }

}
