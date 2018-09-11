<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_anjuke_base_model");

class Site_anjuke_model extends Site_anjuke_base_model
{
  private $anjuke;
  private $orientation; //朝向
  private $serverco;    //装修

  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
    $this->orientation = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $this->serverco = array('毛坯' => 1, '普通装修' => 2, '精装修' => 4, '豪华装修' => 5);
    $this->anjuke = $this->site_model->get_site_byid(array('alias' => 'anjuke'));
    $this->anjuke = $this->anjuke[0];
  }

  //导入列表 要检测,后期修改
  public function isbind_anjuke()
  {
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    return $this->isbind($signatory_id, $site_id);
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $username = $this->input->get('username');
    $password = $this->input->get('password');

    $login = $this->login($username, $password);
    if (!$login) {
      return '123';  //绑定失败
    } else {
      $data = array();
      $data['signatory_id'] = $signatory_id;
      $data['site_id'] = $site_id;
      $data['status'] = '1';
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['user_id'];
      $data['cookies'] = $login['cookies'];
      $data['createtime'] = time();
      $data['otherpwd'] = '';

      //根据用户id和站点来判断mass_site_signatory表里是否存在 是：则更新 否：则插入
      $where = array('signatory_id' => $signatory_id, 'site_id' => $site_id);
      $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
      if (count($find) >= 1) {
        $result = $this->modify_data($where, $data, 'db_city', 'mass_site_signatory');
      } else {
        $result = $this->add_data($data, 'db_city', 'mass_site_signatory');
      }
      return $data;
    }
  }

  //列表采集
  public function collect_list($act)
  {
    $num = 0;
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $login = $this->isbind($signatory_id, $site_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];

    if ($cookie) {
      if ($act == 'sell') {
        $list_one = 'http://my.anjuke.com/ajksignatory/combo/signatory/manage/ajk/';
        $list_two = 'http://my.anjuke.com/ajksignatory/combo/signatory/manage/ajk/v2/';
        $bug = '';
        $type = array('楼售', '铺售');
      } else {
        $list_one = 'http://my.anjuke.com/ajksignatory/combo/signatory/manage/hz/';
        $list_two = 'http://my.anjuke.com/ajksignatory/combo/signatory/manage/hz/v2/';
        $bug = 'http://my.anjuke.com';
        $type = array('楼租', '铺租');
      }
      $tmpInfo = $this->curl->vget($list_one, $cookie);
      $moreInfo = $this->curl->vget('http://my.anjuke.com/ajksignatory/combo/signatory/manage/jp', $cookie); //商业地产:商铺 and 写字楼
      if (empty($tmpInfo)) {
        $tmpInfo = $this->curl->vget($list_two, $cookie);
        $moreInfo = $this->curl->vget('http://my.anjuke.com/ajksignatory/combo/signatory/manage/jp/v2/', $cookie);
      }
      preg_match_all('/<li.*<div class="listhead-title">(.*)<\/li>/siU', $tmpInfo, $prj);
      preg_match_all('/<li.*<div class="listhead-title">(.*)<\/li>/siU', $moreInfo, $more);

      foreach ($more[1] as $val) {
        preg_match('/<h5><a href="(.*)" target="_blank"><span>\[(.*)\]<\/span>(.*)<\/a>/siU', $val, $moretype);
        preg_match('/<dd><a target="_blank" href="(.*)".*>编辑房源<\/a>/siU', $val, $url);
        preg_match('/<p>(.*)<\/p>/siU', $val, $des);
        preg_match('/<div class="listhead-time line-mid ">.*<em.*><?([0-9]*)<\/em>/siU', $val, $releasetime);

        if (!in_array($moretype[2], $type)) continue;
        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1]; //编辑链接
        $data['infourl'] = $moretype[1];  //详情链接
        $data['title'] = trim($moretype[3]) . ' [' . $moretype[2] . ']';    //标题
        $data['des'] = strip_tags($des[1]);
        $data['releasetime'] = trim($releasetime[1]) ? strtotime(($releasetime[1] - 90) . ' day') : ''; //发布时间
        $data['city_spell'] = $this->signatory_info['city_spell'];
        $data['signatory_id'] = $this->signatory_info['signatory_id'];
        $data['site_id'] = $this->anjuke['id'];
        if ($act == 'sell') {
          $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
        } else {
          $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
        }
        if ($res) $num++;
      }
      foreach ($prj[1] as $val) {
        preg_match('/<dd>.*<a href="(.*)".*>编辑房源<\/a>.*<\/dd>/siU', $val, $url);
        preg_match('/<h5>.*<a href="(.*)".*>(.*)<\/a>/siU', $val, $infourl);
        preg_match('/<p>(.*)<\/p>/siU', $val, $des);
        preg_match('/<div class="listhead-time line-mid">.*<em.*><?([0-9]*)<\/em>/siU', $val, $releasetime);

        $data = array();
        $data['source'] = 0;
        $data['url'] = empty($url[1]) ? '' : $bug . $url[1]; //编辑链接
        $data['infourl'] = $infourl[1];  //详情链接
        $data['title'] = trim($infourl[2]);    //标题
        $data['des'] = strip_tags($des[1]);
        $data['releasetime'] = trim($releasetime[1]) ? strtotime(($releasetime[1] - 90) . ' day') : ''; //发布时间
        $data['city_spell'] = $this->signatory_info['city_spell'];
        $data['signatory_id'] = $this->signatory_info['signatory_id'];
        $data['site_id'] = $this->anjuke['id'];
        if ($act == 'sell') {
          $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
        } else {
          $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
        }
        if ($res) $num++;
      }
      return $num;
    } else {
      echo 'no cookie';
    }
  }

  //详情页面导入:出售
  public function collect_sell_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $infourl = str_flow('view/', 'view/A', $infourl);
    $city_spell = $this->signatory_info['city_spell'];
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $login = $this->isbind($signatory_id, $site_id);

    $list = array('编辑二手房' => 1, '编辑商铺出售' => 3, '编辑写字楼出售' => 4);

    if (empty($login['cookies'])) {
      return false;
    } else {
      if (strstr($url, 'shop')) {
        preg_match('/sale\/(.*)\?from=no/siU', $url, $hid);
        $url = 'http://my.anjuke.com/ajksignatory/house/publish/jpss/' . $hid[1];  //编辑页跳转,重新拼装
      } elseif (strstr($url, 'office')) {
        preg_match('/sale\/(.*)\?from=no/siU', $url, $hid);
        $url = 'http://my.anjuke.com/ajksignatory/house/publish/jpos/' . $hid[1];
      }
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $login['cookies']);
      preg_match('/class="com-title page-title">(.*)<\/h4>/siU', $tmpInfo, $mytype);
      $mytype = trim(strip_tags($mytype[1]));
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = $tpimg = array();
    if ($the_type == 3) { //商铺
      preg_match('/class="community-name">(.*)<div class="office-building-address"/siU', $tmpInfo, $house_name);
      preg_match('/name="select_floor".*value="([0-9])" selected="selected">/siU', $tmpInfo, $floor_type);
      if ($floor_type[1] == 1) {
        preg_match('/name="single_floor" class="fi-text fi-max fi-wmini" value="([0-9]*)"/siU', $tmpInfo, $floor);
      } elseif ($floor_type[1] == 2) {
        preg_match('/name="from_floor" class="fi-text fi-max fi-wmini from-floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      } else {
        preg_match('/name="single_family" class="fi-text fi-max fi-wmini" value="([0-9]*)"/siU', $tmpInfo, $totalfloor);
      }
      preg_match('/name="area" id="area" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="shop_total_price" value="(.*)"\/>/siU', $tmpInfo, $price);
      preg_match('/name="shop_title" type="text" class="fi-text fi-max fi-whuge" value="(.*)" data-remote/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)" alt=/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim(strip_tags($house_name[1])); //楼盘名称
      $house_info['floor'] = empty($floor[1]) ? '' : $floor[1];  //楼层
      $house_info['totalfloor'] = empty($totalfloor[1]) ? '' : $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } elseif ($the_type == 4) { //写字楼
      preg_match('/class="community-name">(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/id="office_area" value="(.*)"\/>/siU', $tmpInfo, $area);
      preg_match('/name="office_total_price" value="(.*)"\/>/siU', $tmpInfo, $price);
      preg_match('/id="office_title" name="office_title".*value="(.*)" data-remote/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)"/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } else { //民宅 别墅
      preg_match('/id="housetype".*value="([0-9]*)" selected/siU', $tmpInfo, $dtype);
      preg_match('/class="community-name">(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/id="room".*value="([0-9])".*室/siU', $tmpInfo, $room);
      preg_match('/id="hall".*value="([0-9])".*厅/siU', $tmpInfo, $hall);
      preg_match('/id="toliet".*value="([0-9])".*卫/siU', $tmpInfo, $toilet);
      preg_match('/朝向.*selected>(.*)<\/option>/siU', $tmpInfo, $forward);
      preg_match('/id="ProFloor" name="ProFloor".*value="([0-9]*)".*楼/siU', $tmpInfo, $floor);
      preg_match('/id="FloorNum" name="FloorNum".*value="([0-9]*)".*层/siU', $tmpInfo, $totalfloor);
      preg_match('/装修情况.*selected>(.*)<\/option>/siU', $tmpInfo, $build);
      preg_match('/id="area" name="AreaNum".*value="(.*)".*平米/siU', $tmpInfo, $buildarea);
      preg_match('/id="price" name="ProPrice".*value="(.*)".*万元/siU', $tmpInfo, $price);
      preg_match('/id="sale_title" name="ProName".*value="(.*)".*<span/siU', $tmpInfo, $title);

      preg_match('/id="room_pic_wrap"(.*)id="hx_pic_wrap"/siU', $picInfo, $neipic);  //室内图
      preg_match_all('/<img data-src="(.*)".*src=/siU', $neipic[1], $pic_one);
      foreach ($pic_one[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);//室内图

      preg_match('/id="hx_pic_wrap"(.*)id="surround_pic_wrap"/siU', $picInfo, $hupic);  //户型图
      preg_match_all('/<img data-src="(.*)".*src=/siU', $hupic[1], $pic_two);
      foreach ($pic_two[1] as $val) {
        $tpurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $tpimg[] = $tpurl;
      }
      $house_info['picurl_hu'] = implode('*', $tpimg);//户型图

      $house_info['sell_type'] = ($dtype[1] == 27) ? 2 : 1;  //类型2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $face = trim($forward[1]) ? trim($forward[1]) : '东';
      $house_info['forward'] = $this->orientation[$face];    //朝向
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $this->serverco[$serverco_temp];   //装修
      $house_info['buildarea'] = $buildarea[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea name="ProDesc" autocomplete="off" id="kdedit" name="describe">(.*)<\/textarea>/siU', $tmpInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //详情页面导入:租房
  public function collect_rent_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $city_spell = $this->signatory_info['city_spell'];
    $list = array('编辑租房' => 1, '编辑商铺出租' => 3, '编辑写字楼出租' => 4);
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $login = $this->isbind($signatory_id, $site_id);

    if (empty($login['cookies'])) {
      return false;
    } else {
      if (strstr($url, 'shop')) {
        preg_match('/rent\/(.*)\?from=no/siU', $url, $hid);
        $url = 'http://my.anjuke.com/ajksignatory/house/publish/jpsr/' . $hid[1];  //编辑页跳转,重新拼装
      } elseif (strstr($url, 'office')) {
        preg_match('/rent\/(.*)\?from=no/siU', $url, $hid);
        $url = 'http://my.anjuke.com/ajksignatory/house/publish/jpor/' . $hid[1];
      }
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $login['cookies']);
      preg_match('/class="com-title page-title">(.*)<\/h4>/siU', $tmpInfo, $mytype);
      $mytype = trim(strip_tags($mytype[1]));
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = array();
    if ($the_type == 3) { //商铺
      preg_match('/class="community-name">(.*)<div class="office-building-address"/siU', $tmpInfo, $house_name);
      preg_match('/name="select_floor".*value="([0-9])" selected="selected">/siU', $tmpInfo, $floor_type);
      if ($floor_type[1] == 1) {
        preg_match('/name="single_floor" class="fi-text fi-max fi-wmini" value="([0-9]*)"/siU', $tmpInfo, $floor);
      } elseif ($floor_type[1] == 2) {
        preg_match('/name="from_floor" class="fi-text fi-max fi-wmini from-floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      } else {
        preg_match('/name="single_family" class="fi-text fi-max fi-wmini" value="([0-9]*)"/siU', $tmpInfo, $totalfloor);
      }
      preg_match('/name="area" id="area" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="monthly_rent" value="(.*)"\/>/siU', $tmpInfo, $price);
      preg_match('/name="shop_title" maxlength="30" value="(.*)" data-remote/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)" alt=/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim(strip_tags($house_name[1])); //楼盘名称
      $house_info['floor'] = empty($floor[1]) ? '' : $floor[1];  //楼层
      $house_info['totalfloor'] = empty($totalfloor[1]) ? '' : $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
    } elseif ($the_type == 4) { //写字楼
      preg_match('/class="community-name">(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/name="office_area" value="(.*)"\/>/siU', $tmpInfo, $area);
      preg_match('/name="office_monthly_total_rent" value="(.*)"\/>/siU', $tmpInfo, $price);
      preg_match('/id="office_title" name="office_title".*value="(.*)" data-remote/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)"/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
    } else { //民宅 别墅
      preg_match('/id="housetype".*value="([0-9]*)" selected/siU', $tmpInfo, $dtype);
      preg_match('/class="community-name">(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/id="room".*value="(.*)".*室/siU', $tmpInfo, $room);
      preg_match('/id="hall".*value="(.*)".*厅/siU', $tmpInfo, $hall);
      preg_match('/id="toliet".*value="(.*)".*卫/siU', $tmpInfo, $toliet);
      preg_match('/选择朝向.*selected = "selected">(.*)<\/option>/siU', $tmpInfo, $forward);
      preg_match('/id="floor" name="floor".*value="(.*)".*楼/siU', $tmpInfo, $floor);
      preg_match('/id="floorall" name="floorall".*value="(.*)".*层/siU', $tmpInfo, $totalfloor);
      preg_match('/选择装修.*selected = "selected">(.*)<\/option>/siU', $tmpInfo, $build);
      preg_match('/id="housearea".*value="(.*)".*平米/siU', $tmpInfo, $buildarea);
      preg_match('/id="rentprice".*value="(.*)".*元/siU', $tmpInfo, $price);
      preg_match('/id="rent_title".*value="(.*)".*<span id="rent_title_msg"/siU', $tmpInfo, $title);
      preg_match('/"tabscon tnow".*class="picMove cf"(.*) class="btn_ps btn_next"/siU', $picInfo, $outpic);
      preg_match_all('/<img src="(.*)".*data-src=""/siU', $outpic[1], $pic_one);
      preg_match_all('/<img src=".*".*data-src="([^"]+)"/siU', $outpic[1], $pic_two);
      $pic = array_merge($pic_one[1], $pic_two[1]);

      $house_info = $image = array();
      foreach ($pic as $val) {
        $vp = trim($val);
        if (empty($vp)) continue;
        $tempurl = $this->autocollect_model->get_pic_url($vp, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = ($dtype[1] == 4) ? 2 : 1;  //类型2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toliet[1];  //卫
      $face = trim($forward[1]) ? trim($forward[1]) : '东';
      $house_info['forward'] = $this->orientation[$face];    //朝向
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $this->serverco[$serverco_temp];   //装修
      $house_info['buildarea'] = $buildarea[1];   //面积
      $house_info['price'] = empty($price[1]) ? '' : $price[1];  //租金
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/<textarea name="ProDesc" autocomplete="off" id="kdedit" name="describe">(.*)<\/textarea>/siU', $tmpInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  /*
  //获取发布所需房屋配置信息
  public function get_house($house_id, $block_info, $act)
  {
      $city = $this->signatory_info['city_spell'];
      $signatory_id = $this->signatory_info['signatory_id'];
      $site_id = $this->anjuke['id'];
      $site_signatory = $this->site_model->get_signatoryinfo_byids( array('signatory_id'=>$signatory_id, 'site_id'=>$site_id, 'status'=>1) );
      $userid = $site_signatory[0]['user_id'];
      $username = $site_signatory[0]['username'];

      $this->load->library('CitySite');
      $config = $this->citysite->anjuke_house($city,$act);
      $ajk_fitment = $config['fit'];
      //房屋基本信息
      if ( $act=='sell' ){
          $this->load->model('sell_house_model','my_house_model');
          //$ajk_fitment = array(1=>25, 2=>26, 3=>26, 4=>27, 5=>28, 6=>28); //匹配装修 25毛坯 26普通 27精装 28豪装
          $ajk_forward = array(1=>0, 2=>4, 3=>1, 4=>6, 5=>2, 6=>7, 7=>3, 8=>5, 9=>9, 10=>8); //匹配朝向
      }else{
          $this->load->model('rent_house_model','my_house_model');
          //$ajk_fitment = array(1=>'25|毛坯', 2=>'26|普通装修', 3=>'26|普通装修', 4=>'27|精装修', 5=>'28|豪华装修', 6=>'28|豪华装修'); //匹配 装修
          $ajk_forward = array(1=>'东', 2=>'东南', 3=>'南', 4=>'西南', 5=>'西', 6=>'西北', 7=>'北', 8=>'东北', 9=>'东西', 10=>'南北'); //匹配 朝向
      }
      $this->my_house_model->set_id($house_id);
      $house_info = $this->my_house_model->get_info_by_id();
      $fitment = $ajk_fitment[$house_info['fitment']];    //装修
      $houseori = $ajk_forward[$house_info['forward']];   //朝向
      $ajk_equipment = $this->equipment;  //租房 房屋设施
      $tmp_equipment = array();
      if( !empty($house_info['equipment']) ){
          $equipment = explode(',', $house_info['equipment']);
          foreach ($equipment as $val){
              if( isset($ajk_equipment[$val]) ){
                  $tmp_equipment[] = $ajk_equipment[$val];
              }
          }
      }
      $str_equipment = implode('&houseconfig[]=', $tmp_equipment);

      //提交前,表单验证
      $mustflg = $this->input->get('must');
      if( !$mustflg ){
          if( $house_info['floor']>99 || $house_info['totalfloor']>99){
              echo json_encode(array('flag'=>9,'info'=>'楼层最多2位数')); exit;
          }elseif( $house_info['floor'] > $house_info['totalfloor']){
              echo json_encode(array('flag'=>9,'info'=>'所在楼层不能大于总楼层')); exit;
          }elseif( mb_strlen($house_info['title'])<5 || mb_strlen($house_info['title'])>30 ){
              echo json_encode(array('flag'=>9,'info'=>'房源标题5-30字')); exit;
          }elseif( mb_strlen($house_info['bewrite'])<20 || mb_strlen( strip_tags($house_info['bewrite']) )>5000){
              echo json_encode(array('flag'=>9,'info'=>'房源描述20-5000字')); exit;
          }
      }
      //必须在表单验证后
      if(empty($block_info)){
          return false;
      }else{
          $block_id = $block_info['block_id'];
          $block_name = $block_info['block_name'];
      }

      //上传图片
      $shinei_pic = $huxing_pic = '';
      $shinei = $huxing = array();
      if( $house_info['pic_ids'] && $house_info['pic_tbl'] ){
          $this->load->model('pic_model');
          $pic_info = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
          if( $pic_info ){
              if ( in_array($house_info['sell_type'], array(3,4)) ){  //3商铺 4写字楼
                  foreach($pic_info as $key=>$val){
                      $picurl = changepic_send($val['url']);
                      $shinei = $this->upload_image($picurl,$signatory_id);
                      $shinei_pic .= '&dropDesc['.$shinei[1].']=0&hideaid[]='.$shinei[1].'&newupdroom[]='.$shinei[0].'&roomorder[]='.$shinei[1];
                  }
              }else{
                  foreach($pic_info as $key=>$val){
                      $picurl = changepic_send($val['url']);
                      if($val['type']==1){
                          $shinei = $this->upload_image($picurl,$signatory_id);
                          $shinei_pic .= '&updroom[]='.$shinei[0].'&newupdroom[]='.$shinei[0].'&roomorder[]='.$shinei[1].'&file=&delRoom=[]&change_img_sort=0';
                      }else if($val['type']==2){
                          $huxing = $this->upload_image($picurl,$signatory_id);
                          $huxing_pic .= '&updmodel[]='.$huxing[0].'&newupdmodel[]='.$huxing[0].'&modelorder[]='.$huxing[1].'&file=&delModel=[]&changeModelSort=0';
                      }
                  }
              }
          }
      }
      $this->load->database();
      $this->db_city->reconnect();

      //修改房源权限
      $this->load->model('signatory_purview_model');
      $this->signatory_purview_model->set_signatory_id($signatory_id, $this->signatory_info['company_id']);
      //获得当前经纪人的角色等级，判断店长以上or店长以下
      $role_level = intval($this->signatory_info['role_level']);
      //店长以下的经纪人不允许操作他人的私盘
      if(is_int($role_level) && $role_level > 6){
          if($signatory_id != $house_info['signatory_id'] && $house_info['nature']=='1'){
              //获取当前经纪人的临时详情
              $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], $database='db_city');
              if (!empty($result)) {
                  $house_info['title'] = $result[0]['title'];
                  $house_info['bewrite'] = $result[0]['content'];
              }
          }
      }

      //$house_info['bewrite'] = strip_tags($house_info['bewrite']);
      //$house_info['bewrite'] = $this->autocollect_model->con_flow($house_info['bewrite']);
      $house_info['bewrite'] = $this->autocollect_model->special_flow($house_info['bewrite']);  //描述富文本处理

      $data_house = array();
      $data_house['sell_type'] = $the_type = $house_info['sell_type'];
      $data_house['block_id'] = $block_id;
      $data_house['block_name'] = $block_name;
      $price = round($house_info['price'],0);
      $area = round($house_info['buildarea'],0);
      $unit_price = round($house_info['price']*10000/$house_info['buildarea'],0);  //每平米均价
      if( $act=='sell' ){ //出售 sell
          if( $the_type==1 || $the_type==2 ){ //住宅 别墅
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/ajk';
              //$data_house['post_fielde'] = ($the_type==2) ? 'UseType=27' : 'UseType=25' ; //25住宅 26公寓 27别墅
              $house_tmp = empty($house_info['house_type']) ? 1 :$house_info['house_type'];
              $tmp_type = ($the_type==2) ? $config['type']['bs'] : $config['type'][$house_tmp];
              $data_house['post_fielde'] = 'UseType='.$tmp_type;
              $data_house['post_fielde'] .= '&act=publish&fixPlanId=&signatory_id='.$userid.'&areaTplId=0&commTplId=0&commpropTplId=0&CommId='.$block_id
                      .'&communityName='.$block_name.'&commname='.$block_name.'&AreaNum='.$area.'&ProPrice='.$price
                      .'&minDownPay=&houseAge='.$house_info['buildyear'].'&RoomNum='.$house_info['room'].'&HallNum='.$house_info['hall']
                      .'&ToiletNum='.$house_info['toilet'].'&fitment='.$fitment.'&houseori='.$houseori.'&ProFloor='.$house_info['floor']
                      .'&FloorNum='.$house_info['totalfloor'].'&isFullFive=1&isOnly=1&ProName='.$house_info['title']
                      .'&tags='.$shinei_pic.$huxing_pic.'&signatory_action=save';
          }elseif ( $the_type==3 ){ //商铺
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpss';
              $data_house['post_fielde'] = 'communityId='.$block_id.'&community='.$block_name.'&area='.$area.'&shop_total_price='.$price
                      .'&office_manage_fee='.$house_info['strata_fee'].'&select_floor=1&single_floor='.$house_info['floor']
                      .'&business_state=2&shop_title='.$house_info['title'].'&map_zoom=15&hasMapDataFlag=0'.$shinei_pic
                      .'&file=&delRoom=[]&goto_next_step=justSaveHouse&fix_plan_id=';
          }elseif ( $the_type==4 ){ //写字楼
              $level = $house_info['totalfloor'] / 3;
              $office_floor = ($house_info['floor']<$level) ? 1 : ( $house_info['floor']<($level*2) ? 2 : 3 ) ;
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpos';
              $data_house['post_fielde'] = 'communityId='.$block_id.'&community='.$block_name.'&office_area='.$area.'&office_total_price='.$price
                      .'&office_unit_price='.$unit_price.'&office_floor='.$office_floor.'&office_title='.$house_info['title'].$shinei_pic
                      .'&office_efficient_rate=85&file=&delRoom=[]&goto_next_step=justSaveHouse&fix_plan_id=';
          }else{
              echo json_encode(array('flag'=>9,'info'=>'安居客不支持厂房、仓库、车库','after'=>1));
              exit;
          }
          $data_house['act'] = 'sell';
          $data_house['post_fielde'] .= '&ProDesc='.html_entity_decode($house_info['bewrite']).'&defaultImgID=&anjuke_publish_rules=on';
      }else{  //出租 rent
          if( $the_type==1 || $the_type==2 ){ //住宅 别墅
              //$housetype = ($the_type==2) ? '&housetype=4' : '&housetype=8' ; //1公寓 4别墅 8住宅 6酒店公寓 5其他
              $house_tmp = empty($house_info['house_type']) ? 1 :$house_info['house_type'];
              $tmp_type = ($the_type==2) ? $config['type']['bs'] : $config['type'][$house_tmp];
              $housetype = '&housetype='.$tmp_type;
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/hz/';
              $data_house['post_fielde'] = 'commonid='.$block_id.'&commname='.$block_name.'&areaid=&renttype=1&room='.$house_info['room']
                      .'&hall='.$house_info['hall'].'&toliet='.$house_info['toilet'].'&floor='.$house_info['floor'].'&floorall='.$house_info['totalfloor']
                      .$housetype.'&decoration='.$fitment.'&toward='.$houseori.'&rentsex=0&roomtoward=&housearea='.$area.'&rentprice='.$price
                      .'&paytype=3|1&housetitle='.$house_info['title'].$huxing_pic.'&defaultImgID=0&html_action=save&houseconfig[]='.$str_equipment;
          }elseif ( $the_type==3 ){ //商铺
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpsr';
              $data_house['post_fielde'] = 'communityId='.$block_id.'&community='.$block_name.'&area='.$area.'&monthly_rent='.$price
                      .'&rent_type=1&office_manage_fee='.$house_info['strata_fee'].'&single_floor='.$house_info['floor']
                      .'&select_floor=1&single_family=&select_industry=&business_state=2&shop_title='.$house_info['title']
                      .'&map_zoom=18&hasMapDataFlag=0&file=&delRoom=[]&defaultImgID=&goto_next_step=justSaveHouse&fix_plan_id=';
          }elseif ( $the_type==4 ){ //写字楼
              $level = $house_info['totalfloor'] / 3;
              $office_floor = ($house_info['floor']<$level) ? 1 : ( $house_info['floor']<($level*2) ? 2 : 3 ) ;
              if( $house_info['strata_fee'] && $house_info['costs_type']==1 ){
                  $house_info['strata_fee'] = $house_info['strata_fee'] / $area;
              }
              $day_price = $price/30/$area;
              $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpor';
              $data_house['post_fielde'] = 'communityId='.$block_id.'&community='.$block_name.'&office_area='.$area.'&office_monthly_total_rent='.$price
                      .'&office_manage_fee='.$house_info['strata_fee'].'&office_floor='.$office_floor.'&office_title='.$house_info['title']
                      .'&office_efficient_rate=65&delRoom=[]&defaultImgID=&goto_next_step=justSaveHouse&fix_plan_id=&office_daily_rent='.$day_price;
          }else{
              echo json_encode(array('flag'=>9,'info'=>'安居客不支持厂房、仓库、车库','after'=>1));
              exit;
          }
          $data_house['act'] = 'rent';
          $data_house['post_fielde'] .= '&ProDesc='.html_entity_decode($house_info['bewrite']).$shinei_pic.'&anjuke_publish_rules=on';
      }
      $data_house['house_block_id'] = $house_info['block_id'];
      return $data_house;
  }*/
  /*
  //发布到目标站点
  public function send_house($data_house, $house_id)
  {
      $act = $data_house['act'];  //sell rent
      $post_url = $data_house['post_url'];
      $post_fields = $data_house['post_fielde'];
      $the_type = $data_house['sell_type']; //1民宅 2别墅 3商铺 4写字楼

      ($act=='sell') ? $this->load->model('sell_house_model','my_house_model'):$this->load->model('rent_house_model','my_house_model');
      $this->my_house_model->set_id($house_id);
      $house_info = $this->my_house_model->get_info_by_id();

      $signatory_info = $this->signatory_info;
      $city = $signatory_info['city_spell'];
      $signatory_id = $signatory_info['signatory_id'];
      $site_id = $this->anjuke['id'];
      $this->load->model('group_publish_model');

      $ymd = $this->input->get_post('createtime' , TRUE);
      $paramlog = array(
              'house_id' => $house_id,
              'signatory_id' => $signatory_id,
              'site_id' => $this->anjuke['id'],
              'block_id' => $house_info['block_id'],
              'sell_type' => ($act=='sell') ? 1 : 2, //1出售,2出租
              'ymd' => $ymd?$ymd:time()
      );

      $login = $this->isbind($signatory_id, $site_id);
      $paramlog['username'] = empty($login['username']) ? '':$login['username'];
      if( $login['cookies'] ){
          $cookie = $login['cookies'];
          $tmpInfo = $this->curl->vpost($post_url, $post_fields, $cookie);
          preg_match('/proId=(.*)&/siU', $tmpInfo, $pro);
          $publish_id = $pro[1];

          if( $publish_id ){
              $typeList = array(1, 2, 5, 6, 7);
              $cityList = $this->anjuke_city;
              if( $act=='sell' ){ //出售
                  if( in_array($the_type, $typeList) ){
                      $publish_url = "http://".$cityList[$city].".anjuke.com/prop/view/A".$publish_id."?from=esffyxxw";
                  }elseif ( $the_type==3 ){
                      $publish_url = "http://".$cityList[$city].".sp.anjuke.com/shou/".$publish_id;
                  }elseif ( $the_type==4 ){
                      $publish_url = "http://".$cityList[$city].".xzl.anjuke.com/shou/".$publish_id;
                  }
              }else{ //出租
                  if( in_array($the_type, $typeList) ){
                      $publish_url = "http://www.zu.anjuke.com/fangyuan/".$publish_id."?from=zffyxxw";
                  }elseif ( $data_house['sell_type']==3 ){
                      $publish_url = "http://".$cityList[$city].".sp.anjuke.com/zu/".$publish_id;
                  }elseif ( $data_house['sell_type']==4 ){
                      $publish_url = "http://".$cityList[$city].".xzl.anjuke.com/zu/".$publish_id;
                  }
              }
              $param = array(
                      'signatory_id' => $signatory_id,
                      'house_id'  => $house_id,
                      'site_id'   => $this->anjuke['id'],
                      'publish_id'=> $publish_id,
                      'publish_url'=>$publish_url,
                      'createtime'=> time(),
                      'updatetime'=> time()
              );
              if( $act=='sell' ){
                  $insert_id = $this->group_publish_model->add_sell_info($param);
                  $tbl = 1;
              }else{
                  $insert_id = $this->group_publish_model->add_rent_info($param);
                  $tbl = 2;
              }
              $paramlog['type'] = 1; //1成功,2失败
              $paramlog['info'] = '发布成功';
              $log_id = $this->group_publish_model->add_publish_log($paramlog);
              $data = array('flag'=>1, 'publish_url'=>$publish_url);

              //孙老师统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝   tbl:1出售 2出租
              $this->load->model('publish_count_num_model');
              $this->publish_count_num_model->info_count($signatory_info, 5, array('tbl'=>$tbl, 'house_id'=>$house_id));
              //记录 安居客楼盘
              $ajk= array('block_id' => $house_info['block_id'],
                          'department_id' => $signatory_info['department_id'],
                          'company_id' => $signatory_info['company_id'],
                          'ajk_block_id' => $data_house['block_id'],
                          'ajk_block_name' => $data_house['block_name']
                  );
              $this->load->model('relation_street_model');
              $relation_block = $this->relation_street_model->upload_anjuke_block($ajk);
          }else{
              preg_match('/message=(.*)Cache/siU', $tmpInfo, $errorlog);
              $paramlog['type'] = 2; //1成功,2失败
              $paramlog['info'] = empty($errorlog[1]) ? '发布失败' : urldecode($errorlog[1]);
              $log_id = $this->group_publish_model->add_publish_log($paramlog);
              $data = array('flag'=>9, 'info'=>trim($paramlog['info']), 'after'=>1);
          }
          return $data;
      }
      return false;
  }*/

  //安居客 下架
  //public function esta_delete($house_id, $act, $nolog=0, $queue_id=0, $signatory_id=0)
  public function esta_delete($house_id, $act, $param = array())
  {
    $this->load->model('group_publish_model');
    $site_id = $this->anjuke['id'];
    $signatory_id = empty($param['signatory_id']) ? $this->signatory_info['signatory_id'] : $param['signatory_id'];
    $esta_house_info = $this->group_publish_model->get_publish_by_site_id($site_id, $house_id, $act, $signatory_id);
    $publish_id = $esta_house_info[0]['publish_id'];
    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();
    $the_type = $house_info['sell_type'];

    $login = $this->isbind($signatory_id, $site_id);
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
    if (!empty($login['cookies']) && $publish_id) {
      $cookie = $login['cookies'];
      if ($the_type == 3 || $the_type == 4) {
        $post_url = 'http://my.anjuke.com/ajksignatory/ajax/house/jp_house?act=deleteHouses'; //商铺 写字楼
      } elseif ($act == 'sell') {
        $post_url = 'http://my.anjuke.com/ajksignatory/ajax/combo/ajk/delete_house/'; //二手房
      } else {
        $post_url = "http://my.anjuke.com/ajksignatory/ajax/house/hz_house?act=deleteHouses"; //租房
      }
      $post_fields = 'houseIds=' . $publish_id;
      $tmpInfo = $this->curl->vpost($post_url, $post_fields, $cookie, false, false);  //目标站点 删除

      $tmpInfo = json_decode($tmpInfo, true);
      if (is_array($tmpInfo) && isset($tmpInfo['status']) && 'ok' == $tmpInfo['status']) {
        $result['state'] = 'success';

        $paramlog['type'] = 1; //1成功,2失败
        $paramlog['info'] = '下架成功';
      }
      $bool = $this->group_publish_model->del_info_by_publish_id($publish_id, $act);  //数据库 删除
    }
    if (empty($param['nolog'])) {
      $this->group_publish_model->add_esta_log($paramlog); //重新发布 不加入下架日志
    }
    return $result;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($alias = '', $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type', TRUE);  //民宅 别墅 商铺 写字楼
    $keyword = trim($keyword);
    $site_id = $this->anjuke['id'];
    $signatory_id = $this->signatory_info['signatory_id'];

    $list = array();
    $login = $this->isbind($signatory_id, $site_id);
    if (!empty($login['cookies'])) {
      $cookie = $login['cookies'];
      switch ($sell_type) {
        case 3:
          $type = '&site=jp&pb=property';
          break; //商铺
        case 4:
          $type = '&site=jp&pb=building';
          break; //写字楼
        default :
          $type = '';
          break; //住宅
      }
      $url = 'http://my.anjuke.com/ajksignatory/ajax/community/search/?q=' . urlencode($keyword) . $type;
      $tmpInfo = $this->curl->vget($url, $cookie);
      $info = json_decode($tmpInfo, true);
      if ($info['data']) {
        foreach ($info['data'] as $key => $val) {
          if ($key == 10) break;
          $list[] = array('id' => $val['id'], 'label' => $val['name']);
        }
      }
    }
    return $list;
  }

  //上传图片到 安居客
  public function upload_image($url, $signatory_id)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site('anjuke', $signatory_id);
    if ($login['cookies'] && !empty($finalname)) {
      $post_url = 'http://upd1.ajkimg.com/upload-anjuke';
      $post_fielde = array('file' => '@' . $finalname);
      $tmpInfo = $this->curl->vpost($post_url, $post_fielde, $login['cookies']);
      preg_match('/{"host":1,"id":"(.*)",.*;}.*}/siU', $tmpInfo, $pigname);
      @unlink($finalname);
      return $pigname;
    }
    return false;
  }

  /*
  //检查登录
  public function check_login()
  {
      $username = $this->input->get_post('username');
      $password = $this->input->get_post('password');
      if( empty($username) ){
          $where = array('signatory_id'=>$this->signatory_info['signatory_id'], 'site_id'=>$this->anjuke['id'], 'status'=>1);
          $site_signatory = $this->site_model->get_signatoryinfo_byids($where);
          if( empty($site_signatory[0]) ) return false;
          $username = $site_signatory[0]['username'];
          $password = $site_signatory[0]['password'];
      }

      $post_fields = array();
      $post_fields['username'] = $username;
      $post_fields['password'] = $password;
      $post_fields['loginpost'] = 1;
      $post_fields['sid'] = 'anjukemy';
      $post_fields['url'] = 'aHR0cDovL3d3dy5hbmp1a2UuY29t';
      $post_fields['systemtime'] = time();
      $post_fields['fromsignatory'] = 1;
      $post_fields['act'] = 'login';

      $tmpInfo = $this->curl->vlogin('http://my.anjuke.com/usercenter/login', $post_fields); //登录操作
      preg_match('/<title>(.*)<\/title>.*URL=(.*)"/siU', $tmpInfo, $pro);
      if( $pro[1]=='登录成功  - 安居客通行证' )
      {
          $getUrl = explode('?',$pro[2]);
          $conInfo = $this->curl->vpost($getUrl[0], $getUrl[1]);
          preg_match_all("/set\-cookie:([^\r\n]*)/i", $conInfo, $matches);
          $cookie = implode(';',$matches[1]);
          $resInfo = $this->curl->vget('http://my.anjuke.com/user/combo/signatoryhome', $cookie); //获取用户信息
          preg_match('/<input type="hidden" name="ql_signatory" value="(.*)" id="ql_signatory_id"\/>/siU', $resInfo, $uid);
      }
      if( !empty($uid[1]) ){
          return array('userid'=>$uid[1], 'cookie'=>$cookie);
      }else{
          return false;
      }
  }
  */

  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $signatory_info = $this->signatory_info;
    $signatory_id = $signatory_info['signatory_id'];

    $this->load->model('group_publish_model');
    $this->load->model('group_queue_model');
    if ($act == 'sell') {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $block_info = array();
    $block_name = $this->input->get('block_name', TRUE);
    if ($block_name) {
      $block_info['block_name'] = $block_name;
      $block_info['block_id'] = $this->input->get('block_id', TRUE);
    } else {
      $this->load->model('relation_street_model');
      $relation_block = $this->relation_street_model->relation_block($house_info['block_id'], $signatory_info['company_id']);
      if (!empty($relation_block['ajk_block_id'])) {
        $block_info['block_name'] = $relation_block['ajk_block_name'];
        $block_info['block_id'] = $relation_block['ajk_block_id'];
      }
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
    } elseif ($house_info['floor'] > $house_info['totalfloor']) {
      return array('flag' => 'proerror', 'info' => '所在楼层不能大于总楼层');
    } elseif (mb_strlen($house_info['title']) < 5 || mb_strlen($house_info['title']) > 30) {
      return array('flag' => 'proerror', 'info' => '房源标题5-30字');
    } elseif (mb_strlen($house_info['bewrite']) < 20 || mb_strlen(strip_tags($house_info['bewrite'])) > 5000) {
      return array('flag' => 'proerror', 'info' => '房源描述20-5000字');
    } elseif (!in_array($house_info['sell_type'], array(1, 2, 3, 4))) {
      return array('flag' => 'proerror', 'info' => '安居客不支持厂房、仓库、车库');
    }
    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';  //2楼盘字典
    } else {
      //判断是否已经发布
      $publishinfo = $this->group_publish_model->get_num_sell_publish($signatory_id, $site_id, $house_id);
      if ($publishinfo) {
        $extra = array('nolog' => 1, 'signatory_id' => $signatory_id);
        $del = $this->esta_delete($house_id, $act, $extra);
      }
      //加入定时任务
      $group = $this->group_queue_model->get_queue_one(array('id' => $queue_id));
      if ($group) {
        $group['info'] = serialize($block_info);
        $demon = $this->group_queue_model->add_queue_demon($group);
      }

      if (!empty($demon)) {
        $data['flag'] = 'success';  //加入定时任务
      } else {
        $data['flag'] = 'error';  //0错误
      }
    }
    return $data;
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

      $extra = array('nolog' => 0, 'queue_id' => $queue_id, 'signatory_id' => $signatory_id);
      $data = $this->esta_delete($house_id, $act, $extra);
      return $data;
    }
    return false;
  }

  //发布数据组装
  public function publish_param($queue)
  {
    $this->load->library('CitySite');
    $this->load->model('signatory_info_model');
    $city = $this->config->item('login_city');

    $signatory_id = $queue['signatory_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $block_info = unserialize($queue['info']);
    if ($queue['tbl'] == 1) {
      $act = 'sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $act = 'rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);  //经纪人信息
    $mass_signatory = $this->site_mass_model->isbind_site('anjuke', $signatory_id);  //目标站点 帐号信息
    $userid = $mass_signatory['user_id'];
    $username = $mass_signatory['username'];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $config = $this->citysite->anjuke_house($city, $act);   //安居客房屋类新 装修类型 朝向
    $fit = $config['fit'][$house_info['fitment']];   //装修
    $face = $config['face'][$house_info['forward']]; //朝向
    $tmp_equipment = array();  //租房 设施
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($config['equipment'][$val])) {
          $tmp_equipment[] = $config['equipment'][$val];
        }
      }
    }
    $str_equipment = implode('&houseconfig[]=', $tmp_equipment);

    //上传图片
    $shinei_pic = $huxing_pic = '';
    $shinei = $huxing = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        if (in_array($house_info['sell_type'], array(3, 4))) {  //3商铺 4写字楼
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            $shinei = $this->upload_image($picurl, $signatory_id);
            $shinei_pic .= '&dropDesc[' . $shinei[1] . ']=0&hideaid[]=' . $shinei[1] . '&newupdroom[]=' . $shinei[0] . '&roomorder[]=' . $shinei[1];
          }
        } else {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            if ($val['type'] == 1) {
              $shinei = $this->upload_image($picurl, $signatory_id);
              $shinei_pic .= '&updroom[]=' . $shinei[0] . '&newupdroom[]=' . $shinei[0] . '&roomorder[]=' . $shinei[1] . '&file=&delRoom=[]&change_img_sort=0';
            } else if ($val['type'] == 2) {
              $huxing = $this->upload_image($picurl, $signatory_id);
              $huxing_pic .= '&updmodel[]=' . $huxing[0] . '&newupdmodel[]=' . $huxing[0] . '&modelorder[]=' . $huxing[1] . '&file=&delModel=[]&changeModelSort=0';
            }
          }
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

    $house_info['bewrite'] = $this->autocollect_model->special_flow($house_info['bewrite']);  //描述富文本处理

    $data_house = array();
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    $data_house['block_id'] = $block_id = $block_info['block_id'];
    $data_house['block_name'] = $block_name = $block_info['block_name'];
    $price = round($house_info['price'], 0);
    $area = round($house_info['buildarea'], 0);
    $unit_price = round($house_info['price'] * 10000 / $house_info['buildarea'], 0);  //每平米均价
    if ($act == 'sell') { //出售 sell
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/ajk';
        $house_tmp = empty($house_info['house_type']) ? 1 : $house_info['house_type'];
        $tmp_type = ($the_type == 2) ? $config['type']['bs'] : $config['type'][$house_tmp];
        $data_house['post_fielde'] = 'UseType=' . $tmp_type;
        $data_house['post_fielde'] .= '&act=publish&fixPlanId=&signatory_id=' . $userid . '&areaTplId=0&commTplId=0&commpropTplId=0&CommId=' . $block_id
          . '&communityName=' . $block_name . '&commname=' . $block_name . '&AreaNum=' . $area . '&ProPrice=' . $price
          . '&minDownPay=&houseAge=' . $house_info['buildyear'] . '&RoomNum=' . $house_info['room'] . '&HallNum=' . $house_info['hall']
          . '&ToiletNum=' . $house_info['toilet'] . '&fitment=' . $fit . '&houseori=' . $face . '&ProFloor=' . $house_info['floor']
          . '&FloorNum=' . $house_info['totalfloor'] . '&isFullFive=1&isOnly=1&ProName=' . $house_info['title']
          . '&tags=' . $shinei_pic . $huxing_pic . '&signatory_action=save';
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpss';
        $data_house['post_fielde'] = 'communityId=' . $block_id . '&community=' . $block_name . '&area=' . $area . '&shop_total_price=' . $price
          . '&office_manage_fee=' . $house_info['strata_fee'] . '&select_floor=1&single_floor=' . $house_info['floor']
          . '&business_state=2&shop_title=' . $house_info['title'] . '&map_zoom=15&hasMapDataFlag=0' . $shinei_pic
          . '&file=&delRoom=[]&goto_next_step=justSaveHouse&fix_plan_id=';
      } elseif ($the_type == 4) { //写字楼
        $level = $house_info['totalfloor'] / 3;
        $office_floor = ($house_info['floor'] < $level) ? 1 : ($house_info['floor'] < ($level * 2) ? 2 : 3);
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpos';
        $data_house['post_fielde'] = 'communityId=' . $block_id . '&community=' . $block_name . '&office_area=' . $area . '&office_total_price=' . $price
          . '&office_unit_price=' . $unit_price . '&office_floor=' . $office_floor . '&office_title=' . $house_info['title'] . $shinei_pic
          . '&office_efficient_rate=85&file=&delRoom=[]&goto_next_step=justSaveHouse&fix_plan_id=';
      }
      $data_house['act'] = 'sell';
      $data_house['post_fielde'] .= '&ProDesc=' . html_entity_decode($house_info['bewrite']) . '&defaultImgID=&anjuke_publish_rules=on';
    } else {  //出租 rent
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $house_tmp = empty($house_info['house_type']) ? 1 : $house_info['house_type'];
        $tmp_type = ($the_type == 2) ? $config['type']['bs'] : $config['type'][$house_tmp];
        $housetype = '&housetype=' . $tmp_type;
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/hz/';
        $data_house['post_fielde'] = 'commonid=' . $block_id . '&commname=' . $block_name . '&areaid=&renttype=1&room=' . $house_info['room']
          . '&hall=' . $house_info['hall'] . '&toliet=' . $house_info['toilet'] . '&floor=' . $house_info['floor'] . '&floorall=' . $house_info['totalfloor']
          . $housetype . '&decoration=' . $fit . '&toward=' . $face . '&rentsex=0&roomtoward=&housearea=' . $area . '&rentprice=' . $price
          . '&paytype=3|1&housetitle=' . $house_info['title'] . $huxing_pic . '&defaultImgID=0&html_action=save&houseconfig[]=' . $str_equipment;
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpsr';
        $data_house['post_fielde'] = 'communityId=' . $block_id . '&community=' . $block_name . '&area=' . $area . '&monthly_rent=' . $price
          . '&rent_type=1&office_manage_fee=' . $house_info['strata_fee'] . '&single_floor=' . $house_info['floor']
          . '&select_floor=1&single_family=&select_industry=&business_state=2&shop_title=' . $house_info['title']
          . '&map_zoom=18&hasMapDataFlag=0&file=&delRoom=[]&defaultImgID=&goto_next_step=justSaveHouse&fix_plan_id=';
      } elseif ($the_type == 4) { //写字楼
        $level = $house_info['totalfloor'] / 3;
        $office_floor = ($house_info['floor'] < $level) ? 1 : ($house_info['floor'] < ($level * 2) ? 2 : 3);
        if ($house_info['strata_fee'] && $house_info['costs_type'] == 1) {
          $house_info['strata_fee'] = $house_info['strata_fee'] / $area;
        }
        $day_price = $price / 30 / $area;
        $data_house['post_url'] = 'http://my.anjuke.com/ajksignatory/house/publish/jpor';
        $data_house['post_fielde'] = 'communityId=' . $block_id . '&community=' . $block_name . '&office_area=' . $area . '&office_monthly_total_rent=' . $price
          . '&office_manage_fee=' . $house_info['strata_fee'] . '&office_floor=' . $office_floor . '&office_title=' . $house_info['title']
          . '&office_efficient_rate=65&delRoom=[]&defaultImgID=&goto_next_step=justSaveHouse&fix_plan_id=&office_daily_rent=' . $day_price;
      }
      $data_house['act'] = 'rent';
      $data_house['post_fielde'] .= '&ProDesc=' . html_entity_decode($house_info['bewrite']) . $shinei_pic . '&anjuke_publish_rules=on';
    }
    $data_house['house_block_id'] = $house_info['block_id'];

    $result = $this->publish($data_house, $signatory_id, $house_id, $site_id, $queue['id']);
    if (isset($result['flag']) && $result['flag'] == 'success') {
      //记录 安居客楼盘
      $ajk = array('block_id' => $data_house['house_block_id'],  //我们的block_id
        'department_id' => $signatory_info['department_id'],
        'company_id' => $signatory_info['company_id'],
        'ajk_block_id' => $data_house['block_id'],
        'ajk_block_name' => $data_house['block_name']
      );
      $this->load->model('relation_street_model');
      $relation_block = $this->relation_street_model->upload_anjuke_block($ajk);
    }
    return $result;
  }
}

/* End of file site_anjuke_model.php */
/* Location: ./application/mls_guli/models/site_anjuke_model.php */
