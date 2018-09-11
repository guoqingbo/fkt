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
class Autocollect_nj extends My_Controller
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
   * 采集赶集网二手房(分区域全部数据)列表页
   * 2015.5.11 cc
   */
  public function sell_ganji_house_lists_all()
  {
    $no = isset($_GET['no']) ? $_GET['no'] : '';
    $part = array('xuanwu', 'gulou', 'jianye', 'baixia', 'qinhuai', 'yuhuatai', 'jiangning', 'qixia', 'xiaguan', 'pukou', 'dachang', 'liuhe', 'lishui', 'gaochun', 'nanjingzhoubian');
    $lists = array();
    $i = 0;
    $page = 20;
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
   * 采集赶集网二手房列表页
   * 2015.6.4 cc
   */
  public function sell_ganji_house_lists()
  {
    $lists = array();
    $i = 0;
    $page = 63;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://nj.ganji.com/fang5/a1/";
      } else {
        $url = "http://nj.ganji.com/fang5/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://nj.ganji.com" . $val;
        $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
        if ($res !== 0) {
          $i++;
        }
      }
    }
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }

  /**
   * 采集赶集网二手房住宅
   * author  angel_in_us
   * date    2015-04-16
   */
  public function sell_ganji_house()
  {
    echo "<script>
                        function refresh(seconds){
                            setTimeout(\"self.location.reload()\",seconds*1000);
                        }
                        refresh(180);//调用方法启动定时刷新，数值单位：秒。
                    </script>";
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $like = array('url' => 'http://nj.ganji.com/fang5/');
    $orlike = array();
    $result = $this->autocollect_model->check_collect_house_lists($limit, $like, $orlike, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        $del = array('url' => $value['url']);
        $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        $data = array();
        $data['oldurl'] = $val;
        //房源照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<a href=".*".*src="(.*)".*<\/a>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //楼盘地址
        preg_match('/址：<\/em>(.*)<\/span>/siU', $con, $address);
        if (!empty($address)) {
          //区属
          preg_match('/置：<\/span>.*南京<\/a>.*<a href=".*">(.*)<\/a>/siU', $con, $district);
          $data['district'] = $this->autocollect_model->con_replace(strip_tags($district[1]));
          //板块
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $block);
          if (!empty($block[1])) {
            $blocks = explode('-', $block[1]);
            if (isset($blocks[1])) {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[1]));
            } else {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
            }
          } else {
            $data['block'] = "暂无资料";
          }
          //楼盘名称
          preg_match('/区：.*<a href=".*" target="blank_".*>(.*)<\/a>/siU', $con, $building);
          if (empty($building)) {
            //楼盘名称
            preg_match('/区：.*<\/span>(.*)<span class="around-other">/siU', $con, $building);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          //区属
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $district);
          if (!empty($district[1])) {
            $districts = explode('-', $district[1]);
            $data['district'] = $this->autocollect_model->con_replace(strip_tags($districts[0]));
          } else {
            $data['district'] = "暂无资料";
          }
          //板块
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $block);
          if (!empty($block[1])) {
            $blocks = explode('-', $block[1]);
            if (isset($blocks[1])) {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[1]));
            } else {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
            }
          } else {
            $data['block'] = "暂无资料";
          }
          //楼盘名称
          preg_match('/区：.*<a href=".*" target="blank_".*>(.*)<\/a>/siU', $con, $building);
          if (empty($building)) {
            //楼盘名称
            preg_match('/区：.*<\/span>(.*)<span class="around-other">/siU', $con, $building);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
          //楼盘地址
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $address);
          if (!empty($address)) {
            $addresss = explode('-', $address[1]);
            if (isset($addresss[2])) {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[2]));
            } else if (isset ($addresss[1])) {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[1]));
            } else {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[0]));
            }
          } else {
            $data['house_addr'] = "暂无资料";
          }
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
        $data['price'] = $this->autocollect_model->con_replace(strip_tags($total_price[1]));
        //单价
        preg_match('/单<i class=".*"><\/i>价：<\/span>(.*)元.*<\/li>/siU', $con, $average_price);
        $data['avgprice'] = $this->autocollect_model->con_replace(strip_tags($average_price[1]));
        //朝向
        preg_match('/况：<\/span>(.*)\-/siU', $con, $direction);
        $direction[1] = $this->autocollect_model->con_replace(strip_tags($direction[1]));
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
            $data['forward'] = 3;
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
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags($acreage[1]));
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
        preg_match('/装修程度：<\/span>(.*)<\/li>/siU', $con, $decoration);
        $decoration[1] = $this->autocollect_model->con_replace(strip_tags(@$decoration[1]));
        switch ($decoration[1]) {
          case "豪华装修":
            $data['serverco'] = 5;
            break;
          case "精装修":
            $data['serverco'] = 3;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 4;
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
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/data\-phone="(.*)" data\-username/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //采集时间
        $data['createtime'] = time();
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
              'picurl' => $data['picurl'],
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
//					}
//                                        else{
//						//房源已经入库，请勿重复采集
//						echo "<br><h3>此房源已经入库：</h3><br>标题：".$data['house_title']."<br>链接：".$data['oldurl'];
//						continue;
//					}

          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
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
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }

  /**
   * 采集赶集网租房(分区域全部数据)列表页
   * 2015.5.11 cc
   */
  public function rent_ganji_house_lists_all()
  {
    $no = isset($_GET['no']) ? $_GET['no'] : '';
    $part = array('xuanwu', 'gulou', 'jianye', 'baixia', 'qinhuai', 'yuhuatai', 'jiangning', 'qixia', 'xiaguan', 'pukou', 'dachang', 'liuhe', 'lishui', 'gaochun', 'nanjingzhoubian');
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 20;
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
    $lists = array();
    $i = 0;
    $max = 4;
    $page = 63;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://nj.ganji.com/fang1/a1/";
      } else {
        $url = "http://nj.ganji.com/fang1/a1o" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<li class="list-img clearfix".*>.*<a class="list-info-title js-title" href="(\/fang.*htm)" target="_blank".*<\/li>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = "http://nj.ganji.com" . $val;
        $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
        if ($res !== 0) {
          $i++;
        }
      }
    }
    echo "成功采集到 " . $i . "条租房房源！";
  }

  /**
   * 采集赶集网租房
   * author  angel_in_us
   * date    2015-04-17
   */
  public function rent_ganji_house()
  {
    echo "<script>
                        function refresh(seconds){
                            setTimeout(\"self.location.reload()\",seconds*1000);
                        }
                        refresh(180);//调用方法启动定时刷新，数值单位：秒。
                    </script>";
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 15;
    $like = array('url' => 'http://nj.ganji.com/fang1/');
    $orlike = array('url' => 'http://nj.ganji.com/fang3/');
    $result = $this->autocollect_model->check_collect_house_lists($limit, $like, $orlike, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        $del = array('url' => $value['url']);
        $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        $data = array();
        $data['oldurl'] = $val;
        //房源照片
        preg_match('/<div class="cont-box pics">.*<\/div>/siU', $con, $cons);
        if (!empty($cons)) {
          preg_match_all('/<a href=".*".*src="(.*)".*<\/a>/siU', $cons[0], $photo);
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }
        //房源标题
        preg_match('/<h1 class="title-name">(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags($houseid[1]));
        //楼盘地址
        preg_match('/址：<\/em>(.*)<\/span>/siU', $con, $address);
        if (!empty($address)) {
          //区属
          preg_match('/置：<\/span>.*南京<\/a>.*<a href=".*">(.*)<\/a>/siU', $con, $district);
          $data['district'] = $this->autocollect_model->con_replace(strip_tags($district[1]));
          //板块
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $block);
          if (!empty($block[1])) {
            $blocks = explode('-', $block[1]);
            if (isset($blocks[1])) {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[1]));
            } else {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
            }
          } else {
            $data['block'] = "暂无资料";
          }
          //楼盘名称
          preg_match('/区：.*<a href=".*" target="blank_".*>(.*)<\/a>/siU', $con, $building);
          if (empty($building)) {
            //楼盘名称
            preg_match('/区：.*<\/span>(.*)<span class="around-other">/siU', $con, $building);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        } else {
          //区属
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $district);
          if (!empty($district[1])) {
            $districts = explode('-', $district[1]);
            $data['district'] = $this->autocollect_model->con_replace(strip_tags($districts[0]));
          } else {
            $data['district'] = "暂无资料";
          }
          //板块
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $block);
          if (!empty($block[1])) {
            $blocks = explode('-', $block[1]);
            if (isset($blocks[1])) {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[1]));
            } else {
              $data['block'] = $this->autocollect_model->con_replace(strip_tags($blocks[0]));
            }
          } else {
            $data['block'] = "暂无资料";
          }
          //楼盘名称
          preg_match('/区：.*<a href=".*" target="blank_".*>(.*)<\/a>/siU', $con, $building);
          if (empty($building)) {
            //楼盘名称
            preg_match('/区：.*<\/span>(.*)<span class="around-other">/siU', $con, $building);
          }
          $data['house_name'] = $this->autocollect_model->con_replace(strip_tags($building[1]));
          //楼盘地址
          preg_match('/<p class="map\-top"><i class="ico\-coordinate"><\/i>南京 \-(.*)<\/p>/siU', $con, $address);
          if (!empty($address)) {
            $addresss = explode('-', $address[1]);
            if (isset($addresss[2])) {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[2]));
            } else if (isset ($addresss[1])) {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[1]));
            } else {
              $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($addresss[0]));
            }
          } else {
            $data['house_addr'] = "暂无资料";
          }
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
        $data['price'] = is_numeric($total_prices) ? $total_prices : "1";
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
            $data['forward'] = 3;
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
            $data['serverco'] = 3;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 4;
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
        preg_match('/在线联系：.*class=".*">(.*)<\/i>/siU', $con, $contact);
        $data['owner'] = $this->autocollect_model->con_replace(strip_tags($contact[1]));
        //联系方式
        preg_match('/data\-phone="(.*)" data\-username/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //采集时间
        $data['createtime'] = time();
        //echo "赶集出zu采集测试：<br><pre>";print_r($data);die;

        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            //echo"<pre>";print_r($data);die;
            //非中介房源,可以入库
            //判断该条房源是否已经采集过了
//					$where  = array('telno1'=>$data['telno1'],'house_title'=>$data['house_title']);
//					$result = $this->autocollect_model->check_rent_house_only($where,$database='db_city');
//					if(empty($result)){
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
              'picurl' => $data['picurl'],
              'e_status' => 0,
              'source_from' => 0
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
//					}else{
//						//房源已经入库，请勿重复采集
//						echo "<br><h3>此房源已经入库：</h3><br>标题：".$data['house_title']."<br>链接：".$data['oldurl'];
//						continue;
//					}

          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码没有采集到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          $del = array('url' => $value['url']);
          $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
          continue;
        }
      }
    }
    echo "成功采集到 " . $i . " 条租房房源！";
  }

  /**
   * 采集58同城二手房分区列表页
   * 2015.6.13 cc
   */
  public function sell_wuba_house_lists_all()
  {
    $no = isset($_GET['no']) ? $_GET['no'] : '';
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
    $lists = array();
    $i = 0;
    $page = 70;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://nj.58.com/ershoufang/0/";
      } else {
        $url = "http://nj.58.com/ershoufang/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<tr logr=".*".*<h1 class="bthead">.*href="(http\:\/\/nj\.58\.com\/ershoufang\/.*\.shtml)" target="_blank".*<\/tr>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        $lists['url'] = $val;
        $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
        if ($res !== 0) {
          $i++;
        }
      }
    }
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }

  /**
   * 采集58同城二手房住宅
   * author  angel_in_us
   * date    2015-04-17
   */
  public function sell_wuba_house()
  {
    echo "<script>
                        function refresh(seconds){
                            setTimeout(\"self.location.reload()\",seconds*1000);
                        }
                        refresh(180);//调用方法启动定时刷新，数值单位：秒。
                    </script>";
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $like = array('url' => 'http://nj.58.com/ershoufang/');
    $orlike = array();
    $result = $this->autocollect_model->check_collect_house_lists($limit, $like, $orlike, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        $del = array('url' => $value['url']);
        $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        $data = array();
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
            $data['serverco'] = 3;
            break;
          case "简单装修":
            $data['serverco'] = 2;
            break;
          case "中等装修":
            $data['serverco'] = 4;
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
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            //非中介房源,可以入库
            //判断该条房源是否已经采集过了
//				$where  = array('telno1'=>$data['telno1'],'house_title'=>$data['house_title']);
//				$result = $this->autocollect_model->check_house_only($where,$database='db_city');
//				if(empty($result)){
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
              'tel_url' => (string)$data['telno1'],
              'createtime' => $data['createtime'],
              'remark' => $data['remark'],
              'picurl' => $data['picurl'],
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_collect_house($info, $database = 'db_city');
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
    echo "成功采集到 " . $i . " 条二手房住宅房源！";
  }

  /**
   * 采集58同城租房分区域列表页
   * 2015.6.13 cc
   */
  public function rent_wuba_house_lists_all()
  {
    $no = isset($_GET['no']) ? $_GET['no'] : '';
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
   * 采集58同城网租房列表页
   * 2015.5.12 cc
   */
  public function rent_wuba_house_lists()
  {
    $lists = array();
    $i = 0;
    $page = 100;
    $max = 3;
    for ($num = 1; $num <= $page; $num++) {
      if ($num == 1) {
        $url = "http://nj.58.com/zufang/0/";
      } else {
        $url = "http://nj.58.com/zufang/0/pn" . $num . "/";
      }
      $compress = 'gzip';
      $content = $this->autocollect_model->vcurl($url, $compress);
      preg_match_all('/<td class="t qj-rentd">.*href="(http\:\/\/nj\.58\.com\/zufang\/.*\.shtml)" target="_blank".*<\/a>/siU', $content, $prj);
      foreach ($prj[1] as $key => $val) {
        if ($key < $max) {
          continue;
        }
        $lists['url'] = $val;
        $res = $this->autocollect_model->add_collect_house_lists($lists, $database = 'db_city');
        if ($res !== 0) {
          $i++;
        }
      }
    }
    echo "成功采集到 " . $i . " 条租房房源！";
  }

  /**
   * 采集58同城网租房
   * author  angel_in_us
   * date    2015-04-17
   */
  public function rent_wuba_house()
  {
    echo "<script>
                        function refresh(seconds){
                            setTimeout(\"self.location.reload()\",seconds*1000);
                        }
                        refresh(180);//调用方法启动定时刷新，数值单位：秒。
                    </script>";
    $compress = 'gzip';
    $i = 0;
    $hash = array();
    $limit = 10;
    $like = array('url' => 'http://nj.58.com/zufang/');
    $orlike = array();
    $result = $this->autocollect_model->check_collect_house_lists($limit, $like, $orlike, $database = 'db_city');
    //开始遍历列表页中相对应的详情页=》房源信息
    foreach ($result as $key => $value) {
      $where = array('hash' => md5($value['url']));
      $ress = $this->autocollect_model->check_collect_url_hash($where, $database = 'db_city');
      if (!empty($ress)) {
        $del = array('url' => $value['url']);
        $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
      } else {
        $val = $value['url'];
        $con = $this->autocollect_model->vcurl($val, $compress);  #采集房源详情
        //$con = mb_convert_encoding($con, "UTF-8","GBK");
        $data = array();
        $data['oldurl'] = $val;
        //房源照片
        preg_match_all('/<div class="descriptionImg">.*<img src="(.*)".*<\/div>/siU', $con, $photo);
        if (!empty($photo[1])) {
          $data['picurl'] = implode("*", $photo[1]);
        } else {
          $data['picurl'] = "暂无资料";
        }
        //房源标题
        preg_match('/<div class="bigtitle" ><h1>(.*)<\/h1>/siU', $con, $houseid);
        $data['house_title'] = $this->autocollect_model->con_replace(strip_tags(@$houseid[1]));
        //楼盘地址
        preg_match('/地址.*<div class="su_con .*">(.*)<span class="f12">/siU', $con, $address);
        if (empty($address[1])) {
          $data['house_addr'] = '暂无资料';
        } else {
          $data['house_addr'] = $this->autocollect_model->con_replace(strip_tags($address[1]));
        }
        //区属
        preg_match('/区域.*class="su_con w382".*href=".*">(.*)<\/a>/siU', $con, $district);
        if (empty($district[1])) {
          $data['district'] = "暂无资料";
        } else {
          $data['district'] = $this->autocollect_model->con_replace(strip_tags(@$district[1]));
        }
        //板块
        preg_match('/区域.*\-(.*)<\/li>/siU', $con, $block);
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
        preg_match('/区域.*\-(.*)<\/li>/siU', $con, $building);
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
        preg_match('/概况<\/div>.*㎡(.*)装修/siU', $con, @$type);
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
          default:
            $data['rent_type'] = 1;
            break;
        }
        //租金
        preg_match('/价格.*<span class="bigpri arial">(.*)<\/span>/siU', $con, $total_price);
        $total_prices = $this->autocollect_model->con_replace(strip_tags(@$total_price[1]));
        //有面议设置为1
        $data['price'] = is_numeric($total_prices) ? $total_prices : "1";
        //付款方式
        preg_match('/价格.*元\/月 <span class="f12.*">(.*)<\/span>/siU', $con, $pricetype);
        if (empty($pricetype[1])) {
          $data['pricetype'] = "押一付三";
        } else {
          $data['pricetype'] = $this->autocollect_model->con_replace(strip_tags($pricetype[1]));
        }
        //朝向
        preg_match('/概况.*<div class="su_con">.*朝向(.*)<\/div>/siU', $con, @$direction);
        if (empty($direction[1])) {
          $data['forward'] = 3;
        } else {
          $direction[1] = $this->autocollect_model->con_replace(strip_tags($direction[1]));
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
        }
        //户型（室）
        preg_match('/概况.*class="su_con">(.*)室/siU', $con, $room);
        $data['room'] = $this->autocollect_model->con_replace(strip_tags(@$room[1]));
        //户型（厅）
        preg_match('/概况.*class="su_con">.*室(.*)厅.*<\/li>/siU', $con, $hall);
        $data['hall'] = $this->autocollect_model->con_replace(strip_tags(@$hall[1]));
        //户型（卫）
        preg_match('/概况.*class="su_con">.*厅(.*)卫.*<\/li>/siU', $con, $toilet);
        $data['toilet'] = $this->autocollect_model->con_replace(strip_tags(@$toilet[1]));
        //面积
        preg_match('/概况.*class="su_con">.*卫(.*)㎡.*<\/div>/siU', $con, $acreage);
        $data['buildarea'] = $this->autocollect_model->con_replace(strip_tags(@$acreage[1]));
        //楼层（所属层）
        preg_match('/楼层.*<div class="su_con">(.*)层\/.*层/siU', $con, $floor);
        if (empty($floor[1])) {
          $data['floor'] = '';
        } else {
          $data['floor'] = $this->autocollect_model->con_replace(strip_tags($floor[1]));
        }
        //房源描述-备注
        preg_match('/<div class="description_con ".*<p>(.*)<p class="mb20"/siU', $con, $remark);
        $data['remark'] = $this->autocollect_model->con_replace(strip_tags(@$remark[1]));
        //楼层（总层数）
        preg_match('/楼层.*<div class="su_con">.*层\/(.*)层/siU', $con, $total_floor);
        if (empty($total_floor[1])) {
          $data['totalfloor'] = '';
        } else {
          $data['totalfloor'] = $this->autocollect_model->con_replace(strip_tags($total_floor[1]));
        }
        //装修
        preg_match('/概况.*class="su_con">.*(.*装修)<\/div>/siU', $con, $decoration);
        if (empty($decoration[1])) {
          $data['serverco'] = 2;
        } else {
          $decoration[1] = $this->autocollect_model->con_replace(strip_tags($decoration[1]));
          $decorations[1] = str_replace("&nbsp", "", $decoration[1]);
          switch ($decorations[1]) {
            case "豪华装修":
              $data['serverco'] = 5;
              break;
            case "精装修":
              $data['serverco'] = 3;
              break;
            case "简单装修":
              $data['serverco'] = 2;
              break;
            case "中等装修":
              $data['serverco'] = 4;
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
        }
        //联系人
        preg_match('/联系<\/div>.*rel="nofollow".*>(.*)<\/a>/siU', $con, $contact);
        if (strlen(trim(@$contact[1])) > 0) {
          $data['owner'] = $this->autocollect_model->con_replace(strip_tags(@$contact[1]));
        } else {
          $data['owner'] = "个人";
        }
        //联系方式
        preg_match('/<span id="t_phone" class="f20.*document\.write\("<img src=\'(.*)\' \/>"/siU', $con, $tel);
        $data['telno1'] = $this->autocollect_model->con_replace(strip_tags(@$tel[1]));
        //采集时间
        $data['createtime'] = time();
        //echo "58出zu采集测试：<br><pre>";print_r($data);die;
        //判断该条房源是否是经纪人所发房源（匹配经纪人黑名单库）
        $cond = array('tel' => $data['telno1']);
        $check_result = $this->autocollect_model->check_agent_tel($cond, $database = 'db_city');
        if (empty($check_result)) {
          if (strlen($data['telno1']) > 10) {
            //非中介房源,可以入库
            //判断该条房源是否已经采集过了
//					$where  = array('telno1'=>$data['telno1'],'house_title'=>$data['house_title']);
//					$result = $this->autocollect_model->check_rent_house_only($where,$database='db_city');
//					if(empty($result)){
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
              'tel_url' => (string)$data['telno1'],
              'createtime' => $data['createtime'],
              'remark' => $data['remark'],
              'picurl' => $data['picurl'],
              'e_status' => 0,
              'source_from' => 1
            );
            $rel = $this->autocollect_model->add_rent_collect_house($info, $database = 'db_city');
            $hash['hash'] = md5($val);
            $res = $this->autocollect_model->add_collect_url_hash($hash, $database = 'db_city');
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
//					}else{
//						//房源已经入库，请勿重复采集
//						echo "<br><h3>此房源已经入库：</h3><br>标题：".$data['house_title']."<br>链接：".$data['oldurl'];
//						continue;
//					}

          } else {
            //电话号码为空，不能入库
            echo "<br><h3>此房源号码采集不到：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
            $del = array('url' => $value['url']);
            $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
            continue;
          }
        } else {
          //是中介房源,请勿入库
          echo "<br><h3>此房源为中介房源：</h3><br>标题：" . $data['house_title'] . "<br>链接：" . $data['oldurl'];
          $del = array('url' => $value['url']);
          $result = $this->autocollect_model->del_collect_house_lists($del, $database = 'db_city');
          continue;
        }
      }
    }
    echo "成功采集到 " . $i . " 条租房房源！";
  }

  /**
   * 采集365二手房数据
   * 2015.6.13 cc
   */
  public function sell_365_house()
  {

  }

  /**
   * 采集365租房数据
   * 2015.6.13 cc
   */
  public function rent_365_house()
  {

  }
}

/* End of file autocollect_nj.php */
/* Location: ./application/mls_admin/controllers/autocollect_nj.php */
