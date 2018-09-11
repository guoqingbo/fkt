<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * house配置信息
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class house100_config extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('house100_config_model');
  }


  public function get_house_type()
  {

    $data = $this->house100_config_model->get_house_type();
    if (is_full_array($data)) {
      $this->result(1, '查询成功', $data);
    } else {
      $this->result(0, '查询失败', $data);
    }
  }

  public function get_rent_type()
  {
    $data = $this->house100_config_model->get_rent_type();
    if (is_full_array($data)) {
      $this->result(1, '查询成功', $data);
    } else {
      $this->result(0, '查询失败', $data);
    }
  }
}
