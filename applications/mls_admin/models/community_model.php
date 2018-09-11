<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
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
load_m('Community_base_model');

class Community_model extends Community_base_model
{

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';

  /**
   * 楼盘表名称
   * @var string
   */
  private $_cmt_tbl = 'community';

  public function __construct()
  {
    parent::__construct();
    $this->mls_community = 'community';
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
  public function getcommunity($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize, 'order_by' => array('creattime', 'desc')), $database);
    return $comm;
  }

  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function getcommunity2($where = array(), $like = array(), $pagesize = 0, $order = array('id', 'asc'), $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->mls_community, 'select' => $this->select_fields, 'where' => $where, 'like' => $like, 'limit' => 0, 'offset' => $pagesize, 'order_by' => $order), $database);
    return $comm;
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
   * 查找模匹配某个小区名称的列表
   * @param string $cmtname 小区名称
   * @param int $num 查找显示最多个数
   * @return array 返回匹配到小区记录所组成的二维数组
   */
  public function auto_cmtname($cmtname, $num = 10)
  {
    $cmt_info = array();

    if ($this->get_select_fields()) {
      $base_info_fields = array(
        'id', 'cmt_name', 'dist_id', 'streetid',
        'averprice', 'address', 'status', 'build_date'
      );

      $this->set_select_fields($base_info_fields);
    }

    $cmt_list = $this->get_cmtinfo_by_kw($cmtname, $num);

    if (is_array($cmt_list) && !empty($cmt_list)) {
      //加载区属板块MODEL
      $this->load->model('district_base_model');
      //获取全部区属信息
      $distritct_info = $this->district_base_model->get_district();
      //获取全部板块信息
      $street_info = $this->district_base_model->get_street();

      if (is_array($distritct_info) && !empty($distritct_info)) {
        foreach ($distritct_info as $key => $value) {
          $disrct_arr[$value['id']] = $value;
        }
      }

      if (is_array($street_info) && !empty($street_info)) {
        foreach ($street_info as $key => $value) {
          $street_arr[$value['id']] = $value;
        }
      }

      foreach ($cmt_list as $key => $value) {
        $dist_id = intval($value['dist_id']);
        $street_id = intval($value['streetid']);

        if (isset($dist_id) && $dist_id > 0 && isset($disrct_arr[$dist_id]['district'])) {
          $value['districtname'] = $disrct_arr[$dist_id]['district'];
        }

        if (isset($street_id) && $street_id > 0 && isset($street_arr[$street_id]['streetname'])) {
          $value['streetname'] = $street_arr[$street_id]['streetname'];
        }

        $cmt_info[] = $value;
      }
    }

    return $cmt_info;
  }


  /**
   * 根据关键字获取小区名称
   * @param string $blockname 小区名称
   * @param int $num 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_cmtinfo_by_kw($keyword, $limit = 10, $status = array(1, 2), $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $cond_where = "(`cmt_name` LIKE '%" . $keyword . "%' OR `alias` LIKE '%" . $keyword . "%' "
        . "OR `name_spell` LIKE '" . $keyword . "%' OR `alias_spell` LIKE '" . $keyword . "%' "
        . " OR `alias_spell` LIKE '" . $keyword . "%')";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      $this->dbback_city->where_in('status', $status);

      //查询
      $cmt_info = $this->dbback_city->get($this->_cmt_tbl, $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
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
    $select = array('id', 'cmt_name', 'status', 'dist_id', 'streetid', 'address');
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
    $select = array('id', 'cmt_name', 'status', 'dist_id', 'streetid', 'address');
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
