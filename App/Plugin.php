<?php

namespace App;

use App\Core\KuriakosPlugin;

class Plugin extends KuriakosPlugin
{
    public function set_up(): void
    {

        # Add actions
        add_action('plugins_loaded', array($this, 'action_plugins_loaded'));
        add_action('init', array($this, 'action_init'));
        add_action('members_register_caps', array($this, 'action_register_members_caps'));
        add_action('members_register_cap_groups', array($this, 'action_register_members_groups'));

        # Filters
        add_filter('user_has_cap', array($this, 'filter_user_has_cap'), 10, 4);
        add_filter('ure_built_in_wp_caps', array($this, 'filter_ure_caps'));
        add_filter('ure_capabilities_groups_tree', array($this, 'filter_ure_groups'));
        add_filter('network_admin_plugin_action_links_kuriakos-plugin/kuriakos-plugin.php', array($this, 'filter_plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'filter_plugin_row_meta'), 10, 2);

        $collectors = array();
        $files = glob($this->plugin_path('collectors/*.php'));

        if ($files) {
            foreach ($files as $file) {
                $key = basename($file, 'KTV_Migration_Settings_Table.php');
                $collectors[$key] = $file;
            }
        }
    }

    public function filter_plugin_action_links(array $actions)
    {
        return array_merge(array(
            'settings' => '<a href="#ktv-settings">' . esc_html__('Settings', 'kuriakos') . '</a>',
            'help' => '<a href="https://eduardobessa.pt/wordpress/plugins/kuriakos-plugin/how-to-use/">' . esc_html__('Help', 'kuriakos-plugin') . '</a>',
        ));
    }

    public function filter_plugin_row_meta(array $plugin_meta, string $plugin_file): array
    {
        if ('kuriakos-plugin/kuriakos-plugin.php' !== $plugin_file) {
            return $plugin_meta;
        }

        $plugin_meta[] = sprintf(
            '<a href="%1$s"><span class="dashicons dashicons-star-filled" aria-hidden="true" style="font-size:14px;line-height:1.3"></span>%2$s</a>',
            'https://github.com/sponsors/johnbillion',
            esc_html_x('Sponsor', 'verb', 'xtedder-plugin')
        );

        return $plugin_meta;
    }

    public function filter_user_has_cap(array $user_caps, array $required_caps, array $args, WP_User $user): array
    {
        if ('view_kuriakos_plugin' !== $args[0]) {
            return $user_caps;
        }

        if (array_key_exists('view_kuriakos_plugin', $user_caps)) {
            return $user_caps;
        }

        if (!is_multisite() && user_can($args[1], 'manage_options')) {
            $user_caps['view_kuriakos_plugin'] = true;
        }

        return $user_caps;
    }

    public function action_plugins_loaded()
    {
        if (!defined('XT_HIDE_SELF')) {
            define('XT_HIDE_SELF', true);
        }

        foreach (apply_filters('xt/collectors', array(), $this) as $collector) {
            XT_Collectors::add($collector);
        }

        foreach ((array)glob($this->plugin_path('dispatchers/*XT_Migration_Settings_Table.php')) as $file) {
            include_once $file;
        }

        foreach (apply_filters('xt/dispatchers', array(), $this) as $dispatcher) {
            XT_Dispatchers::add($dispatcher);
        }
    }

    public function action_init()
    {
        load_plugin_textdomain('xtedder-plugin', false, dirname($this->plugin_base()) . '/languages');
    }

    public static function symlink_warning()
    {
        $db = WP_CONTENT_DIR . '/db.php';
        trigger_error(sprintf(
        /* translators: %s: Symlink file location */
            esc_html__('The symlink at %s is no longer pointing to the correct location. Please remove the symlink, then deactivate and reactivate Query Monitor.', 'xtedder-plugin'),
            '<code>' . esc_html($db) . '</code>'
        ), E_USER_WARNING);
    }

    public function action_register_members_groups()
    {
        members_register_cap_group('xtedder_plugin', array(
            'label' => __('Xtedder', 'xtedder-plugin'),
            'caps' => array(
                'view_xtedder_plugin',
            ),
            'icon' => 'dashicons-settings-tools',
            'priority' => 30,
        ));
    }

    public function action_register_members_caps()
    {
        members_register_cap('view_xtedder_plugin', array(
            'label' => _x('View Xtedder Classes', 'Human readable label for the user capability required to view Xtedder Classes.', 'xtedder-plugin'),
            'group' => 'xtedder_plugin',
        ));
    }

    public function filter_ure_groups(array $groups)
    {
        $groups['xtedder_plugin'] = array(
            'caption' => esc_html__('Xtedder', 'xtedder-plugin'),
            'parent' => 'custom',
            'level' => 2,
        );

        return $groups;
    }

    public function filter_ure_caps(array $caps)
    {
        $caps['view_xtedder_plugin'] = array(
            'custom',
            'xtedder_plugin',
        );

        return $caps;
    }

    public function action_cease()
    {
        // iterate collectors, call tear_down
        // discard all collected data
        XT_Collectors::cease();

        // remove dispatchers or prevent them from doing anything
        XT_Dispatchers::cease();
    }

    public static function init($file)
    {
        static $instance = null;

        if (!$instance) {
            $instance = new Plugin($file);
        }

        return $instance;
    }
}