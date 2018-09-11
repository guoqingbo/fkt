<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 科地通讯录
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Signing_employee extends MY_Controller
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
    $this->load->model('signing_employee_model');
    $this->load->model('department_model');
  }

  /**
   * 员工通讯录
   *
   * @access public
   * @return void
   */
  public function index()
  {
    $data['user_menu'] = $this->user_menu;
    // 分页参数
    $page = $this->input->post('page') ? intval($this->input->post('page')) : intval($this->_current_page);
    $this->_init_pagination($page);

      $cond_where = " where b.status = 1";
    //表单提交参数组成的查询条件
    $store_name = $this->input->post('store_name');
    if ($store_name && $store_name !== "no") {
      $cond_where .= " and a.name LIKE '%" . $store_name . "%'";
    }
      $e_name = $this->input->post('e_name');
      if ($e_name) {
          $cond_where .= " and b.truename LIKE '%" . $e_name . "%'";
      }
      $tel = $this->input->post('tel');
      if ($tel) {
          $cond_where .= " and b.phone LIKE '%" . $tel . "%'";
      }
    //符合条件的总行数
    $this->_total_count =
      $this->signing_employee_model->count_by($cond_where);

    $cond_where = $cond_where . " order by b.department_id ASC,p.system_group_id ASC ";
    //计算总页数
    $pages = $this->_total_count > 0 ? ceil($this->_total_count / $this->_limit) : 0;

    //获取列表内容
    $list = $this->signing_employee_model->get_all_by($c_id, $cond_where, $this->_offset, $this->_limit);

    $department = $this->department_model->get_all_by_company_id(1);

    $data['list'] = $list;
    $data['store_name'] = $store_name;
    $data['e_name'] = $e_name;
    $data['tel'] = $tel;
    $data['department'] = $department;
  //  $data['company_id'] = $c_id;
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
    $data['page_title'] = '签约员工通讯录';

    //需要加载的css
    $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css,mls/css/v1.0/personal_center.css,mls/css/v1.0/house_new.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js,mls/js/v1.0/house_list.js,mls/js/v1.0/scrollPic.js');

    $this->view('signing_employee/signing_contents', $data);
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




  //获取备注信息
  public function get_remark($id)
  {
    $isajax = $this->input->get('isajax', TRUE);
    if ($isajax) {
      $broker_id = $this->user_arr['broker_id'];
      $broker_info = $this->company_employee_model->get_broker_by_id($broker_id);
      $c_id = $broker_info['company_id'];
      $list = $this->company_employee_model->get_remark_by($id, $c_id);
      if ($list) {
        $result = array('result' => 'ok', 'list' => $list);
      } else {
        $result = array('result' => 'no');
      }
      echo json_encode($result);
    }
  }

  //更新备注信息
  public function update_remark()
  {
    $this_user = $this->user_arr;
    $broker_id = $this_user['broker_id'];
    $id = $this->input->post('id', true);
    $remarker_id = $this->input->post('remarker_id', true);
    $remark = $this->input->post('remark', true);
    if ($id == "") {
      $remark_data = $this->company_employee_model->insert_remark($broker_id, $remarker_id, $remark);
    } else {
      $remark_data = $this->company_employee_model->update_remark($id, $remark);
    }
    echo $remark_data;
  }
}
