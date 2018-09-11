<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 头像审核
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      sun
 */
class head_review extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('head_review_model');
    $this->load->model('auth_review_model');
    $this->load->model('broker_info_model');
    $this->load->helper('user_helper');
  }

  public function index()
  {
    $data_view = array();
    $this->load->helper('page_helper');//引入分页类
    $where = '';
    $search_where = $this->input->post('search_where');
    $search_value = $this->input->post('search_value');
    $search_status = $this->input->post('search_status');
    if (!$search_status) {
      $search_status = 99;
    }
    $pg = $this->input->post('pg');
    if ($search_where && $search_value) {

      //搜索查询条件值
      $this->broker_info_model->set_select_fields(array('broker_id'));
      $brokers = $this->broker_info_model->get_all_by($search_where . ' like ' . "'%$search_value%'");

      $broker_ids = $this->broker_info_model->format_brokers($brokers);

      if (is_full_array($broker_ids)) {
        $broker_ids = implode(',', $broker_ids);
        //var_dump($broker_ids);exit;
        $where = "broker_id in($broker_ids)";
      } else {
        $where = "broker_id in('')";
      }
    }

    if ($search_status != 99) {
      if ($where == '') {
        $where = "status = " . $search_status;
      } else {
        $where .= " and status = " . $search_status;
      }
    }

    //条件
    $data_view['where_cond'] = array(
      'search_where' => $search_where, 'search_value' => $search_value, 'search_status' => $search_status
    );

    //分页开始
    $data_view['count'] = $this->head_review_model->count_by($where);
    $data_view['pagesize'] = 10; //设定每一页显示的记录数
    $data_view['pages'] = $data_view['count'] ? ceil($data_view['count']
      / $data_view['pagesize']) : 0;  //计算总页数
    $data_view['page'] = isset($pg) && $pg ? intval($pg) : 1; // 获取当前页数
    $data_view['page'] = ($data_view['page'] > $data_view['pages']
      && $data_view['pages'] != 0) ? $data_view['pages']
      : $data_view['page'];  //判断跳转页数
    //计算记录偏移量
    $data_view['offset'] = $data_view['pagesize'] * ($data_view['page'] - 1);
    //申请列表
    $head_info = $this->head_review_model->get_all_by($where, $data_view['offset'], $data_view['pagesize']);


    foreach ($head_info as $key => $value) {
      $this->broker_info_model->set_select_fields(array('truename', 'phone'));
      $broker_info = $this->broker_info_model->get_by_broker_id($value['broker_id']);
      //var_dump($broker_info);exit;
      $head_info[$key]['truename'] = $broker_info['truename'];
      $head_info[$key]['phone'] = $broker_info['phone'];
    }

    $data_view['head_info'] = $head_info;

    $data_view['title'] = '头像审核';
    $data_view['conf_where'] = 'index';
    $this->load->view('head_review/index', $data_view);
  }

  /**
   * 处理图片
   * 2015.10.8
   * cc
   */
  function changepic($pic = '')
  {
    if ($pic != '') {
      if (strstr($pic, '_120x90')) {
        $picurl = str_replace('_120x90', '', $pic);
      } elseif (strstr($pic, '/thumb/')) {
        $picurl = str_replace('/thumb/', '/', $pic);
      } else {
        $picurl = $pic;
      }
    } else {
      $picurl = $pic;
    }
    return $picurl;
  }

  /**
   * 修改头像资料申请信息
   * @param int $head_id 头像资料序号
   */
  public function modify($head_id)
  {
    $data_view = array();

    $data_view['conf_where'] = 'index';
    $data_view['modifyResult'] = '';
    $data_view['head_id'] = $head_id;
    $head_review_info_new = $this->head_review_model->get_by_id($head_id);//获取信息
    $broker_id = $head_review_info_new['broker_id'];
    $broker_info = $this->broker_info_model->get_by_broker_id($broker_id);
    $auth_info = $this->auth_review_model->get_new("broker_id = " . $broker_id, 0, 1);//认证用户的认证信息
    //最新的3张旧头像
    $head_info_old1 = $this->head_review_model->get_new("broker_id = " . $broker_id, 1, 1);
    $head_info_old2 = $this->head_review_model->get_new("broker_id = " . $broker_id, 2, 1);
    $head_info_old3 = $this->head_review_model->get_new("broker_id = " . $broker_id, 3, 1);
    //图片url处理换成大图
    if (!empty($head_review_info_new['headpic'])) {
      $photo_str = '';
      $photo_arr = changepic($head_review_info_new['headpic']);
      $photo_str .= $photo_arr;
      $head_review_info_new['headpic'] = $photo_str;
    }
    if (!empty($broker_info['photo'])) {
      $photo_str = '';
      $photo_arr = changepic($broker_info['photo']);
      $photo_str .= $photo_arr;
      $broker_info['photo'] = $photo_str;
    }
    if (!empty($head_info_old1['headpic'])) {
      $photo_str = '';
      $photo_arr = changepic($head_info_old1['headpic']);
      $photo_str .= $photo_arr;
      $head_info_old1['headpic'] = $photo_str;
    }
    if (!empty($head_info_old2['headpic'])) {
      $photo_str = '';
      $photo_arr = changepic($head_info_old2['headpic']);
      $photo_str .= $photo_arr;
      $head_info_old2['headpic'] = $photo_str;
    }
    if (!empty($head_info_old3['headpic'])) {
      $photo_str = '';
      //$photo_arr = explode('/thumb', $head_info_old3['headpic']);
      $photo_arr = changepic($head_info_old3['headpic']);
      //print_r($photo_arr);
      /*if(!empty($photo_arr) && is_array($photo_arr)){
          foreach($photo_arr as $k => $v){
              $photo_str .= $v;
          }
      }*/
      $photo_str .= $photo_arr;
      $head_info_old3['headpic'] = $photo_str;
    }

    $data_view['head_review_info_new'] = $head_review_info_new;
    $data_view['broker_info'] = $broker_info;
    $data_view['head_info_old1'] = $head_info_old1;
    $data_view['head_info_old2'] = $head_info_old2;
    $data_view['head_info_old3'] = $head_info_old3;
    $data_view['title'] = '头像照片审核';
    //print_r($data_view);die();
    $submit_flag = $this->input->post('submit_flag');
    //echo $broker_id;exit;
    if ($submit_flag == 'modify') {
      //获取参数
      $status = $this->input->post('status');
      $remark = $this->input->post('remark');
      $this->load->model('message_model');
      $broker_name = $broker_info['truename'];


      if ($status == 2) {//通过
        $params['reason'] = $remark;
        //发送推送消息
        $this->load->model('push_func_model');
        $this->push_func_model->send(1, 7, 1, 0, $broker_id);
        //同步头像到资料认证和用户数据库
        //用户数据库
        $update_data = array('photo' => $head_review_info_new['headpic']);
        $this->broker_info_model->update_by_broker_id($update_data, $broker_id);
        //发送消息
        $this->message_model->add_message('8-46-1', $broker_id, $broker_name, '/my_info/', $params);
        //认证数据库
        $this->auth_review_model->update_by_id(array('photo' => $head_review_info_new['headpic'], 'updatetime' => time()), $auth_info['id']);
      } elseif ($status == 3) {//拒绝
        $params['reason'] = $remark;
        $this->message_model->add_message('8-46-2', $broker_id, $broker_name, '/my_info/', $params);
      }

      $this->head_review_model->update_by_id(array('status' => $status, 'remark' => $remark, 'updatetime' => time()), $head_id);
      echo '修改成功！';
      exit;
    }
    //需要加载的JS
    $this->load->helper('common_load_source_helper');
    $data_view['css'] = load_css('mls/css/v1.0/autocomplete.css');
    //需要加载的JS
    $data_view['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('head_review/modify', $data_view);
  }
}

/* End of file head_review.php */
/* Location: ./application/mls_admin/controllers/head_review.php */
