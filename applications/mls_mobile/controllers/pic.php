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

  public function upload()
  {
    $image_type = $this->input->post('image_type', TRUE);
    if (empty($image_type) && !isset($image_type)) {
      $this->result('0', '未接收到参数值');
      exit;
    }
    if ($image_type == 1) {
      $this->house();
    } else {
      $this->common();
    }

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
    $data = array();
    $this->load->model('pic_model');
    $input_name = 'pic';
    $this->pic_model->set_filename($input_name);
    $house_image_url = $this->pic_model->upload("house");
    $data['house_image_url'] = $house_image_url;
    if ($house_image_url) {
      $this->result('1', '返回房源图片地址', $data);
    } else {
      $this->result('0', '上传失败');
    }
  }


  /**
   * 其它图片上传
   *
   * @access  public
   * @param  void
   * @return  void
   */
  public function common()
  {
    $data = array();
    $this->load->model('pic_model');
    $input_name = 'pic';
    $this->pic_model->set_filename($input_name);
    $house_image_url = $this->pic_model->upload("common");
    $data['other_image_url'] = $house_image_url;
    if ($house_image_url) {
      $this->result('1', '返回其它图片地址', $data);
    } else {
      $this->result('0', '上传失败');
    }

  }
}
