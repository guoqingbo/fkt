<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 * 房源采集（赶集）
 * 2016.7.1
 * cc
 */
class Collect_solve_model extends MY_Model
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('collect_details_model');//采集详情模型类
    $this->load->model('collect_model');
    $this->collect_url = 'collect_url'; //房源列表
    $this->collect_url_list = 'collect_url_list'; //房源链接列表
    $this->city = 'city'; //城市表
  }

  //获取采集连接列表
  public function collect_url_list($city = '')
  {
    if ($city != '') {
      $where = array('city' => $city, 'source_from' => 'ganji', 'is_run' => 0);
    } else {
      $where = array('source_from' => 'ganji', 'is_run' => 0);
    }
    $limit = 1;
    $result = $this->get_data(array('form_name' => $this->collect_url_list, 'where' => $where, 'limit' => $limit), 'dbback');
    if (empty($result)) {
      $res_update = $this->update_collect_url_list('change', $city);//重置采集链接
      return array();
    } else {
      foreach ($result as $value) {
        //修改采集链接列表
        $res_update = $this->update_collect_url_list($value['id']);
        if ($res_update) {//更新成功
          return $value;
        } else {
          return array();
        }
      }
    }
  }

  //获取采集列表
  public function collect_url()
  {
    $where = array('source_from' => 'ganji');
    $limit = 1;
    $result = $this->get_data(array('form_name' => $this->collect_url, 'where' => $where, 'order_by' => 'id', 'limit' => $limit), 'dbback');
    if (!empty($result)) {
      foreach ($result as $value) {
        //删除采集列表
        $del = array('id' => $value['id']);
        $this->del_collect_url($del);
        return $value;
      }
    }
  }

  //删除房源列表
  public function del_collect_url($data = array())
  {
    $result = $this->del($data, 'db', $this->collect_url);
    return $result;
  }

  //修改采集链接列表
  public function update_collect_url_list($type, $city = '')
  {
    if ($type == 'change') {
      if ($city != '') {
        $where = array('is_run' => 2, 'city' => $city, 'source_from' => 'ganji');
      } else {
        $where = array('is_run' => 2, 'source_from' => 'ganji');
      }
      $data = array('is_run' => 0);
      $this->modify_data($where, $data, 'db', $this->collect_url_list);
    } else {
      $where = array('id' => $type);
      $data = array('is_run' => 2);
      $this->modify_data($where, $data, 'db', $this->collect_url_list);
    }
    //判断更新成功
    if ($this->db->affected_rows() >= 1) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * 采集详情
   * 2016.7.1
   * cc
   */
  public function collect_info($data, $con)
  {
    //详情采集
    switch ($data['kind_type']) {
      case 1://住宅
        $con = $this->collect_details_model->collect_info_zhuzhai($data, $con);
        break;
      case 3://商铺
        $con = $this->collect_details_model->collect_info_shangpu($data, $con);
        break;
      case 4://写字楼
        $con = $this->collect_details_model->collect_info_xiezilou($data, $con);
        break;
      default:
        $con = $this->collect_details_model->collect_info_zhuzhai($data, $con);
    }
  }

  /**
   * 采集列表
   * 2016.9.1
   * cc
   */
  public function collect_list($data, $con)
  {
    $i = 0;
    $price = '';
    if ($data['kind_type'] == 1) {
      if ($data['type'] == 1) {
        preg_match_all('/<a class="list-info-title js-title" href="\/' . $data['urllist'] . '\/(.*)" target="_blank".*refresh_at=(.*)@post_id.*<em class="sale-price js-price">(.*)<\/em>万/siU', $con, $prj);
      } else if ($data['type'] == 2) {
        preg_match_all('/<a class="list-info-title js-title" href="\/' . $data['urllist'] . '\/(.*)" target="_blank".*refresh_at=(.*)@post_id.*<div class="list-mod3 clearfix">(.*)<\/em>/siU', $con, $prj);
      }
    } else {
      if ($data['host'] == 'bj.ganji.com' && $data['city'] == 32) {
        preg_match_all('/<a class=\'list-info-title\' target=\'_blank\' href=\'\/' . $data['urllist'] . '\/(.*htm)\' id.*refresh_at=(.*)@post_id/siU', $con, $prj);
      } else {
        preg_match_all('/<a class=\'list-info-title\' target=\'_blank\' href=\'\/' . $data['urllist'] . '\/(.*)\' id.*refresh_at=(.*)@post_id/siU', $con, $prj);
      }
    }
    if (!empty($prj[1])) {
      foreach ($prj[1] as $key => $val) {
        $url_details = "http://" . $data['host'] . "/" . $data['urllist'] . "/" . $val;
        //筛选重复房源列表
        if ($data['city'] == 45) {//因为北京有廊坊数据，故用此判断区分对待北京和廊坊，便于各城市都显示（45是北京城市id）
          $where = array('url_hash' => md5($url_details . $data['city']), 'city' => $data['city']);
          $url_hash = md5($url_details . $data['city']);
        } else {
          $where = array('url_hash' => md5($url_details), 'city' => $data['city']);
          $url_hash = md5($url_details);
        }
        $result = $this->collect_model->check_collect_url_hash($where);
        if (empty($result)) {
          //添加房源列表
          $lists = array(
            'city' => $data['city'],
            'type' => $data['type'],
            'kind_type' => $data['kind_type'],
            'list_url' => $url_details,
            'source_from' => $data['source_from'],
            'createtime' => time()
          );
          $res = $this->collect_model->add_collect_url($lists);
          if ($res !== 0) {
            $i++;
          }
        } else {
          $time = $prj[2][$key];
          $price = $prj[3][$key] == '' ? '' : $this->con_replace(strip_tags($prj[3][$key]));
          $res = $this->collect_model->add_collect_refresh_list($data['city'], $data['type'], $url_details, $time, $price);
        }
      }
      echo "成功采集到 " . $i . "条<br>";
      exit;
    }
  }

  //根据城市缩写获取城市ID
  public function collect_city_byspell($spell)
  {
    $where = array('spell' => $spell);
    $result = $this->get_data(array('form_name' => $this->city, 'where' => $where), 'dbback');
    return $result[0];
  }
}
