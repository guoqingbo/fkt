<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * 操作日志业务类
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Operate_log_base_model CLASS
 *
 * 操作日志管理相关功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          yuan
 */
class Signatory_operate_log_base_model extends MY_Model
{
  /**
   * 操作日志表名称
   * @var string
   */
  private $_tbl = 'signatory_operate_log';

  /**
   * 查询字段
   * @var string
   */
  public $select_fields = '';


  /**
   * 设置操作日志表名称
   *
   * @access  public
   * @param  string $tblname 操作日志表名称
   * @return  void
   */
  public function set_tbl($tblname)
  {
    $this->_tbl = trim(strip_tags($tblname));
  }


  /**
   * 获取操作日志表名称
   *
   * @access  public
   * @param  void
   * @return  string 区属表名称
   */
  public function get_tbl()
  {
    return $this->_tbl;
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
   * @param array $status 操作日志状态
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
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
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
   * 添加操作日志
   * @param array $paramlist 操作日志字段
   * @return insert_id or 0
   */
  function add_operate_log($paramlist = array())
  {
    if (!empty($paramlist) && is_array($paramlist)) {
      $this->db_city->insert($this->_tbl, $paramlist);//插入数据

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
   * 筛选操作日志
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 以操作日志信息组成的多维数组
   */
  public function get_operate_log($where = '', $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    if ($like) {
      //查询条件
      $this->dbback_city->where($like);
    }

    $this->dbback_city->order_by('time', 'desc');

    if ($offset >= 0 && $pagesize > 0) {
      $this->dbback_city->limit($pagesize, $offset);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }


  /**
   * 获得操作日志总数
   * @param array $where where字段
   * @return string 操作日志总数
   */
  public function get_operate_log_num($where = array(), $like = array())
  {
    $comm = $this->get_data(array('form_name' => $this->_tbl, 'where' => $where, 'like' => $like, 'select' => array('count(*) as num')), 'dbback_city');
    return $comm[0]['num'];
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
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
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
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
    }
    return $cmt_info;
  }

  /**
   * 根据操作日志ID获取操作日志的信息
   * @param int $id 操作日志ID
   *
   * @return array
   */
  public function find_cmt($id)
  {
    $this->dbback_city->select('cmt_name,address,build_type,build_date,dist_id,streetid ,property_year,buildarea,coverarea,property_company,developers,parking,green_rate,plot_ratio,property_fee,build_num,total_room,floor_instruction,introduction,facilities,subway,bus_line,b_map_x,b_map_y,is_lock');
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl)->result_array();
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
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->row_array();
    }
    return $cmt_info;
  }

  /**
   * 更新某条房源需求信息
   *
   * @access  protected
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_tbl();

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return FALSE;
    }

    foreach ($update_arr as $key => $value) {
      $this->db_city->set($key, $value, $escape);
    }

    //设置条件
    $this->db_city->where($cond_where);

    //更新数据
    $this->db_city->update($tbl_name);

    return $this->db_city->affected_rows();
  }

  /**
   * 根据操作日志ID,提交数据,修改相关操作日志详情
   * @param string $commid 操作日志ID
   * @param array $paramlist 操作日志修改字段
   * @return 0 or 1
   */
  function update_cmt_by_id($commid, $paramlist = array())
  {
    $result = $this->modify_data(array('id' => $commid), $paramlist, 'db_city', $this->_tbl);
    return $result;
  }

  /**
   * 获取操作类型
   *
   * @access  public
   * @return  array 配置信息数组
   */
  public function get_base_conf()
  {
    $type_arr = array(
      1 => '登录系统', 2 => '新增房源', 3 => '修改房源', 4 => '注销房源', 5 => '查看房源跟进',
      6 => '分配任务', 7 => '分配房源', 8 => '导入房源', 9 => '添加视频', 10 => '新增客源',
      11 => '修改客源', 12 => '注销客源', 13 => '查看客源跟进', 14 => '客源分配任务', 15 => '分配客源',
      16 => '导入客源', 17 => '申请合作', 19 => '接受合作', 20 => '拒绝合作',
      21 => '确认成交', 22 => '成交失败', 23 => '取消合作', 24 => '认证', 25 => '修改门店',
      26 => '修改经纪人资料', 27 => '注销经纪人帐号', 28 => '添加门店', 29 => '设置关联门店', 30 => '修改权限',
      31 => '修改基本设置', 32 => '数据转移', 33 => '合作审核', 34 => '朋友圈管理', 35 => '合同管理',
      36 => '采集管理', 37 => '群发管理', 38 => '钥匙管理', 39 => '黑名单', 40 => '个人资料',
      41 => '积分商城', 42 => '旅游地产', 43 => '新房分销', 44 => '修改经纪人密码', 45 => '房源查看保密信息',
        46 => '房源跟进', 47 => '客源查看保密信息', 48 => '客源跟进', 49 => '区域公盘管理'
    );

    return $type_arr;
  }

}


/* End of file block_base_model.php */
/* Location: ./models/block_base_model.php */
