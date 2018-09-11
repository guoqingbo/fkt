<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of collect_mass_notice
 *
 * @author 365
 */
class Collect_mass_notice extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('collect_mass_notice_base_model');
  }

  /**
   * 采集群发公告列表
   */
  public function index()
  {
    $data['title'] = "采集群发公告列表";
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    date_default_timezone_set('PRC');
    if ($this->input->post('start_time') && $this->input->post('end_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/collect_mass_notice/index';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('createtime >=' => $start_time, "createtime <=" => $end_time);
      }
    }
    //分页开始
    $data['notice_num'] = $this->collect_mass_notice_base_model->count_get_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['notice_num'] ? ceil($data['notice_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['notice_msg'] = $this->collect_mass_notice_base_model->get_notice($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('collect_mass_notice/index', $data);
  }

  //设为重要消息 :wjy
  public function set_hard()
  {
    $id = $this->input->get('id');
    $find = $this->collect_mass_notice_base_model->get_notice_byid($id);
    $data['hard'] = $find['hard'] == 1 ? 0 : 1;
    $where = array('id' => $id);
    echo $this->collect_mass_notice_base_model->update_notice($where, $data);
  }

  //设为最新消息 :wjy
  public function set_news()
  {
    $id = $this->input->get('id');
    $find = $this->collect_mass_notice_base_model->get_notice_byid($id);
    $data['news'] = $find['hard'] == 1 ? 0 : 1;
    $where = array('id' => $id);
    echo $this->collect_mass_notice_base_model->update_notice($where, $data);
  }

  /**
   * 发布公告
   */
  public function add()
  {
    $data['title'] = "发布采集群发公告";
    //获取城市
    $res = $this->collect_mass_notice_base_model->select_city();
    $data['city'] = $res;
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $title = $this->input->post('title');
      $content = $this->input->post('content');
      $show = $this->input->post('show');
      $city = $this->input->post('city');
      if (!empty($title) && !empty($content) && !empty($city)) {
        $cityname = implode(',', $city);
        $where = array('title' => $title, 'content' => $content, 'createtime' => time(), 'city' => $cityname, 'show' => $show);
        $data['addResult'] = $this->collect_mass_notice_base_model->add_notice($where, $database = 'db');
      } else {
        $data['notice_error'] = '标题/消息内容/城市不能为空';
      }
    }
    $this->load->view('collect_mass_notice/add', $data);
  }

  /**
   * 修改公告
   */
  public function modify($id)
  {
    $data['title'] = "修改采集群发公告";
    //获取城市
    $res = $this->collect_mass_notice_base_model->select_city();
    $data['city'] = $res;
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['notice_msg'] = $this->collect_mass_notice_base_model->get_notice_byid($id);
    }
    if ('modify' == $submit_flag) {
      $title = $this->input->post('title');
      $content = $this->input->post('content');
      $show = $this->input->post('show');
      $city = $this->input->post('city');
      if (!empty($title) && !empty($content) && !empty($city)) {
        $cityname = implode(',', $city);
        $where = array('id' => $id);
        $update = array('title' => $title, 'content' => $content, 'updatetime' => time(), 'city' => $cityname, 'show' => $show);
        $data['modifyResult'] = $this->collect_mass_notice_base_model->update_notice($where, $update);
      } else {
        $data['notice_error'] = '标题/消息内容/城市不能为空';
      }
    }
    $this->load->view('collect_mass_notice/modify', $data);
  }

  /**
   * 删除公告
   */
  public function del($id)
  {
    $where = array('id' => $id);
    $delResult = $this->collect_mass_notice_base_model->del_notice($where, $database = 'db');
    if ($delResult) {
      echo "<script>alert('删除成功！');location.href='" . MLS_ADMIN_URL . "/collect_mass_notice/index';</script>";
    } else {
      echo "<script>alert('删除失败！');location.href='" . MLS_ADMIN_URL . "/collect_mass_notice/index';</script>";
    }
  }

  /**
   * 设置采集轮播
   */
  public function set_collect()
  {
    $id = $this->input->get('id');
    $collect_type = $this->input->get('collect_type');
    $collect_type = $collect_type == 0 ? 1 : 0;
    $where = array('id' => $id);
    $update = array('collect_type' => $collect_type);
    echo $this->collect_mass_notice_base_model->update_notice($where, $update);
  }

  /**
   * 设置群发轮播
   */
  public function set_mass()
  {
    $id = $this->input->get('id');
    $mass_type = $this->input->get('mass_type');
    $mass_type = $mass_type == 0 ? 1 : 0;
    $where = array('id' => $id);
    $update = array('mass_type' => $mass_type);
    echo $this->collect_mass_notice_base_model->update_notice($where, $update);
  }
}
