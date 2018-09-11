<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 *  关于
 *  用户协议
 */

class About extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $about = file_get_contents(dirname(__FILE__) . '/../views/about/about.php');
    $this->result(1, '查询成功', array('list' => $about));
  }
}
