<?php

namespace App\Core;

class KuriakosPage extends KuriakosPlugin {

    public function __construct(string $file) {

        register_activation_hook($file, [$this, 'page']);

        parent::__construct($file);
    }

    public function page()
    {
        $page = get_page_by_path('ktv-estaeahora-dev');

        if(!$page) {
            $new_page = array(
                'post_title' => 'KuriakosTV - Esta é a hora',
                'post_name' => 'ktv-estaeahora-dev',
                'post_content' => require_once(__DIR__ . '/../../Resources/Views/api/telegram.php'), // Shortcode que será substituído pelo conteúdo
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1
            );

            wp_insert_post($new_page);
        }
    }

    public static function init(string $file)
    {
        static $instance;
        if (!$instance) {
            $instance = new KuriakosPage($file);
        }
        return $instance;
    }
}