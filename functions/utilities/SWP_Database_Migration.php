<?php
/**
 * The Database updater for Social Warfare 3.0.0.
 *
 * This willl either migrate previous options to social_warfare_settings,
 * or create the new default settings.
 */

class SWP_Database_Migration {

    public $metadata_map =  [
        'nc_ogImage'            => 'swp_og_image',
        'nc_ogTitleWrapper'     => 'swp_og_title',
        'nc_pinterestImage'     => 'swp_pinterest_image',
        'nc_customTweet'        => 'swp_custom_tweet',
        'nc_postLocation'       => 'swp_post_location',
        'nc_floatLocation'      => 'swp_float_location',
        'twitterID'             => 'swp_twitter_id',
        'nc_ogDescriptionWrapper'       => 'swp_og_description',
        'nc_pinterestDescription'       => 'nc_pinterest_description',
        'swp_cache_timestamp'           => 'swp_cache_timestamp'
        'swp_pin_browser_extension'     => 'swp_pin_browser_extension',
        'swp_pin_browser_extension_location'    => 'swp_pin_browser_extension_location',
        'swp_pin_browser_extension_url'         => 'swp_pin_browser_extension_url',
    ];


    private $defaults = [
        'location_archive_categories'       => 'below',
        'location_home'				        => 'none',
        'location_post'				        => 'below',
        'location_page'				        => 'below',
        'total_shares'                      => true,
        'network_shares'                    => true,
        'twitter_id'                        => false,
        'swp_twitter_card'                  => true,
        'button_shape'                      => 'flatFresh',
        'default_colors'                    => 'full_color',
        'single_colors'                     => 'full_color',
        'hover_colors'                      => 'full_color',
        'float_default_colors'              => 'full_color',
        'float_single_colors'               => 'full_color',
        'float_hover_colors'                => 'fullColor',
        'float_style_source'                => true,
        'button_size'                       => 1,
        'button_alignment'                  => 'fullWidth',
        'transition'                        => 'slide',
        'float_screen_width'                => 1100,
        'ctt_theme'                         => 'style1',
        'ctt_css'							=> "",
        'twitter_shares'                    => false,
        'floating_panel'                    => true,
        'float_location'                    => 'bottom',
        'float_background_color'            => '#ffffff',
        'float_button_shape'                => 'default',
        'float_vertical'					=> 'center',
        'custom_color'                      => '#000000',
        'recover_shares'                    => false,
        'recovery_format'                   => 'unchanged',
        'recovery_protocol'                 => 'unchanged',
        'recovery_prefix'                   => 'unchanged',
        'decimals'                          => 0,
        'decimal_separator'                 => 'period',
        'totals_alignment'                  => 'totesalt',
        'google_analytics'                  => false,
        'bitly_authentication'              => false,
        'minimum_shares'                    => 0,
        'full_content'				        => false,
        'frame_buster'                      => false,
        'analytics_medium'                  => 'social',
        'analytics_campaign'                => 'SocialWarfare',
        'swp_click_tracking'                => false,
        'order_of_icons_method'             => 'manual',
        'pinit_toggle'                      => false,
        'pinit_location_horizontal'         => 'center',
        'pinit_location_vertical'           => 'top',
        'pinit_min_width'                   => '200',
        'pinit_min_height'                  => '200',
        'pinit_image_source'                => 'image',
        'pinit_image_description'           => 'alt_text',
        'utm_on_pins'	                    => false,
        'pin_browser_extension'             => false,
        'pin_browser_extension_location'    => 'hidden',
        'pinterest_fallback'                => 'all',
        'float_mobile'                      => 'bottom',
        'force_new_shares'                  => false,
        'order_of_icons' => [
            'active' => [
                'twitter'    => 'Twitter',
                'linkedIn'   => 'LinkedIn',
                'pinterest'  => 'Pinterest',
                'facebook'   => 'Facebook',
                'google_plus' => 'Google Plus',
            ],
        ],
    ];


    public function __construct() {
        //* Check to see if the 3.0.0 settings exist.
        $settings = get_option( 'social_warfare_settings', false );

        if ( false === $settings || empty( $settings['location_archive_categories'] ) ) :
            $this->initialize_database();
        endif;

        //* Fetch posts with 2.3.5 metadata.
        $old_metadata = get_posts( ['meta_key' => 'nc_postLocation'] );

        //* Map 2.3.5 metadata to 3.0.0 keys.
        if ( count( $old_metadata ) > 0 ) :
            $this->update_sw_meta( $old_posts );
        endif;

        if ( !$this->is_migrated() ) {
            $this->migrate();
        }
    }

    public function update_sw_meta( $posts ) {
        foreach( $posts as $post ) {
            foreach( $this->metadata_map as $previous_key => $new_key ) {
                $value = get_post_meta( $post->ID, $previous_key );
                update_post_meta ($post->ID, $new_key, $value );
                $del = delete_post_meta( $post->ID, $previous_key );
            }
        }
    }

    public function initialize_database() {
        update_option( 'social_warfare_settings', $this->defaults );
    }


    /**
     * Checks to see if our new options have been stored in the database.
     *
     * @return bool True if migrated, else false.
     *
     */
    public function is_migrated() {
        $option = get_option( 'socialWarfareOptions' , false);

        return is_array( $option );
    }


    /**
     * Map prevous key/value pairs to new keys.
     *
     * @return [type] [description]
     */
    private function migrate() {
        $options = get_option( 'socialWarfareOptions', [] );

        $map = [
            //* Options names
            'locationSite'  => 'location_archive_categories',
            'locationHome'  => 'location_home',
            'totesEach'     => 'network_shares',
            'totes'         => 'total_shares',
            'minTotes'      => 'minimum_shares',
            'visualTheme'   => 'button_shape',
            'buttonSize'    => 'button_size',
            'dColorSet'     => 'default_colors',
            'oColorSet'     => 'hover_colors',
            'iColorSet'     => 'single_colors',
            'swDecimals'    => 'decimals',
            'swp_decimal_separator' => 'decimal_separator',
            'swTotesFormat' => 'totals_alignment',
            'float'         => 'floating_panel',
            'float_background_color'    => 'float_location',
            'swp_float_scr_sz'  => 'float_screen_width',
            'sideReveal'    => 'transition',
            'floatStyle'    => 'float_button_shape',
            'floatStyleSource'  => 'float_style_source',
            'sideDColorSet' => 'float_default_colors',
            'sideOColorSet' => 'float_hover_colors',
            'sideIColorSet' => 'float_single_colors',
            'swp_twitter_card'  => 'twitter_cards',
            'twitterID'     => 'twitter_id',
            'pinterestID'   => 'pinterest_id',
            'facebookPublisherUrl'  => 'facebook_publisher_url',
            'facebookAppID' => 'facebook_app_id',
            'sniplyBuster'  => 'frame_buster',
            'linkShortening'=> 'bitly_authentication',
            'cacheMethod'   => 'cache_method',
            'googleAnalytics' => 'google_analytics',
            'analyticsMedium'   => 'analytics_medium',
            'analyticsCampaign' => 'analytics_campaign',
            'advanced_pinterest_image' => 'pin_browser_extension',
            'pin_browser_extension_location' => 'pinterest_image_location',
            'advanced_pinterest_fallback'   => 'pinterest_fallback',
            'recovery_custom_format'    => 'recovery_permalink',
            'cttTheme'  => 'ctt_theme',
            'cttCSS'    => 'ctt_css',
            'sideCustomColor'   => 'single_custom_color',
            'floatBgColor'  => 'float_background_color',
            'orderOfIconsSelect'    => 'order_of_icons_method',
			'newOrderOfIcons' => 'order_of_icons',

            //* Choices names
            'flatFresh'     => 'flat_fresh',
            'threeDee'      => 'three_dee',
            'fullColor'     => 'full_color',
            'lightGray'     => 'light_gray',
            'mediumGray'    => 'medium_gray',
            'darkGray'      => 'dark_grey',
            'lgOutlines'    => 'light_grey_outlines',
            'mdOutlines'    => 'medium_grey_outlines',
            'dgOutlines'    => 'dark_grey_outlines',
            'colorOutlines' => 'color_outlines',
            'customColor'   => 'custom_color',
            'ccOutlines'    => 'custom_color_outlines',
            'totesAlt'      => 'totals_right',
            'totesAltLeft'  => 'totals_left',
            'buttonFloat'   => 'button_alignment',
            'post'          => 'location_post',
            'page'          => 'location_page',
            'float_vertical'=> 'float_alignment',

            //* Missed choices,
            //* same reason as above ^^
            'fullWidth' => 'full_width',
            'floatLeftMobile'   => 'float_mobile',
        ];


        $removals = [
            'dashboardShares',
            'rawNumbers',
            'notShowing',
            'visualEditorBug',
            'loopFix',
            'locationrevision',
            'locationattachment',
        ];


        $migrations = [];


        foreach( $options as $old => $value ) {

            if ( array_key_exists( $old, $map) ) {
                //* We specified an update to the key.
                $new = $map[$old];
                $migrations[$new] = $value;

            } else {
                //* The previous key was fine, keep it.
                $migrations[$old] = $value;
            }
        }


        update_option( 'social_warfare_settings', $migrations );
        delete_option( 'socialWarfareOptions' );
    }
}
