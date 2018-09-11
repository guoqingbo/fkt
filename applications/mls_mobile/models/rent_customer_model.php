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
   * @access  public
   * @param   array $data_info 客户需求信息数组
   * @return  boolean 是否添加成功，TRUE-成功，FAlSE-失败。
   */
  public function add_rent_customer_info($data_info)
  {
    $result = parent::add_info($data_info);

    return $result;
  }

  /**
   * 获取符合条件的客源需求信息列表
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @param  int $offset 偏移数,默认值为0
   * @param  int $limit 每次取的条数，默认值为10
   * @param  string $order_key 排序字段，默认值
   * @param  string $order_by 升序、降序，默认降序排序
   * @return  array   求购求租信息列表
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
    $this->dbselect('db_city');
    $sql = "";
    if (!empty($customer_id)) {
      $sql = " SELECT * FROM  `rent_customer` WHERE id IN ($customer_id) ";
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();

    }
    return $result_arr;
  }


  /**
   * 获取符合条件的客源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的求购信息条数
   */
  public function get_rentnum_by_cond($cond_where = '')
  {
    $buynum = 0;

    $buynum = parent::get_count_by_cond($cond_where);

    return $buynum;
  }


  /**
   * 更新某条客源需求信息
   *
   * @access  public
   * @param  array $update_arr 需要更新字段的键值对
   * @param  string $cond_where 更新条件
   * @param  boolean $escape 是否转义更新字段的值
   * @return  boolean 是更新成功，TRUE-成功，FAlSE失败。
   */
  public function update_customerinfo_by_cond($update_arr, $cond_where, $escape = TRUE)
  {
    $result = FALSE;
    $result = parent::update_info_by_cond($update_arr, $cond_where, $escape);
    return $result;
  }

  //出租客源验证数组
  public function checkarr($arr)
  {
    $data = array();
    //加载求购、求租基本配置MODEL
    $this->load->model('customer_base_model');
    $data['config'] = $this->customer_base_model->get_base_conf();

    if (!empty($arr[0]) && !eregi("[^\x80-\xff]", "$arr[0]")) { //客户姓名不为空并且为中文
      $res[0] = true;
    } else {
      $res[0] = false;
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
          }
        }
      } else {
        $res[1] = false;
      }
    } else {
      $res[1] = false;
    }

    if (!empty($arr[2])) { //性质不能为空
      $public_type = $data['config']['public_type'];
      if (in_array($arr[2], $public_type)) {
        $res[2] = true;
      } else {
        $res[2] = false;
      }
    } else {
      $res[2] = false;
    }
    if (!empty($arr[3])) { //合作不能为空
      $nature = array('是', '否');
      if (in_array($arr[3], $nature)) {
        $res[3] = true;
      } else {
        $res[3] = false;
      }
    } else {
      $res[3] = false;
    }
    if (!empty($arr[4]) && eregi("^[0-9]+$", $arr[4])) { //户型上限不为空并且为数字
      $res[4] = true;
    } else {
      $res[4] = false;
    }
    if (!empty($arr[5]) && eregi("^[0-9]+$", $arr[5])) {  //户型下限不为空并且为数字
      $res[5] = true;
    } else {
      $res[5] = false;
    }
    if (!empty($arr[6]) && eregi("^[0-9]+$", $arr[6])) { //面积上限不为空并且为数字
      $res[6] = true;
    } else {
      $res[6] = false;
    }
    if (!empty($arr[7]) && eregi("^[0-9]+$", $arr[7])) {  //面积下限不为空并且为数字
      $res[7] = true;
    } else {
      $res[7] = false;
    }
    if (!empty($arr[8]) && eregi("^[0-9]+$", $arr[8])) { //售价上限不为空并且为数字
      $res[8] = true;
    } else {
      $res[8] = false;
    }
    if (!empty($arr[9]) && eregi("^[0-9]+$", $arr[9])) {  //售价下限不为空并且为数字
      $res[9] = true;
    } else {
      $res[9] = false;
    }

    $c = '[\x{4e00}-\x{9fa5}]+'; //汉字编码
    if (preg_match("/^({$c}(\-{$c})?\/){1,3}$/u", $arr[10] . '/')) {  //意向属性板块
      $res[10] = true;
    } else {
      $res[10] = false;
    }

    if (($res[0] == true) && ($res[1] == true) && ($res[2] == TRUE) && ($res[3] == TRUE) && ($res[4] == TRUE) && ($res[5] == TRUE) && ($res[6] == TRUE) && ($res[7] == TRUE) && ($res[8] == TRUE) && ($res[9] == TRUE) && ($res[10] == TRUE)) {
      return true;
    } else {
      return false;
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
}

/* End of file buy_customer_model.php */
/* Location: ./applications/mls/models/buy_customer_model.php */
