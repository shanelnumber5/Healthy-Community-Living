<?php

/**
 * Class Endurance_Options
 *
 * A class for handling the fetching, saving and manipulation of options for the Endurance plugin.
 * All options data is stored in a single database option, but this class allows you to individually
 * set or get options within it.
 */
class Endurance_Options {

	/**
	 * The name where our option is stored in the database.
	 *
	 * @var string
	 */
	protected $option_name;

	/**
	 * Stores all options
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Tracks whether a save is necessary.
	 *
	 * @var bool
	 */
	protected $should_save = false;

	/**
	 * Class constructor.
	 *
	 * @param string $option_name
	 */
	public function __construct( $option_name ) {
		$this->option_name = $option_name;
		$this->options = $this->fetch();
	}

	/**
	 * Check if an option exists.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function has( $name ) {
		return isset( $this->options[ $name ] );
	}

	/**
	 * Get an option by name.
	 *
	 * @param string $name
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $name, $default = null ) {
		return $this->has( $name ) ? $this->options[ $name ] : $default;
	}

	/**
	 * Set an option by name.
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function set( $name, $value ) {
		if ( ! $this->has( $name ) || $this->get( $name ) !== $value ) {
			$this->options[ $name ] = $value;
			$this->should_save = true;
		}
	}

	/**
	 * Populate all options at once.
	 *
	 * @param array $data
	 */
	public function populate( array $data ) {
		$this->options = $data;
	}

	/**
	 * Delete an option by name.
	 *
	 * @param string $name
	 */
	public function remove( $name ) {
		if ( $this->has( $name ) ) {
			unset( $this->options[ $name ] );
			$this->should_save = true;
		}
	}

	/**
	 * Fetch options from database.
	 *
	 * @return array
	 */
	public function fetch() {
		return (array) get_option( $this->option_name, array() );
	}

	/**
	 * Save options to database.
	 *
	 * @param array $options
	 *
	 * @return bool
	 */
	public function save( array $options ) {
		return update_option( $this->option_name, $options, true );
	}

	/**
	 * Persist stored values to the database.
	 */
	public function persist() {
		$this->save( $this->options );
	}

	/**
	 * Maybe persist the options to the database (only if something changed).
	 */
	public function maybePersist() {
		if ( $this->should_save ) {
			$this->persist();
		}
	}

}