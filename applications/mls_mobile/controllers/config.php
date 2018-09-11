<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * house配置信息
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class config extends MY_Controller
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
   *
   * @param unknown $param
   * @param string $init
   */
  private function _format_key_value($param, $init = true)
  {
    $new_param = array();
    if (is_full_array($param)) {
      foreach ($param as $k => $v) {
        $new_param[] = array('key' => $k, 'name' => $v);
      }
    }
    return $new_param;
  }

  private function _format_key_value2($param, $init = true)
  {
    $new_param = array();
    if (is_full_array($param)) {
      foreach ($param as $k => $v) {
        $new_param[] = array('key' => $k + 1, 'name' => $v);
      }
    }
    return $new_param;
  }

  /**
   * 获取城市配置信息
   */
  public function get_city_config()
  {
    $this->load->model('city_model');
    $config = array('province' => array(), 'citys' => array());
    $province = $this->city_model->get_province();
    if (is_full_array($province)) {
      $config['province'] = $province;
      $citys = $this->city_model->get_all_city();
      if (is_full_array($citys)) {
        $new_citys = array();
        foreach ($province as $key => $value) {
          $new_citys[$key] = array('name' => $value, 'list' => array());
          foreach ($citys as $k => $v) {
            if ($value == $v['province'] && $v['cityname'] != '南京') {
              $new_citys[$key]['list'][] = array('key' => $v['id'], 'name' => $v['cityname']);
            }
          }
          $config['citys'] = $new_citys;
        }
      }
    }
    $this->result(1, '查询成功', $config);
  }

  /**
   * 获取出售/出租信息基本配置资料
   */
  public function get_house_config()
  {
    //房源模板
    $house_module = array(
      '位置好、格局棒、随时入住',
      '周边配套设施齐全 性价比超高',
      '成熟小区，人气旺，交通便利，价格可议！',
      '少有户型 拎包入住 温馨家庭首选',
      '成熟社区 景观好房 不可多得',
      '黄金位置 交通便利 升值巨大',
      '年轻时尚 观景房 落地大飘窗 采光视野一流',
      '黄金楼层 足不出户找到好房',
      '婚装全明 格局工整合理 得房率超高',
      '房主急售，精品，满五年',
      '稀缺，低价出售，位置好，随时看房！',
      '地铁楼盘 学区房 精装 拎包即住！',
      '立体交通全程对接，主城生活唾手可得'
    );
    //采集时间
    $valid_time = array('不限', '一周之内', '一个月内', '三个月内', '更早时间');
    $source_from = array('不限', '赶集', '58', '房天下');
    $message = $this->user_arr;
    $city = $message['city_spell'];
    if ($city == 'sz') {
      $cj_district = $this->_format_key_value($this->_get_cj_district());
    } else {
      $cj_district = $this->_format_key_value($this->_get_district());
    }
    $this->load->model('house_config_model');//加载出售基本配置MODEL
    $config_house = $this->house_config_model->get_config();
    $new_config_house = array(
      'property_type' => $this->_format_key_value($config_house['sell_type']),
      'fitment' => $this->_format_key_value($config_house['fitment']),
      'forward' => $this->_format_key_value($config_house['forward']),
      'floor_scale' => $this->_format_key_value($config_house['floor_scale']),
      'nature' => $this->_format_key_value($config_house['nature'], false),
      'status' => $this->_format_key_value($config_house['status'], false),
      'sell_tag' => $this->_format_key_value($config_house['sell_tag'], false),
      'rent_tag' => $this->_format_key_value($config_house['rent_tag'], false),
      'is_share' => array(array('key' => 0, 'name' => '否'), array('key' => 1, 'name' => '是')),
      'keys' => array(array('key' => 0, 'name' => '无'), array('key' => 1, 'name' => '有')),
      'entrust' => $this->_format_key_value($config_house['entrust'], false),
      'rent_entrust' => $this->_format_key_value($config_house['rententrust'], false),
      'taxes' => $this->_format_key_value($config_house['taxes'], false),

      'district' => $this->_format_key_value($this->_get_district()),
      'street' => $this->_get_district_street(),

      'sell_price' => $this->_format_key_value($config_house['sell_price']),
      'sell_area' => $this->_format_key_value($config_house['sell_area']),
      'rent_price' => $this->_format_key_value($config_house['rent_price']),
      'rent_area' => $this->_format_key_value($config_house['rent_area']),
      'price_danwei' => array(array('key' => 0, 'name' => '元/月'),
        array('key' => 1, 'name' => '元/㎡*天')),
      'agency_list' => $this->_format_key_value2(array('本人房源', '公司房源', '门店房源', '合作房源')),
      'time' => $this->_format_key_value(array('不限', '一个月内', '一个季度', '半年内', '一年内')),
      'orderby_id' => $this->_format_key_value(array('不限', '按录入时间由近到远排序', '按录入时间由远到近排序', '按房龄由低到高排序', '按房龄由高到低排序', '按面积由小到大排序', '按面积由大到小排序', '按价格由低到高排序', '按价格由高到低排序')),
      'room' => $this->_format_key_value(array('不限', '一室', '二室', '三室', '四室', '五室', '六室')),
      'is_cooperate_reward' => $this->_format_key_value(array('不限', '是', '否')),
      'cj_district' => $cj_district,
      'cj_street' => $this->_get_cj_district_street(),
      'is_outside' => array(array('key' => -1, 'name' => '不限'),
        array('key' => 0, 'name' => '否'),
        array('key' => 1, 'name' => '是')),
      'apnt_time' => $this->_format_key_value($config_house['apnt_time']),
      'house_degree' => $this->_format_key_value($config_house['house_degree']),
      'reward_type' => $this->_format_key_value($config_house['reward_type']),

//            'country' => $this->_format_key_value($this->_get_country()),
//            'abroad_city' => $this->_get_abroad_city(),

//            'province' => $this->_format_key_value($this->_get_province()),
//            'tourism_city' => $this->_get_tourism_city(),

      'abroad_price' => $this->_format_key_value($config_house['abroad_price']),
      'tourism_price' => $this->_format_key_value($config_house['tourism_price']),
      'abroad_house_type' => $this->_format_key_value($config_house['abroad_house_type']),
      'tourism_house_feature' => $this->_format_key_value($config_house['tourism_house_feature']),
      'abroad_order' => $this->_format_key_value($config_house['abroad_order']),
      'tourism_order' => $this->_format_key_value($config_house['tourism_order']),
      'work_time' => $this->_format_key_value($config_house['work_time']),
      'ios_estate_tel' => $this->config->item('tel400'),
      'create_time_range' => $this->_format_key_value($config_house['create_time_range']),
      'sell_remark_module' => $house_module,
      'rent_remark_module' => $house_module,
      'valid_time' => $this->_format_key_value($valid_time, false),
      'source_from' => $this->_format_key_value($source_from, false),
      'commission_ratio' => $this->_format_key_value($config_house['commission_ratio']),
      'story' => $this->_format_key_value($config_house['story']),
    );
    $this->result(1, '查询成功', $new_config_house);
  }

  /**
   * 获取求购/求租信息基本配置资料
   */
  public function get_customer_config()
  {
    $this->load->model('buy_customer_model');
    $conf_customer = $this->buy_customer_model->get_base_conf();
    $new_conf_customer = array(
      'property_type' => $this->_format_key_value($conf_customer['property_type']),
      'fitment' => $this->_format_key_value($conf_customer['fitment']),
      'forward' => $this->_format_key_value($conf_customer['forward']),
      'public_type' => $this->_format_key_value($conf_customer['public_type']),
      'status' => $this->_format_key_value($conf_customer['status'], false),
      'is_share' => $this->_format_key_value($conf_customer['is_share'], false),
      'district' => $this->_format_key_value($this->_get_district()),
      'street' => $this->_get_district_street(),
      'buy_price' => $this->_format_key_value($conf_customer['buy_price']),
      'buy_area' => $this->_format_key_value($conf_customer['buy_area']),
      'rent_price' => $this->_format_key_value($conf_customer['rent_price']),
      'rent_area' => $this->_format_key_value($conf_customer['rent_area']),
      'price_danwei' => array(array('key' => 0, 'name' => '元/月'),
        array('key' => 1, 'name' => '元/㎡*天')),
      'agency_list' => $this->_format_key_value2(array('本人客源', '公司客源', '门店客源', '合作客源',)),
      'time' => $this->_format_key_value(array('不限', '一个月内', '一个季度', '半年内', '一年内')),
      'room' => $this->_format_key_value(array('不限', '一室', '二室', '三室', '四室', '五室', '六室')),
      'lease' => $this->_format_key_value($conf_customer['lease']),
      'create_time_range' => $this->_format_key_value($conf_customer['create_time_range']),
      'age_group' => $this->_format_key_value($conf_customer['age_group']),
      'intent' => $this->_format_key_value($conf_customer['intent']),
      'infofrom' => $this->_format_key_value($conf_customer['infofrom']),
      'job_type' => $this->_format_key_value($conf_customer['job_type']),
    );
    $this->result(1, '查询成功', $new_conf_customer);
  }

  /**
   * 获取合作状态位的基本配置信息
   */
  public function get_cooperate_config()
  {
    $this->load->model('cooperate_model');
    //状态数组
    $base_conf = $this->cooperate_model->get_base_conf();
    $result = array(
      'status' => $this->_format_key_value($base_conf['esta']),
      'cancel_reason' => $this->_format_key_value($base_conf['cancel_reason']),
      'refuse_reason' => $this->_format_key_value($base_conf['refuse_reason']),
      'stop_reason' => $this->_format_key_value($base_conf['stop_reason']),
    );
    $this->result(1, '查询成功', $result);
  }

  /**
   * 获取区属配置信息
   */
  private function _get_district()
  {
    $this->load->model('district_model');
    $district = $this->district_model->get_district();
    $new_district = array();
    if (is_full_array($district)) {
      foreach ($district as $key => $value) {
        $new_district[$value['id']] = $value['district'];
      }
    }
    ksort($new_district);
    return $new_district;
  }

  /**
   * 获取国家配置信息
   */
  private function _get_country()
  {
//        $this->load->model('abroad_model');
//        $country = $this->abroad_model->get_country();
//        $new_country = array();
//        if (is_full_array($country))
//        {
//            foreach ($country as $key => $value)
//            {
//                $new_country[$value['id']] = $value['country_name'];
//            }
//        }
//        return $new_country;
  }

  /**
   * 获取省配置信息
   */
  private function _get_province()
  {
//        $this->load->model('tourism_model');
//        $province = $this->tourism_model->get_province();
//        $new_province = array();
//        if (is_full_array($province))
//        {
//            foreach ($province as $key => $value)
//            {
//                $new_province[$value['id']] = $value['province_name'];
//            }
//        }
//        return $new_province;
  }

  /**
   * 获取采集区属配置信息
   */
  private function _get_cj_district()
  {
    $this->load->model('district_model');
    $district = $this->district_model->get_cjdistrict();
    $new_district = array();
    if (is_full_array($district)) {
      foreach ($district as $key => $value) {
        $new_district[$value['id']] = $value['district'];
      }
    }
    return $new_district;
  }

  /**
   * 获取区属板块信息
   */
  private function _get_district_street()
  {
    $this->load->model('district_model');
    $street = $this->district_model->get_street();
    $district = $this->_get_district();
    $new_street = array();
    if (is_full_array($district)) {
      $i = 0;
      foreach ($district as $key => $value) {
        $new_street[$i] = array('name' => $value, 'list' => array());
        if (is_full_array($street)) {
          foreach ($street as $k => $v) {
            if ($v['dist_id'] == $key) {
              $new_street[$i]['list'][] = array(
                'key' => $v['id'], 'name' => $v['streetname'],
              );
            }
          }
        }
        $i++;
      }
    }
    return $new_street;
  }

  private function _get_cj_district_street()
  {
    $this->load->model('district_model');
    $street = $this->district_model->get_cjstreet();
    $message = $this->user_arr;
    $city = $message['city_spell'];
    if ($city == 'sz') {
      $district = $this->_get_cj_district();
    } else {
      $district = $this->_get_district();
    }
    $new_street = array();
    if (is_full_array($district) && is_full_array($street)) {
      $i = 0;
      foreach ($district as $key => $value) {
        $new_street[$i] = array('name' => $value, 'list' => array());
        foreach ($street as $k => $v) {
          if ($v['dist_id'] == $key) {
            $new_street[$i]['list'][] = array(
              'key' => $v['id'], 'name' => $v['streetname'],
            );
          }
        }
        $i++;
      }
    }
    return $new_street;
  }

  /**
   * 获取海外城市板块信息
   */
  private function _get_abroad_city()
  {
//        $this->load->model('abroad_model');
//        $city = $this->abroad_model->get_city();
//        $country = $this->_get_country();
//        $new_city = array();
//        if (is_full_array($country) && is_full_array($city))
//        {
//            foreach($country as $key => $value)
//            {
//                $new_city[$key] = array('name' => $value, 'list' =>  array());
//                foreach ($city as $k => $v)
//                {
//                    if ($v['country_id'] == $key)
//                    {
//                    	$new_city[$key]['list'][] = array(
//                    		'key' => $v['id'], 'name' => $v['city_name'],
//                    	);
//                    }
//                }
//            }
//        }
//        $abroad_city = array();
//        foreach($new_city as $vo){
//          $abroad_city[] = $vo;
//        }
//        return $abroad_city;
  }

  /**
   * 获取旅游城市板块信息
   */
  private function _get_tourism_city()
  {
//    $this->load->model('tourism_model');
//    $city = $this->tourism_model->get_city();
//    $province = $this->_get_province();
//    $new_city = array();
//    if (is_full_array($province) && is_full_array($city)) {
//      foreach ($province as $key => $value) {
//        $new_city[$key] = array('name' => $value, 'list' => array());
//        foreach ($city as $k => $v) {
//          if ($v['province_id'] == $key) {
//            $new_city[$key]['list'][] = array(
//              'key' => $v['id'], 'name' => $v['city_name'],
//            );
//          }
//        }
//      }
//    }
//    $tourism_city = array();
//    foreach ($new_city as $vo) {
//      $tourism_city[] = $vo;
//    }
//    return $tourism_city;
  }
}

/* End of file house_config.php */
/* Location: ./application/mls_mobile/controllers/house_config.php */
