<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 推送消息控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Push_message extends MY_Controller
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

  public function send_test()
  {
    $this->load->model('push_func_model');
    $this->push_func_model->send(1, 1, 7, 11, array('c_id' => 10));
    //$this->push_func_model->send(1, 1, 7, 36, array('cid' => 10));
    //$this->push_func_model->send(2, 1, 0, 0, array('cid' => 10), '广播一条新闻，超哥爱美女');
  }
}
/* End of file Demo.php */
/* Location: ./application/mls_mobile/controllers/Push_message.php */
