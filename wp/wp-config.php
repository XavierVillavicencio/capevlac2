<?php
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
define( 'DB_USER', 'root' );

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
define( 'AUTH_KEY',         'NKOQAM{H:cJ/|A7cLr*;_,Z]+-TEPt+dSaT89Q14|xlx_IF= cE!}Q{pm8E0;V;l' );
define( 'SECURE_AUTH_KEY',  'MSE7t,d43QghIx<uY~d@=cg4zYaL5W#m1C.|u+3P}>A9O~U(~E@bG3+Ai#%v~S6;' );
define( 'LOGGED_IN_KEY',    '&q7 ub2rN`^~PD;]wsO,jv>3 iI}Zo7FBF+pi?SM:%cGT#Y~~j$:#Ah*p#{~)$Oo' );
define( 'NONCE_KEY',        'b#(Qx>H{;tFS|[o&(yNXaSN?}bgrwfT=c!sLP3<*%(eqf#gv4PQo$*Yw/G9s[zR|' );
define( 'AUTH_SALT',        'cQ%nab645?K>gLrY7^Ky|N{)yB!87UD4tv-j<p|Z1CmpoTu:E_BK*B}RP=i^ ~yv' );
define( 'SECURE_AUTH_SALT', '4_${ZFmjjW;gw?oIENqI?@na@a`n(q<!kro%+,j&arun^lPF|SG{$$*>!P}?@Zg5' );
define( 'LOGGED_IN_SALT',   'II0jTa81&.KSo`j*UU;4Q<McbPQ1*PNhzi3{5-n@8PsU187Rs}CW)xHEOgVaZW30' );
define( 'NONCE_SALT',       'JZ!VZoS>#j0[v-`k0GW3xyK@yfdqI{Q!Y6AtA^3Ok1]a}?:%~uO0faQ8<h$]B?lg' );

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
define( 'DOMAIN_CURRENT_SITE', 'localhost:8000' );
define( 'PATH_CURRENT_SITE', '/' );
define( 'SITE_ID_CURRENT_SITE', 1 );
define( 'BLOG_ID_CURRENT_SITE', 1 );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
