<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_fdcnum_base_model");

class Site_fdcnum_model extends Site_fdcnum_base_model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $extra['username'] = $username = $this->input->get_post('username');
    $extra['password'] = $password = $this->input->get_post('password');
    $extra['sessid'] = $this->input->get('vcodekey');
    $extra['code'] = $this->input->get_post('checkcode');

    $login = $this->login($extra);
    if (!$login) {
      return '123';  //绑定失败
    } else {
      $data = array();
      $data['broker_id'] = $broker_id;
      $data['site_id'] = $site_id;
      $data['status'] = 1;
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = 0;
      $data['agent_id'] = 0;
      $data['otherpwd'] = '';
      $data['cookies'] = $login['cookies'];
      $data['createtime'] = time();

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
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];

    $config = $this->getconfig();
    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      $project = array();
      $listurl = ($act == 'sell') ? 'http://agent.360fdc.com/EsfList/' : 'http://agent.360fdc.com/CzfList/';
      for ($i = 1; ; $i++) {
        $tmpInfo = $this->curl->vget($listurl . 'OnLineList?pageIndex=' . $i . '&OrdersBy=1', $cookie);
        preg_match_all('/class="ps_r tab_content_cont">(.*)class="ps_a bdsharebuttonbox"/siU', $tmpInfo, $prj);
        if (count($prj[1]) > 0) {
          $project = array_merge($project, $prj[1]);
        } else {
          break;
        }
      }

      foreach ($project as $val) {
        preg_match('/class="li_03".*<a[\s]+href="(.*)"[\s]+>(.*)<\/a>/siU', $val, $url);
        preg_match('/<a class="a_target" aria-type="([0-9])"[\s]+aria-code="([0-9]*)" target="_blank">/siU', $val, $code);
        preg_match('/class="mt_10">(.*)<span class="font_B green">&nbsp;(.*)<\/span> <\/p>.*fonts24">(.*)<\/span>/siU', $val, $des);
        preg_match('/class="ps_r li_04 fonts12".*span>(.*)<\/span>/siU', $val, $pubtime);

        if ($code[1] == 3) {
          $url_mid = ($act == 'sell') ? 'shop-chushou' : 'shop-chuzu';  //商铺
          $name_mid = ' [商铺]';
        } elseif ($code[1] == 4) {
          $url_mid = ($act == 'sell') ? 'office-chushou' : 'office';  //写字楼
          $name_mid = ' [写字楼]';
        } else {
          $url_mid = ($act == 'sell') ? 'ershoufang' : 'zufang';  //二手房
          $name_mid = '';
        }
        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];   //编辑链接
        $data['infourl'] = '/' . $url_mid . '/' . $code[2] . '.html';    //详情链接
        $data['title'] = strip_tags($url[2]) . $name_mid;  //标题
        $data['des'] = $des[1] . ' ' . strip_tags($des[2]) . ' ' . $des[3];  //描述
        $data['releasetime'] = empty($pubtime[1]) ? 0 : strtotime(trim($pubtime[1]));   //发布时间
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

    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $picInfo = $this->curl->vget('http://www.360fdc.com/' . $infourl);
      $tmpInfo = $this->curl->vget('http://agent.360fdc.com/' . $url, $cookie);
      if (stripos($infourl, 'shop-chushou')) {
        $the_type = 3;
      } elseif (stripos($infourl, 'office-chushou')) {
        $the_type = 4;
      } else {
        $the_type = 1;
      }
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    //图片
    preg_match('/id="fimgs">(.*)<\/div>/siU', $picInfo, $pic_div);
    preg_match_all('/src="(.*)" class/siU', $pic_div[1], $pic);
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }

    if ($the_type == 1) {  //民宅 别墅
      preg_match("/onclick=\"GoMytemplate('Esf','\/EsfEdit\/AddEsf','([0-9])')\"/siU", $tmpInfo, $htype);
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $hname);
      preg_match('/name="fjNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $room);
      preg_match('/name="ktNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $hall);
      preg_match('/name="wsjNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $toilet);
      preg_match('/name="cfNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $kitchen);
      preg_match('/name="ytNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $balcony);
      preg_match('/name="cxname" type="hidden" value=" (.*)"/siU', $tmpInfo, $farward);
      preg_match('/name="zxname" type="hidden" value=" (.*)"/siU', $tmpInfo, $fitment);
      preg_match('/name="lc" maxlength="3" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="sumlc" maxlength="3" value="([0-9]*)"/siU', $tmpInfo, $allfloor);
      preg_match('/name="czmj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="price" id="price" maxlength="8" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="esfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/name="richEdit" id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $htype[1] ? $htype[1] : 1;  //类型2别墅
      $house_info['house_name'] = $hname[1];      //楼盘名称
      $house_info['room'] = $room[1];     //室
      $house_info['hall'] = $hall[1];     //厅
      $house_info['toilet'] = $toilet[1]; //卫
      $house_info['kitchen'] = $kitchen[1]; //厨房
      $house_info['balcony'] = $balcony[1]; //阳台
      $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $allfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['price'] = $price[1];       //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 3) {  //商铺
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $house_name);
      preg_match('/name="czmj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="cxname" type="hidden" value="(.*)"/siU', $tmpInfo, $farward);
      preg_match('/name="zxname" type="hidden" value="(.*)"/siU', $tmpInfo, $fitment);
      preg_match('/name="price" id="price" maxlength="8" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="WYFPrice" maxlength="6" value="(.*)"/siU', $tmpInfo, $fee);
      preg_match('/name="esfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['price'] = $price[1];     //总价
      $house_info['strata_fee'] = $fee[1];    //物业费 元/㎡·月
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = $title[1]; //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $house_name);
      preg_match('/name="czmj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="zxname" type="hidden" value="(.*)"/siU', $tmpInfo, $fitment);
      preg_match('/name="price" id="price" maxlength="8" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="WYFPrice" maxlength="6" value="(.*)"/siU', $tmpInfo, $fee);
      preg_match('/name="esfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['price'] = $price[1];
      $house_info['strata_fee'] = $fee[1];    //物业费 元/㎡·月
      $house_info['title'] = $title[1];     //标题
      $house_info['content'] = $content[1];       //描述
    }
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

    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $picInfo = $this->curl->vget('http://www.360fdc.com/' . $infourl);
      $tmpInfo = $this->curl->vget('http://agent.360fdc.com/' . $url, $cookie);
      if (stripos($infourl, 'shop-chuzu')) {
        $the_type = 3;
      } elseif (stripos($infourl, 'office')) {
        $the_type = 4;
      } else {
        $the_type = 1;
      }
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    //图片
    preg_match('/id="fimgs">(.*)<\/div>/siU', $picInfo, $pic_div);
    preg_match_all('/src="(.*)" class/siU', $pic_div[1], $pic);
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
      $image[] = $tempurl;
    }

    if ($the_type == 1) {  //民宅 别墅
      preg_match("/onclick=\"GoMytemplate('Czf','\/CzfEdit\/AddCzf','([0-9])')\"/siU", $tmpInfo, $htype);
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $house_name);
      preg_match('/name="fjNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $room);
      preg_match('/name="ktNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $hall);
      preg_match('/name="wsjNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $toilet);
      preg_match('/name="cfNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $kitchen);
      preg_match('/name="ytNumber" maxlength="1" value="([0-9])"/siU', $tmpInfo, $balcony);
      preg_match('/name="cxname" type="hidden" value="(.*)"/siU', $tmpInfo, $farward);
      preg_match('/name="zxname" type="hidden" value="(.*)"/siU', $tmpInfo, $fitment);
      preg_match('/name="lc" maxlength="3" value="([0-9]*)"/siU', $tmpInfo, $floor);
      preg_match('/name="sumlc" maxlength="3" value="([0-9]*)"/siU', $tmpInfo, $allfloor);
      preg_match('/name="mj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="rentPrice" id="rentPrice" maxlength="10" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="czfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/name="richEdit" id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $htype[1] ? $htype[1] : 1;  //类型2别墅
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['room'] = $room[1];     //室
      $house_info['hall'] = $hall[1];     //厅
      $house_info['toilet'] = $toilet[1];   //卫
      $house_info['kitchen'] = $kitchen[1];  //厨房
      $house_info['balcony'] = $balcony[1];  //阳台
      $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = $floor[1];    //楼层
      $house_info['totalfloor'] = $allfloor[1];   //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['price'] = $price[1];       //总价
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 3) {  //商铺
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $house_name);
      preg_match('/name="mj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="rentPrice" id="rentPrice" maxlength="10" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="czfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];     //租金 元.月
      $house_info['title'] = $title[1]; //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/name="BuildingName" value="(.*)" type="text"/siU', $tmpInfo, $house_name);
      preg_match('/name="mj" maxlength="8" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="zxname" type="hidden" value="(.*)"/siU', $tmpInfo, $fitment);
      preg_match('/name="rentPrice" id="rentPrice" maxlength="10" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="WYFPrice" maxlength="6" value="(.*)"/siU', $tmpInfo, $fee);
      preg_match('/name="czfname" value="(.*)" placeholder/siU', $tmpInfo, $title);
      preg_match('/id="richEdit" cols="30" rows="15">(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;   //住宅类型
      $house_info['house_name'] = $house_name[1];     //楼盘名称
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['price'] = $price[1];       //租金 元.月
      $house_info['strata_fee'] = $fee[1];    //物业费 元/㎡·月
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    }
    return $house_info;
  }

  //上传图片到 中房网
  public function upload_image($url, $broker_id)
  {
    $data = false;
    $finalname = $this->site_model->upload_img($url);
    $login = $this->isbind($broker_id);
    if ($login['cookies'] && !empty($finalname)) {
      $cookie = $login['cookies'];
      $post_field = array(
        'flashvars' => '@' . $finalname,
        'wmode' => 'opaque',
        'movie' => 'http://agent.360fdc.com/Scripts/uploadify/UploadifySwf/uploadify.swf',
        'quality' => 'high',
        'allowScriptAccess' => 'sameDomain'
      );
      $tmpInfo = $this->curl->vpost('http://upload.360fdc.com/upload', $post_field, $cookie);
      preg_match('/"src":"(.*)"/siU', $tmpInfo, $pigname);
      $data = $pigname[1];
    }
    if ($finalname) {
      @unlink($finalname);
    }
    return $data;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($alias = '', $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type');  //1民宅 2别墅 3商铺 4写字楼
    $keyword = trim($keyword);
    $broker_id = $this->broker_info['broker_id'];

    $list = array();
    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      if ($sell_type > 4) {
        $sell_type = 1;
      }
      $url = 'http://agent.360fdc.com/Buliding/GetBuildingList?types=' . $sell_type . '&term=' . urlencode($keyword);
      $tmpInfo = $this->curl->vget($url, $cookie);
      $info = json_decode($tmpInfo, true);
      if ($info) {
        foreach ($info as $key => $val) {
          if ($key == 10) break;
          $list[] = array(
            'id' => $val['BuildingCode'],
            'label' => $val['value'],
            'district' => $val['AreaID'] . '_' . $val['AreaName'],
            'street' => $val['ShangQuanID'] . '_' . $val['ShangQuanName'],
            'address' => $val['XQAddress']
          );
        }
      }
    }
    return $list;
  }

  //定时任务下架
  public function queue_esta($queue)
  {
    $this->load->model('group_publish_model');
    $site_id = $this->website['id'];
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
    $login = $this->isbind($broker_id);
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
    $bool = $this->del(array('site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
    return $result;
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
      $pub_tbl = 'group_publish_sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $tbl = 2;
      $pub_tbl = 'group_publish_rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $block_info = array();
    $block_name = $this->input->get('block_name', TRUE);
    if ($block_name) {
      $block_info['block_name'] = $block_name;  //楼盘name
      $block_info['block_id'] = $this->input->get('block_id');  //楼盘
      $block_info['district'] = $this->input->get('district');  //区属
      $block_info['street'] = $this->input->get('street');      //商圈
      $block_info['address'] = $this->input->get('address');    //地址
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
    } elseif (substr_count($house_info['pic_ids'], ',') < 3 || substr_count($house_info['pic_ids'], ',') > 10) {
      return array('flag' => 'proerror', 'info' => '房源图片3-20张');
    } elseif ($house_info['sell_type'] > 4) {
      return array('flag' => 'proerror', 'info' => '不支持 厂房 车库 仓库 类型');
    }

    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';  //2楼盘字典
    } else {
      //判断是否已经发布
      $pub_sql = array('where' => array('broker_id' => $broker_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
      $pubInfo = $this->get_data($pub_sql, 'dbback_city');
      $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
      if ($publish_id) {
        $login = $this->isbind($broker_id);
        $extra = array('login' => $login, 'publish' => $pubInfo[0], 'tbl' => $tbl, 'htype' => $house_info['sell_type']);
        $del = $this->esta($extra);
        $bool = $this->del(array('house_id' => $house_id, 'site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
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
        $data['flag'] = 'error';
      }
    }
    return $data;
  }

  //发布数据组装
  public function publish_param($queue)
  {
    $this->load->model('broker_info_model');
    $broker_id = $queue['broker_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $tbl = $queue['tbl'];
    $block_info = unserialize($queue['info']);
    if ($tbl == 1) {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $login = $this->isbind($broker_id);  //目标站点帐号信息
    $username = $login['username'];
    $cookie = $login['cookies'];
    $block_id = $block_info['block_id'];
    $block_name = $block_info['block_name'];
    $block_district = $block_info['district'];
    $block_street = $block_info['street'];
    $address = $block_info['address'];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $config = $this->getconfig();   //相关配置
    $the_type = $house_info['sell_type'];

    //配套设施
    $equip_out = $config['equipment_out'];  //配套设施
    $equip_in = $config['equipment_in'];   //租房设施 室内
    $tmp_out_name = $tmp_out_id = $tmp_in_name = $tmp_in_id = array();
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($equip_out[$val]['id'])) {
          $tmp_out_name[] = $equip_out[$val]['name'];
          $tmp_out_id[] = $equip_out[$val]['id'];
        } elseif (isset($equip_in[$val]['id'])) {
          $tmp_in_name[] = $equip_in[$val]['name'];
          $tmp_in_id[] = $equip_in[$val]['id'];
        }
      }
    }
    $ptssname = implode(',', $tmp_out_name);
    $ptssid = implode(',', $tmp_out_id);
    $snss = implode(',', $tmp_in_name);
    $snssid = implode(',', $tmp_in_id);

    //上传图片
    $huxing_pic = $shinei_pic = $title_pic = '';
    $huxing = $shinei = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          if ($val['type'] == 2) {
            $huxing[] = $this->upload_image($picurl, $broker_id);
          } else {
            $shinei[] = $this->upload_image($picurl, $broker_id);
          }
        }
        $title_pic = empty($shinei[0]) ? $huxing[0] : $shinei[0];
        $huxing_pic = implode(',', $huxing);
        $shinei_pic = implode(',', $shinei);
      }
      $this->load->database();
      $this->db_city->reconnect();
    }

    //修改房源权限
    $this->load->model('broker_permission_model');
    $this->broker_permission_model->set_broker_id($broker_id, $broker_info['company_id']);
    $role_level = intval($broker_info['role_level']);   //获得当前经纪人的角色等级，判断店长以上or店长以下
    if (is_int($role_level) && $role_level > 6) {         //店长以下的经纪人不允许操作他人的私盘
      if ($broker_id != $house_info['broker_id'] && $house_info['nature'] == '1') {
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['broker_id'], 'dbback_city');  //当前经纪人的临时详情
        if (!empty($result)) {
          $house_info['title'] = $result[0]['title'];
          $house_info['bewrite'] = $result[0]['content'];
        }
      }
    }

    //数据处理
    $data_house = array();
    $data_house['the_type'] = $the_type = $house_info['sell_type'];
    $fitment = ($the_type == 1 || $the_type == 2) ? $config['fit'] : $config['fitshop'];
    $fit = $fitment[$house_info['fitment']];         //装修
    $face = $config['face'][$house_info['forward']];  //朝向
    $price = round($house_info['price'], 0);             //价格
    $area = round($house_info['buildarea'], 0);          //面积
    $sfje = $price * 0.3;              //首付金额
    $strata_fee = empty($house_info['strata_fee']) ? '' : round($house_info['strata_fee'], 1);  //物业费
    $area_id = $area_name = $sq_id = $sq_name = '';  //板块 商圈
    if ($block_district && $block_street) {
      $block_district = explode('_', $block_district);
      $block_street = explode('_', $block_street);
      $area_id = $block_district[0];
      $area_name = $block_district[1];
      $sq_id = $block_street[0];
      $sq_name = $block_street[1];
    }

    if ($tbl == 1) {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $data_house['url_mid'] = 'ershoufang';
        $data_house['post_field'] = 'cqname=个人产权&cqtypeid=2&cxname=' . $face['name'] . '&cxtypeid=' . $face['id'] . '&fjNumber=' . $house_info['room']
          . '&ktNumber=' . $house_info['hall'] . '&wsjNumber=' . $house_info['toilet'] . '&cfNumber=' . $house_info['kitchen']
          . '&ytNumber=' . $house_info['balcony'] . '&ptssname=' . $ptssname . '&ptssid=' . $ptssid
          . '&jzxsName=塔楼&jzxsid=2&years=2001-01-01 00:00&ckyg=0&iskc=0&esfCode=0&esfID=&oldPrice=&esfTypes=' . $the_type;
      } elseif ($the_type == 3) { //商铺
        $data_house['url_mid'] = 'shop-chushou';
        $data_house['post_field'] = 'spTypesName=临街门面&sptypesID=3&cxname=' . $face['name'] . '&cxtypeid=' . $face['id']
          . '&WYFPrice=' . $strata_fee . '&ptssname=客梯,货梯,扶梯&ptssid=21,22,23&ObjectFormatName=&ObjectFormatID='
          . '&jzxsName=平层&jzxsid=15&years=2001-01-01 00:00&ckyg=0&iskc=0&esfCode=0&esfID=&oldPrice=&esfTypes=3';
      } elseif ($the_type == 4) { //写字楼
        $data_house['url_mid'] = 'office-chushou';
        $data_house['post_field'] = 'xzlTypesName=纯写字楼&xzltypesID=1&LevelName=甲级&OfficeLevel=0&WYFPrice=' . $strata_fee
          . '&jzxsName=&jzxsid=0&years=0&ckyg=0&iskc=0&esfCode=0&esfID=&oldPrice=&esfTypes=4';
      }
      $data_house['post_url'] = 'http://agent.360fdc.com/EsfEdit/AddEsf';
      $data_house['post_field'] .= '&price=' . $price . '&sfje=' . $sfje . '&czmj=' . $area . '&esfname=' . urlencode($house_info['title']);
    } elseif ($tbl == 2) {
      $paytype = $config['paytype'][$house_info['rentpaytype']];
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $data_house['url_mid'] = 'zufang';
        $data_house['post_field'] = 'fjNumber=' . $house_info['room'] . '&ktNumber=' . $house_info['hall'] . '&wsjNumber=' . $house_info['toilet']
          . '&cfNumber=' . $house_info['kitchen'] . '&ytNumber=' . $house_info['balcony'] . '&cztypeName=整租&cztype=1&GenderName=性别不限&GenderId=1'
          . '&cxname=' . $face['name'] . '&cxtypeid=' . $face['id'] . '&ptssname=' . $ptssname . '&ptssid=' . $ptssid . '&snss=' . $snss . '&snssid=' . $snssid
          . '&jzxsName=塔楼&jzxsid=1&years=1995-01-01 00:00&ckprice=0&iskc=0&czfcode=0&czfid=&oldPrice=&czfTypes=' . $the_type;
      } elseif ($the_type == 3) { //商铺
        $data_house['url_mid'] = 'shop-chuzu';
        $shop_type = $config['shop_type'][$house_info['shop_type']];
        $data_house['post_field'] = 'spTypesName=' . $shop_type['name'] . '&sptypesID=' . $shop_type['id']
          . '&cxname=' . $face['name'] . '&cxtypeid=' . $face['id'] . '&spState=1&WYFPrice=' . $strata_fee
          . '&IsAssignment=1&AssignmentPrice=&ptssname=水,燃气&ptssid=27,28&ObjectFormatName=&ObjectFormatID='
          . '&jzxsName=&jzxsid=&years=2003-01-01 00:00&ckprice=0&iskc=0&czfcode=0&czfid=&oldPrice=&czfTypes=3';
      } elseif ($the_type == 4) { //写字楼
        $data_house['url_mid'] = 'office';
        $data_house['post_field'] = 'xzlTypesName=纯写字楼&xzltypesID=1&LevelName=甲级&OfficeLevel=0&WYFPrice=' . $strata_fee
          . '&jzxsName=&jzxsid=&years=2013-03-01 00:00&ckprice=0&iskc=0&czfcode=0&czfid=&oldPrice=&czfTypes=4';
      }
      $data_house['post_url'] = 'http://agent.360fdc.com/CzfEdit/AddCzf';
      $data_house['post_field'] .= '&payTypesName=' . $paytype['name'] . '&payTypes=' . $paytype['id']
        . '&rentPrice=' . $price . '&mj=' . $area . '&czfname=' . urlencode($house_info['title']);
    }
    $data_house['post_field'] .= '&BuildingID=' . $block_id . '&BuildingName=' . $block_name . '&richEdit=' . urlencode($house_info['bewrite'])
      . '&imgurl=' . $title_pic . '&imgNum=0&imgWJT=&imgHXT=' . $huxing_pic . '&imgSNT=' . $shinei_pic
      . '&lc=' . $house_info['floor'] . '&sumlc=' . $house_info['totalfloor'] . '&zxname=' . $fit['name'] . '&zxtypeid=' . $fit['id']
      . '&AreaID=' . $area_id . '&AreaName=' . $area_name . '&ShangQuanID=' . $sq_id . '&ShangQuanName=' . $sq_name . '&BuildingAddress=' . $address
      . '&DiTieID=&DiTieName=&beApartFromDiTie=0&BXaxis=&BYaxis=&DiTieSiteID=&DiTieSiteName=&InternalCode=&BackUrl='
      . '&houseslable=&lablesid=&p_Mark_Name=&CanUseNum=&ShowDay=&MarkID=';

    $data_house['house_block_id'] = $house_info['block_id'];
    $data_house['block_name'] = $block_name;
    $result = $this->publish(array('data_house' => $data_house, 'queue' => $queue, 'login' => $login));
    return $result;
  }
}

// $data_house['post_field'] .= '&imgurl=http://img1.zfwimg.com/display/fcf28906bb0d4c15bc1e498a9ec5865c.jpg&imgNum=0&imgHXT='
//         .'&imgSNT=http://img1.zfwimg.com/display/d40fa79a2a92bb5caa60480d5d8e978c.jpg,http://img1.zfwimg.com/display/fcf28906bb0d4c15bc1e498a9ec5865c.jpg,'
//         .'http://img1.zfwimg.com/display/863b6a90225051851839b41f79babe9c.jpg';
/* End of file site_fdcnum_model.php */
/* Location: ./application/mls/models/site_fdcnum_model.php */
