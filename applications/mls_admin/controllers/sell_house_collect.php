<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 二手房采集内容管理
 *
 * @package     mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author      杨锐
 */
class Sell_house_collect extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->model('sell_house_collect_model');
    $this->load->helper('user_helper');
  }

  /**
   * 二手房采集内容列表页面
   */
  public function index()
  {
    $data['title'] = '二手房采集内容管理';
    $data['conf_where'] = 'index';

    //筛选条件
    $data['where_cond'] = array();

    $start_time = $this->input->post('start_time', TRUE);
    $end_time = $this->input->post('end_time', TRUE);
    $start_time = $start_time != '' ? $start_time : date('Y-m-d', strtotime("-7 day"));
    $end_time = $end_time != '' ? $end_time : date('Y-m-d');

    $start_time = strtotime($start_time . " 00:00");
    $end_time = strtotime($end_time . " 23:59");
    $data['start_time'] = date('Y-m-d H:i:s', $start_time);
    $data['end_time'] = date('Y-m-d H:i:s', $end_time);

    if ($start_time >= $end_time) {
      echo "<script>alert('您查询的开始时间不能大于结束时间！');location.href='" . MLS_ADMIN_URL . "/blacklist/reportlist';</script>";
    }
    if ($start_time < $end_time) {
      $data['where_cond'] = array('createtime >=' => $start_time, "createtime <=" => $end_time);
    }

    $data['where_cond']['city'] = $_SESSION['esfdatacenter']['city_id'];

    $data['sell_house_collect_num'] = $this->sell_house_collect_model->get_num($data['where_cond']);
    $data['pagesize'] = 10;//设定每一页显示的记录数
    $data['pages'] = $data['sell_house_collect_num'] ? ceil($data['sell_house_collect_num'] / $data['pagesize']) : 0;  //计算总页数
    $data['page'] = isset($_POST['pg']) ? intval($_POST['pg']) : 1; // 获取当前页数
    $data['page'] = ($data['page'] > $data['pages'] && $data['pages'] != 0) ? $data['pages'] : $data['page'];  //判断跳转页数
    $data['offset'] = $data['pagesize'] * ($data['page'] - 1);   //计算记录偏移量
    $data['select'] = array('id', 'source_from', 'house_title', 'createtime', 'e_status');   //计算记录偏移量
    $data['order_by'] = 'createtime';
    $data['sell_house_collect'] = $this->sell_house_collect_model->get_collect($data['where_cond'], $data['order_by'], $data['offset'], $data['pagesize'], $data['select']);
    $this->load->view('sell_house_collect/index', $data);
  }

  /**
   * 删除二手房采集内容
   */
  public function del($id)
  {
    $data['title'] = '删除二手房采集内容';
    $data['conf_where'] = 'index';
    $delResult = '';
    $data['delResult'] = $delResult;
    if (!empty($id)) {
      $sell_house_collectData = $this->sell_house_collect_model->del_collect($id);
      if ($sell_house_collectData == 1) {
        $delResult = 1;//删除成功
      } else {
        $delResult = 0;//删除失败
      }
    }
    $data['delResult'] = $delResult;
    $this->load->view('sell_house_collect/del', $data);
  }

  /**
   * 审核二手房采集内容
   */
  public function modify($id)
  {
    if (!$id) {
      echo "<script>alert('系统繁忙，请稍后重试~！');history.go(-1);</script>";
    }

    if (isset($_POST['checkout']) && $_POST['checkout'] == 'angel_in_us') {
      $act = $this->input->post('act');
      $tel = trim($this->input->post('tel'));
      //通过
      if ($act == "pass") {
        if (!empty($tel)) {
          $paramArray = array(
            'e_status' => 2,
            'telno1' => $tel ? $tel : ''
          );
        } else {
          $paramArray = array(
            'e_status' => 2
          );
        }
        $modifyResult = $this->sell_house_collect_model->modify($id, $paramArray);
        if ($modifyResult) {
          //审核成功
          echo "<script>alert('审核成功');location.href='" . MLS_ADMIN_URL . "/sell_house_collect/';</script>";
        } else {
          //审核失败
          echo "<script>alert('审核失败');location.href='" . MLS_ADMIN_URL . "/sell_house_collect/';</script>";
          $delResult = 0;
        }
      }
      //加入黑名单
      if ($act == "blacklist") {
        $paramArray = array(
          'e_status' => 3
        );
        $modifyResult = $this->sell_house_collect_model->modify($id, $paramArray);
        if ($modifyResult) {
          $tel = $this->input->post('tel');
          $this->load->model('blacklist_model');//中介黑名单模型类
          //判断中介黑名单库里有没有，有则不入库，反之入库
          $cond = array('tel' => $tel);
          $check_result = $this->blacklist_model->check_tel($cond);
          if (empty($check_result)) {
            $info = array(
              'tel' => $tel,
              'addtime' => time()
            );
            $rel = $this->blacklist_model->add_blacklist($info);
            if ($rel) {
              echo "<script>alert('审核成功，已将此号码加入经纪人黑名单库~！');location.href='" . MLS_ADMIN_URL . "/sell_house_collect/';</script>";
            }
          } else {
            echo "<script>alert('该号码已入过中介黑名单库~！');location.href='" . MLS_ADMIN_URL . "/sell_house_collect/';</script>";
          }
        } else {
          //加入黑名单失败
          echo "<script>alert('加入黑名单失败');location.href='" . MLS_ADMIN_URL . "/sell_house_collect/';</script>";
          $delResult = 0;
        }
      }
    }

    //根据要修改的待审核编号，进入审核页面
    if ($id) {
      $data['title'] = '审核二手房采集内容';
      $data['conf_where'] = 'index';

      //筛选条件
      $data['select'] = array('telno1', 'oldurl', 'picurl', 'tel_url');
      $sell_house_collectData = $this->sell_house_collect_model->getinfo_byid($id, $data['select']);
      $data['sell_house_collect'] = $sell_house_collectData[0];
      $this->load->view('sell_house_collect/modify', $data);
    }
  }
}

/* End of file sell_house_collect.php */
/* Location: ./application/controllers/sell_house_collect.php */
