<?php

namespace App\Core;

class KuriakosSettings extends KuriakosPlugin {

    protected function __construct($file) {
        parent::__construct($file);
    }

    public function set_up() {
        if(is_admin()){
            add_action('admin_menu', array($this, 'admin_menu'));
        }
    }

    public function admin_menu() {
        add_options_page('KuriakosTV', 'KuriakosTV', 'manage_options', 'kuriakos-plugin', array($this, 'settings_page'));
    }

    public function settings_page() {
        ktv_settings_page('geral');
    }

    public static function init($file){
        static $instance = null;
        if(!$instance){
            $instance = new KuriakosSettings($file);
        }
        return $instance;
    }
}