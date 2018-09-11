<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台合同部分
 *
 * @package    mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      kang
 * Date: 15-2-9
 * Time: 下午1:12
 */
class Contract extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('contract_model');
  }

  /** 合同权证流程步骤设置 */
  public function index()
  {
    //模板使用数据
    $data = array();

    $data['title'] = "权证流程步骤设置";
    $data['conf_where'] = 'index';

    //获取所有步骤
    $data['stages'] = $this->contract_model->get_all_stage();

    $this->load->view('contract/index', $data);
  }

  /** 添加数据保存 */
  public function add_data()
  {
    //模板使用数据
    $data = array();

    $data['stage_name'] = $this->input->post('stage_name');
    //添加步骤
    $rs = $this->contract_model->insert_data('warrant_all_stage', $data);
    //print_r($rs);exit;
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);

  }

  /** 保存修改 */
  public function save_data()
  {
    $data = array();
    $json_data = array();
    $id = $this->input->post('id');
    $where = "id = " . $id;

    $data['stage_name'] = $this->input->post('stage_name');
    //修改步骤
    $rs = $this->contract_model->modify_data('warrant_all_stage', $data, $where);
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }

  /** 删除 */
  public function del_data()
  {
    $data = array();
    $json_data = array();
    $id = $this->input->post('id');
    $where = "id = " . $id;
    //修改步骤
    $rs = $this->contract_model->delete_data('warrant_all_stage', $where);
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }


  /** 模板 */
  public function temp()
  {
    //模板使用数据
    $data = array();

    $data['title'] = "默认模板设置";
    $data['conf_where'] = 'index';
    //获取流程阶段配置信息
    $data['stage_conf'] = $this->contract_model->get_stage_conf();
    //读取模板表中的默认模板
    $sys_temp = $this->contract_model->get_default_temps();

    $data['stage'] = $this->contract_model->get_all_stage();
    //根据模板的ID读取步骤
    $sys_temp['steps'] = $this->contract_model->get_step_by_template_id($sys_temp['id']);

    if (is_full_array($sys_temp['steps'])) {
      foreach ($sys_temp['steps'] as $key => $val) {
        $arr = array();
        $stage_name = explode(',', $val['stage_id']);
        foreach ($stage_name as $k => $v) {
          $arr[] = $data['stage'][$v]['stage_name'];
          $sys_temp['steps'][$key]['stage_name'] = implode('，', $arr);
        }
      }
    }
    $data['sys_temp'] = $sys_temp;

    $this->load->view('contract/temp_manage', $data);
  }

  /** 添加步骤 */
  public function add_step()
  {
    //模板使用数据
    $data = array();
    //读取模板表中的默认模板
    $sys_temp = $this->contract_model->get_default_temps();
    $data['template_id'] = $sys_temp['id'];
    //根据模板的ID读取步骤
    $where = "template_id = " . $sys_temp['id'];
    $step_count = $this->contract_model->get_count('warrant_template_step', $where);
    //设置要添加的步骤（最大步骤+1）
    $data['next_step'] = $step_count + 1;

    //获取流程阶段配置信息
    $data['stage_conf'] = $this->contract_model->get_stage_conf();

    echo json_encode($data);
  }

  /** 保存添加步骤 */
  public function save_add_step()
  {
    $step = $this->input->post('step');
    $where = "template_id = 1";
    $step_count = $this->contract_model->get_count('warrant_template_step', $where);
    $step_data = array('template_id' => $this->input->post('template_id'),
      'stage_id' => implode(',', $step),
      'step_id' => $step_count + 1
    );
    $rs = $this->contract_model->insert_data('warrant_template_step', $step_data);
    if ($rs) {
      if ($step_count == 0) {
        $this->contract_model->modify_data('warrant_template', array('is_addstep' => 1), array('id' => 1));
      }
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }

  /** 修改步骤跳转 */
  public function modify_step()
  {
    $id = $this->input->post('id');
    $result = $this->contract_model->get_default_temp_by_id($id);
    $data['list'] = explode(',', $result['stage_id']);
    //获取流程阶段配置信息
    $stage_conf = $this->contract_model->get_stage_conf();

    $data['key'] = $stage_conf[$result['step_id']]['text'];
    echo json_encode($data);
  }

  /** 保存修改步骤 */
  public function save_modify_step()
  {
    $where = array('id' => $this->input->post('id'));
    $step_data['stage_id'] = implode(',', $this->input->post('step'));
    $rs = $this->contract_model->modify_data('warrant_template_step', $step_data, $where);
    if ($rs) {
      $data['status'] = 1;
    } else {
      $data['status'] = 0;
    }
    echo json_encode($data);
  }

  /** 删除默认模板步骤 */
  public function del_step()
  {
    $where = array('id' => $this->input->post('id'));
    $data = $this->contract_model->get_step_by_id($this->input->post('id'));//获取原来的数据
    //修改步骤
    $rs = $this->contract_model->delete_data('warrant_template_step', $where);
    if ($rs) {

      $total = $this->contract_model->get_count('warrant_template_step', array('template_id' => 1));
      //取出这步之后的步骤
      $steps = $this->contract_model->get_step_by_con(array('template_id' => 1, 'step_id >' => $data['step_id']));
      if (is_full_array($steps)) {
        foreach ($steps as $key => $val) {
          $this->contract_model->update_step_status(array('step_id' => $val['step_id'] - 1), $val['id']);
        }
      }
      if ($total == 0) {
        $this->contract_model->modify_data('warrant_template', array('is_addstep' => 0), array('id' => 1));
      }
      $data['status'] = 1;
    } else {
      $data['status'] = 0;
    }
    echo json_encode($data);
  }

}
