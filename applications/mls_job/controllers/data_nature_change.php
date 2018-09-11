<?php

/**
 * 根据基本设置，房客源变更性质（私-》公）
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      yuan
 */
class Data_nature_change extends MY_Controller
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
      $this->load->model('agency_basic_setting_model');
      //1.处理所有相关门店的出售房源
      $all_agency_basic_data = $this->agency_basic_setting_model->get_all_agency_data();
      if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
        foreach ($all_agency_basic_data as $k => $v) {
          $agency_id = $v['agency_id'];
          $sell_house_nature_public = $v['sell_house_nature_public'];
          if (!empty($agency_id) && !empty($sell_house_nature_public)) {
            //当天的凌晨时间戳
            $today_time = strtotime(date('Y-m-d'));
            $compare_day = intval($sell_house_nature_public);
            $compare_time = $today_time - $compare_day * 24 * 3600;
            $update_result = $this->sell_house_model->change_nature_by_agency_id($agency_id, $compare_time);
            echo "出售房源 所属门店id:$agency_id ，" . " 影响行数：$update_result<br>";
          } else {
            echo '尚未有相关门店设置出售房源变更选项<br>';
          }
        }
      }
      //2.剩余房源按照默认参数处理。判断默认天数是否为空，如果为空，不做处理。
      $default_basic_data = $this->agency_basic_setting_model->get_default_data();
      if (is_array($default_basic_data) && !empty($default_basic_data)) {
        $sell_house_nature_public = $default_basic_data[0]['sell_house_nature_public'];
        if (!empty($sell_house_nature_public)) {
          //当天的凌晨时间戳
          $today_time = strtotime(date('Y-m-d'));
          $compare_day = intval($sell_house_nature_public);
          $compare_time = $today_time - $compare_day * 24 * 3600;
          $agency_id_arr = array();
          if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
            foreach ($all_agency_basic_data as $k => $v) {
              $agency_id_arr[] = $v['agency_id'];
            }
          }
          $update_result = $this->sell_house_model->change_nature_by_agency_id2($agency_id_arr, $compare_time);
          echo " 默认设置影响行数：$update_result<br>";
        }

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
      $this->load->model('agency_basic_setting_model');
      //1.处理所有相关公司的出售房源
      $all_agency_basic_data = $this->agency_basic_setting_model->get_all_agency_data();
      if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
        foreach ($all_agency_basic_data as $k => $v) {
          $agency_id = $v['agency_id'];
          $rent_house_nature_public = $v['rent_house_nature_public'];
          if (!empty($agency_id) && !empty($rent_house_nature_public)) {
            //当天的凌晨时间戳
            $today_time = strtotime(date('Y-m-d'));
            $compare_day = intval($rent_house_nature_public);
            $compare_time = $today_time - $compare_day * 24 * 3600;
            $update_result = $this->rent_house_model->change_nature_by_agency_id($agency_id, $compare_time);
            echo "出租房源 所属门店id:$agency_id ，" . " 影响行数：$update_result<br>";
          } else {
            echo '尚未有相关门店设置出售房源变更选项<br>';
          }
        }
      }
      //2.剩余房源按照默认参数处理。判断默认天数是否为空，如果为空，不做处理。
      $default_basic_data = $this->agency_basic_setting_model->get_default_data();
      if (is_array($default_basic_data) && !empty($default_basic_data)) {
        $rent_house_nature_public = $default_basic_data[0]['rent_house_nature_public'];
        if (!empty($rent_house_nature_public)) {
          //当天的凌晨时间戳
          $today_time = strtotime(date('Y-m-d'));
          $compare_day = intval($rent_house_nature_public);
          $compare_time = $today_time - $compare_day * 24 * 3600;
          $agency_id_arr = array();
          if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
            foreach ($all_agency_basic_data as $k => $v) {
              $agency_id_arr[] = $v['agency_id'];
            }
          }
          $update_result = $this->rent_house_model->change_nature_by_agency_id2($agency_id_arr, $compare_time);
          echo " 默认设置影响行数：$update_result<br>";
        }

      }
    }
  }

  /**
   * 求购客源
   */
  public function buy_customer()
  {
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('buy_customer_model');
    if (!empty($city)) {
      $this->load->model('agency_basic_setting_model');
      //1.处理所有相关公司的出售房源
      $all_agency_basic_data = $this->agency_basic_setting_model->get_all_agency_data();
      if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
        foreach ($all_agency_basic_data as $k => $v) {
          $agency_id = $v['agency_id'];
          $buy_customer_nature_public = $v['buy_customer_nature_public'];
          if (!empty($agency_id) && !empty($buy_customer_nature_public)) {
            //当天的凌晨时间戳
            $today_time = strtotime(date('Y-m-d'));
            $compare_day = intval($buy_customer_nature_public);
            $compare_time = $today_time - $compare_day * 24 * 3600;
            $update_result = $this->buy_customer_model->change_nature_by_agency_id($agency_id, $compare_time);
            echo "求购客源 所属公司id:$agency_id ，" . " 影响行数：$update_result<br>";
          } else {
            echo '尚未有相关门店设置出售房源变更选项<br>';
          }
        }
      }
      //2.剩余房源按照默认参数处理。判断默认天数是否为空，如果为空，不做处理。
      $default_basic_data = $this->agency_basic_setting_model->get_default_data();
      if (is_array($default_basic_data) && !empty($default_basic_data)) {
        $buy_customer_nature_public = $default_basic_data[0]['buy_customer_nature_public'];
        if (!empty($buy_customer_nature_public)) {
          //当天的凌晨时间戳
          $today_time = strtotime(date('Y-m-d'));
          $compare_day = intval($buy_customer_nature_public);
          $compare_time = $today_time - $compare_day * 24 * 3600;
          $agency_id_arr = array();
          if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
            foreach ($all_agency_basic_data as $k => $v) {
              $agency_id_arr[] = $v['agency_id'];
            }
          }
          $update_result = $this->buy_customer_model->change_nature_by_agency_id2($agency_id_arr, $compare_time);
          echo " 默认设置影响行数：$update_result<br>";
        }

      }
    }
  }

  /**
   * 求租客源
   */
  public function rent_customer()
  {
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('rent_customer_model');
    if (!empty($city)) {
      $this->load->model('agency_basic_setting_model');
      //1.处理所有相关公司的出售房源
      $all_agency_basic_data = $this->agency_basic_setting_model->get_all_agency_data();
      if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
        foreach ($all_agency_basic_data as $k => $v) {
          $agency_id = $v['agency_id'];
          $rent_customer_nature_public = $v['rent_customer_nature_public'];
          if (!empty($agency_id) && !empty($rent_customer_nature_public)) {
            //当天的凌晨时间戳
            $today_time = strtotime(date('Y-m-d'));
            $compare_day = intval($rent_customer_nature_public);
            $compare_time = $today_time - $compare_day * 24 * 3600;
            $update_result = $this->rent_customer_model->change_nature_by_agency_id($agency_id, $compare_time);
            echo "求租客源 所属公司id:$agency_id ，" . " 影响行数：$update_result<br>";
          } else {
            echo '尚未有相关门店设置出售房源变更选项<br>';
          }
        }
      }
      //2.剩余房源按照默认参数处理。判断默认天数是否为空，如果为空，不做处理。
      $default_basic_data = $this->agency_basic_setting_model->get_default_data();
      if (is_array($default_basic_data) && !empty($default_basic_data)) {
        $rent_customer_nature_public = $default_basic_data[0]['rent_customer_nature_public'];
        if (!empty($rent_customer_nature_public)) {
          //当天的凌晨时间戳
          $today_time = strtotime(date('Y-m-d'));
          $compare_day = intval($rent_customer_nature_public);
          $compare_time = $today_time - $compare_day * 24 * 3600;
          $agency_id_arr = array();
          if (is_array($all_agency_basic_data) && !empty($all_agency_basic_data)) {
            foreach ($all_agency_basic_data as $k => $v) {
              $agency_id_arr[] = $v['agency_id'];
            }
          }
          $update_result = $this->rent_customer_model->change_nature_by_agency_id2($agency_id_arr, $compare_time);
          echo " 默认设置影响行数：$update_result<br>";
        }

      }
    }
  }

  /**
   * 当前城市下，所有的经纪人查看保密信息次数归零
   */
  public function read_secrecy_num_zero()
  {
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('broker_info_model');
    $result = $this->broker_info_model->update_read_secrecy_num_zero();
    if ($result) {
      echo '经纪人当天查看保密信息次数归零---》成功';
    } else {
      echo '经纪人当天查看保密信息次数归零---》失败';
    }
  }


}
