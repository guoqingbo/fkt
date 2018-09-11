<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of collect_details_model
 * 采集 详情
 * @author ccy
 */
class Collect_details_model extends MY_Model
{

  private $cookies = 'memberLogin=memberLogin&MemberId=13281&UserId=%e5%ae%89%e6%ba%90%e6%88%bf%e4%ba%a7&UserPws=e2c68c0a2a54982e653bde2a0fb819a0&type=2&UserName=%e5%ae%89%e6%ba%90%e6%88%bf%e4%ba%a7; expires=Thu, 21-Mar-2019 09:33:37 GMT; path=/';//吴江房产网登录
  private $xmfish_cookies = '24a79_saltkey=txH3hahP; 24a79_winduser=AwBUAAhaC28HUVABUldQUVUFVAcAVVhaWgVUUgIGAQdWAAEAUQUBCDBq6ceNkOKxSDwYVw;';//厦门小鱼房产网登录

  public function __construct()
  {
    parent::__construct();
    $this->load->model('collect_model');//采集数据库操作类
  }

  //吴江房产网登录
  private function wjajw_login()
  {
    $user_name = '安源房产';
    $pwd = 'anyuan123';
    $url = 'http://www.wjajw.com/LoginAPI/LoginAPI.ashx?name=' . $user_name . '&pwd=' . $pwd . '&type=2';
    $tmpInfo = $this->curl->vget($url, '', '', 1);
    preg_match_all('/set\-cookie:([^\r\n]*)/i', $tmpInfo, $matches);
    if ($matches[1][0]) {
      $this->cookies = $matches[1][0];
      return 'success';
    } else {
      return 'failed';
    }
  }

  //厦门小鱼房产网登录
  private function xmfish_login()
  {
    $mobile = '15980807293';
    $password = 'woaini1314';
    $url = 'http://bbs.xmfish.com/login';
    $post_fields = array(
      'mobile' => $mobile,
      'password' => $password
    );
    $tmpInfo = $this->curl->vlogin($url, $post_fields);
    print_r($tmpInfo);
    exit;
    preg_match_all('/set\-cookie:([^\r\n]*)/i', $tmpInfo, $matches);
    if ($matches[1][0]) {
      $this->cookies = $matches[1][0];
      return 'success';
    } else {
      return 'failed';
    }
  }

  //住宅别墅详情采集
  public function collect_info_zhuzhai($data, $con = '')
  {
    if ($data['source_from'] == '58') {
      $this->wuba_collect_info_house($data);
    } elseif ($data['source_from'] == 'ganji') {
      $this->ganji_collect_info_house($data, $con);
    } elseif ($data['source_from'] == 'wjajw') {
      $this->wjajw_collect_info_house($data);
    } elseif ($data['source_from'] == 'sfang') {
      $this->sfang_collect_info_house($data);
    } elseif ($data['source_from'] == 'fdc') {
      $this->fdc_collect_info_house($data);
    } elseif ($data['source_from'] == 'xmhou') {
      $this->xmhouse_collect_info_house($data);
    } elseif ($data['source_from'] == 'xmfis') {
      $this->xmfish_collect_info_house($data);
    }
  }

  //商铺详情采集
  public function collect_info_shangpu($data, $con = '')
  {
    if ($data['source_from'] == '58') {
      $this->wuba_collect_info_shop($data);
    } elseif ($data['source_from'] == 'ganji') {
      $this->ganji_collect_info_shop($data, $con);
    } elseif ($data['source_from'] == 'sfang') {
      $this->sfang_collect_info_shop($data);
    }
  }

  //写字楼详情采集
  public function collect_info_xiezilou($data, $con = '')
  {
    if ($data['source_from'] == '58') {
      $this->wuba_collect_info_office($data);
    } elseif ($data['source_from'] == 'ganji') {
      $this->ganji_collect_info_office($data, $con);
    } elseif ($data['source_from'] == 'sfang') {
      $this->sfang_collect_info_office($data);
    }
  }

  //58房屋采集
  public function wuba_collect_info_house($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 1;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //过滤经纪人电话
      preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<a href="tel:([\d]{11})"/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/query=(.*)"/siU', $con, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 2 && empty($res)) {
        $cpurl = "http://my.58.com/mobileposthistory/?query=" . $urlarr[1];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集发帖次数
        preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
        $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
        if ($postnum > 5) {
//                    $broker_black=array(
//                        'tel' => $info['telno1'],
//                        'store' => '58二手房发帖记录',
//                        'city' =>$info['city'],
//                        'addtime' =>$info['createtime'],
//                        'type' => 1
//                    );
//                    $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                    //是中介房源,请勿入库
//                    echo "<br><h3>此房源为中介房源：</h3><br>链接：".$url;
          $info['isagent'] = 2;
        } else {
          $info['isagent'] = 1;
        }
      }
      //判断详情页是否还有“个人” 电话号码11位
      preg_match('/<em class="shenfen">(.*)<\/em>/siU', $con, $contact);
      if (empty($res) && strstr($contact[1], "个人") && strlen($info['telno1']) > 10) {
        //户型
        preg_match('/<div class="su_tit">户型：(.*)<\/li>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = intval($result[0][0]); //户型（室）
        $info['hall'] = '';//户型（厅）
        $info['toilet'] = '';//户型（卫）
        if (count($result[0]) == 2) {
          $info['buildarea'] = floatval($result[0][1]);//面积
        } elseif (count($result[0]) == 3) {
          if (strstr($layout, "厅")) {
            $info['hall'] = intval($result[0][1]);//户型（厅）
          } else {
            $info['toilet'] = intval($result[0][1]);//户型（卫）
          }
          $info['buildarea'] = floatval($result[0][2]);//面积
        } else {
          $info['hall'] = intval($result[0][1]);//户型（厅）
          $info['toilet'] = intval($result[0][2]);//户型（卫）
          $info['buildarea'] = floatval($result[0][3]);//面积
        }
        //楼层
        preg_match('/房屋楼层：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $floors);
        //处理楼层
        $totalfloor = $type_floor = '';
        preg_match("/(\d+)层/", $floors[1], $totalfloor);
        $info['totalfloor'] = intval($totalfloor[1]);//楼层（总层数）
        if ($info['totalfloor'] != 0) {
          preg_match("/(.*)层\(/siU", $floors[1], $type_floor);
          $info['type_floor'] = $this->collect_model->con_replace(strip_tags($type_floor[1]));//楼层 高中低
        }
        //曹成远数据处理
//                preg_match_all($reg_num,$floors[1],$floor_num);//取出数值
//                if(count($floor_num[0])==2){
//                    $info['floor'] = $floor_num[0][0];//楼层（所属层）
//                    $info['totalfloor'] = $floor_num[0][1];//楼层（总层数）
//                }elseif(count($floor_num[0])==1){
//                    if(strstr($floors[1],"层")){
//                        $info['floor'] = $floor_num[0][0];//楼层（所属层）
//                    }else{
//                        $info['totalfloor'] = $floor_num[0][0];//楼层（总层数）
//                    }
//                }
        //房源标题
        preg_match('/<div class="bigtitle" >(.*)<\/h1>/siU', $con, $houseid);
        $houseid = explode(')', $houseid[1]);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/联系人：.*<span style="float:left;margin-right:10px;">(.*)<\/span>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $contacts = explode('（', $contact[1]);
          $info['owner'] = $this->collect_model->con_replace(strip_tags($contacts[0]));
        } else {
          $info['owner'] = "个人";
        }
        //楼盘地址
        preg_match('/地址：.*<div class="su_con .*">(.*)<\/div>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          //去除有（地图街景）
          $house_addresss = explode('(', $house_address[1]);
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($house_addresss[0]));
        }
        //区属板块小区名
        preg_match('/<div class="su_tit">位置：(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->collect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($addresss) == 2) {
          $info['district'] = $addresss[0];//区属
          $house_name = $addresss[1];//小区名
        } elseif (count($addresss) == 3) {
          $info['district'] = $addresss[0];//区属
          $info['block'] = $addresss[1];//板块
          $house_name = $addresss[2];//小区名
        }
        //小区名去除（租售信息）
        $house_names = explode('（', $house_name);
        $info['house_name'] = $house_names[0];
        //房源照片
        preg_match_all('/<div class="descriptionImg">.*<img src="(.*)".*<\/div>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //总价
        preg_match('/class="bigpri arial">(.*)<\/span>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/售价：<\/div>.*\（(.*)元\/㎡\）/siU', $con, $average_price);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($average_price[1]));
        //用途（住宅、别墅、写字楼）
        preg_match('/住宅类别：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/朝向：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $direction);
        $direction[1] = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($direction[1]) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度：.*<li class="des_cols2">(.*)<a.*/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          case "婚装":
            $info['serverco'] = 6;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match_all('/建造年代：<\/li>(.*)<\/li>/siU', $con, $buildyears);
        $buildyears[1][0] = $this->collect_model->con_replace(strip_tags($buildyears[1][0]));
        preg_match_all($reg_num, $buildyears[1][0], $buildyear);//取出数值
        $info['buildyear'] = $buildyear[0][0] ? $buildyear[0][0] : 0;
        //房源描述-备注
        preg_match('/<article class="description_con " >(.*)<p class="mb20"/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租  没有发帖记录
      //过滤经纪人电话
      preg_match('/http:\/\/(.*).58.com\/zufang\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/zufang/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span class="meta-phone">([\d]{11})<\/span>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        if ($info['telno1'] == '') {//电话号码存在****的情况
          preg_match('/\d+/', $urlarr[2], $house_ids);
          $house_id = $house_ids[0];
          $cpurl1 = "http://app.58.com/api/windex/scandetail/car/" . $house_id . "/?pid=799";
          $cpcon1 = $this->collect_model->vcurl($cpurl1, $compress, 1);  #采集电话
          preg_match('/href="tel:(.*)"/siU', $cpcon1, $photoarr1);
          if (!empty($photoarr1[1])) {
            if (strstr($photoarr1[1], '-')) {
              $info['telno1'] = $this->collect_model->con_replace(strip_tags(str_replace("-", "", $photoarr1[1])));
            } else {
              $info['telno1'] = $this->collect_model->con_replace(strip_tags($photoarr1[1]));
            }
          } else {
            $info['telno1'] = '';
          }
        }
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //判断详情页是否还有“个人” 电话号码11位
      preg_match('/联系：<\/span>(.*)<\/span>/siU', $con, $contact);
      if (empty($res) && strstr($contact[1], "个人") && strlen($info['telno1']) > 10) {
        //户型 楼层 面积
        preg_match('/房屋：<\/span>(.*)<br>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        //处理室厅卫面积和楼层
        $room = $hall = $toilet = $buildarea = $totalfloor = '';
        preg_match("/(\d+(\.\d{0,2})?)室/", $layout, $room);
        preg_match("/(\d+(\.\d{0,2})?)厅/", $layout, $hall);
        preg_match("/(\d+(\.\d{0,2})?)卫/", $layout, $toilet);
        preg_match("/(\d+(\.\d{0,2})?)m²/", $layout, $buildarea);
        preg_match("/(\d+(\.\d{0,2})?)层/", $layout, $totalfloor);
        $info['room'] = intval($room[1]);//户型（室）
        $info['hall'] = intval($hall[1]);//户型（厅）
        $info['toilet'] = intval($toilet[1]);//户型（卫）
        $info['buildarea'] = intval($buildarea[1]);//面积
        $info['totalfloor'] = intval($totalfloor[1]);//楼层（总层数）
        if ($info['totalfloor'] != 0) {
          preg_match("/(.*)层/siU", $layout, $typefloor);
          $type_floor = explode('-', $typefloor[1]);
          $info['type_floor'] = $type_floor[count($type_floor) - 1];//楼层 高中低
        }
        //曹成远数据处理
//                preg_match_all($reg,$layout,$result);//取出数值
//                $info['room'] =$result[0][0]; //户型（室）
//                $info['hall']='';//户型（厅）
//                $info['toilet']='';//户型（卫）
//                if(count($result[0])==4){
//                    $info['buildarea'] =$result[0][1];//面积
//                    $info['floor'] = $result[0][2];//楼层（所属层）
//                    $info['totalfloor'] = $result[0][3];//楼层（总层数）
//                }elseif(count($result[0])==5){
//                    if(strstr($layout,"厅")){
//                        $info['hall']=$result[0][1];//户型（厅）
//                    }else{
//                        $info['toilet']=$result[0][1];//户型（卫）
//                    }
//                    $info['buildarea'] =$result[0][2];//面积
//                    $info['floor'] = $result[0][3];//楼层（所属层）
//                    $info['totalfloor'] = $result[0][4];//楼层（总层数）
//                }elseif(count($result[0])==6){
//                    $info['hall']=$result[0][1];//户型（厅）
//                    $info['toilet']=$result[0][2];//户型（卫）
//                    $info['buildarea'] =$result[0][3];//面积
//                    $info['floor'] = $result[0][4];//楼层（所属层）
//                    $info['totalfloor'] = $result[0][5];//楼层（总层数）
//                }
        //用途（住宅、别墅、写字楼） 装修 朝向
        preg_match('/房屋：<\/span>.*<br>(.*)<\/div>/siU', $con, $desc);
        $desc = $this->collect_model->con_replace(strip_tags($desc[1]));
        $descs = explode('-', $desc);
        $serverco = $descs[0];//装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        switch ($serverco) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          case "婚装":
            $info['serverco'] = 6;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/房屋：<\/span>.*朝向(.*)\-/siU', $con, $directions);
        $direction = $this->collect_model->con_replace(strip_tags($directions[1]));
        switch ($direction) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        $type = $descs[3];//用途（住宅、别墅、写字楼）
        switch ($type) {
          case "普通住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //房源标题
        preg_match('/<h1 class="main-title font-heiti">(.*)<\/h1>/siU', $con, $houseid);
        $houseid = explode('(', $houseid[1]);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[0]));
        //楼盘地址
        preg_match('/地址：<\/span>(.*)<\/div>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          //去除有（地图街景）
          $house_addresss = explode('(', $house_address[1]);
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($house_addresss[0]));
        }
        //房源照片
        preg_match_all('/<li class="house-images-wrap"><img lazy_src="(http:\/\/pic.*)" src/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //建筑年代
        preg_match_all('/建筑年代<\/td>(.*)<\/td>/siU', $con, $buildyears);
        $buildyears[1][0] = $this->collect_model->con_replace(strip_tags($buildyears[1][0]));
        preg_match_all($reg_num, $buildyears[1][0], $buildyear);//取出数值
        $info['buildyear'] = $buildyear[0][0] ? $buildyear[0][0] : 0;
        //租金
        preg_match('/<em class="house-price">(.*)<\/em>/siU', $con, $total_price);
        $total_prices = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为1
        $info['price'] = is_numeric($total_prices) ? $total_prices : "0";
        //付款方式
        preg_match('/<span class="pay-method f16 c70">(.*)<\/span>/siU', $con, $pricetype);
        if (empty($pricetype[1])) {
          $info['pricetype'] = "押一付三";
        } else {
          $info['pricetype'] = $this->collect_model->con_replace(strip_tags($pricetype[1]));
        }
        //区属板块小区名
        preg_match('/小区：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->collect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($addresss) == 1) {
          $house_name = $addresss[1];//小区名
        } elseif (count($addresss) == 2) {
          $info['district'] = $addresss[0];//区属
          $house_name = $addresss[1];//小区名
        } elseif (count($addresss) == 3) {
          $info['district'] = $addresss[0];//区属
          $info['block'] = $addresss[1];//板块
          $house_name = $addresss[2];//小区名
        }
        //小区名去除（租售信息）
        $house_names = explode('（', $house_name);
        $info['house_name'] = $house_names[0];
        //联系人
        preg_match('/联系：.*<div class="fl c70">(.*)<\/span>/siU', $con, $contact);
        $contact[1] = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (strlen(trim($contact[1])) > 0) {
          $contacts = explode('(', $contact[1]);
          $info['owner'] = $this->collect_model->con_replace(strip_tags($contacts[0]));
        } else {
          $info['owner'] = "个人";
        }
        //房源描述-备注
        preg_match('/<div class="description-content">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //58商铺采集
  public function wuba_collect_info_shop($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 1;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //采集电话
      preg_match('/http:\/\/(.*).58.com\/shangpu\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/shangpu/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/query=(.*)"/siU', $con, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 2 && empty($res)) {
        $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集发帖次数
        preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
        $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
        if ($postnum > 5) {
//                    $broker_black=array(
//                        'tel' => $info['telno1'],
//                        'store' => '58商铺出售发帖记录',
//                        'city' =>$info['city'],
//                        'addtime' =>$info['createtime'],
//                        'type' => 1
//                    );
//                    $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                    //是中介房源,请勿入库
//                    echo "<br><h3>此房源为中介房源：</h3><br>链接：".$url;
          $info['isagent'] = 2;
        } else {
          $info['isagent'] = 1;
        }
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }
        //商铺标题
        preg_match('/<h1 style="font-size:22px;">（出售）(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //商铺照片
        preg_match_all('/img_list.push\("(.*)"\);/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/<li>地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/<li>区域：(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
          $info['block'] = $this->collect_model->con_replace(strip_tags($mess[1]));  //板块
        }
        //商铺面积
        preg_match('/<li>面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/<li>类型：(.*)<li>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1]));//类型
        //售价
        preg_match('/售价：<em class="redfont">(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //房源描述-备注
        preg_match('/<div class="maincon" name="data_2">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['sell_type'] = 3;
        //过滤重复房源hash 采集来源*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      $info['rent_type'] = 3;
      //采集电话、联系方式
      preg_match('/http:\/\/(.*).58.com\/shangpu\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/shangpu/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/query=(.*)"/siU', $con, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 2 && empty($res)) {
        $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集发帖次数
        preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
        $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
        if ($postnum > 5) {
//                    $broker_black=array(
//                        'tel' => $info['telno1'],
//                        'store' => '58商铺出租发帖记录',
//                        'city' =>$info['city'],
//                        'addtime' =>$info['createtime'],
//                        'type' => 1
//                    );
//                    $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                    //是中介房源,请勿入库
//                    echo "<br><h3>此房源为中介房源：</h3><br>链接：".$url;
          $info['isagent'] = 2;
        } else {
          $info['isagent'] = 1;
        }
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/agencyname:\'(.*)\',/siU', $con, $contact);
      if (empty($res) && strstr($contact[1], "个人") && strlen($info['telno1']) > 10) {
        //商铺标题
        preg_match('/<h1 style="font-size:22px;">（出租）(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }
        //商铺照片
        preg_match_all('/img_list.push\("(.*)"\);/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/<li>地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/<li>区域：(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
          $info['block'] = $this->collect_model->con_replace(strip_tags($mess[1]));  //板块
        }
        //商铺面积
        preg_match('/<li>面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/<li>类型：(.*)<li>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1]));//类型
        //房源描述-备注
        preg_match('/<div class="maincon" name="data_2">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //租金
        preg_match('/<em class="redfont">(.*)<\/em>/siU', $con, $total_price);
        $rent_price = $this->collect_model->con_replace(strip_tags($total_price[1]));
        if (strstr($rent_price, "-")) {//范围价格过滤
          $price = 1;
        } else {
          if ($rent_price == '面议') {
            $info['price'] = $rent_price;
          } else {
            preg_match('/<em class="redfont">.*<\/em>(.*)<a href/siU', $con, $rent_type);
            $rent_price_type = $this->collect_model->con_replace(strip_tags($rent_type[1]));
            switch ($rent_price_type) {
              case '元/㎡/天':
                $info['price'] = $rent_price * $info['buildarea'] * 30;
                break;
              case '元/月':
                $info['price'] = $rent_price;
                break;
            }
          }
        }
        //过滤重复房源hash 采集来源*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //58写字楼采集
  public function wuba_collect_info_office($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 1;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //采集电话
      preg_match('/http:\/\/(.*).58.com\/zhaozu\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/zhaozu/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/query=(.*)"/siU', $con, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 2 && empty($res)) {
        $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集发帖次数
        preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
        $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
        if ($postnum > 5) {
//                    $broker_black=array(
//                        'tel' => $info['telno1'],
//                        'store' => '58写字楼出售发帖记录',
//                        'city' =>$info['city'],
//                        'addtime' =>$info['createtime'],
//                        'type' => 1
//                    );
//                    $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                    //是中介房源,请勿入库
//                    echo "<br><h3>此房源为中介房源：</h3><br>链接：".$url;
          $info['isagent'] = 2;
        } else {
          $info['isagent'] = 1;
        }
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="w headline">.*出售\)(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //楼盘
        preg_match('/<li><i>楼盘：<\/i>(.*)<\/li>/siU', $con, $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1]));
        //写字楼地址
        preg_match('/<li><i>地段：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/<li><i>区域：<\/i>(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
          $info['block'] = $this->collect_model->con_replace(strip_tags($mess[1]));  //板块
        }
        //写字楼面积
        preg_match('/<li><i>面积：<\/i>(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //写字楼类型
        preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_type);
        $info['office_type'] = $this->collect_model->con_replace(strip_tags($office_type[1]));//类型
        //售价
        preg_match('/<li><i>价格：<\/i>(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //写字楼照片
        preg_match_all('/img_list.push\("(.*)"\);/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //房源描述-备注
        preg_match('/<div class="maincon" name="data_2">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['sell_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      //采集电话、联系方式
      preg_match('/http:\/\/(.*).58.com\/zhaozu\/(.*).shtml/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 3) {
        $cpurl = "http://m.58.com/" . $urlarr[1] . "/zhaozu/" . $urlarr[2] . ".shtml";
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<span class="f12"><a target="_blank" href="(.*)"/siU', $con, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 2 && empty($res)) {
        $cpcon = $this->collect_model->vcurl($urlarr[1], $compress, 1);  #采集发帖次数
        preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
        $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
        if ($postnum > 5) {
//                    $broker_black=array(
//                        'tel' => $info['telno1'],
//                        'store' => '58写字楼出租发帖记录',
//                        'city' =>$info['city'],
//                        'addtime' =>$info['createtime'],
//                        'type' => 1
//                    );
//                    $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                    //是中介房源,请勿入库
//                    echo "<br><h3>此房源为中介房源：</h3><br>链接：".$url;
          $info['isagent'] = 2;
        } else {
          $info['isagent'] = 1;
        }
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="w headline">.*出租\)(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //写字楼照片
        preg_match_all('/img_list.push\("(.*)"\);/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //楼盘
        preg_match('/<li><i>楼盘：<\/i>(.*)<\/li>/siU', $con, $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1]));
        //写字楼地址
        preg_match('/<li><i>地段：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/<li><i>区域：<\/i>(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $this->collect_model->con_replace(strip_tags($mess[0]));  //区属
          $info['block'] = $this->collect_model->con_replace(strip_tags($mess[1]));  //板块
        }
        //写字楼类型
        preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_type);
        $info['office_type'] = $this->collect_model->con_replace(strip_tags($office_type[1]));//类型
        //房源描述-备注
        preg_match('/<div class="maincon" name="data_2">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //写字楼面积
        preg_match('/<li><i>面积：<\/i>(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        if (strstr($data['buildarea'], "-")) {//范围面积过滤
          $buildarea = 1;
        } else {
          //租金
          preg_match('/<li><i>价格：<\/i>(.*)<\/em>/siU', $con, $total_price);
          $rent_price = $this->collect_model->con_replace(strip_tags($total_price[1]));
          if (strstr($rent_price, "-")) {//范围价格过滤
            $price = 1;
          } else {
            if ($rent_price == '面议') {
              $info['price'] = $rent_price;
            } else {
              preg_match('/<li><i>价格：<\/i>.*<\/em>(.*)<a href/siU', $con, $rent_type);
              $rent_price_type = $this->collect_model->con_replace(strip_tags($rent_type[1]));
              switch ($rent_price_type) {
                case '元/㎡/月':
                  $info['price'] = $rent_price * $data['buildarea'];
                  break;
                case '元/㎡/天':
                  $info['price'] = $rent_price * $data['buildarea'] * 30;
                  break;
                case '元/月':
                  $info['price'] = $rent_price;
                  break;
              }
            }
          }
        }
        $info['rent_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //赶集房屋采集
  public function ganji_collect_info_house($data, $con = '')
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 0;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    if ($con == '') {
      $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    }
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //过滤经纪人电话
      preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 4) {
        $cpurl = "http://3g.ganji.com/" . $urlarr[1] . "_" . $urlarr[2] . "/" . $urlarr[3];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span>联系电话<\/span><i id="midPhoneShow">([\d]{11})<\/i>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        if ($info['telno1'] == '') {//电话号码存在****的情况(首先去查看电话查看)
          $cpurl1 = "http://3g.ganji.com/index.php?a=fcj&from=wap&puid=" . $urlarr[3];
          $cpcon1 = $this->collect_model->vcurl($cpurl1, $compress, 1);  #采集电话
          preg_match('/查看完整电话.*href="tel:([\d]{11})"/siU', $cpcon1, $photoarr1);
          $info['telno1'] = $photoarr1[1] != '' ? $this->collect_model->con_replace(strip_tags($photoarr1[1])) : '';
          if ($info['telno1'] == '') {//电话号码存在****的情况（再去详情页查看）
            preg_match('/联系方式：<\/span>(.*)<\/span>/siU', $con, $tel);
            $info['telno1'] = is_array($tel) && count($tel) == 2 ? $this->collect_model->con_replace(strip_tags($tel[1])) : '';
          }
        }
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<p class="my-shop mb-15">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match_all('/title=\'查看大图\' src="(.*)"/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/位<i class="letter-space-8"><\/i>置：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->collect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($addresss) == 2) {
          $info['district'] = $addresss[1];//区属
        } elseif (count($addresss) == 3) {
          $info['district'] = $addresss[1];//区属
          $info['block'] = $addresss[2];//板块

        }
        // 小区地址
        preg_match('/<span class="addr-area"(.*)<\/span>/siU', $con, $address);
        $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[0]));
        //小区名
        preg_match('/小<i class="letter-space-8"><\/i>区：<\/span>(.*)<\/a>/siU', $con, $building);
        if (!empty($building)) {
          $buildings = $this->collect_model->con_replace(strip_tags($building[1]));
          $house_names = explode('(', $buildings);
          $info['house_name'] = $house_names[0];
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/房屋类型：<\/span>(.*)<\/li>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/售<i class=".*"><\/i>价：.*<b class=".*">(.*)<\/b>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/单<i class=".*"><\/i>价：<\/span>(.*)元.*<\/li>/siU', $con, $average_price);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($average_price[1]));
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/况：<\/span>(.*)\-/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        //$directions= ltrim($directions, "朝");
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "东南朝向":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "西南朝向":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "西北朝向":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "东北朝向":
            $info['forward'] = 8;
            break;
          case "东西朝向":
            $info['forward'] = 9;
            break;
          case "南北朝向":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户<i class="letter-space-8"><\/i>型：<\/span>(.*)<\/li>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = intval($result[0][0]); //户型（室）
        $info['hall'] = '';//户型（厅）
        $info['toilet'] = '';//户型（卫）
        if (count($result[0]) == 2) {
          $info['buildarea'] = floatval($result[0][1]);//面积
        } elseif (count($result[0]) == 3) {
          if (strstr($layout, "厅")) {
            $info['hall'] = intval($result[0][1]);//户型（厅）
          } else {
            $info['toilet'] = intval($result[0][1]);//户型（卫）
          }
          $info['buildarea'] = floatval($result[0][2]);//面积
        } else {
          $info['hall'] = intval($result[0][1]);//户型（厅）
          $info['toilet'] = intval($result[0][2]);//户型（卫）
          $info['buildarea'] = floatval($result[0][3]);//面积
        }
        //房源描述-备注
        preg_match('/房源描述：<\/strong>.*class="summary\-cont">(.*)<p class="clear">/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼<i class="letter-space-8"><\/i>层：<\/span>(.*)<\/li>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
//                    $info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
          if ($info['totalfloor'] != 0) {
            preg_match("/(.*)层/siU", $floors[1], $type_floor);
            $info['type_floor'] = $this->collect_model->con_replace(strip_tags($type_floor[1]));//楼层 高中低
          }
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度：<\/span>(.*)<\/li>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          case "婚装":
            $info['serverco'] = 6;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match('/概<i class="letter-space-8"><\/i>况：<\/span>(.*)<\/li>/siU', $con, $buildyear);
        preg_match_all($reg_num, $buildyear[1], $buildyear);//取出数值
        $info['buildyear'] = date("Y", time()) - $buildyear[0][0];
        $info['buildyear'] = $info['buildyear'] ? $info['buildyear'] : 0;
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租  没有发帖记录
      //过滤经纪人电话
      preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $url, $urlarr);
      if (is_array($urlarr) && count($urlarr) == 4) {
        $cpurl = "http://3g.ganji.com/" . $urlarr[1] . "_" . $urlarr[2] . "/" . $urlarr[3];
        $cpcon = $this->collect_model->vcurl($cpurl, $compress, 1);  #采集电话
        preg_match_all('/<span>联系电话<\/span><i id="midPhoneShow">([\d]{11})<\/i>/siU', $cpcon, $photoarr);
        $info['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        if ($info['telno1'] == '') {//电话号码存在****的情况(查看电话查看)
          $cpurl1 = "http://3g.ganji.com/index.php?a=fcj&from=wap&puid=" . $urlarr[3];
          $cpcon1 = $this->collect_model->vcurl($cpurl1, $compress, 1);  #采集电话
          preg_match('/查看完整电话.*href="tel:([\d]{11})"/siU', $cpcon1, $photoarr1);
          $info['telno1'] = $photoarr1[1] != '' ? $this->collect_model->con_replace(strip_tags($photoarr1[1])) : '';
        }
      }
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<p class="my-shop mb-15">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match_all('/title=\'查看大图\' src="(.*)"/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/位<i class="letter-space-8"><\/i>置：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->collect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($addresss) == 2) {
          $info['district'] = $addresss[1];//区属
        } elseif (count($addresss) == 3) {
          $info['district'] = $addresss[1];//区属
          $info['block'] = $addresss[2];//板块
        }
        // 小区地址
        preg_match('/<span class="addr-area"(.*)<\/span>/siU', $con, $address);
        $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[0]));
        //小区名
        preg_match('/小<i class="letter-space-8"><\/i>区：<\/span>(.*)<\/a>/siU', $con, $building);
        if (!empty($building)) {
          $buildings = $this->collect_model->con_replace(strip_tags($building[1]));
          $house_names = explode('(', $buildings);
          $info['house_name'] = $house_names[0];
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/况：<\/span>.*\-(.*)\-/siU', $con, $type);
        $type[1] = $this->collect_model->con_replace(strip_tags($type[1]));
        switch ($type[1]) {
          case "普通住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //租金
        preg_match('/租<i class=".*"><\/i>金：.*<b class=".*">(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为0
        $info['price'] = is_numeric($total_prices) ? $total_prices : "0";
        //付款方式
        preg_match('/租<i class=".*"><\/i>金：.*<span class="fl">元\/月(.*)<\/span>/siU', $con, $pricetype);
        if (strlen(trim($pricetype[1])) > 0) {
          $pricetypes = str_replace(array("(", ")"), "", $pricetype[1]);
          $info['pricetype'] = $this->collect_model->con_replace(strip_tags($pricetypes));
        } else {
          $info['pricetype'] = "押一付三";
        }
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/况：<\/span>(.*)\-/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        //$directions= ltrim($directions, "朝");
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "东南朝向":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "西南朝向":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "西北朝向":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "东北朝向":
            $info['forward'] = 8;
            break;
          case "东西朝向":
            $info['forward'] = 9;
            break;
          case "南北朝向":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户<i class="letter-space-8"><\/i>型：<\/span>(.*)<\/li>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = intval($result[0][0]); //户型（室）
        $info['hall'] = '';//户型（厅）
        $info['toilet'] = '';//户型（卫）
        if (count($result[0]) == 2) {
          $info['buildarea'] = floatval($result[0][1]);//面积
        } elseif (count($result[0]) == 3) {
          if (strstr($layout, "厅")) {
            $info['hall'] = intval($result[0][1]);//户型（厅）
          } else {
            $info['toilet'] = intval($result[0][1]);//户型（卫）
          }
          $info['buildarea'] = floatval($result[0][2]);//面积
        } else {
          $info['hall'] = intval($result[0][1]);//户型（厅）
          $info['toilet'] = intval($result[0][2]);//户型（卫）
          $info['buildarea'] = floatval($result[0][3]);//面积
        }
        //房源描述-备注
        preg_match('/房源描述：<\/strong>.*class="summary\-cont">(.*)<p class="clear">/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼<i class="letter-space-8"><\/i>层：<\/span>(.*)<\/li>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
//                    $info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
          if ($info['totalfloor'] != 0) {
            preg_match("/(.*)层/siU", $floors[1], $type_floor);
            $info['type_floor'] = $this->collect_model->con_replace(strip_tags($type_floor[1]));//楼层 高中低
          }
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/况：<\/span>.*\-.*\-(.*)<\/li>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          case "婚装":
            $info['serverco'] = 6;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //赶集商铺采集
  public function ganji_collect_info_shop($data, $con = '')
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 0;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    if ($con == '') {
      $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    }
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //采集电话
      preg_match('/联系方式：(.*)<\/em>/siU', $con, $tel);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($tel[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/在线联系：(.*)<\/i>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        } else {
          $info['owner'] = "个人";
        }
        //商铺标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //楼盘名称
        preg_match('/商铺名称：(.*)<\/li>/siU', $con, $building);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($building[1]));
        //商铺照片
        preg_match_all('/title=\'查看大图\' src="(.*)" \/>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/商铺地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/所在区域：<\/span>(.*)<\/li>/siU', $con, $mess);
        $mess[1] = $this->collect_model->con_replace(strip_tags($mess[1]));
        $mess = explode('-', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[1]);
          $info['district'] = $block_addresss[0];  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $mess[1];  //区属
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[2]);
          $info['block'] = $block_addresss[0];  //板块
        }
        //商铺面积
        preg_match('/商铺面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/商铺类型：(.*)<\/li>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1]));//类型
        //售价
        preg_match('/商铺售价：(.*)<\/b>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为0
        $data['price'] = $total_prices ? $total_prices : "0";
        //房源描述-备注
        preg_match('/<div class="summary-cont">(.*)<p class="clear">/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //适合经营
        preg_match('/适合经营.*<\/dl>/siU', $con, $suits);
        if (!empty($suits)) {
          preg_match_all('/<em class=ico-equip-has><\/em>(.*)<\/span>/siU', $suits[0], $suit_manage);
          $info['suit_manage'] = implode("*", $suit_manage[1]);
        }
        $info['sell_type'] = 3;
        //过滤重复房源hash 采集来源*楼盘名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      $info['rent_type'] = 3;
      //采集电话
      preg_match('/联系方式：(.*)<\/em>/siU', $con, $tel);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($tel[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contact);
      if (empty($res) && strstr($contact[1], "个人") && strlen($info['telno1']) > 10) {
        //商铺标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/在线联系：(.*)<\/i>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        } else {
          $info['owner'] = "个人";
        }
        //商铺照片
        preg_match_all('/title=\'查看大图\' src="(.*)" \/>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/商铺地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/所在区域：<\/span>(.*)<\/li>/siU', $con, $mess);
        $mess[1] = $this->collect_model->con_replace(strip_tags($mess[1]));
        $mess = explode('-', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[1]);
          $info['district'] = $block_addresss[0];  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $mess[1];  //区属
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[2]);
          $info['block'] = $block_addresss[0];  //板块
        }
        //商铺面积
        preg_match('/商铺面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/商铺类型：(.*)<\/li>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1]));//类型
        //房源描述-备注
        preg_match('/<div class="summary-cont">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //租金
        preg_match('/租金价格：(.*)<\/b>/siU', $con, $rent_price);
        $total_prices = $this->collect_model->con_replace(strip_tags($rent_price[1]));
        //有面议设置为0
        $info['price'] = $total_prices ? $total_prices : "0";
        //适合经营
        preg_match('/适合经营.*<\/dl>/siU', $con, $suits);
        if (!empty($suits)) {
          preg_match_all('/<em class=ico-equip-has><\/em>(.*)<\/span>/siU', $suits[0], $suit_manage);
          $info['suit_manage'] = implode("*", $suit_manage[1]);
        } else {
          $info['suit_manage'] = "暂无资料";
        }
        //楼盘名称
        preg_match('/商铺名称：(.*)<\/li>/siU', $con, $building);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($building[1]));
        //过滤重复房源hash 采集来源*楼盘名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //赶集写字楼采集
  public function ganji_collect_info_office($data, $con = '')
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 0;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    if ($con == '') {
      $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    }
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //采集电话
      preg_match('/联<i class="letter-space-5"><\/i>系<i class="letter-space-5"><\/i>方<i class="letter-space-5"><\/i>式：(.*)<\/em>/siU', $con, $tel);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($tel[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为中介发帖
      preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/在<i class="letter-space-5"><\/i>线<i class="letter-space-5"><\/i>联<i class="letter-space-5"><\/i>系：(.*)<\/i>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //写字楼标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //楼盘
        preg_match('/<span class="fc-gray">写字楼名称：(.*)<\/li>/siU', $con, $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1]));
        //写字楼地址
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>地<i class="letter-space-5"><\/i>址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>区<i class="letter-space-5"><\/i>域：<\/span>(.*)<\/li>/siU', $con, $mess);
        $mess[1] = $this->collect_model->con_replace(strip_tags($mess[1]));
        $mess = explode('-', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[1]);
          $info['district'] = $block_addresss[0];  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $mess[1];  //区属
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[2]);
          $info['block'] = $block_addresss[0];  //板块
        }
        //写字楼面积
        preg_match('/写字楼面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //写字楼类型
        preg_match('/写字楼类型：(.*)<\/li>/siU', $con, $office_type);
        $info['office_type'] = $this->collect_model->con_replace(strip_tags($office_type[1]));//类型
        //售价
        preg_match('/写字楼售价：<\/span>(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为0
        $info['price'] = $total_prices ? $total_prices : "0";
        //写字楼照片
        preg_match_all('/title=\'查看大图\' src="(.*)" \/>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //房源描述-备注
        preg_match('/<div class="summary-cont">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['sell_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      //采集电话、联系方式
      preg_match('/联<i class="letter-space-5"><\/i>系<i class="letter-space-5"><\/i>方<i class="letter-space-5"><\/i>式：(.*)<\/em>/siU', $con, $tel);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($tel[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      //采集发帖记录，每月发帖量超过5条的判定为疑似中介发帖
      preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
      if (empty($res) && is_array($urlarr) && count($urlarr) == 2) {
        $info['isagent'] = $this->posting_record_ganji($info, $urlarr[1]);
      }
      //判断详情页是否还有“个人”电话号码11位
      preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
      if (empty($res) && strstr($contacts[1], "个人") && strlen($info['telno1']) > 10) {
        //联系人
        preg_match('/在<i class="letter-space-5"><\/i>线<i class="letter-space-5"><\/i>联<i class="letter-space-5"><\/i>系：(.*)<\/i>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //写字楼标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //写字楼照片
        preg_match_all('/title=\'查看大图\' src="(.*)" \/>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //楼盘
        preg_match('/写字楼名称：<\/span>(.*)<\/li>/siU', $con, $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1]));
        //写字楼地址
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>地<i class="letter-space-5"><\/i>址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>区<i class="letter-space-5"><\/i>域：<\/span>(.*)<\/li>/siU', $con, $mess);
        $mess[1] = $this->collect_model->con_replace(strip_tags($mess[1]));
        $mess = explode('-', $mess[1]);
        $info['district'] = '暂无资料';//区属
        $info['block'] = '暂无资料';//板块
        if (count($mess) == 2) {
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[1]);
          $info['district'] = $block_addresss[0];  //区属
        } elseif (count($mess) == 3) {
          $info['district'] = $mess[1];  //区属
          //去除有（周边靠谱装修）
          $block_addresss = explode('(', $mess[2]);
          $info['block'] = $block_addresss[0];  //板块
        }
        //写字楼类型
        preg_match('/写字楼类型：(.*)<\/li>/siU', $con, $office_type);
        $info['office_type'] = $this->collect_model->con_replace(strip_tags($office_type[1]));//类型
        //房源描述-备注
        preg_match('/<div class="summary-cont">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //写字楼面积
        preg_match('/写字楼面积：(.*)㎡/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));//面积
        //租金
        preg_match('/租<i class="letter-space-5"><\/i>金<i class="letter-space-5"><\/i>价<i class="letter-space-5"><\/i>格：<\/span>(.*)<\/li>/siU', $con, $total_price);
        $rent_price = $this->collect_model->con_replace(strip_tags($total_price[1]));
        preg_match_all($reg, $rent_price, $result);//取出数值
        if (count($result[0]) == 2) {
          $info['price'] = $result[0][1];
        } else {
          $info['price'] = '面议';
        }
        $info['rent_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //赶集发帖记录
  public function posting_record_ganji($data, $url)
  {
    $compress = 'gzip';
    $cpcon = $this->collect_model->vcurl($url, $compress, 1);  #采集发帖次数
    preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);
    $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
    if ($postnum > 5)//发帖次数大于5
    {
      preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
      $messages = explode('<li>', $messages[1][0]);
      foreach ($messages as $k => $v) {
        $messages[$k] = $this->collect_model->con_replace(strip_tags($v));
      }
      $sh = 0;
      $rh = 0;
      $sn = 0;
      $ro = 0;
      $so = 0;
      $rs = 0;
      $ss = 0;
      foreach ($messages as $value) {
        $gj_type = explode('房产-', $value);
        $gj_type[1] = substr($gj_type[1], 0, -1);
        if ($gj_type[1] == '二手房出售') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $sec_hand_number[$sh] = strtotime($time);
          $sh++;
        }
        if ($gj_type[1] == '租房') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $rent_house_number[$rh] = strtotime($time);
          $rh++;
        }
        if ($gj_type[1] == '合租房') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $share_house_number[$sn] = strtotime($time);
          $sn++;
        }
        if ($gj_type[1] == '写字楼出租') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $rent_office_number[$ro] = strtotime($time);
          $ro++;
        }
        if ($gj_type[1] == '写字楼出售') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $sell_office_number[$so] = strtotime($time);
          $so++;
        }
        if ($gj_type[1] == '商铺出租') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $rent_shops_number[$rs] = strtotime($time);
          $rs++;
        }
        if ($gj_type[1] == '商铺出售') {
          $reg = '/\d+/';//匹配数字的正则表达式
          preg_match_all($reg, $value, $result);//取出时间
          $time = $result[0][0] . '-' . $result[0][1] . '-' . $result[0][2];
          $sell_shops_number[$ss] = strtotime($time);
          $ss++;
        }
      }
      $type_sec_hand = 0;
      $type_rent_house = 0;
      $type_share_house = 0;
      $type_rent_office = 0;
      $type_sell_office = 0;
      $type_rent_shops = 0;
      $type_sell_shops = 0;
      if (count($sec_hand_number) > 5) {//二手房发帖记录
        $length_time = $sec_hand_number[0] - $sec_hand_number[4];
        $type_sec_hand = $length_time < 2592000 ? 1 : 0;
      }
      if (count($rent_house_number) > 5) {//出租发帖记录
        $length_time = $rent_house_number[0] - $rent_house_number[4];
        $type_rent_house = $length_time < 2592000 ? 1 : 0;
      }
      if (count($share_house_number) > 5) {//合租发帖记录
        $length_time = $share_house_number[0] - $share_house_number[4];
        $type_share_house = $length_time < 2592000 ? 1 : 0;
      }
      if (count($rent_office_number) > 5) {//写字楼出租发帖记录
        $length_time = $rent_office_number[0] - $rent_office_number[4];
        $type_rent_office = $length_time < 2592000 ? 1 : 0;
      }
      if (count($sell_office_number) > 5) {//写字楼出售发帖记录
        $length_time = $sell_office_number[0] - $sell_office_number[4];
        $type_sell_office = $length_time < 2592000 ? 1 : 0;
      }
      if (count($rent_shops_number) > 5) {//商铺出租发帖记录
        $length_time = $rent_shops_number[0] - $rent_shops_number[4];
        $type_rent_shops = $length_time < 2592000 ? 1 : 0;
      }
      if (count($sell_shops_number) > 5) {//商铺出售发帖记录
        $length_time = $sell_shops_number[0] - $sell_shops_number[4];
        $type_sell_shops = $length_time < 2592000 ? 1 : 0;
      }
      if ($type_sec_hand == 1 || $type_rent_house == 1 || $type_share_house == 1 || $type_rent_office == 1 || $type_sell_office == 1 || $type_rent_shops == 1 || $type_sell_shops == 1) {
//                $broker_black=array(
//                    'tel' => $data['telno1'],
//                    'store' => '赶集发帖记录',
//                    'city' => $data['city'],
//                    'addtime' => $data['createtime'],
//                    'type' => 1
//                );
//                $this->collect_model->add_agent_black($broker_black);//加入黑名单
//                //是中介房源,请勿入库
//                echo "<br><h3>此房源为中介房源：</h3><br>链接：".$data['oldurl'];
//                return 6;
        $isagent = 2;
      } else {
        // return 1;
        $isagent = 1;
      }
    } else {
      //return 1;
      $isagent = 1;
    }
    return $isagent;
  }

  //采集亿房网详情规则
  public function fdc_collect_info_house($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产 6=>亿房网
    $info['source_from'] = 6;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress);//采集详情页
    //删除采集列表
//        $del = array('id' => $data['id']);
//        $this->collect_model->del_collect_url($del);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    preg_match('/<p class="without-info">(.*)<\/p>/siU', $con, $result);
    if (!empty($result)) {
      echo "该房源已出售！";
      exit;
    }
    if ($data['type'] == 1) {//出售
      //过滤经纪人电话
      preg_match('/<div class="tel">(.*)<\/div>/siU', $con, $phone);
      preg_match_all($reg_num, $phone[1], $telno);
      $info['telno1'] = $telno[0][0];
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单

      $info['isagent'] = 1;
      //判断详情页是否还有“个人” 电话号码11位
      if (empty($res) && strstr($phone[1], "个人") && strlen($info['telno1']) == 11) {
        //户型
        preg_match('/户　　型：<\/span>(.*)<\/li>/siU', $con, $layout);
        preg_match_all($reg, $layout[1], $result);//取出数值

        $info['room'] = intval($result[0][0]); //户型（室）
        $info['hall'] = intval($result[0][1]);//户型（厅）
        $info['toilet'] = intval($result[0][2]);//户型（卫）
        $info['kitchen'] = intval($result[0][3]);//户型（厨房）
        $info['balcony'] = intval($result[0][4]);//户型（阳台）

        //楼层
        preg_match('/楼　　层：<\/span>(.*)<\/li>/siU', $con, $floors);
        preg_match_all($reg, $floors[1], $result);//取出数值
        $info['floor'] = intval($result[0][0]);//楼层（当前层数）
        $info['totalfloor'] = intval($result[0][1]);//楼层（总层数）

        //面积
        preg_match('/面　　积：<\/span>(.*)<\/li>/siU', $con, $buildarea);
        preg_match_all($reg, $buildarea[1], $result);//取出数值
        $info['buildarea'] = floatval($result[0][0]);

        //房源标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($title[1]));

        //联系人
        preg_match('/联系方式：(.*)<i>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }

        //楼盘地址
        preg_match('/地　　址：<\/span>(.*)<\/li>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $house_address[1];
        }

        //区属板块小区名
        preg_match('/<div class="mNav">(.*)<\/div>/siU', $con, $add_mess);
        $address = explode('<em></em>', $add_mess[1]);

        $dist = $this->collect_model->con_replace(strip_tags($address[2]));//区属
        $info['district'] = str_replace('二手房出售', '', $dist);

        $street = $this->collect_model->con_replace(strip_tags($address[3]));//板块
        $info['block'] = str_replace('二手房出售', '', $street);

        $info['house_name'] = $this->collect_model->con_replace(strip_tags($address[4]));

        //房源照片
        preg_match_all('/<div class="detail_box">(.*)<\/div>/siU', $con, $photo);
        preg_match_all('/<img src="(.*)".*/siU', $photo[1][1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //总价
        preg_match('/总　　价：<\/span><em>(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = $total_price[1];
        //单价
        preg_match('/单　　价：<\/span>(.*)<\/li>/siU', $con, $average_price);
        preg_match_all($reg, $average_price[1], $result);//取出数值
        $info['avgprice'] = $result[0][0];

        //物业费
        preg_match('/物 业 费：<\/span>(.*)<\/li>/siU', $con, $strata_fee);
        if ($strata_fee[1]) {
          preg_match_all($reg, $strata_fee[1], $result);//取出数值
          $info['strata_fee'] = $result[0][0];
          if (strstr($strata_fee[1], "元/m²/月")) {
            $info['costs_type'] = 1;
          } elseif (strstr($strata_fee[1], "元/月")) {
            $info['costs_type'] = 2;
          }
        }

        //房屋情况 朝向 住宅性质
        preg_match('/房屋情况：<\/span>(.*)<\/li>/siU', $con, $type);
        $type_info = explode(' ', $type[1]);
        if (!strstr($type_info[0], "装修")) {
          foreach ($type_info as $key => $val) {
            $data[$key + 1] = $val;
          }
          $type_info = $data;
        } else {
          //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
          switch ($type_info[0]) {
            case "毛坯":
              $info['serverco'] = 1;
              break;
            case "简单装修":
              $info['serverco'] = 2;
              break;
            case "中档装修":
              $info['serverco'] = 3;
              break;
            case "高档装修":
              $info['serverco'] = 4;
              break;
            case "豪华装修":
              $info['serverco'] = 5;
              break;
            case "婚装":
              $info['serverco'] = 6;
              break;
            default:
              $info['serverco'] = 0;
              break;
          }
        }
        if ($type_info[2] == "别墅") {
          $info['sell_type'] = 2;
        } else {
          if ($type_info[2] == "多层") {
            $info['house_type'] = 1;
          } elseif ($type_info[2] == "高层") {
            $info['house_type'] = 2;
          } elseif ($type_info[2] == "小高层") {
            $info['house_type'] = 3;
          } elseif ($type_info[2] == "其他") {
            $info['house_type'] = 8;
          }
          $info['sell_type'] = 1;
        }

        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        switch ($type_info[1]) {
          case "东朝向":
            $info['forward'] = 1;
            break;
          case "东南朝向":
            $info['forward'] = 2;
            break;
          case "南朝向":
            $info['forward'] = 3;
            break;
          case "西南朝向":
            $info['forward'] = 4;
            break;
          case "西朝向":
            $info['forward'] = 5;
            break;
          case "西北朝向":
            $info['forward'] = 6;
            break;
          case "北朝向":
            $info['forward'] = 7;
            break;
          case "东北朝向":
            $info['forward'] = 8;
            break;
          case "东西朝向":
            $info['forward'] = 9;
            break;
          case "南北朝向":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //建筑年代
        preg_match('/建成时间：<\/span>(.*)年/siU', $con, $buildyear);
        $info['buildyear'] = $buildyear[1] ? $buildyear[1] : 0;
        //房源描述-备注
        preg_match_all('/<div class="detail_box">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1][0]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租  没有发帖记录
      //过滤经纪人电话
      preg_match('/<div class="tel">(.*)<\/div>/siU', $con, $phone);
      preg_match_all($reg_num, $phone[1], $telno);
      $info['telno1'] = $telno[0][0];
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单

      $info['isagent'] = 1;
      if (empty($res) && strstr($phone[1], "个人") && strlen($info['telno1']) == 11) {
        //户型
        preg_match('/户　　型：<\/span>(.*)<\/li>/siU', $con, $layout);
        preg_match_all($reg, $layout[1], $result);//取出数值

        $info['room'] = $result[0][0]; //户型（室）
        $info['hall'] = $result[0][1];//户型（厅）
        $info['toilet'] = $result[0][2];//户型（卫）
        $info['kitchen'] = $result[0][3];//户型（厨房）
        $info['balcony'] = $result[0][4];//户型（阳台）

        //楼层
        preg_match('/楼　　层：<\/span>(.*)<\/li>/siU', $con, $floors);
        preg_match_all($reg, $floors[1], $result);//取出数值
        $info['floor'] = intval($result[0][0]);//楼层（当前层数）
        $info['totalfloor'] = intval($result[0][1]);//楼层（总层数）
        //面积
        preg_match('/面　　积：<\/span>(.*)<\/li>/siU', $con, $buildarea);
        preg_match_all($reg, $buildarea[1], $result);//取出数值
        $info['buildarea'] = floatval($result[0][0]);

        //房源标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($title[1]));

        //联系人
        preg_match('/联系方式：(.*)<i>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $contact[1];
        } else {
          $info['owner'] = "个人";
        }

        //楼盘地址
        preg_match('/位　　置：<\/span>(.*)<\/li>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $house_address[1];
        }

        //区属板块小区名
        preg_match('/<div class="mNav">(.*)<\/div>/siU', $con, $add_mess);
        $address = explode('<em></em>', $add_mess[1]);

        $dist = $this->collect_model->con_replace(strip_tags($address[2]));//区属
        $info['district'] = str_replace('租房', '', $dist);

        $street = $this->collect_model->con_replace(strip_tags($address[3]));//板块
        $info['block'] = str_replace('租房', '', $street);

        $info['house_name'] = $this->collect_model->con_replace(strip_tags($address[4]));

        //房源照片
        preg_match_all('/<div class="detail_box">(.*)<\/div>/siU', $con, $photo);
        preg_match_all('/<img src="(.*)".*/siU', $photo[1][1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }

        //租金
        preg_match('/租　　金：<\/span><em>(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = is_numeric($total_price[1]) ? $total_price[1] : 0;


        //付款方式
        preg_match('/押　　金：(.*)<\/li>/siU', $con, $pricetype);
        preg_match_all("/(?<=\()([^\)]*?)(?=\))/", $pricetype[1], $ok);
        if (empty($ok[0][0])) {
          $info['pricetype'] = "押一付三";
        } else {
          $info['pricetype'] = $this->collect_model->con_replace(strip_tags($ok[0][0]));
        }

        //房屋情况 朝向 住宅性质
        preg_match('/房屋情况：<\/span>(.*)<\/li>/siU', $con, $type);
        $type_info = explode(' ', $type[1]);
        if (!strstr($type_info[0], "装修")) {
          foreach ($type_info as $key => $val) {
            $data[$key + 1] = $val;
          }
          $type_info = $data;
        } else {
          //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
          switch ($type_info[0]) {
            case "毛坯":
              $info['serverco'] = 1;
              break;
            case "简单装修":
              $info['serverco'] = 2;
              break;
            case "中档装修":
              $info['serverco'] = 3;
              break;
            case "高档装修":
              $info['serverco'] = 4;
              break;
            case "豪华装修":
              $info['serverco'] = 5;
              break;
            case "婚装":
              $info['serverco'] = 6;
              break;
            default:
              $info['serverco'] = 0;
              break;
          }
        }

        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        switch ($type_info[1]) {
          case "东朝向":
            $info['forward'] = 1;
            break;
          case "东南朝向":
            $info['forward'] = 2;
            break;
          case "南朝向":
            $info['forward'] = 3;
            break;
          case "西南朝向":
            $info['forward'] = 4;
            break;
          case "西朝向":
            $info['forward'] = 5;
            break;
          case "西北朝向":
            $info['forward'] = 6;
            break;
          case "北朝向":
            $info['forward'] = 7;
            break;
          case "东北朝向":
            $info['forward'] = 8;
            break;
          case "东西朝向":
            $info['forward'] = 9;
            break;
          case "南北朝向":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        $info['rent_type'] = 1;
        //房源描述-备注
        preg_match_all('/<div class="detail_box">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1][0]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //吴江房产网采集
  public function wjajw_collect_info_house($data)
  {
    $this->load->library('Curl');
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产；5=》吴江房产网
    $info['source_from'] = 5;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->curl->vget($url, $this->cookies);//采集详情页

    preg_match('/联系手机:.*<td>(.*)<\/td>/siU', $con, $telno1);
    $info['telno1'] = $this->collect_model->con_replace(strip_tags($telno1[1]));
    if (strpos($info['telno1'], '*') !== false) {
      $this->wjajw_login();
      $con = $this->curl->vget($url, $this->cookies);//采集详情页
      preg_match('/联系手机:.*<td>(.*)<\/td>/siU', $con, $telno1);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($telno1[1]));
    }

    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //过滤经纪人电话
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/物业名称:.*<td>(.*)<\/td>/siU', $con, $house_title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/联系人:.*<td>(.*)<\/td>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        $info['picurl'] = '暂无资料';
        $info['pic_cut'] = 0;
        // 区属 板块
        $info['district'] = '吴江';//区属
        preg_match('/区域:.*<td.*>(.*)<\/td>/siU', $con, $block);
        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1]));
        // 小区地址
        $info['house_addr'] = $info['house_title'];
        //小区名
        $info['house_name'] = $info['house_title'];
        //用途（住宅、别墅、写字楼）
        preg_match('/物业类型:.*<td>(.*)<\/td>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          case "商铺":
            $info['sell_type'] = 3;
            break;
          case "写字楼":
            $info['sell_type'] = 4;
            break;
          case "工业厂房":
            $info['sell_type'] = 0;
            break;
          case "其他":
            $info['sell_type'] = 0;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/总价:.*<span.*>(.*)<\/span>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        $info['avgprice'] = '';
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/朝向:.*<td>(.*)<\/td>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型:.*<td>(.*)<\/td>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        //面积
        preg_match('/面积:.*<td>(.*)㎡<\/td>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));

        //房源描述-备注
        preg_match('/<strong>备注<\/strong>.*<td.*>(.*)<\/td>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼层:.*<td>(.*)<\/td>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度:.*<td>(.*)<\/td>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简装":
            $info['serverco'] = 2;
            break;
          case "全装":
            $info['serverco'] = 3;
            break;
          case "精装":
            $info['serverco'] = 4;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match('/房龄:.*<td>(.*)<\/td>/siU', $con, $buildyear);
        $info['buildyear'] = $this->collect_model->con_replace(strip_tags($buildyear[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        if ($info['sell_type']) {
          $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
        }
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租  没有发帖记录
      //过滤经纪人电话
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;

      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/物业名称:.*<td>(.*)<\/td>/siU', $con, $house_title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/联系人:.*<td>(.*)<\/td>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        $info['picurl'] = '暂无资料';
        $info['pic_cut'] = 0;
        // 区属 板块
        $info['district'] = '吴江';//区属
        preg_match('/区域:.*<td.*>(.*)<\/td>/siU', $con, $block);
        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1]));
        // 小区地址
        $info['house_addr'] = $info['house_title'];
        //小区名
        $info['house_name'] = $info['house_title'];
        //用途（住宅、别墅、写字楼）
        preg_match('/物业类型:.*<td>(.*)<\/td>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          case "商铺":
            $info['rent_type'] = 3;
            break;
          case "写字楼":
            $info['rent_type'] = 4;
            break;
          case "工业厂房":
            $info['rent_type'] = 0;
            break;
          case "其他":
            $info['rent_type'] = 0;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //租金
        preg_match('/租金:.*<td.*>(.*)<\/td>/siU', $con, $total_price);
        $info['price'] = str_replace('(元/月)', '', $total_price[1]);
        $info['price'] = $this->collect_model->con_replace(strip_tags($info['price']));
        //付款方式
        preg_match('/付款方式:.*<td>(.*)<\/td>/siU', $con, $pricetype);
        $info['pricetype'] = $this->collect_model->con_replace(strip_tags($pricetype[1]));
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/朝向:.*<td>(.*)<\/td>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型:.*<td>(.*)<\/td>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        //面积
        preg_match('/面积:.*<td>(.*)㎡<\/td>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));

        //房源描述-备注
        preg_match('/<strong>备注<\/strong>.*<td.*>(.*)<\/td>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼层:.*<td>(.*)<\/td>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度:.*<td>(.*)<\/td>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简装":
            $info['serverco'] = 2;
            break;
          case "全装":
            $info['serverco'] = 3;
            break;
          case "精装":
            $info['serverco'] = 4;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match('/房龄:.*<td>(.*)<\/td>/siU', $con, $buildyear);
        $info['buildyear'] = $this->collect_model->con_replace(strip_tags($buildyear[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        if ($info['rent_type']) {
          $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
        }
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //搜房住宅采集
  public function sfang_collect_info_house($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 2;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    $con = iconv("GBK", "UTF-8", $con);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      $info['isagent'] = 1;
      //电话
      preg_match('/<span class="tel">(.*)<a class="name"/siU', $con, $phone);
      $info['telno1'] = $phone[1] == '' ? '' : $this->collect_model->con_replace(strip_tags($phone[1]));
      if ($info['telno1'] != '') {
        //户型
        $room = $hall = $toilet = array();
        preg_match('/<dd class="mt3">户型：(.*)<\/dd>/siU', $con, $huxing);
        $huxing = $this->collect_model->con_replace(strip_tags($huxing[1]));
        preg_match("/(\d+(\.\d{0,2})?)室/", $huxing, $room);
        preg_match("/(\d+(\.\d{0,2})?)厅/", $huxing, $hall);
        preg_match("/(\d+(\.\d{0,2})?)卫/", $huxing, $toilet);
        $info['room'] = $room[1];//户型（室）
        $info['hall'] = $hall[1];//户型（厅）
        $info['toilet'] = $toilet[1];//户型（卫）
        //面积
        $buildarea = array();
        preg_match('/<dd class="mt3">建筑面积：(.*)<\/dd>/siU', $con, $mianji);
        $mianji = $this->collect_model->con_replace(strip_tags($mianji[1]));
        preg_match("/(\d+(\.\d{0,2})?)㎡/", $mianji, $buildarea);
        $info['buildarea'] = $buildarea[1];//面积
        //楼层
        $totalfloor = array();
        preg_match('/<dd>楼层：(.*)<\/dd>/siU', $con, $louceng);
        $louceng = $this->collect_model->con_replace(strip_tags($louceng[1]));
        preg_match("/(\d+(\.\d{0,2})?)层/", $louceng, $totalfloor);
        $info['totalfloor'] = intval($totalfloor[1]);//楼层（总层数）
        if ($info['totalfloor'] != 0) {
          preg_match("/(.*)层/siU", $louceng, $type_floor);
          $info['type_floor'] = $this->collect_model->con_replace(strip_tags($type_floor[1]));//楼层 高中低
        }
        //房源标题
        preg_match('/<div class="title">.*<h1>(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/<a class="name".*>(.*)<\/a>/siU', $con, $contact);
        if (strlen(trim($contact[1])) > 0) {
          $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        } else {
          $info['owner'] = "业主";
        }
        //楼盘地址
        preg_match('/址：<\/span>(.*)<\/p>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($house_address[1]));
        }
        //区属板块小区名
        preg_match('/<dt>小区：(.*)\（(.*)<\/a>(.*)\）/siU', $con, $add_mess);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($add_mess[1]));//小区名
        $info['district'] = $this->collect_model->con_replace(strip_tags($add_mess[2]));//区属
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        $info['block'] = $this->collect_model->con_replace(strip_tags($add_mess[3]));//板块
        //房源照片
        preg_match('/<div class="fy-img">(.*)<\/div>/siU', $con, $photos);
        if (false && !empty($photos[1])) {
          preg_match_all('/<img.*src="(.*)">/siU', $photos[1], $photo);
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //总价
        preg_match('/<dt>售价：<span.*>(.*)<\/span>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/<dt>售价：<span.*\（(.*)元\/㎡\）/siU', $con, $average_price);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($average_price[1]));
        //用途（住宅、别墅、写字楼）
        preg_match('/物业类型：<\/span>(.*)<\/dd>/siU', $con, $type);
        switch ($type[1]) {
          case "住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/<dd>朝向：(.*)<\/dd>/siU', $con, $direction);
        $direction[1] = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($direction[1]) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/<dd>装修：(.*)<\/dd>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简装":
            $info['serverco'] = 2;
            break;
          case "中装":
            $info['serverco'] = 3;
            break;
          case "精装":
            $info['serverco'] = 4;
            break;
          case "豪装":
            $info['serverco'] = 5;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match('/<dd>年代：(.*)年<\/dd>/siU', $con, $buildyear);
        $info['buildyear'] = $this->collect_model->con_replace(strip_tags($buildyear[1])) ? $this->collect_model->con_replace(strip_tags($buildyear[1])) : 0;
        //房源描述-备注
        preg_match('/<p class="cmtC">(.*)<\/p>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));

        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      //电话
      preg_match('/<span class="phoneicon floatl">(.*)<\/span>/siU', $con, $phone);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($phone[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      if (empty($res) && strlen($info['telno1']) == 11) {
        $room = $hall = $toilet = $buildarea = $totalfloor = $floor = array();
        preg_match('/房屋概况：(.*)<\/li>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        //户型
        preg_match("/(\d+(\.\d{0,2})?)室/", $layout, $room);
        preg_match("/(\d+(\.\d{0,2})?)厅/", $layout, $hall);
        preg_match("/(\d+(\.\d{0,2})?)卫/", $layout, $toilet);
        $info['room'] = $room[1];//户型（室）
        $info['hall'] = $hall[1];//户型（厅）
        $info['toilet'] = $toilet[1];//户型（卫）
        //面积
        preg_match("/(\d+(\.\d{0,2})?)m&sup2;/", $layout, $buildarea);
        $info['buildarea'] = $buildarea[1];//面积
        //楼层
        preg_match("/(\d+(\.\d{0,2})?)\//", $layout, $floor);
        preg_match("/(\d+(\.\d{0,2})?)层/", $layout, $totalfloor);
        $info['totalfloor'] = intval($totalfloor[1]);//楼层（总层数）
        $info['floor'] = intval($floor[1]);//楼层
        if ($info['totalfloor'] != 0 && $info['floor'] == 0) {
          preg_match("/\((.*)层\)/siU", $layout, $type_floor);
          $info['type_floor'] = $this->collect_model->con_replace(strip_tags($type_floor[1]));//楼层 高中低
        }
        //装修+朝向+类型
        $descs = explode('|', $layout);
        //装修
        $serverco = $descs[5];//装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        switch ($serverco) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        $direction = $descs[4];
        switch ($direction) {
          case "东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        $type = $descs[1];//用途（住宅、别墅、写字楼）
        switch ($type) {
          case "住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //房源标题
        preg_match('/<div class="h1-tit rel">.*<h1>(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[0]));
        //楼盘地址
        preg_match('/址：<\/span>(.*)<\/li>/siU', $con, $house_address);
        if (empty($house_address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($house_address[1]));
        }
        //房源照片
        preg_match('/<div class="alingC mt20 fy-img">(.*)<\/div>/siU', $con, $photos);
        if (false && !empty($photos[1])) {
          preg_match_all('/<img.*data-src="(.*)" alt/siU', $photos[1], $photo);
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //建筑年代
        $info['buildyear'] = 0;
        //租金
        preg_match('/<strong class="red price bold">(.*)<\/strong>元\/月/siU', $con, $total_price);
        $total_prices = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为1
        $info['price'] = is_numeric($total_prices) ? $total_prices : "0";
        //付款方式
        preg_match('/元\/月[(.*)]<\/li>/siU', $con, $pricetype);
        if (empty($pricetype[1])) {
          $info['pricetype'] = "押一付三";
        } else {
          $info['pricetype'] = $this->collect_model->con_replace(strip_tags($pricetype[1]));
        }
        //区属板块小区名
        preg_match('/区：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->collect_model->con_replace(strip_tags($add_mess[1]));
        preg_match('/(.*)\[/siU', $address, $house_name);
        $info['house_name'] = $house_name[1] ? $house_name[1] : '暂无资料';//小区名
        preg_match('/\[(.*)\]/siU', $address, $dis_blk);
        $dis_blk = explode('/', $dis_blk[1]);
        $info['district'] = $dis_blk[0] ? $dis_blk[0] : '暂无资料';//区属
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        $info['block'] = $dis_blk[1] ? $dis_blk[1] : '暂无资料';//板块
        //联系人
        preg_match('/<span class="floatl name">(.*)<\/span>/siU', $con, $contact);
        $contact = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (!empty($contact)) {
          $info['owner'] = $contact;
        } else {
          $info['owner'] = "个人";
        }
        //房源描述-备注
        preg_match('/<div class="agent-txt agent-txt-per floatl">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //搜房商铺采集
  public function sfang_collect_info_shop($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 2;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1); //采集详情页
    $con = iconv("GBK", "UTF-8", $con);
    $reg = '/\d+(\.\d{0,2})?/'; //匹配数字,小数的正则表达式
    $reg_num = '/\d+/'; //匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //电话
      preg_match('/<label id="mobilecode">(.*)<\/label>/siU', $con, $phone);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($phone[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data); //判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) == 11) {
        //联系人
        preg_match('/联 系 人：(.*)<\/li>/siU', $con, $contact);
        $contact = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (!empty($contact)) {
          $info['owner'] = $contact;
        } else {
          $info['owner'] = "个人";
        }
        //商铺标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //商铺照片
        preg_match('/<div id="hsPic-pos" class="leftBox">(.*)<div id="hsMap-pos"  class="leftBox">/siU', $con, $photos);
        preg_match_all('/<div class="img_.*href = "(.*)" target="_blank">/siU', $photos[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //楼层
        preg_match('/楼　　层：(.*)<\/dd>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        } elseif (count($floor_num[0]) == 1) {
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        }
        //区属板块小区名
        preg_match('/商铺名称：(.*)<\/dt>/siU', $con, $add_mess);
        preg_match('/(.*)\(/siU', $add_mess[1], $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1])) ? $this->collect_model->con_replace(strip_tags($house_name[1])) : '暂无资料';//小区名
        preg_match('/\( <a.*>(.*)<\/a>/siU', $add_mess[1], $district);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district[1])) ? $this->collect_model->con_replace(strip_tags($district[1])) : '暂无资料';//区属
        preg_match('/' . $info['district'] . '<\/a>.*<a.*>(.*)<\/a> \)/siU', $add_mess[1], $block);
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1])) ? $this->collect_model->con_replace(strip_tags($block[1])) : '暂无资料';//板块
        //单价
        preg_match('/\（(.*)元\/平方米\）/siU', $con, $average_price);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($average_price[1]));
        //商铺面积
        preg_match('/建筑面积：(.*)㎡<\/dd>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1])); //面积
        //商铺类型
        preg_match('/类    型：<\/span>(.*)<\/dd>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1])); //类型
        //售价
        preg_match('/价：(.*)万元/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //房源描述-备注
        preg_match('/id="hsPro-pos">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['sell_type'] = 3;
        //过滤重复房源hash 采集来源*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      $info['rent_type'] = 3;
      //电话
      preg_match('/<label id="mobilecode">(.*)<\/label>/siU', $con, $phone);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($phone[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data); //判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) == 11) {
        //联系人
        preg_match('/联 系 人：(.*)<\/li>/siU', $con, $contact);
        $contact = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (!empty($contact)) {
          $info['owner'] = $contact;
        } else {
          $info['owner'] = "个人";
        }
        //商铺标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //商铺照片
        preg_match('/<div class="describe mt10" id="house_des">(.*)<div id="hsMap-pos"  class="leftBox">/siU', $con, $photos);
        preg_match_all('/<div class="img_.*href = "(.*)" target="_blank">/siU', $photos[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //商铺地址
        preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //楼层
        preg_match('/楼　　层：(.*)<\/dd>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        } elseif (count($floor_num[0]) == 1) {
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        }
        //区属板块小区名
        preg_match('/商铺名称：(.*)<\/dt>/siU', $con, $add_mess);
        preg_match('/(.*)\(/siU', $add_mess[1], $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1])) ? $this->collect_model->con_replace(strip_tags($house_name[1])) : '暂无资料';//小区名
        preg_match('/\( <a.*>(.*)<\/a>/siU', $add_mess[1], $district);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district[1])) ? $this->collect_model->con_replace(strip_tags($district[1])) : '暂无资料';//区属
        preg_match('/' . $info['district'] . '<\/a>.*<a.*>(.*)<\/a> \)/siU', $add_mess[1], $block);
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1])) ? $this->collect_model->con_replace(strip_tags($block[1])) : '暂无资料';//板块
        //商铺面积
        preg_match('/出租面积：(.*)㎡<\/dd>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1])); //面积
        //商铺类型
        preg_match('/类    型：<\/span>(.*)<\/dd>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1])); //类型
        //租金
        preg_match('/金：(.*)元\/月/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //房源描述-备注
        preg_match('/id="hsPro-pos">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //过滤重复房源hash 采集来源*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //搜房写字楼采集
  public function sfang_collect_info_office($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产
    $info['source_from'] = 2;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    $con = iconv("GBK", "UTF-8", $con);
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1) {//出售
      //电话
      preg_match('/<label id="mobilecode">(.*)<\/label>/siU', $con, $phone);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($phone[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data); //判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) == 11) {
        //联系人
        preg_match('/联 系 人：(.*)<\/li>/siU', $con, $contact);
        $contact = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (!empty($contact)) {
          $info['owner'] = $contact;
        } else {
          $info['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //写字楼照片
        preg_match('/<div class="describe mt10" id="house_des">(.*)<div id="hsMap-pos"  class="leftBox">/siU', $con, $photos);
        preg_match_all('/<div class="img_.*href = "(.*)" target="_blank">/siU', $photos[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //写字楼地址
        preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //楼层
        preg_match('/楼　　层：(.*)<\/dd>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        } elseif (count($floor_num[0]) == 1) {
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        }
        //区属板块小区名
        preg_match('/写字楼名称：(.*)<\/dt>/siU', $con, $add_mess);
        preg_match('/(.*)\(/siU', $add_mess[1], $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1])) ? $this->collect_model->con_replace(strip_tags($house_name[1])) : '暂无资料';//小区名
        preg_match('/\( <a.*>(.*)<\/a>/siU', $add_mess[1], $district);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district[1])) ? $this->collect_model->con_replace(strip_tags($district[1])) : '暂无资料';//区属
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        preg_match('/' . $info['district'] . '<\/a>.*<a.*>(.*)<\/a> \)/siU', $add_mess[1], $block);
        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1])) ? $this->collect_model->con_replace(strip_tags($block[1])) : '暂无资料';//板块
        //单价
        preg_match('/价：(.*)元\/平方米/siU', $con, $average_price);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($average_price[1]));
        //写字楼面积
        preg_match('/建筑面积：(.*)㎡<\/dd>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1])); //面积
        //写字楼类型
        preg_match('/类    型：<\/span>(.*)<\/dd>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1])); //类型
        //售价
        preg_match('/\（总价：(.*)万\）/siU', $con, $total_price);
        $info['price'] = sprintf("%.2f", $this->collect_model->con_replace(strip_tags($total_price[1])));
        //房源描述-备注
        preg_match('/id="hsPro-pos">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['sell_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租
      //电话
      preg_match('/<label id="mobilecode">(.*)<\/label>/siU', $con, $phone);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($phone[1]));
      $res = $this->collect_model->check_phone($info['telno1'], $data); //判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) == 11) {
        //联系人
        preg_match('/联 系 人：(.*)<\/li>/siU', $con, $contact);
        $contact = $this->collect_model->con_replace(strip_tags($contact[1]));
        if (!empty($contact)) {
          $info['owner'] = $contact;
        } else {
          $info['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="title">(.*)<\/h1>/siU', $con, $houseid);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($houseid[1]));
        //写字楼照片
        preg_match('/<div class="describe mt10" id="house_des">(.*)<div id="hsMap-pos"  class="leftBox">/siU', $con, $photos);
        preg_match_all('/<div class="img_.*href = "(.*)" target="_blank">/siU', $photos[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        //写字楼地址
        preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
        if (empty($address[1])) {
          $info['house_addr'] = '暂无资料';
        } else {
          $info['house_addr'] = $this->collect_model->con_replace(strip_tags($address[1]));
        }
        //楼层
        preg_match('/楼　　层：(.*)<\/dd>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        } elseif (count($floor_num[0]) == 1) {
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        }
        //区属板块小区名
        preg_match('/写字楼名称：(.*)<\/dt>/siU', $con, $add_mess);
        preg_match('/(.*)\(/siU', $add_mess[1], $house_name);
        $info['house_name'] = $this->collect_model->con_replace(strip_tags($house_name[1])) ? $this->collect_model->con_replace(strip_tags($house_name[1])) : '暂无资料';//小区名
        preg_match('/\( <a.*>(.*)<\/a>/siU', $add_mess[1], $district);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district[1])) ? $this->collect_model->con_replace(strip_tags($district[1])) : '暂无资料';//区属
        //潍坊特殊处理
        if ($data['city'] == 65) {
          $info['district'] = $info['district'] == '经济开发区' ? '经开区' : $info['district'];
          $info['district'] = $info['district'] == '高新' ? '高新区' : $info['district'];
          $info['district'] = $info['district'] == '滨海' ? '滨海新区' : $info['district'];
        }

        preg_match('/' . $info['district'] . '<\/a>.*<a.*>(.*)<\/a> \)/siU', $add_mess[1], $block);
        $info['block'] = $this->collect_model->con_replace(strip_tags($block[1])) ? $this->collect_model->con_replace(strip_tags($block[1])) : '暂无资料';//板块
        //写字楼面积
        preg_match('/出租面积：(.*)㎡<\/dd>/siU', $con, $buildarea);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1])); //面积
        //写字楼类型
        preg_match('/类    型：<\/span>(.*)<\/dd>/siU', $con, $shop_type);
        $info['shop_type'] = $this->collect_model->con_replace(strip_tags($shop_type[1])); //类型
        //租金
        preg_match('/金：(.*)元\/月/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //房源描述-备注
        preg_match('/id="hsPro-pos">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        $info['rent_type'] = 4;
        //过滤重复房源hash 采集来源*小区名*电话*面积*区属*板块
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['district'] . '*' . $info['block'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //厦门联合网采集
  public function xmhouse_collect_info_house($data)
  {
    $compress = 'gzip';
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产；5=》吴江房产网 ；6=>亿房网；7=>厦门联合网
    $info['source_from'] = 7;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->collect_model->vcurl($url, $compress, 1);//采集详情页
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式

    if ($data['type'] == 1) {//出售
      //过滤经纪人电话
      preg_match('/<span class="num">(.*)<\/span>/siU', $con, $telno1);
      $info['telno1'] = str_replace(' ', '', $telno1[1]);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($info['telno1']));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;

      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<div class="addressTitle c_blue0041d9">(.*)<img/siU', $con, $house_title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/<dd class="ContactTel">.*<\/span><strong.*>(.*)<\/strong>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match('/<div id="housesPiclist">(.*)<\/div>/siU', $con, $photo_con);
        preg_match_all('/<img src="(.*)".*/siU', $photo_con[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/所在区域：<span>(.*)<\/span>/siU', $con, $district);
        $info['district'] = $district[1];//区属
        $info['block'] = '暂无资料';
        // 小区地址
        preg_match('/地.*址：<span>(.*)<\/span>/siU', $con, $house_addr);
        $info['house_addr'] = $house_addr[1];
        //小区名
        preg_match('/<a title="进入小区".*>(.*)<\/a>/siU', $con, $house_name);
        $info['house_name'] = $house_name[1];
        //用途（住宅、别墅、写字楼）
        preg_match('/房屋用途：<span>(.*)<\/span>/siU', $con, $type);
        switch ($type[1]) {
          case "住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/价 格：.*<em>(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/单 价：<span class="tOrgNum14">(.*)<\/span>/siU', $con, $avgprice);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($avgprice[1]));
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/房屋朝向：<span>(.*)<\/span>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型布局：<span>(.*)<\/span>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        $info['balcony'] = $result[0][3] ? $result[0][3] : 0;//阳台
        //面积
        preg_match('/<li>建筑面积：(.*)<\/li>/siU', $con, $buildarea);
        $info['buildarea'] = str_replace('(单位:M<sup>2</sup>)', '', $buildarea[1]);

        //房源描述-备注
        preg_match('/<div style="padding: 5px 18px 8px; font-size: 12px; line-height: 25px;">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/所在楼层：<span>(.*)<\/span>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度：<span>(.*)<\/span>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛胚":
            $info['serverco'] = 1;
            break;
          case "一般装修":
            $info['serverco'] = 2;
            break;
          case "中档装修":
            $info['serverco'] = 3;
            break;
          case "新装修":
            $info['serverco'] = 4;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        $info['buildyear'] = '';
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2) {//出租  没有发帖记录
      //过滤经纪人电话
      preg_match('/<span class="num">(.*)<\/span>/siU', $con, $telno1);
      $info['telno1'] = str_replace(' ', '', $telno1[1]);
      $info['telno1'] = $this->collect_model->con_replace(strip_tags($info['telno1']));
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;

      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<div class="addressTitle c_blue0041d9">(.*)<img/siU', $con, $house_title);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/<dd class="ContactTel">.*<\/span><strong.*>(.*)<\/strong>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match('/<div id="housesPiclist">(.*)<\/div>/siU', $con, $photo_con);
        preg_match_all('/<img src="(.*)".*/siU', $photo_con[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url($value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/所在区域：<span>(.*)<\/span>/siU', $con, $district);
        $info['district'] = $district[1];//区属
        $info['block'] = '暂无资料';
        // 小区地址
        preg_match('/地.*址：<span>(.*)<\/span>/siU', $con, $house_addr);
        $info['house_addr'] = $house_addr[1];
        //小区名
        preg_match('/<a title="进入小区".*>(.*)<\/a>/siU', $con, $house_name);
        $info['house_name'] = $house_name[1];
        //用途（住宅、别墅、写字楼）
        preg_match('/房屋用途：<span>(.*)<\/span>/siU', $con, $type);
        switch ($type[1]) {
          case "住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //租金
        preg_match('/价 格：.*<em>(.*)<\/em>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/单 价：<span class="tOrgNum14">(.*)<\/span>/siU', $con, $avgprice);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($avgprice[1]));
        //付款方式
        $info['pricetype'] = '暂无资料';
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/房屋朝向：<span>(.*)<\/span>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "东南":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "西南":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "西北":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "东北":
            $info['forward'] = 8;
            break;
          case "东西":
            $info['forward'] = 9;
            break;
          case "南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型布局：<span>(.*)<\/span>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        $info['balcony'] = $result[0][3] ? $result[0][3] : 0;//阳台
        //面积
        preg_match('/<li>建筑面积：(.*)<\/li>/siU', $con, $buildarea);
        $info['buildarea'] = str_replace('(单位:M<sup>2</sup>)', '', $buildarea[1]);

        //房源描述-备注
        preg_match('/<div style="padding: 5px 18px 8px; font-size: 12px; line-height: 25px;">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/所在楼层：<span>(.*)<\/span>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修程度：<span>(.*)<\/span>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛胚":
            $info['serverco'] = 1;
            break;
          case "一般装修":
            $info['serverco'] = 2;
            break;
          case "中档装修":
            $info['serverco'] = 3;
            break;
          case "新装修":
            $info['serverco'] = 4;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        $info['buildyear'] = '';

        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }

  //厦门小鱼房产采集
  public function xmfish_collect_info_house($data)
  {
    $this->load->library('Curl');
    $url = $data['list_url'];
    $info['oldurl'] = $url;
    $info['createtime'] = time();
    //来源 0=》赶集；1=》58同城；2=》搜房；3=》house365；4=》链家地产；5=》吴江房产网；6=>亿房网；7=>厦门联合网；8=>厦门小鱼网
    $info['source_from'] = 8;
    //审核状态(0：待审核；1：成功/有电话号码；2：审核成功；3：审核失败/已加入黑名单)
    $info['e_status'] = 0;
    //采集城市
    $info['city'] = $data['city'];
    $con = $this->curl->vget($url, $this->xmfish_cookies);//采集详情页

    preg_match('/<div class="secondTel r3">(.*)<\/div>/siU', $con, $telno1);
    $info['telno1'] = $this->collect_model->con_replace(strip_tags($telno1[1]));
    if (strpos($info['telno1'], '*') !== false) {
      echo 'xmfish_cookies过期';
      exit;
    }

    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $reg_num = '/\d+/';//匹配数字正则表达式
    if ($data['type'] == 1 && is_numeric($info['telno1'])) {//出售
      //过滤经纪人电话
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;
      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<title>(.*)<\/title>/siU', $con, $house_title);
        $house_title[1] = str_replace('-小鱼房产_厦门最新楼盘、二手房、租房信息_厦门小鱼网', '', $house_title[1]);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/<div class="secondAgent">.*<h4>(.*)<\/h4>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match('/<div class="infoImg">(.*)<\/div>/siU', $con, $photo_con);
        preg_match_all('/<img.*data-url="(.*)".*/siU', $photo_con[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url('http://fangzi.xmfish.com' . $value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/<em>小区：<\/em>.*<\/a>（(.*)）<\/span>/siU', $con, $district_block);
        $district_block_arr = explode(' ', $district_block[1]);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district_block_arr[0]));//区属
        $info['block'] = $this->collect_model->con_replace(strip_tags($district_block_arr[1]));//板块
        // 小区地址
        $info['house_addr'] = $info['house_title'];
        //小区名
        $info['house_name'] = $info['house_title'];
        //用途（住宅、别墅、写字楼）
        preg_match('/物业类型:.*<span>(.*)<\/span>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['sell_type'] = 1;
            break;
          case "别墅":
            $info['sell_type'] = 2;
            break;
          default:
            $info['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/售价：.*<b>(.*)<\/b>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/均价：.*<span>(.*)<\/span>/siU', $con, $avgprice);
        $avgprice[1] = str_replace('元/平米', '', $avgprice[1]);
        $info['avgprice'] = $this->collect_model->con_replace(strip_tags($avgprice[1]));
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/朝向：.*<span>(.*)<\/span>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "朝东南":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "朝西南":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "朝西北":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "朝东北":
            $info['forward'] = 8;
            break;
          case "朝东西":
            $info['forward'] = 9;
            break;
          case "朝南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型：.*<span>(.*)<\/span>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        //面积
        preg_match('/售价：.*<\/b>(.*)平米<\/span>/siU', $con, $buildarea);
        $buildarea[1] = str_replace('万/', '', $buildarea[1]);
        $info['buildarea'] = $this->collect_model->con_replace(strip_tags($buildarea[1]));

        //房源描述-备注
        preg_match('/<div class="infoContent">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼层：.*<span>(.*)<\/span>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修：.*<span>(.*)<\/span>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        preg_match('/<em>产权：<\/em>.*建于(.*)年/siU', $con, $buildyear);
        $info['buildyear'] = $this->collect_model->con_replace(strip_tags($buildyear[1]));
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_sell_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    } elseif ($data['type'] == 2 && is_numeric($info['telno1'])) {//出租  没有发帖记录
      //过滤经纪人电话
      $res = $this->collect_model->check_phone($info['telno1'], $data);//判断电话号码是否在黑名单
      $info['isagent'] = 1;

      if (empty($res) && strlen($info['telno1']) > 10) {
        //房源标题
        preg_match('/<title>(.*)<\/title>/siU', $con, $house_title);
        $house_title[1] = str_replace('-小鱼房产_厦门最新楼盘、二手房、租房信息_厦门小鱼网', '', $house_title[1]);
        $info['house_title'] = $this->collect_model->con_replace(strip_tags($house_title[1]));
        //联系人
        preg_match('/<div class="secondAgent">.*<h4>(.*)<\/h4>/siU', $con, $contact);
        $info['owner'] = $this->collect_model->con_replace(strip_tags($contact[1]));
        //房源照片
        preg_match('/<div class="infoImg">(.*)<\/div>/siU', $con, $photo_con);
        preg_match_all('/<img.*data-url="(.*)".*/siU', $photo_con[1], $photo);
        if (!empty($photo[1])) {
          $info['web_picurl'] = implode("*", $photo[1]);
        } else {
          $info['web_picurl'] = '暂无资料';
        }
        if (false && !empty($photo[1])) {
          foreach ($photo[1] as $key => $value) {
            $photo[1][$key] = $this->collect_model->get_pic_url('http://fangzi.xmfish.com' . $value, $info['city']);
          }
          $info['picurl'] = implode("*", $photo[1]);
          $info['pic_cut'] = 1;
        } else {
          $info['picurl'] = '暂无资料';
          $info['pic_cut'] = 0;
        }
        // 区属 板块
        preg_match('/<em>小区：<\/em>.*<\/a>（(.*)）<\/span>/siU', $con, $district_block);
        $district_block_arr = explode(' ', $district_block[1]);
        $info['district'] = $this->collect_model->con_replace(strip_tags($district_block_arr[0]));//区属
        $info['block'] = $this->collect_model->con_replace(strip_tags($district_block_arr[1]));//板块
        // 小区地址
        $info['house_addr'] = $info['house_title'];
        //小区名
        $info['house_name'] = $info['house_title'];
        //用途（住宅、别墅、写字楼）
        preg_match('/物业类型:.*<span>(.*)<\/span>/siU', $con, $type);
        switch ($type[1]) {
          case "普通住宅":
            $info['rent_type'] = 1;
            break;
          case "别墅":
            $info['rent_type'] = 2;
            break;
          default:
            $info['rent_type'] = 1;
            break;
        }
        //租金
        preg_match('/租金:.*<td.*>(.*)<\/td>/siU', $con, $total_price);
        preg_match('/租金：.*<b>(.*)<\/b>/siU', $con, $total_price);
        $info['price'] = $this->collect_model->con_replace(strip_tags($total_price[1]));
        //付款方式
        preg_match('/支付类型：.*<span>(.*)<\/span>/siU', $con, $pricetype);
        $info['pricetype'] = $this->collect_model->con_replace(strip_tags($pricetype[1]));
        //朝向 1:东 2:东南 3:南 4:西南 5:西 6:西北 7:北 8:东北 9:东西 10:南北
        preg_match('/朝向：.*<span>(.*)<\/span>/siU', $con, $direction);
        $directions = $this->collect_model->con_replace(strip_tags($direction[1]));
        switch ($directions) {
          case "朝东":
            $info['forward'] = 1;
            break;
          case "朝东南":
            $info['forward'] = 2;
            break;
          case "朝南":
            $info['forward'] = 3;
            break;
          case "朝西南":
            $info['forward'] = 4;
            break;
          case "朝西":
            $info['forward'] = 5;
            break;
          case "朝西北":
            $info['forward'] = 6;
            break;
          case "朝北":
            $info['forward'] = 7;
            break;
          case "朝东北":
            $info['forward'] = 8;
            break;
          case "朝东西":
            $info['forward'] = 9;
            break;
          case "朝南北":
            $info['forward'] = 10;
            break;
          default:
            $info['forward'] = 0;
            break;
        }
        //户型
        preg_match('/户型：.*<span>(.*)<\/span>/siU', $con, $layout);
        $layout = $this->collect_model->con_replace(strip_tags($layout[1]));
        preg_match_all($reg, $layout, $result);//取出数值
        $info['room'] = $result[0][0] ? $result[0][0] : 0; //户型（室）
        $info['hall'] = $result[0][1] ? $result[0][1] : 0;//户型（厅）
        $info['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
        //面积
        $info['buildarea'] = $result[0][3] ? $result[0][3] : 0;

        //房源描述-备注
        preg_match('/<div class="infoContent">(.*)<\/div>/siU', $con, $remark);
        $info['remark'] = $this->collect_model->con_replace(strip_tags($remark[1]));
        //楼层
        preg_match('/楼层：.*<span>(.*)<\/span>/siU', $con, $floors);
        preg_match_all($reg_num, $floors[1], $floor_num);//取出数值
        if (count($floor_num[0]) == 1) {
          //$info['floor'] = $floor_num[0][0];//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][0]);//楼层（总层数）
        } else if (count($floor_num[0]) == 2) {
          $info['floor'] = intval($floor_num[0][0]);//楼层（所属层）
          $info['totalfloor'] = intval($floor_num[0][1]);//楼层（总层数）
        }
        //装修 1:毛坯 2:简装 3:中装 4:精装 5:豪装 6:婚装
        preg_match('/装修：.*<span>(.*)<\/span>/siU', $con, $decoration);
        $decoration[1] = $this->collect_model->con_replace(strip_tags($decoration[1]));
        switch ($decoration[1]) {
          case "毛坯":
            $info['serverco'] = 1;
            break;
          case "简单装修":
            $info['serverco'] = 2;
            break;
          case "中等装修":
            $info['serverco'] = 3;
            break;
          case "精装修":
            $info['serverco'] = 4;
            break;
          case "豪华装修":
            $info['serverco'] = 5;
            break;
          default:
            $info['serverco'] = 0;
            break;
        }
        //建筑年代
        $info['buildyear'] = '';
        //过滤重复房源hash 采集来源*小区名*电话*户型*面积*楼层
        $repeat_house = md5($info['source_from'] . '*' . $info['house_name'] . '*' . $info['telno1'] . '*' . $info['room'] . '*' . $info['hall'] . '*' . $info['toilet'] . '*' . $info['buildarea'] . '*' . $info['floor'] . '*' . $info['totalfloor']);
        //采集信息入库和更新
        $result = $this->collect_model->import_update_rent_message($data, $info, $repeat_house);
      } else {
        //是中介房源,请勿入库
        echo "<br><h3>此房源为中介房源：</h3><br>链接：" . $url;
      }
    }
  }
}
