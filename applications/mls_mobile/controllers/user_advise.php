<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 好房看看 Class
 *
 * 采集控制器
 *
 * @package      mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class User_advise extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('user_advise_model');
    error_reporting(E_ALL || ~E_NOTICE);
  }


  /**
   * 用户反馈建议
   * @access public
   * @return void
   * date 2015-03-17
   * author angel_in_us
   */
  public function add_advise()
  {
    $telno = $this->input->post('telno');
    $advice = $this->input->post('advice');
    if (!empty($telno) && !empty($advice)) {
      $data = array(
        'telno' => $telno,
        'advice' => urldecode($advice),
        'create_time' => time()
      );
      $result = $this->user_advise_model->add_advice($data);
      if ($result) {
        $this->result(1, '用户反馈成功！', $data);
      } else {
        $this->result(0, '反馈失败，请联系技术负责人！');
      }
    } else {
      $this->result(0, '参数不合法，反馈失败，请联系技术负责人！');
    }
  }
  //echo "<pre>"; print_r($broker_info);die;
}

/* End of file user_advise.php */
/* Location: ./application/mls/controllers/user_advise.php */
