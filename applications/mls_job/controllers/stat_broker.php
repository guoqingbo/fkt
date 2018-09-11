<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stat_broker extends MY_Controller
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
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  public function index()
  {
    $city = $this->input->get('city');

    $this->load->model('city_model');//房源查看模型类
    $citydata = $this->city_model->get_city_by_spell($city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    if ($cityid > 0) {
      $this->set_city($city);

      $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
      $stattime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

      $data = $this->bd->get_broker_arr($stattime, $cityid);

      if (is_full_array($data)) {
        foreach ($data as $value) {
          $insert_data = array(
            'master' => $value['master_id'],
            'broker_id' => $value['broker_id'],
            'truename' => $value['truename'],
            'phone' => $value['phone'],
            'company_id' => $value['company_id'],
            'company' => $value['company'] != '' ? $value['company'] : '',
            'agency_id' => $value['agency_id'],
            'agency' => $value['agency'] != '' ? $value['agency'] : '',
            'dist_id' => $value['dist_id'],
            'dist' => $value['dist'] != '' ? $value['dist'] : '',
            'login_num' => $value['login'],
            'sell_publish_num' => $value['sell_publish'],
            'rent_publish_num' => $value['rent_publish'],
            'sell_collect_view_num' => $value['sell_collect_view'],
            'rent_collect_view_num' => $value['rent_collect_view'],
            'sell_group_publish_num' => $value['sell_group_publish'],
            'rent_group_publish_num' => $value['rent_group_publish'],
            'sell_outside_num' => $value['sell_outside_num'],
            'rent_outside_num' => $value['rent_outside_num'],
            'sell_level_3_num' => $value['sell_level3_num'],
            'rent_level_3_num' => $value['rent_level3_num'],
            'sell_level_2_num' => $value['sell_level2_num'],
            'rent_level_2_num' => $value['rent_level2_num'],
            'sell_num' => $value['sell_num'],
            'rent_num' => $value['rent_num'],
            'sell_cooperate_num' => $value['sell_cooperate_num'],
            'rent_cooperate_num' => $value['rent_cooperate_num'],
            'app_access_num' => $value['app_access_num'],
            'sell_video_num' => $value['sell_video_num'],
            'rent_video_num' => $value['rent_video_num'],
            'ymd' => date('Y-m-d', $stattime)
          );

          $this->bd->save_statdata($insert_data, 'stat_broker');
          unset($insert_data);
        }
      }
    }

    echo 'over';
  }

  //统计第一步，记录经纪人基础数据
  public function new_stat_1()
  {
    $city = $this->input->get('city');

    $this->load->model('city_model');//房源查看模型类
    $citydata = $this->city_model->get_city_by_spell($city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    if ($cityid > 0) {
      $this->set_city($city);

      $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
      $stattime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

      $data = $this->bd->get_broker($stattime);

      if (is_full_array($data)) {
        foreach ($data as $value) {
          $insert_data = array(
            'ymd' => date('Y-m-d', $stattime),
            'city' => $cityid,
            /****************经纪人基础数据****************/
            //客户经理
            'master' => $value['master_id'],
            //个人资料
            'broker_id' => $value['broker_id'],
            'truename' => $value['truename'],
            'telno' => $value['phone'],
            //是否认证
            'group_id' => $value['group_id'] == 2 ? 1 : 0,  //此处为方便汇总统计需要，认证为1,其他为0
            //所属公司
            'company_id' => $value['company_id'],
            'company' => $value['company'] != '' ? $value['company'] : '',
            //所属门店
            'agency_id' => $value['agency_id'],
            'agency' => $value['agency'] != '' ? $value['agency'] : '',
            //所属区域
            'dist_id' => $value['dist_id'],
            'dist' => $value['dist'] != '' ? $value['dist'] : '',

            /****************今日是否在线，活跃度****************/
            'pc_online' => $value['pc_online'],//////////////////////
            'app_online' => $value['app_online'],//////////////////////
            'is_online' => $value['pc_online'] == 1 || $value['app_online'] == 1 ? 1 : 0,//////////////////////

            /******************是否是合作经纪人******************/
            'is_cooperate' => $value['is_cooperate']
          );
          $this->bd->save_statdata($insert_data, 'stat_broker_daily', 'db');
          unset($insert_data);
        }
      }
    }

    echo 'over';
  }

  //统计第二步，记录经纪人统计数据
  public function new_stat_2()
  {
    $this->set_city('nj');
    $check = $this->input->get('check');

    $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
    $stattime = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));

    $statbroker = $this->bd->get_stat_broker($stattime, 10);

    //用于检查是否还有待执行数据
    if ($check == 1) {
      $check = is_full_array($statbroker) ? 1 : 0;

      echo $check;
      exit;
    }

    if (is_full_array($statbroker)) {
      $this->load->model('city_model');//房源查看模型类
      $citydata = $this->city_model->get_all_city();
      $cityarr = array();
      foreach ($citydata as $city) {
        $cityarr[$city['id']] = $city['spell'];
      }
      unset($citydata);

      $precity = $ids = '';
      $dealarr = $statbroker;
      $this->bd->dbselect('db');
      foreach ($dealarr as $key => $val) {
        $check = $this->bd->execute("UPDATE `mls`.`stat_broker_daily` set step = 2 where id = '" . $val['id'] . "' and step = 0");
        if ($check) {
          //更新成功
        } else {
          unset($statbroker[$key]);
        }
      }


      foreach ($statbroker as $val) {
        $cityid = intval($val['city']);
        $cityspell = $cityarr[$cityid];
        if ($cityspell != $precity) {
          $this->set_city($cityspell);
          $this->bd->dbselect('dbback_city');
          $this->bd->query("use `mls_" . $cityspell . "`");
        }

        $data = $this->bd->get_broker_stat_arr($stattime, $val['broker_id']);
        $statdata = array(
          //表示已执行过
          'step' => 1,

          /****************合作数据****************/
          //出售出租合作房源量
          'sell_cooperate_house_num' => $data['sell_cooperate_house_num'],
          'rent_cooperate_house_num' => $data['rent_cooperate_house_num'],
          //该经纪人合作房源申请量
          'sell_cooperate_num' => $data['sell_cooperate_num'],
          'rent_cooperate_num' => $data['rent_cooperate_num'],
          //该经纪人合作房源生效合作量
          'sell_cooperate_1_num' => $data['sell_cooperate_1_num'],
          'rent_cooperate_1_num' => $data['rent_cooperate_1_num'],
          //该经纪人合作房源成交合作量
          'sell_cooperate_2_num' => $data['sell_cooperate_2_num'],
          'rent_cooperate_2_num' => $data['rent_cooperate_2_num'],
          //该经纪人合作房源成交周期
          'sell_cooperate_time' => $data['sell_cooperate_time'],
          'rent_cooperate_time' => $data['rent_cooperate_time'],

          /****************采集数据****************/
          //查看量
          'sell_collect_open_num' => $data['sell_collect_open_num'],
          'rent_collect_open_num' => $data['rent_collect_open_num'],
          //电话查看量
          'sell_collect_view_num' => $data['sell_collect_view_num'],
          'rent_collect_view_num' => $data['rent_collect_view_num'],
          //标记联系量
          'sell_collect_sign_num' => $data['sell_collect_sign_num'],
          'rent_collect_sign_num' => $data['rent_collect_sign_num'],
          //录入量
          'sell_collect_add_num' => $data['sell_collect_add_num'],
          'rent_collect_add_num' => $data['rent_collect_add_num'],
          //当天是否使用了采集模块
          'collect_used' => $data['sell_collect_open_num'] != 0 || $data['rent_collect_open_num'] != 0 || $data['sell_collect_view_num'] != 0 || $data['rent_collect_view_num'] != 0 || $data['sell_collect_sign_num'] != 0 || $data['rent_collect_sign_num'] != 0 || $data['sell_collect_add_num'] != 0 || $data['rent_collect_add_num'] != 0 ? 1 : 0,

          /****************群发数据****************/
          //群发站点量
          'gp_site_num' => $data['gp_site_num'],
          //群发出售量、出租量
          'sell_group_publish' => $data['sell_group_publish'],
          'rent_group_publish' => $data['rent_group_publish'],
          //群发刷新量
          'gp_refresh_num' => $data['gp_refresh_num'],
          //当天是否使用了群发模块
          'gp_used' => $data['sell_group_publish'] != 0 || $data['rent_group_publish'] != 0 || $data['gp_refresh_num'] != 0 ? 1 : 0,

          /****************ERP房源量****************/
          //录入量
          'sell_publish_num' => $data['sell_publish_num'],
          'rent_publish_num' => $data['rent_publish_num'],
          //总量
          'sell_num' => $data['sell_num'],
          'rent_num' => $data['rent_num'],

          /****************外网房源量****************/
          'sell_outside_num' => $data['sell_outside_num'],
          'rent_outside_num' => $data['rent_outside_num'],

          /****************房源图片量****************/
          'sell_pic_num' => $data['sell_pic_num'],
          'rent_pic_num' => $data['rent_pic_num'],
          'sell_pic_2_num' => $data['sell_pic_2_num'],
          'rent_pic_2_num' => $data['rent_pic_2_num'],

          /****************房源视频量****************/
          'sell_video_num' => $data['sell_video_num'],
          'rent_video_num' => $data['rent_video_num']
        );
        $this->bd->update_statdate($val['id'], $statdata, 'stat_broker_daily');
        unset($statdata);
      }
    }

    echo 'over';
  }

  //统计第三步，汇总统计
  public function new_stat_3()
  {
    $this->set_city('nj');
    $ymd = $this->input->get('ymd');

    $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
    $stattime = $ymd != '' ? strtotime($ymd) : mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
    $ymd = date('Y-m-d', $stattime);

    $city_arr = $this->bd->sum_city($stattime);
    if (is_full_array($city_arr)) {
      foreach ($city_arr as $value) {
        $value['ymd'] = $ymd;
        $this->bd->save_statdata($value, 'stat_broker_city_daily', 'db');
      }
    }
    unset($city_arr);

    $master_arr = $this->bd->sum_master($stattime);
    if (is_full_array($master_arr)) {
      foreach ($master_arr as $value) {
        $value['ymd'] = $ymd;
        $this->bd->save_statdata($value, 'stat_broker_master_daily', 'db');
      }
    }
    unset($master_arr);

    echo 'over';


    //$this->sendsms('15951634202', 'broker_stat_is_over');
  }

  public function reset_collect_data()
  {
    $this->set_city('nj');
    $ymd = $this->input->get('ymd');

    $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
    $stattime = strtotime($ymd);

    $statbroker = $this->bd->get_stat_broker($stattime, 2000);

    if (is_full_array($statbroker)) {
      $this->load->model('city_model');//房源查看模型类
      $citydata = $this->city_model->get_all_city();
      $cityarr = array();
      foreach ($citydata as $city) {
        $cityarr[$city['id']] = $city['spell'];
      }
      unset($citydata);

      $precity = $ids = '';
      $dealarr = $statbroker;
      /*foreach($dealarr as $val)
            {
                $ids .= $ids != '' ? ','.$val['id'] : $val['id'];
            }
            $this->bd->dbselect('db');
            $this->bd->execute("UPDATE `mls`.`stat_broker_daily` set step = 2 where id in (".$ids.")");*/

      foreach ($statbroker as $val) {
        $cityid = intval($val['city']);
        $cityspell = $cityarr[$cityid];
        if ($cityspell != $precity) {
          $this->set_city($cityspell);
          $this->bd->dbselect('dbback_city');
          $this->bd->query("use `mls_" . $cityspell . "`");
        }

        $data = $this->bd->get_broker_stat_arr_2($stattime, $val['broker_id']);
        $statdata = array(
          //表示已执行过
          'step' => 1,

          //群发出售量、出租量
          'sell_group_publish' => $data['sell_group_publish'],
          'rent_group_publish' => $data['rent_group_publish'],
          //群发刷新量
          'gp_refresh_num' => $data['gp_refresh_num'],
          //当天是否使用了群发模块
          'gp_used' => $data['sell_group_publish'] != 0 || $data['rent_group_publish'] != 0 || $data['gp_refresh_num'] != 0 ? 1 : 0
        );
        $this->bd->update_statdate($val['id'], $statdata, 'stat_broker_daily');
        unset($statdata);
      }
    }

    echo 'over';
  }

  public function dist_count()
  {
    $city = $this->input->get('city');

    $this->load->model('city_model');//房源查看模型类
    $citydata = $this->city_model->get_city_by_spell($city);
    $cityid = intval($citydata['id']);
    unset($citydata);

    if ($cityid > 0) {
      $time = strtotime("-1 day");
      $ymd = date('Y-m-d', $time);
      $stat = array('ymd' => $ymd);

      $this->load->model('stat_broker_data_model', 'bd');

      //区域门店数量统计
      $sql = "SELECT dist_id, count(id) as num FROM `mls_" . $city . "`.`agency` WHERE dist_id > 0 AND company_id > 0 GROUP BY dist_id";
      $stat['agency_num'] = $this->bd->query($sql);

      //区域认证经纪人数量统计
      $sql = "SELECT a.dist_id, count(bi.id) as num FROM `mls_" . $city . "`.`broker_info` as bi LEFT JOIN `mls_" . $city . "`.`agency` as a ON bi.agency_id = a.id WHERE bi.status = 1 AND bi.group_id = 2 AND a.company_id > 0 GROUP BY a.dist_id";
      $stat['rz_jjr_num'] = $this->bd->query($sql);
      //区域经纪人总量统计
      $sql = "SELECT a.dist_id, count(bi.id) as num FROM `mls_" . $city . "`.`broker_info` as bi LEFT JOIN `mls_" . $city . "`.`agency` as a ON bi.agency_id = a.id WHERE bi.status = 1 AND a.company_id > 0 GROUP BY a.dist_id";
      $stat['total_jjr_num'] = $this->bd->query($sql);

      //临时用，更新app用户标识
      $sql = "select broker_id from mls_" . $city . ".broker_info where app_user = 0 order by id asc";
      $arr = $this->bd->query($sql);
      foreach ($arr as $value) {
        $sql = "select id from mls.broker_app_access_log where broker_id = '" . $value['broker_id'] . "' limit 0,1";
        $count = $this->bd->query($sql);
        if ($count[0]['id'] > 0) {
          $sql = "update mls_" . $city . ".broker_info set app_user = '1' where broker_id = '" . $value['broker_id'] . "'";
          $this->bd->execute($sql);
        }
      }
      //区域使用过APP经纪人数量统计
      $sql = "SELECT a.dist_id, count(bi.id) as num FROM `mls_" . $city . "`.`broker_info` as bi LEFT JOIN `mls_" . $city . "`.`agency` as a ON bi.agency_id = a.id WHERE bi.status = 1 AND bi.app_user = 1 AND a.company_id > 0 GROUP BY a.dist_id";
      $stat['app_jjr_num'] = $this->bd->query($sql);


      //区域ERP出售量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`sell_house` WHERE district_id > 0 GROUP BY district_id";
      $stat['sell_erp_num'] = $this->bd->query($sql);
      //区域ERP出租量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`rent_house` WHERE district_id > 0 GROUP BY district_id";
      $stat['rent_erp_num'] = $this->bd->query($sql);
      //区域出售外网量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`sell_house` WHERE district_id > 0 AND `status` = 1 AND is_outside = 1 GROUP BY district_id";
      $stat['sell_fang100_num'] = $this->bd->query($sql);
      //区域出租外网量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`rent_house` WHERE district_id > 0 AND `status` = 1 AND is_outside = 1 GROUP BY district_id";
      $stat['rent_fang100_num'] = $this->bd->query($sql);
      //区域出售合作量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`sell_house` WHERE district_id > 0 AND `status` = 1 AND isshare = 1 GROUP BY district_id";
      $stat['sell_coop_num'] = $this->bd->query($sql);
      //区域出租合作量
      $sql = "SELECT district_id as dist_id, COUNT(id) AS num FROM `mls_" . $city . "`.`rent_house` WHERE district_id > 0 AND `status` = 1 AND isshare = 1 GROUP BY district_id";
      $stat['rent_coop_num'] = $this->bd->query($sql);


      //区域ERP出售门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`sell_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['sell_erp_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['sell_erp_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }

      }
      //区域ERP出租门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`rent_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['rent_erp_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['rent_erp_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }
      //区域出售外网门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`sell_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 AND sh.`status` = 1 AND sh.is_outside = 1 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['sell_fang100_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['sell_fang100_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }
      //区域出租外网门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`rent_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 AND sh.`status` = 1 AND sh.is_outside = 1 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['rent_fang100_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['rent_fang100_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }
      //区域出售合作门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`sell_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 AND sh.`status` = 1 AND sh.isshare = 1 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['sell_coop_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['sell_coop_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }
      //区域出租合作门店量
      $sql = "SELECT sh.agency_id, COUNT(sh.id) AS num, a.dist_id FROM `mls_" . $city . "`.`rent_house` AS sh LEFT JOIN `mls_" . $city . "`.`agency` AS a ON sh.agency_id = a.id WHERE sh.agency_id > 0 AND sh.`status` = 1 AND sh.isshare = 1 GROUP BY sh.agency_id";
      $arr = $this->bd->query($sql);
      $stat['rent_coop_agency_num'] = array();
      if (is_full_array($arr)) {
        $temp = array();
        foreach ($arr as $val) {
          $temp[$val['dist_id']] = isset($temp[$val['dist_id']]) ? $temp[$val['dist_id']] + 1 : 1;
        }

        foreach ($temp as $k => $v) {
          $stat['rent_coop_agency_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }

      //合作成交门店刷数据
      $sql = "select a.id from mls_" . $city . ".agency as a where 1 order by a.id asc";
      $arr = $this->bd->query($sql);

      foreach ($arr as $value) {
        $sql = "select id from mls_" . $city . ".cooperate where (agentid_a = '" . $value['id'] . "' or agentid_b = '" . $value['id'] . "') and esta = '7' limit 0,1";
        $count = $this->bd->query($sql);
        if ($count[0]['id'] > 0) {
          $sql = "replace into mls_" . $city . ".agency_sub (agency_id, is_cooperate) values ('" . $value['id'] . "', 1)";
          $this->bd->execute($sql);
        }
      }
      //成交覆盖门店数
      $sql = "SELECT dist_id, COUNT(a.id) as num FROM mls_" . $city . ".`agency` AS a LEFT JOIN mls_" . $city . ".`agency_sub` AS asub ON a.id = asub.agency_id WHERE a.company_id > 0 AND asub.is_cooperate = 1 GROUP BY dist_id";
      $stat['coop_cj_agency_num'] = $this->bd->query($sql);

      //合作成交经纪人刷数据
      $sql = "select a.broker_id from mls_" . $city . ".broker_info as a where 1 order by a.broker_id asc";
      $arr = $this->bd->query($sql);

      foreach ($arr as $value) {
        $sql = "select id from mls_" . $city . ".cooperate where (brokerid_a = '" . $value['broker_id'] . "' or brokerid_b = '" . $value['broker_id'] . "')  and esta = '7' limit 0,1";
        $count = $this->bd->query($sql);
        if ($count[0]['id'] > 0) {
          $sql = "replace into mls_" . $city . ".broker_info_sub (broker_id, is_cooperate) values ('" . $value['broker_id'] . "', 1)";
          $this->bd->execute($sql);
        }
      }
      //成交覆盖经纪人数
      $sql = "SELECT a.dist_id, count(bi.id) as num FROM `mls_" . $city . "`.`broker_info` as bi LEFT JOIN `mls_" . $city . "`.`agency` as a ON bi.agency_id = a.id LEFT JOIN `mls_" . $city . "`.`broker_info_sub` AS bisub ON bi.broker_id = bisub.broker_id WHERE bi.status = 1 AND bisub.is_cooperate = 1 AND a.company_id > 0 GROUP BY a.dist_id";
      $stat['coop_cj_broker_num'] = $this->bd->query($sql);

      //查询新增成交房源
      $sql = "SELECT c.id, c.tbl, c.rowid FROM `mls_" . $city . "`.`cooperate_log` AS cl LEFT JOIN `mls_" . $city . "`.`cooperate` AS c ON cl.cid = c.id WHERE cl.esta = 7 AND cl.dateline > '" . $time . "'";
      $arr = $this->bd->query($sql);
      $housearr = array();
      if (is_full_array($arr)) {
        foreach ($arr as $value) {
          if ($value['tbl'] == 'sell') {
            $sql = "SELECT district_id as dist_id FROM `mls_" . $city . "`.`sell_house` WHERE id = '" . $value['rowid'] . "' limit 0, 1";
            $house = $this->bd->query($sql);
            $housearr[$value['id']] = $house[0];
          } else {
            $sql = "SELECT district_id as dist_id FROM `mls_" . $city . "`.`sell_house` WHERE id = '" . $value['rowid'] . "' limit 0, 1";
            $house = $this->bd->query($sql);
            $housearr[$value['id']] = $house[0];
          }
        }
      }
      $stat['coop_cj_new_num'] = $arr = array();
      if (is_full_array($housearr)) {
        foreach ($housearr as $house) {
          $arr[$house['dist_id']] = isset($arr[$house['dist_id']]) ? $arr[$house['dist_id']] + 1 : 1;
        }

        foreach ($arr as $k => $v) {
          $stat['coop_cj_new_num'][] = array('dist_id' => $k, 'num' => $v);
        }
      }

      $data = addslashes(json_encode($stat));
      $year = date('Y', $time);
      $sql = "replace into mls.stat_district_daily (`ymd`, `city`, `data`, `y`) values ('" . $ymd . "', '" . $cityid . "', '" . $data . "', '" . $year . "')";
      $this->bd->execute($sql);
    }

    echo 'over';
  }

  public function count()
  {
    $ymd = $this->input->get('ymd');
    $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类

    $cityarr = array('3' => '南京', '23' => '哈尔滨', '9' => '昆明', '17' => '苏州', '19' => '杭州', '21' => '西安', '29' => '重庆', '30' => '成都');

    foreach ($cityarr as $key => $cityname) {
      /*$sql = "select count(id) as num from stat_base_broker_daily where ymd = '".$ymd."' and city = '".$key."' and group_id = '2' and (sell_num > 0 or rent_num > 0)";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo $data[0]['num'];
            echo '<br />';*/

      /*$sql = "select company_id, sum(sell_cooperate_num) as sell_num, sum(rent_cooperate_num) as rent_num from stat_base_broker_daily where ymd = '".$ymd."' and city = '".$key."' and company_id > 0 group by company_id";
            $data = $this->bd->query($sql);

            if(is_full_array($data))
            {
                $maxnum = 0;
                foreach($data as $val)
                {
                    $num = $val['sell_num'] + $val['rent_num'];
                    $maxnum = $maxnum > $num ? $maxnum : $num;
                }
            }

            echo $cityname.'：';
            echo $maxnum;
            echo '<br />';*/

      /*$sql = "select agency_id, sum(sell_cooperate_num) as sell_num, sum(rent_cooperate_num) as rent_num from stat_base_broker_daily where ymd = '".$ymd."' and city = '".$key."' and agency_id > 0 group by agency_id";
            $data = $this->bd->query($sql);

            if(is_full_array($data))
            {
                $agencynum = 0;
                foreach($data as $val)
                {
                    $num = $val['sell_num'] + $val['rent_num'];
                    if($num > 0)
                    {
                        $agencynum++;
                    }
                }
            }

            echo $cityname.'：';
            echo $agencynum;
            echo '<br />';*/

      /*$sql = "select count(id) as num from stat_base_broker_daily where ymd = '".$ymd."' and city = '".$key."' and group_id = '2' and (sell_cooperate_num > 0 or rent_cooperate_num > 0)";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo $data[0]['num'];
            echo '<br />';*/
    }
  }

  public function cooperate_count()
  {
    $cityarr = array('nj' => '南京', 'hrb' => '哈尔滨', 'km' => '昆明', 'sz' => '苏州', 'hz' => '杭州', 'xa' => '西安', 'cq' => '重庆', 'cd' => '成都');

    $this->set_city('nj');
    $this->load->model('stat_broker_data_model', 'bd');//房源查看模型类
    $this->bd->dbselect('db_city');

    foreach ($cityarr as $key => $cityname) {
      /*$sql = "select count(id) as num from `mls_".$key."`.cooperate where tbl = 'sell'";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo $data[0]['num'];
            echo '<br />';*/

      /*$sql = "select rowid, count(id) from `mls_".$key."`.cooperate where tbl = 'sell' group by rowid";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo count($data);
            echo '<br />';*/

      /*$sql = "select rowid, count(id) from `mls_".$key."`.cooperate where tbl = 'sell' and esta >= 4 group by rowid";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo count($data);
            echo '<br />';*/

      /*$sql = "select count(id) as num from `mls_".$key."`.cooperate where tbl = 'sell' and esta >= 4";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo $data[0]['num'];
            echo '<br />';*/

      /*$sql = "select rowid, count(id) from `mls_".$key."`.cooperate where tbl = 'sell' and esta >= 4 group by rowid";
            $data = $this->bd->query($sql);

            echo $cityname.'：';
            echo count($data);
            echo '<br />';*/

      /*$sarr = $earr = array();
            $sql = "select cid,dateline from `mls_".$key."`.cooperate_log where esta = 1";
            $data = $this->bd->query($sql);
            if(is_full_array($data))
            {
                foreach($data as $val)
                {
                    $sarr[$val['cid']] = $val['dateline'];
                }
            }

            $sql = "select cid,dateline from `mls_".$key."`.cooperate_log where esta = 4";
            $data1 = $this->bd->query($sql);
            if(is_full_array($data1))
            {
                foreach($data1 as $val)
                {
                    $earr[$val['cid']] = $val['dateline'];
                }
            }

            $day = 0;
            if(is_full_array($sarr) && is_full_array($earr))
            {
                foreach($earr as $key=>$val)
                {
                    $temp = ceil(($val - $sarr[$key]) / 86400);

                    $day = $day + $temp;
                }
            }

            echo $cityname.'：';
            echo $day;
            echo '<br />';*/

      /*$sarr = $earr = array();
            $sql = "select cid,dateline from `mls_".$key."`.cooperate_log where esta = 1";
            $data = $this->bd->query($sql);
            if(is_full_array($data))
            {
                foreach($data as $val)
                {
                    $sarr[$val['cid']] = $val['dateline'];
                }
            }

            $sql = "select cid,dateline from `mls_".$key."`.cooperate_log where esta = 7";
            $data1 = $this->bd->query($sql);
            if(is_full_array($data1))
            {
                foreach($data1 as $val)
                {
                    $earr[$val['cid']] = $val['dateline'];
                }
            }

            $day = 0;
            if(is_full_array($sarr) && is_full_array($earr))
            {
                foreach($earr as $key=>$val)
                {
                    $temp = ceil(($val - $sarr[$key]) / 86400);

                    $day = $day + $temp;
                }
            }

            echo $cityname.'：';
            echo $day;
            echo '<br />';*/
    }
  }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
