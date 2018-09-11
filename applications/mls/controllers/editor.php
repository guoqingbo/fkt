<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Editor Class
 *
 * 编辑器
 *
 * @package     zsb
 * @subpackage      Controllers
 * @category        Controllers
 * @author      mls
 */
class Editor extends MY_Controller
{

  /**
   * 编辑器
   *
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  //展示编辑器页面
  public function display($type = 1)
  {
    if ($type == 1) //房源
    {
      $this->common(820, 350);
    }
  }

  //房源编辑器
  private function common($width, $height)
  {
    $edit_config['width'] = $width;
    $edit_config['height'] = $height;
    $edit_config['items'] = "'fontname', 'fontsize', '|', 'forecolor',
            'hilitecolor', 'bold', 'italic', 'underline', 'removeformat', '|', 
            'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'wordpaste', '|', 'image'";
    $this->load->view('editor', $edit_config);
  }
}
