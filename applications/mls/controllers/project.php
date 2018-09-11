<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 登录控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Project extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  //登录页
  public function fang100()
  {
    $data = array();
    $data['page_title'] = '英雄联盟';
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'css/v1.0/home.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    $this->view('project/fang/sync_house/index', $data);
  }

  //妇女节活动
  public function women()
  {
    $data = array();
    $data['page_title'] = '英雄联盟';
    $data['css'] = load_css('mls/css/v1.0/base.css,'
      . 'css/v1.0/home.css,'
      . 'mls/css/v1.0/house_manage.css,'
      . 'mls/css/v1.0/myStyle.css');
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    $this->view('project/fang/sync_house/women', $data);
  }

}
/* End of file Cooperate_project_lol.php */
/* Location: ./application/mls/controllers/Cooperate_project_lol.php */
