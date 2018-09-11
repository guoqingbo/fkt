<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2015
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Entrust_base_model CLASS
 *
 * 委托房源业务基础类
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          Fisher
 */
class Entrust_base_model extends MY_Model
{

  public $entrust_house_tbl = 'entrust_house';
  public $entrust_broker_tbl = 'entrust_broker';
  public $entrust_appraise_tbl = 'entrust_broker_appraise';
  public $entrust_pic_tbl = 'entrust_broker_pic';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * 已抢房源总行数
   * @param string $where 查询条件
   * @return int
   */
  public function entrust_count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->entrust_broker_tbl);
  }

  /**
   * 已抢房源总行数
   * @param string $where 查询条件
   * @return int
   */
  public function appraise_count_by($where = '')
  {
    if ($where) {
      //查询条件
      $this->dbback_city->where($where);
    }
    return $this->dbback_city->count_all_results($this->entrust_appraise_tbl);
  }

  /**
   * 获取委托房源列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条委托房源记录组成的二维数组
   */
  public function get_all_entrust_by($where, $start = 0, $limit = 10,
                                     $order_key = 'id', $order_by = 'DESC')
  {
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
    return $this->dbback_city->get($this->entrust_house_tbl)->result_array();
  }

  /**
   * 获取我的已抢房源列表页
   * @param string $where 查询条件
   * @param int $start 查询开始行
   * @param int $limit 数据偏移量
   * @param int $order_key 排序字段
   * @param string $order_by 升序、降序，默认降序排序
   * @return array 返回多条我的已抢房源记录组成的二维数组
   */
  public function get_my_entrust_by($broker_id, $start = 0, $limit = 10,
                                    $order_key = 'id', $order_by = 'DESC')
  {
    //查询条件
    $this->dbback_city->where('brokerid = ' . $broker_id);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //返回结果
    return $this->dbback_city->get($this->entrust_broker_tbl)->result_array();
  }

  //根据房源ID获取委托id,state,num
  public function get_entrust_id_by_houseid($houseid)
  {
    $this->dbback_city->select('id,state,num');
    if ($houseid > 0) {
      $cond_where = "houseid = '" . $houseid . "'";
      $this->dbback_city->where($cond_where);
    }
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_house_tbl)->row_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID,经纪人id获取委托经纪人receive
  public function get_entrust_receive_by_houseid($houseid, $brokerid)
  {
    $this->dbback_city->select('id,receive');
    if ($houseid > 0) {
      $cond_where = "houseid = '" . $houseid . "' and brokerid = '" . $brokerid . "'";
      $this->dbback_city->where($cond_where);
    }

    //查询
    $return_arr = $this->dbback_city->get($this->entrust_broker_tbl)->row_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID获取接受委托的经纪人信息
  public function get_entrust_broker_by_houseid($houseid)
  {
    $return_arr = array();
    $this->dbback_city->select('brokerid,receive,dateline');
    $cond_where = "houseid = '" . $houseid . "'";
    $this->dbback_city->where($cond_where);
    //排序条件
    $this->dbback_city->order_by('id', 'ASC');
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_broker_tbl)->result_array();
    if (is_array($return_arr) && !empty($return_arr)) {
      $this->load->model('broker_info_model');
      $this->load->model('agency_model');
      $this->broker_info_model->set_select_fields(array('phone', 'truename', 'photo', 'company_id'));
      foreach ($return_arr as $key => $value) {
        $brokerinfo_arr = $this->broker_info_model->get_by_broker_id($value['brokerid']);
        $return_arr[$key]['phone'] = $brokerinfo_arr['phone'];
        $return_arr[$key]['truename'] = $brokerinfo_arr['truename'];
        $return_arr[$key]['photo'] = $brokerinfo_arr['photo'];
        //获取公司名
        $company = $this->agency_model->get_by_id($brokerinfo_arr['company_id']);
        if (is_full_array($company)) {
          $return_arr[$key]['company_name'] = $company['name'];
        } else {
          $return_arr[$key]['company_name'] = '';
        }
      }
      /*$broker_arr = array();
      foreach($b_arr as $b)
      {
          $broker_arr[] = intval($b['brokerid']);
      }

      if(is_array($broker_arr) && !empty($broker_arr))
      {
          $this->load->model('broker_info_model');

          $this->broker_info_model->set_select_fields(array('broker_id','phone','truename','photo'));
          $brokerinfo_arr = $this->broker_info_model->get_by_broker_id($broker_arr);
          if(is_array($brokerinfo_arr) && !empty($brokerinfo_arr))
          {
              $biarr = array();
              foreach($brokerinfo_arr as $brokerinfo)
              {
                  $brokerinfo['brokerid'] = $brokerinfo['broker_id'];
                  unset($brokerinfo['broker_id']);
                  $biarr[$brokerinfo['brokerid']] = $brokerinfo;
              }

              foreach($broker_arr as $broker)
              {
                  $return_arr[$broker] = $biarr[$broker];
              }
          }
      }*/
    }
    //返回结果
    return $return_arr;
  }

  //根据房源ID获取接受委托的经纪人评价
  public function get_entrust_appraise_by_houseid($houseid, $start = 0, $limit = 10,
                                                  $order_key = 'id', $order_by = 'DESC')
  {
    $this->dbback_city->select('brokerid,appraise,dateline');
    $cond_where = "houseid = '" . $houseid . "'";
    $this->dbback_city->where($cond_where);
    //排序条件
    $this->dbback_city->order_by($order_key, $order_by);
    if ($start >= 0 && $limit > 0) {
      $this->dbback_city->limit($limit, $start);
    }
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_appraise_tbl)->result_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID 经纪人id获取接受委托的经纪人评价
  public function get_entrust_appraise_by_houseid_brokerid($houseid, $brokerid)
  {
    $this->dbback_city->select('id,appraise');
    $cond_where = "houseid = '" . $houseid . "' and brokerid='" . $brokerid . "'";
    $this->dbback_city->where($cond_where);
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_appraise_tbl)->row_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID获取接受委托的房源图片
  public function get_entrust_pic_by_houseid($houseid)
  {
    $this->dbback_city->select('picurl,dateline');

    $cond_where = "houseid = '" . $houseid . "'";
    $this->dbback_city->where($cond_where);
    //排序条件
    $this->dbback_city->order_by('id', 'ASC');
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_pic_tbl)->result_array();
    //返回结果
    return $return_arr;
  }

  //根据房源ID经纪人id获取经纪人上传的房源图片
  public function get_entrust_mypic($houseid, $brokerid)
  {
    $this->dbback_city->select('picurl,dateline');
    $cond_where = "houseid = '" . $houseid . "' and brokerid = '" . $brokerid . "'";
    $this->dbback_city->where($cond_where);
    //查询
    $return_arr = $this->dbback_city->get($this->entrust_pic_tbl)->row_array();
    //返回结果
    return $return_arr;
  }

  //录入委托房源-返回房源id
  public function set_entrust_house($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->entrust_house_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->entrust_house_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  //录入房源评价-返回评价id
  public function set_entrust_appraise($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->entrust_appraise_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->entrust_appraise_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  //录入房源图片-返回图片id
  public function set_entrust_pic($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->entrust_pic_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->entrust_pic_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  //抢拍房源-返回房源id
  public function set_entrust_broker($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->entrust_broker_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->entrust_broker_tbl, $insert_data)) {
        return $this->db_city->insert_id();
      }
    }
    return false;
  }

  //更新抢拍-认领房源
  public function update_entrust_broker_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->entrust_broker_tbl, $update_data);
    } else {
      $this->db_city->update($this->entrust_broker_tbl, $update_data);
    }
    //返回受影响行数
    return $this->db_city->affected_rows();
  }

  //更新委托房源
  public function update_entrust_house_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->entrust_house_tbl, $update_data);
    } else {
      $this->db_city->update($this->entrust_house_tbl, $update_data);
    }
    //返回受影响行数
    return $this->db_city->affected_rows();
  }

  //更新委托房源评价
  public function update_entrust_appraise($update_data, $houseid, $brokerid)
  {
    $this->db_city->where("houseid = '" . $houseid . "' and brokerid='" . $brokerid . "'");
    $this->db_city->update($this->entrust_appraise_tbl, $update_data);
    //返回受影响行数
    return $this->db_city->affected_rows();
  }

  //更新委托房源照片
  public function update_entrust_pic($update_data, $houseid, $brokerid)
  {
    $this->db_city->where("houseid = '" . $houseid . "' and brokerid='" . $brokerid . "'");
    $this->db_city->update($this->entrust_pic_tbl, $update_data);
    //返回受影响行数
    return $this->db_city->affected_rows();
  }
}

/* End of file entrust_base_model.php */
/* Location: ./application/models/entrust_base_model.php */
