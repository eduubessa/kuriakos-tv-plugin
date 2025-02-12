<?php

namespace App;

use App\Core\KuriakosPlugin;

class Activation extends KuriakosPlugin {

	/**
	 * @param $file
	 */
    protected function __construct($file) {
        # Filters
        add_filter('pre_update_option_active_plugins', array($this, 'filter_active_plugins'));
        add_filter('pre_update_site_option_active_sitewide_plugins', array($this, 'filter_active_sitewide_plugins'));

        # Activation and deactivation
	    register_activation_hook($file, array($this, 'activate'));
		register_activation_hook($file, array($this, 'install_database'));
	    register_deactivation_hook($file, array($this, 'deactivate'));
	    register_deactivation_hook($file, array($this, 'uninstall_database'));

	    # Parent setup
        parent::__construct($file);
    }

    public function activate( $sitewide = false ): void {
        $db = WP_CONTENT_DIR . '/db.php';
        $create_symlink = defined('XT_DB_SYMLIMK') ? XT_DB_SYMLIMK : true;

		if( $create_symlink && defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
			$create_symlink = false;
		}

		if( $create_symlink && ! file_exists( $db ) && function_exists( 'symlink' ) ) {
			@symlink( $this->plugin_path('wp-content/db.php'), $db ); // phpcs:ignore
		}

		if( $sitewide ) {
			update_site_option('active_sitewide_plugins', get_site_option('active_sitewide_plugins'));
		}else{
			update_option('active_plugins', get_option('active_plugins'));
		}
    }

	public function filter_active_plugins( $plugins ) {
		if(empty($plugins)){
			return $plugins;
		}

		$f = preg_quote( basename( $this->plugin_base() ), '/' );
		$qm = preg_grep( '/' . $f . '$/', $plugins );
		$notqm = preg_grep( '/' . $f . '$/', $plugins, PREG_GREP_INVERT );

		if ( false === $qm || false === $notqm ) {
			return $plugins;
		}

		return array_merge(
			$qm,
			$notqm
		);
	}

	public function install_database()
	{
		XT_Migration::install();
	}

	public function uninstall_database()
	{
		XT_Migration::uninstall();
	}


	public function deactivate( $network_wide = false ): void {
		$admins = XT_Util::get_admins();
	}

	public static function init($file){
		static $instance = null;

		if(!$instance){
			$instance = new Activation($file);
		}

		return $instance;
	}

}