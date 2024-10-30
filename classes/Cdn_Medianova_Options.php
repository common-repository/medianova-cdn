<?php

class Cdn_Medianova_Options
{

    /**
     * set_settings
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param
     * @return string set validate rules
     */

    public static function set_settings(){
        register_setting(
            'cdn_medianova',
            'cdn_medianova',
            [
                __CLASS__,
                'control_settings'
            ]
        );
    }

    /**
     * cleanHostname
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param string
     * @return string if url has http or https
     */

    public static function cleanHostname( $hostname )
    {
        $hostname = str_replace( "http://", "", $hostname );
        $hostname = str_replace( "https://", "", $hostname );

        return str_replace( "/", "", $hostname );
    }

    /**
     * is_url
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param string
     * @return string validation url
     */

    protected static function is_url( $url ){
        if(preg_match
        ('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/' ,$url)){
            return $url;
        }
        return false;
    }

    public static function get_old_data() {
        $oldData = get_option( 'cdn_medianova' );
        return $oldData;
    }

    /**
     * control_settings
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param array
     * @return array validation for inputs
     */

    public static function control_settings( $data ){

        $returnData = [];

        if ( empty( $data['url'] ) ){

            add_settings_error( 'cdn_medianova', '', 'Field required', 'error' );
            $oldData = Cdn_Medianova_Options::get_old_data();

            return [
                'url' => esc_url( $oldData['url'] ),
                'dirs' => esc_attr( $oldData['dirs'] ),
                'excludes' => esc_attr( $oldData['excludes'] )
            ];

        } if ( Cdn_Medianova_Options::is_url( $data['url'] ) ){
            $returnData['url'] = $data['url'];
        } else {

            add_settings_error('cdn_medianova', '', 'Check Your URL', 'error');
            $oldData = Cdn_Medianova_Options::get_old_data();

            return [
                    'url' => esc_url( $oldData['url'] ),
                    'dirs' => esc_attr( $oldData['dirs'] ),
                    'excludes' => esc_attr( $oldData['excludes'] )
            ];
        }


        if ( empty( $data['dirs'] ) ){

            add_settings_error( 'cdn_medianova', '', 'Field required', 'error' );
            $oldData = Cdn_Medianova_Options::get_old_data();

            return [
                'url' => esc_url( $oldData['url'] ),
                'dirs' => esc_attr( $oldData['dirs'] ),
                'excludes' => esc_attr( $oldData['excludes'] )
            ];

        } else {
            $returnData['dirs'] = $data['dirs'];
        }

        if ( empty( $data['excludes'] ) ){

            add_settings_error('cdn_medianova', '', 'Field required', 'error');
            $oldData = Cdn_Medianova_Options::get_old_data();

            return [
                'url' => esc_url( $oldData['url'] ),
                'dirs' => esc_attr( $oldData['dirs'] ),
                'excludes' => esc_attr( $oldData['excludes'] )
            ];

        } else {
            $returnData['excludes'] = $data['excludes'];
        }

        return  [
            'url' => esc_url( $returnData['url'] ),
            'dirs' => esc_attr( $returnData['dirs'] ),
            'excludes' => esc_attr( $returnData['excludes'] ),
        ];
    }

    /**
     * add_settings_page
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param
     * @return string settings page
     */

    public static function add_settings_page()
    {
        $page = add_options_page(
            'CDN Medianova',
            'CDN Medianova',
            'manage_options',
            'cdn_medianova',
            [
                __CLASS__,
                'settings_page',
            ]
        );
    }

    /**
     * settings_page
     *
     * @since   1.0.0
     * @change  1.0.0
     *
     * @param
     * @return string html form
     */

    public static function  settings_page()
    { ?>

        <?php $options = Cdn_Medianova::get_option_datas(); ?>

        <br>
        <br>

        <div id="wpbody-content">

        <div id="wpcontent" class="wpcontent">

            <br>
            <br>
            <br>
            <div class="content-all">
                <div class="container well">

                    <div class="header">
                        <div class="header-img">
                            <img src="<?php echo plugin_dir_url( dirname(__FILE__) ) . 'assets/img/amblem.png'; ?>">
                        </div>
                        <div class="header-self">
                            <span class="header-color"><?php _e( "Medianova CDN","medianova-cdn" ) ?></span>
                            <span class="header-color-2"><?php  _e( "Settings", "medianova-cdn" ) ?></span>
                        </div>
                    </div>

                    <small class="text-small"><?php _e( "Combine CDN Medianova with MedianovaCDN", "medianova-cdn" ) ?>
                        <?php _e( "for even faster WordPress performance.", "medianova-cdn" ) ?></small>
                    <form action="options.php" method="post">
                        <?php settings_fields( 'cdn_medianova' ) ?>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-25">
                                <label for="url"><?php _e( "Service URL", "medianova-cdn" ) ?></label>
                            </div>
                            <div class="col-60">
                                <input class="text-input" type="text" name="cdn_medianova[url]" placeholder="https://your-username.mncdn.com" value="<?php echo $options['url']; ?>">
                                <div class="col-60">
                                    <small><?php _e( "Use your service url with http or https ", "medianova-cdn" ) ?></small>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-25">
                                <label for="dir"><?php _e( "INCLUDED DIRECTORIES", "medianova-cdn") ?></label>
                            </div>
                            <div class="col-60">
                                <input class="text-input" type="text" name="cdn_medianova[dirs]" value="<?php echo $options['dirs']; ?>"
                                       placeholder="wp-content, wp-includes">
                                <div class="col-60">
                                    <small><?php _e( "Assets in these directories will be pointed to the CDN URL. Enter the directories seperated by ( , )", "medianova-cdn" ) ?></small>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-25">
                                <label for="exl"><?php _e( "EXCLUDES", "medianova-cdn" ) ?></label>
                            </div>
                            <div class="col-60">
                                <input class="text-input" type="text" value="<?php echo $options['excludes']; ?>" name="cdn_medianova[excludes]"
                                       placeholder=".php">
                                <div class="col-60">
                                    <small><?php _e( "Default: ( php ) Enter the exlusions ( Directories or extensions ) seperated
                                            by ( , )", "medianova-cdn" ) ?>
                                    </small>
                                </div>
                                <br>
                            </div>
                        </div>
                        <div style="display: none" class="row">
                            <div class="col-25">
                                <label for="rel"><?php _e( "Relatives", "medianova-cdn" ) ?></label>
                            </div>
                            <div class="col-60">
                                <input class="text-input" type="checkbox" name="cdn_medianova[relative]" value="1" <?php checked(1, $options['relative']) ?> />
                            </div>
                        </div>

                        <div  style="display: none" class="row">
                            <div class="col-25">
                                <label for="https"><?php _e( "CDN HTTPS", "medianova-cdn" ) ?></label>
                            </div>
                            <div class="col-60">
                                <label for="cdn_medianova_https">
                                    <input class="text-input" type="checkbox" name="cdn_medianova[https]" value="1" <?php checked(1, $options['https']) ?> />
                                </label>
                            </div>
                        </div>
                        <div class="col-25">
                        </div>
                        <div class="row">
                            <button class="button-self">
                                    <span class="button-img">
                                        <img src="<?php echo plugin_dir_url( dirname(__FILE__) ) . 'assets/img/bttn_arrow.png'; ?>">
                                    </span>
                                <?php _e("SAVE CHANGES", "MedianovaCDN"); ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="banner">
                    <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/banner.jpg'; ?>">
                    <div class="banner-text"><span><?php _e("Benefice from our free CDN trial and get an easy integration to your wordpress website.", "MedianovaCDN"); ?></span></div>
                    <button class="banner-button"><a href="https://www.medianova.com/free-trial/" target="_blank"><?php _e("Start
                                    Your Free Trial", "MedianovaCDN"); ?></a></button>
                </div>
            </div>
        </div>

        </div><?php
    }
}