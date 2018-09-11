<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 定时任务计划
 *
 * @author xz
 */
class job_crontab extends MY_Controller
{

  /**
   * 构造函数
   */
  public function __construct()
  {
    parent::__construct();
  }


  /*
   * 单项统计-按区属统计数据
   */
  public function statistic_city_district($city)
  {
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //区属板块信息
    $district_arr = array();
    $this->load->model('district_model');
    $district_arr = $this->district_model->get_district();
    $dist_num = count($district_arr);

    //昨天凌晨时间
    $start_day = date("Y-m-d", strtotime("-1 day"));
    $start_time = strtotime($start_day);

    //统计MODEL加载
    $this->load->model('city_statistic_model');

    //循环区属查询当日发布、新增面积、新增总价、新增单价
    for ($i = 0; $i < $dist_num; $i++) {
      $dist_id = $district_arr[$i]['id'];

      //获取出售统计数据
      $cond_where = "district_id = '" . $dist_id . "' AND createtime > $start_time";
      $add_info = $this->city_statistic_model->count_city_district_new_data('sell', $cond_where);
      //添加统计数据
      $add_info['dist_id'] = $dist_id;
      $add_info['stattime'] = $start_day;
      $this->city_statistic_model->add_city_district_data(1, $add_info);

      //获取出租统计数据
      $cond_where = "district_id = '" . $dist_id . "' AND createtime > $start_time";
      $add_info_rent = $this->city_statistic_model->count_city_district_new_data('rent', $cond_where);

      //添加统计数据
      $add_info_rent['dist_id'] = $dist_id;
      $add_info_rent['stattime'] = $start_day;
      $this->city_statistic_model->add_city_district_data(2, $add_info_rent);
    }

    echo 'over';
  }


  /*
   * 单项统计-按区属统计数据
   */
  public function statistic_city_build_type($city)
  {
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //物业类型信息
    $build_type_arr = array(
      array('id' => 1, 'name' => '住宅'),
      array('id' => 2, 'name' => '别墅'),
      array('id' => 3, 'name' => '商铺'),
      array('id' => 4, 'name' => '写字楼'),
      array('id' => 5, 'name' => '厂房'),
      array('id' => 6, 'name' => '仓库'),
      array('id' => 7, 'name' => '车库'),
    );
    $build_type_num = count($build_type_arr);

    //昨天凌晨时间
    $start_day = date("Y-m-d", strtotime("-1 day"));
    $start_time = strtotime($start_day);

    //统计MODEL加载
    $this->load->model('city_statistic_model');

    //循环区属查询当日发布、新增面积、新增总价、新增单价
    for ($i = 0; $i < $build_type_num; $i++) {
      $build_type_id = $build_type_arr[$i]['id'];

      //获取出售统计数据
      $cond_where = "sell_type = '" . $build_type_id . "' AND createtime > $start_time";
      $add_info = $this->city_statistic_model->count_city_district_new_data('sell', $cond_where);
      //添加统计数据
      $add_info['build_type'] = $build_type_id;
      $add_info['stattime'] = $start_day;
      $this->city_statistic_model->add_city_build_type_data(1, $add_info);

      //获取出租统计数据
      $cond_where = "sell_type = '" . $build_type_id . "' AND createtime > $start_time";
      $add_info_rent = $this->city_statistic_model->count_city_district_new_data('rent', $cond_where);

      //添加统计数据
      $add_info_rent['build_type'] = $build_type_id;
      $add_info_rent['stattime'] = $start_day;
      $this->city_statistic_model->add_city_build_type_data(2, $add_info_rent);
    }

    echo 'over';
  }


  /*
   * 趋势统计数据积累
   * 获取各类型数据
   */
  public function statistic_city_trend_sell($city)
  {
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //获取今日已发布的出售房源数据，然后插入趋势统计出售房源表中(每年一张表)
    //昨天凌晨时间
    $start_day = date("Y-m-d", strtotime("-1 day"));
    $start_time = strtotime($start_day);
    $end_time = $start_time + 86399;

    //统计MODEL加载
    $this->load->model('city_statistic_model');

    //二手房数据统计
    $this->load->model('sell_house_model');
    $cond_where = "createtime >= '" . $start_time . "' AND createtime <= '" . $end_time . "'";
    $sell_num = $this->sell_house_model->get_count_by_cond($cond_where);

    $page_size = 50;
    for ($i = 1; $i <= $sell_num; $i++) {
      $offset = ($i - 1) * $page_size;
      $house_list = $this->sell_house_model->get_list_by_cond($cond_where, $offset, $page_size);

      //查询的数据插入插入趋势统计出售房源表中
      $temp_num = count($house_list);
      for ($i = 0; $i < $temp_num; $i++) {
        $this->city_statistic_model->add_city_trend_statistic_sell($house_list[$i]);
      }
    }
    echo "over";
  }


  /*
   * 趋势统计数据积累
   * 获取各类型数据
   */
  public function statistic_city_trend_rent($city)
  {
    $city = strip_tags(trim($city));

    if ($city == '') {
      exit('缺少城市参数无法执行！');
    }
    $this->config->set_item('login_city', $city);

    //获取今日已发布的出售房源数据，然后插入趋势统计出售房源表中(每年一张表)
    //昨天凌晨时间
    $start_day = date("Y-m-d", strtotime("-1 day"));
    $start_time = strtotime($start_day);
    $end_time = $start_time + 86399;

    //统计MODEL加载
    $this->load->model('city_statistic_model');

    //二手房数据统计
    $this->load->model('rent_house_model');
    $cond_where = "id > 0";
    $rent_num = $this->rent_house_model->get_count_by_cond($cond_where);

    $page_size = 50;
    for ($i = 1; $i <= $rent_num; $i++) {
      $offset = ($i - 1) * $page_size;
      $house_list = $this->rent_house_model->get_list_by_cond($cond_where, $offset, $page_size);

      //查询的数据插入插入趋势统计出售房源表中
      $temp_num = count($house_list);
      for ($j = 0; $j < $temp_num; $j++) {
        $this->city_statistic_model->add_city_trend_statistic_rent($house_list[$j]);
      }
    }

    echo "over";
  }
}

/* End of file job_crontab.php */
/* Location: ./application/mls/controllers/job_crontab.php */
