<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Entrust_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 出售、出租获取符合条件的房源
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的房源信息
   */
  public function get_list_by_cond($cond_where, $tbl, $offset = 0, $limit = 10, $order_key = "status", $order_by = "ASC")
  {
    if ($cond_where) {
      $cond_where = "where " . $cond_where;
    }
    $sql = "select s.id as id,s.phone,is_check,realname,comt_name,area,hprice,ctime,a.name as agency_name,b.truename as broker_name,s.status,grab_times from " . $tbl . " s
                left join broker_info b on s.broker_id = b.id 
                left join agency a on a.id =s.agency_id {$cond_where} order by {$order_key} {$order_by} limit {$offset},{$limit}";
    return $this->dbback_city->query($sql)->result_array();
  }

  /**
   * 抢拍房源名单
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的房源信息
   */
  public function get_list_by_grab($cond_where, $tbl, $offset = 0, $limit = 10, $order_key = "createtime", $order_by = "ASC")
  {
    if ($cond_where) {
      $cond_where = "where " . $cond_where;
    }
    $sql = "select g.*,a.name as agency_name,b.truename as broker_name,b.phone,c.name as company_name from " . $tbl . " g
                left join broker_info b on g.broker_id = b.broker_id 
                left join agency a on a.id =b.agency_id 
				left join agency c on c.id =b.company_id
				{$cond_where} order by {$order_key} {$order_by} limit {$offset},{$limit}";
    return $this->dbback_city->query($sql)->result_array();
  }

  /**
   * 出售、出租获取符合条件的房源
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的房源信息
   */
  public function get_list_by($id, $tbl)
  {
    $sql1 = "select s.id as id,s.uid,s.phone,realname,comt_name,c.dist_id,c.streetid,comt_id,area,hprice,ctime,a.name as agency_name,b.truename as broker_name,s.company_id,s.agency_id,s.broker_id,s.status,is_check from " . $tbl . " s
		        left join community c on c.id = s.comt_id 
                left join broker_info b on s.broker_id = b.id 
                left join agency a on a.id =s.agency_id where s.id=" . $id;
    $result = $this->dbback_city->query($sql1)->row_array();
    return $result;
  }


  /**
   * 获取符合条件的房源数量
   *
   * @access  public
   * @cond_where 查询条件
   * @return  int   符合条件的信息条数
   */
  public function get_num_by($cond_where, $tbl)
  {
    //出售名称
    if ($cond_where) {
      $this->dbback_city->where($cond_where);
    }
    $count_num = $this->dbback_city->count_all_results($tbl);
    return intval($count_num);
  }

  public function get_agency($company_name)
  {
    $sql = "select id from agency where name = '" . $company_name . "'";
    $result = $this->dbback_city->query($sql)->row_array();
    $this->dbback_city->where('company_id', $result['id']);
    $this->dbback_city->where('status', "1");
    return $this->dbback_city->get("agency")->result_array();
  }

  public function get_broker($agency_id)
  {
    $time = time();
    $sql = "select id,truename from broker_info where status = 1 and agency_id = " . $agency_id . " and expiretime>" . $time;
    return $this->dbback_city->query($sql)->result_array();
  }

  public function del_by($id, $tbl)
  {
    $this->db_city->where_in("id", $id);
    $this->db_city->delete($tbl);
    return $this->db_city->affected_rows();
  }

  public function update_status($id, $update_array, $tbl)
  {
    $this->db_city->where_in("id", $id);
    $this->db_city->update($tbl, $update_array);
    return $this->db_city->affected_rows();
  }

  public function get_broker_by_dist($dist_id)
  {
    $this->dbback_city->select('broker_id');
    $this->dbback_city->from('broker_info');
    $this->dbback_city->join('agency', 'agency.id = broker_info.agency_id');
    $this->dbback_city->where('agency.dist_id', $dist_id);
    return $this->dbback_city->get()->result_array();
  }
}

