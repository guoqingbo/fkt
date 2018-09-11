<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 统计控制器
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Advert extends MY_Controller
{

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('advert_app_manage_model');
  }

  public function get_new()
  {
    $this->advert_app_manage_model->set_tbl(2);
    $news = $this->advert_app_manage_model->get_one_by();
    if (is_full_array($news)) {
      $data = array('title' => $news['title'], 'content' => $news['new_content'],
        'update_time' => $news['update_time']);
    } else {
      $data = array();
    }
    $this->result(1, '查询成功', $data);
  }
}
/* End of file advert.php */
/* Location: ./application/mls/controllers/advert.php */
