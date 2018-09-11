<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * blacklist_controller CLASS
 *
 * 中介黑名控制器类
 *
 * @package         datacenter
 * @subpackage      controllers
 * @category        controllers
 * @author          angel_in_us
 */
class Blacklist extends My_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('blacklist_model');//中介黑名单模型类
    $this->load->model('message_model');
    $this->load->library('form_validation');//表单验证
    $this->load->helper('user_helper');
  }


  /**
   * 中介黑名单列表页面
   */
  public function index()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';
    if (@$_POST['black'] == 'blacklist') {
      if (!is_numeric(intval($_POST['tel'])) && $_POST['start_time'] == "") {
        echo "<script>alert('请输入查询条件！');history.go(-1);</script>";
      }
    }
    //筛选条件
    $data['where_cond'] = array();
    date_default_timezone_set('PRC');
    if ($this->input->post('start_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/blacklist/index';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('addtime >=' => $start_time, "addtime <=" => $end_time);
      }
    }

    $tel = $this->input->post('tel');
    if ($tel != "" && is_numeric($tel)) {
      $data['where_cond'] = array('tel' => $tel);
    }

    //分页开始
    $data['blacklist_num'] = $this->blacklist_model->get_blacklist_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['blacklist_num'] ? ceil($data['blacklist_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['blacklist'] = $this->blacklist_model->get_blacklist($data['where_cond'], $data['offset'], $data['pagesize']);
    $this->load->view('blacklist/index', $data);
  }

  /**
   * 采集出售房源
   */
  public function sell_collect_list()
  {
    $data['title'] = '采集出售房源';
    $data['conf_where'] = 'index';
    $this->load->model('collections_model_new');

    if (@$_POST['report'] == 'reportlist') {
      if (!is_numeric(intval($_POST['tel'])) && $_POST['start_time'] == "") {
        echo "<script>alert('请输入查询条件！');history.go(-1);</script>";
      }
    }
    //筛选条件
    $data['where_cond'] = array();

    date_default_timezone_set('PRC');
//        if($this->input->post('start_time'))
//        {
//            $start_time = strtotime($this->input->post('start_time')." 00:00");
//            $end_time = strtotime($this->input->post('end_time')." 23:59");
//            if($start_time > $end_time)
//            {
//                echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='".MLS_ADMIN_URL."//blacklist/reportlist';</script>";
//            }
//            if($start_time && $end_time)
//            {
//                $data['where_cond'] = array('r_addtime >='=>$start_time,"r_addtime <="=>$end_time);
//            }
//        }

    //举报人
//        $tel = $this->input->post('tel');
//        if($tel != "" && is_numeric($tel))
//        {
//            $data['where_cond'] = array('r_tel'=>$tel);
//        }
//        //待审核电话
//        $r_person = $this->input->post('r_person');
//        if(isset($r_person) && !empty($r_person))
//        {
//            $data['where_cond'] = array('r_person'=>$r_person);
//        }

    //分页开始
    $data['reportlist_num'] = $this->collections_model_new->get_sell_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['reportlist_num'] ? ceil($data['reportlist_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $sell_list = $this->collections_model_new->get_house_sell($data['where_cond'], array(), array(), array(), array(), $data['offset'], $data['pagesize']);
    $data['reportlist'] = $sell_list;
    $this->load->view('blacklist/sell_collect_list', $data);
  }

  /**
   * 采集出租房源
   */
  public function rent_collect_list()
  {
    echo 'yyy';
  }


  /**
   * 待审核中介列表页面
   */
  public function reportlist()
  {
    $data['title'] = '用户数据中心欢迎你';
    $data['conf_where'] = 'index';

    if (@$_POST['report'] == 'reportlist') {
      if (!is_numeric(intval($_POST['tel'])) && $_POST['start_time'] == "") {
        echo "<script>alert('请输入查询条件！');history.go(-1);</script>";
      }
    }
    //筛选条件
    $data['where_cond'] = array();

    date_default_timezone_set('PRC');
    if ($this->input->post('start_time')) {
      $start_time = strtotime($this->input->post('start_time') . " 00:00");
      $end_time = strtotime($this->input->post('end_time') . " 23:59");
      if ($start_time > $end_time) {
        echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/blacklist/reportlist';</script>";
      }
      if ($start_time && $end_time) {
        $data['where_cond'] = array('r_addtime >=' => $start_time, "r_addtime <=" => $end_time);
      }
    }

    //举报人
    $tel = $this->input->post('tel');
    if ($tel != "" && is_numeric($tel)) {
      $data['where_cond'] = array('r_tel' => $tel);
    }
    //待审核电话
    $r_person = $this->input->post('r_person');
    if (isset($r_person) && !empty($r_person)) {
      $data['where_cond'] = array('r_person' => $r_person);
    }

    $r_status = $this->input->post('r_status');
    if ($r_status) {
      $data['where_cond'] = array('r_status' => $r_status);
    }

    $data['r_status_arr'] = array(
      1 => '驳回',
      2 => '已加入黑名单',
      3 => '待审核',
      4 => '已下架'
    );

    //分页开始
    $data['reportlist_num'] = $this->blacklist_model->get_reportlist_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['reportlist_num'] ? ceil($data['reportlist_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $reportlist = $this->blacklist_model->get_reportlist($data['where_cond'], $data['offset'], $data['pagesize']);
    $reportlist_2 = array();
    if (is_full_array($reportlist)) {
      foreach ($reportlist as $key => $value) {
        $house_info = array();
        $house_id = $value['house_id'];
        $house_str = '暂无信息';
        $oldurl = '暂无信息';
        if (isset($house_id) && !empty($house_id)) {
          $this->load->model('collections_model_new');
          $coolection_where = array(
            'id' => $house_id
          );
          if ('1' == $value['tbl']) {
            $house_info = $this->collections_model_new->get_house_sell($coolection_where);
          } else if ('2' == $value['tbl']) {
            $house_info = $this->collections_model_new->get_house_rent($coolection_where);
          }
          if (is_full_array($house_info)) {
            $house_str = $house_info[0]['house_name'] . '，' . $house_info[0]['buildarea'] . '㎡，' . $house_info[0]['price'] . '万元';
            $oldurl = $house_info[0]['oldurl'];
          }
        }
        $value['oldurl'] = $oldurl;
        $value['house_str'] = $house_str;
        $value['r_status_str'] = $data['r_status_arr'][$value['r_status']];
        $reportlist_2[] = $value;
      }
    }
    $data['reportlist'] = $reportlist_2;
    $this->load->view('blacklist/reportlist', $data);
  }


  /**
   * 删除中介黑名单
   */
  public function del($uid)
  {
    if ($uid) {
      $arr = array("id" => $uid);
      $result = $this->blacklist_model->del_blacklist($arr);
      echo "<script>location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/index';</script>";
    } else {
      echo "<script>alert('删除失败，请稍后重试~！');location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/index';</script>";
    }
  }


  /**
   * 删除虚假待审核的中介号码
   */
  public function del_report_agent($uid)
  {
    if ($uid) {
      $arr = array("r_id" => $uid);
      $result = $this->blacklist_model->del_reportlist($arr);
      echo "<script>location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/reportlist';</script>";
    } else {
      echo "<script>alert('删除失败，请稍后重试~！');location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/reportlist';</script>";
    }
  }


  /**
   * 修改待审核的中介
   */
  public function modify($uid)
  {
    //根据要修改的待审核编号，进入审核页面
    if ($uid) {
      $data['title'] = '用户数据中心欢迎你';
      $data['conf_where'] = 'index';

      //筛选条件
      $data['where_cond'] = array('r_id' => $uid);
      $data['report_agent'] = $this->blacklist_model->get_report_agent($data['where_cond']);
      $house_detail = array();
      if (is_full_array($data['report_agent']) && $data['report_agent'][0]['house_id'] > 0 && $data['report_agent'][0]['tbl'] > 0) {
        $this->load->model('collections_model_new');
        $where_cond = array('id' => $data['report_agent'][0]['house_id']);
        if ($data['report_agent'][0]['tbl'] == 1) {
          $house_detail = $this->collections_model_new->get_housesell_byid($where_cond);
        } else {
          $house_detail = $this->collections_model_new->get_houserent_byid($where_cond);
        }
      }
      $data['house_detail'] = $house_detail;
      $this->load->view('blacklist/report_agent', $data);
    } else {
      echo "<script>alert('系统繁忙，请稍后重试~！');history.go(-1);</script>";
    }

    //根据后台运营的选择:把待审核中介加入黑名单库 或者 经核实不是中介号码=》拒绝加入黑名单库
    if (isset($_POST['checkout']) && $_POST['checkout'] == 'angel_in_us') {
      //经核实，该号码不是中介
      if (is_array(@$_POST['action']) && @$_POST['action'][0] == 'reject') {
        //筛选条件
        $where = array();
        $where = array('r_id' => $_POST['r_id']);
        $data = array(
          'r_comment' => $_POST['r_comment'],
          'r_status' => '1',
        );
        $result = $this->blacklist_model->update_reportlist($where, $data);
        if ($result) {
          echo "<script>location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/reportlist';</script>";
        }
      } else if (is_array(@$_POST['action']) && @$_POST['action'][0] == 'blacklist') {
        //经核实，该号码确实为中介
        //举报属实增加等级分值
        $report_info = $this->blacklist_model->get_report_agent($data['where_cond']);
        $this->load->model('api_broker_level_base_model');
        $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $report_info[0]['broker_id']), 1);
        $this->api_broker_level_base_model->blacklist($report_info[0]);
        //筛选条件
        $where = array();
        $where = array('r_id' => $_POST['r_id']);
        $data = array(
          'r_comment' => $_POST['r_comment'],
          'r_status' => '2',
        );
        $broker_id = $_POST['broker_id'];

        //判断中介黑名单库里有没有，有则不入库，反之入库
        $cond = array('tel' => $_POST['r_tel']);
        $check_result = $this->blacklist_model->check_tel($cond);
        if (empty($check_result)) {
          $info = array(
            'tel' => $_POST['r_tel'],
            'addtime' => time()
          );
          $rel = $this->blacklist_model->add_blacklist($info);
          $report_info = $this->blacklist_model->get_report_agent($data['where_cond']);
          $result = $this->blacklist_model->update_reportlist($where, $data);
          if ($result) {
            $params['phone'] = $report_info[0]['r_tel'];
            $this->message_model->add_message('6-38', $report_info[0]['broker_id'], $report_info[0]['r_person'], '', $params);
            echo "<script>alert('审核成功，已将此号码加入经纪人黑名单库~！');location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/reportlist';</script>";
          } else {
            $params['phone'] = $report_info[0]['r_tel'];
            $this->message_model->add_message('6-39', $report_info[0]['broker_id'], $report_info[0]['r_person'], '', $params);
          }
        } else {
          echo "<script>alert('该号码已入过经纪人黑名单库~！');location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/reportlist';</script>";
        }
      } else {
        echo "<script>alert('您尚未做出有效操作~！');location.href='" . MLS_ADMIN_URL . "/index.php/blacklist/modify/" . $_POST['r_id'] . "';</script>";
      }
    }
  }

  /**
   * 审核
   */
  function details($id = 0)
  {
    $data['reportlist_details'] = array();
    if (isset($id) && $id > 0) {
      $submit = $this->input->post('submit_flag');
      $data['submit_flag'] = $submit;
      $reportlist_details = $this->blacklist_model->get_report_info_by_id($id);
      if ('modify' == $submit && $reportlist_details[0]['r_status'] == 3) {
        $post_data = $this->input->post(null, true);
        $update_data = array(
          'r_comment' => $post_data['r_comment'],
          'r_status' => $post_data['r_status'],
        );
        $update_result = $this->blacklist_model->update_reportlist(array('r_id' => $id), $update_data);
        if (1 == $update_result) {
          $result_text = '操作成功';
          //拉黑、下架 发送消息
          if ('2' == $post_data['r_status'] || '4' == $post_data['r_status']) {
            $params['phone'] = $reportlist_details[0]['r_tel'];
            $this->message_model->add_message('6-38', $reportlist_details[0]['broker_id'], $reportlist_details[0]['r_person'], '', $params);
            //加入黑名单
            $this->load->model('collections_model_new');
            $update_data = array(
              'isdel' => 1
            );
            if ('2' == $post_data['r_status']) {
              $add_black_data = array(
                'tel' => $reportlist_details[0]['r_tel'],
                'addtime' => time()
              );
              $rel = $this->blacklist_model->add_blacklist($add_black_data);
              //加积分
              $this->load->model('api_broker_level_base_model');
              $this->api_broker_level_base_model->set_broker_param(array('broker_id' => $reportlist_details[0]['broker_id']), 1);
              $this->api_broker_level_base_model->blacklist($reportlist_details[0]);
              //相关采集房源下架
              $where_cond = array(
                'telno1' => $reportlist_details[0]['r_tel']
              );
              $this->collections_model_new->update_sell_collect($where_cond, $update_data);
              $this->collections_model_new->update_rent_collect($where_cond, $update_data);
              //房源下架
            } else if ('4' == $post_data['r_status']) {
              if (isset($reportlist_details[0]['house_id']) && !empty($reportlist_details[0]['house_id'])) {
                //相关采集房源下架
                $where_cond = array(
                  'id' => $reportlist_details[0]['house_id']
                );
                $this->collections_model_new->update_sell_collect($where_cond, $update_data);
                $this->collections_model_new->update_rent_collect($where_cond, $update_data);
              }
            }

            /**
             * 举报对快房通加房豆推送
             */
            if ($reportlist_details[0]['is_cooperate_app']) {
              $get_fields = http_build_query(array(
                'broker_id' => $reportlist_details[0]['broker_id'],
                'phone' => $reportlist_details[0]['r_tel'],
                'way_id' => 9
              ));

              // vpost('http://appbroker.fang100.net/my/credit_api/?'.$get_fields,array());
            }

          } else {
            $params['phone'] = $reportlist_details[0]['r_tel'];
            $this->message_model->add_message('6-39', $reportlist_details[0]['broker_id'], $reportlist_details[0]['r_person'], '', $params);
          }
        } else {
          $result_text = '操作失败';
        }
        $data['result_text'] = $result_text;
      } else {
        if (is_full_array($reportlist_details)) {
          //举报人信息
          $broker_id = $reportlist_details[0]['broker_id'];
          $this->load->model('broker_info_model');
          $where_cond = array(
            'broker_id' => $broker_id
          );
          $broker_info = $this->broker_info_model->get_one_by($where_cond);
          if (is_full_array($broker_info)) {
            $data['broker_info'] = $broker_info;
          }

          $house_id = $reportlist_details[0]['house_id'];
          if (isset($house_id) && !empty($house_id)) {
            $this->load->model('collections_model_new');
            $coolection_where = array(
              'id' => $house_id
            );
            if ('1' == $reportlist_details[0]['tbl']) {
              $house_info = $this->collections_model_new->get_house_sell($coolection_where);
            } else if ('2' == $reportlist_details[0]['tbl']) {
              $house_info = $this->collections_model_new->get_house_rent($coolection_where);
            } else {
              $house_info = array();
            }
            //来源
            switch ($house_info[0]['source_from']) {
              case "0":
                $source_from_str = "赶集";
                break;
              case "1":
                $source_from_str = "58同城";
                break;
              case "2":
                $source_from_str = "房天下";
                break;
              case "3":
                $source_from_str = "house365";
                break;
              case "4":
                $source_from_str = "链家地产";
                break;
              default:
                $source_from_str = "暂无资料";
                break;
            }
            $house_info[0]['source_from_str'] = $source_from_str;
            $data['house_info'] = $house_info[0];
          } else {
            $data['house_info'] = array();
          }
          $data['reportlist_details'] = $reportlist_details[0];
        }
      }
    }
    $this->load->view('blacklist/details', $data);
  }

}

/* End of file blacklist.php */
/* Location: ./application/controllers/blacklist.php */
