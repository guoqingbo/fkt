<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 群发站点管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Mass_site extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('mass_site_model');
    $this->load->helper('user_helper');
  }

  /**
   * 群发站点列表页面
   */
  public function index()
  {
    $data['title'] = '群发站点设置管理';
    $data['conf_where'] = 'index';
    //分页开始
    $data['mass_site_num'] = $this->mass_site_model->get_num();
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['mass_site_num'] ? ceil($data['mass_site_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['mass_site'] = $this->mass_site_model->get_mass_site($data['offset'], $data['pagesize']);
    $this->load->view('mass_site/index', $data);
  }

  /**
   * 添加群发站点
   */
  public function add()
  {
    $data['title'] = '添加群发站点';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'alias' => trim($this->input->post('alias')),
        'intro' => trim($this->input->post('intro')),
        'url' => trim($this->input->post('url')),
        'is_fix' => trim($this->input->post('is_fix')),
        'is_auth' => trim($this->input->post('is_auth')),
        'is_upic' => trim($this->input->post('is_upic')),
        'is_refresh' => trim($this->input->post('is_refresh'))
      );
      if (!empty($paramArray['name']) && !empty($paramArray['url']) && !empty($paramArray['alias'])) {
        $addResult = $this->mass_site_model->add($paramArray);
      } else {
        $data['mess_error'] = '网站名称 / 网站地址 / 网站别名 不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('mass_site/add', $data);
  }

  /**
   * 修改群发站点
   */
  public function modify($id)
  {
    $data['title'] = '修改群发站点';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if (!empty($id)) {
      $mass_siteData = $this->mass_site_model->getinfo_byid($id);
      if (!empty($mass_siteData[0]) && is_array($mass_siteData[0])) {
        $data['mass_site'] = $mass_siteData[0];
      }
    }
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'name' => trim($this->input->post('name')),
        'alias' => trim($this->input->post('alias')),
        'intro' => trim($this->input->post('intro')),
        'url' => trim($this->input->post('url')),
        'is_fix' => trim($this->input->post('is_fix')),
        'is_auth' => trim($this->input->post('is_auth')),
        'is_upic' => trim($this->input->post('is_upic')),
        'is_refresh' => trim($this->input->post('is_refresh'))
      );
      if (!empty($paramArray['name']) && !empty($paramArray['url']) && !empty($paramArray['alias'])) {
        $modifyResult = $this->mass_site_model->modify($id, $paramArray);
      } else {
        $data['mess_error'] = '网站名称 / 网站地址 / 网站别名 不能为空';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('mass_site/modify', $data);
  }

  /**
   * 删除群发站点
   */
  public function del($id)
  {
    $data['title'] = '删除群发站点';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $mass_siteData = $this->mass_site_model->del_mass_site($id);
      if ($mass_siteData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('mass_site/del', $data);
  }

  /**
   * 启用群发站点
   */
  public function open($id)
  {
    $data['title'] = '启用群发站点';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $mass_siteData = $this->mass_site_model->open($id);
      if ($mass_siteData == 1) {
        $delResult = 1;//启用成功
      } else {
        $delResult = 0;//启用失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('mass_site/open', $data);
  }

  /**
   * 关闭群发站点
   */
  public function close($id)
  {
    $data['title'] = '关闭群发站点';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $mass_siteData = $this->mass_site_model->close($id);
      if ($mass_siteData == 1) {
        $delResult = 1;//关闭成功
      } else {
        $delResult = 0;//关闭失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('mass_site/close', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
