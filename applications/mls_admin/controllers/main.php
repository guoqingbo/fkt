<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('user_model');
    $this->load->model('user_group_model');
    $this->load->model('purview_node_model');
    $this->load->model('purview_father_node_model');
    $this->load->model('city_model');
  }

  /**
   * 主页面
   */
  public function index()
  {
    $data = array();
    $this->load->view('main/index', $data);
  }

  /**
   * top
   */
  public function top()
  {
    $data = array();
    $data['this_user'] = $_SESSION[WEB_AUTH];
    $city_spell = $_SESSION[WEB_AUTH]['city'];
    $city_id_data = $this->city_model->get_city_by_spell($city_spell);
    $city_id = $city_id_data['id'];
    //当前城市
    $uid = $_SESSION[WEB_AUTH]['uid'];
    $user_data = $this->user_model->getuserByid($uid);
    $city_ids_arr = array();
    $city_ids_arr_new = array();
    $user_group_id_str = $user_data[0]['user_group_ids'];//echo($user_group_id_str);
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
    }
    //当前城市
    if (in_array($city_id, $city_ids_arr_new)) {
      $data['this_city'] = $this->city_model->get_by_id($city_id);
    } else {
      $data['this_city'] = $this->city_model->get_by_id($city_ids_arr_new['0']);
    }
    //print_r($data['this_city']);
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    $this->load->view('main/top', $data);
  }

  /**
   * left
   */
  public function left()
  {
    $data = array();
    if (!empty($_SESSION[WEB_AUTH])) {
      //当前用户权限
      $role = $_SESSION[WEB_AUTH]['role'];
      $data['role'] = $role;
      $uid = $_SESSION[WEB_AUTH]['uid'];
      $city_spell = $_SESSION[WEB_AUTH]['city'];
      //print_r($city_spell);
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
          //print_r($last_purview_node_ids);
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
            $new_purview_node_data[$i]['class_str'] = $father_node_data[0]['class_str'];
            $new_purview_node_data[$i]['purview_node_data'] = $children_purview_node;
            $children_purview_node = array();
            $i++;
          }
          $data['left_menu'] = $new_purview_node_data;
        } else {
          $data['left_menu'] = array('result' => 'failed');
        }
      } else {
        $data['left_menu'] = array('result' => 'failed');
      }
    } else {
      $data['left_menu'] = array('result' => 'failed');
    }
    //有效城市数据
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');
    $data['city_list'] = $this->city_model->get_all_city();
    foreach ($data['city_list'] as $k => $vo) {
      //echo $vo['id'];echo "<br/>";
      if (in_array($vo['id'], $city_ids_arr_new)) {
        $data['city_list_new'][$k] = $vo;
      }
    }

    //当前城市
    if (in_array($city_id, $city_ids_arr_new)) {
      $data['this_city'] = $this->city_model->get_by_id($city_id);
    } else {
      $data['this_city'] = $this->city_model->get_by_id($city_ids_arr_new['0']);
    }
    $data['software_name'] = $this->config->item('title');

    $this->load->view('main/left', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
