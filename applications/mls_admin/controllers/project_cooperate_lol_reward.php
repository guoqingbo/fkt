<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 权限菜单管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class project_cooperate_lol_reward extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('project_cooperate_lol_model');
    $this->load->helper('user_helper');
  }

  /**
   * 奖品菜单列表页面
   */
  public function index()
  {
    $city = $_SESSION[WEB_AUTH]["city"];
    //模块
    //post参数
    $post_param = $this->input->post(NULL, TRUE);
    $data['post_param'] = $post_param;
    //分页开始
    $data['reward_num'] = $this->project_cooperate_lol_model->get_cooperate_reward_num($where);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['reward_num'] ? ceil($data['reward_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['reward_type'] = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $data['reward_list'] = $this->project_cooperate_lol_model->get_cooperate_reward_list($where, $data['offset'], $data['pagesize']);
    //print_r($data['check_house']);
    $this->load->view('project/cooperate/lol/reward_list', $data);
  }

  /**
   * 添加奖品
   */
  public function add()
  {
    $data['title'] = '奖品菜单';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'type' => intval($this->input->post('type')),
        'open_time' => trim($this->input->post('open_time')),
        'valid_flag' => intval($this->input->post('valid_flag')),
      );
      if (!empty($paramArray['type']) && !empty($paramArray['open_time'])) {
        $addResult = $this->project_cooperate_lol_model->add_cooperate_reward($paramArray);
      } else {
        $data['mess_error'] = '奖品类型/开放时间不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $data['reward_type'] = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $this->load->view('project/cooperate/lol/reward_add', $data);
  }

  /**
   * 修改奖品
   */
  public function modify($id)
  {
    $data['title'] = '修改奖品菜单';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $data['reward_details'] = $this->project_cooperate_lol_model->get_cooperate_reward_by_id($id);
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'id' => intval($this->input->post('id')),
        'type' => intval($this->input->post('type')),
        'open_time' => trim($this->input->post('open_time')),
        'valid_flag' => intval($this->input->post('valid_flag')),
      );
      if (!empty($paramArray['id']) && !empty($paramArray['type']) && !empty($paramArray['open_time'])) {
        $modifyResult = $this->project_cooperate_lol_model->update_cooperate_reward($id, $paramArray);
      } else {
        $data['mess_error'] = '奖品类型/开放时间不能为空';
      }
    }

    $data['modifyResult'] = $modifyResult;
    $data['reward_type'] = $this->project_cooperate_lol_model->get_cooperate_reward_type();
    $this->load->view('project/cooperate/lol/reward_modify', $data);
  }

  /**
   * 删除奖品
   */
  public function del($id)
  {
    $data['title'] = '删除奖品菜单';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $permission_tab_menuData = $this->project_cooperate_lol_model->delete_cooperate_reward($id);
      if ($permission_tab_menuData) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('project/cooperate/lol/reward_del', $data);
  }


}


/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
