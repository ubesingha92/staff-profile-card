<?php
/**
 * Plugin Name:       Staff Profile Card
 * Plugin URI:        https://github.com/ubesingha92/staff-profile-card
 * Description:       An Elementor widget that displays an academic staff directory fetched from a remote API. Supports multiple profiles with reorder.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Chanaka Chathuranga Ubesingha
 * Author URI:        https://github.com/ubesingha92
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       staff-profile-card
 * Update URI:        https://github.com/ubesingha92/staff-profile-card
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'SPC_VERSION', '2.0.0' );
define( 'SPC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'SPC_ALLOWED_API_HOSTS' ) ) {
    define( 'SPC_ALLOWED_API_HOSTS', [ 'localhost', '127.0.0.1', '::1' ] );
}

/* ------------------------------------------------------------------ */
/*  1. Admin Settings Page                                            */
/* ------------------------------------------------------------------ */

/**
 * Register plugin settings.
 */
function spc_register_settings() {
    register_setting( 'spc_settings_group', 'spc_api_url', [
        'type'              => 'string',
        'sanitize_callback' => 'spc_sanitize_api_url',
        'default'           => '',
    ] );
}
add_action( 'admin_init', 'spc_register_settings' );

/**
 * Return trusted API hosts configured by the site.
 *
 * @return array
 */
function spc_allowed_api_hosts() {
    $hosts = defined( 'SPC_ALLOWED_API_HOSTS' ) ? SPC_ALLOWED_API_HOSTS : [];
    if ( is_string( $hosts ) ) {
        $hosts = array_map( 'trim', explode( ',', $hosts ) );
    }
    if ( ! is_array( $hosts ) ) {
        return [];
    }

    return array_values( array_filter( array_map( static function ( $host ) {
        return strtolower( trim( (string) $host, " \t\n\r\0\x0B[]" ) );
    }, $hosts ) ) );
}

/**
 * Check whether an API URL points to a trusted host.
 *
 * @param string $url API endpoint URL.
 * @return bool
 */
function spc_api_url_is_allowed( $url ) {
    $parts = wp_parse_url( $url );
    $host  = strtolower( trim( (string) ( $parts['host'] ?? '' ), '[]' ) );

    return $host !== '' && in_array( $host, spc_allowed_api_hosts(), true );
}

/**
 * Sanitize the API URL. Allow only valid http/https URLs on trusted hosts.
 *
 * @param string $value Raw input.
 * @return string Sanitized URL or empty string.
 */
function spc_sanitize_api_url( $value ) {
    $value = esc_url_raw( trim( $value ), [ 'http', 'https' ] );

    if ( empty( $value ) ) {
        add_settings_error(
            'spc_api_url',
            'spc_invalid_url',
            __( 'Please enter a valid HTTP or HTTPS URL.', 'staff-profile-card' ),
            'error'
        );
        return '';
    }

    if ( ! spc_api_url_is_allowed( $value ) ) {
        add_settings_error(
            'spc_api_url',
            'spc_untrusted_url',
            __( 'This API host is not trusted. Add it to SPC_ALLOWED_API_HOSTS before saving this URL.', 'staff-profile-card' ),
            'error'
        );
        return '';
    }

    return $value;
}

/**
 * Add the settings page under the Settings menu.
 */
function spc_add_settings_page() {
    add_options_page(
        __( 'Staff Profile Card Settings', 'staff-profile-card' ),
        __( 'Staff Profile Card', 'staff-profile-card' ),
        'manage_options',
        'staff-profile-card',
        'spc_render_settings_page'
    );
}
add_action( 'admin_menu', 'spc_add_settings_page' );

/**
 * Render the settings page.
 */
function spc_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'spc_settings_group' );
            ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="spc_api_url">
                            <?php esc_html_e( 'API Endpoint URL', 'staff-profile-card' ); ?>
                        </label>
                    </th>
                    <td>
                        <input
                            type="url"
                            id="spc_api_url"
                            name="spc_api_url"
                            value="<?php echo esc_attr( get_option( 'spc_api_url', '' ) ); ?>"
                            class="regular-text"
                            placeholder="http://localhost/profiles/api/profile"
                        />
                        <p class="description">
                            <?php esc_html_e(
                                'Enter the base API URL only. Add the API Profile ID in each widget, not in this URL. The host must be listed in SPC_ALLOWED_API_HOSTS.',
                                'staff-profile-card'
                            ); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button( __( 'Save Settings', 'staff-profile-card' ) ); ?>
        </form>
    </div>
    <?php
}

/* ------------------------------------------------------------------ */
/*  2. Fetch profile helpers                                          */
/* ------------------------------------------------------------------ */

/**
 * Fetch a single profile from the API.
 *
 * @param string $api_profile_id API Profile ID.
 * @return array|WP_Error Profile data array on success, WP_Error on failure.
 */
function spc_fetch_profile( $api_profile_id ) {
    $api_url = get_option( 'spc_api_url', '' );
    $api_profile_id = strtoupper( trim( (string) $api_profile_id ) );

    if ( empty( $api_url ) ) {
        return new WP_Error( 'spc_no_url', __( 'API endpoint URL is not configured.', 'staff-profile-card' ) );
    }

    if ( ! spc_api_url_is_allowed( $api_url ) ) {
        error_log( 'Staff Profile Card blocked untrusted API URL: ' . $api_url );
        return new WP_Error( 'spc_untrusted_url', __( 'The configured API URL is not trusted.', 'staff-profile-card' ) );
    }

    if ( ! preg_match( '/\ASPC-[A-Z0-9]{8}\z/', $api_profile_id ) ) {
        return new WP_Error( 'spc_invalid_profile_id', __( 'Enter the generated API Profile ID, for example SPC-8F3K2Q9X. Do not use the Staff ID or an internal database ID.', 'staff-profile-card' ) );
    }

    $request_url = add_query_arg( 'id', rawurlencode( $api_profile_id ), $api_url );

    $response = wp_remote_get( $request_url, [
        'timeout' => 10,
    ] );

    if ( is_wp_error( $response ) ) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code( $response );
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );

    if ( $code !== 200 || ! is_array( $data ) ) {
        $error_msg = isset( $data['error'] ) ? $data['error'] : __( 'Unknown API error.', 'staff-profile-card' );
        return new WP_Error( 'spc_api_error', $error_msg );
    }

    if ( isset( $data['error'] ) ) {
        return new WP_Error( 'spc_api_error', $data['error'] );
    }

    return $data;
}

/**
 * Fetch multiple profiles from the API.
 *
 * @param array $api_profile_ids Array of API Profile ID strings.
 * @return array Associative array keyed by API Profile ID. Each value is
 *               either a profile data array or a WP_Error.
 */
function spc_fetch_profiles( $api_profile_ids ) {
    $results = [];

    foreach ( $api_profile_ids as $id ) {
        $id = strtoupper( trim( (string) $id ) );
        if ( empty( $id ) ) {
            continue;
        }
        // Avoid duplicate fetches within the same request.
        if ( isset( $results[ $id ] ) ) {
            continue;
        }
        $results[ $id ] = spc_fetch_profile( $id );
    }

    return $results;
}

/* ------------------------------------------------------------------ */
/*  3. REST API preview endpoint (for Elementor editor)               */
/* ------------------------------------------------------------------ */

/**
 * Register REST routes for live preview in the Elementor editor.
 */
function spc_register_rest_routes() {
    // Single profile preview (kept for backward compat).
    register_rest_route( 'staff-profile-card/v1', '/preview', [
        'methods'             => 'GET',
        'callback'            => 'spc_rest_preview_callback',
        'permission_callback' => function () {
            return current_user_can( 'edit_posts' );
        },
        'args'                => [
            'api_profile_id' => [
                'required'          => true,
                'validate_callback' => function ( $param ) {
                    return is_string( $param ) && preg_match( '/\ASPC-[A-Z0-9]{8}\z/', strtoupper( trim( $param ) ) );
                },
                'sanitize_callback' => function ( $param ) {
                    return strtoupper( sanitize_text_field( $param ) );
                },
            ],
            '_wpnonce'   => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ] );

    // Multi-profile preview.
    register_rest_route( 'staff-profile-card/v1', '/preview-multi', [
        'methods'             => 'GET',
        'callback'            => 'spc_rest_preview_multi_callback',
        'permission_callback' => function () {
            return current_user_can( 'edit_posts' );
        },
        'args'                => [
            'ids' => [
                'required'          => true,
                'validate_callback' => function ( $param ) {
                    if ( ! is_string( $param ) ) {
                        return false;
                    }
                    $ids = array_filter( array_map( 'trim', explode( ',', $param ) ) );
                    foreach ( $ids as $id ) {
                        if ( ! preg_match( '/\ASPC-[A-Z0-9]{8}\z/', strtoupper( $id ) ) ) {
                            return false;
                        }
                    }
                    return ! empty( $ids );
                },
                'sanitize_callback' => function ( $param ) {
                    return strtoupper( sanitize_text_field( $param ) );
                },
            ],
            '_wpnonce'   => [
                'required'          => true,
                'sanitize_callback' => 'sanitize_text_field',
            ],
        ],
    ] );
}
add_action( 'rest_api_init', 'spc_register_rest_routes' );

/**
 * REST callback to fetch a single profile for editor preview.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function spc_rest_preview_callback( $request ) {
    $nonce = $request->get_param( '_wpnonce' );
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response( [ 'error' => __( 'Invalid nonce.', 'staff-profile-card' ) ], 403 );
    }

    $api_profile_id = $request->get_param( 'api_profile_id' );
    $data           = spc_fetch_profile( $api_profile_id );

    if ( is_wp_error( $data ) ) {
        return new WP_REST_Response( [ 'error' => $data->get_error_message() ], 400 );
    }

    return new WP_REST_Response( $data, 200 );
}

/**
 * REST callback to fetch multiple profiles for editor preview.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response
 */
function spc_rest_preview_multi_callback( $request ) {
    $nonce = $request->get_param( '_wpnonce' );
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        return new WP_REST_Response( [ 'error' => __( 'Invalid nonce.', 'staff-profile-card' ) ], 403 );
    }

    $ids_raw = $request->get_param( 'ids' );
    $ids     = array_filter( array_map( 'trim', explode( ',', $ids_raw ) ) );

    $results   = spc_fetch_profiles( $ids );
    $output    = [];

    // Return in the order requested.
    foreach ( $ids as $id ) {
        $id = strtoupper( trim( $id ) );
        if ( isset( $results[ $id ] ) ) {
            if ( is_wp_error( $results[ $id ] ) ) {
                $output[] = [
                    'profile_id' => $id,
                    'error'      => $results[ $id ]->get_error_message(),
                ];
            } else {
                $output[] = $results[ $id ];
            }
        }
    }

    return new WP_REST_Response( $output, 200 );
}

/* ------------------------------------------------------------------ */
/*  4. Register Elementor widget                                      */
/* ------------------------------------------------------------------ */

/**
 * Register the Elementor widget.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 */
function spc_register_elementor_widget( $widgets_manager ) {
    require_once SPC_PLUGIN_DIR . 'includes/class-spc-widget.php';
    $widgets_manager->register( new \SPC\Widget() );
}
add_action( 'elementor/widgets/register', 'spc_register_elementor_widget' );

/**
 * Register a custom Elementor widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
 */
function spc_register_elementor_category( $elements_manager ) {
    $elements_manager->add_category( 'staff-profile', [
        'title' => __( 'Staff Profile', 'staff-profile-card' ),
        'icon'  => 'eicon-person',
    ] );
}
add_action( 'elementor/elements/categories_registered', 'spc_register_elementor_category' );

/* ------------------------------------------------------------------ */
/*  5. Enqueue frontend styles                                        */
/* ------------------------------------------------------------------ */

/**
 * Enqueue widget styles in both frontend and Elementor editor.
 */
function spc_enqueue_styles() {
    wp_enqueue_style(
        'spc-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'spc-widget-style',
        SPC_PLUGIN_URL . 'assets/css/spc-widget.css',
        [ 'spc-google-fonts' ],
        SPC_VERSION
    );
}
add_action( 'elementor/frontend/after_enqueue_styles', 'spc_enqueue_styles' );

/**
 * Also enqueue in the Elementor editor.
 */
function spc_editor_enqueue_styles() {
    wp_enqueue_style(
        'spc-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
        [],
        null
    );

    wp_enqueue_style(
        'spc-widget-style',
        SPC_PLUGIN_URL . 'assets/css/spc-widget.css',
        [ 'spc-google-fonts' ],
        SPC_VERSION
    );
}
add_action( 'elementor/editor/after_enqueue_styles', 'spc_editor_enqueue_styles' );

/* ------------------------------------------------------------------ */
/*  6. Plugin activation / deactivation                               */
/* ------------------------------------------------------------------ */

/**
 * Check that Elementor is active on plugin activation.
 */
function spc_activate() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die(
            esc_html__( 'Staff Profile Card requires Elementor to be installed and activated.', 'staff-profile-card' ),
            'Plugin Activation Error',
            [ 'back_link' => true ]
        );
    }
}
register_activation_hook( __FILE__, 'spc_activate' );

/**
 * Clean up options on uninstall (optional).
 */
function spc_uninstall() {
    delete_option( 'spc_api_url' );
}
register_uninstall_hook( __FILE__, 'spc_uninstall' );
