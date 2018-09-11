<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cmtcorrection extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('user_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('cmt_correction_model');//楼盘纠错模型类
    $this->load->model('district_model');//区属模型类
    $this->load->library('form_validation');//表单验证
  }

  /**
   * 楼盘纠错信息列表页面
   */
  public function index()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    $esta_arr = array(
      0 => '未审核',
      1 => '通过',
      2 => '不通过',
    );
    $data['district'] = $this->district_model->get_district();
    //筛选条件
    $data['where_cond'] = array();
    $district_id = $this->input->post('district');
    $street_id = $this->input->post('street');
    $esta = $this->input->post('esta');
    $creattime = $this->input->post('creattime');
    //楼盘状态
    if (is_string($esta) && $esta != '') {
      $data['where_cond']['esta'] = intval($esta);
    }
    //提交时间
    if (!empty($creattime)) {
      $data['where_cond']['creattime'] = $creattime;
    }
    $where_str = 'id != 0 ';
    if (is_array($data['where_cond']) && !empty($data['where_cond'])) {
      foreach ($data['where_cond'] as $k => $v) {
        if ($k != 'creattime') {
          $where_str .= 'and ' . $k . ' = "' . $v . '"';
        }
      }
    }
    if (!empty($data['where_cond']['creattime'])) {
      if ('1' == $data['where_cond']['creattime']) {
        $_time = time() - 24 * 60 * 60;
        $where_str .= 'and creattime > "' . $_time . '"';
      } else if ('3' == $data['where_cond']['creattime']) {
        $_time = time() - 3 * 24 * 60 * 60;
        $where_str .= 'and creattime > "' . $_time . '"';
      } else if ('7' == $data['where_cond']['creattime']) {
        $_time = time() - 7 * 24 * 60 * 60;
        $where_str .= 'and creattime > "' . $_time . '"';
      }
    }
    //楼盘名称、类型模糊查询
    $data['like_code'] = array();
    $condition = $this->input->post('condition');
    $data['condition'] = $condition;
    $strcode = $this->input->post('strcode');
    if (!empty($condition) && !empty($strcode)) {
      $data["like_code"][$condition] = $strcode;
    }
    $build_type = $this->input->post('build_type');
    if (!empty($build_type)) {
      $data['like_code']['build_type'] = trim($build_type);
    }
    //分页开始
    $data['corr_num'] = $this->cmt_correction_model->get_cmt_correction_num($where_str, $data['like_code']);
    $data['pagesize'] = 10; //设定每一页显示的记录数
    $data['pages'] = $data['corr_num'] ? ceil($data['corr_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['cmt_correction'] = $this->cmt_correction_model->get_cmt_correction($where_str, $data['like_code'], $data['offset'], $data['pagesize']);
    //楼盘数据重构
    foreach ($data['cmt_correction'] as $k => $v) {
      $comm_data = $this->community_model->get_comm_by_id($v['cmt_id']);
      if (isset($comm_data[0]) && !empty($comm_data[0])) {
        $v['alias'] = $comm_data[0]['alias'];
        $a = $v['correction_feild'];
        $v['org_info'] = $comm_data[0][$a];
        $v['esta_str'] = $esta_arr[$v['esta']];
        if ($v['correction_feild'] == 'green_rate') {
          $v['org_info'] = strval($v['org_info'] * 100) . '%';
          $v['correctioninfo'] = strval($v['correctioninfo'] * 100) . '%';
        }
      }
      $data['cmt_correction2'][] = $v;
    }
    $this->load->view('cmtcorrection/index', $data);
  }


  /**
   * 页面ajax请求处理信息
   */
  public function submit_action()
  {
    $return_data = '';
    $id = $this->input->get('id');
    $check_esta = $this->input->get('check_esta');
    if (!empty($id)) {
      if ('1' === $check_esta) {
        $correction_data = $this->cmt_correction_model->get_cmt_correction(array('id' => $id));
        if (!empty($correction_data)) {
          $correction_feild = $correction_data[0]['correction_feild'];//纠错字段
          $correctioninfo = $correction_data[0]['correctioninfo'];//纠错新内容
          //1.纠错内容更新到对应楼盘表中的数据
          $update_cmt_data = array(
            $correction_feild => $correctioninfo,
          );
          $update_cmt_result = $this->community_model->modifycommunity($correction_data[0]['cmt_id'], $update_cmt_data);
          //2.更新该条纠错信息（信息状态、更新时间、用户名）
          $update_corr_data = array(
            'esta' => 1,
            'updatetime' => time(),
            'modify_user' => $_SESSION[WEB_AUTH]['username'],
          );
          $update_corr_result = $this->cmt_correction_model->modify_cmt_correction($id, $update_corr_data);
          if ($update_cmt_result === 1 && $update_corr_result === 1) {
            $return_data = 'submitSuccess';
          } else {
            $return_data = 'submitFailed';
          }
        } else {
          $return_data = 'submitFailed';
        }
      } else if ('2' === $check_esta) {//审核不通过
        //修改纠错信息状态为‘不通过’
        $modify_result = $this->cmt_correction_model->modify_cmt_correction($id, array('esta' => 2));
        if ($modify_result === 1) {
          $return_data = 'submitSuccess';
        } else {
          $return_data = 'submitFailed';
        }
      }
    }
    echo $return_data;
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


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
