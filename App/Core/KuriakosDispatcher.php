<?php

use App\Collectors;
use App\Core\KuriakosPlugin;

abstract class KuriakosDispatcher {

	protected  $outputters = [];

	protected  $ktv;

	public $id = '';

	protected $ceased = false;

	public function __construct( KuriakosPlugin $ktv ) {
		$this->ktv = $ktv;

		if ( ! defined( 'KTV_COOKIE' ) ) {
			define( 'KTV_COOKIE', 'wp-kuriakos_' . COOKIEHASH );
		}
		if ( ! defined( 'KTV_EDITOR_COOKIE' ) ) {
			define( 'KTV_EDITOR_COOKIE', 'wp-kuriakos_plugin_' . COOKIEHASH );
		}

		add_action( 'init', [ $this, 'init'] );
	}

	abstract public function is_active();

	final public function should_dispatch() {
		$e = error_get_last();

		if( !empty($e) && ($e['type'] & KTV_ERROR_FATALS)) {
			return false;
		}

		if( !apply_filters("ktv/dispatch/{$this->id}", true, $this)) {
			return false;
		}

		return $this->is_active();
	}

	public function cease()
	{
		$this->ceased = true;

		add_filter('ktv/dispatch/{$this->id}', '__return_false');
	}

	public function get_outputters( $outputter_id ) {
		$collectors = Collectors::init();
		$collectors->process();

		$this->outputters = apply_filters("ktv/outputters/{$outputter_id}",  [], $collectors);

		return $this->outputters;
	}

	public function init() {
		if( !self::user_can_view() ) {
			do_action('ktv/cease');
			return;
		}

		if( !defined('DONOTCACHEPAGE') ){
			define('DONOTCACHEPAGE', 1);
		}

		add_action('send_headers', 'nocache_headers');
	}

	protected function before_output() {
	}

	protected function after_output() {
	}

	public static function user_can_view() {

		if( !did_action('plugins_loaded')) {
			return false;
		}

		if(current_user_can('view_query_monitor')) {
			return true;
		}

		return self::user_verified();
	}

	public static function user_verified() {
		if(isset($_COOKIE[KTV_COOKIE])) {
			return self::verify_cookie(wp_unslash($_COOKIE[KTV_COOKIE]));
		}
		return false;
	}

	public static function editor_cookie() {
		if( defined('XT_EDITOR_COOKIE') && isset($_COOKIE[KTV_EDITOR_COOKIE])) {
			return self::verify_cookie(wp_unslash($_COOKIE[KTV_EDITOR_COOKIE]));
		}

		return '';
	}

	public static function verify_cookie($value) {
		$old_user_id = wp_validate_auth_cookie($value, 'logged_in');
		if($old_user_id){
			return user_can($old_user_id, 'view_kuriakos_plugin');
		}
		return false;
	}

	public static function switch_to_locale($locale){
		global $wp_locale_switcher;

		if($wp_locale_switcher instanceof WP_Locale_Switcher) {
			return switch_to_locale($locale);
		}

		return false;
	}

	public static function restore_previous_locale() {
		global $wp_locale_switcher;

		if($wp_locale_switcher instanceof WP_Locale_Switcher) {
			return restore_previous_locale();
		}

		return false;
	}
}