<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 首页新房推荐
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Mls_apply extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->model('mls_apply_model');

  }

  public function index()
  {
    $data = array();
    $data['title'] = $this->config->item('title');
    //列表信息
    $list = $this->mls_apply_model->get_all();
    $data['list'] = $list;
    $this->load->view('mls_apply/index', $data);
  }

  /**
   *添加管理数据
   */
  public function add()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'apply_name' => trim($this->input->post('apply_name')),
        'version' => trim($this->input->post('version')),
        'is_forced' => $this->input->post('is_forced'),
        'update_url' => trim($this->input->post('update_url')),
        'create_time' => time(),
        'version_type' => $this->input->post('version_type'),
        'update_time' => time(),
        'apply_size' => $this->input->post('apply_size'),
        'update_content' => $this->input->post('update_content'),
        'type' => 1
      );

      if (!empty($paramArray['apply_name']) && !empty($paramArray['version'])) {
        $addResult = $this->mls_apply_model->add_mls($paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('mls_apply/add', $data);
  }

  /**
   *修改管理信息
   */
  public function edit($id)
  {
    $data = array();
    $data['title'] = $this->config->item('title');
    $modifyResult = "";
    $list = $this->mls_apply_model->get_by_id($id);
    $data['list'] = $list[0];
    //echo '<pre>';print_r($data['list']);
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "edit") {
      $updatearray = array(
        'apply_name' => trim($this->input->post('apply_name')),
        'version' => trim($this->input->post('version')),
        'is_forced' => $this->input->post('is_forced'),
        'update_url' => trim($this->input->post('update_url')),
        'version_type' => $this->input->post('version_type'),
        'update_time' => time(),
        'apply_size' => $this->input->post('apply_size'),
        'update_content' => $this->input->post('update_content'),
        'type' => 1
      );
      if (!empty($updatearray['apply_name']) && !empty($updatearray['version'])) {
        $modifyResult = $this->mls_apply_model->update_by_id($id, $updatearray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }

    $data['modifyResult'] = $modifyResult;
    $this->load->view('mls_apply/modify', $data);
  }
}

