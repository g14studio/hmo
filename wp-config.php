<?php
/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'hmo1');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'root');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', '');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'A[I-sT,oHs^wKCe9]b(Z#LM{~%d3g>]&F7bZ.%/)rl_A-s1xkpf98H_-R(,7&P*^');
define('SECURE_AUTH_KEY', 'vVZsD491om&|8NZ>MjkRc)%1^$=J)aG3v(@:|y5eA<{ %U[AchL{J~N<DFk-vC)n');
define('LOGGED_IN_KEY', '}w1L%t/E=dM|8y+D[=[n-H({VQ-+Q(8DN-q>o[f_qucTX,tR<(EsS]+1D+2%M!#@');
define('NONCE_KEY', 'nmdkh<0I cy:B.9Y.H4g^8MBN&p3EU!|F(:C@O`_ewuri8B81(+Fdf7 ?Zvf9@jL');
define('AUTH_SALT', 'aJX[{QfzK.b+d?Udii~^ffG-;ys)((+f)N7UO#f*yfd(g3@khYU7&Gp+LKxvDSB]');
define('SECURE_AUTH_SALT', 'tQU+@;nkSKExl:>89wq7z/NER(|j^fTr>|~t+9a&.g@{Kl#LI(v-&`y+~dB(Tea&');
define('LOGGED_IN_SALT', 'TgXex9[uOY5]yI+7!ge@~|dH7]2UVB>w=W61)&b4k~?]Dh|_B)/-%9!/JXZAPT;L');
define('NONCE_SALT', '+yD/phv+N&A|<v/9`*5gu+,uuI-<X3F.j/);RbL4]O`}TmBa+7CS-WQp|,r%Tk+a');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'wp_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

