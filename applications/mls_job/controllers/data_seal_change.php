<?php

/**
 * 房源解封
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class Data_seal_change extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 出售房源
   */
  public function sell_house()
  {
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('sell_house_model');
    if (!empty($city)) {
      //获得所有需要解封的房源
      //当天的凌晨时间戳
      $today_time = strtotime(date('Y-m-d'));
      $_sql = 'select id,is_seal,seal_broker_id,seal_start_time,seal_end_time from sell_house where is_seal = 1 and seal_end_time < "' . $today_time . '"';
      $seal_house = $this->sell_house_model->query_2($_sql);
      if (is_full_array($seal_house)) {
        foreach ($seal_house as $key => $value) {
          $update_arr = array(
            'is_seal' => 2,
            'seal_broker_id' => 0,
            'seal_start_time' => 0,
            'seal_end_time' => 0
          );
          $this->sell_house_model->set_id(intval($value['id']));
          $update_result = $this->sell_house_model->update_info_by_id($update_arr);
          if ($update_result) {
            echo '房源id:' . $value['id'] . '，解封成功<br>';
          }
        }
      } else {
        echo '未找到需要解封的出售房源';
      }
    }
  }

  /**
   * 出租房源
   */
  public function rent_house()
  {
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('rent_house_model');
    if (!empty($city)) {
      //获得所有需要解封的房源
      //当天的凌晨时间戳
      $today_time = strtotime(date('Y-m-d'));
      $_sql = 'select id,is_seal,seal_broker_id,seal_start_time,seal_end_time from rent_house where is_seal = 1 and seal_end_time < "' . $today_time . '"';
      $seal_house = $this->rent_house_model->query_2($_sql);
      if (is_full_array($seal_house)) {
        foreach ($seal_house as $key => $value) {
          $update_arr = array(
            'is_seal' => 2,
            'seal_broker_id' => 0,
            'seal_start_time' => 0,
            'seal_end_time' => 0
          );
          $this->rent_house_model->set_id(intval($value['id']));
          $update_result = $this->rent_house_model->update_info_by_id($update_arr);
          if ($update_result) {
            echo '房源id:' . $value['id'] . '，解封成功<br>';
          }
        }
      } else {
        echo '未找到需要解封的出售房源';
      }
    }
  }

}
