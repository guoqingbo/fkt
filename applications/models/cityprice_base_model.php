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
 * since           Version 1.0
 * @author         lalala
 */
// ------------------------------------------------------------------------

class cityprice_base_model extends MY_Model
{
  /**
   * 二手房均价表
   * @var string
   */
  private $_tbl = 'cityprice';

  /**
   * 类初始化
   */
  public function __construct()
  {
    parent::__construct();
  }

  public function get_month()
  {
    $this->dbback_city->order_by('id', DESC);
    $this->dbback_city->select('month');
    $array = $this->dbback_city->get($this->_tbl, 12, 0)->result_array();
    $month = array();
    foreach ($array as $key => $val) {
      $month[] = "'" . $val['month'] . "'";
    }
    krsort($month);
    $str = '[' . implode(",", $month) . ']';
    return $str;
  }

  public function get_price()
  {
    $this->dbback_city->order_by('id', DESC);
    $this->dbback_city->select('price');
    $array = $this->dbback_city->get($this->_tbl, 12, 0)->result_array();
    $price = array();
    foreach ($array as $key => $val) {
      $price[] = $val['price'];
    }
    krsort($price);
    $str = '[' . implode(",", $price) . ']';
    return $str;
  }

  public function get_price_xf()
  {
    $this->dbback_city->order_by('id', DESC);
    $this->dbback_city->select('price_xf');
    $array = $this->dbback_city->get($this->_tbl, 12, 1)->result_array();
    $price = array();
    foreach ($array as $key => $val) {
      $price[] = $val['price_xf'];
    }
    krsort($price);
    $str = '[' . implode(",", $price) . ']';
    return $str;
  }

  public function get_min_price()
  {
    $this->dbback_city->order_by('id', DESC);
    $this->dbback_city->select('price_xf');
    $xf_price = $this->dbback_city->get($this->_tbl, 12, 1)->result_array();
    sort($xf_price);

    $this->dbback_city->order_by('id', DESC);
    $this->dbback_city->select('price');
    $price = $this->dbback_city->get($this->_tbl, 12, 0)->result_array();
    sort($price);
    $min_price = $xf_price[0]['price_xf'] <= $price[0]['price'] ? $xf_price[0]['price_xf'] : $price[0]['price'];
    if ($min_price) {
      return $min_price;
    } else {
      return '[]';
    }

  }


  public function transform_pricestr($house_price_arr)
  {
    foreach ($house_price_arr as $key => $val) {
      $insert_array = array();
      $arr = explode(",", $val);
      $month = date("Ym", substr($arr[0], 0, strlen($arr[0]) - 3));
      $price = $arr[1];
      $insert_arr = array(
        'month' => $month,
        'price' => $price
      );
      $this->insert_cityprice($insert_arr);
    }
  }

  public function transform_pricestr_xf($house_price_arr)
  {
    foreach ($house_price_arr as $key => $val) {
      $insert_arr = array();
      $arr = explode(",", $val);
      $month = date("Ym", substr($arr[0], 0, strlen($arr[0]) - 3));
      $price = $arr[1];
      $insert_arr = array(
        'month' => $month,
        'price_xf' => $price
      );
      $this->insert_cityprice($insert_arr);
    }
  }

  public function insert_cityprice($array)
  {
    $month = $array['month'];
    $this->db_city->where('month', $month);
    $result = $this->db_city->get($this->_tbl)->row_array();
    if (empty($result)) {
      $this->insert($array);
    } else {
      $this->update_by_id($array, $result['id']);
    }
  }

  private function insert($insert_data)
  {
    if (isset($insert_data[0]) && is_array($insert_data[0])) {
      //批量插入
      if ($this->db_city->insert_batch($this->_tbl, $insert_data)) {
        return $this->db_city->affected_rows();
      }
    } else {
      //单条插入
      if ($this->db_city->insert($this->_tbl, $insert_data)) {
        return $this->db_city->affected_rows();
      }
    }
    return false;
  }


  public function update_by_id($update_data, $id)
  {
    if (is_array($id)) {
      $ids = $id;
    } else {
      $ids[0] = $id;
    }
    $this->db_city->where_in('id', $ids);
    if (isset($update_data[0]) && is_array($update_data[0])) {
      $this->db_city->update_batch($this->_tbl, $update_data);
    } else {
      $this->db_city->update($this->_tbl, $update_data);
    }
    return $this->db_city->affected_rows();
  }
}

