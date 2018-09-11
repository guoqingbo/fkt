<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Call extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        $city = $this->input->get('city');
        $city = $city ? $city : 'hz';
        //设置城市参数
        $this->set_city($city);
        $this->load->model('hidden_call_base_model');
    }

    public function end()
    {
//        $_REQUEST_METHOD=='POST';
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $ticket = $this->input->get(NULL, TRUE);
        } else {
            $ticket = $this->input->post(NULL, TRUE);
        }
        if (empty($ticket)) {//如果post/get获取不到
            $ticket = json_decode(file_get_contents("php://input"), TRUE); //取得json数据,并转为数组
        }
//        if (empty($ticket['dh'])) {//测试数据
//            $ticket = '{
//                "agentid": "0",
//                "grouptype": "2",
//                "requestid": "2-3-1508750671",
//                "compid": "800999",
//                "serverid": "asterisk1",
//                "AgentGrpId": "",
//                "caller": "01053180888",
//                "App": "advice",
//                "callstatus": "ANSWERED",
//                "ring_time": "4",
//                "rec_path": "asterisk1/800999/2017-09-14/80099911888_18677053663_3_08-14-17.mp3",
//                "Duration": "17",
//                "channel": "SIP/gw-00004d80",
//                "totaltime": "42",
//                "cdrtype": "2",
//                "Isfee": 0,
//                "Linkedid": "",
//                "atime": "2017-09-14 08:14:13",
//                "TaskId": "",
//                "fee": "0",
//                "MemCnt": "",
//                "callee_area": "广西-南宁",
//                "callee": "18677053663",
//                "calltype": "3",
//                "etime": "2017-09-14 08:14:42",
//                "respcode": "200",
//                "dh": "95013345604444",
//                "caller_area": "北京-北京",
//                "duration_time": "29",
//                "id": 271908,
//                "stime": " 2017-10-13 16:21:51",
//                "rtime": "2017-09-14 08:14:09",
//                "callid": "80099911888"
//            }';
//        }

        if (!empty($ticket['dh']) && !empty($ticket['duration_time']) && !empty($ticket['requestid'])) {
            $result = $this->hidden_call_base_model->call_end(($ticket));
            if ($result['status'] === "success") {
                $this->result(true, 'query was successful ');
            } else {
                $this->result(false, $result['msg']);
            }
        } else {
            $this->result(false, 'parameter is not legal ');
        }
    }

    public function start()
    {
//        $_REQUEST_METHOD=='POST';
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $ticket = $this->input->get(NULL, TRUE);
        } else {
            $ticket = $this->input->post(NULL, TRUE);
        }
        if (empty($ticket)) {//如果post/get获取不到
            $ticket = json_decode(file_get_contents("php://input"), TRUE); //取得json数据,并转为数组
        }
//        if (empty($ticket['dh'])) {//测试数据
//            $ticket = '{
//            "caller":"13121210485",
//             "callrecording":0,
//             "dh":"0107758504",
//             "callid":"94167c18-c526-11e7-8d08-000c29314715",
//              "callee":"13121210489",
//              "calltime":"2017-11-09 16:18:34",
//              "requestid":"94167da8-c526-11e7-8d08-000c29314715"
//              }';
//            $ticket = json_decode($ticket, TRUE);
//        }
        if (!empty($ticket['dh']) && !empty($ticket['requestid'])) {
            $result = $this->hidden_call_base_model->call_start(($ticket));
            if ($result['status'] === "success") {
                $this->result(true, 'query was successful ');
            } else {
                $this->result(false, $result['msg']);
            }
        } else {
            $this->result(false, 'parameter is not legal ');
        }
    }
}

/* End of file notice_access.php */
/* Location: ./application/controllers/notice_access.php */
