<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Pinganfang Class 平方好房同步数据接口类
 *
 * @package         fkt
 * @subpackage      Libraries
 * @category        Libraries
 * @author          yuan
 * @link
 */
class Pinganhouse
{
  private $_key = 't6EFtW43ASLVJnXjnmqDBh2VO7JjNzMe';//秘钥
  private $_trackid = '';//数据跟踪id
  private $_url = 'api.pinganfang.com';//域名
  private $_from = 'f100';//from参数
  private $house_config = array();//房源配置信息
  private $cmt_config = array();//楼盘配置信息
  private $district_street_arr = array();//成都所有区属板块数据

  public function __construct()
  {
    $this->set_cmt_config();
    $this->set_house_config();
    $this->set_district_street();
  }

  public function get_from()
  {
    return $this->_from;
  }

  public function get_url()
  {
    return $this->_url;
  }

  private function set_trackid($_trackid)
  {
    $this->_trackid = $_trackid;
  }

  private function get_trackid()
  {
    return $this->_trackid;
  }

  //设置楼盘配置数据
  private function set_cmt_config()
  {
    $cmt_config_result = array();
    $param_arr = array(
      '_from' => $this->_from,
      '_format' => 'json',
      '_requesttime' => time(),
      'type' => 'prop_type',
      'city_id' => 816
    );
    $cmt_config = $this->get_cmt_config_data($param_arr);
    if (!empty($cmt_config)) {
      $cmt_config_obj = json_decode($cmt_config);
      $cmt_config_arr = $this->object_to_array($cmt_config_obj);
      if (is_full_array($cmt_config_arr)) {
        $config_list = $cmt_config_arr['data']['list'];
        if (is_full_array($config_list)) {
          $cmt_config_result['prop_type'] = $config_list;
          $this->cmt_config = $cmt_config_result;
        }
      }
    }
  }

  //设置房源配置数据
  private function set_house_config()
  {
    $house_config_arr = array();
    $param_arr = array(
      '_from' => $this->_from,
      '_format' => 'json',
      '_requesttime' => time(),
      'type' => 'secondhand_decoration',
      'city_id' => 816
    );
    //装修类型
    $cmt_config = $this->get_house_config_data($param_arr);
    if (!empty($cmt_config)) {
      $cmt_config_obj = json_decode($cmt_config);
      $cmt_config_arr = $this->object_to_array($cmt_config_obj);
      if (is_full_array($cmt_config_arr)) {
        $config_list = $cmt_config_arr['data']['list'];
        if (is_full_array($config_list)) {
          $house_config_arr['secondhand_decoration'] = $config_list;
        }
      }
    }
    //房屋类型
    $param_arr['type'] = 'secondhand_housetype';
    $cmt_config = $this->get_house_config_data($param_arr);
    if (!empty($cmt_config)) {
      $cmt_config_obj = json_decode($cmt_config);
      $cmt_config_arr = $this->object_to_array($cmt_config_obj);
      if (is_full_array($cmt_config_arr)) {
        $config_list = $cmt_config_arr['data']['list'];
        if (is_full_array($config_list)) {
          $house_config_arr['secondhand_housetype'] = $config_list;
        }
      }
    }
    //标签
    $param_arr['type'] = 'secondhand_tag';
    $cmt_config = $this->get_house_config_data($param_arr);
    if (!empty($cmt_config)) {
      $cmt_config_obj = json_decode($cmt_config);
      $cmt_config_arr = $this->object_to_array($cmt_config_obj);
      if (is_full_array($cmt_config_arr)) {
        $config_list = $cmt_config_arr['data']['list'];
        if (is_full_array($config_list)) {
          $house_config_arr['secondhand_tag'] = $config_list;
        }
      }
    }
    //朝向
    $param_arr['type'] = 'secondhand_toward';
    $cmt_config = $this->get_house_config_data($param_arr);
    if (!empty($cmt_config)) {
      $cmt_config_obj = json_decode($cmt_config);
      $cmt_config_arr = $this->object_to_array($cmt_config_obj);
      if (is_full_array($cmt_config_arr)) {
        $config_list = $cmt_config_arr['data']['list'];
        if (is_full_array($config_list)) {
          $house_config_arr['secondhand_toward'] = $config_list;
        }
      }
    }
    $this->house_config = $house_config_arr;
  }

  public function get_house_config()
  {
    return $this->house_config;
  }


  /*
  根据数组，拼接参数字符串
  */
  private function get_param_str($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      $param_str = '';
      foreach ($param_arr as $key => $value) {
        $param_str .= $key . '=' . $value . '&';
      }
      return trim($param_str, '&');
    } else {
      return false;;
    }
  }

  /*
  对象转为数组
  */
  public function object_to_array($obj)
  {
    $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
    if (is_full_array($_arr)) {
      foreach ($_arr as $key => $val) {
        $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
        $arr[$key] = $val;
      }
    }
    return $arr;
  }

  /*
  获得成都的所有区属板块数据
  */
  public function set_district_street()
  {
    $return_arr = array();
    $param_arr = array(
      '_format' => 'json',
      '_from' => $this->_from,
      '_requesttime' => time()
    );
    $return_json = $this->get_city_info($param_arr);
    $return_obj = json_decode($return_json);
    if (is_object($return_obj) && !empty($return_obj)) {
      $return_arr = $this->object_to_array($return_obj->data->list);
    }
    $this->district_street_arr = $return_arr;
  }

  /*
  根据区属名，获得区属id
  */
  public function get_district_street_id_by_name($dist_name = '', $street_name = '')
  {
    $return_arr = array();
    if (!empty($dist_name) && is_full_array($this->district_street_arr)) {
      foreach ($this->district_street_arr as $key => $value) {
        if ($dist_name == $value['area_name']) {
          $return_arr['district_id'] = $value['area_id'];
          $street_arr = $value['labels'];
          foreach ($street_arr as $key => $value) {
            if ($street_name == $value['block_name']) {
              $return_arr['street_id'] = $value['block_id'];
            }
          }
        }
      }
    }
    return $return_arr;
  }


  /*
  根据guid规则随机生成数据跟踪id
  */
  public function generate_trackid()
  {
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45);
    $uuid = substr($charid, 0, 8) . $hyphen
      . substr($charid, 8, 4) . $hyphen
      . substr($charid, 12, 4) . $hyphen
      . substr($charid, 16, 4) . $hyphen
      . substr($charid, 20, 12);
    $this->set_trackid($uuid);
  }

  /*
  不同的接口，根据参数，生成加密字符串
  */
  public function set_token($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      $requesttime = $param_arr['_requesttime'];
      unset($param_arr['_from']);
      unset($param_arr['_requesttime']);
      if (is_full_array($param_arr)) {
        //key值正向排序
        ksort($param_arr);
        $key_value_str = '';
        foreach ($param_arr as $key => $value) {
          if ('urls' == $key && is_full_array($value)) {
            $key_value_str .= $key;
            foreach ($value as $k => $v) {
              $key_value_str .= $k;
              foreach ($v as $a => $b) {
                $key_value_str .= $a . $b;
              }
            }
          } else {
            $key_value_str .= $key . $value;
          }
        }
        $key_value_str_2 = sha1($key_value_str) . $this->_key . $requesttime;
        $key_value_str_3 = md5($key_value_str_2);
        return $key_value_str_3;
      } else {
        return false;
      }
    } else {
      return false;
    }

  }

  /*
  获得数据跟踪id,加密字符串
  */
  public function return_trackid_token($param_arr = array())
  {
    $return_arr = array();
    //生成数据跟踪id，加入到参数中
    $this->generate_trackid();
    if (!empty($this->_trackid)) {
      $param_arr['_trackid'] = $this->_trackid;
    }
    //生成加密字符串
    $_token = $this->set_token($param_arr);
    if (!empty($this->_trackid) && !empty($_token)) {
      $return_arr['_trackid'] = $this->_trackid;
      $return_arr['_token'] = $_token;
    }
    return $return_arr;
  }

  /*
  获得城市列表
  */
  public function get_city_list($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      //生成数据跟踪id，加入到参数中
      $this->generate_trackid();
      if (!empty($this->_trackid)) {
        $param_arr['_trackid'] = $this->_trackid;
      }
      //生成加密字符串
      $_token = $this->set_token($param_arr);
      if (!empty($_token)) {
        $param_arr['_token'] = $_token;
      }
      $param_str = $this->get_param_str($param_arr);
      $url = $this->_url . '/hft/1.0/get_city_list' . '?' . $param_str;
      $ci = &get_instance();
      $ci->load->library('Curl');
      $result = Curl::curl_get_contents($url);
      return $result;
    } else {
      return false;
    }
  }

  /*
  获得城市下的区属板块
  */
  public function get_city_info($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      //生成数据跟踪id，加入到参数中
      $this->generate_trackid();
      if (!empty($this->_trackid)) {
        $param_arr['_trackid'] = $this->_trackid;
      }
      //生成加密字符串
      $_token = $this->set_token($param_arr);
      if (!empty($_token)) {
        $param_arr['_token'] = $_token;
      }
      $param_str = $this->get_param_str($param_arr);
      $url = $this->_url . '/hft/1.0/get_city_list/city_info/816' . '?' . $param_str;
      $ci = &get_instance();
      $ci->load->library('Curl');
      $result = Curl::curl_get_contents($url);
      return $result;
    } else {
      return false;
    }
  }

  /*
  二手房数据字典
  */
  public function get_house_config_data($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      //生成数据跟踪id，加入到参数中
      $this->generate_trackid();
      if (!empty($this->_trackid)) {
        $param_arr['_trackid'] = $this->_trackid;
      }
      //生成加密字符串
      $_token = $this->set_token($param_arr);
      if (!empty($_token)) {
        $param_arr['_token'] = $_token;
      }
      $param_str = $this->get_param_str($param_arr);
      $url = $this->_url . '/esf/1.0/dict' . '?' . $param_str;
      $ci = &get_instance();
      $ci->load->library('Curl');
      $result = Curl::curl_get_contents($url);
      return $result;
    } else {
      return false;
    }
  }

  /*
  楼盘数据字典
  */
  public function get_cmt_config_data($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      //生成数据跟踪id，加入到参数中
      $this->generate_trackid();
      if (!empty($this->_trackid)) {
        $param_arr['_trackid'] = $this->_trackid;
      }
      //生成加密字符串
      $_token = $this->set_token($param_arr);
      if (!empty($_token)) {
        $param_arr['_token'] = $_token;
      }
      $param_str = $this->get_param_str($param_arr);
      $url = $this->_url . '/xq/1.0/dict' . '?' . $param_str;
      $ci = &get_instance();
      $ci->load->library('Curl');
      $result = Curl::curl_get_contents($url);
      return $result;
    } else {
      return false;
    }
  }

  /*
  推送公司
  */
  public function deal_company_info($param_arr = array())
  {
    if (is_full_array($param_arr)) {
      //生成数据跟踪id，加入到参数中
      $this->generate_trackid();
      if (!empty($this->_trackid)) {
        $param_arr['_trackid'] = $this->_trackid;
      }
      //生成加密字符串
      $_token = $this->set_token($param_arr);
      if (!empty($_token)) {
        $param_arr['_token'] = $_token;
      }
      $param_str = $this->get_param_str($param_arr);
      $url = $this->_url . '/hft/1.0/sync_company_info';
      $ci = &get_instance();
      $ci->load->library('Curl');
      $result = Curl::static_vpost($url, $param_arr);
      return $result;
    } else {
      return false;
    }
  }


}

/* End of file memcached_library.php */
/* Location: ./application/libraries/memcached_library.php */
