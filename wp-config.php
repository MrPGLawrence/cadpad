<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'cadpaddc_wp370');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'j4d67juivynugz2ym1lyafaef33ewwntasfpjki82xlteh6nw8xea8vk0t2rls4p');
define('SECURE_AUTH_KEY',  '3wwj8ucx1zmkm2wnbi3kfytldqk3hv6kx7yhamhko78vlznoksu4kpsibgpw6jdf');
define('LOGGED_IN_KEY',    'o3zbdmirqqf41j7wzfbrjtraa9rrj2cfqlzzyykiljst9zjz3jko7haof5u6q8x8');
define('NONCE_KEY',        'v2tdcyxa4vluuiwp5n9uixsgp9dcyj9r8xajold0otm86qjbjtyhcyalbjauy0k9');
define('AUTH_SALT',        'e7xpqzhmfpe9ijs0szxh0kwp61covowt8761io9zmdle8x0ymgozh44vmvuxbqft');
define('SECURE_AUTH_SALT', 'z4wj9setpfzzc0b4iegpzgkray7bh6jyruhypdxb3cp2gw2wvgjaqujkiqvus7sx');
define('LOGGED_IN_SALT',   'rcqbifattlcyoqn5hqdawn1yl2gnueoyy0vlmrvw54h8jgm5ynsbkhdgudqrummp');
define('NONCE_SALT',       'dhijxb5pizf0guuqsj9vdmperzary7ctsggcntaeftukuzuyljqxisttzpjijqc4');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp4x_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
define('ALLOW_UNFILTERED_UPLOADS', true);
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


/** Sets up WordPress vars and included files. */
define('WP_CACHE', true);
$currenthost = $_SERVER['HTTP_HOST'];
$mypos = strpos($currenthost, 'localhost');
if ($mypos === false) {
define('WP_HOME','http://cadpad3d.co.uk');
define('WP_SITEURL','http://cadpad3d.co.uk');
} else {
define('WP_HOME','http://localhost:8888');
define('WP_SITEURL','http://localhost:8888');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
