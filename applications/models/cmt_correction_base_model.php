<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 楼盘业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Community_base_model CLASS
 *
 * 楼盘纠错信息管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Cmt_correction_base_model extends MY_Model
{
  /**
   * 楼盘纠错表名称
   * @var string
   */
  private $_cmt_tbl = 'cmt_correction';

  /**
   * 楼盘图库表名称
   * @var string
   */
  private $_cmt_img_tbl = 'cmt_img';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置楼盘纠错表名称
   *
   * @access  public
   * @param  string $tblname 楼盘表名称
   * @return  void
   */
  public function set_cmt_tbl($tblname)
  {
    $this->_cmt_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取楼盘纠错表名称
   *
   * @access  public
   * @param  void
   * @return  string 区属表名称
   */
  public function get_cmt_tbl()
  {
    return $this->_cmt_tbl;
  }


  /**
   * 设置楼盘图片表名称
   *
   * @access  public
   * @param  string $tblname 楼盘图片表名称
   * @return  void
   */
  public function set_cmt_img_tbl($tblname)
  {
    $this->_cmt_img_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取楼盘图片表名称
   *
   * @access  public
   * @param  void
   * @return  string 楼盘图片表名称
   */
  public function get_cmt_img_tbl()
  {
    return $this->_cmt_img_tbl;
  }


  /**
   * 设置需要查询的字段
   * @param array $select_fields
   */
  public function set_select_fields($select_fields)
  {
    $select_fields_str = '';

    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
    }

    $this->select_fields = $select_fields;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->select_fields;
  }

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }


  /**
   * 添加楼盘纠错信息
   * @param array $paramlist 楼盘字段
   * @return insert_id or 0
   */
  function add_cmt_correction($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_tbl, $paramlist);//插入数据
      if (($this->db_city->affected_rows()) >= 1) {
        $result = $this->db_city->insert_id();//如果插入成功，则返回插入的id
      } else {
        $result = 0;    //如果插入失败,返回0
      }
    } else {
      $result = 0;
    }

    return $result;
  }

  /**
   * 获得楼盘纠错信息总数
   * @param array $where where字段
   * @return string 楼盘信息总数
   */
  public function get_cmt_correction_num($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->_cmt_tbl, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }

  /**
   * 筛选楼盘纠错信息
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_cmt_correction($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city', $order_by = array('creattime', 'desc'))
  {
    $comm = $this->get_data(array('form_name' => $this->_cmt_tbl, 'where' => $where, 'like' => $like, 'order_by' => $order_by, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  /**
   * 根据纠错信息ID,提交数据,修改相关信息详情
   * @param string $id 纠错信息ID
   * @param array $paramlist 修改字段
   * @return 0 or 1
   */
  function modify_cmt_correction($id, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $id), $paramlist, 'db_city', $this->_cmt_tbl);
    return $result;
  }

  /**
   * 根据楼盘ID获取楼盘的图片
   * @param int $cmt_id 楼盘ID
   *
   * @return array
   */
  public function find_cmt_pic_by($cmt_id)
  {
    $this->dbback_city->select('id,image,is_surface,cmt_id');
    $this->dbback_city->where('cmt_id', $cmt_id);
    $this->dbback_city->order_by('id', 'DESC');
    return $this->dbback_city->get($this->_cmt_img_tbl)->result_array();
  }


}

/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
