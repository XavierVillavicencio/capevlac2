<?php
define( 'WP_CACHE', true );
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'Capevlac' );

/** Database username */
define( 'DB_USER', 'db_user' );

/** Database password */
define( 'DB_PASSWORD', 'xqdy=]wD' );

/** Database hostname */
define( 'DB_HOST', 'CapevlacMySql' );

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
define( 'AUTH_KEY',         'E>{Z15a@Ra4s3(~8N8t-&%J[REKm6FR4c1&q7GRWku#mC<TC9N/9>hSC[*5emfz^' );
define( 'SECURE_AUTH_KEY',  'W?MW9zJN]Ks> xp#AI<8>%7rU7t^e=0W4T^J>5W`F[ArnXG Sqv|3xnfm#WFZ[Y~' );
define( 'LOGGED_IN_KEY',    'XfCh<9VItuyifCW|;=c-qVP5GH[/80+C0M0+*?_9q7^XZG?0D4=KcU%5iF-FwgY)' );
define( 'NONCE_KEY',        'OGHsGD,%TZgd{ a8yR{Js2jte>>/ Enedg{,l:i44~O}[rk,7L,a>Ouwg>(E}H[Z' );
define( 'AUTH_SALT',        'hL8dfQ1Vq<&xGd@bvCW-~iK$;,HsTMkil~.:X|B+&!u.4x!(05CcOJE?*mJK=P33' );
define( 'SECURE_AUTH_SALT', 'q.,*s|C[UWghD.:6-,}l,>H?/*qu7epKJ~){)q /7`%+01bg|fucm:^u`IT+;RwF' );
define( 'LOGGED_IN_SALT',   'oCU=~yl,.j,/APm+G4._b3d#eF>aG*5FlHaH_.G&*}BChgYJz91][~+I|7Q_%&M*' );
define( 'NONCE_SALT',       'ImouD;0|nsrySf)]:u7-d{2v6./.7#p=X^1LSi4C]*>ZF-nA}kA*gMRH}Rrqkwno' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'cap_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_ALLOW_MULTISITE', true );

define( 'MULTISITE', true );
define( 'SUBDOMAIN_INSTALL', false );
define( 'DOMAIN_CURRENT_SITE', 'cap.olade.org' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );

define('WP_REDIS_PORT', 6379);
define('WP_REDIS_HOST', '172.20.0.2');
define('WP_CACHE_KEY_SALT','thisismynicename');
define('WP_REDIS_PASSWORD', 'pass$12345');
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
