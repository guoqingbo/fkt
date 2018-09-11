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

/**
 * Stat_broker_data_model CLASS
 *
 * 统计经纪人综合数据
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          lu
 */
class Stat_broker_data_model extends MY_Model
{

  private $sell_tbl = 'sell_house';
  private $rent_tbl = 'rent_house';
  private $count_tbl = 'stat_publish';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_broker_arr($stattime, $cityid)
  {
    $data = array();
    $data['form_name'] = 'broker_info';
    $data['select'] = array('broker_id', 'phone', 'truename', 'group_id', 'agency_id', 'company_id', 'master_id');
    $data['where'] = array('status' => 1);
    $broker_arr = $this->get_data($data, 'dbback_city');

    $return = array();

    if ($broker_arr) {
      $temp = 0;
      //$ymd = date('Y-m-d', $stattime);
      foreach ($broker_arr as $broker) {
        /*$sql = "select id from stat_broker where broker_id = '".$broker['broker_id']."' and ymd = '".$ymd."' limit 0,1;";
                $data = $this->query($sql);
                if($data[0]['id'] > 0)
                {
                    continue;
                }*/
        $login = $this->get_login_data($stattime, $broker['broker_id']);
        $district = $this->get_company_dist($broker['company_id']);
        $return[$temp]['broker_id'] = $broker['broker_id'];
        $return[$temp]['master_id'] = $broker['master_id'];
        $return[$temp]['phone'] = $broker['phone'];
        $return[$temp]['group_id'] = $broker['group_id'];
        $return[$temp]['truename'] = $broker['truename'];
        $return[$temp]['agency_id'] = $broker['agency_id'];
        $return[$temp]['company_id'] = $broker['company_id'];
        $return[$temp]['login'] = $this->get_login_data($stattime, $broker['broker_id']);
        $return[$temp]['agency'] = $this->get_agency_info($broker['agency_id']);
        $return[$temp]['company'] = $this->get_company_info($broker['company_id']);
        if (is_full_array($district)) {
          $return[$temp]['dist'] = $district[0]['district'];
          $return[$temp]['dist_id'] = $district[0]['id'];
        } else {
          $return[$temp]['dist'] = '';
          $return[$temp]['dist_id'] = 0;
        }
        $return[$temp]['sell_publish'] = $this->get_sell_publish_data($stattime, $broker['broker_id']);
        $return[$temp]['rent_publish'] = $this->get_rent_publish_data($stattime, $broker['broker_id']);
        $return[$temp]['sell_collect_view'] = $this->get_sell_collect_view_data($stattime, $broker['broker_id']);
        $return[$temp]['rent_collect_view'] = $this->get_rent_collect_view_data($stattime, $broker['broker_id']);
        $return[$temp]['sell_group_publish'] = $this->get_group_publish_data(1, $stattime, $broker['broker_id']);//$this->get_sell_group_publish_data($stattime,$broker['broker_id']);
        $return[$temp]['rent_group_publish'] = $this->get_group_publish_data(2, $stattime, $broker['broker_id']);//$this->get_rent_group_publish_data($stattime,$broker['broker_id']);
        $return[$temp]['sell_outside_num'] = $this->get_sell_outside_data($broker['broker_id']);
        $return[$temp]['rent_outside_num'] = $this->get_rent_outside_data($broker['broker_id']);
        $return[$temp]['sell_num'] = $this->get_sell_num($broker['broker_id']);
        $return[$temp]['rent_num'] = $this->get_rent_num($broker['broker_id']);
        $return[$temp]['sell_cooperate_num'] = $this->get_sell_cooperate_num($broker['broker_id']);
        $return[$temp]['rent_cooperate_num'] = $this->get_rent_cooperate_num($broker['broker_id']);
        $return[$temp]['sell_video_num'] = $this->get_sell_video_num($broker['broker_id']);
        $return[$temp]['rent_video_num'] = $this->get_rent_video_num($broker['broker_id']);

        $return[$temp]['sell_level3_num'] = $this->get_sell_level_num($broker['broker_id'], 3);
        $return[$temp]['rent_level3_num'] = $this->get_rent_level_num($broker['broker_id'], 3);
        $return[$temp]['sell_level2_num'] = $this->get_sell_level_num($broker['broker_id'], array(1, 2));
        $return[$temp]['rent_level2_num'] = $this->get_rent_level_num($broker['broker_id'], array(1, 2));

        $return[$temp]['app_access_num'] = $this->get_app_access_num($stattime, $broker['broker_id'], $cityid);
        $temp++;
      }
    }

    return $return;
  }

  //读取经纪人在线数据
  public function get_online_data($broker_id, $stattime)
  {
    $data = $return = array();
    $this->db_mls->where(array('broker_id' => $broker_id));
    $this->db_mls->from('mls.broker_online_dateline');
    $data = $this->db_mls->get()->row_array();

    $return['pc'] = $data['pc'] >= $stattime ? 1 : 0;
    $return['app'] = $data['app'] >= $stattime ? 1 : 0;

    return $return;
  }

  //获取全部经纪人
  public function get_broker($stattime)
  {
    $data = array();
    $data['form_name'] = 'broker_info left join broker_info_sub on broker_info.broker_id = broker_info_sub.broker_id';
    $data['select'] = array('broker_info.broker_id', 'broker_info.phone', 'broker_info.truename', 'broker_info.group_id', 'broker_info.agency_id', 'broker_info.company_id', 'broker_info.master_id', 'broker_info_sub.is_cooperate');
    $data['where'] = array('broker_info.status' => 1);
    $broker_arr = $this->get_data($data, 'dbback_city');

    $return = array();

    if ($broker_arr) {
      $temp = 0;
      foreach ($broker_arr as $broker) {
        $online = $this->get_online_data($broker['broker_id'], $stattime);
        $district = $this->get_company_dist($broker['company_id']);
        $return[$temp]['broker_id'] = $broker['broker_id'];
        $return[$temp]['master_id'] = $broker['master_id'];
        $return[$temp]['phone'] = $broker['phone'];
        $return[$temp]['group_id'] = $broker['group_id'];
        $return[$temp]['truename'] = $broker['truename'];
        $return[$temp]['agency_id'] = $broker['agency_id'];
        $return[$temp]['company_id'] = $broker['company_id'];
        $return[$temp]['pc_online'] = $online['pc'];
        $return[$temp]['app_online'] = $online['app'];
        $return[$temp]['agency'] = $this->get_agency_info($broker['agency_id']);
        $return[$temp]['company'] = $this->get_company_info($broker['company_id']);
        if (is_full_array($district)) {
          $return[$temp]['dist'] = $district[0]['district'];
          $return[$temp]['dist_id'] = $district[0]['id'];
        } else {
          $return[$temp]['dist'] = '';
          $return[$temp]['dist_id'] = 0;
        }
        $return[$temp]['is_cooperate'] = $broker['is_cooperate'];

        $temp++;
      }
    }

    return $return;
  }

  //获取全部经纪人
  public function get_stat_broker($stattime, $num, $order = 'asc')
  {
    $data = array();

    $ymd = date('Y-m-d', $stattime);

    $data = $this->get_data(array('form_name' => 'mls.stat_broker_daily', 'select' => array('id', 'city', 'broker_id'), 'where' => array('ymd ' => $ymd, 'step' => 0), 'order_by_array' => array('id', $order), 'limit' => $num));

    return $data;
  }

  //汇总统计客户经理数据
  public function sum_master($stattime)
  {
    $data = array();
    $ymd = date('Y-m-d', $stattime);
    $sql = "SELECT `city`, `master`, COUNT(id) AS broker_num , SUM(group_id) as group_num, SUM(pc_online) as pc_online, SUM(app_online) as app_online, SUM(is_online) as is_online, SUM(is_cooperate) as is_cooperate, SUM(sell_cooperate_house_num) as sell_cooperate_house_num, SUM(rent_cooperate_house_num) as rent_cooperate_house_num, SUM(sell_cooperate_num) as sell_cooperate_num, SUM(rent_cooperate_num) as rent_cooperate_num, SUM(sell_cooperate_1_num) as sell_cooperate_1_num, SUM(rent_cooperate_1_num) as rent_cooperate_1_num, SUM(sell_cooperate_2_num) as sell_cooperate_2_num, SUM(rent_cooperate_2_num) as rent_cooperate_2_num, SUM(sell_cooperate_time) as sell_cooperate_time, SUM(rent_cooperate_time) as rent_cooperate_time, SUM(sell_collect_open_num) as sell_collect_open_num, SUM(rent_collect_open_num) as rent_collect_open_num, SUM(sell_collect_view_num) as sell_collect_view_num, SUM(rent_collect_view_num) as rent_collect_view_num, SUM(sell_collect_sign_num) as sell_collect_sign_num, SUM(rent_collect_sign_num) as rent_collect_sign_num, SUM(sell_collect_add_num) as sell_collect_add_num, SUM(rent_collect_add_num) as rent_collect_add_num, SUM(collect_used) as collect_used, SUM(gp_site_num) as gp_site_num, SUM(sell_group_publish) as sell_group_publish, SUM(rent_group_publish) as rent_group_publish, SUM(gp_refresh_num) as gp_refresh_num, SUM(gp_used) as gp_used, SUM(sell_publish_num) as sell_publish_num, SUM(rent_publish_num) as rent_publish_num, SUM(sell_num) as sell_num, SUM(rent_num) as rent_num, SUM(sell_outside_num) as sell_outside_num, SUM(rent_outside_num) as rent_outside_num, SUM(sell_pic_num) as sell_pic_num, SUM(rent_pic_num) as rent_pic_num, SUM(sell_pic_2_num) as sell_pic_2_num, SUM(rent_pic_2_num) as rent_pic_2_num, SUM(sell_video_num) as sell_video_num, SUM(rent_video_num) as rent_video_num FROM `stat_broker_daily` WHERE ymd = '" . $ymd . "' GROUP BY `city`, `master`;";
    $data = $this->query($sql);
    return $data;
  }

  //汇总统计城市数据
  public function sum_city($stattime)
  {
    $data = array();
    $ymd = date('Y-m-d', $stattime);
    $sql = "SELECT `city`, COUNT(id) AS broker_num , SUM(group_id) as group_num, SUM(pc_online) as pc_online, SUM(app_online) as app_online, SUM(is_online) as is_online, SUM(is_cooperate) as is_cooperate,SUM(sell_cooperate_house_num) as sell_cooperate_house_num, SUM(rent_cooperate_house_num) as rent_cooperate_house_num, SUM(sell_cooperate_num) as sell_cooperate_num, SUM(rent_cooperate_num) as rent_cooperate_num, SUM(sell_cooperate_1_num) as sell_cooperate_1_num, SUM(rent_cooperate_1_num) as rent_cooperate_1_num, SUM(sell_cooperate_2_num) as sell_cooperate_2_num, SUM(rent_cooperate_2_num) as rent_cooperate_2_num, SUM(sell_cooperate_time) as sell_cooperate_time, SUM(rent_cooperate_time) as rent_cooperate_time, SUM(sell_collect_open_num) as sell_collect_open_num, SUM(rent_collect_open_num) as rent_collect_open_num, SUM(sell_collect_view_num) as sell_collect_view_num, SUM(rent_collect_view_num) as rent_collect_view_num, SUM(sell_collect_sign_num) as sell_collect_sign_num, SUM(rent_collect_sign_num) as rent_collect_sign_num, SUM(sell_collect_add_num) as sell_collect_add_num, SUM(rent_collect_add_num) as rent_collect_add_num, SUM(collect_used) as collect_used, SUM(gp_site_num) as gp_site_num, SUM(sell_group_publish) as sell_group_publish, SUM(rent_group_publish) as rent_group_publish, SUM(gp_refresh_num) as gp_refresh_num, SUM(gp_used) as gp_used, SUM(sell_publish_num) as sell_publish_num, SUM(rent_publish_num) as rent_publish_num, SUM(sell_num) as sell_num, SUM(rent_num) as rent_num, SUM(sell_outside_num) as sell_outside_num, SUM(rent_outside_num) as rent_outside_num, SUM(sell_pic_num) as sell_pic_num, SUM(rent_pic_num) as rent_pic_num, SUM(sell_pic_2_num) as sell_pic_2_num, SUM(rent_pic_2_num) as rent_pic_2_num, SUM(sell_video_num) as sell_video_num, SUM(rent_video_num) as rent_video_num FROM `stat_broker_daily` WHERE ymd = '" . $ymd . "' GROUP BY `city`;";
    $data = $this->query($sql);
    return $data;
  }

  //获得经纪人统计数据
  public function get_broker_stat_arr($stattime, $brokerid)
  {
    $return = array();

    /****************合作数据****************/
    //合作统计中的经纪人ID都是作为甲方来统计的
    //出售出租合作房源量
    $return['sell_cooperate_house_num'] = $this->get_sell_cooperate_num($brokerid);
    $return['rent_cooperate_house_num'] = $this->get_rent_cooperate_num($brokerid);
    //该经纪人合作房源申请量
    $return['sell_cooperate_num'] = $this->get_cooperate_esta_num($brokerid, 'sell');
    $return['rent_cooperate_num'] = $this->get_cooperate_esta_num($brokerid, 'rent');
    //该经纪人合作房源生效合作量
    $return['sell_cooperate_1_num'] = $this->get_cooperate_esta_num($brokerid, 'sell', 1);
    $return['rent_cooperate_1_num'] = $this->get_cooperate_esta_num($brokerid, 'rent', 1);
    //该经纪人合作房源成交合作量
    $return['sell_cooperate_2_num'] = $this->get_cooperate_esta_num($brokerid, 'sell', 2);
    $return['rent_cooperate_2_num'] = $this->get_cooperate_esta_num($brokerid, 'rent', 2);
    //该经纪人合作房源成交周期
    $return['sell_cooperate_time'] = $this->get_coop_time($brokerid, 'sell');
    $return['rent_cooperate_time'] = $this->get_coop_time($brokerid, 'rent');

    /****************采集数据****************/
    //查看量
    $return['sell_collect_open_num'] = $this->get_collect_open_num($stattime, $brokerid, 1);
    $return['rent_collect_open_num'] = $this->get_collect_open_num($stattime, $brokerid, 2);
    //电话查看量
    $return['sell_collect_view_num'] = $this->get_sell_collect_view_data($stattime, $brokerid);
    $return['rent_collect_view_num'] = $this->get_rent_collect_view_data($stattime, $brokerid);
    //标记联系量
    $return['sell_collect_sign_num'] = $this->get_collect_sign_num($stattime, $brokerid, 'sell_house_collect');
    $return['rent_collect_sign_num'] = $this->get_collect_sign_num($stattime, $brokerid, 'rent_house_collect');
    //录入量
    $return['sell_collect_add_num'] = $this->get_collect_add_num($stattime, $brokerid, 'sell_house_collect');
    $return['rent_collect_add_num'] = $this->get_collect_add_num($stattime, $brokerid, 'rent_house_collect');

    /****************群发数据****************/
    //群发站点量
    $return['gp_site_num'] = $this->count_gp_sitenum($brokerid);
    //群发出售量、出租量
    $return['sell_group_publish'] = $this->get_group_publish_data(1, $stattime, $brokerid);
    $return['rent_group_publish'] = $this->get_group_publish_data(2, $stattime, $brokerid);
    //群发刷新量
    $return['gp_refresh_num'] = $this->count_gp_refreshnum($stattime, $brokerid);


    /****************ERP房源量****************/
    //录入量
    $return['sell_publish_num'] = $this->get_sell_publish_data($stattime, $brokerid);
    $return['rent_publish_num'] = $this->get_rent_publish_data($stattime, $brokerid);
    //总量
    $return['sell_num'] = $this->get_sell_num($brokerid);
    $return['rent_num'] = $this->get_rent_num($brokerid);

    /****************外网房源量****************/
    $return['sell_outside_num'] = $this->get_sell_outside_data($brokerid);
    $return['rent_outside_num'] = $this->get_rent_outside_data($brokerid);

    /****************房源图片量****************/
    $return['sell_pic_num'] = $this->get_sell_level_num($brokerid, array(1, 2));
    $return['rent_pic_num'] = $this->get_rent_level_num($brokerid, array(1, 2));
    $return['sell_pic_2_num'] = $this->get_sell_level_num($brokerid, 3);
    $return['rent_pic_2_num'] = $this->get_rent_level_num($brokerid, 3);

    /****************房源视频量****************/
    $return['sell_video_num'] = $this->get_sell_video_num($brokerid);
    $return['rent_video_num'] = $this->get_rent_video_num($brokerid);

    return $return;
  }

  //获得经纪人统计数据
  public function get_broker_stat_arr_2($stattime, $brokerid)
  {
    $return = array();

    //录入量
    $return['sell_collect_add_num'] = $this->get_collect_add_num($stattime, $brokerid, 'sell_house_collect');
    $return['rent_collect_add_num'] = $this->get_collect_add_num($stattime, $brokerid, 'rent_house_collect');

    return $return;
  }

  //采集房源查看量
  public function get_collect_open_num($stattime, $brokerid, $type)
  {
    $endtime = $stattime + 86400;
    $sql = "SELECT count( id ) AS num FROM `collect_click_log` WHERE broker_id = '" . $brokerid . "' and type = '" . $type . "' and (createtime >= '" . $stattime . "' and createtime < '" . $endtime . "')";
    $data = $this->query($sql);
    return $data[0]['num'];
  }

  //采集标记联系量 sell_house_collect rent_house_collect
  public function get_collect_sign_num($stattime, $brokerid, $type)
  {
    $endtime = $stattime + 86400;
    $type = $type == 'sell_house_collect' ? 'sell_house_collect' : 'rent_house_collect';
    $sql = "SELECT count(id) as num FROM `agent_house_judge` WHERE broker_id = '" . $brokerid . "' and tbl_name = '" . $type . "' and (createtime >= '" . $stattime . "' and createtime < '" . $endtime . "') and is_contact = 1";
    $data = $this->query($sql);
    return $data[0]['num'];
  }


  //采集录入量 sell_house_collect rent_house_collect
  public function get_collect_add_num($stattime, $brokerid, $type)
  {
    $endtime = $stattime + 86400;
    $type = $type == 'sell_house_collect' ? 'sell_house_collect' : 'rent_house_collect';
    $sql = "SELECT count(id) as num FROM `agent_house_judge` WHERE broker_id = '" . $brokerid . "' and tbl_name = '" . $type . "' and (createtime >= '" . $stattime . "' and createtime < '" . $endtime . "') and is_input = 1";
    $data = $this->query($sql);
    return $data[0]['num'];
  }

  //经纪人日刷新数
  public function count_gp_refreshnum($stattime, $brokerid)
  {
    $endtime = $stattime + 86400;
    $sql = "select count(id) as num from `group_refresh_msg` where broker_id = '" . $brokerid . "' and status = 1 and (createtime >= '" . $stattime . "' and createtime < '" . $endtime . "')";
    $data = $this->query($sql);
    return $data[0]['num'];
  }

  //经纪人绑定群发站点数
  public function count_gp_sitenum($brokerid)
  {
    $sql = "select count(id) as num from  `mass_site_broker` where broker_id = '" . $brokerid . "' and status = 1";
    $data = $this->query($sql);
    return $data[0]['num'];
  }

  //计算经纪人合作周期
  public function get_coop_time($brokerid, $tbl)
  {
    $this->bd->dbselect('dbback_city');

    $sarr = $earr = array();
    $hour = 0;

    $tbl = $tbl == 'sell' ? 'sell' : 'rent';
    $sql = "select id from cooperate where esta = 7 and tbl = '" . $tbl . "' and brokerid_a = '" . $brokerid . "'";
    $data = $this->query($sql);
    if (is_full_array($data)) {
      $ids = '';
      foreach ($data as $val) {
        $ids .= $ids != '' ? ',' . $val['id'] : $val['id'];
      }

      $sql = "select cid,dateline from cooperate_log where cid in (" . $ids . ") and esta = 1";
      $data = $this->query($sql);
      if (is_full_array($data)) {
        foreach ($data as $val) {
          $sarr[$val['cid']] = $val['dateline'];
        }
      }

      $sql = "select cid,dateline from cooperate_log where cid in (" . $ids . ") and esta = 7";
      $data1 = $this->query($sql);
      if (is_full_array($data1)) {
        foreach ($data1 as $val) {
          $earr[$val['cid']] = $val['dateline'];
        }
      }

      if (is_full_array($sarr) && is_full_array($earr)) {
        $num = $temp = 0;
        foreach ($earr as $key => $val) {
          $temp = $temp + ($val - $sarr[$key]) / 3600;
          $num++;
        }
        $hour = ceil($temp / $num);
      }
    }

    return $hour;
  }

  public function get_app_access_num($starttime, $broker_id, $cityid)
  {
    $ymd = date('Y-m-d', $starttime);
    $where .= "ymd = '" . $ymd . "' and city = '" . $cityid . "' and broker_id = '" . $broker_id . "'";

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('mls.broker_app_access_log');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function update_statdate($id, $statdata, $table)
  {
    $this->db_mls->where(array('id' => $id));
    $this->db_mls->update($table, $statdata);
  }

  public function save_statdata($statdata, $table, $db = 'db_city')
  {
    if ($db == 'db_city') {
      $this->db_city->replace($table, $statdata);
    } else {
      $this->db_mls->replace($table, $statdata);
    }
  }

  public function get_agency_info($agencyid)
  {
    $data = array();
    $data['form_name'] = 'agency';
    $data['select'] = array('name');
    $data['where'] = array('id' => $agencyid);
    $agency_arr = $this->get_data($data, 'dbback_city');

    return $agency_arr[0]['name'];
  }

  public function get_company_info($companyid)
  {
    $data = array();
    $data['form_name'] = 'agency';
    $data['select'] = array('name');
    $data['where'] = array('id' => $companyid);
    $company_arr = $this->get_data($data, 'dbback_city');

    return $company_arr[0]['name'];
  }

  public function get_login_data($starttime, $broker_id)
  {
    $endtime = $starttime + 86399;
    $where .= 'dateline >= ' . $starttime . ' and dateline <= ' . $endtime . ' and broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('login_log');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_sell_num($broker_id)
  {
    $where = 'broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('sell_house');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_rent_num($broker_id)
  {
    $where = 'broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('rent_house');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_sell_cooperate_num($broker_id)
  {
    $where = 'status = 1 and isshare = 1 and broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('sell_house');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_rent_cooperate_num($broker_id)
  {
    $where = 'status = 1 and isshare = 1 and broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('rent_house');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_cooperate_esta_num($broker_id, $tbl, $esta = 0)
  {
    $where = $tbl == 'sell' ? "tbl = 'sell' and brokerid_a = '" . $broker_id . "'" : "tbl = 'rent' and brokerid_a = '" . $broker_id . "'";

    if ($esta == 1) {
      $where .= " and esta >= 4";
    } else if ($esta == 2) {
      $where .= " and esta = 7";
    }

    $data = array();
    $this->dbback_city->select('count(id) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('cooperate');
    $data = $this->dbback_city->get()->row_array();

    return $data['num'];
  }

  public function get_sell_publish_data($starttime, $broker_id)
  {
    $endtime = $starttime + 86399;

    if ($where) {
      $where .= ' and ';
    }
    $where .= 'createtime >= ' . $starttime . ' and createtime <= ' . $endtime . ' and broker_id = ' . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('sell_house');
  }


  public function get_rent_publish_data($starttime, $broker_id)
  {
    $endtime = $starttime + 86399;

    if ($where) {
      $where .= ' and ';
    }
    $where .= 'createtime >= ' . $starttime . ' and createtime <= ' . $endtime . ' and broker_id = ' . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('rent_house');
  }


  public function get_sell_collect_view_data($starttime, $broker_id)
  {
    $endtime = $starttime + 86400;
    $where .= '(createtime >= ' . $starttime . ' and createtime < ' . $endtime . ') and broker_id = ' . $broker_id;

    $where .= ' and tbl_name = "sell_house_collect"';
    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('agent_house_judge');
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }


  public function get_rent_collect_view_data($starttime, $broker_id)
  {
    $endtime = $starttime + 86400;
    $where .= '(createtime >= ' . $starttime . ' and createtime < ' . $endtime . ') and broker_id = ' . $broker_id;

    $where .= ' and tbl_name = "rent_house_collect"';
    $data = array();
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('agent_house_judge');
    $data = $this->dbback_city->get()->row_array();
    return $data['num'];
  }


  public function get_sell_group_publish_data($starttime, $broker_id)
  {
    $start_time = date("Y-m-d", $starttime);
    $where = "ymd = '" . $start_time . "' and sell_type = 1" . ' and broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(id) as all_num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('group_publish_log');
    $data = $this->dbback_city->get()->row_array();
    return $data['all_num'];
  }


  public function get_rent_group_publish_data($starttime, $broker_id)
  {
    $start_time = date("Y-m-d", $starttime);
    $where = "ymd = '" . $start_time . "' and sell_type = 2" . ' and broker_id = ' . $broker_id;

    $data = array();
    $this->dbback_city->select('count(id) as all_num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('group_publish_log');
    $data = $this->dbback_city->get()->row_array();
    return $data['all_num'];
  }

  public function get_group_publish_data($type, $starttime, $broker_id)
  {
    $starttime2 = $starttime + 86399;
    $type = $type == 1 ? 1 : 2;
    $where = "(ymd >= '" . $starttime . "' and ymd <= '" . $starttime2 . "') and sell_type = '" . $type . "' and broker_id = " . $broker_id;

    $data = array();
    $this->dbback_city->select('count(id) as all_num');
    $this->dbback_city->where($where);
    $this->dbback_city->from('group_publish_log');
    $data = $this->dbback_city->get()->row_array();
    return $data['all_num'];
  }

  public function get_sell_outside_data($broker_id)
  {
    $where = 'status = 1 and is_outside = 1 and broker_id = ' . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('sell_house');
  }


  public function get_rent_outside_data($broker_id)
  {
    $where = 'status = 1 and is_outside = 1 and broker_id = ' . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('rent_house');
  }


  //获取房源等级2的出售房源
  public function get_sell_level_num($broker_id, $level)
  {
    if (is_array($level)) {
      $where = "(house_level = '" . $level[0] . "' or house_level = '" . $level[1] . "') and is_outside = 1 and status = 1 and broker_id = " . $broker_id;
    } else {
      $where = 'house_level = ' . $level . ' and is_outside = 1 and status = 1 and broker_id = ' . $broker_id;
    }

    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('sell_house');
  }

  //获取房源等级2的出租房源
  public function get_rent_level_num($broker_id, $level)
  {
    if (is_array($level)) {
      $where = "(house_level = '" . $level[0] . "' or house_level = '" . $level[1] . "') and is_outside = 1 and status = 1 and broker_id = " . $broker_id;
    } else {
      $where = 'house_level = ' . $level . ' and is_outside = 1 and status = 1 and broker_id = ' . $broker_id;
    }
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('rent_house');
  }

  //获取有视频的出售房源
  public function get_sell_video_num($broker_id)
  {
    $where = "video_id <> '' and video_id <> '0' and video_id is not null and broker_id = " . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('sell_house');
  }

  //获取有视频的出租房源
  public function get_rent_video_num($broker_id)
  {
    $where = "video_id <> '' and video_id <> '0' and video_id is not null and broker_id = " . $broker_id;
    //查询条件
    $this->dbback_city->where($where);

    return $this->dbback_city->count_all_results('rent_house');
  }

  //获取公司所属的区属
  public function get_company_dist($companyid)
  {
    $data = array();
    $data['form_name'] = 'agency';
    $data['select'] = array('dist_id');
    $data['where'] = array('id' => $companyid);
    $company_arr = $this->get_data($data, 'dbback_city');
    return $this->get_district_by_id($company_arr[0]['dist_id']);
  }

  //根据区属编号获取名称
  public function get_district_by_id($dist_id)
  {
    $data = array();
    $data['form_name'] = 'district';
    $data['select'] = array('id, district');
    $data['where'] = array('id' => $dist_id);
    $district_arr = $this->get_data($data, 'dbback_city');
    return $district_arr;
  }
}

/* End of file stat_group_publish_model.php */
/* Location: ./applications/mls_job/models/stat_group_publish_model.php */
