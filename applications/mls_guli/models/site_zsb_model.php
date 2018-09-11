<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Site_zsb_model extends MY_Model
{
  private $zsb;

  public function __construct()
  {
    parent::__construct();
    $this->load->library('Curl');
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
    $this->zsb = $this->site_model->get_site_byid(array('alias' => 'house365'));
    $this->zsb = $this->zsb[0];

    $where = array('signatory_id' => $this->signatory_info['signatory_id'], 'site_id' => $this->zsb['id'], 'status' => 1);
    $site_signatory = $this->site_model->get_signatoryinfo_byids($where);
    $this->zsb['username'] = empty($site_signatory[0]) ? '' : $site_signatory[0]['username'];
    $this->zsb['password'] = empty($site_signatory[0]) ? '' : $site_signatory[0]['password'];
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->zsb['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $username = $this->input->get('username', true);
    $password = $this->input->get('password', true);

    $login = $this->check_login($username, $password);
    if ($login['status'] == 'success') {
      $data = array();
      $data['signatory_id'] = $signatory_id;
      $data['site_id'] = $site_id;
      $data['status'] = '1';
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['uid'];
      $data['cookies'] = '';
      $data['createtime'] = time();
      $data['otherpwd'] = '';

      $where = array('signatory_id' => $signatory_id, 'site_id' => $site_id);
      $find = $this->get_data(array('form_name' => 'mass_site_signatory', 'where' => $where), 'dbback_city');
      if (count($find) >= 1) {
        $result = $this->modify_data($where, $data, 'db_city', 'mass_site_signatory');
      } else {
        $result = $this->add_data($data, 'db_city', 'mass_site_signatory');
      }
      return $data;
    } else {
      return '123';
    }
  }

  //列表采集
  public function collect_list($act)
  {
    $num = 0;
    $type = array('1' => '住宅', '2' => '别墅', '3' => '写字楼', '4' => '商铺', '5' => '仓库厂房', '6' => '车库车位');
    $username = $this->zsb['username'];
    $password = $this->zsb['password'];
    $site_id = $this->zsb['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $city = $this->signatory_info['city_spell'];

    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"' . $act . '","username":"' . $username
      . '","password":"' . $password . '","method":"getlist"}';
    $post_url = "http://api.esf.house365.com/importzsb/";
    $tmpInfo = $this->curl->vpost_by_json($post_url, $data_string);
    $result = json_decode($tmpInfo, true);
    if ($result['state'] == 'success') {
      foreach ($result['data'] as $val) {
        $data = array();
        $data['url'] = $val['id'];
        $data['infourl'] = '';
        $data['title'] = $val['address'] . ' [' . $type[$val['infotype']] . ']';
        $data['des'] = $val['blockshowname'] . " " . $val['room'] . "室" . $val['hall'] . "厅" . " " . $val['floor'] . "/" . $val['totalfloor'] . "层" . " " . intval($val['buildarea']) . "㎡" . " " . $val['price'] . "万";
        $data['releasetime'] = $val['creattime'];
        $data['source'] = 2;
        $data['city_spell'] = $val['id'];
        $data['signatory_id'] = $signatory_id;
        if ($act == 'sell') {
          $res = $this->autocollect_model->add_collect_sell($data, $database = 'db_city');
        } else {
          $res = $this->autocollect_model->add_collect_rent($data, $database = 'db_city');
        }
        if ($res) $num++;
      }
    }
    return $num;
  }

  //详情页面导入:出售
  public function collect_sell_info($id)
  {
    $city = $this->signatory_info['city_spell'];
    $username = $this->zsb['username'];
    $password = $this->zsb['password'];
    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell","id":"' . $id . '","username":"' . $username
      . '","password":"' . $password . '","method":"getdetail"}';
    $post_url = "http://api.esf.house365.com/importzsb/";
    $tmpInfo = $this->curl->vpost_by_json($post_url, $data_string);
    $result = json_decode($tmpInfo, true);
    $house_info = array();
    if ($result['state'] == 'success') {
      $type = array('1' => 1, '2' => 2, '4' => 3, '3' => 4, '5' => 5, '6' => 7);
      $house_info['sell_type'] = $type[$result['data']['infotype']];//出售类型
      $house_info['house_name'] = $result['data']['blockshowname'];//小区名称
      $house_info['room'] = $result['data']['room'];//室
      $house_info['hall'] = $result['data']['hall'];//厅
      $house_info['toilet'] = $result['data']['toilet'];//卫
      $house_info['kitchen'] = $result['data']['kitchen'];//厨
      $house_info['balcony'] = $result['data']['balcony'];//阳台
      $toward = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
      $house_info['forward'] = $toward[$result['data']['forward']];//朝向
      $house_info['floor'] = $result['data']['floor'];//楼层
      $house_info['totalfloor'] = $result['data']['totalfloor'];//总楼层
      $decoration = array('毛坯' => 1, '简装' => 2, '精装' => 4, '豪华装' => 5);
      $house_info['serverco'] = $decoration[$result['data']['fitment']];//装修
      $house_info['buildarea'] = $result['data']['buildarea'];//面积
      $house_info['price'] = $result['data']['price'];//价格
      $house_info['title'] = $result['data']['address'];//标题
      $house_info['buildyear'] = $result['data']['buildyear'];//建造年代
      $house_info['avgprice'] = $result['data']['averprice'];//均价
      $house_info['content'] = $result['data']['remark'];//房源描述
      foreach ($result['data']['photo'] as $key => $val) {
        $piclist[$key] = $val['filename'];
      }
      if (!empty($piclist)) {
        $house_info['picurl'] = implode('*', $piclist);//图片
      }
      $house_info['owner'] = '';  //业主姓名
      $house_info['telno1'] = ''; //业主电话
    }
    return $house_info;
  }

  //详情页面导入:租房
  public function collect_rent_info()
  {
    $city = $this->signatory_info['city_spell'];
    $username = $this->zsb['username'];
    $password = $this->zsb['password'];
    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent","id":"' . $id . '","username":"' . $username
      . '","password":"' . $password . '","method":"getdetail"}';
    $post_url = "http://api.esf.house365.com/importzsb/";
    $tmpInfo = $this->curl->vpost_by_json($post_url, $data_string);
    $result = json_decode($tmpInfo, true);
    $house_info = array();
    if ($result['state'] == 'success') {
      $type = array('1' => 1, '2' => 2, '4' => 3, '3' => 4, '5' => 5, '6' => 7);
      $house_info['sell_type'] = $type[$result['data']['infotype']];//出租类型
      $house_info['house_name'] = $result['data']['blockshowname'];//楼盘名称
      $house_info['room'] = $result['data']['room'];//室
      $house_info['hall'] = $result['data']['hall'];//厅
      $house_info['toilet'] = $result['data']['toilet'];//卫
      $house_info['kitchen'] = $result['data']['kitchen'];//厨
      $house_info['balcony'] = $result['data']['balcony'];//阳台
      $toward = array('东' => 1, '东南' => 2, '南' => 3, '西南' => 4, '西' => 5, '西北' => 6, '北' => 7, '东北' => 8, '东西' => 9, '南北' => 10);
      $house_info['forward'] = $toward[$result['data']['forward']];//朝向
      $house_info['floor'] = $result['data']['floor'];//楼层
      $house_info['totalfloor'] = $result['data']['totalfloor'];//总楼层
      $decoration = array('毛坯' => 1, '简装' => 2, '精装' => 4, '豪华装' => 5);
      $house_info['serverco'] = $decoration[$result['data']['fitment']];//装修
      $house_info['buildarea'] = $result['data']['buildarea'];//面积
      $house_info['content'] = $result['data']['remark'];//房源描述
      if ($result['data']['priceunit'] == 1) {//元/月
        $house_info['price'] = $result['data']['price'];
      } elseif ($result['data']['priceunit'] == 2) {//元/天*平方米
        $house_info['price'] = $result['data']['price'] * $house_info['buildarea'] * 30;
      } elseif ($result['data']['priceunit'] == 3) {//元/月*平方米
        $house_info['price'] = $result['data']['price'] * $house_info['buildarea'];
      } else {
        $house_info['price'] = $result['data']['price'];
      }//价格
      $house_info['title'] = $result['data']['address'];//标题
      $house_info['buildyear'] = $result['data']['buildyear'];//建造年代
      foreach ($result['data']['photo'] as $key => $val) {
        $piclist[$key] = $val['filename'];
      }
      if (!empty($piclist)) {
        $house_info['picurl'] = implode('*', $piclist);//图片
      }
      $house_info['owner'] = '';
      $house_info['telno1'] = '';
    }
    return $house_info;
  }

  //租售宝 下架
  public function esta_delete($house_id, $act)
  {

  }

  //检查登录
  public function check_login($username = '', $password = '')
  {
    $site_id = $this->zsb['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $city = $this->signatory_info['city_spell'];
    if (empty($username)) {
      $username = $this->zsb['username'];
      $password = $this->zsb['password'];
    }
    $post_url = "http://api.esf.house365.com/importzsb/";
    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell","username":"' . $username
      . '","password":"' . $password . '","method":"login"}';
    $tmpInfo = $this->curl->vpost_by_json($post_url, $data_string);
    $info = json_decode($tmpInfo, true);
    if ($info['state'] == 'success') {
      return array('status' => 'success', 'uid' => $info['uid']);
    } else {
      return array('status' => 'error', 'reason' => $info['reason']);
    }
  }

  //获取发布所需房屋配置信息
  public function get_house_rent($house_id, $act, $publishid = 0)
  {
    $this->load->model('site_model');
    $this->load->model('house_config_model');
    $this->load->model('rent_house_model');

    $this->rent_house_model->set_id($house_id);
    $house_info = $this->rent_house_model->get_info_by_id();
    $site_info = $this->site_model->get_site_byid(array('alias' => 'house365'));
    $signatory_info = $this->signatory_info;
    $site_id = $this->zsb['id'];
    $city = $signatory_info['city_spell'];
    $signatory_id = $signatory_info['signatory_id'];
    $username = $this->zsb['username'];
    $password = $this->zsb['password'];

    //获取出售信息基本配置资料
    $config = $this->house_config_model->get_config();
    //单位
    if ($house_info['price_danwei'] == 1) {
      $house_info['price_wei'] = '2'; //元*天
    } else {
      $house_info['price_wei'] = '1'; //元*月
    }
    $house_info['renttype'] = '整租';

    //期限
    if (!$house_info['renttime']) {
      $house_info['renttime'] = 1;
    }
    //付款方式
    if (!$house_info['rentpaytype']) {
      $house_info['rentpaytype'] = 1;
    }
    $data_string = '';
    //装修
    $fang_fitment = array(1 => '毛坯', 2 => '简装', 3 => '简装', 4 => '精装', 5 => '豪装', 6 => '豪装',);
    $zsb_equipment = array(19 => '床', 5 => '冰箱', 10 => '电视', 3 => '空调', 4 => '洗衣机', 6 => '热水器', 7 => '家具', 17 => '宽带', 22 => '可做饭', 23 => '独立卫生间');
    $tmp_equipment = array();
    $str_equipment = '';
    if (!empty($house_info['equipment'])) {
      $equipment = explode(',', $house_info['equipment']);
      foreach ($equipment as $val) {
        if (isset($zsb_equipment[$val])) {
          $str_equipment .= '"' . $zsb_equipment[$val] . '",';
        }
      }
    }
    $str_equipment = trim($str_equipment, ',');

    //图片
    $shinei_pic = '';
    $huxing_pic = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $this->load->model('pic_model');
      $pic_info = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      $shinei = array();
      $huxing = array();
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          if ($val['type'] == 1) {
            $shinei[] = $picurl;
          } else if ($val['type'] == 2) {
            $huxing[] = $picurl;
          }
        }
        foreach ($shinei as $key => $val) {
          $shinei_pic .= '{"title" : "室内图", "sort" : 2, "url" : "' . $val . '"}' . ',';
        }
        for ($i = 0; $i < count($huxing); $i++) {
          if ($i < count($huxing) - 1) {
            $huxing_pic .= '{"title" : "户型图", "sort" : 0, "url" : "' . $huxing[$i] . '"}' . ',';
          } else {
            $huxing_pic .= '{"title" : "户型图", "sort" : 0, "url" : "' . $huxing[$i] . '"}';
          }
        }
      }
    }
    if (intval($house_info['block_id'])) {
      //取当前小区的第一张外景图片
      $this->load->model('community_model');
      $waijing_arr = $this->community_model->get_one_cmt_image_by_cmtid('cmt_id = ' . $house_info['block_id'] . ' and pic_type = 3');
      if (is_full_array($waijing_arr)) {
        $waijing_pic = '{"title" : "外景图", "sort" : 1, "url" : "' . $waijing_arr[0]['image'] . '"}';
      }
    } else {
      $waijing_pic = '';
    }
    $img_dress = $shinei_pic . $huxing_pic . $waijing_pic;
    $img_dress = trim($img_dress, ',');

    if ($publishid) {
      $data_id = '"id":"' . $publishid . '",';
      $method = 'modify';
    } else {
      $data_id = '';
      $method = 'add';
    }

    $zsb_house['Title'] = $house_info['title'];
    $zsb_house['Content'] = html_entity_decode($house_info['bewrite']);
    //标题+描述（临时）
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($signatory_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($signatory_id != $house_info['signatory_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->rent_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], $database = 'db_city');
        if (!empty($result)) {
          $zsb_house['Title'] = $result[0]['title'];
          $zsb_house['Content'] = html_entity_decode($result[0]['content']);
        }
      }
    }
    //$zsb_house['Content'] = strip_tags($zsb_house['Content']);
    //$zsb_house['Content'] = $this->autocollect_model->con_flow($zsb_house['Content']);//描述富文本处理
    $zsb_house['Content'] = $this->autocollect_model->special_flow($zsb_house['Content']);  //描述富文本处理
    $zsb_house['Content'] = html_entity_decode($zsb_house['Content']);

    if ($house_info['sell_type'] == 1) { //住宅
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":1,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceunit":' . $house_info['price_wei'] . ',"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . ',"toilet":' . $house_info['toilet'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":"' . $house_info['pic'] . '","payment":"' . $config['rentpaytype'][$house_info['rentpaytype']] . '","renttype":"' . $house_info['renttype'] . '","rent": "","equipment":[' . $str_equipment . ']}}';
    }
    if ($house_info['sell_type'] == 2) { //别墅
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":2,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceunit":' . $house_info['price_wei'] . ',"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . ',"toilet":' . $house_info['toilet'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":"' . $house_info['pic'] . '","payment":"' . $config['rentpaytype'][$house_info['rentpaytype']] . '","renttype":"' . $house_info['renttype'] . '","rent": ""}}';
    }
    if ($house_info['sell_type'] == 4) { //写字楼
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":3,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceunit":' . $house_info['price_wei'] . ',"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . ',"toilet":' . $house_info['toilet'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":"' . $house_info['pic'] . '","payment":"' . $config['rentpaytype'][$house_info['rentpaytype']] . '","renttype":"' . $house_info['renttype'] . '","tradeclass":"' . $config['office_trade'][$house_info['office_trade']] . '","strata_fee":' . $house_info['strata_fee'] . ',"rent": ""}}';
    }
    if ($house_info['sell_type'] == 3) { //商铺
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":4,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceunit":' . $house_info['price_wei'] . ',"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . ',"toilet":' . $house_info['toilet'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":"' . $house_info['pic'] . '","payment":"' . $config['rentpaytype'][$house_info['rentpaytype']] . '","renttype":"' . $house_info['renttype'] . '","tradeclass":"' . $config['shop_type'][$house_info['shop_type']] . '","rent": ""}}';
    }
    if ($house_info['sell_type'] == 7 || $house_info['sell_type'] == 5 || $house_info['sell_type'] == 6) { //厂房 仓库 车库
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"rent",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":5,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceunit":' . $house_info['price_wei'] . ',"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . ',"toilet":' . $house_info['toilet'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":"' . $house_info['pic'] . '","payment":"' . $config['rentpaytype'][$house_info['rentpaytype']] . '","renttype":"' . $house_info['renttype'] . '","rent": ""}}';
    }

    $url = "http://api.esf.house365.com/importzsb/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);
    $result = json_decode($result, true);

    $this->load->library('log/Log');
    Log::record('同步帐号' . $url . '___' . serialize($result), $data_string, 'qf');

    if (!empty($result['state']) && $result['state'] == 'success') {
      $data = array('flag' => 'success', 'id' => $result['id']);
    } else {
      $info = empty($result['msg']) ? '发布失败' : $result['msg'];
      $data = array('flag' => 'error', 'info' => $info, 'after' => 1);
    }
    return $data;
  }

  //获取发布所需房屋配置信息
  public function get_house_sell($house_id, $act, $publishid = 0)
  {
    $this->load->model('site_model');
    $this->load->model('sell_house_model');

    $this->sell_house_model->set_id($house_id);
    $house_info = $this->sell_house_model->get_info_by_id();
    $signatory_info = $this->signatory_info;
    $site_info = $this->site_model->get_site_byid(array('alias' => 'house365'));

    $site_id = $this->zsb['id'];
    $city = $signatory_info['city_spell'];
    $signatory_id = $signatory_info['signatory_id'];
    $username = $this->zsb['username'];
    $password = $this->zsb['password'];

    //获取出售信息基本配置资料
    $this->load->model('house_config_model');
    $config = $this->house_config_model->get_config();
    $data_string = '';
    //装修
    $fang_fitment = array(1 => '毛坯', 2 => '简装', 3 => '简装', 4 => '精装', 5 => '精装', 6 => '精装');
    //图片
    $shinei_pic = $huxing_pic = '';
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $this->load->model('pic_model');
      $pic_info = $this->pic_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      $shinei = $huxing = array();
      if ($pic_info) {
        foreach ($pic_info as $key => $val) {
          $picurl = changepic_send($val['url']);
          if ($val['type'] == 1) {
            $shinei[] = $picurl;
          } else if ($val['type'] == 2) {
            $huxing[] = $picurl;
          }
        }
        foreach ($shinei as $key => $val) {
          $shinei_pic .= '{"title" : "室内图", "sort" : 2, "url" : "' . $val . '"}' . ',';
        }
        for ($i = 0; $i < count($huxing); $i++) {
          $huxing_pic .= '{"title" : "户型图", "sort" : 0, "url" : "' . $huxing[$i] . '"}' . ',';
        }
      }
    }
    if (intval($house_info['block_id'])) {
      //取当前小区的第一张外景图片
      $this->load->model('community_model');
      $waijing_arr = $this->community_model->get_one_cmt_image_by_cmtid('cmt_id = ' . $house_info['block_id'] . ' and pic_type = 3');
      if (is_full_array($waijing_arr)) {
        $waijing_pic = '{"title" : "外景图", "sort" : 1, "url" : "' . $waijing_arr[0]['image'] . '"}';
      }
    } else {
      $waijing_pic = '';
    }
    $img_dress = $shinei_pic . $huxing_pic . $waijing_pic;
    $img_dress = trim($img_dress, ',');

    if ($publishid) {
      $data_id = '"id":"' . $publishid . '",';
      $method = 'modify';
    } else {
      $data_id = '';
      $method = 'add';
    }

    $zsb_house['Title'] = $house_info['title'];
    $zsb_house['Content'] = $house_info['bewrite'];
    //标题+描述（临时）
    //获得当前经纪人的角色等级，判断店长以上or店长以下
    $role_level = intval($signatory_info['role_level']);
    //店长以下的经纪人不允许操作他人的私盘
    if (is_int($role_level) && $role_level > 6) {
      if ($signatory_id != $house_info['signatory_id'] && $house_info['nature'] == '1') {
        //获取当前经纪人的临时详情
        $result = $this->sell_house_model->get_temporaryinfo($house_info['id'], $house_info['signatory_id'], $database = 'db_city');
        if (!empty($result)) {
          $zsb_house['Title'] = $result[0]['title'];
          $zsb_house['Content'] = $result[0]['content'];
        }
      }
    }
    //$zsb_house['Content'] = strip_tags($zsb_house['Content']);
    //$zsb_house['Content'] = $this->autocollect_model->con_flow($zsb_house['Content']);//描述富文本处理
    $zsb_house['Content'] = $this->autocollect_model->special_flow($zsb_house['Content']);  //描述富文本处理
    $zsb_house['Content'] = html_entity_decode($zsb_house['Content']);

    if ($house_info['sell_type'] == 1) {
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":1,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceterm":1,"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"toilet":' . $house_info['toilet'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":0,"tradeclass": ""}}';
    }
    if ($house_info['sell_type'] == 2) {
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":2,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceterm":1,"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"toilet":' . $house_info['toilet'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":0,"housekind":"' . $config['villa_type'][$house_info['villa_type']] . '","tradeclass": ""}}';
    }
    if ($house_info['sell_type'] == 4) {
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":3,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceterm":1,"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"toilet":' . $house_info['toilet'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":0,"tradeclass":"' . $config['office_trade'][$house_info['office_trade']] . '","strata_fee":' . $house_info['strata_fee'] . ',"add": ""}}';
    }

    if ($house_info['sell_type'] == 3) {
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":4,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceterm":1,"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"toilet":' . $house_info['toilet'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":0, "tradeclass":"' . $config['shop_type'][$house_info['shop_type']] . '","strata_fee":' . $house_info['strata_fee'] . ',"add":""}}';
    }

    if ($house_info['sell_type'] == 7 || $house_info['sell_type'] == 5 || $house_info['sell_type'] == 6) {
      $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"sell",' . $data_id . '"username":"' . $username . '","password":"' . $password . '","method":"' . $method . '","house":{"ownid":"fkt","infotype":5,"blockname":"' . $house_info['block_name'] . '","address":"' . $zsb_house['Title'] . '","price":{"money":' . $house_info['price'] . ',"priceterm":1,"pricetype":1},"buildarea":' . $house_info['buildarea'] . ',"home":{"room":' . $house_info['room'] . ',"hall":' . $house_info['hall'] . ',"toilet":' . $house_info['toilet'] . ',"kitchen":' . $house_info['kitchen'] . ',"balcony":' . $house_info['balcony'] . '},"floor":{"floor":' . $house_info['floor'] . ',"totalfloor":' . $house_info['totalfloor'] . '},"fitment":"' . $fang_fitment[$house_info['fitment']] . '","mright":"产权房","forward":"' . $config['forward'][$house_info['forward']] . '","buildyear":' . $house_info['buildyear'] . ',"baseservice":"","remark":"' . $zsb_house['Content'] . '","pics":[' . $img_dress . '],"picIndex":0, "add":""}}';
    }
    $url = "http://api.esf.house365.com/importzsb/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    $result = json_decode($result, true);
    if (!empty($result['state']) && $result['state'] == 'success') {
      $data = array('flag' => 'success', 'id' => $result['id']);
    } else {
      $info = empty($result['msg']) ? '发布失败' : $result['msg'];
      $data = array('flag' => 'error', 'info' => $info, 'after' => 1);
    }
    return $data;
  }

  //队列发布
  public function queue_publish($alias)
  {
    $this->load->model('group_publish_model');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $signatory_info = $this->signatory_info;
    $signatory_id = $signatory_info['signatory_id'];
    $city = $signatory_info['city_spell'];
    $act = $this->input->get('act');
    if ($act == 'sell') {
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id();
    $paramlog = array(
      'house_id' => $house_id,
      'signatory_id' => $signatory_id,
      'site_id' => $site_id,
      'block_id' => $house_info['block_id'],
      'sell_type' => ($act == 'sell') ? 1 : 2, //1出售,2出租
      'ymd' => time(),
      'username' => $this->zsb['username']
    );

    if ($house_id) {
      //判断是否已经发布
      $publishinfo = $this->group_publish_model->get_num_sell_publish($signatory_id, $site_id, $house_id);
      if (empty($publishinfo)) {
        $publish_data = ($act == 'sell') ? $this->get_house_sell($house_id, $act) : $this->get_house_rent($house_id, $act);
      } else {
        $zsb_id = $publishinfo[0]['publish_id'];
        $get_pid = $publishinfo[0]['id'];
        //此处是修改房源 $data = array('flag'=>'success','id'=>$result['id']);
        $publish_data = ($act == 'sell') ? $this->get_house_sell($house_id, $act, $zsb_id) : $this->get_house_rent($house_id, $act, $zsb_id);
        if ($publish_data['flag'] == 'success') {
          $data['flag'] = 'success';
        } else {
          //可能房源被删，需要新发房源
          $publish_data = ($act == 'sell') ? $this->get_house_sell($house_id, $act) : $this->get_house_rent($house_id, $act);
          $data['flag'] = 'error';
          if ($publish_data['flag'] == 'success') {
            if ($act == 'sell') {
              $publish_url = "http://" . $city . ".sell.house365.com/s_" . $publish_data['id'] . ".html";
            } else {
              $publish_url = "http://" . $city . ".rent.house365.com/r_" . $publish_data['id'] . ".html";
            }
            $data_info = array(
              'publish_id' => $publish_data['id'],
              'publish_url' => $publish_url,
              'updatetime' => time()
            );
            if ($act == 'sell') {
              $return_id = $this->group_publish_model->update_sell_data($get_pid, $data_info);
            } else {
              $return_id = $this->group_publish_model->update_rent_data($get_pid, $data_info);
            }

            if ($return_id > 0) {
              $data['flag'] = 'success';
            }
          }
        }

        if ($data['flag'] == 'success') {
          $paramlog['type'] = 1; //1成功,2失败
          $paramlog['info'] = '发布成功' . $return_id;
          $log_id = $this->group_publish_model->add_publish_log($paramlog);

          //孙老师统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝   tbl:1出售 2出租
          $this->load->model('publish_count_num_model');
          $tbl = ($act == 'sell') ? 1 : 2;
          $this->publish_count_num_model->info_count($signatory_info, 7, array('tbl' => $tbl, 'house_id' => $house_id));
        } else {
          $paramlog['type'] = 2; //1成功,2失败
          $paramlog['info'] = '发布失败';
          $log_id = $this->group_publish_model->add_publish_log($paramlog);
        }
        return $data;
      }
    }

    //发布成功 失败 对数据库操作
    if ($publish_data['flag'] == 'success') {
      if ($act == 'sell') {
        $publish_url = "http://" . $city . ".sell.house365.com/s_" . $publish_data['id'] . ".html";
      } else {
        $publish_url = "http://" . $city . ".rent.house365.com/r_" . $publish_data['id'] . ".html";
      }
      $data_info = array(
        'signatory_id' => $signatory_id,
        'house_id' => $house_id,
        'site_id' => $site_id,
        'publish_id' => $publish_data['id'],
        'publish_url' => $publish_url,
        'createtime' => time(),
        'updatetime' => time()
      );
      if ($act == 'sell') {
        $return_id = $this->group_publish_model->add_sell_info($data_info);
      } else {
        $return_id = $this->group_publish_model->add_rent_info($data_info);
      }

      $paramlog['type'] = 1; //1成功,2失败
      $paramlog['info'] = '发布成功';
      $log_id = $this->group_publish_model->add_publish_log($paramlog);

      //孙老师统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝   tbl:1出售 2出租
      $this->load->model('publish_count_num_model');
      $tbl = ($act == 'sell') ? 1 : 2;
      $this->publish_count_num_model->info_count($signatory_info, 7, array('tbl' => $tbl, 'house_id' => $house_id));

      $data = array('flag' => 'success');
    } else {
      $paramlog['type'] = 2; //1成功,2失败
      $paramlog['info'] = empty($publish_data['info']) ? '发布失败' : $publish_data['info'];
      $log_id = $this->group_publish_model->add_publish_log($paramlog);

      $data = array('flag' => 'error', 'info' => $paramlog['info']);
    }
    return $data;
  }

  //同控制器中
  public function queue_refresh($alias = '')
  {
    $house_id = $this->input->get('house_id');
    $site_id = $this->input->get('site_id');
    $act = $this->input->get('act');

    $this->load->model('site_model');
    $this->load->model('group_publish_model');
    $this->load->model('site_mass_model');
    if ($act == 'sell') {
      $house_info = $this->group_publish_model->get_sell_publish_by_site_id($site_id, $house_id);
    } else {
      $house_info = $this->group_publish_model->get_rent_publish_by_site_id($site_id, $house_id);
    }
    $zsb_id = $house_info[0]['publish_id'];
    $signatory_info = $this->signatory_info;
    $city = $signatory_info['city_spell'];
    $signatory_id = $signatory_info['signatory_id'];

    $username = $this->zsb['username'];
    $password = $this->zsb['password'];
    $typename = 'refresh';
    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"' . $act . '","id":"' . $zsb_id . '","username":"' . $username . '","password":"' . $password . '","method":"' . $typename . '"}';
    $url = "http://api.esf.house365.com/importzsb/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);
    $result = json_decode($result, true);

    $this->load->model('group_refresh_model');
    $log_param = array('signatory_id' => $signatory_id,
      'house_id' => $house_id,
      'site_id' => $site_id,
      'tbl' => ($act == 'sell') ? 1 : 2,  //1出售 2出租
      'ymd' => date('Y-m-d'),
      'his' => '',
      'days' => 0,  //刷新方案 0手动
      'refreshtime' => time(),
      'createtime' => time(),
      'username' => $username
    );

    //刷新成功 更改时间
    if (isset($result['state']) && $result['state'] == 'success') {
      $data = array('state' => 'success');

      //张建统计 1.58p 2.58w 3.赶集 4.赶集vip 5.安居客 6.房天下 7.租售宝
      $this->group_refresh_model->info_count($this->signatory_info, 7);
      //王欣统计 成功
      $log_param['status'] = 1;
      $log_param['msg'] = '';
      $this->group_refresh_model->add_message_log($log_param);
    } else {
      $data = array('state' => 'failed', 'info' => '刷新失败');

      //王欣统计 失败
      $log_param['status'] = 2;
      $log_param['msg'] = '刷新失败';
      $this->group_refresh_model->add_message_log($log_param);
    }
    return $data;
  }

  public function queue_esta($alias = '')
  {
    $house_id = $this->input->get('house_id');
    $site_id = $this->input->get('site_id');
    $act = $this->input->get('act');

    $this->load->model('site_model');
    $this->load->model('group_publish_model');

    if ($act == 'sell') {
      $house_info = $this->group_publish_model->get_sell_publish_by_site_id($site_id, $house_id);
    } else {
      $house_info = $this->group_publish_model->get_rent_publish_by_site_id($site_id, $house_id);
    }
    $zsb_id = $house_info[0]['publish_id'];
    $signatory_info = $this->signatory_info;
    $city = $signatory_info['city_spell'];
    $signatory_id = $signatory_info['signatory_id'];

    $username = $this->zsb['username'];
    $password = $this->zsb['password'];
    $typename = 'esta';
    $data_string = '{"importkey":"7d75bee8e23c966d32f7c74c2235fab3","city":"' . $city . '","type":"' . $act . '","id":"' . $zsb_id . '","username":"' . $username . '","password":"' . $password . '","method":"' . $typename . '"}';
    $url = "http://api.esf.house365.com/importzsb/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);
    $result = json_decode($result, true);

    ($act == 'sell') ? $this->load->model('sell_house_model', 'my_house_model') : $this->load->model('rent_house_model', 'my_house_model');
    $this->my_house_model->set_id($house_id);
    $house_data = $this->my_house_model->get_info_by_id();
    $paramlog = array(
      'house_id' => $house_id,
      'signatory_id' => $signatory_id,
      'site_id' => $site_id,
      'block_id' => $house_data['block_id'],
      'sell_type' => ($act == 'sell') ? 1 : 2, //1出售,2出租
      'ymd' => time(),
      'username' => $username
    );
    if (isset($result['state']) && $result['state'] == 'success') {
      $data = array('state' => 'success');

      $paramlog['type'] = 1; //1成功,2失败
      $paramlog['info'] = '下架成功';
      $this->group_publish_model->add_esta_log($paramlog);
    } else {
      $data = array('state' => 'failed');

      $paramlog['type'] = 2; //1成功,2失败
      $paramlog['info'] = '下架失败';
      $this->group_publish_model->add_esta_log($paramlog);
    }
    $bool = $this->group_publish_model->del_info_by_publish_id($zsb_id, $act, $house_id);  //数据库 删除
    return $data;
  }


}

/* End of file site_zsb_model.php */
/* Location: ./application/mls_guli/models/site_zsb_model.php */
