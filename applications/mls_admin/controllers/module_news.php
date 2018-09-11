<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 资讯页面管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Module_news extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('module_news_base_model');
  }

  public function lists()
  {
    $data['title'] = "资讯页面管理";
    $data['conf_where'] = 'index';
    $data['where_cond'] = array();
    if ($this->input->post('start_time') && $this->input->post('end_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/collect_mass_news/index';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('createtime >=' => $start_time, "createtime <=" => $end_time);
      }
    }
    //分页开始
    $data['news_num'] = $this->module_news_base_model->count_get_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['news_num'] ? ceil($data['news_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['news_msg'] = $this->module_news_base_model->get_news($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('module_news/lists', $data);
  }

  /**
   * 发布资讯
   */
  public function add()
  {
    $data['title'] = "发布资讯";
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $title = $this->input->post('title');
      $content = $this->input->post('content');
      if (!empty($title) && !empty($content)) {
        $where = array('title' => $title, 'content' => $content, 'createtime' => time(), 'updatetime' => time());
        echo $this->module_news_base_model->add_news($where, $database = 'db_city');
      } else {
        echo -1;
      }
    } else {
      $this->load->view('module_news/add', $data);
    }
  }

  /**
   * 修改资讯
   */
  public function modify($id)
  {
    $data['title'] = "修改资讯";
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['news_msg'] = $this->module_news_base_model->get_news_byid($id);
    }
    if ('modify' == $submit_flag) {
      $title = $this->input->post('title');
      $content = $this->input->post('content');
      if (!empty($title) && !empty($content)) {
        $where = array('id' => $id);
        $update = array('title' => $title, 'content' => $content, 'updatetime' => time());
        echo $this->module_news_base_model->update_news($where, $update);
      } else {
        echo -2;
      }
    } else {
      $this->load->view('module_news/modify', $data);
    }
  }

  /**
   * 删除资讯
   */
  public function del($id)
  {
    $where = array('id' => $id);
    $delResult = $this->module_news_base_model->del_news($where, $database = 'db_city');
    if ($delResult) {
      echo "<script>alert('删除成功！');location.href='" . MLS_ADMIN_URL . "/module_news/lists';</script>";
    } else {
      echo "<script>alert('删除失败！');location.href='" . MLS_ADMIN_URL . "/module_news/lists';</script>";
    }
  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');

    $imagesize = getimagesize($_FILES[$filename]['tmp_name']);
    $this->pic_model->set_image_width($imagesize[0]);
    $this->pic_model->set_image_height($imagesize[1]);
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
//        $fileurl = str_replace("/thumb","",$fileurl);
    $fileurl = changepic($fileurl);
    echo "<script>window.parent.changePic('" . $fileurl . "')</script>";
  }
}
