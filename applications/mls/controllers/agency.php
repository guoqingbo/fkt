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
    $this->load->model('agency_model');
    $this->load->model('agency_review_model');
  }

  //门店管理页
  public function index()
  {
    $data_view = array();
    $data_view['user_menu'] = $this->user_menu;
    $data_view['user_func_menu'] = $this->user_func_menu;
    $where = 'status != 2';
    $where .= ' and company_id = ' . $this->user_arr['company_id'];

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
      echo '{"status":"failed","msg":"门店名称已存在，请重新输入"}';
      exit;
    }
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $insert_data = array('name' => $name, 'telno' => $telno, 'address' => $address, 'company_id' => $this->user_arr['company_id'], 'status' => 0);
    $agency_id = $this->agency_model->insert($insert_data);
    $this->agency_review_model->insert(array('agency_id' => $agency_id, 'broker_id' => $this->user_arr['broker_id'], 'action' => 1, 'status' => 0, 'create_time' => time()));
    echo '{"status":"success","msg":"添加门店成功,请等待审核"}';
  }

  //修改门店
  public function modify()
  {
    $agency_info = array();
    $agency_id = $this->input->post("agency_id");

    $agencys = $this->agency_model->get_by_id($agency_id);
    $agency_info['agency_id'] = $agencys['id'];
    $agency_info['company_id'] = $agencys['company_id'];

    $name = $this->input->post('name');
    $telno = $this->input->post('telno');
    $address = $this->input->post('address');
    $update_data = array('name' => $name, 'telno' => $telno, 'address' => $address, 'status' => 0);
    $this->agency_model->update_agency_byid($update_data, $agency_id);
    echo '{"status":"success","msg":"修改门店成功"}';
  }

  //删除门店
  public function delete()
  {
    $wei = 1;
    $arr_id = array();
    $agency_id = $this->input->post("agency_id");

    //判断是否有删除门店权限
    $agencys = $this->agency_model->get_by_id($agency_id);
    foreach ($agencys as $k => $v) {
      if (is_array($v)) {
        $v['agency_id'] = $v['id'];
        $arr_id[$k] = $v['id'];
      } else {
        $wei = 2;
        break;
      }
    }

    if ($wei == 2) {
      $arr_id = $agencys['id'];
    }

    if (is_full_array($arr_id)) {
      $this->load->model('broker_info_model');
      $broker_count = $this->broker_info_model->count_by_agency_id($arr_id);
      if ($broker_count) {
        echo '{"status":"failed","msg":"门店下有经纪人不能删除"}';
        exit;
      }
      //status=0 审核中
      $this->agency_model->update_agency_byid(array('status' => 0), $arr_id);
      //添加删除审核
      foreach ($arr_id as $value) {
        $this->agency_review_model->insert(array('agency_id' => $value, 'broker_id' => $this->user_arr['broker_id'], 'action' => 2, 'status' => 0, 'create_time' => time()));
      }
      echo '{"status":"success","msg":"删除门店成功,请等待审核"}';
    } else {
      $this->redirect_permission_none();
      exit;
    }
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

  /**
   * 根据关键词获取门店信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_agency_info_by_kw()
  {
    //当前经纪人等级
    $role_level = intval($this->user_arr['role_level']);
    //当前经纪人所在公司
    $company_id = intval($this->user_arr['company_id']);
    //当前经纪人所在门店
    $agency_id = intval($this->user_arr['agency_id']);
    //根据角色，决定搜索范围
    $search_arr = array(
      'role_level' => $role_level,
      'company_id' => $company_id,
      'agency_id' => $agency_id
    );

    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('agency_model');
    $select_fields = array('id', 'name');
    $this->agency_model->set_select_fields($select_fields);
    $cmt_info = $this->agency_model->get_agency_info_by_kw($keyword, $search_arr, 10);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
    }

    if (empty($cmt_info) || empty($search_arr['role_level']) || empty($search_arr['company_id']) || empty($search_arr['agency_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无门店';
    }

    echo json_encode($cmt_info);
  }

  /**
   * 根据关键词获取经纪人信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_broker_info_by_kw()
  {
    //当前所在门店的id
    $agency_id = $this->input->get('agency_id', TRUE);
    //根据角色，决定搜索范围
    $search_arr = array(
      'agency_id' => $agency_id
    );

    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('agency_model');
    $select_fields = array('broker_id', 'truename as name', 'phone');
    $this->agency_model->set_select_fields($select_fields);
    $cmt_info = $this->agency_model->get_broker_info_by_kw($keyword, $search_arr, 5);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
    }

    if (empty($cmt_info) || empty($search_arr['agency_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无经纪人';
    }

    echo json_encode($cmt_info);
  }

  /**
   * 根据关键词获取经纪人信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_broker_info_by_kw_2()
  {
    $agency_arr = array();
    //根据权限获取公司名下的所有子公司信息
    $level = intval($this->user_arr['role_level']);
    $company_id = intval($this->user_arr['company_id']);
    $agency_id = intval($this->user_arr['agency_id']);
    if (in_array($level, array(1, 2, 3, 4))) {
      //总经理，副总经理。看到公司下所有的片区、门店
      $company_info = $this->agency_model->get_children_by_company_id($company_id);
      if (is_full_array($company_info)) {
        foreach ($company_info as $key => $value) {
          $agency_arr[] = $value['id'];
        }
      }
    } else if (in_array($level, array(5))) {
      //区域经理。只能看到其所在的一级门店和二级门店
      $this_agency_info = $this->agency_model->get_by_id_one($agency_id);
      $agency_arr[] = $this_agency_info[0]['id'];

      $this_father_agency_id = $this_agency_info[0]['agency_id'];
      if (isset($this_father_agency_id) && $this_father_agency_id > 0) {
        //当前门店为二级门店,找到一级门店
        $father_agency_data = $this->agency_model->get_by_id_one($this_father_agency_id);
        if (is_full_array($father_agency_data)) {
          $agency_arr[] = $father_agency_data[0]['id'];
        }
      } else {
        //当前门店为一级门店,找到二级门店
        $where_cond = array('agency_id' => $this_agency_info[0]['id']);
        $next_agency_data = $this->agency_model->get_all_by($where_cond);
        if (is_full_array($next_agency_data)) {
          $agency_arr[] = $next_agency_data[0]['id'];
        }
      }
    } else if (in_array($level, array(6, 7))) {
      //店长,店务秘书，只能看到当前门店
      $this_agency_info = $this->agency_model->get_by_id_one($agency_id);
      $agency_arr[] = $this_agency_info[0]['id'];
    }

    //根据当前经纪人角色，判断搜索mend范围
    $agency_id = $this->input->get('agency_id', TRUE);
    //根据角色，决定搜索范围
    $search_arr = array(
      'agency_id' => $agency_arr
    );

    $keyword = $this->input->get('keyword', TRUE);
    $this->load->model('agency_model');
    $select_fields = array('broker_id', 'truename as name', 'phone', 'agency_id');
    $this->agency_model->set_select_fields($select_fields);
    $cmt_info = $this->agency_model->get_broker_info_by_kw($keyword, $search_arr, 5);
    foreach ($cmt_info as $key => $value) {
      $cmt_info[$key]['label'] = $value['name'];
    }

    if (empty($cmt_info) || empty($search_arr['agency_id'])) {
      $cmt_info[0]['id'] = 0;
      $cmt_info[0]['label'] = '暂无经纪人';
    }

    echo json_encode($cmt_info);
  }

  /**
   * 根据关键词获取门店信息
   *
   * @access public
   * @param  void
   * @return json
   */
  public function get_agencyinfo_by_kw()
  {
    $keyword = $this->input->get('keyword', TRUE);
    $select_fields = array('id', 'name');
    $this->agency_model->set_select_fields($select_fields);
    $agencyinfo = $this->agency_model->auto_agencyname($keyword, 10);

    if (empty($agencyinfo)) {
      $agencyinfo[0]['id'] = 0;
      $agencyinfo[0]['name'] = '暂无该门店”';
    }
    echo json_encode($agencyinfo);
  }
}

/* End of file agency.php */
/* Location: ./application/mls/controllers/agency.php */
