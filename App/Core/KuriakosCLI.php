<?php

namespace App\Core;

use WP_CLI;
use const WP_CONTENT_DIR;
use const KTV_DB_SYMLINK;

class KuriakosCLI extends KuriakosPlugin {

	protected function __construct($file) {
		# Register command
		WP_CLI::add_comment('xt enable', array($this, 'enable'));

		# Parent setup
		parent::__construct($file);
	}

	public function enable() {
		$drop_in = WP_CONTENT_DIR . '/db.php';

		if(file_exists($drop_in)) {
			$contents = file_get_contents($drop_in);

			if(false !== $contents && false !== strpos($contents, 'new XT_DB')) {
				exit(0);
			}else{
				WP_CLI::error('Unknown wp-content/db.php is already in place');
			}

			if(defined('XT_DB_SYMLINK') && ! KTV_DB_SYMLINK) {
				WP_CLI::warning('Creation of symlink prevented by XT_DB_SYMLINK constant.');
				exit(0);
			}

			if(! function_exists('symlink')) {
				WP_CLI::error('The symlink function is not available');
			}

			if(symlink($this->plugin_path('wp-content/db.php'), $drop_in)) {
				WP_CLI::success('wp-content/db.php symlink created');
				exit(0);
			}else{
				WP_CLI::error("Failed to create wp-content/db.php symlink");
			}
		}
	}

	public static function init($file = null) {
		static $instance = null;

		if(! $instance){
			$instance = new KuriakosCLI($file);
		}

		return $instance;
	}

}