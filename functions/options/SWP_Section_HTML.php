<?php

/**
* For creating markup that does not fit into the exiting options.
*
* This extends SWP_Option rather than SWP_Section because it uses
* many of the same methods as an option and is a child of a
* section, even though this is neither necessarily
* an option or a section.
*
* @since 3.0.0
*/
class SWP_Section_HTML extends SWP_Option {


    /**
    * HTML
    *
    * The non-conformant markup this object represents.
    * Most of the sections and options can be created using
    * one of the existing SWP_{Item} classes. Sometimes we
    * need something that does not fit those boxes.
    * This class provides native methods for a few of those
    * cases, and an add_HTML() method for everything else.
    *
    * @var string $html
    */
    public $html = '';

    /**
    * The required constructor for PHP classes.
    *
    * @param string $name An arbitrary name, except for do_bitly_authentication_button
    * @param Optional string $key If the object requires access beyond itself, pass it a key.
    *                             Otherwise $name will be used.
    * @see  $this->do_bitly_authentication_button()
    *
    */
    public function __construct( $name, $key = null ) {
        $key = $key === null ? $name : $key;

        parent::__construct( $name, $key );

        $this->html = '';
    }


    /**
    * Allows custom HTML to be added.
    *
    * @param string $html The fully qualified, ready-to-print HTML to display.
    * @return SWP_Section_HTML $this This object for method chaining.
    */
    public function add_HTML( $html ) {
        if ( !is_string( $html) ) :
            $this->_throw( 'This requires a string of HTML!' );
        endif;

        $this->html .= $html;

        return $this;
    }


    /**
    * The Active buttons UI in the Display tab.
    *
    * @param array $icons The array of currently selected icons.
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_active_buttons() {
        $user_icons = $this->get_user_icons();

        $html = '<div class="sw-grid sw-col-300">';
            $html .= '<h3 class="sw-buttons-toggle">' . __( 'Active' , 'social-warfare' ) . '</h3>';
        $html .= '</div>';

        $html .= '<div class="sw-grid sw-col-620 sw-fit">';
            $html .= '<div class="sw-active sw-buttons-sort">';

            foreach ( $user_icons['icons'] as $network => $data ) {
                $html .= '<i class="sw-s sw-' . $network . '-icon" ';
                $html .= ' data-network="' . $network . '"';

                if ( isset($data['premium']) && $data['premium'] === 'premium' ) :
                    $html .= ' premium="1"';
                endif;

                $html .= '></i>';
            }

            $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="sw-clearfix"></div>';

        $this->html = $html;

        return $this;
    }


    /**
    * Render the Bitly connection button on the Advanced tab.
    *
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_bitly_authentication_button() {
        $link = "https://bitly.com/oauth/authorize?client_id=96c9b292c5503211b68cf4ab53f6e2f4b6d0defb&state=https://warfareplugins.com/wp-admin/admin-ajax.php&redirect_uri=https://warfareplugins.com/bitly_oauth.php";

        if ( isset( $this->dependant) && !empty( $this->dependant) ):
            $text = __( 'Connected', 'social-warfare' );
            $color = 'sw-green-button';
        else:
            $text = __( 'Authenticated', 'social-warfare' );
            $color = 'sw-navy-button';
        endif;

        ob_start() ?>

            <div class="sw-grid sw-col-940 sw-fit sw-option-container <?php echo $this->key ?> '_wrapper" <?php $this->render_dependency(); $this->render_premium(); ?>>
                <div class="sw-grid sw-col-300">
                    <p class="sw-authenticate-label"><?php __( $this->name, 'social-warfare' ) ?></p>
                </div>
                <div class="sw-grid sw-col-300">
                    <a class="button <?php echo $color ?>" href="<?php echo $link ?>"><?php echo $text ?></a>
                </div>
                <div class="sw-grid sw-col-300 sw-fit"></div>
            </div>

        <?php

        $this->html = ob_get_contents();
        ob_end_clean();

        return $this;
    }


    /**
    * The buttons preview as shown on the Display tab.
    *
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_buttons_preview() {
        ob_start() ?>

        <div class="nc_socialPanel swp_flatFresh swp_d_fullColor swp_i_fullColor swp_o_fullColor" data-position="both" data-float="float_ignore" data-count="6" data-floatColor="#ffffff" data-scale="1" data-align="full_width">
            <div class="nc_tweetContainer googlePlus" data-id="2">
                <a target="_blank" href="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" data-link="https://plus.google.com/share?url=http%3A%2F%2Fwfa.re%2F1W28voz" class="nc_tweet">
                    <span class="iconFiller">
                        <span class="spaceManWilly">
                            <i class="sw sw-google-plus"></i>
                            <span class="swp_share"><?php __( '+1','social-warfare' ) ?></span>
                        </span>
                    </span>
                    <span class="swp_count">1.2K</span>
                </a>
            </div>
            <div class="nc_tweetContainer twitter" data-id="3">
                <a href="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" data-link="https://twitter.com/share?original_referer=/&text=Ultimate+Social+Share+%23WordPress+plugin%21+Beautiful%2C+super+fast+%26+more+http%3A%2F%2Fwarfareplugins.com+pic.twitter.com%2FA2zcCJwZtO&url=/&via=WarfarePlugins" class="nc_tweet">
                    <span class="iconFiller">
                        <span class="spaceManWilly">
                            <i class="sw sw-twitter"></i>
                            <span class="swp_share"><?php __( 'Tweet','social-warfare' ) ?></span>
                        </span>
                    </span>
                    <span class="swp_count">280</span>
                </a>
            </div>
            <div class="nc_tweetContainer nc_pinterest" data-id="6">
                <a data-link="https://pinterest.com/pin/create/button/?url=https://warfareplugins.com/&media=https%3A%2F%2Fwarfareplugins.com%2Fwp-content%2Fuploads%2Fget-content-shared-735x1102.jpg&description=Customize+your+Pinterest+sharing+options%2C+create+easy+%22click+to+tweet%22+buttons+within+your+blog+posts%2C+beautiful+sharing+buttons+and+more.+Social+Warfare+is+the+ultimate+social+sharing+arsenal+for+WordPress%21" class="nc_tweet" data-count="0">
                    <span class="iconFiller">
                        <span class="spaceManWilly">
                            <i class="sw sw-pinterest"></i>
                            <span class="swp_share"><?php __( 'Pin','social-warfare' ) ?>'</span>
                        </span>
                    </span>
                    <span class="swp_count">104</span>
                </a>
            </div>
            <div class="nc_tweetContainer swp_fb" data-id="4">
                <a target="_blank" href="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" data-link="http://www.facebook.com/share.php?u=http%3A%2F%2Fwfa.re%2F1W28vov" class="nc_tweet">
                    <span class="iconFiller">
                        <span class="spaceManWilly">
                            <i class="sw sw-facebook"></i>
                            <span class="swp_share"><?php __( 'Share','social-warfare' ) ?></span>
                        </span>
                    </span>
                    <span class="swp_count">157</span>
                </a>
            </div>
            <div class="nc_tweetContainer linkedIn" data-id="5">
                <a target="_blank" href="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" data-link="https://www.linkedin.com/cws/share?url=http%3A%2F%2Fwfa.re%2F1W28twH" class="nc_tweet">
                    <span class="iconFiller">
                        <span class="spaceManWilly">
                            <i class="sw sw-linkedin"></i>
                            <span class="swp_share"><?php __( 'Share','social-warfare' ) ?></span>
                        </span>
                    </span>
                    <span class="swp_count">51</span>
                </a>
            </div>
            <div class="nc_tweetContainer totes totesalt" data-id="6" >
            <span class="swp_count">
                <span class="swp_label">Shares</span> 1.8K
            </span>
            </div>
        </div>

        <?php

        $this->html = ob_get_contents();
        ob_end_clean();

        return $this;
    }


    /**
    * The Inactive buttons UI in the Display tab.
    *
    * @param array $icons The array of currently selected icons.
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    * //* TODO: finish this method.
    */
    public function do_inactive_buttons() {
        $all_icons = $this->get_all_icons();
        $user_icons = $this->get_user_icons();

        $html = '<div class="sw-grid sw-col-300">';
            $html .=  '<h3 class="sw-buttons-toggle">' . __( 'Inactive' , 'social-warfare' ) . '</h3>';
        $html .=  '</div>';

        $html .=  '<div class="sw-grid sw-col-620 sw-fit">';
            $html .=  '<div class="sw-inactive sw-buttons-sort">';

            foreach( $all_icons['icons'] as $network => $data ) {
                if ( !isset( $user_icons['icons'][$network]) ) :
                    $html .= '<i class="sw-s sw-' . $network . '-icon" ';
                    $html .= ' data-network="' . $network . '"';

                    if ( isset($data['premium']) && $data['premium'] === 'premium' ) :
                        $html .= ' premium="1"';
                    endif;

                    $html .= '></i>';
                endif;
            }

            $html .= '</div>';
        $html .= '</div>';

        $this->html = $html;

        return $this;
    }


    /**
    * Renders the three column table on the Display tab.
    *
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_button_position_table() {
        $static_options = [
            'above'=> 'Above the Content',
            'below' => 'Below the Content',
            'both' => 'Both Above and Below the Content',
            'none' => 'None/Manual Placement'
        ];

        $default_types = ['page', 'post'];
        $post_types = array_merge( $default_types, get_post_types( ['public' => true, '_builtin' => false ], 'names' ) );

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

        $html = '<div class="sw-grid sw-col-940 sw-fit sw-option-container" ';
        $html .= $this->render_dependency();
        $html .= $this->render_premium();
        $html .= '>';

        $html .= '<div class="sw-grid sw-col-300">';
            $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Post Type' ,'social-warfare' ) . '</p>';
        $html .= '</div>';
        $html .= '<div class="sw-grid sw-col-300">';
            $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Static Buttons' ,'social-warfare' ) . '</p>';
        $html .= '</div>';
        $html .= '<div class="sw-grid sw-col-300 sw-fit">';
            $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Floating Buttons (If Activated)' ,'social-warfare' ) . '</p>';
        $html .= '</div>';

        foreach( $post_types as $index => $post ) {
            $priority = ($index + 1) * 10;

            $html .= '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $post . '_wrapper">';

                $html .= '<div class="sw-grid sw-col-300">';
                    $html .= '<p class="sw-input-label">' . str_replace('_', ' ', ucfirst($post)) . '</p>';
                $html .= '</div>';

                $html .= '<div class="sw-grid sw-col-300">';

                    $panel = new SWP_Option_Select( 'Panel '. ucfirst( $post ), 'position_' . $post );
                    $panel->set_priority( $priority )
                        ->set_size( 'two-thirds' )
                        ->set_choices( $panel_locations )
                        ->set_default( 'both' );

                    $html .= $panel->render_HTML_element();

                $html .= '</div>';
                $html .= '<div class="sw-grid sw-col-300 sw-fit">';

                    $float = new SWP_Option_Select( 'Float ' . ucfirst( $post ), 'float_location_' . $post );
                    $float->set_priority( $priority + 5 )
                        ->set_size( 'two-thirds' )
                        ->set_choices( $float_locations )
                        ->set_default( 'on' );

                    $html .= $float->render_HTML_element();

                $html .= '</div>';

            $html .= '</div>';

        }

        $html .= '</div>';

        $this->html = $html;

        return $this;
    }

    /**
    * Creates the Click To Tweet preview for the Styles tab.
    *
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_ctt_preview() {
        //* Pull these variables out just to make the $html string easier to read.
        $link = "https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://warfareplugins.com&amp;via=warfareplugins";
        $data_link = "https://twitter.com/share?text=We+couldn%27t+find+one+social+sharing+plugin+that+met+all+of+our+needs%2C+so+we+built+it+ourselves.&amp;url=http://wfa.re/1PtqdNM&amp;via=WarfarePlugins";
        $text = "We couldn't find one social sharing plugin that met all of our needs, so we built it ourselves.";

        $html = '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_wrapper">';
            $html .= '<a class="swp_CTT style1"  data-style="style1" href="' . $link . '" data-link="' . $data_link . '" target="_blank">';
                $html .= '<span class="sw-click-to-tweet">';
                    $html .= '<span class="sw-ctt-text">' . $text . '</span>';
                    $html .= '<span class="sw-ctt-btn">Click To Tweet';
                        $html .= '<i class="sw sw-twitter"></i>';
                    $html .= '</span>';
                $html .= '</span>';
            $html .= '</a>';
        $html .= '</div>';


        $this->html = $html;

        return $this;

    }


    /**
    * Renders the three column table on the Display tab.
    *
    * @return SWP_Section_HTML $this The calling instance, for method chaining.
    */
    public function do_yummly_display() {
        $html = '<div class="sw-grid sw-col-940 sw-fit sw-option-container ' . $this->key . '_wrapper" ';
        $html .= $this->render_dependency();
        $html .= $this->render_premium();
        $html .= '>';


            //* Table headers
            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding"></p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Choose Category' ,'social-warfare' ) . '</p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300 sw-fit">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Choose Tag' ,'social-warfare' ) . '</p>';
            $html .= '</div>';

            $yummly_categories = new SWP_Option_Text( 'Yummly Categories', 'yummly_categories' );
            $categories_html = $yummly_categories->set_priority( 10 )
                ->set_default( '' )
                ->render_HTML_element();

            $yummly_tags = new SWP_Option_Text( 'Yummly Tags', 'yummly_tags' );
            $tags_html = $yummly_categories->set_priority( 10 )
                ->set_default( '' )
                ->render_HTML_element();

            //* Table body
            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding">' . __( 'Yummly Terms' ,'social-warfare' ) . '</p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding">' . $categories_html . '</p>';
            $html .= '</div>';

            $html .= '<div class="sw-grid sw-col-300 sw-fit">';
                $html .= '<p class="sw-select-label sw-short sw-no-padding">' . $tags_html . '</p>';
            $html .= '</div>';

        $html .= '</div>';

        $this->html = $html;

        return $this;
    }


    /**
    * The rendering method common to all classes.
    *
    * Unlike the other option classes, this class creates its HTML
    * and does not immediately return it. Instead, it stores the
    * HTML inside itself and waits for the render_html method to be called.
    *
    * @return This object's saved HTML.
    */
    public function render_HTML() {
        return $this->html;
    }
}

