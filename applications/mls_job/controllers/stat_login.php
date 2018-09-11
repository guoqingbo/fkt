<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 登录量控制器类
 *
 * @package         mls_job
 * @subpackage      controllers
 * @category        controllers
 * @author          sun
 */
class Stat_login extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'stat_login API for MLS.');
  }

  /*
     *  当日登录量
     */
  public function start()
  {
    $city = $this->input->get('city');
    $this->set_city($city);
    $this->load->model('stat_login_model');
    //判断昨天登录量是否已存库
    $num = $this->stat_login_model->count_by('ymd = "' . date("Y-m-d", strtotime("-1 day")) . '"');
    if ($num) {
      $this->result(0, '数据已录入过');
    } else {
      //获取昨天的登录量
      $y_login_count = $this->stat_login_model->login_log_count_by();
      //录入库
      $insert_id = $this->stat_login_model->set_stat_login(array('num' => $y_login_count, 'ymd' => date('Y-m-d', strtotime("-1 day"))));
      if ($insert_id) {
        $this->result(1, '操作成功');
      } else {
        $this->result(0, '操作失败');
      }
    }
  }
}
/* End of file stat_login.php */
/* Location: ./application/mls_job/controllers/stat_login.php */
