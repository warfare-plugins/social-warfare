<?php

/**
 * Migrates camel cased database keys to semantic, snake cased keys.
 *
 * The migrate() method should only ever be called once. This class is planned
 * to be obsolete in the future.
 */
class SWP_Database_Migration {

    public function __construct() {
        if ( !$this->is_migrated() ) {
            $this->migrate();
        }
    }

    /**
     * Checks to see if our new options have been stored in the database.
     *
     * @return bool True if migrated, else false.
     *
     */
    public function is_migrated() {
        $option = get_option( 'social_warfare_settings' , false);

        return $option;
    }

    /**
     * Map prevous key/value pairs to new keys.
     *
     * @return [type] [description]
     */
    private function migrate() {
        $options = get_option( 'socialWarfareOptions', array() );

        $map = array(
            //* Options names
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
            'floatOption'    => 'float_position',
            'swp_float_scr_sz'  => 'float_screen_width',
            'sideReveal'    => 'transition',
            'floatStyle'    => 'float_button_shape',
            'floatStyleSource'  => 'float_style_source',
            'sideDColorSet' => 'float_default_colors',
            'sideOColorSet' => 'float_hover_colors',
            'sideIColorSet' => 'float_single_colors',
            'swp_twitter_card'  => 'twitter_cards',
            'twitterID'     => 'twitter_id',
            'sniplyBuster'  => 'frame_buster',
            'linkShortening'=> 'bitly_authentication',
            'cacheMethod'   => 'cache_method',
            'google_analytics' => 'google_analytics',
            'analyticsMedium'   => 'analytics_medium',
            'analyticsCampaign' => 'analytics_campaign',
            'advanced_pinterest_image' => 'pin_browser_extension',
            'advanced_pinterest_image_location' => 'pinterest_image_location',
            'advanced_pinterest_fallback'   => 'pinterest_fallback',
            'recovery_custom_format'    => 'recovery_permalink',


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
        );

        $migrations = array();

        //* Camel refers to the previous key in the options table
        //* whether or not it was camelCase.
        foreach( $options as $camel => $value ) {

            if ( array_key_exists( $camel, $map) ) {
                //* We specified an update to the key.
                $snake = $map[$camel];
                $migrations[$snake] = $value;

            } else {
                //* The previous key was fine, keep it.
                $migrations[$camel] = $value;
            }
        }

        update_option( 'social_warfare_settings', $migrations );
    }
}
