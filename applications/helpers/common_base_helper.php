<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 系统基础函数文件
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      HOUSE365 ESF Dev Team
 * @link        http://nj.sell.house365.com/
 */


/**
 * 自动加载model类库
 * @param   string $model model类名
 * @update  2014/6/6 esf dev team
 */
if (!function_exists('load_m')) {
    function load_m($model)
    {
        $model = $model != '' ? strtolower($model) : '';
        $model_file_url = PUBLIC_MODEL_PATH . $model . '.php';
        if ($model != '' && file_exists($model_file_url)) {
            require_once($model_file_url);
        }
    }
}


/**
 * 获取用户IP地址
 * @param void
 * @return string  分页字符串
 * @author esf dev team 2014-02-26
 */
if (!function_exists('get_ip')) {
  function get_ip()
  {

    /*$ip = $_SERVER["REMOTE_ADDR"];

    if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $tip = split(',' , $_SERVER["HTTP_X_FORWARDED_FOR"] );
        $ip = $tip['0'];
    }

    return( trim($ip) );*/

    if (getenv('HTTP_CLIENT_IP')) {
      $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
      $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
      $ip = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
      $ip = getenv('HTTP_FORWARDED_FOR');

    } elseif (getenv('HTTP_FORWARDED')) {
      $ip = getenv('HTTP_FORWARDED');
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }

    $tip = explode(',', $ip);
    $ip = $tip['0'];

    return trim($ip);
  }
}


//通过框架路由类定位
if (!function_exists('where_am_i')) {
  function where_am_i()
  {

    global $RTR;
    //通过框架路由类定位
    $class = $RTR->fetch_class();
    $method = $RTR->fetch_method();
    $method = $method == '' ? 'index' : $method;
    return array('class' => $class, 'method' => $method);
  }
}


/**
 * 个人用户中心分页
 * @param int $page_now 当前页
 * @param int $pages 总页数
 * @param int $show_num 分页显示的页数
 * @param string $form 表单名
 * @return string  分页字符串
 * @author esf dev team 2014-02-26
 */
function helper_pagination($page_now, $pages, $numrows, $show_mum = 7, $form = "search_form")
{
  $form = 'document.' . $form;
  $ss = '';

  //如果页数小于1则返回空
  if ($pages <= 1 || !is_numeric($page_now)) {
    return '';
  } else {
    $pre = $page_now == 1 ? $page_now : $page_now - 1; //前一页
    $end = $page_now == $pages ? $pages : $page_now + 1; //下一页

    //首页、前一页
    if ($page_now == 1) {
      $ss .= "<p class='pag'><a class='lin prev'>首 页</a></p>";
      $ss .= "<p class='pag'><a class='lin prev'>&lt;上一页</a></p>";
    } else {
      $first_page = 1; //第一页
      $ss .= "<p class='pag'><a class='lin prev' onclick=\"$form.page.value=" . $first_page . ";$form.submit();return false;\">首 页</a></p>";
      $ss .= "<p class='pag'><a class='lin prev' onclick=\"$form.page.value=" . $pre . ";$form.submit();return false;\">&lt;上一页</a></p>";
    }

    //前三页
    if ($page_now > 3) {
      if ($pages - $page_now <= 3) {
        $num = $page_now - ($show_mum - ($pages + 1 - $page_now));
        $num = $num >= 1 ? $num : 1;

        for ($i = $num; $i < $page_now; $i++) {
          $ss .= "<p class='pag'><a class='lin' onclick=\"$form.page.value=" . $i . ";$form.submit();return false;\">{$i}</a></p>";
        }
      } else {
        for ($i = $page_now - 3; $i < $page_now; $i++) {
          $ss .= "<p class='pag'><a class='lin' onclick=\"$form.page.value=" . $i . ";$form.submit();return false;\">{$i}</a></p>";
        }
      }
    } else {
      $show_num = $pages > $page_now ? $page_now : $pages;
      for ($i = 1; $i < $show_num; $i++) {
        $ss .= "<p class='pag'><a class='lin' onclick=\"$form.page.value=" . $i . ";$form.submit();return false;\">{$i}</a></p>";
      }
    }

    //当前页面
    $ss .= "<p class='pag'><a  onclick=\"$form.page.value=" . $page_now . ";$form.submit();return false;\" class='lin current'>{$page_now}</a></p>";

    //后三页数
    if ($pages - $page_now > 5) {
      //当前页数小于等于5时，强制显示到第7页
      for ($i = $page_now + 1; $i <= $page_now + 5; $i++) {
        $ss .= "<p class='pag'><a  class='lin' class='lin' onclick=\"$form.page.value=" . $i . ";$form.submit();return false;\">{$i}</a></p>";
      }
    } else {
      for ($i = ($page_now + 1); $i <= $pages; $i++) {
        $ss .= "<p class='pag'><a class='lin' onclick=\"$form.page.value=" . $i . ";$form.submit();return false;\" >{$i}</a></p>";
      }
    }

    //下一页、尾页
    if (($page_now == $pages) || ($pages == 0)) {
      $last_page = $pages;
      $ss .= "<p class='pag'><a class='lin next'>下一页&gt;</a></p>";
      //$ss .= "<p class='pag'><a class='p-next'>末  页</a></p>";
    } else {
      $next_page = $page_now + 1;
      $last_page = $pages;
      $ss .= "<p class='pag'><a  onclick=\"$form.page.value=" . $next_page . ";$form.submit();return false;\" class='lin next'>下一页&gt;</a></p>";
      //$ss .= "<p class='pag'><a  onclick=\"$form.page.value=".$last_page.";$form.submit();return false;\" class='lin next'>末 页</a></p>";
    }
  }

  return $ss;
}


/**
 * 内页ajax新分页
 * @param int $page 当前页
 * @param int $pages 总页数
 * @return mixed
 */
function zsb_pagenavi_ajax($page, $pages, $func_callback)
{
  $form = 'document.search_form';

  $ss = '';
  if ($pages <= 1 || !is_numeric($page)) {//如果页数小于1则返回NULL
    return '';
  } else {
    $pre = $page == 1 ? $page : $page - 1;//前一页
    $end = $page == $pages ? $pages : $page + 1;////下一页

    if ($page == 1) {
      $ss .= '<p class="pag"><a class="lin prev" href="###">&lt;上一页</a></p>';
    } else {
      $first_page = 1;//第一页
      $ss .= '<p class="pag"><a class="lin prev" onclick="' . $func_callback . '(1)" href="###">&lt;上一页</a></p>';
    }

    for ($i = 1; $i <= $pages; $i++) {
      if ($i == $page) {
        $css = 'style="color:#FF8106;"';
      } else {
        $css = '';
      }

      $ss .= '<p class="pag"> <a ' . $css . ' class="lin" href="###" onclick="' . $func_callback . '(' . $i . ')">0' . $i . '</a></p>';
    }

    if (($page == $pages) || ($pages == 0)) {
      $last_page = $pages;
      $ss .= '<p class="pag"><a class="lin next" href="###">下一页&gt;</a></p>';
    } else {
      $next_page = $page + 1;
      $ss .= "<p class='pag'><a class='lin next' href='###' onclick='$func_callback(" . $next_page . ")'>下一页&gt;</a></p>";
    }
  }

  return $ss;
}


/**
 *部署GA统计代码
 * @param string $ga_page GA统计代码
 * @param string $ga_city 页面所属城市
 * @param string $ga_channel 页面所属频道
 * @return string  ga统计代码
 */
function get_ga_js_code($ga_page, $ga_city = 'hz', $ga_channel = 'zsb')
{
  $ga_js_code = '';

  if ($ga_page != '') {
    $last_sign = substr($ga_page, -1, 1);
    if ($last_sign == '/') {
      $ga_page = substr($ga_page, 0, -1);
    }

    $ga_js_code = "<script type='text/javascript'>
            var ga_city = '" . $ga_city . "';
            var ga_channel = '" . $ga_channel . "';
            var ga_page= '" . $ga_page . "';
            </script>
            <script language='javascript'>var website = 2;</script>
            <script language='javascript' src='http://stat.house365.com/365count.js'></script>";
  }

  return $ga_js_code;
}

//把一维数组下标转换指定key的值
if (!function_exists('change_to_key_array')) {
  function change_to_key_array($array, $key)
  {
    $newArray = array();
    if ($array) {
      foreach ($array as $value) {
        if (key_exists($key, $value)) {
          $newArray[$value[$key]] = $value;
        }
      }
    }
    return $newArray;
  }
}

//判断参数是否为有数据的数组
if (!function_exists('is_full_array')) {
  function is_full_array($array)
  {
    return is_array($array) && !empty($array) ? true : false;
  }
}

/* 生成二维码
 * @param $str string 请求的URL地址
 * return string 二维码地址
 */
if (!function_exists('get_qrcode')) {
  function get_qrcode($data = '', $city = '', $level = 'H', $size = 6, $filename = '')
  {
    $data = $data != '' ? urlencode($data) : '';
    $url = MLS_FILE_SERVER_URL . DIRECTORY_SEPARATOR. 'qrcode/index.php?1';
    $url = $data != '' ? $url . '&data=' . $data : $url;
    $url = $city != '' ? $url . '&city=' . $city : $url;
    $url = $level != '' ? $url . '&level=' . $level : $url;
    $url = $size != '' ? $url . '&size=' . $size : $url;
    $url = $filename != '' ? $url . '&filename=' . $filename : $url;

    return curl_get_contents($url);
  }
}

/* 导出生成二维码
 * @param $str string 请求的URL地址
 * return string 二维码地址
 */
if (!function_exists('export_get_qrcode')) {
  function export_get_qrcode($data = '', $city = '', $level = 'H', $size = 6, $filename = '')
  {
    $data = $data != '' ? urlencode($data) : '';
    $url = MLS_FILE_SERVER_URL . DIRECTORY_SEPARATOR. 'qrcode/export.php?1';
    $url = $data != '' ? $url . '&data=' . $data : $url;
    $url = $city != '' ? $url . '&city=' . $city : $url;
    $url = $level != '' ? $url . '&level=' . $level : $url;
    $url = $size != '' ? $url . '&size=' . $size : $url;
    $url = $filename != '' ? $url . '&filename=' . $filename : $url;

    return curl_get_contents($url);
  }
}

/* 实现远程获取和采集内容
 * @param $str string 请求的URL地址
 * return string 请求内容
 */
if (!function_exists('curl_get_contents')) {
  function curl_get_contents($str, $t_url = "")
  {
    $ch = curl_init();
    $t_url = $t_url ? $t_url : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    curl_setopt($ch, CURLOPT_URL, $str);
    curl_setopt($ch, CURLOPT_REFERER, $t_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
  }
}

if (!function_exists('vpost')) {
  function vpost($post_url, $post_fields, $cookie = '')
  {
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $post_url); // 要访问的地址
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields); // Post提交的数据包
    //curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file); // 读取上面所储存的Cookie信息
      // curl_setopt($curl, CURLOPT_PROXY, "ip:1080");
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
      echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话
    return $tmpInfo; // 返回数据
  }
}

/**
 * 处理图片
 * 2015.10.8
 * cc
 */
function changepic($pic = '')
{
  if ($pic != '') {
    if (strstr($pic, '_120x90')) {
      $picurl = str_replace('_120x90', '', $pic);
    } elseif (strstr($pic, '/thumb/')) {
      $picurl = str_replace('/thumb/', '/', $pic);
      // } elseif (strstr($pic, '/big/')){
      //$picurl = str_replace('/big/','/',$pic);
    } else {
      $picurl = $pic;
    }
  } else {
    $picurl = $pic;
  }
  return $picurl;
}

/**
 * 群发处理图片
 * 2016.3.5
 * cc
 */
function changepic_send($pic = '')
{
  if ($pic != '') {
    if (strstr($pic, '_120x90')) {
      $picurl = str_replace('_120x90', '', $pic);
    } elseif (strstr($pic, '/thumb/')) {
      $picurl = str_replace('/thumb/', '/initial/', $pic);
      // } elseif (strstr($pic, '/big/')){
      //$picurl = str_replace('/big/','/',$pic);
    } else {
      $picurl = $pic;
    }
  } else {
    $picurl = $pic;
  }
  return $picurl;
}


function job_start($referer_url)
{
  //开始时间
  $referer_url = urlencode($referer_url);
  $jobs_time_url = 'http://jobsadmin.house365.com/index.php?a=track&m=Api&referer=' . $referer_url . '&start=' . time();
  //echo $jobs_time_url;
  curl_get_contents($jobs_time_url);
}

function job_end($referer_url)
{
  //结束时间
  $referer_url = urlencode($referer_url);
  $jobs_time_url = 'http://jobsadmin.house365.com/index.php?a=track&m=Api&referer=' . $referer_url . '&end=' . time();
  //echo $jobs_time_url;
  curl_get_contents($jobs_time_url);
}

function days_in_month($month, $year)
{
// calculate number of days in a month
  return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
}
/* End of file common_base_helper.php */
/* Location: ./applications/helpers/common_base_helper.php */
