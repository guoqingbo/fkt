<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 *
 * mls系统基本类库
 *
 * @package         mls
 * @author          EllisLab Dev Team
 * @copyright       Copyright (c) 2006 - 2014
 * @link            http://nj.sell.house.com
 * @since           Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * collections_model CLASS
 *
 * 采集模型类
 *
 * @package         datacenter
 * @subpackage      Models
 * @category        Models
 * @date      2014-12-28
 * @author          angel_in_us
 */
class Collections_model_cooperate extends MY_Model
{

  public function __construct()
  {
    parent::__construct();

    $this->rent_house_collect = 'rent_house_collect';
    $this->sell_house_collect = 'sell_house_collect';
    $this->agent_house_judge = 'agent_house_judge_cooperate';
    $this->collect_house_collection = 'collect_house_collection';
    $this->agent_reportlist = 'agent_reportlist';
    $this->city = 'city';
    $this->sell_house_collect_history = 'sell_house_collect_history';
    $this->rent_house_collect_history = 'rent_house_collect_history';
    $this->sell_house_collect_check = 'sell_house_collect_check';
    $this->rent_house_collect_check = 'rent_house_collect_check';
    $this->sell_house_sub = 'sell_house_sub';
    $this->rent_house_sub = 'rent_house_sub';
    $this->sell_house = 'sell_house';
    $this->rent_house = 'rent_house';
    $this->collect_set = 'collect_set_cooperate';//采集设置表
    $city = $this->config->item('login_city');
    $this->_mem_key = $city . '_collections_model_';
    $this->load->library('SphinxClient', '', 'sc');
    $this->load->config('sphinx_conf');
  }

  /**
   * sphinx查询
   * 2016.4.27
   * cc
   */
  function sphinx($data, $where_in, $dis_blk, $house_name, $timetype, $city, $type, $page, $limit_page = 0, $offset = 0)
  {
    $cityid = $this->get_city($city);
    $city = $cityid[0]['id'];
    //表单验证
    $sphinxconfig = $this->config->item('sphinx');

    $this->sc->SetServer($sphinxconfig['servers']['host'], $sphinxconfig['servers']['port']);

    //以下设置用于返回数组形式的结果
    $this->sc->SetArrayResult(true);

    //设置连接超时时间
    $this->sc->SetConnectTimeout(3);

    //ID的过滤
    //$this->sc->SetIDRange(144924, 145924);

    //sql_attr_uint等类型的属性字段，需要使用setFilter过滤，类似SQL的WHERE q_type=1
    //当为true时，相当于$attribute!=$value，默认值是false
    //$this->sc->SetFilter($attribute, $values, $exclude);

    $this->sc->setFilter('isdel', array(0));//删除：0未删除1删除
    $this->sc->setFilter('city', array($city));//城市
    if (!empty($where_in)) {
      $this->sc->setFilter('@id', $where_in[1]);//已查看
    }
    if ($timetype == 4) {
      $this->sc->SetFilterRange('createtime', 0, $data['createtime <=']);//时间(更早)
    } else {
      $this->sc->SetFilterRange('createtime', $data['createtime >='], time());//时间(一周，一月，三月内)
    }
    if ($type == 'sell') {
      if (isset($data['sell_type'])) {
        $this->sc->setFilter('sell_type', array($data['sell_type']));//物业类型(出售)
      }
    } else {
      if (isset($data['rent_type'])) {
        $this->sc->setFilter('rent_type', array($data['rent_type']));//物业类型(出租)
      }
    }

    if (isset($data['room'])) {
      $this->sc->setFilter('room', array($data['room']));//户型
    }
    if (isset($data['isdel'])) {
      $this->sc->setFilter('isdel', array($data['isdel']));//是否删除
    }
    if (isset($data['forward'])) {
      $this->sc->setFilter('forward', array($data['forward']));//朝向
    }
    if (isset($data['serverco'])) {
      $this->sc->setFilter('serverco', array($data['serverco']));//装修
    }
    if (isset($data['source_from'])) {
      $this->sc->setFilter('source_from', array($data['source_from']));//采集来源
    }
    if (isset($data['buildarea >=']) || isset($data['buildarea <='])) {
      if (!isset($data['buildarea >='])) {
        $data['buildarea >='] = 0;
      }
      if (!isset($data['buildarea <='])) {
        $data['buildarea <='] = 9999999;
      }
      $this->sc->SetFilterFloatRange('buildarea', $data['buildarea >='], $data['buildarea <=']);//面积
    }
    if (isset($data['price >=']) || isset($data['price <='])) {
      if (!isset($data['price >='])) {
        $data['price >='] = 0;
      }
      if (!isset($data['price <='])) {
        $data['price <='] = 9999999;
      }
      $this->sc->SetFilterFloatRange('price', $data['price >='], $data['price <=']);//总价
    }


    //sql_attr_uint等类型的属性字段，也可以设置过滤范围，类似SQL的WHERE price>=100 AND price<=200
    //$this->sc->SetFilterRange('price',100,200);
    //$this->sc->SetFilterFloatRange('price',100,200);
    if ($limit_page) {
      $limit_page = intval($limit_page);
      $this->sc->SetLimits($offset, $limit_page, 1500);
    } else {
      $pg = intval($page);
      $pg = $pg > 0 ? $pg : 1;

      $pagesize = 15;
      $offset = ($pg - 1) * $pagesize;
      //取从头开始的前20条数据，0,20类似SQl语句的LIMIT 0,20
      $this->sc->SetLimits($offset, $pagesize, 1500);
    }

    //如果需要搜索指定全文字段的内容，可以使用扩展匹配模式：
    //SPH_MATCH_ALL, 匹配所有查询词(默认模式)
    //SPH_MATCH_ANY, 匹配查询词中的任意一个
    //SPH_MATCH_PHRASE, 将整个查询看作一个词组，要求按顺序完整匹配
    //SPH_MATCH_BOOLEAN, 将查询看作一个布尔表达式
    //SPH_MATCH_EXTENDED, 将查询看作一个CoreSeek/Sphinx内部查询语言的表达式
    //SPH_MATCH_EXTENDED2, 使用第二版的“扩展匹配模式”对查询进行匹配
    //SPH_MATCH_FULLSCAN, 强制使用下文所述的“完整扫描”模式来对查询进行匹配，没有搜索词的情况下默认激活
    $this->sc->SetMatchMode(SPH_MATCH_EXTENDED2);
    $kw = '';
    if (!empty($dis_blk)) {
      $kw = "@district " . $dis_blk['district'];
      if (isset($dis_blk['block'])) {
        $kw .= " @block " . $dis_blk['block'];
      }
    }
    if (!empty($house_name)) {
      $kw .= " @(block,house_name) " . $house_name['like_value'];
    }

    //排序模式
    //SPH_SORT_RELEVANCE 模式, 按相关度降序排列（最好的匹配排在最前面）
    //SPH_SORT_ATTR_DESC 模式, 按属性降序排列 （属性值越大的越是排在前面）
    //SPH_SORT_ATTR_ASC 模式, 按属性升序排列（属性值越小的越是排在前面）
    //SPH_SORT_TIME_SEGMENTS 模式, 先按时间段（最近一小时/天/周/月）降序，再按相关度降序
    //SPH_SORT_EXTENDED 模式, 按一种类似SQL的方式将列组合起来，升序或降序排列。
    //SPH_SORT_EXPR 模式，按某个算术表达式排序。
    $this->sc->SetSortMode(SPH_SORT_EXTENDED, "createtime DESC");

    //一定要转换为UTF-8编码
    //$kw = iconv("GBK", "UTF-8", $keyword);
    //在做索引时，没有进行 sql_attr_类型 设置的字段，可以作为“搜索字符串”，进行全文搜索
    $res = $this->sc->Query($kw, $sphinxconfig['source']['collect_' . $type]);//"*"表示在所有索引里面同时搜索，"索引名称（例如test或者test,test2）"则表示搜索指定的

    if (isset($res['matches']) && !empty($res['matches'])) {
      foreach ($res['matches'] as $key => $val) {
        $houseid[$key] = $val['id'];
      }
      $result = $this->select_house_collect($houseid, $type);
      foreach ($houseid as $val) {
        foreach ($result as $value) {
          if ($val == $value['id']) {
            $ress[] = $value;
          }
        }
      }
      $datas = array(
        'total' => $res['total_found'],
        'blacklist' => $ress
      );
    } else {
      $datas = array(
        'total' => 0,
        'blacklist' => array()
      );
    }

    return $datas;
  }

  /**
   * sphinx根据订阅条件查询
   * 2016.4.27
   * cc
   */
  function sphinx_set($data, $dis_blk = array(), $house_name = array(), $timetype, $city, $type, $page, $limit_page = 0, $offset = 0)
  {
    $cityid = $this->get_city($city);
    $city = $cityid[0]['id'];
    //表单验证
    $sphinxconfig = $this->config->item('sphinx');

    $this->sc->SetServer($sphinxconfig['servers']['host'], $sphinxconfig['servers']['port']);

    //以下设置用于返回数组形式的结果
    $this->sc->SetArrayResult(true);

    //设置连接超时时间
    $this->sc->SetConnectTimeout(3);

    //ID的过滤
    //$this->sc->SetIDRange(144924, 145924);

    //sql_attr_uint等类型的属性字段，需要使用setFilter过滤，类似SQL的WHERE q_type=1
    //当为true时，相当于$attribute!=$value，默认值是false
    //$this->sc->SetFilter($attribute, $values, $exclude);

    $this->sc->setFilter('isdel', array(0));//删除：0未删除1删除
    $this->sc->setFilter('city', array($city));//城市

    if ($timetype == 4) {
      $this->sc->SetFilterRange('createtime', 0, $data['createtime <=']);//时间(更早)
    } else {
      $this->sc->SetFilterRange('createtime', $data['createtime >='], time());//时间(一周，一月，三月内)
    }
    if ($type == 'sell') {
      if (isset($data['sell_type'])) {
        $this->sc->setFilter('sell_type', array($data['sell_type']));//物业类型(出售)
      }
    } else {
      if (isset($data['rent_type'])) {
        $this->sc->setFilter('rent_type', array($data['rent_type']));//物业类型(出租)
      }
    }

    if (isset($data['room'])) {
      $this->sc->setFilter('room', array($data['room']));//户型
    }
    if (isset($data['forward'])) {
      $this->sc->setFilter('forward', array($data['forward']));//朝向
    }
    if (isset($data['serverco'])) {
      $this->sc->setFilter('serverco', array($data['serverco']));//装修
    }
    if (isset($data['source_from'])) {
      $this->sc->setFilter('source_from', array($data['source_from']));//采集来源
    }
    if (isset($data['price >=']) || isset($data['price <='])) {
      if (!isset($data['price >='])) {
        $data['price >='] = 0;
      }
      if (!isset($data['price <='])) {
        $data['price <='] = 9999999;
      }
      $this->sc->SetFilterFloatRange('price', $data['price >='], $data['price <=']);//总价
    }


    //sql_attr_uint等类型的属性字段，也可以设置过滤范围，类似SQL的WHERE price>=100 AND price<=200
    //$this->sc->SetFilterRange('price',100,200);
    //$this->sc->SetFilterFloatRange('price',100,200);
    if ($limit_page) {
      $limit_page = intval($limit_page);
      $this->sc->SetLimits($offset, $limit_page, 1500);
    } else {
      $pg = intval($page);
      $pg = $pg > 0 ? $pg : 1;

      $pagesize = 15;
      $offset = ($pg - 1) * $pagesize;
      //取从头开始的前20条数据，0,20类似SQl语句的LIMIT 0,20
      $this->sc->SetLimits($offset, $pagesize, 1500);
    }

    //如果需要搜索指定全文字段的内容，可以使用扩展匹配模式：
    //SPH_MATCH_ALL, 匹配所有查询词(默认模式)
    //SPH_MATCH_ANY, 匹配查询词中的任意一个
    //SPH_MATCH_PHRASE, 将整个查询看作一个词组，要求按顺序完整匹配
    //SPH_MATCH_BOOLEAN, 将查询看作一个布尔表达式
    //SPH_MATCH_EXTENDED, 将查询看作一个CoreSeek/Sphinx内部查询语言的表达式
    //SPH_MATCH_EXTENDED2, 使用第二版的“扩展匹配模式”对查询进行匹配
    //SPH_MATCH_FULLSCAN, 强制使用下文所述的“完整扫描”模式来对查询进行匹配，没有搜索词的情况下默认激活
    $this->sc->SetMatchMode(SPH_MATCH_EXTENDED2);
    $kw = '';
    if (is_full_array($dis_blk)) {
      $kw .= '(';
      foreach ($dis_blk as $key => $value) {
        $kw .= '(@district' . $value['district'] . ' & ';
        $kw .= '@block' . $value['block'] . ' ) | ';
      }
      $kw = trim($kw, '| ');
      $kw .= ')';
    }

//        if (!empty($house_name)) {
//            $kw .= " @(block,house_name) ".$house_name['like_value'];
//        }
    if (is_full_array($house_name)) {
      $kw .= ' & (';
      foreach ($house_name as $key => $value) {
        $kw .= '@(block,house_name) ' . $value . ' | ';
      }
      $kw = trim($kw, '| ');
      $kw .= ' )';
    }

    //排序模式
    //SPH_SORT_RELEVANCE 模式, 按相关度降序排列（最好的匹配排在最前面）
    //SPH_SORT_ATTR_DESC 模式, 按属性降序排列 （属性值越大的越是排在前面）
    //SPH_SORT_ATTR_ASC 模式, 按属性升序排列（属性值越小的越是排在前面）
    //SPH_SORT_TIME_SEGMENTS 模式, 先按时间段（最近一小时/天/周/月）降序，再按相关度降序
    //SPH_SORT_EXTENDED 模式, 按一种类似SQL的方式将列组合起来，升序或降序排列。
    //SPH_SORT_EXPR 模式，按某个算术表达式排序。
    $this->sc->SetSortMode(SPH_SORT_EXTENDED, "createtime DESC");

    //一定要转换为UTF-8编码
    //$kw = iconv("GBK", "UTF-8", $keyword);
    //在做索引时，没有进行 sql_attr_类型 设置的字段，可以作为“搜索字符串”，进行全文搜索
    $res = $this->sc->Query($kw, $sphinxconfig['source']['collect_' . $type]);//"*"表示在所有索引里面同时搜索，"索引名称（例如test或者test,test2）"则表示搜索指定的

    if (isset($res['matches']) && !empty($res['matches'])) {
      foreach ($res['matches'] as $key => $val) {
        $houseid[$key] = $val['id'];
      }
      $result = $this->select_house_collect($houseid, $type);
      foreach ($houseid as $val) {
        foreach ($result as $value) {
          if ($val == $value['id']) {
            $ress[] = $value;
          }
        }
      }
      $datas = array(
        'total' => $res['total_found'],
        'blacklist' => $ress
      );
    } else {
      $datas = array(
        'total' => 0,
        'blacklist' => array()
      );
    }

    return $datas;
  }

  //获取采集房源
  function select_house_collect($data, $type)
  {
    $where_in = array('id', $data);
    if ($type == 'sell') {
      $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where_in' => $where_in), 'dbback');
    } else {
      $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where_in' => $where_in), 'dbback');
    }
    return $result;
  }

  //获取城市id
  function get_city($city)
  {
    $where = array('spell' => $city);
    $result = $this->get_data(array('form_name' => $this->city, 'where' => $where, 'select' => array('id')), 'dbback');
    return $result;
  }

  //根据城市缩写获取城市ID
  public function collect_city_byab($spell)
  {
    $where = array('spell' => $spell);
    $result = $this->get_data(array('form_name' => $this->city, 'where' => $where), 'dbback');
    return $result[0];
  }

  /**
   * 获取采集的二手房总数量
   * @date      2015/8/4
   * @author       fisher
   */
  function get_new_sell_num($database = 'dbback')
  {
    $count_num = 0;
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $mem_key = $this->_mem_key . 'new_sell_num';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $count_num = $cache['data'];
    } else {
      $time = time() - 86400;
      $where = array('createtime >=' => $time, 'city' => $city['id']);

      $sell_sum = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'select' => array('count(id) as num')), $database);
      $count_num = $sell_sum[0]['num'];

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $count_num), 60);
    }

    return $count_num;
  }

  /**
   * 获取采集的租房总数量
   * @date      2015/8/4
   * @author       fisher
   */
  function get_new_rent_num($database = 'dbback_city')
  {
    $count_num = 0;

    $mem_key = $this->_mem_key . 'new_rent_num';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $count_num = $cache['data'];
    } else {
      $spell = $this->config->item('login_city');
      $city = $this->collect_city_byab($spell);
      $time = time() - 86400;
      $where = array('createtime >=' => $time, 'city' => $city['id']);

      $rent_sum = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'select' => array('count(id) as num')), $database);
      $count_num = $rent_sum[0]['num'];

      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $count_num), 60);
    }

    return $count_num;
  }

  /**
   * 获取采集的二手房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_sell_num($where = array(), $like = array(), $or_like = array(), $database = 'dbback', $or_like_2 = array(), $or_like_3 = array())
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $sell_sum = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'like' => $like, 'or_like' => $or_like, 'or_like_2' => $or_like_2, 'or_like_3' => $or_like_3, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }

  /**
   * 获取采集的二手房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_sell_num_set($where = '', $database = 'dbback')
  {
    $sell_sum = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }

  /**
   * 获取采集的二手房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_rent_num_set($where = '', $database = 'dbback')
  {
    $sell_sum = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'select' => array('count(*) as num')), $database);
    return $sell_sum[0]['num'];
  }


  /**
   * 获取采集的租房总数量
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_rent_num($where = array(), $like = array(), $or_like = array(), $database = 'dbback', $or_like_2 = array(), $or_like_3 = array())
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $rent_num = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'like' => $like, 'or_like' => $or_like, 'or_like_2' => $or_like_2, 'or_like_3' => $or_like_3, 'select' => array('count(*) as num')), $database);
    return $rent_num[0]['num'];
  }


  /**
   * 获取采集的二手房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_sell($where = array(), $where_in = array(), $like = array(), $or_like = array(), $order_by = '', $offset = 0, $limit = 10, $database = 'dbback', $or_like_2 = array(), $or_like_3 = array())
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'or_like' => $or_like, 'or_like_2' => $or_like_2, 'or_like_3' => $or_like_3, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 获取采集的二手房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_sell_index($where = array(), $order_by = '', $offset = 0, $limit = 10, $database = 'dbback')
  {
    $mem_key = $this->_mem_key . 'sell_house_index_60';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $result = $cache['data'];
    } else {
      $spell = $this->config->item('login_city');
      $city = $this->collect_city_byab($spell);
      $where['city'] = $city['id'];
      $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $result), 60);
    }
    return $result;
  }

  /**
   * 根据订阅条件获取采集的二手房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_sell_set($where = '', $order_by = '', $offset = 0, $limit = 10, $database = 'dbback')
  {
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 根据订阅条件获取采集的二手房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_rent_set($where = '', $order_by = '', $offset = 0, $limit = 10, $database = 'dbback')
  {
    $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 根据时间排序获取采集的二手房房源信息(边框底部滚动)
   * @date      2015-03-26
   * @author       angel_in_us
   */
  function get_house_sell_orderby($offset = 0, $limit = 3, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'order_by' => 'createtime', 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 获取最近三天的采集的二手房房源信息
   * @date      2015-03-25
   * @author       angel_in_us
   */
  function get_recent_hosue_num()
  {
    $this->dbselect('dbback_city');
    //出售房源
    $sell_sql = "SELECT COUNT(*) as sell_num FROM " . $this->sell_house_collect . " where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($sell_sql);
    $result_arr = $query->result();
    $sell_num = $result_arr[0]->sell_num;
    //出租房源
    $rent_sql = "SELECT COUNT(*) as rent_num FROM " . $this->rent_house_collect . " where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($rent_sql);
    $result_arr = $query->result();
    $rent_num = $result_arr[0]->rent_num;
    return $sell_num + $rent_num;
  }

  /**
   * 获取最近七天被浏览的房源数量
   * @date      2015-03-25
   * @author       angel_in_us
   */
  function get_recent_brower_hosue_num()
  {
    $this->dbselect('dbback_city');
    $sql = "SELECT COUNT(distinct house_id) as num FROM agent_house_judge where `createtime` > " . strtotime('-3 day');
    $query = $this->db->query($sql);
    $result_arr = $query->result();
    $num = $result_arr[0]->num;
    return $num;
  }

  /**
   * 获取查看记录
   * @date      2015-03-25
   * @author       angel_in_us
   */
  function get_view_house_query($sql = '')
  {
    $this->dbselect('dbback_city');
    if (!empty($sql)) {
      $query = $this->db->query($sql);
      $result_arr = $query->result_array();
      return $result_arr;
    } else {
      return false;
    }
  }

  /**
   * 获取采集的租房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_rent($where = array(), $where_in = array(), $like = array(), $or_like = array(), $order_by = '', $offset = 0, $limit = 0, $database = 'dbback', $or_like_2 = array(), $or_like_3 = array())
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'where_in' => $where_in, 'like' => $like, 'or_like' => $or_like, 'or_like_2' => $or_like_2, 'or_like_3' => $or_like_3, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 获取采集的租房房源信息
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_house_rent_index($where = array(), $order_by = '', $offset = 0, $limit = 0, $database = 'dbback')
  {
    $mem_key = $this->_mem_key . 'rent_house_index_60';
    $cache = $this->mc->get($mem_key);
    if (isset($cache['is_ok']) && $cache['is_ok'] == 1) {
      $result = $cache['data'];
    } else {
      $spell = $this->config->item('login_city');
      $city = $this->collect_city_byab($spell);
      $where['city'] = $city['id'];
      $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
      $this->mc->add($mem_key, array('is_ok' => 1, 'data' => $result), 60);
    }
    return $result;
  }

  /**
   * 根据房源id来查询详细房源信息
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_housesell_byid($where = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->sell_house_collect, 'where' => $where), $database);
    return $result;
  }

  /**
   * 根据房源 house_id 来查询 agent_house_judge 表中 该房源被查看的次数
   * @date      2015-06-14
   * @author  angel_in_us
   */
  function get_readtimes_byid($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }

  /**
   * 判断该号码是不是已经被举报过了
   * @date      2015-06-14
   * @author  angel_in_us
   */
  function check_reprot_tel($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_reportlist, 'where' => $where), $database);
    return $result;
  }


  /**
   * 根据房源id来查询详细房源信息
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_houserent_byid($where = array(), $database = 'dbback')
  {
    $spell = $this->config->item('login_city');
    $city = $this->collect_city_byab($spell);
    $where['city'] = $city['id'];
    $result = $this->get_data(array('form_name' => $this->rent_house_collect, 'where' => $where), $database);
    return $result;
  }

  /**
   * 根据经纪人编号broker_id 查询经纪人查看房源情况
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function get_agent_house($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }


  /**
   * 把经纪人已查看的房源插入到  agent_house_judge 表中
   */
  public function add_agent_house($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->agent_house_judge);
    return $result;
  }

  /**
   * 收藏表添加
   */
  public function add_collect_house($data = array(), $database = 'db_city', $form_name = '')
  {
    $result = $this->add_data($data, $database, $this->collect_house_collection);
    return $result;
  }


  /**
   * 根据房源house_id 和 经纪人broker_id 来查询 agent_house_judge 表中是否已存在
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function check_agent_house($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->agent_house_judge, 'where' => $where), $database);
    return $result;
  }

  /**
   * 收藏表判断
   * @date      2015-01-08
   * @author       angel_in_us
   */
  function check_collect_house($where = array(), $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->collect_house_collection, 'where' => $where), $database);
    return $result;
  }

  /**
   * 获得收藏表
   * @date      2014-12-28
   * @author       angel_in_us
   */
  function get_collect_house($where = array(), $order_by = '', $offset = 0, $limit = 10, $database = 'dbback_city')
  {
    $result = $this->get_data(array('form_name' => $this->collect_house_collection, 'where' => $where, 'order_by' => $order_by, 'offset' => $offset, 'limit' => $limit), $database);
    return $result;
  }

  /**
   * 收藏表修改
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function update_collect_house($where = array(), $data = array(), $database = 'db_city')
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->collect_house_collection);
    return $result;
  }


  /**
   * 把被举报的中介信息插入到 agent_reportlist
   */
  public function agent_reportlist($data = array(), $database = 'db_city', $form_name = '')
  {
    $agent_reportlist = $this->add_data($data, $database, $this->agent_reportlist);
    return $agent_reportlist;
  }

  /**
   * 采集录入成功的房源 插入 rent_house_sub "出租房源_附表"
   */
  public function add_rent_house_sub($house_id, $cid, $database = 'db_city')
  {
    $data = array('id' => $house_id, 'collect_id' => $cid);
    $result = $this->add_data($data, $database, $this->rent_house_sub);
    return $result;
  }

  public function add_sell_house_sub($house_id, $cid, $database = 'db_city')
  {
    $data = array('id' => $house_id, 'collect_id' => $cid);
    $result = $this->add_data($data, $database, $this->sell_house_sub);
    return $result;
  }

  /**
   * 我的采集里，成功录入房源后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_input 字段值为 1
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function change_house_status_byid($house_id, $broker_id, $tbl_name, $database = 'db_city')
  {
    $where = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'tbl_name' => $tbl_name
    );
    $data = array('is_input' => 1);
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }

  /**
   * 我的采集里，成功录入房源后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_input 字段值为 1
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function change_house_contact_byid($house_id, $broker_id, $tbl_name, $database = 'db_city')
  {
    $where = array(
      'house_id' => $house_id,
      'broker_id' => $broker_id,
      'tbl_name' => $tbl_name
    );
    $data = array('is_contact' => 1);
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }

  /**
   * 采集管理-已查看列表，点击删除按钮后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_del 字段值为 1
   * @date      2015-08-07
   * @author       angel_in_us
   */
  function change_del_status($where, $data)
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }


  /**
   * 采集管理，经纪人选择标记联系后，根据房源编号 house_id 、经纪人编号 broker_id 和 房源类型 tbl_name 改变数据表 agent_house_judge 中的 is_contact 字段值为 0
   * @date      2015-04-02
   * @author       angel_in_us
   */
  function update_contact_status($where = array(), $data = array(), $database = 'db_city')
  {
    $result = $this->modify_data($where, $data, $database = 'db_city', $form_name = $this->agent_house_judge);
    return $result;
  }

  /**
   * 采集管理，订阅修改
   */
  function update_collect_set_byid($id = 0, $data = array(), $database = 'db_city')
  {
    $result = false;
    if ($id > 0) {
      $where_cond = array(
        'id' => $id
      );
      $result = $this->modify_data($where_cond, $data, $database = 'db_city', $form_name = $this->collect_set);
    }
    return $result;
  }

  /**
   * 获取已保存的搜索条件的条数
   * @date      2015-06-16
   * @author       lujun
   */
  function get_search_num($id, $type)
  {
    $where = "broker_id = " . $id . " and type = " . "'$type'";
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['num'];
  }

  /**
   * 保存搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function save_search($param)
  {
    if ($this->db_city->insert('my_search', $param)) {
      return $this->db_city->insert_id();
    }
    return 0;
  }

  /**
   * 获取经纪人已保存的搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function get_my_search($id, $type)
  {
    $where = "broker_id = " . $id . " and type = " . "'$type'";
    $this->dbback_city->select('*');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->result_array();
    return $result;
  }

  /**
   * 根据ID获取搜索条件的内容
   * @date      2015-06-16
   * @author       lujun
   */
  function get_search_info_by_id($id)
  {
    $this->dbback_city->select('*');
    $this->dbback_city->from('my_search');
    $this->dbback_city->where('id', $id);
    $result = $this->dbback_city->get()->row_array();
    return $result;
  }

  /**
   * 删除保存的搜索条件
   * @date      2015-06-16
   * @author       lujun
   */
  function del_my_search_by_id($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->delete('my_search');
    $num = $this->db_city->affected_rows();
    return $num;
  }

  function get_sell_house_collect_history($id, $database = 'dbback')
  {
    $where = array('house_id' => $id);
    $result = $this->get_data(array('form_name' => $this->sell_house_collect_history, 'where' => $where, 'order_by' => 'oldtime', 'limit' => 3), $database);
    return $result;
  }

  function get_rent_house_collect_history($id, $database = 'dbback')
  {
    $where = array('house_id' => $id);
    $result = $this->get_data(array('form_name' => $this->rent_house_collect_history, 'where' => $where, 'order_by' => 'oldtime', 'limit' => 3), $database);
    return $result;
  }
  //****************************************检查数据start**************************************
  /**
   * 随机生成一个ip
   */
  public function getRandIp()
  {
    $iparr = array("113.117.95.101", "113.44.170.48", "116.243.21.64", "183.138.5.17", "117.140.62.5", "14.126.175.253", "221.228.240.94", "123.98.12.148", "14.126.175.253", "121.32.201.161", "14.126.175.253", "42.96.202.45", "183.38.234.172", "121.8.6.30", "58.55.99.97", "121.228.144.218", "125.113.149.27", "223.64.60.173", "42.185.98.138", "113.214.13.1", "42.96.202.45", "125.116.26.20", "183.130.56.105", "125.127.111.131", "42.62.48.230", "172.16.10.2", "182.118.23.7", "183.138.5.17", "39.180.63.243", "121.32.202.254", "120.193.157.158", "121.14.138.50", "221.228.240.94", "110.87.67.216", "140.206.201.226", "111.112.74.150", "211.141.204.105", "14.29.125.148", "121.32.202.254", "14.145.254.67", "222.245.218.145", "121.8.6.30", "163.179.49.6", "219.139.29.175", "116.243.21.64", "58.246.242.14", "123.98.12.148", "122.13.132.215", "119.188.2.54", "36.63.192.186", "121.12.128.98", "117.140.62.5", "58.61.29.233", "115.172.110.186", "183.138.5.17", "223.64.60.173", "219.128.149.178", "120.198.230.67", "58.68.246.12", "121.12.128.98", "114.243.46.221", "183.130.56.105", "117.27.186.127", "121.226.196.37", "121.228.144.218", "14.126.175.253", "171.221.126.21", "114.243.46.221", "221.228.240.94", "121.32.201.161", "110.87.67.216", "180.169.129.226", "14.126.175.253", "14.126.175.253", "121.12.128.98", "119.79.121.68", "125.116.26.20", "119.39.10.20", "221.228.240.94", "183.138.5.17", "14.126.175.253", "113.44.170.48", "114.61.47.225", "211.141.204.105", "218.107.217.70", "124.174.152.90", "211.141.204.105", "39.180.63.243", "14.126.175.253", "163.179.49.6", "120.213.15.145", "140.206.201.226", "121.8.6.30", "122.69.158.44", "222.245.218.145", "183.138.5.17", "42.121.105.155", "183.146.38.111", "221.237.156.54", "42.185.98.138", "183.138.5.17", "110.87.67.216", "113.12.30.170", "111.1.36.25", "183.138.5.17", "14.126.175.253", "140.206.201.226", "123.98.12.148", "117.27.186.127", "219.139.29.175", "121.12.128.98", "36.249.78.175", "183.208.141.183", "114.243.46.221", "183.130.56.105", "221.228.240.94", "221.176.14.78", "114.243.46.221", "122.4.232.218", "121.226.196.37", "58.68.246.12", "39.180.63.243", "117.15.70.46", "171.221.126.21", "125.127.111.131", "121.12.128.98", "140.206.86.68", "119.142.227.162", "120.213.15.145", "180.153.32.93", "123.138.206.55", "121.12.128.98", "125.127.111.131", "223.64.60.173", "123.151.136.66", "58.215.142.208", "183.138.5.17", "120.193.157.158", "122.143.3.68", "124.174.152.90", "210.14.152.91", "221.197.158.84", "110.87.67.216", "211.141.204.105", "115.172.110.186", "121.228.144.218", "123.98.12.148", "183.208.141.183", "183.138.5.17", "222.245.218.145", "14.126.175.253", "219.139.29.175", "125.73.184.24", "123.160.138.40", "119.39.10.20", "211.141.204.105", "183.38.234.172", "42.185.98.138", "14.126.175.253", "121.32.203.253", "125.113.168.65", "14.126.175.253", "14.126.175.253", "113.44.170.48", "183.207.224.17", "180.169.129.226", "113.12.30.170", "111.1.36.24", "14.126.175.253", "116.243.21.64", "27.156.0.10", "39.180.63.243", "125.116.26.20", "122.69.158.44", "110.87.67.216", "123.150.196.116", "125.127.111.131", "111.1.36.27", "58.61.29.233", "121.12.128.98", "183.138.5.17", "120.213.15.145", "183.130.56.105", "223.64.60.173", "121.226.196.37", "42.185.98.138", "117.27.186.127", "39.190.87.47", "183.138.5.17", "183.207.224.21", "171.221.126.21", "117.140.62.5", "42.121.105.155", "121.12.128.98", "113.214.13.1", "202.118.10.100", "123.98.12.148", "121.32.201.161", "58.215.142.208", "117.66.13.26", "125.127.111.131", "222.245.218.145", "37.239.46.42", "125.113.149.27", "123.160.138.40", "183.138.5.17", "121.228.144.218", "122.4.232.218", "1.202.15.102", "121.32.203.71", "221.197.158.84", "119.79.121.68", "120.202.249.194", "124.174.152.90", "125.113.168.65", "58.246.242.14", "58.61.29.233", "117.27.186.127", "114.243.46.221", "121.32.203.253", "36.63.192.186", "14.126.175.253", "122.69.158.44", "58.55.99.97", "140.206.201.226", "125.113.168.65", "113.12.30.170", "183.208.141.183", "114.61.47.225", "119.142.227.162", "49.122.70.23", "221.228.240.94", "211.141.204.105", "223.64.60.173", "121.12.128.98", "219.155.95.229", "125.127.111.131", "180.169.129.226", "112.112.11.82", "183.130.56.105", "223.211.43.9", "14.145.254.67", "113.117.95.101", "36.249.42.179", "114.243.46.221", "117.140.62.5", "42.185.98.138", "125.127.111.131", "221.10.102.199", "58.53.150.13", "119.39.10.20", "114.226.221.21", "171.221.126.21", "183.207.224.21", "121.32.201.161", "125.113.149.27", "183.138.5.17", "27.156.0.10", "106.38.203.45", "115.172.110.186", "222.245.218.145", "211.141.204.105", "183.138.5.17", "42.185.98.138", "111.112.74.150", "124.174.152.90", "123.98.12.148", "119.142.227.162", "27.151.193.153", "121.12.128.98", "123.151.136.18", "125.113.168.65", "219.155.95.229", "120.33.62.58", "221.11.234.10", "140.206.201.226", "117.27.186.127", "211.141.204.105", "223.211.43.9", "219.155.95.229", "121.12.128.98", "117.27.186.127", "183.167.179.200", "111.1.36.6", "223.64.60.173", "183.130.56.105", "120.198.230.67", "121.228.144.218", "42.185.98.138", "114.243.46.221", "42.121.28.111", "183.38.234.172", "140.206.201.226", "183.208.141.183", "122.69.158.44", "114.243.46.221", "58.61.29.233", "127.0.0.1", "58.53.150.13", "113.117.95.101", "49.70.240.77", "180.169.129.226", "120.193.157.158", "210.14.152.91", "218.207.195.206", "125.113.149.27", "119.188.2.54", "119.188.2.54", "183.138.5.17", "125.113.168.65", "121.12.128.98", "14.126.175.253", "117.140.62.5", "115.172.110.186", "121.226.196.37", "183.138.5.17", "219.155.95.229", "123.98.12.148", "121.12.128.98", "121.32.201.161", "113.12.30.170", "14.126.175.253", "113.44.170.48", "123.160.138.40", "120.33.62.58", "220.202.129.210", "14.126.175.253", "114.61.47.225", "116.243.21.64", "223.211.43.9", "121.8.6.30", "221.197.158.84", "58.55.99.97", "14.145.254.67", "183.138.5.17", "221.237.156.54", "183.138.5.17", "219.128.149.178", "14.124.180.211", "125.113.168.65", "113.75.170.249", "183.138.5.17", "121.32.201.161", "121.32.203.71", "1.85.79.98", "180.169.129.226", "121.12.128.98", "119.39.10.20", "183.38.234.172", "125.113.168.65", "183.138.5.17", "125.127.111.131", "119.79.121.68", "117.140.62.5", "14.126.175.253", "121.8.6.30", "140.206.201.226", "42.96.202.45", "121.12.128.98", "123.98.12.148", "211.141.204.105", "219.155.95.229", "183.130.56.105", "121.228.144.218", "127.0.0.1", "120.202.249.201", "14.126.175.253", "42.62.48.230", "124.174.152.90", "121.12.128.98", "183.138.5.17", "183.138.5.17", "115.172.110.186", "121.12.128.98", "111.1.36.25", "114.61.47.225", "14.126.175.253", "183.138.5.17", "183.207.224.21", "113.75.170.249", "113.44.170.48", "221.228.240.94", "114.243.46.221", "221.197.158.84", "219.139.29.175", "140.206.201.226", "121.8.6.30", "125.116.26.20", "171.221.126.21", "183.208.141.183", "125.127.111.131", "42.185.98.138", "58.61.29.233", "121.226.196.37", "140.206.201.226", "14.126.175.253", "218.207.195.206", "27.156.0.10", "183.38.234.172", "125.127.111.131", "211.138.121.38", "223.64.60.173", "120.193.157.158", "49.70.240.77", "123.98.12.148", "183.130.56.105", "27.156.0.10", "117.140.62.5", "183.23.143.186", "117.27.186.127", "183.138.5.17", "222.245.218.145", "121.12.128.98", "42.121.105.155", "121.12.128.98", "210.14.152.91", "14.126.175.253", "113.75.170.249", "211.138.121.37", "183.138.5.17", "219.139.29.175", "121.8.6.30", "113.12.30.170", "114.243.46.221", "119.142.227.162", "221.228.240.94", "121.226.196.37", "114.243.46.221", "111.112.74.150", "122.69.158.44", "125.127.111.131", "183.138.5.17", "36.249.78.175", "113.44.170.48", "219.128.149.178", "14.126.175.253", "220.202.129.215", "171.221.126.21", "117.15.70.46", "211.141.204.105", "183.207.224.49", "125.127.111.131", "123.151.136.66", "223.211.43.9", "106.38.203.15", "183.207.229.136", "183.130.56.105", "183.138.5.17", "119.39.10.20", "140.206.201.226", "14.126.175.253", "125.116.26.20", "221.228.240.94", "115.172.110.186", "119.79.121.68", "39.180.63.243", "27.156.0.10", "123.150.214.75", "117.27.186.127", "183.138.5.17", "183.138.5.17", "211.141.204.105", "42.185.98.138", "123.160.138.40", "116.243.21.64", "222.87.129.29", "223.65.143.95", "223.64.60.173", "140.206.201.226", "127.0.0.1", "140.206.201.226", "183.57.78.99", "121.228.144.218", "125.73.184.24", "120.198.230.9", "120.198.230.8", "183.207.224.49", "183.208.141.183", "222.245.218.145", "58.61.29.233", "221.228.240.94", "123.98.12.148", "117.140.62.5", "183.138.5.17", "36.63.192.186", "223.240.91.86", "121.226.196.37", "183.138.5.17", "183.138.5.17", "183.146.38.111", "125.113.168.65", "183.130.56.105", "183.138.5.17", "124.238.238.50", "125.113.149.27", "113.44.170.48", "42.121.105.191", "219.139.29.175", "117.27.186.127", "14.126.175.253", "221.237.156.54", "117.15.70.46", "183.70.229.218", "117.66.13.26", "110.87.67.216", "115.205.134.179", "180.169.129.226", "123.160.138.40", "39.180.63.243", "117.27.186.127", "183.138.5.17", "183.38.234.172", "49.70.240.77", "221.228.240.94", "119.142.227.162", "113.117.95.101", "27.151.193.153", "183.138.5.17", "220.202.129.212", "42.185.98.138", "171.221.126.21", "223.64.60.173", "122.69.158.44", "14.124.180.211", "113.75.170.249", "123.98.12.148", "120.33.62.58", "140.206.201.226", "119.39.10.20", "58.55.99.97", "115.172.110.186", "218.204.131.250", "120.193.157.158", "114.243.46.221", "221.197.158.84", "42.185.98.138", "140.206.201.226", "125.113.149.27", "125.113.149.27", "113.12.30.170", "183.57.78.99", "60.184.55.240", "124.174.152.90", "121.12.128.98", "125.116.26.20", "125.113.168.65", "222.245.218.145", "125.64.82.170", "120.198.230.67", "180.169.129.226", "219.155.95.229", "117.66.13.26", "121.32.203.71", "183.130.56.105", "140.206.201.226", "39.180.63.243", "119.79.121.68", "172.16.8.2", "221.176.14.78", "36.249.78.175", "211.141.204.105", "61.50.245.133", "113.117.95.101", "183.138.5.17", "183.138.5.17", "123.98.12.148", "219.155.95.229", "183.138.5.17", "121.12.128.98", "211.141.204.105", "121.12.128.98", "36.63.192.186", "14.126.175.253", "125.113.168.65", "125.116.26.20", "222.245.218.145", "221.197.158.84", "125.113.149.27", "183.146.38.111", "183.208.141.183", "114.226.221.21", "123.160.138.40", "14.126.175.253", "114.243.46.221", "42.62.48.230", "39.190.87.47", "140.206.201.226", "202.118.10.101", "223.64.60.173", "14.126.175.253", "58.55.99.97", "120.33.62.58", "113.12.30.170", "183.57.78.99", "221.228.240.94", "122.69.158.44", "117.140.62.5", "125.114.168.103", "27.156.0.10", "180.169.129.226", "140.206.201.226", "121.228.144.218", "27.156.0.10", "140.206.201.226", "125.113.168.65", "119.39.10.20", "58.68.246.12", "124.174.152.90", "113.44.170.48", "121.12.128.98", "211.141.204.105", "211.141.204.105", "121.32.201.161", "219.155.95.229", "116.243.21.64", "119.142.227.162", "121.226.196.37", "183.130.56.105", "123.98.12.148", "183.138.5.17", "125.113.168.65", "219.155.95.229", "121.12.128.98", "42.185.98.138", "222.245.218.145", "42.185.98.138", "114.243.46.221", "125.127.111.131", "183.138.5.17", "183.138.5.17", "223.64.60.173", "36.249.42.179", "14.145.254.67", "58.55.99.97", "113.12.30.170", "183.38.234.172", "111.112.74.150", "113.75.170.249", "219.128.149.178", "183.167.179.200", "121.228.144.218", "42.121.105.155", "106.38.203.58", "120.193.157.158", "125.113.168.65", "125.64.82.170", "125.64.82.170", "117.140.62.5", "27.156.0.10", "123.160.138.40", "117.27.186.127", "114.243.46.221", "183.138.5.17", "115.172.110.186", "120.198.230.67", "49.70.240.77", "113.44.170.48", "221.237.156.54", "171.221.126.21", "180.169.129.226", "219.139.29.175", "183.138.5.17", "119.142.227.162", "116.243.21.64", "125.127.111.131", "110.87.67.216", "121.12.128.98", "183.130.56.105", "223.211.43.9", "219.155.95.229", "121.32.200.49", "1.202.15.102", "183.207.224.17", "211.141.204.105", "211.141.204.105", "42.185.98.138", "183.138.5.17", "123.98.12.148", "36.250.166.135", "110.4.24.173", "58.53.150.13", "183.146.38.111", "121.32.200.49", "121.228.144.218", "125.114.168.103", "114.243.46.221", "125.127.111.131", "42.121.105.155", "125.127.111.131", "125.114.168.103", "140.206.201.226", "93.115.8.229", "183.138.5.17", "114.61.47.225", "125.127.111.131", "42.96.202.45", "223.64.60.173", "222.87.129.29", "115.172.110.186", "140.206.201.226", "218.89.170.110", "113.117.95.101", "221.10.102.199", "219.139.29.175", "183.138.5.17", "121.12.128.98", "121.8.6.30", "121.8.6.30", "180.169.129.226", "221.228.240.94", "125.116.26.20", "14.126.175.253", "49.70.240.77", "119.39.10.20", "42.62.48.230", "127.0.0.1", "114.80.136.112", "39.190.87.47", "114.61.47.225", "117.140.62.5", "183.138.5.17", "111.1.36.27", "180.169.129.226", "223.211.43.9", "183.38.234.172", "183.208.141.183", "180.169.129.226", "123.98.12.148", "125.127.111.131", "183.138.5.17", "221.197.158.84", "119.79.121.68", "183.130.56.105", "115.58.128.222", "14.126.175.253", "14.126.175.253", "183.207.224.21", "211.138.121.38", "219.155.95.229", "121.12.128.98", "113.117.95.101", "125.127.111.131", "211.141.204.105", "42.185.98.138", "125.114.168.103", "171.221.126.21", "121.228.144.218", "183.138.5.17", "140.206.201.226", "121.226.196.37", "221.228.240.94", "125.73.184.24", "223.64.60.173", "113.117.95.101", "121.8.6.30", "116.228.55.217", "211.141.204.105", "113.75.170.249", "183.138.5.17", "219.128.149.178", "111.1.36.26", "121.12.128.98", "113.44.170.48", "117.27.186.127", "116.243.21.64", "113.214.13.1", "119.39.10.20", "117.140.62.5", "211.138.121.37", "180.169.129.226", "183.38.234.172", "49.89.205.98", "182.118.23.7", "211.138.121.37", "183.57.78.99", "114.243.46.221", "183.130.56.105", "219.139.29.175", "123.98.12.148", "219.155.95.229", "123.98.12.148", "117.27.186.127", "123.98.12.148", "113.117.95.101", "218.28.13.235", "121.32.200.49", "125.113.149.27", "121.12.128.98", "218.28.13.235", "120.198.230.67", "221.228.240.94", "121.226.196.37", "114.243.46.221", "121.228.144.218", "220.202.129.215", "42.95.190.30", "117.15.70.46", "36.249.78.175", "219.155.95.229", "183.208.141.183", "113.44.170.48", "121.12.128.98", "125.113.149.27", "113.12.30.170", "221.228.240.94", "36.249.42.179", "122.69.158.44", "117.27.186.127", "218.94.82.118", "123.160.138.40", "221.197.158.84", "42.62.48.230", "117.140.62.5", "27.156.0.10", "222.245.218.145", "223.64.60.173", "117.27.186.127", "125.73.184.24", "113.117.95.101", "27.156.0.10", "140.206.201.226", "183.146.38.111", "171.221.126.21", "123.98.12.148", "180.169.129.226", "121.32.202.254", "183.138.5.17", "125.64.82.170", "183.138.5.17", "115.172.110.186", "1.85.79.98", "125.113.168.65", "58.68.246.12", "183.138.5.17", "119.79.121.68", "121.226.196.37", "211.141.204.105", "39.180.63.243", "120.33.62.58", "1.85.49.74", "140.206.201.226", "42.95.190.30", "183.70.229.218", "221.11.234.10", "180.169.129.226", "120.198.230.7", "125.113.149.27", "183.138.5.17", "117.15.70.46", "119.46.110.17", "122.69.158.44", "125.116.26.20", "58.48.109.114", "117.66.13.26", "42.121.28.111", "116.243.21.64", "221.237.156.54", "140.206.201.226", "183.38.234.172", "58.55.99.97", "124.174.152.90", "42.185.98.138", "123.160.138.40", "117.27.186.127", "223.64.60.173", "58.215.142.208", "125.113.168.65", "27.156.0.10", "36.249.42.179", "222.245.218.145", "221.228.240.94", "117.27.186.127", "121.12.128.98", "113.75.170.249", "115.172.110.186", "39.190.87.47", "183.208.141.183", "123.98.12.148", "49.70.240.77", "117.140.62.5", "39.180.63.243", "125.64.82.170", "183.57.78.99", "42.95.190.30", "119.142.227.162", "111.112.74.150", "183.146.38.111", "183.138.5.17", "116.243.21.64", "125.116.26.20", "120.33.62.58", "119.39.10.20", "183.138.5.17", "183.138.5.17", "221.228.240.94", "114.243.46.221", "183.130.56.105", "121.32.201.161", "42.185.98.138", "125.113.149.27", "111.1.36.24", "36.249.78.175", "125.114.168.103", "42.185.98.138", "113.120.62.43", "223.64.60.173", "211.152.50.70", "125.113.168.65", "121.32.200.49", "121.199.30.110", "180.169.129.226", "183.138.5.17", "42.121.105.155", "123.98.12.148", "183.138.5.17", "121.32.200.49", "110.87.67.216", "222.245.218.145", "119.142.227.162", "117.140.62.5", "221.197.158.84", "121.228.144.218", "113.44.170.48", "58.55.99.97", "123.160.138.40", "58.48.109.114", "119.79.121.68", "36.249.42.179", "39.180.63.243", "211.138.121.37", "183.138.5.17", "117.66.13.26", "114.226.221.21", "124.174.152.90", "120.33.62.58", "183.130.56.105", "183.57.78.99", "211.141.204.105", "183.138.5.17", "113.12.30.170", "221.228.240.94", "180.169.129.226", "125.113.168.65", "49.70.240.77", "183.138.5.17", "42.121.105.155", "42.95.190.30", "125.64.82.170", "123.98.12.148", "113.44.170.48", "221.197.158.84", "121.228.144.218", "222.245.218.145", "114.243.46.221", "119.39.10.20", "171.221.126.21", "113.75.170.249", "125.116.26.20", "39.190.87.47", "123.160.138.40", "125.127.111.131", "121.14.138.50", "125.127.111.131", "219.155.95.229", "183.208.141.183", "121.226.196.37", "125.73.184.24", "58.55.99.97", "122.69.158.44", "125.113.168.65", "219.155.95.229", "117.27.186.127", "122.143.3.68", "122.143.3.68", "110.87.67.216", "140.206.201.226", "219.139.29.175", "220.202.129.213", "115.172.110.186", "42.185.98.138", "58.215.142.208", "218.107.217.70", "125.113.168.65", "183.146.38.111", "223.64.60.173", "219.128.149.178", "183.130.56.105", "211.138.121.37", "183.138.5.17", "211.141.204.105", "223.211.43.9", "114.243.46.221", "140.206.201.226", "121.228.144.218", "113.12.30.170", "42.95.190.30", "183.57.78.99", "125.127.111.131", "119.39.10.20", "117.140.62.5", "42.185.98.138", "125.116.26.20", "123.98.12.148", "125.114.168.103", "27.156.0.10", "42.96.202.45", "210.14.152.91", "120.198.230.30", "211.141.204.105", "211.138.121.37", "183.138.5.17", "222.245.218.145", "219.139.29.175", "113.44.170.48", "183.138.5.17", "121.32.200.49", "119.142.227.162", "221.237.156.54", "183.208.141.183", "114.243.46.221", "183.138.5.17", "211.138.121.38", "125.127.111.131", "27.156.0.10", "110.4.24.173", "61.50.245.133", "223.64.60.173", "58.68.246.12", "183.130.56.105", "124.116.245.89", "113.117.95.101", "125.113.168.65", "211.138.121.36", "123.98.12.148", "183.57.78.99", "42.185.98.138", "171.221.126.21", "42.185.98.138", "219.155.95.229", "219.128.149.178", "183.138.5.17", "14.124.180.211", "49.89.205.98", "110.84.92.120", "124.174.152.90", "211.141.204.105", "58.68.246.12", "42.95.190.30", "183.138.5.17", "219.155.95.229", "27.156.0.10", "58.48.109.114", "125.114.168.103", "121.226.196.37", "183.138.5.17", "125.64.82.170", "121.228.144.218", "183.138.5.17", "125.127.111.131", "183.138.5.17", "210.14.138.102", "172.16.8.2", "211.141.204.105", "183.138.5.17", "14.18.243.139", "110.87.67.216", "117.27.186.127", "113.117.95.101", "119.142.227.162", "123.98.12.148", "121.32.200.49", "121.32.200.49", "171.221.126.21", "114.243.46.221", "113.75.170.249", "223.64.60.173", "119.79.121.68", "221.228.240.94", "119.39.10.20", "183.138.5.17", "125.73.184.24", "85.90.222.213", "183.138.5.17", "42.185.98.138", "113.44.170.48", "36.249.78.175", "123.160.138.40", "210.14.152.91", "114.243.46.221", "183.130.56.105", "140.206.201.226", "27.156.0.10", "42.95.190.30", "116.243.21.64", "140.206.201.226", "183.167.179.200", "117.140.62.5", "183.146.38.111", "219.155.95.229", "223.211.43.9", "125.127.111.131", "115.172.110.186", "183.138.5.17", "125.114.168.103", "58.48.109.114", "49.70.240.77", "110.87.67.216", "211.141.204.105", "180.169.129.226", "113.117.95.101", "211.141.204.105", "211.138.121.37", "125.113.149.27", "221.197.158.84", "121.228.144.218", "121.226.196.37", "120.198.230.67", "58.53.150.13", "119.79.121.68", "183.208.141.183", "211.138.121.36", "119.39.10.20", "117.27.186.127", "113.12.30.170", "211.138.121.36", "117.15.70.46", "122.143.3.68", "223.64.60.173", "221.228.240.94", "114.243.46.221", "123.160.138.40", "171.221.126.21", "42.121.105.155", "113.120.62.43", "183.130.56.105", "36.249.42.179", "183.167.179.200", "14.29.81.20", "117.140.62.5", "223.211.43.9", "116.243.21.64", "42.95.190.30", "110.84.92.120", "106.38.203.45", "120.198.230.67", "183.138.5.17", "114.61.47.225", "113.117.95.101", "120.33.62.58", "219.155.95.229", "58.55.99.97", "140.206.201.226", "113.72.183.237", "221.237.156.54", "117.27.186.127", "121.8.6.30", "121.8.6.30", "125.64.82.170", "211.141.204.105", "183.208.141.183", "221.197.158.84", "223.64.60.173", "125.113.149.27", "221.228.240.94", "14.18.243.139", "117.27.186.127", "116.243.21.64", "183.130.56.105", "114.61.47.225", "123.98.12.148", "183.167.179.200", "122.69.158.44", "117.66.13.26", "110.84.92.120", "42.95.190.30", "125.114.168.103", "125.113.168.65", "39.180.63.243", "183.138.5.17", "115.172.110.186", "42.185.98.138", "125.116.26.20", "140.206.86.68", "140.206.86.68", "113.117.95.101", "123.160.138.40", "211.141.204.105", "183.207.224.21", "183.138.5.17", "183.138.5.17", "117.140.62.5", "183.138.5.17", "114.243.46.221", "49.70.240.77", "113.75.170.249", "36.249.42.179", "183.138.5.17", "183.207.224.19", "183.57.78.99", "125.94.71.182", "125.127.111.131", "27.156.0.10", "222.245.218.145", "223.64.60.173", "183.138.5.17", "183.138.5.17", "140.206.201.226", "211.138.121.38", "119.39.10.20", "220.202.129.212", "111.63.38.145", "125.113.149.27", "183.130.56.105", "221.228.240.94", "180.169.129.226", "117.66.13.26", "180.169.129.226", "114.226.221.21", "127.0.0.1", "113.44.170.48", "36.249.78.175", "27.156.0.10", "171.221.161.20", "183.138.5.17", "123.98.12.148", "218.207.195.206", "120.33.62.58", "111.63.38.147", "113.117.95.101", "115.172.110.186", "140.206.201.226", "125.113.168.65", "171.221.167.140", "180.169.129.226", "58.55.99.97", "39.180.63.243", "121.228.144.218", "218.107.217.70", "218.89.170.110", "119.142.227.162", "122.69.158.44", "223.64.60.173", "123.160.138.40", "211.141.204.105", "114.243.46.221", "183.208.141.183", "119.39.10.20", "183.207.224.51", "1.85.79.98", "125.113.168.65", "117.66.13.26", "219.139.29.175", "183.130.56.105", "140.206.201.226", "111.1.36.27", "42.185.98.138", "117.27.186.127", "140.206.201.226", "123.98.12.148", "183.138.5.17", "183.138.5.17", "183.138.5.17", "49.70.240.77", "183.138.5.17", "222.245.218.145", "114.243.46.221", "183.207.224.21", "125.127.111.131", "171.221.126.21", "117.140.62.5", "27.211.54.71", "183.138.5.17", "58.55.99.97", "125.127.111.131", "27.156.0.10", "113.75.170.249", "121.228.144.218", "219.139.29.175", "113.12.30.170", "183.207.224.19", "124.174.152.90", "113.117.95.101", "113.44.170.48", "121.226.196.37", "125.113.168.65", "223.64.60.173", "36.249.78.175", "211.141.204.105", "180.169.129.226", "58.215.142.208", "183.138.5.17", "111.205.122.222", "1.85.79.98", "123.98.12.148", "221.228.240.94", "119.142.227.162", "183.130.56.105", "183.138.5.17", "114.61.47.225", "219.128.149.178", "171.221.126.21", "125.116.26.20", "183.138.5.17", "114.243.46.221", "127.0.0.1", "117.140.62.5", "42.185.98.138", "119.39.10.20", "222.245.218.145", "113.75.170.249", "183.208.141.183", "114.243.46.221", "49.89.205.98", "125.114.168.103", "106.38.203.45", "180.169.129.226", "120.33.62.58", "27.156.0.10", "125.113.168.65", "219.155.95.229", "115.172.110.186", "183.138.5.17", "171.221.126.21", "42.95.190.30", "124.174.152.90", "210.14.152.91", "125.116.26.20", "183.138.5.17", "42.185.98.138", "183.146.38.111", "223.64.60.173", "183.207.229.136", "223.211.43.9", "117.140.62.5", "123.98.12.148", "222.74.6.10", "119.142.227.162", "113.72.183.237", "42.185.98.138", "117.15.70.46", "125.127.111.131", "211.151.50.179", "222.245.218.145", "219.128.149.178", "218.75.155.242", "218.207.195.206", "140.206.201.226", "183.57.78.99", "125.113.168.65", "123.160.138.40", "180.169.129.226", "121.228.144.218", "114.243.46.221", "219.155.95.229", "117.89.227.59", "140.206.201.226", "113.44.170.48", "183.138.5.17", "125.113.168.65", "127.0.0.1", "121.32.202.254", "221.237.156.54", "210.38.1.84", "114.226.221.21", "183.138.5.17", "183.138.5.17", "42.121.105.155", "111.1.36.26", "117.27.186.127", "120.198.230.67", "222.245.218.145", "183.146.38.111", "42.185.98.138", "125.127.111.131", "113.72.183.237", "183.208.141.183", "183.138.5.17", "114.243.46.221", "171.221.126.21", "113.117.95.101", "121.32.201.161", "183.130.56.105", "171.221.161.20", "42.185.98.138", "223.64.60.173", "221.197.158.84", "221.228.240.94", "113.12.30.170", "220.202.129.210", "123.98.12.148", "117.27.186.127", "183.138.5.17", "123.160.138.40", "125.113.168.65", "218.207.195.206", "220.202.129.213", "183.138.5.17", "183.207.224.21", "116.243.21.64", "115.172.110.186", "111.1.36.24", "119.142.227.162", "171.221.167.140", "219.155.95.229", "117.140.62.5", "125.116.26.20", "58.68.246.12", "113.44.170.48", "183.57.78.99", "183.138.5.17", "125.113.168.65", "116.22.16.173", "183.138.5.17", "58.240.238.212", "121.228.144.218", "49.70.240.77", "125.127.111.131", "119.39.10.20", "114.243.46.221", "121.226.196.37", "42.185.98.138", "113.72.183.237", "58.61.29.233", "182.118.23.7", "140.206.201.226", "183.207.224.19", "112.94.171.116", "123.98.12.148", "110.87.67.216", "221.228.240.94", "221.197.158.84", "27.211.54.71", "183.130.56.105", "140.206.201.226", "42.121.105.155", "42.95.190.30", "58.55.99.97", "115.172.110.186", "183.207.224.18", "223.64.60.173", "113.12.30.170", "117.27.186.127", "183.207.224.51", "113.120.62.43", "113.75.170.249", "183.57.78.99", "1.202.15.102", "183.146.38.111", "36.249.78.175", "123.160.138.40", "121.32.201.161", "219.155.95.229", "124.174.152.90", "211.141.204.105", "42.121.105.155", "58.68.246.12", "120.198.230.67", "106.38.203.7", "121.226.196.37", "171.221.126.21", "127.0.0.1", "117.15.70.46", "42.121.28.111", "49.74.222.217", "119.39.10.20", "117.27.186.127", "180.169.129.226", "140.206.201.226", "27.156.0.10", "123.98.12.148", "124.166.177.220", "116.243.21.64", "121.32.201.161", "58.61.29.233", "218.107.217.70", "42.96.202.45", "39.190.87.47", "49.89.205.98", "140.206.201.226", "125.116.26.20", "113.117.95.101", "42.62.48.230", "183.138.5.17", "58.55.99.97", "117.140.62.5", "111.1.36.6", "183.57.78.99", "114.243.46.221", "183.130.56.105", "221.228.240.94", "117.27.186.127", "39.180.63.243", "221.237.156.54", "42.95.190.30", "183.138.5.17", "140.206.201.226", "223.64.60.173", "121.32.200.49", "219.155.95.229", "183.146.38.111", "183.138.5.17", "123.160.138.40", "121.226.196.37", "121.228.144.218", "116.243.21.64", "183.208.141.183", "180.169.129.226", "120.198.230.7", "115.172.110.186", "116.228.55.217", "113.117.95.101", "127.0.0.1", "113.72.183.237", "58.68.246.12", "180.169.129.226", "110.87.67.216", "140.206.201.226", "113.44.170.48", "211.141.204.105", "117.15.70.46", "36.249.78.175", "42.185.98.138", "14.29.81.20", "223.211.43.9", "27.156.0.10", "119.39.10.20", "183.138.5.17", "120.33.62.58", "125.113.168.65", "183.130.56.105", "211.138.121.38", "39.180.63.243", "121.8.6.30", "223.64.60.173", "49.70.240.77", "218.94.115.131", "140.206.201.226", "119.142.227.162", "211.141.204.105", "42.95.190.30", "140.206.201.226", "117.27.186.127", "117.140.62.5", "36.249.42.179", "114.243.46.221", "121.8.6.30", "221.228.240.94", "115.172.110.186", "113.12.30.170", "219.139.29.175", "183.146.38.111", "112.94.171.116", "114.226.221.21", "1.85.79.98", "113.120.62.43", "58.215.142.208", "124.174.152.90", "180.169.129.226", "183.138.5.17", "39.190.87.47", "58.61.29.233", "113.75.170.249", "180.169.129.226", "121.228.144.218", "140.206.201.226", "119.39.10.20", "58.53.150.13", "223.211.43.9", "121.8.6.30", "183.138.5.17", "49.70.240.77", "113.71.88.147", "123.98.12.148", "123.181.201.26", "125.116.26.20", "125.113.168.65", "121.32.203.253", "117.140.62.5", "110.87.67.216", "223.64.60.173", "119.142.227.162", "183.207.224.49", "125.113.168.65", "120.33.62.58", "211.141.204.105", "125.127.111.131", "183.167.179.200", "1.85.79.98", "219.139.29.175", "171.221.126.21", "42.96.202.45", "27.211.54.71", "113.72.183.237", "39.180.63.243", "120.198.230.67", "113.44.170.48", "113.117.95.101", "42.185.98.138", "183.138.5.17", "183.138.5.17", "42.62.48.230", "117.27.186.127", "127.0.0.1", "58.68.246.12", "219.128.149.178", "117.15.70.46", "117.66.13.26", "211.141.204.105", "110.87.67.216", "113.75.170.249", "210.38.1.84", "223.64.60.173", "180.169.129.226", "42.95.190.30", "123.98.12.148", "114.243.46.221", "121.226.196.37", "183.57.78.99", "183.167.179.200", "210.14.152.91", "171.221.126.21", "121.228.144.218", "123.160.138.40", "221.228.240.94", "211.141.204.105", "120.33.62.58", "58.61.29.233", "125.127.111.131", "1.204.51.16", "183.130.56.105", "116.22.16.173", "121.32.201.161", "183.138.5.17", "58.55.99.97", "119.39.10.20", "180.169.129.226", "42.185.98.138", "125.94.71.182", "115.172.110.186", "113.12.30.170", "117.15.70.46", "119.142.227.162", "220.202.129.211", "171.221.161.20", "140.206.201.226", "5.23.98.130", "123.181.201.26", "27.211.54.71", "58.61.29.233", "222.245.218.145", "223.64.60.173", "117.27.186.127", "113.117.95.101", "42.185.98.138", "221.197.158.84", "183.138.5.17", "123.98.12.148", "183.138.5.17", "183.138.5.17", "14.145.254.67", "211.141.204.105", "117.140.62.5", "117.27.186.127", "218.204.131.250", "111.1.36.163", "140.206.201.226", "124.174.152.90", "183.130.56.105", "113.44.170.48", "1.204.51.16", "125.127.111.131", "171.221.126.21", "183.138.5.17", "183.138.5.17", "42.95.190.30", "140.206.201.226", "211.138.121.38", "58.68.246.12", "180.153.32.93", "210.14.152.91", "117.66.13.26", "221.237.156.54", "183.146.38.111", "125.113.168.65", "183.138.5.17", "223.211.43.9", "125.127.111.131", "114.243.46.221", "123.98.12.148", "36.249.78.175", "221.228.240.94", "223.64.60.173", "111.1.36.26", "117.140.62.5", "211.141.204.105", "117.27.186.127", "222.245.218.145", "221.197.158.84", "180.169.129.226", "42.185.98.138", "183.130.56.105", "183.138.5.17", "180.169.129.226", "113.44.170.48", "121.228.144.218", "112.94.171.116", "114.226.221.21", "211.151.50.179", "125.127.111.131", "113.72.183.237", "140.206.201.226", "39.180.63.243", "183.138.5.17", "171.221.126.21", "110.87.67.216", "114.243.46.221", "125.116.26.20", "183.138.5.17", "116.243.21.64", "113.120.62.43", "117.66.13.26", "210.38.1.84", "125.113.168.65", "125.94.71.182", "58.215.142.208", "221.228.240.94", "117.27.186.127", "223.64.60.173", "140.206.201.226", "115.172.110.186", "123.98.12.148", "114.243.46.221", "119.142.227.162", "219.155.95.229", "183.138.5.17", "119.39.10.20", "183.146.38.111", "183.207.224.21", "123.160.138.40", "58.68.246.12", "114.112.91.135", "183.130.56.105", "42.185.98.138", "58.55.99.97", "14.18.243.155", "117.15.70.46", "36.249.78.175", "1.204.51.16", "110.87.67.216", "42.95.190.30", "210.38.1.84", "113.117.95.101", "42.121.105.155", "222.245.218.145", "113.72.183.237", "117.27.186.127", "58.240.140.95", "140.206.201.226", "202.118.10.101", "124.174.152.90", "121.228.144.218", "27.156.0.10", "183.57.78.99", "42.96.202.45", "121.32.200.49", "221.228.240.94", "116.243.21.64", "183.138.5.17", "180.169.129.226", "183.207.224.51", "218.90.80.131", "183.146.38.111", "110.87.67.216", "58.55.99.97", "121.226.196.37", "123.181.201.26", "220.202.129.210", "36.249.78.175", "49.70.240.77", "183.130.56.105", "14.124.180.211", "123.160.138.40", "222.245.218.145", "125.127.111.131", "180.169.129.226", "14.18.243.139", "183.138.5.17", "210.73.220.18", "113.71.88.147", "42.95.190.30", "219.155.95.229", "223.64.60.173", "140.206.201.226", "125.113.168.65", "1.204.51.16", "117.140.62.5", "183.208.141.183", "114.243.46.221", "113.44.170.48", "183.146.38.111", "116.243.21.64", "123.181.201.26", "221.237.156.54", "221.228.240.94", "27.156.0.10", "1.85.79.98", "113.12.30.170", "123.160.138.40", "112.95.241.76", "123.98.12.148", "119.39.10.20", "210.14.138.102", "121.228.144.218", "121.226.196.37", "39.180.63.243", "119.142.227.162", "183.138.5.17", "180.169.129.226", "42.185.98.138", "117.66.13.26", "58.61.29.233", "223.64.60.173", "183.138.5.17", "183.138.5.17", "49.70.240.77", "183.138.5.17", "125.116.26.20", "117.27.186.127", "120.33.62.58", "183.208.141.183", "116.22.16.173", "211.141.204.105", "219.139.29.175", "113.117.95.101", "202.118.10.100", "121.14.138.50", "114.243.46.221", "1.204.51.16", "112.94.171.116", "114.226.221.21", "116.243.21.64", "221.228.240.94", "113.72.183.237", "125.94.71.182", "123.98.12.148", "119.39.10.20", "42.95.190.30", "49.70.240.77", "183.130.56.105", "27.156.0.10", "171.221.126.21", "121.228.144.218", "121.32.203.71", "125.113.168.65", "110.87.67.216", "117.15.70.46", "113.44.170.48", "14.29.125.148", "183.138.5.17", "219.155.95.229", "60.210.18.11", "183.138.5.17", "183.57.78.99", "121.8.6.30", "219.139.29.175", "120.33.62.58", "219.128.149.178", "113.117.95.101", "123.160.138.40", "125.127.111.131", "1.204.51.16", "123.98.12.148", "113.120.62.43", "121.8.6.30", "210.38.1.84", "119.39.10.20", "113.12.30.170", "125.113.168.65", "42.95.190.30", "117.140.62.5", "120.198.230.67", "123.181.201.26", "117.27.186.127", "183.138.5.17", "183.130.56.105", "221.197.158.84", "183.146.38.111", "183.138.5.17", "119.142.227.162", "180.169.129.226", "125.116.26.20", "116.22.16.173", "125.113.168.65", "110.87.67.216", "223.64.60.173", "14.145.254.67", "140.206.201.226", "123.160.138.40", "113.72.183.237", "111.1.36.26", "1.204.51.16", "36.249.78.175", "119.36.39.69", "222.87.129.29", "113.117.95.101", "42.185.98.138", "140.206.201.226", "58.214.247.158", "119.39.10.20", "171.221.126.21", "111.1.36.27", "117.27.186.127", "211.141.204.105", "125.127.111.131", "117.27.186.127", "117.140.62.5", "121.228.144.218", "125.94.71.182", "221.228.240.94", "125.116.26.20", "124.174.152.90", "117.66.13.26", "58.55.99.97", "119.142.227.162", "110.87.67.216", "58.53.150.13", "183.146.38.111", "112.94.171.116", "183.207.224.17", "114.243.46.221", "113.44.170.48", "123.160.138.40", "1.204.51.16", "120.33.62.58", "113.69.78.157", "180.169.129.226", "221.237.156.54", "114.243.46.221", "36.249.78.175", "183.130.56.105", "39.180.63.243", "221.197.158.84", "171.221.126.21", "211.141.204.105", "42.185.98.138", "117.27.186.127", "117.27.186.127", "113.117.95.101", "218.85.16.151", "223.64.60.173", "140.206.201.226", "110.87.67.216", "117.140.62.5", "140.206.201.226", "183.138.5.17", "125.127.111.131", "182.130.243.128", "125.113.168.65", "14.18.243.139", "123.98.12.148", "113.72.183.237", "113.44.170.48", "114.226.221.21", "114.243.46.221", "42.185.98.138", "117.15.70.46", "183.207.224.49", "1.204.51.16", "222.74.6.10", "114.243.46.221", "180.169.129.226", "42.95.190.30", "120.198.230.67", "121.228.144.218", "123.160.138.40", "117.27.186.127", "116.243.21.64", "223.64.60.173", "183.138.5.17", "211.141.204.105", "180.169.129.226", "183.167.179.200", "58.215.142.208", "221.228.240.94", "117.140.62.5", "117.66.13.26", "171.221.126.21", "182.130.243.128", "113.72.183.237", "183.130.56.105", "36.249.42.179", "123.98.12.148", "42.185.98.138", "119.39.10.20", "211.141.204.105", "1.204.51.16", "182.254.129.123", "125.127.111.131", "39.190.87.47", "119.188.2.54", "42.95.190.30", "183.138.5.17", "113.69.78.229", "39.190.87.47", "39.190.87.47", "222.245.218.145", "183.138.5.17", "125.113.168.65", "125.116.26.20", "113.12.30.170", "183.136.221.6", "42.185.98.138", "14.145.254.67", "223.64.60.173", "111.1.36.23", "114.243.46.221", "113.72.183.237", "183.138.5.17", "183.138.5.17", "121.228.144.218", "183.130.56.105", "117.140.62.5", "140.206.201.226", "115.28.50.204", "211.141.204.105", "183.138.5.17", "211.138.121.38", "123.160.138.40", "125.94.71.182", "219.155.95.229", "58.61.29.233", "211.138.121.37", "123.98.12.148", "61.135.153.22", "125.127.111.131", "49.70.240.77", "116.243.21.64", "58.55.99.97", "183.207.224.19", "222.245.218.145", "211.138.121.38", "113.12.30.170", "221.228.240.94", "1.85.79.98", "210.14.152.91", "202.98.123.126", "1.204.51.16", "113.72.183.237", "140.206.201.226", "125.127.111.131", "111.1.36.6", "14.145.254.67", "183.138.5.17", "183.138.5.17", "27.156.0.10", "112.94.171.116", "223.64.60.173", "113.117.95.101", "113.44.170.48", "140.206.201.226", "39.180.63.243", "114.243.46.221", "58.215.142.208", "122.13.132.215", "221.237.156.54", "117.140.62.5", "121.228.144.218", "117.66.13.26", "119.142.227.162", "113.120.62.43", "117.27.186.127", "125.113.168.65", "183.130.56.105", "39.190.87.47", "27.156.0.10", "119.39.10.20", "221.197.158.84", "58.61.29.233", "123.98.12.148", "211.141.204.105", "42.185.98.138", "222.245.218.145", "116.243.21.64", "183.207.224.19", "123.160.138.40", "183.57.78.99", "1.204.51.16", "42.121.105.155", "125.113.168.65", "221.228.240.94", "223.64.60.173", "42.96.202.45", "117.15.70.46", "120.33.62.58", "140.206.201.226", "114.221.43.225", "125.116.26.20", "42.95.190.30", "114.226.221.21", "211.141.204.105", "221.197.158.84", "219.139.29.175", "123.98.12.148", "121.8.6.30", "183.138.5.17", "58.61.29.233", "121.8.6.30", "183.138.5.17", "219.155.95.229", "114.243.46.221", "119.39.10.20", "36.249.78.175", "27.156.0.10", "42.62.48.230", "123.181.201.26", "42.96.202.45", "113.69.78.229", "113.12.30.170", "183.130.56.105", "180.111.165.192", "183.57.78.99", "1.204.51.16", "183.148.1.204", "222.74.6.10", "117.140.62.5", "42.185.98.138", "116.243.21.64", "180.169.129.226", "171.221.126.21", "222.245.218.145", "211.138.121.37", "117.27.186.127", "182.130.243.128", "223.64.60.173", "140.206.201.226", "120.33.62.58", "183.138.5.17", "221.228.240.94", "49.70.240.77", "125.113.168.65", "117.27.186.127", "211.141.204.105", "121.32.200.49", "123.98.12.148", "113.44.170.48", "219.139.29.175", "58.55.99.97", "124.73.191.103", "113.72.183.237", "121.32.201.161", "172.16.8.2", "219.128.149.178", "114.243.46.221", "221.197.158.84", "117.66.13.26", "119.39.10.20", "113.12.30.170", "42.121.105.155", "120.198.230.34", "1.204.51.16", "125.127.111.131", "183.130.56.105", "113.117.95.101", "124.174.152.90", "123.181.201.26", "183.148.1.204", "114.243.46.221", "36.249.78.175", "123.160.138.40", "183.138.5.17", "125.113.168.65", "183.148.1.204", "183.148.1.204", "42.95.190.30", "124.68.3.1", "140.206.201.226", "42.96.202.45", "117.27.186.127", "123.98.12.148", "49.70.240.77", "121.228.144.218", "61.135.153.22", "113.72.183.237", "125.116.26.20", "211.141.204.105", "211.141.204.105", "39.180.63.243", "58.68.246.12", "182.130.243.128", "114.243.46.221", "223.64.60.173", "183.138.5.17", "113.72.183.237", "183.130.56.105", "117.15.70.46", "211.138.121.37", "140.206.201.226", "119.39.10.20", "14.214.237.157", "221.237.156.54", "219.155.95.229", "113.44.170.48", "42.95.190.30", "202.109.163.75", "125.127.111.131", "183.148.1.204", "171.221.126.21", "113.117.95.101", "125.113.168.65", "123.160.138.40", "1.204.51.16", "117.27.186.127", "119.142.227.162", "106.3.40.249", "112.94.171.116", "183.138.5.17", "120.33.62.58", "210.14.152.91", "113.120.62.43", "221.228.240.94", "42.185.98.138", "183.148.1.204", "113.72.183.237", "183.138.5.17", "39.180.63.243", "117.140.62.5", "183.130.56.105", "123.181.201.26", "111.1.36.24", "121.228.144.218", "117.27.186.127", "111.1.36.25", "123.98.12.148", "183.207.224.18", "219.155.95.229", "125.116.26.20", "42.185.98.138", "113.117.95.101", "121.32.202.254", "14.214.237.157", "123.160.138.40", "183.138.5.17", "39.190.87.47", "119.142.227.162", "120.33.62.58", "117.66.13.26", "42.62.48.230", "221.228.240.94", "1.204.51.16", "180.153.32.93", "183.138.5.17", "111.63.38.147", "113.44.170.48", "111.1.36.133", "183.138.5.17", "183.207.224.21", "211.138.121.38", "183.57.78.99", "114.243.46.221", "140.206.201.226", "183.138.5.17", "183.130.56.105", "183.138.5.17", "42.95.190.30", "183.245.40.127", "221.197.158.84", "117.140.62.5", "223.64.60.173", "183.148.1.204", "123.98.12.148", "42.185.98.138", "222.87.129.29", "211.138.121.38", "39.180.63.243", "123.181.201.26", "39.190.87.47", "114.61.47.225", "106.117.97.44", "218.204.131.250", "211.141.204.105", "140.206.201.226", "183.148.1.204", "123.151.136.19", "123.151.136.67", "114.243.46.221", "171.221.126.21", "180.169.129.226", "140.206.201.226", "49.89.205.98", "58.53.150.13", "116.243.21.64", "14.124.180.211", "117.66.13.26", "113.12.30.170", "125.113.168.65", "221.228.240.94", "211.138.121.37", "183.207.224.51", "180.169.129.226", "202.98.123.126", "1.204.51.16", "125.127.111.131", "125.127.111.131", "113.117.95.101", "140.206.201.226", "182.130.243.128", "39.190.87.47", "223.64.60.173", "183.130.56.105", "183.138.5.17", "119.142.227.162", "123.98.12.148", "183.207.224.19", "114.243.46.221", "106.117.97.44", "211.138.121.37", "183.146.38.111", "183.138.5.17", "140.206.201.226", "123.181.201.26", "116.228.55.217", "117.140.62.5", "42.185.98.138", "211.138.121.38", "125.116.26.20", "119.39.10.20", "1.85.79.98", "211.138.121.37", "58.68.246.12", "211.141.204.105", "36.249.78.175", "14.214.237.157", "14.145.254.67", "39.180.63.243", "183.138.5.17", "117.27.186.127", "125.127.111.131", "221.228.240.94", "113.44.170.48", "221.197.158.84", "223.64.60.173", "123.98.12.148", "42.95.190.30", "114.243.46.221", "113.72.183.237", "42.185.98.138", "39.190.87.47", "183.146.38.111", "1.204.51.16", "112.94.171.116", "114.226.221.21", "58.68.246.12", "117.15.70.46", "119.142.227.162", "113.72.183.237", "183.138.5.17", "183.207.224.17", "140.206.201.226", "211.138.121.36", "36.249.42.179", "117.140.62.5", "121.32.149.210", "116.243.21.64", "61.50.245.133", "125.127.111.131", "125.113.168.65", "111.1.36.27", "140.206.201.226", "219.139.29.175", "123.181.201.26", "42.185.98.138", "121.228.144.218", "113.12.30.170", "180.169.129.226", "183.138.5.17", "183.167.179.200", "27.156.0.10", "218.85.16.151", "119.39.10.20", "27.156.0.10", "113.120.62.43", "60.214.244.153", "140.206.201.226", "219.155.95.229", "183.130.56.105", "183.148.1.204", "211.141.204.105", "221.237.156.54", "117.66.13.26", "123.98.12.148", "183.138.5.17", "106.117.97.44", "211.138.121.38", "123.160.138.40", "180.169.129.226", "223.64.60.173", "183.138.5.17", "113.117.95.101", "111.1.36.165", "1.204.51.16", "140.206.201.226", "222.87.129.29", "117.140.62.5", "221.228.240.94", "116.243.21.64", "140.206.201.226", "58.215.142.208", "36.249.78.175", "42.185.98.138", "117.27.186.127", "39.190.87.47", "111.63.38.146", "119.188.46.42", "140.206.201.226", "183.207.224.17", "183.130.56.105", "121.8.6.30", "39.180.63.243", "121.8.6.30", "123.150.214.75", "123.98.12.148", "183.138.5.17", "110.87.67.216", "14.214.237.157", "183.138.5.17", "111.205.122.222", "121.32.203.71", "60.214.244.153", "121.32.201.161", "121.228.144.218", "219.128.149.178", "125.113.168.65", "223.64.60.173", "222.245.218.145", "117.25.129.238", "58.215.142.208", "125.116.26.20", "58.68.246.12", "123.181.201.26", "106.117.97.44", "123.160.138.40", "113.44.170.48", "121.32.201.161", "120.33.62.58", "113.12.30.170", "117.27.186.127", "171.221.126.21", "14.29.81.20", "219.139.29.175", "111.63.38.144", "42.185.98.138", "183.148.1.204", "121.8.6.30", "123.98.12.148", "114.243.46.221", "111.1.36.24", "183.130.56.105", "119.39.10.20", "140.206.201.226", "221.228.240.94", "183.138.5.17", "1.204.51.16", "117.27.186.127", "116.243.21.64", "113.71.80.229", "58.61.29.233", "182.130.243.128", "183.138.5.17", "140.206.201.226", "219.128.149.178", "121.228.144.218", "125.113.168.65", "211.141.204.105", "120.198.230.31", "120.33.62.58", "222.245.218.145", "183.138.5.17", "183.207.224.50", "121.32.201.161", "117.15.70.46", "58.68.246.12", "110.87.67.216", "114.243.46.221", "113.117.95.101", "183.138.5.17", "14.18.243.139", "218.85.16.151", "14.145.254.67", "222.94.41.87", "183.138.5.17", "119.39.10.20", "221.237.156.54", "171.221.126.21", "183.138.5.17", "1.204.51.16", "125.127.111.131", "221.228.240.94", "111.1.36.27", "42.185.98.138", "121.228.144.218", "182.130.243.128", "39.180.63.243", "42.95.190.30", "183.130.56.105", "221.176.14.80", "42.62.48.230", "60.214.244.153", "183.148.1.204", "211.141.204.105", "223.64.60.173", "125.116.26.20", "123.98.12.148", "125.127.111.131", "183.138.5.17", "125.113.168.65", "117.27.186.127", "113.44.170.48", "180.169.129.226", "210.14.152.91", "111.1.36.23", "117.27.186.127", "221.197.158.84", "58.61.29.233", "219.155.95.229", "113.72.183.237", "114.243.46.221", "14.18.243.139", "117.140.62.5", "113.117.95.101", "111.1.36.25", "183.57.78.99", "113.12.30.170", "140.206.201.226", "36.249.78.175", "58.68.246.12", "112.94.171.116", "117.66.13.26", "49.89.205.98", "42.185.98.138", "1.204.51.16", "111.1.36.133", "14.145.254.67", "221.228.240.94", "39.180.63.243", "125.127.111.131", "223.64.60.173", "123.160.138.40", "123.98.12.148", "120.198.230.67", "121.228.144.218", "122.143.3.68", "183.130.56.105", "221.228.240.94", "180.109.180.249", "42.95.190.30", "113.44.170.48", "125.113.168.65", "119.142.227.162", "183.138.5.17", "113.71.80.229", "183.138.5.17", "125.116.26.20", "113.120.62.43", "211.141.204.105", "140.206.201.226", "183.57.78.99", "111.1.36.24", "113.117.95.101", "113.12.30.170", "183.207.224.21", "42.185.98.138", "219.155.95.229", "117.140.62.5", "211.141.204.105", "221.197.158.84", "183.207.224.21", "183.146.38.111", "120.33.62.58", "183.92.215.63", "106.38.203.21", "183.138.5.17", "221.228.240.94", "123.160.138.40", "42.95.190.30", "111.63.38.145", "39.180.63.243", "223.64.60.173", "140.206.201.226", "140.206.201.226", "114.243.46.221", "123.75.37.243", "113.71.80.229", "36.249.78.175", "121.32.203.71", "125.127.111.131", "211.141.204.105", "117.15.70.46", "60.214.244.153", "171.221.126.21", "183.130.56.105", "42.185.98.138", "116.243.21.64", "36.249.42.179", "114.243.46.221", "117.66.13.26", "114.226.221.21", "180.109.180.249", "113.12.30.170", "123.98.12.148", "113.44.170.48", "125.113.168.65", "219.139.29.175", "182.130.243.128", "117.27.186.127", "14.29.81.20", "58.61.29.233", "58.53.150.13", "211.152.50.70", "183.207.224.19", "183.148.1.204", "113.117.95.101", "112.94.12.58", "121.228.144.218", "211.141.204.105", "223.64.60.173", "183.207.224.18", "221.228.240.94", "218.85.16.151", "183.207.224.19", "42.62.48.230", "121.8.6.30", "125.127.111.131", "60.214.244.153", "121.8.6.30", "61.50.245.133", "42.185.98.138", "125.116.26.20", "183.130.56.105", "117.140.62.5", "113.72.183.237", "125.113.168.65", "221.228.240.94", "123.98.12.148", "117.66.13.26", "219.139.29.175", "113.44.170.48", "42.185.98.138", "183.146.38.111", "183.138.5.17", "113.71.80.229", "116.243.21.64", "125.127.111.131", "119.39.10.20", "117.15.70.46", "1.85.79.98", "113.117.95.101", "117.27.186.127", "140.206.201.226", "114.243.46.221", "58.240.238.212", "183.57.78.99", "223.64.60.173", "111.1.36.6", "120.39.47.87", "180.110.160.71", "211.151.50.179", "183.138.5.17", "183.138.5.17", "183.138.5.17", "121.8.6.30", "119.142.227.162", "42.185.98.138", "140.206.201.226", "218.85.16.151", "36.249.42.179", "123.98.12.148", "39.180.63.243", "221.237.156.54", "183.138.5.17", "121.228.144.218", "125.116.26.20", "183.130.56.105", "42.185.98.138", "58.215.142.208", "180.169.129.226", "27.156.0.10", "120.33.62.58", "183.167.179.200", "120.202.249.205", "180.169.129.226", "125.113.168.65", "112.94.12.58", "183.138.5.17", "110.87.67.216", "27.156.0.10", "117.27.186.127", "140.206.201.226", "120.198.230.67", "121.32.203.71", "42.95.190.30", "140.206.201.226", "140.206.86.68", "219.155.95.229", "140.206.201.226", "120.39.47.87", "211.138.121.36", "223.64.60.173", "117.140.62.5", "116.243.21.64", "219.128.149.178", "123.151.136.67", "117.66.13.26", "120.198.230.93", "183.148.1.204", "111.63.38.147", "218.94.115.131", "61.177.119.235", "220.180.24.243", "121.229.62.206", "117.88.139.195", "125.122.27.69", "114.98.221.225", "114.222.108.162", "180.110.182.50", "114.222.108.162", "114.222.108.162", "112.4.235.88", "121.237.224.36", "223.104.4.60", "114.96.42.128", "114.96.42.128", "117.88.139.195", "117.88.139.195", "114.97.5.21", "220.248.226.198", "122.193.163.240", "122.193.163.240", "61.164.214.44", "117.89.68.98", "117.88.31.142", "60.166.87.202", "180.111.32.21", "223.65.188.153", "91.58.17.173", "91.58.17.173", "153.34.172.54", "58.52.133.139", "222.44.86.213", "60.190.144.41", "223.64.234.249", "121.225.153.116", "121.225.153.116", "117.88.153.40", "153.3.24.225", "120.210.165.130", "223.65.143.13", "153.3.131.93", "153.3.131.93", "114.226.176.143", "121.229.100.77", "101.247.63.112", "60.168.249.252", "49.65.70.37", "153.34.65.223", "60.190.144.40", "180.98.33.222", "220.180.176.129", "222.44.86.189", "114.98.33.74", "222.94.148.83", "153.3.52.9", "223.65.188.117", "121.229.207.69", "223.65.188.186", "223.65.142.69", "112.80.162.45", "100.110.227.119", "117.64.23.71", "223.65.188.93", "121.238.80.59", "183.212.166.3", "112.25.185.132", "60.168.187.120", "121.229.26.241", "49.65.224.168", "49.65.224.168", "222.48.22.86", "114.221.91.193", "49.74.105.129", "49.74.105.129", "49.74.50.12", "218.23.117.104", "180.109.193.114", "183.157.113.229", "220.178.99.129", "223.240.119.137", "183.160.107.254", "60.166.57.198", "49.74.154.172", "49.77.128.67", "223.240.133.126", "221.178.202.234", "220.178.89.219", "218.94.9.79", "117.88.106.182", "49.77.142.223", "49.77.142.223", "218.94.16.98", "1.87.171.87", "121.237.119.237", "114.220.15.201", "180.109.34.196", "49.77.131.43", "114.98.199.226", "114.222.128.210", "114.97.224.110", "114.97.224.110", "114.97.224.110", "49.77.197.198", "122.95.113.147", "180.102.210.141", "49.77.129.62", "221.224.214.98", "220.178.12.51", "117.89.226.218", "222.45.51.240", "221.6.3.71", "122.96.60.162", "114.97.76.125", "122.96.60.162", "121.224.90.45", "222.94.188.97", "222.94.188.97", "221.226.77.250", "222.94.140.170", "58.213.23.228", "111.20.148.240", "222.95.187.124", "121.225.142.82", "60.173.212.10", "117.88.184.22", "60.166.56.82", "114.222.96.160", "222.94.185.0", "221.226.161.97", "180.110.177.62", "221.226.124.142", "60.166.182.121", "221.226.100.38", "117.89.180.43", "58.240.67.154", "218.94.57.194", "223.65.34.78", "58.213.22.66", "122.195.46.43", "180.111.162.161", "180.109.1.249", "49.68.184.138", "58.101.200.78", "121.225.226.45", "180.113.123.75", "58.243.237.159", "222.94.142.30", "218.94.18.124", "114.221.169.171", "114.218.14.70", "60.166.40.26", "1.80.198.198", "36.250.88.2", "112.132.228.7", "180.106.96.18", "49.80.181.118", "153.34.163.187", "221.6.27.122", "121.229.164.249", "180.110.183.126", "222.95.187.124", "115.193.172.135", "14.105.12.153", "117.88.189.125", "117.64.132.236", "180.111.222.252", "113.87.83.81", "58.212.73.79", "222.191.190.180", "60.166.36.194", "183.160.123.44", "60.190.224.59", "222.93.240.27", "49.65.149.149", "223.240.170.131", "114.103.11.63", "61.153.65.138", "223.65.190.35", "220.178.123.154", "114.222.140.204", "114.221.99.139", "36.47.5.142", "115.204.165.91", "49.77.144.38", "115.204.165.91", "180.109.188.83", "121.225.84.55", "121.238.80.84", "121.237.172.146", "60.174.249.17", "222.95.231.103", "61.191.199.202", "49.77.140.232", "114.221.81.170", "223.240.219.217", "60.173.195.3", "114.222.59.25", "60.173.128.34", "58.100.2.72", "222.45.140.4", "180.109.201.10", "121.236.218.203", "121.236.218.203", "121.236.218.203", "49.77.141.237", "61.164.214.45", "115.60.198.147", "49.77.117.206", "61.132.131.162", "114.97.28.95", "61.191.199.202", "222.64.139.146", "49.77.134.191", "121.229.220.56", "111.39.62.75", "115.211.81.119", "115.211.81.119", "115.211.81.119", "36.7.159.98", "115.211.81.119", "100.66.1.15", "115.211.81.119", "114.96.37.144", "221.226.69.202", "36.7.159.98", "114.97.19.3", "180.112.10.83", "223.65.189.82", "60.168.153.86", "122.96.21.63", "180.109.139.132", "114.221.189.56", "122.96.177.119", "60.177.241.36", "36.62.81.223", "121.237.152.41", "180.111.223.149", "180.111.223.149", "180.111.223.149", "223.65.190.112", "153.3.40.137", "180.109.27.236", "91.58.18.193", "112.81.102.222", "223.65.11.208", "121.225.237.90", "112.80.80.245", "223.65.11.208", "117.84.175.91", "223.65.140.80", "222.190.112.2", "220.184.141.67", "114.221.187.237", "222.94.232.160", "223.243.89.134", "123.150.68.65", "117.62.119.84", "49.65.244.62", "114.225.47.15", "180.102.211.10", "222.95.124.219", "60.166.218.78", "61.164.214.38", "180.110.160.71", "121.235.17.184", "112.80.247.175", "49.65.244.62", "222.95.173.247", "49.65.244.62", "49.74.240.65", "180.158.166.56", "49.75.22.170", "218.2.112.249", "100.66.40.89", "223.65.143.173", "223.65.143.173", "180.109.55.156", "124.126.174.37", "114.221.49.195", "112.80.202.57", "60.168.4.79", "114.96.117.73", "58.213.141.108", "223.65.190.53", "61.164.214.42", "112.80.174.10", "222.94.218.144", "222.94.218.144", "220.178.18.58", "223.65.141.131", "223.65.141.131", "223.65.188.137", "39.187.172.40", "221.226.6.194", "180.111.153.115", "153.34.117.114", "124.73.119.191", "220.178.6.18", "61.132.202.67", "180.110.16.209", "180.111.130.22", "218.90.163.198", "218.94.68.80", "60.174.249.130", "114.97.236.204", "117.89.62.55", "222.95.250.2", "223.65.188.172", "58.213.60.146", "223.65.188.172", "221.224.68.26", "222.45.130.231", "218.72.248.246", "49.80.181.51", "180.109.118.39", "223.65.191.238", "223.240.239.109", "180.111.87.161", "180.110.251.20", "180.109.92.57", "117.62.119.45", "221.6.207.82", "61.164.214.37", "117.89.132.149", "112.80.230.66", "49.72.57.159", "49.74.42.156", "121.234.161.108", "220.180.239.109", "220.180.239.109", "220.180.239.109", "14.104.255.59", "122.83.1.22", "58.213.124.114", "49.74.14.146", "222.190.112.2", "180.109.216.20", "223.240.171.16", "183.213.76.196", "61.191.199.86", "125.38.199.11", "114.226.221.184", "221.226.28.50", "220.114.251.13", "112.0.42.37", "114.222.116.185", "114.222.36.44", "124.73.5.29", "180.111.182.253", "124.73.5.29", "180.110.18.99", "220.178.14.24", "49.82.30.90", "114.98.50.169", "124.73.88.123", "218.94.96.134", "222.95.66.212", "222.95.66.212", "117.64.128.86", "49.65.242.200", "211.143.239.66", "117.63.7.115", "58.210.189.106", "121.237.30.139", "121.237.30.139", "223.65.189.79", "117.89.203.184", "218.94.121.226", "58.212.127.224", "220.178.104.242", "180.106.79.8", "60.166.27.230", "117.89.19.251", "180.110.72.52", "122.83.1.22", "218.94.65.86", "49.77.231.162", "180.111.56.251", "49.77.231.162", "49.77.231.162", "58.240.143.82", "180.111.56.251", "223.240.171.78", "114.222.108.162", "114.222.108.162", "114.98.239.39", "218.108.210.170", "223.240.129.17", "120.195.113.9", "49.74.34.195", "223.240.79.237", "117.62.247.76", "58.246.61.94", "58.216.240.108", "60.166.247.14", "180.110.5.84", "218.22.36.234", "220.178.25.6", "218.94.115.131", "218.94.115.131", "58.214.240.68", "58.214.240.68", "223.66.234.67", "114.105.47.244", "61.133.143.52", "49.77.137.64", "180.110.161.17", "114.222.237.250", "49.77.131.210", "121.237.74.10", "222.94.191.30", "58.213.50.86", "49.77.131.210", "49.65.140.103", "221.6.3.80", "112.80.173.239", "221.6.3.80", "49.77.141.227", "49.77.141.227", "49.77.141.227", "60.173.215.127", "49.77.141.227", "60.173.215.127", "221.226.111.139", "180.109.161.56", "180.110.248.52", "180.110.248.52", "211.103.10.180", "125.118.34.232", "180.111.94.57", "60.173.201.81", "60.8.44.161", "218.94.73.2", "121.237.74.246", "218.205.192.52", "218.205.192.52", "218.22.26.66", "49.74.155.195", "117.88.141.104", "60.173.201.81", "218.93.26.90", "114.239.168.20", "222.94.148.38", "222.94.148.38", "124.73.113.16", "61.133.143.49", "58.213.161.114", "58.212.55.168", "58.213.161.114", "58.213.129.195", "223.65.190.164", "58.212.55.168", "221.6.3.72", "49.77.132.161", "180.121.135.195", "180.102.218.106", "180.102.218.106", "221.6.3.79", "180.110.164.254", "180.110.164.254", "180.110.126.17", "110.217.230.108", "121.225.43.16", "121.225.43.16", "121.225.43.16", "180.102.212.2", "58.213.161.114", "58.213.161.114", "58.213.161.114", "58.213.161.114", "58.213.161.114", "49.77.142.230", "49.77.142.230", "49.77.142.230", "49.77.142.230", "221.6.3.70", "49.74.229.44", "113.98.246.228", "180.102.212.2", "124.73.184.55", "223.65.191.198", "223.65.191.198", "220.179.250.11", "222.44.86.164", "49.74.230.94", "36.56.182.199", "36.56.182.199", "183.160.36.147", "112.2.232.18", "114.97.35.216", "180.102.218.38", "117.88.200.178", "114.221.155.85", "112.24.239.216", "180.107.70.187", "112.24.239.216", "180.102.218.82", "180.102.218.82", "112.24.239.216", "123.157.222.178", "114.97.110.214", "222.95.217.151", "222.95.217.151", "58.213.158.34", "58.213.158.34", "58.213.158.34", "117.84.164.136", "49.65.243.179", "221.226.197.227", "112.0.89.209", "180.110.85.45", "220.178.64.254", "222.87.243.87", "49.77.86.219", "112.2.34.106", "112.2.34.106", "60.8.44.166", "218.22.217.38", "61.164.214.47", "61.164.214.47", "180.110.9.228", "114.222.39.226", "183.212.174.241", "114.222.39.226", "180.110.162.185", "223.65.191.81", "114.222.39.226", "58.42.31.148", "121.61.158.215", "220.178.80.202", "220.178.80.202", "220.178.80.202", "220.178.80.202", "114.222.204.156", "180.102.210.224", "61.129.51.146", "61.191.24.242", "61.191.24.242", "223.65.142.254", "60.55.12.71", "223.65.141.76", "49.77.233.45", "223.65.140.211", "183.212.179.114", "180.102.188.208", "112.86.211.14", "120.210.164.59", "112.80.213.129", "218.94.154.182", "58.212.18.161", "49.77.243.53", "122.96.9.41", "223.240.24.111", "117.84.74.21", "117.88.146.236", "180.164.106.102", "122.96.125.10", "122.96.125.10", "223.245.175.211", "117.89.63.155", "222.95.163.67", "114.96.197.92", "112.86.151.223", "114.96.197.92", "112.86.239.108", "223.65.188.2", "36.57.208.189", "180.102.219.94", "112.1.51.85", "112.86.151.223", "183.167.246.170", "183.167.246.170", "183.167.246.170", "223.65.170.66", "183.208.246.213", "49.65.125.206", "222.94.145.30", "117.88.129.115", "58.240.145.128", "58.240.145.128", "49.65.179.158", "49.65.179.158", "49.65.179.158", "117.89.168.71", "121.229.103.33", "121.225.7.225", "58.215.23.202", "58.215.23.202", "114.224.73.188", "223.65.141.41", "114.222.30.133", "223.65.140.71", "223.65.189.168", "221.6.3.70", "221.6.3.70", "221.6.3.70", "221.6.3.70", "120.209.157.111", "58.212.56.140", "58.212.56.140", "60.166.9.83", "180.111.189.9", "223.65.191.8", "223.65.191.8", "112.86.134.241", "180.110.19.113", "112.86.134.241", "122.96.87.25", "49.77.241.210", "218.90.114.44", "222.95.108.180", "49.77.66.139", "221.226.114.134", "221.226.114.134", "114.221.29.146", "221.226.121.162", "221.226.121.162", "180.111.145.129", "49.74.127.191", "112.22.171.184", "223.3.50.52", "122.88.49.97", "117.86.187.190", "117.86.187.190", "49.80.233.97", "49.80.233.97", "117.86.187.190", "49.80.233.97", "120.195.69.210", "120.195.69.210", "220.179.39.50", "121.229.164.104", "222.95.171.146", "180.102.219.169", "121.225.159.0", "49.65.250.77", "117.84.38.232", "218.94.64.86", "180.111.87.161", "49.77.135.101", "121.237.252.70", "180.114.242.237", "210.73.154.2", "180.111.212.191", "180.110.87.209", "180.111.87.161", "58.213.165.12", "49.74.145.88", "153.3.58.145", "153.3.58.145", "222.94.218.97", "180.111.87.161", "153.3.58.145", "221.6.3.83", "221.6.3.83", "221.6.3.83", "180.111.87.161", "180.111.87.161", "112.80.173.239", "58.216.158.106", "112.25.222.171", "112.80.94.143", "112.80.94.143", "120.195.112.153", "180.111.87.161", "180.111.87.161", "60.168.172.97", "1.84.153.52", "1.84.153.52", "180.109.160.120", "223.65.140.245", "180.109.160.120", "60.166.114.142", "180.111.87.161", "180.109.160.120", "101.231.76.98", "112.2.76.239", "101.231.76.98", "180.111.87.161", "218.94.119.214", "49.77.220.115", "122.96.30.16", "222.190.111.91", "122.96.30.16", "223.65.142.22", "180.110.63.77", "180.173.18.236", "223.240.104.164", "221.130.159.222", "180.111.87.161", "220.180.238.251", "180.110.63.77", "180.110.63.77", "221.130.159.222", "221.130.159.222", "221.130.159.222", "221.226.161.148", "221.130.159.222", "61.190.26.71", "221.130.159.222", "58.216.242.247", "218.93.120.59", "221.130.159.222", "49.77.129.141", "117.62.204.57", "218.94.115.131", "122.96.60.82", "49.89.137.190", "122.96.60.82", "180.111.193.93", "49.77.99.245", "114.222.39.29", "221.226.91.3", "120.209.15.196", "49.65.160.151", "60.173.146.214", "117.88.191.81", "183.160.28.39", "223.65.191.89", "121.237.77.203", "121.237.77.203", "49.77.196.162", "117.62.244.209", "223.65.140.144", "122.88.41.255", "61.164.214.47", "49.80.226.191", "218.104.55.34", "49.80.226.191", "114.221.80.144", "58.213.60.210", "221.181.208.110", "222.93.89.74", "121.237.103.247", "121.248.145.79", "180.111.87.196", "180.111.87.196", "180.111.87.196", "180.111.87.196", "122.96.27.26", "180.111.87.196", "183.156.121.58", "49.74.228.121", "180.111.87.196", "180.111.87.196", "180.111.87.196", "49.74.58.235", "49.74.58.235", "180.111.87.196", "180.111.87.196", "61.155.4.50", "58.212.127.228", "58.216.158.106", "58.216.158.106", "221.130.49.237", "221.226.48.130", "117.89.225.218", "117.89.225.218", "117.89.225.218", "114.97.156.123", "218.94.82.21", "114.97.156.123", "180.110.40.239", "49.77.135.28", "222.94.200.45", "49.77.135.28", "223.65.143.123", "112.2.35.252", "114.234.213.31", "114.234.213.31", "114.96.211.61", "114.96.211.61", "223.65.142.132", "61.155.4.162", "60.166.41.150", "218.94.37.58", "223.65.188.84", "49.77.188.72", "180.110.0.195", "117.88.224.51", "123.77.114.247", "222.94.163.53", "222.94.163.53", "61.160.74.198", "49.77.225.33", "49.65.204.163", "49.77.225.33", "121.225.198.142", "222.94.163.53", "218.94.72.150", "58.219.126.112", "114.98.24.44", "218.94.124.46", "36.7.148.20", "222.94.200.45", "222.191.241.118", "114.221.132.198", "223.65.189.25", "117.88.66.239", "223.65.190.79", "49.80.223.141", "49.77.145.117", "218.94.69.181", "222.95.219.139", "221.6.3.83", "121.237.56.212", "58.213.117.82", "58.213.117.82", "211.143.242.59", "221.226.182.54", "59.46.78.162", "221.226.182.54", "114.80.140.35", "221.226.182.54", "180.102.199.166", "223.65.52.54", "49.73.58.248", "122.88.4.84", "122.88.4.84", "49.74.84.145", "117.88.156.213", "112.80.81.22", "112.86.252.223", "180.111.171.61", "114.97.225.247", "100.66.156.89", "222.95.52.21", "222.95.52.21", "223.68.186.102", "180.111.86.100", "222.95.187.41", "218.94.145.58", "114.222.97.36", "180.175.171.17", "180.111.234.232", "114.224.211.148", "49.77.142.43", "58.212.184.69", "180.109.42.186", "180.111.86.100", "115.205.146.8", "115.205.146.8", "61.132.203.226", "61.132.203.226", "58.243.229.244", "180.111.32.138", "117.89.11.172", "60.166.110.89", "183.160.17.67", "61.132.203.226", "218.94.72.150", "117.89.0.121", "112.0.91.26", "218.94.72.150", "61.190.30.154", "61.177.119.232", "122.96.8.239", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "117.89.11.172", "121.237.26.80", "61.132.203.226", "220.178.49.190", "121.236.156.72", "222.95.6.141", "223.67.59.31", "180.111.86.100", "58.240.94.101", "112.80.217.161", "124.15.120.241", "101.247.83.162", "180.111.86.100", "120.195.21.86", "180.108.196.171", "153.3.58.175", "180.111.86.100", "221.181.146.196", "58.212.56.136", "49.74.155.190", "121.237.41.156", "180.110.14.131", "180.110.14.131", "180.110.14.131", "180.110.14.131", "180.110.14.131", "180.110.14.131", "121.237.41.156", "60.171.142.22", "58.211.173.250", "223.65.140.65", "223.65.140.65", "222.45.32.48", "117.63.64.76", "36.63.174.142", "180.111.32.255", "180.111.139.199", "153.3.44.55", "223.65.140.65", "223.65.140.65", "117.89.69.164", "218.0.204.215", "218.0.201.110", "61.177.137.149", "125.115.249.25", "61.191.202.20", "180.109.42.64", "117.88.104.149", "180.107.105.150", "117.88.152.139", "114.96.131.118", "180.108.85.217", "180.111.87.196", "180.111.87.196", "180.111.87.196", "180.111.87.196", "180.111.87.196", "180.111.87.196", "180.111.87.196", "117.89.182.116", "117.89.182.116", "117.89.182.116", "218.94.124.57", "61.136.95.182", "218.94.124.57", "221.130.58.230", "223.65.140.247", "222.94.202.166", "125.35.4.100", "61.191.21.149", "61.191.21.148", "61.191.21.148", "222.94.153.90", "222.95.90.207", "58.213.161.114", "114.222.5.103", "122.88.132.60", "60.170.190.15", "60.171.56.170", "218.94.115.131", "218.94.115.131", "61.132.137.201", "218.94.115.131", "218.94.115.131", "218.94.115.131", "218.94.115.131", "218.94.115.131", "121.225.76.226", "121.225.76.226", "114.96.61.12", "223.65.188.249", "117.88.62.3", "223.65.188.249", "120.195.112.153", "117.64.35.6", "220.248.245.170", "222.95.90.110", "221.226.2.254", "49.80.217.79", "183.160.134.70", "180.111.150.220", "180.110.4.122", "61.164.214.34", "117.89.77.250", "49.77.178.147", "117.89.77.250", "223.64.236.236", "114.222.28.158", "117.89.65.87", "49.77.103.217", "223.65.191.249", "49.77.221.182", "222.94.131.51", "61.164.214.47", "222.94.149.212", "223.65.140.64", "222.49.243.193", "222.94.149.212", "180.111.32.8", "180.111.32.8", "58.219.251.82", "183.208.140.40", "49.84.198.206", "122.192.45.129", "112.80.192.177", "112.237.77.5", "58.240.156.46", "121.225.27.91", "222.95.126.49", "61.164.214.46", "49.65.158.235", "114.246.81.194", "120.195.55.12", "180.102.218.61", "220.184.126.128", "112.20.75.4", "183.212.226.111", "101.47.33.238", "222.95.156.8", "222.95.126.146", "218.11.179.4", "223.65.141.203", "49.65.158.235", "58.34.177.182", "121.236.165.112", "117.67.221.137", "60.168.44.252", "221.6.3.71", "223.243.49.0", "125.118.57.0", "180.111.162.209", "27.193.164.192", "222.184.11.130", "222.95.90.206", "223.65.143.254", "114.221.126.79", "223.65.141.43", "180.164.14.179", "221.11.33.30", "221.6.3.72", "112.23.162.125", "223.65.189.228", "122.96.23.134", "121.227.192.59", "112.80.209.20", "112.86.217.241", "49.77.131.79", "49.77.131.79", "218.108.45.8", "49.74.123.122", "49.77.248.239", "117.84.156.227", "180.103.220.81", "117.84.156.227", "49.80.193.129", "183.213.73.176", "183.213.73.176", "223.65.13.92", "121.56.76.104", "218.108.45.8", "114.96.26.109", "180.111.154.27", "183.69.183.63", "112.80.209.221", "61.190.15.198", "112.82.100.253", "117.82.166.211", "183.212.251.52", "223.65.141.100", "223.65.191.23", "219.82.5.71", "122.88.25.72", "49.74.84.159", "121.237.30.241", "222.94.253.184", "115.181.101.9", "218.94.136.179", "49.74.84.159", "114.221.15.108", "49.77.241.210", "222.45.139.195", "121.238.12.170", "211.141.188.126", "221.6.3.83", "202.102.37.173", "112.4.128.39", "220.178.19.66", "223.65.143.232", "180.110.11.115", "61.160.66.98", "49.76.35.122", "49.77.140.41", "49.76.35.122", "49.77.222.70", "223.65.142.107", "114.222.163.49", "223.65.142.107", "121.237.132.21", "222.95.0.121", "218.94.96.137", "36.62.152.90", "221.226.48.130", "218.94.131.90", "49.77.228.76", "180.102.214.118", "61.191.27.210", "134.159.114.133", "121.237.70.183", "114.221.185.200", "61.164.214.44", "222.94.79.42", "180.110.162.104", "121.237.253.144", "121.225.89.10", "124.114.218.139", "121.229.49.181", "180.109.197.163", "49.77.132.96", "61.164.214.38", "120.195.112.153", "49.77.196.46", "122.96.128.253", "60.174.249.130", "112.86.241.23", "183.156.29.95", "183.212.188.224", "61.190.90.162", "183.212.188.224", "183.212.188.224", "117.88.155.121", "114.221.90.241", "58.210.189.106", "49.65.251.216", "121.237.121.23", "218.94.14.146", "220.178.95.102", "183.208.98.128", "183.212.188.224", "58.213.48.178", "180.114.69.112", "223.240.226.153", "121.229.32.60", "218.94.60.180", "58.215.212.182", "114.222.226.250", "220.178.102.218", "180.109.113.70", "223.243.119.156", "121.229.102.185", "223.65.191.74", "58.212.133.65", "121.229.102.185", "121.229.213.205", "180.115.246.219", "114.221.152.137", "222.94.88.26", "122.235.183.37", "180.111.151.136", "180.111.151.136", "180.111.151.136", "114.221.152.137", "60.166.2.10", "180.115.246.219", "49.77.133.21", "117.88.82.245", "121.229.206.29", "49.75.43.252", "49.77.133.21", "114.222.226.250", "121.225.158.116", "180.111.96.8", "114.228.176.248", "114.216.93.88", "153.3.58.147", "114.222.226.250", "218.22.32.186", "112.4.155.199", "112.25.188.58", "112.4.155.199", "117.89.63.40", "114.222.193.102", "114.222.193.102", "49.77.189.231", "114.222.193.102", "183.156.121.58", "121.237.253.193", "114.222.193.102", "121.237.253.193", "122.193.42.6", "218.22.9.250", "36.59.226.128", "49.77.141.43", "61.172.251.80", "183.156.70.135", "218.104.44.222", "153.3.52.75", "112.80.105.41", "114.222.193.102", "218.23.45.186", "153.3.52.75", "121.229.190.191", "121.225.9.96", "49.74.215.169", "117.88.189.40", "49.65.202.78", "180.115.246.219", "117.88.112.69", "113.200.182.130", "58.240.89.69", "61.190.32.150", "49.77.74.64", "49.77.74.64", "223.65.189.40", "112.80.77.159", "58.212.51.157", "49.77.74.64", "49.77.74.64", "114.98.227.14", "49.77.74.64", "153.3.66.146", "220.178.14.24", "218.94.14.146", "58.242.219.40", "221.226.98.78", "121.237.70.183", "101.47.33.73", "180.102.210.194", "101.47.33.73", "49.77.128.250", "180.98.11.81", "223.65.142.203", "223.65.142.203", "49.77.130.6", "100.110.16.123", "100.66.52.15", "220.178.105.228", "223.65.10.71", "100.66.52.15", "100.66.52.15", "117.89.182.116", "218.94.115.132", "117.88.155.121", "117.89.182.116", "101.47.33.73", "223.243.26.240", "221.6.14.218", "221.6.14.218", "60.168.124.212", "49.77.139.119", "180.109.202.38", "112.25.222.103", "221.226.253.122", "121.235.251.90", "222.94.62.249", "121.228.113.29", "58.212.40.27", "221.130.130.130", "49.77.128.118", "221.181.208.109", "221.226.189.130", "221.181.208.109", "112.80.230.66", "223.65.189.86", "61.164.214.40", "61.164.214.40", "221.226.127.122", "112.80.230.66", "58.212.40.7", "180.107.213.198", "180.110.183.147", "180.107.213.198", "121.237.201.84", "121.237.201.84", "121.237.201.84", "121.237.201.84", "121.237.203.20", "121.237.201.84", "220.178.103.166", "117.89.168.63", "114.98.198.111", "223.65.140.180", "114.98.198.111", "114.98.198.111", "58.100.131.222", "114.98.198.111", "124.73.97.187", "61.164.214.37", "121.237.21.167", "114.221.55.68", "180.102.164.206", "221.6.3.83", "117.136.35.97", "220.178.89.74", "220.178.89.74", "180.110.86.149", "61.191.25.115", "117.89.205.159", "223.240.90.90", "223.240.90.90", "60.166.188.33", "60.166.188.33", "49.74.229.66", "113.71.255.199", "218.94.61.62", "180.110.6.75", "60.173.220.13", "36.32.33.59", "114.221.40.52", "223.65.143.174", "121.239.222.214", "180.109.225.55", "112.4.151.154", "183.212.224.181", "222.45.139.195", "114.221.98.172", "218.22.230.58", "117.89.43.19", "114.221.63.88", "117.89.43.19", "218.22.31.69", "121.225.151.3", "222.49.247.86", "222.94.126.185", "218.106.178.9", "222.138.30.230", "114.217.194.87", "123.145.30.232", "101.85.207.108", "123.145.30.232", "123.145.30.232", "49.65.158.153", "180.111.204.78", "58.212.52.3", "223.65.140.180", "223.65.140.180", "112.80.142.34", "58.208.187.2", "117.88.229.29", "223.65.140.180", "218.94.63.79", "218.94.63.79", "120.210.165.33", "218.22.9.18", "223.240.173.58", "114.222.193.102", "114.224.193.105", "10.140.202.106", "106.89.162.56", "223.65.143.123", "223.65.143.123", "180.111.150.165", "122.83.1.114", "114.97.133.140", "114.97.133.140", "222.179.234.166", "221.181.210.247", "223.243.37.101", "223.65.140.21", "183.213.81.80", "117.88.66.239", "60.166.250.254", "183.213.81.80", "221.226.81.13", "114.98.245.248", "221.226.115.43", "220.178.99.128", "223.65.140.21", "121.237.155.213", "49.77.157.37", "223.65.140.21", "183.164.58.3", "61.170.136.184", "14.104.179.26", "180.114.223.100", "61.164.214.44", "183.160.70.210", "180.102.214.34", "221.6.3.71", "60.166.129.70", "112.86.214.182", "58.212.5.74", "222.187.210.198", "58.213.157.140", "114.222.158.169", "49.77.142.120", "218.94.67.90", "101.228.27.92", "221.6.3.70", "49.77.142.120", "49.77.130.45", "115.238.50.22", "121.232.57.147", "223.65.188.109", "115.238.50.22", "114.221.42.249", "114.221.42.249", "115.198.252.60", "114.221.180.248", "115.198.252.60", "115.198.252.60", "61.164.214.37", "183.160.31.42", "223.65.142.187", "122.88.16.69", "49.76.108.23", "49.76.108.23", "49.76.108.23", "49.76.108.23", "49.76.108.23", "120.210.165.215", "180.110.0.133", "180.110.0.133", "180.110.0.133", "180.110.0.133", "180.110.0.133", "61.164.214.35", "183.212.252.233", "218.108.193.115", "112.0.60.253", "114.228.127.62", "114.228.127.62", "114.228.127.62", "114.228.127.62", "112.86.128.214", "101.105.201.102", "112.80.209.20", "223.65.189.142", "114.221.45.249", "153.3.114.238", "183.208.4.49", "153.3.114.238", "222.132.76.2", "114.221.64.201", "122.96.113.213", "122.95.27.127", "114.102.55.41", "114.102.55.41", "183.212.255.74", "114.102.55.41", "49.77.157.172", "36.62.211.63", "171.212.87.196", "112.80.207.109", "36.62.211.63", "171.212.87.196", "112.80.207.109", "114.222.34.144", "223.65.140.21", "49.64.98.133", "60.166.146.157", "58.208.40.248", "124.90.12.252", "223.65.140.21", "100.68.10.218", "171.212.87.196", "182.242.227.229", "58.213.14.72", "183.212.255.229", "183.212.255.229", "180.115.31.47", "180.110.212.6", "222.94.231.54", "111.180.122.147", "180.159.158.23", "180.159.158.23", "221.6.3.83", "61.164.214.38", "223.65.188.155", "49.77.161.60", "49.77.189.240", "112.80.223.253", "222.95.248.149", "221.6.3.84", "180.102.214.92", "223.65.142.164", "61.164.214.47", "61.164.214.47", "114.97.121.53", "223.65.188.232", "180.111.32.61", "222.95.189.37", "222.95.189.37", "61.190.77.88", "114.222.1.107", "117.88.206.79", "223.65.140.173", "218.2.216.8", "223.65.140.173", "222.92.66.82", "60.166.213.213", "112.80.165.201", "222.92.66.82", "61.164.214.37", "112.80.213.51", "183.128.184.45", "112.80.213.51", "180.115.107.238", "125.119.110.42", "49.77.179.113", "49.77.179.113", "222.95.250.222", "180.110.85.215", "100.68.144.130", "221.226.170.190", "221.226.170.190", "49.65.86.253", "114.230.238.177", "180.102.218.144", "112.86.129.170", "180.109.80.59", "117.88.231.103", "180.102.244.254", "180.117.13.185", "223.65.143.65", "100.65.23.105", "114.222.193.102", "121.229.127.127", "121.229.127.127", "121.229.127.127", "223.65.143.65", "223.65.143.65", "223.65.143.65", "221.226.76.168", "58.208.198.117", "49.77.249.221", "180.111.169.91", "183.246.133.61", "117.88.104.242", "58.212.40.27", "114.222.193.102", "117.88.61.163", "223.64.63.33", "49.77.249.221", "223.65.191.95", "49.77.249.221", "49.77.249.221", "180.116.136.222", "222.95.199.178", "180.111.235.225", "49.65.175.181", "122.96.123.178", "180.111.95.189", "121.229.19.230", "117.88.157.120", "113.249.25.36", "220.180.18.123", "61.164.214.45", "222.126.160.20", "221.226.48.130", "58.240.156.115", "110.122.36.165", "222.94.191.64", "114.221.48.68", "119.49.170.198", "119.49.170.198", "125.122.236.164", "180.111.160.218", "119.49.170.198", "223.65.191.54", "223.64.148.18", "222.94.112.101", "58.213.250.62", "58.212.147.147", "180.107.163.77", "180.109.131.44", "60.166.31.34", "180.109.131.44", "58.212.8.46", "49.77.245.98", "117.89.50.4", "223.65.140.202", "49.77.135.129", "114.221.25.102", "223.65.140.202", "183.160.131.132", "183.206.7.18", "117.89.169.67", "58.240.132.30", "180.110.106.48", "61.164.214.45", "49.77.55.59", "49.80.127.246", "114.97.109.153", "49.77.135.53", "49.74.235.81", "112.4.44.50", "122.96.9.149", "180.111.145.135", "49.74.154.181", "117.64.50.140", "49.77.69.253", "218.2.103.50", "114.222.77.113", "121.229.62.113", "36.33.28.95", "114.222.77.113", "222.95.117.60", "223.65.189.117", "218.94.21.6", "49.80.144.26", "222.94.149.239", "58.212.154.211", "121.224.170.96", "121.224.170.96", "121.224.170.96", "49.77.128.26", "121.237.75.139", "114.222.193.102", "58.213.42.101", "223.64.211.100", "223.65.188.193", "49.81.91.88", "120.210.165.135", "153.3.58.102", "153.3.58.102", "114.222.130.100", "223.65.142.115", "180.109.163.234", "112.2.75.129", "121.229.111.167", "221.226.171.54", "121.229.206.29", "121.237.70.99", "180.102.211.14", "221.6.3.83", "42.239.179.160", "117.89.59.227", "117.89.70.196", "60.168.157.234", "120.210.164.209", "58.100.14.35", "58.100.14.35", "180.102.213.228", "10.77.10.33", "49.77.131.57", "180.109.166.4", "49.77.138.98", "58.212.8.46", "106.89.185.12", "112.86.195.32", "112.2.78.9", "114.102.145.139", "183.212.177.242", "49.77.143.246", "60.173.241.2", "223.65.191.154", "10.77.10.33", "153.34.18.227", "112.4.147.233", "223.65.191.154", "222.45.147.150", "121.231.140.193", "49.77.218.44", "121.229.103.163", "222.45.147.150", "112.0.47.198", "223.65.140.252", "49.80.126.132", "49.64.98.210", "223.65.141.146", "223.65.190.22", "49.77.138.11", "114.225.47.189", "222.95.130.111", "180.111.38.134", "117.89.55.38", "223.64.62.174", "60.168.243.117", "223.64.62.174", "222.191.238.106", "49.65.113.152", "60.173.218.13", "222.191.238.106", "222.191.238.106", "58.212.193.166", "49.77.226.70", "180.110.161.238", "60.8.44.163", "114.221.111.66", "114.98.215.144", "49.77.129.99", "183.13.84.105", "223.65.191.154", "183.206.2.61", "49.77.143.16", "36.62.6.30", "121.237.147.36", "223.65.143.209", "223.65.188.35", "117.39.42.182", "117.89.49.171", "114.97.28.223", "220.178.55.234", "223.65.188.190", "58.240.156.104", "58.240.156.104", "49.77.244.131", "223.65.191.110", "222.49.240.17", "223.65.189.117", "100.98.205.104", "121.224.99.165", "112.0.54.25", "101.247.93.162", "113.194.123.212", "223.65.191.164", "114.219.152.171", "117.89.46.189", "223.240.85.120", "114.221.61.52", "183.160.109.68", "114.221.61.52", "49.77.188.70", "58.212.203.212", "58.208.204.11", "121.239.134.170", "183.206.2.61", "122.96.45.88", "58.240.134.83", "114.221.25.202", "112.4.128.229", "117.89.28.48", "222.48.22.167", "58.240.135.247", "58.240.135.247", "222.48.22.167", "60.166.71.30", "211.161.247.147", "223.240.143.47", "101.247.78.157", "112.2.69.167", "124.112.171.0", "180.102.244.118", "120.209.162.7", "60.176.72.9", "218.23.102.108", "49.77.235.91", "223.65.141.95", "36.32.3.52", "49.85.128.206", "36.32.4.244", "180.111.148.188", "114.222.30.19", "114.222.30.19", "60.8.44.165", "49.77.41.31", "114.222.129.145", "114.221.42.195", "180.111.144.42", "36.32.7.42", "121.225.174.33", "120.210.164.184", "120.210.164.184", "117.89.48.7", "121.237.123.22", "120.210.164.184", "218.23.125.239", "223.240.142.84", "114.222.220.113", "58.212.102.56", "49.77.102.53", "101.105.201.152", "223.65.166.176", "121.229.55.157", "112.80.208.110", "180.110.235.221", "180.111.32.208", "180.109.233.89", "112.80.171.27", "120.210.164.90", "61.164.214.32", "110.122.3.178", "49.77.221.207", "223.64.234.116", "180.102.214.194", "49.77.118.229", "114.222.131.216", "112.80.121.235", "112.80.121.235", "101.47.33.77", "183.212.247.243", "115.204.143.188", "180.99.186.32", "112.80.147.149", "121.225.82.232", "114.96.96.221", "112.7.68.82", "36.62.94.157", "101.46.54.155", "183.209.51.36", "122.96.12.38", "36.32.41.36", "36.32.41.36", "49.77.94.125", "36.32.41.36", "183.212.191.171", "112.123.246.142", "122.88.5.34", "36.57.207.176", "49.80.121.19", "122.96.47.12", "180.102.218.36", "183.209.50.146", "121.229.136.199", "221.6.3.71", "119.177.37.255", "117.88.241.115", "223.65.140.93", "220.178.102.42", "1.83.220.195", "1.83.220.195", "49.80.220.121", "218.108.97.249", "218.108.97.249", "222.95.251.81", "49.65.140.219", "49.77.198.26", "100.65.47.240", "120.210.166.212", "49.80.244.62", "218.108.97.249", "218.108.97.249", "49.65.192.56", "112.80.237.132", "49.77.157.234", "49.77.221.40", "114.224.98.9", "114.222.255.62", "49.77.229.73", "220.178.123.58", "49.77.135.53", "112.86.215.6", "112.2.75.122", "222.94.218.188", "49.77.130.84", "114.221.49.84", "49.77.135.53", "223.65.140.117", "49.77.226.74", "49.77.226.74", "223.65.140.117", "49.77.226.74", "49.77.226.74", "180.110.3.37", "117.64.91.216", "106.84.196.170", "180.110.42.239", "117.64.91.216", "60.166.145.95", "49.77.226.74", "114.97.100.70", "180.102.178.120", "180.110.42.239", "223.65.188.144", "49.77.226.74", "106.84.196.170", "49.77.226.74", "117.64.91.216", "221.6.3.70", "223.65.190.26", "180.110.26.199", "218.4.157.82", "117.88.83.224", "49.77.218.188", "117.88.83.224", "124.113.178.224", "112.86.214.125", "180.111.235.205", "49.77.140.106", "180.102.195.198", "49.65.219.93", "123.138.33.34", "115.172.88.91", "218.11.179.2", "218.11.179.2", "60.166.192.48", "222.94.144.177", "117.82.179.179", "60.166.192.48", "120.195.115.130", "220.178.52.194", "58.212.8.46", "222.94.190.87", "223.65.143.101", "117.64.139.217", "222.94.190.87", "121.225.25.205", "121.225.25.205", "183.160.255.196", "121.225.25.205", "121.225.25.205", "218.94.72.150", "223.65.143.195", "117.88.84.76", "49.77.143.42", "223.65.140.54", "180.111.148.6", "180.111.148.6", "218.23.99.18", "180.110.103.157", "222.49.240.25", "223.65.189.234", "121.225.192.50", "117.88.225.151", "61.164.214.44", "60.168.135.26", "222.94.145.217", "117.81.197.238", "101.247.84.13", "223.65.189.171", "223.65.189.171", "115.204.165.200", "101.247.84.13", "221.226.221.183", "117.89.107.16", "180.110.103.91", "122.233.168.83", "140.207.87.150", "49.74.246.139", "60.171.43.72", "122.88.1.56", "58.219.55.221", "223.65.142.30", "180.109.197.245", "121.225.19.106", "223.65.141.225", "218.94.72.150", "221.227.110.61", "49.77.134.208", "117.88.83.205", "117.88.83.205", "112.82.178.46", "49.80.127.11", "61.164.214.41", "49.80.127.11", "121.229.121.201", "121.229.121.201", "114.222.237.150", "121.229.213.202", "124.113.145.133", "58.52.133.129", "121.225.193.203", "222.94.189.196", "49.77.136.139", "180.110.161.12", "114.221.179.170", "114.222.140.226", "112.86.159.42", "112.86.159.42", "112.86.159.42", "117.35.166.14", "180.111.13.161", "180.109.114.41", "121.225.252.46", "114.220.3.119", "222.190.108.98", "60.166.72.121", "180.109.139.214", "180.110.161.21", "180.110.161.21", "180.109.139.214", "49.77.241.166", "58.212.193.246", "223.65.142.57", "180.110.247.216", "180.110.60.23", "58.212.8.46", "61.235.220.208", "58.212.8.46", "58.212.8.46", "58.212.8.46", "180.102.188.129", "180.111.150.225", "61.236.224.28", "123.77.106.224", "222.94.75.251", "123.77.106.224", "60.166.245.83", "124.119.137.77", "124.119.137.77", "60.169.22.26", "49.77.167.70", "223.65.188.72", "218.94.128.131", "114.97.151.55", "180.110.168.81", "121.225.171.114", "114.97.151.55", "183.212.171.101", "60.168.18.80", "222.45.144.246", "218.205.21.247", "114.97.151.55", "223.68.128.166", "114.221.47.100", "183.212.240.77", "49.77.229.150", "183.212.240.77", "222.95.66.128", "117.88.200.82", "117.88.200.82", "223.65.188.197", "223.65.191.87", "223.65.188.163", "49.77.207.164", "58.240.152.134", "60.168.31.67", "218.108.205.226", "180.110.173.180", "223.65.143.239", "180.102.220.49", "101.242.8.20", "124.73.94.55", "60.176.151.122", "114.97.95.71", "49.65.200.216", "220.180.227.231", "223.64.237.57", "100.66.135.240", "117.64.139.187", "222.95.191.124", "122.194.13.241", "112.86.214.182", "117.89.139.131", "60.175.31.103", "223.65.143.187", "112.81.94.138", "112.81.94.138", "58.100.21.49", "58.100.21.49", "222.45.139.186", "180.110.160.208", "49.77.128.26", "180.107.79.17", "112.80.228.167", "49.77.129.242", "49.74.35.114", "101.105.222.251", "223.65.191.130", "223.65.189.67", "111.195.8.187", "123.72.72.168", "49.65.111.249", "223.65.189.187", "49.74.195.94", "49.80.105.193", "223.65.189.187", "49.77.254.68", "49.77.254.68", "121.237.52.134", "180.110.84.202", "49.74.42.186", "117.89.66.154", "121.237.98.187", "101.86.249.140", "223.65.190.54", "61.164.214.42", "223.64.234.192", "121.225.59.81", "223.64.234.192", "112.81.131.110", "218.2.96.98", "223.65.189.148", "112.10.171.161", "114.221.100.93", "112.86.175.43", "124.73.118.19", "114.222.70.184", "183.160.115.181", "183.160.115.181", "221.6.3.83", "223.65.50.217", "117.64.62.181", "117.71.69.249", "123.77.118.241", "61.164.214.36", "49.77.98.106", "49.77.98.106", "111.194.37.251", "117.88.109.24", "180.115.246.98", "123.77.112.249", "221.226.113.106", "114.222.222.134", "121.229.61.222", "61.190.59.2", "49.77.245.23", "223.65.141.26", "101.106.133.193", "223.65.142.144", "112.86.253.11", "49.74.38.226", "223.65.188.91", "222.95.25.62", "60.186.162.221", "58.212.5.12", "117.89.89.172", "223.65.140.76", "124.73.111.55", "121.229.101.143", "223.65.141.231", "49.65.251.170", "58.240.254.192", "112.80.227.54", "58.240.254.192", "58.240.254.192", "49.77.103.34", "49.77.34.87", "114.96.116.154", "58.243.203.93", "180.110.120.51", "180.110.120.51", "122.88.57.64", "122.88.57.64", "36.57.205.107", "180.110.211.194", "223.65.191.136", "122.88.57.93", "122.88.57.93", "218.94.158.227", "223.65.140.208", "58.252.63.199", "180.108.214.57", "114.221.129.139", "58.222.101.18", "180.102.212.50", "180.102.212.50", "180.102.212.50", "223.240.232.114", "122.96.118.130", "223.65.190.231", "222.49.242.253", "223.65.157.57", "121.229.95.223", "218.90.128.2", "58.213.47.18", "180.109.208.45", "221.227.27.142", "221.227.27.142", "49.65.102.181", "112.86.159.42", "218.94.67.90", "218.94.67.90", "223.65.188.160", "58.211.174.122", "49.77.238.24", "220.178.82.18", "49.77.238.24", "113.47.18.178", "49.77.238.24", "218.23.109.10", "222.95.160.87", "49.77.238.24", "121.237.148.59", "61.164.214.34", "180.108.157.73", "60.8.44.167", "58.216.242.248", "180.108.157.73", "117.88.105.137", "180.111.152.75", "58.213.104.130", "58.213.44.158", "58.213.104.130", "180.111.73.140", "58.213.104.130", "119.183.204.122", "58.212.4.26", "58.212.4.26", "101.47.33.225", "58.213.104.130", "115.236.1.19", "115.236.7.198", "180.111.150.255", "49.77.141.49", "180.111.150.255", "121.229.61.152", "221.226.38.118", "153.3.58.102", "49.77.130.11", "49.77.244.114", "60.174.248.194", "223.240.58.179", "153.3.58.102", "58.213.131.211", "58.213.104.130", "222.94.109.226", "183.128.185.90", "222.94.109.226", "58.213.104.130", "121.229.30.90", "153.3.66.210", "58.213.104.130", "222.94.121.97", "49.80.193.85", "58.213.104.130", "121.229.187.127", "58.213.104.130", "218.22.14.194", "49.80.193.85", "49.80.193.85", "180.102.211.217", "117.80.9.69", "112.23.181.130", "183.209.136.251", "183.209.136.251", "221.226.2.254", "61.133.143.50", "222.66.116.117", "58.213.139.218", "118.183.104.113", "49.77.178.153", "220.178.105.2", "223.65.143.231", "223.65.143.231", "49.65.173.15", "61.190.91.46", "218.94.39.242", "114.221.146.188", "49.77.249.65", "223.65.191.160", "223.68.128.166", "223.65.142.206", "61.132.52.38", "180.109.130.61", "218.94.36.150", "180.109.130.61", "120.209.195.124", "218.94.18.10", "121.237.253.21", "180.110.7.160", "180.110.7.160", "120.193.116.10", "117.62.235.189", "180.110.7.160", "114.222.168.3", "1.84.70.45", "180.110.7.160", "114.222.168.3", "114.222.168.3", "114.222.168.3", "49.65.203.62", "49.74.50.96", "223.65.188.100", "125.117.229.179", "180.109.81.174", "114.222.168.3", "125.117.229.179", "218.22.36.234", "36.63.36.131", "36.32.60.65", "61.191.29.214", "218.94.0.15", "180.110.7.160", "114.222.227.123", "49.77.162.217", "114.222.227.123", "180.110.7.160", "61.191.25.91", "49.77.189.124", "222.95.15.159", "218.2.103.215", "220.178.59.98", "121.229.211.188", "114.224.122.111", "58.213.154.136", "125.117.229.179", "222.95.236.61", "114.222.236.231", "58.212.251.155", "122.193.136.234", "114.222.2.127", "180.110.7.160", "218.2.227.155", "49.77.142.23", "117.89.152.68", "218.23.40.250", "183.213.74.63", "218.23.40.250", "49.67.37.225", "125.120.176.118", "60.168.242.197", "49.77.133.137", "58.213.124.66", "117.81.252.203", "223.65.188.112", "120.210.164.209", "58.212.180.244", "114.222.145.190", "122.88.37.225", "223.65.9.203", "58.212.180.244", "58.212.180.244", "114.226.206.56", "117.64.210.146", "58.212.124.252", "61.191.29.214", "222.95.21.223", "117.64.210.146", "49.77.128.20", "36.33.27.28", "49.74.229.188", "49.74.229.188", "117.64.210.146", "117.64.210.146", "49.74.229.188", "100.64.81.21", "221.226.116.75", "117.80.36.29", "120.210.161.165", "117.88.226.44", "58.212.4.26", "114.221.177.197", "223.65.141.66", "117.82.158.63", "183.129.200.50", "125.119.204.104", "49.77.240.123", "112.25.184.163", "100.68.145.153", "218.94.54.82", "202.102.102.39", "218.94.148.170", "221.6.207.122", "221.6.207.122", "221.6.207.122", "223.65.188.89", "223.64.236.246", "58.213.131.220", "58.213.80.246", "121.248.52.176", "125.210.223.145", "117.62.148.133", "49.77.147.214", "114.96.49.163", "121.229.19.85", "125.120.12.230", "223.65.191.118", "220.178.5.98", "180.111.76.49", "221.226.240.72", "180.109.42.101", "180.110.246.88", "49.80.124.204", "114.221.81.63", "49.80.124.204", "121.237.70.243", "58.240.39.5", "218.94.3.10", "58.240.39.5", "122.96.152.78", "221.225.186.127", "124.205.64.66", "58.213.138.110", "49.77.138.124", "220.178.18.180", "58.213.104.130", "121.225.143.19", "122.96.154.34", "121.237.66.29", "180.111.214.90", "49.77.231.210", "58.213.158.30", "115.192.193.95", "58.213.14.190", "122.96.154.34", "222.190.117.130", "222.190.117.130", "117.81.149.82", "122.96.154.34", "221.6.3.71", "221.226.36.82", "122.96.154.34", "218.69.250.214", "218.94.41.198", "221.6.3.71", "58.212.124.252", "58.212.124.252", "121.236.143.163", "49.65.71.168", "49.65.71.168", "60.166.87.136", "222.66.116.117", "114.98.17.241", "222.190.111.121", "114.98.17.241", "222.190.111.121", "221.6.14.186", "221.6.14.186", "221.226.14.2", "49.77.189.143", "180.110.120.51", "49.77.189.143", "180.110.120.51", "58.240.29.162", "122.96.30.15", "180.110.120.51", "180.111.163.68", "114.221.102.105", "221.226.113.242", "58.213.167.3", "120.209.156.221", "58.213.167.3", "120.209.156.221", "221.226.189.70", "117.89.201.111", "221.226.4.2", "117.89.201.111", "117.89.201.111", "221.224.0.254", "180.109.43.184", "223.65.143.212", "58.208.65.166", "180.110.120.51", "49.65.102.101", "218.22.34.138", "117.89.245.150", "180.109.180.39", "222.95.147.224", "121.237.173.124", "121.237.173.124", "218.2.156.8", "114.222.168.3", "49.80.234.175", "114.222.168.3", "218.23.114.4", "121.237.173.124", "114.222.168.3", "180.110.120.51", "218.22.34.138", "180.110.7.160", "221.226.148.178", "180.110.133.247", "121.229.26.223", "218.2.111.146", "121.229.26.223", "218.2.111.146", "121.229.26.223", "218.23.114.4", "223.65.143.139", "58.240.65.251", "218.23.114.4", "58.215.216.94", "61.191.27.250", "121.225.94.78", "58.212.133.63", "218.23.114.4", "49.65.193.96", "58.213.129.195", "49.77.228.181", "218.94.37.82", "117.63.0.64", "49.77.145.237", "49.77.145.237", "58.212.13.169", "117.89.73.167", "114.221.133.101", "101.47.33.139", "180.106.48.136", "61.191.28.124", "122.195.137.28", "121.225.92.92", "49.77.49.213", "223.65.190.211", "114.222.103.77", "49.74.148.120", "49.77.142.174", "183.212.251.52", "60.168.121.125", "180.111.214.90", "121.229.215.147", "60.166.3.154", "122.88.62.165", "180.111.133.191", "180.114.92.62", "120.209.114.24", "61.164.214.34", "114.98.248.51", "49.77.139.155", "180.111.88.37", "223.65.143.174", "180.115.197.26", "49.77.15.77", "180.115.197.26", "223.65.143.174", "153.35.18.63", "114.221.41.183", "60.166.109.64", "180.109.160.135", "221.6.3.83", "221.226.48.130", "221.226.48.130", "121.237.221.18", "49.77.130.46", "180.111.151.192", "49.74.197.246", "36.63.48.12", "49.77.130.46", "114.218.124.243", "49.77.87.237", "121.229.141.181", "122.84.65.139", "223.65.23.223", "220.178.80.157", "101.105.214.46", "172.24.180.61", "172.24.180.61", "117.81.144.233", "172.24.180.61", "180.110.20.64", "121.235.105.158", "58.212.210.138", "220.178.80.157", "218.94.67.162", "218.94.67.162", "117.89.28.24", "222.185.98.148", "49.77.222.159", "122.88.24.205", "122.88.24.205", "122.88.24.205", "122.88.24.205", "117.64.35.250", "36.63.0.66", "36.63.0.66", "36.63.0.66", "180.108.216.191", "221.226.11.122", "60.166.105.24", "110.122.35.59", "218.22.37.134", "121.229.208.146", "117.82.226.103", "36.7.149.200", "122.233.170.225", "218.86.180.19", "117.82.226.103", "218.86.180.19", "180.110.84.216", "221.6.3.83", "114.222.98.222", "121.237.120.207", "58.212.170.175", "223.65.189.216", "121.237.40.22", "223.64.62.119", "180.110.9.176", "114.221.1.110", "221.6.3.80", "121.237.54.232", "125.121.249.139", "100.66.85.159", "112.1.170.207", "122.94.90.44", "114.98.202.67", "49.74.234.52", "218.93.157.236", "49.74.234.52", "112.81.176.142", "221.6.3.71", "221.6.3.71", "221.6.3.70", "49.77.138.17", "223.65.191.250", "223.65.140.248", "60.168.78.163", "112.80.158.198", "114.222.98.222", "113.107.166.42", "223.65.189.181", "114.222.220.217", "120.210.165.244", "223.65.189.181", "223.65.140.236", "223.65.140.236", "60.168.118.1", "60.168.118.1", "60.168.118.1", "223.65.142.228", "222.94.90.108", "49.77.230.103", "223.65.188.59", "112.80.128.192", "221.6.3.83", "78.34.91.161", "223.65.141.247", "223.65.141.247", "106.37.236.177", "113.215.2.54", "113.215.2.54", "112.82.218.98", "113.215.2.54", "49.77.196.22", "112.87.179.45", "180.111.33.34", "221.6.3.83", "180.102.211.230", "223.64.62.122", "122.95.2.56", "223.240.32.106", "218.94.67.90", "218.94.67.90", "218.11.179.6", "221.6.3.83", "49.75.20.159", "221.226.113.242", "122.94.253.221", "221.6.207.122", "222.95.44.171", "49.77.199.50", "223.65.188.135", "111.39.109.188", "180.113.223.21", "60.8.44.166", "180.109.223.36", "49.74.154.42", "180.110.4.63", "112.2.234.150", "122.83.2.173", "60.8.44.166", "58.213.14.190", "180.110.17.54", "223.65.188.227", "223.65.141.20", "223.65.188.135", "49.65.82.242", "218.94.13.206", "223.65.188.227", "223.65.141.20", "223.65.188.135", "223.65.188.135", "223.65.141.20", "222.94.129.232", "119.183.203.140", "222.94.129.232", "117.89.8.215", "223.65.141.20", "218.94.92.131", "218.94.92.131", "218.22.49.205", "117.89.8.4", "49.77.134.211", "222.95.173.17", "218.94.108.62", "222.95.173.17", "222.95.173.17", "180.108.205.161", "221.226.62.34", "121.237.41.158", "114.221.188.118", "58.212.230.242", "58.212.230.242", "223.65.140.192", "58.212.230.242", "121.237.9.254", "220.178.8.202", "121.237.9.254", "121.237.9.254", "221.130.58.238", "180.110.7.160", "221.226.75.130", "114.224.174.254", "117.136.20.83", "180.110.79.135", "101.228.161.96", "222.95.94.145", "222.95.211.45", "112.82.222.36", "121.229.71.121", "218.94.158.211", "49.65.140.5", "61.155.234.2", "223.65.189.43", "112.80.139.209", "222.95.94.145", "218.4.173.74", "218.22.6.53", "223.68.128.166", "223.65.143.23", "218.2.227.149", "218.2.227.149", "117.89.87.226", "180.167.7.35", "222.95.94.145", "115.195.43.240", "180.167.7.35", "222.95.94.145", "117.89.168.69", "180.109.81.13", "221.226.83.35", "58.240.105.118", "58.240.105.118", "114.96.127.211", "223.65.140.124", "222.95.250.186", "180.111.192.180", "121.225.159.96", "123.77.123.76", "221.226.22.218", "117.89.180.3", "220.178.0.18", "123.77.123.76", "220.178.29.36", "218.94.96.136", "218.94.55.18", "125.122.211.45", "49.77.219.24", "121.237.254.143", "220.178.64.26", "222.94.74.86", "180.116.254.40", "183.208.6.13", "58.212.124.36", "120.210.164.209", "180.102.42.194", "183.209.62.117", "58.240.152.134", "220.180.228.151", "171.116.70.126", "171.116.70.126", "221.226.154.218", "153.3.58.239", "58.213.104.170", "221.226.185.174", "49.77.142.173", "218.2.111.162", "112.86.159.49", "220.178.35.102", "117.89.170.25", "223.65.140.192", "117.64.210.92", "117.89.170.25", "218.94.72.150", "223.65.5.114", "223.65.5.114", "58.210.31.42", "49.77.166.243", "172.28.118.171", "172.28.118.171", "112.86.159.49", "218.94.136.169", "117.89.170.25", "117.89.170.25", "112.86.149.152", "117.89.170.25", "218.90.91.123", "112.86.159.49", "180.109.104.248", "222.95.145.31", "117.71.66.118", "180.111.148.183", "221.226.240.72", "114.221.55.6", "221.226.218.230", "58.213.131.211", "218.94.4.38", "114.97.29.226", "180.102.19.45", "121.225.155.49", "61.132.137.201", "218.94.3.10", "49.77.139.76", "114.97.88.219", "117.89.170.25", "60.168.254.26", "218.94.114.26", "60.8.44.165", "58.211.149.45", "117.89.57.221", "58.212.46.25", "61.155.4.66", "117.88.156.9", "61.155.4.66", "60.166.58.226", "114.221.77.4", "180.102.220.243", "60.173.202.50", "121.225.154.159", "49.65.159.47", "121.225.154.159", "223.65.141.66", "222.45.38.44", "49.74.17.163", "121.225.92.124", "223.65.190.162", "114.222.129.19", "61.190.25.242", "120.210.164.203", "221.6.3.72", "121.237.222.197", "58.216.245.114", "121.235.17.126", "222.94.187.94", "58.212.252.64", "222.94.187.94", "218.90.189.70", "116.237.6.133", "121.225.19.201", "117.62.148.3", "117.62.148.3", "121.225.157.46", "221.226.35.250", "221.226.35.250", "180.102.17.86", "180.102.17.86", "180.102.17.86", "180.110.162.72", "100.64.12.119", "180.110.162.72", "117.89.131.253", "49.77.130.127", "58.212.184.197", "100.64.61.202", "223.65.141.245", "180.102.210.243", "180.102.210.243", "112.3.225.0", "117.85.147.247", "58.240.150.247", "117.85.147.247", "117.80.242.134", "223.65.141.111", "114.221.181.109", "117.85.147.247", "114.97.39.145", "112.4.133.81", "112.4.133.81", "114.221.181.109", "49.77.91.110", "61.160.22.94", "106.37.236.185", "222.95.254.240", "101.247.84.61", "221.226.2.30", "113.232.137.73", "223.65.189.134", "114.221.181.109", "221.178.181.136", "218.94.128.131", "221.178.181.136", "121.228.101.50", "218.94.128.131", "121.237.85.110", "58.219.234.82", "60.166.141.11", "49.77.142.173", "49.74.93.76", "122.94.249.6", "223.66.163.98", "106.37.236.182", "180.110.85.66", "222.95.216.27", "49.80.117.250", "122.94.249.6", "10.197.58.188", "112.82.222.36", "114.221.181.109", "58.213.124.66", "121.229.216.248", "218.23.100.242", "180.111.204.166", "117.89.180.3", "121.229.202.242", "58.240.80.154", "220.178.51.58", "180.111.151.74", "60.166.37.183", "180.113.138.205", "180.111.151.74", "180.111.151.74", "180.111.150.25", "180.113.82.231", "180.109.222.101", "61.132.133.24", "180.111.150.25", "121.229.216.95", "60.168.145.3", "218.94.6.165", "180.110.0.213", "100.107.152.195", "36.46.43.146", "222.95.88.92", "121.229.169.69", "121.237.227.123", "180.111.151.74", "221.226.91.5", "221.12.22.82", "180.111.151.74", "114.97.16.181", "180.111.151.74", "218.109.44.106", "122.88.24.109", "120.210.161.55", "221.226.177.42", "221.226.177.42", "49.65.70.66", "122.88.46.165", "114.221.181.109", "117.89.88.127", "49.77.156.15", "183.160.119.110", "221.6.8.242", "117.89.170.25", "117.89.170.25", "117.62.210.195", "117.89.170.25", "223.64.61.210", "221.226.105.198", "122.96.30.15", "117.89.170.25", "222.94.40.195", "49.74.228.12", "58.212.181.160", "58.216.157.35", "121.237.73.41", "121.237.144.166", "114.222.198.141", "27.16.214.43", "223.65.191.101", "101.105.198.246", "114.97.16.181", "218.94.72.150", "221.226.171.182", "221.226.171.182", "218.94.142.62", "61.132.132.90", "121.237.129.199", "49.65.70.171", "211.162.26.24", "49.65.70.171", "180.110.247.155", "122.96.60.162", "122.96.60.162", "60.166.144.86", "60.166.144.86", "60.166.144.86", "60.166.144.86", "223.65.141.20", "114.96.132.129", "114.222.129.140", "221.226.171.182", "223.65.143.55", "223.65.141.20", "120.209.234.226", "49.77.157.88", "49.77.157.88", "222.45.138.188", "222.44.86.211", "223.65.191.86", "223.65.191.86", "112.80.161.122", "124.160.241.110", "115.238.243.37", "180.111.133.62", "122.96.114.242", "223.65.142.59", "49.77.232.158", "223.64.236.217", "222.48.20.199", "124.160.241.110", "60.166.248.138", "60.168.17.53", "60.168.17.53", "122.88.47.151", "49.77.166.159", "58.212.101.125", "58.212.101.125", "58.212.101.125", "218.11.179.1", "58.212.101.125", "223.65.190.42", "114.96.52.233", "60.168.44.105", "180.111.150.86", "112.20.78.47", "180.109.27.82", "223.65.142.170", "218.205.23.149", "49.77.217.150", "49.77.178.14", "223.64.209.26", "222.95.159.5", "101.107.169.112", "49.77.189.166", "49.77.140.1", "112.80.128.27", "124.73.99.204", "124.73.99.204", "180.114.113.32", "124.73.99.204", "60.168.65.194", "124.73.99.204", "112.86.225.187", "180.110.70.239", "222.95.158.56", "222.95.158.56", "49.77.67.129", "123.77.118.233", "123.77.118.233", "49.77.136.3", "223.65.189.40", "223.65.189.132", "223.65.143.86", "223.65.143.86", "223.65.143.19", "180.109.11.41", "117.89.50.241", "114.237.232.246", "218.94.151.192", "223.65.190.84", "49.77.140.228", "121.229.109.135", "49.77.140.228", "223.65.143.159", "112.3.240.222", "112.80.159.229", "114.98.37.120", "223.65.143.253", "223.65.33.251", "121.229.223.201", "121.237.74.62", "223.65.33.251", "106.37.236.190", "106.37.236.190", "180.110.119.51", "180.110.119.51", "121.225.143.252", "180.102.215.85", "49.77.196.155", "121.225.128.213", "114.221.255.246", "49.65.211.198", "183.212.246.8", "183.212.246.8", "49.77.249.137", "121.229.30.29", "180.110.30.122", "116.234.205.121", "218.94.67.90", "218.94.67.90", "101.47.33.77", "122.88.8.29", "10.116.151.175", "121.225.4.34", "112.3.232.58", "112.3.232.58", "117.88.248.203", "222.45.138.41", "112.4.48.152", "220.178.33.126", "49.77.145.144", "122.96.12.248", "180.111.188.229", "49.77.142.159", "121.237.33.183", "49.77.142.159", "61.191.27.12", "223.65.142.244", "223.65.54.70", "223.65.142.244", "180.111.12.5", "101.47.33.114", "101.47.33.114", "180.111.12.5", "121.237.202.222", "117.88.81.167", "223.65.141.47", "60.8.44.166", "218.94.92.131", "223.65.141.47", "121.229.88.25", "222.66.116.117", "221.226.3.98", "223.65.141.20", "117.88.133.57", "117.88.133.57", "114.221.30.25", "180.111.51.119", "122.88.58.207", "121.237.94.178", "121.237.94.178", "49.74.55.223", "117.88.207.168", "117.85.28.202", "221.226.48.130", "49.74.6.51", "117.89.224.56", "117.89.224.56", "58.212.231.3", "121.237.94.178", "121.235.220.80", "222.95.216.27", "121.237.94.178", "180.102.202.247", "112.3.246.15", "49.77.131.251", "222.95.46.175", "61.132.52.10", "49.77.188.125", "114.221.8.237", "112.86.158.122", "110.122.12.223", "49.77.133.153", "223.65.141.145", "112.86.158.122", "223.65.143.47", "222.95.59.44", "180.110.227.69", "106.37.236.189", "49.77.249.255", "112.80.229.207", "117.89.88.127", "114.221.180.210", "221.226.6.2", "223.65.10.134", "218.2.104.170", "114.97.78.199", "223.68.187.50", "58.213.124.66", "114.221.205.34", "58.223.4.10", "114.221.205.34", "222.94.142.102", "58.213.147.227", "218.94.57.194", "223.65.140.154", "218.94.77.50", "218.94.19.214", "113.107.166.42", "218.94.57.194", "218.94.19.214", "49.77.138.236", "180.109.184.129", "110.122.10.166", "110.122.10.166", "120.210.164.68", "110.122.10.166", "121.229.62.81", "120.210.164.68", "218.94.19.214", "183.157.12.123", "218.94.19.214", "120.210.164.209", "110.122.12.223", "180.111.222.214", "58.212.252.47", "58.212.40.141", "58.212.252.47", "106.44.88.232", "106.44.88.232", "49.74.84.41", "117.81.180.248", "221.6.3.88", "117.88.200.139", "117.62.245.17", "61.160.101.2", "61.160.101.2", "112.80.161.242", "180.110.17.63", "49.88.64.211", "121.237.21.109", "112.81.101.255", "183.160.29.11", "121.237.21.109", "49.88.64.211", "218.22.18.118", "218.94.136.168", "121.237.21.109", "114.222.182.146", "223.244.233.196", "49.88.64.211", "223.244.233.196", "49.88.64.211", "49.77.167.47", "223.65.141.65", "49.88.64.211", "180.110.17.63", "49.88.64.211", "114.226.252.241", "49.77.188.175", "58.213.134.74", "220.178.84.142", "114.234.209.192", "49.77.132.94", "49.77.141.44", "218.94.72.150", "115.198.91.214", "115.200.209.172", "49.77.174.235", "49.77.174.235", "49.74.238.174", "218.94.142.62", "222.95.49.60", "49.74.238.174", "112.81.101.255", "180.111.32.92", "61.155.112.34", "117.89.132.180", "61.190.32.43", "114.221.80.16", "114.221.80.16", "121.237.68.255", "58.212.13.103", "180.109.9.0", "180.111.70.155", "180.109.9.0", "222.94.186.10", "117.63.130.147", "218.2.101.90", "117.63.130.147", "117.63.130.147", "49.77.167.47", "58.213.104.170", "58.213.104.170", "153.3.118.40", "115.199.162.123", "114.216.168.201", "114.216.168.201", "124.73.121.197", "122.96.28.5", "114.221.203.163", "49.77.145.138", "114.221.203.163", "180.110.181.212", "223.65.143.140", "49.77.143.166", "223.65.143.140", "180.111.149.223", "100.65.20.77", "1.85.21.8", "124.90.146.153", "180.111.162.27", "180.111.162.27", "218.94.148.170", "117.89.109.242", "218.94.96.136", "112.81.101.255", "117.64.26.155", "124.113.146.21", "117.64.26.155", "222.94.188.123", "180.102.214.133", "49.77.140.157", "112.86.209.4", "218.94.130.98", "58.211.174.2", "117.88.152.29", "222.95.147.98", "221.226.251.66", "222.94.248.254", "117.88.152.29", "114.222.35.7", "125.85.39.230", "49.65.71.160", "220.175.154.252", "117.88.104.79", "58.213.162.162", "218.94.110.203", "58.213.46.30", "114.221.40.253", "114.221.40.253", "222.94.123.31", "114.97.225.31", "180.110.161.106", "222.94.123.31", "121.229.223.220", "49.77.230.192", "180.110.9.127", "121.229.124.198", "121.229.124.198", "60.180.153.217", "121.229.124.198", "121.229.124.198", "221.226.44.160", "60.168.161.84", "221.226.44.160", "114.97.71.98", "221.226.47.133", "49.77.164.135", "220.178.80.202", "49.77.229.209", "220.178.80.202", "220.178.94.98", "220.178.80.202", "49.77.133.155", "220.178.80.202", "220.178.94.98", "220.178.80.202", "61.191.21.147", "61.153.7.150", "58.212.54.161", "114.97.71.98", "117.89.70.154", "180.109.131.229", "180.110.121.12", "180.111.99.199", "14.104.189.188", "180.102.213.190", "180.111.99.199", "58.212.91.161", "221.174.173.30", "49.74.155.7", "218.23.124.103", "218.23.124.103", "180.110.212.23", "49.80.121.199", "218.23.124.103", "117.89.172.226", "218.23.124.103", "218.23.124.103", "218.94.124.41", "221.226.18.214", "218.94.39.242", "121.229.193.218", "118.205.154.68", "49.77.141.229", "117.89.63.156", "117.89.63.156", "117.68.144.191", "218.23.42.155", "221.226.175.2", "117.88.114.205", "180.109.182.146", "106.37.236.190", "180.102.211.221", "58.212.13.103", "1.203.246.141", "114.229.239.95", "49.72.95.16", "114.221.27.78", "121.229.26.65", "122.192.40.109", "211.138.191.83", "180.102.211.221", "180.102.212.4", "220.178.52.146", "183.129.140.62", "115.236.1.242", "121.229.8.90", "100.65.130.4", "180.110.63.13", "121.229.206.119", "60.166.222.35", "221.226.212.22", "220.178.94.98", "220.178.94.98", "112.25.140.182", "112.86.208.213", "61.132.139.210", "223.65.141.67", "223.65.141.67", "121.229.124.198", "222.95.204.229", "121.229.124.198", "121.229.124.198", "121.229.124.198", "121.229.124.198", "114.97.231.151", "60.168.109.53", "115.194.119.20", "115.194.119.20", "117.89.45.75", "180.113.138.198", "117.89.23.247", "220.178.88.61", "121.229.61.103", "121.229.61.103", "121.229.61.103", "180.109.95.126", "49.74.194.134", "223.65.189.66", "49.77.131.17", "223.65.155.163", "121.237.57.46", "58.213.135.122", "180.110.250.216", "180.110.250.216", "222.95.144.102", "117.88.120.231", "223.240.225.176", "180.111.162.27", "36.33.5.146", "180.111.224.221", "223.65.9.199", "49.80.131.67", "222.94.190.29", "49.80.131.67", "180.111.212.253", "223.65.143.114", "112.23.171.247", "121.229.53.68", "223.65.189.249", "42.196.149.203", "110.122.23.74", "61.173.195.29", "121.234.130.106", "121.234.130.106", "223.65.25.155", "183.213.75.70", "223.65.140.202", "223.65.166.195", "117.89.54.247", "180.111.34.210", "117.88.189.1", "36.32.58.51", "221.226.196.236", "223.65.143.62", "223.65.143.62", "223.65.143.62", "221.226.221.153", "222.95.59.248", "218.108.220.113", "218.108.220.113", "218.108.220.113", "223.65.188.4", "61.134.5.78", "112.2.65.122", "123.72.66.19", "114.229.95.115", "114.227.68.76", "121.237.113.140", "114.221.80.140", "153.3.36.84", "114.221.187.185", "106.37.236.187", "49.77.141.117", "58.192.118.14", "121.237.175.230", "114.226.29.70", "183.213.75.70", "117.85.23.95", "180.110.105.97", "221.6.3.86", "223.65.189.122", "114.222.37.97", "122.88.18.123", "49.80.125.108", "183.208.5.157", "114.89.217.168", "114.89.217.168", "60.166.131.90", "60.166.131.90", "183.165.218.4", "121.237.40.143", "180.110.208.191", "114.96.237.28", "121.237.40.143", "121.237.40.143", "121.225.153.27", "223.65.140.245", "221.6.3.83", "117.89.60.234", "117.89.60.234", "221.6.3.83", "58.240.133.7", "100.66.37.84", "180.114.84.164", "58.208.65.124", "58.212.113.54", "125.121.236.50", "223.65.190.140", "223.65.53.215", "100.66.148.46", "180.111.133.227", "114.96.40.103", "114.96.40.103", "223.65.190.140", "121.237.57.170", "121.237.157.68", "124.73.94.206", "180.109.141.166", "120.210.164.209", "223.65.140.79", "223.65.140.9", "223.65.140.9", "183.212.186.130", "124.73.8.223", "121.229.60.149", "121.229.88.188", "60.166.76.65", "114.96.101.139", "117.88.89.17", "121.237.54.148", "121.237.54.148", "49.77.232.253", "101.231.76.98", "222.49.250.104", "223.65.189.50", "58.212.50.205", "218.94.9.8", "49.80.218.14", "125.125.82.38", "218.94.9.8", "180.166.202.195", "120.210.143.158", "218.94.9.8", "121.229.58.57", "223.65.141.192", "222.94.101.170", "60.166.20.155", "49.80.227.158", "121.229.101.85", "180.110.216.88", "222.66.116.117", "220.178.91.226", "121.229.88.30", "220.178.40.42", "112.4.128.215", "60.166.13.118", "125.85.172.46", "121.236.52.228", "122.88.37.204", "49.77.188.50", "117.89.157.24", "49.77.138.25", "117.89.174.115", "180.109.90.93", "49.77.179.171", "49.77.230.13", "183.213.72.189", "223.65.191.246", "49.74.149.195", "121.236.52.228", "218.94.115.131", "115.192.222.214", "223.65.191.246", "180.110.163.113", "114.226.92.209", "180.110.163.113", "121.229.186.6", "223.65.191.246", "114.226.92.209", "49.77.131.209", "223.240.96.105", "180.110.16.85", "36.33.6.191", "222.90.105.26", "153.3.58.105", "223.65.140.246", "49.77.133.159", "222.94.195.221", "223.65.142.137", "58.213.14.190", "117.89.226.214", "110.122.33.15", "117.62.227.8", "223.65.142.100", "183.206.30.217", "60.166.122.26", "49.65.107.120", "210.73.154.2", "221.6.44.68", "58.213.129.195", "221.6.44.68", "218.94.117.234", "112.25.223.9", "117.88.18.98", "114.221.73.251", "211.141.224.44", "121.229.105.223", "223.3.46.106", "211.141.224.44", "183.209.59.1", "180.116.246.250", "116.234.31.5", "114.97.37.39", "124.74.40.86", "112.87.126.94", "121.229.88.182", "113.132.94.154", "113.132.94.154", "112.80.242.118", "122.96.17.41", "223.67.59.242", "221.11.26.147", "124.73.91.201", "124.73.118.143", "221.226.41.246", "60.177.213.126", "180.109.92.238", "180.110.21.57", "180.110.21.57", "114.96.219.72", "222.190.104.156", "222.190.104.156", "49.77.98.79", "49.65.117.25", "49.65.117.25", "49.65.117.25", "49.65.117.25", "218.94.149.50", "112.3.233.160", "218.94.149.50", "114.217.205.183", "218.94.149.50", "114.217.205.183", "218.94.149.50", "218.94.149.50", "121.237.21.109", "221.6.60.124", "100.70.2.255", "221.130.252.36", "221.226.3.242", "221.130.252.36", "180.111.133.229", "221.130.252.36", "221.130.252.36", "114.97.82.205", "223.240.117.217", "114.97.82.205", "218.2.227.155", "124.79.110.166", "114.97.82.205", "180.111.49.124", "112.20.74.217", "58.212.192.180", "112.20.74.217", "114.222.81.4", "218.94.100.18", "221.226.215.130", "221.6.3.83", "117.89.204.100", "221.6.3.83", "221.6.3.83", "121.229.137.190", "124.79.110.166", "180.110.216.19", "180.110.221.15", "221.6.3.83", "220.178.24.138", "121.237.44.220", "49.77.157.150", "49.77.157.150", "180.110.124.240", "221.226.27.242", "117.89.91.200", "122.96.42.206", "124.79.110.166", "180.110.188.223", "114.218.156.143", "114.98.196.66", "222.95.196.95", "61.160.83.244", "49.77.57.252", "49.77.229.42", "221.226.39.138", "112.80.161.122", "222.185.26.146", "49.77.216.241", "112.80.161.122", "115.206.153.139", "112.80.161.122", "49.77.144.74", "122.96.116.59", "223.65.142.253", "211.161.221.242", "211.161.221.242", "222.190.125.254", "218.93.18.82", "112.2.5.211", "115.199.84.94", "58.212.125.68", "121.229.49.89", "117.64.233.198", "223.65.141.155", "117.89.123.72", "117.89.123.72", "218.2.111.134", "218.2.111.134", "117.89.123.72", "49.74.208.168", "49.74.208.168", "117.62.148.96", "180.109.8.102", "223.65.142.104", "180.111.112.254", "180.111.112.254", "117.89.54.176", "117.89.54.176", "60.190.144.40", "117.89.134.156", "222.92.90.170", "117.88.55.51", "117.92.176.130", "58.216.189.242", "49.93.35.120", "61.175.194.22", "58.216.207.181", "61.175.194.22", "114.221.210.14", "58.212.84.16", "222.49.245.157", "114.98.43.212", "221.6.3.83", "49.74.84.99", "221.226.113.19", "58.215.193.186", "58.215.244.134", "58.215.216.94", "121.229.106.235", "122.88.17.90", "60.191.94.146", "58.216.175.163", "122.95.108.249", "49.65.193.169", "58.216.175.163", "49.80.116.50", "222.95.13.70", "49.77.188.117", "223.65.190.207", "124.114.200.162", "58.213.113.125", "49.65.117.25", "49.65.117.25", "49.65.117.25", "101.247.73.212", "49.65.117.25", "49.77.189.240", "223.65.140.62", "223.65.191.196", "223.65.143.124", "49.77.189.240", "218.94.154.110", "60.168.88.143", "61.132.133.24", "58.212.58.67", "58.212.58.67", "223.65.140.162", "223.243.89.156", "180.109.153.79", "223.243.89.156", "121.237.172.46", "180.102.212.240", "36.63.173.109", "223.65.189.54", "180.109.173.47", "36.63.173.109", "223.65.140.252", "223.65.188.179", "222.95.212.239", "117.89.30.63", "218.11.179.2", "58.212.13.103", "183.209.152.82", "106.37.236.178", "223.64.187.23", "58.213.51.162", "58.240.128.182", "49.77.156.78", "49.65.200.234", "180.110.7.151", "100.66.131.252", "122.192.44.236", "60.186.207.54", "49.65.126.74", "110.122.11.250", "60.168.20.167", "117.88.189.16", "117.62.246.97", "36.33.24.113", "180.110.18.159", "114.222.108.33", "36.59.224.130", "49.77.139.55", "100.64.37.152", "112.82.91.109", "58.58.48.254", "121.229.62.86", "49.77.228.243", "180.110.22.233", "117.89.68.162", "117.89.68.162", "112.25.191.2", "60.166.225.107", "49.65.95.57", "112.86.146.134", "223.65.141.78", "223.240.189.221", "183.208.98.241", "223.240.189.221", "114.221.99.183", "223.65.191.138", "114.221.99.183", "49.77.98.156", "223.65.190.238", "113.215.2.13", "223.65.142.146", "221.227.93.178", "61.150.67.216", "223.65.142.253", "153.3.67.183", "153.3.67.183", "223.65.189.147", "112.80.103.69", "223.65.189.147", "223.65.189.147", "223.65.189.147", "49.74.82.194", "180.112.6.152", "183.212.226.42", "223.65.140.7", "180.119.4.146", "223.65.140.7", "223.65.189.169", "101.105.217.172", "117.67.174.30", "101.244.115.184", "223.240.172.143", "49.74.235.50", "117.60.22.213", "180.109.223.130", "223.65.190.170", "222.95.165.21", "222.95.165.21", "49.74.151.122", "222.95.165.21", "221.137.97.95", "223.64.209.171", "180.109.13.253", "121.237.84.61", "223.65.188.51", "218.22.15.249", "112.80.206.53", "180.106.25.139", "100.66.82.8", "100.66.82.8", "100.66.82.8", "49.65.149.111", "116.17.101.227", "180.158.76.135", "112.80.106.175", "112.80.106.175", "49.77.249.197", "153.3.0.127", "117.85.14.218", "121.229.184.54", "121.229.184.54", "114.221.208.54", "117.85.119.84", "49.80.107.42", "223.65.141.12", "36.32.28.22", "106.37.236.180", "114.222.205.229", "221.226.113.242", "106.37.236.186", "58.39.100.144", "223.65.191.54", "122.88.12.67", "121.225.182.192", "114.96.228.38", "117.89.190.108", "117.89.190.108", "60.166.42.50", "36.32.21.172", "58.213.141.107", "114.222.28.212", "49.80.223.14", "112.0.36.26", "113.200.85.249", "222.95.54.232", "121.225.92.97", "222.95.146.218", "58.213.48.219", "180.98.2.90", "106.37.236.176", "106.37.236.176", "117.89.221.61", "58.212.99.4", "114.222.1.136", "220.178.80.202", "221.227.93.190", "49.77.129.255", "49.77.239.172", "221.227.93.190", "218.94.80.20", "49.77.225.134", "49.77.239.172", "49.77.225.134", "223.65.141.2", "223.65.189.2", "223.65.141.2", "49.77.244.224", "49.65.70.238", "49.77.244.224", "223.65.141.2", "220.178.25.6", "223.65.141.2", "218.2.110.66", "220.178.25.6", "183.213.77.105", "114.222.0.49", "112.2.34.42", "183.213.77.105", "61.191.21.148", "60.166.47.226", "121.237.201.219", "121.237.201.219", "122.96.42.66", "49.77.239.202", "112.86.254.136", "49.77.135.33", "112.86.254.136", "49.77.239.202", "49.74.145.89", "180.106.166.154", "121.225.210.109", "180.109.214.181", "58.208.231.186", "60.8.44.166", "60.166.138.58", "117.88.98.78", "180.113.202.248", "221.6.3.83", "49.80.183.154", "112.80.71.163", "218.94.114.50", "121.235.240.227", "122.233.207.120", "122.233.207.120", "218.94.114.50", "114.222.122.221", "117.89.205.33", "218.94.114.50", "58.213.124.66", "218.94.114.50", "58.215.229.134", "222.95.44.166", "58.213.124.66", "58.213.124.66", "120.210.142.8", "221.6.3.79", "58.213.154.78", "112.80.121.110", "49.80.183.154", "223.65.188.215", "221.228.242.106", "114.221.132.174", "36.33.24.94", "36.33.24.94", "221.228.242.106", "58.240.21.98", "221.226.82.202", "49.77.129.90", "221.226.83.35", "221.226.82.202", "218.94.11.162", "140.246.19.162", "49.65.210.225", "36.33.24.94", "36.33.24.94", "221.6.33.218", "218.94.118.134", "223.68.131.19", "114.97.18.70", "10.224.255.189", "101.231.76.98", "218.94.3.10", "121.229.186.81", "49.65.83.211", "106.37.236.179", "58.240.26.203", "58.240.26.203", "180.102.218.246", "180.109.172.5", "180.109.172.5", "117.88.149.109", "218.23.93.84", "218.94.86.150", "221.226.212.202", "58.212.54.161", "221.226.75.11", "122.88.25.76", "180.102.218.246", "49.77.137.53", "124.79.110.166", "121.224.109.143", "221.226.215.130", "49.77.137.53", "121.224.109.143", "117.89.8.176", "60.190.144.43", "114.98.28.39", "112.80.154.148", "114.222.237.157", "114.222.237.157", "221.226.3.250", "221.226.3.250", "221.216.36.100", "218.94.16.238", "117.88.18.102", "49.77.226.77", "180.111.196.201", "180.111.196.201", "180.111.196.201", "180.111.196.201", "218.94.44.126", "218.94.44.126", "180.110.237.15", "112.80.170.136", "222.95.88.107", "114.105.110.187", "222.95.8.143", "218.2.106.172", "117.64.72.113", "222.45.49.50", "221.6.3.83", "121.225.155.248", "117.88.105.228", "223.65.141.240", "100.66.188.59", "121.231.188.48", "218.94.68.40", "49.77.139.24", "180.102.212.141", "1.85.21.8", "1.85.21.8", "117.88.129.83", "180.102.212.141", "218.94.57.194", "180.102.212.141", "223.65.143.225", "180.109.12.160", "223.65.141.155", "221.226.37.66", "221.226.37.66", "49.77.130.222", "112.24.164.152", "49.90.141.101", "221.11.67.70", "180.106.171.112", "120.210.164.117", "49.80.217.145", "49.77.137.139", "117.88.159.236", "117.88.159.236", "114.221.253.15", "117.88.159.236", "114.222.131.113", "121.229.222.247", "121.229.222.247", "114.97.40.246", "49.77.128.89", "36.33.2.170", "120.195.112.164", "218.2.102.98", "121.225.47.175", "117.88.178.250", "117.89.74.80", "121.225.47.175", "114.221.0.135", "121.229.222.247", "121.225.155.248", "49.65.202.70", "222.95.93.170", "114.222.83.105", "223.65.191.238", "114.222.83.105", "223.65.191.238", "115.211.84.195", "180.110.163.96", "211.141.223.28", "117.80.68.12", "49.80.120.139", "183.212.227.83", "218.93.12.78", "117.80.68.12", "180.111.196.201", "180.111.196.201", "222.95.50.209", "49.77.138.39", "36.33.24.94", "117.88.189.94", "27.43.174.65", "180.110.4.59", "58.214.4.66", "58.213.133.58", "27.43.174.65", "180.111.215.132", "220.178.35.226", "223.65.140.62", "183.157.14.35", "180.111.196.201", "180.111.196.201", "221.226.178.150", "223.65.140.62", "180.111.196.201", "180.111.196.201", "61.133.143.35", "49.77.188.232", "223.240.81.180", "183.165.49.64", "49.77.188.232", "100.64.25.28", "221.181.210.247", "100.69.12.242", "120.195.112.164", "100.69.12.242", "60.166.103.179", "49.77.141.197", "122.88.33.78", "221.226.175.98", "223.65.5.245", "121.229.25.169", "223.65.191.213", "223.65.191.213", "114.92.41.44", "49.65.172.134", "124.127.41.205", "221.226.84.218", "223.65.62.168", "211.140.5.111", "223.65.62.168", "49.66.52.59", "223.65.62.168", "61.190.76.26", "223.65.140.69", "49.221.62.114", "60.173.202.50", "223.65.62.168", "58.240.75.102", "218.94.67.90", "218.94.125.35", "49.77.137.139", "218.22.32.186", "218.22.32.186", "222.90.106.149", "49.77.137.139", "218.94.67.90", "122.96.47.16", "222.72.250.82", "183.208.5.123", "183.160.188.153", "121.225.141.12", "61.132.200.6", "49.77.235.159", "36.44.144.7", "100.68.5.107", "49.65.168.118", "100.68.5.107", "36.32.16.77", "49.65.168.118", "223.65.62.168", "122.96.25.106", "58.212.8.220", "223.65.62.168", "49.65.168.118", "121.225.5.242", "218.94.128.51", "218.94.37.58", "117.22.230.134", "49.77.34.2", "49.77.34.2", "211.143.232.162", "218.22.40.75", "218.22.40.75", "222.190.117.130", "223.65.142.60", "114.98.33.6", "58.213.154.116", "49.74.55.103", "49.74.84.224", "180.115.35.67", "222.94.184.207", "121.229.27.5", "222.94.184.207", "222.94.184.207", "49.74.85.21", "221.130.124.82", "117.89.69.133", "49.77.197.172", "112.86.135.40", "117.89.69.133", "222.45.145.185", "180.102.219.76", "117.88.224.89", "223.65.15.80", "122.92.2.211", "115.238.243.48", "117.89.69.133", "112.2.77.102", "222.94.62.120", "112.2.77.102", "121.237.129.82", "121.229.203.93", "122.88.18.129", "114.84.238.41", "222.95.216.111", "218.94.72.150", "223.65.191.54", "223.65.191.54", "114.97.74.221", "223.65.191.54", "223.65.188.122", "122.83.2.98", "49.65.173.63", "223.65.140.237", "114.221.2.89", "223.65.191.254", "221.6.3.93", "221.6.3.93", "221.227.74.12", "180.111.224.117", "49.77.140.146", "49.77.140.146", "121.229.154.179", "110.194.51.242", "223.65.191.54", "58.212.127.27", "222.48.23.179", "58.212.158.111", "117.89.123.26", "223.65.143.73", "106.37.236.190", "223.65.143.73", "223.65.143.73", "218.94.136.173", "112.80.181.127", "58.212.8.232", "114.96.144.126", "122.96.31.23", "223.65.142.167", "114.222.222.47", "114.222.222.47", "117.62.157.77", "223.65.188.49", "49.77.219.4", "223.65.142.120", "49.77.219.4", "121.238.101.63", "223.64.234.169", "49.77.235.163", "58.213.157.140", "60.55.42.137", "223.65.188.178", "60.55.42.137", "49.77.140.146", "121.229.18.65", "58.212.1.47", "49.74.104.99", "121.237.227.78", "49.74.234.20", "49.74.234.20", "221.6.3.94", "221.6.3.94", "112.86.134.113", "112.86.134.113", "112.80.227.223", "36.33.217.23", "112.86.134.113", "49.77.221.12", "49.77.147.107", "49.77.147.107", "113.200.204.208", "112.86.134.113", "27.24.95.174", "112.86.134.113", "112.86.134.113", "122.94.235.116", "180.102.201.108", "183.15.220.142", "58.240.156.140", "180.111.46.198", "121.225.195.150", "121.225.195.150", "100.66.15.255", "49.77.143.98", "117.89.236.255", "112.10.192.117", "221.6.3.93", "218.104.78.196", "61.132.138.212", "223.64.62.180", "221.6.3.92", "221.6.3.92", "61.155.111.162", "58.39.100.144", "121.237.89.165", "120.210.160.138", "180.109.193.150", "122.96.138.69", "221.6.3.93", "223.65.143.225", "122.96.118.119", "223.65.63.177", "223.65.143.225", "223.65.143.225", "223.65.143.225", "223.65.63.177", "223.65.19.6", "117.62.134.34", "112.25.137.122", "121.229.185.243", "183.209.51.96", "223.65.141.120", "49.65.235.47", "223.65.142.72", "117.89.246.212", "49.65.235.47", "117.64.239.220", "114.222.21.167", "223.65.62.130", "122.192.39.198", "114.222.21.167", "122.192.39.198", "180.110.214.113", "223.240.106.92", "223.65.142.75", "120.210.165.31", "49.77.250.135", "122.224.111.246", "223.65.191.239", "222.95.95.77", "223.240.106.92", "58.213.147.74", "58.213.147.74", "112.86.175.11", "58.213.147.74", "223.240.106.92", "121.225.171.61", "49.65.161.37", "121.229.143.119", "121.237.52.86", "223.65.188.190", "49.77.179.111", "58.212.71.52", "112.86.155.75", "121.237.201.219", "49.77.179.111", "223.65.188.233", "27.43.174.37", "112.80.222.107", "180.111.38.24", "123.93.152.134", "49.77.130.190", "180.109.95.237", "27.43.174.37", "27.43.174.37", "49.74.78.167", "49.77.179.111", "49.77.231.95", "221.226.173.8", "58.212.193.124", "117.136.20.38", "223.65.188.159", "222.190.122.86", "222.95.224.238", "222.190.122.86", "222.95.145.20", "117.64.88.182", "223.65.188.159", "180.102.200.21", "117.62.199.166", "49.77.245.191", "114.221.31.186", "153.3.58.174", "223.65.190.191", "114.221.31.186", "221.6.3.92", "183.208.10.123", "58.213.131.220", "100.64.34.8", "117.64.88.182", "49.77.248.197", "182.96.188.93", "182.96.188.93", "182.96.188.93", "153.35.10.91", "117.88.91.186", "117.88.91.186", "121.229.78.159", "222.177.23.161", "114.221.31.186", "121.229.73.53", "121.229.10.8", "180.110.1.54", "121.225.51.189", "218.23.117.104", "223.240.188.72", "121.225.51.189", "223.64.62.228", "121.225.51.189", "60.168.88.244", "121.225.51.189", "223.65.140.94", "42.196.67.162", "223.65.141.155", "183.210.197.62", "223.65.140.94", "121.225.51.189", "121.237.32.122", "121.225.231.144", "112.87.51.120", "49.74.16.100", "222.94.115.242", "121.237.45.10", "60.166.151.168", "60.166.151.168", "222.94.209.160", "222.95.213.231", "180.102.213.143", "114.98.207.163", "101.105.223.141", "121.229.101.96", "58.212.127.171", "117.64.238.7", "58.243.224.11", "114.221.42.102", "49.65.205.170", "114.97.104.112", "218.94.72.150", "114.221.70.81", "117.89.47.112", "180.102.200.192", "117.64.134.60", "117.64.134.60", "101.47.33.240", "49.80.250.141", "222.95.225.159", "49.74.196.67", "121.237.132.39", "49.74.32.170", "180.102.195.122", "221.6.3.94", "106.37.236.180", "121.235.43.80", "60.166.242.118", "122.83.172.21", "180.110.102.87", "221.6.3.92", "114.222.35.184", "114.217.243.191", "180.110.73.66", "49.65.235.47", "115.238.243.45", "49.80.116.114", "223.65.142.24", "49.77.134.188", "49.77.134.188", "223.65.141.120", "49.77.134.188", "180.102.210.50", "117.84.95.121", "223.65.188.1", "14.106.127.218", "121.225.43.213", "101.231.76.98", "180.109.95.241", "49.77.140.129", "49.77.140.129", "101.231.76.98", "49.77.130.75", "61.132.50.226", "49.77.95.81", "221.226.161.243", "49.77.173.128", "121.229.26.61", "180.110.188.160", "180.110.188.160", "180.110.188.160", "180.110.132.146", "180.110.132.146", "223.65.142.147", "218.106.88.214", "180.102.178.15", "223.65.142.147", "180.111.77.167", "223.65.140.220", "61.133.143.55", "180.111.163.182", "180.102.220.60", "60.166.231.153", "49.80.127.201", "222.185.10.227", "180.109.173.76", "124.114.107.48", "180.109.195.227", "124.114.107.48", "124.114.107.48", "221.226.39.45", "121.225.43.213", "49.77.178.96", "180.109.195.227", "49.77.178.96", "60.168.124.23", "112.20.65.2", "49.77.247.163", "60.168.120.20", "49.77.247.163", "222.95.225.253", "117.81.235.150", "223.65.143.156", "58.213.60.42", "120.210.164.180", "106.37.236.189", "113.206.40.177", "120.210.164.180", "106.37.236.189", "106.37.236.178", "60.173.221.42", "49.77.242.246", "61.190.52.213", "58.212.126.68", "218.94.72.150", "218.94.72.150", "121.225.210.34", "58.212.126.68", "112.0.41.68", "114.98.59.252", "180.110.91.224", "221.6.3.94", "180.102.184.177", "222.185.125.22", "180.102.201.59", "114.222.208.246", "114.222.208.246", "60.168.113.135", "183.209.138.28", "183.209.138.28", "58.212.231.37", "180.115.33.148", "180.115.33.148", "49.77.245.149", "60.166.104.65", "223.65.143.73", "218.94.21.6", "117.89.121.82", "117.89.121.82", "223.65.189.205", "121.237.233.46", "114.222.208.246", "49.77.140.34", "223.65.189.205", "180.115.33.148", "117.62.211.16", "180.115.33.148", "49.77.140.34", "180.115.33.148", "223.3.22.137", "180.115.33.148", "117.62.211.16", "49.77.140.34", "223.3.22.137", "180.115.33.148", "121.237.44.125", "121.237.44.125", "117.88.107.113", "60.166.83.152", "61.132.73.162", "61.132.73.162", "61.132.73.162", "61.132.73.162", "61.132.73.162", "218.205.22.23", "61.132.73.162", "218.205.22.23", "223.65.188.104", "180.111.35.58", "112.0.90.234", "60.166.77.12", "180.110.133.156", "180.110.183.225", "180.110.183.225", "180.110.183.225", "113.215.2.16", "113.215.2.16", "122.92.7.246", "223.65.140.158", "113.215.2.16", "222.94.192.114", "223.65.143.139", "49.77.135.109", "180.110.186.153", "223.240.109.12", "121.225.93.239", "153.35.26.174", "223.65.191.7", "180.110.182.56", "121.225.136.180", "114.222.128.28", "121.229.102.17", "49.77.240.90", "121.229.102.17", "112.86.199.175", "122.225.181.190", "122.225.181.190", "122.96.78.79", "114.221.184.94", "49.77.131.63", "49.77.206.133", "114.96.57.199", "221.225.105.180", "223.240.114.238", "180.110.252.180", "180.110.252.180", "180.110.252.180", "223.65.190.201", "223.65.190.201", "223.65.190.201", "223.65.190.201", "223.65.143.154", "58.213.161.215", "100.69.132.50", "58.213.161.215", "100.69.132.50", "112.80.114.113", "223.65.190.201", "112.80.114.113", "36.63.150.91", "221.226.161.77", "222.185.96.36", "60.168.7.182", "60.168.7.182", "180.102.220.203", "180.102.220.203", "180.102.220.203", "221.226.161.77", "221.226.161.77", "121.225.85.3", "221.226.161.77", "121.225.85.3", "180.102.220.203", "221.226.161.77", "121.225.85.3", "223.65.189.203", "114.96.111.194", "223.65.189.203", "121.225.85.3", "221.226.161.77", "223.65.189.203", "121.225.85.3", "49.74.238.141", "49.74.238.141", "223.65.189.203", "223.65.143.154", "223.65.189.203", "223.65.189.203", "100.64.43.130", "223.65.189.203", "100.64.43.130", "100.64.43.130", "223.65.142.31", "223.65.189.203", "223.65.142.31", "223.65.189.203", "223.65.142.31", "113.135.116.174", "113.135.116.174", "113.135.116.174", "223.65.189.203", "113.135.116.174", "113.135.116.174", "223.65.35.72", "223.65.35.72", "180.110.252.180", "183.212.245.100", "183.212.245.100", "121.237.197.112", "183.212.245.100", "121.237.197.112", "121.237.197.112", "121.237.197.112", "49.74.199.188", "223.65.140.53", "223.65.140.53", "49.74.199.188", "49.74.199.188", "49.74.199.188", "49.74.199.188", "49.74.199.188", "180.110.188.65", "117.62.149.134", "122.83.186.101", "122.83.186.101", "36.32.30.28", "222.95.236.115", "222.95.236.115", "122.96.138.253", "112.22.170.242", "222.95.236.115", "112.22.170.242", "222.95.236.115", "180.110.252.180", "49.77.242.89", "197.111.255.240", "180.110.161.216", "180.110.161.216", "223.65.62.145", "197.111.255.240", "223.65.62.145", "223.65.62.145", "197.111.255.240", "223.65.62.145", "197.111.255.240", "117.88.207.131", "221.6.3.93", "221.6.3.93", "221.6.3.93", "221.6.3.93", "221.6.3.93", "221.6.3.93", "221.6.3.93", "183.209.139.57", "223.65.8.163", "221.6.3.92", "122.96.138.69", "122.96.138.69", "122.96.138.69", "122.96.138.69", "110.206.24.9", "122.96.138.69", "110.206.24.9", "223.243.114.241", "223.65.14.144", "223.65.140.49", "223.65.140.49", "112.2.76.204", "112.82.196.198", "223.65.140.49", "112.82.196.198", "112.82.196.198", "49.65.127.44", "223.65.191.214", "112.0.44.113", "223.65.191.214", "223.65.191.214", "180.102.201.244", "223.65.191.214", "112.0.44.113", "117.88.60.134", "117.88.60.134", "112.0.44.113", "180.102.201.244", "117.88.60.134", "180.102.201.244", "114.221.68.39", "117.88.229.174", "114.221.68.39", "221.226.170.111", "221.226.170.111", "221.226.170.111", "221.6.3.93", "218.94.115.132", "112.80.239.126", "221.6.3.93", "221.6.3.93", "221.226.170.111", "221.226.170.111", "180.102.201.244", "111.20.118.58", "49.65.242.153", "180.110.187.177", "49.65.242.153", "180.110.187.177", "120.210.161.93", "120.210.161.93", "58.240.30.132", "120.210.161.93", "180.115.104.89", "58.240.30.132", "49.77.138.243", "221.226.113.242", "58.240.30.132", "218.94.3.10", "58.240.30.132", "121.225.19.4", "122.88.130.241", "122.88.130.241", "112.122.31.147", "122.88.130.241", "122.192.39.198", "112.122.31.147", "122.92.9.10", "180.111.232.113", "112.122.31.147", "112.122.31.147", "49.77.134.188", "180.111.232.113", "114.222.197.43", "112.122.31.147", "58.212.110.10", "58.212.110.10", "122.192.39.198", "122.192.39.198", "58.240.30.132", "49.74.100.1", "223.65.191.81", "49.80.241.74", "122.96.123.60", "49.74.36.252", "101.231.76.98", "183.213.77.234", "49.74.36.252", "221.6.3.93", "183.213.77.234", "58.240.30.132", "101.231.76.98", "58.240.30.132", "101.231.76.98", "221.6.3.93", "101.231.76.98", "121.237.99.93", "220.178.99.130", "101.231.76.98", "220.178.99.130", "220.178.99.130", "180.111.150.46", "117.89.185.58", "117.89.185.58", "117.89.185.58", "114.222.218.147", "106.37.236.176", "121.237.3.156", "49.65.83.239", "121.237.121.216", "49.74.36.252", "223.65.140.181", "180.102.220.60", "49.65.225.50", "49.77.239.188", "49.65.225.50", "49.65.225.50", "49.77.239.188", "101.231.76.98", "58.213.149.218", "49.77.241.184", "49.77.216.109", "49.77.239.188", "123.77.73.105", "49.77.241.184", "223.65.191.135", "121.225.152.121", "180.98.20.28", "120.210.161.93", "117.89.31.156", "222.177.23.161", "223.65.191.45", "223.240.137.32", "112.2.238.102", "223.244.226.223", "223.64.62.162", "117.89.188.160", "114.238.103.173", "180.109.110.200", "58.212.231.146", "218.94.101.67", "218.94.41.154", "180.109.110.200", "49.77.72.242", "49.77.72.242", "49.77.145.25", "49.77.72.242", "121.237.121.235", "122.83.186.101", "114.222.158.42", "120.209.195.114", "218.23.42.103", "58.240.110.83", "120.210.164.184", "114.96.24.248", "117.89.30.212", "114.98.104.228", "114.98.22.23", "114.98.22.23", "218.94.41.154", "49.74.234.117", "223.65.143.156", "114.98.211.68", "117.89.49.43", "49.77.212.22", "49.74.228.140", "221.226.240.72", "183.160.24.4", "49.77.189.44", "60.166.58.202", "183.165.51.57", "117.89.48.130", "117.89.109.175", "218.94.117.74", "49.76.78.29", "49.77.72.242", "49.77.72.242", "49.74.85.123", "100.107.144.157", "223.65.142.65", "49.77.72.242", "49.80.105.148", "117.64.56.233", "58.212.64.153", "49.77.72.242", "223.65.142.15", "221.6.3.92", "221.226.215.130", "112.80.145.217", "180.109.93.218", "223.65.62.145", "122.88.53.154", "58.212.54.161", "121.224.173.52", "101.109.16.92", "180.109.207.32", "180.102.184.177", "49.65.70.95", "49.65.70.95", "153.3.118.156", "58.214.199.11", "223.64.235.253", "58.214.199.11", "58.214.199.11", "221.6.3.93", "101.105.199.196", "222.95.88.116", "60.168.248.155", "112.4.46.100", "223.65.190.107", "180.110.198.108", "223.65.191.246", "58.240.156.131", "121.229.184.21", "121.229.184.21", "112.2.241.70", "112.2.241.70", "121.237.158.92", "180.110.209.132", "117.67.238.86", "222.95.94.73", "223.65.188.41", "117.67.238.86", "180.109.130.193", "180.109.130.193", "223.65.140.194", "114.228.36.122", "114.228.36.122", "180.109.209.125", "117.64.113.255", "49.77.186.91", "223.65.143.228", "114.98.132.181", "222.95.8.59", "114.222.207.23", "222.191.238.106", "223.65.191.81", "180.109.132.212", "121.229.122.230", "114.222.88.53", "121.237.44.30", "121.237.44.30", "180.106.51.210", "49.65.159.234", "120.195.113.9", "49.77.219.4", "49.77.219.4", "180.116.44.125", "117.83.147.79", "124.113.157.219", "36.6.61.130", "36.6.61.130", "124.113.157.219", "121.237.223.137", "114.98.48.23", "122.95.29.96", "197.111.223.231", "180.111.72.164", "49.74.199.188", "124.113.157.219", "124.113.157.219", "180.111.242.15", "180.111.242.15", "49.74.79.186", "49.77.213.14", "180.111.242.15", "49.74.79.186", "180.111.242.15", "221.6.3.92", "221.6.3.92", "114.97.23.72", "117.89.50.171", "49.73.144.121", "117.88.60.134", "49.73.144.121", "120.210.161.95", "58.100.132.253", "120.210.161.95", "221.226.113.242", "223.65.142.197", "222.94.206.225", "180.110.60.106", "180.102.131.68", "223.65.141.56", "106.37.236.188", "117.88.202.170", "221.224.42.130", "122.88.22.210", "121.237.31.117", "180.110.169.226", "223.65.188.76", "180.102.218.19", "218.108.64.122", "180.110.212.105", "180.110.212.105", "183.160.25.51", "60.168.17.247", "211.103.107.78", "120.210.164.209", "223.65.140.17", "223.65.140.17", "49.80.114.77", "180.109.80.39", "49.65.215.98", "223.65.142.115", "223.240.114.200", "117.88.157.157", "223.65.142.106", "221.226.221.135", "49.77.72.242", "180.109.12.215", "49.77.72.242", "49.77.189.246", "49.77.72.242", "49.77.72.242", "60.168.82.203", "114.226.159.154", "183.213.77.231", "121.229.219.211", "117.89.109.114", "223.65.10.239", "117.89.55.226", "49.77.233.254", "112.86.208.61", "121.237.229.117", "121.237.229.117", "121.237.229.117", "121.237.21.89", "114.221.68.105", "153.3.44.135", "112.86.134.113", "58.222.188.126", "221.131.84.101", "60.168.29.144", "101.244.6.28", "180.109.210.65", "60.166.113.162", "49.65.250.155", "112.81.2.232", "36.33.8.6", "100.68.142.67", "49.77.218.175", "221.6.3.93", "223.65.141.169", "117.80.234.193", "58.212.208.125", "49.80.109.0", "49.80.182.27", "223.3.34.250", "49.80.182.27", "122.96.29.192", "222.95.219.129", "222.95.219.129", "180.110.13.199", "222.191.149.113", "60.168.15.211", "223.65.142.154", "49.65.215.143", "122.96.9.173", "114.228.38.255", "180.111.132.188", "183.212.171.19", "49.66.43.77", "223.65.141.76", "223.65.141.76", "113.249.119.138", "49.74.155.97", "112.0.39.24", "117.88.185.4", "49.77.242.188", "121.228.247.175", "121.237.72.9", "114.222.16.100", "121.237.72.9", "122.92.9.243", "112.80.72.199", "122.96.139.173", "222.94.232.241", "114.96.230.45", "222.94.232.241", "114.219.32.246", "180.109.1.156", "180.109.1.156", "117.89.74.218", "49.77.238.146", "112.22.171.20", "117.89.49.165", "117.89.49.165", "117.89.49.165", "122.96.124.94", "122.96.124.94", "122.88.58.209", "223.65.143.203", "49.74.234.167", "220.178.122.150", "223.65.142.9", "49.77.186.216", "218.94.72.150", "60.186.200.217", "218.94.72.150", "223.65.142.231", "114.221.253.101", "112.25.191.2", "123.77.101.246", "218.2.227.155", "223.240.237.121", "223.240.237.121", "180.109.8.184", "122.96.47.70", "114.221.104.68", "114.221.104.68", "180.102.210.77", "58.33.140.182", "58.33.140.182", "117.82.197.225", "223.65.143.252", "112.87.157.77", "112.87.157.77", "114.222.200.108", "114.222.200.108", "112.86.137.241", "121.229.212.216", "121.229.140.234", "121.229.140.234", "121.229.140.234", "218.23.114.6", "223.65.143.31", "218.23.114.6", "122.88.25.76", "100.65.90.99", "49.77.244.165", "180.110.125.117", "100.66.51.28", "49.65.87.196", "117.68.229.233", "100.66.51.28", "221.226.49.210", "49.74.229.196", "49.73.57.139", "183.160.11.198", "49.77.157.208", "222.94.90.66", "117.136.35.2", "223.65.188.18", "61.173.203.254", "180.111.151.154", "223.64.234.172", "153.3.53.59", "49.77.32.19", "113.246.86.152", "223.64.61.126", "121.237.39.146", "223.65.11.27", "223.65.11.27", "49.77.216.191", "117.150.214.3", "218.22.51.134", "218.22.51.134", "183.209.139.57", "223.65.190.25", "101.231.76.98", "223.65.188.232", "222.95.242.156", "49.77.156.56", "180.102.214.168", "180.110.162.213", "221.226.113.242", "112.86.213.215", "112.86.213.215", "223.65.55.77", "223.65.141.117", "221.6.3.92", "223.65.188.245", "223.65.188.245", "61.190.12.134", "223.65.188.245", "223.64.236.208", "180.102.219.197", "221.226.46.130", "49.77.241.184", "220.178.10.10", "49.77.224.103", "49.77.128.98", "49.75.125.141", "49.77.174.164", "114.221.29.231", "49.77.174.164", "49.77.128.98", "117.89.114.239", "114.222.90.94", "49.74.195.156", "180.110.174.3", "49.74.195.156", "117.89.49.234", "117.89.49.234", "117.89.49.234", "117.89.49.234", "180.110.174.3", "180.111.34.155", "218.93.73.242", "180.110.174.3", "113.108.195.132", "223.65.8.192", "222.190.112.2", "223.65.8.192", "222.190.112.2", "222.190.112.2", "222.190.112.2", "223.65.8.192", "222.190.112.2", "49.65.142.21", "120.195.69.210", "218.93.73.242", "49.65.142.21", "222.190.112.2", "121.237.252.224", "120.195.69.210", "49.65.142.21", "222.190.112.2", "120.195.69.210", "222.190.112.2", "49.77.145.171", "113.108.195.132", "223.65.8.192", "223.65.8.192", "223.65.8.192", "222.190.112.2", "122.96.12.248", "117.89.130.174", "117.89.130.174", "60.166.45.154", "219.82.238.230", "60.166.45.154", "58.240.33.162", "220.178.98.36", "180.111.74.223", "220.178.98.36", "180.111.74.223", "220.178.98.36", "58.240.33.162", "221.221.13.3", "180.110.163.14", "221.221.13.3", "180.111.74.223", "121.225.211.237", "121.225.24.145", "221.221.13.3", "182.34.25.106", "114.98.77.56", "223.65.188.87", "221.221.13.3", "218.22.104.74", "121.225.211.237", "223.65.188.87", "218.22.104.74", "49.65.142.21", "121.225.211.237", "119.32.52.2", "58.240.33.162", "49.65.142.21", "121.225.211.237", "49.65.142.21", "220.178.98.36", "218.22.104.74", "121.225.159.246", "218.22.104.74", "112.3.247.118", "220.191.249.130", "211.138.191.83", "114.222.83.34", "121.229.31.210", "121.229.31.210", "218.11.179.4", "49.77.221.200", "221.226.41.2", "221.226.2.254", "221.226.2.254", "112.4.128.215", "112.2.242.210", "112.2.242.210", "222.190.112.2", "218.23.45.2", "223.65.188.87", "182.34.25.106", "112.2.242.210", "180.110.133.31", "223.65.8.192", "180.110.133.31", "180.110.133.31", "180.110.133.31", "218.22.16.226", "218.94.3.10", "218.11.179.4", "121.229.157.122", "114.221.101.131", "223.167.118.206", "180.110.211.233", "211.138.191.83", "121.237.69.129", "114.221.101.131", "222.94.217.133", "180.111.192.170", "58.213.141.110", "222.92.228.42", "114.222.163.222", "222.92.228.42", "180.110.212.230", "222.94.149.51", "222.94.149.51", "58.240.104.171", "49.77.207.87", "58.212.22.51", "49.74.155.194", "49.77.241.219", "58.212.22.51", "114.221.81.168", "222.190.111.226", "222.94.45.21", "114.98.63.163", "222.94.45.21", "114.221.73.230", "49.80.127.241", "218.94.70.45", "58.213.51.162", "112.80.141.213", "218.94.4.170", "58.213.51.162", "222.94.160.252", "218.94.4.170", "49.77.116.126", "49.77.116.126", "180.114.41.205", "180.109.110.2", "221.226.191.146", "114.98.77.145", "121.225.128.251", "114.98.77.145", "49.77.129.202", "180.110.19.139", "112.2.243.73", "121.229.43.196", "180.110.19.139", "112.2.243.73", "211.103.12.162", "122.96.116.51", "36.57.137.205", "58.212.21.196", "49.65.192.102", "58.212.252.62", "180.110.190.200", "202.102.194.70", "114.221.171.2", "115.236.84.10", "120.210.164.248", "180.109.27.202", "49.65.194.188", "221.226.9.195", "221.226.9.195", "222.190.119.4", "49.65.194.188", "58.213.114.130", "222.95.198.67", "49.77.140.76", "121.229.85.185", "36.63.4.50", "58.212.62.39", "58.212.62.39", "106.37.236.185", "112.2.233.7", "106.37.236.185", "222.94.207.190", "222.94.207.190", "114.228.137.118", "112.2.233.7", "112.2.233.7", "222.94.207.190", "114.228.137.118", "112.2.233.7", "222.93.89.195", "120.210.165.213", "218.94.72.150", "180.111.149.179", "180.109.192.141", "121.237.229.240", "114.228.137.118", "114.96.226.96", "220.180.131.37", "220.180.131.37", "220.178.35.211", "180.102.212.9", "218.94.74.172", "114.222.221.100", "223.68.141.74", "223.65.140.212", "180.111.151.4", "49.77.26.220", "223.65.140.173", "112.4.57.72", "123.139.40.10", "117.39.22.4", "49.77.145.57", "218.94.67.18", "49.65.161.82", "114.222.120.169", "222.95.242.100", "222.95.223.156", "122.96.42.89", "222.177.23.161", "180.109.47.81", "49.77.145.57", "114.221.126.95", "221.226.9.61", "117.63.182.92", "114.98.16.48", "117.63.182.92", "114.222.120.169", "60.166.49.115", "114.222.120.169", "114.222.120.169", "114.222.120.169", "218.94.77.50", "114.222.120.169", "1.86.136.179", "114.222.120.169", "218.4.142.83", "58.240.100.228", "58.212.70.84", "221.226.84.106", "218.94.77.50", "61.132.137.100", "58.214.9.150", "221.226.38.178", "49.77.134.43", "114.98.75.215", "121.225.44.249", "60.166.38.226", "49.80.228.178", "180.111.79.250", "218.91.115.242", "121.229.43.158", "218.22.51.134", "61.132.138.212", "112.4.49.122", "121.225.152.167", "222.190.126.142", "222.94.148.117", "60.176.136.165", "60.176.136.165", "114.222.122.230", "120.210.160.49", "114.222.122.230", "60.176.136.165", "61.177.119.227", "61.177.119.227", "61.177.119.227", "114.221.183.202", "58.212.148.232", "58.212.148.232", "222.94.195.221", "222.126.160.20", "180.110.17.72", "117.84.165.104", "100.91.244.142", "49.77.249.246", "121.237.201.109", "117.88.149.56", "221.226.120.214", "221.226.120.214", "49.74.85.70", "58.215.199.74", "117.89.156.55", "58.215.199.74", "58.215.199.74", "180.110.221.185", "180.109.202.200", "223.65.78.168", "112.80.233.163", "223.65.78.168", "180.109.160.10", "121.229.49.76", "121.237.202.100", "121.225.23.135", "121.225.23.135", "221.226.29.198", "117.36.48.166", "218.94.21.6", "58.211.27.74", "58.213.114.59", "222.95.249.82", "220.178.116.146", "121.225.23.135", "114.221.43.73", "220.178.116.146", "180.110.15.102", "58.213.23.98", "112.22.103.52", "112.22.103.52", "114.222.109.205", "218.11.179.2", "117.81.80.226", "222.92.16.66", "180.111.128.39", "58.240.66.98", "114.222.109.205", "218.94.142.39", "121.237.202.100", "114.222.109.205", "222.95.219.87", "114.222.109.205", "121.225.23.135", "121.225.23.135", "58.212.96.51", "114.222.180.35", "58.242.219.137", "223.65.141.98", "223.65.141.196", "223.65.141.196", "180.99.186.13", "223.240.116.94", "60.166.229.96", "114.98.124.145", "180.111.82.89", "114.98.124.145", "221.226.93.70", "222.90.170.186", "61.236.219.114", "220.178.81.210", "222.90.170.186", "114.222.180.35", "221.226.93.70", "183.160.142.197", "49.74.150.31", "221.6.3.80", "180.111.148.95", "180.98.19.179", "117.36.48.166", "60.168.69.204", "100.64.31.244", "220.178.86.230", "60.168.69.204", "124.73.72.246", "49.77.118.238", "117.88.155.138", "114.226.89.156", "221.226.191.194", "218.94.96.137", "180.98.1.45", "180.102.213.209", "60.166.45.154", "218.94.96.137", "121.229.49.76", "114.222.120.169", "114.98.61.1", "122.96.107.175", "114.222.120.169", "49.77.156.108", "223.65.191.165", "114.222.120.169", "114.222.120.169", "221.226.83.35", "121.237.155.159", "121.237.155.159", "218.22.18.213", "112.4.152.150", "121.229.179.17", "60.168.115.224", "183.69.230.8", "114.96.30.127", "222.95.109.189", "117.81.242.27", "49.77.217.18", "121.225.193.157", "223.65.188.110", "121.239.100.195", "121.225.44.249", "222.95.221.144", "222.190.122.234", "222.190.122.234", "180.111.131.16", "221.6.3.92", "114.98.82.200", "49.65.141.95", "218.94.72.150", "180.110.161.153", "180.110.161.153", "117.38.185.50", "222.95.84.29", "221.226.56.237", "218.94.82.115");
    $randnum = rand(0, 8296);
    return $iparr[$randnum];
  }

  //curl采集
  public function vcurl($url, $compress = '', $sp = 0)
  {
    $tmpInfo = '';
    $curl = curl_init();
    $ip = $this->getRandIp();
    $headers = array("X-FORWARDED-FOR:$ip", 'CLIENT-IP:' . $ip);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
    if ($sp == 1) {
      curl_setopt($curl, CURLOPT_URL, "http://218.94.115.131:18088/njesfcj.php?url=" . $url);
      $t_url = $t_url ? $t_url : "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      curl_setopt($curl, CURLOPT_REFERER, $t_url); //来路
    } else {
      curl_setopt($curl, CURLOPT_URL, $url);
    }
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if ($compress != '') {
      curl_setopt($curl, CURLOPT_ENCODING, $compress);
    }
    $tmpInfo = curl_exec($curl);
    curl_close($curl);
    return $tmpInfo;
  }

  //替换空格字符串等
  public function con_replace($str)
  {
    $patterns[0] = "/	/";
    $patterns[1] = "/\n/";
    $patterns[2] = "/\r/";
    $patterns[3] = "/ /";
    $patterns[4] = "/&nbsp;/";
    $replacements[0] = "";
    $replacements[1] = "";
    $replacements[2] = "";
    $replacements[3] = "";
    $replacements[4] = "";
    $arr = preg_replace($patterns, $replacements, $str);
    return $arr;
  }

  //检测入库、更新    sell_house_collect_check
  public function import_update_sell_check($id, $status)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->sell_house_collect_check, 'where' => $where, 'select' => array('id')), 'db');
    if (empty($result)) {
      $where = $where + array('status' => $status, 'checktime' => time());
      $this->add_data($where, 'db', $this->sell_house_collect_check);
    } else {
      $data = array('status' => $status, 'checktime' => time());
      $this->modify_data($where, $data, 'db', $this->sell_house_collect_check);
    }
  }

  //检测入库、更新    rent_house_collect_check
  public function import_update_rent_check($id, $status)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->rent_house_collect_check, 'where' => $where, 'select' => array('id')), 'db');
    if (empty($result)) {
      $where = $where + array('status' => $status, 'checktime' => time());
      $this->add_data($where, 'db', $this->rent_house_collect_check);
    } else {
      $data = array('status' => $status, 'checktime' => time());
      $this->modify_data($where, $data, 'db', $this->rent_house_collect_check);
    }
  }
  //source_from 0=》赶集；1=》58同城
  //出售数据检查
  function check_sell_ajax($data)
  {
    if ($data['source_from'] == 1) {
      $status = $this->check_wuba($data);
    } else {
      $status = $this->check_ganji($data);
    }
    $this->import_update_sell_check($data['id'], $status);
    return $status;
  }

  //出租数据检查
  function check_rent_ajax($data)
  {
    if ($data['source_from'] == 1) {
      $status = $this->check_wuba($data);
    } else {
      $status = $this->check_ganji($data);
    }
    $this->import_update_rent_check($data['id'], $status);
    return $status;
  }

  //58下架检查
  function check_wuba($data)
  {
    $compress = 'gzip';
    $con = $this->vcurl($data['oldurl'], $compress);//采集详情页
    preg_match('/<h2 class="item">(.*)<\/h2>/siU', $con, $mess);
    $choose = $this->con_replace($mess[1]);
    if (strstr($choose, "该页面可能被删除")) {
      $status = 2;
    } else {
      $status = 1;
    }
    return $status;
  }

  //赶集下架检查
  function check_ganji($data)
  {
    $compress = 'gzip';
    preg_match('/http:\/\/(.*).ganji.com\/(.*)\/(.*).htm/siU', $data['oldurl'], $urlarr);
    if (is_array($urlarr) && count($urlarr) == 4) {
      $cpurl = "http://wap.ganji.com/" . $urlarr[1] . "/$urlarr[2]/" . $urlarr[3];
      $con = $this->vcurl($cpurl, $compress);//采集详情页
      preg_match('/<font color="#FF7609">(.*)<\/font>/siU', $con, $mess);
      $choose = $this->con_replace($mess[1]);
    }
    if (strstr($choose, "此信息已被删除")) {
      $status = 2;
    } else {
      $status = 1;
    }
    return $status;
  }
  //****************************************检查数据end**************************************

  /**
   * 出售下架检测
   * sell_house_collect_check
   */
  function check_sell_house($id)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->sell_house_collect_check, 'where' => $where), 'dbback');
    return $result[0];
  }

  /**
   * 出租下架检测
   * rent_house_collect_check
   */
  function check_rent_house($id)
  {
    $where = array('id' => $id);
    $result = $this->get_data(array('form_name' => $this->rent_house_collect_check, 'where' => $where), 'dbback');
    return $result[0];
  }

  //检查该房源同公司是否已经录入
  public function collect_sell_publish_check($collect_id, $company_id)
  {
    $where = array('sell_house_sub.collect_id ' => $collect_id, 'sell_house.company_id ' => $company_id);
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->sell_house_sub);
    $this->dbback_city->join($this->sell_house, "$this->sell_house_sub.id = $this->sell_house.id");
    //返回结果
    return $this->dbback_city->count_all_results();
  }

  public function collect_rent_publish_check($collect_id, $company_id)
  {
    $where = array('rent_house_sub.collect_id ' => $collect_id, 'rent_house.company_id ' => $company_id);
    $this->dbback_city->where($where);
    $this->dbback_city->from($this->rent_house_sub);
    $this->dbback_city->join($this->rent_house, "$this->rent_house_sub.id = $this->rent_house.id");
    //返回结果
    return $this->dbback_city->count_all_results();
  }

  /**
   * 获取已保存的搜索条件的条数
   * 2016.5.26
   * cc
   */
  public function get_collect_set_num($id, $type)
  {
    $where = "broker_id = " . $id . " and type = " . "'$type'";
    $this->dbback_city->select('count(*) as num');
    $this->dbback_city->from('collect_set_cooperate');
    $this->dbback_city->where($where);
    $result = $this->dbback_city->get()->row_array();
    return $result['num'];
  }

  /**
   * 获取已保存的搜索条件
   * 2016.5.26
   * cc
   */
  public function get_collect_set_info($broker_id, $type)
  {
    $where = array(
      'broker_id' => $broker_id,
      'type' => $type
    );
    $result = $this->get_data(array('form_name' => $this->collect_set, 'where' => $where), 'db_city');
    return $result;
  }

  /**
   * 判断是否已经存在
   * 2016.5.26
   * cc
   */
  public function get_collect_set($post_param)
  {
    $result = $this->get_data(array('form_name' => $this->collect_set, 'where' => $post_param, 'select' => array('*')), 'dbback_city');
    return $result;
  }

  /**
   * 保存搜索条件
   * 2016.5.26
   * cc
   */
  public function save_collect_set($param)
  {
    if ($this->db_city->insert('collect_set_cooperate', $param)) {
      return $this->db_city->insert_id();
    }
    return 0;
  }

  /**
   * 删除采集设置条件
   * 2016.5.27
   * cc
   */
  public function delete_collect_set_byid($id)
  {
    $this->db_city->where('id', $id);
    $this->db_city->delete('collect_set_cooperate');
    $num = $this->db_city->affected_rows();
    return $num;
  }
}


/* End of file collections_model.php */
/* Location: ./application/mls/models/collections_model.php */
