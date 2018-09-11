<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('user_model');
    $this->load->model('user_group_model');
    $this->load->model('purview_node_model');
    $this->load->model('purview_father_node_model');
    $this->load->model('city_model');
    $this->load->model('features_notice_model');
  }

  /**
   * 入口页面，功能迭代通知
   */
  public function index()
  {
    $data = array();

    $data['title'] = "功能迭代通知";
    $data['conf_where'] = 'index';

    $where_cond = array(
      'status' => 1
    );
    //分页开始
    $data['user_num'] = $this->features_notice_model->get_features_notice_num();
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    //获取所有主菜单
    $data['lists'] = $this->features_notice_model->getfeatures_notice($where_cond, $data['like_code'], $data['offset'], $data['pagesize'], 'create_time');

    //获取待办相关信息
    //认证审核
    $auth_review = $this->purview_node_model->get_base_node(array('name' => '认证资料审核'));
    $this->load->model('auth_review_model');
    $count = $this->auth_review_model->count_by(array('status' => 1));
    $data['auth_review'] = $auth_review[0];
    $data['auth_review']['num'] = $count ? $count : 0;
    //头像审核
    $head_review = $this->purview_node_model->get_base_node(array('name' => '头像审核'));
    $this->load->model('head_review_model');
    $count = $this->head_review_model->count_by(array('status' => 1));
    $data['head_review'] = $head_review[0];
    $data['head_review']['num'] = $count ? $count : 0;
    //楼盘审核
    $community = $this->purview_node_model->get_base_node(array('name' => '楼盘审核'));
    $this->load->model('community_model');
    $count = $this->community_model->getcommunitynum(array('status' => 3));
    $data['community'] = $community[0];
    $data['community']['num'] = $count ? $count : 0;
    //中介举报审核管理
    $blacklist = $this->purview_node_model->get_base_node(array('name' => '中介举报审核管理'));
    $this->load->model('blacklist_model');
    $count = $this->blacklist_model->get_reportlist_num(array('r_status' => 3));
    $data['blacklist'] = $blacklist[0];
    $data['blacklist']['num'] = $count ? $count : 0;
    //合作资料一审审核
    $cooperate_chushen = $this->purview_node_model->get_base_node(array('name' => '合作资料一审审核'));
    $this->load->model('cooperate_chushen_model');
    $count = $this->cooperate_chushen_model->get_cooperate_chushen_num(array('status' => 0));
    $data['cooperate_chushen'] = $cooperate_chushen[0];
    $data['cooperate_chushen']['num'] = $count ? $count : 0;
    //真实合作资料审核
    $cooperate_check = $this->purview_node_model->get_base_node(array('name' => '真实合作资料审核'));
    $this->load->model('sell_house_model');
    $count = $this->sell_house_model->get_sell_house_num_by_cond("status != 5 and cooperate_check = 2 and (isshare=1 or isshare=3) ");
    $data['cooperate_check'] = $cooperate_check[0];
    $data['cooperate_check']['num'] = $count ? $count : 0;
    //出售委托管理审核
    $entrust_sell_review = $this->purview_node_model->get_base_node(array('name' => '出售委托管理审核'));
    $this->load->model('entrust_model');
    $count = $this->entrust_model->get_num_by(array('is_check' => 1), $tbl = "ent_sell s");
    $data['entrust_sell_review'] = $entrust_sell_review[0];
    $data['entrust_sell_review']['num'] = $count ? $count : 0;

    $this->load->view('features_notice/index2', $data);
  }

  /**
   * 用户列表页面
   */
  public function data_list()
  {
    //当前用户
    $this_user = $_SESSION[WEB_AUTH];
    $role_arr = array(2);
    //超级管理员
    if ($this_user['role'] == 1) {
      $role_arr = array(2, 3, 4);
    }
    $data['city'] = $this->city_model->get_all_city();
    $like = array();
    $where_arr = array();
    //状态
    $status = intval($this->input->post('status'));
    $data['status'] = $status;
    if (isset($status) && !empty($status)) {
      if (1 == $status) {
        $where_arr['status'] = 1;
      } else {
        $where_arr['status'] = 0;
      }
    }
    //城市
    $city_id = intval($this->input->post('city'));
    if (!empty($city_id)) {
      $data['city_id'] = $city_id;
      $where_cond = array(
        'city_id' => $city_id
      );
      $data['user_group_arr'] = $this->user_group_model->get_user_group($where_cond);
    }
    //用户组
    $group = $this->input->post('group');
    if (!empty($group)) {
      $data['user_group_ids'] = $group;
      $like['user_group_ids'] = ',' . $group . ',';
    }
    //某城市用户组不限
    $or_like_where = array();
    if (!empty($city_id) && empty($like)) {
      if (is_full_array($data['user_group_arr'])) {
        foreach ($data['user_group_arr'] as $key => $value) {
          $or_like_where[] = array(
            'user_group_ids' => ',' . $value['id'] . ','
          );
        }
      }
    }
    $where_in = array('role', $role_arr);

    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    //分页开始
    $data['user_num'] = $this->user_model->getusernum_like($where_arr, $like, $or_like_where, $where_in);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['user'] = $this->user_model->getuser_like($where_arr, $like, $or_like_where, $data['offset'], $data['pagesize'], $where_in);
    $this->load->view('user/index', $data);
  }

  function find_group_by_cityid($city_id)
  {
    if (!empty($city_id)) {
      $city_id = intval($city_id);
      $where_cond = array(
        'city_id' => $city_id
      );
      $user_group = $this->user_group_model->get_user_group($where_cond);
      echo json_encode($user_group);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 用户列表页面(分站管理者创建的用户列表)
   */
  public function data_list_city_manage()
  {
    //当前用户
    $this_user = $_SESSION[WEB_AUTH];
    $city_id = intval($this_user['city_id']);
    $where_cond = array('belong_cityid' => $city_id);
    $role_arr = array(2);
    //超级管理员
    if ($this_user['role'] == 1) {
      $role_arr = array(2, 3, 4);
    }
    $where_in = array('role', $role_arr);

    $data['title'] = $this->config->item('title');
    $data['conf_where'] = 'index';
    //分页开始
    $data['user_num'] = $this->user_model->getusernum($where_cond, $where_in);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    //$data['user'] = $this->user_model->getuser($data['where_cond'],$data['wherein_cond'],$data['offset'],$data['pagesize']);
    $data['user'] = $this->user_model->getuser($where_cond, $data['offset'], $data['pagesize'], $where_in);
    $this->load->view('user/index_city_manage', $data);
  }

  /**
   * 添加用户
   */
  public function add()
  {
    $data['title'] = '添加用户';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    //获取用户组表中的城市id
    $city_id_arr = $this->user_group_model->get_cityid();
    //根据城市id获得对应的用户组,数据重构
    $user_group_data = array();
    foreach ($city_id_arr as $k => $v) {
      $city_data = $this->city_model->get_by_id($v['city_id']);
      $v['city_name'] = $city_data['cityname'];
      $city_id_arr[$k]['name'] = $city_data['cityname'];
      $v['user_group'] = $this->user_group_model->get_user_group(array('city_id' => $v['city_id']));
      $user_group_data[] = $v;
    }
    //城市数据
    $data['city_arr'] = $city_id_arr;
    $data['user_group_data'] = $user_group_data;
    //添加动作
    if ('add' == $submit_flag) {
      $master = trim($this->input->post('master'));
      if ($master == 1) {
        $am_cityid = $this->input->post('am_cityid');
      } else {
        $am_cityid = 0;
      }
      $paramArray = array(
        'username' => trim($this->input->post('username')),
        'password' => trim($this->input->post('password')),
        'truename' => trim($this->input->post('truename')),
        'telno' => trim($this->input->post('telno')),
        'message' => trim($this->input->post('message')),
        'am_cityid' => $am_cityid,
      );
      //用户组
      $user_group_ids = ',';
      foreach ($user_group_data as $k => $v) {
        $user_group_id = $this->input->post('user_group' . $k);
        if (!empty($user_group_id)) {
          $user_group_ids .= $user_group_id . ',';
        }
      }
      $paramArray['user_group_ids'] = ($user_group_ids == ',') ? '' : $user_group_ids;
      if (!empty($paramArray['username']) && !empty($paramArray['password'])) {
        $is_exisit_user = $this->user_model->getuserByname($paramArray['username']);
        if ($is_exisit_user) {
          $data['mess_error'] = '该用户名已存在';
        } else {
          $paramArray['password'] = md5($paramArray['password']);
          $addResult = $this->user_model->adduser($paramArray);
        }
      } else {
        $data['mess_error'] = '用户名/密码不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('user/add', $data);
  }

  /**
   * 添加用户(分站管理者创建的用户列表)
   */
  public function add_city_manage()
  {
    //当前用户
    $this_user = $_SESSION[WEB_AUTH];
    $city_id = intval($this_user['city_id']);

    $data['title'] = '添加用户';
    $data['conf_where'] = 'index';
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');

    $user_group_data = array();
    $city_data = $this->city_model->get_by_id($city_id);

    $user_group_data['city_name'] = $city_data['cityname'];
    $user_group_data['user_group'] = $this->user_group_model->get_user_group(array('city_id' => $city_id));
    $user_group_2 = array();
    if (is_full_array($user_group_data['user_group'])) {
      foreach ($user_group_data['user_group'] as $key => $value) {
        if ($value['name'] != '总部运营' && $value['name'] != '功能测试') {
          $user_group_2[] = $value;
        }
      }
    }
    $user_group_data['user_group'] = $user_group_2;

    $city_id_arr = array(
      'city_id' => $city_id,
      'name' => $city_data['cityname']
    );

    //城市数据
    $data['city_arr'] = $city_id_arr;
    $data['user_group_data'] = $user_group_data;
    //添加动作
    if ('add' == $submit_flag) {
      $master = trim($this->input->post('master'));
      if ($master == 1) {
        $am_cityid = $this->input->post('am_cityid');
      } else {
        $am_cityid = 0;
      }
      $paramArray = array(
        'username' => trim($this->input->post('username')),
        'password' => trim($this->input->post('password')),
        'truename' => trim($this->input->post('truename')),
        'telno' => trim($this->input->post('telno')),
        'message' => trim($this->input->post('message')),
        'am_cityid' => $am_cityid,
        'belong_cityid' => $city_id
      );
      //用户组
      $user_group_id = $this->input->post('user_group' . $k);
      if (!empty($user_group_id) && intval($user_group_id) > 0) {
        $user_group_id = ',' . $user_group_id . ',';
      } else {
        $user_group_id = '';
      }
      $paramArray['user_group_ids'] = $user_group_id;
      if (!empty($paramArray['username']) && !empty($paramArray['password'])) {
        $is_exisit_user = $this->user_model->getuserByname($paramArray['username']);
        if ($is_exisit_user) {
          $data['mess_error'] = '该用户名已存在';
        } else {
          $paramArray['password'] = md5($paramArray['password']);
          $addResult = $this->user_model->adduser($paramArray);
        }
      } else {
        $data['mess_error'] = '用户名/密码不能为空';
      }
    }
    $data['addResult'] = $addResult;
    $this->load->view('user/add_city_manage', $data);
  }

  /**
   * 修改密码
   */
  public function change_pwd()
  {
    $modifyResult = '';
    $data['title'] = '修改密码';
    $submit_flag = $this->input->post('submit_flag');
    if ('modify' == $submit_flag) {
      $uid = $_SESSION[WEB_AUTH]["uid"];
      $password = md5(trim($this->input->post('password')));
      $password1 = trim($this->input->post('password1'));
      $password2 = trim($this->input->post('password2'));
      if ($password != $_SESSION[WEB_AUTH]["password"]) {
        $modifyResult = '当前密码输入错误';
      } else if ($password1 == '') {
        $modifyResult = '新密码不能为空';
      } else if ($password1 != '' && $password2 == $password1) {
        $paramArray['password'] = md5($password1);
        $modifyResult = $this->user_model->modifyuser($uid, $paramArray);
      } else {
        $modifyResult = '两次密码不一致';
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('user/change_pwd', $data);
  }

  /**
   * 修改用户
   */
  public function modify($uid)
  {
    $data['title'] = '修改用户';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    //获取用户组表中的城市id
    $city_id_arr = $this->user_group_model->get_cityid();
    //根据城市id获得对应的用户组,数据重构
    $user_group_data = array();
    foreach ($city_id_arr as $k => $v) {
      $city_data = $this->city_model->get_by_id($v['city_id']);
      $v['city_name'] = $city_data['cityname'];
      $city_id_arr[$k]['name'] = $city_data['cityname'];
      $v['user_group'] = $this->user_group_model->get_user_group(array('city_id' => $v['city_id']));
      $user_group_data[] = $v;
    }
    $data['city_arr'] = $city_id_arr;
    $data['user_group_data'] = $user_group_data;
    if (!empty($uid)) {
      $userData = $this->user_model->getuserByid($uid);
      if (!empty($userData[0]) && is_array($userData[0])) {
        $data['user'] = $userData[0];
      }
    }
    if ('modify' == $submit_flag) {
      $master = trim($this->input->post('master'));
      if ($master == 1) {
        $am_cityid = $this->input->post('am_cityid');
      } else {
        $am_cityid = 0;
      }
      $paramArray = array(
        'username' => trim($this->input->post('username')),
        'truename' => trim($this->input->post('truename')),
        'telno' => trim($this->input->post('telno')),
        'message' => trim($this->input->post('message')),
        'am_cityid' => $am_cityid,
      );
      if (trim($this->input->post('password'))) {
        $paramArray['password'] = md5(trim($this->input->post('password')));
      }
      //用户组
      $user_group_ids = ',';
      foreach ($user_group_data as $k => $v) {
        $user_group_id = $this->input->post('user_group' . $k);
        if (!empty($user_group_id)) {
          $user_group_ids .= $user_group_id . ',';
        }
      }
      $paramArray['user_group_ids'] = ($user_group_ids == ',') ? '' : $user_group_ids;

      $user_role = $userData[0]['role'];
      $user_group_arr = explode(',', trim($paramArray['user_group_ids'], ','));
      //判断该帐号是否是分站管理员，如果是，不能选择多个城市的角色。
      if ('4' == $user_role && count($user_group_arr) > 1) {
        $data['mess_error'] = '该帐号是分站管理员，不能管理多个分站。';
      } else {
        if (!empty($paramArray['username'])) {
          $modifyResult = $this->user_model->modifyuser($uid, $paramArray);
        } else {
          $data['mess_error'] = '用户名/密码不能为空';
        }
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('user/modify', $data);
  }

  /**
   * 修改用户(分站管理者创建的用户列表)
   */
  public function modify_city_manage($uid)
  {
    //当前用户
    $this_user = $_SESSION[WEB_AUTH];
    $city_id = intval($this_user['city_id']);

    $data['title'] = '修改用户';
    $data['conf_where'] = 'index';
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');

    $user_group_data = array();

    $city_data = $this->city_model->get_by_id($city_id);
    $user_group_data['city_name'] = $city_data['cityname'];
    $user_group_data['user_group'] = $this->user_group_model->get_user_group(array('city_id' => $city_id));
    $user_group_2 = array();
    if (is_full_array($user_group_data['user_group'])) {
      foreach ($user_group_data['user_group'] as $key => $value) {
        if ($value['name'] != '总部运营' && $value['name'] != '功能测试') {
          $user_group_2[] = $value;
        }
      }
    }
    $user_group_data['user_group'] = $user_group_2;

    $city_id_arr = array(
      'city_id' => $city_id,
      'name' => $city_data['cityname']
    );

    $data['city_arr'] = $city_id_arr;
    $data['user_group_data'] = $user_group_data;

    if (!empty($uid)) {
      $userData = $this->user_model->getuserByid($uid);
      if (!empty($userData[0]) && is_array($userData[0])) {
        $data['user'] = $userData[0];
      }
    }
    if ('modify' == $submit_flag) {
      $master = trim($this->input->post('master'));
      if ($master == 1) {
        $am_cityid = $this->input->post('am_cityid');
      } else {
        $am_cityid = 0;
      }
      $paramArray = array(
        'username' => trim($this->input->post('username')),
        'truename' => trim($this->input->post('truename')),
        'telno' => trim($this->input->post('telno')),
        'message' => trim($this->input->post('message')),
        'am_cityid' => $am_cityid,
        'belong_cityid' => $city_id
      );
      if (trim($this->input->post('password'))) {
        $paramArray['password'] = md5(trim($this->input->post('password')));
      }
      //用户组
      $user_group_id = $this->input->post('user_group' . $k);
      if (!empty($user_group_id) && intval($user_group_id) > 0) {
        $user_group_id = ',' . $user_group_id . ',';
      } else {
        $user_group_id = '';
      }
      $paramArray['user_group_ids'] = $user_group_id;

      $user_role = $userData[0]['role'];
      $user_group_arr = explode(',', trim($paramArray['user_group_ids'], ','));
      //判断该帐号是否是分站管理员，如果是，不能选择多个城市的角色。
      if ('4' == $user_role && count($user_group_arr) > 1) {
        $data['mess_error'] = '该帐号是分站管理员，不能管理多个分站。';
      } else {
        if (!empty($paramArray['username'])) {
          $modifyResult = $this->user_model->modifyuser($uid, $paramArray);
        } else {
          $data['mess_error'] = '用户名/密码不能为空';
        }
      }
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('user/modify_city_manage', $data);
  }

  /**
   * 删除用户
   */
  public function del($uid)
  {
    $data['title'] = '删除用户';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($uid)) {
      $userData = $uid != $_SESSION[WEB_AUTH]["uid"] ? $this->user_model->deluser($uid) : 2;
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else if ($userData == 2) {
        $delResult = 2;//删除失败
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('user/del', $data);
  }

  /**
   * 设置失效
   */
  public function set_status($uid, $type)
  {
    $data['title'] = '删除用户';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($uid) && !empty($type)) {
      if ('1' == $type) {
        $paramArray = array(
          'status' => '0'
        );
      } else {
        $paramArray = array(
          'status' => '1'
        );
      }
      $userData = $uid != $_SESSION[WEB_AUTH]["uid"] ? $this->user_model->modifyuser($uid, $paramArray) : 2;
      if ($userData == 1) {
        $delResult = 1;//删除成功
      } else if ($userData == 2) {
        $delResult = 2;//删除失败
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('user/set_status', $data);
  }

  /**
   * 城市切换
   */
  public function change_city($_city)
  {
    if ($_city == 'admin') {
      $_SESSION[WEB_AUTH]["is_admin"] = 1;
    } else {
      $_SESSION[WEB_AUTH]["is_admin"] = 0;
      if (!empty($_city)) {
        $_SESSION[WEB_AUTH]["city"] = $_city;
        $this->load->model('city_model');
        $city_id_data = $this->city_model->get_city_by_spell($_city);
        $_SESSION[WEB_AUTH]["city_id"] = intval($city_id_data['id']);
      }
    }
  }

  /**
   * ajax获取当前用户所属用户组拥有的权限菜单节点
   */
  public function ajax_get_purview_node()
  {
    if (!empty($_SESSION[WEB_AUTH])) {
      $uid = $_SESSION[WEB_AUTH]['uid'];
      $city_spell = $_SESSION[WEB_AUTH]['city'];
      $city_id_data = $this->city_model->get_city_by_spell($city_spell);
      $city_id = $city_id_data['id'];
      $user_data = $this->user_model->getuserByid($uid);
      //当前用户所属的用户组id
      $city_ids_arr = array();
      $city_ids_arr_new = array();
      $user_group_id_str = $user_data[0]['user_group_ids'];
      if (!empty($user_group_id_str)) {
        $user_group_id_arr = array_filter(explode(',', $user_group_id_str));
        //根据id获得当前城市的用户组信息
        $user_group_arr = array();
        foreach ($user_group_id_arr as $k => $vo) {
          $city_ids = $this->user_group_model->get_city_id_by("id = " . $vo);
          if (!empty($city_ids)) {
            $city_ids_arr[] = $city_ids['0']['city_id'];
          }
        }
        foreach ($city_ids_arr as $k => $vo) {
          if ($this->city_model->get_by_id($vo)) {
            $city_ids_arr_new[] = $vo;
          }
        }
        foreach ($user_group_id_arr as $k => $v) {
          $user_group_data = $this->user_group_model->get_user_group_by_id($v);
          //if($user_group_data[0]['city_id']==$city_ids_arr_new['0']){
          if (in_array($city_id, $city_ids_arr_new)) {
            if ($user_group_data[0]['city_id'] == $city_id) {
              $user_group_arr[] = $user_group_data[0];
            }
          } else {
            if ($user_group_data[0]['city_id'] == $city_ids_arr_new['0']) {
              $user_group_arr[] = $user_group_data[0];
            }
          }
        }
        //获得用户组中的权限节点
        $purview_node_arr = array();
        foreach ($user_group_arr as $k => $v) {
          if (!empty($v['purview_nodes'])) {
            $purview_node_arr[] = array_filter(explode(',', $v['purview_nodes']));
          }
        }
        $new_purview_node_arr = array();
        foreach ($purview_node_arr as $k => $v) {
          foreach ($v as $key => $val) {
            $new_purview_node_arr[] = $val;
          }
        }
        if (!empty($new_purview_node_arr)) {
          //除去重复值
          $last_purview_node_ids = array_unique($new_purview_node_arr);
          //获得权限节点数据
          $purview_node_data = array();
          foreach ($last_purview_node_ids as $k => $v) {
            $v_data = $this->purview_node_model->get_node_by_id($v);
            if (!empty($v_data)) {
              $purview_node_data[] = $v_data[0];
            }
          }
          //根据父节点分组数据重构
          $pid_arr = array();
          foreach ($purview_node_data as $k => $v) {
            if (!in_array($v['p_id'], $pid_arr)) {
              $pid_arr[] = $v['p_id'];
            }
          }
          $new_purview_node_data = array();
          $i = 0;
          foreach ($pid_arr as $key => $val) {
            foreach ($purview_node_data as $k => $v) {
              if ($v['p_id'] == $val) {
                $children_purview_node[] = $v;
              }
            }
            $father_node_data = $this->purview_father_node_model->get_node_by_id($val);
            $new_purview_node_data[$i]['p_name'] = $father_node_data[0]['name'];
            $new_purview_node_data[$i]['purview_node_data'] = $children_purview_node;
            $children_purview_node = array();
            $i++;
          }
          echo json_encode($new_purview_node_data);
          exit;
        } else {
          echo json_encode(array('result' => 'failed'));
          exit;
        }
      } else {
        echo json_encode(array('result' => 'failed'));
        exit;
      }
    } else {
      echo json_encode(array('result' => 'failed'));
      exit;
    }
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
