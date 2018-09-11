<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * autocollect_model CLASS
 *
 * 自动采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          cc
 */
class Autocollect_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->collect_sell = 'collect_sell';
    $this->collect_rent = 'collect_rent';
  }

  /**
   * curl采集
   */
  public function vcurl($url, $compress = '')
  {
    $tmpInfo = '';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if ($compress != '') {
      curl_setopt($curl, CURLOPT_ENCODING, $compress);
    }
    $tmpInfo = curl_exec($curl);
    curl_close($curl);
    return $tmpInfo;
  }

  /**
   * 通过房源图片url来采集图片到本地
   * 2015.6.6 cc
   */
  public function get_pic_url($url, $area)
  {
    $urllist = getimagesize($url);
    if ($urllist['mime'] == 'image/jpeg') {
      $form = ".jpg";
    } else if ($urllist['mime'] == 'image/png') {
      $form = ".png";
    } else {
      $form = ".jpg";
    }
    $filename = md5($url) . $form;
    $localfile = "applications/mls/photo/" . $filename;
    $img = $this->get_html($url);
    if (!$img) {
      return false;
    }
    $fp = @fopen($localfile, "w");   //此处可加上图片存放路径
    fwrite($fp, $img);
    fclose($fp);
    if (!file_exists($localfile)) {
      return false;
    }
    $_FILES['photo']['name'] = $filename;
    $_FILES['photo']['type'] = 'image/pjpeg';
    $_FILES['photo']['tmp_name'] = $localfile;
    $_FILES['photo']['error'] = 0;
    $_FILES['photo']['size'] = filesize($localfile);
    $this->load->library('UploadFile', array('filename' => 'photo'), 'uf');
    $this->uf->setFileType("jpg|gif|png");
    $this->uf->setMaxSize('5120');
    $this->uf->setUploadType("");
    $this->uf->setSaveDir("/" . $area . "mls/");
    $this->uf->setWatermark(true);
    $this->uf->setWatermarkType(2);
    $this->uf->setWatermarkPosition("rb");
    $this->uf->setWatermarkImage(MLS_SOURCE_URL . "/common/images/sy4.png");//水印
    $this->uf->setImageCut(true);
    $this->uf->setImageWidth(640);
    $this->uf->setImageHeight(480);
    $this->uf->setResizeImage(true);
    $this->uf->setResizeWidth(130);
    $this->uf->setResizeHeight(100);

    $this->uf->setAudit(true);
    $this->uf->setAuditWidth(200);
    $this->uf->setAuditHeight(150);
    $this->uf->setExtention($form);
    //把图片信息保存到临时表中
    if ($this->uf->upload()) {
      $picurl = $this->uf->getResizeImageURL();//缩略图片地址
    }
    @unlink($localfile);
    return $picurl;
  }

  public function get_html($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
  }

  /**
   * 替换空格字符串等
   */
  public function con_replace($str)
  {
    $patterns[0] = "/	/";
    $patterns[1] = "/\n/";
    $patterns[2] = "/\r/";
    $patterns[3] = "/ /";
    $patterns[4] = "/&nbsp;/";
    $replacements[0] = "";
    $replacements[1] = "";
    $replacements[2] = "";
    $replacements[3] = "";
    $replacements[4] = "";
    $arr = preg_replace($patterns, $replacements, $str);
    return $arr;
  }

  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的出售信息条数
   */
  public function get_count_by_cond($cond_where = '', $form_name = '')
  {
    $count_num = 0;
    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $this->dbback_city->distinct();
      $count_num = $this->dbback_city->count_all_results($form_name);
    }
    return intval($count_num);
  }

  /**
   * 查询出售列表中数据
   * 2015.9.15
   * cc
   */
  public function select_collect_sell($where, $start = 0, $limit = 10, $order_key = 'id', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('collect_sell');
    $this->dbback_city->where($where);
    //排序条件
    if (is_full_array($order_key)) {
      foreach ($order_key as $k => $val) {
        $this->dbback_city->order_by($k, $val);
      }
    } else {
      $this->dbback_city->order_by($order_key, $order_by);
    }

    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  public function select_collect_rent($where, $start = 0, $limit = 10, $order_key = 'releasetime', $order_by = 'DESC')
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('collect_rent');
    $this->dbback_city->where($where);
    //排序条件
    if (is_full_array($order_key)) {
      foreach ($order_key as $k => $val) {
        $this->dbback_city->order_by($k, $val);
      }
    } else {
      $this->dbback_city->order_by($order_key, $order_by);
    }
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 添加出售列表
   * 2015.9.14
   * cc
   */
  public function add_collect_sell($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->collect_sell);
    return $result;
  }

  public function add_collect_rent($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->collect_rent);
    return $result;
  }

  /**
   * 清空出售列表
   * 2015.9.15
   * cc
   */
  public function del_collect_sell($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->del($data, $database, $this->collect_sell);
    return $result;
  }

  public function del_collect_rent($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->del($data, $database, $this->collect_rent);
    return $result;
  }

  //导入列表插入数据库 :wjy
  public function add_list_indata($act, $data_list)
  {
    $table = ($act == 'sell') ? $this->collect_sell : $this->collect_rent;
    $num = count($data_list);
    $step = 50;  //该值最佳 50-100
    for ($i = 0; $i < $num; $i += $step) {
      $param = array_slice($data_list, $i, $step);
      $res = $this->db_city->insert_batch($table, $param);
    }
    return $num;
  }

  //html转义字符去除 :wjy
  public function special_replace($str)
  {
    $patterns[0] = '/&[A-Za-z]+;/';
    $patterns[1] = '/	/';
    $patterns[2] = '/\n/';
    $patterns[3] = '/\r/';
    $replacements[0] = ' ';
    $replacements[1] = ' ';
    $replacements[2] = ' ';
    $replacements[3] = ' ';
    $arr = preg_replace($patterns, $replacements, $str);
    return $arr;
  }
}
