<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS-admin
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * buy_customer_info_model CLASS
 *
 * 后台客源管理求租模块类
 *
 * @package         MLS-admin
 * @subpackage      Models
 * @category        Models
 * @author          kang
 */
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
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
    $this->district = 'district';
    $this->street = 'street';
    //初始化表名称
    $this->set_tbl($this->_rent_customer_tbl);
    $this->tmp_uploads = 'tmp_uploads';
    $this->community = 'community';
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
   * 获取符合条件的房源需求信息条数
   *
   * @access  protected
   * @param  string $cond_where 查询条件
   * @return  int   符合条件的信息条数
   * @author  kang
   */
  public function get_total_by_cond($cond_where)
  {
    $count_num = 0;

    //查询条件
    if ($cond_where != '') {
      $this->dbback_city->select('count(*)');
      $this->dbback_city->from('rent_customer as a');
      $this->dbback_city->join('broker_info as b', 'a.broker_id = b.broker_id');
      $this->dbback_city->join('agency as c', 'a.agency_id = c.id');
      $this->dbback_city->where($cond_where);
      $count_num = $this->dbback_city->count_all_results();
    }

    return intval($count_num);
  }

  /**
   * 获取符合条件的所有求租客源信息
   *
   * @param $cond_where   查询条件
   * @param int $offset 偏移量
   * @param int $limit 每页查询数据条数
   * @param string $order_key 排序字段
   * @param string $order_by 排序方式（升序、降序）
   * @return array
   * @author   kang
   */
  public function get_rentcustomerlist_by_cond($cond_where, $offset = 0, $limit = 10,
                                               $order_key = 'a.id', $order_by = 'ASC')
  {
    //客源需求信息表
    $this->dbback_city->select('a.*,b.id bid,b.phone,c.name agency_name');
    $this->dbback_city->from('rent_customer as a');
    $this->dbback_city->join('broker_info as b', 'a.broker_id = b.broker_id');
    $this->dbback_city->join('agency as c', 'a.agency_id = c.id');
    //查询条件
    $this->dbback_city->where($cond_where);

    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    $this->dbback_city->limit($limit, $offset);

    //查询
    $arr_data = $this->dbback_city->get()->result_array();
    return $arr_data;
  }

  /**
   * 根据ID获取求租客源信息
   *
   * @param $id   客源ID
   * @return   array
   */
  public function get_customer_info_by_id($id)
  {
    $condition = "a.status != 5 AND a.id = " . $id;

    $this->dbback_city->select('a.*');
    $this->dbback_city->from('rent_customer as a');
    $this->dbback_city->join('broker_info as b', 'a.broker_id = b.broker_id');
    $this->dbback_city->join('agency as c', 'a.agency_id = c.id');

    $this->dbback_city->where($condition);

    $row = $this->dbback_city->get()->result_array();
    return $row;
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

  /**
   * 根据条件获取跟进信息
   */
  public function get_follows_by_cond($condition)
  {

    $this->dbback_city->select('a.date,c.follow_name, a.text, b.truename, b.broker_name');
    $this->dbback_city->from('detailed_follow as a');
    $this->dbback_city->join('rent_customer as b', 'a.customer_id = b.id');
    $this->dbback_city->join('follow_up as c', 'a.follow_way = c.id');
    $this->dbback_city->where($condition);

    $rows = $this->dbback_city->get()->result_array();
    //echo $this->dbback_city->last_query();
    return $rows;
  }

  /**
   * 根据ID更新客源删除信息
   */
  public function upd_info_by_id($customer_id)
  {
    $data = array('status' => 5);
    $where = "id = " . $customer_id;
    $rs = $this->db_city->update('rent_customer', $data, $where);

    return $rs;
  }

  public function checkarr_taizhou($arr)
  {
    $data = array();
    $data_fail = array();
    //加载求购、求租基本配置MODEL
    $this->load->model('customer_base_model');
    $data['config'] = $this->customer_base_model->get_base_conf();
    $community_arr = explode("/", $arr[0]);
    //if(!empty($arr[0]) && !eregi("[^\x80-\xff]","$arr[0]")){ //楼盘名称不为空并且为中文
    if (!empty($community_arr) && is_full_array($community_arr)) { //楼盘名称不为空
      foreach ($community_arr as $key => $vo) {
        $where['cmt_name'] = $arr[0];
        $community_info = $this->community_info($where);
        if ($community_info[0]['id']) {
          $res[0] = true;
          //判断此楼盘有无板块没有的话添加上去
          if (!$community_info[0]['streetid'] && $arr[23]) {
            $this->load->model('district_base_model');
            $this->load->model('community_model');
            $street_arr = $this->district_base_model->get_street_id($arr[23]);
            if (!empty($street_arr)) {
              $modify_result = $this->community_model->modifycommunity($community_info[0]['id'], array('streetid' => $street_arr['id']));//楼盘数据入库
            }
          }
        } else {
          //判断是否楼盘需要加入临时小区
          $this->load->model('district_base_model');
          if (!empty($arr[22])) {  //区属不能空
            $dist_arr = $this->district_base_model->get_district_id($arr[22]);
            if (!empty($dist_arr)) {
              $res[22] = true;
            } else {
              $res[22] = false;
              $data_fail[] = 22;
            }
          } else {
            $res[22] = false;
            $data_fail[] = 22;
          }

          /*if(!empty($arr[23])){  //板块不能空
                        if(!empty($dist_arr)){
                            $streetname_arr = $this->district_base_model->get_streetname_bydist($dist_arr['id']);
                            //print_r($streetname_arr);exit;
                            if(in_array($arr[23],$streetname_arr)){
                                $res[23] = true;
                            }else{
                                $res[23] = false;
                                $data_fail[] = 23;
                            }
                        }else{
                            $street_arr = $this->district_base_model->get_street_id($arr[23]);
                            //print_r( $street_arr);exit;
                            if(!empty($street_arr)){
                                $res[23] = true;
                            }else{
                                $res[23] = false;
                                $data_fail[] = 23;
                            }
                        }
                    }else{
                        $res[23] = false;
                        $data_fail[] = 23;
                    }*/

          if (!empty($arr[24])) {  //地址不能空
            $res[24] = true;
          } else {
            $res[24] = false;
            $data_fail[] = 24;
          }

          if (($res[22] == true) || ($res[24] == true)) {
            $res[0] = true;
          } else {
            $res[0] = false;
            $data_fail[] = 0;
          }
        }
      }
    } else {
      $res[0] = false;
      $data_fail[] = 0;
    }

    if (!empty($arr[1])) { //物业类型不能为空
      $property_type = $data['config']['property_type'];
      if (in_array($arr[1], $property_type)) {
        $res[1] = true;
      } else {
        $res[1] = false;
        $data_fail[] = 1;
      }
    } else {
      $res[1] = false;
      $data_fail[] = 1;
    }
    /*if(!empty($arr[6])){ //业主电话不为空
       $tel = explode("/", $arr[6]);
       if(count($tel) < 4){
            $isMob="/^1[3-5,8]{1}[0-9]{9}$/";
            $isTel="/^([0-9]{3,4})?[0-9]{7,8}$/";
            $isTel1="/^[0-9]{6,8}$/";
            foreach($tel as $vo => $v){
                if(preg_match($isMob,$v) || preg_match($isTel,$v) || preg_match($isTel1,$v)){
                   $res[6] = true;
                }else{
                   $res[6] = false;
                   $data_fail[] = 6;
                }
            }
       }else{
            $res[6] = false;
            $data_fail[] = 6;
       }
    }else{
        $res[6] = false;
        $data_fail[] = 6;
    }*/
    if (!empty($arr[7])) { //状态不能为空
      $nature = array('有效', '预定', '成交', '无效', '注销', '暂不售（租）');
      if (in_array($arr[7], $nature)) {
        $res[7] = true;
      } else {
        $res[7] = false;
        $data_fail[] = 7;
      }
    } else {
      $res[7] = false;
      $data_fail[] = 7;
    }

    /*if(in_array($arr[1],array('厂房','仓库','车库'))){
            //$res[5] = true;
            $res[11] = true;
            $res[13] = true;
        }else{
            if(!empty($arr[13])){ //装修不能为空
                $fitment = $data['config']['fitment'];
                if(in_array($arr[13],$fitment)){
                    $res[13] = true;
                }else{
                    $res[13] = false;
                    $data_fail[] = 13;
                }
            }else{
                $res[13] = false;
                $data_fail[] = 13;
            }
        }
        if(!empty($arr[17]) && is_numeric($arr[17])){ //售价不能为空
            $res[17] = true;
        }else{
            $res[17] = false;
            $data_fail[] = 17;
        }

        if(!empty($arr[20])){ //委托类型不能为空
            $entrust = $data['config']['entrust'];
            if(in_array($arr[20],$entrust)){
                $res[20] = true;
            }else{
                $res[20] = false;
                $data_fail[] = 20;
            }
        }else{
            $res[20] = false;
            $data_fail[] = 20;
        }*/

    if (($res[22] == true) || ($res[24] == true)) {
      if (($res[0] == true) && ($res[1] == true)
        && ($res[7] == TRUE)
        && ($res[22] == true) && ($res[24] == true)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    } else {
      if (($res[0] == true) && ($res[1] == true)
        && ($res[7] == TRUE)
      ) {
        return 'pass';
      } else {
        return $data_fail;
      }
    }

  }

  /**
   * 获取临时表数据
   * @param array $where where字段
   * @param array $like 模糊查询字段
   * @return array 临时表的多维数组
   */
  public function get_tmp($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->tmp_uploads, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
  }

  public function community_info($where = array(), $like = array(), $offset = 0, $pagesize = 0, $database = 'dbback_city')
  {
    $comm = $this->get_data(array('form_name' => $this->community, 'where' => $where, 'like' => $like, 'limit' => $offset, 'offset' => $pagesize), $database);
    return $comm;
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

}
