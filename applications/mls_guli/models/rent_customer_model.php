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
 * rent_customer_model CLASS
 *
 * 求租客户信息管理类,提供增加、修改、删除、查询 求购客户信息的方法。
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          xz
 */

//加载父类文件
load_m('Customer_base_model');

class Rent_customer_model extends Customer_base_model
{

  /**
   * 信息录入经纪人编号
   *
   * @access private
   * @var string
   */
  private $_rent_customer_tbl = 'rent_customer';


  /**
   * Constructor
   */
  public function __construct()
  {
    parent::__construct();

    //初始化表名称
    $this->set_tbl($this->_rent_customer_tbl);
    $this->district = 'district';
    $this->street = 'street';

  }


  /**
   * 添加求租客户信息
   *
   * @access    public
   * @param   array $data_info 客户需求信息数组
   * @return    boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_rent_customer_info($data_info)
  {
    $result = parent::add_info($data_info);

    return $result;
  }

  /**
   * 获取符合条件的客源需求信息列表
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @param    int $offset 偏移数,默认值为0
   * @param    int $limit 每次取的条数，默认值为10
   * @param    string $order_key 排序字段，默认值
   * @param    string $order_by 升序、降序，默认降序排序
   * @return    array   求购求租信息列表
   */
  public function get_rentlist_by_cond($cond_where, $offset = 0, $limit = 10,
                                       $order_key = 'updatetime', $order_by = 'DESC')
  {
    $arr_data = array();

    $arr_data = parent::get_list_by_cond($cond_where, $offset, $limit, $order_key, $order_by);
    return $arr_data;
  }

  /**
   * 根据多个customer_id查询多条房源信息
   * @param  customer_id字段
   * @return array
   */

  public function get_all_customer_by_ids($customer_id)
  {
    $this->dbselect('dbback_city');
    $sql = "";
    if (!empty($customer_id)) {
      $sql = " SELECT * FROM  `rent_customer` WHERE id IN ($customer_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

  /**
   * 根据多个id查询多条客源合作的信息
   * @param  id字段
   * @return array
   */

  public function get_all_isshare_by_ids($id)
  {
    $this->dbselect('dbback_city');

    if (!empty($id)) {
      $sql = " SELECT is_share FROM  `rent_customer` WHERE id IN ($id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }


  /**
   * 获取符合条件的客源需求信息条数
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的求购信息条数
   */
  public function get_rentnum_by_cond($cond_where = '')
  {
    $buynum = 0;

    $buynum = parent::get_count_by_cond($cond_where);

    return $buynum;
  }

  /**
   * 获取符合条件的房源id需求信息条数
   *
   * @access    protected
   * @param    string $cond_where 查询条件
   * @return    int   符合条件的出售信息条数
   */
  public function get_id_by_signatoryid($signatory_id)
  {
    $this->dbback_city->select("id");
    $this->dbback_city->where("signatory_id =" . $signatory_id);
    $this->dbback_city->from($this->_rent_customer_tbl);
    return $this->dbback_city->get()->result_array();
  }

  /**
   * 更新某条客源需求信息
   *
   * @access    public
   * @param    array $update_arr 需要更新字段的键值对
   * @param    string $cond_where 更新条件
   * @param    boolean $escape 是否转义更新字段的值
   * @return    boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_customerinfo_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $result = FALSE;
    $result = parent::update_info_by_cond($update_arr, $cond_where, $escape);
    return $result;
  }

  //出租客源验证数组
  public function checkarr($arr, $signatory_info, $view_import_customer)
  {
    $data = array();
    $data_fail = array();
    //加载求购、求租基本配置MODEL
    $this->load->model('customer_base_model');
    $data['config'] = $this->customer_base_model->get_base_conf();

    if (!empty($arr[0]) && !eregi("[^\x80-\xff]", "$arr[0]")) { //客户姓名不为空并且为中文
      $res[0] = true;
    } else {
      $res[0] = false;
      $data_fail[] = 0;
    }

    if (!empty($arr[1])) { //联系电话不为空
      $tel = explode("/", $arr[1]);
      if (count($tel) < 4) {
        $isMob = "/^1[3-5,8]{1}[0-9]{9}$/";
        $isTel = "/^([0-9]{3,4})?[0-9]{7,8}$/";
        foreach ($tel as $vo => $v) {
          if (preg_match($isMob, $v) || preg_match($isTel, $v)) {
            $res[1] = true;
          } else {
            $res[1] = false;
            $data_fail[] = 1;
          }
        }
      } else {
        $res[1] = false;
        $data_fail[] = 1;
      }
    } else {
      $res[1] = false;
      $data_fail[] = 1;
    }
    if (!empty($arr[2])) { //状态不能为空
      $nature = array('有效', '预定', '成交', '无效', '注销');
      if (in_array($arr[2], $nature)) {
        $res[2] = true;
      } else {
        $res[2] = false;
        $data_fail[] = 2;
      }
    } else {
      $res[2] = false;
      $data_fail[] = 2;
    }
    if (!empty($arr[3])) { //性质不能为空
      $public_type = $data['config']['public_type'];
      if (in_array($arr[3], $public_type)) {
        $res[3] = true;
      } else {
        $res[3] = false;
        $data_fail[] = 3;
      }
    } else {
      $res[3] = false;
      $data_fail[] = 3;
    }
    if (!empty($arr[4])) { //物业类型不能为空
      $property_type = $data['config']['property_type'];
      if (in_array($arr[4], $property_type)) {
        $res[4] = true;
      } else {
        $res[4] = false;
        $data_fail[] = 4;
      }
    } else {
      $res[4] = false;
      $data_fail[] = 4;
    }
    if (!in_array($arr[4], array('住宅', '别墅'))) {
      $res[5] = true;
      $res[6] = true;
    } else {
      if (!empty($arr[5]) && eregi("^[0-9]+$", $arr[5])) { //户型上限不为空并且为数字
        $res[5] = true;
      } else {
        $res[5] = false;
        $data_fail[] = 5;
      }
      if (!empty($arr[6]) && eregi("^[0-9]+$", $arr[6])) {  //户型下限不为空并且为数字
        $res[6] = true;
      } else {
        $res[6] = false;
        $data_fail[] = 6;
      }
    }
    if (!empty($arr[7]) && eregi("^[0-9]+$", $arr[7])) { //面积上限不为空并且为数字
      $res[7] = true;
    } else {
      $res[7] = false;
      $data_fail[] = 7;
    }
    if (!empty($arr[8]) && eregi("^[0-9]+$", $arr[8])) {  //面积下限不为空并且为数字
      $res[8] = true;
    } else {
      $res[8] = false;
      $data_fail[] = 8;
    }
    if (!empty($arr[9]) && eregi("^[0-9]+$", $arr[9])) { //售价上限不为空并且为数字
      $res[9] = true;
    } else {
      $res[9] = false;
      $data_fail[] = 9;
    }
    if (!empty($arr[10]) && eregi("^[0-9]+$", $arr[10])) {  //售价下限不为空并且为数字
      $res[10] = true;
    } else {
      $res[10] = false;
      $data_fail[] = 10;
    }

    $c = '[\x{4e00}-\x{9fa5}]+'; //汉字编码
    if (preg_match("/^({$c}(\-{$c})?\/){1,3}$/u", $arr[11] . '/')) {  //意向属性板块
      $res[11] = true;
    } else {
      $res[11] = false;
      $data_fail[] = 11;
    }
    //判断权限
    if ($view_import_customer['auth']) //有权限 --判断级别
    {
      //判断role_level
      if ($signatory_info['role_level'] < 6) //公司
      {
        $view_import_customer['area'] = 1;
      } else if ($signatory_info['role_level'] >= 6 && $signatory_info['role_level'] <= 7) //店长
      {
        $view_import_customer['area'] = 2;
      } else {
        $view_import_customer['area'] = 3;//本人
      }
    }
    //加载经纪人模型
    $this->load->model('signatory_info_model');
    //通过电话号码查找经纪人信息
    $signatory = array();
    if (!empty($arr[12])) { //电话不能为空
      $signatory = $this->signatory_info_model->get_one_by(array('phone' => $arr[12]));
      if ($view_import_customer['area'] == 1
        && $signatory['company_id'] == $signatory_info['company_id']
      ) {
        $res[12] = true;
      } else if ($view_import_customer['area'] == 2
        && $signatory['department_id'] == $signatory_info['department_id']
      ) {
        $res[12] = true;
      } else if ($view_import_customer['area'] == 3
        && $signatory['signatory_id'] == $signatory_info['signatory_id']
      ) {
        $res[12] = true;
      } else {
        $res[12] = false;
        $data_fail[] = 12;
      }
    } else {
      $res[12] = false;
      $data_fail[] = 12;
    }
    if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE) && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE) && ($res[9] == TRUE) && ($res[10] == TRUE) && ($res[11] == TRUE) && ($res[12] == TRUE)) {
      return 'pass';
    } else {
      return $data_fail;
    }
  }

  public function dist_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $comm = $this->get_data(array('form_name' => $this->district, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  public function street_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'db_city')
  {
    $comm = $this->get_data(array('form_name' => $this->street, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  /**
   * 根据公司id获得当前公司合作流程结束的客源id
   * @param int $company_id 公司id
   * @return $result_arr 客源id
   */
  public function get_customer_cooperate_end_by_company_id($company_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($company_id)) {
      $where_sql = 'SELECT distinct rent_customer.id FROM `rent_customer` left join `cooperate` on rent_customer.id = cooperate.customer_id ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.esta in (5,6,8,9,10,11) ';
      $where_sql .= ' and rent_customer.company_id = "' . intval($company_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 根据公司id获得当前公司刚发起合作申请的客源
   * @param int $company_id 公司id
   * @return $result_arr 客源id
   */
  public function get_customer_id_esta_1_by_company_id($company_id = 0)
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    if (!empty($company_id)) {
      $where_sql = 'SELECT distinct rent_customer.id FROM `rent_customer` left join `cooperate` on rent_customer.id = cooperate.customer_id ';
      $where_sql .= ' where cooperate.tbl = "rent" and cooperate.step = 1 and cooperate.esta = 1 ';
      $where_sql .= 'and rent_customer.company_id = "' . intval($company_id) . '"';
      $query = $this->db->query($where_sql);
      $result_arr = $query->result_array();
    }
    return $result_arr;
  }

  /**
   * 获得合作生效和交易成功的客源
   * @param array $where where字段
   * @return array 房源id
   */
  public function get_customer_id_esta_4_7()
  {
    $this->dbselect('dbback_city');
    $where_sql = '';
    $result_arr = array();
    $where_sql = 'SELECT distinct customer_id FROM `cooperate` ';
    $where_sql .= ' where tbl = "rent" and (esta = 4 or esta = 7) and apply_type=2';
    $query = $this->db->query($where_sql);
    $result_arr = $query->result_array();
    return $result_arr;
  }

  public function change_is_share_by_customer_id($customer_id_arr, $type = 0)
  {
    $customer_id_str = '';
    $result = false;
    if (is_full_array($customer_id_arr) && !empty($type)) {
      foreach ($customer_id_arr as $k => $v) {
        $customer_id_str .= $v['id'] . ',';
      }
      $customer_id_str = trim($customer_id_str, ',');
      $data = array();
      $data['is_share'] = (1 == $type) ? 0 : 1;
      $old_share = (1 == $type) ? 1 : 2;
      $cond_where = "id in ($customer_id_str) and is_share = '" . $old_share . "'";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
    return $result;
  }

  public function change_is_share_not_customer_id($customer_id_arr, $department_id = 0)
  {
    $customer_id_str = '';
    $result = false;
    if (is_full_array($customer_id_arr) && !empty($department_id)) {
      foreach ($customer_id_arr as $k => $v) {
        $customer_id_str .= $v['customer_id'] . ',';
      }
      $customer_id_str = trim($customer_id_str, ',');
      $data = array();
      $data['is_share'] = 0;
      $old_share = 1;
      $cond_where = "department_id = '" . "$department_id" . "' and id not in ($customer_id_str) and is_share = '" . $old_share . "'";
      $result = parent::update_info_by_cond($data, $cond_where);
    }
    return $result;
  }

  //修改合作的方法 isshare 默认0是不合作 当为2的时候是审核状态
  function change_isshare_status($where, $data)
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->_rent_customer_tbl);
    return $result;
  }


  /**
   * 根据门店id获得合作待审核的客源id
   * @param  department_id字段
   * @return array
   */

  public function get_isshare_2_customer_id_by_department_id($department_id)
  {
    $this->dbselect('dbback_city');

    if (!empty($department_id)) {
      $sql = " SELECT id FROM  `rent_customer` WHERE is_share = 2 AND department_id = '" . $department_id . "'";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }

}

/* End of file buy_customer_model.php */
/* Location: ./applications/mls_guli/models/buy_customer_model.php */
