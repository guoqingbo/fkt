<?php

/**
 * Description of relation_street
 *
 * @author ccy
 */
class Relation_street_model extends MY_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //根据板块id获取第三方数据 58 赶集 :wjy
  public function select_relation_street($street_id, $dist_id)
  {
    if (empty($dist_id)) {
      return false;
    } elseif (empty($street_id)) {
      $where = array('dist_id' => $dist_id);
    } else {
      $where = array('street_id' => $street_id);
    }
    return $this->get_data(array('form_name' => 'relation_street', 'where' => $where), 'dbback_city');
  }

  //安居客 搜房 查询板块信息是否存在 :wjy
  public function relation_block($block_id, $company_id)
  {
    $where = array('block_id' => $block_id, 'company_id' => $company_id);
    $data = $this->dbback_city->from('relation_block')->where($where)->get()->row_array();
    return $data;
  }

  //插入 or 更新 安居客 信息 :wjy
  public function upload_anjuke_block($data)
  {
    $find = $this->relation_block($data['block_id'], $data['company_id']);
    if (empty($find)) { //插入
      $data['createtime'] = time();
      $result = $this->db_city->insert('relation_block', $data);
    } elseif (empty($find['ajk_block_id'])) { //更新
      $where = array('block_id' => $data['block_id'], 'company_id' => $data['company_id']);
      $result = $this->modify_data($where, $data, 'db_city', 'relation_block');
    } else {
      $result = false;
    }
    return $result;
  }

  //插入 or 更新 搜房 信息 :wjy
  public function upload_fang_block($data)
  {
    $find = $this->relation_block($data['block_id'], $data['company_id']);
    if (empty($find)) { //插入
      $data['createtime'] = time();
      $result = $this->db_city->insert('relation_block', $data);
    } elseif (empty($find['fang_block'])) { //更新
      $where = array('block_id' => $data['block_id'], 'company_id' => $data['company_id']);
      $result = $this->modify_data($where, $data, 'db_city', 'relation_block');
    } else {
      $result = false;
    }
    return $result;
  }
}
