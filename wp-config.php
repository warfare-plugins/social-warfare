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
define('DB_NAME', 'melanjk4_social-warfare');

/** MySQL database username */
define('DB_USER', 'melanjk4_sw');

/** MySQL database password */
define('DB_PASSWORD', 'social-warfare');

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
define('AUTH_KEY',         '3O0d7beWl8o[;Y,W=Nj6sy7}0x|^V/V,V6Jx{+|v-*PH#5x~R&`!vxG^B9keI;Tx');
define('SECURE_AUTH_KEY',  ')EJ?Pk([=Wvatc0q[` O VFe5g!;9MM4qgT;4~?KkE0czdK7}cd[;e5:sz>WUYX^');
define('LOGGED_IN_KEY',    'LEU/aC@`8?UAWj4iaeTkL98hIwka){K6g-3Ev!!D}=vs*;S(*+W00Kvm  @0tOKx');
define('NONCE_KEY',        '@0z2*A0g#m41N,!WlG+?mAIVK`#:vGZ^*qMh$EfcEgYm<EwQ%G|UrXYad!v9cy|{');
define('AUTH_SALT',        'hVw19~t552?_Cj49sn|y%&/k(V6q)`d^6gxG{^4bSY<]8s#/VC8N%&K#,O%M0 Gt');
define('SECURE_AUTH_SALT', '@mZZ<r:n#lMX3gwv%djdohP7X9m|xcybY~*O5x3PU,O7A*DF]s2zvFI][bvyiLU*');
define('LOGGED_IN_SALT',   'N3{?(P>D]p$-5zsS/*cxU%1*5Ee1wn5jf@K{$ YTK0_W/E6IGD0(j=-T*./EMgk|');
define('NONCE_SALT',       '?9V-8PD3m7,%2dDY?Skm$wT(8%;UJ?+[ d?zJtVlJ$6c5tou2<u%-8tzAli@Y?im');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
