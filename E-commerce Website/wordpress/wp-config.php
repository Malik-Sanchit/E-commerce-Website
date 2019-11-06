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
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Db,J-d_Zw9lGm7v+y]?SVRg/odh6}<6GUr-avd$2:(dF/m&d6rAOh>kQPMa]O.4(' );
define( 'SECURE_AUTH_KEY',  'p<h_y:VZut4EOAqaU7m>oZ2K>D{+5fc7v*27<]vnj1GdM54[esJLQPtEG+a,|A5_' );
define( 'LOGGED_IN_KEY',    'R!@Nk_ShcUUX]*UZE[F~FV(s0f@/?%Jo3WECS_ZJq8t^i v4U%L$`VvC%]H92q/h' );
define( 'NONCE_KEY',        '&oN/uh/&g!t{,rZVO!{~h ;R(wTRMn-CR6A-yj4Xd^`4k]U%3*s!2qPq~p`d(SVC' );
define( 'AUTH_SALT',        '^sDr&HFshPuGJ]Dj>yKScU7~^n`#.5^Sormp2 G3bpRrPSYzPP3n+i0mJQ?tmyaf' );
define( 'SECURE_AUTH_SALT', 'X(}K<#d&YJK_tVjbF`9=st;.70.q1tXO uvS^&*zQP%GJ#/P^CiBH7VlgRZnd]<~' );
define( 'LOGGED_IN_SALT',   '#FU_#v&d3jMuzI3eo;k2d1qlK$)g;3|iVz~BMA^}ldq$q(/yew^k-siyJ,npn>c9' );
define( 'NONCE_SALT',       'hIE-Lwzm3oAfe~;e<h.xZ``lxY1a/&P2`,YL2LlD!/ j_uJ17lwl[w@U~mCLdf$-' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
