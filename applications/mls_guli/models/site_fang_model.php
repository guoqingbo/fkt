<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_fang_base_model");

class Site_fang_model extends Site_fang_base_model
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('site_model');
    $this->load->model('autocollect_model');
  }

  //绑定帐号
  public function save_bind()
  {
    $site_id = $this->website['id'];
    $signatory_id = $this->signatory_info['signatory_id'];
    $extra['username'] = $username = $this->input->get('username');
    $extra['password'] = $password = $this->input->get('password');
    $extra['otherpwd'] = $otherpwd = $this->input->get('otherpwd');
    $extra['code'] = $this->input->get('checkcode');

    $login = $this->login($extra);
    if ($login['cookies']) {
      $data = array();
      $data['signatory_id'] = $signatory_id;
      $data['site_id'] = $site_id;
      $data['status'] = '1';
      $data['username'] = $username;
      $data['password'] = $password;
      $data['user_id'] = $login['user_id'];
      $data['agent_id'] = $login['agent_id'];
      $data['otherpwd'] = $otherpwd;
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
      return $data;
    } else {
      return $login;
    }
  }

  //列表采集
  public function collect_list($act)
  {
    $signatory_id = $this->signatory_info['signatory_id'];
    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      $num = 0;
      $project = array();
      $type = ($act == 'sell') ? 'cs' : 'cz';
      for ($i = 1; ; $i++) {
        $listurl = 'http://' . $login['cityurl'] . '/magent/house/houselist.aspx?flag=1&businesstype=' . $type . '&pagesize=20&page=' . $i;
        $tmpInfo = $this->curl->vget($listurl, $cookie);
        preg_match_all('/<tr onhover="ChangeColor">(.*)id="agentmahouse_input_form_list_viewrelash"/siU', $tmpInfo, $prj);
        if (count($prj[1]) > 0) {
          $project = array_merge($project, $prj[1]);
        } else {
          break;
        }
      }
      foreach ($project as $value) {
        $val = iconv("GBK", "UTF-8", $value);
        preg_match('/<td class="wid120">.*<a.*href="(.*)">/siU', $val, $url);
        preg_match('/<a href="(.*)".*id="agentmahouse_input_form_houselink">/siU', $val, $infourl);
        preg_match('/<span class="bold ft16 bluecolor">(.*)<\/span>/siU', $val, $tp);
        preg_match("/<span class='ml10'>(.*)<\/span>/siU", $val, $ty);
        preg_match('/<p class="mt5">(.*)<\/p>/siU', $val, $des);
        preg_match('/<span class="gray9 ml10">最后更新(.*)<\/span>/siU', $val, $releasetime);

        $data = array();
        $data['source'] = 0;
        $data['url'] = $url[1];  //编辑链接
        $data['infourl'] = $infourl[1];  //详情链接
        $data['title'] = trim($tp[1]) . ' ' . trim($ty[1]);//标题
        $data['des'] = $this->autocollect_model->con_flow(strip_tags($des[1]));
        $data['releasetime'] = trim($releasetime[1]) ? strtotime($releasetime[1]) : ''; //发布时间
        $data['city_spell'] = $this->signatory_info['city_spell'];
        $data['signatory_id'] = $this->signatory_info['signatory_id'];
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
      echo 'no cookie';
    }
  }

  //详情页面导入:二手房
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
      $type = array('修改住宅出售房源' => 1, '修改别墅出售房源' => 2, '修改商铺出售房源' => 3, '修改写字楼出售房源' => 4, '修改厂房出售房源' => 5);
      $tmpInfo = $this->curl->vget($this->geturl() . $url, $cookie);
      $tmpInfo = iconv("GBK", "UTF-8", $tmpInfo);
      preg_match('/<h3 class="title01">[\s]*<a.*>(.*)<\/a>/siU', $tmpInfo, $htype);
      $the_type = $type[$htype[1]];
      //$picInfo = $this->autocollect_model->vcurl($infourl, 'gzip');	//房源详情
      //$picInfo = iconv("GBK", "UTF-8", $picInfo);
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    if ($the_type == 1 || $the_type == 2) {
      preg_match('/id="input_PROJNAME".*value=\'(.*)\'/iU', $tmpInfo, $house_name);
      preg_match('/name="input_ROOM".*value="([0-9])" type="text" id="input_ROOM"/iU', $tmpInfo, $room);
      preg_match('/name="input_HALL".*value="([0-9])" maxlength="1" id="input_HALL"/iU', $tmpInfo, $hall);
      preg_match('/name="input_TOILET".*value="([0-9])" type="text" id="input_TOILET"/iU', $tmpInfo, $toilet);
      preg_match('/name="input_KITCHEN".*value="([0-9])" id="input_KITCHEN"/iU', $tmpInfo, $kitchen);
      preg_match('/name="input_BALCONY".*value="([0-9])" id="input_BALCONY"/iU', $tmpInfo, $balcony);
      preg_match("/SetValue\('single', 'input_n_str_FORWARD', '(.*)'\);/iU", $tmpInfo, $forward);
      preg_match("/SetValue\('single', 'input_n_str_FITMENT', '(.*)'\);/iU", $tmpInfo, $fitment);
      preg_match('/name="input_FLOOR" type="text" maxlength="2" value="([0-9]*)" id="input_FLOOR"/siU', $tmpInfo, $floor);
      preg_match('/name="input_ALLFLOOR" type="text" id="input_ALLFLOOR" value="([0-9]*)" maxlength="2"/siU', $tmpInfo, $totalfloor);
      preg_match('/id="BuildingArea"[\s]*value="(.*)" maxlength/siU', $tmpInfo, $area);
      preg_match('/id="input_PRICE" name="input_y_num_PRICE".*value="(.*)" onblur/iU', $tmpInfo, $price);
      preg_match('/id="houseTitle" class="input01 wid350" value=\'(.*)\' name=/siU', $tmpInfo, $title);
      preg_match('/name="input_n_str_CONTENT" value="(.*)"/siU', $tmpInfo, $content);

      preg_match("/LoadImages\('.*', '(.*)'\);/iU", $tmpInfo, $picdiv);
      $pics = explode(';', $picdiv[1]);
      if ($pics) {
        foreach ($pics as $val) {
          $imgurl = explode('|', $val);
          $tempurl = $this->autocollect_model->get_pic_url($imgurl[0], $city_spell);
          $image[] = $tempurl;
        }
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型2别墅
      $house_info['house_name'] = $house_name[1];  //楼盘名称
      $house_info['room'] = $room[1];        //室
      $house_info['hall'] = $hall[1];        //厅
      $house_info['toilet'] = $toilet[1];    //卫
      $house_info['kitchen'] = $kitchen[1];  //厨房
      $house_info['balcony'] = $balcony[1];  //阳台
      $house_info['forward'] = $config['forward'][$forward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['price'] = $price[1];       //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 3) {
      preg_match('/id="input_PROJNAME".*value=\'(.*)\'/siU', $tmpInfo, $house_name);
      preg_match("/SetValue\('single', 'input_n_str_FITMENT', '(.*)'\);/siU", $tmpInfo, $fitment);
      preg_match('/name="input_FLOOR" type="text" value="([0-9]*)" maxlength/siU', $tmpInfo, $floor);
      preg_match('/name="input_ALLFLOOR" type="text" value="([0-9]*)" id="input_ALLFLOOR"/siU', $tmpInfo, $totalfloor);
      preg_match('/name="input_y_num_BUILDINGAREA".*value="(.*)" validation/siU', $tmpInfo, $area);
      preg_match('/name="input_y_num_PRICE".*value="(.*)" validation/siU', $tmpInfo, $price);
      preg_match('/name="input_n_num_PropFee".*value="(.*)" validation/siU', $tmpInfo, $fee);
      preg_match('/id="houseTitle".*value="(.*)" onfocus/siU', $tmpInfo, $title);
      preg_match('/name="input_n_str_CONTENT" value="(.*)"/siU', $tmpInfo, $content);

      preg_match("/LoadImagesOfpur\('.*', '(.*)', 'shop'\);/iU", $tmpInfo, $picdiv);
      $pics = explode(';', $picdiv[1]);
      if ($pics) {
        foreach ($pics as $val) {
          $imgurl = explode('|', $val);
          $tempurl = $this->autocollect_model->get_pic_url($imgurl[0], $city_spell);
          $image[] = $tempurl;
        }
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = $floor[1];      //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];   //建筑面积
      $house_info['price'] = $price[1];      //总价
      $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
      $house_info['strata_fee'] = $fee[1];   //物业费 元/㎡·月
      $house_info['title'] = $title[1];      //标题
      $house_info['content'] = $content[1];  //描述
    } elseif ($the_type == 4) {
      preg_match('/id="input_PROJNAME".*value=\'(.*)\'/siU', $tmpInfo, $house_name);
      preg_match("/SetValue\('single','input_n_str_FITMENT','(.*)'\);/siU", $tmpInfo, $fitment);
      preg_match('/name="input_FLOOR" type="text" value="([0-9]*)" maxlength/siU', $tmpInfo, $floor);
      preg_match('/name="input_ALLFLOOR" type="text" id="input_ALLFLOOR" value="(.*)" maxlength/siU', $tmpInfo, $totalfloor);
      preg_match('/name="input_y_num_BUILDINGAREA".*value="(.*)" validation/siU', $tmpInfo, $area);
      preg_match('/name="input_y_num_PRICE".*value="(.*)" validation/siU', $tmpInfo, $price);
      preg_match('/name="input_n_num_PropFee".*value="(.*)" maxlength/iU', $tmpInfo, $fee);
      preg_match('/id="houseTitle".*value=\'(.*)\'/iU', $tmpInfo, $title);
      preg_match('/name="input_n_str_CONTENT" value="(.*)"/siU', $tmpInfo, $content);

      preg_match("/LoadImagesOfpur\('.*','(.*)','office'\);/iU", $tmpInfo, $picdiv);
      $pics = explode(';', $picdiv[1]);
      if ($pics) {
        foreach ($pics as $val) {
          $imgurl = explode('|', $val);
          $tempurl = $this->autocollect_model->get_pic_url($imgurl[0], $city_spell);
          $image[] = $tempurl;
        }
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;  //住宅类型
      $house_info['house_name'] = $house_name[1]; //楼盘名称
      $house_info['serverco'] = $config['fitment'][$fitment[1]];   //装修
      $house_info['floor'] = $floor[1];  //楼层
      $house_info['totalfloor'] = $totalfloor[1]; //总楼层
      $house_info['buildarea'] = $area[1];  //建筑面积
      $house_info['price'] = $price[1] * $area[1] / 10000;
      $house_info['avgprice'] = $price[1];
      $house_info['strata_fee'] = $fee[1];  //物业费 元/㎡·月
      $house_info['title'] = $title[1];     //标题
      $house_info['content'] = $content[1]; //描述
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
      $type = array('修改住宅出租房源' => 1, '修改别墅出租房源' => 2, '修改商铺出租房源' => 3, '修改写字楼出租房源' => 4, '修改厂房出租房源' => 5);
      $tmpInfo = $this->curl->vget($this->geturl() . $url, $cookie);
      $tmpInfo = iconv("GBK", "UTF-8", $tmpInfo);
      preg_match('/<h3 class="title01">[\s]*<a.*>(.*)<\/a>/siU', $tmpInfo, $htype);
      $the_type = $type[$htype[1]];
      //$picInfo = $this->autocollect_model->vcurl($infourl, 'gzip');	//房源详情
      //$picInfo = iconv("GBK", "UTF-8", $picInfo);
    }

    $config = $this->getconfig();
    $house_info = $image = array();
    if ($the_type == 1 || $the_type == 2) {
      preg_match('/id="input_PROJNAME".*value=\'(.*)\'/iU', $tmpInfo, $house_name);
      preg_match('/name="input_ROOM".*value="([0-9]*)" type=/iU', $tmpInfo, $room);
      preg_match('/name="input_HALL".*value="([0-9]*)" maxlength=/iU', $tmpInfo, $hall);
      preg_match('/name="input_TOILET".*value="([0-9]*)" type=/iU', $tmpInfo, $toilet);
      preg_match('/name="input_KITCHEN".*value="([0-9])" /siU', $tmpInfo, $kitchen);
      preg_match('/name="input_BALCONY".*value="([0-9])" /siU', $tmpInfo, $balcony);
      preg_match('/name="input_y_num_BUILDINGAREA".*value="(.*)" maxlength/siU', $tmpInfo, $area);
      preg_match('/name="input_y_num_PRICE".*value="(.*)" validation/siU', $tmpInfo, $price);
      preg_match('/id="houseTitle".*value=\'(.*)\' /iU', $tmpInfo, $title);
      preg_match('/name="input_n_str_CONTENT" value="(.*)" \/>/siU', $tmpInfo, $content);
      if ($the_type == 1) {
        preg_match('/input_n_str_FORWARD"\)\.attr\("value", "(.*)"\);/siU', $tmpInfo, $farward);
        preg_match('/input_n_str_FITMENT"\)\.attr\("value", "(.*)"\);/siU', $tmpInfo, $fitment);
        preg_match('/name="input_FLOOR" type="text" maxlength="2" value="([0-9]*)" validation/iU', $tmpInfo, $floor);
        preg_match('/name="input_ALLFLOOR".*value="([0-9]*)" maxlength/siU', $tmpInfo, $allfloor);
      } else {
        preg_match("/SetValue\('single', 'input_n_str_FORWARD', '(.*)'\);/siU", $tmpInfo, $farward);
        preg_match("/SetValue\('single', 'input_n_str_FITMENT', '(.*)'\);/siU", $tmpInfo, $fitment);
        preg_match('/name="input_ALLFLOOR".*value="([0-9]*)" validation/siU', $tmpInfo, $allfloor);
      }

      preg_match("/LoadImages\('.*', '(.*)'\);/iU", $tmpInfo, $picdiv);
      $pics = explode(';', $picdiv[1]);
      if ($pics) {
        foreach ($pics as $val) {
          $imgurl = explode('|', $val);
          if ($imgurl[2] == '小区相关图') continue;
          $tempurl = $this->autocollect_model->get_pic_url($imgurl[0], $city_spell);
          $image[] = $tempurl;
        }
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;   //住宅类型
      $house_info['house_name'] = $house_name[1];     //楼盘名称
      $house_info['room'] = $room[1];    //室
      $house_info['hall'] = $hall[1];    //厅
      $house_info['toilet'] = $toilet[1];  //卫
      $house_info['kitchen'] = $kitchen[1]; //厨房
      $house_info['balcony'] = $balcony[1]; //阳台
      $house_info['forward'] = $config['forward'][$farward[1]];    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];    //装修
      $house_info['floor'] = empty($floor[1]) ? 0 : $floor[1];   //楼层
      $house_info['totalfloor'] = $allfloor[1];   //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      $house_info['price'] = $price[1];       //租金 元.月
      $house_info['title'] = strip_tags($title[1]);       //标题
      $house_info['content'] = $content[1];   //描述
    } elseif ($the_type == 3 || $the_type == 4) {
      preg_match('/name="input_y_str_PROJNAME".*value=\'(.*)\' \/>/siU', $tmpInfo, $house_name);
      preg_match("/SetValue\('single',[\s]?'input_n_str_FITMENT',[\s]?'(.*)'\);/siU", $tmpInfo, $fitment);
      preg_match('/name="input_ALLFLOOR" type="text" value="([0-9]*)" id="input_ALLFLOOR"/siU', $tmpInfo, $allfloor);
      preg_match('/name="input_y_num_PRICE".*value="(.*)" validation/siU', $tmpInfo, $price);
      preg_match('/name="input_n_str_CONTENT" value="(.*)" \/>/siU', $tmpInfo, $content);
      if ($the_type == 3) {
        preg_match('/name="input_FLOOR" type="text" value="([0-9]*)" maxlength/iU', $tmpInfo, $floor);
        preg_match('/name="input_y_num_BUILDINGAREA".*value="(.*)" \/>/siU', $tmpInfo, $area);
        preg_match('/id="houseTitle".*value="(.*)" onfocus/siU', $tmpInfo, $title);
        preg_match("/LoadImagesOfpur\('.*', '(.*)', 'shop'\);/iU", $tmpInfo, $picdiv);
        preg_match('/name="rdPriceType" type="radio".*checked="checked"[\s]*value="(.*)" \/>/siU', $tmpInfo, $pricetype);
      } else {
        preg_match('/name="input_FLOOR" value="([0-9]*)" type="text" maxlength/iU', $tmpInfo, $floor);
        preg_match('/name="input_y_num_BUILDINGAREA" value="(.*)" type/siU', $tmpInfo, $area);
        preg_match('/id="houseTitle".*value=\'(.*)\' onkeyup/siU', $tmpInfo, $title);
        preg_match("/LoadImagesOfpur\('.*','(.*)','office'\);/iU", $tmpInfo, $picdiv);
        preg_match('/var t=\'(.*)\';[\s]*if\(t=="元\/平米·天"\)/siU', $tmpInfo, $pricetype);

      }

      $pics = explode(';', $picdiv[1]);
      if ($pics) {
        foreach ($pics as $val) {
          $imgurl = explode('|', $val);
          if ($imgurl[2] == '外景图') continue;
          $tempurl = $this->autocollect_model->get_pic_url($imgurl[0], $city_spell);
          $image[] = $tempurl;
        }
      }
      $house_info['picurl'] = implode('*', $image);
      $house_info['sell_type'] = $the_type;   //住宅类型
      $house_info['house_name'] = $house_name[1];     //楼盘名称
      $house_info['forward'] = 3;    //朝向
      $house_info['serverco'] = $config['fitment'][$fitment[1]];    //装修
      $house_info['floor'] = $floor[1];    //楼层
      $house_info['totalfloor'] = $allfloor[1];   //总楼层
      $house_info['buildarea'] = $area[1];    //建筑面积
      if ($pricetype[1] == '元/平米·天') {
        $house_info['price'] = $price[1] * $house_info['buildarea'] * 30;  //租金 元.月
      } elseif ($pricetype[1] == '元/平米·月') {
        $house_info['price'] = $price[1] * $house_info['buildarea']; //租金 元.月
      } else {
        $house_info['price'] = $price[1];   //租金 元.月
      }
      $house_info['title'] = $title[1];       //标题
      $house_info['content'] = $content[1];   //描述
    }
    return $house_info;
  }

  //上传图片到 搜房
  public function upload_image($url, $signatory_id, $type)
  {
    $city = $this->config->item('login_city');
    $cityurl = $this->geturl();
    $finalname = $this->site_model->upload_img($url);
    $login = $this->site_mass_model->isbind_site('fang', $signatory_id);
    if ($login['cookies'] && !empty($finalname)) {
      $cookie = $login['cookies'];
      $agent_id = $login['agent_id'];
      $tmpInfo = $this->curl->vget('http://' . $cityurl . '/magent/house/sale/saleinput.aspx', $cookie);
      preg_match('/id="agentmainput_Hfile".*UploadPic.*,.*,.*,.*,.*,.*,\'(.*)\',(.*),/siU', $tmpInfo, $pro);
      $refer = 'http://' . $cityurl . '/magent/house/sale/saleinput.aspx';
      if (empty($pro[1])) {
        $tmpInfo = $this->curl->vget('http://' . $cityurl . '/magent/house/lease/leaseinput.aspx', $cookie);
        preg_match('/id="Hfile".*UploadPic.*,.*,.*,.*,.*,.*,\'(.*)\',(.*),/siU', $tmpInfo, $pro);
        $refer = 'http://' . $cityurl . '/magent/house/lease/leaseinput.aspx';
      }

// 	        if($city=='taizhou'){
      $cutype = ($type == 'Hfile') ? 3 : 1;

      $api_url = 'http://' . $cityurl . '/MAgent/PicInterface/PicCloudAction.aspx?opt=GetCloudUrlAndSign&cutype=' . $cutype;  //腾讯云地址和签名
      $apiInfo = $this->curl->vget($api_url, $cookie, $refer);
      $api = json_decode($apiInfo, true);

      $ext_url = $api['Url'] . '?sign=' . urlencode($api['Sign']);
      $ext_field = array('filecontent' => '@' . $finalname);
      $extInfo = $this->curl->vpost($ext_url, $ext_field, $cookie, $refer, 0);  //腾讯云返回数据
      $extdata = json_decode($extInfo, true);
      $ext = $extdata['data'];

      $post_url = 'http://' . $cityurl . '/Magent/PicInterface/PicCloudSingleFinishAction.aspx?callback=ShowImgPicCloud&city=sh&type=' . $cutype;
      $post_field = 'download_url=' . $ext['download_url'] . '&fileid=' . $ext['fileid'] . '&info[0][0][height]=' . $ext['info'][0][0]['height']
        . '&info[0][0][width]=' . $ext['info'][0][0]['width'] . '&photo_rgb=' . $ext['photo_rgb'] . '&url=' . $ext['url'];
      $conInfo = $this->curl->vpost($post_url, $post_field, $cookie, $refer);
      $conInfo = mb_convert_encoding($conInfo, "UTF-8", "GBK");
      preg_match('/imgUrl\\\u0027:\\\u0027http(.*)\\\u0027,/siU', $conInfo, $pigname);
// 	        }else{
// 	            $post_url = 'http://imgfku1.fang.com/upload/agents/houseinfo2?channel=agent.houseinfo&uid='.$agent_id
// 	                      . '&city=nj&t='.$pro[1].'&kind=houseinfo&sid='.$pro[2].'&backurl=http%3a%2f%2f'.$cityurl
// 	                      . '%2fMagent%2fPicInterface%2fSingleImgUploadFinish.aspx%3fcallback%3dShowImg%26city%3dnj&type=3&drawtext=';
// 	            $post_field = array($type=>'@'.$finalname);
// 	            $conInfo = $this->curl->vpost($post_url, $post_field, $cookie, $refer);
// 	            $conInfo = mb_convert_encoding($conInfo, "UTF-8", "GBK");
// 	            preg_match('/<script.*u0027http(.*)\\\u0027,/siU', $conInfo, $pigname);
// 	        }

      @unlink($finalname);
      if (!empty($pigname[1])) {
        $picurl = 'http' . $pigname[1];
        return $picurl;
      }
    } elseif (!empty($finalname)) {
      @unlink($finalname);
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
    $btype = ($alias == 'sell') ? 'CS' : 'CZ';

    $list = array();
    $signatory_id = $this->signatory_info['signatory_id'];
    $login = $this->isbind($signatory_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie) {
      $keyword = mb_convert_encoding($keyword, "GBK", "UTF-8");
      $url = 'http://' . $this->geturl() . '/MAgent/House/getDistrictList.aspx?key=' . urlencode($keyword) . '&type=' . $type . '&btype=' . $btype;
      $tmpInfo = $this->curl->vget($url, $cookie);
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

      return $this->refresh($signatory_id, $house_id, $act, '', $queue_id);
    }
  }

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

  //同控制器中
  public function queue_publish($alias)
  {
    $act = $this->input->get('act');
    $site_id = $this->input->get('site_id');
    $house_id = $this->input->get('house_id');
    $queue_id = $this->input->get('queue_id');
    $signatory_info = $this->signatory_info;
    $signatory_id = $this->signatory_info['signatory_id'];

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
      $block_info['block_name'] = $block_name;
      $block_info['address'] = $this->input->get('address', TRUE);
      $block_info['district'] = $this->input->get('district', TRUE);
      $block_info['street'] = $this->input->get('street', TRUE);
    } else {
      $this->load->model('relation_street_model');
      $relation_block = $this->relation_street_model->relation_block($house_info['block_id'], $signatory_info['company_id']);
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
      $pub_sql = array('where' => array('signatory_id' => $signatory_id, 'house_id' => $house_id, 'site_id' => $site_id), 'form_name' => $pub_tbl);
      $pubInfo = $this->get_data($pub_sql, 'dbback_city');
      $publish_id = $pubInfo ? $pubInfo[0]['publish_id'] : 0;
      if ($publishinfo) {
        $login = $this->isbind($signatory_id);
        $extra = array('login' => $login, 'publish' => $pubInfo[0], 'htype' => $house_info['sell_type'], 'tbl' => $tbl);
        $del = $this->esta($extra);
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

  //发布数据组装
  public function publish_param($queue)
  {
    $this->load->model('signatory_info_model');
    $city = $this->config->item('login_city');
    $signatory_id = $queue['signatory_id'];
    $site_id = $queue['site_id'];
    $house_id = $queue['house_id'];
    $tbl = $queue['tbl'];
    $block_info = unserialize($queue['info']);
    if ($queue['tbl'] == 1) {
      $act = 'sell';
      $this->load->model('sell_house_model', 'my_house_model');
    } else {
      $act = 'rent';
      $this->load->model('rent_house_model', 'my_house_model');
    }

    $signatory_info = $this->signatory_info_model->get_by_signatory_id($signatory_id);  //经纪人信息
    $this->my_house_model->set_id($house_id);
    $house_info = $this->my_house_model->get_info_by_id(); //房源信息
    $login = $this->isbind($signatory_id);  //目标站点帐号信息
    $username = $login['username'];
    $config = $this->getconfig();

    $fang_fitment = $config['fit']['fang']; //装修匹配:住宅 别墅
    $shop_fitment = $config['fit']['shop']; //装修匹配:商铺 写字楼
    $forward = $config['face'][$house_info['forward']];  //朝向
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
    $str_equipment = implode('&input_n_str_BASESERVICE=', $tmp_equipment);

    //上传图片
    $shinei_pic = $huxing_pic = '';
    $shinei = $huxing = array();
    if ($house_info['pic_ids'] && $house_info['pic_tbl']) {
      $pic_info = $this->site_mass_model->find_house_pic_by_ids($house_info['pic_tbl'], $house_info['pic_ids']);
      if ($house_info['sell_type'] == 4) {
        if ($pic_info) {
          foreach ($pic_info as $key => $val) {
            $picurl = changepic_send($val['url']);
            if ($val['type'] == 1) {
              $shinei[] = $this->upload_image($picurl, $signatory_id, 'Nfile');
            } else if ($val['type'] == 2) {
              $huxing[] = $this->upload_image($picurl, $signatory_id, 'Pfile');
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
            $shinei[] = $this->upload_image($picurl, $signatory_id, 'Nfile');
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
              $shinei[] = $this->upload_image($picurl, $signatory_id, 'Sfile');
            } else if ($val['type'] == 2) {
              $huxing[] = $this->upload_image($picurl, $signatory_id, 'Hfile');
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

    $house_info['bewrite'] = $this->autocollect_model->special_flow($house_info['bewrite']);  //描述富文本处理
    $data_house = array();
    $data_house['block_name'] = $block_name = $block_info['block_name'];
    $data_house['district'] = $district = $block_info['district'];
    $data_house['street'] = $street = $block_info['street'];
    $data_house['address'] = $address = $block_info['address'];
    $data_house['sell_type'] = $the_type = $house_info['sell_type'];
    $area = round($house_info['buildarea'], 0);
    $price = round($house_info['price'], 0);
    if ($act == 'sell') { //出售 sell
      if ($the_type == 1) { //住宅
        $fitment = $fang_fitment[$house_info['fitment']];
        $data_house['post_field'] = 'input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
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
        $data_house['post_field'] = 'input_ROOM=' . $house_info['room'] . '&input_HALL=' . $house_info['hall'] . '&input_TOILET=' . $house_info['toilet']
          . '&input_KITCHEN=' . $house_info['kitchen'] . '&input_BALCONY=' . $house_info['balcony'] . '&input_n_str_CREATETIME=' . $house_info['buildyear']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $floor_area . $garden_area . $park_num . '&input_str_BuildingType=' . $villa_type
          . '&Hfile=' . $huxing_pic . '&Sfile=' . $shinei_pic . '&imageCount=' . $_i . '&b832bsdf4inpu=9e03a5input&input_DelegateIDAndAgentID=0'
          . '&input_y_str_PRICETYPE=万元/套&input_isHaveGarage=无&input_n_str_LOOKHOUSE=随时看房&input_y_str_PURPOSE=别墅&newcode=2110445582';
      } elseif ($the_type == 3) { //商铺
        $fitment = $shop_fitment[$house_info['fitment']];
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';
        $data_house['post_field'] = 'input_n_num_PropFee=' . $house_info['strata_fee'] . '&input_FLOOR=' . $house_info['floor']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&Wfile=&Nfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&input_y_str_PRICETYPE=万元/套&input_y_str_SubType=商业街商铺&input_y_str_PURPOSE=商铺&newcode=3110971366';
      } elseif ($the_type == 4) { //写字楼
        $fitment = $shop_fitment[$house_info['fitment']];
        $house_info['price'] = $house_info['price'] * 10000 / $house_info['buildarea'];  //写字楼 元/平米
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';
        $data_house['post_field'] = 'input_n_num_PropFee=' . $house_info['strata_fee'] . '&input_FLOOR=' . $house_info['floor']
          . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&Pfile=' . $huxing_pic . '&Nfile=' . $shinei_pic . '&imageCount=' . $_i
          . '&input_y_str_PRICETYPE=元/平米&input_n_str_propertyGrade=甲级&input_y_str_PURPOSE=写字楼&newcode=3111048994';
      }
      if (in_array($city, $config['special_city'])) {
        $b_html = '&f606a287input3d41=' . $area . '&2e333063input530a=' . $house_info['title'];
      } else {
        $b_html = '&6b5e2e9binput9d54=' . $area . '&5fed3002input40d5=' . $house_info['title'];
      }
      $data_house['act'] = 'sell';
      $data_house['post_url'] = '/MAgent/house/InputSave.aspx?flag=1&haveMultipleCity=False&city=' . $cityname . '&isWireless=0';
      $data_house['post_field'] .= '&input_y_str_PROJNAME=' . $block_name . '&input_y_str_ADDRESS=' . $address . '&input_y_str_DISTRICT=' . $district
        . '&input_y_str_COMAREA=' . $street . '&input_y_num_PRICE=' . $price . '&input_n_str_FITMENT=' . $fitment . '&input_y_str_MANAGERNAME=' . $username
        . '&hiddenProjname=' . $block_name . '&input_n_str_CONTENT=' . html_entity_decode($house_info['bewrite']) . $b_html
        . '&hdHouseDicCity=false&input_y_str_BUSINESSTYPE=CS&input_draftsID=0';
    } else {  //出租 rent
      if ($the_type == 1) { //住宅
        $fitment = $fang_fitment[$house_info['fitment']];
        $data_house['post_field'] = 'input_n_str_BASESERVICE=' . $str_equipment . '&HouseTags=拎包入住'
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
        $data_house['post_field'] = 'input_str_BuildingType=' . $villa_type
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
        $data_house['post_field'] = 'input_y_str_SubType=商业街商铺&input_shopstatus=闲置中&rdPriceType=元/月' . $propfee
          . '&input_istransfer=0&input_transferfee=面议'
          . '&input_FLOOR=' . $house_info['floor'] . "&input_ALLFLOOR=" . $house_info['totalfloor'] . $division . '&input_y_str_PURPOSE=商铺'
          . "&Nfile=" . $shinei_pic . '&imageCount=' . $_i
          . '&newcode=3110930598&input_y_str_PRICETYPE=元/月';
      } elseif ($the_type == 4) { //写字楼
        $fitment = $shop_fitment[$house_info['fitment']];
        $division = ($house_info['division'] == 1) ? '&input_n_int_IsDivisi=1' : '&input_n_int_IsDivisi=0';  //可分割
        $propfee = empty($house_info['strata_fee']) ? '&input_y_int_isIncludFee=0&input_n_num_PropFee='
          : '&input_y_int_isIncludFee=1&input_n_num_PropFee=' . round($house_info['strata_fee'], 0); //物业费
        $data_house['post_field'] = 'input_y_str_SubType=纯写字楼&rdPriceType=元/月' . $propfee
          . '&input_y_str_PAYINFO=个人产权&input_n_str_propertyGrade=甲级'
          . '&input_FLOOR=' . $house_info['floor'] . '&input_ALLFLOOR=' . $house_info['totalfloor'] . $division . '&input_y_str_PURPOSE=写字楼'
          . '&Hfile=&Pfile=' . $huxing_pic . '&Nfile=' . $shinei_pic . '&imageCount=' . $_i . '&newcode=3110005884&input_y_str_PRICETYPE=元/月';
      }
      if (in_array($city, $config['special_city'])) {
        $b_html = '&f606a287input3d41=' . $area . '&2e333063input530a=' . $house_info['title'];
      } else {
        $b_html = '&6b5e2e9binput9d54=' . $area . '&5fed3002input40d5=' . $house_info['title'];
      }
      $data_house['act'] = 'rent';
      $data_house['post_url'] = '/MAgent/house/InputSave.aspx?flag=1&haveMultipleCity=False&city=' . $cityname . '&isWireless=0';
      $data_house['post_field'] .= '&input_y_str_PROJNAME=' . $block_name . '&input_y_str_ADDRESS=' . $address . '&input_y_str_DISTRICT=' . $district
        . '&input_y_str_COMAREA=' . $street . '&input_y_num_PRICE=' . $price . '&input_n_str_FITMENT=' . $fitment . '&input_y_str_MANAGERNAME=' . $username
        . "&hiddenProjname=" . $block_name . '&input_n_str_CONTENT=' . html_entity_decode($house_info['bewrite']) . $b_html
        . '&hdHouseDicCity=0&input_y_str_BUSINESSTYPE=CZ&input_draftsID=0&input_y_str_LEASESTYLE=整租&input_y_str_PAYDETAIL_Y=押一付三';
    }
    $data_house['house_block_id'] = $house_info['block_id'];
    $result = $this->publish(array('data_house' => $data_house, 'queue' => $queue, 'login' => $login));
    if (isset($result['flag']) && $result['flag'] == 'success') {
      //记录 搜房楼盘
      $sf = array('block_id' => $data_house['house_block_id'],  //我们的block_id
        'department_id' => $signatory_info['department_id'],
        'company_id' => $signatory_info['company_id'],
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

//$conInfo = str_flow('script', '', $conInfo);
//$conInfo = iconv("GBK", "UTF-8", $conInfo);
/* End of file site_fang_model.php */
/* Location: ./application/mls_guli/models/site_fang_model.php */
