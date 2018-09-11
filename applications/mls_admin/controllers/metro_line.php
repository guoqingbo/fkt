<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Metro_line extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('metro_model');
    $this->load->model('city_model');
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = '地铁线路列表';
    $data['conf_where'] = 'index';
    //筛选条件
    $data['where_cond'] = array();
    //分页开始
    $data['metro_num'] = $this->metro_model->get_metro_line_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['metro_num'] ? ceil($data['metro_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['metro_line_list'] = $this->metro_model->get_metro_line($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('metro/metro_line', $data);
  }

  /**
   * 添加地铁线路
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
    $data['title'] = '添加地铁线路';
    //$data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'line_name' => trim($this->input->post('line_name')),
        'city_id' => $city_id,
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
      );

      if (!empty($paramArray['line_name']) && !empty($paramArray['is_show'])) {
        $addResult = $this->metro_model->add_metro_line($paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('metro/line_add', $data);
  }

  /**
   * 修改地铁线路
   */
  public function modify($id)
  {
    $data['title'] = '修改地铁线路';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $metro_line_data = $this->metro_model->get_metro_line_by_id($id);
      //echo '<pre>';print_r($metro_line_data);die;
      if (!empty($metro_line_data[0]) && is_array($metro_line_data[0])) {
        $data['city_data'] = $metro_line_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'line_name' => trim($this->input->post('line_name')),
        'order' => intval($this->input->post('order')),
        'is_show' => intval($this->input->post('is_show')),
      );
      if (!empty($paramArray['line_name'])) {
        $modifyResult = $this->metro_model->modify_metro_line($id, $paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('metro/line_modify', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
