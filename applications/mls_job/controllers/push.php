<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
set_time_limit(0);
ignore_user_abort();

/**
 * Push controller CLASS
 *
 * 自动采集控制器类
 *
 * @package         Push
 * @subpackage      controllers
 * @category        controllers
 */
class Push extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city', true);
    //设置成熟参数
    $this->set_city($city);
    $this->load->model('push_func_model');//自动采集控制器类

  }

  /**
   * 新增采集房源
   * 早8点，晚8点推送新增的采集信息
   * @return boolean
   */
  public function collect()
  {
    $city = $this->input->get('city', true);
    $this->load->model('collections_model_new');//采集模型类
    $collect_sell_num = $this->collections_model_new->get_new_sell_num();
    $collect_rent_num = $this->collections_model_new->get_new_rent_num();
    $collect_num = $collect_sell_num + $collect_rent_num;
    if ($collect_num <= 0) {
      return false;
    }
    $this->load->model('broker_online_app_model');
    $brokers = $this->broker_online_app_model->get_all_by_city($city);
    if (is_full_array($brokers)) {
      $pagesize = 50;
      $count_num = count($brokers);
      $pages = $count_num > 0 ? ceil($count_num / $pagesize) : 0;
      for ($i = 0; $i < $pages; $i++) {
        for ($j = 0; $j < $pagesize; $j++) {
          $k = $i * $pagesize + $j;
          if ($k > $count_num) {
            break;
          }
          $this->push_func_model->send(1, 2, 1, 0, $brokers[$k]['broker_id'],
            array(), array('collect_num' => $collect_num));
        }
        sleep(2);
      }
    }
  }

  /**
   * 合作房客源
   * 服务器将经纪人所发布的房客源的所在区域与当前经纪人的区域进行匹配
   * 每五分钟推送一次
   * @return boolean
   */
  public function coop_house()
  {
    $city = $this->input->get('city', true);
    if ($city != 'hz') {
      return false;
    }
    $push_coop_house = array();
    //获取出售、出租、求购、求租5分钟之内更新，并且是合作的房源
    $arr_type = array(1 => 'sell', 2 => 'rent', 3 => 'buy_customer', 4 => 'rent_customer');
    //出售
    $this->load->model('sell_house_model');
    //出租
    $this->load->model('rent_house_model');
    //求购
    $this->load->model('buy_customer_model');
    //求租
    $this->load->model('rent_customer_model');
    //区属板块信息
    $this->load->model('district_model');
    $arr_district = $this->district_model->get_district();
    $format_district = change_to_key_array($arr_district, 'id');
    //板块数据
    $arr_street = $this->district_model->get_street();
    $format_street = change_to_key_array($arr_street, 'id');
    $push_coop_house = array();
    foreach ($arr_type as $k => $v) {
      if ($v == 'sell' || $v == 'rent') //出售和出租
      {
        $house_coop = $v == 'sell' ? $this->sell_house_model->get_coop_in_time()
          : $this->rent_house_model->get_coop_in_time();
        if (!is_full_array($house_coop)) {
          continue;
        }
        $house_coop['price'] = strip_end_0($house_coop['price']);
        $house_coop['buildarea'] = strip_end_0($house_coop['buildarea']);
        if ($v == 'sell') {
          $message = "{$house_coop['block_name']} {$house_coop['buildarea']}平"
            . " {$house_coop['price']}万,在合作中心发布出售,点击查看";
        } else {
          $price_danwei = '';
          if ($house_coop['price_danwei'] == 0) {
            $price_danwei = '元/月';
            $price = strip_end_0($house_coop['price']) . $price_danwei;
          } else {
            $price_danwei = '元/㎡*天';
            $price = strip_end_0($house_coop['price'] / 30 / $house_coop['buildarea']) . $price_danwei;
          }
          $message = "{$house_coop['block_name']} {$house_coop['buildarea']}平 "
            . "{$price},在合作中心发布出租,点击查看";
        }
        $push_coop_house[] = array(
          'district_id' => $house_coop['district_id'],
          'street_id' => $house_coop['street_id'],
          'tbl' => $k, 'id' => $house_coop['id'],
          'message' => $message, 'broker_id' => $house_coop['broker_id']
        );

      } else if ($v == 'buy_customer' || $v == 'rent_customer') //求购和求租
      {
        $customer_coop = $v == 'buy_customer' ? $this->buy_customer_model->get_coop_in_time()
          : $this->rent_customer_model->get_coop_in_time();
        if (!is_full_array($customer_coop)) {
          continue;
        }
        $district_name = $format_district[$customer_coop['dist_id1']]['district'];
        $street_name = $format_street[$customer_coop['street_id1']]['streetname'];
        $area_min = strip_end_0($customer_coop['area_min']);
        $area_max = strip_end_0($customer_coop['area_max']);
        $price_min = strip_end_0($customer_coop['price_min']);
        $price_max = strip_end_0($customer_coop['price_max']);
        if ($v == 'buy_customer') {
          $message = "{$customer_coop['truename']} 求购 {$district_name}"
            . "{$street_name} {$area_min}-{$area_max}平 {$price_min}-{$price_max}万,点击查看";
        } else {
          $message = "{$customer_coop['truename']} 求租 {$district_name}"
            . "{$street_name} {$area_min}-{$area_max}平 {$price_min}-{$price_max}万,点击查看";
        }
        $push_coop_house[] = array(
          'district_id' => $customer_coop['dist_id1'],
          'street_id' => $customer_coop['street_id1'],
          'tbl' => $k, 'id' => $customer_coop['id'],
          'message' => $message, 'broker_id' => $customer_coop['broker_id']
        );
      }
    }
    //有没有合作房客源
    if (!is_full_array($push_coop_house)) {
      return false;
    }
    //获取到有需要推送的合作房客源信息
    $this->load->model('coop_push_record_model');
    $new_push_coop_house = array();
    foreach ($push_coop_house as $v) {
      $coop_record = $this->coop_push_record_model->
      get_by_tbl_house_id($v['tbl'], $v['id']);
      if (!is_full_array($coop_record)) {
        $new_push_coop_house[] = $v;
      }
    }
    //引入发送推送各类model
    $this->load->model('push_func_model');
    $this->load->model('agency_model');
    $this->load->model('broker_info_model');
    //计算可以推送的经纪人
    foreach ($new_push_coop_house as $v) {
      //根据房源区属和板块编号查找所司门店
      $district_id = $v['district_id'];
      if (intval($district_id) <= 0) {
        continue;
      }
      $this->agency_model->set_select_fields(array('id'));
      $agencys = $this->agency_model->get_all_by('dist_id = ' . $district_id);
      if (!is_full_array($agencys)) {
        continue;
      }
      $new_agencys = array();
      foreach ($agencys as $agency) {
        $new_agencys[] = $agency['id'];
      }
      //根据门店找经纪人
      $this->broker_info_model->set_select_fields(array('broker_id'));
      $brokers = $this->broker_info_model->get_by_agency_id($new_agencys);
      if (!is_full_array($brokers)) {
        continue;
      }
      //字段用以跳转房客源的详情页
      $field = array('tbl' => $v['tbl'], 'house_id' => $v['id']);
      foreach ($brokers as $broker) {
        if ($broker['broker_id'] == $v['broker_id']) {
          continue;
        }
        //发送推送消息
        $this->push_func_model->send(1, 5, 1, 0, $broker['broker_id'],
          $field, array(), $v['message']);
      }
      //插入此房源的合作记录
      $this->coop_push_record_model->insert(array('tbl' => $v['tbl'], 'house_id' => $v['id']));
    }
  }

  //提醒推送
  public function remind()
  {
    $this->load->model('push_func_model');
    $interval_time = strtotime(date('Y-m-d'));
    $where_cond = 'notice_time = ' . $interval_time;
    $this->load->model('remind_model');
    $remind_list = $this->remind_model->get_remind($where_cond);
    if (is_full_array($remind_list)) {
      foreach ($remind_list as $v) {
        $field = array('house_id' => $v['row_id'], 'tbl' => $v['tbl']);
        //发送推送消息
        $this->push_func_model->send(1, 8, 1, 0, $v['broker_id'],
          $field, array(), '您有一条带看提醒：' . $v['contents']);
      }
    }
  }

  //端客户预约
  public function apnt_push()
  {
    $this->load->model('house_model');
    $i = 0;
    $time = time();
    $sdate = date('Y-m-d', $time) . ' ' . get_week(date('Y-m-d', $time));
    $hour = date('H', $time);//获取当前小时
    if ($hour >= 8 && $hour <= 10) {
      $stime = '上午(9:00-12:00)';
    } elseif ($hour >= 11 && $hour <= 13) {
      $stime = '下午(12:00-18:00)';
    } elseif ($hour >= 17 && $hour <= 19) {
      $stime = '晚上(18:00-22:00)';
    }
    $where = array('sdate' => $sdate, 'stime' => $stime, 'is_push' => 0);
    $list = $this->push_func_model->get_apnt_list($where);
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        if ($val['type'] == 1) {
          $this->house_model->set_tbl('sell_house');
          $danwei = '万';
        } elseif ($val['type'] == 2) {
          $this->house_model->set_tbl('rent_house');
          $danwei = '元/月';
        }
        $this->house_model->set_id($val['house_id']);
        $house_info = $this->house_model->get_info_by_id();
        $message = '您原定于【' . $val['sdate'] . $val['stime'] . '】【' . $house_info['block_name'] . ' ' . $house_info['room'] . '室' . $house_info['hall'] . '厅' . ' ' . intval($house_info['price']) . $danwei . '】看房，请安排好看房时间。如您已另约时间，请以与经纪人另约时间为准';
        $result = $this->push_func_model->send_fang(1, $val['uid'], $field = array(), $message);
        if ($result['status']) {
          $update_arr = array('is_push' => 1, 'push_time' => time());
          $this->push_func_model->update_apnt_by_id($update_arr, $val['id']);
          $i++;
        }

      }
    }
    echo "共有{$i}条推送消息发送";
  }
}
/* End of file Push.php */
/* Location: ./application/mls_admin/controllers/Push.php */
