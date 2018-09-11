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
 * 楼盘数据管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
class Community_base_model extends MY_Model
{
  /**
   * 楼盘表名称
   * @var string
   */
  private $_cmt_tbl = 'community';

  /**
   * 楼盘图库表名称
   * @var string
   */
  private $_cmt_img_tbl = 'cmt_img';

  /**
   * 楼盘栋座表名称
   * @var string
   */
  private $_cmt_lock_tbl = 'community_lock';

  /**
   * 楼盘栋座表名称
   * @var string
   */
  private $_cmt_dong_tbl = 'community_dong';

  /**
   * 楼盘单元表名称
   * @var string
   */
  private $_cmt_unit_tbl = 'community_unit';

  /**
   * 楼盘门牌表名称
   * @var string
   */
  private $_cmt_door_tbl = 'community_door';


  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置楼盘表名称
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
   * 获取楼盘表名称
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
   * 根据关键字获取小区名称
   * @param string $blockname 小区名称
   * @param int $num 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_cmtinfo_by_kw($keyword, $limit = 10, $status = array(1, 2, 3), $order_key = 'id', $order = 'ASC')
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
        . " OR `name_spell_s` LIKE '%" . $keyword . "%')";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      //$this->dbback_city->where_in('status' , $status);
      $this->dbback_city->where('status', 2);

      //查询
      $cmt_info = $this->dbback_city->get($this->_cmt_tbl, $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
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
   * 添加楼盘
   * @param array $paramlist 楼盘字段
   * @return insert_id or 0
   */
  public function add_community($paramlist = array())
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
   * 锁盘表添加数据
   * @param array $paramlist 字段
   * @return insert_id or 0
   */
  public function add_cmt_lock($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_lock_tbl, $paramlist);//插入数据

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
   * 添加楼栋
   * @param array $paramlist 楼栋字段
   * @return insert_id or 0
   */
  public function add_dong($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_dong_tbl, $paramlist);//插入数据

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
   * 筛选锁盘楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_lock_cmt($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $dong = $this->get_data(array('form_name' => $this->_cmt_lock_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);

    return $dong;
  }

  /**
   * 筛选楼栋(防止主从不及时 改成读主库)
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_dong($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $dong = $this->get_data(array('form_name' => $this->_cmt_dong_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);

    return $dong;
  }

  /**
   * 添加单元
   * @param array $paramlist 单元字段
   * @return insert_id or 0
   */
  public function add_unit($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_unit_tbl, $paramlist);//插入数据

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
   * 筛选单元(防止主从不及时 改成读主库)
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_unit($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $unit = $this->get_data(array('form_name' => $this->_cmt_unit_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);

    return $unit;
  }

  /**
   * 添加门牌
   * @param array $paramlist 门牌字段
   * @return insert_id or 0
   */
  public function add_door($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_door_tbl, $paramlist);//插入数据

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
   * 筛选门牌(防止主从不及时 改成读主库)
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_door($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $door = $this->get_data(array('form_name' => $this->_cmt_door_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);

    return $door;
  }

  /**
   * 筛选楼盘
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以楼盘信息组成的多维数组
   */
  public function get_community($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->_cmt_tbl, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);

    return $comm;
  }


  /**
   * 获得楼盘总数
   * @param array $where where字段
   * @return string 楼盘总数
   */
  public function get_community_num($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->_cmt_tbl, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
  }


  /**
   * 添加楼盘图片
   * @param array $paramlist 楼盘图片字段
   * @return insert_id or 0
   */
  function add_cmt_image($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_cmt_img_tbl, $paramlist);//插入数据
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
   * 添加楼盘图片
   * @param array $paramlist 楼盘图片字段、楼盘编号、图片类型
   * @return insert_id or 0
   */
  function add_cmt_pic($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      if (count($paramlist['image']) > 1) {
        $sql = '';
        foreach ($paramlist['image'] as $key => $value) {
          $createtime = time();
          $sql = "insert into cmt_img (cmt_id,image,pic_type,creattime) values(" . $paramlist['cmt_id'] . ",'" . $value . "'," . $paramlist['pic_type'] . "," . $createtime . ")";
          $result = $this->db_city->query($sql);
        }
      } else {
        $sql = '';
        $createtime = time();
        $sql = "insert into cmt_img (cmt_id,image,pic_type,creattime) values(" . $paramlist['cmt_id'] . ",'" . $paramlist['image'][0] . "'," . $paramlist['pic_type'] . "," . $createtime . ")";
        $result = $this->db_city->query($sql);
      }
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
   * 根据小区名获得该小区信息
   * @param string $blockname 小区名称
   * @return array 小区信息数组
   */
  public function get_cmtinfo_by_cmtname($cmt_name, $limit = 1)
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($cmt_name));

    if ($cmt_name != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }
      $cond_where = array('cmt_name' => $cmt_name);
      //设置查询条件
      $this->dbback_city->where($cond_where);
      //查询
      $cmt_info = $this->dbback_city->get($this->_cmt_tbl, $limit)->result_array();
    }
    return $cmt_info;
  }

  /**
   * 根据小区名从正式小区中获得该小区信息
   * @param string $cmt_name 小区名称
   * @return array 小区信息数组
   */
  public function get_cmtinfo_by_cmtname_from_official($cmt_name, $limit = 1)
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($cmt_name));

    if ($cmt_name != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }
      $cond_where = array('cmt_name' => $cmt_name, 'status' => 2);
      //设置查询条件
      $this->dbback_city->where($cond_where);
      //查询
      $cmt_info = $this->dbback_city->get($this->_cmt_tbl, $limit)->result_array();
    }
    return $cmt_info;
  }

  /**
   * 根据楼盘ID获取楼盘的信息
   * @param int $id 楼盘ID
   *
   * @return array
   */
  public function find_cmt($id)
  {
    $this->dbback_city->select('cmt_name,address,build_type,build_date,dist_id,streetid ,property_year,buildarea,coverarea,property_company,developers,parking,green_rate,plot_ratio,property_fee,build_num,total_room,floor_instruction,introduction,facilities,subway,bus_line,b_map_x,b_map_y,is_lock');
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_cmt_tbl)->result_array();
  }


  /**
   * 根据楼盘ID获取楼盘的 姓名
   * @return array
   */
  public function get_cmt()
  {
    $data = array();
    $this->dbselect('dbback_city');
    $sql = " SELECT id , cmt_name FROM `community` ";
    $query = $this->db->query($sql);
    $arr = $query->result_array();
    foreach ($arr as $key => $val) {
      $data[$val['id']] = $val['cmt_name'];
    }

    return $data;
  }


  /**
   * 根据小区id获得该小区信息
   * @param string $blockname 小区名称
   * @return array 小区信息数组
   */
  public function get_cmtinfo_longitude($cmt_id, $limit = 1)
  {
    $cmt_info = array();
    $keyword = intval(strip_tags($cmt_id));

    if ($cmt_id != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }
      $cond_where = array('id' => $cmt_id);
      //设置查询条件
      $this->dbback_city->where($cond_where);
      //查询
      $cmt_info = $this->dbback_city->get($this->_cmt_tbl, $limit)->row_array();
    }
    return $cmt_info;
  }

  /**
   * 根据参数获得该楼盘的图片信息
   * @param array $where where字段
   * @return array
   */
  public function get_all_cmt_image_by_cmtid($where, $database = 'db_city')
  {
    $cmt_images = $this->get_data(array('form_name' => $this->_cmt_img_tbl, 'where' => $where), $database);
    return $cmt_images;
  }

  /**
   * 根据参数获得该楼盘的所有楼栋号
   * @param array $where where字段
   * @return array
   */
  public function get_all_dong_by_cmtid($cmt_id = 0, $database = 'db_city')
  {
    $cmt_dong = false;
    if (isset($cmt_id) && $cmt_id > 0) {
      $where_cond = array(
        'cmt_id' => $cmt_id
      );
      $cmt_dong = $this->get_data(array('form_name' => $this->_cmt_dong_tbl, 'where' => $where_cond, 'order_by_array' => array('id', 'asc')), $database);
    }
    return $cmt_dong;
  }

  /**
   * 根据参数获得该楼盘的所有楼栋号
   * @param array $where where字段
   * @return array
   */
  public function get_all_dong_by_param($where, $database = 'db_city')
  {
    $cmt_dong = $this->get_data(array('form_name' => $this->_cmt_dong_tbl, 'where' => $where), $database);
    return $cmt_dong;
  }

  /**
   * 根据参数获得该楼盘的所有单元号
   * @param array $where where字段
   * @return array
   */
  public function get_all_unit_by_param($where, $database = 'db_city')
  {
    $cmt_unit = $this->get_data(array('form_name' => $this->_cmt_unit_tbl, 'where' => $where), $database);
    return $cmt_unit;
  }

  /**
   * 根据参数获得该楼盘的所有门牌号
   * @param array $where where字段
   * @return array
   */
  public function get_all_door_by_param($where, $database = 'db_city')
  {
    $cmt_door = $this->get_data(array('form_name' => $this->_cmt_door_tbl, 'where' => $where), $database);
    return $cmt_door;
  }

  /**
   * 根据楼栋号，获得该楼栋下的所有单元。
   * @param array $where where字段
   * @return array
   */
  public function get_all_unit_by_dongid($dong_id = 0, $database = 'db_city')
  {
    $cmt_unit = false;
    if (isset($dong_id) && $dong_id > 0) {
      $where_cond = array(
        'dong_id' => $dong_id
      );
      $cmt_unit = $this->get_data(array('form_name' => $this->_cmt_unit_tbl, 'where' => $where_cond, 'order_by_array' => array('id', 'asc')), $database);
    }
    return $cmt_unit;
  }

  /**
   * 根据楼栋号，获得该楼栋下的所有单元。
   * @param array $where where字段
   * @return array
   */
  public function get_all_door_by_unitid($unit_id = 0, $database = 'db_city')
  {
    $cmt_door = false;
    if (isset($unit_id) && $unit_id > 0) {
      $where_cond = array(
        'unit_id' => $unit_id
      );
      $cmt_door = $this->get_data(array('form_name' => $this->_cmt_door_tbl, 'where' => $where_cond, 'order_by_array' => array('id', 'asc')), $database);
    }
    return $cmt_door;
  }

  /**
   * 根据参数获得该楼盘的图片信息
   * @param array $where where字段
   * @return array
   */
  public function get_one_cmt_image_by_cmtid($where, $database = 'db_city')
  {
    $cmt_images = $this->get_data(array('form_name' => $this->_cmt_img_tbl, 'where' => $where, 'limit' => 1), $database);
    return $cmt_images;
  }

  /**
   * 根据楼盘id，删除该楼盘下的所有楼栋、单元、门牌
   * @param array $where where字段
   * @return array
   */
  public function delete_dong_unit_door_by_cmtid($id = 0, $database = 'db_city')
  {
    $result = false;
    if ($id > 0) {
      $where_cond = array(
        'cmt_id' => $id
      );
      $del_result_1 = $this->del($where_cond, $database, $this->_cmt_dong_tbl);
      $del_result_2 = $this->del($where_cond, $database, $this->_cmt_unit_tbl);
      $del_result_3 = $this->del($where_cond, $database, $this->_cmt_door_tbl);
      if ($del_result_1 && $del_result_2 && $del_result_3) {
        $result = true;
      }
    }
    return $result;
  }

  /**
   * 根据楼栋id，删除该楼栋下的所有单元、门牌
   * @param array $where where字段
   * @return array
   */
  public function delete_unit_door_by_dongid($id = 0, $database = 'db_city')
  {
    $result = false;
    if ($id > 0) {
      $where_cond = array(
        'dong_id' => $id
      );
      $del_result_2 = $this->del($where_cond, $database, $this->_cmt_unit_tbl);
      $del_result_3 = $this->del($where_cond, $database, $this->_cmt_door_tbl);
      if ($del_result_2 && $del_result_3) {
        $result = true;
      }
    }
    return $result;
  }

  /**
   * 根据楼盘ID,提交数据,修改相关楼盘详情
   * @param string $commid 楼盘ID
   * @param array $paramlist 楼盘修改字段
   * @return 0 or 1
   */
  function update_cmt_by_id($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db_city', $this->_cmt_tbl);
    return $result;
  }

  /**
   * 根据ID,提交数据,修改锁盘表id
   * @param string $commid 楼盘ID
   * @param array $paramlist 楼盘修改字段
   * @return 0 or 1
   */
  function update_cmt_lock_by_id($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db_city', $this->_cmt_lock_tbl);
    return $result;
  }

  /**
   * 根据ID,提交数据,修改楼栋表id
   * @param string $commid 楼栋ID
   * @param array $paramlist 楼栋修改字段
   * @return 0 or 1
   */
  function update_cmt_dong_by_id($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db_city', $this->_cmt_dong_tbl);
    return $result;
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
