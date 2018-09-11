<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 门店增删改查
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Agency extends MY_Controller
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

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('agency_model');
    $this->load->model('agency_review_model');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $data_view['user_func_menu'] = $this->user_func_menu;
    $where = 'company_id = ' . $this->user_arr['company_id'];
    $pg = $this->input->post('page');

    $page = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $this->_init_pagination($page);
    $this->_total_count = $this->agency_model->count_by($where);
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
    //门店列表
    $this->agency_model->set_select_fields(array());
    $data_view['agency'] = $this->agency_model->get_all_by($where, $this->_offset, $this->_limit);

    //页面标题
    $data_view['page_title'] = '办公管理-跟进日志';

    //需要加载的css
    $data_view['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
      . ',mls/css/v1.0/house_manage.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    //底部JS
    $data_view['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house.js,'
      . 'mls/js/v1.0/backspace.js');
    //var_dump($data_view);exit;
    $this->view("agency/personnel/agency", $data_view);
  }

  //添加门店
  public function add()
  {
    $name = $this->input->post('name');
    $agency_info = $this->agency_model->get_one_by('name = "' . $name . '"');
    if (is_full_array($agency_info)) {
      echo '门店名称已存在，请重新输入';
      exit;
    }
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $insert_data = array('name' => $name, 'telno' => $telno, 'address' => $address, 'company_id' => $this->user_arr['company_id'], 'status' => 0);
    $agency_id = $this->agency_model->insert($insert_data);
    $this->agency_review_model->insert(array('agency_id' => $agency_id, 'broker_id' => $this->user_arr['broker_id'], 'action' => 1, 'status' => 0, 'create_time' => time()));
    echo '添加门店成功,请等待审核';
  }

  //修改门店
  public function modify()
  {
    $agency_id = $this->input->post("agency_id");
    $name = $this->input->post('name');
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $update_data = array('name' => $name, 'telno' => $telno, 'address' => $address, 'status' => 0);
    $this->agency_model->update_agency_byid($update_data, $agency_id);
    echo '修改门店成功';
  }

  //删除门店
  public function delete()
  {
    $agency_id = $this->input->post("agency_id");
    //echo $agency_id;exit;
    $this->load->model('broker_info_model');
    $broker_count = $this->broker_info_model->count_by_agency_id($agency_id);
    if ($broker_count) {
      echo '{"status":"failed","msg":"门店下有经纪人不能删除"}';
      exit;
    }
    $this->agency_model->update_agency_byid(array('status' => 2), $agency_id);
    echo '{"status":"success","msg":"删除门店成功"}';
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
   * 根据门店ID获取当前门店下的经纪人
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_brokerinfo_by_agencyid()
  {
    $this->load->model('api_broker_model');
    //经纪人信息
    $brokerinfo = array();
    $agency_id = $this->input->get('agency_id', TRUE);
    $agency_id = intval($agency_id);

    if ($agency_id > 0) {
      $brokerinfo = $this->api_broker_model->get_brokers_agency_id($agency_id);
    }

    echo json_encode($brokerinfo);
  }
}

/* End of file agency.php */
/* Location: ./application/mls/controllers/agency.php */
