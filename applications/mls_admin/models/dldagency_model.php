<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Dldagency_model CLASS
 *
 * 会员中心数据后台
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Dldagency_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
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

/* End of file Dldagency_model.php */
/* Location: ./application/models/Dldagency_model.php */
