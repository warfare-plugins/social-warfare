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

	public function __construct() {
        $this->tabs = [];
    }

    /**
     * Create the Share Counts section of the display tab.
     *
     * This section of options allows users to control the share count settings.
     *
     */
    public function init_display_tab() {
        $display = new SWP_Options_Page_Tab( "Display" );
		$display->set_priority( 10 );

    		$share_counts = new SWP_Options_Page_Section( 'Share Counts' );
    	    $share_counts->set_description( 'This is the description' )
                ->set_priority( 10 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-share-counts/' );

                //* toteseach => network_count
        		$network_count = new SWP_Options_Toggle( 'Button Counts' );
        		$network_count->set_default( true )
                    ->set_priority( 10 )
                    ->set_size('two-thirds');

                //* totes => totals
                $totals = new SWP_Options_Toggle( 'Total Counts');
                $totals->set_default( true )
                    ->set_priority( 20 )
                    ->set_size( 'two-thirds' );

            $share_counts->add_options( [$network_count, $totals, $minimum_shares] );

        /* Twitter Cards   */

        $twitter_cards = new SWP_Options_Page_Section( 'Twitter Cards' );
        $twitter_cards->set_description( 'Activating Twitter Cards will cause the plugin to output certain meta tags in the head section of your site\'s HTML. Twitter cards are pretty much exactly like Open Graph meta tags, except that there is only one network, Twitter, that looks at them.' )
            ->set_priority( 20 )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-twitter-cards/' );

                $twitter_card = new SWP_Options_Toggle( 'Show Twitter Cards' );
                $twitter_card->set_default( true )
                    ->set_size( 'two-thirds' );

            $twitter_cards->add_option( $twitter_card );

        /* Position Share Buttons  */

        $button_position = new SWP_Option_Page_Section( 'Position Share Buttons' );
        $button_position->set_description( 'These settings let you decide where the share buttons should go for each post type.' )
            ->set_priority( 30 )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-position-share-buttons/' );

        //* TODO: Create the mini-table for this option.

        /* Yummly Display Control  */

        $yummly_display = new SWP_Options_Page_Option( 'Yummy Display Control' );
        $yummly_display->set_description( 'If you would like the Yummly button to only display on posts of a specific category or tag, enter the category or tag name below (e.g "Recipe"). Leave blank to display the button on all posts.' )
            ->set_priority( 50 )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-yummly-display-control/' );

        //* TODO: Create the mini-table for this option.

        $display->add_sections( [$share_counts, $twitter_cards, $button_position, $image_hover, $yummly_display] );

        array_push( $this->tabs, $display );

        return $this;
    }

    public function init_styles_tab() {
        $styles = new SWP_Options_Page_Tab( 'Styles' );
        $styles->set_priority( 20 );

            $visual_options = new SWP_Options_Page_Section( 'Visual Options' );
            $visual_options->set_description( 'Use the settings below to customize the look of your share buttons.' )
                ->set_priority( 10 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-visual-options/' );

                //* oColorSet => hover_colors
                $hover_colors = clone $default_colors;
                $hover_colors->set_name( 'Hover Color Set')
                    ->set_priority( 40 );

                //* iColorSet => single_colors
                $single_colors = clone $default_colors;
                $single_colors->set_name( 'Single Button Hover' )
                    ->set_priority( 50 );

            $visual_options->add_options( [$default_colors, $single_colors] );

            $total_counts = new SWP_Options_Page_Section( 'Total Counts' );
            $total_counts->set_description( 'Customize how the "Total Shares" section of your share buttons look.' )
                ->set_priority( 20 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-total-counts/' );

                //* swDecimals => decmials
                $decimals = new SWP_Options_Select( 'Decimal Places' );
                $decimals->set_choices( [
                    '0' => 'Zero',
                    '1' => 'One',
                    '2' => 'Two',
                ])
                    ->set_default( '0' )
                    ->set_size( 'two-fourths' );

                //* swp_decimal_separator => decimal_separator
                $decimal_separator = new SWP_Options_Select( 'Decimal Separator' );
                $decimal_separator->set_choices( [
                    'period'    => 'Period',
                    'comma'     => 'Comma',
                ])
                    ->set_default( 'period' )
                    ->set_size( 'two-fourths' );


                //* swTotesFormat => totals_format
                $totals_alignment = new SWP_Options_Select( 'Alignment' );
                $totals_alignment->set_choices( [
                    'totals_right'  => 'Right',
                    'totals_left'   => 'Left'
                ])
                    ->set_default( 'totals_right' )
                    ->set_size( 'two-fourths' );

            $total_counts->add_options( [$decimals, $decimal_separator, $totals_alignment] );

            $floating_panel = new SWP_Option_Page_Section( 'Floating Share Buttons' );
            $floating_panel->set_description( 'If you would like to activate floating share buttons, turn this on.' )
                ->set_priority( 30 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-floating-share-buttons/' );

                //* float => floating_panel
                $show_floating_panel = new SWP_Options_Toggle( '' );
                $show_floating_panel->set_default( false )
                    ->set_priority( 10 );

                //* floatOption => float_position
                $float_position = new SWP_Options_Select( 'Float Position' );
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
                $float_screen_width = new SWP_Options_Text( 'Minimum Screen Width' );
                $float_screen_width->set_default( '1100' )
                    ->set_priority( 30 )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_position', ['left', 'right'] );

                //* sideDColorSet => float_default_colors
                //* dColorSet => default_colors
                $float_default_colors = new SWP_Options_Select( 'Default Color Set' );
                $default_colors->set_choices( $color_choices )
                    ->set_default( 'full_color' )
                    ->set_priority( 30 )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_style_source', false );

                //* sideOColorSet => float_hover_colors
                $float_hover_colors = clone $float_default_colors;
                $float_hover_colors->set_name( 'float_hover_colors')
                     ->set_priority( 80 )

                //* sideIColorSet => float_single_colors
                $float_single_colors = clone $float_default_colors;
                $float_single_colors->set_name( 'Single Button Hover' )
                    ->set_priority( 90 )

                //* floatBgColor => float_background_color
                $float_background_color = new SWP_Options_Text( 'Background Color' );
                $float_background_color->set_default( '#ffffff' )
                    ->set_priority( 100 )
                     ->set_dependency( 'float_position', ['top', 'bottom'] );

                $floating_panel->add_options( [$show_floating_panel, $float_position,
                    $float_screen_width, $float_style_source, $float_default_colors,
                    $float_hover_colors, $float_single_colors,
                    $float_background_color] );


        $styles->add_sections( $visual_options, $total_counts,
            $floating_panel);

        array_push( $this->tabs, $styles );

        return $this;

    }

    public function init_social_tab() {
        $social_identity = new SWP_Option_Page_Tab( 'Social Identity' );
        $social_identity->set_priority( 30 );

        $sitewide_identity = new SWP_Option_Page_Section( 'Sitewide Identity' );
        $sitewide_identity->set_description( 'If you would like to set sitewide defaults for your social identity, add them below.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-social-identity-tab-sitewide-identity/' );

            $twitter_id = new SWP_Option_Input( 'Twitter Username' );
            $twitter_id->set_size( 'two-thirds' )

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

    }

    public function init_advanced_tab() {
        $advanced = new SWP_Option_Page_Tab();
        $advanced->set_priority( 40 );

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

            $full_content_toggle = new SWP_Options_Toggle( 'Full Content? ');
            $full_content_toggle->set_default( false )
                ->set_size( 'two-thirds' );

            $full_content->add_option( $full_content_toggle );
        }

    public function init_registration_tab() {
        $registration = new SWP_Option_Page_Tab( 'Registration' );
        $registration->set_priority( 50 );
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

    private function get_color_choices_array() {
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
