<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 首页新房推荐
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Fang100_apply extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //查看所有的模块
    $this->load->model('fang100_apply_model');

  }

  public function index()
  {
    $data = array();
    $data['title'] = $this->config->item('title') . "管理";

    //列表信息
    $list = $this->fang100_apply_model->get_all();
    $data['list'] = $list;
    $this->load->view('fang100_apply/index', $data);
  }

  /**
   *修改应用管理信息
   */
  public function add()
  {
    $data['title'] = '修改' . $data['software_name'] = $this->config->item('title') . '应用管理信息';
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
        'update_time' => time(),
        'apply_size' => $this->input->post('apply_size'),
        'version_type' => $this->input->post('version_type'),
        'update_content' => trim($this->input->post('update_content')),
        'type' => 2
      );

      if (!empty($paramArray['apply_name']) && !empty($paramArray['version'])) {
        $addResult = $this->fang100_apply_model->add_mls($paramArray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('fang100_apply/add', $data);
  }

  /**
   *修改管理信息
   */
  public function edit($id)
  {
    $data = array();
    $data['title'] = "修改" . $data['software_name'] = $this->config->item('title') . "应用管理信息";
    $modifyResult = "";
    $list = $this->fang100_apply_model->get_by_id($id);
    $data['list'] = $list[0];
    $submit_flag = $this->input->post('submit_flag', true);
    if ($submit_flag == "edit") {
      $updatearray = array(
        'apply_name' => trim($this->input->post('apply_name')),
        'version' => trim($this->input->post('version')),
        'is_forced' => $this->input->post('is_forced'),
        'update_time' => time(),
        'update_url' => trim($this->input->post('update_url')),
        'apply_size' => $this->input->post('apply_size'),
        'version_type' => $this->input->post('version_type'),
        'update_content' => trim($this->input->post('update_content')),
        'type' => 2
      );
      if (!empty($updatearray['apply_name']) && !empty($updatearray['version'])) {
        $modifyResult = $this->fang100_apply_model->update_by_id($id, $updatearray);
      } else {
        $data['mess_error'] = '带*为必填字段';
      }
    }

    $data['modifyResult'] = $modifyResult;
    $this->load->view('fang100_apply/modify', $data);
  }
}
