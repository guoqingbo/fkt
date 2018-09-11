<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ajax Class
 *
 * ajax管理
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Ajax extends MY_Controller
{
  /**
   * 解析函数
   *
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('ajax_model');
  }

  //@todo
  public function index()
  {
    echo '测试专用';
  }

  /**
   * 小区名称模糊获取小区列表信息
   */
  public function find_like_block_list()
  {
    $this->load->model('community_base_model');
    $blockname = $this->input->post('blockname');
    $blockname = trim(urldecode($blockname));

    $block_list = $this->community_base_model->auto_cmtname($blockname);
    echo $this->ajax_model->array_to_json($block_list);
  }

}


/* End of file ajax.php */
/* Location: ./application/mls/controllers/ajax.php */
