<?php

namespace App;

class Backtrace {

	protected static array $ignore_class = array(
		'wpdb'              => true,
		'hyperdb'           => true,
		'LudicrousDB'       => true,
		'QueryMonitor'      => true,
		'W3_Db'             => true,
		'Debug_Bar_PHP'     => true,
		'WP_Hook'           => true,
		'Altis\Cloud\DB'    => true,
		'Yoast\WP\Lib\ORM'  => true,
		'Perflab_SQLite_DB' => true,
		'WP_SQLite_DB'      => true,
	);

	protected static array $ignore_method = array();

	protected static array $ignore_func = array(
		'include_once'            => true,
		'require_once'            => true,
		'include'                 => true,
		'require'                 => true,
		'call_user_func_array'    => true,
		'call_user_func'          => true,
		'trigger_error'           => true,
		'_doing_it_wrong'         => true,
		'_deprecated_argument'    => true,
		'_deprecated_constructor' => true,
		'_deprecated_file'        => true,
		'_deprecated_function'    => true,
		'_deprecated_hook'        => true,
		'dbDelta'                 => true,
		'maybe_create_table'      => true,
	);

	protected static array $show_args = array(
		'do_action'                  => 1,
		'apply_filters'              => 1,
		'do_action_ref_array'        => 1,
		'apply_filters_ref_array'    => 1,
		'do_action_deprecated'       => 1,
		'apply_filters_deprecated'   => 1,
		'get_query_template'         => 1,
		'resolve_block_template'     => 1,
		'get_template_part'          => 2,
		'get_extended_template_part' => 2,
		'ai_get_template_part'       => 2,
		'load_template'              => 'dir',
		'dynamic_sidebar'            => 1,
		'get_header'                 => 1,
		'get_sidebar'                => 1,
		'get_footer'                 => 1,
		'get_transient'              => 1,
		'set_transient'              => 1,
		'class_exists'               => 2,
		'current_user_can'           => 3,
		'user_can'                   => 4,
		'current_user_can_for_blog'  => 4,
		'author_can'                 => 4,
	);

	protected static array $ignore_hook = array();

	protected static bool $filtered = false;

	protected array $args = array();

	protected array $trace = array();

	protected mixed $filtered_trace = null;
	protected int $calling_line = 0;
	protected string $calling_file = '';
	protected mixed $component = null;
	protected mixed $top_frame = null;

	public function __construct( array $args = array(), array $trace = null ) {
		$this->trace = $trace ?? debug_backtrace( 0 );

		$this->args = array_merge( array(
			'ignore_class'  => array(),
			'ignore_method' => array(),
			'ignore_func'   => array(),
			'ignore_hook'   => array(),
			'show_args'     => array(),
		), $args );

		foreach ( $this->trace as & $frame ) {
			if ( ! isset( $frame['args'] ) ) {
				continue;
			}

			if ( isset( $frame['function'], self::$show_args[ $frame['function'] ] ) ) {
				$show = self::$show_args[ $frame['function'] ];
				if ( ! is_int( $show ) ) {
					$show = 1;
				}

				$frame['args'] = array_slice( $frame['args'], 0, $show );
			} else {
				unset( $frame['args'] );
			}
		}
	}

	public function push_frame( array $frame ): void {
		$this->top_frame = $frame;
	}

	public function get_stack(): array {
		$trace = $this->get_filtered_trace();
		$stack = array_column($trace, 'display');

		return $stack;
	}

	public function get_caller()
	{
		$trace = $this->get_filtered_trace();
		return reset($trace);
	}

	public function get_component() {
		if( isset($this->component)){
			return $this->component;
		}

		$components = array();
		$frames = $this->get_filtered_trace();

		if($this->top_frame){
			array_unshift($frames, $this->top_frame);
		}

		foreach($frames as $frame)
		{
			$component = self::get_frame_component( $frame );

			if($component) {
				if( 'plugin' === $component->type ) {
					$this->component = $component;
					return $this->component;
				}

				$components[$component->type] = $component;
			}
		}

		$file_dirs = XT_Util::get_file_dirs();
		$file_dirs['dropin'] = WP_CONTENT_DIR;

		foreach($file_dirs as $type => $dir){
			if( isset($components[$type]) ){
				$this->component = $components[$type];
				return $this->component;
			}
		}
	}
}