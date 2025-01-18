<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'jfg03.h.filess.io';
$CFG->dbname    = 'edupacidmood582_winherself';
$CFG->dbuser    = 'edupacidmood582_winherself';
$CFG->dbpass    = '557d7e590fca95ab02522c20ff4a44bd1f3429d4';
$CFG->prefix    = 'mdllu_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => 3305,
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);



$CFG->wwwroot   = 'http://localhost/e-kaplan.edupac-id';
$CFG->dataroot  = 'E:\\XAMPP\\moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
