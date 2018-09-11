<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of district_base_model
 *
 * @author wang
 */
class Metro_base_model extends MY_Model
{

  /**
   * 地铁线路里表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_line_tbl = 'metro_line';

  /**
   * 地铁站点表名称
   *
   * @access private
   * @var string
   */
  protected $_metro_site_tbl = 'metro_site';

  /**
   *  安居客区属表名称
   *
   * @access private
   * @var string
   */
  protected $_ajk_tbl = 'anjuke_xiaoqu';
  protected $_select_fields = 'name';

  /**
   * 缓存key
   * @var string
   */
  protected $_mem_key = '';

  /*
   * 构造函数
   */
  public function __construct()
  {
    parent::__construct();

    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_district_base_model_';
  }


  /**
   * 设置区属表名称
   *
   * @access  public
   * @param  string $tblname 区属表名称
   * @return  void
   */
  public function set_district_tbl($tblname)
  {
    $this->_district_tbl = strip_tags(trim($tblname));
  }


  /**
   * 获取区属表名称
   *
   * @access  public
   * @param  void
   * @return  string 区属表名称
   */
  public function get_district_tbl()
  {
    return $this->_district_tbl;
  }


  /**
   * 设置区属表名称
   *
   * @access  public
   * @param  string $tblname 板块表名称
   * @return  void
   */
  public function set_street_tbl($tblname)
  {
    $this->_street_tbl = strip_tags(trim($tblname));
  }


  /**
   * 获取区属表名称
   *
   * @access  public
   * @param  void
   * @return  string 板块表名称
   */
  public function get_street_tbl()
  {
    return $this->_street_tbl;
  }


  /**
   * 获取区属数据
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array 区属数据数组
   */
  public function get_district($cond_where = '')
  {
    $district_arr = array();

    //获取区属表名称
    $district_tbl = $this->get_district_tbl();

    $mem_key = $this->_mem_key . '_get_district_' . $district_tbl . '_' . $cond_where;
    $cache = $this->mc->get($mem_key);

    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $district_arr = $cache['data'];
    } else {
      if ($cond_where != '') {
        $this->dbback_city->where($cond_where);
      }
      //查询
      $district_arr = $this->dbback_city->get($district_tbl)->result_array();

      //缓存区属信息
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $district_arr), 86400);
    }
    return $district_arr;
  }


  /**
   * 根据区 属名district 来获取对应的 区属id
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array 区属数据数组
   */
  public function get_district_id($cond_where = '')
  {
    $district_arr = array();

    //获取区属表名称
    $district_tbl = $this->get_district_tbl();
    if ($cond_where != '') {
      $where = array('district' => $cond_where);
    }
    //查询
    $district_arr = $this->get_data(array('form_name' => $district_tbl, 'where' => $where), 'dbback_city');
    return $district_arr[0];
  }


  /**
   * 根据区属id获得该区属对应的板块
   *
   * @access  public
   * @param  int $dist_id 区属ID
   * @return  array 板块数据数组
   */
  public function get_street_bydist($dist_id = 0)
  {
    $dist_id = intval($dist_id);
    $street_arr = array();

    if ($dist_id > 0) {
      $mem_key = $this->_mem_key . '_get_street_bydist_' . $dist_id;
      $cache = $this->mc->get($mem_key);

      if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
        $street_arr = $cache['data'];
      } else {
        //查询条件
        $cond_where = 'dist_id = ' . $dist_id;
        $street_arr = $this->get_street($cond_where);
        $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $street_arr), 3600);
      }
    }

    return $street_arr;
  }


  /**
   * 根据区属district获得该区属对应的区属id
   *
   * @access  public
   * @param  int $district 区属ID
   * @return  array 区属数据数组
   */
  public function get_street_bydt($district = " ")
  {
    $district = trim(urldecode($district));
    $street_arr = array();

    if (!empty($district)) {
      //根据 $district =》 区属 来查询对应的 dist_id =》 区属id
      $result = $this->get_district_id($district);
      $dist_id = intval($result['id']);
      //查询条件
      $cond_where = array('dist_id' => $dist_id);
      $street_arr = $this->get_street_ding($cond_where);
    }
    return $street_arr;
  }


  /**
   * 根据条件获得对应的板块
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array 板块数据数组
   */
  public function get_street_ding($cond_where = '')
  {
    //板块表名称
    $street_tbl = $this->get_street_tbl();
    $result = $this->get_data(array('form_name' => $street_tbl, 'where' => $cond_where), 'dbback_city');
    return $result;
  }


  /**
   * 根据条件获得对应的板块
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  array 板块数据数组
   */
  public function get_street($cond_where = '')
  {
    $cond_where = strip_tags($cond_where);
    $street_arr = array();

    //板块表名称
    $street_tbl = $this->get_street_tbl();

    $mem_key = $this->_mem_key . '_get_street_' . $street_tbl . '_' . $cond_where;
    $cache = $this->mc->get($mem_key);

    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $street_arr = $cache['data'];
    } else {
      //查询条件
      if ($cond_where != '') {
        $this->dbback_city->where($cond_where);
      }

      //排序条件
      $this->dbback_city->order_by('id', 'ASC');

      //查询
      $street_arr = $this->dbback_city->get($street_tbl)->result_array();

      //缓存区属信息
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $street_arr), 86400);
    }

    return $street_arr;
  }


  /**
   * 根据区属id获得区属名
   *
   * @access  public
   * @param  int $dist_id 区属ID
   * @return  string 区属名称
   */
  public function get_distname_by_id($dist_id = 0)
  {
    $dist_id = intval($dist_id);
    $dist_name = '';
    $dist_info = array();

    if ($dist_id > 0) {
      //板块表名称
      $district_tbl = $this->get_district_tbl();

      //查询条件
      $cond_where = 'id = ' . $dist_id;

      $this->dbback_city->select('district');
      $this->dbback_city->where($cond_where);

      //查询
      $dist_info = $this->dbback_city->get($district_tbl)->row_array();
      $dist_name = (is_array($dist_info) && !empty($dist_info)) ?
        $dist_info['district'] : '';
    }

    return $dist_name;
  }


  /**
   * 根据id获得板块名
   *
   * @access  public
   * @param  int $dist_id 区属ID
   * @return  string 板块名称
   */
  public function get_streetname_by_id($street_id = 0)
  {
    $street_id = intval($street_id);
    $street_name = '';
    $street_info = array();
    if ($street_id > 0) {
      //板块表名称
      $street_tbl = $this->get_street_tbl();

      $this->dbback_city->select('streetname');
      //查询条件
      $cond_where = 'id = ' . $street_id;
      $this->dbback_city->where($cond_where);

      //查询
      $street_info = $this->dbback_city->get($street_tbl)->row_array();
      $street_name = (is_array($street_info) && !empty($street_info)) ? $street_info['streetname'] : '';
    }

    return $street_name;
  }


  //获取安居客小区名
  public function get_ajktb()
  {
    $$street_arr = array();
    //排序条件
    $this->dbback_city->order_by('id', 'ASC');

    //查询
    $street_arr = $this->dbback_city->get($this->_ajk_tbl)->result_array();

    return $street_arr;
  }
}

/* End of file district_base_model.php */
/* Location: ./application/models/district_base_model.php */
