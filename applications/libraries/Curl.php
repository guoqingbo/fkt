<?php

/**
 * Description of Curl
 *
 * @author user
 */
class Curl
{

  private $user_agent;

  public function __construct()
  {
    $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
  }

  public function fktdata($url, $post_fields)
  {
    $post = http_build_query($post_fields);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; (R1 1.5))');
    $tmpInfo = curl_exec($ch);
    curl_close($ch);

    //$this->record('同步帐号'.$url.'___'.$tmpInfo, $post_fields, 'fkt2xffx');
    return $tmpInfo;
  }

  public static function curl_get_contents($str, $t_url = "")
  {
    $ch = curl_init();
    $t_url = $t_url ? $t_url : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    curl_setopt($ch, CURLOPT_URL, $str);
    curl_setopt($ch, CURLOPT_REFERER, $t_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
  }

  //群发定时任务用
  public function set_ua($num)
  {
    if ($num == 1) {
      //系统xp
      $this->user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; (R1 1.5))';
    } elseif ($num == 2) {
      $this->user_agent = $_SERVER['HTTP_USER_AGENT'];
    } elseif ($num == 3) {
      //系统win7以上
      $this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; .NET CLR 3.5.21022; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    }
  }

  public function vlogin($url, $post_fields, $cookie = '')
  {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);  // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关闭CURL会话

    return $tmpInfo;
  }

  public function vget($url, $cookie = '', $refer = '', $header = 0)
  {
    $header = $header == 0 ? 0 : 1;
    $curl = curl_init(); // 启动一个CURL会话
    if ($refer) {
      curl_setopt($curl, CURLOPT_REFERER, $refer);
    }
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);  // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关闭CURL会话

    return $tmpInfo; // 返回数据
  }


  public function vpost($post_url, $post_fields, $cookie = '', $refer = '', $header = true)
  {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
    if ($refer) {
      curl_setopt($curl, CURLOPT_REFERER, $refer);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
    curl_setopt($curl, CURLOPT_COOKIE, $cookie); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话

    return $tmpInfo; // 返回数据
  }

    public function httpRequstPost($post_url, $post_fields, $cookie = '', $refer = '', $header = false)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
        if ($refer) {
            curl_setopt($curl, CURLOPT_REFERER, $refer);
        }
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
        curl_setopt($curl, CURLOPT_COOKIE, $cookie); // 读取上面所储存的Cookie信息
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno' . curl_error($curl);
        }
        curl_close($curl); // 关键CURL会话

        return $tmpInfo; // 返回数据
    }

  public function vpost_pingan($post_url, $post_fields, $cookie = '', $refer = '', $header = true)
  {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
    if ($refer) {
      curl_setopt($curl, CURLOPT_REFERER, $refer);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_fields)); // Post提交的数据包
    //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话

    $this->record('群发' . $post_url . '___' . $tmpInfo, $post_fields, 'qf');


    return $tmpInfo; // 返回数据
  }

  public static function static_vpost($post_url, $post_fields, $cookie = '', $refer = '', $header = true)
  {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
    if ($refer) {
      curl_setopt($curl, CURLOPT_REFERER, $refer);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
    //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, $header); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话

    return $tmpInfo; // 返回数据
  }

  /**
   * post json格式的数组
   * @param string $post_url 请求地址
   * @param json $data_string 提交数据
   */
  public static function vpost_by_json($post_url, $data_string)
  {
    $ch = curl_init($post_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );
    return curl_exec($ch);
  }

  public static function browers($host, $url)
  {
    if (!function_exists('gzdecode')) {
      function gzdecode($data)
      {
        $flags = ord(substr($data, 3, 1));
        $headerlen = 10;
        $extralen = 0;
        $filenamelen = 0;
        if ($flags & 4) {
          $extralen = unpack('v', substr($data, 10, 2));
          $extralen = $extralen[1];
          $headerlen += 2 + $extralen;
        }
        if ($flags & 8) // Filename
          $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 16) // Comment
          $headerlen = strpos($data, chr(0), $headerlen) + 1;
        if ($flags & 2) // CRC at end of file
          $headerlen += 2;
        $unpacked = @gzinflate(substr($data, $headerlen));
        if ($unpacked === FALSE)
          $unpacked = $data;
        return $unpacked;
      }
    }
    $header = array(
      "Host: $host",
      "Connection: keep-alive",
      "Cache-Control: max-age=0",
      "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
      "User-Agent: Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.114 Safari/537.36",
      "Accept-Encoding: gzip,deflate,sdch",
      "Accept-Language: zh-CN,zh;q=0.8,en;q=0.6",
      "Cookie: __zpspc=8.37.1421824652.1421824652.1%233%7Ccb.baidu.com%7C%7C%7C%7C%23; ajk_member_from=12055346; ajk_member_key=12055346; ajk_member_time=12055346; me=1; aQQ_Memberauthinfos=QmIkbL37J0JYEdQct9rwKLz%2FPPliYMixtEaLzqYb1DZW8JImtV5%2B2C4LEI%2B6Rwc1qGYk; aQQ_hzweb_uid=12055346; aQQ_haozuusername=%E6%88%9A%E5%8D%87%E8%BE%89%7Cbroker%7Chttp%3A%2F%2Fmy.anjuke.com%2Fmy%2Fhome%2F%7Chttp%3A%2F%2Fagent.anjuke.com%2Fmy%2Flogout%2F; lui=12055346%3A2; ajk_member_name=1419843257mex; mlta=%7B%22mltn%22%3A%7B%22251462%22%3A%5B%226729849809171656192%3E1%3E1437466679385%3E1%3E1437466679385%3E171742900422227884%3E1431568342670%22%2C1453018668102%5D%7D%2C%22mlti%22%3A%7B%22251462%22%3A%5B%22143156829879756680%22%2C1447120298797%5D%7D%2C%22mlts%22%3A%7B%22251462%22%3A%5B%224%3Ebaidu.com%22%2C1451211077895%5D%7D%2C%22mltmapping%22%3A%7B%220%22%3A%5B1%2C1440058668105%5D%7D%7D; propertys=540fkd-nshihc_; lps=http%3A%2F%2Fnanjing.anjuke.com%2F%7C; sessid=E1B6E1AB-83C6-D2AB-CE56-BA45E1A66ABC; ctid=19; __xsptplus8=8.68.1438758177.1438765556.57%232%7Cwww.baidu.com%7C%7C%7C%7C%23%23OPJOVDLJbV3W0iaxw_3E3dt0YjVgu4Qy%23; aQQ_ajkguid=1CB5875C-C3AC-EE43-4E39-25468B8ABB97; isp=true; twe=2; Hm_lvt_c5899c8768ebee272710c9c5f365a6d8=1438765185,1438765242,1438765545,1438765556; Hm_lpvt_c5899c8768ebee272710c9c5f365a6d8=1438766422"
    );

    // 初始化一个 cURL 对象
    $curl = curl_init();

    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);

    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置header

    // 设置header显示方式
    curl_setopt($curl, CURLOPT_HEADER, 0);

    // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    // 运行cURL，请求网页
    $data = curl_exec($curl);

    // 关闭URL请求
    curl_close($curl);

    return gzdecode($data);
  }

  public static function record($msg, $content, $filename)
  {
    $msg = '[' . date("Y-m-d H:i:s") . ']' . ' [message]' . $msg . ' [data]' . serialize($content) . "\r\n";
    //$msg = '['.  date("Y-m-d H:i:s").']'.' '.iconv('GBK','UTF-8',urldecode($content))."\r\n";
    $filename = PUBLIC_LIBRARIES_PATH . 'log/' . $filename . '_' . date("Y-m-d") . '.log';
    @file_put_contents($filename, $msg, FILE_APPEND);
  }
}
