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
class Department_base_model extends MY_Model
{

  /**
   * 中介表
   * @var string
   */
  private $_tbl = 'department';

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
   * 获取中介公司列表页
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
   * 根据门店id获取门店数据
   * @param string $department_id_str 门店id
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条公司记录组成的二维数组
   */
  public function get_all_by_department_id($department_id_str, $order_key = 'id', $order_by = 'ASC')
  {
    $department_fileds = array('id as department_id', 'name as department_name', 'dist_id', 'street_id', 'telno', 'address', 'department_id as father_department_id');
    $this->set_select_fields($department_fileds);
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if ($department_id_str) {
      if (!empty($department_id_str)) {
        $where_cond = 'id in (' . $department_id_str . ')';
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
     * 根据门店id获取门店数据
     * @param string $department_id_str 门店id
     * @param int $order_key 排序字段
     * @param string $order_by 升序、降序，默认降序排序
     * @return array 返回多条公司记录组成的二维数组
     */
    public function get_all_by_company_id($company_id=1, $order_key = 'id', $order_by = 'ASC')
    {
        $department_fileds = array('id as department_id', 'name as department_name', 'dist_id', 'street_id', 'telno', 'address', 'department_id as father_department_id');
        $this->set_select_fields($department_fileds);
        //查询字段
        if ($this->_select_fields) {
            $this->dbback_city->select($this->_select_fields);
        }
        $this->dbback_city->where('company_id', $company_id);

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
   * 根据经纪人编号和公司编号获取基本记录
   * @param int $department_id 公司编号
   * @param int $company_id 总公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_departmentid_companyid($department_id, $company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    //查询条件
    $this->dbback_city->where('id', $department_id);
    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->get($this->_tbl)->row_array();
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $department_id 公司编号
   * @return array 公司记录组成的一维数组
   */
  public function get_by_id($department_id)
  {
    $departments = array();
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if (is_array($department_id)) {
      $this->dbback_city->where_in('id', $department_id);
      $departments = $this->dbback_city->get($this->_tbl)->result_array();
    } else if (intval($department_id) > 0) {
      $this->dbback_city->where('id', $department_id);
      $departments = $this->dbback_city->get($this->_tbl)->row_array();
    }
    return $departments;
  }

  /**
   * 通过公司编号获取公司记录
   * @param int $department_id 公司编号
   * @return array 公司记录组成的二维数组
   */
  public function get_by_id_one($department_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    if (is_array($department_id)) {
      $this->dbback_city->where_in('id', $department_id);
      $departments = $this->dbback_city->get($this->_tbl)->result_array();
    } else if (intval($department_id) > 0) {
      $this->dbback_city->where('id', $department_id);
      $departments = $this->dbback_city->get($this->_tbl)->result_array();
    }
    return $departments;
  }

  /**
   * 判断是否已经设置过经纪人默认挂靠的门店
   * @param int $department_id 门店编号
   */
  public function is_exist_init_department($department_id)
  {
    $this->db_city->where('id', $department_id);
    $this->db_city->where('init', '1');
    return $this->db_city->count_all_results($this->_tbl);
  }

  /**
   * 获取默认挂靠的门店信息
   */
  public function get_by_init_department()
  {
    $this->dbback_city->where('init', '1');
    $init_department = $this->dbback_city->get($this->_tbl)->row_array();
    if (!is_full_array($init_department)) {
      $company_id = $this->insert(array('name' => '网络经纪人', 'status' => 1));
      $this->insert(array('name' => '网络经纪人', 'status' => 1, 'init' => 1, 'company_id' => $company_id));
      $this->init_company_purview($company_id);
      return $this->get_by_init_department();
    } else {
      return $init_department;
    }
  }

  /**
   * 初始化某公司的权限
   * @param int $company_id 公司编号
   */
  public function init_company_purview($company_id)
  {
    //角色权限
    $this->load->model('purview_system_role_model');
    $system_role = $this->purview_system_role_model->get_all_by('');
    $this->load->model('purview_company_role_model');
    //查询所有的角色
    $fields_role = array();
    foreach ($system_role as $v) {
      $fields_role[] = array(
        'name' => $v['name'], 'description' => $v['description'],
        'package_id' => $v['id'], 'company_id' => $company_id,
        'menu_auth' => $v['menu_auth'], 'func_auth' => $v['func_auth']
      );
    }
    $this->purview_company_role_model->insert($fields_role);
  }

  /**
   * 初始化某公司的权限
   * @param int $company_id 公司编号
   * created by angel_in_us
   */
  public function init_company_purview2($company_id)
  {
    //角色权限
    $this->load->model('purview_system_group_model');
    $system_group = $this->purview_system_group_model->get_all_by(array());
    foreach ($system_group as $key => $value) {
      $sql = "insert into purview_company_group (company_id,system_group_id,func_auth) values(" . $company_id . "," . $value['id'] . ",'" . $value['func_auth'] . "')";
      $this->db_city->query($sql);
    }

    $sql = "select count(*) from purview_company_group where company_id = {$company_id}";
    $result = $this->db_city->query($sql)->row_array();
    return $num = $result['count(*)'];
  }

  /**
   * 初始化某门店的权限
   * @param int $company_id , $department_id 公司编号
   * created by angel_in_us
   */
  public function init_department_purview($company_id, $department_id)
  {
    //角色权限
    $this->load->model('purview_system_group_model');
    $system_group = $this->purview_system_group_model->get_all_by(array());
    foreach ($system_group as $key => $value) {
      $sql = "insert into purview_department_group (company_id,department_id,system_group_id,func_auth) values(" . $company_id . "," . $department_id . "," . $value['id'] . ",'" . $value['func_auth'] . "')";
      $this->db_city->query($sql);
    }

    $sql = "select count(*) from purview_department_group where department_id = {$department_id}";
    $result = $this->db_city->query($sql)->row_array();
    return $num = $result['count(*)'];
  }

  /**
   * 设置门店的状态
   * @param int $department_id 门店编号
   * @param int $status 门店状态
   */
  public function set_esta($department_id, $status)
  {
    $data = array();
    $data['status'] = $status;
    $this->update_by_departmentid_companyid($data, $department_id);
  }

  /**
   * 查找所有有效总公司列表
   * @return array
   */
  function get_company_by()
  {
    $this->dbback_city->where('company_id', 0);
    $this->dbback_city->where('status', 1);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 根据一级门店查找归属二级门店
   * @return array
   */
  function get_department_by_one($department_id = 0)
  {
    if (!empty($department_id)) {
      $this->dbback_city->where('department_id', $department_id);
      return $this->dbback_city->get($this->_tbl)->result_array();
    } else {
      return false;
    }
  }

  /**
   * 根据一级门店查找归属二级门店的个数
   * @return array
   */
  function count_department_by_one($department_id = 0)
  {
    if (!empty($department_id)) {
      $this->dbback_city->where('department_id', $department_id);
      $this->dbback_city->where('status', 1);
      return $this->dbback_city->count_all_results($this->_tbl);
    } else {
      return false;
    }
  }

  /**
   * 添加总公司
   * @param int $dist_id 区属编号
   * @param int $street_id 板块编号
   * @param int $name 公司名称
   * @param int $telno 电话号码
   * @param string $address 公司地址
   * @return 插入成功的编号
   */
  public function add_company($dist_id, $street_id, $name, $telno,
                              $address, $linkman, $zip_code, $fax, $email, $website)
  {
    $data = array();
    $data['dist_id'] = $dist_id;
    $data['street_id'] = $street_id;
    $data['name'] = $name;
    $data['telno'] = $telno;
    $data['address'] = $address;
    $data['company_id'] = 0;
    $data['status'] = 1;
    $data['linkman'] = $linkman;
    $data['zip_code'] = $zip_code;
    $data['fax'] = $fax;
    $data['email'] = $email;
    $data['website'] = $website;
    $data['add_time'] = time();
    $result = $this->insert($data);
    $xffxdata = array(
      'kcp_name' => $name,
      'city' => $_SESSION[WEB_AUTH]["city"],
      'username' => $telno,
      'realname' => $linkman,
      'password' => md5(''),
      'status' => 1,
      'create_time' => time(),
      'update_time' => time(),
      'isdel' => 0,
      'company_id' => $result
    );
    //11
    //$this->newhouse_sync_account_base_model->addcompany($xffxdata);
    /*
    $url = 'http://adminxffx.fang100.com/fktdata/addcompany';
    $this->load->library('Curl');
    Curl::fktdata($url,$xffxdata);*/
    return $result;
  }

  /**
   * 修改总公司
   * @param int $dist_id 区属编号
   * @param int $street_id 板块编号
   * @param int $name 公司名称
   * @param int $telno 电话号码
   * @param string $address 公司地址
   * @return int
   */
  public function update_company($company_id, $dist_id, $street_id,
                                 $name, $telno, $address, $linkman, $zip_code, $fax, $email, $website)
  {
    $data = array();
    $data['dist_id'] = $dist_id;
    $data['street_id'] = $street_id;
    $data['name'] = $name;
    $data['telno'] = $telno;
    $data['address'] = $address;
    $data['linkman'] = $linkman;
    $data['zip_code'] = $zip_code;
    $data['fax'] = $fax;
    $data['email'] = $email;
    $data['website'] = $website;
    $result = $this->update_by_departmentid_companyid($data, $company_id, 0);
    $xffxdata = array(
      'kcp_name' => $name,
      'city' => $_SESSION[WEB_AUTH]["city"],
      'username' => $telno,
      'realname' => $linkman,
      'password' => md5(''),
      'status' => 1,
      //    'create_time' => time(),
      'update_time' => time(),
      'isdel' => 0,
      'company_id' => $company_id
    );
    //11
    //$this->newhouse_sync_account_base_model->updatecompany($xffxdata);
    /*
    $url = 'http://adminxffx.fang100.com/fktdata/updatecompany';
    $this->load->library('Curl');
    Curl::fktdata($url,$xffxdata);*/
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
   * 查找某公司名下的子公司列表
   * @param int $company_id 公司编号
   * @return array
   */
  function get_children_by_company_id($company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select($this->_select_fields);
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('company_id', $company_id);
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
        if (in_array($search_arr['role_level'], array(1, 2, 3, 4, 5))) {
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
  public function get_signatory_info_by_kw($keyword = '', $search_arr = 0, $limit = 10, $order_key = 'id', $order = 'ASC')
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
      $cmt_info = $this->dbback_city->get('signatory_info', $limit)->result_array();
      //echo $this->dbback_city->last_query();
    }

    return $cmt_info;
  }

  /**
   * 查找某公司名下的一级门店
   * @param int $company_id 公司编号
   * @return array
   */
  function get_department_1_by_company_id($company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select(array('id', 'name'));
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('department_id', 0);
    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 查找某公司名下的一级门店
   * @param int $company_id 公司编号
   * @return array
   */
  function get_department_2_by_company_id($company_id)
  {
    //查询字段
    if ($this->_select_fields) {
      $this->dbback_city->select(array('id', 'name'));
    }
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('department_id >', 0);
    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->get($this->_tbl)->result_array();
  }

  /**
   * 查找某公司名下的子公司总数
   * @param int $company_id 公司编号
   * @return int
   */
  function count_childrea_by_company_id($company_id)
  {
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->count_all_results($this->_tbl);

  }

  /**
   * 查找某公司名下的经纪人总数
   * @param int $company_id 公司编号
   * @return int
   */
  function count_childsignatory_by_company_id($company_id)
  {
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('company_id', $company_id);
    //查找经纪人表
    $this->tbl = 'signatory_info';
    return $this->dbback_city->count_all_results($this->tbl);
  }

  /**
   * 查找某门店名下的经纪人总数
   * @param int $department_id 公司编号
   * @return int
   */
  function count_childsignatory_by_department_id($department_id)
  {
    $this->dbback_city->where('status', 1);
    $this->dbback_city->where('department_id', $department_id);
    //查找经纪人表
    $this->tbl = 'signatory_info';
    return $this->dbback_city->count_all_results($this->tbl);
  }

  /**
   * 添加门店
   * @param int $department_id 门店编号
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
        $department_id_arr = array();
        $department_arr = $this->get_children_by_company_id($company_id);
        //去掉自身门店
        if (is_full_array($department_arr)) {
          foreach ($department_arr as $k => $v) {
            if ($v['id'] != $result) {
              $department_id_arr[] = $v['id'];
            }
          }
        }
        if (is_full_array($department_id_arr)) {
          //默认所有权限节点
          $this->load->model('department_purview_node_base_model');
          $func_auth = $this->department_purview_node_base_model->get_all_node_serialize();
          //初始化新增门店对其它门店的权限数据
          $this->load->model('department_purview_base_model');
          $this->department_purview_base_model->init_department_area($company_id, $result, $department_id_arr, $func_auth);
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
   * @param int $department_id 门店编号
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
  public function update_department($department_id, $dist_id, $street_id, $name, $telno,
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
    $result = $this->update_department_byid($data, $department_id);
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
      'store_id' => $department_id
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


  public function update_department_byid($update_data, $department_id)
  {
    return $this->update_by_departmentid_companyid($update_data, $department_id);
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
   * 更新公司数据
   * @param array $update_data 更新的数据源数组
   * @param array $department_id 公司编号数组
   * @param int $company_id 总公司编号
   * @return int 成功后返回受影响的行数
   */
  public function update_by_departmentid_companyid($update_data,
                                                   $department_id, $company_id = '')
  {
    if (is_array($department_id)) {
      $ids = $department_id;
    } else {
      $ids[0] = $department_id;
    }
    $this->db_city->where_in('id', $ids);
    if ($company_id) {
      $this->db_city->where('company_id', $company_id);
    }
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }

  /**
   * 删除公司数据
   * @param array or int $department_id 公司编号 可接受整数或者公司数组编号
   * @param int $company_id 总公司编号
   * @return boolean true 成功 false 失败
   */
  public function delete_by_departmentid_companyid($department_id, $company_id = '')
  {
    //多条删除
    if (is_array($department_id)) {
      $ids = $department_id;
    } else {
      $ids[0] = $department_id;
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
  public function is_purview_initialize_success($company_id)
  {
    $sql = "select count(*) from purview_company_group where company_id = {$company_id}";
    $result = $this->dbback_city->query($sql)->row_array();
    //return $result;
    return $num = $result['count(*)'];
  }

  public function update_company_purview($company_id)
  {
    $this->load->model('purview_system_group_model');
    $system_group = $this->purview_system_group_model->get_all_by(array());
    foreach ($system_group as $key => $val) {
      $this->dbback_city->where('company_id', $company_id);
      $this->dbback_city->where('system_group_id', $val['id']);
      $result = $this->dbback_city->get('purview_company_group')->row_array();

      if (empty($result)) {
        $sql = "insert into purview_company_group (company_id,system_group_id,func_auth) values(" . $company_id . "," . $val['id'] . ",'" . $val['func_auth'] . "')";
        $this->db_city->query($sql);
      }
    }

    $this->dbback_city->where('company_id', $company_id);
    return $this->dbback_city->count_all_results("purview_company_group");
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
