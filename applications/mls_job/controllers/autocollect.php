<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect_nj controller CLASS
 *
 * 自动采集控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Autocollect extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $city = $this->input->get('city', true);
    //设置成熟参数
    $this->set_city($city);
    $this->load->model('autocollect_model');//自动采集控制器类
    $this->load->model('notice_access_model', 'na');
    //$this->output->enable_profiler(TRUE); //CI激活分析器（调试用）
  }

  /**
   * Index Page for this controller.
   */
  public function index()
  {
    $this->result(1, 'Entrust API for MLS.');
  }

  /********************跑数据 开始******************************/
  public function checkdata()
  {
    $city = $this->input->get('city', true);
    if ($city != 'nj') {
      $result = $this->autocollect_model->check_data($city, $database = 'db_city');
    }
  }
  /********************跑数据 结束******************************/

  /********************老版本列表去重 开始******************************/
  public function deldata()
  {
    $city = $this->input->get('city', true);
    $result = $this->autocollect_model->del_data($city, $database = 'db_city');
  }
  /********************老版本列表去重 结束******************************/

  /**
   * 采集赶集网二手房(分区域全部数据)列表页
   * 2015.5.11 cc
   */
  public function sell_ganji_house_lists_all()
  {
    $no = $this->input->get('no', true);
    $part = array('xuanwu', 'gulou', 'jianye', 'baixia', 'qinhuai', 'yuhuatai', 'jiangning', 'qixia', 'xiaguan', 'pukou', 'dachang', 'liuhe', 'lishui', 'gaochun', 'nanjingzhoubian');
    $lists = array();
    $i = 0;
    $page = 63;
    $max = 3;
    foreach ($part as $parkey => $parval) {
      if ($parkey == $no) {
        for ($num = 1; $num <= $page; $num++) {
          if ($num == 1) {
            $url = "http://nj.ganji.com/fang5/" . $parval . "/a1/";
          } else {
            $url = "http://nj.ganji.com/fang5/" . $parval . "/a1o" . $num . "/";
          }
          $compress = 'gzip';
          $content = $this->autocollect_model->vcurl($url, $compress);
          preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
          foreach ($prj[1] as $key => $val) {
            if ($key < $max) {
              continue;
            }
            $lists['url'] = "http://nj.ganji.com" . $val;
            $lists['type'] = 1;
            $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
            if ($res !== 0) {
              $i++;
            }
          }
        }
      }
    }
    echo "成功采集到 " . $i . "条" . $part[$no] . "租房房源！";
  }

  /**
   * 采集赶集网二手房列表页
   * 2015.6.4 cc
   */
  public function sell_ganji_house_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang5/a1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang5/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com" . $val;
        $lists['type'] = 1;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网二手房住宅
   * author  angel_in_us
   * date    2015-04-16
   */
  public function sell_ganji_house()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 1);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        //采集时间
        $data['createtime'] = time();
        $data['oldurl'] = $val;
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //联系人
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式(手机号码)
        preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 4) {
          $cpurl = "http://wap.ganji.com/" . $urlarr[1] . "/$urlarr[2]/" . $urlarr[3];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span>电话联系：<\/span>([\d]{11})<\/p>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        //发帖详情过滤经纪人
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集二手房发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }

        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<p class="my-shop mb-15">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);

        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数

          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
            }
            //print_r($messages);exit;
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
            //print_r($sec_hand);echo "<hr>";print_r($rent_house);echo "<hr>";print_r($rent_office);echo "<hr>";print_r($sell_office);exit;
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
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }
        }

        //房源照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<a href=".*".*src="(.*)".*<\/a>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        // 区属 板块
        preg_match('/位<i class="letter-space-8"><\/i>置：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->autocollect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $data['district'] = '暂无资料';//区属
        $data['block'] = '暂无资料';//板块
        if (count($addresss) == 2) {
          $data['district'] = $addresss[1];//区属
        } elseif (count($addresss) == 3) {
          $data['district'] = $addresss[1];//区属
          $data['block'] = $addresss[2];//板块

        }
        // 小区地址
        preg_match('/<span class="addr-area"(.*)<\/span>/siU', $con, $address);
        $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[0]));
        //小区名
        preg_match('/小<i class="letter-space-8"><\/i>区：<\/span>(.*)<\/a>/siU', $con, $building);
        if (!empty($building)) {
          $buildings = $this->autocollect_model->con_replace(strip_tags($building[1]));
          $house_names = explode('(', $buildings);
          $data['house_name'] = $house_names[0];
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/房屋类型：<\/span>(.*)<\/li>/siU', $con, $type);
        switch (@$type[1]) {
          case "普通住宅":
            $data['sell_type'] = 1;
            break;
          case "别墅":
            $data['sell_type'] = 2;
            break;
          case "公寓":
            $data['sell_type'] = 1;
            break;
          case "其他":
            $data['sell_type'] = 3;
            break;
          default :
            $data['sell_type'] = 1;
            break;

        }
        //总价
        preg_match('/售<i class=".*"><\/i>价：.*<b class=".*">(.*)<\/b>/siU', $con, $total_price);
        $data['price'] = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //单价
        preg_match('/单<i class=".*"><\/i>价：<\/span>(.*)元.*<\/li>/siU', $con, $average_price);
        $data['avgprice'] = $this->autocollect_model->con_replace(strip_tags(@$average_price[1]));
        //朝向
        preg_match('/况：<\/span>(.*)\-/siU', $con, $direction);
        $direction[1] = $this->autocollect_model->con_replace(strip_tags(@$direction[1]));
        switch ($direction[1]) {
          case "朝南":
            $data['forward'] = 3;
            break;
          case "朝北":
            $data['forward'] = 7;
            break;
          case "朝东":
            $data['forward'] = 1;
            break;
          case "朝西":
            $data['forward'] = 5;
            break;
          case "西南朝向":
            $data['forward'] = 4;
            break;
          case "东北朝向":
            $data['forward'] = 8;
            break;
          case "东南朝向":
            $data['forward'] = 2;
            break;
          case "西北朝向":
            $data['forward'] = 6;
            break;
          case "东西朝向":
            $data['forward'] = 9;
            break;
          case "南北朝向":
            $data['forward'] = 10;
            break;
          default:
            $data['forward'] = 0;
            break;
        }
        //户型（室）
        preg_match('/户<i class=".*"><\/i>型：<\/span>(.*)室.*概<i class="letter-space-8">/siU', $con, $room);
        @$data['room'] = $this->autocollect_model->con_replace(strip_tags(@$room[1]));
        //户型（厅）
        preg_match('/户<i class=".*"><\/i>型：<\/span>.*室(.*)厅.*概<i class="letter-space-8">/siU', $con, $hall);
        //户型（卫）
        if (!empty($hall)) {
          $data['hall'] = $this->autocollect_model->con_replace(strip_tags($hall[1]));
          preg_match('/户<i class=".*"><\/i>型：<\/span>.*室.*厅(.*)卫.*概<i class="letter-space-8">/siU', $con, $toilet);
        } else {
          $data['hall'] = "暂无资料";
          preg_match('/户<i class=".*"><\/i>型：<\/span>.*室(.*)卫.*概<i class="letter-space-8">/siU', $con, $toilet);
        }
        if (!empty($toilet)) {
          $data['toilet'] = $this->autocollect_model->con_replace(strip_tags($toilet[1]));
        } else {
          $data['toilet'] = "暂无资料";
        }
        //面积
        preg_match('/建筑面积：<\/span>(.*)㎡<\/li>/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags(@$acreage[1]));
        //楼层（所属层）
        preg_match('/楼<i class=".*"><\/i>层：<\/span>(.*)\//siU', $con, $floor);
        $data['floor'] = $this->autocollect_model->con_replace(strip_tags(@$floor[1]));
        //房源描述-备注
        preg_match('/房源描述：<\/strong>.*class="summary\-cont">(.*)<p class="clear">/siU', $con, $remark);
        $data['remark'] = $this->autocollect_model->con_replace(strip_tags(@$remark[1]));
        //楼层（总层数）
        preg_match('/楼<i class=".*"><\/i>层：<\/span>.*\/(.*)<\/li>/siU', $con, $total_floor);
        $data['totalfloor'] = $this->autocollect_model->con_replace(strip_tags($total_floor[1]));
        //装修
        preg_match('/装修程度：<\/span>(.*)<\/li>/siU', $con, $decoration);
        $decoration[1] = $this->autocollect_model->con_replace(strip_tags(@$decoration[1]));
        switch ($decoration[1]) {
          case "豪华装修":
            $data['serverco'] = 5;
            break;
          case "精装修":
            $data['serverco'] = 4;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 3;
            break;
          case "毛坯":
            $data['serverco'] = 1;
            break;
          case "婚装":
            $data['serverco'] = 6;
            break;
          default:
            $data['serverco'] = 0;
            break;
        }

        //echo "赶集出售采集测试：<br><pre>";print_r($data);die;

        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            //非中介房源,可以入库
            //判断该条房源是否已经采集过了
//					$where  = array('telno1'=>$data['telno1'],'house_title'=>$data['house_title']);
//					$result = $this->autocollect_model->check_house_only($where,$database='db_city');
//					if(empty($result)){
            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'sell_type' => $data['sell_type'],
              'price' => $data['price'],
              'avgprice' => $data['avgprice'],
              'forward' => $data['forward'],
              'room' => $data['room'],
              'hall' => $data['hall'],
              'toilet' => $data['toilet'],
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'remark' => $data['remark'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');

          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-出售详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集二手房房源图片存入服务器
   * 2015.6.6 cc
   */
  public function sell_ganji_house_picture()
  {
    $city = $this->input->get('city', true);
    $piclist = array();
    $limit = 1;
    $i = 0;
    $where = array('pictype' => 'sell_gj');
    $pic = array();
    $res = $this->autocollect_model->select_picture_control($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      $piclist = explode('*', $res[0]['picurl']);
      $num = count($piclist);
      foreach ($piclist as $key => $val) {
        $url = $val;
        $result = $this->autocollect_model->get_pic_url($url, $city);
        $pic[$key] = $result;
        $i++;
      }
      $picurl = implode("*", $pic);
      $where = array('id' => $res[0]['picid']);
      $update = array('picurl' => $picurl);
      $result = $this->autocollect_model->update_sell_house($where, $update, $database = 'db_city');
      $del = array('picid' => $res[0]['picid']);
      $result = $this->autocollect_model->del_picture_control($del, $database = 'db_city');
      $this->na->post_job_notice("赶集-出售照片-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无二手房房源照片";
    }
  }

  /**
   * 临时跑赶集sell_house_collect中有图片的房源
   * 2015.6.9 cc
   */
//        public function sell_ganji_house_photo() {
//            $i = 0;
//            $like = array('picurl'=>'http://');
//            $result = $this->autocollect_model->select_sell_ganji_house($like,$database='db_city');
//            foreach ($result as $val) {
//                if ($val['id'] != '' && $val['picurl'] != '' && $val['picurl'] != '暂无资料') {
//                    $picture = array(
//                    'picid'=>$val['id'],
//                    'picurl'=>$val['picurl'],
//                    'pictype'=>'sell_gj'
//                    );
//                    $rel = $this->autocollect_model->add_picture_control($picture,$database='db_city');
//                    $i++;
//                }
//            }
//            echo "成功采集到".$i."条二手房房源照片链接";
//        }

  /**
   * 采集赶集网租房(分区域全部数据)列表页
   * 2015.5.11 cc
   */
  public function rent_ganji_house_lists_all()
  {
    $no = $this->input->get('no', true);
    $part = array('xuanwu', 'gulou', 'jianye', 'baixia', 'qinhuai', 'yuhuatai', 'jiangning', 'qixia', 'xiaguan', 'pukou', 'dachang', 'liuhe', 'lishui', 'gaochun', 'nanjingzhoubian');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 63;
    foreach ($part as $parkey => $parval) {
      if ($parkey == $no) {
        for ($num = 1; $num <= $page; $num++) {
          if ($num == 1) {
            $url = "http://nj.ganji.com/fang1/" . $parval . "/a1/";
          } else {
            $url = "http://nj.ganji.com/fang1/" . $parval . "/a1o" . $num . "/";
          }
          $compress = 'gzip';
          $content = $this->autocollect_model->vcurl($url, $compress);
          preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
          foreach ($prj[1] as $key => $val) {
            if ($key < $max) {
              continue;
            }
            $lists['url'] = "http://nj.ganji.com" . $val;
            $lists['type'] = 2;
            $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
            if ($res !== 0) {
              $i++;
            }
          }
        }
      }
    }
    echo "成功采集到 " . $i . "条" . $part[$no] . "租房房源！";
  }

  /**
   * 采集赶集网租房列表页
   * 2015.6.4 cc
   */
  public function rent_ganji_house_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang1/a1m1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang1/a1m1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com" . $val;
        $lists['type'] = 2;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网租房
   * author  angel_in_us
   * date    2015-04-17
   */
  public function rent_ganji_house()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $where = array('type' => 2);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");

        $data = array();
        $data['oldurl'] = $val;
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //联系人
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));

        //采集电话
        preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 4) {
          $cpurl = "http://wap.ganji.com/" . $urlarr[1] . "/$urlarr[2]/" . $urlarr[3];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span>电话联系：<\/span>([\d]{11})<\/p>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        //采集时间
        $data['createtime'] = time();
        //发帖详情过滤经纪人
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集租房发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<p class="my-shop mb-15">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);

          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
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
              $type_sell_office = $length_time > 2592000 ? 1 : 0;
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
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }
        }
        //房源照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<a href=".*".*src="(.*)".*<\/a>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        // 区属 板块
        preg_match('/位<i class="letter-space-8"><\/i>置：<\/span>(.*)<\/li>/siU', $con, $add_mess);
        $address = $this->autocollect_model->con_replace(strip_tags($add_mess[1]));
        $addresss = explode('-', $address);
        $data['district'] = '暂无资料';//区属
        $data['block'] = '暂无资料';//板块
        if (count($addresss) == 2) {
          $data['district'] = $addresss[1];//区属
        } elseif (count($addresss) == 3) {
          $data['district'] = $addresss[1];//区属
          $data['block'] = $addresss[2];//板块
        }
        // 小区地址
        preg_match('/<span class="addr-area"(.*)<\/span>/siU', $con, $address);
        $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[0]));
        //小区名
        preg_match('/小<i class="letter-space-8"><\/i>区：<\/span>(.*)<\/a>/siU', $con, $building);
        if (!empty($building)) {
          $buildings = $this->autocollect_model->con_replace(strip_tags($building[1]));
          $house_names = explode('(', $buildings);
          $data['house_name'] = $house_names[0];
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/况：<\/span>.*\-(.*)\-/siU', $con, $type);
        switch (@$type[1]) {
          case "普通住宅":
            $data['rent_type'] = 1;
            break;
          case "别墅":
            $data['rent_type'] = 2;
            break;
          case "公寓":
            $data['rent_type'] = 1;
            break;
          case "其他":
            $data['rent_type'] = 3;
            break;
          default :
            $data['rent_type'] = 1;
            break;

        }
        //租金
        preg_match('/租<i class=".*"><\/i>金：.*<b class=".*">(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为1
        $data['price'] = is_numeric($total_prices) ? $total_prices : "0";
        //付款方式
        preg_match('/租<i class=".*"><\/i>金：.*<span class="fl">元\/月(.*)<\/span>/siU', $con, $pricetype);
        if (strlen(trim(@$pricetype[1])) > 0) {
          $pricetypes = str_replace(array("(", ")"), "", $pricetype[1]);
          $data['pricetype'] = $this->autocollect_model->con_replace(strip_tags($pricetypes));
        } else {
          $data['pricetype'] = "押一付三";
        }
        //朝向
        preg_match('/况：<\/span>(.*)\-/siU', $con, $direction);
        $direction[1] = $this->autocollect_model->con_replace(strip_tags(@$direction[1]));
        switch ($direction[1]) {
          case "朝南":
            $data['forward'] = 3;
            break;
          case "朝北":
            $data['forward'] = 7;
            break;
          case "朝东":
            $data['forward'] = 1;
            break;
          case "朝西":
            $data['forward'] = 5;
            break;
          case "西南朝向":
            $data['forward'] = 4;
            break;
          case "东北朝向":
            $data['forward'] = 8;
            break;
          case "东南朝向":
            $data['forward'] = 2;
            break;
          case "西北朝向":
            $data['forward'] = 6;
            break;
          case "东西朝向":
            $data['forward'] = 9;
            break;
          case "南北朝向":
            $data['forward'] = 10;
            break;
          default:
            $data['forward'] = 0;
            break;
        }
        //户型（室）
        preg_match('/户<i class=".*"><\/i>型：<\/span>(.*)室.*楼<i class="letter-space-8"><\/i>层/siU', $con, $room);
        @$data['room'] = $this->autocollect_model->con_replace(strip_tags(@$room[1]));
        //户型（厅）
        preg_match('/户<i class=".*"><\/i>型：<\/span>.*室(.*)厅.*楼<i class="letter-space-8"><\/i>层/siU', $con, $hall);
        //户型（卫）
        if (!empty($hall)) {
          $data['hall'] = $this->autocollect_model->con_replace(strip_tags($hall[1]));
          preg_match('/户<i class=".*"><\/i>型：<\/span>.*室.*厅(.*)卫.*楼<i class="letter-space-8"><\/i>层/siU', $con, $toilet);
        } else {
          $data['hall'] = "暂无资料";
          preg_match('/户<i class=".*"><\/i>型：<\/span>.*室(.*)卫.*楼<i class="letter-space-8"><\/i>层/siU', $con, $toilet);
        }
        if (!empty($toilet)) {
          $data['toilet'] = $this->autocollect_model->con_replace(strip_tags($toilet[1]));
        } else {
          $data['toilet'] = "暂无资料";
        }
        //面积
        preg_match('/户<i class=".*"><\/i>型：<\/span>.*卫 \-.*\-(.*)㎡.*楼<i class="letter-space-8"><\/i>层/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags(@$acreage[1]));
        //楼层（所属层）
        preg_match('/楼<i class=".*"><\/i>层：<\/span>(.*)\//siU', $con, $floor);
        $data['floor'] = $this->autocollect_model->con_replace(strip_tags($floor[1]));
        //房源描述-备注
        preg_match('/房源描述：<\/strong>.*class="summary\-cont">(.*)<p class="clear">/siU', $con, $remark);
        $data['remark'] = $this->autocollect_model->con_replace(strip_tags($remark[1]));
        //楼层（总层数）
        preg_match('/楼<i class=".*"><\/i>层：<\/span>.*\/(.*)<\/li>/siU', $con, $total_floor);
        $data['totalfloor'] = $this->autocollect_model->con_replace(strip_tags($total_floor[1]));
        //装修
        preg_match('/况：<\/span>.*\-.*\-(.*)<\/li>/siU', $con, $decoration);
        $decoration[1] = $this->autocollect_model->con_replace(strip_tags(@$decoration[1]));
        switch ($decoration[1]) {
          case "豪华装修":
            $data['serverco'] = 5;
            break;
          case "精装修":
            $data['serverco'] = 4;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 3;
            break;
          case "毛坯":
            $data['serverco'] = 1;
            break;
          case "婚装":
            $data['serverco'] = 6;
            break;
          default:
            $data['serverco'] = 0;
            break;
        }

        //echo "赶集出zu采集测试：<br><pre>";print_r($data);die;

        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => $data['rent_type'],
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'],
              'hall' => $data['hall'],
              'toilet' => $data['toilet'],
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'remark' => $data['remark'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];

            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-出租详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集租房房源图片存入服务器
   * 2015.6.13 cc
   */
  public function rent_ganji_house_picture()
  {
    $city = $this->input->get('city', true);
    $piclist = array();
    $limit = 1;
    $i = 0;
    $where = array('pictype' => 'rent_gj');
    $pic = array();
    $res = $this->autocollect_model->select_picture_control($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      $piclist = explode('*', $res[0]['picurl']);
      $num = count($piclist);
      foreach ($piclist as $key => $val) {
        $url = $val;
        $result = $this->autocollect_model->get_pic_url($url, $city);
        $pic[$key] = $result;
        $i++;
      }
      $picurl = implode("*", $pic);
      $where = array('id' => $res[0]['picid']);
      $update = array('picurl' => $picurl);
      $result = $this->autocollect_model->update_rent_house($where, $update, $database = 'db_city');
      $del = array('picid' => $res[0]['picid']);
      $result = $this->autocollect_model->del_picture_control($del, $database = 'db_city');
      $this->na->post_job_notice("赶集-出租照片-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无租房房源照片";
    }
  }

  /**
   * 临时跑赶集rent_house_collect中有图片的房源
   * 2015.6.9 cc
   */
//        public function rent_ganji_house_photo() {
//            $i = 0;
//            $like = array('picurl'=>'http://');
//            $result = $this->autocollect_model->select_rent_ganji_house($like,$database='db_city');
//            foreach ($result as $val) {
//                if ($val['id'] != '' && $val['picurl'] != '' && $val['picurl'] != '暂无资料') {
//                    $picture = array(
//                    'picid'=>$val['id'],
//                    'picurl'=>$val['picurl'],
//                    'pictype'=>'rent_gj'
//                    );
//                    $rel = $this->autocollect_model->add_picture_control($picture,$database='db_city');
//                    $i++;
//                }
//            }
//            echo "成功采集到".$i."条租房房源照片链接";
//        }

  /**
   * 采集58同城二手房分区列表页
   * 2015.6.13 cc
   */
  public function sell_wuba_house_lists_all()
  {
    $no = $this->input->get('no', true);
    $part = array('xuanwuqu', 'gulouqu', 'jianye', 'baixia', 'qinhuai', 'xiaguan', 'yuhuatai', 'pukouqu', 'qixiaqu', 'qixiaqu', 'qixiaqu', 'gaochunxian', 'lishuixian', 'lishuixian', 'nanjing');
    $lists = array();
    $i = 0;
    $page = 70;
    foreach ($part as $parkey => $parval) {
      if ($parkey == $no) {
        for ($num = 1; $num <= $page; $num++) {
          if ($num == 1) {
            $url = "http://nj.58.com/" . $parval . "/ershoufang/0/";
          } else {
            $url = "http://nj.58.com/" . $parval . "/ershoufang/0/pn" . $num . "/";
          }
          $compress = 'gzip';
          $content = $this->autocollect_model->vcurl($url, $compress);
          preg_match_all('/<tr logr=".*".*<h1 class="bthead">.*href="(http\:\/\/nj\.58\.com\/ershoufang\/.*\.shtml)" target="_blank".*<\/tr>/siU', $content, $prj);
          foreach ($prj[1] as $key => $val) {
            $lists['url'] = $val;
            $lists['type'] = 3;
            $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
            if ($res !== 0) {
              $i++;
            }
          }
        }
      }
    }
    echo "成功采集到 " . $i . " 条" . $part[$no] . "二手房住宅房源！";
  }

  /**
   * 采集58同城二手房列表页
   * 2015.5.12 cc
   */
  public function sell_wuba_house_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/ershoufang/0/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/ershoufang/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress, 1);
      preg_match_all('/<p class="bthead">.*href="(http\:\/\/' . $realcity[$city] . '\.58\.com\/ershoufang\/.*\.shtml).*" target="_blank".*<\/tr>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        $lists['url'] = $val;
        $lists['type'] = 3;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城二手房住宅
   * author  angel_in_us
   * date    2015-04-17
   */
  public function test()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 3);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        $del = array('url' => $value['url']);
        $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      } else {
        $val = "http://cd.58.com/ershoufang/24461509795896x.shtml";//$value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情

        $data = array();
        $housephoto = '';

        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;

          if ($postnum > 5) {
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            $del = array('url' => $val);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
            continue;
          }
        }

        //采集电话
        preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<li class="yellow">([\d]{11})<\/li>/siU', $cpcon, $photoarr);
          $housephoto = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }


        $data['oldurl'] = $val;
        //房源照片
        preg_match_all('/<div class="descriptionImg">.*<img src="(.*)".*<\/div>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }
        //房源标题
        preg_match('/<div class="bigtitle" >(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //楼盘地址
        preg_match('/地址：.*<div class="su_con .*">(.*)<\/div>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          //去除有（地图街景）
          $addresss = explode('(', $address[1]);
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[0]));
        }
        //区属
        preg_match('/位置.*<a href=".*">(.*)<\/a>/siU', $con, $district);
        if (empty($district[1])) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $this->autocollect_model->con_replace(strip_tags(@$district[1]));
        }
        //板块
        preg_match('/位置.*\-(.*)<\/li>/siU', $con, $block);
        if (!empty($block[1])) {
          $blocks = explode('-', $block[1]);
          if (isset($blocks[1])) {
            $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
          } else {
            $data['block'] = "暂无资料";
          }
        } else {
          $data['block'] = "暂无资料";
        }
        //楼盘名称
        preg_match('/位置.*\-(.*)<\/li>/siU', $con, $building);
        if (!empty($building[1])) {
          $build = explode('-', $building[1]);
          if (isset($build[1])) {
            $builds = explode('（', $build[1]);
          } else {
            $builds = explode('（', $build[0]);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($builds[0]));
        } else {
          $data['house_name'] = "暂无资料";
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/住宅类别：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $type);
        switch (@$type[1]) {
          case "普通住宅":
            $data['sell_type'] = 1;
            break;
          case "别墅":
            $data['sell_type'] = 2;
            break;
          case "公寓":
            $data['sell_type'] = 1;
            break;
          case "其他":
            $data['sell_type'] = 3;
            break;
          default:
            $data['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/售价：<\/div>.*<span class="bigpri arial">(.*)<\/span>/siU', $con, $total_price);
        $data['price'] = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //单价
        preg_match('/售价：<\/div>.*\（(.*)元\/㎡\）/siU', $con, $average_price);
        $data['avgprice'] = $this->autocollect_model->con_replace(strip_tags(@$average_price[1]));
        //朝向
        preg_match('/朝向：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $direction);
        $direction[1] = $this->autocollect_model->con_replace(strip_tags(@$direction[1]));
        switch ($direction[1]) {
          case "南":
            $data['forward'] = 3;
            break;
          case "北":
            $data['forward'] = 7;
            break;
          case "东":
            $data['forward'] = 1;
            break;
          case "西":
            $data['forward'] = 5;
            break;
          case "西南":
            $data['forward'] = 4;
            break;
          case "东北":
            $data['forward'] = 8;
            break;
          case "东南":
            $data['forward'] = 2;
            break;
          case "西北":
            $data['forward'] = 6;
            break;
          case "东西":
            $data['forward'] = 9;
            break;
          case "南北":
            $data['forward'] = 10;
            break;
          default:
            $data['forward'] = 3;
            break;
        }
        //户型（室）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">(.*)室/siU', $con, $room);
        @$data['room'] = $this->autocollect_model->con_replace(strip_tags(@$room[1]));
        //户型（厅）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*室(.*)厅/siU', $con, $hall);
        @$data['hall'] = $this->autocollect_model->con_replace(strip_tags(@$hall[1]));
        //户型（卫）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*厅(.*)卫/siU', $con, $toilet);
        @$data['toilet'] = $this->autocollect_model->con_replace(strip_tags(@$toilet[1]));
        //面积
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*卫(.*)㎡/siU', $con, $acreage);
        $data['buildareas'] = $this->autocollect_model->con_replace(strip_tags(@$acreage[1]));
        $data['buildarea'] = str_replace("　", "", $data['buildareas']);
        $data['buildarea'] = str_replace("（套内", "", $data['buildarea']);
        $data['buildarea'] = trim($data['buildarea']);
        //楼层（所属层）
        preg_match('/房屋楼层：.*<li class="des_cols2">(.*)\//siU', $con, $floor);
        if (empty($floor[1])) {
          $data['floor'] = '';
        } else {
          $data['floor'] = $this->autocollect_model->con_replace(strip_tags($floor[1]));
        }
        //房源描述-备注
        preg_match('/<article class="description_con " >(.*)<\/p>/siU', $con, $remark);
        $data['remark'] = $this->autocollect_model->con_replace(strip_tags(@$remark[1]));
        //楼层（总层数）
        preg_match('/房屋楼层：.*<li class="des_cols2">.*\/(.*)楼/siU', $con, $total_floor);
        if (empty($total_floor[1])) {
          $data['totalfloor'] = '';
        } else {
          $data['totalfloor'] = $this->autocollect_model->con_replace(strip_tags($total_floor[1]));
        }
        //装修
        preg_match('/装修程度：.*<li class="des_cols2">(.*)<a.*/siU', $con, $decoration);
        $decoration[1] = $this->autocollect_model->con_replace(strip_tags(@$decoration[1]));
        $decorations[1] = str_replace("&nbsp", "", $decoration[1]);
        switch ($decorations[1]) {
          case "豪华装修":
            $data['serverco'] = 5;
            break;
          case "精装修":
            $data['serverco'] = 4;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 3;
            break;
          case "毛坯":
            $data['serverco'] = 1;
            break;
          case "婚装":
            $data['serverco'] = 6;
            break;
          default:
            $data['serverco'] = 2;
            break;
        }
        //联系人
        preg_match('/联系人：.*<span style="float:left;margin-right:10px;">(.*)<\/span>/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $contacts = explode('（', $contact[1]);
          $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contacts[0]));
        } else {
          $data['owner'] = "个人";
        }
        //联系方式
        preg_match('/<span id="t_phone" class="f20.*document\.write\("<img src=\'(.*)\' \/>"/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //采集时间
        $data['createtime'] = time();
        //echo "58出售采集测试：<br><pre>";print_r($data);die;
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $housephoto);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            //非中介房源,可以入库
            //判断该条房源是否已经采集过了
            //$where  = array('telno1'=>$data['telno1'],'house_title'=>$data['house_title']);
            //$result = $this->autocollect_model->check_house_only($where,$database='db_city');
            //if(empty($result)){
            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => urlencode($data['house_title']),
              'district' => urlencode($data['district']),
              'block' => urlencode($data['block']),
              'house_name' => urlencode($data['house_name']),
              'house_addr' => urlencode($data['house_addr']),
              'sell_type' => urlencode($data['sell_type']),
              'price' => urlencode($data['price']),
              'avgprice' => urlencode($data['avgprice']),
              'forward' => urlencode($data['forward']),
              'room' => urlencode($data['room']),
              'hall' => urlencode($data['hall']),
              'toilet' => urlencode($data['toilet']),
              'buildarea' => urlencode($data['buildarea']),
              'floor' => urlencode($data['floor']),
              'totalfloor' => urlencode($data['totalfloor']),
              'serverco' => urlencode($data['serverco']),
              'oldurl' => urlencode($data['oldurl']),
              'owner' => urlencode($data['owner']),
//                                            'tel_url'=>(string)$data['telno1'],
              'createtime' => urlencode($data['createtime']),
              'remark' => urlencode($data['remark']),
              'picurl' => urlencode($data['picurl']),
              'e_status' => urlencode(0),
              'source_from' => urlencode(1)
            );

            $text = array(
              'text' => json_encode($info),
              'telpic' => (string)$data['telno1'],
              'type' => 0,
              'telno' => $housephoto
            );
            $rel = $this->autocollect_model->add_wuba_house_collect($text, $database = 'db_city');
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
//                                  }else{
//                                      //房源已经入库，请勿重复采集
//					echo "<br><h3>此房源已经入库：</h3><br>标题：".$data['house_title']."<br>链接：".$data['oldurl'];
//					continue;
//                                  }
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            $del = array('url' => $val);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          $del = array('url' => $val);
          $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-出售临时详情-" . $i, $city);
    echo 'over';
  }


  /**
   * 采集58同城二手房住宅
   * author  angel_in_us
   * date    2015-04-17
   */
  public function sell_wuba_house()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 3);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {

      //删除采集队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');

      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        $data = array();
        $housephoto = '';
        //采集时间
        $data['createtime'] = time();
        $data['oldurl'] = $val;
        //房源标题
        preg_match('/<div class="bigtitle" >(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //联系方式
        preg_match('/<span id="t_phone" class="f20.*document\.write\("<img src=\'(.*)\' \/>"/siU', $con, $tel);
        $housephoto = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //采集电话
        preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span id="number" >([\d]{11})<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        //联系人
        preg_match('/联系人：.*<span style="float:left;margin-right:10px;">(.*)<\/span>/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $contacts = explode('（', $contact[1]);
          $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contacts[0]));
        } else {
          $data['owner'] = "个人";
        }
        if (strstr($contact[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58二手房发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }


        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;

          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }
        //房源照片
        preg_match_all('/<div class="descriptionImg">.*<img src="(.*)".*<\/div>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        //楼盘地址
        preg_match('/地址：.*<div class="su_con .*">(.*)<\/div>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          //去除有（地图街景）
          $addresss = explode('(', $address[1]);
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[0]));
        }
        //区属
        preg_match('/位置.*<a href=".*">(.*)<\/a>/siU', $con, $district);
        if (empty($district[1])) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $this->autocollect_model->con_replace(strip_tags(@$district[1]));
        }
        //板块
        preg_match('/位置.*\-(.*)<\/li>/siU', $con, $block);
        if (!empty($block[1])) {
          $blocks = explode('-', $block[1]);
          if (isset($blocks[1])) {
            $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
          } else {
            $data['block'] = "暂无资料";
          }
        } else {
          $data['block'] = "暂无资料";
        }
        //楼盘名称
        preg_match('/位置.*\-(.*)<\/li>/siU', $con, $building);
        if (!empty($building[1])) {
          $build = explode('-', $building[1]);
          if (isset($build[1])) {
            $builds = explode('（', $build[1]);
          } else {
            $builds = explode('（', $build[0]);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($builds[0]));
        } else {
          $data['house_name'] = "暂无资料";
        }
        //用途（住宅、别墅、写字楼）
        preg_match('/住宅类别：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $type);
        switch (@$type[1]) {
          case "普通住宅":
            $data['sell_type'] = 1;
            break;
          case "别墅":
            $data['sell_type'] = 2;
            break;
          case "公寓":
            $data['sell_type'] = 1;
            break;
          case "其他":
            $data['sell_type'] = 3;
            break;
          default:
            $data['sell_type'] = 1;
            break;
        }
        //总价
        preg_match('/售价：<\/div>.*<span class="bigpri arial">(.*)<\/span>/siU', $con, $total_price);
        $data['price'] = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //单价
        preg_match('/售价：<\/div>.*\（(.*)元\/㎡\）/siU', $con, $average_price);
        $data['avgprice'] = $this->autocollect_model->con_replace(strip_tags(@$average_price[1]));
        //朝向
        preg_match('/朝向：.*<li class="des_cols2">(.*)<\/li>/siU', $con, $direction);
        $direction[1] = $this->autocollect_model->con_replace(strip_tags(@$direction[1]));
        switch ($direction[1]) {
          case "南":
            $data['forward'] = 3;
            break;
          case "北":
            $data['forward'] = 7;
            break;
          case "东":
            $data['forward'] = 1;
            break;
          case "西":
            $data['forward'] = 5;
            break;
          case "西南":
            $data['forward'] = 4;
            break;
          case "东北":
            $data['forward'] = 8;
            break;
          case "东南":
            $data['forward'] = 2;
            break;
          case "西北":
            $data['forward'] = 6;
            break;
          case "东西":
            $data['forward'] = 9;
            break;
          case "南北":
            $data['forward'] = 10;
            break;
          default:
            $data['forward'] = 0;
            break;
        }
        //户型（室）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">(.*)室/siU', $con, $room);
        @$data['room'] = $this->autocollect_model->con_replace(strip_tags(@$room[1]));
        //户型（厅）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*室(.*)厅/siU', $con, $hall);
        @$data['hall'] = $this->autocollect_model->con_replace(strip_tags(@$hall[1]));
        //户型（卫）
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*厅(.*)卫/siU', $con, $toilet);
        @$data['toilet'] = $this->autocollect_model->con_replace(strip_tags(@$toilet[1]));
        //面积
        preg_match('/<div class="su_tit">户型：.*<div class="su_con">.*卫(.*)㎡/siU', $con, $acreage);
        $data['buildareas'] = $this->autocollect_model->con_replace(strip_tags(@$acreage[1]));
        $data['buildarea'] = str_replace("　", "", $data['buildareas']);
        $data['buildarea'] = str_replace("（套内", "", $data['buildarea']);
        $data['buildarea'] = trim($data['buildarea']);

        //楼层（所属层）
        preg_match('/房屋楼层：.*<li class="des_cols2">(.*)\//siU', $con, $floor);
        if (empty($floor[1])) {
          $data['floor'] = '';
        } else {
          $data['floor'] = $this->autocollect_model->con_replace(strip_tags($floor[1]));
        }
        //房源描述-备注
        preg_match('/<article class="description_con " >(.*)<\/p>/siU', $con, $remark);
        $data['remark'] = $this->autocollect_model->con_replace(strip_tags(@$remark[1]));
        //楼层（总层数）
        preg_match('/房屋楼层：.*<li class="des_cols2">.*\/(.*)楼/siU', $con, $total_floor);
        if (empty($total_floor[1])) {
          $data['totalfloor'] = '';
        } else {
          $data['totalfloor'] = $this->autocollect_model->con_replace(strip_tags($total_floor[1]));
        }
        //装修
        preg_match('/装修程度：.*<li class="des_cols2">(.*)<a.*/siU', $con, $decoration);
        $decoration[1] = $this->autocollect_model->con_replace(strip_tags(@$decoration[1]));
        $decorations[1] = str_replace("&nbsp", "", $decoration[1]);
        switch ($decorations[1]) {
          case "豪华装修":
            $data['serverco'] = 5;
            break;
          case "精装修":
            $data['serverco'] = 4;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 3;
            break;
          case "毛坯":
            $data['serverco'] = 1;
            break;
          case "婚装":
            $data['serverco'] = 6;
            break;
          default:
            $data['serverco'] = 2;
            break;
        }

        //echo "58出售采集测试：<br><pre>";print_r($data);die;
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'sell_type' => $data['sell_type'],
              'price' => $data['price'],
              'avgprice' => $data['avgprice'],
              'forward' => $data['forward'],
              'room' => $data['room'],
              'hall' => $data['hall'],
              'toilet' => $data['toilet'],
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'remark' => $data['remark'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-出售临时详情-" . $i, $city);
    echo 'over';
  }

  /**
   *采集58房源电话图片存入服务器并同时入房源详情库
   * 2015.6.29 cc
   */
  public function sell_wuba_telpic()
  {
    $city = $this->input->get('city', true);
    $num = 0;//用来触屏采集计数
    $i = 0;
    $where = array('type' => 0);
    $limit = 5;
    $res = $this->autocollect_model->select_wuba_telpic($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      foreach ($res as $key => $val) {
        $info = json_decode(urldecode($val['text']), TRUE);
        $picurl = $info['picurl'];
        $housephoto = $val['telno'];
        //如果在触屏那已经采集到电话了就不用识别图片了
        if ('' != $housephoto) {
          $i++;
          $num++;
          $info['telno1'] = $housephoto;
          $info['picurl'] = '暂无资料';
          $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
        } else {
          $url = $val['telpic'];
          $picresult1 = $this->autocollect_model->get_telpic_url($url, $city, '&1');
          $picresult2 = $this->autocollect_model->get_telpic_url($url, $city, '&2');

          $res1 = $this->doshow($picresult1, 1);
          $res2 = $this->doshow($picresult2, 2);
          if (strlen($res1) > 10 && strlen($res2) > 10) {
            if ($res1 == $res2) {
              $i++;
              $info['telno1'] = $res1;
              $info['picurl'] = '暂无资料';
              $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
              $this->na->post_job_notice("58-出售号码-成功（第一次：" . $res1 . " 第二次：" . $res2 . "）", $city);
            } else {
              $this->na->post_job_notice("58-出售号码-失败（第一次：" . $res1 . " 第二次：" . $res2 . "）", $city);
            }
          } elseif (strlen($res1) > 10 && strlen($res2) < 11) {
            $i++;
            $info['telno1'] = $res1;
            $info['picurl'] = '暂无资料';
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            $this->na->post_job_notice("58-出售号码-成功（第一次成功：" . $res1 . " 第二次：" . $res2 . "）", $city);
          } elseif (strlen($res2) > 10 && strlen($res1) < 11) {
            $i++;
            $info['telno1'] = $res2;
            $info['picurl'] = '暂无资料';
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            $this->na->post_job_notice("58-出售号码-成功（第一次：" . $res1 . " 第二次成功：" . $res2 . "）", $city);
          }
          $localfile1 = "applications/mls_job/telpic/" . $picresult1;
          $localfile2 = "applications/mls_job/telpic/" . $picresult2;
          @unlink($localfile1);
          @unlink($localfile2);
        }
        $del = array('id' => $val['id']);
        $result = $this->autocollect_model->del_wuba_telpic($del, $database = 'db_city');
        if (isset($rel) && $rel != '' && $picurl != '暂无资料') {
          $picture = array(
            'picid' => $rel,
            'picurl' => $picurl,
            'pictype' => 'sell_58'
          );
          $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
        }
      }
      if ($num != 0) {
        $this->na->post_job_notice("58-出售号码-成功（触屏采集）-" . $num, $city);
      }
      $this->na->post_job_notice("58-出售详情-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无二手房房源电话图片";
    }
  }

  /**
   * 采集二手房房源图片存入服务器
   * 2015.6.13 cc
   */
  public function sell_wuba_house_picture()
  {
    $city = $this->input->get('city', true);
    $piclist = array();
    $limit = 1;
    $i = 0;
    $where = array('pictype' => 'sell_58');
    $pic = array();
    $res = $this->autocollect_model->select_picture_control($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      $piclist = explode('*', $res[0]['picurl']);
      $num = count($piclist);
      foreach ($piclist as $key => $val) {
        $url = $val;
        $result = $this->autocollect_model->get_pic_url($url, $city);
        $pic[$key] = $result;
        $i++;
      }
      $picurl = implode("*", $pic);
      $where = array('id' => $res[0]['picid']);
      $update = array('picurl' => $picurl);
      $result = $this->autocollect_model->update_sell_house($where, $update, $database = 'db_city');
      $del = array('picid' => $res[0]['picid']);
      $result = $this->autocollect_model->del_picture_control($del, $database = 'db_city');
      $this->na->post_job_notice("58-出售照片-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无二手房房源照片";
    }
  }

  /**
   * 采集58同城租房分区域列表页
   * 2015.6.13 cc
   */
  public function rent_wuba_house_lists_all()
  {
    $no = $this->input->get('no', true);
    $part = array('xuanwuqu', 'gulouqu', 'jianye', 'baixia', 'qinhuai', 'xiaguan', 'yuhuatai', 'pukouqu', 'qixiaqu', 'qixiaqu', 'qixiaqu', 'gaochunxian', 'lishuixian', 'lishuixian', 'nanjing');
    $lists = array();
    $i = 0;
    $page = 100;
    $max = 3;
    foreach ($part as $parkey => $parval) {
      if ($parkey == $no) {
        for ($num = 1; $num <= $page; $num++) {
          if ($num == 1) {
            $url = "http://nj.58.com/" . $parval . "/zufang/0/";
          } else {
            $url = "http://nj.58.com/" . $parval . "/zufang/0/pn" . $num . "/";
          }
          $compress = 'gzip';
          $content = $this->autocollect_model->vcurl($url, $compress);
          preg_match_all('/<td class="t qj-rentd">.*href="(http\:\/\/nj\.58\.com\/zufang\/.*\.shtml)" target="_blank".*<\/a>/siU', $content, $prj);
          foreach ($prj[1] as $key => $val) {
            if ($key < $max) {
              continue;
            }
            $lists['url'] = $val;
            $lists['type'] = 4;
            $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
            if ($res !== 0) {
              $i++;
            }
          }
        }
      }
    }
    echo "成功采集到 " . $i . " 条" . $part[$no] . "租房房源！";
  }

  /**
   * 采集58同城网租房列表页 新版
   * 2016.3.3 ccy
   */
  public function rent_wuba_house_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('km' => 'km', 'xa' => 'xa', 'nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/zufang/0/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/zufang/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress, 1);
      preg_match_all('/<div class="img_list">.*href="http:\/\/' . $realcity[$city] . '.58.com\/zufang(.*)shtml.*target="_blank"/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = 'http://' . $realcity[$city] . '.58.com/zufang' . $val . 'shtml';
        $lists['type'] = 4;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网租房
   * author  angel_in_us
   * date    2015-04-17
   */
  public function rent_wuba_house()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 4);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      //删除链接队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');

      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        // $del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        // print_r($con);exit;
        $data = array();
        $housephoto = '';

        $data['oldurl'] = $val;
        //采集电话
        preg_match('/http:\/\/(.*).58.com\/zufang\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/zufang/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span class="meta-phone">([\d]{11})<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        //房源标题
        preg_match('/<h1 class="main-title font-heiti">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //采集时间
        $data['createtime'] = time();
        //联系人
        preg_match('/<span class="fl pr20 c2e">联系：(.*)<\/a>/siU', $con, $contact);
        $contacts = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($contact[1]));
        $contact = explode('(', $contacts);
        if (strlen(trim(@$contact[0])) > 0) {
          $data['owner'] = $contact[0];
        } else {
          $data['owner'] = "个人";
        }
        if (strstr($contacts, "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58租房发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }

        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;

          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }
        //房源照片
        preg_match('/<ul class="house-images-list">(.*)<\/ul>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<li class="house-images-wrap"><img lazy_src="(.*)" src="/siU', $cons[1], $photo);
          if (!empty($photo[1])) {
            $data['picurl'] = implode("*", $photo[1]);
          } else {
            $data['picurl'] = "暂无资料";
          }
        } else {
          $data['picurl'] = "暂无资料";
        }
        //楼盘地址
        preg_match('/<div class="fl c70">(.*)<\/div>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块、楼盘名称
        preg_match('/<div class="fl xiaoqu c70">(.*)<\/div>/siU', $con, $mess);
        $mess = explode('-', $mess[1]);
        if (count($mess) == 1) {
          $buildings = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //楼盘名称
        } elseif (count($mess) == 2) {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $buildings = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //楼盘名称
        } else {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $blocks = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //板块
          $buildings = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[2]));  //楼盘名称
        }
        if (empty($district)) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $district;
        }

        if (empty($blocks)) {
          $data['block'] = "暂无资料";
        } else {
          $data['block'] = $blocks;
        }
        if (strstr($buildings, "在租") || strstr($buildings, "在售")) {
          $buildings = explode('（', $buildings);
          $building = $buildings[0];
        } else {
          $building = $buildings;
        }
        if (empty($building)) {
          $data['house_name'] = "暂无资料";
        } else {
          $data['house_name'] = $building;
        }
        //楼盘信息
        preg_match('/<div class="fl house-type c70">(.*)<\/div>/siU', $con, $loupan_mess);
        $loupan_mess = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($loupan_mess[1]));
        $loupan_mess = explode('-', $loupan_mess);
        $reg = '/\d+/';//匹配数字的正则表达式
        foreach ($loupan_mess as $key => $value) {
          if (strstr($value, "室")) {
            preg_match_all($reg, $value, $result);
            if (strstr($value, "卫") && strstr($value, "厅")) {
              $data['room'] = $result[0][0];//户型（室）
              $data['hall'] = $result[0][1];//户型（厅）
              $data['toilet'] = $result[0][2] ? $result[0][2] : 0;//户型（卫）
            } elseif (strstr($value, "卫") && !strstr($value, "厅")) {
              $data['room'] = $result[0][0];//户型（室）
              $data['toilet'] = $result[0][1];//户型（卫）
              $data['hall'] = 0;
            }
          }
          if (strstr($value, "m²")) {
            preg_match_all($reg, $value, $acreage);//面积
            $data['buildarea'] = $acreage[0][0];//面积
          }
          if (strstr($value, "层")) {
            $decorat = explode('层', $value);//装修
            $decoration = $decorat[1];
            preg_match_all($reg, $value, $floor_mess);//楼层
            $floor = $floor_mess[0][0];//楼层（所属层）
            $total_floor = $floor_mess[0][1];//楼层（总层数）
          }
          if (strstr($value, "朝向")) {
            $direction = substr($value, 6);//朝向
          }
          if (strstr($value, "普通住宅") || strstr($value, "别墅") || strstr($value, "公寓") || strstr($value, "其他")) {
            $type = $value;//用途（住宅、别墅、写字楼）
          }
        }
        switch (@$type) {
          case "普通住宅":
            $data['rent_type'] = 1;
            break;
          case "别墅":
            $data['rent_type'] = 2;
            break;
          case "公寓":
            $data['rent_type'] = 1;
            break;
          case "其他":
            $data['rent_type'] = 3;
            break;
          default:
            $data['rent_type'] = 1;
            break;
        }
        //楼层（所属层）
        if (empty($floor)) {
          $data['floor'] = '';
        } else {
          $data['floor'] = $floor;
        }
        //楼层（总层数）
        if (empty($total_floor)) {
          $data['totalfloor'] = '';
        } else {
          $data['totalfloor'] = $total_floor;
        }
        //朝向
        if (empty($direction)) {
          $data['forward'] = 0;
        } else {
          $direction = $direction;
          switch ($direction) {
            case "南":
              $data['forward'] = 3;
              break;
            case "北":
              $data['forward'] = 7;
              break;
            case "东":
              $data['forward'] = 1;
              break;
            case "西":
              $data['forward'] = 5;
              break;
            case "西南":
              $data['forward'] = 4;
              break;
            case "东北":
              $data['forward'] = 8;
              break;
            case "东南":
              $data['forward'] = 2;
              break;
            case "西北":
              $data['forward'] = 6;
              break;
            case "东西":
              $data['forward'] = 9;
              break;
            case "南北":
              $data['forward'] = 10;
              break;
            default:
              $data['forward'] = 0;
              break;
          }
        }
        //装修
        if (empty($decoration)) {
          $data['serverco'] = 0;
        } else {
          $decoration = $decoration;
          switch ($decoration) {
            case "豪华装修":
              $data['serverco'] = 5;
              break;
            case "精装修":
              $data['serverco'] = 4;
              break;
            case "简单装修":
              $data['serverco'] = 2;
              break;
            case "中等装修":
              $data['serverco'] = 3;
              break;
            case "毛坯":
              $data['serverco'] = 1;
              break;
            case "婚装":
              $data['serverco'] = 6;
              break;
            default:
              $data['serverco'] = 0;
              break;
          }
        }
        //租金
        preg_match('/<em class="house-price">(.*)<\/em>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //有面议设置为1
        $data['price'] = is_numeric($total_prices) ? $total_prices : "0";
        //付款方式
        preg_match('/<span class="pay-method f16 c70">(.*)<\/span>/siU', $con, $pricetype);
        if (empty($pricetype[1])) {
          $data['pricetype'] = "押一付三";
        } else {
          $data['pricetype'] = $this->autocollect_model->con_replace(strip_tags($pricetype[1]));
        }
        //房源描述-备注
        preg_match('/<div class="description-content">(.*)<\/div>/siU', $con, $remark);
        $data['remark'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($remark[1]));
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            $info = array(
              'house_name' => $data['house_name'],
              'rent_type' => $data['rent_type'],
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'],
              'hall' => $data['hall'],
              'toilet' => $data['toilet'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'remark' => $data['remark'],
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_addr' => $data['house_addr'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-出租临时详情-" . $i, $city);
    echo 'over';
  }

  /**
   *采集58房源电话图片存入服务器并同时入房源详情库
   * 2015.6.29 cc
   */
  public function rent_wuba_telpic()
  {
    $city = $this->input->get('city', true);
    $num = 0;//用来触屏采集计数
    $i = 0;
    $where = array('type' => 1);
    $limit = 5;
    $res = $this->autocollect_model->select_wuba_telpic($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      foreach ($res as $val) {
        $info = json_decode(urldecode($val['text']), TRUE);
        $picurl = $info['picurl'];
        $housephoto = $val['telno'];
        //如果在触屏那已经采集到电话了就不用识别图片了
        if ('' != $housephoto) {
          $i++;
          $num++;
          $info['telno1'] = $housephoto;
          $info['picurl'] = '暂无资料';
          $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
        } else {
          $url = $val['telpic'];
          $picresult1 = $this->autocollect_model->get_telpic_url($url, $city, '&1');
          $picresult2 = $this->autocollect_model->get_telpic_url($url, $city, '&2');

          $res1 = $this->doshow($picresult1, 1);
          $res2 = $this->doshow($picresult2, 2);
          if (strlen($res1) > 10 && strlen($res2) > 10) {
            if ($res1 == $res2) {
              $i++;
              $info['telno1'] = $res1;
              $info['picurl'] = '暂无资料';
              $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
              $this->na->post_job_notice("58-出租号码-成功（第一次：" . $res1 . " 第二次：" . $res2 . "）", $city);
            } else {
              $this->na->post_job_notice("58-出租号码-失败（第一次：" . $res1 . " 第二次：" . $res2 . "）", $city);
            }
          } elseif (strlen($res1) > 10 && strlen($res2) < 11) {
            $i++;
            $info['telno1'] = $res1;
            $info['picurl'] = '暂无资料';
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            $this->na->post_job_notice("58-出租号码-成功（第一次成功：" . $res1 . " 第二次：" . $res2 . "）", $city);
          } elseif (strlen($res2) > 10 && strlen($res1) < 11) {
            $i++;
            $info['telno1'] = $res2;
            $info['picurl'] = '暂无资料';
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            $this->na->post_job_notice("58-出租号码-成功（第一次：" . $res1 . " 第二次成功：" . $res2 . "）", $city);
          }
          $localfile1 = "applications/mls_job/telpic/" . $picresult1;
          $localfile2 = "applications/mls_job/telpic/" . $picresult2;
          @unlink($localfile1);
          @unlink($localfile2);
        }

        $del = array('id' => $val['id']);
        $result = $this->autocollect_model->del_wuba_telpic($del, $database = 'db_city');
        if (isset($rel) && $rel != '' && $picurl != '暂无资料') {
          $picture = array(
            'picid' => $rel,
            'picurl' => $picurl,
            'pictype' => 'rent_58'
          );
          $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
        }
      }
      if ($num != 0) {
        $this->na->post_job_notice("58-出租号码-成功（触屏采集）-" . $num, $city);
      }
      $this->na->post_job_notice("58-出租详情-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无租房房源电话图片";
    }
  }

  /**
   * 采集租房房源图片存入服务器
   * 2015.6.13 cc
   */
  public function rent_wuba_house_picture()
  {
    $city = $this->input->get('city', true);
    $piclist = array();
    $limit = 1;
    $i = 0;
    $where = array('pictype' => 'rent_58');
    $pic = array();
    $res = $this->autocollect_model->select_picture_control($where, $limit, $database = 'db_city');
    if (!empty($res)) {
      $piclist = explode('*', $res[0]['picurl']);
      $num = count($piclist);
      foreach ($piclist as $key => $val) {
        $url = $val;
        $result = $this->autocollect_model->get_pic_url($url, $city);
        $pic[$key] = $result;
        $i++;
      }
      $picurl = implode("*", $pic);
      $where = array('id' => $res[0]['picid']);
      $update = array('picurl' => $picurl);
      $result = $this->autocollect_model->update_rent_house($where, $update, $database = 'db_city');
      $del = array('picid' => $res[0]['picid']);
      $result = $this->autocollect_model->del_picture_control($del, $database = 'db_city');
      $this->na->post_job_notice("58-出租照片-" . $i, $city);
      echo 'over';
    } else {
      echo "暂时无租房房源照片";
    }
  }

  public function doshow($img, $k)
  {
    $imgPath = "applications/mls_job/telpic/" . $img;
    $this->load->library('Gjphone', array($imgPath), 'gjphone');
    //进行颜色分离
    $this->gjphone->getHec();
    //画出横向数据
    $size = $this->gjphone->getSize();
    $limit = 11;

    for ($i = 6; $i < $size[0]; ++$i) {
      $p = 0;
      $horData = $this->gjphone->magHorData($i, $limit);
      if (count($horData[1]) < $limit) {
        break;
      }

      $firststr = implode('', $horData[1]);
      if ('0000000001' == $firststr) {
        $horData = $this->gjphone->magHorData($i, $limit - 1);
      }
      if ('1000000000' == $firststr) {
        $horData = $this->gjphone->magHorData($i, $limit, true);
      }
      //$gjPhone->drawWH($horData);echo "<br />";
      $p = $this->gjphone->mgArr(0.6, $horData, $limit);
      //echo $i.'-'.$p.' ';
      if ($p >= 0) {
//                    $i = $i + 3;
//                    echo "<font style='font-size:22px;'>".$p."</font>";
        $arr[$i] = $p;
      }
    }
    unset($this->gjphone);
    $phone = "1" . implode('', $arr);
//            echo "第".$k."次扫描得到的号码为：".$phone."<br>";
//            echo "<br /><img src='/".$imgPath."' /><br /><br />";
    return $phone;
  }

  /**
   * 采集58同城网列表商铺出租页
   * 2016.01.27 ccy
   */
  public function rent_wuba_shops_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/shangpucz/0/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/shangpucz/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<td class="t">.*href="(http\:\/\/' . $realcity[$city] . '\.58\.com\/shangpu\/.*\.shtml).*" target="_blank".*<\/a>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = $val;
        $lists['type'] = 7;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-商铺出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网列表商铺出售页
   * 2016.01.29 ccy
   */
  public function sell_wuba_shops_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/shangpucs/0/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/shangpucs/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<td class="t">.*href="(http\:\/\/' . $realcity[$city] . '\.58\.com\/shangpu\/.*\.shtml).*" target="_blank".*<\/a>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = $val;
        $lists['type'] = 8;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-商铺出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网列表写字楼出租页
   * 2016.01.29 ccy
   */
  public function rent_wuba_office_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/zhaozu/0/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/zhaozu/0/pn" . $num . "/pve_1092_0/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<td class="t">.*href="(http\:\/\/' . $realcity[$city] . '\.58\.com\/zhaozu\/.*\.shtml).*" target="_blank".*<\/a>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = $val;
        $lists['type'] = 11;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-写字楼出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网列表写字楼出售页
   */
  public function sell_wuba_office_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $page = 5;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".58.com/zhaozu/0/pve_1092_2/";
      } else {
        $url = "http://" . $realcity[$city] . ".58.com/zhaozu/0/pn" . $num . "/pve_1092_2/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<td class="t">.*href="(http\:\/\/' . $realcity[$city] . '\.58\.com\/zhaozu\/.*\.shtml).*" target="_blank".*<\/a>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = $val;
        $lists['type'] = 12;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("58-写字楼出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网商铺出租列表页
   * 2016.1.29 ccy
   */
  public function rent_ganji_shops_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang6/a1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang6/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<\/span><a onclick=\'event.cancelBubble=true;\' href=\'\/fang6.(.*)\' target=\'_blank\'/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com/fang6/" . $val;
        $lists['type'] = 5;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-商铺出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网商铺出售列表页
   * 2016.1.29 ccy
   */
  public function sell_ganji_shops_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang7/a1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang7/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<a class=\'list-info-title\' target=\'_blank\' href=\'\/fang7.(.*)\' id/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com/fang7/" . $val;
        $lists['type'] = 6;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-商铺出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网写字楼出租列表页
   * 2016.1.29 ccy
   */
  public function rent_ganji_office_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang8/a1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang8/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<\/span><a onclick=\'event.cancelBubble=true;\' href=\'\/fang8.(.*)\' target=\'_blank\'/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com/fang8/" . $val;
        $lists['type'] = 9;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-写字楼出租列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网写字楼出售列表页
   * 2016.1.29 ccy
   */
  public function sell_ganji_office_lists()
  {
    $city = $this->input->get('city', true);
    $realcity = array('nj' => 'nj', 'hf' => 'hf', 'sz' => 'su', 'wx' => 'wx', 'hz' => 'hz', 'km' => 'km', 'xa' => 'xa', 'lz' => 'lz', 'cq' => 'cq', 'cd' => 'cd', 'hrb' => 'hrb');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 5;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang9/a1/";
      } else {
        $url = "http://" . $realcity[$city] . ".ganji.com/fang9/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<a class=\'list-info-title\' target=\'_blank\' href=\'\/fang9.(.*)\' id/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://" . $realcity[$city] . ".ganji.com/fang9/" . $val;
        $lists['type'] = 10;
        $check = $this->autocollect_model->check_collect_lists_byurl($lists, $database = 'db_city');
        if (empty($check)) {
          $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
          if ($res !== 0) {
            $i++;
          }
        }
      }
    }
    $this->na->post_job_notice("赶集-写字楼出售列表-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网商铺出租
   * 2016.2.1 ccy
   */
  public function rent_ganji_shops()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $where = array('type' => 5);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        // $del = array('url' => $value['url']);
        // $result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $data['oldurl'] = $val;
        //联系人
        preg_match('/在线联系：(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/联系方式：(.*)<\/em>/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));

        //采集时间
        $data['createtime'] = time();

        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //发帖详情过滤经纪人
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集商铺出租发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);

        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5)//发帖次数大于5
          {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
            }
            //print_r($messages);exit;
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
              $type_sec_hand = $length_time > 2592000 ? 1 : 0;
            }
            if (count($rent_house_number) > 5) {//出租发帖记录
              $length_time = $rent_house_number[0] - $rent_house_number[4];
              $type_rent_house = $length_time > 2592000 ? 1 : 0;
            }
            if (count($share_house_number) > 5) {//合租发帖记录
              $length_time = $share_house_number[0] - $share_house_number[4];
              $type_share_house = $length_time > 2592000 ? 1 : 0;
            }
            if (count($rent_office_number) > 5) {//写字楼出租发帖记录
              $length_time = $rent_office_number[0] - $rent_office_number[4];
              $type_rent_office = $length_time > 2592000 ? 1 : 0;
            }
            if (count($sell_office_number) > 5) {//写字楼出售发帖记录
              $length_time = $sell_office_number[0] - $sell_office_number[4];
              $type_sell_office = $length_time > 2592000 ? 1 : 0;
            }
            if (count($rent_shops_number) > 5) {//商铺出租发帖记录
              $length_time = $rent_shops_number[0] - $rent_shops_number[4];
              $type_rent_shops = $length_time > 2592000 ? 1 : 0;
            }
            if (count($sell_shops_number) > 5) {//商铺出售发帖记录
              $length_time = $sell_shops_number[0] - $sell_shops_number[4];
              $type_sell_shops = $length_time > 2592000 ? 1 : 0;
            }
            if ($type_sec_hand == 1 || $type_rent_house == 1 || $type_share_house == 1 || $type_rent_office == 1 || $type_sell_office == 1 || $type_rent_shops == 1 || $type_sell_shops == 1) {
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }
        }
        //商铺照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<img alt=".*" title=\'查看大图\' src="(.*)" \/>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        //区属、板块
        preg_match('/所在区域：<\/span>(.*)<\/li>/siU', $con, $district);
        $district = $this->autocollect_model->con_replace(strip_tags($district[1]));
        if (!empty($district)) {
          $districts = explode('-', $district);
          if (count($districts) == 3) {
            $data['district'] = $districts[1];
            $blocks = explode('(', $districts[2]);
            $data['block'] = $blocks[0];
          } elseif (count($districts) == 2) {
            $dist = explode('(', $districts[1]);
            $data['district'] = $dist[0];
            $data['block'] = "暂无资料";
          }
        } else {
          $data['district'] = "暂无资料";
          $data['block'] = "暂无资料";
        }
        //楼盘名称
        preg_match('/商铺名称：(.*)<\/li>/siU', $con, $building);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
        //商铺地址
        preg_match('/商铺地址：(.*)<\/li>/siU', $con, $address);
        if (!empty($address)) {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          $data['house_addr'] = "暂无资料";
        }
        //租金
        preg_match('/租金价格：(.*)<\/b>/siU', $con, $rent_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags($rent_price[1]));
        //有面议设置为0
        $data['price'] = $total_prices ? $total_prices : "0";
        //print_r($data['price']);exit;
        //面积
        preg_match('/商铺面积：(.*)㎡/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($acreage[1]));
        //商铺类型
        preg_match('/商铺类型：(.*)<\/li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));
        //适合经营
        preg_match('/适合经营.*<\/dl>/siU', $con, $suits);
        if (!empty($suits)) {
          preg_match_all('/<em class=ico-equip-has><\/em>(.*)<\/span>/siU', $suits[0], $suit_manage);
          $data['suit_manage'] = implode("*", $suit_manage[1]);
        } else {
          $data['suit_manage'] = "暂无资料";
        }

        //判断该条商铺是否是经纪人所发商铺（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //商铺还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 3,
              'price' => $data['price'],
              'suit_manage' => $data['suit_manage'],
              'shop_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此商铺号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此商铺为中介商铺：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-商铺出租详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网商铺出售
   * 2016.2.1 ccy
   */
  public function sell_ganji_shops()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $where = array('type' => 6);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $data['oldurl'] = $val;
        //联系人
        preg_match('/在线联系：(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/联系方式：(.*)<\/em>/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //采集时间
        $data['createtime'] = time();
        //发帖详情过滤经纪人
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集商铺出售发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }

        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
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
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }

        }
        //商铺照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<img alt=".*" title=\'查看大图\' src="(.*)" \/>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        //区属、板块
        preg_match('/所在区域：<\/span>(.*)<\/li>/siU', $con, $district);
        $district = $this->autocollect_model->con_replace(strip_tags($district[1]));
        if (!empty($district)) {
          $districts = explode('-', $district);
          if (count($districts) == 3) {
            $data['district'] = $districts[1];
            $blocks = explode('(', $districts[2]);
            $data['block'] = $blocks[0];
          } elseif (count($districts) == 2) {
            $dist = explode('(', $districts[1]);
            $data['district'] = $dist[0];
            $data['block'] = "暂无资料";
          }
        } else {
          $data['district'] = "暂无资料";
          $data['block'] = "暂无资料";
        }
        //楼盘名称
        preg_match('/商铺名称：(.*)<\/li>/siU', $con, $building);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
        //商铺地址
        preg_match('/商铺地址：(.*)<\/li>/siU', $con, $address);
        if (!empty($address)) {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          $data['house_addr'] = "暂无资料";
        }

        //售价
        preg_match('/商铺售价：(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags($total_price[1]));

        //有面议设置为0
        $data['price'] = $total_prices ? $total_prices : "0";
        //面积
        preg_match('/商铺面积：(.*)㎡/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($acreage[1]));
        //商铺类型
        preg_match('/商铺类型：(.*)<\/li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));
        //适合经营
        preg_match('/适合经营.*<\/dl>/siU', $con, $suits);
        if (!empty($suits)) {
          preg_match_all('/<em class=ico-equip-has><\/em>(.*)<\/span>/siU', $suits[0], $suit_manage);
          $data['suit_manage'] = implode("*", $suit_manage[1]);
        } else {
          $data['suit_manage'] = "暂无资料";
        }
        //判断该条商铺是否是经纪人所发商铺（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //商铺还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'sell_type' => 3,
              'price' => $data['price'],
              'suit_manage' => $data['siut_manage'],
              'shop_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此商铺号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此商铺为中介商铺：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-商铺出售详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网写字楼出租
   * 2016.2.2 ccy
   */
  public function rent_ganji_office()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $where = array('type' => 9);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $data['oldurl'] = $val;
        //联系人
        preg_match('/在<i class="letter-space-5"><\/i>线<i class="letter-space-5"><\/i>联<i class="letter-space-5"><\/i>系：(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/联<i class="letter-space-5"><\/i>系<i class="letter-space-5"><\/i>方<i class="letter-space-5"><\/i>式：(.*)<\/em>/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags($tel[1]));
        //采集时间
        $data['createtime'] = time();
        //写字楼标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //发帖详情过滤经纪人
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集写字楼出租发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);

        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;

          if ($postnum > 5)//发帖次数大于5
          {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
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
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }
        }
        //写字楼照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<img alt=".*" title=\'查看大图\' src="(.*)" \/>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        //区属、板块
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>区<i class="letter-space-5"><\/i>域：(.*)<\/li>/siU', $con, $district);
        $district = $this->autocollect_model->con_replace(strip_tags($district[1]));
        if (!empty($district)) {
          $districts = explode('-', $district);
          if (count($districts) == 3) {
            $data['district'] = $districts[1];
            $blocks = explode('(', $districts[2]);
            $data['block'] = $blocks[0];
          } elseif (count($districts) == 2) {
            $dist = explode('(', $districts[1]);
            $data['district'] = $dist[0];
            $data['block'] = "暂无资料";
          }
        } else {
          $data['district'] = "暂无资料";
          $data['block'] = "暂无资料";
        }

        //楼盘名称
        preg_match('/<span class="fc-gray">写字楼名称：(.*)<\/li>/siU', $con, $building);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
        //写字楼地址
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>地<i class="letter-space-5"><\/i>址：(.*)<\/li>/siU', $con, $address);
        if (!empty($address)) {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          $data['house_addr'] = "暂无资料";
        }
        //面积
        preg_match('/写字楼面积：(.*)㎡/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($acreage[1]));

        //租金
        preg_match('/租<i class="letter-space-5"><\/i>金<i class="letter-space-5"><\/i>价<i class="letter-space-5"><\/i>格：.*<b class="basic-info-price">(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags($total_price[1]));
        if ($total_price == '面议') {
          $total_prices = $total_price;
        } else {
          preg_match('/租<i class="letter-space-5"><\/i>金<i class="letter-space-5"><\/i>价<i class="letter-space-5"><\/i>格：.*（合计(.*)元\/月/siU', $con, $rent_type);
          $total_prices = $rent_type[1];
        }
        //有面议设置为0
        $data['price'] = $total_prices ? $total_prices : "0";

        //写字楼类型
        preg_match('/写字楼类型：(.*)<\/li>/siU', $con, $office_type);
        $data['office_type'] = $this->autocollect_model->con_replace(strip_tags($office_type[1]));

        //判断该条商铺是否是经纪人所发商铺（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //商铺还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 4,
              'price' => $data['price'],
              'office_type' => $data['office_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此写字楼号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此写字楼为中介写字楼：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-写字楼出租详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集赶集网写字楼出售
   * 2016.2.3 ccy
   */
  public function sell_ganji_office()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $where = array('type' => 10);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        // print_r($con);exit;
        $data = array();
        $data['oldurl'] = $val;
        //联系人
        preg_match('/在<i class="letter-space-5"><\/i>线<i class="letter-space-5"><\/i>联<i class="letter-space-5"><\/i>系：(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/联<i class="letter-space-5"><\/i>系<i class="letter-space-5"><\/i>方<i class="letter-space-5"><\/i>式：(.*)<\/em>/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags($tel[1]));
        //采集时间
        $data['createtime'] = time();

        //写字楼标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //发帖详情过滤经纪人
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '赶集写字楼出售发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过5条的判定为中介发帖
        preg_match('/<span class="ftjl">.*href="(.*)" target="_blank" title="查看该发帖纪录"/siU', $con, $urlarr);
        //print_r($urlarr);exit;
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/房产<\/span>类别发布了<span class="f_c_red">([\d]*)<\/span>条信息/siU', $cpcon, $numarr);

          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5)//发帖次数大于5
          {
            preg_match_all('/查看该号码发布的帖子>><\/a>(.*)<\/ul>/siU', $cpcon, $messages);
            $messages = explode('<li>', $messages[1][0]);
            foreach ($messages as $k => $v) {
              $messages[$k] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($v));
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
              //print_r($gj_type);echo "<hr>";
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
              //判断该条房源经纪人黑名单
              $cond = array('tel' => $data['telno1']);
              $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
              if ($check_result) {
                echo "<h3>该房源经纪人已在黑名单</h3>";
              } else {
                $broker_black = array(
                  'username' => $data['owner'],
                  'tel' => $data['telno1'],
                  'store' => '赶集发帖记录',
                  'addtime' => $data['createtime'],
                  'type' => 1
                );
                $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
              }
              //是中介房源,请勿入库
              echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
              continue;
            }
          }
        }
        //写字楼照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<img alt=".*" title=\'查看大图\' src="(.*)" \/>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }

        //区属、板块
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>区<i class="letter-space-5"><\/i>域：(.*)<\/li>/siU', $con, $district);
        $district = $this->autocollect_model->con_replace(strip_tags($district[1]));
        if (!empty($district)) {
          $districts = explode('-', $district);
          if (count($districts) == 3) {
            $data['district'] = $districts[1];
            $blocks = explode('(', $districts[2]);
            $data['block'] = $blocks[0];
          } elseif (count($districts) == 2) {
            $dist = explode('(', $districts[1]);
            $data['district'] = $dist[0];
            $data['block'] = "暂无资料";
          }
        } else {
          $data['district'] = "暂无资料";
          $data['block'] = "暂无资料";
        }

        //楼盘名称
        preg_match('/<span class="fc-gray">写字楼名称：(.*)<\/li>/siU', $con, $building);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
        //写字楼地址
        preg_match('/所<i class="letter-space-5"><\/i>在<i class="letter-space-5"><\/i>地<i class="letter-space-5"><\/i>址：(.*)<\/li>/siU', $con, $address);
        if (!empty($address)) {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          $data['house_addr'] = "暂无资料";
        }
        //写字楼售价
        preg_match('/写字楼售价：<\/span>(.*)<\/b>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags($total_price[1]));
        //有面议设置为0
        $data['price'] = $total_prices ? $total_prices : "0";
        //面积
        preg_match('/写字楼面积：(.*)㎡/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($acreage[1]));
        //写字楼类型
        preg_match('/写字楼类型：(.*)<\/li>/siU', $con, $office_type);
        $data['office_type'] = $this->autocollect_model->con_replace(strip_tags($office_type[1]));

        //判断该条商铺是否是经纪人所发商铺（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            //商铺还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'sell_type' => 4,
              'price' => $data['price'],
              'office_type' => $data['office_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_gj'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此写字楼号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此写字楼为中介写字楼：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("赶集-写字楼出售详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网商铺出租
   * ccy 2016.2.15
   */
  public function rent_wuba_shops()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 7);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》商铺信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      //删除链接队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');

      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集商铺详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $housephoto = '';

        $data['oldurl'] = $val;
        //商铺标题
        preg_match('/<h1 style="font-size:22px;">（出租）(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //采集电话、联系方式
        preg_match('/http:\/\/(.*).58.com\/shangpu\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/shangpu/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        if (empty($data['telno1'])) {
          continue;
        }
        //采集时间
        $data['createtime'] = time();
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $data['owner'] = $contact[1];
        } else {
          $data['owner'] = "个人";
        }
        //58商铺出租发帖详情，过滤经纪人
        preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58商铺出租发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }

        //商铺照片
        preg_match('/var img_list(.*)<\/script>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/http(.*)jpg/siU', $cons[1], $photo);
          if (!empty($photo[0])) {
            $data['picurl'] = implode("*", $photo[0]);
          } else {
            $data['picurl'] = "暂无资料";
          }
        } else {
          $data['picurl'] = "暂无资料";
        }

        //商铺地址
        preg_match('/<li>地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/ <li>区域：(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        if (count($mess) == 2) {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
        } else {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $blocks = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //板块
        }
        if (empty($district)) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $district;
        }
        if (empty($blocks)) {
          $data['block'] = "暂无资料";
        } else {
          $data['block'] = $blocks;
        }
        //商铺面积
        preg_match('/<li>面积：(.*)㎡/siU', $con, $buildarea);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/<li>类型：(.*)<li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));//类型
        //租金
        preg_match('/<em class="redfont">(.*)<\/em>/siU', $con, $total_price);
        $rent_price = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        if (strstr($rent_price, "-")) {//范围价格过滤
          continue;
        }
        if ($rent_price == '面议') {
          $data['rent_price'] = $rent_price;
        } else {
          preg_match('/<em class="redfont">.*<\/em>(.*)<a href/siU', $con, $rent_type);
          $rent_price_type = $this->autocollect_model->con_replace(strip_tags($rent_type[1]));
          switch ($rent_price_type) {
            case '元/㎡/天':
              $data['rent_price'] = $rent_price * $data['buildarea'] * 30;
              break;
            case '元/月':
              $data['rent_price'] = $rent_price;
              break;
          }
        }
        //判断该条商铺是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 3,
              'shop_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'price' => $data['rent_price'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-商铺出租详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网商铺出售
   * ccy 2016.2.15
   */
  public function sell_wuba_shops()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 8);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》商铺信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      //删除链接队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');

      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集商铺详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $housephoto = '';

        $data['oldurl'] = $val;
        //采集电话、联系方式
        preg_match('/http:\/\/(.*).58.com\/shangpu\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/shangpu/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }

        //采集时间
        $data['createtime'] = time();
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $data['owner'] = $contact[1];
        } else {
          $data['owner'] = "个人";
        }
        //采集发帖，过滤经纪人
        preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58商铺出售发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //商铺标题
        preg_match('/<h1 style="font-size:22px;">（出售）(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }
        //商铺照片
        preg_match('/var img_list(.*)<\/script>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/http(.*)jpg/siU', $cons[1], $photo);
          if (!empty($photo[0])) {
            $data['picurl'] = implode("*", $photo[0]);
          } else {
            $data['picurl'] = "暂无资料";
          }
        } else {
          $data['picurl'] = "暂无资料";
        }

        //商铺地址
        preg_match('/<li>地址：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/ <li>区域：(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        if (count($mess) == 2) {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
        } else {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $blocks = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //板块
        }
        if (empty($district)) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $district;
        }
        if (empty($blocks)) {
          $data['block'] = "暂无资料";
        } else {
          $data['block'] = $blocks;
        }

        //商铺面积
        preg_match('/<li>面积：(.*)㎡/siU', $con, $buildarea);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($buildarea[1]));//面积
        //商铺类型
        preg_match('/<li>类型：(.*)<li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));//类型
        //售价
        preg_match('/售价：<em class="redfont">(.*)<\/em>/siU', $con, $total_price);
        $data['sell_price'] = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //判断该条商铺是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $housephoto);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_addr' => $data['house_addr'],
              'sell_type' => 3,
              'shop_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'price' => $data['sell_price'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-商铺出售详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网写字楼出租
   * ccy 2016.2.15
   */
  public function rent_wuba_office()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 11);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》写字楼信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      //删除链接队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集写字楼详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        // print_r($con);exit;
        $data = array();
        $housephoto = '';

        $data['oldurl'] = $val;
        //采集电话、联系方式
        preg_match('/http:\/\/(.*).58.com\/zhaozu\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/zhaozu/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }
        //采集时间
        $data['createtime'] = time();
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $data['owner'] = $contact[1];
        } else {
          $data['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="w headline">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //58写字楼出租发帖详情，过滤经纪人
        preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58写字楼出租发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/<span class="f12"><a target="_blank" href="(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpcon = $this->autocollect_model->vcurl($urlarr[1], $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }

        //商铺照片
        preg_match('/var img_list(.*)<\/script>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/http(.*)jpg/siU', $cons[1], $photo);
          if (!empty($photo[0])) {
            $data['picurl'] = implode("*", $photo[0]);
          } else {
            $data['picurl'] = "暂无资料";
          }
        } else {
          $data['picurl'] = "暂无资料";
        }

        //楼盘
        preg_match('/<li><i>楼盘：<\/i>(.*)<\/li>/siU', $con, $house_name);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags(@$house_name[1]));
        //写字楼地址
        preg_match('/<li><i>地段：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }
        //区属、板块
        preg_match('/ <li><i>区域：<\/i>(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        if (count($mess) == 2) {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
        } else {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $blocks = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //板块
        }
        if (empty($district)) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $district;
        }
        if (empty($blocks)) {
          $data['block'] = "暂无资料";
        } else {
          $data['block'] = $blocks;
        }

        //写字楼面积
        preg_match('/<li><i>面积：<\/i>(.*)㎡/siU', $con, $buildarea);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($buildarea[1]));//面积
        if (strstr($data['buildarea'], "-")) {//范围面积过滤
          continue;
        }
        //写字楼类型
        preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));//类型
        //租金
        preg_match('/<li><i>价格：<\/i>(.*)<\/em>/siU', $con, $total_price);
        $rent_price = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        if (strstr($rent_price, "-")) {//范围价格过滤
          continue;
        }
        if ($rent_price == '面议') {
          $data['rent_price'] = $rent_price;
        } else {
          preg_match('/<li><i>价格：<\/i>.*<\/em>(.*)<a href/siU', $con, $rent_type);
          $rent_price_type = $this->autocollect_model->con_replace(strip_tags($rent_type[1]));
          switch ($rent_price_type) {
            case '元/㎡/月':
              $data['rent_price'] = $rent_price * $data['buildarea'];
              break;
            case '元/㎡/天':
              $data['rent_price'] = $rent_price * $data['buildarea'] * 30;
              break;
            case '元/月':
              $data['rent_price'] = $rent_price;
              break;
          }
        }
        //判断该条写字楼是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_addr' => $data['house_addr'],
              'house_name' => $data['house_name'],
              'rent_type' => 4,
              'office_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'price' => $data['rent_price'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'rent_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-写字楼出租详情-" . $i, $city);
    echo 'over';
  }

  /**
   * 采集58同城网写字楼出售
   * ccy 2016.2.16
   */
  public function sell_wuba_office()
  {
    $city = $this->input->get('city', true);
    $area = array('nj' => '南京', 'hf' => '合肥', 'sz' => '苏州', 'wx' => '无锡', 'hz' => '杭州', 'km' => '昆明', 'xa' => '西安', 'lz' => '兰州', 'cq' => '重庆', 'cd' => '成都', 'hrb' => '哈尔滨');
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $where = array('type' => 12);
    $result = $this->autocollect_model->check_collect_house_lists($limit, $where, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》写字楼信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      //删除链接队列
      $del = array('url' => $value['url']);
      $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');

      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        //$del = array('url' => $value['url']);
        //$result = $this->autocollect_model->del_collect_house_lists($del,$database='db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集写字楼详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        //print_r($con);exit;
        $data = array();
        $housephoto = '';

        $data['oldurl'] = $val;
        //采集电话、联系方式
        preg_match('/http:\/\/(.*).58.com\/zhaozu\/(.*).shtml/siU', $val, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 3) {
          $cpurl = "http://m.58.com/" . $urlarr[1] . "/zhaozu/" . $urlarr[2] . ".shtml";
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
          preg_match_all('/<span id="number" >(.*)<\/span>/siU', $cpcon, $photoarr);
          $data['telno1'] = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
        }

        //采集时间
        $data['createtime'] = time();
        //联系人
        preg_match('/username:\'(.*)\',/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $data['owner'] = $contact[1];
        } else {
          $data['owner'] = "个人";
        }
        //写字楼标题
        preg_match('/<div class="w headline">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //58写字楼出售发帖详情，过滤经纪人
        preg_match('/agencyname(.*)ageyear/siU', $con, $contacts);
        if (strstr($contacts[1], "个人")) {
        } else {
          $cond = array('tel' => $data['telno1']);
          $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
          if ($check_result) {
            echo "<h3>该房源经纪人已在黑名单</h3>";
          } else {
            $broker_black = array(
              'username' => $data['owner'],
              'tel' => $data['telno1'],
              'store' => '58写字楼出租发帖详情',
              'addtime' => $data['createtime'],
              'type' => 1
            );
            $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
          }
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
        //采集发帖记录，每月发帖量超过10条的判定为中介发帖
        preg_match('/query=(.*)"/siU', $con, $urlarr);
        if (is_array($urlarr) && count($urlarr) == 2) {
          $cpurl = "http://my.58.com/mobileposthistory/?hidemobile=1&query=" . $urlarr[1];
          $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集发帖次数
          preg_match_all('/频道发布了 <span>([\d]*)<\/span> 条信息/siU', $cpcon, $numarr);
          $postnum = is_array($numarr) && count($numarr) == 2 ? intval($numarr[1][0]) : 1;
          if ($postnum > 5) {
            //判断该条房源经纪人黑名单
            $cond = array('tel' => $data['telno1']);
            $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
            if ($check_result) {
              echo "<h3>该房源经纪人已在黑名单</h3>";
            } else {
              $broker_black = array(
                'username' => $data['owner'],
                'tel' => $data['telno1'],
                'store' => '58发帖记录',
                'addtime' => $data['createtime'],
                'type' => 1
              );
              $this->autocollect_model->add_apent_broker($broker_black, $database = 'db_city');//加入黑名单
            }
            //是中介房源,请勿入库
            echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        }

        //商铺照片
        preg_match('/var img_list(.*)<\/script>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/http(.*)jpg/siU', $cons[1], $photo);
          if (!empty($photo[0])) {
            $data['picurl'] = implode("*", $photo[0]);
          } else {
            $data['picurl'] = "暂无资料";
          }
        } else {
          $data['picurl'] = "暂无资料";
        }

        //楼盘
        preg_match('/<li><i>楼盘：<\/i>(.*)<\/li>/siU', $con, $house_name);
        $data['house_name'] = $this->autocollect_model->con_replace(strip_tags(@$house_name[1]));
        //写字楼地址
        preg_match('/<li><i>地段：(.*)<\/li>/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }

        //区属、板块
        preg_match('/ <li><i>区域：<\/i>(.*)<\/li>/siU', $con, $mess);
        $mess = explode('</a>', $mess[1]);
        if (count($mess) == 2) {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
        } else {
          $district = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[0]));  //区属
          $blocks = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($mess[1]));  //板块
        }
        if (empty($district)) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $district;
        }
        if (empty($blocks)) {
          $data['block'] = "暂无资料";
        } else {
          $data['block'] = $blocks;
        }
        //写字楼面积
        preg_match('/<li><i>面积：<\/i>(.*)㎡/siU', $con, $buildarea);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($buildarea[1]));//面积
        //写字楼类型
        preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $shop_type);
        $data['shop_type'] = $this->autocollect_model->con_replace(strip_tags($shop_type[1]));//类型
        //售价
        preg_match('/<li><i>价格：<\/i>(.*)<\/em>/siU', $con, $total_price);
        $data['sell_price'] = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //判断该条写字楼是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            $i++;
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_addr' => $data['house_addr'],
              'house_name' => $data['house_name'],
              'sell_type' => 4,
              'office_type' => $data['shop_type'],
              'buildarea' => $data['buildarea'],
              'oldurl' => $data['oldurl'],
              'owner' => $data['owner'],
              'price' => $data['sell_price'],
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'picurl' => '暂无资料',
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            if (isset($rel) && $rel != '' && $data['picurl'] != '暂无资料') {
              $picture = array(
                'picid' => $rel,
                'picurl' => $data['picurl'],
                'pictype' => 'sell_58'
              );
              $rel = $this->autocollect_model->add_picture_control($picture, $database = 'db_city');
            }
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          continue;
        }
      }
    }
    $this->na->post_job_notice("58-写字楼出租详情-" . $i, $city);
    echo 'over';
  }

  //58写字楼出售刷数据
  function refresh_wuba_sell_office()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'sell_type', 'oldurl', 'createtime', 'source_from', 'office_type', 'shop_type');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'sell_type =4 and source_from =1';
      $office_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $shop_type = $office_info[0]['shop_type'];
        $office_type = $office_info[0]['office_type'];
        $createtime = $office_info[0]['createtime'];
        if (empty($shop_type) && !empty($office_type)) {

        } else {
          if (!empty($shop_type) && empty($office_type)) {
            $office_type = $shop_type;
            $shop_type = '';
          } elseif (empty($shop_type) && empty($office_type)) {
            $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
            preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_types);
            $office_type = $this->autocollect_model->con_replace(strip_tags($office_types[1]));//写字楼类型
            $shop_type = '';
          }
          $data = array('office_type' => $office_type, 'shop_type' => $shop_type);
          $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          echo $mess[1] . $createtime;
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_sell_office/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =4 and source_from =1 and id<' . $id;
      $office_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $shop_type = $office_info[0]['shop_type'];
        $office_type = $office_info[0]['office_type'];
        $createtime = $office_info[0]['createtime'];
        if (empty($shop_type) && !empty($office_type)) {

        } else {
          if (!empty($shop_type) && empty($office_type)) {
            $office_type = $shop_type;
            $shop_type = '';
          } elseif (empty($shop_type) && empty($office_type)) {
            $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
            preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_types);
            $office_type = $this->autocollect_model->con_replace(strip_tags($office_types[1]));//写字楼类型
            $shop_type = '';
          }
          $data = array('office_type' => $office_type, 'shop_type' => $shop_type);
          $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          echo $mess[1] . $createtime;
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_sell_office/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //gj写字楼出售刷数据
  function refresh_ganji_sell_office()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'sell_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'sell_type =4 and source_from =0';
      $office_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $office_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_sell_office/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =4 and source_from =0 and id<' . $id;
      $office_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $office_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_sell_office/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //58商铺出租售价刷数据
  function refresh_wuba_sell_shop()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'sell_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'sell_type =3 and source_from =1';
      $shop_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/agencyname(.*)ageyear/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_sell_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =3 and source_from =1 and id<' . $id;
      $shop_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/agencyname(.*)ageyear/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_sell_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //ganji商铺出租售价刷数据
  function refresh_ganji_sell_shop()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'sell_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'sell_type =3 and source_from =0';
      $shop_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_sell_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =3 and source_from =0 and id<' . $id;
      $shop_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_sell_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //58商铺出租刷数据
  function refresh_wuba_rent_shop()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =3 and source_from =1';
      $shop_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/agencyname(.*)ageyear/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_rent_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where_first = 'rent_type =3 and source_from =1 and id <' . $id;
      $shop_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/agencyname(.*)ageyear/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_rent_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //gj商铺出租刷数据
  function refresh_ganji_rent_shop()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =3 and source_from =0';
      $shop_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_rent_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where_first = 'rent_type =3 and source_from =0 and id <' . $id;
      $shop_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($shop_info) {
        $now_id = $shop_info[0]['id'];
        $url = $shop_info[0]['oldurl'];
        $createtime = $shop_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_rent_shop/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //58写字楼出租刷数据
  function refresh_wuba_rent_office()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from', 'office_type', 'shop_type');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =4 and source_from =1';
      $office_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $shop_type = $office_info[0]['shop_type'];
        $office_type = $office_info[0]['office_type'];
        $createtime = $office_info[0]['createtime'];
        if (empty($shop_type) && !empty($office_type)) {

        } else {
          if (!empty($shop_type) && empty($office_type)) {
            $office_type = $shop_type;
            $shop_type = '';
          } elseif (empty($shop_type) && empty($office_type)) {
            $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
            preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_types);
            $office_type = $this->autocollect_model->con_replace(strip_tags($office_types[1]));//写字楼类型
            $shop_type = '';
          }
          $data = array('office_type' => $office_type, 'shop_type' => $shop_type);
          $this->autocollect_model->update_rent_info_byid($now_id, $data, $database = 'db_city');
          echo $office_type . $createtime;
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_rent_office/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'rent_type =4 and source_from =1 and id<' . $id;
      $office_info = $this->autocollect_model->select_rent_info($where, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $shop_type = $office_info[0]['shop_type'];
        $office_type = $office_info[0]['office_type'];
        $createtime = $office_info[0]['createtime'];
        if (empty($shop_type) && !empty($office_type)) {

        } else {
          if (!empty($shop_type) && empty($office_type)) {
            $office_type = $shop_type;
            $shop_type = '';
          } elseif (empty($shop_type) && empty($office_type)) {
            $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
            preg_match('/<li><i>类别：<\/i>(.*)<li>/siU', $con, $office_types);
            $office_type = $this->autocollect_model->con_replace(strip_tags($office_types[1]));//写字楼类型
            $shop_type = '';
          }
          $data = array('office_type' => $office_type, 'shop_type' => $shop_type);
          $this->autocollect_model->update_rent_info_byid($now_id, $data, $database = 'db_city');
          echo $office_type . $createtime;
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_rent_office/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //gj写字楼出租刷数据
  function refresh_ganji_rent_office()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =4 and source_from =0';
      $office_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $office_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_rent_office/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where_first = 'rent_type =4 and source_from =0 and id <' . $id;
      $office_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $office_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<!--个人信息 start-->(.*)<\/legend>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:s", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_rent_office/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //58二手房出售 电话号码 刷数据 住宅
  function refresh_wuba_sell_house()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $compress = 'gzip';
    $select = array('id', 'sell_type', 'telno1', 'oldurl', 'source_from');
    if (empty($id)) {
      $where_first = 'sell_type =1 and source_from =1';
      $info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($info) {
        $now_id = $info[0]['id'];
        $url = $info[0]['oldurl'];
        $telno1 = $info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 3) {
            $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<li class="yellow">([\d]{11})<\/li>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_sell_house/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =1 and source_from =1 and id <' . $id;
      $info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($info) {
        $now_id = $info[0]['id'];
        $url = $info[0]['oldurl'];
        $telno1 = $info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 3) {
            $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<li class="yellow">([\d]{11})<\/li>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_sell_house/?id=" . $now_id . "&city=" . $city . "';</script>";
        }
      } else {
        echo 'over';
      }
    }
  }

  //58二手房出售 电话号码 刷数据 住宅
  function refresh_wuba_sell_house1()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $compress = 'gzip';
    $select = array('id', 'sell_type', 'telno1', 'oldurl', 'source_from');
    if (empty($id)) {
      $where_first = 'sell_type =2 and source_from =1';
      $info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($info) {
        $now_id = $info[0]['id'];
        $url = $info[0]['oldurl'];
        $telno1 = $info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 3) {
            $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<li class="yellow">([\d]{11})<\/li>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        echo "<script>window.location.href='/autocollect/refresh_wuba_sell_house1/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =2 and source_from =1 and id <' . $id;
      $info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($info) {
        $now_id = $info[0]['id'];
        $url = $info[0]['oldurl'];
        $telno1 = $info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).58.com\/ershoufang\/(.*).shtml/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 3) {
            $cpurl = "http://m.58.com/" . $urlarr[1] . "/ershoufang/" . $urlarr[2] . ".shtml";
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<li class="yellow">([\d]{11})<\/li>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_sell_house1/?id=" . $now_id . "&city=" . $city . "';</script>";
        }
      } else {
        echo 'over';
      }
    }
  }

  function refresh_wuba_rent_house()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =1 and source_from =1';
      $house_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($house_info) {
        $now_id = $house_info[0]['id'];
        $url = $house_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<span class="fl pr20 c2e">联系：<\/span>(.*)<\/span>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($createtime < 1457758800) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_rent_house/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    } else {
      $where_first = 'rent_type =1 and source_from =1 and id <' . $id;
      $house_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($house_info) {
        $now_id = $house_info[0]['id'];
        $url = $house_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<span class="fl pr20 c2e">联系：<\/span>(.*)<\/span>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($createtime < 1457758800) {
          echo "<script>window.location.href='/autocollect/refresh_wuba_rent_house/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  function refresh_ganji_rent_house()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $select = array('id', 'rent_type', 'oldurl', 'createtime', 'source_from');
    $compress = 'gzip';
    if (empty($id)) {
      $where_first = 'rent_type =2 and source_from =0';
      $house_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($house_info) {
        $now_id = $house_info[0]['id'];
        $url = $house_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_rent_house/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where_first = 'rent_type =2 and source_from =0 and id <' . $id;
      $house_info = $this->autocollect_model->select_rent_info($where_first, $select, $database = 'db_city');
      if ($house_info) {
        $now_id = $house_info[0]['id'];
        $url = $house_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_rent_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_rent_house/?id=" . $now_id . "&city=" . $city . "';</script>";
        } else {
          echo 'over';
        }
      } else {
        echo 'over';
      }
    }
  }

  //ganji二手房出售 电话号码 刷数据 住宅
  function refresh_ganji_sell_house()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $compress = 'gzip';
    $select = array('id', 'sell_type', 'telno1', 'oldurl', 'source_from');
    if (empty($id)) {
      $where_first = 'sell_type =2 and source_from =0';
      $office_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $telno1 = $office_info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 4) {
            $cpurl = "http://wap.ganji.com/" . $urlarr[1] . "/" . $urlarr[2] . "/" . $urlarr[3];
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<span>电话联系：<\/span>([\d]{11})<\/p>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_sell_house/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =2 and source_from =0 and id <' . $id;
      $office_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $telno1 = $office_info[0]['telno1'];
        if (empty($telno1)) {
          //采集电话
          preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $url, $urlarr);
          if (is_array($urlarr) && count($urlarr) == 4) {
            $cpurl = "http://wap.ganji.com/" . $urlarr[1] . "/" . $urlarr[2] . "/" . $urlarr[3];
            $cpcon = $this->autocollect_model->vcurl($cpurl, $compress);  #采集电话
            preg_match_all('/<span>电话联系：<\/span>([\d]{11})<\/p>/siU', $cpcon, $photoarr);
            $phone = is_array($photoarr) && count($photoarr) == 2 ? $photoarr[1][0] : '';
          }
          if ($phone) {
            echo $phone . '刷新';
            $data = array('telno1' => $phone);
            $this->autocollect_model->update_sell_info_byid($now_id, $data, $database = 'db_city');
          } else {
            echo '删';
            $del = array('id' => $now_id);
            $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
          }
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_sell_house/?id=" . $now_id . "&city=" . $city . "';</script>";
        }
      } else {
        echo 'over';
      }
    }
  }

  function refresh_ganji_sell_house1()
  {
    $city = $this->input->get('city', true);
    $id = $this->input->get('id', true);
    $compress = 'gzip';
    $select = array('id', 'sell_type', 'oldurl', 'createtime', 'source_from');
    if (empty($id)) {
      $where_first = 'sell_type =2 and source_from =0';
      $office_info = $this->autocollect_model->select_sell_info($where_first, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
        }
        echo "<script>window.location.href='/autocollect/refresh_ganji_sell_house1/?id=" . $now_id . "&city=" . $city . "';</script>";
      } else {
        echo 'over';
      }
    } else {
      $where = 'sell_type =2 and source_from =0 and id <' . $id;
      $office_info = $this->autocollect_model->select_sell_info($where, $select, $database = 'db_city');
      if ($office_info) {
        $now_id = $office_info[0]['id'];
        $url = $office_info[0]['oldurl'];
        $createtime = $house_info[0]['createtime'];
        $con = $this->autocollect_model->vcurl($url, $compress);  #采集房源详情
        preg_match('/<i class="fc-999">(.*)<\/i>/siU', $con, $mess);
        $createtime = date("Y-m-d h:i:sa", $createtime);
        echo $mess[1] . $createtime;
        if (strstr($mess[1], "个人")) {
          echo '不删';
        } else {
          echo '删';
          $del = array('id' => $now_id);
          $result = $this->autocollect_model->del_collect_sell_house_info($del, $database = 'db_city');
        }
        if ($id > $now_id) {
          echo "<script>window.location.href='/autocollect/refresh_ganji_sell_house1/?id=" . $now_id . "&city=" . $city . "';</script>";
        }
      } else {
        echo 'over';
      }
    }
  }

  public function spell()
  {
    $this->load->model('district_base_model');
    $this->load->model('read_model');
    $street = $this->district_base_model->get_street();
    foreach ($street as $key => $val) {
      $name_spell = $this->read_model->encode($val['streetname'], 'all');
      echo $name_spell;
      $this->district_base_model->update_street_by(array('name_spell' => $name_spell), array('id' => $val['id']));
    }
  }

}
/* End of file autocollect_nj.php */
/* Location: ./application/mls_admin/controllers/autocollect_nj.php */
