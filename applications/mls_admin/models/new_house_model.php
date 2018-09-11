<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class new_house_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  private $_tbl1 = "district";
  private $_tbl2 = "street";
  private $_tbl3 = "new_house";

  /*
   * 获取地区数据
   * 返回的是地区数组
   */
  public function get_district()
  {
    return $this->dbback_city->get($this->_tbl1)->result_array();
  }

  public function get_district_by($id)
  {
    $this->dbback_city->where("id", $id);
    return $this->dbback_city->get($this->_tbl1)->row_array();
  }

  /*
   * 获取板块数据
   * $dist_id 是地区id
   * 返回的是板块数组
   */
  public function get_street($dist_id)
  {
    $this->dbback_city->where('dist_id', $dist_id);
    return $this->dbback_city->get($this->_tbl2)->result_array();
  }

  public function get_street_by($id)
  {
    $this->dbback_city->where('id', $id);
    return $this->dbback_city->get($this->_tbl2)->row_array();
  }

  /*
   * 获取所有相关的房源
   * $cond_where  条件语句
   * $offset  偏移量
   * $limit   显示显示条数
   * 返回的是房源数组
   */
  public function get_all($cond_where, $offset = 0, $limit = 10, $order_key = "is_over", $order_by = "ASC")
  {

    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    //查询
    $result = $this->dbback_city->get($this->_tbl3, $limit, $offset)->result_array();
    if (!empty($result)) {
      foreach ($result as $key => $val) {
        $street = $this->get_street_by($val['street_id']);
        $district = $this->get_district_by($val['district_id']);
        $result[$key]['street_id'] = $street['streetname'];
        $result[$key]['district_id'] = $district['district'];
      }
    }
    return $result;
  }

  /**
   * 获取符合条件的房源数量
   *
   * @access  public
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_house_num_by($cond_where)
  {
    //出售名称
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    $count_num = $this->dbback_city->count_all_results($this->_tbl3);
    return intval($count_num);
  }

  //确认房源操作
  public function sure_is_over($id, $is_over)
  {
    $this->db_city->where_in("id", $id);
    $this->db_city->update($this->_tbl3, array("is_over" => $is_over));
  }

  /*
   * 添加数据到新房房源表
   * 返回的是表格影响条数
   */
  public function add_data($insert_array)
  {
    //单条插入
    $this->db_city->insert($this->_tbl3, $insert_array);
    return $this->db_city->affected_rows();

  }

  public function update_data($id, $insert_array)
  {
    //单条插入
    $this->db_city->where_in('id', $id);
    $this->db_city->update($this->_tbl3, $insert_array);
    return $this->db_city->affected_rows();

  }

  //删除房源
  public function del_by($id)
  {
    $this->db_city->where_in('id', $id);
    $this->db_city->delete($this->_tbl3);
    return $this->db_city->affected_rows();
  }

  public function get_all_by($id)
  {
    $this->dbback_city->where('id', $id);
    //查询
    $result = $this->dbback_city->get($this->_tbl3)->row_array();
    $apartment = explode("-", $result['apartment']);
    $arr = array("room", "hall", "toilet", "kitchen", "balcony");
    foreach ($apartment as $key => $val) {
      foreach ($arr as $k => $v) {
        $apartment_new[$v] = $val;
      }
    }
    $result['apartment'] = $apartment_new;
    $result['face_img'] = explode(",", $result['face_img']);
    $result['hx_imgurl'] = explode(",", $result['hx_imgurl']);
    return $result;
  }
}
