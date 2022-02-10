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
define( 'DB_NAME', 'ealtibj2_wp970' );

/** Database username */
define( 'DB_USER', 'ealtibj2_wp970' );

/** Database password */
define( 'DB_PASSWORD', ')]3Se08PSp' );

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
define( 'AUTH_KEY',         'ac2xeevqvajxxcisvisso3vduujfgq3losnapcwit24o97mbpotnczpic6f5f68c' );
define( 'SECURE_AUTH_KEY',  'qlkouvxp41v20gupcvn585bjwdamoixeui1i3lhv4xezhnvdx3yqx6awzhgifhlw' );
define( 'LOGGED_IN_KEY',    'p63i4ppwshde6taf5opbycjctadbdbjrxqflzglbk6zfkzuoohkuam0tdn1fvgxc' );
define( 'NONCE_KEY',        'ejudf9wlso3j9qshqgk3yemurihn7lsumlqvahyax5nfagphgraavlycidt8svyp' );
define( 'AUTH_SALT',        'jxmhzajeobnw8i0vj9bz7nhuskwbfiparsptnjnlp7fedjkkgrlf10vhrwms6pzo' );
define( 'SECURE_AUTH_SALT', 'd2r3pbxtjj5dkdoqybl6byu3hjfjxwu8eimuptucp4kknf5rqirlmnj4gkn5db0y' );
define( 'LOGGED_IN_SALT',   '89qmumooqdbciiyhdzg8lwooyahjokqe1vxayewmhld9kvgx2jqodlkvl3zmbtx0' );
define( 'NONCE_SALT',       'dkiabvehl4btx9c6gw0bpb6xfytxcihtgxcz2q6wddfwov839d4rnvkm3xw4sbsi' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpaw_';

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
