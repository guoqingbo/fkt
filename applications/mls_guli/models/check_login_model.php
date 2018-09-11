<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Check_login_model extends MY_Model
{
  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->load->library('Curl');
  }

  //安居客登录
  public function check_login_anjuke()
  {
    $signatory_info = $this->signatory_model->get_user_session();
    $signatory_id = $signatory_info['signatory_id'];
    $this->load->model('site_model');
    $arr = array('alias' => 'anjuke');
    $site_info = $this->site_model->get_site_byid($arr);
    $site_id = $site_info[0]['id'];
    $where = array(
      'signatory_id' => $signatory_id,
      'site_id' => $site_id
    );
    $data = $this->site_model->get_signatoryinfo_byids($where);
    $username = $data[0]['username'];
    $password = $data[0]['password'];
    $url = 'http://my.anjuke.com/usercenter/login';//登录页地址
    $post_fields = array();
    $post_fields['username'] = $username;
    $post_fields['password'] = $password;
    $post_fields['loginpost'] = 1;
    $post_fields['sid'] = 'anjukemy';
    $post_fields['url'] = 'aHR0cDovL3d3dy5hbmp1a2UuY29t';
    $post_fields['systemtime'] = time();
    $post_fields['fromsignatory'] = 1;
    $post_fields['act'] = 'login';

    $tmpInfo = $this->curl->vlogin($url, $post_fields);

    preg_match('/<title>(.*)<\/title>/siU', $tmpInfo, $pro);
    preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo, $matches);
    $cookie = implode(';', $matches[1]);

    if ($pro[1] == '登录成功  - 安居客通行证') {
      preg_match('/URL=(.*)"/siU', $tmpInfo, $pro);
      $getUrl = explode('?', $pro[1]);
      $tmpInfo2 = $this->curl->vlogin($getUrl[0], $getUrl[1]);
      preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo2, $matches2);
      $cookie = implode(';', $matches2[1]);
    } else {
      $cookie = '';
    }
    $datas['signatory_id'] = $signatory_id;
    $datas['cookie'] = $cookie;
    return $datas;
  }

  //搜房登录
  public function check_login_fang()
  {
    $signatory_info = $this->signatory_model->get_user_session();
    $city_spell = $signatory_info['city_spell'];
    $signatory_id = $signatory_info['signatory_id'];
    $this->load->model('site_model');
    $arr = array('alias' => 'fang');
    $site_info = $this->site_model->get_site_byid($arr);
    $site_id = $site_info[0]['id'];
    $where = array(
      'signatory_id' => $signatory_id,
      'site_id' => $site_id
    );
    $data = $this->site_model->get_signatoryinfo_byids($where);
    $username = $data[0]['username'];
    $password = $data[0]['password'];
    $otherpwd = $data[0]['otherpwd'];
    $url = 'http://agent.fang.com/DealCenterLogin.aspx';//登录页地址
    $post_fields = array();
    $post_fields['str_username'] = $username;
    $post_fields['str_userpwd'] = $otherpwd;

    $tmpInfo = $this->curl->vlogin($url, $post_fields);

    $pos = strpos($tmpInfo, 'userid');

    if ($pos > 0) { //登录成功
      preg_match_all("/set\-cookie:([^\r\n]*)/i", $tmpInfo, $matches);
      $cookie = implode(';', $matches[1]);
    } else {
      $cookie = '';
    }
    $datas['city_spell'] = $city_spell;
    $datas['signatory_id'] = $signatory_id;
    $datas['cookie'] = $cookie;
    return $datas;
  }
}

?>
