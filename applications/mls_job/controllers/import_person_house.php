<?php

/**
 * 导入三六五个人房源
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class Import_person_house extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city', true);
    //设置成熟参数
    $this->set_city($city);
    $this->load->model('autocollect_model');//自动采集控制器类
    $this->load->model('notice_access_model', 'na');
  }

  /**
   * 抓取导入房源接口
   * @param string $city 城市
   * @param string $type 类型
   */
  public function index()
  {
    //$page = $this->input->get('page', true);
    //if (!$page) {$page = 1;}
    $city = $this->input->get('city', true);
    $type = $this->input->get('type', true);
    //暂定开通的城市
    $open_import_city = array('nj', 'sz', 'wx', 'hz', 'hf', 'km', 'xa', 'lz',
      'wz', 'sjz', 'hrb', 'wuhan', 'tj', 'sy', 'zz', 'cc');
    //类型
    $import_type = array('sell', 'rent');
    //参证城市
    if (!in_array($city, $open_import_city)) {
      echo json_encode(array('result' => 2, 'msg' => '城市参数不合法'));
      return false;
    }
    //验证类型
    if (!in_array($type, $import_type)) {
      echo json_encode(array('result' => 3, 'msg' => '类型参数不合法'));
      return false;
    }
    //设置城市
    $this->config->set_item('login_city', $city);
    //引入导入房源模型
    $this->load->model('import_person_house_model');
    /**
     * $count_last_time = $this->import_person_house_model->get_last_time_by_type($type);
     * if (!$count_last_time)
     * {
     * echo '计入错误日志';
     * return false;
     * }**/
    switch ($type) {
      case 'sell':
        $ary = $this->import_person_house_model->get_sell_house();
        break;
      case 'rent':
        $ary = $this->import_person_house_model->get_rent_house();
        break;
      default :
    }
    $numrows = $ary["TOTAL"] ? intval($ary["TOTAL"]) : 0;
    $is_redirect = false;
    if ($numrows < 20) {
      $is_redirect = true;
    }
    $arr = array();
    $j = 0;
    $last_time = 0;
    $get_house_pic_url = $this->config->item('get_pic_url');
    $get_house_pic_url .= 'city=' . $city . '&tbl=' . $type;
    //$this->load->library('log/Log');
    if ($numrows > 0) {
      foreach ($ary["Record"] as $key => $value) {
        $arr_key = array_keys($value);
        for ($i = 0; $i < count($arr_key); $i++) {
          $value[strtolower($arr_key[$i])] = iconv('gbk', 'utf-8', $value[$arr_key[$i]]);
          unset($value[$arr_key[$i]]);
        }
        //远程获取某条房源图片
        $get_house_pic_url .= '&house_id=' . $value['id'] . '&streetid=' . $value['streetid'];
        $get_house_info = $this->import_person_house_model->send_request($get_house_pic_url);
        $value['pics'] = $get_house_info['pics'];
        $value['url'] = $get_house_info['request_url'];
        $value['streetname'] = $get_house_info['streetname'];
        $arr[$j] = $value;
        $last_time = $value['creattime'];
        $j++;
      }
      //调用保存数据
      if ($type == "sell") {
        $i = 0;
        foreach ($arr as $val) {
          $result = $this->autocollect_model->sell_365_house($val);
          if ($result != 0) {
            $i++;
          }
        }
        $this->na->post_job_notice("365—出售详情—" . $i, $city);
        echo 'over';
      } elseif ($type == "rent") {
        $j = 0;
        foreach ($arr as $val) {
          $result = $this->autocollect_model->rent_365_house($val);
          if ($result != 0) {
            $j++;
          }
        }
        $this->na->post_job_notice("365—出租详情—" . $j, $city);
        echo 'over';
      }
      $log_arr = array();
      //Log::record('保存数据', $log_arr, 'import_person');
      //更新最后一次更新的时间
      //$this->import_person_house_model->update_by_type_and_last_time($type, $last_time);
    }
    /***
     * if ($is_redirect && false)
     * {
     * $page++;
     * $url =  'http://'.$_SERVER['HTTP_HOST'] . '/';
     * $request_url = explode('/', $_SERVER['REQUEST_URI']);
     * $url = $url . $request_url[1] . '/?city=' . $city . '&type=' . $type . '&page=' . $page;
     * $this->import_person_house_model->show_msg("接下来将导入 "." 第".$page."页","$url");
     * }***/
  }
}
