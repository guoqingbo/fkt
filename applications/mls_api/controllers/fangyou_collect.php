<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 房友房源数据导入
 *
 * 用于MLS房源导入处理
 *
 *
 * @package         applications
 * @author          lalala
 * @copyright       Copyright (c) 2006 - 2015
 * @version         1.0
 */
class Fangyou_collect extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    //加载出售基本配置MODEL
    $city = $this->input->get('city');
    $this->set_city($city);
    $this->load->model('house_config_model');
    $this->load->model('broker_info_base_model');
    $this->load->model('import_model');
    $this->load->model('district_base_model');
    $this->load->model('community_base_model');
    $this->load->model('api_broker_base_model');
    $this->load->model('customer_base_model');
  }

  //房源导入
  public function index()
  {
    $string = $this->input->get('string');
    $broker_id = $this->input->get('broker_id');
    //获取配置项
    $config = $this->house_config_model->get_config();

    foreach ($config['sell_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($config['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    $nature = array();
    foreach ($config['nature'] as $key => $k) { //性质类型
      $nature[$k] = $key;
    }
    $forward = array();
    foreach ($config['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($config['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    $taxes = array();
    foreach ($config['taxes'] as $key => $k) { //税费类型
      $taxes[$k] = $key;
    }
    $entrust = array();
    foreach ($config['entrust'] as $key => $k) { //委托类型
      $entrust[$k] = $key;
    }

    if ($string) {
      if (!$broker_id) {
        echo "请输入经纪人编号";
        exit;
      }
      $broker_info = $this->broker_info_base_model->get_by_broker_id($broker_id);
      $array = json_decode($string, true);
      $i = 0;
      $j = 0;
      foreach ($array as $key => $val) {
        $insert['broker_id'] = $broker_info['broker_id'];//经纪人编号
        $insert['broker_name'] = $broker_info['truename'];//经纪人名字
        $insert['agency_id'] = $broker_info['agency_id'];//门店id
        $insert['company_id'] = $broker_info['company_id'];//公司id
        $insert['room'] = $val['CountF'] ? $val['CountF'] : 0;   //室
        $insert['hall'] = $val['CountT'] ? $val['CountT'] : 0;   //厅
        $insert['toilet'] = $val['CountW'] ? $val['CountW'] : 0;   //卫
        $insert['balcony'] = $val['CountY'] ? $val['CountY'] : 0;   //阳台
        $insert['dong'] = ltrim(preg_replace("#[^0-9]#", '', $val['BuildNo']), 0); //栋
        $insert['door'] = $val['RoomNo'] ? $val['RoomNo'] : 0;   //房号
        $insert['forward'] = $forward[$val['PropertyDirection']] ? $forward[$val['PropertyDirection']] : 1;   //朝向
        $insert['owner'] = $val['OwnerName'];//业主姓名
        //$insert['bewrite'] = $val['Remark'];//房源描述
        $insert['buildarea'] = $val['SquareUse'] ? $val['SquareUse'] : 0;   //面积
        $insert['buildyear'] = $val['CompleteYear'] ? $val['CompleteYear'] : 0;   //建筑年代
        $insert['createtime'] = strtotime($val['TrustDate']);//创建时间
        $insert['updatetime'] = time();
        $insert['ip'] = get_ip();
        $insert['is_publish'] = 1; //默认群发房源
        $insert['isshare'] = 0; //默认为不合作
        $insert['nature'] = 2; //默认公盘
        $insert['keys'] = 0;
        $insert['totalfloor'] = $val['FloorAll'];   //总楼层
        $floor = explode("/", $val['Floor']);
        if (strpos($floor[0], "-") !== false) { //存在
          $insert['floor_type'] = 2;
          $floor2 = explode("-", $floor[0]);
          $insert['floor'] = $floor2[0];
          $insert['subfloor'] = $floor2[1];
        } else {
          $insert['floor_type'] = 1;
          $insert['floor'] = $floor[0];
        }

        $insert['fitment'] = $fitment[$val['PropertyDecoration']];   //装修情况
        if (!$insert['fitment']) {  //如果在配置项找不到，默认为简装
          $insert['fitment'] = 2;
        }

        $OwnerMobile = explode('/', $val['OwnerMobile']);   //电话
        if (is_full_array($OwnerMobile)) {
          foreach ($OwnerMobile as $k => $v) {
            $insert["telno" . ($k + 1)] = $v;
          }
        }

        $insert['status'] = $status[$val['Status']];//性质类型
        if (!$insert['status']) {
          if ($val['Status'] == '已售' || $val['Status'] == '已租') {
            $insert['status'] = 3;
          } elseif ($val['Status'] == '暂缓') {
            $insert['status'] = 6;
          } else {
            $insert['status'] = 1;
          }
        }

        $insert['sell_type'] = $sell_type[$val['PropertyUsage']];//住宅类型
        if (!$insert['sell_type']) {
          if ($val['PropertyUsage'] == '商住') {
            $insert['sell_type'] = 1;
          } elseif ($val['Status'] == '网点') {
            $insert['status'] = 3;
          } elseif ($val['Status'] == '写厂') {
            $insert['status'] = 5;
          } elseif ($val['Status'] == '铺厂') {
            $insert['status'] = 5;
          } elseif ($val['Status'] == '车位') {
            $insert['status'] = 7;
          } else {
            $insert['status'] = 6;
          }
        }

        $community_info = $this->import_model->community_info(array('cmt_name' => $val['EstateName']));
        if (!$community_info[0]['id']) {
          $dist_arr = $this->district_base_model->get_district_id($val['DistrictName']);
          $street_arr = $this->district_base_model->get_street_id($val['AreaName']);
          if (is_full_array($dist_arr) && is_full_array($street_arr)) {
            $paramArray = array(
              'cmt_name' => $val['EstateName'],//楼盘名称
              'dist_id' => trim($dist_arr['id']),//区属
              'streetid' => trim($street_arr['id']),//板块
              'address' => $val['DistrictName'] . $val['EstateName'],//地址
              'creattime' => time(),
              'status' => 3,
            );
            $add_result = $this->community_base_model->add_community($paramArray);//楼盘数据入库
            if (!empty($add_result) && is_int($add_result)) {
              $where = array('id' => $add_result);
              $community_info = $this->import_model->community_info($where);
            }
          }
        }
        //有楼盘才能导入，没有的直接PASS
        if ($community_info[0]['id']) {
          $insert['block_id'] = $community_info[0]['id'];//楼盘id
          $insert['block_name'] = $community_info[0]['cmt_name'];//楼盘名称
          $insert['district_id'] = $community_info[0]['dist_id'];//区属id
          $insert['street_id'] = $community_info[0]['streetid'];//板块id
          $insert['address'] = $community_info[0]['address'];//地址

          if ($val['Trade'] == "出售") {   //价格或者租金
            $insert['price'] = $val['Price'];
            $insert['lowprice'] = $val['PriceLine'];
            $insert['avgprice'] = $val['PriceUnit'];
            //导入数据的唯一性判断
            $house_num = $this->check_house($broker_info, $insert['block_id'], $insert['door'], $insert['dong'], 'sell_house');
            if ($house_num == 0) {
              if (($this->import_model->add_data($insert, 'db_city', 'sell_house')) > 0) {
                $i++;
              }
            }
          } elseif ($val['Trade'] == "出租") {
            $insert['price'] = $val['RentPrice'];
            if ($val['RentUnitName'] == "元/月") {
              $insert['price_danwei'] = 0;
            } else {
              $insert['price_danwei'] = 1;
            }
            //导入数据的唯一性判断
            $house_num = $this->check_house($broker_info, $insert['block_id'], $insert['door'], $insert['dong'], 'rent_house');
            if ($house_num == 0) {
              if (($this->import_model->add_data($insert, 'db_city', 'rent_house')) > 0) {
                $j++;
              }
            }
          } elseif ($val['Trade'] == "租售") {
            $insert['price'] = $val['Price'];
            $insert['lowprice'] = $val['PriceLine'];
            $insert['avgprice'] = $val['PriceUnit'];
            //导入数据的唯一性判断
            $house_num = $this->check_house($broker_info, $insert['block_id'], $insert['door'], $insert['dong'], 'sell_house');
            if ($house_num == 0) {
              $result = $this->import_model->add_data($insert, 'db_city', 'sell_house');
              if ($result) {
                $i++;
              }
            }
            unset($insert['lowprice']);
            unset($insert['avgprice']);
            $insert['price'] = $val['RentPrice'];
            if ($val['RentUnitName'] == "元/月") {
              $insert['price_danwei'] = 0;
            } else {
              $insert['price_danwei'] = 1;
            }
            //导入数据的唯一性判断
            $house_num = $this->check_house($broker_info, $insert['block_id'], $insert['door'], $insert['dong'], 'rent_house');
            if ($house_num == 0) {
              if ($this->import_model->add_data($insert, 'db_city', 'rent_house')) {
                $j++;
              }
            }
          }
        }
      }
      echo "共有" . $i . "条出售房源录入成功<br/>共有" . $j . "条出租房源录入成功";
    } else {
      echo "请输入参数";
    }
  }


  //判断房源是否重复
  public function check_house($broker_info, $block_id, $door, $dong, $tbl)
  {
    //根据经济人总公司编号获取全部分店信息
    $company_id = intval($broker_info['company_id']);//获取总公司编号
    //获取全部分公司信息
    $agency_list = $this->api_broker_base_model->get_agencys_by_company_id($company_id);
    $arr_agency_id = array();
    foreach ($agency_list as $key => $val) {
      $arr_agency_id[] = $val['agency_id'];
    }
    $agency_ids = implode(',', $arr_agency_id);
    $cond_where = "block_id = '$block_id' and door = '$door' and dong = '$dong' ";
    if ($agency_ids) {
      $cond_where .= " and agency_id in (" . $agency_ids . ")";
    }
    $house_num = $this->import_model->get_count_by_cond($cond_where, $tbl);
    return $house_num;
  }

  //客源导入
  public function customer()
  {
    $string = $this->input->get('string');
    //$string = '[{"Trade":"\u6c42\u8d2d","Floor":"10","Status":"\u6709\u6548","CountF":"3","CountT":"2","CountW":"1","CountY":"2","PropertyUsage":"\u4f4f\u5b85","PropertyDirection":"\u5357","SquareMin":"50","SquareMax":"80","PriceMin":"30.0000","PriceMax":"40.0000","PropertyDecoration":"\u6e05\u6c34","TrustDate":"2014-09-04 00:00:00","CustName":"\u4f55\u5148\u751f","RentUnitName":"\u4e07","OwnerMobile":"1234567","CustTitle":"540103710218231"}]';
    $broker_id = $this->input->get('broker_id');
    //获取配置项
    $config = $this->customer_base_model->get_base_conf();

    foreach ($config['property_type'] as $key => $k) { //物业类型
      $sell_type[$k] = $key;
    }
    $status = array();
    foreach ($config['status'] as $key => $k) { //状态类型
      $status[$k] = $key;
    }
    /*$nature = array();
    foreach($config['nature'] as $key => $k){ //性质类型
        $nature[$k] = $key;
    }*/
    $forward = array();
    foreach ($config['forward'] as $key => $k) { //朝向类型
      $forward[$k] = $key;
    }
    $fitment = array();
    foreach ($config['fitment'] as $key => $k) { //装修类型
      $fitment[$k] = $key;
    }
    /*$entrust = array();
    foreach($config['entrust'] as $key => $k){ //委托类型
        $entrust[$k] = $key;
    }*/

    if ($string) {
      if (!$broker_id) {
        echo "请输入经纪人编号";
        exit;
      }
      $broker_info = $this->broker_info_base_model->get_by_broker_id($broker_id);
      $array = json_decode($string, true);
      //print_r($array);exit;
      $i = 0;
      $j = 0;
      foreach ($array as $key => $val) {
        $insert['broker_id'] = $broker_info['broker_id'];//经纪人编号
        $insert['broker_name'] = $broker_info['truename'];//经纪人名字
        $insert['agency_id'] = $broker_info['agency_id'];//门店id
        $insert['company_id'] = $broker_info['company_id'];//公司id
        $insert['room_max'] = $val['CountF'] ? $val['CountF'] : 0;   //室
        $insert['room_min'] = $val['CountF'] ? $val['CountF'] : 0;   //室
        //$insert['hall'] = $val['CountT']?$val['CountT']:0;   //厅
        //$insert['toilet'] = $val['CountW']?$val['CountW']:0;   //卫
        //$insert['balcony'] = $val['CountY']?$val['CountY']:0;   //阳台
        //$insert['dong'] = ltrim(preg_replace("#[^0-9]#",'',$val['BuildNo']),0); //栋
        //$insert['door'] = $val['RoomNo']?$val['RoomNo']:0;   //房号
        $insert['forward'] = $forward[$val['PropertyDirection']] ? $forward[$val['PropertyDirection']] : 1;   //朝向
        $insert['truename'] = $val['CustName'];//业主姓名
        //$insert['bewrite'] = $val['Remark'];//房源描述
        //$insert['buildarea'] = $val['SquareUse']?$val['SquareUse']:0;   //面积
        $insert['area_min'] = $val['SquareMin'] ? $val['SquareMin'] : 0;   //最小面积
        $insert['area_max'] = $val['SquareMax'] ? $val['SquareMax'] : 0;   //最大面积
        $insert['price_min'] = $val['PriceMin'] ? $val['PriceMin'] : 0;   //最小价格
        $insert['price_max'] = $val['PriceMax'] ? $val['PriceMax'] : 0;   //最大价格
        //$insert['buildyear'] = $val['CompleteYear']?$val['CompleteYear']:0;   //建筑年代
        $insert['creattime'] = strtotime($val['TrustDate']);//创建时间
        $insert['updatetime'] = time();
        $insert['ip'] = get_ip();
        $insert['is_share'] = 0; //默认为不合作
        $insert['public_type'] = 2; //默认公客
        //$insert['totalfloor'] = $val['FloorAll'];   //总楼层
        /*$floor = explode("/",$val['Floor']);
        if(strpos($floor[0],"-") !==false){ //存在
           $insert['floor_type'] = 2;
           $floor2 = explode("-",$floor[0]);
           $insert['floor'] = $floor2[0];
           $insert['subfloor'] = $floor2[1];
        }else{
           $insert['floor_type'] = 1;
           $insert['floor'] = $floor[0];
        }*/
        $insert['floor_max'] = $val['Floor'];
        $insert['floor_min'] = $val['Floor'];
        $insert['fitment'] = $fitment[$val['PropertyDecoration']];   //装修情况
        if (!$insert['fitment']) {  //如果在配置项找不到，默认为简装
          $insert['fitment'] = 2;
        }

        $OwnerMobile = explode('/', $val['OwnerMobile']);   //电话
        if (is_full_array($OwnerMobile)) {
          foreach ($OwnerMobile as $k => $v) {
            $insert["telno" . ($k + 1)] = $v;
          }
        }

        $insert['status'] = $status[$val['Status']];//性质类型
        if (!$insert['status']) {
          if ($val['Status'] == '已购' || $val['Status'] == '已租') {
            $insert['status'] = 3;
          } elseif ($val['Status'] == '暂缓') {
            $insert['status'] = 6;
          } else {
            $insert['status'] = 1;
          }
        }

        $insert['property_type'] = $sell_type[$val['PropertyUsage']];//住宅类型
        if (!$insert['property_type']) {
          if ($val['PropertyUsage'] == '商住') {
            $insert['sell_type'] = 1;
          } elseif ($val['Status'] == '网点') {
            $insert['status'] = 3;
          } elseif ($val['Status'] == '写厂') {
            $insert['status'] = 5;
          } elseif ($val['Status'] == '铺厂') {
            $insert['status'] = 5;
          } elseif ($val['Status'] == '车位') {
            $insert['status'] = 7;
          } else {
            $insert['status'] = 6;
          }
        }

        /*$community_info = $this->import_model->community_info(array('cmt_name'=>$val['EstateName']));
        if(!$community_info[0]['id']){
            $dist_arr = $this->district_base_model->get_district_id($val['DistrictName']);
            $street_arr = $this->district_base_model->get_street_id($val['AreaName']);
            $paramArray = array(
                    'cmt_name' => $val['EstateName'],//楼盘名称
                    'dist_id' => trim($dist_arr['id']),//区属
                    'streetid' => trim($street_arr['id']),//板块
                    'address' => $val['DistrictName'].$val['EstateName'],//地址
                    'creattime'=>time(),
                    'status' => 3,
            );
            $add_result = $this->community_base_model->add_community($paramArray);//楼盘数据入库
            if(!empty($add_result) && is_int($add_result)){
                $where = array('id'=>$add_result);
                $community_info = $this->import_model->community_info($where);
            }
        }
        $insert['block_id'] =  $community_info[0]['id'];//楼盘id
        $insert['block_name'] = $community_info[0]['cmt_name'];//楼盘名称
        $insert['district_id'] = $community_info[0]['dist_id'];//区属id
        $insert['street_id'] =  $community_info[0]['streetid'];//板块id
        $insert['address'] =  $community_info[0]['address'];//地址*/

        if ($val['Trade'] == "求购") {   //价格或者租金
          /*$insert['price'] = $val['Price'];
          $insert['lowprice'] = $val['PriceLine'];
          $insert['avgprice'] = $val['PriceUnit'];
          //导入数据的唯一性判断
          $house_num = $this->check_house($broker_info,$insert['block_id'],$insert['door'],$insert['dong'],'sell_house');
          if($house_num == 0){*/
          if (($this->import_model->add_data($insert, 'db_city', 'buy_customer')) > 0) {
            $i++;
          }
          //}
        } elseif ($val['Trade'] == "求租") {
          //$insert['price'] = $val['RentPrice'];
          if ($val['RentUnitName'] == "元") {
            $insert['price_danwei'] = 0;
          } else {
            $insert['price_danwei'] = 1;
          }
          //导入数据的唯一性判断
          /*$house_num = $this->check_house($broker_info,$insert['block_id'],$insert['door'],$insert['dong'],'rent_house');
          if($house_num == 0){*/
          if (($this->import_model->add_data($insert, 'db_city', 'rent_customer')) > 0) {
            $j++;
          }
          //}
        } elseif ($val['Trade'] == "租购") {
          /*$insert['price'] = $val['Price'];
          $insert['lowprice'] = $val['PriceLine'];
          $insert['avgprice'] = $val['PriceUnit'];
          //导入数据的唯一性判断
          $house_num = $this->check_house($broker_info,$insert['block_id'],$insert['door'],$insert['dong'],'sell_house');
          if($house_num == 0){*/
          $result = $this->import_model->add_data($insert, 'db_city', 'buy_customer');
          if ($result) {
            $i++;
          }
          /*}
          unset($insert['lowprice']);
          unset($insert['avgprice']);
          $insert['price'] = $val['RentPrice'];*/
          if ($val['RentUnitName'] == "元") {
            $insert['price_danwei'] = 0;
          } else {
            $insert['price_danwei'] = 1;
          }
          //导入数据的唯一性判断
          /*$house_num = $this->check_house($broker_info,$insert['block_id'],$insert['door'],$insert['dong'],'rent_house');
          if($house_num == 0){*/
          if ($this->import_model->add_data($insert, 'db_city', 'rent_customer')) {
            $j++;
          }
          //}
        }
      }
      echo "共有" . $i . "条出售客源录入成功<br/>共有" . $j . "条出租客源录入成功";
    } else {
      echo "请输入参数";
    }
  }
}
