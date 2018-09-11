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

// ------------------------------------------------------------------------

/**
 * Agency_base_model CLASS
 *
 * 公司、门店添加、删除、修改管理功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
class Dictionary_base_model extends MY_Model
{

  /**
   * 字典表
   * @var string
   */
  private $_tbl = 'dictionary';

  /**
   * 字典类型表
   * @var string
   */
  private $_tbl2 = 'dictionary_type';

  /**
   * 查询字段
   * @var string
   */
  private $_select_fields = '';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    //$this->load->model('newhouse_sync_account_base_model');
//        $this->load->model('department_access_area_base_model');
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
    $this->_select_fields = $select_fields_str;
  }

  /**
   * 获取需要查询的字段
   * @return string
   */
  public function get_select_fields()
  {
    return $this->_select_fields;
  }

  /**
   * 符合条件的行数
   * @param string $where 查询条件
   * @return int
   */
  public function count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 获取数据字典列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by($where, $start = -1, $limit = 20,
                             $order_key = 'id', $order_by = 'DESC')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据数据字典类型id获取该类型所有记录
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by_dictionary_type_id($id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $where = "status = 1 and dictionary_type_id = " . $id . "";
    $this->dbback_city->where($where);

    //排序条件
    $order_key = 'id';
    $order_by = 'ASC';
    $this->dbback_city->order_by($order_key, $order_by);
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据数据字典id获取类型数据
   * @param string $dictionary_id_str 字典id
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by_dictionary_id($dictionary_id_str, $order_key = 'id', $order_by = 'ASC')
  {
    $dictionary_fileds = array('id', 'name', 'name_abbr', 'dictionary_type_id');
    $this->set_select_fields($dictionary_fileds);
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($dictionary_id_str) {
      if (!empty($dictionary_id_str)) {
        $where_cond = 'id in (' . $dictionary_id_str . ')';
        //查询条件
        $this->dbback_city->where($where_cond);
      }
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    //返回结果
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据查询条件返回一条公司表的记录
   * @param string $where 查询条件
   * @return array 返回一条一维数组的公司表记录
   */
  public function get_one_by($where = '')
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where($where);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $dictionary_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_id($dictionary_id)
  {
    $dictionarys = array();
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if (is_array($dictionary_id)) {
      $this->dbback_city->where_in('id', $dictionary_id);
      $dictionarys = $this->dbback_city->get($this->_tbl)->result_array();
    } else if (intval($dictionary_id) >= 0) {
      $this->dbback_city->where('id', $dictionary_id);
      $dictionarys = $this->dbback_city->get($this->_tbl)->row_array();
    }
    return $dictionarys;
  }

  /**
   * 设置门店的状态
   * @param int $dictionary_id 门店编号
   * @param int $status 门店状态
   */
  public function set_esta($dictionary_id, $status)
  {
    $data = array();
    $data['status'] = $status;
    $this->update_by_dictionary_id($data, $dictionary_id);
  }

  /**
   * 查找所有有效数据字典
   * @return array
   */
  function get_dictionary_by()
  {
    $this->dbback_city->where('status', 1);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据一级门店查找归属二级门店
   * @return array
   */
  function get_department_by_one($dictionary_id = 0)
  {
    if (!empty($dictionary_id)) {
      $this->dbback_city->where('department_id', $dictionary_id);
      return $this->dbback_city->get($this->_tbl)->result_array();
    } else {
      return false;
    }
  }

  /**
   * 添加数据
   * @param $key 键（Key）
   * @param $name 值（Value）
   * @param $name_abbr 值缩写（Value）
   * @param $desc 描述
   * @param int $status 状态
   * @param int $dictionary_type_id 类型
   * @return int 插入成功的编号
   */
  public function add_dictionary($key, $name, $name_abbr, $desc, $status = 1, $dictionary_type_id = 0)
  {
    $data = array();
    $data['key'] = $key;
    $data['name'] = $name;
    $data['name_abbr'] = $name_abbr;
    $data['desc'] = $desc;
    $data['dictionary_type_id'] = $dictionary_type_id;
    $data['status'] = $status;
    $data['createtime'] = time();
    $result = $this->insert($data);

    return $result;
  }


  /**
   * 修改数据
   * @param $key 键（Key）
   * @param $name 值（Value）
   * @param $name_abbr 值缩写（Value）
   * @param $desc 描述
   * @param int $status 状态
   * @param int $dictionary_type_id 类型
   * @return int 插入成功的编号
   */
  public function update_dictionary($key, $name, $name_abbr, $desc, $status = 1, $dictionary_type_id = 0)
  {
    $data = array();
    $data['key'] = $key;
    $data['name'] = $name;
    $data['name_abbr'] = $name_abbr;
    $data['desc'] = $desc;
    $data['dictionary_type_id'] = $dictionary_type_id;
    $data['status'] = $status;
    $data['updatetime'] = time();
    $result = $this->update_by_dictionary_id($data, $dictionary_type_id);

    return $result;
  }

  /**
   * 修改总公司的logo
   * @return int
   */
  public function update_company_logo($photo, $company_id)
  {
    $data = array();
    $data['photo'] = $photo;
    $result = $this->update_by_departmentid_companyid($data, $company_id, 0);
    $xffxdata = array(
      'city' => $_SESSION[WEB_AUTH]["city"],
      'password' => md5(''),
      'status' => 1,
      // 'create_time' => time(),
      'update_time' => time(),
      'isdel' => 0,
      'company_id' => $company_id
    );
    //新房分销没总公司的logo
    // $this->newhouse_sync_account_base_model->updatecompany($xffxdata);
    /*
    $url = 'http://adminxffx.fang100.com/fktdata/updatecompany';
    $this->load->library('Curl');
    Curl::fktdata($url,$xffxdata);*/
    return $result;
  }

  /**
   * 查找某类型下的子类型列表
   * @param int $dictionary_id 类型编号
   * @return array
   */
  function get_children_by_dictionary_type_id($dictionary_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('dictionary_id', $dictionary_id);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据查找某公司名下的子公司列表
   * @param int $company_id 公司编号
   * @return array
   */
  function get_children_by_company_id_type($company_id = 0, $department_type = 0)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('company_id', $company_id);
    $this->dbback_city->where('department_type', $department_type);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }


  /*
     * 根据公司名获取下属门店信息
     * @param string $companyname
    */
  public function get_department_by_companyname($companyname)
  {
    $this->dbback_city->select('id');
    $this->dbback_city->where('name', $companyname);
    $companyinfo = $this->dbback_city->get($this->_tbl)->row_array();
    $companyid = $companyinfo['id'];

    $return = array();
    if ($companyid > 0) {
      $this->dbback_city->select('id,name');
      $this->dbback_city->where('company_id', $companyid);

      $return = $this->dbback_city->get($this->_tbl)->result_array();
    }

    return $return;
  }


  /**
   * 根据关键字获取类型名称
   * @param string $keyword 类型名称
   * @param int $limit 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_dictionary_info_by_kw($keyword = '', $dictionary_type_id, $search_arr = 0, $limit = 10, $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '' && is_full_array($search_arr)) {

      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $status = 1;


//      $this->dbback_city->from ("(select id, `name` from " . $this->_tbl . " where `name` LIKE '%"  . $keyword . "%' and `status` = " . $status . ") as a");
//      $this->dbback_city->join ("(select dictionary_type_id, count(*) from " . $this->_tbl . " where `status` = " . $status . " group by dictionary_type_id) as b", "a.id = b.dictionary_type_id", 'left');

      $cond_where = 'select a.`id` as `id`, a.`name` as `name`, a.`key` as `key`, a.`dictionary_type_id` as `dictionary_type_id`, b.`name` as `dictionary_type_name`, b.`name_abbr` as `dictionary_type_name_abbr` from';
      $cond_where .= "(select `id`, `name`, `key`, `dictionary_type_id` from " . $this->_tbl . " where concat(`name`, `key`, `name_abbr`) LIKE '%" . $keyword . "%' and `status` = " . $status . ") as a";
      $cond_where .= " left join ";
      $cond_where .= "(select `id`, `name`, `name_abbr` from " . $this->_tbl2 . " where `status` = " . $status . ") as b";
      $cond_where .= " on a.`dictionary_type_id` = b.`id` ";
      $cond_where .= " limit " . $limit;

      $cmt_info = $this->dbback_city->query($cond_where)->result_array();

      return $cmt_info;
    }


    return $cmt_info;
  }


  /**
   * 根据关键字获取门店名称
   * @param string $keyword 门店名称
   * @param int $limit 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_department_info_by_kw($keyword = '', $search_arr = 0, $limit = 10, $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '' && is_full_array($search_arr)) {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $cond_where = "(`name` LIKE '%" . $keyword . "%')";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      $this->dbback_city->where('status', 1);
      //全公司范围
      if (in_array($search_arr['role_level'], array(1, 2, 3, 4))) {
        $this->dbback_city->where('company_id', $search_arr['company_id']);
      } else if (in_array($search_arr['role_level'], array(6, 7))) {
        //当前门店范围
        $this->dbback_city->where('id', $search_arr['department_id']);
      } else if (in_array($search_arr['role_level'], array(5))) {
        //一级门店范围
        $this->dbback_city->where('department_id', $search_arr['department_id']);
      } else {
        return $cmt_info;
      }

      //查询
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
  }

  /**
   * 根据关键字获取门店下经纪人名称
   * @param string $keyword 门店名称
   * @param int $limit 显示数量
   * @param array $status 楼盘状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function get_broker_info_by_kw($keyword = '', $search_arr = 0, $limit = 10, $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));
    $time = time();

    if ($keyword != '' && is_full_array($search_arr)) {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $cond_where = "(`truename` LIKE '%" . $keyword . "%') and expiretime >= {$time}";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      $this->dbback_city->where('status', 1);

      if (is_array($search_arr['department_id'])) {
        $this->dbback_city->where_in('department_id', $search_arr['department_id']);
      } else {
        //当前门店范围
        $this->dbback_city->where('department_id', $search_arr['department_id']);
      }
      //查询
      $cmt_info = $this->dbback_city->get('broker_info', $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
  }

  /**
   * 查找某公司名下的经纪人总数
   * @param int $company_id 公司编号
   * @return int
   */
  function count_by_dictionary_type_id($dictionary_type_id)
  {
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('dictionary_type_id', $dictionary_type_id);
    return $this->dbback_city->count_all_results($this->_tbl);
  }

  /**
   * 添加门店
   * @param int $dictionary_id 门店编号
   * @param int $dist_id 区属编号
   * @param int $street_id 板块编号
   * @param int $name 门店名称
   * @param int $telno 电话号码
   * @param string $address 门店地址
   * @param int $company_id 公司编号
   * @param int $init 默认用户挂靠的公司
   * @param int $status 状态
   * @param int $department_type 门店类型，是否加盟
   * @param int $city 城市
   * @param int $father_department_id 挂靠门店ID
   * @param int $department_type 门店类型
   * @param int $master_id 客户经理
   * @return 插入成功的编号
   */
  public function add_department($dist_id, $street_id, $name, $telno,
                                 $address, $company_id, $init = 0, $status = 1,
                                 $city = '', $father_department_id = 0, $department_type = 0, $master_id = 0)
  {

    $data = array();
    $data['dist_id'] = $dist_id;
    $data['street_id'] = $street_id;
    $data['name'] = $name;
    $data['telno'] = $telno;
    $data['address'] = $address;
    $data['company_id'] = $company_id;
    $data['department_id'] = $father_department_id;
    $data['init'] = $init;
    $data['status'] = $status;
    $data['add_time'] = time();
    $data['department_type'] = $department_type;
    $data['master_id'] = $master_id;
    $result = $this->insert($data);

    //初始化门店关联数据权限
    if (is_int($result) && $result > 0) {
      $departmentarr = array();

      if ($department_type == 1) {
        $this->set_select_fields(array('id'));
        $dictionary_id_arr = array();
        $department_arr = $this->get_children_by_company_id($company_id);
        //去掉自身门店
        if (is_full_array($department_arr)) {
          foreach ($department_arr as $k => $v) {
            if ($v['id'] != $result) {
              $dictionary_id_arr[] = $v['id'];
            }
          }
        }
        if (is_full_array($dictionary_id_arr)) {
          //默认所有权限节点
          $this->load->model('department_permission_node_base_model');
          $func_auth = $this->department_permission_node_base_model->get_all_node_serialize();
          //初始化新增门店对其它门店的权限数据
          $this->load->model('department_permission_base_model');
          $this->department_permission_base_model->init_department_area($company_id, $result, $dictionary_id_arr, $func_auth);
        }
      }
    }

    //同步数据到新房分销
    // $datacom = $this->selectcompamy($company_id);
    //print_r($datacom);die();
//        if(isset($_SESSION[WEB_AUTH]["city"])){
//            $city = $_SESSION[WEB_AUTH]["city"];
//        }
//        $storedata = array(
//            'area_id' => $dist_id,
//            'city' => $city,
//            'storeName' => $name,
//            'address' => $address,
//            'kcp_id' => $company_id,
//            'status' => $status,
//            'special' => 0,
//            'create_time' => time(),
//            'update_time' => time(),
//            'isdel' => 0,
//            'store_id' => $result,
//        );

//        $xffxdata = array(
//            'area_id' => $dist_id,
//            'city' => $city,
//            'storeName' => $name,
//            'address' => $address,
//            'kcp_id' => $company_id,
//            'status' => $status,
//            'special' => 0,
//            'create_time' => time(),
//            'update_time' => time(),
//            'isdel' => 0,
//            'store_id' => $result,
//            'com_id' => $datacom[0]['id'],
//            'com_name' => $datacom[0]['name'],
//            'com_username' => $datacom[0]['telno'],
//            'com_realname' => $datacom[0]['linkman'],
//            'com_password' => md5(''),
//            'com_status' => 1,
//        );
    //11
    $this->load->model('district_base_model');
    $area = $this->district_base_model->get_distname_by_id($dist_id);
    //$this->newhouse_sync_account_base_model->addstore($xffxdata,$area,$storedata);
    /*
   $url = 'http://adminxffx.fang100.com/fktdata/addstore';
   $this->load->library('Curl');
   Curl::fktdata($url,$xffxdata);*/
    return $result;
  }

  //获取公司信息
  public function selectcompamy($company_id)
  {
    $this->dbback_city->where('id', $company_id);
    $this->dbback_city->where('status', 1);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 修改门店
   * @param int $dictionary_id 门店编号
   * @param int $dist_id 区属编号
   * @param int $street_id 板块编号
   * @param int $name 门店名称
   * @param int $telno 电话号码
   * @param string $address 门店地址
   * @param int $company_id 公司编号
   * @param int $init 默认用户挂靠的公司
   * @param int $status 状态
   * @param int $department_type 状态
   * @param int $master_id 客户经理编号
   * @return
   */
  public function update_department($dictionary_id, $dist_id, $street_id, $name, $telno,
                                    $address, $company_id, $init = 0, $status = 1, $department_type = 0, $master_id = 0)
  {
    $data = array();
    $data['dist_id'] = $dist_id;
    $data['street_id'] = $street_id;
    $data['name'] = $name;
    $data['telno'] = $telno;
    $data['address'] = $address;
    $data['init'] = $init;
    $data['status'] = $status;
    $data['department_type'] = $department_type;
    $data['master_id'] = $master_id;
    $data['company_id'] = $company_id;
    $result = $this->update_department_byid($data, $dictionary_id);
    $xffxdata = array(
      'area_id' => $dist_id,
      'city' => $_SESSION[WEB_AUTH]["city"],
      'storeName' => $name,
      'address' => $address,
      'status' => $status,
      'special' => 0,
      //  'create_time' => time(),
      'update_time' => time(),
      'isdel' => 0,
      'store_id' => $dictionary_id
    );
    //11
    $this->load->model('district_base_model');
    $area = $this->district_base_model->get_distname_by_id($dist_id);
    //$this->newhouse_sync_account_base_model->updatestore($xffxdata,$area);
    /*
    $url = 'http://adminxffx.fang100.com/fktdata/updatestore';
    $this->load->library('Curl');
    Curl::fktdata($url,$xffxdata);*/
    return $result;
  }


  public function update_department_byid($update_data, $dictionary_id)
  {
    return $this->update_by_departmentid_companyid($update_data, $dictionary_id);
  }

  /**
   * 插入公司数据
   * @param array $insert_data 插入数据源数组
   * @return int 成功 返回插入成功后的公司id 失败 false
   */
  public function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  /**
   * 更新数据字典
   * @param array $update_data 更新的数据源数组
   * @param array $dictionary_id 数据字典数组
   * @return int 成功后返回受影响的行数
   */
  public function update_by_dictionary_id($update_data, $dictionary_id)
  {
    if (is_array($dictionary_id)) {
      $ids = $dictionary_id;
    } else {
      $ids[0] = $dictionary_id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除公司数据
   * @param array or int $dictionary_id 公司编号 可接受整数或者公司数组编号
   * @param int $company_id 总公司编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_departmentid_companyid($dictionary_id, $company_id = '')
  {
    //多条删除
    if (is_array($dictionary_id)) {
      $ids = $dictionary_id;
    } else {
      $ids[0] = $dictionary_id;
    }
    if ($ids) {
      if ($company_id) {
        $this->db_city->where('company_id', $company_id);
      }
      $this->db_city->where_in('id', $ids);
      $this->db_city->delete($this->_tbl);
    }
    if ($this->db_city->affected_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 根据关键字获取小区名称
   * @param string 公司名称 小区名称
   * @param int $num 显示数量
   * @return array 公司信息数组
   */
  public function auto_companyname($keyword, $limit = 10)
  {
    $company_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }
      $cond_where = "(`name` LIKE '%" . $keyword . "%')  and company_id = 0 and status = 1";

      //设置查询条件
      $this->dbback_city->where($cond_where);

      //查询
      $company_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
    }

    return $company_info;
  }

  /*判断公司是否初始化成功
   * @compang_id   公司id
   * @num   返回值，公司权限条数
   */
  public function is_permission_initialize_success($company_id)
  {
    $sql = "select count(*) from permission_company_group where company_id = {$company_id}";
    $result = $this->dbback_city->query($sql)->row_array();
    //return $result;
    return $num = $result['count(*)'];
  }

  public function update_company_permission($company_id)
  {
    $this->load->model('permission_system_group_model');
    $system_group = $this->permission_system_group_model->get_all_by(array());
    foreach ($system_group as $key => $val) {
      $this->dbback_city->where('company_id', $company_id);
      $this->dbback_city->where('system_group_id', $val['id']);
      $result = $this->dbback_city->get('permission_company_group')->row_array();

      if (empty($result)) {
        $sql = "insert into permission_company_group (company_id,system_group_id,func_auth) values(" . $company_id . "," . $val['id'] . ",'" . $val['func_auth'] . "')";
        $this->db_city->query($sql);
      }
    }

    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->count_all_results("permission_company_group");
  }

  /**
   * 根据关键字获取门店名称
   * @param string $departmentname 小区名称
   * @param int $num 显示数量
   * @param array $status 门店状态
   * @param string $order_key 排序字段
   * @param string $order 升序降序
   * @return array 小区信息数组
   */
  public function auto_departmentname($keyword, $limit = 10, $status = array(1, 2, 3), $order_key = 'id', $order = 'ASC')
  {
    $cmt_info = array();
    $keyword = trim(strip_tags($keyword));

    if ($keyword != '') {
      //查询字段
      $select_fields = $this->get_select_fields();
      if ($select_fields != '') {
        $this->dbback_city->select($select_fields);
      }

      $cond_where = "`name` LIKE '%" . $keyword . "%' and company_id > 0";

      //设置查询条件
      $this->dbback_city->where($cond_where);
      //$this->dbback_city->where_in('status' , $status);
      $this->dbback_city->where('status', 1);

      //查询
      $cmt_info = $this->dbback_city->get($this->_tbl, $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
  }
}

/* End of file department_base_model.php */
/* Location: ./applications/models/department_base_model.php */
