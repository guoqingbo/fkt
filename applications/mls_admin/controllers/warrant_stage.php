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
class Warrant_stage extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('warrant_model');
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
}
