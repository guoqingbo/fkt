<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_wuba_base_model");

class Site_wuba_model extends Site_wuba_base_model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
  }

  //检查是否绑定
  public function isbind_wuba($vip)
  {
    $site_id = ($vip == '58P') ? $this->wubap['id'] : $this->wubaw['id'];
    $broker_id = $this->broker_info['broker_id'];
    $site_broker = $this->site_model->get_brokerinfo_byids(array('broker_id' => $broker_id, 'site_id' => $site_id, 'status' => 1));
    if (!empty($site_broker[0])) {
      $data['otherpwd'] = $site_broker[0]['otherpwd'];
      $data['username'] = $site_broker[0]['username'];
      $data['password'] = $site_broker[0]['password'];
      $data['cookie'] = $site_broker[0]['cookies'];
    } else {
      $data['cookie'] = '';
    }
    return $data;
  }

  //58W 重新 绑定 帐号
  public function bind_again()
  {
    $extra = array();
    $extra['p3'] = $this->input->get('p3');
    $extra['alias'] = $this->input->get('alias');
    $extra['username'] = $this->input->get('username');
    $extra['timesign'] = $this->input->get('timesign');
    $extra['vcodekey'] = $this->input->get('vcodekey');
    $extra['checkcode'] = $this->input->get('checkcode');
    $extra['city_spell'] = $this->wuba_city_spell;

    $login = $this->login($extra);
    if ($login['userid']) {
      $where = array('broker_id' => $this->broker_info['broker_id'], 'site_id' => $this->wubaw['id']);
      $param = array('cookies' => $login['cookie'], 'createtime' => time());
      $result = $this->modify_data($where, $param, 'db_city', 'mass_site_broker');
      $data = array('flag' => 'success', 'param' => $param);
    } elseif ($login['error'] == 'wbcode') {
      $data = array('flag' => 'wbcode', 'vcodekey' => $login['vcodekey']);
    } else {
      $data = array('flag' => 'error', 'info' => $login['info']);
    }
    return $data;
  }

  //绑定帐号
  public function save_bind()
  {
    $extra = array();
    $extra['p3'] = $p3 = $this->input->get('p3');
    $extra['username'] = $username = $this->input->get('username');
    $extra['password'] = $password = $this->input->get('password');
    $extra['timesign'] = $timesign = $this->input->get('timesign');
    $extra['vcodekey'] = $vcodekey = $this->input->get('vcodekey');
    $extra['checkcode'] = $code = $this->input->get('checkcode');
    $extra['alias'] = $alias = $this->input->get('site_id');
    $extra['wuba_phone'] = $wuba_phone = $this->input->get('wuba_phone');
    $extra['city_spell'] = $this->wuba_city_spell;

    $login = $this->login($extra);
    if (!$login) {
      return 123;  //绑定失败
    } elseif (!empty($login['error'])) { //wbcode  yes
      return $login;
    } else {
      $site_id = ($alias == '58P') ? $this->wubap['id'] : $this->wubaw['id'];
      $broker_info = $this->broker_info;
      $city = $this->wuba_city_spell;
      $cityid = $this->wuba_city_id;
      $cookie = $login['cookie'];
    }
    //判断是否vip
    $tmpInfo = $this->curl->vget('https://my.58.com/index', $cookie);   //7.23改版
    preg_match('/<li >.*<a.*href="\/\/vip.58.com".*>(.*)中心<\/a>/siU', $tmpInfo, $prj);

    if ($prj[1] == 'VIP' && $alias == '58W') {
      //带入58发布自动载入的 联系人 and 电话
      $conInfo = $this->curl->vget('http://qy.58.com/entedit', $cookie);
      preg_match('/id="txtPhone" type="text" value="(.*)"/siU', $conInfo, $phone);
      preg_match('/id="txtContacts" type="text" value="(.*)"/siU', $conInfo, $man);
      $phone = empty($phone[1]) ? '' : trim($phone[1]);
      $man = $man[1];
      $other = $prj[1];
      if ($wuba_phone == '2') {
        $phone = $broker_info['phone'];
        $man = $broker_info['truename'];
      }
      if (empty($phone)) {
        return array('error' => 'yes', 'info' => '您所绑定的58网邻通帐号企业联系电话为空，请选择' . SOFTWARE_NAME . '手机号进行绑定');
      }
    } else {
      preg_match('/<input type="hidden" name="myInputToken" id="myInputToken" value="(.*)">/siU', $tmpInfo, $token);
      $other = empty($token[1]) ? '' : $token[1];
      if ($alias == '58P') {
        $phone = $broker_info['phone'];
        $man = $broker_info['truename'];
      } else {
        return array('error' => 234, 'type' => '58网邻通');
      }
    }

    $data = array();
    $data['broker_id'] = $broker_info['broker_id'];
    $data['site_id'] = $site_id;
    $data['status'] = '1';
    $data['username'] = $username;
    $data['password'] = $password;
    $data['user_id'] = $login['userid'];
    $data['cookies'] = $cookie;
    $data['createtime'] = time();
    $data['otherpwd'] = $phone . "|" . $man . "|" . $other;
    //根据用户id和站点来判断mass_site_broker表里是否存在 是：则更新 否：则插入
    $where = array('broker_id' => $broker_info['broker_id'], 'site_id' => $site_id);
    $find = $this->get_data(array('form_name' => 'mass_site_broker', 'where' => $where), 'dbback_city');
    if (count($find) >= 1) {
      $result = $this->modify_data($where, $data, 'db_city', 'mass_site_broker');
    } else {
      $result = $this->add_data($data, 'db_city', 'mass_site_broker');
    }
    return $data;
  }

  /**
   * 列表导入 58租房,二手房同一个页面,用$act区别
   * @param string $vip :58P or 58W
   * @param string $act :sell or rent
   * @return number|string
   */
  public function collect_list($vip, $act)
  {
    $login = $this->isbind_wuba($vip);
    $cookie = empty($login['cookie']) ? '' : $login['cookie'];

    $num = 0;
    $data_list = array();
    if ($cookie && $vip == '58P') {
      $project = array();
      for ($i = 1; ; $i++) {
        $listurl = 'http://my.58.com/xinxiguanli/' . $i;
        $tmpInfo = $this->curl->vget($listurl, $cookie);
        preg_match_all('/<tr id="tr[0-9]*" .*>(.*)<\/tr>/siU', $tmpInfo, $prj);
        if (count($prj[1]) > 0) {
          $project = array_merge($project, $prj[1]);
        } else {
          break;
        }
      }
      $outtype = ($act == 'rent') ? '租房' : '二手房';
      foreach ($project as $val) {
        preg_match("/房产信息<\/a>.*$outtype<\/a>/", $val, $intype);
        if (empty($intype[0])) continue;
        preg_match('/<div class="divpos">.*<a target="_blank" href="(.*)">(.*)<\/a>.*<span class="titletd">/siU', $val, $infourl);
        preg_match("/<a id='update[0-9]*' href='(.*)'.*>修改<\/a>/siU", $val, $url);
        preg_match('/更新时间:(.*)<cite>/siU', $val, $releasetime);

        $data = array();
        $data['source'] = 0;
        $data['url'] = empty($url[1]) ? '' : $url[1];              //编辑链接
        $data['infourl'] = empty($infourl[1]) ? '' : $infourl[1];  //详情链接
        $data['title'] = trim($infourl[2]);    //标题
        $data['des'] = '';
        $data['releasetime'] = trim($releasetime[1]) ? strtotime($releasetime[1]) : ''; //发布时间
        $data['city_spell'] = $this->broker_info['city_spell'];
        $data['broker_id'] = $this->broker_info['broker_id'];
        $data['site_id'] = $this->wubap['id'];
        $data_list[] = $data;
        $num++;
      }
      $res = $this->autocollect_model->add_list_indata($act, $data_list);
      return $num;
    } elseif ($cookie && $vip == '58W') {
      $project = $aa = array();
      $listurl = ($act == 'sell') ? 'http://fang.vip.58.com/fangyuan/12/' : 'http://fang.vip.58.com/fangyuan/8/';
      for ($i = 1; ; $i++) {
        $tempurl = $listurl . '?pageindex=' . $i;
        $tmpInfo = $this->curl->vget($tempurl, $cookie);
        preg_match_all('/<div id="tr[0-9]*" class="tgitembox1">(.*)<font id="cubestate/siU', $tmpInfo, $prj);
        if (count($prj[1]) > 0) {
          $project = array_merge($project, $prj[1]);
        } else {
          break;
        }
      }
      foreach ($project as $val) {
        preg_match('/infoId="([0-9]*)"/siU', $val, $infoid);
        preg_match('/<h2 class="fangchan_removeBr">.*\]<a href="(.*)".*>(.*)<\/a>/siU', $val, $title);
        preg_match('/<span class="ccc">(.*)<\/span>.*<\/p>/siU', $val, $des);
        //preg_match('/发布：(.*)<span class="ml15">/siU',$val, $releasetime);
        $data = array();
        $data['source'] = 0;
        $data['url'] = 'http://fang.vip.58.com/edit/' . $infoid[1];  //编辑链接
        $data['infourl'] = trim($title[1]);  //详情链接'http://hrb.58.com/zufang/'.$infoid[1].'x.shtml'
        $data['title'] = trim($title[2]);    //标题
        $data['des'] = strip_tags($des[0]);
        $data['releasetime'] = time(); //发布时间,网邻通时间没有年份
        $data['city_spell'] = $this->broker_info['city_spell'];
        $data['broker_id'] = $this->broker_info['broker_id'];
        $data['site_id'] = $this->wubaw['id'];
        $data_list[] = $data;
        $num++;
      }
      $res = $this->autocollect_model->add_list_indata($act, $data_list);
      return $num;
    } else {
      return 'no cookie';
    }
  }

  //详情页面导入:二手房
  public function collect_sell_info($vip)
  {
    $infourl = $this->input->get('infourl');
    $find = $this->isbind_wuba($vip);
    $city_spell = $this->broker_info['city_spell'];
    $orientation = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $serverco = array('毛坯' => 1, '简单装修' => 2, '中等装修' => 3, '精装修' => 4, '豪华装修' => 5);

    if ($find['cookie']) {
      $tmpInfo = $this->curl->vget($infourl);
      preg_match('/小区名称：.*target="_blank">(.*)<\/a>/siU', $tmpInfo, $house_name);
      preg_match('/<div class="su_tit">户型：<\/div>.*([0-9]*)室.*([0-9]*)厅.*([0-9]*)卫/siU', $tmpInfo, $room);
      preg_match('/朝向：<\/li>.*<li class="des_cols2">(.*)<\/li>/siU', $tmpInfo, $forward);
      preg_match('/房屋楼层：<\/li>.*<li class="des_cols2">([0-9]*)\/([0-9]*)楼/siU', $tmpInfo, $floor);
      preg_match('/装修程度：<\/li>.*<li class="des_cols2">(.*)\s/siU', $tmpInfo, $build);
      preg_match('/<div class="su_tit">户型：.*([0-9\.]*)㎡/siU', $tmpInfo, $buildarea);
      preg_match('/售价：<\/div>.*<div class="su_con">.*<span class="bigpri arial">([0-9\.]*)<\/span>/siU', $tmpInfo, $price);
      preg_match('/<h1>.*\(出售\)(.*)<\/h1>/siU', $tmpInfo, $title);
      preg_match_all('/<div class="descriptionImg">.*<img src="(.*)" alt/siU', $tmpInfo, $pic);    //图片
      preg_match('/<article class="description_con " >(.*)<p class="mb20" name="data_2">/siU', $tmpInfo, $content);    //房源描述
      $house_info = $image = array();
      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = 1;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $room[2];    //厅
      $house_info['toilet'] = $room[3];  //卫
      $face = trim($forward[1]) ? trim($forward[1]) : '东';
      $house_info['forward'] = $orientation[$face];    //朝向
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $floor[2]; //总楼层
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $house_info['buildarea'] = $buildarea[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['owner'] = '';
      $house_info['telno1'] = '';
      $house_info['kitchen'] = 0;
      $house_info['balcony'] = 0;
      return $house_info;
    } else {
      return false;
    }
  }

  //详情页面导入:租房
  public function collect_rent_info($vip)
  {
    $infourl = $this->input->get('infourl');
    $find = $this->isbind_wuba($vip);
    $city_spell = $this->broker_info['city_spell'];
    $orientation = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $serverco = array('毛坯' => 1, '简单装修' => 2, '中等装修' => 3, '精装修' => 4, '豪华装修' => 5);

    if ($find['cookie']) {
      $tmpInfo = $this->curl->vget($infourl);
      //preg_match('/onclick="clickLog\(\'from=fcpc_detail_nj_xiaoquxq_xiaoqu\'\)">(.*)<\/a>/siU', $tmpInfo, $house_name);
      preg_match('/<div class="fl xiaoqu c70">.*>(.*)<\/a>.*>(.*)<\/a>&nbsp;-&nbsp;(.*)</siU', $tmpInfo, $house_name);
      preg_match('/房屋：.*c70">.*([0-9]*)室.*([0-9]*)厅.*([0-9]*)卫.*([0-9\.]*) m².*([0-9]*)\/([0-9]*)层/siU', $tmpInfo, $room);
      preg_match('/房屋：.*c70">.*<br>(.*)&nbsp;-&nbsp;朝向(.*)&nbsp;-&nbsp;(.*)/siU', $tmpInfo, $build);
      preg_match('/租金：.*<em class="house-price">([0-9\.]*)<\/em>/siU', $tmpInfo, $price);
      preg_match('/<h1 class="main-title font-heiti">(.*)<\/h1>/siU', $tmpInfo, $title);
      preg_match_all('/<li class="house-images-wrap">.*<img lazy_src="(.*)".*width="640"/siU', $tmpInfo, $pic);
      preg_match('/<div class="description-content">(.*)<div id="fytupian">/siU', $tmpInfo, $content);    //房源描述

      $house_info = $image = array();
      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = 1;  //住宅类型
      $temp_name = strip_tags($house_name[3]);
      $house_info['house_name'] = trim($temp_name); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $room[2];    //厅
      $house_info['toilet'] = $room[3];  //卫
      $house_info['buildarea'] = $room[4];   //面积
      $house_info['floor'] = $room[5];   //楼层
      $house_info['totalfloor'] = $room[6];  //总楼层
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '简单装修';
      $house_info['serverco'] = $serverco[$serverco_temp];   //装修
      $forward_temp = trim($build[2]) ? trim($build[2]) : '东';
      $house_info['forward'] = $orientation[$forward_temp];    //朝向
      $house_info['price'] = empty($price[1]) ? '' : $price[1];  //租金
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['owner'] = '';
      $house_info['telno1'] = '';
      $house_info['kitchen'] = 0;
      $house_info['balcony'] = 0;
      return $house_info;
    } else {
      return false;
    }
  }

  //58网灵通 刷新
  public function refresh_vip($house_id, $act)
  {
    $site_id = $this->wubaw['id'];
    $house_info = $this->group_publish_model->get_publish_detail($site_id, $house_id, $act);
    $id = $house_info['id'];
    $publish_id = $house_info['publish_id'];
    $refresh_times = $house_info['refresh_times'] + 1;

    $login = $this->isbind_wuba('58W');
    if ($login['cookie']) {
      $cookie = $login['cookie'];
      $prom_url = 'http://fang.vip.58.com/promotion/add/' . $publish_id . '/1/100301/?protype=0&priceid=0'; //1、2、 3、 4、 5、 6、 7、 15天 选择
      $promInfo = $this->curl->vpost($prom_url, '', $cookie); //推送
      $post_url = 'http://fang.vip.58.com/manualrefresh/' . $publish_id . '/100301/?r=' . time() . '000';
      $tmpInfo = $this->curl->vpost($post_url, '', $cookie);  //刷新
      //立即刷新成功
      if (strpos($tmpInfo, '立即刷新成功')) {
        $param = array('refresh_times' => $refresh_times, 'updatetime' => time());
        $this->group_publish_model->update_data($id, $param, $act);
        $result = array('state' => 'success');

        //张建统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝
        $this->load->model('group_refresh_model');
        $this->group_refresh_model->info_count($this->broker_info, 2);
        //王欣统计
        $ref_param = array('broker_id' => $this->broker_info['broker_id'],
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
        preg_match('/,"msg":"(.*)",/siU', $tmpInfo, $error);
        $info = empty($error[1]) ? '刷新失败' : $error[1];
        $result = array('state' => 'failed', 'info' => $info);
      }
    }
    return $result;
  }

  //上传图片到 58
  public function upload_image($url, $broker_id, $alias)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site($alias, $broker_id);
    if ($login['cookies'] && !empty($finalname)) {
      $post_url = 'http://post.image.58.com/upload';
      $post_fielde = array(
        'fileUploadInput' => '@' . $finalname,
        'name' => 'Jeky',
        'backFunction' => 'SINGLEUP.addImg',
        '__pic_dir' => 'p1',
        'PicPos' => '0'
      );
      $tmpInfo = $this->curl->vpost($post_url, $post_fielde, $login['cookies']);
      preg_match("/parent.SINGLEUP.addImg\(.*'(.*)', 0/siU", $tmpInfo, $pigname);
      @unlink($finalname);

      if (!empty($pigname[1]))
        return $pigname[1];
    }
    return false;
  }

  //群发匹配目标站点楼盘名 普通 网邻通 通用
  public function get_keyword($vip, $act)
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type', TRUE);  //民宅 别墅 商铺 写字楼
    $keyword = trim($keyword);
    $city = $this->wuba_city_spell;
    $cityid = $this->wuba_city_id;

    $list = array();
    $find = $this->isbind_wuba($vip);
    if ($find['cookie'] && in_array($sell_type, array(1, 2))) {
      $cat = ($act == 'sell') ? 12 : 8;
      $url = 'http://suggest.58.com.cn/searchsuggest_6.do?inputbox=' . urlencode($keyword) . '&cityid=' . $cityid . '&catid=' . $cat . '&type=1&callback=callback1111';
      $tmpInfo = $this->curl->vget($url, $find['cookie']);
      $tmpInfo = str_replace('callback1111(', '', $tmpInfo);
      $tmpInfo = str_replace(' ] })', ' ] }', $tmpInfo);
      $tmpInfo = json_decode($tmpInfo, true);
      if ($tmpInfo) {
        foreach ($tmpInfo['w'] as $key => $val) {
          if ($key == 10) break;
          $list[] = array(
            'label' => $val['k'],
            'address' => $val['s'],
            'district' => $val['r'],
            'street' => $val['m'],
            'id' => $val['id']);
        }
      }
    }
    return $list;
  }

  //群发获取58区属板块
  public function get_block()
  {
    $city = $this->wuba_city_spell;
    $cityid = $this->wuba_city_id;
    $district = $street = array();

    $tmpInfo = $this->curl->vget('http://post.58.com/' . $cityid . '/13/s5');
    preg_match('/value="">--区域--<\/option>(.*)id="localDiduan"/siU', $tmpInfo, $area);
    preg_match('/id="selectDiduanHidden" style="display:none">(.*)<\/select>.*<\/td>/siU', $tmpInfo, $diduan);
    preg_match_all('/<option value="([0-9]*)">(.*)<\/option>/siU', $area[1], $localArea);
    preg_match_all("/<option id='([0-9]*)_[0-9]*' value='([0-9]*)'>(.*)<\/option>/siU", $diduan[1], $localDiduan);
    foreach ($localArea[1] as $k => $val) {
      $district[$val] = $localArea[2][$k];
    }
    foreach ($localDiduan[2] as $k => $val) {
      $street[$val] = array('pro' => $localDiduan[1][$k], 'name' => $localDiduan[3][$k]);
    }
    $data = array('district' => $district, 'street' => $street);
    return $data;
  }

  //定时任务下架
  public function queue_esta($queue)
  {
    $this->load->model('group_publish_model');
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $broker_id = $queue['broker_id'];
    $tbl = $queue['tbl'];
    $dealtime = $queue['createtime'] ? $queue['createtime'] : time();

    if ($tbl == 1) {
      $house_tbl = 'sell_house';
      $pub_tbl = 'group_publish_sell';
    } else {
      $house_tbl = 'rent_house';
      $pub_tbl = 'group_publish_rent';
    }

    //帐号信息
    $login = $this->site_mass_model->isbind_site_by_id($site_id, $broker_id);
    //发布信息
    $pub_sql = array('where' => array('broker_id' => $broker_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
    $pubInfo = $this->get_data($pub_sql, 'dbback_city');
    $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
    //房源信息
    $house_sql = array('where' => array('id' => $house_id), 'form_name' => $house_tbl);
    $houseInfo = $this->get_data($house_sql, 'dbback_city');
    $block_id = $houseInfo ? $houseInfo[0]['block_id'] : 0;
    $the_type = $houseInfo ? $houseInfo[0]['sell_type'] : 0;

    //日志表
    $paramlog = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'site_id' => $site_id,
      'block_id' => $block_id,
      'sell_type' => $tbl, //1出售,2出租
      'ymd' => time(),
      'username' => isset($login['username']) ? $login['username'] : 0,
      'dealtime' => $dealtime,
      'type' => 2,
      'info' => '下架失败'
    );
    $result = array('flag' => 'error', 'info' => '下架失败');

    if (isset($login['cookies']) && $publish_id && $houseInfo) {
      $post = array('login' => $login, 'publish' => $pubInfo[0], 'htype' => $the_type, 'tbl' => $tbl);
      $result = $this->esta($post);
      if ($result) {
        $paramlog['type'] = ($result['flag'] == 'success') ? 1 : 2;  //1成功 2失败
        $paramlog['info'] = $result['info'];
      }
    }
    $addlog = $this->group_publish_model->add_esta_log($paramlog);    //加入下架日志
    $bool = $this->del(array('broker_id' => $broker_id, 'site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
    return $result;
  }

  //是否转移到定时任务
  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $broker_id = $this->broker_info['broker_id'];

    $this->load->model('group_publish_model');
    $this->load->model('group_queue_model');
    if ($act == 'sell') {
      $tbl = 1;
      $pub_tbl = 'group_publish_sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $tbl = 2;
      $pub_tbl = 'group_publish_rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();
    $group = $this->group_queue_model->get_queue_one(array('id' => $queue_id));  //队列信息

    //定时任务 需要的数据
    if ($group['isback']) {
      $block_info = array();
      $block_info['uuid'] = $this->input->get('uuid', TRUE);
      $block_info['voice'] = $this->input->get('voice', TRUE);
      $block_info['check'] = $this->input->get('check', TRUE);
      $block_info['vcodekey'] = $this->input->get('vcodekey', TRUE);
      $block_info['checkcode'] = $this->input->get('checkcode', TRUE);

      $tmpinfo = array_merge(unserialize($group['info']), $block_info);
      $group['info'] = serialize($tmpinfo);
      $demon = $this->group_queue_model->add_queue_demon($group);   //加入定时任务
      $del = $this->group_queue_model->delete_queue(array('id' => $queue_id));  //删除队列任务1
      $data['flag'] = 'success';
      return $data;
    }

    $block_info = array();
    $block_name = $this->input->get('block_name', TRUE);
    if ($block_name) {
      $block_info['block_name'] = $block_name;
      $block_info['block_id'] = $this->input->get('block_id', TRUE);
      $block_info['address'] = $this->input->get('address', TRUE);
      if (empty($block_info['address'])) {
        $block_info['address'] = $house_info['address'];
      }
      $block_info['street'] = $this->input->get('street', TRUE);
      $block_info['district'] = $this->input->get('district', TRUE);
    } elseif ($alias == '58P') {
      $this->load->model('relation_street_model');
      $relation_street = $this->relation_street_model->select_relation_street($house_info['street_id'], $house_info['district_id']);
      if (!empty($relation_street[0])) {
        $block_info['block_id'] = '';
        $block_info['block_name'] = $house_info['block_name'];
        $block_info['address'] = $house_info['address'];
        $block_info['street'] = $relation_street[0]['wuba_street_id'];
        $block_info['district'] = $relation_street[0]['wuba_dist_id'];
      }
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
    } elseif ($house_info['floor'] > $house_info['totalfloor']) {
      return array('flag' => 'proerror', 'info' => '所在楼层不能大于总楼层');
    } elseif (mb_strlen($house_info['title']) < 8 || mb_strlen($house_info['title']) > 30) {
      return array('flag' => 'proerror', 'info' => '房源标题8-30字');
    } elseif (preg_match("/\d{7,}/", $house_info['Title'])) {
      return array('flag' => 'proerror', 'info' => '房源标题不能填写电话');
    } elseif (in_array($house_info['sell_type'], array(1, 2)) && mb_strlen($house_info['bewrite']) < 10) {
      return array('flag' => 'proerror', 'info' => '房源描述最少10个字');
    }
    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';   //2楼盘字典
    } else {
      //判断是否已经发布
      $pub_sql = array('where' => array('broker_id' => $broker_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
      $pubInfo = $this->get_data($pub_sql, 'dbback_city');
      $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
      if ($publish_id) {
        $login = $this->site_mass_model->isbind_site_by_id($site_id, $broker_id);
        $extra = array('login' => $login, 'publish' => $pubInfo[0], 'tbl' => $tbl, 'htype' => $house_info['sell_type']);
        $del = $this->esta($extra);
        $bool = $this->del(array('house_id' => $house_id, 'site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
      }

      //加入定时任务
      $group['info'] = serialize($block_info);
      $demon = $this->group_queue_model->add_queue_demon($group);
      if (!empty($demon)) {
        $data['flag'] = 'success';  //加入定时任务
      } elseif (!empty($block_info['district'])) {
        $data['flag'] = 'error';  //错误
      } elseif (in_array($the_type, array(3, 4, 5, 6, 7))) {
        $data['flag'] = '58block';
        $data['block_area'] = $this->get_block(); //新加 商铺 写字楼 厂房
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
      $broker_id = $group[0]['broker_id'];
      $act = $group[0]['tbl'] == 1 ? 'sell' : 'rent';

      return parent::refresh_vip($broker_id, $house_id, $act, '', $queue_id);
    }
  }

  //发布数据拼装
  public function publish_param($queue)
  {
    $this->load->model('broker_info_model');
    $site_id = $queue['site_id'];
    $site_info = $this->site_mass_model->get_only_site($site_id);
    $alias = $site_info['alias'];
    if ($alias == '58P') {
      return $this->publish_param_normal($queue);
    } else {
      return $this->publish_param_vip($queue);
    }
  }

  //58普通 发布
  protected function publish_param_normal($queue)
  {
    $broker_id = $queue['broker_id'];
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

    $login_city = $this->config->item('login_city');  //经纪人 城市
    $wuba_city = $this->citysite->wuba_city($login_city);
    $cityid = $wuba_city['id'];  //58专属城市id
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $mass_broker = $this->site_mass_model->isbind_site('58P', $broker_id);  //目标站点 帐号信息
    $userlist = explode('|', $mass_broker['otherpwd']);
    $userid = $mass_broker['user_id'];
    $username = $userlist[1];
    $userphone = $userlist[0];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();  //房源信息
    $config = $this->getconfig();
    $fittype = $config['fit'][$house_info['fitment']];
    $forward = $config['face'][$house_info['forward']];
    $equipment = $config['equipment'];  //租房 房屋设施
    $tmp_equipment = array();
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($equipment[$val])) {
          $tmp_equipment[] = $equipment[$val];
        }
      }
    }
    $str_equipment = implode('|', $tmp_equipment);

    //上传图片
    $pic = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          $shinei = $this->upload_image($picurl, $broker_id, '58P');
          $pic .= $shinei . "|";
        }
      }
    }
    $this->load->database();
    $this->db_city->reconnect();

    //修改房源权限
    $this->load->model('broker_permission_model');
    $this->broker_permission_model->set_broker_id($broker_id, $broker_info['company_id']);
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($broker_id != $house_info['broker_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['broker_id'], $database = 'db_city');
        if (!empty($result)) {
          $house_info['title'] = $result[0]['title'];
          $house_info['bewrite'] = $result[0]['content'];
        }
      }
    }

    $data_house = array();
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    $data_house['uuid'] = $uuid = $block_info['uuid'];
    $data_house['voice'] = $voice = $block_info['voice'];
    $data_house['check'] = $check = $block_info['check'];
    $data_house['checkcode'] = $checkcode = $block_info['checkcode'];
    $data_house['userphone'] = $userphone;
    $block_id = $block_info['block_id'];
    $block_name = $block_info['block_name'];
    $district = $block_info['district'];
    $street = $block_info['street'];
    $address = $block_info['address'];
    if ($act == 'sell') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $objectType = ($the_type == 2) ? 5 : 3;
        $data_house['dispcateid'] = 12;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/12/s5/submit';
        $data_house['post_fielde'] = 'type=0&isBiz=1&xiaoqu=' . $block_id . '&xiaoquname=' . $block_name . '&localArea=' . $district . '&localDiduan=' . $street
          . '&dizhi=' . $address . '&huxingshi=' . $house_info['room'] . '&huxingting=' . $house_info['hall'] . '&huxingwei=' . $house_info['toilet']
          . '&ObjectType=' . $objectType . '&fittype=' . $fittype . '&Toward=' . $forward . '&chanquannianxian=70&chanquan=1&jianzhuniandai=' . $house_info['buildyear']
          . '&Floor=' . $house_info['floor'] . '&zonglouceng=' . $house_info['totalfloor'] . '&yzm=' . $checkcode . '&fangyuantese=';
      } elseif ($the_type == 3) { //商铺
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 14;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/14/s5/submit';
        $data_house['post_fielde'] = 'type=0&fenlei=511570&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=4';
      } elseif ($the_type == 4) { //写字楼
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 13;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/13/s5/submit';
        $data_house['post_fielde'] = 'type=2&loupan=' . $block_name . '&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address
          . '&ObjectType=1&danwei=1';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $address = mb_substr($address, 0, 11, 'utf-8');
        $objectType = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 5);
        $data_house['dispcateid'] = 15;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/15/s5/submit';
        $data_house['post_fielde'] = 'type=4&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=' . $objectType;
      }
      $data_house['act'] = 'sell';
      $data_house['post_fielde'] .= '&area=' . round($house_info['buildarea'], 0) . '&minPrice=' . round($house_info['price'], 0) . '&Pic=' . $pic . '&PicPos=0'
        . '&Title=' . urlencode($house_info['title']) . '&Content=' . urlencode($house_info['bewrite']) . '&Phone=' . $userphone . '&goblianxiren=' . $username
        . '&userid=' . $userid . '&postparam_userid=' . $userid . '&captcha_type=400&captcha_input=' . $checkcode
        . '&hidPostParam=0&post_captcha_biz=phone_verify';
    } elseif ($act == 'rent') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $objectType = ($the_type == 2) ? 5 : 3;
        $tmp_pay = empty($house_info['rentpaytype']) ? 9 : $house_info['rentpaytype'];    //默认面议
        $pay_t = empty($config['paytype'][$tmp_pay]) ? 0 : $config['paytype'][$tmp_pay]; //付款方式
        $data_house['dispcateid'] = 8;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/8/s5/submit';
        $data_house['post_fielde'] = 'HireType=2&isBiz=1&xiaoqu=' . $block_id . '&xiaoquname=' . $block_name . '&localArea=' . $district . '&localDiduan=' . $street
          . '&dizhi=' . $address . '&huxingshi=' . $house_info['room'] . '&huxingting=' . $house_info['hall'] . '&huxingwei=' . $house_info['toilet']
          . '&ObjectType=' . $objectType . '&FitType=' . $fittype . '&Toward=' . $forward . '&HouseAllocation=' . urlencode($str_equipment) . '&fukuanfangshi=' . $pay_t
          . '&Floor=' . $house_info['floor'] . '&zonglouceng=' . $house_info['totalfloor'] . '&userid=' . $userid . '&yzm=' . $checkcode
          . '&jz_refresh_post_key=0&postparam_openidtype=&selectBiz=0';
      } elseif ($the_type == 3) { //商铺
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 14;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/14/s5/submit';
        $data_house['post_fielde'] = 'type=2&fenlei=511570&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=4&danwei=2';
      } elseif ($the_type == 4) { //写字楼
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 13;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/13/s5/submit';
        $data_house['post_fielde'] = 'type=0&loupan=' . $block_name . '&localArea=' . $district . '&localDiduan=&diduan=' . $address . '&ObjectType=1&danwei=1';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $address = mb_substr($address, 0, 11, 'utf-8');
        $objectType = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 5);
        $data_house['dispcateid'] = 15;
        $data_house['post_url'] = 'http://post.58.com/' . $cityid . '/15/s5/submit';
        $data_house['post_fielde'] = 'type=0&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=' . $objectType . '&danwei=3';
      }
      $data_house['act'] = 'rent';
      $data_house['post_fielde'] .= '&area=' . round($house_info['buildarea'], 0) . '&minPrice=' . round($house_info['price'], 0) . '&Pic=' . $pic . '&PicPos=0&IM='
        . '&Title=' . urlencode($house_info['title']) . '&Content=' . urlencode($house_info['bewrite']) . '&Phone=' . $userphone . '&goblianxiren=' . $username
        . '&postparam_userid=' . $userid . '&captcha_type=400&captcha_input=' . $checkcode . '&hidPostParam=0&post_captcha_biz=phone_verify';
    }
    $data_house['house_block_id'] = $house_info['block_id'];
    $result = $this->publish($data_house, $broker_id, $house_id, $site_id, $queue['id']);
    return $result;
  }

  //网灵通 发布
  protected function publish_param_vip($queue)
  {
    $broker_id = $queue['broker_id'];
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

    $login_city = $this->config->item('login_city');  //经纪人 城市
    $wuba_city = $this->citysite->wuba_city($login_city);
    $cityid = $wuba_city['id'];  //58专属城市id
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $mass_broker = $this->site_mass_model->isbind_site('58W', $broker_id);  //目标站点 帐号信息
    $userlist = explode('|', $mass_broker['otherpwd']);
    $userid = $mass_broker['user_id'];
    $username = $userlist[1];
    $userphone = $userlist[0];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();  //房源信息
    $config = $this->getconfig();
    $fittype = $config['fit'][$house_info['fitment']];
    $forward = $config['facevip'][$house_info['forward']];
    $equipment = $config['equipment'];  //租房 房屋设施
    $tmp_equipment = array();
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($equipment[$val])) {
          $tmp_equipment[] = $equipment[$val];
        }
      }
    }
    $str_equipment = implode('|', $tmp_equipment);

    //上传图片
    $pic = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          $shinei = $this->upload_image($picurl, $broker_id, '58W');
          $pic .= $shinei . "|";
        }
      }
      $this->load->database();
      $this->db_city->reconnect();
    }

    //修改房源权限
    $this->load->model('broker_permission_model');
    $this->broker_permission_model->set_broker_id($broker_id, $broker_info['company_id']);
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($broker_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($broker_id != $house_info['broker_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['broker_id'], $database = 'db_city');
        if (!empty($result)) {
          $house_info['title'] = $result[0]['title'];
          $house_info['bewrite'] = $result[0]['content'];
        }
      }
    }

    $house_info['title'] = str_replace($config['cn_str'], $config['en_str'], $house_info['title']);
    $data_house = array();
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    $block_id = $block_info['block_id'];
    $block_name = $block_info['block_name'];
    $district = $block_info['district'];
    $street = $block_info['street'];
    $address = $block_info['address'];

    //https://epost.58.com/v37/12/s5//submit?rand=0.04256258950771874     2016.08.31 接口地址改版
    if ($act == 'sell') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $objectType = ($the_type == 2) ? 5 : 3;
        $yijuhua = $house_info['block_name'] . ' 好房出售';  //一句话广告 必填
        $data_house['dispcateid'] = 12;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/12/s5//submit';
        $data_house['post_fielde'] = 'type=0&xiaoqu=' . $block_id . '&refpricexiaoquid=' . $block_id . '&localArea=' . $district . '&localDiduan=' . $street
          . '&dizhi=' . $address . '&jushishuru=' . $house_info['room'] . '&huxingting=' . $house_info['hall'] . '&huxingwei=' . $house_info['toilet']
          . '&ObjectType=' . $objectType . '&jianzhujiegou=&fittype=' . $fittype . '&Toward=' . $forward . '&chanquannianxian=70&chanquan=1'
          . '&jianzhuniandai=' . $house_info['buildyear'] . '&Floor=' . $house_info['floor'] . '&zonglouceng=' . $house_info['totalfloor']
          . '&yijuhuaguanggao=' . $yijuhua . '&huxingshi=' . $house_info['room']
          . '&BuildingEra=17&fangyuantese=chanquannianxian@70年产权&peitaosheshi=&dingceng=0&xiaobao_option=0';
      } elseif ($the_type == 3) { //商铺
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 14;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/14/s5//submit';
        $data_house['post_fielde'] = 'type=0&fenlei=511570&localArea=' . $district . '&localDiduan=' . $street . '&diduan3=' . $address . '&ObjectType=4';
      } elseif ($the_type == 4) { //写字楼
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 13;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/13/s5//submit';
        $data_house['post_fielde'] = 'type=2&loupan=' . $block_name . '&localArea=' . $district . '&localDiduan=' . $street . '&shangquan=' . $street
          . '&selectDiduanHidden=' . $street . '&diduan=' . $address . '&ObjectType=1&IsBiz=1';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $address = mb_substr($address, 0, 11, 'utf-8');
        $objectType = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 5);
        $data_house['dispcateid'] = 15;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/15/s5//submit';
        $data_house['post_fielde'] = 'type=4&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=' . $objectType;
      }
      $data_house['act'] = 'sell';
      $data_house['post_fielde'] .= '&area=' . round($house_info['buildarea'], 0) . '&minPrice=' . round($house_info['price'], 0) . '&Pic=' . $pic
        . '&Title=' . urlencode($house_info['title']) . '&Content=' . urlencode($house_info['bewrite']) . '&Phone=' . $userphone . '&goblianxiren=' . $username
        . '&postparam_userid=' . $userid . '&hidPostParam=0' . '&PicPos=0&IM=';
    } elseif ($act == 'rent') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $objectType = ($the_type == 2) ? 5 : 3;
        $tmp_pay = empty($house_info['rentpaytype']) ? 9 : $house_info['rentpaytype'];    //默认面议
        $pay_t = empty($config['paytype'][$tmp_pay]) ? 0 : $config['paytype'][$tmp_pay]; //付款方式
        $yijuhua = $house_info['block_name'] . ' 好房出租';  //一句话广告 必填
        $data_house['dispcateid'] = 8;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/8/s5//submit';
        $data_house['post_fielde'] = 'HireType=2&xiaoqu=' . $block_id . '&localArea=' . $district . '&localDiduan=' . $street . '&dizhi=' . $address
          . '&jushishuru=' . $house_info['room'] . '&huxingting=' . $house_info['hall'] . '&huxingwei=' . $house_info['toilet'] . '&FitType=' . $fittype
          . '&Toward=' . $forward . '&HouseAllocation=' . urlencode($str_equipment) . '&fukuanfangshi=' . $pay_t . '&yijuhua=' . $yijuhua
          . '&ObjectType=' . $objectType . '&Floor=' . $house_info['floor'] . '&zonglouceng=' . $house_info['totalfloor'] . '&huxingshi=' . $house_info['room'];
      } elseif ($the_type == 3) { //商铺
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 14;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/14/s5//submit';
        $data_house['post_fielde'] = 'type=2&fenlei=511570&localArea=' . $district . '&localDiduan=' . $street . '&diduan3=' . $address . '&ObjectType=4&danwei=2';
      } elseif ($the_type == 4) { //写字楼
        $address = mb_substr($address, 0, 11, 'utf-8');
        $data_house['dispcateid'] = 13;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/13/s5//submit';
        $data_house['post_fielde'] = 'type=0&loupan=' . $block_name . '&localArea=' . $district . '&localDiduan=' . $street . '&shangquan=' . $street
          . '&selectDiduanHidden=' . $street . '&diduan=' . $address . '&ObjectType=1&IsBiz=1&danwei=3';
      } elseif (in_array($the_type, array(5, 6, 7))) { //厂房 仓库 车库
        $address = mb_substr($address, 0, 11, 'utf-8');
        $objectType = ($the_type == 6) ? 1 : (($the_type == 7) ? 2 : 5);
        $data_house['dispcateid'] = 15;
        $data_house['post_url'] = 'https://epost.58.com/v' . $cityid . '/15/s5//submit';
        $data_house['post_fielde'] = 'type=0&localArea=' . $district . '&localDiduan=' . $street . '&diduan=' . $address . '&ObjectType=' . $objectType . '&danwei=3';
      }
      $data_house['act'] = 'rent';
      $data_house['post_fielde'] .= '&area=' . round($house_info['buildarea'], 0) . '&MinPrice=' . round($house_info['price'], 0) . '&Pic=' . $pic
        . '&Title=' . urlencode($house_info['title']) . '&Content=' . urlencode($house_info['bewrite']) . '&Phone=' . $userphone . '&goblianxiren=' . $username
        . '&postparam_userid=' . $userid . '&hidPostParam=0' . '&PicPos=0&IM=';
    }
    $data_house['house_block_id'] = $house_info['block_id'];
    return $this->publish($data_house, $broker_id, $house_id, $site_id, $queue['id']);
  }

}

//++++++++++++++++++测试用++++++++++++++
//      $tmpInfo = strip_tags($tmpInfo);
// 	    $p1 = "adbbaec4ec73dbad18575bb4ffb6c230";
// 	    $p2 = "34c69577f38decb8afe548cdcc155cce";
// 	    $p3 = "189222c09568eba39ef0e783c33b4c0b66…………d3a06ec";
// 	    $timesign = '1453174658596';
// 	    $username = '经纬360A713';
// $t1 = microtime(true);
// $t2 = microtime(true);
// echo '耗时'.round($t2-$t1,3).'秒';
/* End of file site_wuba_model.php */
/* Location: ./application/mls/models/site_wuba_model.php */
