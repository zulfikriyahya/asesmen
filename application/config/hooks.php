<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$hook['post_controller'] = array(       // post_controller menunjukan eksekusi hooks                                                                                    setelah pengontrolan selesai
    'class'    => 'Db_log',         // Nama Class
    'function' => 'logQueries',     // Nama method yang di eksekusi dari class
    'filename' => 'Db_log.php',     // Nama File Hook
    'filepath' => 'hooks'           // nama folder tempat file hook disimpan
);