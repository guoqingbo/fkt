<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 新房评论管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class New_house_comment extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->helper('page_helper');
    $this->load->model('comment_manage_model');
    $this->load->helper('user_helper');
  }

  public function index($id = "", $status = "")
  {
    $data = array();

    $data['title'] = "新房评论管理";
    $data['conf_where'] = 'index';
    if ($status != "" && $id != "") {
      $this->comment_manage_model->update_status_by($id, $status, $tbl = "new_house_talk");
    }

    $search = $this->input->post('search', true);

    $pg = $this->input->post('pg', true);
    if ($pg == "") {
      $pg = 1;
    }
    if ($search) {
      $id = $this->input->post('id', true);
      $nickname = $this->input->post('nickname', true);
      $hid = $this->input->post('hid', true);
      $is_from = $this->input->post('is_from', true);
      $score = $this->input->post('score', true);
      $status = $this->input->post('status', true);

      $where = "";
      if ($id !== "") {
        $where .= " and id = " . $id;
      }
      if ($nickname !== "") {
        $where .= " and nickname = " . $nickname;
      }
      if ($hid !== "") {
        $where .= " and hid = " . $hid;
      }
      if ($is_from !== "") {
        $where .= " and is_from = " . $is_from;
      }
      if ($score !== "") {
        $where .= " and score = " . $score;
      }
      if ($status !== "") {
        $where .= " and status = " . $status;
      }

      //清除条件头尾多余的“AND”和空格
      $where = trim($where);
      $where = trim($where, "and");
      $where = trim($where);


    }
    //分页开始
    $data['sold_num'] = $this->comment_manage_model->get_talk_num_by($where, $tbl = "new_house_talk");

    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['sold_num'] ? ceil($data['sold_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($pg) ? intval($pg) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['list'] = $this->comment_manage_model->get_list_by_cond($where, $tbl = 'new_house_talk', $data['offset'], $data['pagesize'], 'status', 'ASC');

    $this->load->view("new_house_comment/index", $data);
  }

  public function del($id)
  {
    $data = array();
    $result = $this->comment_manage_model->del_by($id, $tbl = "new_house_talk");
    $data['result'] = $result;
    $this->load->view("new_house_comment/del", $data);
  }
}


