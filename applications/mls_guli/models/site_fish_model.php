<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_fish_base_model");

class Site_fish_model extends Site_fish_base_model
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
    $signatory_id = $this->signatory_info['signatory_id'];
    $username = $this->input->get('username');
    $password = $this->input->get('password');
    $code = $this->input->get('checkcode');

    $login = $this->login($username, $password, $code);
    if ($login['cookies']) {
      $data = array();
      $data['signatory_id'] = $signatory_id;
      $data['site_id'] = $site_id;
      $data['status'] = 1;
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['user_id'];
      $data['agent_id'] = 0;
      $data['otherpwd'] = $login['otherpwd'];
      $data['cookies'] = $login['cookies'];
      $data['createtime'] = time();

      //根据用户id和站点来判断mass_site_signatory表里是否存在 是：则更新 否：则插入
      $where = array('signatory_id' => $signatory_id, 'site_id' => $site_id);
      $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
      if (count($find) >= 1) {
        $result = $this->modify_data($where, $data, 'db_city', 'mass_site_signatory');
      } else {
        $result = $this->add_data($data, 'db_city', 'mass_site_signatory');
      }
    } else {
      $data = array('error' => 'yes', 'info' => isset($login['info']) ? $login['info'] : '绑定失败');
    }
    return $data;
  }

  //列表采集
  public function collect_list($act)
  {
    $site_id = $this->website['id'];
    $signatory_id = $this->signatory_info['signatory_id'];

    $config = $this->getconfig();
    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      if ($act == 'sell') {
        $listurl = array('http://fangzi.xmfish.com/admin/sell2.html', 'http://fangzi.xmfish.com/admin/sellb.html');//商铺出售在商业地产
        $pattern = '/type="checkbox" name="sid(.*)type="hidden" name="status"/siU';
      } else {
        $listurl = array('http://fangzi.xmfish.com/admin/hire.html'); //商铺出租在 租房
        $pattern = '/type="checkbox" name="hid(.*)type="hidden" name="status"/siU';
      }

      $num = 0;
      $project = $isback = $data_list = array();  //isback处理for循环超出分页,依然读到列表
      foreach ($listurl as $val) {
        for ($i = 1; $i < 10; $i++) {
          $tmpInfo = $this->curl->vget($val . '?&page=' . $i, $cookie);
          preg_match_all($pattern, $tmpInfo, $prj);
          if (count($prj[1]) > 0) {
            preg_match('/<td>([0-9]*)<\/td>/siU', $prj[1][0], $temp_end);
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
        preg_match('/class="font-gray fn-ml5 fn-mr5".*a href="(.*)">修改<\/a>/siU', $val, $url);
        preg_match('/class="w-50".*a href="(.*)" target="_blank">(.*)<\/a>/siU', $val, $infourl);
        preg_match('/class="fn-mt5">(.*)<\/p>/siU', $val, $des);
        preg_match('/<li>更新时间：(.*)<\/li>/siU', $val, $pubtime);

        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];   //编辑链接
        $data['infourl'] = $infourl[1];    //详情链接
        $data['title'] = strip_tags($infourl[2]);  //标题
        $data['des'] = strip_tags($des[1]);  //描述
        $data['releasetime'] = empty($pubtime[1]) ? 0 : strtotime(trim($pubtime[1]));   //发布时间
        $data['city_spell'] = $this->signatory_info['city_spell'];
        $data['signatory_id'] = $this->signatory_info['signatory_id'];
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
    $signatory_id = $this->signatory_info['signatory_id'];
    $city_spell = $this->signatory_info['city_spell'];

    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $tmpInfo = $this->curl->vget('http://fangzi.xmfish.com' . $url, $cookie);
    }

    preg_match('/name="stype" value="([0-9]*)" checked/siU', $tmpInfo, $stype);
    if ($stype[1] == '600') {
      $the_type = 4;  //写字楼
    } elseif ($stype[1] == '200') {
      $the_type = 3;  //商铺
    } else {
      $the_type = 1;
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    //图片
    preg_match_all('/input value="(.*)" name="imgurl\[\]"/iU', $tmpInfo, $pic);
    foreach ($pic[1] as $val) {
      $imgurl = explode('|', $val);
      $tempurl = $this->autocollect_model->get_pic_url('http://fangzi.xmfish.com' . $imgurl[0], $city_spell);
      $image[] = $tempurl;
    }
    if ($the_type == 1) {  //民宅 别墅
      preg_match('/name="stype".*value="([0-9]*)" selected/siU', $tmpInfo, $htype);
      preg_match('/name="village" type="text" value="(.*)" oninput/iU', $tmpInfo, $house_name);
      preg_match('/name="room".*value="([0-9])"/siU', $tmpInfo, $room);
      preg_match('/name="hall".*value="([0-9])"/siU', $tmpInfo, $hall);
      preg_match('/name="toilet".*value="([0-9])"/siU', $tmpInfo, $toilet);
      preg_match('/name="area".*value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="forward".*value="([0-9]*)" selected/siU', $tmpInfo, $farward);
      preg_match('/name="decorate".*value="([0-9]*)" selected/siU', $tmpInfo, $fitment);
      preg_match('/name="sellprice".*value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/input value="(.*)" name="title"/iU', $tmpInfo, $title);
      preg_match('/name="describ".*>(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $htype[1] == '103' ? 2 : 1;  //类型2别墅
      $house_info['house_name'] = $house_name[1];      //楼盘名称
      $house_info['room'] = $room[1];     //室
      $house_info['hall'] = $hall[1];     //厅
      $house_info['toilet'] = $toilet[1]; //卫
      $house_info['kitchen'] = 0; //厨房
      $house_info['balcony'] = 0; //阳台
      $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = '';  //楼层
      $house_info['totalfloor'] = ''; //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['price'] = $price[1];       //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 3) {  //商铺
      preg_match('/name="prop" type="text" value="(.*)"/siU', $tmpInfo, $house_name);
      preg_match('/id="area" name="area" type="text" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/name="sellprice".*class="inputcss" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/input value="(.*)" name="title"/siU', $tmpInfo, $title);
      preg_match('/name="describ".*>(.*)<\/textarea>/siU', $tmpInfo, $content);

      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1];     //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = $title[1]; //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 4) {  //写字楼
      preg_match('/name="prop" type="text" value="(.*)"/siU', $tmpInfo, $house_name);
      preg_match('/id="area" name="area" type="text" value="(.*)"/siU', $tmpInfo, $area);
      preg_match('/装修情况.*value="([0-9]*)" selected/siU', $tmpInfo, $fitment);
      preg_match('/name="sellprice".*class="inputcss" value="(.*)"/siU', $tmpInfo, $price);
      preg_match('/name="propm" type="text" value="(.*)"/siU', $tmpInfo, $fee);
      preg_match('/input value="(.*)" name="title"/siU', $tmpInfo, $title);
      preg_match('/name="describ".*>(.*)<\/textarea>/siU', $tmpInfo, $content);

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
    $signatory_id = $this->signatory_info['signatory_id'];
    $city_spell = $this->signatory_info['city_spell'];

    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $tmpInfo = $this->curl->vget('http://fangzi.xmfish.com' . $url, $cookie);
    }

    preg_match('/房源类型<\/label>(.*)<span id="hwayTip"/siU', $tmpInfo, $stype);
    $stype = trim($stype[1]);
    if ($stype == '写字楼') {
      $the_type = 4;  //写字楼
    } elseif ($stype == '商铺') {
      $the_type = 3;  //商铺
    } elseif ($stype == '别墅') {
      $the_type = 2;
    } else {
      $the_type = 1;
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    //图片
    preg_match_all('/input value="(.*)" name="imgurl\[\]"/iU', $tmpInfo, $pic);
    foreach ($pic[1] as $val) {
      $imgurl = explode('|', $val);
      $tempurl = $this->autocollect_model->get_pic_url('http://fangzi.xmfish.com' . $imgurl[0], $city_spell);
      $image[] = $tempurl;
    }
    preg_match('/name="village" type="text" value="(.*)"/iU', $tmpInfo, $house_name);
    preg_match('/name="room".*value="([0-9])"/iU', $tmpInfo, $room);
    preg_match('/name="hall".*value="([0-9])"/iU', $tmpInfo, $hall);
    preg_match('/name="toilet".*value="([0-9])"/iU', $tmpInfo, $toilet);
    preg_match('/选择朝向.*value="([0-9]*)" selected/siU', $tmpInfo, $farward);
    preg_match('/装修情况.*value="([0-9]*)" selected/siU', $tmpInfo, $fitment);
    preg_match('/name="floor".*value="([0-9]*)"/iU', $tmpInfo, $floor);
    preg_match('/name="allfloor".*value="([0-9]*)"/siU', $tmpInfo, $allfloor);
    preg_match('/name="area".*value="(.*)"/iU', $tmpInfo, $area);
    preg_match('/name="rent".*value="(.*)"/iU', $tmpInfo, $price);
    preg_match('/value="(.*)" name="title"/iU', $tmpInfo, $title);
    preg_match('/name="describ".*>(.*)<\/textarea>/siU', $tmpInfo, $content);

    $house_info['picurl'] = implode('*', $image);
    $house_info['sell_type'] = $the_type;   //住宅类型
    $house_info['house_name'] = $house_name[1];     //楼盘名称
    $house_info['room'] = empty($room[1]) ? 0 : $room[1];   //室
    $house_info['hall'] = empty($hall[1]) ? 0 : $hall[1];   //厅
    $house_info['toilet'] = empty($toilet[1]) ? 0 : $toilet[1];  //卫
    $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
    $house_info['serverco'] = $config['fitment'][$fitment[1]];    //装修
    $house_info['floor'] = $floor[1];    //楼层
    $house_info['totalfloor'] = $allfloor[1];   //总楼层
    $house_info['buildarea'] = $area[1];    //建筑面积
    $house_info['price'] = $price[1];       //租金 元.月
    $house_info['title'] = $title[1];       //标题
    $house_info['content'] = $content[1];   //描述

    return $house_info;
  }

  //上传图片到 中房网
  public function upload_image($url, $signatory_id)
  {
    $data = false;
    $finalname = $this->site_model->upload_img($url);
    $login = $this->isbind($signatory_id);
    if ($login['cookies'] && !empty($finalname)) {
      $cookie = $login['cookies'];
      $post_url = 'http://fangzi.xmfish.com/web/public_addimgdoit.html';
      $post_field = array('picdata' => '@' . $finalname);
      $tmpInfo = $this->curl->vpost($post_url, $post_field, $cookie, '', 0);
      $tmpInfo = json_decode($tmpInfo, true);
      $data = $tmpInfo['photo'];
    }
    if ($finalname) {
      @unlink($finalname);
    }
    return $data;
  }

  //获取区属
  public function get_district()
  {
    $district = '<option value="59201" >思明区</option>
                    <option value="59202" >湖里区</option>
                    <option value="59203" >集美区</option>
                    <option value="59204" >海沧区</option>
                    <option value="59205" >翔安区</option>
                    <option value="59206" >同安区</option>
                    <option value="59207" >厦门周边</option>';

    $tmpInfo = $this->curl->vget('http://fangzi.xmfish.com/web/public_getroad.html?canton=59201');
    $result = json_decode($tmpInfo);
    $street = substr($result->html, 38);
    $data = array('district' => $district, 'street' => $street);
    return $data;
  }

  //获取街道
  public function get_street($id)
  {
    $url = 'http://fangzi.xmfish.com/web/public_getroad.html?canton=' . $id;
    $tmpInfo = $this->curl->vget($url);
    $result = json_decode($tmpInfo);
    $street = substr($result->html, 38);
    return $street;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($alias = '', $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type');  //1民宅 2别墅 3商铺 4写字楼
    $keyword = trim($keyword);
    $signatory_id = $this->signatory_info['signatory_id'];

    $list = array();
    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      $url = 'http://fangzi.xmfish.com/web/public_village.html?term=' . $keyword;
      $tmpInfo = $this->curl->vget($url, $cookie);
      $info = json_decode($tmpInfo, true);
      if ($info) {
        foreach ($info as $val) {
          if ($key == 10) break;
          $list[] = array(
            'id' => $val['id'],
            'label' => $val['label']
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
    $signatory_id = $queue['signatory_id'];
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
    $login = $this->isbind($signatory_id);
    //发布信息
    $pub_sql = array('where' => array('signatory_id' => $signatory_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
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
      'signatory_id' => $signatory_id,
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
    $bool = $this->del(array('signatory_id' => $signatory_id, 'site_id' => $site_id, 'publish_id' => $publish_id), 'db_city', $pub_tbl);  //数据库删除
    return $result;
  }

  //是否转移到定时任务
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
    } elseif ($house_info['sell_type'] > 4) {
      return array('flag' => 'proerror', 'info' => '不支持 厂房 车库 仓库 类型');
    }

    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';  //2楼盘字典
      $data['block_area'] = $this->get_district();
    } else {
      //判断是否已经发布
      $pub_sql = array('where' => array('signatory_id' => $signatory_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
      $pubInfo = $this->get_data($pub_sql, 'dbback_city');
      $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
      if ($publish_id) {
        $login = $this->isbind($signatory_id);
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
    $this->load->model('signatory_info_model');
    $signatory_id = $queue['signatory_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $tbl = $queue['tbl'];
    $block_info = unserialize($queue['info']);
    if ($tbl == 1) {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);  //经纪人信息
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $login = $this->isbind($signatory_id);  //目标站点帐号信息
    $otherpwd = explode('|', $login['otherpwd']);
    $username = $otherpwd[0];
    $phone = $otherpwd[1];
    $cookie = $login['cookies'];
    $block_id = $block_info['block_id'];
    $block_name = $block_info['block_name'];
    $block_district = $block_info['district'];
    $block_street = $block_info['street'];
    $address = empty($house_info['address']) ? '' : $house_info['address'];
    $config = $this->getconfig();   //相关配置
    $the_type = $house_info['sell_type'];
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
    $str_equipment = implode('&equipment[]=', $tmp_equipment);

    //上传图片
    $pic_arr = array();
    $defimg = $image_list = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($pic_info) {
        foreach ($pic_info as $val) {
          $picurl = changepic_send($val['url']);
          $defimg = $this->upload_image($picurl, $signatory_id);
          $image_list .= '&imgurl[]=' . $defimg;
        }
      }
      $this->load->database();
      $this->db_city->reconnect();
    }

    //修改房源权限
    $this->load->model('signatory_purview_model');
    $this->signatory_purview_model->set_signatory_id($signatory_id, $signatory_info['company_id']);
    $role_level = intval($signatory_info['role_level']);   //获得当前经纪人的角色等级，判断店长以上or店长以下
    if (is_int($role_level) && $role_level > 6) {         //店长以下的经纪人不允许操作他人的私盘
      if ($signatory_id != $house_info['signatory_id'] && $house_info['nature'] == '1') {
        $result = $this->my_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], 'dbback_city');  //当前经纪人的临时详情
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
    $avgprice = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;  //均价
    $sfje = round($price * 0.3, 0);              //首付金额
    $fit = $config['fit'][$house_info['fitment']];   //装修
    $face = $config['face'][$house_info['forward']]; //朝向
    if ($house_info['totalfloor'] < 6) {
      $floor = '101';
    } elseif ($house_info['totalfloor'] < 11) {
      $floor = '102';
    } elseif ($house_info['totalfloor'] < 19) {
      $floor = '103';
    } elseif ($house_info['totalfloor'] < 26) {
      $floor = '104';
    } else {
      $floor = '105';
    }
    if ($tbl == 1) {
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $stype = $the_type == 2 ? '103' : '100';
        $data_house['post_url'] = 'http://fangzi.xmfish.com/admin/sell2_add.html';
        $data_house['post_field'] = 'village=' . $block_name . '&room=' . $house_info['room'] . '&hall=' . $house_info['hall'] . '&toilet=' . $house_info['toilet']
          . '&stype=' . $stype . '&structure=101&forward=' . $face . '&agelimit=70&skind=101&agecreate=' . $house_info['buildyear'];
      } elseif ($the_type == 3) { //商铺
        $proptype = empty($config['stype'][$house_info['shop_type']]) ? '102' : $config['stype'][$house_info['shop_type']];  //102商业街商铺
        $data_house['post_url'] = 'http://fangzi.xmfish.com/admin/sellb_add.html';
        $data_house['post_field'] = 'prop=' . $block_name . '&stype=200&proptype=' . $proptype;
      } elseif ($the_type == 4) { //写字楼
        $proptype = ($house_info['office_type'] == 2) ? 102 : 101;  //商住 ：纯写字楼
        $data_house['post_url'] = 'http://fangzi.xmfish.com/admin/sellb_add.html';
        $data_house['post_field'] = 'prop=' . $block_name . '&stype=600&proptype=' . $proptype . '&propm=' . $fee;
      }
      $data_house['post_field'] .= '&do=b2319d8ee55f0921a9208d2c869133b7&sfrom=2&villid=&lift=100&canton=' . $block_district . '&road=' . $block_street
        . '&road2=&address=' . $address . '&sellprice=' . $price . '&averprice=' . $avgprice . '&firstpay=' . $sfje . '&area=' . $area . '&decorate=' . $fit
        . '&floor=' . $floor . '&title=' . urlencode($house_info['title']) . '&describ=' . urlencode($house_info['bewrite'])
        . '&imgdefault=' . $defimg . $image_list . '&bargain=' . $username . '&tel=' . $phone;
    } elseif ($tbl == 2) {
      $paytype = $config['paytype'][$house_info['rentpaytype']];
      if ($the_type == 1 || $the_type == 2) { //住宅 别墅
        $htype = $the_type == 2 ? '102' : '100';
        $data_house['post_field'] = 'htype=' . $htype . '&room=' . $house_info['room'] . '&hall=' . $house_info['hall'] . '&toilet=' . $house_info['toilet']
          . '&equipment[]=' . $str_equipment;
      } elseif ($the_type == 3) { //商铺
        $data_house['post_field'] = 'htype=200&room=1&hall=1&toilet=1&equipment[]=';
      } elseif ($the_type == 4) { //写字楼
        $data_house['post_field'] = 'htype=600&room=1&hall=1&toilet=1&equipment[]=';
      }
      $data_house['post_url'] = 'http://fangzi.xmfish.com/admin/hire_add.html';
      $data_house['post_field'] .= '&do=b2319d8ee55f0921a9208d2c869133b7&hfrom=2&hway=1&villid=&village=' . $block_name . '&canton=' . $block_district
        . '&road=' . $block_street . '&address=' . $address . '&floor=' . $house_info['floor'] . '&allfloor=' . $house_info['totalfloor']
        . '&area=' . $area . '&rent=' . $price . '&paytype=' . $paytype . '&forward=' . $face . '&decorate=' . $fit
        . '&title=' . urlencode($house_info['title']) . '&describ=' . urlencode($house_info['bewrite'])
        . '&tag[]=&imgdefault=' . $defimg . $image_list . '&bargain=' . $username . '&tel=' . $phone;
    }

    $data_house['house_block_id'] = $house_info['block_id'];
    $data_house['title'] = $house_info['title'];
    $result = $this->publish(array('data_house' => $data_house, 'queue' => $queue, 'login' => $login));
    return $result;
  }
}

/* End of file site_fish_model.php */
/* Location: ./application/mls_guli/models/site_fish_model.php */
