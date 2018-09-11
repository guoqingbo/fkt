<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * zsb
 *
 * 业务类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 导入个人房源
 *
 *
 * @package         zsb
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("import_person_house_base_model");

class Import_person_house_model extends Import_person_house_base_model
{

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();
    $this->_set_luc();
  }

  /**
   * 设置luc
   */
  private function _set_luc()
  {
    $city = $this->config->item('login_city');
    $luc = $this->config->item('luc');
    $city_luc = $luc[$city];
    $luc_param = array('strCheckMethod' => "", 'returnMethod' => "ary", 'returnCharSet' => "gbk");
    $luc_param['appName'] = $city_luc['sell'];
    $this->load->library('server', $luc_param, 'sell_server');
    $luc_param['appName'] = $city_luc['rent'];
    $this->load->library('server', $luc_param, 'rent_server');
  }

  /**
   *
   * @param type $count_line
   * @param type $infofrom
   * @return type
   */
  public function get_sell_house($infofrom = 1)
  {
    $num = 20;
    $sql = "select * from sell where esta = 1 and infofrom = '$infofrom' order by updatetime desc "
      . "limit 0," . $num . " luc";
    return $this->sell_server->query($sql);
  }

  /**
   *
   * @param type $count_line
   * @param type $infofrom
   * @return type
   */
  public function get_rent_house($infofrom = 1)
  {
    $num = 20;
    $sql = "select * from rent where esta = 1 and infofrom = '$infofrom' order by updatetime desc  "
      . "limit 0," . $num . " luc";
    return $this->rent_server->query($sql);
  }

  /**
   * 导入的数据
   * @param string $url 导入的地址
   * @param array $array
   */
  public function send_request($url)
  {
    $this->load->library('Curl');
    $json = Curl::curl_get_contents($url);
    return json_decode($json, true);
  }

  function show_msg($msg, $gourl, $onlymsg = 0, $limittime = 0)
  {
    if (empty($GLOBALS['cfg_plus_dir'])) $GLOBALS['cfg_plus_dir'] = '..';

    $htmlhead = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=gb2312\" />\r\n";
    $htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0' bgcolor='#FFFFFF'>" . (isset($GLOBALS['ucsynlogin']) ? $GLOBALS['ucsynlogin'] : '') . "\r\n<center>\r\n<script>\r\n";
    $htmlfoot = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";

    $litime = ($limittime == 0 ? 1000 : $limittime);
    $func = '';

    if ($gourl == '-1') {
      if ($limittime == 0) $litime = 5000;
      $gourl = "javascript:history.go(-1);";
    }

    if ($gourl == '' || $onlymsg == 1) {
      $msg = "<script>alert(\"" . str_replace("\"", "“", $msg) . "\");</script>";
    } else {
      //当网址为:close::objname 时, 关闭父框架的id=objname元素
      if (preg_match('/close::/', $gourl)) {
        $tgobj = trim(preg_replace('/close::/', '', $gourl));
        $gourl = 'javascript:;';
        $func .= "window.parent.document.getElementById('{$tgobj}').style.display='none';\r\n";
      }

      $func .= "      var pgo=0;
			function JumpUrl(){
			if(pgo==0){ location='$gourl'; pgo=1; }
			}\r\n";
      $rmsg = $func;
      $rmsg .= "document.write(\"<br /><div style='width:450px;padding:0px;border:1px solid #DADADA;'>";
      $rmsg .= "<div style='padding:6px;font-size:12px;border-bottom:1px solid #DADADA;background:#DBEEBD url({$GLOBALS['cfg_plus_dir']}/img/wbg.gif)';'><b>提示信息！</b></div>\");\r\n";
      $rmsg .= "document.write(\"<div style='height:130px;font-size:10pt;background:#ffffff'><br />\");\r\n";
      $rmsg .= "document.write(\"" . str_replace("\"", "“", $msg) . "\");\r\n";
      $rmsg .= "document.write(\"";

      if ($onlymsg == 0) {
        if ($gourl != 'javascript:;' && $gourl != '') {
          $rmsg .= "<br /><a href='{$gourl}'>如果你的浏览器没反应，请点击这里...</a>";
          $rmsg .= "<br/></div>\");\r\n";
          $rmsg .= "setTimeout('JumpUrl()',$litime);";
        } else {
          $rmsg .= "<br/></div>\");\r\n";
        }
      } else {
        $rmsg .= "<br/><br/></div>\");\r\n";
      }
      $msg = $htmlhead . $rmsg . $htmlfoot;
    }
    echo $msg;
  }
}

?>
