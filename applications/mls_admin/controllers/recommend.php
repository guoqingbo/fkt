<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 系统默认角色
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Recommend extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->model('homepage_recommend_model');

  }

  public function index()
  {
    $data = array();
    $data['title'] = "设置首页推荐房源";

    //列表信息
    $list = $this->homepage_recommend_model->get_all();
    $data['list'] = $list;

    $this->load->view('recommend/index', $data);
  }

  public function edit($id)
  {
    $data = array();
    $data['title'] = "修改推荐房源编号";
    $setinfo = "";
    $list = $this->homepage_recommend_model->get_by_id($id);
    $row_ids = explode(',', $list['row_ids']);
    $list['row_ids'] = $row_ids;
    $data['list'] = $list;

    $sid = $this->input->post('id', true);
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "edit") {
      foreach ($row_ids as $key => $val) {
        $row_id[] = $this->input->post('row_ids' . $key, true);
      }
      $row_ids = implode(",", $row_id);
      $updatearray = array("row_ids" => $row_ids);
      $setinfo = $this->homepage_recommend_model->update_by_id($updatearray, $sid);
    }

    $data['setinfo'] = $setinfo;
    $this->load->view('recommend/edit', $data);
  }
}

