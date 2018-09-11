<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Street_xf extends My_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {

    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper("page_helper");
    //加载区属模型类
    $this->load->model("district_xf_model");
    $this->load->model("city_model");
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = '新房板块管理';
    $data['conf_where'] = 'index';
    //筛选条件
    $data['where_cond'] = array();
    //分页开始
    $data['district_num'] = $this->district_xf_model->get_street_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['street_list'] = $this->district_xf_model->get_street($data['where_cond'], $data['offset'], $data['pagesize']);

    $this->load->view("street_xf/index", $data);
  }

  /**
   * 添加城市
   */
  public function add()
  {
    if (isset($_SESSION[WEB_AUTH]["city"]) && !empty($_SESSION[WEB_AUTH]["city"])) {
      $city_spell = $_SESSION[WEB_AUTH]["city"];
      //根据城市拼音，获得城市id
      $city_id_arr = $this->city_model->get_city_by_spell($city_spell);
      $city_id = $city_id_arr['id'];
    } else {
      $city_id = 0;
    }
    $data['title'] = '添加新房板块';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'streetname' => trim($this->input->post('streetname')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
      );

      if (!empty($paramArray['streetname']) && !empty($paramArray['is_show'])) {
        $addResult = $this->district_xf_model->add_street($paramArray);

      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('street_xf/add', $data);
  }

  /**
   * 修改区属
   */
  public function modify($id)
  {
    $data['title'] = '修改板块';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $district_data = $this->district_xf_model->get_street_by_id($id);
      if (!empty($district_data[0]) && is_array($district_data[0])) {
        $data['city_data'] = $district_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'streetname' => trim($this->input->post('streetname')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
      );
      if (!empty($paramArray['streetname'])) {
        $modifyResult = $this->district_xf_model->modify_street($id, $paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('street_xf/modify', $data);
  }

}

?>
