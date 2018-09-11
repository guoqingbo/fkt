<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 城市 Class
 *
 * 城市控制器
 *
 * @package     mls
 * @subpackage      Controllers
 * @category        Controllers
 * @author      Lion
 */
class House extends MY_Controller
{
    /**
     * 城市参数
     *
     * @access private
     * @var string
     */
    protected $_city = 'hz';


    /**
     * 录入经纪人id
     *
     * @access private
     * @var int
     */
    private $_broker_id = 0;

    /**
     * 当前页码
     *
     * @access private
     * @var string
     */
    private $_current_page = 1;

    /**
     * 每页条目数
     *
     * @access private
     * @var int
     */
    private $_limit = 100;

    /**
     * 偏移
     *
     * @access private
     * @var int
     */
    private $_offset = 0;

    /**
     * 条目总数
     *
     * @access private
     * @var int
     */
    private $_total_count = 0;


    /**
     * 解析函数
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //加载区属模型类
        //$this->load->model('district_model');
    }

    public function field()
    {
        $data = [];

        $data['list'] = [
            1 => '住宅',
            2 => '别墅',
            3 => '商铺',
            4 => '写字楼',
            5 => '厂房',
            6 => '仓库',
            7 => '车库',
            8 => '酒店式公寓'
        ];

        //需要加载的css
        $data['css'] = load_css('mls/css/v1.0/base.css,'
            . 'mls/third/iconfont/iconfont.css,'
            . 'mls/css/v1.0/guest_disk.css,'
            . 'mls/css/v1.0/myStyle.css,'
            . 'mls/css/v1.0/house_manage.css');

        //需要加载的JS
        $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js,common/third/jquery-ui-1.9.2.custom.min.js,'
            . 'mls/js/v1.0/group_publish.js');

        //底部JS
        $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,'
            . 'mls/js/v1.0/jquery.validate.min.js,mls/js/v1.0/house.js,'
            . 'mls/js/v1.0/cooperate_common.js');

        $this->view('house/field', $data);
    }

    public function set_field($sell_type)
    {
        $user = $this->user_arr;
        $agency_id = isset($user['agency_id']) ? $user['agency_id'] : '';
        //print_r($user);
        //echo $sell_type;
        //exit;
        if (empty($agency_id)) {
            echo '经纪人门店不存在';
            exit;
        }
        $sell_type = $sell_type ?: 1;
        $sellTypes = [
            1 => '住宅',
            2 => '别墅',
            3 => '商铺',
            4 => '写字楼',
            5 => '厂房',
            6 => '仓库',
            7 => '车库',
            8 => '酒店式公寓',
        ];
        if (!isset($sellTypes[$sell_type])) {
            echo '数据错误';
            exit;
        }
        $this->load->model('sell_house_field_agency_model');
        $db_city = $this->sell_house_field_agency_model->get_db_city();
        $fieldList = $db_city->from('sell_house_field_agency')->where("agency_id = $agency_id and sell_type = $sell_type")->order_by('id', 'asc')->get()->result_array();
        $defaultList = $db_city->from('sell_house_field_agency')->where("agency_id = 0 and sell_type = $sell_type")->order_by('id', 'asc')->get()->result_array();
        $lists = [];
        if (!empty($fieldList)) {
            foreach ($fieldList as $v) {
                $lists[$v['field_name']] = $v;
            }
        }
        foreach ($defaultList as $v) {
            if (!isset($lists[$v['field_name']])) {
                $lists[$v['field_name']] = $v;
            }
        }
        $data['lists'] = $lists;
        $data['sell_type'] = $sell_type;
        $data['sellTypes'] = $sellTypes;

        //页面标题
        $data['page_title'] = '配置房源字段';
        //需要加载的css
        $data['css'] = load_css('mls/css/v1.0/base.css,mls/third/iconfont/iconfont.css'
            . ',mls/css/v1.0/house_manage.css'
            . ',mls/css/v1.0/myStyle.css');
        //需要加载的JS
        $data['js'] = load_js('mls/js/v1.0/jquery-1.8.3.min.js');

        //底部JS
        $data['footer_js'] = load_js('mls/js/v1.0/openWin.js,mls/js/v1.0/house_list.js,mls/js/v1.0/house.js,'
            . 'mls/js/v1.0/backspace.js,mls/js/v1.0/scrollPic_0630.js');
        //print_r($lists);
        //echo $sell_type;
        $this->view('house/set_field', $data);
    }

    public function save_field($sell_type)
    {
        $user = $this->user_arr;
        $agency_id = isset($user['agency_id']) ? $user['agency_id'] : '';
        $result = ['error' => 0, 'msg' => ''];
        if (empty($agency_id) || empty($sell_type)) {
            $result = ['error' => 1, 'msg' => '数据错误'];
            echo json_encode($result);
            exit;
        }
        //$data = $this->input->post();
        $agencyIdList = $this->input->post('agency_id');
        $nameList = $this->input->post('field_name');
        $valueList = $this->input->post('field_value');
        $displayList = $this->input->post('display');
        $requiredList = $this->input->post('required');
        //print_r($data);exit;
        if (empty($agencyIdList) || empty($displayList) || empty($requiredList)) {
            $result = ['error' => 1, 'msg' => '数据错误'];
            echo json_encode($result);
            exit;
        }
        $this->load->model('sell_house_field_agency_model');
        $db_city = $this->sell_house_field_agency_model->get_db_city();
        $db_city->trans_start();

        foreach ($agencyIdList as $k => $v) {
            if (empty($v)) {
                $date = array(
                    'sell_type' => $sell_type,
                    'field_name' => $nameList[$k],
                    'field_value' => $valueList[$k],
                    'display' => $displayList[$k],
                    'required' => $requiredList[$k],
                    'agency_id' => $agency_id
                );
                $db_city->insert('sell_house_field_agency', $date);
            } else {
                $db_city->where('id', $k)->update('sell_house_field_agency', ['display' => $displayList[$k], 'required' => $requiredList[$k]]);
            }
        }

        $db_city->trans_complete();
        if ($db_city->trans_status() === FALSE) {
            $result = ['error' => 1, 'msg' => '提交失败'];
        } else {
            $result = ['error' => 0, 'msg' => '提交成功'];
        }
        echo json_encode($result);
        exit;
    }
}