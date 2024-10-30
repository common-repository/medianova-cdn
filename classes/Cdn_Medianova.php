<?php

class Cdn_Medianova
{
    public static function instance() {
        new self;
    }

    public function __construct() {

        /* call hook for change url  */
        add_action(
            'template_redirect',
            [
                __CLASS__,
                'call_rewrite_hook',
            ]
        );

        /* set html form */
        add_action(
            'admin_init',
            [
                'Cdn_Medianova_Options',
                'set_settings'
            ]
        );

        /* set setting page */
        add_action(
            'admin_menu',
            [
                'Cdn_Medianova_Options',
                'add_settings_page'
            ]
        );

        /* set link for plugin bio for setting page */
        add_filter(
            'plugin_action_links_' .CDN_MEDIANOVA_BASE,
            [
                __CLASS__,
                'add_settings_link',
            ]
        );

        /* check wp version */
        add_action(
            'all_admin_notices',
            [
                __CLASS__,
                'cdn_medianova_requirements_control',
            ]
        );

    }

    /**
     * call_activation_hook
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param
     * @return string
     */

    public static function call_activation_hook() {
        add_option(
            'Cdn_Medianova',
            [
                'url' => get_option('home'),
                'dirs' => 'wp-content,wp-includes',
                'excludes' => '.php',
                'relative' => '',
                'https' => ''
            ]
        );
    }

    /**
     * call_uninstall_hook
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * Delete hook.
     */

    public static function  call_uninstall_hook() {
        delete_option( 'Cdn_Medianova' );
    }

    /**
     * add_settings_link
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * add links on bio plugin for settings page.
     */

    public static function add_settings_link($data) {

        if ( ! current_user_can('manage_options') ) {
            return $data;
        }

        return array_merge(
            $data,
            [
                sprintf(
                    '<a href="%s">%s</a>',
                    add_query_arg( [ 'page' => 'cdn_medianova' ], admin_url( 'options-general.php' ) ), __( "Settings" ) ),
            ]
        );
    }

    /**
     * cdn_medianova_requirements_control
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * check wordpress version
     */

    public static function cdn_medianova_requirements_control() {
        if ( version_compare($GLOBALS['wp_version'], CDN_MEDIANOVA_MIN_WP, '<') ) {
            show_message(
                sprintf(
                    '<div class="error"><p>%s</p></div>',
                    sprintf(
                        __("MedianovaCDN is optimized for WordPress %s. Delete the plugin or upgrade your WordPress installation (recommended).", "MedianovaCDN"),
                        CDN_MEDIANOVA_MIN_WP
                    )
                )
            );
        }
    }

    /**
     * get_option_datas
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * get data
     */

    public static function get_option_datas() {
        return wp_parse_args(
            get_option( 'Cdn_Medianova' ),
            [
                'url' => get_option('home'),
                'dirs' => 'wp-content,wp-includes',
                'excludes' => '.php',
                'relative' => 1,
                'https' => 1
            ]
        );
    }

    /**
     * call_rewrite_hook
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * post data to cdn_medianova_change class
     */

    public static function call_rewrite_hook() {
        $options = self::get_option_datas();

        if ( get_option( 'home' ) == $options['url'] ) {
            return;
        }

        $excludes = array_map( 'trim', explode(',', $options['excludes'] ) );

        $rewriter = new Cdn_Medianova_Change();

        $rewriter->setBlogUrl( get_option( 'home' ) );
        $rewriter->setCdnUrl( $options['url'] );
        $rewriter->setDirs( $options['dirs'] );
        $rewriter->setExcludes( $excludes );
        $rewriter->setHttps( $options['https'] );
        $rewriter->setRelative( $options['relative'] );

        ob_start( array( &$rewriter, 'change' ) );
    }
}