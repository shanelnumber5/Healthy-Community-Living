<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ealtibj2_wp754' );

/** Database username */
define( 'DB_USER', 'ealtibj2_wp754' );

/** Database password */
define( 'DB_PASSWORD', 'p5s@4Ski2-' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ka6xjknqcuigeprgsfoirs6rxbqhillhpwtxhvdfo5ilszz3gueb2k0wrxvd7fbi' );
define( 'SECURE_AUTH_KEY',  'vuywwm2savjcsogph3i8iigp6rnjgvtnnjsgdti7ca7b84jsodh56ktnnahanhzu' );
define( 'LOGGED_IN_KEY',    'mevujs0orbwytgu2tws8sbrs2qxpegoga2ecf0rgxrjdb7ygoaxspogsxchonbli' );
define( 'NONCE_KEY',        'ozdaq3cmlynvumewulo0wxy5qp3y3pjmmlmunsgrbwm7z7feoxg9rowqogzq9zsv' );
define( 'AUTH_SALT',        'l0xpfya5dli5uktnwi1vutfr8enbklutbpbbino54hjsuxg1iaz0y7k4i36trjgz' );
define( 'SECURE_AUTH_SALT', 'sskqspzexkbvzt6txj95hv0bwetj8r2orqg1sgis2mgqongbde8szz5a3xeyeuqa' );
define( 'LOGGED_IN_SALT',   '0caqw8kzf6bi4tb65ore00gxxxdmqv6pjajel6aezbxcvhur9th3rwl9zbldusol' );
define( 'NONCE_SALT',       'b8jkhdbvis43pfvrtvsdxgm75ntvebbza1lxupn3e5ovfjl1gwxskp23fz0tqtle' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpkr_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
