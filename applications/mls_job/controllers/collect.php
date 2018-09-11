<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 房源采集（赶集，58）
 * 2016.3.10
 * cc
 */
class Collect extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('collect_model');
    $this->load->model('notice_access_model', 'na');
  }

  //采集列表
  public function collect_list()
  {
    $result = $this->collect_model->collect_list();
  }

  //采集列表（分城市）
  public function collect_list_spell($spell)
  {
    $res = $this->collect_model->collect_city_byspell($spell);
    $result = $this->collect_model->collect_list($res['id']);
  }

  //采集列表
  public function more_collect_list($id, $start, $total)
  {
    $result = $this->collect_model->more_collect_list($id, $start);
    $start = $start + 1;
    if ($start <= $total)
      echo "<script>window.location.href='" . MLS_JOB_URL . "/collect/more_collect_list/" . $id . "/" . $start . "/" . $total . "/'</script>";
  }

  //测试赶集采集详情用
  public function collect_info_test($city_id)
  {
    $result = $this->collect_model->collect_info($city_id);//传城市id
    //$compress = 'gzip';
    //$result = $this->collect_model->vget('http://bj.ganji.com/fang5/2173881697x.htm');
    //$result = $this->collect_model->vcurl('http://bj.ganji.com/fang5/2173881697x.htm',$compress);
    //$result = preg_replace("/script/is", "", $result);
    echo $result;
    //echo "<script>window.location.href='".MLS_JOB_URL."/collect/collect_info_test/".$city_id."/'</script>";
    exit;
  }

  //采集详情
  public function collect_info()
  {
    //遍历城市，同时采集数据
    $res = $this->collect_model->collect_city();
    foreach ($res as $val) {
      $result = $this->collect_model->collect_info($val['id']);//传城市id
    }
    echo "<hr>over";
  }

  public function collect_info_ganji($web)
  {
    //遍历城市，同时采集数据
    $res = $this->collect_model->collect_city();
    foreach ($res as $val) {
      $result = $this->collect_model->collect_info($val['id'], $web);//传城市id
    }
    echo "<hr>over";
  }

  //采集小网站  如：吴江房产网，亿房网
  public function collect_list_other()
  {
    $result = $this->collect_model->collect_list_other();
  }

  //采集小网站  如：吴江房产网，亿房网(补充)
  public function collect_list_other_more()
  {
    $city = $_GET['city'];
    $web = $_GET['web'];
    $result = $this->collect_model->collect_list_other_more($city, $web);
  }

  //遍历房源刷新列表
  public function check_refresh()
  {
    $deltime = strtotime(date("Y-m-d 23:58:00", time()));//清空时间
    $time = time();//获取时间便于及时清空刷新列表
    if ($time > $deltime) {
      //清空已经跑过的链接
      $this->collect_model->del_collect_refresh_list();
    }
    $res = $this->collect_model->refresh();
  }

  //修复搜房电话漏采问题
  public function soufang_tel()
  {
    $res = $this->collect_model->soufang_tel();
    echo "<script>
        function refresh(seconds){
            setTimeout('self.location.reload()',seconds*1000);
        }
        refresh(5);//调用方法启动定时刷新，数值单位：秒。
        </script>";
  }
}
