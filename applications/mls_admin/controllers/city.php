<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class City extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('city_model');
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    //筛选条件
    $data['where_cond'] = array();
    //分页开始
    $data['city_num'] = $this->city_model->get_city_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['city_num'] ? ceil($data['city_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['city_list'] = $this->city_model->get_city($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('city/index', $data);
  }

  /**
   * 添加城市
   */
  public function add()
  {
    $data['title'] = '添加城市';
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'province' => trim($this->input->post('province')),
        'cityname' => trim($this->input->post('cityname')),
        'spell' => trim($this->input->post('spell')),
        'order' => intval($this->input->post('order')),
        'status' => intval($this->input->post('status')),
      );
      if (!empty($paramArray['province']) && !empty($paramArray['cityname']) && !empty($paramArray['spell']) && !empty($paramArray['status'])) {
        $addResult = $this->city_model->add_city($paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('city/add', $data);
  }

  /**
   * 修改城市
   */
  public function modify($id)
  {
    $data['title'] = '修改城市';
    $data['conf_where'] = 'index';
    $data['city'] = $this->city_model->get_all_city();
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $city_data = $this->city_model->get_city_by_id($id);
      if (!empty($city_data[0]) && is_array($city_data[0])) {
        $data['city_data'] = $city_data[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'province' => trim($this->input->post('province')),
        'cityname' => trim($this->input->post('cityname')),
        'spell' => trim($this->input->post('spell')),
        'order' => intval($this->input->post('order')),
        'status' => intval($this->input->post('status')),
      );
      if (!empty($paramArray['province']) && !empty($paramArray['cityname']) && !empty($paramArray['spell']) && !empty($paramArray['status'])) {
        $modifyResult = $this->city_model->modify_city($id, $paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('city/modify', $data);
  }

  /**
   * 修改城市状态
   */
  public function change_status($id, $status)
  {
    $data['title'] = '修改状态';
    $modifyResult = '';
    if (!empty($id) && !empty($status)) {
      $paramArray = array(
        'status' => intval($status),
      );
      $modifyResult = $this->city_model->modify_city(intval($id), $paramArray);
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('city/change_status', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
