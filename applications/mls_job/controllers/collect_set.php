<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Collect_set extends My_Controller
{
  public function __construct()
  {
    parent::__construct();
  }

  //每天10点定时刷新获取采集订阅，只要有房源即发送系统消息和推送
  public function refresh()
  {
    $city = $this->input->get('city');

    $this->load->model('city_model');//房源查看模型类
    $citydata = $this->city_model->get_city_by_spell($city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    if ($cityid > 0) {
      $this->set_city($city);
      $this->load->model('message_model');
      $this->load->model('collections_model_new');
      $this->load->model('house_config_model');
      $this->load->model('push_func_model');
      $config_house = $this->house_config_model->get_config();
      $broker_ids = $this->collections_model_new->get_collect_broker_ids();
      //print_r($broker_ids);

      if (is_full_array($broker_ids)) {
        foreach ($broker_ids as $k => $v) {
          $i = 0;
          //获取采集订阅
          $search_sell = $this->collections_model_new->get_collect_set_info($v['broker_id'], 'sell');
          if (is_full_array($search_sell)) {
            foreach ($search_sell as $key => &$value) {
              if (date('Y-m-d', $value['updatetime']) < date('Y-m-d', time())) {
                //获取当前条件下的房源数量
                $where_set = array();
                $valid_time = time() - 86400;//一天之内
                $where_set['where_cond']['createtime >='] = $valid_time;
                $where_set['where_cond']['sell_type'] = 1;
                $info = '';
                if ($value['district']) {
                  $info .= $value['district'] . '/';
                  $where_set['like'] = array('district' => $value['district']);
                }
                if ($value['street']) {
                  $info .= $value['street'] . '/';
                  $where_set['like'] = array('block' => $value['street']);
                }
                if ($value['block_name']) {
                  $info .= $value['block_name'] . '/';
                  $where_set['or_like']['like_key'] = array('house_name');
                  $where_set['or_like']['like_value'] = $value['block_name'];
                }
                if ($value['price']) {
                  $info .= $config_house['sell_price'][$value['price']] . '/';
                  $price = $config_house['sell_price'][$value['price']];
                  $price_arr = explode('-', $price);
                  if (count($price_arr) == 2) {
                    $price1 = intval($price_arr[0]);
                    $price2 = intval($price_arr[1]);
                    $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
                    $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
                  } else {
                    if ($search_rent[0]['price'] == 1) {
                      $price2 = intval($price_arr[0]);
                      $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
                    } else {
                      $price1 = intval($price_arr[0]);
                      $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
                    }
                  }
                }
                if ($value['room']) {
                  $info .= $config_house['room'][$value['room']];
                  $where_set['where_cond']['room'] = $value['room'];
                }
                $info = rtrim($info, "/");
                $house_num = $this->collections_model_new->get_sell_num($where_set['where_cond'], $where_set['like'], $where_set['or_like']);
                $this->collections_model_new->update_collect_set_time($value['id']);//更新job刷新时间
                if ($house_num) {
                  $i++;
                  $params = array('info' => $info);
                  $this->message_model->add_message('6-52', $v['broker_id'], '', '/house_collections_new/collect_sell/', $params);
                  $this->push_func_model->send(1, 16, 1, 0, $v['broker_id'], array('type' => '1'), array(), '您订阅的“' . $info . '”有新房源喽！赶紧去看看吧！房源订阅，好房一手掌握！');
                  break;
                }
              }
            }
          }
          if ($i > 0) {
            continue;
          }
          $search_rent = $this->collections_model_new->get_collect_set_info($v['broker_id'], 'rent');
          if ($search_rent) {
            foreach ($search_rent as $key => &$value) {
              if (date('Y-m-d', $value['updatetime']) < date('Y-m-d', time())) {
                //获取当前条件下的房源数量
                $where_set = array();
                $valid_time = time() - 86400;//一天之内
                $where_set['where_cond']['createtime >='] = $valid_time;
                $where_set['where_cond']['rent_type'] = 1;
                $info = '';
                if ($value['district']) {
                  $info .= $value['district'] . '/';
                  $where_set['like'] = array('district' => $value['district']);
                }
                if ($value['street']) {
                  $info .= $value['street'] . '/';
                  $where_set['like'] = array('block' => $value['street']);
                }
                if ($value['block_name']) {
                  $info .= $value['block_name'] . '/';
                  $where_set['or_like']['like_key'] = array('house_name');
                  $where_set['or_like']['like_value'] = $value['block_name'];
                }
                if ($value['price']) {
                  $info .= $config_house['rent_price'][$value['price']] . '/';
                  $price = $config_house['rent_price'][$value['price']];
                  $price_arr = explode('-', $price);
                  if (count($price_arr) == 2) {
                    $price1 = intval($price_arr[0]);
                    $price2 = intval($price_arr[1]);
                    $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
                    $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
                  } else {
                    if ($search_rent[0]['price'] == 1) {
                      $price2 = intval($price_arr[0]);
                      $where_set['where_cond'] = $where_set['where_cond'] + array('price <=' => $price2);
                    } else {
                      $price1 = intval($price_arr[0]);
                      $where_set['where_cond'] = $where_set['where_cond'] + array('price >=' => $price1);
                    }
                  }
                }
                if ($value['room']) {
                  $info .= $config_house['room'][$value['room']];
                  $where_set['where_cond']['room'] = $value['room'];
                }
                $info = rtrim($info, "/");
                $this->collections_model_new->update_collect_set_time($value['id']);//更新job刷新时间
                $house_num = $this->collections_model_new->get_rent_num($where_set['where_cond'], $where_set['like'], $where_set['or_like']);
                if ($house_num) {
                  $params = array('info' => $info);
                  $this->message_model->add_message('6-52', $v['broker_id'], '', '/house_collections_new/collect_rent/', $params);
                  //发送推送消息
                  $this->push_func_model->send(1, 16, 1, 0, $v['broker_id'], array('type' => '2'), array(), '您订阅的“' . $info . '”有新房源喽！赶紧去看看吧！房源订阅，好房一手掌握！');
                  break;
                }
              }
            }
          }
        }
      }
    }

  }

}

