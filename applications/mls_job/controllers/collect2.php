<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 房源采集（赶集，58）
 * 2016.3.10
 * cc
 */
class Collect2 extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('collect/collect2_model', 'cm');
    $this->load->model('notice_access_model', 'na');
  }

  //采集列表
  public function collect_list($cityid = 0, $source_from = '58', $autojump = 0)
  {
    echo "<a href='" . MLS_JOB_URL . "/collect2/collect_list/" . $cityid . "/" . $source_from . "/" . $autojump . "/'>刷新</a><br />";

    if ($autojump != 0) {
      echo "<script>setTimeout(function(){window.location.href='" . MLS_JOB_URL . "/collect2/loading?parm=collect_list/" . $cityid . "/" . $source_from . "/" . $autojump . "/'}, " . $autojump . ");</script>";
    }

    $result = $this->cm->collect_list($cityid, $source_from);
  }

  public function loading()
  {
    $parm = $this->input->get('parm');
    echo '执行中，请稍候。。。';
    echo "<a href='" . MLS_JOB_URL . "/collect2/" . $parm . "'>刷新</a><br />";
    echo "<script>window.location.href='" . MLS_JOB_URL . "/collect2/" . $parm . "';</script>";
  }

  //采集列表（分城市）
  public function collect_list_spell($spell, $source_from = '')
  {
    $res = $this->cm->collect_city_byspell($spell);
    $result = $this->cm->collect_list($res['id'], $source_from);
  }

  //采集列表
  public function more_collect_list($id, $start, $total)
  {
    $result = $this->cm->more_collect_list($id, $start);
    $start = $start + 1;
    if ($start <= $total)
      echo "<script>window.location.href='" . MLS_JOB_URL . "/collect2/more_collect_list/" . $id . "/" . $start . "/" . $total . "/'</script>";
  }

  //采集详情
  public function collect_info($cityid = 0, $source_from = '58', $autojump = 0)
  {
    echo "<a href='" . MLS_JOB_URL . "/collect2/collect_info/" . $cityid . "/" . $source_from . "/" . $autojump . "/'>刷新</a><br />";

    if ($autojump != 0) {
      echo "<script>setTimeout(function(){window.location.href='" . MLS_JOB_URL . "/collect2/loading?parm=collect_info/" . $cityid . "/" . $source_from . "/" . $autojump . "/'}, " . $autojump . ");</script>";
    }

    $this->cm->collect_info($cityid, $source_from);//传城市id
  }
}
