<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

/**
 * 图片处理类
 *
 * @author sun
 */
class Pic_model extends MY_Model
{

  /**
   * 上传文件数组_FILES[$filename]的名称
   * @var string
   */
  private $_filename = 'Filedata';

  /**
   * 上传文件允许的大小 默认2M
   * @var int  2M
   */
  private $_max_size = 5120;

  /**
   * 文件保存的目录
   * @var string
   */
  private $_save_dir = '/shmls/';

  /**
   * 是否默认加水印
   * @var bollean 默认开启
   */
  private $_water_mark_status = true;

  /**
   * 是否缩放原图
   * @var bollean
   */
  private $_image_cut_status = true;

  /**
   * 缩放图片宽度
   * @var int
   */
  private $_image_width = 640;

  /**
   * 缩放图片高度
   * @var int
   */
  private $_image_height = 480;

  /**
   * 是否生成调整图
   * @var bollean 默认开启
   */
  private $_resize_image_status = true;

  /**
   * 调整图片宽度
   * @var int
   */
  private $_resize_width = 130;

  /**
   * 调整图片高度
   * @var int
   */
  private $_resize_height = 100;

  /**
   * 后台审核用的缩略图
   * @var bollean 默认开启
   */
  private $_audit_status = true;

  /**
   * 缩略图宽度
   * @var int
   */
  private $_audit_width = 400;

  /**
   * 缩略图高度
   * @var int
   */
  private $_audit_height = 330;

  /**
   * 图片上附加内容 例如:此图片由某经纪人提供
   * @var string
   */
  private $_other_string = '';


  /**
   * 返回的数据结果数组
   * @var array
   */
  private $result = array('result' => '', 'msg' => '', 'data' => '');

  /**
   * 设置$_FILE数组的名称
   * @param string $filename 名称
   */
  public function set_filename($filename)
  {
    $this->_filename = $filename;
  }

  /**
   * 获取$_FILE数组的名称
   * @return string
   */
  public function get_filename()
  {
    return $this->_filename;
  }

  /**
   * 设置上传文件允许的大小
   * @param int $max_size 文件大小
   */
  public function set_max_size($max_size)
  {
    $this->_max_size = $max_size;
  }

  /**
   * 获取上传文件允许的大小
   * @return int
   */
  public function get_max_size()
  {
    return $this->_max_size;
  }

  /**
   * 设置上传文件目录
   * @param string $save_dir 目录名称
   */
  public function set_save_dir($save_dir)
  {
    $this->_save_dir = $save_dir;
  }

  /**
   * 获取上传文件目录
   * @return string
   */
  public function get_save_dir()
  {
    return $this->_save_dir;
  }

  /**
   * 设置图片水印
   * @param string $water_mark_status 水印
   */
  public function set_water_mark($water_mark_status)
  {
    $this->_water_mark_status = $water_mark_status;
  }

  /**
   * 获取图片水印
   * @return string
   */
  public function get_water_mark()
  {
    return $this->_water_mark_status;
  }

  /**
   * 设置是否开启图片缩放功能
   * @param boolean $image_cut_status true or false
   */
  public function set_image_cut($image_cut_status)
  {
    $this->_image_cut_status = $image_cut_status;
  }

  /**
   * 获取是否开启图片缩放功能
   * @return boolean
   */
  public function get_image_cut()
  {
    return $this->_image_cut_status;
  }

  /**
   * 设置缩放图片的宽度
   * @param int $width 宽度
   */
  public function set_image_width($width)
  {
    $this->_image_width = $width;
  }

  /**
   * 获取缩放图片的宽度
   * @return int
   */
  public function get_image_width()
  {
    return $this->_image_width;
  }

  /**
   * 设置缩放图片的高度
   * @param int $height
   */
  public function set_image_height($height)
  {
    $this->_image_height = $height;
  }

  /**
   * 获取缩放图片的高度
   * @return int
   */
  public function get_image_height()
  {
    return $this->_image_height;
  }

  /**
   * 设置是否生成调整图
   * @param boolean $resize_image_status true or false
   */
  public function set_resize_image($resize_image_status)
  {
    $this->_resize_image_status = $resize_image_status;
  }

  /**
   * 获取是否生成调整图
   * @return boolean
   */
  public function get_resize_image()
  {
    return $this->_resize_image_status;
  }

  /**
   * 设置生成调整图的宽度
   * @param int $width 宽度
   */
  public function set_resize_width($width)
  {
    $this->_resize_width = $width;
  }

  /**
   * 获取生成调整图的宽度
   * @return int
   */
  public function get_resize_width()
  {
    return $this->_resize_image_status;
  }

  /**
   * 设置生成调整图的高度
   * @param int $height 高度
   */
  public function set_resize_height($height)
  {
    $this->_resize_height = $height;
  }

  /**
   * 获取生成调整图的高度
   * @return int
   */
  public function get_resize_height()
  {
    return $this->_resize_height;
  }

  /**
   * 设置是否生成后台审核用的缩略图
   * @param boolean $audit_status true or false
   */
  public function set_audit($audit_status)
  {
    $this->_audit_status = $audit_status;
  }

  /**
   * 获取是否生成后台审核用的缩略图
   * @return boolean
   */
  public function get_audit()
  {
    return $this->_audit_status;
  }

  /**
   * 设置后台审核用的缩略图的宽度
   * @param int $width 宽度
   */
  public function set_audit_width($width)
  {
    $this->_audit_width = $width;
  }

  /**
   * 获取后台审核用的缩略图的宽度
   * @return int
   */
  public function get_audit_width()
  {
    return $this->_audit_width;
  }

  /**
   * 设置后台审核用的缩略图的高度
   * @param int $height 高度
   */
  public function set_audit_height($height)
  {
    $this->_audit_height = $height;
  }

  /**
   * 获取后台审核用的缩略图的高度
   * @return int
   */
  public function get_audit_height()
  {
    $this->_audit_height;
  }

  /**
   * 设置图片上附加内容
   * @param string $other_string 内容
   */
  public function set_other_string($other_string)
  {
    $this->_other_string = $other_string;
  }

  /**
   * 获取图片上附加内容
   * @return string
   */
  public function get_other_string()
  {
    return $this->_other_string;
  }

  /**
   * 类的初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 获取临时图片的信息
   * @param string $filename 图片上传时input名称
   * @return string
   */
  private function get_temp_pic($filename)
  {
    return $_FILES[$filename]["tmp_name"];
  }

  /**
   * 检查是否有图片上传
   * @param string $filename 图片上传时input名称
   * @return boolean
   */
  private function check_pic_isupload($filename)
  {
    if (isset($_FILES[$filename]['nocheck']) && 'house365' == $_FILES[$filename]['nocheck']) {
      return true;
    } else if (!isset($_FILES[$filename]) || !is_uploaded_file($this->get_temp_pic($filename))
      || $_FILES[$filename]["error"] != 0
    ) {
      $this->result['result'] = 0;
      $this->result['msg'] = '未知文件，上传失败';
      return false;
    } else {
      return true;
    }
  }

  /**
   * 获取图片上传的类型
   * @param string $temp_pic 临时文件路径
   * @return string  jpg and png
   */
  private function get_pic_type($temp_pic)
  {
    $img_info = getimagesize($temp_pic);
    return $img_info['mime'] == "image/jpeg" ? "jpg" : "png";
  }

  /**
   * 判断图片是否来自设备
   * 1代表是 0代表否
   * @param string $temp_pic 临时文件路径
   * @return int
   */
  private function get_pic_from_device($temp_pic)
  {
    $from_maker = 0;

    if (function_exists("exif_read_data")) {
      $exif = @exif_read_data($temp_pic, 0, true);
      if ($exif) {
        foreach ($exif as $key => $section) {
          foreach ($section as $name => $val) {
            if ($key == 'IFD0' && $name == 'Model') {
              $from_maker = 1; //来源于设备
              break;
            }
          }
        }
      }
    }
    return $from_maker;
  }

  /**
   * 获取图片信息并给起md5加密
   * @param string $temp_pic 临时文件路径
   * @return sting md5加密后的字符串
   */
  private function set_pic_md($temp_pic)
  {
    $bin = '';
    $f = fopen($temp_pic, "r");
    fseek($f, 0);
    $bin .= fread($f, 100);
    fseek($f, 165, SEEK_CUR);
    $bin = fread($f, 50);
    fseek($f, 365, SEEK_CUR);
    $bin .= fread($f, 50);
    fseek($f, 565, SEEK_CUR);
    $bin .= fread($f, 50);
    fseek($f, 765, SEEK_CUR);
    $bin .= fread($f, 50);
    fseek($f, -100, SEEK_END);
    $bin .= fread($f, 100);
    $file_md5 = md5($bin);
    return $file_md5;
  }


  /**
   * 上传图片，并返回上传后的地址
   */
  public function common_upload()
  {
    header('Content-Type:text/html;charset=utf-8');

    //设置文件上传时session_id值
    //$this->set_session_id($this->input->post('PHPSESSID'));

    $filename = $this->get_filename();
    //获取临时图片信息
    $temp_pic = $this->get_temp_pic($filename);
    //判断上传有效的图片
    $pic_validate = $this->check_pic_isupload($filename);
    if (!$pic_validate) {
      return false;
    }

    //设置上传图片的属性并且上传
    $this->load->library('UploadFile', array('filename' => $filename), 'uf');
    $this->uf->setExtention($pic_type);
    $this->uf->setFileType("jpg|gif|png");
    $this->uf->setMaxSize($this->_max_size);
    $this->uf->setUploadType("ftp");
    $this->uf->setSaveDir($this->_save_dir);
    $this->uf->setImageCut($this->_image_cut_status);
    $this->uf->setImageWidth($this->_image_width);
    $this->uf->setImageHeight($this->_image_height);
    $this->uf->setResizeImage($this->_resize_image_status);
    $this->uf->setResizeWidth($this->_resize_width);
    $this->uf->setResizeHeight($this->_resize_height);

    if ($this->get_other_string()) {
      $this->uf->otherstring = $this->get_other_string();
    }

    if ($this->uf->upload()) {
      $picurl = $this->uf->getResizeImageURL();//缩略图片地址
    }
    return $picurl;
  }


  /**
   * 上传时设置session_id
   * @param string $session_id
   */
  private function set_session_id($session_id)
  {
    if ($session_id) {
      session_id($session_id);
    }
  }

  /**
   * 上传图片，并返回上传后的地址
   */
  public function upload($type = '')
  {
    $filename = $this->get_filename();
    //获取临时图片信息
    $temp_pic = $this->get_temp_pic($filename);
    //判断上传有效的图片
    $pic_validate = $this->check_pic_isupload($filename);
    if (!$pic_validate) {
      return false;
    }
    //获取上传图片的类型
    $pic_type = $this->get_pic_type($temp_pic);
    //设置上传图片的属性并且上传
    $this->load->library('UploadFile', array('filename' => $filename), 'uf');
    $this->uf->setExtention($pic_type);
    $this->uf->setFileType("jpg|gif|png");
    $this->uf->setMaxSize($this->_max_size);
    $this->uf->setUploadType("ftp");
    $this->uf->setSaveDir($this->_save_dir);
    if ($type == "house") {
      $this->uf->setWatermark($this->_water_mark_status);
      $this->uf->setWatermarkType(2);
      $this->uf->setWatermarkPosition("rb");
      $this->uf->setWatermarkImage(MLS_SOURCE_URL . "/common/images/sy4.png");//水印
    }
    $this->uf->setImageCut($this->_image_cut_status);
    $this->uf->setImageWidth($this->_image_width);
    $this->uf->setImageHeight($this->_image_height);
    $this->uf->setResizeImage($this->_resize_image_status);
    $this->uf->setResizeWidth($this->_resize_width);
    $this->uf->setResizeHeight($this->_resize_height);

    $this->uf->setAudit($this->_audit_status);
    $this->uf->setAuditWidth($this->_audit_width);
    $this->uf->setAuditHeight($this->_audit_height);
    if ($this->get_other_string()) {
      $this->uf->otherstring = $this->get_other_string();
    }
    //把图片信息保存到临时表中
    if ($this->uf->upload()) {
      //echo 'aa';
      $picurl = $this->uf->getResizeImageURL();//缩略图片地址
    }
    //返回图片的地址
    return $picurl;
  }


  /**
   * 获取图片保存到哪张表里
   * @param type $tbl 表名
   * @param type $id 房源编号
   * @return string
   */
  public function get_upload_table_name($tbl, $id)
  {
    if ($tbl == 'sell' && $id < 23972101) return 'upload0';
    if ($tbl == 'rent' && $id < 11907211) return 'upload0';

    if ($tbl == 'sell' && ($id >= 23972101 && $id < 27029364)) return 'upload1';
    if ($tbl == 'rent' && ($id >= 11907211 && $id < 13224200)) return 'upload1';

    if ($tbl == 'sell' && ($id >= 27029364 && $id < 31695721)) return 'upload2';
    if ($tbl == 'rent' && ($id >= 13224200 && $id < 14495163)) return 'upload2';

    if ($tbl == 'sell' && ($id >= 31695721 && $id < 34963370)) return 'upload3';
    if ($tbl == 'rent' && ($id >= 14495163 && $id < 15172611)) return 'upload3';

    if ($tbl == 'sell' && ($id >= 34963370 && $id < 38640564)) return 'upload4';
    if ($tbl == 'rent' && ($id >= 15172611 && $id < 15871631)) return 'upload4';

    if ($tbl == 'sell' && ($id >= 38640564 && $id < 42138930)) return 'upload5';
    if ($tbl == 'rent' && ($id >= 15871631 && $id < 16675751)) return 'upload5';

    if ($tbl == 'sell' && ($id >= 42138930 && $id < 45947999)) return 'upload';
    if ($tbl == 'rent' && ($id >= 16675751 && $id < 43871899)) return 'upload';

    if ($tbl == 'sell' && ($id >= 45947999 && $id < 49830000)) return 'upload6';
    //49825505
    if ($tbl == 'rent' && ($id >= 43871899 && $id < 45040000)) return 'upload6';
    //45038726
    return 'upload7';
  }

  /**
   * 插入房源图片
   * @param array $insert_data 房源数据数组
   * @return int
   */
  public function insert_house_pic($insert_data, $tbl = 'upload')
  {
    if ($this->db_city->insert($tbl, $insert_data)) {
      return $this->db_city->insert_id();
    }
  }

  /**
   * 获取某条房源的图片
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function find_house_pic_by($tbl, $house_id, $sort = '')
  {
    $uploadtable = 'upload';
    $this->db_city->select('id,type,url,is_top');
    $this->db_city->where('tbl', $tbl);
    $this->db_city->where('rowid', $house_id);
    $this->db_city->order_by('is_top', 'DESC');
    if ($sort !== '') {
      $this->db_city->where('sort', $sort);
    }
    return $this->db_city->get($uploadtable)->result_array();
  }

  /**
   * 获取某条房源的图片
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function find_house_pic_by_ids($tbl, $ids)
  {
    if ($ids) {
      $ids = trim($ids, ',');
      $this->db_city->select('id,type,url');
      $where = "id in ($ids) ";
      $this->db_city->where($where);
      return $this->db_city->get($tbl)->result_array();
    }

  }

  /**
   * 获取某条房源的图片
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function count_house_pic_by_cond($cond_where)
  {
    $this->db_city->where($cond_where);
    $num = $this->db_city->count_all_results('upload');
    return $num;
  }

  /**
   * 删除某条房源图片
   * @param string $tblname 表名
   * @param int $house_id 房源编号
   * @param string $tbl 表名
   */
  public function del_house_pic_by($tblname, $house_id, $tbl = 'upload')
  {
    $this->db_city->where('tbl', $tblname);
    $this->db_city->where('rowid', $house_id);
    $this->db_city->delete($tbl);
    return $this->db_city->affected_rows() > 0 ? true : false;
  }

  /**
   * 获取临时图片表数据
   * @param int $id id编号
   * @return array 一维数组组成的记录
   */
  public function find_temp_by_id($id)
  {
    $this->db_city->select('from_device, file_md5');
    $this->db_city->where('id', $id);
    return $this->db_city->get($this->_insert_temp_tablename)->row_array();
  }


  /**
   * 获取某条房源的室内封面图片
   * @param string $tbl 表名
   * @param int $house_id 房源编号
   * @param int $sort 图片类型
   * @return array
   */
  public function find_house_top_pic($tbl, $house_id)
  {
    $uploadtable = 'upload';
    $this->db_city->select('id,type,url,is_top');
    $this->db_city->where('tbl', $tbl);
    $this->db_city->where('rowid', $house_id);
    $this->db_city->where('is_top', 1);
    return $this->db_city->get($uploadtable)->result_array();
  }

  /**
   * 删除房源图片
   * @param string $tblname 表名
   * @param int $house_id 房源编号
   * @param string $tbl 表名
   */
  public function del_pic_by_ids($ids, $tbl = 'upload')
  {
    if ($ids) {
      $ids = trim($ids, ',');
      $where = "id in ($ids)";
      $this->db_city->where($where);
      $this->db_city->delete($tbl);
      return $this->db_city->affected_rows() > 0 ? true : false;
    }
  }
}
