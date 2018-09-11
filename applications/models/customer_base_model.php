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
 * Customer_base_model CLASS
 *
 * 求购、求租客户信息管理类,提供增加、修改、删除、查询求购、求租客户信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */
class Customer_base_model extends MY_Model
{

  /**
   * 信息录入经纪人编号
   *
   * @access private
   * @var integer
   */
  private $_broker_id = 0;

  /**
   * 求购、求租信息编号
   *
   * @access private
   * @var integer
   */
  private $_id = 0;

  /**
   * 求购求租信息录入城市
   *
   * @access private
   * @var string
   */
  private $_city = NULL;

  /**
   * 求购、求租信息表
   *
   * @access private
   * @var string
   */
  private $_customer_tbl = NULL;

  /**
   * 求购、求租信息表名称数组
   *
   * @access private
   * @var string
   */
  protected $_tbl_arr = array('buy_customer', 'rent_customer');

  /**
   * 求购求租基本配置信息表
   *
   * @access private
   * @var string
   */
  protected $_customer_config_tbl = 'customer_config';


  /**
   * 客源数据状态
   *
   * @access private
   * @var string
   */
  private $_status_arr = array('valid' => 1, 'reserve' => 2, 'deal' => 3,
    'invalid' => 4, 'delete' => 5);

  /**
   * 求购、求租表查询字段
   *
   * @access private
   * @var array
   */
  private $_search_fields = array();

  /**
   * 缓存key
   * @var string
   */
  private $_mem_key = '';

  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();

    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_customer_base_model_';
  }

  /**
   * 初始化求购求租信息编号
   *
   * @access  public
   * @param  int $id 求购求租信息编号
   * @return  void
   */
  public function set_id($id)
  {
    $this->_id = intval($id);
  }


  /**
   * 获取求购求租信息编号
   *
   * @access  public
   * @param  void
   * @return  int 求购求租信息编号
   */
  public function get_id()
  {
    return $this->_id;
  }


  /**
   * 初始化经纪人帐号编号
   *
   * @access  public
   * @param  int $broker_id 经纪人帐号编号
   * @return  void
   */
  public function set_broker_id($broker_id)
  {
    $this->_broker_id = intval($broker_id);
  }


  /**
   * 获取经纪人帐号编号
   *
   * @access  public
   * @param  void
   * @return  int 经纪人帐号编号
   */
  public function get_broker_id()
  {
    return intval($this->_broker_id);
  }


  /**
   * 设置求购、求组信息表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_tbl($tbl_name)
  {
    $this->_customer_tbl = strip_tags($tbl_name);
  }


  /**
   * 获取求购、求组信息表名称
   *
   * @access  public
   * @param  void
   * @return  string 求购、求组信息表名称
   */
  public function get_tbl()
  {
    return $this->_customer_tbl;
  }


  /**
   * 设置求购、求租配置表名称
   *
   * @access  public
   * @param  string $tbl_name 表名称
   * @return  void
   */
  public function set_customer_config_tbl($tbl_name)
  {
    $this->_customer_config_tbl = strip_tags($tbl_name);
  }


  /**
   * 获取求购、求租配置表名称
   *
   * @access  public
   * @param  void
   * @return  string 求购、求租配置表名称
   */
  public function get_customer_config_tbl()
  {
    return $this->_customer_config_tbl;
  }


  /**
   * 设置的房源需求信息表需要查询的字段数组
   *
   * @access  public
   * @param  array $arr_fields 房源信息字段
   * @return  void
   */
  public function set_search_fields($arr_fields)
  {
    $this->_search_fields = $arr_fields;
  }


  /**
   * 获取设置的房源需求信息表需要查询的字段数组
   *
   * @access  public
   * @param  void
   * @return  array  房源需求信息表需要查询的字段数组
   */
  public function get_search_fields()
  {
    return $this->_search_fields;
  }


  /**
   * 获取客源信息状态
   *
   * @access  public
   * @param  void
   * @return  array
   */
  public function get_status_arr()
  {
    return $this->_status_arr;
  }


  /**
   * 获取求购、求租信息基础配置信息
   *
   * @access  public
   * @param  string $type 基本信息类型 buy-求购/rent-求租
   * @param  int $key 需求信息编号数组 配置信息Key值
   * @param  int $status 求购求租配置信息状态
   * @return  array 配置信息数组
   */
  public function get_base_conf($type = '', $sort = -1, $status = -1)
  {
    $conf_arr = array();
    $conf_tbl = $this->get_customer_config_tbl();

    $type = strip_tags($type);

    if ($conf_tbl != '') {
      $cond_where = '';
      $mem_key = 'customer_base_model_get_base_conf_' . $conf_tbl;
      $cache = $this->mc->get($mem_key);
      if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
        $conf_arr_temp = $cache['data'];
      } else {
        //查询
        $where_cond = array('status' => 1);
        $conf_arr_temp = $this->get_data(array('form_name' => $conf_tbl, 'where' => $where_cond), 'dbback');

        //缓存区属信息
        if (is_array($conf_arr_temp) && !empty($conf_arr_temp))
          $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $conf_arr_temp), 43200);
      }

      //根据查询条件获取需要的数据
      if (is_array($conf_arr_temp) && !empty($conf_arr_temp)) {
        foreach ($conf_arr_temp as $key => $value) {
          //不做KEY查询
          if ($type == '') {
            $conf_arr[$value['type']][$value['sort']] = $value['name'];
          } else {
            //根据KEY查询
            if ($type == $value['type']) {
              //不根据排序号查找
              if ($sort == -1) {
                $conf_arr[$value['type']][$value['sort']] = $value['name'];
                continue;
              } else {
                //不限制状态
                if ($status == -1) {
                  $conf_arr[$value['type']][$sort] = $value['name'];
                  break;
                } else {
                  if ($status == $value['status']) {
                    $conf_arr[$value['type']][$sort] = $value['name'];
                    break;
                  }
                }
              }
            }
          }
        }
      }
    }

    return $conf_arr;
  }


  /**
   * 添加求购求租配置信息
   *
   * @access  public
   * @param  array $conf_arr 配置信息
   * @return  array 配置信息数组
   */
  public function add_customer_info($conf_arr)
  {
    if (is_array($conf_arr) && !empty($conf_arr)) {
      foreach ($conf_arr as $key => $value) {
        foreach ($value as $sort => $name) {
          $data_info = array('type' => $key, 'name' => $name,
            'sort' => $sort);
          $this->db->insert('customer_config', $data_info);
        }
      }
    }

    return ($this->db->affected_rows() >= 1) ? TRUE : FALSE;
  }


  /**
   * 添加求购、求租需求信息
   *
   * @access  protected
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE失败。
   */
  protected function add_info($data_info)
  {
    $this->db_city->insert($this->get_tbl(), $data_info);
    return ($this->db_city->affected_rows() == 1) ? $this->db_city->insert_id() : FALSE;
  }


  /**
   * 根据编号删除求购、求租需求信息
   *
   * @access  protected
   * @return  int 删除条数，0删除失败
   */
  protected function delete_info_by_id()
  {
    $id = $this->get_id();

    $tbl_name = $this->get_tbl();

    $this->db_city->delete($tbl_name, array('id' => $id));

    return $this->db_city->affected_rows();
  }


  /**
   * 根据多个编号批量删除求购、求租需求信息
   *
   * @access  protected
   * @param  array $arr_ids 需求信息编号数组
   * @return  int 删除条数，0删除失败
   */
  protected function delete_info_by_ids($arr_ids)
  {
    $tbl_name = $this->get_tbl();

    if (isset($arr_ids) && !empty($arr_ids)) {
      //查询字段
      $arr_ids_str = implode(',', $arr_ids);
      $cond_where = "id IN(" . $arr_ids_str . ")";

      $this->db_city->where($cond_where);
      $this->db_city->delete($tbl_name);
    }

    return $this->db_city->affected_rows();
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
  protected function update_info_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $tbl_name = $this->get_tbl();

    if ($tbl_name == '' || empty($update_arr) || $cond_where == '') {
      return 0;
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
   * 更新客源需求信息
   *
   * @access  protected
   * @param  mixed $ids 单个ID或者ID数组
   * @param  array $update_arr 需要更新字段的键值对
   * @param  boolean $escape 是否转义更新字段的值
   * @return  int  更新条数
   */
  public function update_info_by_id($ids, $update_arr, $escape = TRUE)
  {
    $update_num = 0;

    if (!empty($ids) && is_array($update_arr) && !empty($update_arr)) {
      if (is_array($ids)) {
        $customer_id_str = implode(',', $ids);
        $cond_where = "id IN (" . $customer_id_str . ") ";
        $update_num = $this->update_info_by_cond($update_arr, $cond_where, $escape);
      } else {
        $cond_where = "id = " . $ids;
        $update_num = $this->update_info_by_cond($update_arr, $cond_where, $escape);
      }
    }

    return $update_num;
  }


  /**
   * 获取符合条件的房源需求信息条数
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_count_by_cond($cond_where)
  {
    $count_num = 0;

    //房源需求信息表
    $tbl_name = $this->get_tbl();

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
      $count_num = $this->dbback_city->count_all_results($tbl_name);
    }

    return intval($count_num);
  }


  /**
   * 获取符合条件的房源需求信息列表
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   求购求租信息列表
   */
  public function get_list_by_cond($cond_where, $offset = 0, $limit = 10,
                                   $order_key = 'updatetime', $order_by = 'DESC')
  {
    //房源需求信息表
    $tbl_demand = $this->get_tbl();

    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();

    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields);
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);

    if ($offset >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $offset);
    }

    //查询
    $arr_data = $this->dbback_city->get($tbl_demand)->result_array();

    return $arr_data;
  }


  /**
   * 根据条件获取求购、求租信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 求购、求租信息
   */
  protected function get_info_by_cond($cond_where)
  {
    $arr_data = array();

    //获取表名称
    $tbl_name = $this->get_tbl();

    //获得需要查询的求购、求租信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields_str);
    }

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->where($cond_where);
    }

    //查询
    $arr_data = $this->dbback_city->get($tbl_name)->row_array();

    return $arr_data;
  }

  /**
   * 根据条件获取求购、求租信息(主库取数据)
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 求购、求租信息
   */
  protected function get_info_by_cond_2($cond_where)
  {
    $arr_data = array();

    //获取表名称
    $tbl_name = $this->get_tbl();

    //获得需要查询的求购、求租信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      $select_fields_str = implode(',', $select_fields);
      $this->db_city->select($select_fields_str);
    }

    //查询条件
    if ($cond_where != '') {
      $this->db_city->where($cond_where);
    }

    //查询
    $arr_data = $this->db_city->get($tbl_name)->row_array();

    return $arr_data;
  }


  /**
   * 根据条件获取求购、求租信息
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  array 求购、求租信息
   */
  public function get_newinfo_by_id($id)
  {
    $arr_data = array();

    //获取表名称
    $tbl_name = $this->get_tbl();

    $cond_where = "id = '" . $id . "'";

    //查询条件
    if ($cond_where != '') {
      $this->db_city->where($cond_where);
    }

    //查询
    $arr_data = $this->db_city->get($tbl_name)->row_array();

    return $arr_data;
  }


  /**
   * 根据求购、求租编号获取求购、求租信息
   *
   * @access  protected
   * @return  array 求购求租信息
   */
  public function get_info_by_id()
  {
    $demandinfo = array();

    //获取求购、求租信息编号
    $id = $this->get_id();

    if ($id <= 0) {
      return $demandinfo;
    }

    $cond_where = "id = '" . $id . "'";
    $demandinfo = $this->get_info_by_cond($cond_where);

    return $demandinfo;
  }

  /**
   * 根据求购、求租编号获取求购、求租信息(主库取数据)
   *
   * @access  protected
   * @return  array 求购求租信息
   */
  public function get_info_by_id_2()
  {
    $demandinfo = array();

    //获取求购、求租信息编号
    $id = $this->get_id();

    if ($id <= 0) {
      return $demandinfo;
    }

    $cond_where = "id = '" . $id . "'";
    $demandinfo = $this->get_info_by_cond_2($cond_where);

    return $demandinfo;
  }

  /**
   * 根据经纪人的id获取求购客源信息
   *
   * @access  protected
   * @return  array 求购求租信息
   */
  public function get_customer($where)
  {

    $custome_arr = array();
    $custome_tbl = $this->get_tbl();
    if ($where != '') {

      $custome_arr = $this->dbback_city->where($where)->get($custome_tbl)->result_array();

    }

    return $custome_arr;
  }


  /**
   * 更改经纪人私客的门店编号
   *
   * @access  public
   * @param  int $broker_id 经纪人编号
   * @param  int $new_agency_id 新的门店编号
   * @param  int $old_agency_id 经纪人原来所在门店编号
   * @return  int 影响行数
   */
  public function update_private_customer_info_by_brokerid($broker_id,
                                                           $new_agency_id, $old_agency_id = '')
  {
    $new_agency_id = intval($new_agency_id);
    $broker_id = intval($broker_id);
    $old_agency_id = intval($old_agency_id);
    $result = 0;

    if ($broker_id > 0 && $new_agency_id > 0) {
      //更新条件
      $cond_where = "broker_id = '" . $broker_id . "'";
      if ($old_agency_id > 0) {
        $cond_where .= " AND agency_id = '" . $old_agency_id . "'";
      }
      //更新字段
      $update_arr['agency_id'] = $new_agency_id;
      $result = $this->update_info_by_cond($update_arr, $cond_where);
    }

    return $result;
  }

  /**
   * 更改经纪人私客的公司编号
   *
   * @access  public
   * @param  int $broker_id 经纪人编号
   * @param  int $new_company_id 新的门店编号
   * @param  int $old_company_id 经纪人原来所在门店编号
   * @return  int 影响行数
   */
  public function update_private_customer_info_by_companyid($broker_id,
                                                            $new_company_id, $old_company_id = '')
  {
    $new_company_id = intval($new_company_id);
    $broker_id = intval($broker_id);
    $old_company_id = intval($old_company_id);
    $result = 0;

    if ($broker_id > 0 && $new_company_id > 0) {
      //更新条件
      $cond_where = "broker_id = '" . $broker_id . "'";
      if ($old_company_id > 0) {
        $cond_where .= " AND company_id = '" . $old_company_id . "'";
      }
      //更新字段
      $update_arr['company_id'] = $new_company_id;
      $result = $this->update_info_by_cond($update_arr, $cond_where);
    }

    return $result;
  }

  /**
   * 自定义语句执行
   */
  public function query($sql)
  {
    $result = false;
    if ($sql) {
      $result = $this->db_city->query($sql);
    }
    return $result;
  }

  //公司分店下拉列表框数据
  public function get_agency_norepeat($where)
  {
    $sql = "select id,name as store_name from agency " . $where;
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  //获得分店下的所有经纪人
  public function get_all_by($aid, $cid)
  {
    $agency_id = $aid;
    $company_id = $cid;
    $time = time();
    $sql = "select truename,broker_id from broker_info where agency_id= '" . $agency_id . "' and company_id = '" . $company_id . "' and expiretime >=  {$time} and status = 1";
    $result = $this->dbback_city->query($sql)->result_array();
    return $result;
  }

  /**
   * 在指定时间内设置为合作的最新一条房源
   * @param int $in_time 时间内  单位秒
   * @return array
   */
  public function get_coop_in_time($in_time = 300)
  {
    //房源需求信息表
    $tbl_demand = $this->get_tbl();
    //需要查询的房源需求信息字段
    $select_fields = $this->get_search_fields();
    if (isset($select_fields) && !empty($select_fields)) {
      //查询字段
      $select_fields_str = implode(',', $select_fields);
      $this->dbback_city->select($select_fields_str);
    }
    $to_time = time();
    $from_time = $to_time - $in_time;
    $cond_where = 'is_share = 1 AND updatetime >= ' . $from_time
      . ' AND updatetime <= ' . $to_time;
    //查询条件.
    $this->dbback_city->where($cond_where);
    //排序条件
    $this->dbback_city->order_by('id', 'DESC');
    $this->dbback_city->limit(1);
    //查询
    return $this->dbback_city->get($tbl_demand)->row_array();
  }
}

/* End of file customer_base_model.php */
/* Location: ./applications/models/customer_base_model.php */
