<?php

namespace App\Core;
use QM_Hook;
use WP_User;
use XT_Data_Fallback;
use XT_Hook;
use XT_Timer;

abstract class KuriakosCollector
{

    protected mixed $timer;
    protected mixed $data;

    protected static mixed $hide_xt = null;

    public array $concerned_actions = array();
    public array $concerned_filters = array();
    public array $concerned_constants = array();
    public array $tracked_hooks = array();

    public string $id = '';

    public function __construct()
    {
        $this->data = $this->get_storage();
    }

    public function set_up()
    {
    }

    final public function id(): string
    {
        return "xt-{$this->id}";
    }

    protected function log_type($type)
    {
        if (isset($this->data->types[$type])) {
            $this->data->types[$type]++;
        } else {
            $this->data->types[$type] = 1;
        }
    }

    protected function log_component($component, $time, $type): void
    {
        if (!isset($this->data->component_times[$component->name])) {
            $this->data->component_times[$component->name] = array(
                'component' => $component->name,
                'ltime' => 0,
                'types' => array(),
            );
        }

        $this->data->component_times[$component->name]['ltime'] += $time;

        if (isset($this->data->component_times[$component->name]['types'][$type])) {
            $this->data->component_times[$component->name]['types'][$type]++;
        } else {
            $this->data->component_times[$component->name]['types'][$type] = 1;
        }
    }

    public static function format_bool_constant($constant): string
    {
        if (!defined($constant)) {
            return __('undefined', 'xtedder-plugin');
        } elseif (constant($constant) === '') {
            return __('empty string', 'xtedder-plugin');
        } elseif (is_string(constant($constant)) && !is_numeric(constant($constant))) {
            return constant($constant);
        } elseif (!$constant($constant)) {
            return 'false';
        } else {
            return 'true';
        }
    }

    public function get_data()
    {
        return $this->data;
    }

    public function get_storage()
    {
        return new XT_Data_Fallback();
    }

    final public function discard_data()
    {
        $this->data = $this->get_storage();
    }

    final public function set_id($id)
    {
        $this->id = $id;
    }

    final public function process_concerns()
    {
        global $wp_filter;

        $tracked = array();
        $id = $this->id;

        $concerned_actions = apply_filters("xt/collect/concerned_actions/{$id}", $this->concerned_actions);
        $concerned_filters = apply_filters("xt/collect/concerned_filters/{$id}", $this->get_concerned_filters());
        $concerned_options = apply_filters("xt/collect/concerned_options/{$id}", $this->get_concerned_options());

        $concerned_constants = apply_filters("qm/collect/concerned_constants/{$id}", $this->get_concerned_constants());

        foreach ($concerned_actions as $action) {
            if (has_action($action)) {
                $this->concerned_actions[$action] = QM_Hook::process($action, 'action', $wp_filter, true, false);
            }
            $tracked[] = $action;
        }

        foreach ($concerned_filters as $filter) {
            if (has_filter($filter)) {
                $this->concerned_filters[$filter] = QM_Hook::process($filter, 'filter', $wp_filter, true, false);
            }
            $tracked[] = $filter;
        }

        $option_filters = array(
            // Should this include the pre_delete_ and pre_update_ filters too?
            'pre_option_%s',
            'default_option_%s',
            'option_%s',
            'pre_site_option_%s',
            'default_site_option_%s',
            'site_option_%s',
        );

        foreach ($concerned_options as $option) {
            foreach ($option_filters as $option_filter) {
                $filter = sprintf(
                    $option_filter,
                    $option
                );
                if (has_filter($filter)) {
                    $this->concerned_filters[$filter] = XT_Hook::process($filter, 'filter', $wp_filter, true, false);
                }
                $tracked[] = $filter;
            }
        }

        $this->concerned_actions = array_filter($this->concerned_actions, array($this, 'filter_concerns'));
        $this->concerned_filters = array_filter($this->concerned_filters, array($this, 'filter_concerns'));

        foreach ($concerned_constants as $constant) {
            if (defined($constant)) {
                $this->concerned_constants[$constant] = constant($constant);
            }
        }

        sort($tracked);

        $this->tracked_hooks = $tracked;
    }

    public function filter_concerns($concerns)
    {
        return !empty($concerns['actions']);
    }

    public static function format_user(WP_User $user_object)
    {
        $user = get_object_vars($user_object->data);

        unset(
            $user['user_pass'],
            $user['user_activation_key'],
        );
        $user['roles'] = $user_object->roles;

        return $user;
    }

    public static function enabled()
    {
        return true;
    }

    public static function hide_xt()
    {
        if (null === self::$hide_xt) {
            self::$hide_xt = apply_filters('xt/hide_xt', false);
        }

        return self::$hide_xt;
    }

    public function filter_remove_xt(array $item)
    {
        return ('xtedder-plugin' !== $item['component']->context);
    }

    public function filter_dupe_items($items)
    {
        return (count($items) > 1);
    }

    public function process()
    {
    }

    public function tear_down()
    {
    }

    public function get_time()
    {
        return $this->timer;
    }

    public function set_timer(XT_Timer $timer)
    {
        $this->timer = $timer;
    }

    public function get_concerned_actions()
    {
        return [];
    }

    public function get_concerned_filters()
    {
        return [];
    }

    public function get_concerned_options()
    {
        return [];
    }

    public function get_concerned_constants()
    {
        return [];
    }

}