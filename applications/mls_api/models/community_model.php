<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * MLS
 *
 * MLS系统类库
 *
 * @package         MLS
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://mls.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * Agency_model CLASS
 *
 * 门店业务逻辑类 提供增加公司，修改、删除等功能
 *
 * @package         MLS
 * @subpackage      Models
 * @category        Models
 * @author          sun
 */
load_m("Community_base_model");

class Community_model extends Community_base_model
{
    protected $_tab = 'community';

    /**
     * 类初始化
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据条件查询小区列表
     */

    public function get_by_where($where, $selectField = '', $limit = 0, $start = 0)
    {
        if (!empty($selectField)) {
            $this->dbback_city->select($selectField);
        }
        if ($start >= 0 && $limit > 0) {
            $this->dbback_city->limit($limit, $start);
        }
        $communityList = $this
            ->dbback_city
            ->where($where)
            ->get($this->_tab)
            ->result_array();
        return $communityList;
    }


    /**
     * 批量更新
     */
    public function update_batch($data)
    {
        $updateRes = $this
            ->dbback_city
            ->update_batch($this->_tab, $data, 'id');
        return $updateRes;
    }

    /**
     * 单条更新
     */
    public function update($data)
    {
        $updateRes = $this
            ->dbback_city
            ->update($this->_tab, $data, 'id = ' . $data['id']);
        return $updateRes;
    }
}

/* End of file Agency_model.php */
/* Location: ./app/models/Agency_model.php */
