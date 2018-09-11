<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
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
 * Community_model CLASS
 *
 * 楼盘数据模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @author          yzt
 */
class Community_bak_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->mls_community = 'community_bak';
    $this->mls_cmt_img = 'cmt_img';
    $this->mls_district = 'district';
    $this->mls_street = 'street';
  }

  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function getcommunity($offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'select' => array('id', 'build_date'), 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }


  /**
   * 获得楼盘总数
   * @param array $where where字段
   * @return string 楼盘总数
   */
  public function getcommunitynum($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }

  /**
   * 获得楼盘图片总数
   * @param array $where where字段
   * @return string 楼盘总数
   */
  public function get_cmt_img_num($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->mls_cmt_img, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }


  /**
   * 添加楼盘
   * @param array $paramlist 楼盘字段
   * @return insert_id or 0
   */
  function addcommunity($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->mls_community);
    return $result;
  }


  /**
   * 根据楼盘ID,提交数据,修改相关楼盘详情
   * @param string $commid 楼盘ID
   * @param array $paramlist 楼盘修改字段
   * @return 0 or 1
   */
  function modifycommunity($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db_city', $this->mls_community);
    return $result;
  }


  /**
   * 删除楼盘
   * @param string $commid 楼盘ID
   * @return 0 or 1
   */
  function delcommunity($commid = '')
  {
    $result = $this->del(array('id' => $commid), 'db_city', $this->mls_community);
    return $result;
  }


  /**
   * 根据楼盘ID获得楼盘详情
   * @param string $commid 楼盘ID
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_comm_by_id($commid = '')
  {
    $wherecond = array('id' => $commid);
    $commData = $this->get_data(array('form_name' => $this->mls_community, 'where' => $wherecond), 'dbback_city');
    return $commData;
  }


  /**
   * 以楼盘地址模糊查询出楼盘的记录
   * @param string $address 楼盘名称
   * @return array 以楼盘信息组成的多维数组
   */
  public function find_like_by_address($address)
  {
    $like_code = array('address' => $address);
    $select = array('id', 'cmt_name');
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'select' => $select, 'like' => $like_code), 'dbback_city');
    return $comm;
  }

  /**
   * 以楼盘名称模糊查询出楼盘的记录
   * @param string $commname 楼盘名称
   * @return array 以楼盘信息组成的多维数组
   */
  public function find_like_by_commname($commname)
  {
    $like_code = array('cmt_name' => $commname);
    $or_like_code = array('name_spell' => $commname, 'alias_spell' => $commname);
    $select = array('id', 'cmt_name');
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'select' => $select, 'like' => $like_code, 'or_like' => $or_like_code), 'dbback_city');
    return $comm;
  }


  /**
   * 初始化楼盘图库数量表
   *
   * cp后台新增楼盘或者楼盘从待审核状态变成临时（或者正式）状态时，初始化楼盘图库数量表。
   */
  function add_cmtimage($commid)
  {
    $paramlist = array('cmt_id' => $commid);
    $result = $this->add_data($paramlist, 'db_city', $this->mls_cmt_img);
    return $result;
  }


  /**
   * 根据楼盘id获得该楼盘的图片总数、户型图、外景图
   * @param array $where where字段
   * @return array
   */
  public function get_cmt_image_by_cmtid($cmt_id, $database = 'dbback_city')
  {
    $cmt_images = $this->get_data(array('form_name' => $this->mls_cmt_img, 'where' => array('cmt_id' => $cmt_id)), $database);
    $img_data = array('img_num' => 0, 'apart_img_num' => 0, 'Location_img_num' => 0);
    if (is_array($cmt_images) && !empty($cmt_images)) {
      $img_data['img_num'] = count($cmt_images);
      foreach ($cmt_images as $k => $v) {
        if ($v['pic_type'] == 1) {
          $img_data['apart_img_num']++;
        }
        if ($v['pic_type'] == 3) {
          $img_data['Location_img_num']++;
        }
      }
    }
    return $img_data;
  }


  /**
   * 根据参数获得该楼盘的图片信息
   * @param array $where where字段
   * @return array
   */
  public function get_all_cmt_image_by_cmtid($where, $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $cmt_images = $this->get_data(array('form_name' => $this->mls_cmt_img, 'where' => $where, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $cmt_images;
  }

  /**
   * 删除楼盘图片
   * @param string $id 字段
   * @return array
   */
  public function del_cmt_img($id = '')
  {
    $result = $this->del(array('id' => $id), 'db_city', $this->mls_cmt_img);
    return $result;
  }


  /**
   * 添加楼盘图片
   * @param array $paramlist 楼盘图片字段
   * @return 1 or 0
   */
  function add_cmt_image($paramlist = array())
  {
    $result = $this->add_data($paramlist, 'db_city', $this->mls_cmt_img);
    return $result;
  }

  /**
   * 根据楼盘图片ID,提交数据,修改相关楼盘详情
   * @param string $cmt_img_id 楼盘图片ID
   * @param array $paramlist 楼盘修改字段
   * @return 0 or 1
   */
  function modify_cmt_image($cmt_img_id, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $cmt_img_id), $paramlist, 'db_city', $this->mls_cmt_img);
    return $result;
  }


  /**
   * 设置图片为封面
   * @param string $imgid 图片ID
   * @param array $paramlist 楼盘图片修改字段
   * @return insert_id or 0
   */
  function set_cmt_img_surface($imgid, $commid, $imgsrc)
  {
    //设置为封面
    $result = $this->modify_data(array('id' => $imgid), array('is_surface' => 1), 'db_city', $this->mls_cmt_img);
    $where = array('cmt_id' => $commid);
    $all_img_id = $this->get_data(array('form_name' => $this->mls_cmt_img, 'select' => array('id'), 'where' => $where), 'dbback_city');
    $other_img_id = array();
    foreach ($all_img_id as $k => $v) {
      if ($v['id'] != $imgid) {
        $other_img_id[] = $v['id'];
      }
    }
    //设置其它图片的封面字段为0
    foreach ($other_img_id as $k => $v) {
      $this->modify_data(array('id' => $v), array('is_surface' => 0), 'db_city', $this->mls_cmt_img);
    }
    //修改楼盘表中的surface_img字段
    $this->modifycommunity($commid, array('surface_img' => $imgsrc));
    return $result;
  }

}

/* End of file buy_match_model.php */
/* Location: ./application/models/buy_match_model.php */
