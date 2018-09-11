<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------


load_m("Cityprice_base_model");

class Cityprice_model extends Cityprice_base_model
{

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 采集安居客二手房均价（分城市）
   * 2015.10.19 qsq
   */
  public function anjuke_houseprice_collect($city)
  {
    //MLS_JOB_URL/cityprice/anjuke_houseprice_collect/?city=langfang
    $this->load->library('Curl');
    $data = $this->curl->vget('http://' . $city . '.anjuke.com/market/');
    //防止跳转
    $data = str_replace('script', '', $data);
    preg_match("/id:'regionChart',(.*)\}\);/siU", $data, $prj);
    //$prj[1] = trim(trim($prj[1]), "]");
    $json_data = '{' . $prj[1] . '}';
    $format_arr = array('type', 'xdata', 'xyear', 'ydata', "'line'");
    foreach ($format_arr as $v) {
      $json_data = str_replace($v, '"' . $v . '"', $json_data);
    }
    return $this->_format_price($json_data);
  }

  /**
   * 采集安居客新房均价（分城市）
   * 2015.10.19 qsq
   */
  public function anjuke_newhouseprice_collect($city)
  {
    //MLS_JOB_URL/cityprice/anjuke_houseprice_collect/?city=langfang
    $this->load->library('Curl');
    $data = $this->curl->vpost('http://' . $city . '.fang.anjuke.com/fangjia/', '');
    //防止跳转
    $data = str_replace('script', '', $data);
    preg_match("/drawfn:'drawLine',(.*)\}\);/siU", $data, $prj);
    //$prj[1] = trim(trim($prj[1]), "]");
    $json_data = '{' . $prj[1] . '}';

    $format_arr = array('id', 'xdata', 'xyear', 'ydata', "'#j-charts-city'");
    foreach ($format_arr as $v) {
      $json_data = str_replace($v, '"' . $v . '"', $json_data);
    }
    return $this->_format_price($json_data);
  }

  private function _format_price($json_data)
  {
    $arr_data = json_decode($json_data, true);
    $house_price = array();
    $year = 0;
    $month = 0;
    $price = 0;
    foreach ($arr_data['xdata'] as $k => $v) {
      if ($k == 11) {
        break;
      }
      $year = str_replace('年', '', $arr_data['xyear'][$v]);
      $month = str_replace('月', '', $v);
      $price = $arr_data['ydata'][0]['data'][$k];
      $strtime = strtotime($year . '-' . $month . '-1') . '000';
      $house_price[] = $strtime . ',' . $price;
    }
    return $house_price;
  }
}

/* End of file City_model.php */
/* Location: ./app/models/City_model.php */
