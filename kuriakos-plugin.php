<?php

/*
Plugin Name: Kuriakos TV
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: Plugin para o canal do Kuriakos TV, para o programa Esta é a Hora
Version: 1.0
Author: Eduardo Bessa
Author URI: http://eduardobessa.pt
License: GPL2
*/

use App\Core\KuriakosPage;
use App\Core\KuriakosSettings;

if(file_exists(__DIR__ . '/vendor/autoload.php')){
    require_once __DIR__ . '/vendor/autoload.php';
}else{
    die("Please run composer install");
}

if(!defined('ABSPATH')){
    exit;
}

KuriakosPage::init(__FILE__);
KuriakosSettings::init(__FILE__)->set_up();