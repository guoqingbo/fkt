<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Pic Class
 *
 * 图片上传控制器
 *
 * @package     MLS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      xz
 */
class Pic extends MY_Controller
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
  }


  /**
   * 房源图片上传
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function house()
  {
    $this->load->model('pic_model');
    echo $this->pic_model->upload();
  }


  /**
   * 起塔图片上传
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function common()
  {
    $this->load->model('pic_model');
    echo $this->pic_model->common_upload();
  }
}
