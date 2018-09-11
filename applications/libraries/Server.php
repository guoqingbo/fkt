<?php

/*
 * ################# 特别声明：由于php4不支持simplexml类，所以在php4服务器上的调用请设置返回类型为array #####################
 * ################# 特别声明：默认使用字符集 UTF-8，因simpleXML只支持UTF-8编码，所以当返回类型为simpleXML时，对应参数无效 ###
*/

class Server
{
  var $appName; //应用名，具体参见constant.php常量表
  var $strCheckMethod; //报错方式
  var $returnMethod; //数据返回类型
  var $returnCharSet; //数据返回字符编码 gbk,gb2312或utf-8
  var $sourceCharSet; //调用页面编码 gbk或utf-8
  var $mc; //MEMCACHE

  function Server($arr)
  {
    //include_once(dirname(__FILE__)."/logOutput.php");
    include_once(dirname(__FILE__) . "/xml.php");

    $this->appName = $arr['appName'];
    $this->strCheckMethod = $arr['strCheckMethod'];
    $this->returnMethod = strtolower($arr['returnMethod']);
    $this->returnCharSet = strtolower($arr['returnCharSet']);

    $this->mc = new Memcache;
  }

  function setMyCharSet($sourceCharSet)
  {
    $this->sourceCharSet = strtolower($sourceCharSet);
  }

  function execute($strSql, $writeLog = false, $method = "get")
  {
    $method = strtolower($method);

    /* strSql 中 过滤 [] () */
    $matches = array();
    preg_match_all('/%(.*)%/siU', $strSql, $matches);

    if (is_array($matches[0]) && sizeof($matches[0]) > 0) {
      foreach ($matches[0] as $keyword) {
        $new_keyword = str_replace(array('[', ']', '(', ')'), array('', '', '', ''), $keyword);

        if ($new_keyword != $keyword) {
          $strSql = str_replace($keyword, $new_keyword, $strSql);
        }
      }
    }
    /* strSql 中 过滤 [] () */

    /* 处理 %2C */
    $strSql = str_replace("%2C", ",", $strSql);
    /* 处理 %2C */

    $url = "http://" . $this->appName . "/servlet/ExecuteServlet";
    if ($method == "post")
      $arySql = array("sql" => $this->gbk2utf($strSql, true));
    else
      //  $url .= "?sql=".urlencode($strSql);
      $url .= "?sql=" . urlencode($this->gbk2utf($strSql, true));
    $msg = "";
    if ($this->returnMethod == "ary" || $this->returnMethod == "array") {
      if ($method == "post")
        $strData = $this->posttohost($url, $arySql);
      else
        $strData = @$this->curl_get_contents($url);


      if ($this->returnCharSet == "gbk" || $this->returnCharSet == "gb2312") {
        if (PHP_VERSION > 5) {
          $ary = XML_unserialize($strData, "Record");
          $ary = $this->aryIconv($ary, "utf-8", "gbk");
        } else {
          $ary = XML_unserialize($this->strIconv($strData, "utf-8", "gbk"), "Record");
        }
      } else
        $ary = XML_unserialize($strData, "Record");
      $ary = $ary["RecordSet"];
      $msg = $ary["MSG"];
    } else {
      if ($method == "post") {
        $strData = $this->posttohost($url, $arySql);
        $ary = @simplexml_load_string($strData);
      } else
        $ary = @simplexml_load_file($url);
      $msg = $ary->MSG;
    }
    if ($msg != "ok") {
      /*$writeLog && new LogOutput("errorMsg=".$msg." sql=".$strSql,"E");
      switch($this->strCheckMethod)
      {
          case "die":
              die("操作失败!");
              break;
          case "msg":
              die($msg);
              break;
          case "alert":
              die("<script>alert('".$msg."');</script>");
              break;
      }*/
    }
    return $ary;
  }

  function query($strSql, $writeLog = false, $method = "get")
  {
    global $arr_executetimes;
    list($usec, $sec) = explode(" ", microtime());
    $firsttime = $begintime = ((float)$usec + (float)$sec);

    $method = strtolower($method);

    /* strSql 中 过滤 [] () */
    $matches = array();
    preg_match_all('/%(.*)%/siU', $strSql, $matches);

    if (is_array($matches[0]) && sizeof($matches[0]) > 0) {
      foreach ($matches[0] as $keyword) {
        $new_keyword = str_replace(array('[', ']', '(', ')'), array('', '', '', ''), $keyword);

        if ($new_keyword != $keyword) {
          $strSql = str_replace($keyword, $new_keyword, $strSql);
        }
      }
    }
    /* strSql 中 过滤 [] () */

    /* 处理 %2C */
    $strSql = str_replace("%2C", ",", $strSql);
    /* 处理 %2C */

    $url = "http://" . $this->appName . "/servlet/QueryServlet";
    if ($method == "post")
      $arySql = array("sql" => $this->gbk2utf($strSql, true));
    else
      $url .= "?sql=" . urlencode($this->gbk2utf($strSql, true));

    $msg = "";
    if ($this->returnMethod == "ary" || $this->returnMethod == "array") {
      if ($method == "post")
        $strData = $this->posttohost($url, $arySql);
      else
        $strData = @$this->curl_get_contents($url);
      //$strData=str_replace("&lt;/font&gt;","</font>",$strData);
      //$strData=str_replace("&lt;font&gt;","<font>",$strData);

      //echo $strData.'<br>';
      if ($this->returnCharSet == "gbk" || $this->returnCharSet == "gb2312") {
        if (PHP_VERSION > 5) {
          $ary = xml_unserialize($strData, "Record");
          $ary = $this->aryIconv($ary, "utf-8", "gbk");
        } else {
          $ary = XML_unserialize($this->strIconv($strData, "utf-8", "gbk"), "Record");
          if (@sizeof($ary) == 0) {
            $ary = XML_unserialize($strData, "Record");
            $ary = $this->aryIconv($ary, "utf-8", "gbk");
          }

        }
      } else
        $ary = XML_unserialize($strData, "Record");

      $ary = isset($ary["RecordSet"]) ? $ary["RecordSet"] : array();
      $msg = isset($ary["MSG"]) ? $ary["MSG"] : array();
    } else {
      if ($method == "post") {
        $strData = $this->posttohost($url, $arySql);
        $ary = @simplexml_load_string($strData);
      } else
        $ary = @simplexml_load_file($url);
      $msg = $ary->MSG;
    }
    if ($msg != "ok") {
      /*$writeLog && new LogOutput("errorMsg=".$msg." sql=".$strSql,"E");
      switch($this->strCheckMethod)
      {
          case "die":
              die("数据获取失败!");
              break;
          case "msg":
              die($msg);
              break;
          case "alert":
              die("<script>alert('".$msg."');</script>");
              break;
      }*/
    }


    list($usec, $sec) = explode(" ", microtime());
    $endtime = ((float)$usec + (float)$sec);
    $executetime = round(($endtime - $begintime) * 1000, 6);
    $arr_executetimes[$strSql] = $executetime;


    return $ary;
  }

  /**
   * 将数据存入memcache服务器
   * 参数支持的数据类型包括simpleXML,array,String，暂不支持其他类型
   * （由于数组转xml无法完全支持，目前数组只支持一维数组，多维可能出错，如果使用请详细测试）
   * @param 包含数据的ximpleXML ，数组或字符串
   * @return 保存数据的对应session id
   * */
  function setMemcache($data, $uid = "")
  {
    if (is_array($data))
      $strData = XML_serialize($data);
    elseif (get_class($data) == "SimpleXMLElement")
      $strData = $data->asXML();
    else
      $strData = "<?xml version=\"1.0\"?><RecordSet>" . $data . "</RecordSet>";

    $memcache = new Memcache;
    $aryServer = explode(":", $this->appName);
    $memcache->connect($aryServer[0], $aryServer[1]) or die ("连接memcache失败！");
    if ($uid == "")
      $uid = md5(mt_rand());
    $memcache->set($uid, $this->gbk2utf($strData, true)) or die ("memcache数据写入失败");
    return $uid;
  }

  /**
   * 通过session id将数据从memcache服务器中取出
   * @param session id
   * @return 按指定类型方式返回数据。
   * */
  function getMemcache($uid)
  {
    $memcache = new Memcache;
    $aryServer = explode(":", $this->appName);
    $memcache->connect($aryServer[0], $aryServer[1]) or die ("连接memcache失败！");
    $strData = $memcache->get($uid);
    if (!$strData) return;

    if ($this->returnMethod == "ary" || $this->returnMethod == "array") {
      if ($this->returnCharSet == "gbk" || $this->returnCharSet == "gb2312") {
        if (PHP_VERSION > 5) {
          $ary = XML_unserialize($strData, "Record");
          $ary = $this->aryIconv($ary, "utf-8", "gbk");
        } else {
          $ary = XML_unserialize($this->strIconv($strData, "utf-8", "gbk"), "Record");
        }
      } else
        $ary = XML_unserialize($strData, "Record");
    } else
      $ary = @simplexml_load_string($strData);

    return $ary;
  }

  /*function is_gb2312($str)
  {
      for($i=0; $i<strlen($str); $i++) {
          $v = ord( $str[$i] );
          if( $v > 127) {
              if( ($v >= 228) && ($v <= 233) )
              {
                  if( ($i+2) >= (strlen($str) - 1)) return true;  // not enough characters
                  $v1 = ord( $str[$i+1] );
                  $v2 = ord( $str[$i+2] );
                  if( ($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191) ) // utf编码
                      return false;
                  else
                      return true;
              }
          }
      }
      return true;
  }
  function is_utf8($string) {
      return preg_match('%^(?:
      [\x09\x0A\x0D\x20-\x7E] # ASCII
      | [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
      | \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
      | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
      | \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
      | \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
      | [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
      | \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
      )*$%xs', $string);
  }*/

  function gbk2utf($strInput, $utfCheck = false)
  {
    if ($utfCheck) {

      if ($this->is_utf8($strInput)) {
        return $strInput;
      } else
        return $this->strIconv($strInput, "gbk", "utf-8");
    } else {
      return $this->sourceCharSet == "utf-8" ? $strInput : $this->strIconv($strInput, "gbk", "utf-8");
    }
  }

  function is_utf8($strInput)
  {
    //return $strInput === iconv("gbk","utf-8",iconv("utf-8","gbk//IGNORE",$strInput));
    //return (utf8_encode(utf8_decode($strInput)) == $strInput);
    return preg_match('/^(.|\n)*$/u', $strInput) > 0;//preg_match('/^./u', $string)
  }

  function posttohost($url, $data)
  {
    $url = parse_url($url);
    if (!$url) return "couldn't parse url";
    if (!isset($url['port'])) {
      $url['port'] = "";
    }
    if (!isset($url['query'])) {
      $url['query'] = "";
    }

    $encoded = "";

    while (list($k, $v) = each($data)) {
      $encoded .= ($encoded ? "&" : "");
      $encoded .= rawurlencode($k) . "=" . rawurlencode($v);
    }

    $fp = fsockopen($url['host'], $url['port'] ? $url['port'] : 80);
    if (!$fp) return "Failed to open socket to $url[host]";

    fputs($fp, sprintf("POST %s%s%s HTTP/1.0\n", $url['path'], $url['query'] ? "?" : "", $url['query']));
    fputs($fp, "Host: $url[host]\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
    fputs($fp, "Content-length: " . strlen($encoded) . "\n");
    fputs($fp, "Connection: close\n\n");

    fputs($fp, "$encoded\n");

    $line = fgets($fp, 1024);
    if (!eregi("^HTTP/1\.. 200", $line)) return;

    $results = "";
    $inheader = 1;
    while (!feof($fp)) {
      $line = fgets($fp, 1024);
      if ($inheader && ($line == "\n" || $line == "\r\n")) {
        $inheader = 0;
      } elseif (!$inheader) {
        $results .= $line;
      }
    }
    fclose($fp);

    return $results;
  }

  //以下为数组编码转换
  var $inChar;
  var $outChar;

  /**
   * 静态方法,该方法输入数组并返回数组
   *
   * @param unknown_type $array 输入的数组
   * @param unknown_type $inChar 输入数组的编码
   * @param unknown_type $outChar 返回数组的编码
   * @return unknown 返回的数组
   */
  function aryIconv($array, $inChar, $outChar)
  {
    $this->inChar = $inChar;
    $this->outChar = $outChar;
    return $this->arraymyicov($array);
  }

  /**
   * 内部方法,循环数组
   *
   * @param unknown_type $array
   * @return unknown
   */
  function arraymyicov($array)
  {
    foreach ((array)$array as $key => $value) {
      $key = $this->strIconv($key);
      if (!is_array($value)) {
        $value = $this->strIconv($value);
      } else {
        $value = $this->arraymyicov($value);
      }
      $temparray[$key] = $value;
    }
    return isset($temparray) ? $temparray : '';
  }

  /**
   * 替换数组编码
   * @param unknown_type $strInput
   * @return unknown
   */
  function strIconv($strInput, $inChar = "", $outChar = "")
  {
    if ($inChar)
      $this->inChar = $inChar;
    if ($outChar)
      $this->outChar = $outChar;
    $strOutput = iconv($this->inChar, $this->outChar, $strInput);
    if ($strOutput !== false)
      return $strOutput;
    else
      return $strInput;
  }

  function curl_get_contents($url, $t_url = "")
  {
    global $nocache;
    /********在这里增加memcache实属无奈之举**********/
    $this->mc->connect('localhost', 1122);//这个memcache专门给server类调用,记得换正式
    $key = md5($url);
    if (!($str = $this->mc->get($key)) || $nocache == "1") {

      /********在这里增加memcache实属无奈之举**********/
      $ch = curl_init();
      $t_url = $t_url ? $t_url : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      curl_setopt($ch, CURLOPT_URL, $url);
      /*
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $useragent="Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; QQDownload 1.7; TencentTraveler 4.0";
            curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
            curl_setopt($ch,CURLOPT_COOKIESESSION,true);
            */
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // 连接超时（秒）
      curl_setopt($ch, CURLOPT_REFERER, $t_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

      $time_start = $this->getmicrotime();

      $str = curl_exec($ch);

      $this->mc->set($key, $str, false, 60);

      $time_end = $this->getmicrotime();
      $time_exec = $time_end - $time_start;
      if ($time_exec >= 1) {
        //$filename = dirname(__FILE__)."/middle_".date("Y-m-d",time()).".log";
        //$handle = fopen($filename, "a");
        //$strContent=date("Y-m-d H:i:s",time())."--".$time_exec."--".$t_url."--".$url."\n";
        //fwrite($handle, $strContent);
        //fclose($handle);
        //unset($strContent);
      }
      curl_close($ch);
      /********在这里增加memcache实属无奈之举 打开连接要有关的习惯！！**********/
    }
    $this->mc->close();
    /********在这里增加memcache实属无奈之举 打开连接要有关的习惯！！**********/
    return $str;
  }

  function getmicrotime()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
}

?>
