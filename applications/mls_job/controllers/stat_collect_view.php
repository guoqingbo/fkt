<?php

/**
 * job刷新每日房源查看量
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      angel_in_us
 */
class Stat_collect_view extends MY_Controller
{
  /**
   * 解析函数
   * @access public
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->model('notice_access_model', 'na');
    $this->set_city('nj');
    $this->load->model('collect_view_model');//房源查看模型类
  }

  /**
   * 刷新每日房源查看量
   * @param string $city 城市
   */
  public function index()
  {
    $city = $this->input->get('city');
    if (strlen(trim($city)) < 2) {
      echo json_encode(array('result' => 0, 'msg' => '请选择要刷新的城市代号！'));
      exit();
    }

    $this->set_city($city);
    //统计数据只取昨天的
    $starttime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天起始
    $endtime = $starttime + 86399;//昨天的结束

    $start_time = date("Y-m-d", strtotime("-1 day"));//录入数据表 ymd 字段的值

    $where = 'createtime >= ' . $starttime . ' and createtime <= ' . $endtime;//组装查询语句

    $sell_num = $this->collect_view_model->get_sell_view_num($where);//出售查看量

    $rent_num = $this->collect_view_model->get_rent_view_num($where);//出租查看量

    //查询 stat_collect_view 数据表里是否已有昨日房源查看数据
    $date = date('Y-m-d', strtotime("-1 day"));
    $where_check = 'ymd = "' . $date . '"';//组装查询语句
    $result = $this->collect_view_model->check_view_exist($where_check);
    if ($result) {
      //如果表里面已经有当日数据，更新最新的房源查看量，否则新插入一条数据
      //更新 stat_collect_view 数据表对应的数据
      $data = array(
        'sell_num' => $sell_num,
        'rent_num' => $rent_num
      );
      $where_up = array('id' => $result['id']);
      $rel = $this->collect_view_model->update_daily_count($data, $where_up);
    } else {
      //往 stat_collect_view 数据表里插入数据
      $data = array(
        'sell_num' => $sell_num,
        'rent_num' => $rent_num,
        'ymd' => $start_time
      );
      $rel = $this->collect_view_model->add_daily_count($data);
    }

    $this->na->post_job_notice('房源查看量—sell:' . $data['sell_num'] . '_rent:' . $data['rent_num']);
    echo 'over';
  }
}
