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

                //* minTotes => minimum_shares
                $minimum_shares = new SWP_Options_Text( 'Minimum Shares' );
                $minimum_shares->set_default( 0 )
                    ->set_priority( 30 )
                    ->set_size( 'two-thirds' )
                    ->set_premium( true, 'pro' );

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

        /* Image Hover Pin Button   */

        $image_hover = new SWP_Options_Page_Option( 'Image Hover Pin Button' );
        $image_hover->set_description( 'If you would like a "Pin" button to appear on images when users hover over them, activate this.' )
            ->set_priority( 40 )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-display-tab-image-hover-pin-button/' );

            $pinit_button = new SWP_Options_Toggle( 'Pinit Button' );
            $pinit_button->set_default( true )
                ->set_size( 'two-thirds' )
                ->set_premium( true, 'pro' );

        $image_hover->add_option( $pinit_button );

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
        $styles->set_priority( 20 )
            ->set_description( 'Use the settings below to customize the look of your share buttons.' )
            ->set_premium( true, 'pro' );

            $visual_options = new SWP_Options_Page_Section( 'Visual Options' );
            $visual_options->set_description( 'Use the settings below to customize the look of your share buttons.' )
                ->set_priority( 10 )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-visual-options/' );

                //* visualTheme => button_shape
                $button_shape = new SWP_Options_Select( 'Button Shape' );
                $button_shape->set_choices( [
                    'flat_fresh'=> 'Flat & Fresh',
                    'leaft'     => 'A Leaf on the Wind',
                    'shift'     => 'Shift',
                    'pill'      => 'Pills',
                    'three_dee' => 'Three-Dee',
                    'connectd'  => 'Connected',
                    'boxed'     => 'Boxed'
                ])
                    ->set_default( 'flat_fresh' )
                    ->set_size( 'two-fourths' )
                    ->set_premium( true, 'pro' );

                //* buttonSize => button_size
                $button_size = new SWP_Options_Select( 'Button Size' );
                $button_size->set_choices( [
                    '1.4' => '140%',
                    '1.3' => '130%',
                    '1.2' => '120%',
                    '1.1' => '110%',
                    '1'     => '100%',
                    '0.9'   => '90%',
                    '0.8'   => '80%',
                    '0.7'   => '70%'
                ])
                    ->set_default( '1' )
                    ->set_size( 'two-fourths' )
                    ->set_premium( true, 'pro' );

                $color_choices = $this->get_color_choices_array();

                //* dColorSet => default_colors
                $default_colors = new SWP_Options_Select( 'Default Color Set' );
                $default_colors->set_choices( $color_choices )
                    ->set_default( 'full_color' )
                    ->set_size( 'two-fourths' )
                    ->set_premium( true, 'pro' );

                //* oColorSet => hover_colors
                $hover_colors = clone $default_colors;
                $hover_colors->set_name( 'Hover Color Set');

                //* iColorSet => single_colors
                $single_colors = clone $default_colors;
                $single_colors->set_name( 'Single Button Hover' );

            $button_shape->add_options( [$button_shape, $button_size,
                $color_choices, $default_colors, $single_colors] );


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
                $show_floating_panel->set_default( false );

                //* floatOption => float_position
                $float_position = new SWP_Options_Select( 'Float Position' );
                $float_position->set_choices( [
                    'top'   => 'Top of the Page',
                    'bottom'    => 'Bottom of the Page',
                    'left'      => 'Left side of the Page',
                    'right'     => 'Right side of the page'
                ])
                    ->set_default( 'bottom' )
                    ->set_dependency( 'floating_panel', true );

                //* swp_float_scr_sz => float_screen_width
                $float_screen_width = new SWP_Options_Text( 'Minimum Screen Width' );
                $float_screen_width->set_default( '1100' )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_position', ['left', 'right'] );

                //* sideReveal => transition
                $transition = new SWP_Options_Select( 'Transition' );
                $transition->set_choices( [
                    'slide' => 'Slide In / Slide Out',
                    'fade'  => 'Fade In / Fade Out'
                ])
                    ->set_default( 'slide' )
                    ->set_dependency( 'floating_panel', ['left', 'right'] );
                    ->set_premium( true, 'pro' );

                //* floatStyle => float_button_shape
                $float_button_shape = new SWP_Options_Select( 'Button Shape' );
                $float_button_shape->set_choices( [
                    'default'   => 'Buttons',
                    'boxed'     => 'Boxes',
                ])
                    ->set_default( 'boxed' )
                    ->set_dependency( 'floating_panel', ['left', 'rigt'] )
                    ->set_premium( true, 'pro' );

                //* floatStyleSource => float_style_source
                $float_style_source = new SWP_Options_Select( 'Inherit Visual Options' );
                $float_style_source->set_default( true )
                    ->set_dependency( 'floating_panel', ['left', 'right'] )
                    ->set_premium( true )
                    ->set_addon( 'pro ');

                //* sideDColorSet => float_default_colors
                //* dColorSet => default_colors
                $float_default_colors = new SWP_Options_Select( 'Default Color Set' );
                $default_colors->set_choices( $color_choices )
                    ->set_default( 'full_color' )
                    ->set_size( 'two-fourths' )
                    ->set_dependency( 'float_style_source', false );

                //* sideOColorSet => float_hover_colors
                $float_hover_colors = clone $float_default_colors;
                $float_hover_colors->set_name( 'float_hover_colors');

                //* sideIColorSet => float_single_colors
                $float_single_colors = clone $float_default_colors;
                $float_single_colors->set_name( 'Single Button Hover' );

                //* sideCustomColor => float_custom_color
                $float_custom_color = new SWP_Options_Text( 'Custom Color' );
                $float_custom_color->set_default( '#ced3dc' )
                    ->set_size( 'two-fourths' )
                    ->set_premium( true )
                    ->set_addon( 'pro ');

                //* floatBgColor => float_background_color
                $float_background_color = new SWP_Options_Text( 'Background Color' );
                $float_background_color->set_default( '#ffffff' )
                     ->set_dependency( 'float_position', ['top', 'bottom'] );

                $floating_panel->add_options( [$show_floating_panel, $float_position,
                    $float_screen_width, $transition, $float_button_shape,
                    $float_button_shape, $float_style_source, $float_default_colors,
                    $float_hover_colors, $float_single_colors, $float_custom_color,
                    $float_background_color] );

            $click_to_tweet = new SWP_Options_Page_Section( 'Click-To-Tweet Style' );
            $click_to_tweet->set_description( 'Select the default visual style for Click-to-Tweets on your site.' )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-styles-tab-click-tweet-style/' )
                ->set_priority( 40 );

                $ctt_style = new SWP_Options_Select( 'Visual Theme' );
                $ctt_style->set_choices( [
                    'style1' => 'Send Her My Love',
    				'style2' => 'Roll With The Changes',
    				'style3' => 'Free Bird',
    				'style4' => 'Don\'t Stop Believin\'',
    				'style5' => 'Thunderstruck',
    				'style6' => 'Livin\' On A Prayer',
    				'none' => 'None - Create Your Own CSS In Your Theme'
                ])
                    ->set_default( 'style1' )
                    ->set_premium( true, 'pro' );

                $ctt_css = new SWP_Options_Textarea( 'Custom CSS ' );
                $ctt_css->set_dependency( 'ctt_style', 'none' )
                    ->set_premium( true, 'pro' );

            //* TODO: $click_to_tweet needs the preview added to its section.

            $ctt->add_options( [$ctt_style, $ctt_css] );

        $styles->add_sections( $visual_options, $total_counts,
            $floating_panel, $ctt);

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

            foreach( $post_types as $index => $type ) {
                $priority = ( ( $index + 1 ) * 10 );
                $option = new SWP_Options_Select( usfirst( $type ) );
                $option->set_priority( $priority )
                    ->set_choices( $custom_post_types )
                    ->set_default( 'article' )
                    ->set_premium( true, 'pro' );

                $open_graph->add_option( $option );
            }

            //* TODO: integrate sw_get_post_types() into this section.

        $sitewide_identity->add_option( $open_graph );

    }

    public function init_advanced_tab() {
        $advanced = new SWP_Option_Page_Tab() {
            $frame_buster = new SWP_Option_Page_Section( 'Frame Buster' );
            $frame_buster->set_priority( 10 )
                ->set_description( 'If you want to stop content pirates from framing your content, turn this on.' )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-frame-buster/');

                $frame_buster_toggle = new SWP_Option_Page_Checkbox( '' );
                $frame_buster_toggle->set_default( true )
                    ->set_premium( true, 'pro' );

            $frame_buster->add_option( $frame_buster_toggle;

            //* linkShortening => bitly_authentication
            $bitly_authentication = new SWP_Option_Page_Section( 'Bitly Link Shortening' );
            $bitly_authentication->set_description( 'If you like to have all of your links automatically shortened, turn this on.' )
                ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-bitly-link-shortening/' );

                //* TODO: Add the Bitly Authentication Button.

        $analytics_tracking = new SWP_Option_Page_Section( 'Analytics Tracking' );
        $analytics_tracking->set_description( 'If you want to activate UTM tracking for shared URL, turn this on.' )
            ->set_priority( 30 )


            //* swp_click_tracking => click_tracking
            $click_tracking = new SWP_Option_Page_Checkbox( 'Button Click Tracking ')
                ->set_size( 'two-thirds' )
                ->set_default( false )
                ->set_premium( true )
                ->set_addon( 'pro ');

            //* googleAnalytics => google_analytics
            $google_analytics = clone $click_tracking;
            $google_analytics->set_name( 'UTM Link Tracking' );

            //* analyticsMedium => analytics_medium
            $analytics_medium = new SWP_Options_Text( 'UTM Medium' );
            $analytics_medium->set_default( 'Social' )
                ->set_dependency( 'google_analytics', true )
                ->set_premium( true)
                ->set_addon( 'pro' );

            $analytics_campaign = clone $analytics_medium;
            $analytics_campaign->set_name( 'UTM Campaign' )
                ->set_default( 'SocialWarfare' );
            }

            $analytics_pin_tracking = new SWP_Option_Page_Checkbox( 'UTM Tracking on Pins ' );
            $analytics_pin_tracking->set_default( false )
                ->set_size( 'two-thirds' )
                ->set_dependency( 'google_analytics', true )
                ->set_premium( true, 'pro' );

        $analytics_tracking->add_options( [$click_tracking, $google_analytics, $analytics_medium,
            $analytics_campaign,, $analytics_pin_tracking] );

        $advanced_pinterest = new SWP_Option_Page_Section( 'Advanced Pinterest Settings ');
        $advanced_pinterest->set_description( 'Get maximum control over how your visitors are sharing your content on Pinterest.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-advanced-pinterest-settings/' )
            ->set_priority( 40 )
            ->set_premium( true, 'pro' );

            //* advanced_pinterest_image => pin_browser_extension
            $pin_browser_extension = new SWP_Options_Toggle( 'Pinterest Image for Browser Extensions' );
            $pin_browser_extension->set_default( false )
                ->set_size( 'two-thirds ')
                ->set_premium( true, 'pro' );

            //* advanced_pinterest_image_location => pinterest_image_location
            $pinterest_image_location = new SWP_Options_Select( 'Pinterest Image Location' );
            $pinterest_image_location->set_choices( [
                'hidden'    => 'Hidden',
                'top'       => 'At the top of each post.',
                'bottom'    => 'At the bottom of each post.'
            ])
                ->set_default( 'hidden ')
                ->set_size( 'two-thirds' )
                ->set_dependency( 'pin_browser_extension', true )
                ->set_premium( true )
                ->set_addon( 'pro ');

            //* advanced_pinterest_fallback => pinterest_fallback
            $pinterest_fallback = new SWP_Options_Select( 'Pinterest Image Fallback ');
            $pinterest_fallback->set_choices( [
                'all'   => 'Show a selection of all images on the page.',
                'featured'  => 'Show my featured image.'
            ])
                ->set_default( 'all' )
                ->set_premium( true, 'pro' );

        $advanced_pinterest->add_options( [$pin_browser_extension, $pinterest_image_location, $pinterest_fallback] );

        $share_recovery = new SWP_Option_Page_Section( 'Share Recovery' );
        $share_recovery->set_description( 'If at any point you have changed permalink structures or have gone from http to https (SSL) then you will have undoubtedly lost all of your share counts. This tool allows you to recover them. See <a target="_blank" href="https://warfareplugins.com/support/recover-social-share-counts-after-changing-permalink-settings/">this guide</a> for more detailed instructions on how to use this feature.' )
            ->set_information_link( 'https://warfareplugins.com/support/options-page-advanced-tab-share-recovery/' )
            ->set_premium( true, 'pro' );

            $recover_shares = new SWP_Option_Page_Checkbox( 'Activate Share Recovery' );
            $recover_shares->set_default( false )
                ->set_size( 'two-thirds' )
                ->set_premium( true )
                ->set_addon( 'pro ');

            $recovery_format = new SWP_Options_Select( 'Previous URL Format ');
            $recovery_format->set_choices( [
                'unchanged'			=> 'Unchanged',
				'default' 			=> 'Plain',
				'day_and_name' 		=> 'Day and Name',
				'month_and_name' 	=> 'Month and Name',
				'numeric' 			=> 'Numeric',
				'post_name' 		=> 'Post Name',
				'custom'			=> 'Custom'
            ])
                ->set_default( 'unchanged' )
                ->set_size( 'two-thirds' )
                ->set_dependency( 'recover_shares', true )
                ->set_premium( true, 'pro' );

            //* recovery_custom_format => recovery_permalink
            $recovery_permalink = new SWP_Options_Text( 'Custom Permalink Format' );
            $recovery_permalink->set_size( 'two-thirds' )
                ->set_dependency( 'recover_shares' , true )
                ->set_premium( true, 'pro' );

            $recovery_protocol = new SWP_Options_Select( 'Previous Connection Protocol ');
            $recovery_protocol->set_choices( [
                'unchanged'     => 'Unchanged',
                'http'  => 'http',
                'https' => 'https'
            ])
                ->set_default( 'unchanged' )
                ->set_dependency( 'recover_shares', true )
                ->set_premium( true, 'pro' );

            $recovery_prefix = new SWP_Options_Select( 'Previous Domain Prefix ' );
            $recovery_prefix->set_choices( [
                'Unchanged' => 'Unchanged',
                'www'       => 'www',
                'nonwww'    => 'non-www',
            ])
                ->set_default( 'unchanged' )
                ->set_size( 'two-thirds' )
                ->set_dependency( 'recover_shares', true )
                ->set_premium( true, 'pro' );

            $recovery_subdomain = new SWP_Options_Text( 'Submdomain' );
            $recovery_subdomain->set_default( '' )
                ->set_size( 'two-thirds' )
                ->set_dependency( 'recover_shares', true );
                ->set_premium( true, 'pro' );

            //* TODO: Add the Cross Domain text here.

            $former_domain = new SWP_Options_Text( 'Former Domain ' );
            $former_domain->set_default( '' )
                ->set_size( 'two-thirds' )
                ->set_dependency( 'recover_shares', true )
                ->set_premium( true, 'pro' );

            $current_domain = clone $former_domain;
            $current_domain->set_name( 'Current Domain ');

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
    }
