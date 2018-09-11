<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// --------------------------------------------------------------------------
// Servers
// --------------------------------------------------------------------------
$sphinx['servers'] = array(
  'host' => '127.0.0.1',
  'port' => 9312
);

$sphinx['source'] = array(
  'collect_sell' => 'collect_sell',
  'collect_rent' => 'collect_rent'
);

$config['sphinx'] = $sphinx;

/* End of file sphinx_conf.php */
/* Location: ./applications/config/sphinx_conf.php */
