<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cmtimage extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('district_model');//区属模型类
    $this->load->library('form_validation');//表单验证
  }

  /**
   * 楼盘图片列表页面
   */
  public function index()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    $data['district'] = $this->district_model->get_district();
    //筛选条件
    $data['where_cond'] = array();
    $district_id = $this->input->post('district');
    $street_id = $this->input->post('street');
    if ($district_id) {
      $data['where_cond']['dist_id'] = intval($district_id);
      $street_arr = $this->find_street_bydis_arr($district_id);
      $data['street_arr'] = $street_arr;
    }
    if ($street_id) {
      $data['where_cond']['streetid'] = intval($street_id);
    }
    //楼盘名称、类型模糊查询
    $data['like_code'] = array();
    $condition = $this->input->post('condition');
    $strcode = $this->input->post('strcode');
    if (!empty($condition) && !empty($strcode)) {
      $data["like_code"][$condition] = $strcode;
    }
    $build_type = $this->input->post('build_type');
    if (!empty($build_type)) {
      $data['like_code']['build_type'] = trim($build_type);
    }
    //分页开始
    $data['user_num'] = $this->community_model->getcommunitynum($data['where_cond'], $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['user_num'] ? ceil($data['user_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $data['community'] = $this->community_model->getcommunity($data['where_cond'], $data['like_code'], $data['offset'], $data['pagesize']);
    //楼盘数据重构
    foreach ($data['community'] as $k => $v) {
      $v['dist_name'] = $this->district_model->get_distname_by_id($v['dist_id']);
      $v['street_name'] = $this->district_model->get_streetname_by_id($v['streetid']);
      $v['image_data'] = $this->community_model->get_cmt_image_by_cmtid($v['id']);
      $data['community2'][] = $v;
    }
    $this->load->view('cmtimage/index', $data);
  }


  /**
   * 页面ajax请求根据属区获得对应板块
   */
  public function find_street_bydis($districtID)
  {
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      echo json_encode($street);
    } else {
      echo json_encode(array('result' => 'no result'));
    }
  }

  /**
   * 根据属区获得对应板块
   */
  public function find_street_bydis_arr($districtID)
  {
    if (!empty($districtID)) {
      $districtID = intval($districtID);
      $street = $this->district_model->get_street_bydist($districtID);
      return $street;
    } else {
      return FALSE;
    }
  }


  /**
   * 楼盘图库展示
   */
  public function cmt_pic_manage_list($commid)
  {
    $data['title'] = '楼盘图库';
    $data['conf_where'] = 'index';
    //图片类型
    $pic_type_arr = array(
      1 => '户型图',
      2 => '小区正门',
      3 => '外景图',
      4 => '小区环境',
      5 => '内部设施',
      6 => '周边配套',
      7 => '未分类'
    );
    $data['pic_type_arr'] = $pic_type_arr;
    //筛选条件
    $where = array();
    $where['cmt_id'] = $commid;
    $pic_type = $this->input->post('pic_type');
    if (!empty($pic_type)) {
      $where['pic_type'] = $pic_type;
    }
    $data['pic_type'] = !empty($where['pic_type']) ? $where['pic_type'] : '';
    //分页开始
    $data['cmtimg_num'] = $this->community_model->get_cmt_img_num($where);
    $data['pagesize'] = 15; //设定每一页显示的记录数
    $data['pages'] = $data['cmtimg_num'] ? ceil($data['cmtimg_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量

    $all_image_data = $this->community_model->get_all_cmt_image_by_cmtid($where, $data['offset'], $data['pagesize']);
    $all_image_data2 = array();
    //图片url换成大图地址
    foreach ($all_image_data as $k => $v) {
      $img_url_str = '';
      if (!empty($v['image'])) {
//                    $img_url_arr = explode('/thumb', $v['image']);
        $img_url_str = changepic($v['image']);
        $v['image'] = $img_url_str;
      }
      $all_image_data2[] = $v;
    }
    $img_num = count($all_image_data);//图片总数
    $rows = ceil($img_num / 5);//行数
    $data['all_image_data'] = $all_image_data2;
    $data['img_num'] = $img_num;
    $data['rows'] = $rows;
    $data['commid'] = $commid;
    $this->load->view('cmtimage/pic_manage', $data);
  }

  /**
   * 楼盘图库操作
   * ajax请求
   */
  public function cmt_pic_manage_action($commid)
  {
    $data['title'] = '楼盘图库';
    $data['conf_where'] = 'index';
    $action_method = $this->input->get('actiontype');
    $img_arr = $this->input->get('imgarr');
    $img_src = $this->input->get('imgsrc');
    $pic_type = $this->input->get('pic_type');
    if ('multdel' == $action_method) {
      foreach ($img_arr as $k => $v) {
        $commData = $this->community_model->del_cmt_img($v);
      }
      if ($commData == 1) {
        $result = 'delSuccess'; //删除成功
      } else {
        $result = 'delFail'; //删除失败
      }
    } else if ('setface' == $action_method) {
      $commData = $this->community_model->set_cmt_img_surface($img_arr[0], $commid, $img_src[0]);
      if ($commData == 1) {
        $result = 'surfaceSuccess';//设置封面成功
      } else {
        $result = 'surfaceFail';//设置封面失败
      }
    } else if ('update_type' == $action_method) {
      foreach ($img_arr as $k => $v) {
        $commData = $this->community_model->modify_cmt_image($v, array('pic_type' => intval($pic_type)));
      }
      if ($commData == 1) {
        $result = 'update_type_success';//修改类型成功
      } else {
        $result = 'update_type_fail';//修改类型失败
      }
    }
    echo $result;
  }

  /**
   * 添加图片
   */
  public function add_cmt_img($commid)
  {
    $data['title'] = '添加楼盘图片';
    $data['conf_where'] = 'index';
    if (isset($commid) && !empty($commid)) {
      $comm_data = $this->community_model->get_comm_by_id($commid);
    }
    if (!empty($comm_data[0]['cmt_name'])) {
      $data['cmt_name'] = $comm_data[0]['cmt_name'];
    }
    //图片类型
    $pic_type_arr = array(
      1 => '户型图',
      2 => '小区正门',
      3 => '外景图',
      4 => '小区环境',
      5 => '内部设施',
      6 => '周边配套',
    );
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->form_validation->set_rules('pic_type', 'Pic Type', 'required');
    $this->form_validation->set_rules('p_filename1[]', 'File Name', 'required');
    $addResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('add' == $submit_flag) {
      $img_arr = $this->input->post('p_filename1');
      $paramArray = array(
        'cmt_id' => $commid,
        'pic_type' => intval($this->input->post('pic_type')),
        'room' => trim($this->input->post('room')),
        'hall' => trim($this->input->post('hall')),
      );
      $img_arr_param = array();
      if ($this->form_validation->run() === true) {
        //楼盘图片数据重构
        foreach ($img_arr as $k => $v) {
          $img_arr_param[] = array(
            'cmt_id' => $paramArray['cmt_id'],
            'pic_type' => $paramArray['pic_type'],
            'room' => $paramArray['room'],
            'hall' => $paramArray['hall'],
            'creattime' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'image' => $v
          );
        }
        if ($paramArray['pic_type'] == 1 && ($paramArray['room'] == '' || $paramArray['hall'] == '')) {
          $data['mess_error'] = '图片类型为户型图，必须填写房型';
        } else {
          foreach ($img_arr_param as $k => $v) {
            $addResult = $this->community_model->add_cmt_image($v);
          }
        }
      } else {
        $data['mess_error'] = '带 * 为必填字段';
      }
    }
    $data['addResult'] = $addResult;
    $data['pic_type_arr'] = $pic_type_arr;
    $this->load->view('cmtimage/add', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
