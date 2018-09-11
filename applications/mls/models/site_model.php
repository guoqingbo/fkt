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
 * site_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2014-12-28
 * @author          angel_in_us
 */
class Site_model extends MY_Model
{
  private $imgLogo;

  public function __construct()
  {
    parent::__construct();
    $this->mass_site = 'mass_site';
    $this->mass_site_broker = 'mass_site_broker';
    $this->load->model('broker_model');
    $this->load->library('Curl');

    $this->imgLogo = array(
      'ganji' => 'bar_logo1.gif', 'ganjivip' => 'bar_logo1.gif', '58P' => 'bar_logo2.gif',
      '58W' => 'bar_logo2.gif', 'fang' => 'bar_logo3.gif', 'anjuke' => 'bar_logo5.gif', 'house365' => 'bar_logo4.gif',
      'lianjia' => 'bar_logo8.gif', 'baixing' => 'BAIXING.jpg', 'focus' => 'bar_logo7.gif', 'sina' => 'bar_logo9.gif',
      '360fdc' => 'bar_logo10.gif', 'ffw' => 'bar_logo11.gif', 'ajkvip' => 'bar_logo5.gif', 'fdc' => 'yifang.gif', 'fish' => 'fish.gif',
      'pxf' => 'fish.gif'
    );
  }

  /**
   * 根据 $alias 获取站点logo
   */
  public function get_imglogo($alias = '')
  {
    if ('' == $alias) return $this->imgLogo;
    return isset($this->imgLogo[$alias]) ? $this->imgLogo[$alias] : '';
  }

  //群发 生成临时图片
  function upload_img($url)
  {
    $urllist = getimagesize($url);
    if ($urllist['mime'] == 'image/jpeg') {
      $form = ".jpg";
    } else if ($urllist['mime'] == 'image/png') {
      $form = ".png";
    } else {
      $form = ".jpg";
    }
    $broker_id = $this->broker_info['broker_id'];
    $picname = $broker_id . date('YmdHis') . $form;
    $localfile = "./applications/mls/photo/" . $picname;
    $finalname = dirname(dirname(__FILE__)) . "/photo/" . $picname;

    for ($i = 0; $i < 4; $i++) {
      $nowG = date('G');
      $timeout = $nowG < 13 ? 10 : 30;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $img = curl_exec($ch);
      curl_close($ch);
      if ($img) {
        break;
      }
    }
    if (!$img) {
      return false;
    }

    $fp = @fopen($localfile, "wb");
    fwrite($fp, $img);
    fclose($fp);

    //以下缩略图
    $max_width = $max_height = 800;
    list($intWidth, $intHeight) = getimagesize($finalname);
    $new_width = $intWidth;
    $new_height = $intHeight;
    if ($intWidth > $max_width) //宽度大于最大宽度,设置宽度为最大宽度
    {
      $new_width = $max_width;
      $new_height = round($new_width * $intHeight / $intWidth);
    }
    if ($intHeight > $max_height) //高度大于最大高度,设置高度为最大高度
    {
      $new_width = round($intWidth * $max_height / $intHeight);
      $new_height = $max_height;
    }
    $myimg = imagecreatetruecolor($new_width, $new_height);
    if ($form == '.jpg') {
      imagecopyresampled($myimg, imagecreatefromjpeg($finalname), 0, 0, 0, 0, $new_width, $new_height, $intWidth, $intHeight);
      imagejpeg($myimg, $finalname);
    } else if ($form == '.png') {
      imagecopyresampled($myimg, imagecreatefrompng($finalname), 0, 0, 0, 0, $new_width, $new_height, $intWidth, $intHeight);
      imagePNG($myimg, $finalname);
    }
    //imagecopyresampled($myimg,imagecreatefromjpeg($finalname),0,0,0,0,$new_width,$new_height,$intWidth,$intHeight);
    //imagejpeg($myimg, $finalname);
    return $finalname;
  }

  /**
   * 获取要设置网站的总数量
   * @date      2015-01-21
   * @author       angel_in_us
   */
  function get_site_num($where = array(), $like = array(), $database = 'dbback_city')
  {
    $sell_sum = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }

  /**
   * 获取要设置网站的总数量
   * @date      2015-01-21
   * @author       angel_in_us
   */
  function get_site_broker_num($where = array(), $like = array(), $database = 'dbback_city')
  {
    $sell_sum = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }


  /**
   * 获取要设置网站的信息
   * @date      2015-01-21
   * @author       angel_in_us
   */
  function get_site_info($where = array(), $where_in = array(), $like = array(), $order_by = '', $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 根据经纪人id获取他(她)所开通的群发网站信息
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function get_broker_site($where = array(), $where_in = array(), $like = array(), $offset = 0, $limit = 50, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }


  /**
   * 修改待网站是否启用
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function delete_site_usage($where = array())
  {
    $result = $this->del($where, 'db_city', $this->mass_site_broker);
    return $result;
  }


  /**
   * 经纪人启用对应网站端口
   * @date      2015-01-22
   * @author       angel_in_us
   */
  function add_broker_interface($data = array())
  {
    $result = $this->add_data($data, 'db_city', $this->mass_site_broker);
    return $result;
  }


  /**
   * 根据id查询群发网站信息
   * @date      2015-01-25
   * @author       angel_in_us
   */
  function get_site_byid($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据id查询群发网站信息
   * @date      2015-01-26
   * @author       angel_in_us
   */
  function get_brokerinfo_byids($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据username查询经纪人是否绑定网站
   * @date      2015-01-25
   * @author       angel_in_us
   */
  function check_broker_site($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->mass_site_broker, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据 username 来更新 mass_site_broker 表中的密码
   */
  function update_broker_pwd($where = array(), $arr = array())
  {
    $result = $this->modify_data($where, $arr, 'db_city', $this->mass_site_broker);
    return $result;
  }

  /**
   * 根据 broker_id 获取绑定 群发站点
   */
  function get_mess_site($broker_id, $site = '')
  {
    $data = array();
    if ($broker_id) {
      $this->dbback_city->select($this->mass_site . ".*," . $this->mass_site_broker . ".status as bstatus");
      $where = $this->mass_site_broker . ".broker_id = '$broker_id'";
      if ($site) {
        $where .= " and " . $this->mass_site . ".id in ($site,0)";
      }
      $this->dbback_city->where($where);
      $this->dbback_city->from($this->mass_site_broker);
      $this->dbback_city->join($this->mass_site, "$this->mass_site_broker.site_id =  $this->mass_site.id");
      $data = $this->dbback_city->get()->result_array();
    }
    return $data;
  }


  /**
   * 根据 broker_id 获取绑定 群发站点
   */
  function publish_site($broker_id, $type = '')
  {
    $data = array();
    if ($broker_id) {
      $this->dbback_city->select($this->mass_site . ".*");
      $where = $this->mass_site_broker . ".broker_id = '$broker_id' and " . $this->mass_site_broker . ".status = 1 ";
      $where .= " and " . $this->mass_site . ".is_fix =0 and " . $this->mass_site . ".status = 1 ";
      if ($type != '') {
        $where .= " and " . $this->mass_site . "." . $type;
      }
      $this->dbback_city->where($where);
      $this->dbback_city->from($this->mass_site_broker);
      $this->dbback_city->join($this->mass_site, "$this->mass_site_broker.site_id =  $this->mass_site.id");
      $data = $this->dbback_city->get()->result_array();
    }
    return $data;
  }

  /**
   * 根据 broker_id 获取绑定 群发站点
   */
  function get_all_site($ids = '')
  {
    $data = array();
    $this->dbback_city->select($this->mass_site . ".*");
    $where = $this->mass_site . ".is_fix =0";
    if ($ids) {
      $where .= ' and id in (' . $ids . ') ';
    }
    $this->dbback_city->where($where);
    $this->dbback_city->order_by('status desc');
    $this->dbback_city->from($this->mass_site);
    $data = $this->dbback_city->get()->result_array();
    return $data;
  }
}

/* End of file site_model.php */
/* Location: ./application/mls/models/site_model.php */
