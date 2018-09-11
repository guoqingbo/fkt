<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_ajkvip_base_model");

class Site_ajkvip_model extends Site_ajkvip_base_model
{

  public function __construct()
  {
    $this->load->model('site_model');
    parent::__construct();
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $username = $this->input->get('username');
    $password = $this->input->get('password');
    $otherpwd = $this->input->get('otherpwd');

    $login = $this->login($username, $password, $otherpwd);
    if (!$login) {
      return '123';  //绑定失败
    } else {
      $data = array();
      $data['broker_id'] = $broker_id;
      $data['site_id'] = $site_id;
      $data['status'] = '1';
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['user_id'];
      $data['cookies'] = $login['cookies'];
      $data['createtime'] = time();
      $data['otherpwd'] = $otherpwd;

      //根据用户id和站点来判断mass_site_broker表里是否存在 是：则更新 否：则插入
      $where = array('broker_id' => $broker_id, 'site_id' => $site_id);
      $find = $this->get_data(array('form_name' => 'mass_site_broker', 'where' => $where), 'dbback_city');
      if (count($find) >= 1) {
        $result = $this->modify_data($where, $data, 'db_city', 'mass_site_broker');
      } else {
        $result = $this->add_data($data, 'db_city', 'mass_site_broker');
      }
      return $data;
    }
  }

  //列表采集
  public function collect_list($act)
  {
    $num = 0;
    $otherpwd = $this->input->post('otherpwd');
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $login = $this->isbind($broker_id, $otherpwd);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];

    if ($cookie) {
      if ($act == 'sell') {
        $list_one = 'http://vip.anjuke.com/combo/broker/manage/ajk/';
        $list_two = 'http://vip.anjuke.com/combo/broker/manage/ajk/?from=V2';
        $type = array('楼售', '铺售');
      } else {
        $list_one = 'http://vip.anjuke.com/combo/broker/manage/hz/';
        $list_two = 'http://vip.anjuke.com/combo/broker/manage/hz/?from=V2';
        $type = array('楼租', '铺租');
      }
      $tmpInfo = $this->curl->vget($list_one, $cookie);
      $moreInfo = $this->curl->vget('http://vip.anjuke.com/combo/broker/manage/jp/', $cookie); //商业地产:商铺 and 写字楼
      if (empty($tmpInfo)) {
        $tmpInfo = $this->curl->vget($list_two, $cookie);
      }
      preg_match_all('/tr id="house_[0-9]*"(.*)<\/tr>/siU', $tmpInfo, $prj);
      preg_match_all('/tr id="house_[0-9]*"(.*)<\/tr>/siU', $moreInfo, $more);

      foreach ($more[1] as $val) {
        preg_match('/class="ui-table-operate".*<a href="(.*)".*target="_blank"/siU', $val, $url);
        preg_match('/<a href="(.*)" target="_blank">\[(.*)\](.*)<\/a>.*propview/siU', $val, $infourl);
        preg_match('/<p>(.*)<\/p>.*编号/siU', $val, $des);
        preg_match('/<td class="num">\s*([0-9]*)\s*<\/td>/siU', $val, $days);

        if (!in_array($infourl[2], $type)) continue;
        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];  //编辑链接
        $data['infourl'] = $infourl[1];  //详情链接
        $data['title'] = trim($infourl[3]) . ' [' . $infourl[2] . ']';  //标题
        $data['des'] = strip_tags($des[1]);  //描述
        $data['releasetime'] = trim($days[1]) ? strtotime(($days[1] - 90) . ' day') : ''; //发布时间
        $data['city_spell'] = $this->broker_info['city_spell'];
        $data['broker_id'] = $this->broker_info['broker_id'];
        $data['site_id'] = $this->website['id'];

        if ($act == 'sell') {
          $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
        } else {
          $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
        }
        if ($res) $num++;
      }
      foreach ($prj[1] as $val) {
        preg_match('/class="ui-table-operate".*推广.*a href="(.*)".*编辑/siU', $val, $url);
        preg_match('/<div>.*a href="(.*)" target="_blank">(.*)<\/a>.*propview/siU', $val, $infourl);
        preg_match('/<p>(.*)<\/p>.*编号/siU', $val, $des);
        preg_match('/<td class="num">\s*([0-9]*)\s*<\/td>/siU', $val, $days);

        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];  //编辑链接
        $data['infourl'] = $infourl[1];  //详情链接
        $data['title'] = trim($infourl[2]);  //标题
        $data['des'] = strip_tags($des[1]);  //描述
        $data['releasetime'] = trim($days[1]) ? strtotime(($days[1] - 90) . ' day') : ''; //发布时间
        $data['city_spell'] = $this->broker_info['city_spell'];
        $data['broker_id'] = $this->broker_info['broker_id'];
        $data['site_id'] = $this->website['id'];

        if ($act == 'sell') {
          $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
        } else {
          $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
        }
        if ($res) $num++;
      }
      return $num;
    } else {
      return 'no cookie';
    }
  }

  //详情页面导入:出售
  public function collect_sell_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $city_spell = $this->broker_info['city_spell'];

    $list = array('二手房' => 1, '商铺' => 3, '写字楼' => 4);
    $config = $this->getconfig();

    $login = $this->isbind($broker_id, '');
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      if (strstr($infourl, 'esffyxxd')) {
        $infourl = str_replace(array('view/'), array('view/A'), $infourl);
      }
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $cookie);
      preg_match('/<title>(房源编辑|编辑房源) - (.*) - 中国网络经纪人<\/title>/siU', $tmpInfo, $mytype);
      $mytype = trim($mytype[2]);
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = $tpimg = array();
    if (empty($tmpInfo)) {
      return $house_info;
    }
    if ($the_type == 3) {  //商铺
      preg_match('/安居客商铺名称：<\/label>.*<span>(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/type="text" name="floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="roomarea"  value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="salePrice_group_c" value="88"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)" alt=/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim(strip_tags($house_name[1])); //楼盘名称
      $house_info['floor'] = empty($floor[1]) ? '' : $floor[1];  //楼层
      $house_info['totalfloor'] = ''; //总楼层
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/安居客写字楼：<\/label>.*<span>(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/name="roomarea" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/type="text" value="(.*)" name="salePrice_normal"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)" \/>/siU', $picInfo, $pic);    //图片

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
    } else {  //民宅 别墅
      preg_match('/房屋类型.*selected="selected">(.*)<\/option>.*select-housefit/siU', $tmpInfo, $dtype);
      preg_match('/安居客对应小区.*<span>(.*)<\/span/siU', $tmpInfo, $house_name);
      preg_match('/name="room" maxlength="1" value="(\d)"/siU', $tmpInfo, $room);
      preg_match('/name="hall" maxlength="1" value="(\d)"/siU', $tmpInfo, $hall);
      preg_match('/name="bathroom" maxlength="1"\s*value="(\d)"/siU', $tmpInfo, $toilet);
      preg_match('/朝向<\/option>.*selected="selected">(.*)<\/option>/siU', $tmpInfo, $forward);
      preg_match('/装修情况<\/option>.*selected="selected">(.*)<\/option>/siU', $tmpInfo, $build);
      preg_match('/name="floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="allFloor" value="([0-9]*)"/siU', $tmpInfo, $totalfloor);
      preg_match('/name="allArea" value="(.*)"/siU', $tmpInfo, $buildarea);
      preg_match('/data-group="realPriceGroupD" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);

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

      $house_info['sell_type'] = ($dtype[1] == '别墅') ? 2 : 1;  //类型2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $face = trim($forward[1]) ? trim($forward[1]) : '东';
      $house_info['forward'] = $config['forward'][$face];    //朝向
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $config['fitment'][$serverco_temp];   //装修
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $buildarea[1];  //建筑面积
      $house_info['price'] = $price[1];  //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/name="describe" placeholder="个性且详尽的房源描述可加快出售您的房子哦...">(.*)<\/textarea>/siU', $tmpInfo, $content);
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
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $city_spell = $this->broker_info['city_spell'];

    $list = array('二手房' => 1, '商铺' => 3, '写字楼' => 4);
    $config = $this->getconfig();

    $login = $this->isbind($broker_id, '');
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      if (!strstr($url, 'anjuke.com')) {
        $url = 'http://vip.anjuke.com' . $url;
      }
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $cookie);
      preg_match('/<title>(房源编辑|编辑房源) - (.*) - 中国网络经纪人<\/title>/siU', $tmpInfo, $mytype);
      $mytype = trim($mytype[2]);
      $the_type = empty($list[$mytype]) ? '' : $list[$mytype];
    }

    $house_info = $image = $tpimg = array();
    if (empty($tmpInfo)) {
      return $house_info;
    }
    if ($the_type == 3) {  //商铺
      preg_match('/安居客商铺名称：<\/label>.*<span>(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/type="text" name="floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="roomarea"  value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="rentPrice_cityD" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);
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
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/安居客写字楼：<\/label>.*<span>(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/name="roomarea" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/type="text" value="(.*)" name="rentPrice_cityGroupB"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);
      preg_match_all('/img data-lazy="(.*)" \/>/siU', $picInfo, $pic);    //图片

      foreach ($pic[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1] * $house_info['buildarea'] * 30;  //总价
      $house_info['title'] = trim($title[1]);    //标题
    } else {  //民宅 别墅
      preg_match('/房屋类型.*selected="selected">(.*)<\/option>.*select-housefit/siU', $tmpInfo, $dtype);
      preg_match('/安居客对应小区：<\/label>.*<span>(.*)<\/span>/siU', $tmpInfo, $house_name);
      preg_match('/name="room" maxlength="1" value="([0-9])"/siU', $tmpInfo, $room);
      preg_match('/name="hall" maxlength="1" value="([0-9])"/siU', $tmpInfo, $hall);
      preg_match('/name="bathroom" maxlength="1" value="([0-9])"/siU', $tmpInfo, $toliet);
      preg_match('/name="floor" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="allFloor" value="([0-9]*)"/siU', $tmpInfo, $totalfloor);
      preg_match('/朝向<\/option>.*selected="selected" >(.*)<\/option>/siU', $tmpInfo, $forward);
      preg_match('/装修情况<\/option>.*selected="selected" >(.*)<\/option>/siU', $tmpInfo, $build);
      preg_match('/name="roomarea" value="(.*)"/siU', $tmpInfo, $buildarea);
      preg_match('/type="text" value="(.*)" name="rentprice"/siU', $tmpInfo, $price);
      preg_match('/name="title" value="(.*)" \/>/siU', $tmpInfo, $title);

      preg_match('/class="tabscon tnow"(.*)<\/ul>/siU', $picInfo, $neipic);  //室内图
      preg_match_all('/img src="(.*)" alt=""/siU', $neipic[1], $pic_one);
      foreach ($pic_one[1] as $val) {
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }
      $house_info['picurl'] = implode('*', $image);//室内图
      $house_info['sell_type'] = ($dtype[1] == '别墅') ? 2 : 1;  //类型2别墅
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toliet[1];  //卫
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $face = trim($forward[1]) ? trim($forward[1]) : '东';
      $house_info['forward'] = $config['forward'][$face];    //朝向
      $serverco_temp = trim($build[1]) ? trim($build[1]) : '毛坯';
      $house_info['serverco'] = $config['fitment'][$serverco_temp];   //装修
      $house_info['buildarea'] = $buildarea[1];   //面积
      $house_info['price'] = empty($price[1]) ? '' : $price[1];  //租金
      $house_info['title'] = trim($title[1]);    //标题
    }
    preg_match('/name="describe" placeholder="个性且详尽的房源描述可加快出售您的房子哦...">(.*)<\/textarea>/siU', $tmpInfo, $content);
    $house_info['content'] = isset($content[1]) ? $content[1] : '';    //房源描述
    $house_info['kitchen'] = 0; //厨房
    $house_info['balcony'] = 0; //阳台
    $house_info['owner'] = '';  //业主姓名
    $house_info['telno1'] = ''; //业主电话
    return $house_info;
  }

  //下架
  public function esta_delete($extra)
  {
    $this->load->model('group_publish_model');
    $site_id = $this->website['id'];
    $house_id = $extra['house_id'];
    $broker_id = $extra['broker_id'];
    $tbl = $extra['tbl'];     //1出售 2出租
    $queue_id = isset($extra['queue_id']) ? $extra['queue_id'] : 0; //0为 重复发布
    $nolog = isset($extra['nolog']) ? $extra['nolog'] : 0;          //1时 不加入日志, 默认0
    if ($tbl == 1) {
      $house_tbl = 'sell_house';
      $pub_tbl = 'group_publish_sell';
    } else {
      $house_tbl = 'rent_house';
      $pub_tbl = 'group_publish_rent';
    }

    //帐号信息
    $login = $this->isbind($broker_id);
    //发布信息
    $pub_sql = array('where' => array('broker_id' => $broker_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
    $pubInfo = $this->get_data($pub_sql, 'dbback_city');
    $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
    //房源信息
    $house_sql = array('where' => array('id' => $house_id), 'form_name' => $house_tbl);
    $houseInfo = $this->get_data($house_sql, 'dbback_city');
    $block_id = $houseInfo ? $houseInfo[0]['block_id'] : 0;
    //队列信息
    $queue_sql = array('where' => array('id' => $queue_id), 'form_name' => 'group_queue_demon');
    $queueInfo = $queue_id ? $this->get_data($queue_sql, 'dbback_city') : '';
    $dealtime = empty($queueInfo) ? 0 : $queueInfo[0]['createtime'];

    //日志表
    $paramlog = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'site_id' => $site_id,
      'block_id' => $block_id,
      'sell_type' => $tbl, //1出售,2出租
      'ymd' => time(),
      'username' => $login['username'],
      'dealtime' => $dealtime,
      'type' => 2,
      'info' => '下架失败'
    );
    $result = array('flag' => 'error', 'info' => '下架失败');

    if (isset($login['cookies']) && $publish_id) {
      $post = array('cookies' => $login['cookies'], 'publish_id' => $publish_id, 'htype' => $houseInfo[0]['sell_type'], 'tbl' => $tbl);
      $result = $this->esta($post);
      if ($result) {
        $paramlog['type'] = ($result['flag'] == 'success') ? 1 : 2;  //1成功 2失败
        $paramlog['info'] = $result['info'];
      }
    }
    $addlog = $nolog ? '' : $this->group_publish_model->add_esta_log($paramlog);    //加入下架日志
    $bool = $this->del(array('site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
    return $result;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($alias = '', $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type');  //1民宅 2别墅 3商铺 4写字楼
    $otherpwd = $this->input->get('otherpwd');
    $keyword = trim($keyword);
    $broker_id = $this->broker_info['broker_id'];

    $list = array();
    $login = $this->isbind($broker_id, $otherpwd);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
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
      $url = 'http://vip.anjuke.com/ajax/community/search/?q=' . urlencode($keyword) . $type;
      $tmpInfo = $this->curl->vget($url, $cookie);
      $info = json_decode($tmpInfo, true);
      if ($info['data']) {
        foreach ($info['data'] as $key => $val) {
          if ($key == 10) break;
          $list[] = array(
            'id' => $val['id'],
            'label' => $val['name'],
            'district' => $val['district_id'],
            'street' => $val['block_id']
          );
        }
      }
    }
    return $list;
  }

  //上传图片
  public function upload_image($url, $broker_id)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site('ajkvip', $broker_id);
    if ($login['cookies'] && !empty($finalname)) {
      $post_url = 'http://upd1.ajkimg.com/upload-anjuke';
      $post_field = array('file' => '@' . $finalname);
      $tmpInfo = $this->curl->vpost($post_url, $post_field, $login['cookies']);
      preg_match('/{"host":1,"id":"(.*)",.*;}.*}/siU', $tmpInfo, $pigname);
      @unlink($finalname);
      return $pigname;
    }
    return false;
  }

  //是否转移到定时任务
  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $broker_info = $this->broker_info;
    $broker_id = $broker_info['broker_id'];

    $this->load->model('group_publish_model');
    $this->load->model('group_queue_model');
    if ($act == 'sell') {
      $tbl = 1;
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $tbl = 2;
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $block_info = array();
    $block_name = $this->input->get('block_name', TRUE);
    if ($block_name) {
      $block_info['block_name'] = $block_name;
      $block_info['block_id'] = $this->input->get('block_id');
      $block_info['district'] = $this->input->get('district');
      $block_info['street'] = $this->input->get('street');
      $block_info['otherpwd'] = $this->input->get('otherpwd');
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
      $publishinfo = $this->group_publish_model->get_num_sell_publish($broker_id, $site_id, $house_id);
      if ($publishinfo) {
        $extra = array('house_id' => $house_id, 'broker_id' => $broker_id, 'tbl' => $tbl, 'nolog' => 1);
        $del = $this->esta_delete($extra);
      }
      //加入定时任务
      $group = $this->group_queue_model->get_queue_one(array('id' => $queue_id));
      if ($group) {
        $block_info['otherpwd'] = $this->input->get('otherpwd');
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

  //定时任务下架
  public function queue_esta($queue)
  {
    $query = array('form_name' => 'group_queue_demon', 'where' => array('id' => $queue['id']));
    $group = $this->get_data($query, 'dbback_city');
    if ($group[0]) {
      $group = $group[0];
      $extra = array(
        'house_id' => $group['house_id'],
        'queue_id' => $group['id'],
        'broker_id' => $group['broker_id'],
        'tbl' => $group['tbl'],
        'nolog' => 0
      );
      $data = $this->esta_delete($extra);
      return $data;
    }
    return false;
  }

  //发布数据组装
  public function publish_param($queue)
  {
    $this->load->model('broker_info_model');
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

    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $mass_broker = $this->site_mass_model->isbind_site('ajkvip', $broker_id);  //目标站点 帐号信息
    $userid = $mass_broker['user_id'];
    $username = $mass_broker['username'];
    $otherpwd = $block_info['otherpwd'];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $config = $this->getconfig();   //安居客VIP配置
    //租房 设施
    $tmp_equipment = array();
    $config_equip = $house_info['sell_type'] == 3 ? $config['equipment_shop'] : $config['equipment'];
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($config_equip[$val])) {
          $tmp_equipment[] = $config_equip[$val];
        }
      }
    }
    $str_equipment = implode('&fitment[]=', $tmp_equipment);

    //上传图片
    $shinei_pic = $huxing_pic = '';
    $shinei = $huxing = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        if (in_array($house_info['sell_type'], array(3, 4))) {  //3商铺 4写字楼
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            $shinei = $this->upload_image($picurl, $broker_id);
            $shinei_pic .= '&dropDesc[' . $shinei[1] . ']=0&updroom[]=' . $shinei[0] . '&newupdroom[]=' . $shinei[0] . '&roomorder[]=' . $shinei[1];
          }
        } else {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            if ($val['type'] == 1) {
              $shinei = $this->upload_image($picurl, $broker_id);
              $shinei_pic .= '&updroom[]=' . $shinei[0] . '&newupdroom[]=' . $shinei[0] . '&roomorder[]=' . $shinei[1];
            } else if ($val['type'] == 2) {
              $huxing = $this->upload_image($picurl, $broker_id);
              $huxing_pic .= '&updmodel[]=' . $huxing[0] . '&newupdmodel[]=' . $huxing[0] . '&modelorder[]=' . $huxing[1];
            }
          }
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
    $data_house['block_id'] = $block_id = $block_info['block_id'];
    $data_house['block_name'] = $block_name = $block_info['block_name'];
    $block_district = $block_info['district'];
    $block_street = $block_info['street'];

    $login = $this->isbind($broker_id, '');
    $resInfo = $this->curl->vget('http://vip.anjuke.com/house/publish/ershou/?from=manage', $login['cookies']); //获取cityid
    preg_match('/name="cityId" type="hidden" value="([0-9]*)"\/>/siU', $resInfo, $cityTmp);
    $city_id = $cityTmp[1];          //安居客 城市id
    $fit = $config['fit'][$house_info['fitment']];   //装修
    $face = $config['face'][$house_info['forward']]; //朝向
    $price = round($house_info['price'], 0);  //价格
    $area = round($house_info['buildarea'], 0);  //面积
    $strata_fee = empty($house_info['strata_fee']) ? '' : round($house_info['strata_fee'], 1);  //物业费
    if ($act == 'sell') {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $htype = $the_type == 2 ? 2 : 8;
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/ershou/?from=manage';
        $data_house['post_field'] = 'action=publish&fixPlanId=&communityAJK=' . $block_name . '&ajk_CommId=' . $block_id . '&room=' . $house_info['room']
          . '&hall=' . $house_info['hall'] . '&bathroom=' . $house_info['toilet'] . '&housetype=' . $htype . '&housefit=' . $fit . '&houseorient=' . $face
          . '&floor=' . $house_info['floor'] . '&allFloor=' . $house_info['totalfloor'] . '&allArea=' . $area . '&age=' . $house_info['buildyear']
          . '&istwo=1&unique=1&price=' . $price . '&modelImagesSaved=' . $huxing_pic . '&outsideImagesSaved=';
      } elseif ($the_type == 3) { //商铺
        $floor = $house_info['floor_type'] == 2 ? $house_info['subfloor'] : '';
        $str_equipment = empty($str_equipment) ? '&fitment[]=5' : '&fitment[]=' . $str_equipment;  //此项必填
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/shop/?jpChooseType=2&chooseWeb[]=1';
        $data_house['post_field'] = 'act=publish&type=2&industrytype=&industry=&shopAJK=' . $block_name . '&ajk_jpId=' . $block_id
          . '&ajk_jpName=' . $block_name . '&zoneAJK=' . $block_district . '&blockAJK=' . $block_street . '&nearbyAJK=&floorType=' . $house_info['floor_type']
          . '&floor=' . $house_info['floor'] . '&allFloor=' . $floor . '&roomarea=' . $area . $str_equipment
          . '&status=2&otherfeature=&othercustomer=&rentPrice_cityD=&rentUnit=1&payModePay=&salePrice_group_c=' . $price
          . '&payModeMortgage=&electricFee=&assignFee=&parkingFee=&map_lng=&map_lat=&map_zoom=16&propertyFee=' . $strata_fee
          . '&hasMapDataFlag=0&pictype=0&sort_time=1';
      } elseif ($the_type == 4) { //写字楼
        $level = $house_info['totalfloor'] / 3;
        $floor_zone = ($house_info['floor'] < $level) ? 1 : ($house_info['floor'] < ($level * 2) ? 2 : 3);
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/office/?jpChooseType=1&chooseWeb[]=1';
        $data_house['post_field'] = 'act=publish&fixPlanId=&type=2&officeAJK=' . $block_name . '&ajk_jpId=' . $block_id . '&ajk_jpName=' . $block_name
          . '&floorZone=' . $floor_zone . '&roomarea=' . $area . '&areapercent=81&salePrice_normal=' . $price
          . '&rentPrice_cityGroupB=&rentUnit=2&propertyFee=' . $strata_fee . '&pictype=0&sort_time=1';
      }
    } else {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $housetype = $config['type'][$house_info['house_type']]; //房屋类型
        $paytype = $config['paytype'][$house_info['rentpaytype']];  //付款方式
        $paytype = empty($paytype) ? 7 : $paytype;
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/rent/?from=manage';
        $data_house['post_field'] = 'act=publish&rentType=1&communityAJK=' . $block_name . '&ajk_CommId=' . $block_id . '&ajk_communityName=' . $block_name
          . '&zoneAJK=&blockAJK=&addressAJK=&room=' . $house_info['room'] . '&hall=' . $house_info['hall'] . '&bathroom=' . $house_info['toilet']
          . '&floor=' . $house_info['floor'] . '&allFloor=' . $house_info['totalfloor'] . '&housetype=' . $housetype . '&housefit=' . $fit
          . '&exposure=' . $face . '&roomorient=&sex=0&roomarea=' . $area . '&fitment[]=' . $str_equipment . '&rentprice=' . $price . '&paymode=' . $paytype
          . '&modelImagesSaved=' . $huxing_pic . '&outsideImagesSaved=';
      } elseif ($the_type == 3) { //商铺
        $floor = $house_info['floor_type'] == 2 ? $house_info['subfloor'] : '';
        $str_equipment = empty($str_equipment) ? '&fitment[]=5' : '&fitment[]=' . $str_equipment;  //此项必填
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/shop/?jpChooseType=2&chooseWeb[]=1';
        $data_house['post_field'] = 'act=publish&type=1&industrytype=&industry=&shopAJK=' . $block_name . '&ajk_jpId=' . $block_id . '&ajk_jpName=' . $block_name
          . '&zoneAJK=' . $block_district . '&blockAJK=' . $block_street . '&nearbyAJK=&floorType=' . $house_info['floor_type']
          . '&floor=' . $house_info['floor'] . '&allFloor=' . $floor . '&roomarea=' . $area . $str_equipment
          . '&status=2&otherfeature=&othercustomer=&salePrice_group_c=&rentUnit=1&payModePay=&rentPrice_cityD=' . $price
          . '&payModeMortgage=&electricFee=&assignFee=&parkingFee=&propertyFee=' . $strata_fee
          . '&map_lng=&map_lat=&map_zoom=16&hasMapDataFlag=0&pictype=0&sort_time=1';
      } elseif ($the_type == 4) { //写字楼
        $level = $house_info['totalfloor'] / 3;
        $floor_zone = ($house_info['floor'] < $level) ? 1 : ($house_info['floor'] < ($level * 2) ? 2 : 3);
        $day_price = $price / 30 / $area; //元.㎡.天
        $data_house['post_url'] = 'http://vip.anjuke.com/house/publish/office/?jpChooseType=1&chooseWeb[]=1';
        $data_house['post_field'] = 'act=publish&fixPlanId=&type=1&officeAJK=' . $block_name . '&ajk_jpId=' . $block_id . '&ajk_jpName=' . $block_name
          . '&floorZone=' . $floor_zone . '&roomarea=' . $area . '&areapercent=81&salePrice_normal=&rentPrice_cityGroupB=' . $day_price
          . '&rentUnit=2&propertyFee=' . $strata_fee . '&pictype=0&sort_time=1';
      }
    }
    $data_house['act'] = $act;
    $data_house['post_field'] .= '&broker_id=' . $userid . '&cityId=' . $city_id . '&sites=1&roomImagesSaved=' . $shinei_pic . '&file=&defaultImgID='
      . '&title=' . urlencode($house_info['title']) . '&describe=' . urlencode($house_info['bewrite']) . '&_sign=' . urlencode($otherpwd);
    $data_house['house_block_id'] = $house_info['block_id'];

    $result = $this->publish($data_house, $broker_id, $house_id, $site_id, $queue['id']);
    return $result;
  }


}

/* End of file site_ajkvip_model.php */
/* Location: ./application/mls/models/site_ajkvip_model.php */
