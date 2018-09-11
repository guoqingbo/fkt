<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 自动更新数据库类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          lalala
 */
class fang100_job extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city', true);
    $this->set_city($city);
    $this->load->model('fang100_model');
  }

  //委托房源满一个月，自动下架
  public function auto_offshelf()
  {
    $i = 0;
    $j = 0;
    $k = 0;

    $this->fang100_model->set_tbl('ent_sell');
    $entsell = $this->fang100_model->get_all_notdel_list();

    foreach ($entsell as $key => $val) {
      $update_array = array('status' => 2, 'del_reason' => 3, 'del_time' => time());
      $result = $this->fang100_model->update_del($val['id'], $update_array);
      if ($result) {
        $i++;
      }
    }

    $this->fang100_model->set_tbl('seek_sell');
    $seek_sell = $this->fang100_model->get_all_notdel_list();

    foreach ($seek_sell as $key => $val) {
      $update_array = array('status' => 2, 'del_reason' => 3, 'del_time' => time());
      $result = $this->fang100_model->update_del($val['id'], $update_array);
      if ($result) {
        $j++;
      }
    }

    $this->fang100_model->set_tbl('seek_rent');
    $seek_rent = $this->fang100_model->get_all_notdel_list();

    foreach ($seek_rent as $key => $val) {
      $update_array = array('status' => 2, 'del_reason' => 3, 'del_time' => time());
      $result = $this->fang100_model->update_del($val['id'], $update_array);
      if ($result) {
        $k++;
      }
    }

    echo "委托出售有" . $i . "条委托信息已下架" . "<br/>";
    echo "求购有" . $j . "条委托信息已下架" . "<br/>";
    echo "求租有" . $k . "条委托信息已下架" . "<br/>";
  }

  //成都同步平安好房，每天18点将当天同步成功的房源，对应的经纪人+20积分
  public function fang100_activity()
  {
    $this->load->model('api_broker_credit_base_model');
    $this->load->model('pinganhouse_base_model');
    $start_time = strtotime('-1 day 18 hours');//当天零点时间戳
    $end_time = $start_time + 86400;//当天结束时间戳
    //获得当天18点前同步成功的房源，加积分
    $where = "p.is_credit = 0 and p.is_check = 1 and p.update_time >= " . $start_time . ' and p.update_time <= ' . $end_time;
    //获取总数
    $count = $this->pinganhouse_base_model->get_num_by($where);
    $list = $this->pinganhouse_base_model->get_list_by_cond($where, $offset = 0, $count);
    $i = 0;
    if (is_full_array($list)) {
      foreach ($list as $key => $val) {
        $this->api_broker_credit_base_model->set_broker_param('', 1);
        $result = $this->api_broker_credit_base_model->pinganhaofang_jifen($val['broker_id'], $val['house_id']);
        if ($result['status'] == 1) {
          $i++;
          $this->pinganhouse_base_model->update_house($val['id'], array('is_credit' => 1));
        }
      }
    }
    echo $i . '条同步平安好房的房源发放积分';
  }


  public function import_excel()
  {
    $id = $this->input->get('id');
    //获取数据
    $info = $this->fang100_model->get_excel_house($id);
    //加载出售基本配置MODEL
    $this->load->model('house_config_model');
    $data['config'] = $this->house_config_model->get_config();

    $sell_type = array();
    foreach ($data['config']['sell_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($data['config']['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $house_structure = array();
    foreach ($data['config']['house_structure'] as $key => $k) { //性质类型
      $house_structure[$k] = $key;
    }
    $forward = array();
    foreach ($data['config']['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($data['config']['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    if (is_full_array($info)) {
      $this->load->model('sell_model');
      $community_info = $this->sell_model->community_info(array('cmt_name' => $info['cmt_name']));
      if (!$community_info[0]['id']) {
        $this->load->model('district_model');
        $this->load->model('community_base_model');
        //$k[20]$k[21]需要判断为空？
        $dist_arr = $this->district_model->get_district_id($info['district']);
        $street_arr = $this->district_model->get_street_id($info['street']);
        $paramArray = array(
          'cmt_name' => $info['cmt_name'],//楼盘名称
          'dist_id' => trim($dist_arr['id']),//区属
          'streetid' => trim($street_arr['id']),//板块
          'address' => '',//地址
          'status' => 3,
        );
        $add_result = $this->community_base_model->add_community($paramArray);//楼盘数据入库
        if (!empty($add_result) && is_int($add_result)) {
          $where = array('cmt_name' => $info['cmt_name']);
          $community_info = $this->sell_model->community_info($where);
        }
      }
      $res['block_id'] = $community_info[0]['id'];
      $res['block_name'] = $community_info[0]['cmt_name'];
      $res['district_id'] = $community_info[0]['dist_id'];
      $res['street_id'] = $community_info[0]['streetid'] ? $community_info[0]['streetid'] : 0;
      $res['address'] = $community_info[0]['address'];
      $res['owner'] = trim(ltrim($info['owner'], '0'));//业主
      $info['phone'] = explode(';', $info['phone']);
      $res['telno1'] = $info['phone'][0] ? $info['phone'][0] : '';
      $res['telno2'] = $info['phone'][1] ? $info['phone'][1] : '';
      $res['telno3'] = $info['phone'][2] ? $info['phone'][2] : '';
      $floor = explode('.', str_replace(' 楼 ', '', $info['floor']));
      $res['floor'] = $floor[0];
      if ($floor[1]) {
        $res['floor_type'] = 2;
        $res['subfloor'] = $floor[1];
      } else {
        $res['floor_type'] = 1;
      }
      $res['totalfloor'] = intval($info['totalfloor']);
      $res['forward'] = $forward[$info['forward']] ? $forward[$info['forward']] : 0;
      $info['fitment'] = str_replace('装修情况： ', '', $info['fitment']);
      $res['fitment'] = $fitment[$info['fitment']] ? $fitment[$info['fitment']] : 0;
      $res['house_structure'] = $house_structure[$info['house_structure']] ? $house_structure[$info['house_structure']] : 1;
      if ($info['sell_type'] == '车位') {
        $info['sell_type'] == '车库';
      }
      $res['sell_type'] = $sell_type[$info['sell_type']] ? $sell_type[$info['sell_type']] : 0;
      $res['buildarea'] = intval($info['buildarea']) ? intval($info['buildarea']) : 0;
      $res['buildyear'] = intval($info['buildyear']) ? intval($info['buildyear']) : 0;
      $res['price'] = intval($info['price']) ? intval($info['price']) : 0;
      $res['avgprice'] = $res['buildarea'] ? intval($res['price'] * 10000 / $res['buildarea']) : 0;
      $res['keys'] = 0;
      $res['updatetime'] = time();
      $res['ip'] = get_ip();
      $res['is_publish'] = 1; //默认群发房源
      $res['isshare'] = 0; //默认为不合作
      if ($info['status'] == '他售' || $info['status'] == '我售' || $info['status'] == '他租') {
        $info['status'] == '成交';
      } elseif ($info['status'] == '暂缓') {
        $info['status'] == '暂不售（租）';
      } elseif ($info['status'] == '未知' || $info['status'] == '电话错误') {
        $info['status'] == '无效';
      }
      $res['status'] = $status[$info['status']] ? $status[$info['status']] : 1;
      $res['createtime'] = strtotime($info['createtime']);
      $apartment = preg_replace('/([\x80-\xff]*)/i', '', $info['apartment']);
      $res['room'] = substr($apartment, 0, 1) ? substr($apartment, 0, 1) : '';
      $res['hall'] = substr($apartment, 1, 2) ? substr($apartment, 1, 2) : '';
      $res['toilet'] = substr($apartment, 2, 3) ? substr($apartment, 2, 3) : '';
      $res['balcony'] = substr($apartment, 3, 4) ? substr($apartment, 3, 4) : '';
      $dong = explode('栋', $info['unit']);
      $res['dong'] = $dong[0] ? $dong[0] : '';
      $unit = explode('单元', $dong[1]);
      $res['unit'] = $unit[0] ? $unit[0] : '';
      $res['door'] = $unit[1] ? $unit[1] : '';
      print_R($res);
      echo '</br>';
      //通过经纪人电话号码查底所属的基本信息
      $this->load->model('broker_info_model');
      $broker = $this->broker_info_model->get_one_by(array('truename' => $info['broker_name']));
      if (!is_full_array($broker)) {
        $broker = $this->broker_info_model->get_one_by(array('truename' => '刘勇'));
      }
      $res['broker_name'] = $broker['truename'];
      $res['broker_id'] = $broker['broker_id'];
      $res['agency_id'] = $broker['agency_id'] ? $broker['agency_id'] : 0;
      $res['company_id'] = $broker['company_id'] ? $broker['company_id'] : 0;
      switch ($info['property']) {
        case '产权证':
          $res['paperwork'] = 2;
          break;
        case '房改房':
          $res['property'] = 3;
          break;
        case '公产房':
          $res['property'] = 5;
          break;
        case '经济适用房':
          $res['property'] = 2;
          break;
        case '双证齐全':
          $res['paperwork'] = 3;
          break;
        case '两证未办':
          $res['paperwork'] = 0;
          break;
        case '商品房':
          $res['property'] = 1;
          break;
        case '私产房':
          $res['property'] = 5;
          break;
        case '土地证':
          $res['paperwork'] = 1;
          break;
      }
      $res['remark'] = strip_tags($info['remark']);
      $res['remark'] = str_replace('\n', ',', $res['remark']);
      $result = $this->sell_model->add_data($res, 'db_city', 'sell_house');
    }
    if ($result) {
      echo "<script>window.location.href='" . MLS_JOB_URL . "/fang100_job/import_excel?city=pingxiang&id=" . ($id + 1) . "'</script>";
    }

  }
}
