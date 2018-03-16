<?php
//* For options whose database name has changed, it is notated as follows:
//* prevOption => new_option
//* @see SWP_Database_Migration

class SWP_Options_Page {
	/**
	 * The Options Page Tabs
	 *
	 * Docblock each class property like this. Include a title, and then
	 * a one or two sentence minimum description.
	 *
	 */
	public $tabs;

    /**
    * Boolean indicating whether the plugin is registered or not.
    *
    * @var bool $swp_registration
    */
    public $swp_registration;

	public function __construct() {
        $this->tabs = new stdClass();
        $this->swp_registration = true;
        add_action( 'admin_menu', array( $this, 'options_page') );
    }

    public function init() {
        // $this->init_display_tab();
        $this->init_styles_tab();
        $Pro = new SWP_Pro_Options_Page();
        // $Pro->update_display_tab();
        $Pro->update_styles_tab();

        $this->render_HTML();
    }

    public function options_page() {
        $swp_top_level_menu = apply_filters( 'swp_top_level_menu' , true );

        // Make the menu item top level
        if ( !!apply_filters( 'swp_top_level_menu', true ) ) :

            // Declare the menu link
            $swp_menu = add_menu_page(
                'Social Warfare',
                'Social Warfare',
                'manage_options',
                'social-warfare',
                array( $this, 'init'),
                SWP_PLUGIN_URL . '/images/admin-options-page/socialwarfare-20x20.png'
            );

        // Make the menu a submenu page of the settings menu
        else :

            // Declare the menu link
            $swp_menu = add_submenu_page(
                'options-general.php',
                'Social Warfare',
                'Social Warfare',
                'manage_options',
                'social-warfare',
                array( $this, 'init')
            );

        endif;

        // Hook into the CSS and Javascript Enqueue process for this specific page
        add_action( 'admin_print_styles-' . $swp_menu, array( $this, 'admin_css' ) );
        add_action( 'admin_print_scripts-' . $swp_menu, array( $this, 'admin_js' ) );

    }

    /**
     * Enqueue the Settings Page CSS & Javascript
     */
    public function admin_css() {
        $suffix = SWP_Script::get_suffix();

        wp_enqueue_style(
            'swp_admin_options_css',
            SWP_PLUGIN_URL . "/css/admin-options-page{$suffix}.css",
            array(),
            SWP_VERSION
        );
    }

    /**
     * Enqueue the admin javascript
     *
     * @since  2.0.0
     * @return void
     */
    public function admin_js() {
        $suffix = SWP_Script::get_suffix();

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-effects-core' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-tooltip' );
        wp_enqueue_media();
        wp_enqueue_script(
            'swp_admin_options_js',
            SWP_PLUGIN_URL . "/js/admin-options-page{$suffix}.js",
            array( 'jquery' ),
            SWP_VERSION
        );

        wp_localize_script( 'swp_admin_options_js', 'swpAdminOptionsData', array(
            'registerNonce' => wp_create_nonce( 'swp_plugin_registration' ),
            'optionsNonce'  => wp_create_nonce( 'swp_plugin_options_save' ),
        ));
    }

    /**
     * Create the Share Counts section of the display tab.
     *
     * This section of options allows users to control the share count settings.
     *
     */
    public function init_display_tab() {
        $display = new SWP_Options_Page_Tab( 'Display' );
		$display->set_priority( 10 )
            ->set_link( 'display' );

    		$share_counts = new SWP_Options_Page_Section( 'Share Counts' );
    	    $share_counts->set_description( 'Use the toggles below to determine how to display your social proof.' )
                ->set_priority( 10 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-share-counts/' );

                //* toteseach => network_count
        		$network_shares = new SWP_Option_Toggle( 'Button Counts', 'network_shares' );
        		$network_shares->set_default( true )
                    ->set_priority( 10 )
                    ->set_size('two-thirds');

                //* totes => totals
                $total_shares = new SWP_Option_Toggle( 'Total Counts', 'total_shares' );
                $total_shares->set_default( true )
                    ->set_priority( 20 )
                    ->set_size( 'two-thirds' );

            $share_counts->add_options( [$network_shares, $total_shares] );

            /* Twitter Cards   */
            $twitter_cards = new SWP_Options_Page_Section( 'Twitter Cards' );
            $twitter_cards->set_description( 'Activating Twitter Cards will cause the plugin to output certain meta tags in the head section of your site\'s HTML. Twitter cards are pretty much exactly like Open Graph meta tags, except that there is only one network, Twitter, that looks at them.' )
                ->set_priority( 20 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-twitter-cards/' );

                    $twitter_card = new SWP_Option_Toggle( 'Show Twitter Cards', 'twitter_cards' );
                    $twitter_card->set_default( true )
                        ->set_priority( 10 )
                        ->set_size( 'two-thirds' );

                $twitter_cards->add_option( $twitter_card );

            /* Position Share Buttons  */
            $button_position = new SWP_Options_Page_Section( 'Position Share Buttons' );
            $button_position->set_description( 'These settings let you decide where the share buttons should go for each post type.' )
                ->set_priority( 30 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-position-share-buttons/' );

            //* TODO: Create the mini-table for this option.

            /* Yummly Display Control  */
            $yummly_display = new SWP_Options_Page_Section( 'Yummy Display Control' );
            $yummly_display->set_description( 'If you would like the Yummly button to only display on posts of a specific category or tag, enter the category or tag name below (e.g "Recipe"). Leave blank to display the button on all posts.' )
                ->set_priority( 50 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-yummly-display-control/' );

        //* TODO: Create the mini-table for this option.
        $display->add_sections( [$share_counts, $twitter_cards, $button_position, $yummly_display] );

        $this->tabs->display = $display;

        return $this;
    }

    public function init_styles_tab() {
        $styles = new SWP_Options_Page_Tab( 'Styles' );
        $styles->set_priority( 20 )
            ->set_link( 'styles' );

            $total_counts = new SWP_Options_Page_Section( 'Total Counts' );
            $total_counts->set_description( 'Customize how the "Total Shares" section of your share buttons look.' )
                ->set_priority( 20 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-total-counts/' );

                //* swDecimals => decmials
                $decimals = new SWP_Option_Select( 'Decimal Places', 'decimals' );
                $decimals->set_choices( [
                    '0' => 'Zero',
                    '1' => 'One',
                    '2' => 'Two',
                ])
                    ->set_default( '0' )
                    ->set_size( 'two-fourths' );

                //* swp_decimal_separator => decimal_separator
                $decimal_separator = new SWP_Option_Select( 'Decimal Separator', 'decimal_separator' );
                $decimal_separator->set_choices( [
                    'period'    => 'Period',
                    'comma'     => 'Comma',
                ])
                    ->set_default( 'period' )
                    ->set_size( 'two-fourths' );

                //* swTotesFormat => totals_alignment
                $totals_alignment = new SWP_Option_Select( 'Alignment', 'totals_alignment' );
                $totals_alignment->set_choices( [
                    'totals_right'  => 'Right',
                    'totals_left'   => 'Left'
                ])
                    ->set_default( 'totals_right' )
                    ->set_size( 'two-fourths' );

            $total_counts->add_options( [$decimals, $decimal_separator, $totals_alignment] );

            $floating_share_buttons = new SWP_Options_Page_Section( 'Floating Share Buttons' );
            $floating_share_buttons->set_description( 'If you would like to activate floating share buttons, turn this on.' )
                ->set_priority( 30 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-floating-share-buttons/' );

                //* float => floating_panel
                $show_floating_panel = new SWP_Option_Toggle( 'Floating Share Buttons', 'floating_panel' );
                $show_floating_panel->set_default( false )
                    ->set_priority( 10 );

                //* floatOption => float_position
                $float_position = new SWP_Option_Select( 'Float Position', 'float_position' );
                $float_position->set_choices( [
                    'top'   => 'Top of the Page',
                    'bottom'    => 'Bottom of the Page',
                    'left'      => 'Left side of the Page',
                    'right'     => 'Right side of the page'
                ])
                    ->set_default( 'bottom' )
                    ->set_priority( 20 )
                    ->set_dependency( 'floating_panel', true );

                //* swp_float_scr_sz => float_screen_width
                $float_screen_width = new SWP_Option_Text( 'Minimum Screen Width', 'float_screen_width' );
                $float_screen_width->set_default( '1100' )
                    ->set_priority( 30 )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_position', ['left', 'right'] );

                //* sideDColorSet => float_default_colors
                $float_default_colors = new SWP_Option_Select( 'Default Color Set', 'default_colors' );

                $color_choices = $this::get_color_choices_array();

                $float_default_colors->set_choices( $color_choices )
                    ->set_default( 'full_color' )
                    ->set_priority( 30 )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_style_source', false );

                //* sideOColorSet => float_hover_colors
                $float_hover_colors = clone $float_default_colors;
                $float_hover_colors->set_name( 'float_hover_colors')
                    ->set_key( 'float_hover_colors' )
                    ->set_priority( 80 );

                //* sideIColorSet => float_single_colors
                $float_single_colors = clone $float_default_colors;
                $float_single_colors->set_name( 'Single Button Hover' )
                    ->set_key( 'float_single_colors' )
                    ->set_priority( 90 );

                //* floatBgColor => float_background_color
                $float_background_color = new SWP_Option_Text( 'Background Color', 'float_background_color' );
                $float_background_color->set_default( '#ffffff' )
                    ->set_priority( 100 )
                     ->set_dependency( 'float_position', ['top', 'bottom'] );

                $floating_share_buttons->add_options( [$show_floating_panel, $float_position,
                    $float_screen_width, $float_default_colors,
                    $float_hover_colors, $float_single_colors,
                    $float_background_color] );

        $styles->add_sections( [$total_counts, $floating_share_buttons] );

        $this->tabs->styles = $styles;

        return $this;
    }

    public function init_social_tab() {
        $social_identity = new SWP_Option_Page_Tab( 'Social Identity' );
        $social_identity->set_priority( 30 )
            ->set_link( 'social_identity' );

        $sitewide_identity = new SWP_Option_Page_Section( 'Sitewide Identity' );
        $sitewide_identity->set_description( 'If you would like to set sitewide defaults for your social identity, add them below.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-social-identity-tab-sitewide-identity/' );

            $twitter_id = new SWP_Option_Input( 'Twitter Username' );
            $twitter_id->set_size( 'two-thirds' );

            $pinterest_id = new SWP_Option_Input( 'Pinterest Username' );
            $pinterest_id->set_size( 'two-thirds' );

            $facebook_publisher_url = new SWP_Option_Input( 'Facebook Page URL ');
            $facebook_publisher_url->set_size( 'two-thirds' );

            $facebook_app_id = new SWP_Option_Input( 'Facebook App ID' );
            $facebook_app_id->set_size( 'two-thirds' );

        $sitewide_identity->add_options( [$twitter_id, $pinterest_id, $facebook_publisher_url, $facebook_app_id] );

        $open_graph = new SWP_Option_Page_Section( 'Open Graph og:type Values');
        $open_graph->set_description( 'These options allow you to control which value you would like to use for the Open Graph og:type tag for each post type.' )
            ->set_priority( 20 );

            $custom_post_types = $this->get_custom_post_type_associative_array();


            //* TODO: integrate sw_get_post_types() into this section.

        $sitewide_identity->add_option( $open_graph );

        $this->tabs->social_identity = $social_identity;
    }

    public function init_advanced_tab() {
        $advanced = new SWP_Option_Page_Tab();
        $advanced->set_priority( 40 )
            ->set_link( 'advanced' );

        //* linkShortening => bitly_authentication
        $bitly_authentication = new SWP_Option_Page_Section( 'Bitly Link Shortening' );
        $bitly_authentication->set_description( 'If you like to have all of your links automatically shortened, turn this on.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-bitly-link-shortening/' );

        //* TODO: Add the Bitly Authentication Button.

        $share_recovery->add_options( [$recover_shares, $recovery_format,
            $recovery_permalink, $recovery_prefix, $recovery_subdomain,
            $former_domain, $current_domain] );

        $caching_method = new SWP_Option_Page_Section( 'Caching Method' );
        $caching_method->set_priority( 60 );

        $full_content = new SWP_Option_Page_Section( 'Full Content vs. Excerpts' );
        $full_content->set_priority( 70 )
             ->set_description( 'If your theme does not use excerpts, but instead displays the full post content on archive, category, and home pages, activate this toggle to allow the buttons to appear in those areas.' )
             ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-full-content-vs-excerpts/' );

            $full_content_toggle = new SWP_Option_Toggle( 'Full Content?', 'full_content' );
            $full_content_toggle->set_default( false )
                ->set_size( 'two-thirds' );

            $full_content->add_option( $full_content_toggle );

        $this->tabs->advanced = $advanced;
    }

    public function init_registration_tab() {
        $registration = new SWP_Option_Page_Tab( 'Registration' );
        $registration->set_priority( 50 )
            ->set_link( 'registration' );
    }

    public function render_HTML() {
        $menu = $this->create_menu();

        $tabs = $this->create_tabs();

        $html = $menu . $tabs;
        $this->html = $html;

        echo $html;

        return $this;
    }

    /**
     * Creates the HTML for the admin top menu (Logo, tabs, and save button).
     *
     * @return $html The fully qualified HTML for the menu.
     */
    private function create_menu() {
        //* Open the admin top menu wrapper.
        $html = '<div class="sw-header-wrapper">';
        $html .= '<div class="sw-grid sw-col-940 sw-top-menu" sw-registered="' . absint( $this->swp_registration ) . '">';


        //* Menu wrapper and tabs.
        $html .= '<div class="sw-grid sw-col-700">';
        $html .= '<img class="sw-header-logo" src="' . SWP_PLUGIN_URL . '/images/admin-options-page/social-warfare-light.png" />';
        $html .= '<img class="sw-header-logo-pro" src="' . SWP_PLUGIN_URL . '/images/admin-options-page/social-warfare-pro-light.png" />';
        $html .= '<ul class="sw-header-menu">';

        foreach( $this->tabs as $index => $tab ) {
            $active = $index === 1 ? 'sw-active-tab' : '';

            $html .= '<li class="' . $active . '">';
            $html .= '<a class="sw-tab-selector" href="#" data-link="swp_' . $tab->link . '">';
            $html .= '<span>' . $tab->name . '</span>';
            $html .= '</a></li>';
        }

        $html .= '</ul>';
        $html .= '</div>';

        //* "Save Changes" button.
        $html .= '<div class="sw-grid sw-col-220 sw-fit">';
        $html .= '<a href="#" class="button sw-navy-button sw-save-settings">Save Changes</a>';
        $html .= '</div>';
        $html .= '<div class="sw-clearfix"></div>';


        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function create_tabs() {
        $container = '<div class="sw-admin-wrapper" sw-registered="' . $this->swp_registration . '">';
        $container .= '<form class="sw-admin-settings-form">';
        $container .= '<div class="sw-tabs-container sw-grid sw-col-700">';

        foreach( $this->tabs as $index => $tab ) {
            $html = $tab->render_HTML();
            $container .= $html;
        }

        $container .= '</div>';
        $container .= '</form>';
        $container .= '</div>';

        return $container;
    }

    private function get_custom_post_type_array() {
        return [
            'article',
			'book',
			'books.author',
			'books.book',
			'books.genre',
			'business.business',
			'fitness.course',
			'game.achievement',
			'music.album',
			'music.playlist',
			'music.radio_station',
			'music.song',
			'place',
			'product',
			'product.group',
			'product.item',
			'profile',
			'restaurant.menu',
			'restaurant.menu_item',
			'restaurant.menu_section',
			'restaurant.restaurant',
			'video.episode',
			'video.movie',
			'video.other',
			'video.tv_show',
        ];
    }

    private function get_custom_post_type_associative_array() {
        $array = $this->get_custom_post_type_array();
        $assosiative = [];

        foreach( $array as $value ) {
            $assosiative[$value] = $value;
        }

        return $assosiative;
    }

    public static function get_color_choices_array() {
        return [
            'full_color' 		=>  'Full Color',
            'light_gray' 		=>  'Light Gray',
            'medium_grey'		=>  'Medium Gray',
            'dark_grey' 		=>  'Dark Gray',
            'light_grey_outlines' 	=>  'Light Gray Outlines',
            'medium_grey_outlines'	=>  'Medium Gray Outlines',
            'dark_grey_outlines' 	=>  'Dark Gray Outlines',
            'color_outlines' 	=>  'Color Outlines',
            'custom_color' 		=>  'Custom Color',
            'custom_color_outlines' =>  'Custom Color Outlines'
        ];
    }
}
