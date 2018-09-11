<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 后台帮助中心
 *
 * @package    mls_admin
 * @subpackage      Controllers
 * @category        Controllers
 * @author     lujun
 * Date: 15-3-16
 * Time: 下午3:17
 */
class Help_center extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->helper('page_helper');
    $this->load->helper('user_helper');
    $this->load->model('help_center_model');
    $this->load->helper('common_load_source_helper');
  }

  /** 帮助中心首页 OK*/
  public function index()
  {
    //模板使用数据
    $data = array();

    $data['title'] = "帮助中心管理";
    $data['conf_where'] = 'index';

    //获取所有主菜单
    $data['parents'] = $this->help_center_model->get_all_parents();

    $this->load->view('help_center/index', $data);
  }

  /** 新增主菜单 */
  public function add()
  {
    //模板使用数据
    $data = array();

    $data['title'] = "新增主菜单";
    $data['conf_where'] = 'index';
    $this->load->view('help_center/add', $data);
  }

  /** 修改主菜单名字 */
  public function modify_pname($id)
  {
    //模板使用数据
    $data = array();

    $data['title'] = "修改主菜单信息";
    $data['conf_where'] = 'index';
    //传递数据
    $data['id'] = $id;
    $where = "id = " . $id;
    $data['parent_name'] = $this->help_center_model->get_parent_name($where);
    $this->load->view('help_center/modify_parent', $data);
  }

  /** 修改子菜单名字 */
  public function modify_sname($id)
  {
    //模板使用数据
    $data = array();

    $data['title'] = "修改子菜单信息";
    $data['conf_where'] = 'index';
    //传递数据
    $data['id'] = $id;
    $where = "id = " . $id;
    $data['parent_name'] = $this->help_center_model->get_parent_name($where);
    $data['prev_parent_id'] = $this->help_center_model->get_prev_parent_id($where);
    $this->load->view('help_center/modify_sparent', $data);
  }

  /** 保存修改 */
  public function save_modify_pname()
  {
    $id = $this->input->post('parent_id');
    $title = $this->input->post('title');
    $old_title = $this->input->post('old_title');
    $orderby = $this->input->post('orderby');
    $old_orderby = $this->input->post('old_orderby');
    if (($title == $old_title) && ($orderby == $old_orderby)) {
      $json_data['status'] = 2;
    } else {
      //修改主菜单
      $where = 'id = ' . $id;
      $data['title'] = $title;
      $data['orderby'] = $orderby;
      $rs = $this->help_center_model->save_modify('help_center', $data, $where);
      if ($rs) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  /** 删除主菜单 */
  public function del_parent()
  {
    $id = $this->input->post('id');
    $where = "id = " . $id . " or parent_id = " . $id;
    //修改步骤
    $rs = $this->help_center_model->delete_data($where, 'help_center');
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }

  /** 删除子项 */
  public function del_child()
  {
    $id = $this->input->post('id');
    $where = 'id = ' . $id;
    $rs = $this->help_center_model->delete_data($where, 'help_center');
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }

  /** 展示主菜单下子标题 */
  public function show($id)
  {
    //模板使用数据
    $data = array();
    $data['conf_where'] = 'index';
    $data['parent_id'] = $id;
    $where = "id = " . $id;
    $parent_info = $this->help_center_model->get_parent_name($where);
    $data['title'] = "[" . $parent_info['title'] . "]下的子标题操作";
    //读取子内容
    $data['children'] = $this->help_center_model->get_children($id);
    $this->load->view('help_center/sshow', $data);
  }

  /** 展示子菜单下的标题 */
  public function sshow($id)
  {
    //模板使用数据
    $data = array();
    $data['conf_where'] = 'index';
    $data['parent_id'] = $id;
    $where = "id = " . $id;
    $parent_info = $this->help_center_model->get_parent_name($where);
    $data['title'] = "[" . $parent_info['title'] . "]下的操作";
    //读取子内容
    $data['children'] = $this->help_center_model->get_children($id);
    $this->load->view('help_center/sshow', $data);
  }

  /** 展示子菜单下的标题 */
  public function show_sall($id)
  {
    //模板使用数据
    $data = array();
    $data['conf_where'] = 'index';
    $data['parent_id'] = $id;
    $where = "id = " . $id;
    $parent_info = $this->help_center_model->get_parent_name($where);
    $data['title'] = "[" . $parent_info['title'] . "]下的操作";
    //读取子内容
    $data['children'] = $this->help_center_model->get_children($id);
    $data['prev_parent_id'] = $this->help_center_model->get_prev_parent_id($where);
    $this->load->view('help_center/show', $data);
  }

  /** 修改子菜单信息*/
  public function modify_child($id, $parent_id)
  {
    $data = array();
    $data['conf_where'] = 'index';
    $data['parent_id'] = $parent_id;
    $data['title'] = "修改子项信息";
    $data['child_info'] = $this->help_center_model->get_child_info($id);
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $this->load->view('help_center/modify_child', $data);
  }

  /** 保存子项信息 */
  public function save_modify_child()
  {
    $id = $this->input->post('id');
    $data['title'] = $this->input->post('title');
    $data['content'] = $this->input->post('content');
    $data['orderby'] = $this->input->post('orderby');
    //修改主菜单
    $where = 'id = ' . $id;
    $rs = $this->help_center_model->save_modify('help_center', $data, $where);
    if ($rs) {
      $json_data['status'] = 1;
    } else {
      $json_data['status'] = 0;
    }
    echo json_encode($json_data);
  }

  /** 新增主菜单 */
  public function add_parent()
  {
    $data = array();

    $data['title'] = "新增主菜单名字";
    $data['conf_where'] = 'index';
    $this->load->view('help_center/add_parent', $data);
  }

  /** 新增子菜单 */
  public function add_sparent($id)
  {
    $data = array();
    $data['parent_id'] = $id;
    $data['title'] = "新增子菜单名字";
    $data['conf_where'] = 'index';
    $this->load->view('help_center/add_sparent', $data);
  }

  /** 保存主菜单*/
  public function save_add_pname()
  {
    $title = $this->input->post('title');
    if (empty($title)) {
      $json_data['status'] = 2;
    } else {
      $data['title'] = $title;
      $data['parent_id'] = null;
      $data['is_parent'] = 1;
      $data['content'] = null;
      $data['orderby'] = $this->input->post('orderby');
      $rs = $this->help_center_model->save_add('help_center', $data);
      if ($rs) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  /** 保存子菜单*/
  public function save_add_spname()
  {
    $title = $this->input->post('title');
    $parent_id = $this->input->post('parent_id');
    if (empty($title)) {
      $json_data['status'] = 2;
    } else {
      $data['title'] = $title;
      $data['is_parent'] = 0;
      $data['parent_id'] = $parent_id;
      $data['content'] = null;
      $data['orderby'] = $this->input->post('orderby');
      $rs = $this->help_center_model->save_add('help_center', $data);
      if ($rs) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  /** 新增子项 */
  public function add_child($parent_id)
  {
    //需要加载的JS
    $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,'
      . 'common/third/swf/swfupload.js,'
      . 'mls/js/v1.0/uploadpic.js,'
      . 'common/third/jquery-ui-1.9.2.custom.min.js');
    $data = array();
    $data['parent_id'] = $parent_id;
    $data['title'] = "新增子项";
    $data['conf_where'] = 'index';
    $this->load->view('help_center/add_child', $data);
  }

  /** 保存子项*/
  public function save_add_child()
  {
    $title = $this->input->post('title');
    if (empty($title)) {
      $json_data['status'] = 2;
    } else {
      $data['title'] = $title;
      $data['parent_id'] = $this->input->post('parent_id');
      $data['is_parent'] = 0;
      $data['content'] = $this->input->post('content');
      $data['orderby'] = $this->input->post('orderby');
      $rs = $this->help_center_model->save_add('help_center', $data);
      if ($rs) {
        $json_data['status'] = 1;
      } else {
        $json_data['status'] = 0;
      }
    }
    echo json_encode($json_data);
  }

  /*
   * 上传图片
   */
  public function upload_photo()
  {
    $filename = $this->input->post('action');
    $this->load->model('pic_model');
    $this->pic_model->set_filename($filename);
    $fileurl = $this->pic_model->common_upload();
//        $fileurl = str_replace("/thumb","",$fileurl);
    $fileurl = changepic($fileurl);
    echo "<script>window.parent.changePic('" . $fileurl . "')</script>";
  }

}
