<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房友房源数据导入
 *
 * 用于MLS房源导入处理
 *
 *
 * @package         applications
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Push_message extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //加载出售基本配置MODEL
    $city = $this->input->get('city');
    $this->set_city($city);
  }

  /**
   * 发送推送消息
   * @param int $house_id 房源id
   * @param int $uname 客户姓名
   * @param int $type 房源 1 客源 2
   */
  public function push_send()
  {
    $string = $this->input->get('string');
    $array = json_decode($string, true);
    $this->load->model('push_func_base_model');
    if (is_full_array($array)) {
      $this->push_func_base_model->send(1, 10, 1, 0, $array['broker_id'], array(), array('uname' => $array['name'], 'block_name' => $array['block_name'], 'price' => intval($array['price']), 'price_danwei' => $array['price_danwei']), '');
    }
  }

  public function add_message()
  {
    $string = $this->input->get('string');
    $array = json_decode($string, true);
    $this->load->model('message_base_model');
    if (is_full_array($array)) {
      $this->message_base_model->add_message('3-26', $array['broker_id'], $array['broker_name'], $array['url'], array('name' => $array['name'], 'title' => $array['title']));
    }
  }

}
