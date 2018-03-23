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

        $this->icons = apply_filters( 'swp_button_options', array() );
    }

    public function init() {
        $swp_user_options = swp_get_user_options( true );
        // echo "<pre>";`
        // var_export($swp_user_options);
        // die();`
        $this->init_display_tab()
            ->init_styles_tab()
            ->init_social_tab()
            ->init_advanced_tab()
            ->init_registration_tab();

        if ( class_exists( 'SWP_Pro_Options_Page' ) ) :
            $Pro = new SWP_Pro_Options_Page();
            $Pro->update_display_tab()
                ->update_styles_tab()
                ->update_social_tab()
                ->update_advanced_tab();
        endif;

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

    public function render_HTML() {
        $menu = $this->create_menu();
        $tabs = $this->create_tabs();

        $html = $menu . $tabs;
        $this->html = $html;

        echo $html;

        return $this;
    }

    public static function get_color_choices_array() {
        return [
            'full_color'        =>  'Full Color',
            'light_gray'        =>  'Light Gray',
            'medium_grey'       =>  'Medium Gray',
            'dark_grey'         =>  'Dark Gray',
            'light_grey_outlines'   =>  'Light Gray Outlines',
            'medium_grey_outlines'  =>  'Medium Gray Outlines',
            'dark_grey_outlines'    =>  'Dark Gray Outlines',
            'color_outlines'    =>  'Color Outlines',
            'custom_color'      =>  'Custom Color',
            'custom_color_outlines' =>  'Custom Color Outlines'
        ];
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
    protected function init_display_tab() {
        $display = new SWP_Options_Page_Tab( 'Display', 'display' );
		$display->set_priority( 10 );

            $social_networks = new SWP_Options_Page_Section( 'Social Networks' );
            $social_networks->set_priority( 10 )
                ->set_description( 'Drag & Drop to activate and order your share buttons.' );

                //* These two sections are unique and need special HTML.
                $active = new SWP_Section_HTML( 'Active' );

                $icons = isset( $this->user_options['new_order_of_icons'] ) ? $this->user_options['new_order_of_icons'] : $this->icons;

                $active->do_active_buttons( $icons );

                $inactive = new SWP_Section_HTML( 'Inactive' );
                $inactive->do_inactive_buttons();

                $social_networks->add_options( [$active, $inactive] );

    		$share_counts = new SWP_Options_Page_Section( 'Share Counts' );
    	    $share_counts->set_description( 'Use the toggles below to determine how to display your social proof.' )
                ->set_priority( 20 )
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

            $button_position = new SWP_Options_Page_Section( 'Position Share Buttons' );
            $button_position->set_description( 'These settings let you decide where the share buttons should go for each post type.' )
                ->set_priority( 40 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-position-share-buttons/' );

            $static_options = $this->get_static_options_array();

            //* locationHome => location_home
            // $location_home = new SWP_Option_Select( 'Home Page', 'location_home' );
            // $location_home->set_choices( $static_options )
            //     ->set_size( 'two-thirds' )
            //     ->set_default( 'none' )
            //     ->set_priority( 30 );

            // //* locationSite => location_archive_categories
            // $location_archive_categories = new SWP_Option_Select( 'Archive & Categories', 'location_archive_categories' );
            // $location_archive_categories->set_choices( $static_options )
            //     ->set_size( 'two-thirds' )
            //     ->set_default( 'below' )
            //     ->set_priority( 40 );

            // $button_position->add_options( [$location_home, $location_archive_categories] );

            $post_types = get_post_types( ['public' => true, '_builtin' => false ], 'names' );
            array_push( $post_types, 'page', 'post' );

            $panel_locations = [
                'above' => 'Above the Content',
                'below' => 'Below the Content',
                'both'  => 'Both Above and Below the Content',
                'none'  => 'None/Manual Placement'
            ];

            $float_locations = [
                'on'    => 'On',
                'off'   => 'Off'
            ];

            //* TODO: This does not exactly match the original layout.
            //* It needs to be a table of options, not a column.
            foreach( $post_types as $index => $post ) {
                $priority = ($index + 1) * 10;

                $panel = new SWP_Option_Select( 'Panel '. ucfirst( $post ), 'position_' . $post );
                $panel->set_priority( $priority )
                    ->set_size( 'two-thirds' )
                    ->set_choices( $panel_locations )
                    ->set_default( 'both' );

                $float = new SWP_Option_Select( 'Float ' . ucfirst( $post ), 'float_position_' . $post );
                $float->set_priority( $priority + 5 )
                    ->set_size( 'two-thirds' )
                    ->set_choices( $float_locations )
                    ->set_default( 'on' );

                $button_position->add_options( [$panel, $float] );
            }

        $display->add_sections( [$social_networks, $share_counts, $button_position] );

        $this->tabs->display = $display;

        return $this;
    }

    private function render_share_buttons_HTML() {
        $html = '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $key . '_wrapper" ' . (isset( $option['dep'] ) ? 'data-dep="' . $option['dep'] . '" data-dep_val=\'' . json_encode( $option['dep_val'] ) . '\'' : '') . ' ' . (isset( $option['premium'] ) ? 'premium="' . $option['premium'] . '"' : '') . '>';
        $html .= '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_1'] . '</p></div>';
        $html .= '<div class="sw-grid sw-col-300"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_2'] . '</p></div>';
        $html .= '<div class="sw-grid sw-col-300 sw-fit"><p class="sw-select-label sw-short sw-no-padding">' . $option['column_3'] . '</p></div>';
        $html .= '<div class="sw-premium-blocker"></div>';
        $html .= '</div>';
    }

    protected function init_styles_tab() {
        $styles = new SWP_Options_Page_Tab( 'Styles', 'styles' );
        $styles->set_priority( 20 );

            $buttons_preview = new SWP_Section_HTML( 'Buttons Preview' );
            $buttons_preview->set_priority( 10 );
            $buttons_preview->do_buttons_preview();

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

                //* sideReveal => transition
                $float_transition = new SWP_Option_Select( 'Transition', 'transition' );
                $float_transition->set_priority( 40 )
                    ->set_choices( [
                        'slide' => 'Slide In / Slide Out',
                        'fade'  => 'Fade In / Fade Out'
                    ] )
                    ->set_default( 'slide' )
                    ->set_dependency( 'float_position', ['left', 'right']);

                //* sideDColorSet => float_default_colors
                $float_default_colors = new SWP_Option_Select( 'Default Color Set', 'default_colors' );

                $color_choices = $this::get_color_choices_array();

                $float_default_colors->set_choices( $color_choices )
                    ->set_default( 'full_color' )
                    ->set_priority( 50 )
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

                $floating_share_buttons->add_options( [$show_floating_panel, $float_position, $float_transition,
                    $float_screen_width, $float_default_colors,
                    $float_hover_colors, $float_single_colors,
                    $float_background_color] );

        $styles->add_sections( [$total_counts, $floating_share_buttons] );

        $this->tabs->styles = $styles;

        return $this;
    }

    protected function init_social_tab() {
        $social_identity = new SWP_Options_Page_Tab( 'Social Identity', 'social_identity' );
        $social_identity->set_priority( 30 );

        $sitewide_identity = new SWP_Options_Page_Section( 'Sitewide Identity' );
        $sitewide_identity->set_description( 'If you would like to set sitewide defaults for your social identity, add them below.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-social-identity-tab-sitewide-identity/' );

            $twitter_id = new SWP_Option_Text( 'Twitter Username', 'twitter_id' );
            $twitter_id->set_size( 'two-thirds' );

            //* pinterestID => pinterest_id
            $pinterest_id = new SWP_Option_Text( 'Pinterest Username', 'pinterest_id' );
            $pinterest_id->set_size( 'two-thirds' );

            //* facebookPublisherUrl => facebook_publisher_url
            $facebook_publisher_url = new SWP_Option_Text( 'Facebook Page URL', 'facebook_publisher_url' );
            $facebook_publisher_url->set_size( 'two-thirds' );

            //* facebookAppID => facebook_app_id
            $facebook_app_id = new SWP_Option_Text( 'Facebook App ID', 'facebook_app_id' );
            $facebook_app_id->set_size( 'two-thirds' );

        $sitewide_identity->add_options( [$twitter_id, $pinterest_id, $facebook_publisher_url, $facebook_app_id] );

            //* TODO: integrate sw_get_post_types() into this section.

        $social_identity->add_section( $sitewide_identity );

        $this->tabs->social_identity = $social_identity;

        return $this;
    }

    protected function init_advanced_tab() {
        $advanced = new SWP_Options_Page_Tab( 'Advanced', 'advanced' );
        $advanced->set_priority( 40 );

        $frame_buster = new SWP_Options_Page_Section( 'Frame Buster' );
        $frame_buster->set_priority( 10 )
            ->set_description( 'If you want to stop content pirates from framing your content, turn this on.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-frame-buster/');

            //* sniplyBuster => frame_buster
            $frame_buster_toggle = new SWP_Option_Toggle( 'Frame Buster', 'frame_buster' );
            $frame_buster_toggle->set_default( true );

            $frame_buster->add_option( $frame_buster_toggle );

        //* TODO: Add the Bitly Authentication Button.

        $caching_method = new SWP_Options_Page_Section( 'Caching Method' );
        $caching_method->set_priority( 60 );

            //* cacheMethod => cache_method
            $cache_method = new SWP_Option_Select( 'Cache Rebuild Method', 'cache_method' );
            $cache_method->set_choices( [
                'advanced'  => 'Advanced Cache Triggering',
                'legacy'    => 'Legacy Cache Rebuilding during Page Loads'
            ])
                ->set_default( 'advanced' )
                ->set_size( 'two-thirds' );

            $caching_method->add_option( $cache_method );

        $full_content = new SWP_Options_Page_Section( 'Full Content vs. Excerpts' );
        $full_content->set_priority( 70 )
             ->set_description( 'If your theme does not use excerpts, but instead displays the full post content on archive, category, and home pages, activate this toggle to allow the buttons to appear in those areas.' )
             ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-full-content-vs-excerpts/' );

            $full_content_toggle = new SWP_Option_Toggle( 'Full Content?', 'full_content' );
            $full_content_toggle->set_default( false )
                ->set_size( 'two-thirds' );

            $full_content->add_option( $full_content_toggle );

        $advanced->add_sections( [$frame_buster, $caching_method, $full_content] );

        $this->tabs->advanced = $advanced;

        return $this;
    }

    protected function init_registration_tab() {
        $registration = new SWP_Options_Page_Tab( 'Registration', 'registration' );

        $registration->set_priority( 50 );

            $wrap = new SWP_Options_Page_Section( 'Addon Registrations', 'addon' );
            $wrap->set_priority( 10 );

                $pro = new SWP_Addon_Registration( 'Pro Registration', 'pro' );
                $pro->set_priority( 10 );

            $wrap->add_option( $pro );

        $registration->add_section( $wrap );

        $this->tabs->registration = $registration;

        return $this;
    }

    protected function get_static_options_array() {
        return [
            'above'=> 'Above the Content',
            'below' => 'Below the Content',
            'both' => 'Both Above and Below the Content',
            'none' => 'None/Manual Placement'
        ];
    }

    protected function get_custom_post_types() {
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
            $active = $index === 2 ? 'sw-active-tab' : '';

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

}
