<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 自动采集控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          lalala
 */
class Cityprice extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

  }

  /**
   * 采集搜房网二手房均价（分城市）
   * 2015.10.19 qsq
   */
  public function soufang_houseprice_collect()
  {
    $this->load->library('Curl');
    $city = $this->input->get('city');
    $this->set_city($city);
    // echo 'aa';die();
    $this->load->model('cityprice_model');
    $city_arr = array('langfang', 'songyuan', 'zhangzhou', 'haikou', 'lasa', 'wulumuqi', 'liuzhou', 'pingxiang');
    if (in_array($city, $city_arr)) //廊坊 soufun没有数据，只能采安居客的
    {
      $house_price_arr_xf = $this->cityprice_model->anjuke_newhouseprice_collect($city);

      $house_price_arr = $this->cityprice_model->anjuke_houseprice_collect($city);

    } else {
      $this->load->config('cityname_utf8');
      $city_arr = $this->config->item('city_price');


      $cityname = $city_arr[$city];

      if ($cityname) {
        $api_new_url = 'http://fangjia.fang.com/pinggu/ajax/chartajax.aspx?dataType=4&city=' . $cityname . '&Class=defaultnew&year=1';
      } else {
        $api_new_url = 'http://fangjia.fang.com/pinggu/ajax/chartajax.aspx?dataType=4&city=%u5357%u4EAC&Class=defaultnew&year=1';
      }

      $data = $this->curl->vget($api_new_url);


      $house_price_str1 = file_get_contents($api_new_url);
//print_R($api_new_url);die();
      $house_price = explode("&", $house_price_str1);
      //取出二手房价格
      $house_price_array = ltrim($house_price[0], "[[");
      $house_price_array = rtrim($house_price_array, "]]");
      $house_price_arr = explode("],[", $house_price_array);
      //取出新房价格

      $house_price_array_xf = ltrim($house_price[1], "[[");
      $house_price_array_xf = rtrim($house_price_array_xf, "]]");
      $house_price_arr_xf = explode("],[", $house_price_array_xf);
    }


    $row2 = $this->cityprice_model->transform_pricestr_xf($house_price_arr_xf);

    $row1 = $this->cityprice_model->transform_pricestr($house_price_arr);


  }
}

