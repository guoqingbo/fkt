<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------------
// Servers
// --------------------------------------------------------------------------
$sphinx['servers'] = array(
  'host' => '192.168.105.140',
  'port' => 9312
);

$sphinx['source'] = array(
  'nj_sell_house' => 'nj_sell_house',
  'nj_rent_house' => 'nj_rent_house'
);

$config['sphinx'] = $sphinx;

/* End of file sphinx_conf.php */
/* Location: ./applications/config/sphinx_conf.php */
