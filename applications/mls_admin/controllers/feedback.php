<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Feedback extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('feedback_model');
    $this->load->model('city_model');
  }

  /**
   * 列表页面
   */
  public function index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    $data['all_city_data'] = $this->city_model->get_all_city();
    //筛选条件
    $data['where_cond'] = array();
    $city_id = $this->input->post('city_id');
    if (!empty($city_id)) {
      $data['where_cond']['city_id'] = $city_id;
    }
    //分页开始
    $data['feedback_num'] = $this->feedback_model->get_feedback_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['feedback_num'] ? ceil($data['feedback_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $feedback_list = $this->feedback_model->get_feedback($data['where_cond'], $data['offset'], $data['pagesize']);
    //根据城市id，获取城市名
    foreach ($feedback_list as $k => $v) {
      $city_name = '';
      if (!empty($v['city_id'])) {
        $city_data = $this->city_model->get_city_by_id($v['city_id']);
        $city_name = $city_data[0]['cityname'];
      }
      $feedback_list[$k]['city_name'] = $city_name;
    }
    $data['feedback_list'] = $feedback_list;
    $this->load->view('feedback/index', $data);
  }

  /**
   * 删除
   */
  public function del($id)
  {
    $data['title'] = '删除意见反馈';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $userData = $this->feedback_model->del_feedback($id);
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('feedback/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
