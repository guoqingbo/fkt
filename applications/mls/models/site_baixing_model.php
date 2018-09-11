<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

load_m("Site_baixing_base_model");

class Site_baixing_model extends Site_baixing_base_model
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

    $login = $this->login($extra);
    if (!empty($login['cookies'])) {
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
    } else {
      $data = array('error' => 'yes', 'info' => isset($login['info']) ? $login['info'] : '绑定失败');
    }
    return $data;
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
        $intype = '二手房出售';
      } else {
        $intype = '租房/出租';
      }

      $tmpInfo = $this->curl->vget('http://www.baixing.com/w/posts/myPosts/active', $cookie);
      preg_match_all("/li id='ad-([0-9]*)' data(.*)<\/div><\/li>/siU", $tmpInfo, $prj);
      $data_list = array();
      foreach ($prj[2] as $val) {
        preg_match("/html'>(.*)<\/a>&nbsp;<a target='_blank'.*class='tag'>(.*)<\/a>/siU", $val, $htype);
        preg_match("/a href='(.*)' data-toggle='woverify' data-type='edit'/iU", $val, $url);
        preg_match("/style='float:left'>(.*)<small style='clear:right' class='pull-right'><a id='superRefresh/siU", $val, $dec);
        preg_match("/浏览<span class='dividing'>\/<\/span>(.*)<\/small>/iU", $val, $time);

        if ($htype[2] == $intype) {
          if ($time[1]) {
            $day = str_replace(array('年', '月', '日'), array('-', '-', ''), $time[1]);
            $strtime = strtotime(date('Y') . '-' . $day);
            if ($strtime > time()) {
              $strtime = strtotime('2016-' . $day);  //防止 12月的数据错误
            }
          } else {
            $strtime = time();
          }
          $data = array();
          $data['source'] = 0;
          $data['url'] = $url[1];   //编辑链接
          $data['infourl'] = $url[1];    //详情链接
          $data['title'] = strip_tags($htype[1]);  //标题
          $data['des'] = strip_tags($dec[1]);  //描述
          $data['releasetime'] = $strtime;     //发布时间
          $data['city_spell'] = $this->broker_info['city_spell'];
          $data['broker_id'] = $broker_id;
          $data['site_id'] = $site_id;
          $data_list[] = $data;
          $num++;
        }
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
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $city_spell = $this->broker_info['city_spell'];
    $config = $this->getconfig();

    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $tmpInfo = $this->curl->vget($url, $cookie);
      preg_match("/hasSubmitBtn: false,   values: '(.*)',   \/\* 在修改状态下传入/siU", $tmpInfo, $jsonp);
      $prj = json_decode($jsonp[1], true);
      if (empty($prj)) {
        return false;
      }
    }

    $house_info = $image = array();
    //图片
    if (!empty($prj['images'])) {
      foreach ($prj['images'] as $val) {
        $tpval = str_replace('_180x180', '_bi', $val);
        $tempurl = $this->autocollect_model->get_pic_url($tpval, $city_spell);
        $image[] = $tempurl;
      }
    }
    preg_match('/([0-9])室([0-9])厅([0-9])卫/siU', $prj['房型'], $room);

    $house_info['picurl'] = implode('*', $image);
    $house_info['sell_type'] = 1;  //类型2别墅3商铺
    $house_info['house_name'] = $prj['小区名'];         //楼盘名称
    $house_info['room'] = $room[1] ? $room[1] : 4;  //室
    $house_info['hall'] = $room[2] ? $room[2] : 0;  //厅
    $house_info['toilet'] = $room[3] ? $room[3] : 0;  //卫
    $house_info['kitchen'] = 0;     //厨房
    $house_info['balcony'] = 0;     //阳台
    $house_info['forward'] = $config['forward'][$prj['房间朝向']] ? $config['forward'][$prj['房间朝向']] : 3;    //朝向
    $house_info['serverco'] = $config['fitment'][$prj['装修情况']] ? $config['fitment'][$prj['装修情况']] : 2;    //装修
    $house_info['floor'] = $prj['楼层'];  //楼层
    $house_info['totalfloor'] = $prj['总楼层'];  //总楼层
    $house_info['buildarea'] = empty($prj['面积']) ? 1 : $prj['面积'];    //建筑面积
    $house_info['price'] = $prj['价格'];       //总价
    $house_info['avgprice'] = round($house_info['price'] * 1000000 / $house_info['buildarea']) / 100;
    $house_info['title'] = $prj['title'];       //标题
    $house_info['content'] = strip_tags($prj['content']);   //描述
    return $house_info;
  }

  //详情页面导入:租房
  public function collect_rent_info()
  {
    $url = $this->input->get('url');
    $site_id = $this->website['id'];
    $broker_id = $this->broker_info['broker_id'];
    $city_spell = $this->broker_info['city_spell'];
    $config = $this->getconfig();

    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if (empty($cookie)) {
      return false;
    } else {
      $tmpInfo = $this->curl->vget($url, $cookie);
      preg_match("/hasSubmitBtn: false,   values: '(.*)',   \/\* 在修改状态下传入/siU", $tmpInfo, $jsonp);
      $prj = json_decode($jsonp[1], true);
      if (empty($prj)) {
        return false;
      }
    }

    $house_info = $image = array();
    //图片
    if (!empty($prj['images'])) {
      foreach ($prj['images'] as $val) {
        $tpval = str_replace('_180x180', '_bi', $val);
        $tempurl = $this->autocollect_model->get_pic_url($tpval, $city_spell);
        $image[] = $tempurl;
      }
    }
    preg_match('/([0-9])室/siU', $prj['房型'], $room);

    $house_info['picurl'] = implode('*', $image);
    $house_info['sell_type'] = 1;  //类型2别墅3商铺
    $house_info['house_name'] = $prj['小区名'];         //楼盘名称
    $house_info['room'] = $room[1] ? $room[1] : 1;  //室
    $house_info['hall'] = $prj['厅'];    //厅
    $house_info['toilet'] = $prj['卫'];    //卫
    $house_info['kitchen'] = 0;     //厨房
    $house_info['balcony'] = 0;     //阳台
    $house_info['forward'] = $config['forward'][$prj['房间朝向']] ? $config['forward'][$prj['房间朝向']] : 3;    //朝向
    $house_info['serverco'] = $config['fitment'][$prj['装修']] ? $config['fitment'][$prj['装修']] : 2;    //装修
    $house_info['floor'] = $prj['楼层'];  //楼层
    $house_info['totalfloor'] = $prj['总楼层']; //总楼层
    $house_info['buildarea'] = empty($prj['面积']) ? 1 : $prj['面积'];    //建筑面积
    $house_info['price'] = $prj['价格'];       //总价
    $house_info['title'] = $prj['title'];      //标题
    $house_info['content'] = strip_tags($prj['content']);   //描述
    return $house_info;
  }

  //上传图片
  public function upload_image($url, $broker_id, $tbl, $publish_id)
  {
    $finalname = $this->site_model->upload_img($url);
    $login = $this->isbind($broker_id);
    $cookie = empty($login['cookies']) ? '' : $login['cookies'];
    if ($cookie && !empty($finalname)) {

    }
    if ($finalname) {
      @unlink($finalname);
    }
    return $data;
  }

  //定时任务下架
  public function queue_esta($queue)
  {

  }

  //是否转移到定时任务
  public function queue_publish($alias)
  {

  }

  //发布数据组装
  public function publish_param($queue)
  {

  }
}

/* End of file site_baixing_model.php */
/* Location: ./application/mls/models/site_baixing_model.php */
