<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 公告
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Notice extends MY_Controller
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

  //安全说明
  public function system_safe()
  {
    $this->load->view('notice/system_safe');
  }

  //贷款
  public function mortgage()
  {
    $this->load->view('notice/mortgage');
  }

  //按揭
  public function anjie()
  {
    $this->load->view('notice/anjie_3');
  }

  //消息模板页
  public function module_news()
  {
    $id = $this->input->get('id');
    if ($id > 0) {
      $this->load->model('module_news_base_model');
      $news_msg = $this->module_news_base_model->get_news_byid($id);
      $data = array();
      $data['news_msg'] = $news_msg;
      $this->load->view('notice/module_news', $data);
    }
  }

  //积分活动
  public function credit_active()
  {
    $city = $this->user_arr['city_spell'];
    if ($city == 'cd') {
      $this->load->view('notice/credit_active_cd');
    } else if ($city == 'sz' || $city == 'km') {
      $this->load->view('notice/credit_active_km');
    }
  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
