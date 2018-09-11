<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * autocollect controller CLASS
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
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('autocollect_model');//自动采集控制器类
    //$this->output->enable_profiler(TRUE); //CI激活分析器（调试用）
  }


  /**
   * 采集搜房二手房住宅
   */
  public function sell_fang_house()
  {
    $url = "http://esf.sh.fang.com/house-a025/a21-i389/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;

    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title"><a href="(.*)"  target="_blank" title=".*">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://esf.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title flc".*span>(.*)<\/span>/siU', $con, $houseid);
      $data['house_title'] = $this->autocollect_model->con_replace($houseid[1]);
      //区属
      preg_match('/target="_blank" id="esfshxq_12">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="esfshxq_13">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //楼盘名称
      preg_match('/楼盘名称：<\/span>.*style="font-size: 14px;">(.*)<\/strong>/siU', $con, $building);
      $data['house_name'] = $building[1];
      //楼盘地址
      preg_match('/地　　址：<\/span>(.*)<\/dt>/siU', $con, $address);
      $data['house_addr'] = str_replace("[地图]", " ", strip_tags($address[1]));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/span>(.*)<\/dd>/siU', $con, $type);
      $data['sell_type'] = $type[1];
      //总价
      preg_match('/总　　价：<span class="red20b">(.*)<\/span>/siU', $con, $total_price);
      $data['price'] = $total_price[1];
      //单价
      preg_match('/单　　价：(.*)元\/平方米/siU', $con, $average_price);
      $data['avgprice'] = $average_price[1];
      //朝向
      preg_match('/朝　　向：<\/span>(.*)<\/dd>/siU', $con, $direction);
      @$data['forward'] = @$direction[1] ? @$direction[1] : "暂无资料";
      //户型（室）
      preg_match('/户　　型：<\/span>(.*)室.*<\/dt>/siU', $con, $room);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户　　型：<\/span>.*室(.*)厅.*<\/dt>/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户　　型：<\/span>.*厅(.*)卫<\/dt>/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/建筑面积：<span class="red20b">(.*)<\/span>/siU', $con, $acreage);
      $data['buildarea'] = $acreage[1];
      //楼层（所属层）
      preg_match('/楼　　层：<\/span>第(.*)层/siU', $con, $floor);
      $data['floor'] = $floor[1];
      //楼层（总层数）
      preg_match('/楼　　层：<\/span>第.*层\(共(.*)层\)<\/dd>/siU', $con, $total_floor);
      $data['totalfloor'] = $total_floor[1];
      //装修
      preg_match('/装　　修：<\/span>(.*)<\/dd/siU', $con, $decoration);
      @$data['serverco'] = @$decoration[1] ? @$decoration[1] : "暂无资料";
      //联系人
      preg_match('/<span class="tel">联系人:(.*)手机/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace($contact[1]);
      //联系方式
      preg_match('/机：<strong class="tel14">(.*)<\/strong>/siU', $con, $tel);
      $data['telno1'] = $tel[1];
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
      $cond = array('tel' => $data['telno1']);
      $check_result = $this->autocollect_model->check_agent_tel($cond);
      if (empty($check_result)) {
        //非中介房源,可以入库
        //判断该条房源是否已经采集过了
        $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
        $result = $this->autocollect_model->check_house_only($where);
        if (empty($result)) {
          $i++;
          //房源还未采集入库，可以入库
          $info = array(
            'house_title' => $data['house_title'],
            'district' => $data['district'],
            'block' => $data['block'],
            'house_name' => $data['house_name'],
            'house_addr' => $data['house_addr'],
            'sell_type' => 1,
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
            'owner' => $data['owner'],
            'telno1' => $data['telno1'],
            'createtime' => $data['createtime'],
            'source_from' => '搜房'
          );
          $rel = $this->autocollect_model->add_collect_house($info);
        } else {
          //房源已经入库，请勿重复采集
          continue;
        }
      } else {
        //是中介房源,请勿入库
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }


  /**
   * 采集搜房二手房商铺
   */
  public function sell_fang_shop()
  {
    $url = "http://shop.sh.fang.com/shou/house/a21-i32/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title"><a href="(.*)" target="_blank" title=".*">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://shop.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title".*h1>(.*)<\/h1>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="A3">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="A4">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //商铺名称
      preg_match('/商铺名称：<\/span>(.*)\(.*<a.*target="_blank" id="A3"/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //写字楼地址
      preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
      $data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags($address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/span>(.*)<\/li>/siU', $con, $type);
      $data['sell_type'] = $type[1];
      //总价
      preg_match('/<span class="red20b">(.*)<\/span>/siU', $con, $total_price);
      $data['price'] = $total_price[1];
      //单价
      preg_match('/万元<\/span.*（(.*)元\/平方米/siU', $con, $average_price);
      $data['avgprice'] = $average_price[1];
      //朝向
      preg_match('/朝　　向：<\/span>(.*)<\/dd>/siU', $con, $direction);
      @$data['forward'] = @$direction[1] ? @$direction[1] : 3;
      //户型（室）
      preg_match('/户　　型：<\/span>(.*)室.*<\/dt>/siU', $con, $room);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户　　型：<\/span>.*室(.*)厅.*<\/dt>/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户　　型：<\/span>.*厅(.*)卫<\/dt>/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/建筑面积：(.*)㎡<\/dd>/siU', $con, $acreage);
      $data['buildarea'] = $acreage[1];
      //楼层（所属层）
      preg_match('/楼　　层：<\/span>第(.*)层/siU', $con, $floor);
      @$data['floor'] = @$floor[1] ? @$floor[1] : 0;
      //楼层（总层数）
      preg_match('/楼　　层：<\/span>第.*层\(共(.*)层\)<\/dd>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/<span class="gray6">装.*<\/span>(.*)\(可分割\)/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)年/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/联 系 人.*strong>(.*)<\/strong>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/<input id="mobile" type="hidden"  value="(.*)"\/>/siU', $con, $tel);
      $data['telno1'] = $tel[1];
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
      $cond = array('tel' => $data['telno1']);
      $check_result = $this->autocollect_model->check_agent_tel($cond);
      if (empty($check_result)) {

        //判断该条房源是否已经采集过了
        $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
        $result = $this->autocollect_model->check_house_only($where);
        if (empty($result)) {

          $i++;
          //房源还未采集入库，可以入库
          $info = array(
            'house_title' => $data['house_title'],
            'district' => $data['district'],
            'block' => $data['block'],
            'house_name' => $data['house_name'],
            'house_addr' => $data['house_addr'],
            'sell_type' => 3,
            'price' => $data['price'],
            'avgprice' => $data['avgprice'],
            'forward' => $data['forward'],
            'room' => $data['room'] ? $data['room'] : 0,
            'hall' => $data['hall'] ? $data['hall'] : 0,
            'toilet' => $data['toilet'] ? $data['toilet'] : 0,
            'buildarea' => $data['buildarea'],
            'floor' => $data['floor'],
            'totalfloor' => $data['totalfloor'],
            'serverco' => $data['serverco'],
            'owner' => $data['owner'],
            'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
            'telno1' => $data['telno1'],
            'createtime' => $data['createtime'],
            'source_from' => '搜房'
          );
          //$sql = "insert into sell_house_collect (house_title,district,block,house_name,house_addr,sell_type,price,avgprice,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['sell_type']."','".$info['price']."','".$info['avgprice']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
          //echo $sql." 阿顶";
          $rel = $this->autocollect_model->add_collect_house($info);
        } else {
          //房源已经入库，请勿重复采集
          continue;
        }
      } else {
        //是中介房源,请勿入库
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房商铺房源！";
  }


  /**
   * 采集搜房二手房别墅
   */
  public function sell_fang_villa()
  {

    $url = "http://esf.sh.fang.com/villa/a21-i32/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title"><a href="(.*)"  target="_blank">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://esf.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title flc".*span>(.*)<\/span>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="esfshxq_12">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="esfshxq_13">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //楼盘名称
      preg_match('/楼盘名称：<\/span>.*style="font-size: 14px;">(.*)<\/strong>/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //别墅地址
      preg_match('/地　　址：<\/span>(.*)<\/dt>/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/span>(.*)<\/dd>/siU', $con, $type);
      $data['sell_type'] = $type[1];
      //总价
      preg_match('/总　　价：<span class="red20b">(.*)<\/span>万元/siU', $con, $total_price);
      $data['price'] = $total_price[1];
      //单价
      preg_match('/单　　价：(.*)元\/平方米/siU', $con, $average_price);
      $data['avgprice'] = $this->autocollect_model->con_replace($average_price[1]);
      //朝向
      preg_match('/朝　　向：<\/span>(.*)<\/dd>/siU', $con, $direction);
      @$data['forward'] = @$direction[1] ? @$direction[1] : 3;
      //户型（室）
      preg_match('/户　　型：<\/span>(.*)室.*<\/dt>/siU', $con, $room);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户　　型：<\/span>.*室(.*)厅.*<\/dt>/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户　　型：<\/span>.*厅(.*)卫<\/dt>/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/建筑面积：<span class="red20b">(.*)<\/span>平方米/siU', $con, $acreage);
      $data['buildarea'] = $acreage[1];
      //楼层（所属层）
      preg_match('/<span class="gray9">楼　　层：<\/span>第(.*)层/siU', $con, $floor);
      @$data['floor'] = @$floor[1] ? @$floor[1] : 0;
      //楼层（总层数）
      preg_match('/<span class="gray9">楼　　层：<\/span>第.*共(.*)层/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/<span class="gray9">装　　修：<\/span>(.*)<\/dd>/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)年/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/联 系 人：(.*)<\/dd>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/手　　机：<strong class="tel14">(.*)<\/strong>/siU', $con, $tel);
      $data['telno1'] = $tel[1] ? $tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;
      //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
      $cond = array('tel' => $data['telno1']);
      $check_result = $this->autocollect_model->check_agent_tel($cond);
      if ($data['telno1'] != "") {
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'sell_type' => 2,
              'price' => $data['price'],
              'avgprice' => $data['avgprice'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => $data['owner'],
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '搜房'
            );
            //$sql = "insert into sell_house_collect (house_title,district,block,house_name,house_addr,sell_type,price,avgprice,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['sell_type']."','".$info['price']."','".$info['avgprice']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶";
            $rel = $this->autocollect_model->add_collect_house($info);
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房别墅房源！";
  }


  /**
   * 采集搜房二手房写字楼
   */
  public function sell_fang_office()
  {
    $url = "http://office.sh.fang.com/shou/house/a21-i33/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title"><a href="(.*)" target="_blank" title=".*">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 15) {
        continue;
      }
      $val = "http://office.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title".*h1>(.*)<\/h1>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="A3">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="A4">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //写字楼名称
      preg_match('/写字楼名称：<\/span>(.*)\(.*<a.*target="_blank" id="A3">/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //写字楼地址
      preg_match('/楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
      $data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags($address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/span>(.*)<\/li>/siU', $con, $type);
      $data['sell_type'] = $type[1];
      //总价
      preg_match('/总价：(.*)万/siU', $con, $total_price);
      $data['price'] = $total_price[1];
      //单价
      preg_match('/<span class="red20b">(.*)<\/span>/siU', $con, $average_price);
      $data['avgprice'] = $average_price[1];
      //朝向
      preg_match('/朝　　向：<\/span>(.*)<\/dd>/siU', $con, $direction);
      @$data['forward'] = @$direction[1] ? @$direction[1] : 3;
      //户型（室）
      preg_match('/户　　型：<\/span>(.*)室.*<\/dt>/siU', $con, $room);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户　　型：<\/span>.*室(.*)厅.*<\/dt>/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户　　型：<\/span>.*厅(.*)卫<\/dt>/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/建筑面积：(.*)㎡<\/dd>/siU', $con, $acreage);
      $data['buildarea'] = $acreage[1];
      //楼层（所属层）
      preg_match('/楼　　层：<\/span>第(.*)层/siU', $con, $floor);
      $data['floor'] = $floor[1];
      //楼层（总层数）
      preg_match('/楼　　层：<\/span>第.*层\(共(.*)层\)<\/dd>/siU', $con, $total_floor);
      $data['totalfloor'] = $total_floor[1];
      //装修
      preg_match('/<span class="gray6">装.*<\/span>(.*)\(可分割\)/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)年/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : "暂无资料");
      //联系人
      preg_match('/联 系 人.*strong>(.*)<\/strong>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/<input id="mobile" type="hidden"  value="(.*)"\/>/siU', $con, $tel);
      $data['telno1'] = $tel[1];
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;


      //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
      $cond = array('tel' => $data['telno1']);
      $check_result = $this->autocollect_model->check_agent_tel($cond);
      if (empty($check_result)) {

        //判断该条房源是否已经采集过了
        $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
        $result = $this->autocollect_model->check_house_only($where);
        if (empty($result)) {

          $i++;
          //房源还未采集入库，可以入库
          $info = array(
            'house_title' => $data['house_title'],
            'district' => $data['district'],
            'block' => $data['block'],
            'house_name' => $data['house_name'],
            'house_addr' => $data['house_addr'],
            'sell_type' => 4,
            'price' => $data['price'],
            'avgprice' => $data['avgprice'],
            'forward' => $data['forward'],
            'room' => $data['room'] ? $data['room'] : 0,
            'hall' => $data['hall'] ? $data['hall'] : 0,
            'toilet' => $data['toilet'] ? $data['toilet'] : 0,
            'buildarea' => $data['buildarea'],
            'floor' => $data['floor'],
            'totalfloor' => $data['totalfloor'],
            'serverco' => $data['serverco'],
            'owner' => $data['owner'],
            'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
            'telno1' => $data['telno1'],
            'createtime' => $data['createtime'],
            'source_from' => '搜房'
          );
          //$sql = "insert into sell_house_collect (house_title,district,block,house_name,house_addr,sell_type,price,avgprice,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['sell_type']."','".$info['price']."','".$info['avgprice']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
          //echo $sql." 阿顶";
          $rel = $this->autocollect_model->add_collect_house($info);
        } else {
          //房源已经入库，请勿重复采集
          continue;
        }
      } else {
        //是中介房源,请勿入库
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房写字楼房源！";
  }


  /**
   * 采集搜房租房住宅
   */
  public function rent_fang_house()
  {

    $url = "http://zu.sh.fang.com/house/a21-i32/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList" id="rentid_66">(.*)<div class="fanye gray6" id="rentid_67">/siU', $content, $pro);
    preg_match_all('/<p class="title".*a href="(.*)" target="_blank">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://zu.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<input type="hidden" name="talkTitle" id="talkTitle" value="(.*)" \/>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="gerenzfxq_B04_03" class="blue".*target="_blank">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="gerenzfxq_B04_04" class="blue".*target="_blank">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //楼盘名称
      preg_match('/小 区：<\/p>.*class="info">(.*)<\/p>/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //地址
      preg_match('/址：<\/p>.*class="info" title=".*">(.*)<\/p>/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型.*class="info">(.*)<\/p>/siU', $con, $type);
      $data['rent_type'] = $this->autocollect_model->con_replace($type[1]);
      //租金
      preg_match('/class="num red">(.*)<\/span>/siU', $con, $total_price);
      $data['price'] = $this->autocollect_model->con_replace($total_price[1]);
      //付款方式
      preg_match('/<\/span>元\/月\[(.*)\]/siU', $con, $average_price);
      $data['pricetype'] = $this->autocollect_model->con_replace($average_price[1]);
      //朝向
      preg_match('/朝 向：<\/p>.*class="info">(.*)<\/p>/siU', $con, $direction);
      @$data['forward'] = $this->autocollect_model->con_replace(@$direction[1] ? @$direction[1] : 3);
      //户型（室）
      preg_match('/户 型：<\/p>.*class="info">(.*)室.*卫/siU', $con, $room);
      @$room[1] = $this->autocollect_model->con_replace(@$room[1]);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }

      //户型（厅）
      preg_match('/户 型：<\/p>.*class="info">.*室(.*)厅/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户 型：<\/p>.*class="info">.*厅(.*)卫/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/面 积：<\/p>.*class="info">(.*)平米<\/p>/siU', $con, $acreage);
      $data['buildarea'] = $this->autocollect_model->con_replace($acreage[1]);
      //楼层（所属层）
      preg_match('/楼 层：<\/p>.*class="info">(.*)\/.*层<\/p>/siU', $con, $floor);
      @$data['floor'] = $this->autocollect_model->con_replace(@$floor[1] ? @$floor[1] : 0);
      //楼层（总层数）
      preg_match('/楼 层：<\/p>.*class="info">.*\/(.*)层<\/p>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/装 修：<\/p>.*class="info">(.*)<\/p>/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)<\/dd>/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/<span class="font14 bold">(.*)<\/span>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/<span class="telno0">(.*)<\/span>/siU', $con, $tel);
      @$data['telno1'] = @$tel[1] ? @$tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      if ($data['telno1'] != "") {
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond);
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_rent_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 1,
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => strip_tags($data['owner']),
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '搜房'
            );
            //$sql = "insert into rent_house_collect (house_title,district,block,house_name,house_addr,rent_type,price,pricetype,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['rent_type']."','".$info['price']."','".$info['pricetype']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶<br>";
            $rel = $this->autocollect_model->add_rent_collect_house($info);
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }


  /**
   * 采集搜房租房商铺
   */
  public function rent_fang_shop()
  {
    $url = "http://shop.sh.fang.com/zu/house/a21-i33/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title".*a href=\'(.*)\' target="_blank" title=".*">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://shop.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title".*h1>(.*)<\/h1>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="esfshxq_12">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="esfshxq_13">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //商铺名称
      preg_match('/商铺名称：<\/span>(.*)\(.*<a.*target="_blank" id="esfshxq_12"/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //地址
      preg_match('/<span class="gray6">楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/<span class="gray6">类    型：<\/span>(.*)<\/dd>/siU', $con, $type);
      $data['rent_type'] = $this->autocollect_model->con_replace($type[1]);
      //租金
      preg_match('/<span class="red20b">(.*)<\/span>/siU', $con, $total_price);
      $data['price'] = $this->autocollect_model->con_replace($total_price[1]);
      //付款方式
      preg_match('/支付方式：(.*)\)/siU', $con, $average_price);
      $data['pricetype'] = $this->autocollect_model->con_replace($average_price[1]);
      //朝向
      preg_match('/朝 向：<\/p>.*class="info">(.*)<\/p>/siU', $con, $direction);
      @$data['forward'] = $this->autocollect_model->con_replace(@$direction[1] ? @$direction[1] : 3);
      //户型（室）
      preg_match('/户 型：<\/p>.*class="info">(.*)室.*卫/siU', $con, $room);
      @$room[1] = $this->autocollect_model->con_replace(@$room[1]);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户 型：<\/p>.*class="info">.*室(.*)厅/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户 型：<\/p>.*class="info">.*厅(.*)卫/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/出租面积：(.*)㎡<\/dd>/siU', $con, $acreage);
      $data['buildarea'] = $this->autocollect_model->con_replace($acreage[1]);
      //楼层（所属层）
      preg_match('/楼　　层：<\/span>第(.*)层\(共.*层\)<\/dd>/siU', $con, $floor);
      @$data['floor'] = $this->autocollect_model->con_replace(@$floor[1] ? @$floor[1] : 0);
      //楼层（总层数）
      preg_match('/楼　　层：<\/span>第.*层\(共(.*)层\)<\/dd>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/<span class="gray6">装    修：<\/span>(.*)\(不可分割/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)<\/dd>/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/联 系 人.*strong>(.*)<\/strong>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/<input id="mobile" type="hidden"  value="(.*)"\/>/siU', $con, $tel);
      $data['telno1'] = $tel[1] ? $tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      if ($data['telno1'] != "") {
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond);
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_rent_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 3,
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => strip_tags($data['owner']),
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '搜房'
            );
            //$sql = "insert into rent_house_collect (house_title,district,block,house_name,house_addr,rent_type,price,pricetype,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['rent_type']."','".$info['price']."','".$info['pricetype']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶<br>";
            $rel = $this->autocollect_model->add_rent_collect_house($info);
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房商铺房源！";
  }


  /**
   * 采集搜房租房别墅
   */
  public function rent_fang_villa()
  {
    $url = "http://zu.sh.fang.com/villa/a24-i33/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="fanye gray6" id="rentid_67">/siU', $content, $pro);
    preg_match_all('/<p class="title".*a href=\'(.*)\' target="_blank">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $val = "http://zu.sh.fang.com/" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<input type="hidden" name="talkTitle" id="talkTitle" value="(.*)" \/>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="agantzfxq_B04_04".*target="_blank">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="agantzfxq_B04_05".*target="_blank">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //名称
      preg_match('/小 区：<\/p.*class="info">(.*)<\/p>/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //地址
      preg_match('/地 址：<\/p.*class="info" title=".*"> (.*)<\/p>/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/p.*class="info">(.*)<\/p>/siU', $con, $type);
      $data['rent_type'] = $this->autocollect_model->con_replace($type[1]);
      //租金
      preg_match('/<span class="num red">(.*)<\/span>元\/月/siU', $con, $total_price);
      $data['price'] = $this->autocollect_model->con_replace($total_price[1]);
      //付款方式
      preg_match('/<span class="num red">.*span>元\/月\[(.*)\]/siU', $con, $average_price);
      $data['pricetype'] = $this->autocollect_model->con_replace($average_price[1]);
      //朝向
      preg_match('/朝 向：<\/p.*class="info">(.*)<\/p>/siU', $con, $direction);
      @$data['forward'] = $this->autocollect_model->con_replace(@$direction[1] ? @$direction[1] : 3);
      //户型（室）
      preg_match('/户 型：<\/p.*p class="info">(.*)室.*卫/siU', $con, $room);
      @$room[1] = $this->autocollect_model->con_replace(@$room[1]);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户 型：<\/p.*p class="info">.*室(.*)厅.*卫/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户 型：<\/p.*p class="info">.*厅(.*)卫/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/面 积：<\/p.*class="info">(.*)平方米<\/p>/siU', $con, $acreage);
      $data['buildarea'] = $this->autocollect_model->con_replace($acreage[1]);
      //楼层（所属层）
      preg_match('/楼 层：<\/p.*class="info">(.*)\/.*层<\/p>/siU', $con, $floor);
      @$data['floor'] = $this->autocollect_model->con_replace(@$floor[1] ? @$floor[1] : 0);
      //楼层（总层数）
      preg_match('/楼 层：<\/p.*class="info">.*\/(.*)层<\/p>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/装 修：<\/p.*class="info">(.*)<\/p>/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)<\/dd>/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/<span class="name floatl" id="Span2">(.*)<\/span>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/id="agtphone" name="agtphone">(.*)<\/span>/siU', $con, $tel);
      $data['telno1'] = $tel[1] ? $tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      if ($data['telno1'] != "") {
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond);
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_rent_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 2,
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => strip_tags($data['owner']),
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '搜房'
            );//echo "<pre>";print_r($info);die;
            //$sql = "insert into rent_house_collect (house_title,district,block,house_name,house_addr,rent_type,price,pricetype,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['rent_type']."','".$info['price']."','".$info['pricetype']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶<br>";
            $rel = $this->autocollect_model->add_rent_collect_house($info);
            //echo $rel;die;
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房别墅房源！";
  }


  /**
   * 采集搜房租房写字楼
   */
  public function rent_fang_office()
  {
    $url = "http://office.sh.fang.com/zu/house/a21-i32/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="houseList">(.*)<div class="clearfix mt15">/siU', $content, $pro);
    preg_match_all('/<p class="title".*href=\'(.*)\' target="_blank" title=".*">/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 15) {
        continue;
      }
      $val = "http://office.sh.fang.com" . $val;
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<div class="title".*h1>(.*)<\/h1>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/id="esfshxq_12">(.*)<\/a>/siU', $con, $district);
      $data['district'] = $this->autocollect_model->con_replace($district[1]);
      //板块
      preg_match('/id="esfshxq_13">(.*)<\/a>/siU', $con, $block);
      $data['block'] = $block[1];
      //名称
      preg_match('/写字楼名称：<\/span>(.*)\(.*<a.*id="esfshxq_12"/siU', $con, $building);
      $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
      //地址
      preg_match('/<span class="gray6">楼盘地址：<\/span>(.*)<\/dt>/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型：<\/span>(.*)<\/li>/siU', $con, $type);
      $data['rent_type'] = $this->autocollect_model->con_replace($type[1]);
      //租金
      preg_match('/<span class="red20b">(.*)<\/span>/siU', $con, $total_price);
      $data['price'] = $this->autocollect_model->con_replace($total_price[1]);
      //付款方式
      preg_match('/支付方式：(.*)\).*<\/dt>/siU', $con, $average_price);
      $data['pricetype'] = $this->autocollect_model->con_replace($average_price[1]);
      //朝向
      preg_match('/朝 向：<\/p>.*class="info">(.*)<\/p>/siU', $con, $direction);
      @$data['forward'] = $this->autocollect_model->con_replace(@$direction[1] ? @$direction[1] : 3);
      //户型（室）
      preg_match('/户 型：<\/p>.*class="info">(.*)室.*卫/siU', $con, $room);
      @$room[1] = $this->autocollect_model->con_replace(@$room[1]);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }
      //户型（厅）
      preg_match('/户 型：<\/p>.*class="info">.*室(.*)厅/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/户 型：<\/p>.*class="info">.*厅(.*)卫/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/出租面积：(.*)㎡<\/dd>/siU', $con, $acreage);
      $data['buildarea'] = $this->autocollect_model->con_replace($acreage[1]);
      //楼层（所属层）
      preg_match('/楼　　层：<\/span>第(.*)层\(共.*层\)<\/dd>/siU', $con, $floor);
      @$data['floor'] = $this->autocollect_model->con_replace(@$floor[1] ? @$floor[1] : 0);
      //楼层（总层数）
      preg_match('/楼　　层：<\/span>第.*层\(共(.*)层\)<\/dd>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/装    修：<\/span>(.*)\(可分割/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/建筑年代：<\/span>(.*)<\/dd>/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/联 系 人：(.*)<\/strong>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "暂无");
      //联系方式
      preg_match('/<input id="mobile" type="hidden"  value="(.*)"\/>/siU', $con, $tel);
      $data['telno1'] = $tel[1] ? $tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      if ($data['telno1'] != "") {
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond);
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_rent_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 4,
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => strip_tags($data['owner']),
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '搜房'
            );//echo "<pre>";print_r($info);die;
            //$sql = "insert into rent_house_collect (house_title,district,block,house_name,house_addr,rent_type,price,pricetype,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['rent_type']."','".$info['price']."','".$info['pricetype']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶<br>";
            $rel = $this->autocollect_model->add_rent_collect_house($info);
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房写字楼房源！";
  }


  /**
   * 采集安居客租房住宅
   */
  public function rent_anjk_house()
  {

    $url = "http://sh.zu.anjuke.com/fangyuan/l2-p5/";
    $compress = 'gzip';
    $content = $this->autocollect_model->vcurl($url, $compress);
    $i = 0;
    preg_match('/<div class="zuTab">(.*)<div class="multi plate">/siU', $content, $pro);
    preg_match_all('/<dt class="dt_photo">.*sign=\'true\' href="(.*)"/siU', $pro[1], $prj);
    ini_set("max_execution_time", 24000);
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($prj[1] as $key => $val) {
      if ($key > 20) {
        continue;
      }
      $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      $data = array();

      //房源标题
      preg_match('/<h3 class="fl">(.*)<\/h3>/siU', $con, $title);
      $data['house_title'] = $this->autocollect_model->con_replace($title[1]);
      //区属
      preg_match('/所在版块<\/dt.*a target="_blank" href=".*">(.*)<\/a>/siU', $con, $district);
      @$data['district'] = $this->autocollect_model->con_replace(@$district[1]);
      //板块
      preg_match('/所在版块<\/dt.*a target="_blank" href=".*">.*<\/a.*a target="_blank".*>(.*)<\/a>/siU', $con, $block);
      @$data['block'] = @$block[1];
      //楼盘名称
      preg_match('/小区名<\/dt.*a href=".*" target="_blank">(.*)<\/a>/siU', $con, $building);
      @$data['house_name'] = $this->autocollect_model->con_replace(strip_tags(@$building[1]));
      //地址
      preg_match('/<dt>地址<\/dt.*dd>(.*)<a href=".*" target="_blank" class="f12">/siU', $con, $address);
      @$data['house_addr'] = $this->autocollect_model->con_replace(str_replace("[地图]", " ", strip_tags(@$address[1])));
      //用途（住宅、别墅、写字楼）
      preg_match('/物业类型<\/dt.*dd>(.*)<\/dd>/siU', $con, $type);
      @$data['rent_type'] = $this->autocollect_model->con_replace(@$type[1]);
      //租金
      preg_match('/租价<\/dt.*class="og"><strong><span class="f26">(.*)<\/span>/siU', $con, $total_price);
      @$data['price'] = $this->autocollect_model->con_replace(@$total_price[1]);
      //付款方式
      preg_match('/租金押付<\/dt.*dd>(.*)<\/dd>/siU', $con, $average_price);
      @$data['pricetype'] = $this->autocollect_model->con_replace(@$average_price[1]);
      //朝向
      preg_match('/朝向<\/dt.*dd>(.*)<\/dd>/siU', $con, $direction);
      @$data['forward'] = $this->autocollect_model->con_replace(@$direction[1] ? @$direction[1] : 3);
      //户型（室）
      preg_match('/<dt>房型<\/d.*dd>(.*)室.*<\/dd>/siU', $con, $room);
      @$room[1] = $this->autocollect_model->con_replace(@$room[1]);
      switch (@$room[1]) {
        case "一":
          @$data['room'] = 1;
          break;
        case "二":
          @$data['room'] = 2;
          break;
        case "三":
          @$data['room'] = 3;
          break;
        case "四":
          @$data['room'] = 4;
          break;
        case "五":
          @$data['room'] = 5;
          break;
        case "六":
          @$data['room'] = 6;
          break;
        case "七":
          @$data['room'] = 7;
          break;
        case "八":
          @$data['room'] = 8;
          break;
        case "九":
          @$data['room'] = 9;
        default:
          @$data['room'] = @$room[1];
      }

      //户型（厅）
      preg_match('/<dt>房型<\/dt.*室(.*)厅.*<\/dd>.*class="p_phrase cf".*租赁方式/siU', $con, $hall);
      switch (@$hall[1]) {
        case "一":
          @$data['hall'] = 1;
          break;
        case "二":
          @$data['hall'] = 2;
          break;
        case "三":
          @$data['hall'] = 3;
          break;
        case "四":
          @$data['hall'] = 4;
          break;
        case "五":
          @$data['hall'] = 5;
          break;
        case "六":
          @$data['hall'] = 6;
          break;
        case "七":
          @$data['hall'] = 7;
          break;
        case "八":
          @$data['hall'] = 8;
          break;
        case "九":
          @$data['hall'] = 9;
        default:
          @$data['hall'] = @$hall[1];
      }
      //户型（卫）
      preg_match('/房型<\/dt.*dd>.*厅(.*)卫<\/dd>.*class="p_phrase cf".*租赁方式/siU', $con, $toilet);
      switch (@$toilet[1]) {
        case "一":
          @$data['toilet'] = 1;
          break;
        case "二":
          @$data['toilet'] = 2;
          break;
        case "三":
          @$data['toilet'] = 3;
          break;
        case "四":
          @$data['toilet'] = 4;
          break;
        case "五":
          @$data['toilet'] = 5;
          break;
        case "六":
          @$data['toilet'] = 6;
          break;
        case "七":
          @$data['toilet'] = 7;
          break;
        case "八":
          @$data['toilet'] = 8;
          break;
        case "九":
          @$data['toilet'] = 9;
        default:
          @$data['toilet'] = @$toilet[1];
      }
      //面积
      preg_match('/<dt>面积<\/dt.*dd>(.*)平米<\/dd>/siU', $con, $acreage);
      @$data['buildarea'] = $this->autocollect_model->con_replace(@$acreage[1]);
      //楼层（所属层）
      preg_match('/楼层<\/dt.*dd>(.*)\/.*<\/dd>/siU', $con, $floor);
      @$data['floor'] = $this->autocollect_model->con_replace(@$floor[1] ? @$floor[1] : 0);
      //楼层（总层数）
      preg_match('/楼层<\/dt.*dd>.*\/(.*)<\/dd>/siU', $con, $total_floor);
      @$data['totalfloor'] = @$total_floor[1] ? @$total_floor[1] : 0;
      //装修
      preg_match('/<dt>装修<\/dt.*dd>(.*)<\/dd>/siU', $con, $decoration);
      @$data['serverco'] = $this->autocollect_model->con_replace(@$decoration[1] ? @$decoration[1] : "暂无资料");
      //建筑年代
      preg_match('/<dt>建造年代<\/dt.*dd>(.*)<\/dd>/siU', $con, $time);
      @$data['buildyear'] = $this->autocollect_model->con_replace(@$time[1] ? @$time[1] : 0);
      //联系人
      preg_match('/<strong class="name">(.*)<\/strong>/siU', $con, $contact);
      $data['owner'] = $this->autocollect_model->con_replace(@$contact[1] ? @$contact[1] : "匿名");
      //联系方式
      preg_match('/<i class="p_icon icon_tel"><\/i>(.*)<\/div>/siU', $con, $tel);
      @$data['telno1'] = @$tel[1] ? @$tel[1] : "";
      //采集时间
      $data['createtime'] = time() + 8 * 60 * 60;

      if ($data['telno1'] != "") {
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond);
        if (empty($check_result)) {

          //判断该条房源是否已经采集过了
          $where = array('telno1' => $data['telno1'], 'house_title' => $data['house_title']);
          $result = $this->autocollect_model->check_rent_house_only($where);
          if (empty($result)) {

            $i++;
            //房源还未采集入库，可以入库
            $info = array(
              'house_title' => $data['house_title'],
              'district' => $data['district'],
              'block' => $data['block'],
              'house_name' => $data['house_name'],
              'house_addr' => $data['house_addr'],
              'rent_type' => 1,
              'price' => $data['price'],
              'pricetype' => $data['pricetype'],
              'forward' => $data['forward'],
              'room' => $data['room'] ? $data['room'] : 0,
              'hall' => $data['hall'] ? $data['hall'] : 0,
              'toilet' => $data['toilet'] ? $data['toilet'] : 0,
              'buildarea' => $data['buildarea'],
              'floor' => $data['floor'],
              'totalfloor' => $data['totalfloor'],
              'serverco' => $data['serverco'],
              'owner' => strip_tags($data['owner']),
              'buildyear' => ($data['buildyear'] ? $data['buildyear'] : ""),
              'telno1' => $data['telno1'],
              'createtime' => $data['createtime'],
              'source_from' => '安居客'
            );
            echo "<pre>";
            print_r($info);
            die;
            //$sql = "insert into rent_house_collect (house_title,district,block,house_name,house_addr,rent_type,price,pricetype,forward,room,hall,toilet,buildarea,floor,totalfloor,serverco,owner,buildyear,telno1,createtime,source_from) values('".$info['house_title']."','".$info['district']."','".$info['block']."','".$info['house_name']."','".$info['house_addr']."','".$info['rent_type']."','".$info['price']."','".$info['pricetype']."','".$info['forward']."','".$info['room']."','".$info['hall']."','".$info['toilet']."','".$info['buildarea']."','".$info['floor']."','".$info['totalfloor']."','".$info['serverco']."','".$info['owner']."','".$info['buildyear']."','".$info['telno1']."','".$info['createtime']."','".$info['source_from']."');";
            //echo $sql." 阿顶<br>";
            $rel = $this->autocollect_model->add_rent_collect_house($info);
          } else {
            //房源已经入库，请勿重复采集
            continue;
          }
        } else {
          //是中介房源,请勿入库
          continue;
        }
      } else {
        continue;
      }
    }
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }
}

/* End of file autocollect.php */
/* Location: ./application/mls_admin/controllers/autocollect.php */
