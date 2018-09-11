<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 公告
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Finance extends MY_Controller
{
  private $url = MLS_FINANCE_URL;

  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function override()
  {
    $class = $this->uri->segment(2);
    $method = $this->uri->segment(3);

    $url = $this->url . $class . '/' . $method . '?city_spell=' . $this->user_arr['city_spell'] . '&broker_id=' . $this->user_arr['broker_id'];

    $get = $this->input->get();
    if (is_full_array($get)) {
      $str = '';
      foreach ($get as $key => $value) {
        if (!in_array($key, array('scode', 'city_spell', 'broker_id'))) {
          $str .= $key . '=' . $value . '&';
        }
      }
      $str = substr($str, 0, -1);
      $url .= $str;
    }

    $post = $this->input->post();
    if ($post) $post = http_build_query($post);
    echo vpost($url, $post);
  }
}

/* End of file broker.php */
/* Location: ./application/mls/controllers/broker.php */
