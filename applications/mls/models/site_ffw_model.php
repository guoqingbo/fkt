<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_ffw_base_model");

class Site_ffw_model extends Site_ffw_base_model
{
  private $site;
  private $orientation; //朝向
  private $serverco; //装修
  private $equipment; //租房 设施

  public function __construct()
  {
    parent::__construct();
    $this->load->library('Curl');
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
    $this->orientation = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
    $this->serverco = array('毛坯' => 1, '普通装修' => 2, '精装修' => 4, '豪华装修' => 5);
    $this->site = $this->site_model->get_site_byid(array('alias' => 'ffw'));
    $this->site = $this->site[0];
  }

  //检查是否绑定
  public function isbind_site()
  {
    $site_id = $this->site['id'];
    $broker_id = $this->broker_info['broker_id'];
    $site_broker = $this->site_model->get_brokerinfo_byids(array('broker_id' => $broker_id, 'site_id' => $site_id, 'status' => 1));
    if (!empty($site_broker[0])) {
      $data['username'] = $site_broker[0]['username'];
      $data['password'] = $site_broker[0]['password'];
      $data['cookie'] = $site_broker[0]['cookies'];
      $data['user_id'] = $site_broker[0]['user_id'];
      $data['cityurl'] = $this->fang_url;
    } else {
      $data['cookie'] = '';
    }
    return $data;
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->site['id'];
    $broker_id = $this->broker_info['broker_id'];
    $username = $this->input->get('username');
    $password = $this->input->get('password');
    $otherpwd = $this->input->get('otherpwd');

    $login = $this->check_login();
    if (!$login) {
      return '123';  //绑定失败
    } else {
      $data = array();
      $data['broker_id'] = $broker_id;
      $data['site_id'] = $site_id;
      $data['status'] = '1';
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['userid'];
      $data['cookies'] = $login['token'];
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
    $login = $this->isbind_site();
    $cookie = empty($login['cookie']) ? '' : $login['cookie'];
    $user_id = empty($login['user_id']) ? '' : $login['user_id'];

    if ($cookie) {
      $project = array();
      $type = ($act == 'sell') ? 'itf/salelist.php' : 'itf/rentlist.php';

      $url = $this->api_url . $type;
      $post_fields = array('id' => $user_id, 'token' => $cookie);
      $post_fields['sign'] = $this->makeSign($post_fields);

      $tmpInfo = $this->curl->static_vpost($url, $post_fields, 0, 0, false);
      $json = json_decode($tmpInfo, true);
      unset($tmpInfo);
      if ('0' == $json['ret_code']) {
        if (isset($json['data']['list']) && is_array($json['data']['list'])) {
          foreach ($json['data']['list'] as $value) {

            $data = array();
            $data['source'] = 0;
            $data['infourl'] = $value['id'];  //详情链接
            $data['title'] = trim($value['title']);//标题

            $data['city_spell'] = $this->broker_info['city_spell'];
            $data['broker_id'] = $this->broker_info['broker_id'];
            $data['site_id'] = $this->site['id'];

            $url = $this->api_url . 'itf/get.php';
            $post_fields = array('id' => $value['id']);
            $post_fields['sign'] = $this->makeSign($post_fields);

            $_tmpInfo = $this->curl->static_vpost($url, $post_fields, 0, 0, false);
            $_json = json_decode($_tmpInfo, true);
            if ('0' == $_json['ret_code']) {
              $data['des'] = $this->autocollect_model->con_replace($_json['data']['intro']);
              $data['releasetime'] = strtotime($_json['data']['f64']); //发布时间
            }

            if ($act == 'sell') {
              $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
            } else {
              $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
            }
            if ($res) $num++;
          }
        }
      }

      return $num;
    } else {
      echo 'no cookie';
    }
  }

  //详情页面导入:二手房
  public function collect_sell_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $houseid = $this->input->get('houseid');
    $flag = $this->input->get('flag');
    $city_spell = $this->broker_info['city_spell'];
    $type = array('普通住宅' => 1, '别墅' => 2, '商铺' => 3, '写字楼' => 4, '厂房' => 5, '仓库' => 6, '车库' => 7, '商住楼' => 1, '民房' => 1);

    //$find = $this->check_login();
    $find = $this->isbind_site();
    if ($find['cookie']) {
      $house_info = $image = array();

      $url = $this->api_url . 'itf/get.php';
      $post_fields = array('id' => $infourl);
      $post_fields['sign'] = $this->makeSign($post_fields);

      $_tmpInfo = $this->curl->static_vpost($url, $post_fields, 0, 0, false);
      $_json = json_decode($_tmpInfo, true);
      if ('0' == $_json['ret_code']) {
        $sell_type = $type[$_json['data']['f3']];
        $house_info['sell_type'] = $sell_type;  //出售类型

        $pic = array();
        if (is_array($_json['data']['pic'])) {
          foreach ($_json['data']['pic'] as $value) {
            $pic[] = $value['path'];
          }
        }
        $house_info['picurl'] = implode('*', $pic);

        $house_info['house_name'] = trim($_json['data']['f10']); //楼盘名称
        $house_info['buildarea'] = $_json['data']['f19'];  //建筑面积
        $house_info['price'] = $_json['data']['f16'];  //总价
        $house_info['title'] = trim($_json['data']['f1']);    //标题
        $house_info['avgprice'] = empty($house_info['buildarea']) ? '' : round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
        $house_info['room'] = $_json['data']['f4'];
        $house_info['hall'] = $_json['data']['f5'];
        $house_info['toilet'] = $_json['data']['f6'];
        $house_info['balcony'] = $_json['data']['f7'];
        if ($sell_type != 2) {
          $house_info['floor'] = $_json['data']['f12'];
        }
        $house_info['totalfloor'] = $_json['data']['f13'];
        $decoration = array('毛坯' => 1, '简易装修' => 2, '精装修' => 4, '高档装修' => 5, '中档装修' => 3);
        $house_info['serverco'] = $decoration[$_json['data']['f22']];
        $house_info['build_date'] = $_json['data']['f15'];

        $house_info['content'] = $this->autocollect_model->con_replace(strip_tags($_json['data']['intro']));    //房源描述
        $house_info['kitchen'] = 0; //厨房
        $house_info['owner'] = '';  //业主姓名
        $house_info['telno1'] = ''; //业主电话
      }
      return $house_info;
    } else {
      return false;
    }
  }

  //详情页面导入:租房
  public function collect_rent_info()
  {
    $url = $this->input->get('url');
    $infourl = $this->input->get('infourl');
    $houseid = $this->input->get('houseid');
    $flag = $this->input->get('flag');
    $city_spell = $this->broker_info['city_spell'];
    $type = array('普通住宅' => 1, '别墅' => 2, '商铺' => 3, '写字楼' => 4, '厂房' => 5, '仓库' => 6, '车库' => 7, '商住楼' => 1, '民房' => 1);

    //$find = $this->check_login();
    $find = $this->isbind_site();
    if ($find['cookie']) {
      $house_info = $image = array();

      $url = $this->api_url . 'itf/get.php';
      $post_fields = array('id' => $infourl);
      $post_fields['sign'] = $this->makeSign($post_fields);

      $_tmpInfo = $this->curl->static_vpost($url, $post_fields, 0, 0, false);
      $_json = json_decode($_tmpInfo, true);
      if ('0' == $_json['ret_code']) {
        $sell_type = $type[$_json['data']['f3']];
        $house_info['sell_type'] = $sell_type;  //出租类型

        $pic = array();
        if (is_array($_json['data']['pic'])) {
          foreach ($_json['data']['pic'] as $value) {
            $pic[] = $value['path'];
          }
        }
        $house_info['picurl'] = implode('*', $pic);

        $house_info['house_name'] = trim($_json['data']['f10']); //楼盘名称
        $house_info['buildarea'] = $_json['data']['f14'];  //使用面积

        $pricenum = $_json['data']['f33'];//价格
        $pricetype = $_json['data']['f34'];;//价格类型
        switch ($pricetype) {
          case '元/年':
            $house_info['price'] = round($pricenum / 365);
            break;
          case '元/㎡/月':
            $house_info['price'] = $pricenum * $house_info['buildarea'];
            break;
          case '元/㎡/天':
            $house_info['price'] = $pricenum * $house_info['buildarea'] * 30;
            break;
          default :
            $house_info['price'] = $pricenum;
            break;
        }
        $house_info['title'] = trim($_json['data']['f1']);    //标题
        $house_info['room'] = $_json['data']['f4'];
        $house_info['hall'] = $_json['data']['f5'];
        $house_info['toilet'] = $_json['data']['f6'];
        $house_info['balcony'] = $_json['data']['f7'];
        if ($sell_type != 2) {
          $house_info['floor'] = $_json['data']['f12'];
        }
        $house_info['totalfloor'] = $_json['data']['f13'];
        $decoration = array('毛坯' => 1, '简易装修' => 2, '精装修' => 4, '高档装修' => 5, '中档装修' => 3);
        $house_info['serverco'] = $decoration[$_json['data']['f22']];
        $house_info['build_date'] = $_json['data']['f15'];

        $house_info['content'] = $this->autocollect_model->con_replace(strip_tags($_json['data']['intro']));    //房源描述
        $house_info['owner'] = '';  //业主姓名
        $house_info['telno1'] = ''; //业主电话
      }
      return $house_info;
    } else {
      return false;
    }
  }

  //获取发布所需房屋配置信息
  public function get_house($house_id, $block_info, $act)
  {
    $city = $this->broker_info['city_spell'];
    $cityname = $this->broker_info['cityname'];
    $broker_id = $this->broker_info['broker_id'];
    $site_id = $this->fang['id'];
    $site_broker = $this->site_model->get_brokerinfo_byids(array('broker_id' => $broker_id, 'site_id' => $site_id, 'status' => 1));
    $userid = $site_broker[0]['user_id'];
    $username = $site_broker[0]['username'];

    //房屋基本信息
    if ($act == 'sell') {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    //提交前,表单验证
    $mustflg = $this->input->get('must');
    if (!$mustflg) {
      if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
        echo json_encode(array('flag' => 9, 'info' => '楼层最多2位数'));
        exit;
      } elseif ($house_info['floor'] > $house_info['totalfloor']) {
        echo json_encode(array('flag' => 9, 'info' => '所在楼层不能大于总楼层'));
        exit;
      } elseif (mb_strlen($house_info['title']) < 1 || mb_strlen($house_info['title']) > 30) {
        echo json_encode(array('flag' => 9, 'info' => '房源标题1-30字'));
        exit;
      }
    }
    //必须在表单验证后
    if (empty($block_info)) {
      return false;
    } else {
      $block_name = $block_info['block_name'];
      $address = $block_info['address'];
      $district = $block_info['district'];
      $street = $block_info['street'];
    }

    //上传图片
    $shinei_pic = $huxing_pic = '';
    $shinei = $huxing = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $this->load->model('pic_model');
      $pic_info = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($house_info['sell_type'] == 4) {
        if ($pic_info) {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            if ($val['type'] == 1) {
              $shinei[] = $this->upload_image($picurl, $broker_id, 'Nfile');
            } else if ($val['type'] == 2) {
              $huxing[] = $this->upload_image($picurl, $broker_id, 'Pfile');
            }
          }
          $_i = 1;
          foreach ($huxing as $key => $val) {
            $huxing_pic .= '&txtImageDes_5_' . $_i . '=平面图&txtImage_5_' . $_i . '=' . $val;
            $_i++;
          }
          foreach ($shinei as $key => $val) {
            $shinei_pic .= '&txtImageDes_6_' . $_i . '=内景图&txtImage_6_' . $_i . '=' . $val;
            $_i++;
          }
          $_i--;
        }
      } else if ($house_info['sell_type'] == 3) {
        if ($pic_info) {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            $shinei[] = $this->upload_image($picurl, $broker_id, 'Nfile');
          }
          $_i = 1;
          foreach ($shinei as $key => $val) {
            $shinei_pic .= '&txtImageDes_6_' . $_i . '=内景图&txtImage_6_' . $_i . '=' . $val;
            $_i++;
          }
          $_i--;
        }
      } else {
        if ($pic_info) {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            if ($val['type'] == 1) {
              $shinei[] = $this->upload_image($picurl, $broker_id, 'Sfile');
            } else if ($val['type'] == 2) {
              $huxing[] = $this->upload_image($picurl, $broker_id, 'Hfile');
            }
          }
          $_i = 1;
          foreach ($huxing as $key => $val) {
            $huxing_pic .= '&txtImageDes_3_' . $_i . '=户型图&txtImage_3_' . $_i . '=' . $val;
            $_i++;
          }
          foreach ($shinei as $key => $val) {
            $shinei_pic .= '&txtImageDes_1_' . $_i . '=室内图&txtImage_1_' . $_i . '=' . $val;
            $_i++;
          }
          $_i--;
        }
      }
    }
    $this->load->database();
    $this->db_city->reconnect();

    $fang_fitment = array(1 => '毛坯', 2 => '简装修', 3 => '中等装修', 4 => '精装修', 5 => '豪华装修', 6 => '豪华装修'); //装修匹配:住宅 别墅
    $shop_fitment = array(1 => '毛坯', 2 => '简装修', 3 => '简装修', 4 => '精装修', 5 => '精装修', 6 => '精装修');  //装修匹配:商铺 写字楼
    $fang_forward = array(1 => '东', 2 => '东南', 3 => '南', 4 => '西南', 5 => '西', 6 => '西北', 7 => '北', 8 => '东北', 9 => '东西', 10 => '南北');
    $forward = $fang_forward[$house_info['forward']];

    $fang_equipment = $this->equipment;  //租房 房屋设施
    $tmp_equipment = array();
    if (!empty($house_info['equipment'])) {
      $equipment = explode(',', $house_info['equipment']);
      foreach ($equipment as $val) {
        if (isset($fang_equipment[$val])) {
          $tmp_equipment[] = $fang_equipment[$val];
        }
      }
    }
    $str_equipment = implode('&input_n_str_BASESERVICE=', $tmp_equipment);  //input_n_str_BASESERVICE=床  +++++++++++++++++

    //修改房源权限
    $this->load->model('broker_permission_model');
    $this->broker_permission_model->set_broker_id($broker_id, $this->broker_info['company_id']);
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($this->broker_info['role_level']);
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
    //$house_info['bewrite'] = strip_tags($house_info['bewrite']);
    //$house_info['bewrite'] = $this->autocollect_model->con_replace($house_info['bewrite']);
    $house_info['bewrite'] = $this->autocollect_model->special_replace($house_info['bewrite']);  //描述富文本处理
    $data_house = array();
    $data_house['block_name'] = $block_name;
    $data_house['district'] = $district;
    $data_house['street'] = $street;
    $data_house['address'] = $address;
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    $area = round($house_info['buildarea'], 0);
    $price = round($house_info['price'], 0);
    if ($act == 'sell') { //出售 sell
      if ($the_type == 1) { //住宅
        $fitment = $fang_fitment[$house_info['fitment']];
        $data_house['post_fielde'] = 'input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
          . '&input_KITCHEN=' . $house_info['kitchen'] . '&input_BALCONY=' . $house_info['balcony'] . '&input_n_str_CREATETIME=' . $house_info['buildyear']
          . '&input_FLOOR=' . $house_info['floor'] . '&input_ALLFLOOR=' . $house_info['totalfloor'] . '&input_n_str_FORWARD=' . $forward
          . '&Hfile=' . $huxing_pic . '&Sfile=' . $shinei_pic . '&imageCount=' . $_i . '&input_n_str_LOOKHOUSE=随时看房&input_DelegateIDAndAgentID=0'
          . '&input_y_str_PRICETYPE=万元/套&input_y_str_PAYINFO=个人产权&input_PropertySubType=普通住宅&input_y_str_PURPOSE=住宅&newcode=3111129518';
      } elseif ($the_type == 2) { //别墅
        $fitment = $fang_fitment[$house_info['fitment']];
        $villa_list = array(1 => '联排', 2 => '叠加', 3 => '双拼', 4 => '独栋'); //别墅用
        $villa = empty($house_info['villa_type']) ? 4 : $house_info['villa_type'];
        $villa_type = $villa_list[$villa];
        if ($house_info['floor_area']) {
          $floor_area = "&input_isHaveCellar=有&input_y_int_workshopArea=" . $house_info['floor_area'];
          if ($house_info['light_type'] == 1) {
            $floor_area .= "&input_n_str_shopType=全明";
          } else if ($house_info['light_type'] == 2) {
            $floor_area .= "&input_n_str_shopType=半明";
          } else {
            $floor_area .= "&input_n_str_shopType=暗";
          }
        }
        $garden_area = empty($house_info['garden_area']) ? '&input_isHaveGarden=无&input_y_int_spaceArea='
          : '&input_isHaveGarden=有&input_y_int_spaceArea=' . $house_info['garden_area'];
        $park_num = empty($house_info['park_num']) ? '&input_isHaveparkingPlace=无&input_y_int_parkingPlace='
          : '&input_isHaveparkingPlace=有&input_y_int_parkingPlace=' . $house_info['park_num'];
        $data_house['post_fielde'] = 'input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
          . '&input_KITCHEN=' . $house_info['kitchen'] . '&input_BALCONY=' . $house_info['balcony'] . '&input_n_str_CREATETIME=' . $house_info['buildyear']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $floor_area . $garden_area . $park_num . '&input_str_BuildingType=' . $villa_type
          . '&Hfile=' . $huxing_pic . '&Sfile=' . $shinei_pic . '&imageCount=' . $_i . '&b832bsdf4inpu=9e03a5input&input_DelegateIDAndAgentID=0'
          . '&input_y_str_PRICETYPE=万元/套&input_isHaveGarage=无&input_n_str_LOOKHOUSE=随时看房&input_y_str_PURPOSE=别墅&newcode=2110445582';
      } elseif ($the_type == 3) { //商铺
        $fitment = $shop_fitment[$house_info['fitment']];
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';
        $data_house['post_fielde'] = 'input_n_num_PropFee=' . $house_info['strata_fee'] . '&input_FLOOR=' . $house_info['floor']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&Wfile=&Nfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&input_y_str_PRICETYPE=万元/套&input_y_str_SubType=商业街商铺&input_y_str_PURPOSE=商铺&newcode=3110971366';
      } elseif ($the_type == 4) { //写字楼
        $fitment = $shop_fitment[$house_info['fitment']];
        $house_info['price'] = $house_info['price'] * 10000 / $house_info['buildarea'];  //写字楼 元/平米
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';
        $data_house['post_fielde'] = 'input_n_num_PropFee=' . $house_info['strata_fee'] . '&input_FLOOR=' . $house_info['floor']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&Pfile=' . $huxing_pic . '&Nfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&input_y_str_PRICETYPE=元/平米&input_n_str_propertyGrade=甲级&input_y_str_PURPOSE=写字楼&newcode=3111048994';
      } else {
        echo json_encode(array('flag' => 9, 'info' => '搜房不支持厂房、仓库、车库', 'after' => 1));
        exit;
      }
      if ($city == 'hrb') {
        $b_html = '&f606a287input3d41=' . $area . '&2e333063input530a=' . $house_info['title'];
      } else {
        $b_html = '&6b5e2e9binput9d54=' . $area . '&5fed3002input40d5=' . $house_info['title'];
      }
      $data_house['act'] = 'sell';
      $data_house['post_url'] = '/MAgent/house/InputSave.aspx?flag=1&haveMultipleCity=False&city=' . $cityname . '&isWireless=0';
      $data_house['post_fielde'] .= '&input_y_str_PROJNAME=' . $block_name . '&input_y_str_ADDRESS=' . $address . '&input_y_str_DISTRICT=' . $district
        . '&input_y_str_COMAREA=' . $street . '&input_y_num_PRICE=' . $price . '&input_n_str_FITMENT=' . $fitment . '&input_y_str_MANAGERNAME=' . $username
        . '&hiddenProjname=' . $block_name . '&input_n_str_CONTENT=' . html_entity_decode($house_info['bewrite']) . $b_html
        . '&hdHouseDicCity=false&input_y_str_BUSINESSTYPE=CS&input_draftsID=0';
    } else {  //出租 rent
      if ($the_type == 1) { //住宅
        $fitment = $fang_fitment[$house_info['fitment']];
        $data_house['post_fielde'] = 'input_n_str_BASESERVICE=' . $str_equipment . '&HouseTags=拎包入住'
          . '&input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
          . '&input_KITCHEN=' . $house_info['kitchen'] . '&input_BALCONY=' . $house_info['balcony'] . '&input_n_str_FORWARD=' . $forward
          . '&input_FLOOR=' . $house_info['floor'] . '&input_ALLFLOOR=' . $house_info['totalfloor'] . '&input_y_str_PURPOSE=住宅'
          . '&Hfile=' . $huxing_pic . '&Sfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&newcode=3110005834&input_y_str_PRICETYPE=元/月&input_DelegateIDAndAgentID=0&b832bsdf4inpu=9e03a5input';
      } elseif ($the_type == 2) { //别墅
        $fitment = $fang_fitment[$house_info['fitment']];
        $villa_list = array(1 => '联排', 2 => '叠加', 3 => '双拼', 4 => '独栋'); //别墅用
        $villa = empty($house_info['villa_type']) ? 4 : $house_info['villa_type'];
        $villa_type = $villa_list[$villa];
        if ($house_info['floor_area']) {
          $floor_area = "&input_isHaveCellar=有&input_y_int_workshopArea=" . $house_info['floor_area'];
          if ($house_info['light_type'] == 1) {
            $floor_area .= "&input_n_str_shopType=全明";
          } else if ($house_info['light_type'] == 2) {
            $floor_area .= "&input_n_str_shopType=半明";
          } else {
            $floor_area .= "&input_n_str_shopType=暗";
          }
        }
        $garden_area = empty($house_info['garden_area']) ? '&input_isHaveGarden=无&input_y_int_spaceArea='
          : '&input_isHaveGarden=有&input_y_int_spaceArea=' . $house_info['garden_area'];
        $park_num = empty($house_info['park_num']) ? '&input_isHaveparkingPlace=无&input_y_int_parkingPlace='
          : '&input_isHaveparkingPlace=有&input_y_int_parkingPlace=' . $house_info['park_num'];
        $data_house['post_fielde'] = 'input_str_BuildingType=' . $villa_type
          . '&input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
          . '&input_KITCHEN=' . $house_info['kitchen'] . '&input_BALCONY=' . $house_info['balcony'] . '&input_n_str_CREATETIME=' . $house_info['buildyear']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $floor_area . $garden_area . $park_num . '&input_y_str_PURPOSE=别墅&input_n_str_EQUITMENT=床'
          . '&input_n_str_LOOKHOUSE=随时看房' . '&Hfile=' . $huxing_pic . '&Sfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&newcode=3110005803&input_y_str_PRICETYPE=元/月&input_DelegateIDAndAgentID=0&b832bsdf4inpu=9e03a5input';
      } elseif ($the_type == 3) { //商铺
        $fitment = $shop_fitment[$house_info['fitment']];
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';  //可分割
        $propfee = ($house_info['strata_fee'] > 1) ? '&input_y_int_isIncludFee=0&input_n_num_PropFee='
          : '&input_y_int_isIncludFee=1&input_n_num_PropFee=' . round($house_info['strata_fee'], 0); //物业费
        $data_house['post_fielde'] = 'input_y_str_SubType=商业街商铺&input_shopstatus=闲置中&rdPriceType=元/月' . $propfee
          . '&input_istransfer=0&input_transferfee=面议'
          . '&input_FLOOR=' . $house_info['floor'] . "&input_ALLFLOOR=" . $house_info['totalfloor'] . $division . '&input_y_str_PURPOSE=商铺'
          . "&Nfile=" . $shinei_pic . '&imageCount=' . $_i
          . '&newcode=3110930598&input_y_str_PRICETYPE=元/月';
      } elseif ($the_type == 4) { //写字楼
        $fitment = $shop_fitment[$house_info['fitment']];
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';  //可分割
        $propfee = empty($house_info['strata_fee']) ? '&input_y_int_isIncludFee=0&input_n_num_PropFee='
          : '&input_y_int_isIncludFee=1&input_n_num_PropFee=' . round($house_info['strata_fee'], 0); //物业费
        $data_house['post_fielde'] = 'input_y_str_SubType=纯写字楼&rdPriceType=元/月' . $propfee
          . '&input_y_str_PAYINFO=个人产权&input_n_str_propertyGrade=甲级'
          . '&input_FLOOR=' . $house_info['floor'] . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&input_y_str_PURPOSE=写字楼'
          . '&Hfile=&Pfile=' . $huxing_pic . '&Nfile=' . $shinei_pic . '&imageCount=' . $_i . '&newcode=3110005884&input_y_str_PRICETYPE=元/月';
      } else {
        echo json_encode(array('flag' => 9, 'info' => '搜房不支持厂房、仓库、车库', 'after' => 1));
        exit;
      }
      if ($city == 'hrb') {
        $b_html = '&f606a287input3d41=' . $area . '&2e333063input530a=' . $house_info['title'];
      } else {
        $b_html = '&6b5e2e9binput9d54=' . $area . '&5fed3002input40d5=' . $house_info['title'];
      }
      $data_house['act'] = 'rent';
      $data_house['post_url'] = '/MAgent/house/InputSave.aspx?flag=1&haveMultipleCity=False&city=' . $cityname . '&isWireless=0';
      $data_house['post_fielde'] .= '&input_y_str_PROJNAME=' . $block_name . '&input_y_str_ADDRESS=' . $address . '&input_y_str_DISTRICT=' . $district
        . '&input_y_str_COMAREA=' . $street . '&input_y_num_PRICE=' . $price . '&input_n_str_FITMENT=' . $fitment . '&input_y_str_MANAGERNAME=' . $username
        . "&hiddenProjname=" . $block_name . '&input_n_str_CONTENT=' . html_entity_decode($house_info['bewrite']) . $b_html
        . '&hdHouseDicCity=0&input_y_str_BUSINESSTYPE=CZ&input_draftsID=0&input_y_str_LEASESTYLE=整租&input_y_str_PAYDETAIL_Y=押一付三';
    }
    $data_house['house_block_id'] = $house_info['block_id'];
    return $data_house;
  }

  //发布到目标站点
  public function send_house($data_house, $house_id)
  {
    $act = $data_house['act'];  //sell rent
    //$post_url = mb_convert_encoding($data_house['post_url'], "gb2312", "UTF-8");
    //$post_fields = mb_convert_encoding($data_house['post_fielde'], "gb2312", "UTF-8");
    $post_url = iconv('UTF-8', 'gb2312//IGNORE', $data_house['post_url']);
    $post_fields = iconv('UTF-8', 'gb2312//IGNORE', $data_house['post_fielde']);
    $the_type = $data_house['sell_type']; //1民宅 2别墅 3商铺 4写字楼

    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $broker_info = $this->broker_info;
    $city = $broker_info['city_spell'];
    $broker_id = $broker_info['broker_id'];
    $this->load->model('group_publish_model');

    $ymd = $this->input->get_post('createtime', TRUE);
    $paramlog = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'site_id' => $this->fang['id'],
      'block_id' => $house_info['block_id'],
      'sell_type' => ($act == 'sell') ? 1 : 2, //1出售,2出租
      'ymd' => $ymd ? $ymd : time()
    );

    //$login = $this->check_login();
    $login = $this->isbind_site();
    $this->load->database();
    $this->db_city->reconnect();

    $paramlog['username'] = empty($login['username']) ? '' : $login['username'];
    if ($login['cookie']) {
      //$post_url = 'http://'.$login['cityurl'].$post_url;  //http://sh.agent.fang.com
      $post_url = 'http://' . $this->fang_url . $post_url;
      $tmpInfo = $this->curl->vpost($post_url, $post_fields, $login['cookie']);
      preg_match('/houseid=(.*)&/siU', $tmpInfo, $prj);
      if (!empty($prj)) {
        //可能存在发布已满,存入待发布列表的情况,仍然可以取到 publish_id,却取不到 publish_url,算发布失败
        $publish_id = $prj[1];
        $type = ($act == 'sell') ? 'cs' : 'cz';
        $url = 'http://' . $login['cityurl'] . '/magent/house/houselist.aspx?flag=1&businesstype=' . $type . '&txtHouseId=' . $publish_id;
        $conInfo = $this->curl->vget($url, $login['cookie']);
        preg_match('/id=\'' . $publish_id . '_isOrder\'.*<a href="(.*)".*id="agentmahouse_input_form_houselink">/siU', $conInfo, $infourl);
        $publish_url = empty($infourl[1]) ? '' : $infourl[1];

        if (empty($publish_url)) {
          $paramlog['type'] = 2; //1成功,2失败
          $paramlog['info'] = '搜房今日发布量已用完,请明日再发';
          $log_id = $this->group_publish_model->add_publish_log($paramlog);
          $over = array('flag' => 9, 'info' => $paramlog['info'], 'after' => 1);
          return $over;
        }
        $param = array(
          'broker_id' => $broker_id,
          'house_id' => $house_id,
          'site_id' => $this->fang['id'],
          'publish_id' => $publish_id,
          'publish_url' => $publish_url,
          'createtime' => time(),
          'updatetime' => time()
        );
        if ($act == 'sell') {
          $insert_id = $this->group_publish_model->add_sell_info($param);
          $tbl = 1;
        } else {
          $insert_id = $this->group_publish_model->add_rent_info($param);
          $tbl = 2;
        }
        $paramlog['type'] = 1; //1成功,2失败
        $paramlog['info'] = '发布成功';
        $log_id = $this->group_publish_model->add_publish_log($paramlog);
        $data = array('flag' => 1, 'publish_url' => $publish_url);

        //孙老师统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝   tbl:1出售 2出租
        $this->load->model('publish_count_num_model');
        $this->publish_count_num_model->info_count($this->broker_info, 6, array('tbl' => $tbl, 'house_id' => $house_id));
        //记录 搜房楼盘
        $sf = array('block_id' => $house_info['block_id'],
          'agency_id' => $broker_info['agency_id'],
          'company_id' => $broker_info['company_id'],
          'fang_block' => $data_house['block_name'],
          'fang_district' => $data_house['district'],
          'fang_street' => $data_house['street'],
          'fang_address' => $data_house['address']
        );
        $this->load->model('relation_street_model');
        $relation_block = $this->relation_street_model->upload_fang_block($sf);
      } else {
        $tmpInfo = mb_convert_encoding($tmpInfo, "UTF-8", "GBK");
        preg_match('/出错信息：(.*)xx/siU', $tmpInfo . 'xx', $errorlog);
        preg_match('/您发布的房源与现有房源重复(.*)xx/siU', $tmpInfo . 'xx', $errorlogx);
        preg_match('/Connection: keep-alive(.*)xx/siU', $tmpInfo . 'xx', $errorlogy);
        if (!empty($errorlog[1]) && strlen($errorlog[1]) > 8) {
          $paramlog['info'] = $errorlog[1];
        } elseif (!empty($errorlogx[1]) && strlen($errorlogx[1]) > 8) {
          $paramlog['info'] = '您发布的房源与现有房源重复';
        } elseif (!empty($errorlogy[1]) && strlen($errorlogy[1]) > 8) {
          $paramlog['info'] = trim($errorlogy[1]);
        } else {
          preg_match('/light.soufun.com([^-]*)xx/siU', $tmpInfo . 'xx', $errorlogv);
          $paramlog['info'] = empty($errorlogv[1]) ? '发布失败' : $errorlogv[1];
        }
        $paramlog['info'] = $paramlog['info'];
        $paramlog['type'] = 2; //1成功,2失败
        $log_id = $this->group_publish_model->add_publish_log($paramlog);
        $data = array('flag' => 9, 'info' => $paramlog['info'], 'after' => 1);
      }
      return $data;
    }
    return false;
  }

  //搜房 下架
  //public function esta_delete($house_id, $act, $nolog=0, $queue_id=0, $broker_id=0)
  public function esta_delete($house_id, $act, $param = array())
  {
    $this->load->model('group_publish_model');
    $site_id = $this->site['id'];
    $broker_id = empty($param['broker_id']) ? $this->broker_info['broker_id'] : $param['broker_id'];
    $esta_house_info = $this->group_publish_model->get_publish_by_site_id($site_id, $house_id, $act, $broker_id);
    $publish_id = $esta_house_info[0]['publish_id'];
    $type = ($act == 'sell') ? 'businesstype=cs' : 'businesstype=cz';
    $tbl = ($act == 'sell') ? 1 : 2;
    //$login = $this->isbind_site();
    $login = $this->site_mass_model->isbind_site_by_id($site_id, $broker_id);  //绑定帐号信息

    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();

    $paramlog = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
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
      $post_fields = array('id' => $publish_id, 'uid' => $login['user_id'], 'token' => $login['cookies']);
      $post_fields['sign'] = $this->makeSign($post_fields);
      var_dump($post_fields);
      $tmpInfo = $this->curl->static_vpost($this->api_url . 'itf/off.php', $post_fields, 0, 0, false);
      $json = json_decode($tmpInfo, true);
      if ('0' == $json['ret_code']) {
        $result['state'] = 'success';

        $paramlog['type'] = 1; //1成功,2失败
        $paramlog['info'] = '下架成功';
      }
      $bool = $this->group_publish_model->del_info_by_publish_id($publish_id, $act, $house_id);  //数据库 删除

      $where_del = array('broker_id' => $broker_id, 'house_id' => $house_id, 'tbl' => $tbl, 'site_id' => $site_id);
      $del = $this->group_publish_model->delete_refresh_time($where_del);  //预约刷新 删除
      $where_msg = array('broker_id' => $broker_id, 'house_id' => $house_id, 'tbl' => $tbl, 'days != ' => 0);
      $msg_refresh = $this->group_publish_model->checked_refresh_msg($where_msg); //预约刷新 修改查看状态
    }
    if (empty($param['nolog'])) {
      $this->group_publish_model->add_esta_log($paramlog); //重新发布 不加入下架日志
    }
    return $result;
  }

  //搜房 刷新
  public function refresh($house_id, $act)
  {
    $cityurl = $this->fang_url;
    $site_id = $this->fang['id'];
    $type = ($act == 'sell') ? 'businesstype=cs' : 'businesstype=cz';
    $house_info = $this->group_publish_model->get_publish_detail($site_id, $house_id, $act);
    $id = $house_info['id'];
    $publish_id = $house_info['publish_id'];
    $refresh_times = $house_info['refresh_times'] + 1;

    try {
      $login = $this->isbind_site();
      if ($login['cookie']) {
        $agent_id = $login['agent_id'];
        $post_url = 'http://' . $cityurl . '/Magent/House/HouseListAction.aspx?' . $type . '&page=1&pagesize=20&flag=1&agentid=' . $agent_id
          . '&mendianID=0&action=refhouse&houseid=' . $publish_id . '&pageurl=';
        $tmpInfo = $this->curl->vget($post_url, $login['cookie']);
        $tmpInfo = mb_convert_encoding($tmpInfo, 'UTF-8', 'GBK');
        $msg = strpos($tmpInfo, '刷新成功');
        if ($msg) {
          $param = array('refresh_times' => $refresh_times, 'updatetime' => time());
          $this->group_publish_model->update_data($id, $param, $act);
          $result = array('state' => 'success');

          //张建统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝
          $this->load->model('group_refresh_model');
          $this->group_refresh_model->info_count($this->broker_info, 6);
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
          preg_match("/alert\('(.*)'\)/siU", $tmpInfo, $error);
          $info = empty($error[1]) ? '刷新失败' : $error[1];
          $result = array('state' => 'failed', 'info' => $info);
        }
      }
      return $result;
    } catch (Exception $e) {
      return array('state' => 'failed', 'info' => '刷新超时!!!');
    }
  }

  //上传图片到 搜房
  public function upload_image($url, $broker_id, $type)
  {
    $cityurl = $this->fang_url;
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site('fang', $broker_id);
    //$login = $this->isbind_site();
    if ($login['cookies'] && !empty($finalname)) {
      $cookie = $login['cookies'];
      $agent_id = $login['agent_id'];
      $tmpInfo = $this->curl->vget('http://' . $cityurl . '/magent/house/sale/saleinput.aspx', $cookie);
      preg_match('/id="agentmainput_Hfile".*UploadPic.*,.*,.*,.*,.*,.*,\'(.*)\',(.*),/siU', $tmpInfo, $pro);
      if (empty($pro[1])) {
        $tmpInfo = $this->curl->vget('http://' . $cityurl . '/magent/house/lease/leaseinput.aspx', $cookie);
        preg_match('/id="Hfile".*UploadPic.*,.*,.*,.*,.*,.*,\'(.*)\',(.*),/siU', $tmpInfo, $pro);
      }

      $post_url = 'http://imgfku1.fang.com/upload/agents/houseinfo2?channel=agent.houseinfo&uid=' . $agent_id
        . '&city=nj&t=' . $pro[1] . '&kind=houseinfo&sid=' . $pro[2] . '&backurl=http%3a%2f%2f' . $cityurl
        . '%2fMagent%2fPicInterface%2fSingleImgUploadFinish.aspx%3fcallback%3dShowImg%26city%3dnj&type=3&drawtext=';
      $post_fields = array($type => '@' . $finalname);
      $refer = 'http://' . $cityurl . '/magent/house/sale/saleinput.aspx';
      $conInfo = $this->curl->vpost($post_url, $post_fields, $cookie, $refer);
      $conInfo = mb_convert_encoding($conInfo, "UTF-8", "GBK");
      preg_match('/<script.*u0027http(.*)\\\u0027,/siU', $conInfo, $pigname);
      @unlink($finalname);

      if (!empty($pigname[1])) {
        $picurl = 'http' . $pigname[1];
        return $picurl;
      }
    }
    return false;
  }

  //群发匹配目标站点楼盘名
  public function get_keyword($alias = '', $act = '')
  {
    $keyword = $this->input->get('keyword', TRUE);
    $sell_type = $this->input->get('sell_type', TRUE);  //民宅 别墅 商铺 写字楼
    $keyword = trim($keyword);
    switch ($sell_type) {
      case 2:
        $type = '%22%B1%F0%CA%FB%22';
        break;   //别墅
      case 3:
        $type = '%22%C9%CC%C6%CC%22';
        break;   //商铺
      case 4:
        $type = '%22%D0%B4%D7%D6%C2%A5%22';
        break;//写字楼
      case 5:
        $type = '%22%B3%A7%B7%BF%22';
        break;   //厂房
      default :
        $type = '%22%D7%A1%D5%AC%22';
        break; //住宅
    }

    $list = array();
    //$login = $this->check_login();  //$login = $this->isbind_site();  sh.agent.fang.com
    $login = $this->isbind_site();
    if (!empty($login['cookie'])) {
      $keyword = mb_convert_encoding($keyword, "GBK", "UTF-8");
      $url = 'http://' . $login['cityurl'] . '/MAgent/House/getDistrictList.aspx?key=' . urlencode($keyword) . '&type=' . $type;
      $tmpInfo = $this->curl->vget($url, $login['cookie']);
      $tmpInfo = mb_convert_encoding($tmpInfo, "UTF-8", "GBK");
      if ($tmpInfo) {
        $info = explode('~', $tmpInfo);
        foreach ($info as $key => $val) {
          if ($key == 10) break;
          $row = explode('|', $val);
          $list[] = array(
            'label' => $row[0],
            'address' => $row[1],
            'district' => $row[2],
            'street' => $row[3],
            'id' => $row[0]
          );
        }
      }
    }
    return $list;
  }

  //检查登录
  public function check_login()
  {
    $username = $this->input->get_post('username');
    $password = $this->input->get_post('password');
    $otherpwd = $this->input->get_post('otherpwd');
    if (empty($username)) {
      $where = array('broker_id' => $this->broker_info['broker_id'], 'site_id' => $this->site['id'], 'status' => 1);
      $site_broker = $this->site_model->get_brokerinfo_byids($where);
      if (empty($site_broker[0])) return false;
      $username = $site_broker[0]['username'];
      $otherpwd = $site_broker[0]['otherpwd'];
      $password = $site_broker[0]['password'];
    }

    $url = $this->api_url . 'itf/login.php';
    $post_fields = array('acc' => $username, 'pwd' => $password);
    $post_fields['sign'] = $this->makeSign($post_fields);

    $tmpInfo = $this->curl->static_vpost($url, $post_fields, 0, 0, false);
    $json = json_decode($tmpInfo, true);
    if ('0' == $json['ret_code']) {
      return array('userid' => $json['data']['id'], 'token' => $json['data']['token']);
    }
    return false;
  }


  //同控制器中 sending_soufang()
  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $broker_info = $this->broker_info;
    $broker_id = $this->broker_info['broker_id'];

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
      $block_info['address'] = $this->input->get('address', TRUE);
      $block_info['district'] = $this->input->get('district', TRUE);
      $block_info['street'] = $this->input->get('street', TRUE);
    } else {
      $this->load->model('relation_street_model');
      $relation_block = $this->relation_street_model->relation_block($house_info['block_id'], $broker_info['company_id']);
      if (!empty($relation_block['fang_block'])) {
        $block_info['block_name'] = $relation_block['fang_block'];
        $block_info['address'] = $relation_block['fang_address'];
        $block_info['district'] = $relation_block['fang_district'];
        $block_info['street'] = $relation_block['fang_street'];
      }
    }

    //提交前,表单验证
    if ($house_info['floor'] > 99 || $house_info['totalfloor'] > 99) {
      return array('flag' => 'proerror', 'info' => '楼层最多2位数');
    } elseif ($house_info['floor'] > $house_info['totalfloor']) {
      return array('flag' => 'proerror', 'info' => '所在楼层不能大于总楼层');
    } elseif (mb_strlen($house_info['title']) < 1 || mb_strlen($house_info['title']) > 30) {
      return array('flag' => 'proerror', 'info' => '房源标题1-30字');
    } else if (!in_array($house_info['sell_type'], array(1, 2, 3, 4))) {
      return array('flag' => 'proerror', 'info' => '搜房不支持厂房、仓库、车库');
    }
    //必须在表单验证后
    if (empty($block_info)) {
      $data['flag'] = 'block';  //2楼盘字典
    } else {
      //判断是否已经发布
      $publishinfo = $this->group_publish_model->get_num_sell_publish($broker_id, $site_id, $house_id);
      if ($publishinfo) {
        $extra = array('nolog' => 1, 'broker_id' => $broker_id);
        $del = $this->esta_delete($house_id, $act, $extra);
      }
      //加入定时任务
      $group = $this->group_queue_model->get_queue_one(array('id' => $queue_id));
      $group['info'] = serialize($block_info);
      $demon = $this->group_queue_model->add_queue_demon($group);

      if (!empty($demon)) {
        $data['flag'] = 'success';  //加入定时任务
      } else {
        $data['flag'] = 'error';  //0错误
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
      return parent::refresh($broker_id, $house_id, $act, '', $queue_id);
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
      $broker_id = $group[0]['broker_id'];
      $act = $group[0]['tbl'] == 1 ? 'sell' : 'rent';

      $extra = array('nolog' => 0, 'queue_id' => $queue_id, 'broker_id' => $broker_id);
      $data = $this->esta_delete($house_id, $act, $extra);
      return $data;
    }
    return false;
  }

  //发布数据组装
  public function publish_param($queue)
  {
    $this->load->library('CitySite');
    $this->load->model('broker_info_model');
    $city = $this->config->item('login_city');

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

    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);   //经纪人信息
    $mass_broker = $this->site_mass_model->isbind_site('ffw', $broker_id);  //目标站点 帐号信息
    $userid = $mass_broker['user_id'];
    $username = $mass_broker['username'];
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $config = $this->getconfig();

    $post_fields = array('id' => $mass_broker['user_id'], 'token' => $mass_broker['cookies']);

    $post_fields['f39'] = 'sell' == $act ? 2 : 1; //房源类型 (1出租房源 2出售房源)

    $post_fields['f1'] = $house_info['title']; //房源标题
    $post_fields['f3'] = $config['type'][$house_info['sell_type']]; //房型
    $post_fields['f4'] = $house_info['room']; //户型 室
    $post_fields['f5'] = $house_info['hall']; //户型 厅
    $post_fields['f6'] = $house_info['toilet']; //户型 卫
    $post_fields['f7'] = $house_info['balcony']; //户型 阳
    $post_fields['f9'] = $house_info['district_id']; //区域（例:鼓楼）
    $post_fields['f9_2'] = $house_info['street_id']; //版块（例:温泉公园）
    $post_fields['f10'] = $house_info['block_name']; //小区名
    $post_fields['f11'] = $house_info['address']; //具体地址
    $post_fields['f12'] = $house_info['floor']; //所在楼层
    $post_fields['f13'] = $house_info['totalfloor']; //总层数
    $post_fields['f14'] = $house_info['buildarea']; //面积
    $post_fields['f22'] = $config['decoration'][$house_info['fitment']]; //装修程度
    //$post_fields['f45'] = ''; //联系人
    //$post_fields['f47'] = ''; //联系人手机
    //$post_fields['f51'] = ''; //固定电话
    $post_fields['f20'] = $config['face'][$house_info['forward']]; //房屋朝向
    $post_fields['intro'] = $house_info['bewrite']; //描述
    $post_fields['f50'] = $house_info['pic']; //封面图片路径

    if ('sell' == $act) {
      $post_fields['f15'] = $house_info['buildyear']; //建造年代 (例:2015)
      $post_fields['f16'] = $house_info['price']; //出售总价（万元）
      $post_fields['f19'] = $house_info['usage_area']; //产权面积
      $post_fields['f65'] = $house_info['avgprice']; //出售单价
      //$post_fields['f73'] = ''; //首付（万）
    } else {
      $post_fields['f27'] = '0'; //租赁方式1 (0 不限 1整租 2合租)
      $post_fields['f28'] = '1'; //租赁方式2 (1 性别不限 2限男性 3限女性 )
      $post_fields['f33'] = $house_info['rent_price']; //出租价格（元）
    }
    //$post_fields['f26'] = ''; //房源有效期（天）
    foreach ($post_fields as $key => $value) {
      if (empty($value)) {
        unset($post_fields[$key]);
      }
    }

    $post_fields['sign'] = $this->makeSign($post_fields);


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

    $data_house['act'] = $act;
    $data_house['sell_type'] = $house_info['sell_type'];
    $data_house['post_url'] = $this->api_url . 'itf/house.php';
    $data_house['post_fielde'] = $post_fields;
    $data_house['house_block_id'] = $house_info['block_id'];
    $result = $this->publish($data_house, $broker_id, $house_id, $site_id, $queue['id']);
    if (isset($result['flag']) && $result['flag'] == 'success') {
      //记录 搜房楼盘
      $sf = array('block_id' => $data_house['house_block_id'],  //我们的block_id
        'agency_id' => $broker_info['agency_id'],
        'company_id' => $broker_info['company_id'],
        'fang_block' => $data_house['block_name'],
        'fang_district' => $data_house['district'],
        'fang_street' => $data_house['street'],
        'fang_address' => $data_house['address']
      );
      $this->load->model('relation_street_model');
      $relation_block = $this->relation_street_model->upload_fang_block($sf);
    }
    return $result;
  }


}

//$conInfo = str_replace('script', '', $conInfo);
//$conInfo = iconv("GBK", "UTF-8", $conInfo);
/* End of file site_fang_model.php */
/* Location: ./application/mls/models/site_fang_model.php */
