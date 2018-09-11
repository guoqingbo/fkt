<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of relation_street_district
 *
 * @author ccy
 */
class Relation_district_street extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('relation_district_street_model');
    $this->load->model('relation_tp_street_model');
    $this->load->model('read_model');
    $this->load->helper('page_helper');
    $this->load->helper('cookie');
  }

  /**
   * 赶集区属列表
   */
  public function ganji_district_index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'ganji_district_index';
    $type = 2;
    //分页开始
    $data['district_num'] = $this->relation_district_street_model->get_district_num($type);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['district_list'] = $this->relation_district_street_model->get_district($type, $data['offset'], $data['pagesize']);
    $this->load->view('relation_gj_district_street/district_index', $data);
  }

  /**
   * 赶集区属采集
   */
  function ganji_district()
  {
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('ganji_city');
    $city = $city_arr[$city];
    $val = 'http://www.ganji.com/pub/pub.php?act=pub&method=load&cid=7&mcid=21&domain=' . $city . '&domain=' . $city;
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    preg_match('/<div class="select-option" id="district_options" data-domain="bj">(.*)<\/div>/siU', $con, $cons);
    $district = $this->relation_district_street_model->district_handle($cons[1]);
  }

  /**
   * 赶集区属添加
   */
  function ganji_district_add()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'ganji_district_index';
    $addResult = '';
    $paramArray = array();
    $submit_flag = $this->input->post('submit_flag');
    $district = trim($this->input->post('district'));
    if ('add' == $submit_flag && $district != 0) {
      $district = explode('&', $district);
      $exist = $this->relation_district_street_model->select_relation_district($district[1], 2);
      if ($exist) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'district_id' => $district[0],
          'district_name' => $district[1],
          'tp_type' => 2
        );
        $addResult = $this->relation_district_street_model->add_relation_district($paramArray);
      }
    }
    $this->ganji_district();//刷新临时表数据
    $data['district'] = $this->relation_district_street_model->select_temporany();
    $data['addResult'] = $addResult;
    $this->load->view('relation_gj_district_street/district_add', $data);
  }

  /**
   * 赶集区属修改
   */
  public function ganji_district_modify($id)
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'ganji_district_index';
    $modifyResult = '';
    $modify_district = $this->relation_district_street_model->select_relation_district_id($id);//修改的数据
    $data['modify_district'] = $modify_district[0];
    //print_r($data['modify_district']);
    $submit_flag = $this->input->post('submit_flag');
    $district = trim($this->input->post('district'));
    if ('modify' == $submit_flag && $district != 0) {
      $district = explode('&', $district);
      $exist = $this->relation_district_street_model->select_relation_district($district[1], 2);
      //print_r($exist);exit;
      if (!empty($exist[0]['district_id']) && $exist[0]['district_id'] != $data['modify_district']['district_id']) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'district_id' => $district[0],
          'district_name' => $district[1]
        );
        $modifyResult = $this->relation_district_street_model->update_relation_district($id, $paramArray);
      }
      //同步更新第三方关联板块
      if ($modifyResult == 1) {
        $date = array();
        $date = array(
          'dist_id' => $district[0]
        );
        $street = $this->relation_district_street_model->select_relation_tp_street($data['modify_district']['district_id'], 2);
        foreach ($street as $value) {
          //print_r($value);echo $value['dist_id'].'<hr>';
          $result = $this->relation_district_street_model->update_relation_tp_street($value['id'], $date);
        }
        $relation_date = array();
        $relation_date = array(
          'ganji_dist_id' => $district[0]
        );
        $relation_street = $this->relation_district_street_model->select_relation_street($data['modify_district']['district_id']);
        foreach ($relation_street as $value) {
          $relation_result = $this->relation_district_street_model->update_relation_street($value['id'], $relation_date);
        }
      }
    }
    $this->ganji_district();//刷新临时表数据
    $data['district'] = $this->relation_district_street_model->select_temporany();
    $data['modifyResult'] = $modifyResult;
    $this->load->view('relation_gj_district_street/district_modify', $data);
  }

  /**
   * 赶集板块列表页面
   */
  public function ganji_street_index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'street_index';
    $type = 2;
    //获得所有区属
    $data['all_district'] = $this->relation_district_street_model->get_district(2);
    //筛选条件
    $data['where_cond'] = '';
    $dist_id = intval($this->input->post('dist_id'));
    if (!empty($dist_id)) {
      $data['where_cond']['dist_id'] = $dist_id;
    }
    //分页开始
    $data['district_num'] = $this->relation_district_street_model->get_street_num($type, $data['where_cond']['dist_id']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['street_list'] = $this->relation_district_street_model->get_street($type, $data['where_cond']['dist_id'], $data['offset'], $data['pagesize']);

    //数据重构
    $data['street_list2'] = array();
    foreach ($data['street_list'] as $k => $v) {
      $dist_data = $this->relation_district_street_model->select_relation_tp_district_id($v['dist_id'], 2);
      $v['dist_name'] = $dist_data[0]['district_name'];
      $data['street_list2'][] = $v;
    }
    $this->load->view('relation_gj_district_street/street_index', $data);
  }

  /**
   * 赶集板块添加
   */
  function ganji_street_add()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'ganji_street_index';
    $type = 2;
    //获得所有区属
    $data['all_district'] = $this->relation_district_street_model->get_all_district($type);
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $district_id = trim($this->input->post('district'));
    $street = trim($this->input->post('street'));
    if ('add' == $submit_flag && $street != 0) {
      $street = explode('&', $street);
      $exist = $this->relation_district_street_model->select_relation_tp_street($district_id, $type, $street[1], $street[0]);
      if ($exist) {//数据已存在
        $data['mess_error'] = '该板块已存在,请先修改该板块';
      } else {
        $paramArray = array(
          'street_name' => $street[1],
          'dist_id' => $district_id,
          'street_id' => $street[0],
          'tp_type' => $type
        );
        $addResult = $this->relation_district_street_model->add_relation_street($paramArray);
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('relation_gj_district_street/street_add', $data);
  }

  /**
   * 赶集板块修改
   */
  public function ganji_street_modify($id = 0)
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'ganji_street_index';
    $type = 2;
    //获得所有区属
    $street_mess = $this->relation_district_street_model->select_relation_street_id($id);
    $dist_mess = $this->relation_district_street_model->select_relation_tp_district_id($street_mess[0]['dist_id']);
    $data['dist_mess'] = $dist_mess[0];
    $data['street_mess'] = $street_mess[0];
    $data['gj_street'] = $this->ganji_street_district_id($street_mess[0]['dist_id']);
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $street = trim($this->input->post('street'));
    if ('modify' == $submit_flag && $street != 0) {
      $street = explode('&', $street);
      $exist = $this->relation_district_street_model->select_relation_tp_street($data['dist_mess']['district_id'], $type, $street[1]);
      //print_r($exist);
      if (!empty($exist[0]['street_id']) && $exist[0]['street_id'] != $data['street_mess']['street_id']) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'street_name' => $street[1],
          'street_id' => $street[0],
        );
        $modifyResult = $this->relation_district_street_model->update_relation_tp_street($id, $paramArray);
      }
      if ($modifyResult == 1) {
        $relation_date = array();
        $relation_date = array(
          'ganji_street_id' => $street[0],
          'street_name' => $street[1],
        );
        $relation_street = $this->relation_district_street_model->select_relation_street($data['dist_mess']['district_id'], $data['street_mess']['street_id']);
        //print_r($relation_street);
        $relation_result = $this->relation_district_street_model->update_relation_street($relation_street[0]['id'], $relation_date);
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('relation_gj_district_street/street_modify', $data);
  }

  /**
   * 二级联动
   * post方法
   */
  function test_post()
  {
    $this->load->library('Curl');
    $post_url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=streetOptions';
    $id = 1557;
    $post_fielde = 'domain=bj&district_id=' . $id . '&with_all_option=1';
    $tmpInfo3 = $this->curl->vpost($post_url, $post_fielde);
    preg_match('/fang(.*)]]/siU', $tmpInfo3, $pigname);
    $mess = $pigname[1] . ']]';
    //$str=preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))",$mess);//unicode编码转成中文
    $str = json_decode($mess);
    print_r($str);
  }

  /**
   * 赶集板块采集
   * post方法ajax
   */
  function ganji_street()
  {
    $this->load->library('Curl');
    $district_id = intval($this->input->post('district_id'));
    $post_url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=streetOptions';
    $post_fielde = 'domain=bj&district_id=' . $district_id . '&with_all_option=1';
    $tmpInfo3 = $this->curl->vpost($post_url, $post_fielde);
    preg_match('/fang(.*)]]/siU', $tmpInfo3, $pigname);
    $mess = $pigname[1] . ']]';
    echo $mess;
  }

  /**
   * 根据板块id采集赶集板块
   * post方法传值
   */
  function ganji_street_district_id($district_id)
  {
    $this->load->library('Curl');
    $post_url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=streetOptions';
    $post_fielde = 'domain=bj&district_id=' . $district_id . '&with_all_option=1';
    $tmpInfo3 = $this->curl->vpost($post_url, $post_fielde);
    preg_match('/fang(.*)]]/siU', $tmpInfo3, $pigname);
    $mess = $pigname[1] . ']]';
    $str = json_decode($mess);
    return $str;
  }

  /**
   * 58区属采集
   */
  function wuba_district()
  {
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('58_city');
    $num = $city_arr[$city];
    $val = 'post.58.com/' . $num . '/12/s5';
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    preg_match('/var datasrc=(.*)\/\/2.是否新发布/siU', $con, $cons);
    preg_match('/"localArea"(.*),"type"/siU', $cons[1], $mess);
    $messesage = ltrim($mess[1], ":");
    $str = json_decode($messesage);
    $i = 0;
    foreach ($str->values as $value) {//php5规则
      //print_r($value);exit;
      $data[$i] = array(
        'district_id' => $value->val,
        'name' => $value->text,
      );
      $i++;
    }
    $this->relation_district_street_model->district_temporany($data);
  }

  /**
   * 58区属列表
   */
  public function wuba_district_index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'wuba_district_index';
    $type = 1;
    //分页开始
    $data['district_num'] = $this->relation_district_street_model->get_district_num($type);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['district_list'] = $this->relation_district_street_model->get_district($type, $data['offset'], $data['pagesize']);
    $this->load->view('relation_wb_district_street/district_index', $data);
  }

  /**
   * 58区属添加
   */
  function wuba_district_add()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'wuba_district_index';
    $addResult = '';
    $paramArray = array();
    $submit_flag = $this->input->post('submit_flag');
    $district = trim($this->input->post('district'));
    if ('add' == $submit_flag && $district != 0) {
      $district = explode('&', $district);
      $exist = $this->relation_district_street_model->select_relation_district($district[1], 1);
      if ($exist) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'district_id' => $district[0],
          'district_name' => $district[1],
          'tp_type' => 1
        );
        $addResult = $this->relation_district_street_model->add_relation_district($paramArray);
      }
    }
    $this->wuba_district();//刷新临时表数据
    $data['district'] = $this->relation_district_street_model->select_temporany();
    $data['addResult'] = $addResult;
    $this->load->view('relation_wb_district_street/district_add', $data);
  }

  /**
   * 58区属修改
   */
  public function wuba_district_modify($id)
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'wuba_district_index';
    $modifyResult = '';
    $modify_district = $this->relation_district_street_model->select_relation_district_id($id);//修改的数据
    $data['modify_district'] = $modify_district[0];
    //print_r($data['modify_district']);
    $submit_flag = $this->input->post('submit_flag');
    $district = trim($this->input->post('district'));
    if ('modify' == $submit_flag && $district != 0) {
      $district = explode('&', $district);
      $exist = $this->relation_district_street_model->select_relation_district($district[1], 1);
      //print_r($exist);exit;
      if (!empty($exist[0]['district_id']) && $exist[0]['district_id'] != $data['modify_district']['district_id']) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'district_id' => $district[0],
          'district_name' => $district[1]
        );
        $modifyResult = $this->relation_district_street_model->update_relation_district($id, $paramArray);
      }
      //同步更新第三方关联板块
      if ($modifyResult == 1) {
        $date = array();
        $date = array(
          'dist_id' => $district[0]
        );
        $street = $this->relation_district_street_model->select_relation_tp_street($data['modify_district']['district_id'], 1);
        foreach ($street as $value) {
          //print_r($value);echo $value['dist_id'].'<hr>';
          $result = $this->relation_district_street_model->update_relation_tp_street($value['id'], $date);
        }
        $relation_date = array();
        $relation_date = array(
          'wuba_dist_id' => $district[0]
        );
        $relation_street = $this->relation_district_street_model->select_relation_wb_street($data['modify_district']['district_id']);
        foreach ($relation_street as $value) {
          $relation_result = $this->relation_district_street_model->update_relation_street($value['id'], $relation_date);
        }
      }
    }
    $this->wuba_district();//刷新临时表数据
    $data['district'] = $this->relation_district_street_model->select_temporany();
    $data['modifyResult'] = $modifyResult;
    $this->load->view('relation_wb_district_street/district_modify', $data);
  }

  /**
   * 58板块列表页面
   */
  public function wuba_street_index()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'street_index';
    $type = 1;
    //获得所有区属
    $data['all_district'] = $this->relation_district_street_model->get_district($type);
    //筛选条件
    $data['where_cond'] = '';
    $dist_id = intval($this->input->post('dist_id'));
    if (!empty($dist_id)) {
      $data['where_cond']['dist_id'] = $dist_id;
    }
    //分页开始
    $data['district_num'] = $this->relation_district_street_model->get_street_num($type, $data['where_cond']['dist_id']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['district_num'] ? ceil($data['district_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['street_list'] = $this->relation_district_street_model->get_street($type, $data['where_cond']['dist_id'], $data['offset'], $data['pagesize']);

    //数据重构
    $data['street_list2'] = array();
    foreach ($data['street_list'] as $k => $v) {
      $dist_data = $this->relation_district_street_model->select_relation_tp_district_id($v['dist_id'], $type);
      $v['dist_name'] = $dist_data[0]['district_name'];
      $data['street_list2'][] = $v;
    }
    $this->load->view('relation_wb_district_street/street_index', $data);
  }

  /**
   * 58板块添加
   */
  function wuba_street_add()
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'wuba_street_index';
    $type = 1;
    //获得所有区属
    $data['all_district'] = $this->relation_district_street_model->get_all_district($type);
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $district_id = trim($this->input->post('district'));
    $street = trim($this->input->post('street'));
    if ('add' == $submit_flag && $street != 0) {
      $street = explode('&', $street);
      $exist = $this->relation_district_street_model->select_relation_tp_street($district_id, $type, $street[1], $street[0]);
      if ($exist) {//数据已存在
        $data['mess_error'] = '该板块已存在,请先修改该板块';
      } else {
        $paramArray = array(
          'street_name' => $street[1],
          'dist_id' => $district_id,
          'street_id' => $street[0],
          'tp_type' => $type
        );
        $addResult = $this->relation_district_street_model->add_relation_street($paramArray);
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('relation_wb_district_street/street_add', $data);
  }

  /**
   * 58板块修改
   */
  public function wuba_street_modify($id)
  {
    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'wuba_street_index';
    $type = 1;
    //获得所有区属
    $street_mess = $this->relation_district_street_model->select_relation_street_id($id);
    $dist_mess = $this->relation_district_street_model->select_relation_tp_district_id($street_mess[0]['dist_id']);
    $data['dist_mess'] = $dist_mess[0];
    $data['street_mess'] = $street_mess[0];
    $data['wb_street'] = $this->wuba_street_district_id($street_mess[0]['dist_id']);
    // print_r($data['wb_street']);
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    $street = trim($this->input->post('street'));
    if ('modify' == $submit_flag && $street != 0) {
      $street = explode('&', $street);
      $exist = $this->relation_district_street_model->select_relation_tp_street($data['dist_mess']['district_id'], $type, $street[1]);
      //print_r($exist);
      if (!empty($exist[0]['street_id']) && $exist[0]['street_id'] != $data['street_mess']['street_id']) {//数据已存在
        $data['mess_error'] = '该区属已存在';
      } else {
        $paramArray = array(
          'street_name' => $street[1],
          'street_id' => $street[0],
        );
        $modifyResult = $this->relation_district_street_model->update_relation_tp_street($id, $paramArray);
      }
      if ($modifyResult == 1) {
        $relation_date = array();
        $relation_date = array(
          'wuba_street_id' => $street[0],
          'street_name' => $street[1],
        );
        $relation_street = $this->relation_district_street_model->select_relation_wb_street($data['dist_mess']['district_id'], $data['street_mess']['street_id']);
        //print_r($relation_date);
        $relation_result = $this->relation_district_street_model->update_relation_street($relation_street[0]['id'], $relation_date);
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('relation_wb_district_street/street_modify', $data);
  }

  /**
   * 根据板块id采集58板块
   * post方法传值
   */
  function wuba_street_district_id($district_id)
  {
    $this->load->library('Curl');
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('58_city');
    $num = $city_arr[$city];
    $val = 'post.58.com/' . $num . '/12/s5';
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    preg_match('/var datasrc=(.*)\/\/2.是否新发布/siU', $con, $cons);
    preg_match('/"localArea"(.*),"type"/siU', $cons[1], $mess);
    $messesage = ltrim($mess[1], ":");
    $str = json_decode($messesage);
    foreach ($str->values as $value) {//php5规则
      if ($value->val == $district_id) {
        $street_str = $value->children;
        $street = $street_str[0]->values;
        break;
      }
    }
    // print_r($street);
    return $street;
  }

  /**
   * 58板块采集
   * ajax
   */
  function wuba_street()
  {
    $district_id = intval($this->input->post('district_id'));
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('58_city');
    $num = $city_arr[$city];
    $val = 'post.58.com/' . $num . '/12/s5';
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    preg_match('/var datasrc=(.*)\/\/2.是否新发布/siU', $con, $cons);
    preg_match('/"localArea"(.*)mianshui/siU', $cons[1], $mess);
    $messes = substr($mess[1], 0, strlen($mess[1]) - 2);
    $messesage = ltrim($messes, ":");
    $str = json_decode($messesage);

    foreach ($str->values as $value) {//php5规则
      if ($value->val == $district_id) {
        $street_str = $value->children;
        $street = $street_str[0]->values;
        break;
      }
    }
    echo json_encode($street);
  }

  /**
   * 二级联动
   * get方法
   */
  function test_get()
  {
    $this->load->library('Curl');
    //$post_fielde = 'http://www.xsfc.com/Account/PHouseList.aspx';//萧山房产网
    $post_fielde = 'http://house.xs163.net/account/house/manage_salefdhouse.asp';//萧山房产中介网
    //$post_fielde = $post_url.'dispLocalId='.$id.'&dispCateId=12&source=1501&userType=0';
    //萧山房产中介网
    $cookie = 'ASPSESSIONIDCABSRDAS=KNGFILNDECKGDOHKOIPOBPPM; house%5Fusername=Lzfc; house%5Fpassword=82825115; CNZZDATA30028232=cnzz_eid%3D761995738-1453280854-%26ntime%3D1453336176';
    //萧山房产网
    //$cookie = 'ASP.NET_SessionId=betoziezlvl3peqqxwvrtmac; loginInfo=UserName=bnzl&UserType=%e4%b8%ad%e4%bb%8b&UserId=d5b821a3-e6c5-4e91-8449-9f08dd254a3d&UserRole=2&AgencyId=303; ucenter2=nDS7QqdQDTA=; CNZZDATA1253049527=354898087-1453277165-%7C1453336014; noticed=true; JumpAfterWritefsj=285038187; fangshijie_city=1; fangshijie_cityname=è§å±±';
    $tmpInfo3 = $this->curl->vget($post_fielde, $cookie);
    $tmpInfo3 = mb_convert_encoding($tmpInfo3, "UTF-8", "GBK");
    print_r($tmpInfo3);
  }
  //58获取板块
  /**
   * 58的区属板块采集
   */
  function get_58_district_street()
  {
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    $city_id = $_SESSION[WEB_AUTH]["city_id"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('58_city');
    $num = $city_arr[$city];
    $val = 'http://post.58.com/' . $num . '/12/s5';
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    preg_match('/var datasrc=(.*)\/\/2.是否新发布/siU', $con, $cons);
    preg_match('/"localArea"(.*),"type"/siU', $cons[1], $mess);
    $messesage = ltrim($mess[1], ":");
    $str = json_decode($messesage);
    //print_r($str);die();
    foreach ($str->values as $value) {//php5规则
      //$position = $this->get_position($_SESSION[WEB_AUTH]['cityname'].$value->text);
      //同步到区属表
      $insert_data1 = array(
        'district' => $value->text,
        'city_id' => $city_id,
        'is_show' => 1,
        //'b_map_x'=>$position['location']['lng'],
        //'b_map_y'=>$position['location']['lat'],
      );
      $result1 = $this->relation_tp_street_model->add_district($insert_data1);
      //添加到新房区属
      $insert_data2 = array(
        'district' => $value->text,
      );
      $result2 = $this->relation_tp_street_model->add_district_xf($insert_data2);
      //添加58区属
      $insert_data = array(
        'district_id' => $value->val,
        'district_name' => $value->text,
        'tp_type' => 1
      );
      $result = $this->relation_tp_street_model->add_tp_district($insert_data);
      if ($result) {
        $street = $value->children;
        if (is_full_array($street)) {
          foreach ($street[0]->values as $k => $v) {
            $name_spell = $this->read_model->encode($v->text, 'all');
            //$position = $this->get_position($_SESSION[WEB_AUTH]['cityname'].$value->text.$v->text);
            //同步到区属表
            $street_data1 = array(
              'streetname' => $v->text,
              'name_spell' => $name_spell,
              'dist_id' => $result1,
              'is_show' => 1,
              //'b_map_x'=>$position['location']['lng'],
              //'b_map_y'=>$position['location']['lat'],
            );
            $this->relation_tp_street_model->add_street($street_data1);
            //同步到新房板块表
            $street_data2 = array(
              'streetname' => $v->text,
            );
            $this->relation_tp_street_model->add_street_xf($street_data2);
            $street_data = array(
              'street_id' => $v->val,
              'street_name' => $v->text,
              'dist_id' => $value->val,
              'tp_type' => 1
            );
            $this->relation_tp_street_model->add_tp_street($street_data);
          }
        }
      }
    }
    $this->update_relation_street($type = 1);
  }
  //赶集区属板块
  /**
   * 赶集区属板块
   */
  function get_ganji_district_street()
  {
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    $city_id = $_SESSION[WEB_AUTH]["city_id"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('ganji_city');
    $city = $city_arr[$city];
    $val = 'http://www.ganji.com/pub/pub.php?act=pub&method=load&cid=7&mcid=21&domain=' . $city . '&domain=' . $city;
    $con = $this->relation_district_street_model->vcurl($val, $compress);  #采集页面
    //echo $con;die();
    preg_match('/<div class="select-option" id="district_options" data-domain="bj">(.*)<\/div>/siU', $con, $cons);
    //echo $cons[1];die();
    $mess = explode('</a>', $cons[1]);//print_R($mess[8]);die();
    $length = count($mess) - 1;//区属数量
    foreach ($mess as $key => $val) {
      if (trim($val)) {
        $reg = '/\d+/';//匹配数字的正则表达式
        preg_match_all($reg, $val, $result);//取出区属id
        $district['district_id'] = $result[0][1];
        $district['district_name'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($val));//取出区属文字
        $district['tp_type'] = 2;
        $result = $this->relation_tp_street_model->add_tp_district($district);
        if ($result) {
          $this->load->library('Curl');
          $district_id = $district['district_id'];
          $post_url = 'http://www.ganji.com/ajax.php?_pdt=fang&module=streetOptions';
          $post_fielde = 'domain=bj&district_id=' . $district_id . '&with_all_option=1';
          $tmpInfo3 = $this->curl->vpost($post_url, $post_fielde);
          preg_match('/fang(.*)]]/siU', $tmpInfo3, $pigname);
          $mess = $pigname[1] . ']]';
          $mess = substr($mess, 28, strlen($mess));
          $messes = json_decode($mess);
          foreach ($messes as $v) {
            //添加58区属
            $street_data = array(
              'street_id' => $v[0],
              'street_name' => $v[1],
              'dist_id' => $district['district_id'],
              'tp_type' => 2
            );
            $this->relation_tp_street_model->add_tp_street($street_data);
          }
        }
      }
    }
    $this->update_relation_street($type = 2);
  }

  //赶集区属板块
  /**
   * 赶集区属板块
   */
  function get_soufang_district_street()
  {
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    $city_id = $_SESSION[WEB_AUTH]["city_id"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('soufang_xiaoqu');
    $city = $city_arr[$city];
    if ($city) {
      $val = 'http://esf.' . $city . '.fang.com/housing/';
    } else {
      $val = 'http://esf.fang.com/housing/';
    }

    $con = $this->relation_district_street_model->vcurl($val, $compress);#采集页面
    $con = mb_convert_encoding($con, "UTF-8", "GBK");
    preg_match('/<div style="z-index: 2" class="qxName">(.*)<\/div>/siU', $con, $cons);
    $mess = explode('</a>', $cons[0]);
    unset($mess[0]);
    unset($mess[count($mess)]);
    foreach ($mess as $key => $val) {
      if (trim($val)) {
        $reg = '/\d+/';//匹配数字的正则表达式
        preg_match_all($reg, $val, $result);//取出区属id
        $district['district_id'] = $result[0][0];
        $district['district_name'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($val));//取出区属文字
        $district['tp_type'] = 3;
        $result = $this->relation_tp_street_model->add_tp_district($district);
        if ($result) {
          $url = 'http://esf.' . $city . '.fang.com/housing/' . $result . '__0_0_0_0_1_0_0/';
          $content = $this->relation_district_street_model->vcurl($url, $compress);
          preg_match('/<p class="contain" id="shangQuancontain">(.*)<\/p>/siU', $con, $cons);
          echo $url;
          //print_R($con);die();
        }
      }
    }
  }


  //采集58小区链接
  function community_url_collect()
  {
    set_time_limit(0);
    $city = $_SESSION[WEB_AUTH]["city"];
    //加载采集群发config
    $this->load->config('relation');
    //读取城市配置项
    $city_arr = $this->config->item('58_xiaoqu');
    $city = $city_arr[$city];
    //取出所有区属id
    $district_arr = $this->relation_tp_street_model->select_tp_district(array('tp_type' => 1));
    foreach ($district_arr as $key => $val) {
      $compress = 'gzip';
      $url = 'http://' . $city . '.58.com/xiaoqu/' . $val['district_id'] . '/';
      $content = $this->relation_district_street_model->vcurl($url, $compress);
      preg_match_all('/<b class="filternum">(.*)"/siU', $content, $num);
      $reg = '/\d+/';//匹配数字的正则表达式
      preg_match_all($reg, $num[0][0], $result);//取出小区总页数
      //计算总页数,58是每页20条
      $num = ceil($result[0][0] / 20);
      for ($i = 1; $i <= $num; $i++) {
        $url = 'http://' . $city . '.58.com/xiaoqu/' . $val['district_id'] . '/pn_' . $i . '/';
        $content = $this->relation_district_street_model->vcurl($url, $compress);
        preg_match_all('/<li class="tli1">.*href="(.*)" target="_blank"/siU', $content, $prj);
        foreach ($prj[1] as $value) {
          $data = array('url' => $value);
          $this->relation_district_street_model->add_community_url_collect($data);
        }
      }
    }
  }

  //采集58小区详情（不用改）
  function community_collect($id = 1)
  {
    $compress = 'gzip';
    $reg_num = '/\d+/';//匹配数字正则表达式
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $url_mess = $this->relation_district_street_model->get_community_url_collect($id);
    if ($url_mess) {
      $url = $url_mess['url'];
      echo $url;
      echo '<br>';
      $content = $this->relation_district_street_model->vcurl($url, $compress);
      //cmt_name 楼盘名称
      preg_match_all('/<h1 class="xiaoquh1">(.*)<span style="font-size:14px;">/siU', $content, $cmt_name);
      $info['cmt_name'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($cmt_name[1][0]));
      $this->load->model('read_model');
      //name_spell_s 名称拼音首字母
      $info['name_spell_s'] = $this->read_model->encode($info['cmt_name'], 'head');
      //name_spell 名称拼音
      $info['name_spell'] = $this->read_model->encode($info['cmt_name'], 'all');
      //dist_id 所属区属	streetid 所属街道
      preg_match_all('/<span class="ddtit">所在商圈<\/span><span class="ddmohao">：(.*)<\/dd>/siU', $content, $dis_str_mess);
      $dis_str = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($dis_str_mess[1][0]));
      $dis_strs = explode('-', $dis_str);
      $dist = $this->relation_district_street_model->deal_dist_id($dis_strs[0]);
      $street = $this->relation_district_street_model->deal_street_id($dist['id'], $dis_strs[1]);
      $info['dist_id'] = $dist['id'] == '' ? 0 : $dist['id'];
      $info['streetid'] = $street['id'] == '' ? 0 : $street['id'];
      //address 楼盘地址
      preg_match_all('/小区地址<\/span><span class="ddmohao">：(.*)查看地图/siU', $content, $address);
      $info['address'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($address[1][0]));
      //developers 开发商
      preg_match_all('/开发商<\/span><span class="ddmohao">：(.*)<\/dd>/siU', $content, $developers);
      $developer = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($developers[1][0]));
      $info['developers'] = $developer == '暂无信息' ? '0' : $developer;
      //property_company 物业公司
      preg_match_all('/物业公司<\/span><span class="ddmohao">：(.*)<\/dd>/siU', $content, $property_companys);
      $property_company = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($property_companys[1][0]));
      $info['property_company'] = $property_company == '暂无信息' ? '0' : $property_company;
      //buildarea 建筑面积
      preg_match_all('/建筑面积<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $buildareas);
      $buildarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($buildareas[1][0]));
      preg_match_all($reg_num, $buildarea, $buildarea_num);//取出数值
      $info['buildarea'] = $buildarea_num[0][0] == '' ? '0' : $buildarea_num[0][0];
      //coverarea 占地面积
      preg_match_all('/占地面积<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $coverareas);
      $coverarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($coverareas[1][0]));
      preg_match_all($reg_num, $coverarea, $coverarea_num);//取出数值
      $info['coverarea'] = $coverarea_num[0][0] == '' ? '0' : $coverarea_num[0][0];
      //build_date 建筑日期
      preg_match_all('/建筑年代<\/span><span class="ddmohao">：(.*)<span/siU', $content, $build_dates);
      $build_date = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($build_dates[1][0]));
      $build_date = strtotime($build_date);
      $build_date = date('Y', $build_date);
      $build_date = $build_date == '' ? '0' : $build_date;
      $info['build_date'] = $build_date == '1970' ? '0' : $build_date;
      //build_type 物业类型
      $info['build_type'] = '住宅';
      //parking 车位情况
      preg_match_all('/车位信息<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $parkings);
      $info['parking'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($parkings[1][0]));
      //total_room 总户数
      //build_num 总栋数
      //plot_ratio 容积率
      preg_match_all('/容&nbsp;积&nbsp;率<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $plot_ratios);
      $plot_ratio = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($plot_ratios[1][0]));
      preg_match_all($reg, $plot_ratio, $plot_ratio_num);//取出数值
      $info['plot_ratio'] = $plot_ratio_num[0][0] == '' ? '0' : $plot_ratio_num[0][0];
      //green_rate 绿化率
      preg_match_all('/绿&nbsp;化&nbsp;率<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $green_rates);
      $green_rate = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($green_rates[1][0]));
      preg_match_all($reg, $green_rate, $green_rate_num);//取出数值
      $info['green_rate'] = $green_rate_num[0][0] == '' ? '0' : $green_rate_num[0][0] / 100;
      //facilities 楼盘配套
      preg_match_all('/其&nbsp;&nbsp;它<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $facilities);
      $info['facilities'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($facilities[1][0]));
      //bus_line 公交线路
      preg_match_all('/公&nbsp;&nbsp;交<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $bus_lines);
      $info['bus_line'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($bus_lines[1][0]));
      //subway 地铁线路
      preg_match_all('/地&nbsp;&nbsp;铁<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content, $subways);
      $info['subway'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($subways[1][0]));
      //property_fee 物业费
      preg_match_all('/物业费<\/span><span class="ddmohao">：(.*)<\/dd>/siU', $content, $property_fees);
      $property_fee = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($property_fees[1][0]));
      preg_match_all($reg, $property_fee, $property_fee_num);//取出数值
      $info['property_fee'] = $property_fee_num[0][0] == '' ? '0' : $property_fee_num[0][0];
      //b_map_x 百度地图X坐标
      preg_match_all('/lon:\'(.*)\'/siU', $content, $b_map_xs);
      $info['b_map_x'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($b_map_xs[1][0]));
      //b_map_y 百度地图Y坐标
      preg_match_all('/lat:\'(.*)\'/siU', $content, $b_map_ys);
      $info['b_map_y'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($b_map_ys[1][0]));
      //averprice 均价
      preg_match_all('/本月均价<\/span><span class="ddmohao" style=" vertical-align: middle">：<\/span>(.*)<\/span>/siU', $content, $averprices);
      $averprice = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($averprices[1][0]));
      preg_match_all($reg_num, $averprice, $averprice_num);//取出数值
      $info['averprice'] = $averprice_num[0][0] == '' ? '0' : $averprice_num[0][0];
      //小区详情
      preg_match_all('/<div class="peitaoDiv" id="peitao_4">(.*)<\/div>/siU', $content, $introduce);
      $info['introduction'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($introduce[1][0]));
      $info['introduction'] = $info['introduction'] == '0' ? '' : $info['introduction'];
      //print_r($info);exit;
      //creattime 录入时间
      $info['creattime'] = time();
      //status 小区信息状态(1临时小区，2正式小区，3待审核,4已删除)
      $info['status'] = '2';
      //lock_correct 是否锁定不让纠错（0正常纠错，1锁定不允许纠错）
      $info['lock_correct'] = '0';
      //is_upload_pic 0 不允许显示上传按钮 1允许显示上传按钮
      $info['is_upload_pic'] = '1';
      if ($info['cmt_name']) {
        $add = $this->relation_district_street_model->add_community($info);
        if ($add) {
          echo "</br>采集成功";
          $this->relation_district_street_model->del_community_url_collect($url_mess['id']);
        }
      }
    }
    $id++;
    echo "<script>window.location.href='/relation_district_street/community_collect/" . $id . "';</script>";

    //print_r($info);
  }
//搜房小区名搜索详情
//    function community_collect_details() {
//        $compress = 'gzip';
//        $keyword='美好上郡';
//        $conInfo=strtolower(urlencode(iconv('utf-8', 'gbk', $keyword)));
//        $url = 'http://esf.taizhou.fang.com/EsfHouse/Project/list_new.aspx?searchtype=0&keyword='.$conInfo;
//        $content = $this->relation_district_street_model->vcurl($url, $compress);
//        $content = mb_convert_encoding($content, "UTF-8","GBK");
//        print_r($content);
//    }
  //采集搜房小区链接
  function community_url_collect_soufang()
  {
    //加载采集群发config
    $this->load->config('relation');
    $city = $_SESSION[WEB_AUTH]["city"];
    //读取城市配置项
    $city_arr = $this->config->item('soufang_xiaoqu');
    $city = $city_arr[$city];
    //获取所有搜房区属
    $district_arr = $this->relation_tp_street_model->select_tp_district(array('tp_type' => 3));
    foreach ($district_arr as $key => $val) {
      $compress = 'gzip';
      if ($city) {
        $url = 'http://esf.' . $city . '.fang.com/housing/' . $val['district_id'] . '__0_0_0_0_1_0_0/';
      } else {
        $url = 'http://esf.fang.com/housing/' . $val['district_id'] . '__0_0_0_0_1_0_0/';
      }
      $content = $this->relation_district_street_model->vcurl($url, $compress);
      $content = mb_convert_encoding($content, "UTF-8", "GBK");
      preg_match_all('/<span class="txt">(.*)"/siU', $content, $num);
      $reg = '/\d+/';//匹配数字的正则表达式
      preg_match_all($reg, $num[0][0], $result);//取出小区总页数
      $exist_block_num = 0;
      for ($num = 1; $num <= $result[0][0]; $num++) {
        if (!$city) {
          $url = 'http://esf.fang.com/housing/' . $val['district_id'] . '__0_0_0_0_' . $num . '_0_0/';
        } else {
          $url = 'http://esf.' . $city . '.fang.com/housing/' . $val['district_id'] . '__0_0_0_0_' . $num . '_0_0/';
        }
        $content = $this->relation_district_street_model->vcurl($url, $compress);
        $content = mb_convert_encoding($content, "UTF-8", "GBK");
        preg_match_all('/<!--详情小区图片-->.*style="visibility:hidden"><a href="(.*)" target="_blank" class="iconinfo" >查看详情/siU', $content, $prj);
        echo $num . '-' . $url . "<br>";
        //print_r($prj[1]) . '<br>';
        $exist_block_num_line = 0;
        foreach ($prj[1] as $k => $value) {
          $data = array('url' => $value);
          $this->load->database();
          $this->db->reconnect();
          $add_result = $this->relation_district_street_model->add_community_url_collect_soufang($data);
          if (is_full_array($add_result)) {
            $exist_block_num++;
            $exist_block_num_line++;
            //print_r($add_result);
          } else {
            echo $add_result . '-';
          }
        }
        $k = $k + 1;
        echo "-成功{$k}小区，重复{$exist_block_num_line}<br>";
      }
      $total = $total + $exist_block_num;
    }
    echo 'over' . '总重复小区数：' . $total;
  }

  //采集搜房小区详情（不用改）
  function community_collect_soufang($id = 1)
  {
    set_time_limit(0);
    $compress = 'gzip';
    $reg_num = '/\d+/';//匹配数字正则表达式
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $url_mess = $this->relation_district_street_model->get_community_url_collect_soufang($id);
    if ($url_mess) {
      $url = $url_mess['url'];
      echo $url;
      echo '<br>';
      $content = $this->relation_district_street_model->vcurl($url, $compress);
      $content = mb_convert_encoding($content, "UTF-8", "GBK");
      //cmt_name 楼盘名称
      preg_match_all('/<span class="floatl">(.*)<\/span>/siU', $content, $cmt_name);
      $info['cmt_name'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($cmt_name[1][0]));
      //搜房有两种详情页
      if ($info['cmt_name']) {
        //dist_id 所属区属	streetid 所属街道
        preg_match_all('/<strong>所在区域：<\/strong>(.*)<\/dd>/siU', $content, $dis_str_mess);
        $arr = explode(" ", $dis_str_mess[1][0]);//所属区域
        $arr[0] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($arr[0]));
        $arr[1] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($arr[1]));
        $dist = $this->relation_district_street_model->deal_dist_id($arr[0]);
        $street = $this->relation_district_street_model->deal_street_id($dist['id'], $arr[1]);
        $info['dist_id'] = $dist['id'] == '' ? 0 : $dist['id'];
        $info['streetid'] = $street['id'] == '' ? 0 : $street['id'];
        //address 楼盘地址
        preg_match_all('/<strong>小区地址：(.*)<\/dd>/siU', $content, $address);
        $info['address'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($address[1][0]));
        //developers 开发商
        preg_match_all('/<strong>开 发 商：(.*)<\/dd>/siU', $content, $developers);
        $developer = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($developers[1][0]));
        $info['developers'] = $developer == '暂无信息' ? '0' : $developer;
        //property_company 物业公司
        preg_match_all('/<strong>物业顾问公司:(.*)<\/dd>/siU', $content, $property_companys);
        $property_company = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($property_companys[1][0]));
        $info['property_company'] = $property_company == '暂无信息' ? '0' : $property_company;
        //buildarea 建筑面积
        preg_match_all('/<strong>建筑面积：(.*)<\/dd>/siU', $content, $buildareas);
        $buildarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($buildareas[1][0]));
        preg_match_all($reg_num, $buildarea, $buildarea_num);//取出数值
        $info['buildarea'] = $buildarea_num[0][0] == '' ? '0' : $buildarea_num[0][0];
        //coverarea 占地面积
        preg_match_all('/<strong>占地面积：(.*)<\/dd>/siU', $content, $coverareas);
        $coverarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($coverareas[1][0]));
        preg_match_all($reg_num, $coverarea, $coverarea_num);//取出数值
        $info['coverarea'] = $coverarea_num[0][0] == '' ? '0' : $coverarea_num[0][0];
        //build_date 建筑日期
        preg_match_all('/<strong>竣工时间：(.*)<\/dd>/siU', $content, $build_dates);
        $build_date = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($build_dates[1][0]));
        $build_date = strtotime($build_date);
        $build_date = date('Y', $build_date);
        $build_date = $build_date == '' ? '0' : $build_date;
        $info['build_date'] = $build_date == '1970' ? '0' : $build_date;
        //build_type 物业类型
        preg_match_all('/<strong>物业类别：(.*)<\/dd>/siU', $content, $build_types);
        $build_type = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($build_types[1][0]));
        $info['build_type'] = $build_type == '暂无信息' ? '0' : $build_type;
        //parking 车位情况
        preg_match_all('/<strong>停 车 位：(.*)<\/dt>/siU', $content, $parkings);
        $info['parking'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($parkings[1][0]));
        //total_room 总户数
        preg_match_all('/<strong>总 户 数：(.*)<\/dd>/siU', $content, $total_rooms);
        $total_room = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($total_rooms[1][0]));
        preg_match_all($reg_num, $total_room, $total_room_num);//取出数值
        $info['total_room'] = $total_room_num[0][0] == '' ? '0' : $total_room_num[0][0];
        //build_num 总栋数
        //plot_ratio 容积率
        preg_match_all('/<strong>容 积 率：(.*)<\/dd>/siU', $content, $plot_ratios);
        $plot_ratio = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($plot_ratios[1][0]));
        preg_match_all($reg, $plot_ratio, $plot_ratio_num);//取出数值
        $info['plot_ratio'] = $plot_ratio_num[0][0] == '' ? '0' : $plot_ratio_num[0][0];
        //green_rate 绿化率
        preg_match_all('/<strong>绿 化 率：(.*)<\/dd>/siU', $content, $green_rates);
        $green_rate = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($green_rates[1][0]));
        preg_match_all($reg, $green_rate, $green_rate_num);//取出数值
        $info['green_rate'] = $green_rate_num[0][0] == '' ? '0' : $green_rate_num[0][0] / 100;
        //facilities 楼盘配套
        preg_match_all('/<strong>附加信息：(.*)<\/dd>/siU', $content, $facilities);
        $info['facilities'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($facilities[1][0]));
        //property_fee 物业费
        preg_match_all('/<strong>物 业 费：(.*)<\/dd>/siU', $content, $property_fees);
        $property_fee = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($property_fees[1][0]));
        preg_match_all($reg, $property_fee, $property_fee_num);//取出数值
        $info['property_fee'] = $property_fee_num[0][0] == '' ? '0' : $property_fee_num[0][0];
        //introduction 楼盘简介
        preg_match_all('/<dt><div id="jjShow">(.*)<\/dl>/siU', $content, $introductions);
        $introduction = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($introductions[1][0]));
        $introduction = str_replace("收起", "", $introduction);
        $info['introduction'] = str_replace('&lt;', "", $introduction);
      } else {
        //cmt_name 楼盘名称
        preg_match_all('/<span class="biaoti">(.*)<\/span>/siU', $content, $cmt_name);
        $info['cmt_name'] = $cmt_name[1][0];

        //dist_id 所属区属	streetid 所属街道
        preg_match_all('/<dd>所属区域：(.*)<\/dd>/siU', $content, $dis_str_mess);
        $arr = explode(" ", $dis_str_mess[1][0]);//所属区域
        $arr[0] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($arr[0]));
        $arr[1] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($arr[1]));
        $dist = $this->relation_district_street_model->deal_dist_id(str_replace('区', '', $arr[0]));
        $street = $this->relation_district_street_model->deal_street_id($dist['id'], $arr[1]);
        $info['dist_id'] = $dist['id'] == '' ? 0 : $dist['id'];
        $info['streetid'] = $street['id'] == '' ? 0 : $street['id'];

        //address 楼盘地址
        preg_match_all('/<dd>楼盘地址：<span title="(.*)">(.*)<\/span><\/dd>/siU', $content, $address);
        $info['address'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $address[1][0]);

        //developers 开发商
        preg_match_all('/<dd>开 发 商：(.*)<\/dd>/siU', $content, $developers);
        $developer = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $developers[1][0]);
        $info['developers'] = $developer == '暂无资料' ? '0' : $developer;

        //property_company 物业公司
        preg_match_all('/<dd>物业公司：(.*)<\/dd>/siU', $content, $property_companys);
        $property_company = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $property_companys[1][0]);
        $info['property_company'] = $property_company == '暂无资料' ? '0' : $property_company;

        //buildarea 建筑面积
        preg_match_all('/<dd>建筑面积：(.*)平方米<\/dd>/siU', $content, $buildareas);
        $buildarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $buildareas[1][0]);
        $info['buildarea'] = $buildarea == '' ? '0' : $buildarea;
        //coverarea 占地面积
        preg_match_all('/<dd>占地面积：(.*)平方米<\/dd>/siU', $content, $coverareas);
        $coverarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $coverareas[1][0]);
        $info['coverarea'] = $coverarea == '' ? '0' : $coverarea;

        //build_date 建筑日期
        preg_match_all('/<dd>竣工时间：(.*)<\/dd>/siU', $content, $build_dates);
        $build_date = date('Y', strtotime($build_dates[1][0]));
        $info['build_date'] = $build_date == '1970' ? '0' : $build_date;

        //build_type 物业类型
        preg_match_all('/<dd>物业类别：(.*)<\/dd>/siU', $content, $build_types);
        $build_type = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $build_types[1][0]);
        $info['build_type'] = $build_type == '暂无资料' ? '0' : $build_type;

        //parking 车位情况
        preg_match_all('/<dd>停 车 位：(.*)<\/dd>/siU', $content, $parkings);
        $paker = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $parkings[1][0]);
        $info['parking'] = $paker == '暂无资料' ? '0' : $paker;

        //total_room 总户数
        preg_match_all('/<dd>总 户 数：(.*)<\/dd>/siU', $content, $total_rooms);
        $total_room = $total_rooms[1][0];
        preg_match_all($reg_num, $total_room, $total_room_num);//取出数值
        $info['total_room'] = $total_room_num[0][0] == '' ? '0' : $total_room_num[0][0];
        //build_num 总栋数
        //
        //plot_ratio 容积率
        preg_match_all('/<dd>容 积 率：(.*)<\/dd>/siU', $content, $plot_ratios);
        $plot_ratio = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $plot_ratios[1][0]);
        $info['plot_ratio'] = $plot_ratio == '' ? '0' : $plot_ratio;

        //green_rate 绿化率
        preg_match_all('/<dd>绿 化 率：(.*)%<\/dd>/siU', $content, $green_rates);
        $green = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $green_rates[1][0]);
        $info['green_rate'] = $green == '' ? '0' : $green / 100;

        //facilities 楼盘配套
        preg_match_all('/<dt>楼内配套：(.*)<\/dt>/siU', $content, $facilities);
        $info['facilities'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', $facilities[1][0]);

        //property_fee 物业费
        preg_match_all('/<dd>物 业 费：(.*)<\/dd>/siU', $content, $property_fees);
        preg_match_all($reg, $property_fees[1][0], $total_room_num);//取出数值
        $info['property_fee'] = $total_room_num[0][0] == '' ? '0' : $total_room_num[0][0];

        //introduction 楼盘简介
        preg_match_all('/<div class="jianjie" style="border:0;">(.*)<\/div>/siU', $content, $introductions);
        $info['introduction'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($introductions[1][0]));
      }
      //print_R($info);die();
      //creattime 录入时间
      $info['creattime'] = time();//print_R($info);
      if ($info['cmt_name']) {
        $add = $this->relation_district_street_model->add_community_soufang($info);
        if ($add) {
          $this->relation_district_street_model->del_community_url_collect_soufang($url_mess['id']);
        }
      }

    }
    //print_r($info);
    $id++;
    echo "<script>window.location.href='/relation_district_street/community_collect_soufang/{$id}';</script>";
  }
  //楼盘建筑年代更改（只留年份）
//    function update_year() {
//        for($i=1;$i<765;$i++){
//            $result=$this->relation_district_street_model->get_community_message($i);
//            if($result['build_date']>0){
//                $build_date=date('Y',$result['build_date']);
//                $data=array('build_date'=>$build_date);
//                $this->relation_district_street_model->update_community_message($result['id'],$data);
//            }
//        }
//    }
  //楼盘数据完善
  function perfected_community()
  {
    //取出城市站楼盘数据
    $num = $this->relation_district_street_model->count_community();
    $j = 0;
    for ($i = 1; $i <= $num['id']; $i++) {
      $result = $this->relation_district_street_model->get_community_message(array('id' => $i));
      if ($result) {
        //搜索搜房数据相同小区信息
        $soufang = $this->relation_district_street_model->get_community_soufang($result['cmt_name']);
        if ($soufang) {
          $data = array();
          if (strlen($result['developers']) < 1 && strlen($soufang['developers']) > 1) {//开发商 developers
            $data['developers'] = $soufang['developers'];
          }
          if (strlen($result['property_company']) < 1 && strlen($soufang['property_company']) > 1) {//property_company 物业公司
            $data['property_company'] = $soufang['property_company'];
          }
          if ($result['dist_id'] == 0 && $soufang['dist_id'] != 0) {//dist_id 所属区属	streetid 所属街道
            $data['dist_id'] = $soufang['dist_id'];
            $data['streetid'] = $soufang['streetid'];
          }
          if ($result['buildarea'] == 0 && $soufang['buildarea'] != 0) {//buildarea 建筑面积
            $data['buildarea'] = $soufang['buildarea'];
          }
          if ($result['coverarea'] == 0 && $soufang['coverarea'] != 0) {//coverarea 占地面积
            $data['coverarea'] = $soufang['coverarea'];
          }
          if ($result['build_date'] == 0 && $soufang['build_date'] != 0) {//build_date 建筑日期
            $data['build_date'] = $soufang['build_date'];
          }
          if ($result['build_type'] == 0 && strlen($soufang['build_type']) > 1) {//build_type 物业类型
            $data['build_type'] = $soufang['build_type'];
          }
          if ($result['total_room'] == 0 && $soufang['total_room'] != 0) {//total_room 总户数
            $data['total_room'] = $soufang['total_room'];
          }
          if ($result['plot_ratio'] == 0 && $soufang['plot_ratio'] != 0) {//plot_ratio 容积率
            $data['plot_ratio'] = $soufang['plot_ratio'];
          }
          if ($result['green_rate'] == 0 && $soufang['green_rate'] != 0) {//green_rate 绿化率
            $data['green_rate'] = $soufang['green_rate'];
          }
          if (strlen($result['introduction']) < 1 && strlen($soufang['introduction']) > 1) {//introduction 楼盘简介
            $data['introduction'] = $soufang['introduction'];
          }
          if ($result['facilities'] == '暂无资料' && strlen($soufang['facilities']) > 1) {//facilities 楼盘配套
            $data['facilities'] = $soufang['facilities'];
          }
          if ($result['parking'] == '暂无资料' && strlen($soufang['parking']) > 1) {//parking 车位情况
            $data['parking'] = $soufang['parking'];
          }
          if ($result['property_fee'] == 0 && $soufang['property_fee'] != 0) {//property_fee 物业费
            $data['property_fee'] = $soufang['property_fee'];
          }
          $result = $this->relation_district_street_model->update_community_message($i, $data);
          if ($result) {
            $j++;
          }
          //            print_r($data);exit;
          //            print_r($val);echo '<br>';print_r($soufang);exit;
        }
      }
    }
    echo "共有" . $j . "条小区完善成功";
  }

  /**
   * 采集完区属板块后更新关联网
   */
  public function update_relation_street($type = 1)
  {
    //获取所有板块
    $street = $this->relation_tp_street_model->get_streets();
    if (is_full_array($street)) {
      //将数据插入关联表
      foreach ($street as $key => $val) {
        $data['street_id'] = $val['id'];
        $data['street_name'] = $val['streetname'];
        $data['dist_id'] = $val['dist_id'];
        $this->relation_tp_street_model->add_relation_street($data);
      }
      $this->relation_street($type);
    }
  }


  /**
   * 更新第三方板块数据id
   * type 1五八2赶集
   * PD搞 关联58赶集（用于群发）
   */
  public function relation_street($type = 1)
  {
    $tp_street = $this->relation_tp_street_model->select_tp_street(array('tp_type' => $type));
    //print_r($tp_street);
    foreach ($tp_street as $value) {
      $where = "(street_name = '{$value['street_name']}'";
      if (mb_substr($value['street_name'], mb_strlen($value['street_name']) - 2, mb_strlen($value['street_name'])) == '街道') {
        $street_name1 = mb_substr($value['street_name'], 0, mb_strlen($value['street_name']) - 2);
        $where .= " or street_name = '{$street_name1}'";
      } elseif (mb_substr($value['street_name'], mb_strlen($value['street_name']) - 1, mb_strlen($value['street_name'])) == '镇') {
        $street_name1 = mb_substr($value['street_name'], 0, mb_strlen($value['street_name']) - 1);
        $where .= " or street_name = '{$street_name1}'";
      } elseif ($value['street_name'] == '其他') {
        $result = $this->relation_tp_street_model->select_tp_district(array('district_id' => $value['dist_id'], 'tp_type' => 2));
        $where .= " or street_name = '{$result[0]['district_name']}周边'";
      }
      $where .= ")";
      if ($type == 1) {
        $data = array('wuba_dist_id' => $value['dist_id'], 'wuba_street_id' => $value['street_id']);//58
      } else {
        $data = array('ganji_dist_id ' => $value['dist_id'], 'ganji_street_id ' => $value['street_id']);//gj
      }
      $result = $this->relation_tp_street_model->select_street($where);
      if ($result) {
        $this->relation_tp_street_model->update_street($result[0]['id'], $data);
      }
    }
  }

  public function get_position($address)
  {
    $compress = 'gzip';
    $url = "http://api.map.baidu.com/geocoder/v2/?ak=B944e1fce373e33ea4627f95f54f2ef9&address=" . $address . "&output=json";
    $res = $this->relation_district_street_model->vcurl($url, $compress);
    $result = json_decode($res, true);
    return $result['result'];
  }


  public function get_metro()
  {
    $this->load->model('metro_model');
    $compress = 'gzip';
    $city = $_SESSION[WEB_AUTH]["city"];
    //读取城市配置项
    $this->load->config('relation');
    $city_arr = $this->config->item('soufang_xiaoqu');
    $city = $city_arr[$city];
    if ($city) {
      $url = 'http://esf.' . $city . '.fang.com/housing/__0_0_0_0_1_0_1/';
    } else {
      $url = 'http://esf.fang.com/housing/__0_0_0_0_1_0_1/';
    }
    $con = $this->relation_district_street_model->vcurl($url, $compress);  #采集页面
    $con = mb_convert_encoding($con, "UTF-8", "GBK");
    preg_match('/<div style="z-index: 2" class="qxName">(.*)<\/div>/siU', $con, $cons);
    $metros = explode('</a>', $cons[1]);
    unset($metros[0]);
    foreach ($metros as $key => $val) {
      //匹配地铁线
      preg_match('/<a href="(.*)">(.*)/', $val, $metro_line);
      $insert_data = array('line_name' => $metro_line[2], 'is_show' => 1);
      $result = $this->metro_model->get_metro_line($insert_data);
      $line_id = $result[0]['id'];
      if (!$line_id) {
        $line_id = $this->metro_model->add_metro_line($insert_data);
      }
      //匹配地铁站
      if ($city) {
        $url = 'http://esf.' . $city . '.fang.com' . $metro_line[1];
      } else {
        $url = 'http://esf.fang.com' . $metro_line[1];
      }
      $con = $this->relation_district_street_model->vcurl($url, $compress);  #采集页面
      $con = mb_convert_encoding($con, "UTF-8", "GBK");
      preg_match('/<p class="contain" id="shangQuancontain">(.*)<\/p>/siU', $con, $cons);
      $metro_sites = explode('</a>', $cons[1]);
      unset($metro_sites[0]);
      unset($metro_sites[count($metro_sites)]);
      foreach ($metro_sites as $key => $val) {
        //匹配地铁线
        preg_match('/<a href="(.*)">(.*)/', $val, $metro_site);
        $insert_data1 = array('site_name' => $metro_site[2], 'is_show' => 1, 'metro_id' => $line_id);
        $result = $this->metro_model->get_metro_site($insert_data1);
        if (!$result) {
          $select_data = array('site_name' => $metro_site[2], 'is_show' => 1, 'metro_id <>' => $line_id);
          $result = $this->metro_model->get_metro_site($select_data);
          if ($result) {
            $insert_data1['line_center_point'] = 1;
          } else {
            $insert_data1['line_center_point'] = 2;
          }
          //$position = $this->get_position($_SESSION[WEB_AUTH]['cityname'].'地铁'.$metro_site[2]);
          //$insert_data1['b_map_x'] = $position['location']['lng'];
          //$insert_data1['b_map_y'] = $position['location']['lat'];
          $this->metro_model->add_metro_site($insert_data1);
        }
      }
    }
  }


  public function strip_tags($id = 1)
  {
    $info = $this->relation_district_street_model->get_community_soufang_by_id($id);
    $introduction = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($info['introduction']));
    $this->relation_district_street_model->update_community_soufang($id, array('introduction' => $introduction));
    $id++;
    echo "<script>window.location.href='/relation_district_street/strip_tags/{$id}';</script>";
  }

  //蓝房网一次性增量楼盘获取url
  public function lanfang_community_url()
  {
    //取出所有区属id
    $compress = 'gzip';
    for ($i = 1; $i <= 366; $i++) {
      $url = 'http://fz.esf.lanfw.com/xiaoqu/p' . $i;
      $content = $this->relation_district_street_model->vcurl($url, $compress);//echo $content;die();
      preg_match_all('/<div class="houseTxt fl">.*href="(.*)" target="_blank"/siU', $content, $prj);
      foreach ($prj[1] as $value) {
        $data = array('url' => $value);
        $this->relation_district_street_model->add_community_url_collect($data);
      }
    }
  }

  public function lanfang_community_collect($id = 1)
  {
    $compress = 'gzip';
    $reg_num = '/\d+/';//匹配数字正则表达式
    $reg = '/\d+(\.\d{0,2})?/';//匹配数字,小数的正则表达式
    $url_mess = $this->relation_district_street_model->get_community_url_collect($id);
    if ($url_mess) {
      $url = $url_mess['url'];
      echo $url;
      echo '<br>';
      $content = $this->relation_district_street_model->vcurl($url, $compress);
      //cmt_name 楼盘名称
      preg_match_all('/<div class="name">(.*)<\/h2>/siU', $content, $cmt_name);
      $info['cmt_name'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($cmt_name[1][0]));
      //检查该楼盘是否在楼盘字典里
      $community = $this->relation_district_street_model->get_community_by_cond(array('cmt_name' => $info['cmt_name']));
      if (empty($community)) {
        echo '无此楼盘';
        $this->load->model('read_model');
        //name_spell_s 名称拼音首字母
        $info['name_spell_s'] = $this->read_model->encode($info['cmt_name'], 'head');
        //name_spell 名称拼音
        $info['name_spell'] = $this->read_model->encode($info['cmt_name'], 'all');
        //dist_id 所属区属	streetid 所属街道
        preg_match_all('/<samp>区&nbsp;&nbsp;&nbsp;&nbsp;域：<\/samp>(.*)<\/span>/siU', $content, $dis_str_mess);
        $dist = $this->relation_district_street_model->deal_dist_id($dis_str_mess[1][0]);
        $info['dist_id'] = $dist['id'] == '' ? 0 : $dist['id'];
        $info['streetid'] = 0;
        //address 楼盘地址
        preg_match_all('/<samp>地&nbsp;&nbsp;&nbsp;&nbsp;址：<\/samp>(.*)<\/li>/siU', $content, $address);
        $info['address'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($address[1][0]));
        //developers 开发商
        preg_match_all('/<samp>开 发 商：<\/samp>(.*)<\/li>/siU', $content, $developers);
        $developer = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($developers[1][0]));
        $info['developers'] = $developer == '暂无' ? '0' : $developer;
        //property_company 物业公司
        preg_match_all('/<samp>物业公司：<\/samp>(.*)<\/li>/siU', $content, $property_companys);
        $property_company = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($property_companys[1][0]));
        $info['property_company'] = $property_company == '暂无' ? '0' : $property_company;

        $url1 = str_replace('.html', '/details', $url);
        $content1 = $this->relation_district_street_model->vcurl($url1, $compress);
        echo $url1;
        echo '<br>';

        //buildarea 建筑面积
        preg_match_all('/<td bgcolor="#f9f9f9">总建筑面积<\/td>(.*)<\/td>/siU', $content1, $buildareas);
        $buildarea = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($buildareas[1][0]));
        preg_match_all($reg_num, $buildarea, $buildarea_num);//取出数值
        $info['buildarea'] = $buildarea_num[1][0] == '' ? '0' : $buildarea_num[1][0];
        //coverarea 占地面积
        //preg_match_all('/占地面积<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content,$coverareas);
        ////$coverarea=preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/','',strip_tags($coverareas[1][0]));
        //preg_match_all($reg_num,$coverarea,$coverarea_num);//取出数值
        //$info['coverarea']=$coverarea_num[0][0]==''?'0':$coverarea_num[0][0];
        //build_date 建筑日期
        preg_match_all('/<samp>年&nbsp;&nbsp;&nbsp;&nbsp;代：<\/samp>(.*)<\/span>/siU', $content, $build_dates);
        $build_date = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($build_dates[1][0]));
        preg_match_all($reg_num, $build_date, $build_date);//取出数值
        $info['build_date'] = $build_date[1][0] == '' ? '0' : $build_date[1][0];
        //build_type 物业类型
        preg_match_all('/<td width="108" bgcolor="#f9f9f9">物业类型<\/td>(.*)<\/td>/siU', $content1, $buy_type);
        $buy_type = trim(strip_tags($buy_type[1][0]));
        $buy_type = str_replace('普通', '', $buy_type);
        $buy_type = str_replace(' ', ',', $buy_type);
        $buy_type = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($buy_type));
        $info['build_type'] = $buy_type == '暂无数据' ? '0' : $buy_type;
        //parking 车位情况
        preg_match_all('/<td bgcolor="#f9f9f9">停车位<\/td>(.*)<\/td>/siU', $content1, $parkings);
        $info['parking'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($parkings[1][0]));
        //total_room 总户数
        //build_num 总栋数
        //plot_ratio 容积率
        preg_match_all('/<td bgcolor="#f9f9f9">容积率<\/td>(.*)<\/td>/siU', $content1, $plot_ratios);
        $plot_ratio = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($plot_ratios[1][0]));
        preg_match_all($reg, $plot_ratio, $plot_ratio_num);//取出数值
        $info['plot_ratio'] = $plot_ratio_num[0][0] == '' ? '0' : $plot_ratio_num[0][0];
        //green_rate 绿化率
        preg_match_all('/<td bgcolor="#f9f9f9">绿化率<\/td>(.*)<\/td>/siU', $content1, $green_rates);
        $green_rate = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($green_rates[1][0]));
        preg_match_all($reg, $green_rate, $green_rate_num);//取出数值
        $info['green_rate'] = $green_rate_num[0][0] == '' ? '0' : $green_rate_num[0][0] / 100;
        //facilities 楼盘配套
        preg_match_all('/<div class="detailedContent">(.*)<\/div>/siU', $content1, $facilities);
        $info['facilities'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($facilities[1][1]));
        //bus_line 公交线路
        //preg_match_all('/公&nbsp;&nbsp;交<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content,$bus_lines);
        // $info['bus_line']=preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/','',strip_tags($bus_lines[1][0]));
        //subway 地铁线路
        //preg_match_all('/地&nbsp;&nbsp;铁<\/span><span class="ddmohao">：(.*)<\/li>/siU', $content,$subways);
        //$info['subway']=preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/','',strip_tags($subways[1][0]));
        //property_fee 物业费
        //preg_match_all('/物业费<\/span><span class="ddmohao">：(.*)<\/dd>/siU', $content,$property_fees);
        //$property_fee=preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/','',strip_tags($property_fees[1][0]));
        //preg_match_all($reg,$property_fee,$property_fee_num);//取出数值
        //$info['property_fee']=$property_fee_num[0][0]==''?'0':$property_fee_num[0][0];
        //b_map_x 百度地图X坐标
        preg_match_all($reg_num, $url, $id_array);//取出数值
        $url2 = 'http://house.lanfw.com/map/smap.php?id=' . $id_array[0][0] . '&city_id=fz&width=690';
        echo $url2 . '</br>';
        $content2 = $this->relation_district_street_model->vcurl($url2, $compress);
        preg_match_all('/\'lng\':\'(.*)\'/siU', $content2, $b_map_xs);
        $info['b_map_x'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($b_map_xs[1][0]));
        //b_map_y 百度地图Y坐标
        preg_match_all('/\'lat\':\'(.*)\'/siU', $content2, $b_map_ys);
        $info['b_map_y'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($b_map_ys[1][0]));
        //averprice 均价
        //preg_match_all('/本月均价<\/span><span class="ddmohao" style=" vertical-align: middle">：<\/span>(.*)<\/span>/siU', $content,$averprices);
        //$averprice=preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/','',strip_tags($averprices[1][0]));
        //preg_match_all($reg_num,$averprice,$averprice_num);//取出数值
        //$info['averprice']=$averprice_num[0][0]==''?'0':$averprice_num[0][0];
        //小区详情
        preg_match_all('/<div class="detailedContent">(.*)<\/div>/siU', $content1, $introduce);
        $info['introduction'] = preg_replace('/(\s|\&nbsp\;|　|\xc2\xa0)/', '', strip_tags($introduce[1][0]));
        //print_r($info);exit;
        //creattime 录入时间
        $info['creattime'] = time();
        //status 小区信息状态(1临时小区，2正式小区，3待审核,4已删除)
        $info['status'] = '2';
        //lock_correct 是否锁定不让纠错（0正常纠错，1锁定不允许纠错）
        $info['lock_correct'] = '0';
        //is_upload_pic 0 不允许显示上传按钮 1允许显示上传按钮
        $info['is_upload_pic'] = '1';
        $add = $this->relation_district_street_model->add_community($info);
        $this->relation_district_street_model->del_community_url_collect($url_mess['id']);
      } else {
        echo '有此楼盘';
      }
    }
    $id++;
    echo "<script>window.location.href='/relation_district_street/lanfang_community_collect/" . $id . "';</script>";
  }

}
