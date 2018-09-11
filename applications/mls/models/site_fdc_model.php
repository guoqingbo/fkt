<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_fdc_base_model");

class Site_fdc_model extends Site_fdc_base_model
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

    $login = $this->login($username, $password);
    if (!$login) {
      return '123';  //绑定失败
    } else {
      $data = array();
      $data['broker_id'] = $broker_id;
      $data['site_id'] = $site_id;
      $data['status'] = 1;
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['user_id'];
      $data['agent_id'] = $login['agent_id'];
      $data['otherpwd'] = $login['otherpwd'];
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
      $agent_str = $login['agent_id'] . '-';
      if ($act == 'sell') {
        $infoPro = 'http://oldhouse.fdc.com.cn/esf/' . $agent_str;
        $editPro = 'http://oldhouseinfo.fdc.com.cn/house/sale/ajax_page.aspx?action=loaddata&fid=';
        $pricetype = '万元';
        $biz = 1;
        $shop_data = $config['shop_sell_list'];
        $offi_data = $config['office_sell_list'];
      } else {
        $infoPro = 'http://zufang.fdc.com.cn/zufang/' . $agent_str;
        $editPro = 'http://oldhouseinfo.fdc.com.cn/house/lease/ajax_page.aspx?action=loaddata&fid=';
        $pricetype = '元/月';
        $biz = 2;
        $shop_data = $config['shop_rent_list'];
        $offi_data = $config['office_rent_list'];
      }

      //商铺 写字楼
      $uid = $login['user_id'];
      $shopInfo = $this->curl->vpost('http://efbabyshop.fdc.com.cn/HouseLst.aspx?EFBaby_UserId=' . $uid, $shop_data, $cookie, '', 0);
      $offiInfo = $this->curl->vpost('http://efbabyoffice.fdc.com.cn/HouseLst.aspx?EFBaby_UserId=' . $uid, $offi_data, $cookie, '', 0);
      preg_match_all("/name='cb_check1'(.*)<\/tr>/siU", $shopInfo, $shop);
      preg_match_all("/name='cb_check1'(.*)<\/tr>/siU", $offiInfo, $offi);  //1发布 2未发布
      $more = array_merge($shop[0], $offi[0]);
      if (is_full_array($more)) {
        foreach ($more as $val) {
          preg_match("/确定重新发布(.*)吗？/siU", $val, $tag);
          preg_match('/<a href="(.*)".*修改.*<td>(.*)<\/td>/siU', $val, $url);
          preg_match('/onclick="setCheck.*<td>(.*)<a target="_blank" href="(.*)">(.*)<\/a><br\/>/siU', $val, $infourl);
          $desc = preg_split("/[\s]+/", trim($infourl[1]));
          if ($tag[1] == '商铺') {
            $tag_pro = 'http://efbabyshop.fdc.com.cn/';
            $tag_title = ' [商铺]';
          } else {  //办公房
            $tag_pro = 'http://efbabyoffice.fdc.com.cn/';
            $tag_title = ' [写字楼]';
          }

          $data = array();
          $data['source'] = 0;
          $data['url'] = $tag_pro . $url[1];   //编辑链接
          $data['infourl'] = $infourl[2];    //详情链接
          $data['title'] = strip_tags($infourl[3]) . $tag_title;  //标题
          $data['des'] = implode(',', $desc);  //描述
          $data['releasetime'] = empty($url[2]) ? 0 : strtotime(trim($url[2]));   //发布时间
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
      }

      //住宅
      $project = array();
      for ($i = 1; $i < 10; $i++) {  //小于10防止意外情况死循环
        //fad: 1发布 0未发布 2过期 3违规
        //$18$7$18$0$0$9$1$0  发布 未发布 发布 过期 违规
        $post_url = 'http://oldhouseinfo.fdc.com.cn/house/ajax_page.aspx?action=houselist&biz=' . $biz . '&fad=1&page=' . $i . '&selectWhere=cancel&SelectOrder=reltimedesc';
        $tmpInfo = $this->curl->vget($post_url, $cookie);
        preg_match('/\[\{(.*)\}\]/siU', $tmpInfo, $prj);
        $arrtmp = empty($prj[0]) ? '' : json_decode($prj[0], true);
        if (is_full_array($arrtmp)) {
          $project = array_merge($project, $arrtmp);
        } else {
          break;
        }
      }
      foreach ($project as $val) {
        $data = array();
        $data['source'] = 0;
        $data['url'] = $editPro . $val['fid'];        //编辑链接 到详情中组装
        $data['infourl'] = $infoPro . $val['fid'] . '.html';    //详情链接 到详情中组装
        $data['title'] = $val['fname'];    //标题
        $data['des'] = $val['rooms'] . '室' . $val['halls'] . '厅' . $val['barea'] . '平' . $val['price'] . $pricetype;  //描述
        $data['releasetime'] = strtotime($val['modifiedon']);   //发布时间
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
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $cookie);
      if (stripos($url, 'oldhouseinfo')) {
        $the_type = 1;
      } elseif (stripos($url, 'efbabyshop')) {
        $the_type = 3;
      } elseif (stripos($url, 'efbabyoffice')) {
        $the_type = 4;
      }
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    if ($the_type == 1) {  //民宅 别墅
      $prj = explode('|$', $tmpInfo);
      if (is_full_array($prj)) {
        $info = json_decode($prj[0], true);
        $info = $info[0];
        $content = end($prj);

        preg_match('/class="bigImg"(.*)class="smallScroll"/siU', $picInfo, $pic_div);
        preg_match_all('/img src="(.*)" alt=/siU', $pic_div[1], $pic);    //图片
        foreach ($pic[1] as $val) {
          if (strpos($val, 'xqImg')) {
            continue;
          }
          $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
          $image[] = $tempurl;
        }

        $house_info['picurl'] = implode('*', $image);
        $house_info['sell_type'] = ($info['buildstru'] == 4) ? 2 : 1;  //类型2别墅
        $house_info['house_name'] = $info['buildingName']; //楼盘名称
        $house_info['room'] = $info['rooms'];     //室
        $house_info['hall'] = $info['halls'];     //厅
        $house_info['toilet'] = $info['toilets']; //卫
        $house_info['kitchen'] = $info['kitchens']; //厨房
        $house_info['balcony'] = $info['balcony']; //阳台
        $house_info['forward'] = $config['forward'][$info['farward']];    //朝向
        $house_info['serverco'] = $config['fitment'][$info['fitment']];   //装修
        $house_info['floor'] = $info['floor'];  //楼层
        $house_info['totalfloor'] = $info['floors']; //总楼层
        $house_info['buildarea'] = $info['barea'];  //建筑面积
        $house_info['price'] = $info['price'];  //总价
        $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
        $house_info['title'] = $info['fname'];    //标题
        $house_info['content'] = $content;        //描述
      }
    } elseif ($the_type == 3) {  //商铺
      preg_match('/name="txtName" type="text" value="(.*)" id="txtName"/siU', $tmpInfo, $house_name);
      preg_match('/name="txtMinArea" type="text" value="(.*)" id="txtMinArea"/siU', $tmpInfo, $area);
      preg_match('/name="txtPrice" type="text" value="(.*)" id="txtPrice"/siU', $tmpInfo, $price);
      preg_match('/name="txtitle" type="text" value="(.*)" id="txtitle"/siU', $tmpInfo, $title);
      preg_match('/id="hfDesc" value="(.*)" \/>/siU', $tmpInfo, $content);

      preg_match('/class="picArtFoucks"(.*)div class="hd"/siU', $picInfo, $pic_div);  //图片
      preg_match_all('/img src="(.*)" width/siU', $pic_div[1], $pic);
      foreach ($pic[1] as $val) {
        if (strpos($val, 'tu_house.jpg')) {
          continue;
        }
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];     //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = trim($title[1]); //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/name="txtName" type="text" value="(.*)" id="txtName"/siU', $tmpInfo, $house_name);
      preg_match('/name="txtMinArea" type="text" value="(.*)" id="txtMinArea"/siU', $tmpInfo, $area);
      preg_match('/name="rbDecoration" value="([0-9])" checked="checked"/siU', $tmpInfo, $build);
      preg_match('/name="txtPrice" type="text" value="(.*)" id="txtPrice"/siU', $tmpInfo, $price);
      preg_match('/name="txtPropertyFee" type="text" value="(.*)" id="txtPropertyFee"/siU', $tmpInfo, $fee);
      preg_match('/name="txtitle" type="text" value="(.*)" id="txtitle"/siU', $tmpInfo, $title);
      preg_match('/id="hfDesc" value="(.*)" \/>/siU', $tmpInfo, $content);

      preg_match('/class="picArtFoucks"(.*)div class="hd"/siU', $picInfo, $pic_div);  //图片
      preg_match_all('/img src="(.*)" width/siU', $pic_div[1], $pic);
      foreach ($pic[1] as $val) {
        if (strpos($val, 'tu_house.jpg')) {
          continue;
        }
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['serverco'] = empty($build[1]) ? 2 : $build[1];   //装修
      $house_info['price'] = $price[1];     //租金 元.月
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['content'] = $content[1];   //描述
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
      $picInfo = $this->curl->vget($infourl);
      $tmpInfo = $this->curl->vget($url, $cookie);

      if (stripos($url, 'oldhouseinfo')) {
        $the_type = 1;
      } elseif (stripos($url, 'efbabyshop')) {
        $the_type = 3;
      } elseif (stripos($url, 'efbabyoffice')) {
        $the_type = 4;
      }
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    if ($the_type == 1) {  //民宅 别墅
      $prj = explode('|$', $tmpInfo);
      if (is_full_array($prj)) {
        $info = json_decode($prj[0], true);
        $info = $info[0];
        $content = end($prj);

        preg_match('/class="bigImg"(.*)class="smallScroll"/siU', $picInfo, $pic_div);
        preg_match_all('/img src="(.*)" alt=/siU', $pic_div[1], $pic);    //图片
        foreach ($pic[1] as $val) {
          if (strpos($val, 'xqImg')) {
            continue;
          }
          $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
          $image[] = $tempurl;
        }
        $house_info['picurl'] = implode('*', $image);
        $house_info['sell_type'] = ($info['buildstru'] == 4) ? 2 : 1;  //类型2别墅
        $house_info['house_name'] = $info['buildingName']; //楼盘名称
        $house_info['room'] = $info['rooms'];     //室
        $house_info['hall'] = $info['halls'];     //厅
        $house_info['toilet'] = $info['toilets']; //卫
        $house_info['kitchen'] = $info['kitchens']; //厨房
        $house_info['balcony'] = $info['balcony']; //阳台
        $house_info['forward'] = $config['forward'][$info['farward']];    //朝向
        $house_info['serverco'] = $config['fitment'][$info['fitment']];   //装修
        $house_info['floor'] = $info['floor'];  //楼层
        $house_info['totalfloor'] = $info['floors']; //总楼层
        $house_info['buildarea'] = $info['barea'];  //建筑面积
        $house_info['price'] = $info['price'];  //总价
        $house_info['title'] = $info['fname'];    //标题
        $house_info['content'] = $content;        //描述
      }
    } elseif ($the_type == 3) {  //商铺
      preg_match('/name="txtName" type="text" value="(.*)" id="txtName"/siU', $tmpInfo, $house_name);
      preg_match('/name="txtMinArea" type="text" value="(.*)" id="txtMinArea"/siU', $tmpInfo, $area);
      preg_match('/name="txtPrice" type="text" value="(.*)" id="txtPrice"/siU', $tmpInfo, $price);
      preg_match('/name="txtitle" type="text" value="(.*)" id="txtitle"/siU', $tmpInfo, $title);
      preg_match('/id="hfDesc" value="(.*)" \/>/siU', $tmpInfo, $content);

      preg_match('/class="picArtFoucks"(.*)div class="hd"/siU', $picInfo, $pic_div);  //图片
      preg_match_all('/img src="(.*)" width/siU', $pic_div[1], $pic);
      foreach ($pic[1] as $val) {
        if (strpos($val, 'tu_house.jpg')) {
          continue;
        }
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];     //租金 元.月
      $house_info['title'] = trim($title[1]); //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/name="txtName" type="text" value="(.*)" id="txtName"/siU', $tmpInfo, $house_name);
      preg_match('/name="txtMinArea" type="text" value="(.*)" id="txtMinArea"/siU', $tmpInfo, $area);
      preg_match('/name="rbDecoration" value="([0-9])" checked="checked"/siU', $tmpInfo, $build);
      preg_match('/name="txtPrice" type="text" value="(.*)" id="txtPrice"/siU', $tmpInfo, $price);
      preg_match('/name="txtPropertyFee" type="text" value="(.*)" id="txtPropertyFee"/siU', $tmpInfo, $fee);
      preg_match('/name="txtitle" type="text" value="(.*)" id="txtitle"/siU', $tmpInfo, $title);
      preg_match('/id="hfDesc" value="(.*)" \/>/siU', $tmpInfo, $content);

      preg_match('/class="picArtFoucks"(.*)div class="hd"/siU', $picInfo, $pic_div);  //图片
      preg_match_all('/img src="(.*)" width/siU', $pic_div[1], $pic);
      foreach ($pic[1] as $val) {
        if (strpos($val, 'tu_house.jpg')) {
          continue;
        }
        $tempurl = $this->autocollect_model->get_pic_url($val, $city_spell);
        $image[] = $tempurl;
      }

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = trim($house_name[1]); //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['serverco'] = empty($build[1]) ? 2 : $build[1];   //装修
      $house_info['price'] = $price[1];     //租金 元.月
      //$house_info['strata_fee'] = empty($fee[1]) ? 0 : $fee[1];  //物业费 元/㎡·月
      $house_info['title'] = trim($title[1]);    //标题
      $house_info['content'] = $content[1];   //描述
    }

    return $house_info;
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
    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      $url = 'http://oldhouseinfo.fdc.com.cn/house/sale/ajax_page.aspx?action=get_xq&count=10&ch=Fang&callback=&city=0';
      $tmpInfo = $this->curl->vget($url . '&q=' . urlencode($keyword), $cookie);
      preg_match_all("/em   alt='([0-9]*)'  >(.*)<\/em><span   alt='([0-9]*)' >(.*)<\/span><\/li>/siU", $tmpInfo, $obj);
      //              <em   alt='6'  > 洪山区  街道口片</em><span   alt='8381' > 巴黎春天</span>
      if (is_full_array($obj[3])) {
        foreach ($obj[3] as $key => $val) {
          if ($key == 10) break;
          $list[] = array(
            'id' => $val,  //$obj[3][$key]  楼盘id
            'label' => $obj[4][$key],
            'district' => $obj[1][$key],  //区属id
            'street' => $obj[2][$key]     //区属板块name
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
      $block_info['block_id'] = $this->input->get('block_id');  //楼盘id
      $block_info['district'] = $this->input->get('district');  //区属id
      $block_info['street'] = $this->input->get('street');      //区属板块 name
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
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
        $bool = $this->del(array('site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
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
      $pro_url = 'http://oldhouseinfo.fdc.com.cn/house/sale/saleinput.aspx';
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
      $pro_url = 'http://oldhouseinfo.fdc.com.cn/house/lease/leaseinput.aspx';
    }

    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $login = $this->isbind($broker_id);  //目标站点帐号信息
    $userid = $login['user_id'];
    $username = $login['username'];
    $agentid = $login['agent_id'];
    $otherpwd = $login['otherpwd'];
    $cookie = $login['cookies'];
    $block_id = $block_info['block_id'];
    $block_name = $block_info['block_name'];
    $block_district = $block_info['district'];
    $block_street = $block_info['street'];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $config = $this->getconfig();   //相关配置
    $the_type = $house_info['sell_type'];
    //租房设施

    //获取页面数据
    if ($the_type == 1 || $the_type == 2) {
      $proInfo = $this->curl->vget($pro_url, $cookie);
      preg_match("/var gid=(.*);/siU", $proInfo, $gid);  //图片id
      preg_match('/adminauth="(.*)";/siU', $proInfo, $adminid);   //图片校验码
      preg_match("/URL='&fid='\+escape\((.*)\)\+/siU", $proInfo, $fid);  //订单id
    } elseif ($the_type == 3 || $the_type == 4) {
      $post_url = ($tbl == 1) ? 'http://efbabyshop.fdc.com.cn/ShopSale.aspx?EFBaby_UserId=' . $userid :
        'http://efbabyshop.fdc.com.cn/ShopRent.aspx?EFBaby_UserId=' . $userid;
      $proInfo = $this->curl->vget($post_url, $cookie);
      preg_match('/id="hidSessionID" value="(.*)" \/>/siU', $proInfo, $sessid);  //sessid
    }

    //上传图片
    $pic_arr = array();
    $defimg = $image_list = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        if ($the_type == 3 || $the_type == 4) {  //3商铺 4写字楼
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            $ext = array('url' => $picurl, 'broker_id' => $broker_id, 'the_type' => $the_type, 'sessid' => $sessid[1]);
            $pictmp = $this->upload_image($ext);
            $pic_arr[] = 'http://img3.fdc.com.cn/mshome' . $pictmp['Link'] . $pictmp['FileID'] . $pictmp['Suffixname'];
          }
        } else {  //1民宅 2别墅
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            $ext = array('url' => $picurl, 'broker_id' => $broker_id, 'the_type' => $the_type, 'gid' => $gid[1], 'adminid' => $adminid[1]);
            $pic_arr[] = $this->upload_image($ext);
          }
        }
      }
      $this->load->database();
      $this->db_city->reconnect();
    }
    if ($pic_arr) {
      if ($the_type == 3 || $the_type == 4) {
        $image_list = implode(',', $pic_arr);
      } else {
        $pic_end = end($pic_arr);
        $imglen = count($pic_arr);  //图片长度
        $curr_id = $pic_end['id'];  //默认图片id
        $defimg = $pic_end['small_img'];  //默认缩略图
      }
    } else {
      $curr_id = $imglen = 0;
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
    $fit = $config['fit'][$house_info['fitment']];     //装修
    $face = $config['face'][$house_info['forward']];    //朝向
    $price = round($house_info['price'], 0);             //价格
    $area = round($house_info['buildarea'], 0);          //面积
    $strata_fee = empty($house_info['strata_fee']) ? '' : round($house_info['strata_fee'], 1);  //物业费
    $avgprice = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;  //均价

    $list = explode('_', $otherpwd);
    $resInfo = $this->curl->vget('http://img2.fdc.com.cn/oldhouse/Community/district' . $block_id . '.js?r=' . time() . '000', $cookie);
    preg_match("/var detail = '(.*)';/siU", $resInfo, $tmpblock);  //获取block,busline,support信息
    $tpblock = explode('|', $tmpblock[1]);
    if ($tbl == 1) {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $btype = $the_type == 2 ? 4 : 1;
        $data_house['post_url'] = 'http://oldhouseinfo.fdc.com.cn/house/sale/ajax_page.aspx?action=addrele';
        $data_house['post_field'] = '&fid=' . $fid[1] . '&regionid=' . $block_district . '&buildingid=' . $block_id . '&buildingname=' . $block_name
          . '&regionName=&districtName=&address=' . urlencode($tpblock[6]) . '&districtid=' . $tpblock[4] . '&barea=' . $area . '&price=' . $price
          . '&taxtype=4&rooms=' . $house_info['room'] . '&halls=' . $house_info['hall'] . '&toilets=' . $house_info['toilet']
          . '&kitchens=' . $house_info['kitchen'] . '&balcony=' . $house_info['balcony'] . '&farward=' . $face . '&housestru=' . $btype
          . '&floor=' . $house_info['floor'] . '&floors=' . $house_info['totalfloor'] . '&housemode=&attribute=1&certtime=&houseage=' . $house_info['buildyear']
          . '&fitment=' . $fit . '&property=' . $strata_fee . '&baseservice=&title=' . urlencode($house_info['title']) . '&unitprice=' . $avgprice
          . '&busline=' . urlencode($tpblock[10]) . '&support=' . urlencode($tpblock[9]) . '&looktype=1&note=' . urlencode($house_info['bewrite'])
          . '&tag=0&imglen=' . $imglen . '&curr_id=' . $curr_id . '&defimg=' . $defimg . '&userid=' . $otherpwd . '&xqimg=&compid=' . $agentid;
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://efbabyshop.fdc.com.cn/ShopSale.aspx?EFBaby_UserId=' . $userid;
        $data_house['post_field'] = '__VIEWSTATE=' . urlencode($config['shop_sell']) . '&hfAreaId=' . $tpblock[4] . '&hidCover=&hidImageList=' . $image_list
          . '&hidKeyword=&hidShopID=&hidSaleShopID=&hfPrice=&hfArea=&hfDesc=&txtitle=' . urlencode($house_info['title']) . '&txtName=' . $block_name
          . '&ddlArea=' . $block_district . '&ddlAreaSel=' . $tpblock[4] . '&txtAddress=' . urlencode($tpblock[6])
          . '&ddlShopType=1&radStatus=1&txtMinArea=' . $area . '&txtPrice=' . $price . '&txtSubPrice=' . $avgprice
          . '&radTax=4&radWrran=1&hidContactName=' . $list[1] . '&hidContactPhone=' . $list[2] . '&hidContactPhone1=' . '&hidContactUserID=' . $list[2]
          . '&rbtnviewTime=1&txtDecs=&btnPublish=&txtDecs=' . urlencode($house_info['bewrite']);
      } elseif ($the_type == 4) { //写字楼
        $ofit = $config['fit_office'][$house_info['fitment']];
        $data_house['post_url'] = 'http://efbabyoffice.fdc.com.cn/ShopSale.aspx?EFBaby_UserId=' . $userid;
        $data_house['post_field'] = '__VIEWSTATE=' . urlencode($config['office_sell']) . '&hfAreaId=' . $tpblock[4] . '&hidCover=&hidImageList=' . $image_list
          . '&hidKeyword=&hidShopID=&hidSaleShopID=&hfPrice=&hfArea=&hfDesc=&txtitle=' . urlencode($house_info['title']) . '&txtName=' . $block_name
          . '&ddlArea=' . $block_district . '&ddlAreaSel=' . $tpblock[4] . '&txtAddress=' . urlencode($tpblock[6])
          . '&ddlShopType=1&txtMinArea=' . $area . '&txtPrice=' . $price . '&txtSubPrice=' . $avgprice . '&txtMixFloor=' . $house_info['floor']
          . '&txtMaxFloor=' . $house_info['totalfloor'] . '&txtPropertyFee=' . $strata_fee . '&radIsRegisterCompany=True&rbDecoration=' . $ofit
          . '&hidContactName=' . $list[1] . '&hidContactPhone=' . $list[2] . '&hidContactPhone1=&hidContactUserID=' . $list[2]
          . '&rbtnviewTime=1&txtDecs=' . urlencode($house_info['bewrite']) . '&btnPublish=';
      }
    } elseif ($tbl == 2) {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $btype = $the_type == 2 ? 4 : 1;
        $data_house['post_url'] = 'http://oldhouseinfo.fdc.com.cn/house/lease/ajax_page.aspx?action=addrele';
        $data_house['post_field'] = '&fid=' . $fid[1] . '&regionid=' . $block_district . '&districtid=' . $tpblock[4]
          . '&regionName=&districtName=&address=' . urlencode($tpblock[6]) . '&buildingid=' . $block_id . '&buildingname=' . $block_name . '&barea=' . $area
          . '&price=' . $price . '&payy=3&payz=3&rooms=' . $house_info['room'] . '&halls=' . $house_info['hall'] . '&toilets=' . $house_info['toilet']
          . '&kitchens=' . $house_info['kitchen'] . '&balcony=' . $house_info['balcony'] . '&farward=' . $face . '&housestru=' . $btype
          . '&floor=' . $house_info['floor'] . '&floors=' . $house_info['totalfloor'] . '&houseage=' . $house_info['buildyear'] . '&fitment=' . $fit
          . '&biz=2&roomz=1&limit=1&baseservice=&equipment=&furniture=&looktype=&intime=&title=' . urlencode($house_info['title'])
          . '&note=' . urlencode($house_info['bewrite']) . '&busline=' . urlencode($tpblock[10]) . '&support=' . urlencode($tpblock[9])
          . '&tag=0&imglen=' . $imglen . '&curr_id=' . $curr_id . '&defimg=' . $defimg . '&userid=' . $otherpwd . '&xqimg=&compid=' . $agentid;
      } elseif ($the_type == 3) { //商铺
        $data_house['post_url'] = 'http://efbabyshop.fdc.com.cn/ShopRent.aspx?EFBaby_UserId=' . $userid;
        $data_house['post_field'] = '__VIEWSTATE=' . urlencode($config['shop_rent']) . '&hfAreaId=' . $tpblock[4] . '&hidCover=&hidImageList=' . $image_list
          . '&hidKeyword=&hidShopID=&hidTransferShopID=&hidLeaseShopID=&hfDesc=&hfArea=&hfCash=&hfPrice=&hfTranfer=' . '&txtName=' . $block_name
          . '&ddlArea=' . $block_district . '&ddlAreaSel=' . $tpblock[4] . '&txtAddress=' . urlencode($tpblock[6]) . '&txtitle=' . urlencode($house_info['title'])
          . '&radiorent=rdorent&ddlShopType=1&radStatus=2&txtMinArea=' . $area . '&txtPrice=' . $price . '&txtcash=' . $price
          . '&cbkTransferform=1&tbTransfermoney=&ddlyear=' . $house_info['buildyear'] . '&ddlmoth=1&hidContactName=' . $list[1]
          . '&hidContactPhone=' . $list[2] . '&hidContactUserID=' . $list[2] . '&hidContactPhone1='
          . '&rbtnviewTime=1&txtDecs=' . urlencode($house_info['bewrite']) . '&btnTrue=';
      } elseif ($the_type == 4) { //写字楼
        $ofit = $config['fit_office'][$house_info['fitment']];
        $data_house['post_url'] = 'http://efbabyoffice.fdc.com.cn/ShopRent.aspx?EFBaby_UserId=' . $userid;
        $data_house['post_field'] = '__VIEWSTATE=' . urlencode($config['office_rent']) . '&hfAreaId=' . $tpblock[4] . '&hidCover=&hidImageList=' . $image_list
          . '&hidKeyword=&hidShopID=&hidTransferShopID=&hidLeaseShopID=&hfDesc=&hfArea=&hfCash=&hfPrice=&hfTranfer=' . '&txtName=' . $block_name
          . '&ddlArea=' . $block_district . '&ddlAreaSel=' . $tpblock[4] . '&txtAddress=' . urlencode($tpblock[6]) . '&txtitle=' . urlencode($house_info['title'])
          . '&ddlShopType=1&txtMinArea=' . $area . '&txtPrice=' . $price . '&txtMixFloor=' . $house_info['floor'] . '&txtMaxFloor=' . $house_info['totalfloor']
          . '&radIsRegisterCompany=True&txtPropertyFee=' . $strata_fee . '&rbDecoration=' . $ofit . '&hidContactName=' . $list[1]
          . '&hidContactPhone=' . $list[2] . '&hidContactUserID=' . $list[2] . '&hidContactPhone1='
          . '&rbtnviewTime=1&txtDecs=' . urlencode($house_info['bewrite']) . '&btnTrue=';
      }
    }

    $fid = empty($fid[1]) ? 0 : $fid[1];  //订单id
    $data_house['house_block_id'] = $house_info['block_id'];
    $data_house['block_name'] = $block_name;
    $result = $this->publish(array('data_house' => $data_house, 'queue' => $queue, 'login' => $login, 'fid' => $fid));
    return $result;
  }

  //上传图片
  public function upload_image($extra)
  {
    $url = $extra['url'];
    $broker_id = $extra['broker_id'];
    $tbl = $extra['tbl'];
    $the_type = $extra['the_type'];
    $gid = isset($extra['gid']) ? $extra['gid'] : '';
    $adminid = isset($extra['adminid']) ? $extra['adminid'] : '';
    if ($tbl == 1) {
      $biz = 1;
      $htype = 0;
    } else {
      $biz = 2;
      $htype = 1;
    }

    $data = false;
    $finalname = $this->site_model->upload_img($url);
    $login = $this->isbind($broker_id);
    if ($login['cookies'] && !empty($finalname)) {
      $cookie = $login['cookies'];
      if ($the_type == 1 || $the_type == 2) {  //民宅
        $post_url = 'http://oldhouseinfo.fdc.com.cn/house/ajax_upload.aspx?action=ajaxuploadimg&biz=' . $biz . '&htype=' . $htype;
        $post_field = array('gid' => $gid, 'adminauth' => $adminid, 'Filedata' => '@' . $finalname);
        $tmpInfo = $this->curl->vpost($post_url, $post_field, $cookie, '', 0);
        $data = json_decode($tmpInfo, true);
      } elseif ($the_type == 3 || $the_type == 4) {  //商铺 写字楼
        $post_url = 'http://efbabyshop.fdc.com.cn/Ajax/upload/upload_json.ashx?jsessionid=' . $extra['sessid'];
        $post_field = array('Filedata' => '@' . $finalname, 'imageType' => 1);
        $tmpInfo = $this->curl->vpost($post_url, $post_field, $cookie, '', 0);
        $data = json_decode($tmpInfo, true);
      }
    }

    if ($finalname) {
      @unlink($finalname);
    }
    return $data;
  }

}

// 编辑二手出售  http://oldhouseinfo.fdc.com.cn/house/sale/saleinput.aspx?action=edit&fid=17504085
// 编辑二手出租  http://oldhouseinfo.fdc.com.cn/house/lease/leaseinput.aspx?action=edit&fid=17912267
// 编辑商铺出售  http://efbabyshop.fdc.com.cn/ShopSale.aspx?id=b2a15757-b9ef-4383-84e7-38054cf38856&EFBaby_UserId=113261
// 编辑商铺出租  http://efbabyshop.fdc.com.cn/ShopRent.aspx?id=c56027b5-e531-48c3-b471-e2a834e51873&EFBaby_UserId=113261
/* End of file site_fdc_model.php */
/* Location: ./application/mls/models/site_fdc_model.php */
