<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_ganji_base_model");

class Site_ganji_model extends Site_ganji_base_model
{
  private $ganji_city;
  private $ganji;
  private $ganjivip;
  private $facelist;  //朝向
  private $buildlist; //装修

  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
    $this->facelist = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $this->buildlist = array('毛坯' => 1, '简单装修' => 2, '中等装修' => 3, '精装修' => 4, '豪华装修' => 5);
    $this->ganji = $this->site_model->get_site_byid(array('alias' => 'ganji'));
    $this->ganjivip = $this->site_model->get_site_byid(array('alias' => 'ganjivip'));
    $this->ganji = $this->ganji[0];
    $this->ganjivip = $this->ganjivip[0];

    $tpcity = $this->config->item('login_city');
    if ($this->citysite->ganji_city($tpcity)) {
      $this->signatory_info['city_spell'] = $this->citysite->ganji_city($tpcity);
    }
  }

  //检查是否绑定
  public function isbind_ganji($vip)
  {
    $site_id = ($vip == 'ganji') ? $this->ganji['id'] : $this->ganjivip['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $site_signatory = $this->site_model->get_signatoryinfo_byids(array('signatory_id' => $signatory_id, 'site_id' => $site_id, 'status' => 1));
    if (!empty($site_signatory[0])) {
      $data['username'] = $site_signatory[0]['username'];
      $data['password'] = $site_signatory[0]['password'];
      $data['cookie'] = $site_signatory[0]['cookies'];
    } else {
      $data['cookie'] = '';
    }
    return $data;
  }

  /**
   * 列表导入 赶集租房,二手房同一个页面,用$act区别
   * @param string $vip :ganji or ganjivip
   * @param string $act :sell or rent
   * @return number|string
   */
  public function collect_list($vip, $act)
  {
    $login = $this->isbind_ganji($vip);
    $uuid = $this->gj_uuid();
    $cookie = empty($login['cookie']) ? '' : "fangvip_userc_postlist_freeuser=true; ganji_uuid=$uuid ;" . $login['cookie'];

    $num = 0;
    $data_list = array();
    if ($cookie && $vip == 'ganji') {
      $project = array();
      for ($i = 0; ; $i = $i + 10) {
        $listurl = 'http://www.ganji.com/vip/my_post_list.php?source=display_post&page=' . $i;
        $tmpInfo = $this->curl->vget($listurl, $cookie);
        preg_match_all('/id="resume_store_url_[0-9]*".*>(.*)<\/tr>/siU', $tmpInfo, $prj);
        if (count($prj[1]) > 0) {
          $project = array_merge($project, $prj[1]);
        } else {
          break;
        }
      }

      if ($act == 'sell') {
        $type_list = array('二手房出售', '商铺出售', '写字楼出售');
      } else {
        $type_list = array('租房', '合租房', '商铺出租', '写字楼出租');
      }
      foreach ($project as $val) {
        preg_match("/\(房产 - (.*)\)/", $val, $itype);
        if (!in_array(trim($itype[1]), $type_list)) continue;
        preg_match('/title="修改帖子内容" href="(.*)"/siU', $val, $url);
        preg_match('/<a href="(.*)" target="_blank" gjalog/siU', $val, $infourl);
        preg_match('/detail@atype=click">(.*)<\/a>/siU', $val, $title);
        preg_match('/title="发布时间：(.*)"/siU', $val, $releasetime);
        preg_match('/data-category-id="([0-9]*)"/siU', $val, $cid);
        preg_match('/data-major-id="([0-9]*)"/siU', $val, $mcid);

        $data = array();
        $data['source'] = 0;    //5=>ganji or 6=>ganjivip
        $data['url'] = empty($url[1]) ? '' : trim($url[1]) . '&mcid=' . $mcid[1] . '&cid=' . $cid[1];  //编辑链接
        $data['infourl'] = empty($infourl[1]) ? '' : trim($infourl[1]);  //详情链接
        $data['title'] = trim($title[1]) . ' [' . $itype[1] . ']';    //标题
        $data['des'] = '';
        $data['releasetime'] = trim($releasetime[1]) ? strtotime($releasetime[1]) : ''; //发布时间
        $data['city_spell'] = $this->signatory_info['city_spell'];
        $data['signatory_id'] = $this->signatory_info['signatory_id'];
        $data['site_id'] = $this->ganji['id'];
        $data_list[] = $data;
        $num++;
      }
      $res = $this->autocollect_model->add_list_indata($act, $data_list);
      return $num;
    } elseif ($cookie && $vip == 'ganjivip') {
      $project = array();
      if ($act == 'sell') {
        $type = array(1 => '民宅综合', 4 => '二手', 5 => '厂房', 6 => '商铺', 7 => '写字楼');
        $atype = array('二手', '出售');
        $stype = '出售';
      } else {
        $type = array(1 => '民宅综合', 3 => '出租', 5 => '厂房', 6 => '商铺', 7 => '写字楼');
        $atype = array('出租', '合租');
        $stype = '出租';
      }

      $isback = array();  //处理for循环超出分页,依然读到列表
      foreach ($type as $k => $op) {
        $con = $this->curl->vget('http://fangvip.ganji.com/index.php?bizScope=' . $k, $cookie);
        for ($i = 1; ; $i++) {
          $tempurl = 'http://fangvip.ganji.com/post_list.php?pageSize=80&page=' . $i;
          $tmpInfo = $this->curl->vget($tempurl, $cookie);
          preg_match_all('/data-role="item"(.*)<\/tr>/siU', $tmpInfo, $prj);
          if (count($prj[1]) > 0) {
            preg_match('/data-id="([0-9]*)"/siU', $prj[1][0], $temp_end);
            if (in_array($temp_end[1], $isback)) {
              break;
            } else {
              $project = array_merge($project, $prj[1]);
              $isback[] = $temp_end[1];
            }
          } else {
            break;
          }
        }
      }
      foreach ($project as $val) {
        preg_match('/data-role="edit" href="(.*)">编辑/siU', $val, $url);
        preg_match('/<p>\[(.*)\].*<a.*href="(.*)" target="_blank">(.*)<\/a>/siU', $val, $infourl);
        preg_match('/<i class="ico-general"><\/i>(.*)<\/span>/siU', $val, $des);
        preg_match('/<em class="sput-time">(.*)<\/em><em class="sput-time">(.*)<\/p>/siU', $val, $putime);
        if (in_array($infourl[1], $atype) || strpos($infourl[1], $stype)) {
          $data = array();
          $data['source'] = 0;
          $data['url'] = empty($url[1]) ? '' : trim($url[1]);              //编辑链接
          $data['infourl'] = empty($infourl[2]) ? '' : trim($infourl[2]);  //详情链接
          $data['title'] = trim($infourl[3]) . ' [' . $infourl[1] . ']';    //标题
          $data['des'] = strip_tags($des[1]) . ' ' . strip_tags($putime[1]) . ' ' . strip_tags($putime[2]);
          $data['releasetime'] = time(); //发布时间,赶集VIP时间没有年份
          $data['city_spell'] = $this->signatory_info['city_spell'];
          $data['signatory_id'] = $this->signatory_info['signatory_id'];
          $data['site_id'] = $this->ganjivip['id'];
          $data_list[] = $data;
          $num++;
        }
      }
      $res = $this->autocollect_model->add_list_indata($act, $data_list);
      return $num;
    } else {
      echo 'no cookie';
    }
  }

  //赶集普通版 详情页面导入:出售
  public function collect_sell_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $login = $this->isbind_ganji('ganji');
    $cid = $this->input->get('cid');  //赶集url参数cid
    $id = $this->input->get('id'); //赶集url参数 id
    $mcid = $this->input->get('mcid'); //赶集url参数 mcid
    $city_spell = $this->signatory_info['city_spell'];
    $orientation = $this->facelist;
    $serverco = $this->buildlist;
    $list = array('二手房出售' => 1, '商铺出售' => 3, '写字楼出售' => 4);

    if (empty($login['cookie'])) {
      return false;
    } else {
      preg_match('/http:\/\/(.*).ganji.com/siU', $url, $city_gj);
      $edit_url = "http://www.ganji.com/pub/pub.php?act=update&method=load&cid=$cid&mcid=$mcid&id=$id&domain=" . $city_gj[1];//赶集编辑页跳转,重新拼装
      $edtInfo = $this->curl->vget($edit_url, $login['cookie']);
      $tmpInfo = $this->curl->vget($infourl);
      preg_match('/<span class="xh">2<\/span>(.*)<\/li>/siU', $edtInfo, $mytype);
      $mytype = trim(strip_tags($mytype[1]));
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = array();
    preg_match('/<div class="cont-box pics">(.*)<\/div>/siU', $tmpInfo, $picdiv);
    preg_match_all('/<img.*src="(.*)" \/>/siU', $picdiv[1], $pic); //图片
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }
    $house_info['picurl'] = implode('*', $image);

    if ($the_type == 3) { //商铺
      preg_match('/id="id_loupan_auto".*value="(.*)" name="loupan_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_area" value="(.*)" maxlength/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" value="(.*)" maxlength=/siU', $edtInfo, $price);
      preg_match('/id="title" type="text" value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } elseif ($the_type == 4) { //写字楼
      preg_match('/id="id_loupan_auto".*value="(.*)" name="house_name"/siU', $edtInfo, $house_name);
      preg_match('/name="area" value="(.*)" maxlength/siU', $edtInfo, $area);
      preg_match('/name="price" value="(.*)" maxlength/siU', $edtInfo, $price);
      preg_match('/id="title" type="text"value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //写字楼名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } else { //住宅 or 别墅
      preg_match('/type="hidden" class="select-input" value="([0-9])" name="fang_xing"/siU', $edtInfo, $dtype);
      preg_match('/id="id_xiaoqu" type="text" class="input-style" value="(.*)" name="xiaoqu"/siU', $edtInfo, $house_name);
      preg_match('/name="huxing_shi" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $room);
      preg_match('/name="huxing_ting" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $hall);
      preg_match('/name="huxing_wei" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $toilet);
      preg_match('/id="id_area" value="(.*)"/siU', $edtInfo, $area);
      preg_match('/name="ceng" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/name="ceng_total" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $totalfloor);
      preg_match('/input type="hidden" class="select-input" value="([0-9]*)" name="chaoxiang"/siU', $edtInfo, $forward);
      preg_match('/input type="hidden" class="select-input" value="([0-9])" name="zhuangxiu"/siU', $edtInfo, $build);
      preg_match('/name="price" value="(.*)"><\/span>/siU', $edtInfo, $price);
      preg_match('/name="title" type="text" class="input-style" id="title".*value="(.*)"  \/>/siU', $edtInfo, $title);

      $house_info['sell_type'] = ($dtype[1] == 7) ? 2 : 1;  //1住宅2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      //1东2南3西4北5东西6南北7东南8东北9西南10西北*赶集
      //array('东'=>1,'东南'=>2,'南'=>3,'西南'=>4,'西'=>5,'西北'=>6,'北'=>7,'东北'=>8,'东西'=>9,'南北'=>10); *我们
      $face_arr = array('1' => 1, '7' => 2, '2' => 3, '9' => 4, '3' => 5, '10' => 6, '4' => 7, '8' => 8, '5' => 9, '6' => 10);
      $face = trim($forward[1]) ? trim($forward[1]) : 3;
      $house_info['forward'] = $face_arr[$face];    //朝向
      //1豪华装修 2精装修 3中等装修 4简单装修 5毛坯
      //array('毛坯'=>1,'简单装修'=>2,'中等装修'=>3,'精装修'=>4,'豪华装修'=>5);
      $serverco = array('5' => 1, '4' => 2, '3' => 3, '2' => 4, '1' => 5);
      $serverco_temp = trim($build[1]) ? trim($build[1]) : 1;
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea id="id_description" name="description".*>(.*)<\/textarea>/siU', $edtInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //赶集VIP 详情页面导入:出售
  public function collect_sell_info_vip()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $find = $this->isbind_ganji('ganjivip');
    $city_spell = $this->signatory_info['city_spell'];
    $orientation = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $serverco = array('毛坯' => 1, '简单装修' => 2, '中等装修' => 3, '精装修' => 4, '豪华装修' => 5);
    $list = array('修改商铺出售' => 3, '修改写字楼出售' => 4, '修改厂房/仓库/土地' => 5);

    if (empty($find['cookie'])) {
      return false;
    } else {
      $edtInfo = $this->curl->vget('http://fangvip.ganji.com/' . $url, $find['cookie']);
      $tmpInfo = $this->curl->vget($infourl);
      preg_match('/房产帮帮首页<\/a> -(.*)<\/h1>/siU', $edtInfo, $mytype);
      $mytype = trim($mytype[1]);
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = array();
    preg_match('/<div class="cont-box pics">(.*)<\/div>/siU', $tmpInfo, $picdiv);
    preg_match_all('/<img.*src="(.*)" \/>/siU', $picdiv[1], $pic); //图片
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }
    $house_info['picurl'] = implode('*', $image);

    if ($the_type == 3) { //商铺
      preg_match('/id="loupan_name_field".*value="(.*)" name="loupan_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_ceng" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="([0-9]*)"/siU', $edtInfo, $total_floor);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>/siU', $edtInfo, $build);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);
      preg_match('/<input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $total_floor[1]; //总楼层
      $house_info['serverco'] = $serverco[$build[1]];   //装修
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } elseif ($the_type == 4) { //写字楼
      preg_match('/id="loupan_name_field".*value="(.*)" name="house_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_ceng" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="([0-9]*)"/siU', $edtInfo, $total_floor);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>/siU', $edtInfo, $build);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);
      preg_match('/<input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $total_floor[1]; //总楼层
      $house_info['serverco'] = $serverco[$build[1]];   //装修
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } elseif ($the_type == 5) { //厂房/仓库/车库
      preg_match('/id="radio_house_type([0-9])" checked="checked"/siU', $edtInfo, $dtype);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);
      preg_match('/<input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      if ($dtype[1] == 1) {
        $house_info['sell_type'] = 6; //仓库
      } elseif ($dtype[1] == 2) {
        $house_info['sell_type'] = 7; //车库
      } else {
        $house_info['sell_type'] = 5; //厂房
      }
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } else { //住宅 or 别墅
      preg_match('/id="id_fang_xing".*selected="selected">(.*)<\/option>.*name="zhuangxiu"/siU', $edtInfo, $dtype);
      preg_match('/id="id_xiaoqu" type="text"  value="(.*)"/siU', $edtInfo, $house_name);
      preg_match('/name="huxing_shi" value="(.*)" class="text"/siU', $edtInfo, $room);
      preg_match('/name="huxing_ting" value="(.*)" class="text"/siU', $edtInfo, $hall);
      preg_match('/name="huxing_wei" value="(.*)" class="text"/siU', $edtInfo, $toilet);
      preg_match('/id="id_area" maxlength="7" name="area" class="text" value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_ceng" value="(.*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="(.*)"/siU', $edtInfo, $totalfloor);
      preg_match('/id="id_chaoxiang".*selected="selected">(.*)<\/option>.*name="bid_structure"/siU', $edtInfo, $forward);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>.*id="tip_span_house"/siU', $edtInfo, $build);
      preg_match('/id="id_price" name="price".*value="(.*)".*id="trade_price_type"/siU', $edtInfo, $price);
      preg_match('/input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = ($dtype[1] == '别墅') ? 2 : 1;  //1住宅2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $face = trim($forward[1]) ? trim($forward[1]) : '南';
      $house_info['forward'] = $orientation[$face];    //朝向
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea name="description"  id="id_description".*>(.*)<\/textarea>/siU', $edtInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //赶集普通版 详情页面导入:出租
  public function collect_rent_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $cid = $this->input->get('cid');  //赶集url参数cid
    $id = $this->input->get('id'); //赶集url参数 id
    $mcid = $this->input->get('mcid'); //赶集url参数 mcid
    $login = $this->isbind_ganji('ganji');
    $city_spell = $this->signatory_info['city_spell'];
    $orientation = $this->facelist;
    $serverco = $this->buildlist;
    $list = array('合租房' => 1, '租房' => 2, '商铺出租' => 3, '写字楼出租' => 4);

    if (empty($login['cookie'])) {
      return false;
    } else {
      preg_match('/http:\/\/(.*).ganji.com/siU', $url, $city_gj);
      $edit_url = "http://www.ganji.com/pub/pub.php?act=update&method=load&cid=$cid&mcid=$mcid&id=$id&domain=" . $city_gj[1];//赶集编辑页跳转,重新拼装
      $edtInfo = $this->curl->vget($edit_url, $login['cookie']);
      $tmpInfo = $this->curl->vget($infourl);
      preg_match('/<span class="xh">2<\/span>(.*)<\/li>/siU', $edtInfo, $mytype);
      $mytype = trim(strip_tags($mytype[1]));
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = array();
    preg_match('/<div class="cont-box pics">(.*)<\/div>/siU', $tmpInfo, $picdiv);
    preg_match_all('/<img.*src="(.*)"/siU', $picdiv[1], $pic); //图片
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }
    $house_info['picurl'] = implode('*', $image);

    if ($the_type == 3) { //商铺
      preg_match('/id="id_loupan_auto".*value="(.*)" name="loupan_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_area" value="(.*)" maxlength/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" value="(.*)" maxlength/siU', $edtInfo, $price);
      preg_match('/id="id_price_type".*value="([0-9])" selected="selected"/siU', $edtInfo, $price_type);
      preg_match('/id="title" type="text" value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      if ($price_type[1] == 1) {
        $house_info['price'] = $price[1] * 30 * $house_info['buildarea'];  //元/平米·天
      } elseif ($price_type[1] == 3) {
        $house_info['price'] = $price[1] * $house_info['buildarea'];  //元/平米.月
      } else {
        $house_info['price'] = $price[1];
      }
      $house_info['title'] = trim($title[1]);    //标题
    } elseif ($the_type == 4) { //写字楼
      preg_match('/id="id_loupan_auto" type="text" class="input-style" value="(.*)" name="house_name"/siU', $edtInfo, $house_name);
      preg_match('/name="area" value="(.*)" maxlength="6"/siU', $edtInfo, $area);
      preg_match('/name="price" value="(.*)" maxlength/siU', $edtInfo, $price);
      preg_match('/id="id_price_type".*value="([0-9])" selected="selected"/siU', $edtInfo, $price_type);
      preg_match('/id="title" type="text"value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = strip_tags($house_name[1]); //写字楼名称 strip_tags()
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['title'] = trim($title[1]); //标题
      $house_info['price'] = ($price_type[1] == 1) ? $price[1] * 30 * $house_info['buildarea'] : $price[1];  //租金
    } else { //住宅 or 别墅
      preg_match('/type="hidden" class="select-input" value="([0-9])" name="fang_xing"/siU', $edtInfo, $dtype);
      preg_match('/id="id_xiaoqu" type="text" class="input-style" value="(.*)" name="xiaoqu"/siU', $edtInfo, $house_name);
      preg_match('/name="huxing_shi" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $room);
      preg_match('/name="huxing_ting" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $hall);
      preg_match('/name="huxing_wei" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $toilet);
      preg_match('/id="id_area" value="(.*)"/siU', $edtInfo, $area);
      preg_match('/name="ceng" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/name="ceng_total" maxlength="2" value="([0-9]*)"/siU', $edtInfo, $totalfloor);
      preg_match('/input type="hidden" class="select-input" value="([0-9]*)" name="chaoxiang"/siU', $edtInfo, $forward);
      preg_match('/input type="hidden" class="select-input" value="([0-9])" name="zhuangxiu"/siU', $edtInfo, $build);
      preg_match('/name="price" value="(.*)"><\/span>/siU', $edtInfo, $price);
      preg_match('/name="title" type="text" class="input-style" id="title".*value="(.*)"  \/>/siU', $edtInfo, $title);

      $house_info['sell_type'] = ($dtype[1] == 7) ? 2 : 1;  //1住宅2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      //1东2南3西4北5东西6南北7东南8东北9西南10西北*赶集
      //array('东'=>1,'东南'=>2,'南'=>3,'西南'=>4,'西'=>5,'西北'=>6,'北'=>7,'东北'=>8,'东西'=>9,'南北'=>10); *我们
      $face_arr = array('1' => 1, '7' => 2, '2' => 3, '9' => 4, '3' => 5, '10' => 6, '4' => 7, '8' => 8, '5' => 9, '6' => 10);
      $face = trim($forward[1]) ? trim($forward[1]) : 3;
      $house_info['forward'] = $face_arr[$face];    //朝向
      //1豪华装修 2精装修 3中等装修 4简单装修 5毛坯
      //array('毛坯'=>1,'简单装修'=>2,'中等装修'=>3,'精装修'=>4,'豪华装修'=>5);
      $serverco = array('5' => 1, '4' => 2, '3' => 3, '2' => 4, '1' => 5);
      $serverco_temp = trim($build[1]) ? trim($build[1]) : 1;
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['price'] = empty($price[1]) ? '' : $price[1];  //租金
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea id="id_description" name="description".*>(.*)<\/textarea>/siU', $edtInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //赶集VIP 详情页面导入:出租
  public function collect_rent_info_vip()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $find = $this->isbind_ganji('ganjivip');
    $city_spell = $this->signatory_info['city_spell'];
    $orientation = $this->facelist;
    $serverco = $this->buildlist;
    $list = array('修改合租房' => 1, '修改租房' => 2, '修改商铺出租' => 3, '修改写字楼出租' => 4, '修改厂房/仓库/土地' => 5);

    if (empty($find['cookie'])) {
      return false;
    } else {
      $edtInfo = $this->curl->vget('http://fangvip.ganji.com/' . $url, $find['cookie']);
      $tmpInfo = $this->curl->vget($infourl);
      preg_match('/房产帮帮首页<\/a> -(.*)<\/h1>/siU', $edtInfo, $mytype);
      $mytype = trim($mytype[1]);
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = array();
    preg_match('/<div class="cont-box pics">(.*)<\/div>/siU', $tmpInfo, $picdiv);
    preg_match_all('/<img.*src="(.*)"/siU', $picdiv[1], $pic); //图片
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }
    $house_info['picurl'] = implode('*', $image);

    if ($the_type == 3) { //商铺
      preg_match('/id="loupan_name_field".*value="(.*)" name="loupan_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_ceng" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="([0-9]*)"/siU', $edtInfo, $total_floor);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>/siU', $edtInfo, $build);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);
      preg_match('/<input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $total_floor[1]; //总楼层
      $house_info['serverco'] = $serverco[$build[1]];   //装修
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
    } elseif ($the_type == 4) { //写字楼
      preg_match('/id="loupan_name_field".*value="(.*)" name="house_name"/siU', $edtInfo, $house_name);
      preg_match('/id="id_ceng" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="([0-9]*)"/siU', $edtInfo, $total_floor);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>/siU', $edtInfo, $build);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/<input type="text".*value="(.*)" name="title"/siU', $edtInfo, $title);
      preg_match('/id="id_price_type".*script_index="([0-9])" selected="selected"/siU', $edtInfo, $pay);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);

      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = strip_tags($house_name[1]); //楼盘名称 strip_tags()
      $house_info['forward'] = 1; //朝向ganji写字楼没有
      $house_info['floor'] = $floor[1];
      $house_info['totalfloor'] = $total_floor[1];
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['buildarea'] = $area[1];
      $house_info['title'] = trim($title[1]);
      $house_info['price'] = ($pay[1] == 1) ? $price[1] * 30 : $price[1];
    } elseif ($the_type == 5) { //厂房/仓库/车库
      preg_match('/id="radio_house_type([0-9])" checked="checked"/siU', $edtInfo, $dtype);
      preg_match('/id="id_area" name="area".*value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_price" name="price" class="text" value="(.*)"/siU', $edtInfo, $price);
      preg_match('/id="id_price_type".*script_index="([0-9])" selected="selected"/siU', $edtInfo, $price_type);
      preg_match('/<input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      if ($dtype[1] == 1) {
        $house_info['sell_type'] = 6; //仓库
      } elseif ($dtype[1] == 2) {
        $house_info['sell_type'] = 7; //车库
      } else {
        $house_info['sell_type'] = 5; //厂房
      }
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['price'] = ($price_type[1] == 1) ? $price[1] * 30 * $house_info['buildarea'] : $price[1];  //总价
    } else { //住宅 or 别墅
      preg_match('/id="id_fang_xing".*script_index="([0-9])" selected="selected">.*id="tip_span_house"/siU', $edtInfo, $dtype);
      preg_match('/id="id_xiaoqu" type="text"  value="(.*)"/siU', $edtInfo, $house_name);
      preg_match('/name="huxing_shi" value="([0-9]*)"/siU', $edtInfo, $room);
      preg_match('/name="huxing_ting" value="([0-9]*)"/siU', $edtInfo, $hall);
      preg_match('/name="huxing_wei" value="([0-9]*)"/siU', $edtInfo, $toilet);
      preg_match('/id="id_ceng" value="([0-9]*)"/siU', $edtInfo, $floor);
      preg_match('/id="id_ceng_total" value="([0-9]*)"/siU', $edtInfo, $total_floor);
      preg_match('/id="id_area" maxlength="5" name="area" class="text" value="(.*)"/siU', $edtInfo, $area);
      preg_match('/id="id_chaoxiang".*selected="selected">(.*)<\/option>.*select>/siU', $edtInfo, $forward);
      preg_match('/id="id_zhuangxiu".*selected="selected">(.*)<\/option>.*id="tip_span_house"/siU', $edtInfo, $build);
      preg_match('/id="id_price" name="price".*value="(.*)"/siU', $edtInfo, $price);
      preg_match('/input type="text"  value="(.*)" name="title"/siU', $edtInfo, $title);

      if ($the_type == 2 && $dtype[1] == '7') {
        $house_info['sell_type'] = 2;  //别墅
      } else {
        $house_info['sell_type'] = 1;  //类型,合租没有别墅
      }
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称 strip_tags()
      $house_info['room'] = $room[1];        //室
      $house_info['hall'] = $hall[1];        //厅
      $house_info['toilet'] = $toilet[1];    //卫
      $house_info['buildarea'] = $area[1];   //面积
      $house_info['floor'] = $floor[1];      //楼层
      $house_info['totalfloor'] = $total_floor[1];       //总楼层
      $face = trim($forward[1]) ? trim($forward[1]) : '南';
      $house_info['forward'] = $orientation[$face];      //朝向
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['price'] = empty($price[1]) ? '' : $price[1];  //租金
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea name="description"  id="id_description".*>(.*)<\/textarea>/siU', $edtInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //赶集普通 下架
  public function esta_delete($house_id, $act, $param = array())
  {
    $this->load->model('group_publish_model');
    $site_id = $this->ganji['id'];
    $signatory_id = empty($param['signatory_id']) ? $this->signatory_info['signatory_id'] : $param['signatory_id'];
    $esta_house_info = $this->group_publish_model->get_publish_by_site_id($site_id, $house_id, $act, $signatory_id);
    $publish_id = $esta_house_info[0]['publish_id'];

    //$login = $this->isbind_ganji('ganji');
    $login = $this->site_mass_model->isbind_site_by_id($site_id, $signatory_id);  //绑定帐号信息

    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $paramlog = array(
      'house_id' => $house_id,
      'signatory_id' => $signatory_id,
      'site_id' => $site_id,
      'block_id' => $house_info['block_id'],
      'sell_type' => ($act == 'sell') ? 1 : 2, //1出售,2出租
      'ymd' => time(),
      'username' => empty($login['username']) ? '' : $login['username']
    );
    if (!empty($param['queue_id'])) {
      $query = array('form_name' => 'group_queue_demon', 'where' => array('id' => $param['queue_id']));
      $group = $this->get_data($query, 'dbback_city');
      $paramlog['dealtime'] = empty($group) ? 0 : $group[0]['createtime'];
    }
    $paramlog['type'] = 2; //1成功,2失败
    $paramlog['info'] = '目标站点下架失败';

    $result = array('state' => 'failed');
    if ($login['cookies']) {
      $post_url = 'http://www.ganji.com/vip/ajax/rm_post.php';
      $post_fields = 'one=' . $publish_id . '%2C0&permanently=0';
      $tmpInfo = $this->curl->vpost($post_url, $post_fields, $login['cookies']);  //目标站点 删除

      //true 删除成功关键字
      if (false != strpos($tmpInfo, 'true')) {
        $result['state'] = 'success';

        $paramlog['type'] = 1; //1成功,2失败
        $paramlog['info'] = '下架成功';
      }
      $bool = $this->group_publish_model->del_info_by_publish_id($publish_id, $act);  //数据库 删除
    }
    //重新发布 不加入下架日志
    if (empty($param['nolog'])) {
      $this->group_publish_model->add_esta_log($paramlog);
    }
    return $result;
  }

  //赶集VIP 下架
  //public function esta_delete_vip($house_id, $act, $nolog=0, $queue_id=0, $signatory_id=0 )
  public function esta_delete_vip($house_id, $act, $param = array())
  {
    $this->load->model('group_publish_model');
    $site_id = $this->ganjivip['id'];
    $signatory_id = empty($param['signatory_id']) ? $this->signatory_info['signatory_id'] : $param['signatory_id'];
    $esta_house_info = $this->group_publish_model->get_publish_by_site_id($site_id, $house_id, $act, $signatory_id);

    $publish_id = $esta_house_info[0]['publish_id'];
    $remark = $esta_house_info[0]['remark'];
    $tbl = ($act == 'sell') ? 1 : 2;

    //$login = $this->isbind_ganji('ganjivip');
    $login = $this->site_mass_model->isbind_site_by_id($site_id, $signatory_id);  //绑定帐号信息

    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $paramlog = array(
      'house_id' => $house_id,
      'signatory_id' => $signatory_id,
      'site_id' => $site_id,
      'block_id' => $house_info['block_id'],
      'sell_type' => ($act == 'sell') ? 1 : 2, //1出售,2出租
      'ymd' => time(),
      'username' => empty($login['username']) ? '' : $login['username']
    );
    if (!empty($param['queue_id'])) {
      $query = array('form_name' => 'group_queue_demon', 'where' => array('id' => $param['queue_id']));
      $group = $this->get_data($query, 'dbback_city');
      $paramlog['dealtime'] = empty($group) ? 0 : $group[0]['createtime'];
    }
    $paramlog['type'] = 2; //1成功,2失败
    $paramlog['info'] = '目标站点下架失败';

    $result = array('state' => 'failed');
    if ($login['cookies'] && $publish_id) {
      $cookie = 'fangvip_userc_postlist_freeuser=true; ' . $login['cookies'];
      $url = 'http://fangvip.ganji.com/post_list.php?do=deletePost&ids=' . $remark;
      $tmpInfo = $this->curl->vget($url, $cookie, '', 1);  //目标站点 删除

      if (strpos($tmpInfo, '%E5%88%A0%E9%99%A4%E5%A4%B1%E8%B4%A5')) {
        $prem_url = 'http://fangvip.ganji.com/premier.php?do=cancelPremierPost&ids=' . $remark;
        $premInfo = $this->curl->vget($prem_url, $cookie, '', 1); //先取消推广,推广状态下无法删除
        $tmpInfo = $this->curl->vget($url, $cookie, '', 1); //取消推广后 删除
      }

      //%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F 删除成功关键字
      if (false != strpos($tmpInfo, '%E5%88%A0%E9%99%A4%E6%88%90%E5%8A%9F')) {
        $result['state'] = 'success';

        $paramlog['type'] = 1; //1成功,2失败
        $paramlog['info'] = '下架成功';
      }
      $bool = $this->group_publish_model->del_info_by_publish_id($publish_id, $act);  //数据库 删除

      $where_del = array('signatory_id' => $signatory_id, 'house_id' => $house_id, 'tbl' => $tbl, 'site_id' => $site_id);
      $del = $this->group_publish_model->delete_refresh_time($where_del);  //预约刷新 删除
      $where_msg = array('signatory_id' => $signatory_id, 'house_id' => $house_id, 'tbl' => $tbl, 'days != ' => 0);
      $msg_refresh = $this->group_publish_model->checked_refresh_msg($where_msg); //预约刷新 修改查看状态
    }
    if (empty($param['nolog'])) {
      $this->group_publish_model->add_esta_log($paramlog); //重新发布 不加入下架日志
    }
    return $result;
  }

  //赶集VIP 刷新
  public function refresh_vip($house_id, $act)
  {
    $site_id = $this->ganjivip['id'];
    $house_info = $this->group_publish_model->get_publish_detail($site_id, $house_id, $act);
    $id = $house_info['id'];
    $remark = $house_info['remark'];
    $publish_id = $house_info['publish_id'];
    $publish_url = $house_info['publish_url'];
    $refresh_times = $house_info['refresh_times'] + 1;
    preg_match('/\/fang([0-9]*)\//siU', $publish_url, $typenum);
    $type = empty($typenum[1]) ? 1 : $typenum[1]; //1租房 5二手房
    //post_type: 1出租 3合租 5出售 6商铺 8写字楼 11厂房
    //bizScope:  1综合 3出租 4出售 6商铺 7写字楼 5厂房
    $bizList = array(5 => 4, 6 => 6, 8 => 7, 11 => 5, 1 => 3);
    $biz = $bizList[$type];

    try {
      $login = $this->isbind_ganji('ganjivip');
      if ($login['cookie']) {
        $cookie = 'fangvip_userc_postlist_freeuser=true;' . $login['cookie'];
        $bizs_refer = 'http://fangvip.ganji.com/index.php?bizScope=' . $biz;
        $bizsInfo = $this->curl->vget($bizs_refer, $cookie);

        $post_url = 'http://fangvip.ganji.com/premier.php?do=ajaxNowRefresh';
        $post_fields = 'house_id=' . $publish_id . '&type=' . $type;
        $tmpInfo = $this->curl->vpost($post_url, $post_fields, $cookie, 'http://fangvip.ganji.com/premier.php');  //尝试 刷新
        //未推广
        if (strpos($tmpInfo, '\u672a\u63a8\u5e7f')) {
          $prem_url = 'http://fangvip.ganji.com/premier.php?do=premierPost&ids=' . $remark . '&ref=729609124';
          $premInfo = $this->curl->vget($prem_url, $cookie, 'http://fangvip.ganji.com/', 1); //推广
          $tmpInfo = $this->curl->vpost($post_url, $post_fields, $cookie, 'http://fangvip.ganji.com/premier.php'); //推广后 刷新
        }
        //立即刷新成功
        if (strpos($tmpInfo, '\u7acb\u5373\u5237\u65b0\u6210\u529f')) {
          $param = array('refresh_times' => $refresh_times, 'updatetime' => time());
          $this->group_publish_model->update_data($id, $param, $act);
          $result = array('state' => 'success');

          //张建统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝
          $this->load->model('group_refresh_model');
          $this->group_refresh_model->info_count($this->signatory_info, 4);
          //王欣统计
          $ref_param = array('signatory_id' => $this->signatory_info['signatory_id'],
            'house_id' => $house_id,
            'site_id' => $site_id,
            'tbl' => ($act == 'sell') ? 1 : 2,  //1出售 2出租
            'ymd' => date('Y-m-d'),
            'his' => '',
            'days' => 0,  //刷新方案 0手动
            'refreshtime' => time(),
            'createtime' => time(),
            'status' => 1,  //1成功 2 失败
            'msg' => '',  //失败原因
            'username' => empty($login['username']) ? '' : $login['username']
          );
          $this->group_refresh_model->add_message_log($ref_param);
        } else {
          //刷新失败
          preg_match('/{"ok".*}}/siU', $tmpInfo, $jsondata);
          $arrdata = json_decode($jsondata[0], true);
          $info = empty($arrdata['msg']) ? '刷新失败' : $arrdata['msg'];
          $result = array('state' => 'failed', 'info' => $info);
        }
      }
      return $result;
    } catch (Exception $e) {
      return array('state' => 'failed', 'info' => '刷新超时!!!');
    }
  }

  //上传图片到 赶集
  public function upload_image($url, $signatory_id, $alias)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site($alias, $signatory_id);
    if ($login['cookies'] && !empty($finalname)) {
      $post_url = 'http://image.ganji.com/upload.php';
      $post_fielde = array(
        'flashvars' => '@' . $finalname,
        'wmode' => 'opaque',
        'movie' => 'http://sta.ganji.com/public/swf/swfupload-2.swf?311',
        'quality' => 'high',
        'allowScriptAccess' => 'always'
      );
      $tmpInfo = $this->curl->vpost($post_url, $post_fielde, $login['cookies']);
      preg_match('/{"url":"(.*)",/siU', $tmpInfo, $pigname);
      $pigname[1] = stripslashes($pigname[1]);
      @unlink($finalname);
      if (!empty($pigname[1]))
        return $pigname[1];
    }
    return 0;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($vip, $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type', TRUE);
    $keyword = trim($keyword);
    $city = $this->signatory_info['city_spell'];

    $list = array();
    $login = $this->isbind_ganji($vip);
    if ($login['cookie']) {
      $cookie = $login['cookie'];
      $hash = '&__hash__=KBLfBtERoQ58RY8F8v0jZzsmmg4xCcwFXmwdI1dz7BRHhRgw3VFK1SrTEoSJLYOX';
      if (in_array($sell_type, array(3, 4))) { //商铺 写字楼
        $url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=loupan_name_autocomplete&city=' . $city . '&key=' . urlencode($keyword) . $hash;
        $tmpInfo = $this->curl->vget($url, $cookie);
        $tmpInfo = json_decode($tmpInfo, true);
        if (!empty($tmpInfo)) {
          $getInfo = $this->curl->vget('http://www.ganji.com/pub/pub.php?act=pub&method=load&cid=7&mcid=26&domain=' . $city . '&_pdt=fang');
          foreach ($tmpInfo as $key => $val) {
            if ($key == 10) break;
            preg_match('/option value="([0-9]*)"  script_index=\'' . $val['d_id'] . '\'/', $getInfo, $district_id);  //匹配 district_id
            $post_fields = 'district_id=' . $district_id[1] . '&with_all_option=1' . $hash;
            $conInfo = $this->curl->vpost('http://www.ganji.com/ajax.php?module=streetOptions', $post_fields, $cookie);
            preg_match('/\["([0-9]*)","[^,]*",false,"' . $val['s_id'] . '"\]/siU', $conInfo, $street_id);  //匹配 street_id
            $list[] = array(
              'label' => $val['name'],
              'address' => $val['address'],
              'district' => ($vip == 'ganji') ? $district_id[1] : $val['d_id'] . ',' . $val['d_n'],
              'street' => ($vip == 'ganji') ? $street_id[1] : $val['s_id'] . ',' . $val['s_n'],
              'id' => $val['x_id']
            );
          }
        }
      } else { //民宅
        $url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=xiaoqu_name_autocomplete&city=' . $city . '&key=' . urlencode($keyword) . $hash;
        $tmpInfo = $this->curl->vget($url, $cookie);
        $tmpInfo = json_decode($tmpInfo, true);
        if (!empty($tmpInfo)) {
          foreach ($tmpInfo as $key => $val) {
            if ($key == 10) break;
            $post_url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=XiaoquGetInfoByIdV2';
            $post_fields = 'name=' . $val['x_id'] . '&xiaoqu=' . $val['name'] . '&domain=' . $city . $hash;
            $conInfo = $this->curl->vpost($post_url, $post_fields, $cookie);
            preg_match('/({"id":.*})/', $conInfo, $item);
            $conInfo = json_decode($item[1], true);
            $list[] = array(
              'label' => $val['name'],
              'address' => $val['address'],
              'district' => ($vip == 'ganji') ? $conInfo['district_info']['id'] : $conInfo['district_id'] . ',' . $conInfo['district_info']['name'],
              'street' => ($vip == 'ganji') ? $conInfo['street_info']['id'] : $conInfo['street_id'] . ',' . $conInfo['street_info']['name'],
              'id' => $val['x_id']
            );
          }
        }
      }
    }
    return $list;
  }

  //绑定帐号
  public function save_bind()
  {
    $vip = $this->input->get_post('site_id');
    $username = $this->input->get_post('username');
    $password = $this->input->get_post('password');
    $checkget = $this->input->get_post('checkcode');
    $ganjisessid = $this->input->get_post('ganjisessid');
    $signatory_info = $this->signatory_info;
    $site_id = ($vip == 'ganji') ? $this->ganji['id'] : $this->ganjivip['id'];

    if ($username && $password) {
      $uuid = $this->gj_uuid();
      $post_fields['checkCode'] = $checkget ? $checkget : '';  //重要
      $cookies = $ganjisessid ? "ganji_uuid=$uuid ;GANJISESSID=$ganjisessid; " : "ganji_uuid=$uuid ;";

      $url = 'https://passport.ganji.com/login.php?callback=jQuery182020571666893339668_' . time() . '000'
        . '&username=' . $username . '&password=' . $password . '&checkCode=' . $post_fields['checkCode'] . '&setcookie=14&second=&parentfunc='
        . '&redirect_in_iframe=&next=%2F&__hash__=i7lE7s18pjxEuxHa%2F6nHZHwZf9znb0jeKLT%2FfinzNQOvgMIyDkfuLfbtciVepc77&_=' . (time() + 7) . '000';
      $tmpInfo = $this->curl->vget($url, $cookies, 'passport.ganji.com', 1);
      preg_match('/"type":"(.*)"/siU', $tmpInfo, $checkcode);

      if (!empty($checkcode[1]) && $checkcode[1] == 'need_captcha' && empty($checkget)) {
        //获取GANJISESSID
        $codeInfo = $this->curl->vget('http://fangvip.ganji.com', '', '', 1);
        preg_match("/GANJISESSID=(.*);/siU", $codeInfo, $ganjisessid);
        return array('error' => 'gjcode', 'ganjisessid' => $ganjisessid[1]);
      } else {
        $pos = strpos($tmpInfo, 'user_id');
        if ($pos > 0)  //登录成功
        {
          preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo, $matches);
          $cookie = implode(';', $matches[1]);
          $tmpInfo = urldecode($tmpInfo);
          preg_match('/"user_id":(.*),"email"/siU', $tmpInfo, $userinfo);
          $user_id = $userinfo[1];
          $conInfo = $this->curl->vget('http://www.ganji.com/vip/?_rid=0.87518778391', $cookie);
          preg_match('/<a href="http:\/\/fangvip.ganji.com\/index.php".*<span>(.*)<\/span>/siU', $conInfo, $prj);

          if (!empty($prj[1]) && $prj[1] == '房产帮帮' && $vip == 'ganjivip') {
            if ($vip == 'ganjivip') {
              $plus = empty($ganjisessid) ? '' : " GANJISESSID=$ganjisessid; path=/; domain=.ganji.com; ";
              $cookie = "fangvip_userc_index_freeuser=true;" . $plus . $cookie;
              $proInfo = $this->curl->vget('http://fangvip.ganji.com/index.php?bizScope=4', $cookie, 'fangvip.ganji.com', 1);
              $useInfo = $this->curl->vget('http://fangvip.ganji.com/post_pub.php?type=5', $cookie, 'fangvip.ganji.com', 1);
              preg_match("/人:.*<input.*readonly value='(.*)' size/siU", $useInfo, $man);
              preg_match("/联系电话:.*<input.*value='(.*)' size/siU", $useInfo, $phone);
              $phone = $phone[1];
              $man = $man[1];
            } else {
              return array('error' => '234', 'type' => '赶集普通版');
            }
          } else {
            if ($vip == 'ganji') {
              $phone = $signatory_info['phone'];
              $man = $signatory_info['truename'];
            } else {
              return array('error' => '234', 'type' => '赶集房产帮帮');
            }
          }

          $data = array();
          $data['signatory_id'] = $signatory_info['signatory_id'];
          $data['site_id'] = $site_id;
          $data['status'] = '1';
          $data['username'] = $username;
          $data['password'] = $password;
          $data['user_id'] = $user_id;
          $data['cookies'] = $cookie;
          $data['createtime'] = time();
          $data['otherpwd'] = $phone . "|" . $man;
          //根据用户id和站点来判断mass_site_signatory表里是否存在 是：则更新 否：则插入
          $where = array('signatory_id' => $signatory_info['signatory_id'], 'site_id' => $site_id);
          $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
          if (count($find) >= 1) {
            $result = $this->modify_data($where, $data, 'db_city', 'mass_site_signatory');
          } else {
            $result = $this->add_data($data, 'db_city', 'mass_site_signatory');
          }
          return $data;
        }
      }
    }
    return '123';
  }

  //绑定帐号
  public function save_bind_vip()
  {
    $vip = $this->input->get_post('site_id');
    $username = $this->input->get_post('username');
    $password = $this->input->get_post('password');
    $checkget = $this->input->get_post('checkcode');
    $ganjisessid = $this->input->get_post('ganjisessid');
    $signatory_info = $this->signatory_info;
    $site_id = $this->ganjivip['id'];

    if ($username && $password) {
      $uuid = $this->gj_uuid();
      $post_fields['checkCode'] = $checkget ? $checkget : '';  //重要
      $cookies = $ganjisessid ? "ganji_uuid=$uuid ;GANJISESSID=$ganjisessid; " : "ganji_uuid=$uuid ;";

      $url = 'https://passport.ganji.com/login.php?callback=jQuery182020571666893339668_' . time() . '000'
        . '&username=' . urlencode($username) . '&password=' . urlencode($password) . '&checkCode=' . $post_fields['checkCode'] . '&setcookie=14&second=&parentfunc='
        . '&redirect_in_iframe=&next=%2F&__hash__=i7lE7s18pjxEuxHa%2F6nHZHwZf9znb0jeKLT%2FfinzNQOvgMIyDkfuLfbtciVepc77&_=' . (time() + 7) . '000';
      $tmpInfo = $this->curl->vget($url, $cookies, 'passport.ganji.com', 1);
      preg_match('/"type":"(.*)"/siU', $tmpInfo, $checkcode);

      if (!empty($checkcode[1]) && $checkcode[1] == 'need_captcha' && empty($checkget)) {
        //获取GANJISESSID
        $codeInfo = $this->curl->vget('http://fangvip.ganji.com', '', '', 1);
        preg_match("/GANJISESSID=(.*);/siU", $codeInfo, $ganjisessid);
        return array('error' => 'gjcode', 'ganjisessid' => $ganjisessid[1]);
      } else {
        preg_match('/"user_id":(.*),"sscode"/siU', $tmpInfo, $userid);
        if ($userid[1])  //登录成功
        {
          preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo, $matches);
          $cookie = implode(';', $matches[1]);
          $plus = empty($ganjisessid) ? '' : " GANJISESSID=$ganjisessid; path=/; domain=.ganji.com; ";
          $cookie = "fangvip_userc_index_freeuser=true;" . $plus . $cookie;

          $proInfo = $this->curl->vget('http://fangvip.ganji.com/index.php?bizScope=4', $cookie, 'fangvip.ganji.com', 1);
          $useInfo = $this->curl->vget('http://fangvip.ganji.com/post_pub.php?type=5', $cookie, 'fangvip.ganji.com', 1);
          preg_match("/人:.*<input.*readonly value='(.*)' size/siU", $useInfo, $man);
          preg_match("/联系电话:.*<input.*value='(.*)' size/siU", $useInfo, $phone);
          $man = trim($man[1]);
          $phone = trim($phone[1]);

          if (empty($man) || empty($phone)) {
            $conInfo = $this->curl->vget('http://fangvip.ganji.com/account.php?do=profile', $cookie);
            preg_match('/真实姓名：<\/th>.*<td>(.*)<\/td>.*所在公司/siU', $conInfo, $man);
            preg_match('/联系电话：<\/th>.*<td>(.*)<\/td>.*QQ\/MSN/siU', $conInfo, $phone);
            $man = trim($man[1]);
            $phone = trim($phone[1]);
          }
          $data = array();
          $data['signatory_id'] = $signatory_info['signatory_id'];
          $data['site_id'] = $site_id;
          $data['status'] = '1';
          $data['username'] = $username;
          $data['password'] = $password;
          $data['user_id'] = $userid[1];
          $data['cookies'] = $cookie;
          $data['createtime'] = time();
          $data['otherpwd'] = $phone . "|" . $man;
          //根据用户id和站点来判断mass_site_signatory表里是否存在 是：则更新 否：则插入
          $where = array('signatory_id' => $signatory_info['signatory_id'], 'site_id' => $site_id);
          $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
          if (count($find) >= 1) {
            $result = $this->modify_data($where, $data, 'db_city', 'mass_site_signatory');
          } else {
            $result = $this->add_data($data, 'db_city', 'mass_site_signatory');
          }
          return $data;
        }
      }
    }
    return '123';
  }

  //数组转化为对象
  public function array_to_object($array = array())
  {
    if (!empty($array)) {
      $data = false;
      foreach ($array as $akey => $aval) {
        $data->$akey = $aval;
      }
      return $data;
    }
  }

  //绑定帐号
  public function save_bind_vip22()
  {
    $username = $this->input->get('username');
    $password = $this->input->get('password');
    $signatory_info = $this->signatory_info;
    $site_id = $this->ganjivip['id'];
    if ($username && $password) {
      $tmpcookie = "GANJISESSID=89aab9ded7f9ca4f06041b8ecdcba72f;"; //89aab9ded7f9ca4f06041b8ecdcba72f    c96d40bab7c65fcdb9776d4480a27d94
      $post_fields = array('next' => '', 'no_cookie_test' => 1, 'username' => $username, 'password' => $password);
      $tmpInfo = $this->curl->vlogin('http://fangvip.ganji.com/auth.php?do=login', $post_fields, $tmpcookie);  //登录地址
      preg_match('/class="msg_err">(.*)<\/div>/siU', $tmpInfo, $msgerr);  //错误信息
      preg_match('/user_id%22%3A(.*)%2C%22email/siU', $tmpInfo, $userid); //成功信息

      if ($userid[1]) {
        preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo, $matches);
        $cookie = $tmpcookie . implode(';', array_unique($matches[1]));
        $conInfo = $this->curl->vget('http://fangvip.ganji.com/account.php?do=profile', $cookie);

        preg_match('/真实姓名：<\/th>.*<td>(.*)<\/td>.*所在公司/siU', $conInfo, $man);
        preg_match('/联系电话：<\/th>.*<td>(.*)<\/td>.*QQ\/MSN/siU', $conInfo, $phone);
        $man = trim($man[1]);
        $phone = trim($phone[1]);
        if (empty($man) || empty($phone)) {
          $proInfo = $this->curl->vget('http://fangvip.ganji.com/index.php?bizScope=4', $cookie, 'fangvip.ganji.com', 1);
          $useInfo = $this->curl->vget('http://fangvip.ganji.com/post_pub.php?type=5', $cookie, 'fangvip.ganji.com', 1);
          preg_match("/人:.*<input.*readonly value='(.*)' size/siU", $useInfo, $man);
          preg_match("/联系电话:.*<input.*value='(.*)' size/siU", $useInfo, $phone);
          $man = trim($man[1]);
          $phone = trim($phone[1]);
        }
        $param = array();
        $param['signatory_id'] = $signatory_info['signatory_id'];
        $param['site_id'] = $site_id;
        $param['status'] = '1';
        $param['username'] = $username;
        $param['password'] = $password;
        $param['user_id'] = $userid[1];
        $param['cookies'] = $cookie;
        $param['createtime'] = time();
        $param['otherpwd'] = $phone . "|" . $man;
        //根据用户id和站点来判断mass_site_signatory表里是否存在 是：则更新 否：则插入
        $where = array('signatory_id' => $signatory_info['signatory_id'], 'site_id' => $site_id);
        $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
        if (count($find) >= 1) {
          $result = $this->modify_data($where, $param, 'db_city', 'mass_site_signatory');
        } else {
          $result = $this->add_data($param, 'db_city', 'mass_site_signatory');
        }
        $data = array('error' => 'success', 'info' => $param);
      } elseif ($msgerr[1] == '验证码错误') {
        preg_match("/GANJISESSID=(.*);/siU", $codeInfo, $ganjisessid);
        $data = array('error' => 'gjcode', 'ganjisessid' => $ganjisessid[1]);
      } else {
        $msginfo = empty($msgerr[1]) ? '' : $msgerr[1];
        $data = array('error' => 'yes', 'info' => $msginfo);
      }
    }
    return $data;
  }

  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $signatory_id = $this->signatory_info['signatory_id'];
    $city = $this->config->item('login_city');

    $this->load->model('group_publish_model');
    $this->load->model('group_queue_model');
    if ($act == 'sell') {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();
    $group = $this->group_queue_model->get_queue_one(array('id' => $queue_id));  //队列信息

    $block_info = $data = array();
    $block_name = $this->input->get('block_name', TRUE);
    $checkcode = $this->input->get('checkcode', TRUE);
    if ($block_name) {
      $block_info['checkcode'] = $checkcode;
      $block_info['ganjisessid'] = $this->input->get('ganjisessid', TRUE);
      $block_info['user_code'] = $this->input->get('user_code', TRUE);
      $block_info['block_name'] = $block_name;
      $block_info['block_id'] = $this->input->get('block_id', TRUE);
      $block_info['address'] = $this->input->get('address', TRUE);
      $block_info['street'] = $this->input->get('street', TRUE);
      $block_info['district'] = $this->input->get('district', TRUE);
    } else {
      $this->load->model('relation_street_model');
      $relation_street = $this->relation_street_model->select_relation_street($house_info['street_id'], $house_info['district_id']);
      $data['noblock'] = 0;
      if (!empty($relation_street[0]['ganji_dist_id'])) {
        $block_info['checkcode'] = $checkcode;
        $block_info['ganjisessid'] = $this->input->get('ganjisessid', TRUE);
        $block_info['user_code'] = $this->input->get('user_code', TRUE);
        $block_info['block_name'] = $house_info['block_name'];
        $block_info['block_id'] = '';
        $block_info['address'] = $house_info['address'];
        $block_info['street'] = $relation_street[0]['ganji_street_id'];
        $block_info['district'] = $relation_street[0]['ganji_dist_id'];
        $data['noblock'] = 1;
      }
      if (strlen($checkcode) != 4 && $alias == 'ganjivip') { //获取GANJISESSID
        $sessInfo = $this->curl->vget('http://fangvip.ganji.com', '', '', 1);
        preg_match("/GANJISESSID=(.*);/siU", $sessInfo, $ganjisessid);
        $data['user_code'] = substr(time(), -8);
        $data['ganjisessid'] = $ganjisessid[1];
        $data['flag'] = 'gjcode';
        //$data['noblock'] = empty($relation_street[0]['ganji_dist_id']) ? 0 : 1 ; //1不需要楼盘字典
        return $data;
      }
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
    } elseif ($house_info['floor'] > $house_info['totalfloor']) {
      return array('flag' => 'proerror', 'info' => '所在楼层不能大于总楼层');
    } elseif (mb_strlen($house_info['title']) < 6 || mb_strlen($house_info['title']) > 30) {
      return array('flag' => 'proerror', 'info' => '房源标题6-30字');
    } elseif (preg_match("/\d{7,}/", $house_info['title'])) {
      return array('flag' => 'proerror', 'info' => '房源标题不能填写电话');
    } elseif (mb_strlen($house_info['bewrite']) < 10 || mb_strlen(strip_tags($house_info['bewrite'])) > 800) {
      return array('flag' => 'proerror', 'info' => '房源描述10-800字');
    } elseif ($alias == 'ganjivip' && $city == 'pingxiang') {
      $ispic = 0;  //萍乡室内图 必须有
      if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
        $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
        if ($pic_info) {
          foreach ($pic_info as $key => $val) {
            if ($val['type'] == 1) {
              $ispic = 1;
              break;
            }
          }
        }
      }
      if (!$ispic) {
        return array('flag' => 'proerror', 'info' => '发布失败,缺少室内图');
      }
    }

    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';  //楼盘字典
    } else {
      //判断是否已经发布
      $publishinfo = $this->group_publish_model->get_num_sell_publish($signatory_id, $site_id, $house_id);
      if ($publishinfo) {
        $extra = array('nolog' => 1, 'signatory_id' => $signatory_id);
        $del = ($alias == 'ganji') ? $this->esta_delete($house_id, $act, $extra) : $this->esta_delete_vip($house_id, $act, $extra);
      }

      //加入定时任务
      $group['info'] = serialize($block_info);
      $demon = $this->group_queue_model->add_queue_demon($group);
      if (!empty($demon)) {
        $data['flag'] = 'success';  //加入定时任务
      } else {
        $data['flag'] = 'error';  //错误
      }
    }
    return $data;
  }

  public function queue_refresh($queue)
  {
    $query = array('form_name' => 'group_queue_demon', 'where' => array('id' => $queue['id']));
    $group = $this->get_data($query, 'dbback_city');
    if ($group[0]) {
      $site_id = $group[0]['site_id'];
      $house_id = $group[0]['house_id'];
      $queue_id = $group[0]['id'];
      $signatory_id = $group[0]['signatory_id'];
      $act = $group[0]['tbl'] == 1 ? 'sell' : 'rent';

      return parent::refresh_vip($signatory_id, $house_id, $act, '', $queue_id);
    }
  }

  public function queue_esta($queue)
  {
    $query = array('form_name' => 'group_queue_demon', 'where' => array('id' => $queue['id']));
    $group = $this->get_data($query, 'dbback_city');
    if ($group[0]) {
      $site_id = $group[0]['site_id'];
      $house_id = $group[0]['house_id'];
      $queue_id = $group[0]['id'];
      $signatory_id = $group[0]['signatory_id'];
      $act = $group[0]['tbl'] == 1 ? 'sell' : 'rent';

      $mass_site = $this->dbback_city->where(array('id' => $site_id))->get('mass_site')->row_array();
      $alias = $mass_site['alias'];

      $extra = array('nolog' => 0, 'queue_id' => $queue_id, 'signatory_id' => $signatory_id);
      if ($alias == 'ganji') {
        $data = $this->esta_delete($house_id, $act, $extra);
      } else {
        $data = $this->esta_delete_vip($house_id, $act, $extra);
      }
      return $data;
    }
    return false;
  }

  //发布数据拼装
  public function publish_param($queue)
  {
    $this->load->model('signatory_info_model');
    $site_id = $queue['site_id'];
    $site_info = $this->site_mass_model->get_only_site($site_id);
    $alias = $site_info['alias'];
    if ($alias == 'ganji') {
      return $this->publish_param_normal($queue);
    } else {
      return $this->publish_param_vip($queue);
    }
  }

  //赶集普通发布
  protected function publish_param_normal($queue)
  {
    $signatory_id = $queue['signatory_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $block_info = unserialize($queue['info']);
    $checkcode = $block_info['checkcode'];

    if (empty($block_info['street'])) {
      $block_info['street'] = '-1';
    }

    if ($queue['tbl'] == 1) {
      $act = 'sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $act = 'rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $login_city = $this->config->item('login_city');
    $city = $this->citysite->ganji_city($login_city);  //赶集城市
    if (empty($city)) {
      return array('flag' => 'proerror', 'info' => '未获得城市信息');
    }
    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);  //经纪人信息
    $mass_signatory = $this->site_mass_model->isbind_site('ganji', $signatory_id);  //目标站点 帐号信息
    $userlist = explode('|', $mass_signatory['otherpwd']);
    $userid = $mass_signatory['user_id'];
    $username = $userlist[1];
    $userphone = $userlist[0];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();  //房源信息
    $config = $this->getconfig();
    $zhuangxiu = $config['fit'][$house_info['fitment']];  //装修
    $chaoxiang = $config['face'][$house_info['forward']]; //朝向
    $equipment = $config['equipment'];  //租房设施
    $tmp_equipment = array();  //租房 设施
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($equipment[$val])) {
          $tmp_equipment[] = $equipment[$val];
        }
      }
    }
    $str_equipment = implode('&peizhi[]=', $tmp_equipment);

    //上传图片
    $i = 0;
    $shineipic = $shinei_pic = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          $shinei = $this->upload_image($picurl, $signatory_id, 'ganji');
          $shineipic[$i] = array(
            'image' => $shinei,
            'thumb_image' => str_flow('.jpg', '_120-100c_6-0.jpg', $shinei),
            'width' => '600',
            'height' => '398',
            'id' => 'SWFUpload_1_' . $i,
            'is_new' => 'true'
          );
          $shineipic[$i] = $this->array_to_object($shineipic[$i]);
          $i++;
        }
        if ($shineipic != '') {
          $shinei_pic[0] = $shineipic;
          $shinei_pic[1] = array();
          $shinei_pic = stripslashes(json_encode($shinei_pic));
        }
      }
    }
    $this->load->database();
    $this->db_city->reconnect();

    //修改房源权限
    $this->load->model('signatory_purview_model');
    $this->signatory_purview_model->set_signatory_id($signatory_id, $signatory_info['company_id']);
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($signatory_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($signatory_id != $house_info['signatory_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], $database = 'db_city');
        if (!empty($result)) {
          $house_info['title'] = $result[0]['title'];
          $house_info['bewrite'] = $result[0]['content'];
        }
      }
    }

    //赶集普通描述不支持富文本,前台显示时候中文：。会丢失
    $house_info['bewrite'] = strip_tags($house_info['bewrite']);
    $house_info['bewrite'] = $this->autocollect_model->special_flow($house_info['bewrite']); //描述富文本处理

    $data_house = array();
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    if ($act == 'sell') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $fang_xing = ($the_type == 2) ? 7 : 3;
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=21&act=pub&method=submit&domain=' . $city . '&_pdt=fang';
        $data_house['post_fielde'] = 'xiaoqu=' . $block_info['block_name'] . '&xiaoqu_address=' . $block_info['address']
          . '&huxing_shi=' . $house_info['room'] . '&huxing_ting=' . $house_info['hall'] . '&huxing_wei=' . $house_info['toilet']
          . '&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor'] . '&chaoxiang=' . $chaoxiang . '&fang_xing=' . $fang_xing
          . '&zhuangxiu=' . $zhuangxiu . '&niandai=' . $house_info['buildyear'] . '&house_property=1&land_tenure=1&bid_structure=5';
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=26&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=3&loupan_name=' . $block_info['block_name'] . '&address=' . $block_info['address'] . '&house_type=4';
      } elseif ($the_type == 4) { //写字楼
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=28&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=3&house_name=' . $block_info['block_name'] . '&address=' . $block_info['address'] . '&house_type=0';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $house_type = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 0);
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=132&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=3&address=' . $block_info['address'] . '&house_type=' . $house_type;
      }
      $data_house['act'] = 'sell';
      $data_house['post_fielde'] .= '&checkcode=' . $checkcode . '&district_id=' . $block_info['district'] . '&street_id=' . $block_info['street']
        . '&price=' . round($house_info['price'], 0) . '&title=' . $house_info['title'] . '&description=' . html_entity_decode($house_info['bewrite'])
        . '&images=' . $shinei_pic . '&person=' . $username . '&phone=' . $userphone . '&agent=1&is_edit=0&area=' . round($house_info['buildarea'], 0);
    } elseif ($act == 'rent') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $fang_xing = ($the_type == 2) ? 7 : 3;
        $tmp_pay = empty($house_info['rentpaytype']) ? 9 : $house_info['rentpaytype'];    //默认面议
        $pay_t = empty($config['paytype'][$tmp_pay]) ? 2 : $config['paytype'][$tmp_pay]; //付款方式
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=20&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'rent_mode=1&xiaoqu=' . $block_info['block_name'] . '&xiaoqu_address=' . $block_info['address']
          . '&huxing_shi=' . $house_info['room'] . '&huxing_ting=' . $house_info['hall'] . '&huxing_wei=' . $house_info['toilet']
          . '&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor'] . '&chaoxiang=' . $chaoxiang . '&zhuangxiu=' . $zhuangxiu
          . '&fang_xing=' . $fang_xing . '&peizhi[]=' . $str_equipment . '&pay_type_int=' . $pay_t;
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=26&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=1&loupan_name=' . $block_info['block_name'] . '&address=' . $block_info['address']
          . '&house_type=4&price_type=2&store_rent_type=2&store_stat=2&trade[]=9';
      } elseif ($the_type == 4) { //写字楼
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=28&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=1&house_name=' . $block_info['block_name'] . '&address=' . $block_info['address'] . '&house_type=0&price_type=2';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $house_type = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 0);
        $data_house['post_url'] = 'http://www.ganji.com/pub/pub.php?cid=7&mcid=132&act=pub&method=submit&domain=' . $city;
        $data_house['post_fielde'] = 'deal_type=1&price_type=2&address=' . $block_info['address'] . '&house_type=' . $house_type;
      }
      $data_house['act'] = 'rent';
      $data_house['post_fielde'] .= '&checkcode=' . $checkcode . '&district_id=' . $block_info['district'] . '&street_id=' . $block_info['street']
        . '&price=' . round($house_info['price'], 0) . '&title=' . $house_info['title'] . '&description=' . html_entity_decode($house_info['bewrite'])
        . '&images=' . $shinei_pic . '&person=' . $username . '&phone=' . $userphone . '&agent=1&is_edit=0&area=' . round($house_info['buildarea'], 0);
    }
    $data_house['ganjisessid'] = $block_info['ganjisessid'];
    $data_house['house_block_id'] = $house_info['block_id'];
    return $this->publish($data_house, $signatory_id, $house_id, $site_id, $queue['id']);
  }

  //赶集vip发布
  protected function publish_param_vip($queue)
  {
    $signatory_id = $queue['signatory_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $block_info = unserialize($queue['info']);
    $checkcode = $block_info['checkcode'];
    $user_code = empty($block_info['user_code']) ? substr(time(), -8) : $block_info['user_code'];

    if ($queue['tbl'] == 1) {
      $act = 'sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $act = 'rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);  //经纪人信息
    $mass_signatory = $this->site_mass_model->isbind_site('ganjivip', $signatory_id);  //目标站点 帐号信息
    $userlist = explode('|', $mass_signatory['otherpwd']);
    $userid = $mass_signatory['user_id'];
    $username = $userlist[1];
    $userphone = $userlist[0];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();  //房源信息
    $config = $this->getconfig();
    $equipment = $config['equipmentvip'];  //租房设施
    $zhuangxiu = $config['fit'][$house_info['fitment']];  //装修
    $chaoxiang = $config['face'][$house_info['forward']]; //朝向
    $tmp_equipment = array();  //租房 设施
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($equipment[$val])) {
          $tmp_equipment[] = $equipment[$val];
        }
      }
    }
    $str_equipment = implode('&peizhi[]=', $tmp_equipment);

    //上传图片
    $i = $j = 0;
    $shineipic = $huxingpic = $shinei_pic = $huxing_pic = '';
    $shinei = $huxing = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          if ($val['type'] == 1) {
            $shinei = $this->upload_image($picurl, $signatory_id, 'ganjivip');
            $shineipic[$i] = array(
              'image' => $shinei,
              'thumb_image' => str_flow('.jpg', '_120-100c_6-0.jpg', $shinei),
              'width' => '600',
              'height' => '398',
              'id' => 'SWFUpload_1_' . $i,
              'is_new' => 'true'
            );
            $shineipic[$i] = $this->array_to_object($shineipic[$i]);
            $i++;
          } else if ($val['type'] == 2) {
            $huxing = $this->upload_image($picurl, $signatory_id, 'ganjivip');
            $huxingpic[$j] = array(
              'image' => $huxing,
              'thumb_image' => str_flow('.jpg', '_120-100c_6-0.jpg', $huxing),
              'width' => '600',
              'height' => '398',
              'id' => 'SWFUpload_1_' . $i,
              'is_new' => 'true'
            );
            $huxingpic[$j] = $this->array_to_object($huxingpic[$j]);
            $j++;
          }
        }
        if ($shineipic != '') {
          $shinei_pic[0] = $shineipic;
          $shinei_pic[1] = array();
          $shinei_pic = stripslashes(json_encode($shinei_pic));
        }
        if ($huxingpic != '') {
          $huxing_pic[0] = $huxingpic;
          $huxing_pic[1] = array();
          $huxing_pic = stripslashes(json_encode($huxing_pic));
        }
      }
      $this->load->database();
      $this->db_city->reconnect();
    }

    //修改房源权限
    $this->load->model('signatory_purview_model');
    $this->signatory_purview_model->set_signatory_id($signatory_id, $signatory_info['company_id']);
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($signatory_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($signatory_id != $house_info['signatory_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], $database = 'db_city');
        if (!empty($result)) {
          $house_info['title'] = $result[0]['title'];
          $house_info['bewrite'] = $result[0]['content'];
        }
      }
    }

    $house_info['bewrite'] = $this->autocollect_model->special_flow($house_info['bewrite']);  //描述富文本处理
    $house_info['title'] = str_flow($config['cn_str'], $config['en_str'], $house_info['title']);

    $data_house = array();
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    if ($act == 'sell') {
      $data_house['act'] = 'sell';
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $fang_xing = ($the_type == 2) ? 7 : 3;
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=5';
        $data_house['post_fielde'] = 'type=5&xiaoqu=' . $block_info['block_name'] . '&xiaoqu_address=' . $block_info['address']
          . '&huxing_shi=' . $house_info['room'] . '&huxing_ting=' . $house_info['hall'] . '&huxing_wei=' . $house_info['toilet'] . '&area=' . $house_info['buildarea']
          . '&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor'] . '&chaoxiang=' . $chaoxiang . '&bid_structure=5&fang_xing=' . $fang_xing
          . '&zhuangxiu=' . $zhuangxiu . '&niandai=' . $house_info['buildyear'] . '&house_property=1&land_tenure=1&tab_system=4&huxing_tu=' . $huxing_pic . '&is_free=1';
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=6';
        $data_house['post_fielde'] = 'type=7&loupan_name=' . $block_info['block_name']
          . '&address=' . $block_info['address'] . '&house_type=4&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor']
          . '&area=' . $house_info['buildarea'] . '&zhuangxiu=' . $zhuangxiu . '&trade[]=9&peizhi[]=shui';
      } elseif ($the_type == 4) { //写字楼
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=8';
        $data_house['post_fielde'] = 'type=9&house_name=' . $block_info['block_name'] . '&address=' . $block_info['address']
          . '&building_type=1&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor']
          . '&area=' . $house_info['buildarea'] . '&zhuangxiu=' . $zhuangxiu . '&house_type=0&peizhi[]=dian_ti';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $house_type = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 0);
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=11';
        $data_house['post_fielde'] = 'type=11&deal_type=3&address=' . $block_info['address'] . '&house_type=' . $house_type
          . '&peizhi[]=shui&area=' . $house_info['buildarea'] . '&price_type=1';
      }
      $data_house['post_fielde'] .= '&checkcode=' . $checkcode . '&district_id=' . $block_info['district'] . '&street_id=' . $block_info['street']
        . '&price=' . round($house_info['price'], 0) . '&title=' . $house_info['title'] . '&description=' . html_entity_decode($house_info['bewrite'])
        . '&images=' . $shinei_pic . '&person=' . $username . '&phone=' . $userphone . '&do=submit&user_code=' . $user_code;
    } elseif ($act == 'rent') {
      $data_house['act'] = 'rent';
      $tmp_pay = empty($house_info['rentpaytype']) ? 9 : $house_info['rentpaytype'];    //默认面议
      $pay_t = empty($config['paytype'][$tmp_pay]) ? 2 : $config['paytype'][$tmp_pay]; //付款方式
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $fang_xing = ($the_type == 2) ? 7 : 3;
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=1';
        $data_house['post_fielde'] = 'type=1&xiaoqu=' . $block_info['block_name'] . '&xiaoqu_address=' . $block_info['address'] . '&is_free=1'
          . '&huxing_shi=' . $house_info['room'] . '&huxing_ting=' . $house_info['hall'] . '&huxing_wei=' . $house_info['toilet']
          . '&area=' . intval($house_info['buildarea']) . '&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor']
          . '&chaoxiang=' . $chaoxiang . '&zhuangxiu=' . $zhuangxiu . '&fang_xing=' . $fang_xing . '&peizhi[]=' . $str_equipment . '&pay_type=' . $pay_t . '&tab_system=1';
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=6';
        $data_house['post_fielde'] = 'type=6&loupan_name=' . $block_info['block_name'] . '&zhuangxiu=' . $zhuangxiu
          . '&house_type=4&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor'] . '&area=' . $house_info['buildarea']
          . '&address=' . $block_info['address'] . '&price_type=2&pay_type=' . $pay_t . '&store_rent_type=2&store_stat=2&trade[]=9&peizhi[]=shui';
      } elseif ($the_type == 4) { //写字楼
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=8';
        $data_house['post_fielde'] = 'type=8&house_name=' . $block_info['block_name'] . '&address=' . $block_info['address']
          . '&building_type=1&ceng=' . $house_info['floor'] . '&ceng_total=' . $house_info['totalfloor'] . '&area=' . $house_info['buildarea']
          . '&zhuangxiu=' . $zhuangxiu . '&house_type=0&peizhi[]=dian_ti&price_type=1&pay_type=' . $pay_t . '&lease_term=0&lease_term_unit=2';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $house_type = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 0);
        $data_house['post_url'] = 'http://fangvip.ganji.com/post_pub.php?type=11';
        $data_house['post_fielde'] = 'type=11&deal_type=1&address=' . $block_info['address'] . '&house_type=' . $house_type
          . '&peizhi[]=dian&area=' . $house_info['buildarea'] . '&price_type=2&pay_type=' . $pay_t . '&lease_term=0&lease_term_unit=2';
      }
      $data_house['post_fielde'] .= '&checkcode=' . $checkcode . '&district_id=' . $block_info['district'] . '&street_id=' . $block_info['street']
        . '&price=' . round($house_info['price'], 0) . '&title=' . $house_info['title'] . '&description=' . html_entity_decode($house_info['bewrite'])
        . '&images=' . $shinei_pic . '&person=' . $username . '&phone=' . $userphone . '&do=submit&user_code=' . $user_code;
    }
    $data_house['ganjisessid'] = $block_info['ganjisessid'];
    $data_house['user_code'] = $user_code;
    $data_house['type'] = $the_type;
    $data_house['house_block_id'] = $house_info['block_id'];
    return $this->publish($data_house, $signatory_id, $house_id, $site_id, $queue['id']);
  }

}

/* End of file site_ganji_model.php */
/* Location: ./application/mls_guli/models/site_ganji_model.php */
