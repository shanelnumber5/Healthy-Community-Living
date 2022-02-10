<?php

namespace Endurance\WP\Module\Data\Listeners;

/**
 * Monitors Bluehost plugin events
 */
class BHPlugin extends Listener {

	/**
	 * Register the hooks for the listener
	 *
	 * @return void
	 */
	public function register_hooks() {
		// Site Launched - Coming Soon page disabled
		add_filter( 'pre_update_option_mm_coming_soon', array( $this, 'site_launch' ), 10, 2 );

		// SSO
		add_action( 'eig_sso_success', array( $this, 'sso_success' ), 10, 2 );
		add_action( 'eig_sso_fail', array( $this, 'sso_fail' ) );

		// Staging
		add_action( 'bh_staging_command', array( $this, 'staging' ) );
	}

	/**
	 * Disable Coming Soon
	 *
	 * @param string $new_option New value of the mm_coming_soon option
	 * @param string $old_option Old value of the mm_coming_soon option
	 *
	 * @return string The new option value
	 */
	public function site_launch( $new_option, $old_option ) {
		// Ensure it only fires when Coming Soon is disabled
		if ( $new_option !== $old_option && 'false' === $new_option ) {
			if ( bh_has_plugin_install_date() ) {
				$install_time = bh_get_plugin_install_date();
			} else {
				$install_time = strtotime( get_option( 'mm_install_date', gmdate( 'M d, Y' ) ) );
			}

			$data = array(
				'ttl' => time() - $install_time,
			);
			$this->push( 'site_launched', $data );
		}
		return $new_option;
	}

	/**
	 * Successful SSO
	 *
	 * @param WP_User $user User who logged in
	 * @param string  $redirect URL redirected to after login
	 *
	 * @return void
	 */
	public function sso_success( $user, $redirect ) {
		$data = array(
			'status'       => 'success',
			'landing_page' => $redirect,
		);
		$this->push( 'sso', $data );
	}

	/**
	 * SSO failure
	 *
	 * @return void
	 */
	public function sso_fail() {
		$this->push( 'sso', array( 'status' => 'fail' ) );
	}

	/**
	 * Staging commands executed
	 *
	 * @param string $command The staging command executed
	 * @return void
	 */
	public function staging( $command ) {
		$this->push( 'staging', array( 'command' => $command ) );
	}

}
