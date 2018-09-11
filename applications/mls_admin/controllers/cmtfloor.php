<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cmtfloor extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('common_load_source_helper');
    $this->load->helper('page_helper');
    $this->load->helper('community_helper');
    $this->load->model('community_model');//楼盘模型类
    $this->load->model('floor_model');//楼栋模型类
    $this->load->model('room_model');//房间模型类
    $this->load->library('form_validation');//表单验证
  }

  /**
   * 添加楼栋号
   *
   * @access public
   * @param  $cmt_id
   */
  public function add_floor($cmt_id = 0)
  {
    $data['title'] = '添加楼栋号';
    $data['mess_error'] = '';
    $data['cmt_id'] = intval($cmt_id);
    $room_add_result = '';
    $this->form_validation->set_rules('num', 'Num', 'required');
    $this->form_validation->set_rules('unit_start', 'Unit Start', 'required');
    $this->form_validation->set_rules('unit_end', 'Unit End', 'required');
    $this->form_validation->set_rules('level_start', 'Level Start', 'required');
    $this->form_validation->set_rules('level_end', 'Level End', 'required');
    $this->form_validation->set_rules('room_start', 'Room Start', 'required');
    $this->form_validation->set_rules('room_end', 'room End', 'required');

    $paramArray = array(
      'cmt_id' => intval($cmt_id),
      'num' => trim($this->input->post('num')),//楼栋编号
      'baidu_x' => floatval($this->input->post('baidu_x')),//百度坐标X
      'baidu_y' => floatval($this->input->post('baidu_y')),//百度坐标Y
      'unit_start' => intval($this->input->post('unit_start')),//起始单元
      'unit_end' => intval($this->input->post('unit_end')),//终止单元
      'unit_miss' => intval($this->input->post('unit_miss')),//没有的单元
      'level_start' => intval($this->input->post('level_start')),//起始楼层
      'level_end' => intval($this->input->post('level_end')),//终止楼层
      'level_miss' => intval($this->input->post('level_miss')),//没有的楼层
      'room_start' => trim($this->input->post('room_start')),//起始房间
      'room_end' => trim($this->input->post('room_end')),//终止房间
      'room_miss' => trim($this->input->post('room_miss')),//没有的房间
      'room_status' => intval($this->input->post('room_status')),//房间号状态（延续or重复）
    );
    //楼栋编号验证，判断当前楼盘是否已经存在该楼栋
    $where_cond = array(
      'cmt_id' => $cmt_id,
      'num' => $paramArray['num']
    );
    $floor_array = $this->floor_model->get_floor($where_cond);
    if ($this->form_validation->run() === true && empty($floor_array)) {
      //1）楼栋号数据添加
      $add_floor_id = $this->floor_model->add_floor($paramArray);
      if (is_int($add_floor_id) && !empty($add_floor_id)) {
        //2）根据相关属性，生成房间号规则，入库房间表
        $floor_data = $this->floor_model->get_floor_by_id($add_floor_id);
        $unit_start = $floor_data['unit_start'];//起始单元
        $unit_end = $floor_data['unit_end'];//终止单元
        $level_start_int = intval($floor_data['level_start']);//起始楼层
        $level_end_int = intval($floor_data['level_end']);//起始单元
        $room_start_int = intval($floor_data['room_start']);//每层起始房间
        $room_end_int = intval($floor_data['room_end']);//每层终止房间
        //房间号延续
        if ($floor_data['room_status'] == 1) {
          $room_param_array = array(
            'floor_id' => $add_floor_id,
            'floor_num' => $floor_data['num'],
            'cmt_id' => intval($cmt_id)
          );
          //起止房间号为纯数字
          if ($room_start_int > 0 && $room_end_int > 0) {
            for ($i = $level_start_int; $i <= $level_end_int; $i++) {
              for ($j = $room_start_int; $j <= $room_end_int; $j++) {
                $j_str = ($j < 10) ? '0' . strval($j) : strval($j);
                $i_str = ($i < 10) ? '0' . strval($i) : strval($i);
                //楼层
                $room_param_array['level'] = $i;
                //房间号
                $room_num = strval($i_str) . strval($j_str);
                $room_param_array['room_num'] = $room_num;
                $room_add_result = $this->room_model->add_room($room_param_array);
              }
            }
          } else {
            for ($i = $level_start_int; $i <= $level_end_int; $i++) {
              for ($j = $floor_data['room_start']; $j <= $floor_data['room_end']; $j++) {
                $i_str = ($i < 10) ? '0' . strval($i) : strval($i);
                //楼层
                $room_param_array['level'] = $i;
                //房间号
                $room_num = strval($i_str) . $j;
                $room_param_array['room_num'] = $room_num;
                $room_add_result = $this->room_model->add_room($room_param_array);
              }
            }
          }
          //房间号重复
        } else if ($floor_data['room_status'] == 2) {
          $room_param_array = array(
            'floor_id' => $add_floor_id,
            'floor_num' => $floor_data['num'],
            'cmt_id' => intval($cmt_id)
          );
          //起止房间号为纯数字
          if ($room_start_int > 0 && $room_end_int > 0) {
            for ($i = $level_start_int; $i <= $level_end_int; $i++) {
              for ($j = $room_start_int; $j <= $room_end_int; $j++) {
                $j_str = ($j < 10) ? '0' . strval($j) : strval($j);
                $i_str = ($i < 10) ? '0' . strval($i) : strval($i);
                $room_num = strval($i_str) . strval($j_str);
                for ($k = $unit_start; $k <= $unit_end; $k++) {
                  //楼层
                  $room_param_array['level'] = $i;
                  //房间号
                  $room_num_total = $k . '-' . $room_num;
                  $room_param_array['room_num'] = $room_num_total;
                  $room_add_result = $this->room_model->add_room($room_param_array);
                }
              }
            }
          } else {
            for ($i = $level_start_int; $i <= $level_end_int; $i++) {
              for ($j = $floor_data['room_start']; $j <= $floor_data['room_end']; $j++) {
                $i_str = ($i < 10) ? '0' . strval($i) : strval($i);
                $room_num = strval($i_str) . $j;
                for ($k = $unit_start; $k <= $unit_end; $k++) {
                  //楼层
                  $room_param_array['level'] = $i;
                  //房间号
                  $room_num_total = $k . '-' . $room_num;
                  $room_param_array['room_num'] = $room_num_total;
                  $room_add_result = $this->room_model->add_room($room_param_array);
                }
              }
            }
          }
        }
      } else {
        $data['mess_error'] = '楼栋添加失败';
      }
    } else if ($this->form_validation->run() === false) {
      $data['mess_error'] = '带 * 为必填字段';
    } else if (!empty($floor_array)) {
      $data['mess_error'] = '该楼栋号已存在';
    }
    $data['room_add_result'] = $room_add_result;
    $this->load->view('cmtfloor/add', $data);
  }

  /**
   * 楼栋号详情页
   */
  public function floor_detail($cmt_id)
  {
    $data['title'] = '楼栋号详情';
    //根据楼盘号，获得相应楼栋和房间数据
    $cmt_id = intval($cmt_id);
    $data['cmt_id'] = $cmt_id;
    $cmt_data = $this->community_model->get_comm_by_id($cmt_id);
    $data['cmt_name'] = $cmt_data[0]['cmt_name'];
    $floor_data = $this->floor_model->get_floor(array('cmt_id' => $cmt_id));
    if (is_array($floor_data) && !empty($floor_data)) {
      $cmt_room_arr = array();
      $floor_num_str = '';
      foreach ($floor_data as $k => $v) {
        $floor_num_str .= $v['num'] . ',';
        //当前楼号
        $floor_num = $v['num'];
        //当前楼栋起止层数
        $level_start = $v['level_start'];
        $level_end = $v['level_end'];
        for ($i = $level_start; $i <= $level_end; $i++) {
          $level_room_arr = array();
          $where_cond = array(
            'cmt_id' => $cmt_id,
            'floor_num' => $floor_num,
            'level' => $i
          );
          $room_data = $this->room_model->get_room($where_cond);
          if (is_array($room_data) && !empty($room_data)) {
            foreach ($room_data as $key => $value) {
              $level_room_arr['level'] = $value['level'];
              $level_room_arr['room'][] = $value;
            }
          }
          $cmt_room_arr[$floor_num]['id'] = $v['id'];
          $cmt_room_arr[$floor_num]['data'][] = $level_room_arr;
        }
      }
      $data['floor_num_str'] = $floor_num_str;
      $data['cmt_room_arr'] = $cmt_room_arr;
    }
    $this->load->view('cmtfloor/detail', $data);
  }

  /**
   * 房间号详情页
   */
  function room_detail($room_id)
  {
    $data['title'] = '修改楼盘';
    $room_data = $this->room_model->get_room_by_id($room_id);
    $data['room_data'] = $room_data;
    $modifyResult = '';
    $submit_flag = $this->input->post('submit_flag');
    if ('modify' == $submit_flag) {
      $paramArray = array(
        'room_num' => $this->input->post('room_num'),
        'room_count' => intval($this->input->post('room_count')),
        'hall_count' => intval($this->input->post('hall_count')),
        'area' => $this->input->post('area'),
        'build_type' => $this->input->post('build_type'),
      );
      $modifyResult = $this->room_model->modify_room($room_id, $paramArray);
    }
    $data['modifyResult'] = $modifyResult;
    $this->load->view('cmtfloor/room_detail', $data);
  }

  /**
   * 删除楼栋号
   */
  function del_floor($floor_id)
  {
    $data['title'] = '删除楼栋';
    $floor_id = intval($floor_id);
    $del_result = '';
    if (!empty($floor_id) && is_int($floor_id)) {
      //1.删除楼栋号
      $del_floor_result = $this->floor_model->del_floor($floor_id);
      if ($del_floor_result === 1) {
        //2.删除房间号
        $del_result = $this->room_model->del_room($floor_id);
      }
    }
    $data['del_result'] = $del_result;
    $this->load->view('cmtfloor/del', $data);
  }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
