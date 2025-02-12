<?php

namespace App\Core;

abstract class KuriakosPlugin
{

    private array $plugin = [];
    public string $file = "";

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    final public function plugin_url(string $file = "")
    {
        return $this->_plugin('url', $file);
    }

    final public function plugin_ver(string $file)
    {
        return VERSION;
    }

    final public function plugin_path(string $file = ""): string
    {
        return $this->_plugin('path', $file);
    }

    final public function plugin_base()
    {
        return $this->_plugin('base');
    }

    private function _plugin(string $item, string $file = ""): string
    {
        if (!array_key_exists($item, $this->plugin)) {
            switch ($item) {
                case 'url':
                    $this->plugin[$item] = plugin_dir_url($this->file);
                    break;
                case 'path':
                    $this->plugin[$item] = plugin_dir_path($this->file);
                    break;
                case 'base':
                    $this->plugin[$item] = plugin_basename($this->file);
                    break;
            }
        }

        return $this->plugin[$item] . ltrim($file, '/');
    }

    public static function icon($name): string
    {
        if ('blank' === $name) {
            return '<span class="qm-icon qm-icon-blank"></span>';
        }

        return sprintf(
            '<svg class="qm-icon qm-icon-%1$s" aria-hidden="true" width="20" height="20" viewBox="0 0 20 20"><use href="#qm-icon-%1$s" /></svg>',
            esc_attr($name)
        );
    }

}