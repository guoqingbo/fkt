<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 添加分门店
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Project_cooperate_lol_effect extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('project_cooperate_lol_model');
    $this->load->helper('page_helper');
    $this->load->model('derive_model');//excel导出类
  }

  //门店管理页
  public function index()
  {
    $data['title'] = '英雄联盟-抢占先机';
    $data['conf_where'] = 'index';
    //筛选条件

    $data['where_cond'] = '';
    $order_sn = $this->input->post('order_sn', TRUE);
    if ($order_sn) {
      $data['where_cond'] .= "order_sn like '%" . $order_sn . "%'";
    }
    //分页开始
    $data['num'] = $this->project_cooperate_lol_model->get_cooperate_effect_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['num'] ? ceil($data['num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $list = $this->project_cooperate_lol_model->get_cooperate_effect_list($data['where_cond'], $data['offset'], $data['pagesize']);
    foreach ($list as $key => $vo) {
      $city_arr = $this->project_cooperate_lol_model->get_city_by_broker_id($vo['broker_id']);
      $list[$key]['cityname'] = $city_arr['cityname'];
    }
    $data['list'] = $list;
    $data['param_array'] = array(
      'order_sn' => $order_sn
    );
    $this->load->view('project/cooperate/lol/effect_list', $data);
  }

  /**
   * 导出数据
   */
  public function derive()
  {
    ini_set('memory_limit', '-1');
    $data['effect'] = $this->project_cooperate_lol_model->geteffect(array(), array(), 0, 0, 'db');
    foreach ($data['effect'] as $key => $vo) {
      $city_arr = $this->project_cooperate_lol_model->get_city_by_broker_id($vo['broker_id']);
      $data['effect'][$key]['cityname'] = $city_arr['cityname'];
    }
    $this->derive_model->getExcel_effect($data);
  }
}

/* End of file Project_cooperate_lol_effect.php */
/* Location: ./application/mls_admin/controllers/Project_cooperate_lol_effect.php */
