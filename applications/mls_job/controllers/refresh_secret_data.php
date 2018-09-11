<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Refresh_secret_data extends My_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('city_model');
  }

  public function demon($city_flag = 0, $lastid = 0)
  {
    $citys = $this->city_model->get_all_city();
    $city = $citys[$city_flag]['spell'];
    $this->set_city($city);
    if ($city_flag >= count($citys) - 1) {
      echo 'over';
      die();
    }
    echo $citys[$city_flag]['cityname'];
    $this->load->model('stat_broker_data_model', 'bd');//APP统计模型类
    $sql = "select * from mls_" . $city . ".agency_permission where id > " . $lastid . " order by id asc limit 20";
    $arr = $this->bd->query($sql);
    if (is_full_array($arr)) {
      foreach ($arr as $v) {
        $func_auth = unserialize($v['func_auth']);
        $new_func_auth = array();
        if (is_full_array($func_auth)) {
          foreach ($func_auth as $f_k => $f_v) {
            foreach ($f_v as $ff_v) {
              $new_func_auth[] = $ff_v;
            }
          }
          if (in_array(2, $new_func_auth)) {
            $sql = "update mls_" . $city . ".agency_permission set is_house_secret = 1 where id = " . $v['id'];
            $this->bd->execute($sql);
          }
          if (in_array(33, $new_func_auth)) {
            $sql = "update mls_" . $city . ".agency_permission set is_customer_secret = 1 where id = " . $v['id'];
            $this->bd->execute($sql);
          }
        }
        $lastid = $v['id'];
      }
    } else {
      $city_flag++;
      $lastid = 0;
    }
    echo "<script>window.location.href='/refresh_secret_data/demon/" . $city_flag . "/" . $lastid . "';</script>";
  }
}
