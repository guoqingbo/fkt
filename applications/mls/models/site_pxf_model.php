<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_pxf_base_model");

class Site_pxf_model extends Site_pxf_base_model
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
    $extra['username'] = $username = $this->input->get('username');
    $extra['password'] = $password = $this->input->get('password');
    $extra['sessid'] = $this->input->get('vcodekey');
    $extra['code'] = $this->input->get('checkcode');
    $extra['username'] = iconv("UTF-8", "GBK", $username);

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
      $data['user_id'] = $login['user_id'];
      $data['agent_id'] = '';
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

    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      if ($act == 'sell') {
        $url = 'http://www.jxpxf.cn/user/sale.php';
        $pattern = '/li class=lirq0>(.*)<\/li><li class=lizx0>([0-9]*)<\/li><li class=lidd0>(.*)<\/li><li class=lihx0>(.*)<\/li><li class=lilc0>(.*)<\/li><li class=limj0>(.*)<\/li><li class=lijg0>(.*)<\/li>/iU';
      } else {
        $url = 'http://www.jxpxf.cn/user/rent.php';
        $pattern = '/li class=lirq0>(.*)<\/li><li class=libh0>([0-9]*)<\/li><li class=lidd0>(.*)<\/li><li class=lihx0>(.*)<\/li><li class=lilc0>(.*)<\/li><li class=lizx0>(.*)<\/li><li class=lijg0>(.*)<\/li>/iU';
      }
      $tmpInfo = $this->curl->vget($url, $cookie);
      $tmpInfo = iconv("GBK", "UTF-8", $tmpInfo);
      preg_match_all("/name='XID(.*)确定要删除/siU", $tmpInfo, $prj);

      foreach ($prj[0] as $val) {
        preg_match('/class=lixg><a href="(.*)">详细<\/a>/iU', $val, $url);
        preg_match($pattern, $val, $des);

        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];   //编辑链接
        $data['infourl'] = $url[1];    //详情链接
        $data['title'] = $des[3] . ' ' . $des[4];  //标题
        $data['des'] = $des[5] . ' ' . $des[6] . ' ' . $des[7] . ' ' . $des[1];  //描述
        $data['releasetime'] = empty($des[1]) ? 0 : strtotime(trim($des[1]));   //发布时间
        $data['city_spell'] = $this->broker_info['city_spell'];
        $data['broker_id'] = $this->broker_info['broker_id'];
        $data['site_id'] = $this->website['id'];
        $data_list[] = $data;
        $num++;
      }
      $res = $this->autocollect_model->add_list_indata($act, $data_list);
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
      $tmpInfo = $this->curl->vget('http://www.jxpxf.cn/user/' . $url, $cookie);
      $tmpInfo = iconv("GBK", "UTF-8", $tmpInfo);
      $config = $this->getconfig();
    }

    $house_info = $image = array();
    //图片
    preg_match_all("/onclick=\"javascript:picshow\('\.\.(.*)'\)\"><img/iU", $tmpInfo, $pic);
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url('http://www.jxpxf.cn' . $val, $city_spell);
      $image[] = $tempurl;
    }
    preg_match('/name="YFX" style="width:166px"><option>(.*)<\/option>/iU', $tmpInfo, $htype);
    preg_match('/name="YDD" style="width:160px" maxlength="15" value="(.*)"/iU', $tmpInfo, $house_name);
    preg_match('/name="YHX" style="width:160px" maxlength="6" value="([0-9])室([0-9])厅([0-9])卫">/iU', $tmpInfo, $room);
    preg_match('/name="YZX" style="width:166px"><option>(.*)<\/option>/iU', $tmpInfo, $fitment);
    preg_match('/name="YCX" style="width:166px"><option>(.*)<\/option>/siU', $tmpInfo, $farward);
    preg_match('/name="YLC" style="width:166px"><option>([0-9]*)<\/option>/siU', $tmpInfo, $floor);
    preg_match('/name="YZC" style="width:166px"><option>([0-9]*)<\/option>/siU', $tmpInfo, $totalfloor);
    preg_match('/name="YMJ" style="width:70px" maxlength="10" value="(.*)" onChange/siU', $tmpInfo, $area);
    preg_match('/name="YJG" style="width:70px" maxlength="10" value="(.*)" onChange/siU', $tmpInfo, $price);
    preg_match('/name="YHX" style="width:160px" maxlength="6" value="(.*)">/siU', $tmpInfo, $title);
    preg_match('/<textarea name="YBZ" style="width:750px;height:75px">(.*)<\/textarea>/siU', $tmpInfo, $content);

    $house_info['picurl'] = implode('*', $image);
    $house_info['sell_type'] = empty($config['htype'][$htype[1]]) ? 1 : $config['htype'][$htype[1]];  //类型2别墅3商铺
    $house_info['house_name'] = $house_name[1];         //楼盘名称
    $house_info['room'] = $room[1] ? $room[1] : 0;      //室
    $house_info['hall'] = $room[2] ? $room[2] : 0;      //厅
    $house_info['toilet'] = $room[3] ? $room[3] : 0;    //卫
    $house_info['kitchen'] = 0;     //厨房
    $house_info['balcony'] = 0;     //阳台
    $house_info['forward'] = $config['forward'][$farward[1]] ? $config['forward'][$farward[1]] : 3;    //朝向
    $house_info['serverco'] = $config['fitment'][$fitment[1]] ? $config['fitment'][$fitment[1]] : 2;    //装修
    $house_info['floor'] = $floor[1];  //楼层
    $house_info['totalfloor'] = $totalfloor[1]; //总楼层
    $house_info['buildarea'] = $area[1];    //建筑面积
    $house_info['price'] = $price[1];       //总价
    $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    $house_info['title'] = $house_name[1] . ' ' . $title[1] . ' ' . $fitment[1];       //标题
    $house_info['content'] = strip_tags($content[1]);   //描述

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
      $tmpInfo = $this->curl->vget('http://www.jxpxf.cn/user/' . $url, $cookie);
      $tmpInfo = iconv("GBK", "UTF-8", $tmpInfo);
      $config = $this->getconfig();
    }

    $house_info = $image = array();
    //图片
    preg_match_all("/onclick=\"javascript:picshow\('\.\.(.*)'\)\"><img/iU", $tmpInfo, $pic);
    foreach ($pic[1] as $val) {
      $tempurl = $this->autocollect_model->get_pic_url('http://www.jxpxf.cn' . $val, $city_spell);
      $image[] = $tempurl;
    }
    preg_match('/name="YFX" style="width:166px"><option>(.*)<\/option>/iU', $tmpInfo, $htype);
    preg_match('/name="YDD" style="width:160px" maxlength="15" value="(.*)"/iU', $tmpInfo, $house_name);
    preg_match('/name="YHX" style="width:160px" maxlength="6" value="([0-9])室([0-9])厅([0-9])卫">/iU', $tmpInfo, $room);
    preg_match('/name="YZX" style="width:166px"><option>(.*)<\/option>/iU', $tmpInfo, $fitment);
    preg_match('/name="YCX" style="width:166px"><option>(.*)<\/option>/siU', $tmpInfo, $farward);
    preg_match('/name="YLC" style="width:166px"><option>([0-9]*)<\/option>/siU', $tmpInfo, $floor);
    preg_match('/name="YZC" style="width:166px"><option>([0-9]*)<\/option>/siU', $tmpInfo, $totalfloor);
    preg_match('/name="YMJ" style="width:70px" maxlength="10" value="(.*)">/siU', $tmpInfo, $area);
    preg_match('/name="YJG" style="width:70px" maxlength="10" value="(.*)">/siU', $tmpInfo, $price);
    preg_match('/name="YHX" style="width:160px" maxlength="6" value="(.*)">/siU', $tmpInfo, $title);
    preg_match('/<textarea name="YBZ" style="width:750px;height:75px">(.*)<\/textarea>/siU', $tmpInfo, $content);

    $house_info['picurl'] = implode('*', $image);
    $house_info['sell_type'] = empty($config['htype'][$htype[1]]) ? 1 : $config['htype'][$htype[1]];  //类型2别墅3商铺
    $house_info['house_name'] = $house_name[1];             //楼盘名称
    $house_info['room'] = $room[1] ? $room[1] : 0;      //室
    $house_info['hall'] = $room[2] ? $room[2] : 0;      //厅
    $house_info['toilet'] = $room[3] ? $room[3] : 0;      //卫
    $house_info['forward'] = $config['forward'][$farward[1]] ? $config['forward'][$farward[1]] : 1;    //朝向
    $house_info['serverco'] = $config['fitment'][$fitment[1]] ? $config['fitment'][$fitment[1]] : 2;    //装修
    $house_info['floor'] = $floor[1];  //楼层
    $house_info['totalfloor'] = $totalfloor[1]; //总楼层
    $house_info['buildarea'] = $area[1];    //建筑面积
    $house_info['price'] = $price[1];       //租金 元.月
    $house_info['title'] = $house_name[1] . ' ' . $title[1] . ' ' . $fitment[1];       //标题
    $house_info['content'] = strip_tags($content[1]);   //描述

    return $house_info;
  }

  //上传图片
  public function upload_image($url, $broker_id, $tbl, $publish_id)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie && !empty($finalname)) {
      $act = ($tbl == 1) ? '出售房源' : '出租房源';
      $post_url = 'http://www.jxpxf.cn/user/photosave.php';
      $post_field = array(
        "YLX" => iconv("UTF-8", "GB2312//IGNORE", $act),
        'YBH' => $publish_id,
        'YTP' => '@' . $finalname,
        'YMC' => iconv("UTF-8", "GB2312//IGNORE", '其他图'),
      );
      $tmpInfo = $this->curl->vpost($post_url, $post_field, $cookie);
    }
    if ($finalname) {
      @unlink($finalname);
    }
    return $data;
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

    $config = $this->getconfig();
    $block_info['district'] = $config['district'][$house_info['district_id']];   //'安源新区' -----测试时候替换-----
    //必须在表单验证后
    if (empty($block_info['district'])) {
      return array('flag' => 'proerror', 'info' => '区属不存在 请联系f100客服');
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

    $config = $this->getconfig();   //相关配置
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);  //经纪人信息
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $login = $this->isbind($broker_id);  //目标站点帐号信息
    $block_name = $house_info['block_name'];
    $the_type = $house_info['sell_type'];
    $district = $block_info['district'];
    //租房 设施
    $tmp_equipment = array();
    $config_equip = $config['equipment'];
    if (!empty($house_info['equipment'])) {
      $equip_arr = explode(',', $house_info['equipment']);
      foreach ($equip_arr as $val) {
        if (isset($config_equip[$val])) {
          $tmp_equipment[] = $config_equip[$val];
        }
      }
    }
    $str_equipment = implode('&YPT[]=', $tmp_equipment);

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
    $price = round($house_info['price'], 0);         //价格
    $area = round($house_info['buildarea'], 0);      //面积
    $fee = empty($house_info['strata_fee']) ? '' : round($house_info['strata_fee'], 1);  //物业费
    $fit = $config['fit'][$house_info['fitment']];   //装修
    $face = empty($config['face'][$house_info['forward']]) ? '坐北朝南' : $config['face'][$house_info['forward']];  //朝向
    $data_house['rdnum'] = $rdnum = rand(10000, 99999);  //标记房源用
    if ($the_type == 2) {  //房屋类型
      $yfx = '&YFX=别墅';
    } elseif ($the_type == 3) {
      $yfx = '&YFX=店面';
    } elseif ($the_type == 4) {
      $yfx = '&YFX=写字楼';
    } else {
      $house_type = $house_info['house_type'];
      if ($house_type == 2) {
        $yfx = '&YFX=公寓';
      } elseif ($house_type == 4) {
        $yfx = '&YFX=民房';
      } else {
        $yfx = '&YFX=多层';
      }
    }
    if ($tbl == 1) {
      $data_house['pro_url'] = 'http://www.jxpxf.cn/user/salepost.php';
      $data_house['post_url'] = 'http://www.jxpxf.cn/user/salesave.php';
      $data_house['post_field'] = 'YWN=未满五年&YXX=&YXZ=&YTD=国有出让&YSZ=证件齐全&MC1=小区图';
    } else {
      $paytype = $config['paytype'][$house_info['rentpaytype']];
      $data_house['pro_url'] = 'http://www.jxpxf.cn/user/rentpost.php';
      $data_house['post_url'] = 'http://www.jxpxf.cn/user/rentsave.php';
      $data_house['post_field'] = 'YFS=整租&YFK=' . $paytype;
    }
    $data_house['post_field'] .= '&YBH=' . $rdnum . '&YFW=' . $district . '&YDD=' . $block_name . $yfx . '&YHX1=' . $house_info['room'] . '&YHX2=' . $house_info['hall']
      . '&YHX3=' . $house_info['toilet'] . '&YLC=' . $house_info['floor'] . '&YZC=' . $house_info['totalfloor'] . '&YZX=' . $fit . '&YMJ=' . $area
      . '&YCK=&YCX=' . $face . '&YNF=' . $house_info['buildyear'] . '&YJG=' . $price . '&YBZ=' . strip_tags($house_info['bewrite']) . '&YSS=自己中介'
      . $str_equipment;
    $data_house['house_block_id'] = $house_info['block_id'];
    $result = $this->publish(array('data_house' => $data_house, 'queue' => $queue, 'login' => $login));

    //上传图片
    if ($house_info['pic_ids'] && $house_info['pic_tbl'] && $result['flag'] == 'success') {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $val) {
          $picurl = changepic_send($val['url']);
          $this->upload_image($picurl, $broker_id, $tbl, $result['publish_id']); //上传中
        }
      }
    }

    return $result;
  }
}

/* End of file site_pxf_model.php */
/* Location: ./application/mls/models/site_pxf_model.php */
