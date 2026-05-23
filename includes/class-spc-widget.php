<?php
/**
 * Staff Profile Card Elementor Widget — v2.0 Multi-Profile Directory.
 *
 * @package StaffProfileCard
 */

namespace SPC;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Widget class for the Staff Profile Card (v2 — directory).
 */
class Widget extends Widget_Base {

    /* -------------------------------------------------------------- */
    /*  Widget meta                                                   */
    /* -------------------------------------------------------------- */

    public function get_name() {
        return 'staff_profile_card';
    }

    public function get_title() {
        return esc_html__( 'Staff Profile Card', 'staff-profile-card' );
    }

    public function get_icon() {
        return 'eicon-person';
    }

    public function get_categories() {
        return [ 'staff-profile' ];
    }

    public function get_keywords() {
        return [ 'staff', 'profile', 'card', 'team', 'member', 'directory', 'academic' ];
    }

    public function get_style_depends() {
        return [ 'spc-widget-style' ];
    }

    /* -------------------------------------------------------------- */
    /*  Controls                                                      */
    /* -------------------------------------------------------------- */

    protected function register_controls() {


        /* ---- Content Tab: Staff Profiles (Repeater) ---- */
        $this->start_controls_section( 'section_staff_list', [
            'label' => esc_html__( 'Staff Profiles', 'staff-profile-card' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $repeater = new Repeater();

        $repeater->add_control( 'api_profile_id', [
            'label'       => esc_html__( 'API Profile ID', 'staff-profile-card' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => esc_html__( 'SPC-XXXXXXXX', 'staff-profile-card' ),
            'label_block' => true,
            'description' => esc_html__( 'Enter the generated API Profile ID. Do not use the Staff ID or internal database ID.', 'staff-profile-card' ),
        ] );

        $this->add_control( 'staff_list', [
            'label'       => esc_html__( 'Staff Members', 'staff-profile-card' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [],
            'title_field' => '{{{ api_profile_id || "New Profile" }}}',
        ] );

        $this->end_controls_section();

        /* ---- Content Tab: Display Options ---- */
        $this->start_controls_section( 'section_display', [
            'label' => esc_html__( 'Display Options', 'staff-profile-card' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ] );

        $this->add_control( 'show_image', [
            'label'        => esc_html__( 'Show Image', 'staff-profile-card' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'staff-profile-card' ),
            'label_off'    => esc_html__( 'Hide', 'staff-profile-card' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_designation', [
            'label'        => esc_html__( 'Show Designation', 'staff-profile-card' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'staff-profile-card' ),
            'label_off'    => esc_html__( 'Hide', 'staff-profile-card' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_qualifications', [
            'label'        => esc_html__( 'Show Qualifications', 'staff-profile-card' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'staff-profile-card' ),
            'label_off'    => esc_html__( 'Hide', 'staff-profile-card' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->add_control( 'show_view_profile', [
            'label'        => esc_html__( 'Show "View Profile" Link', 'staff-profile-card' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => esc_html__( 'Show', 'staff-profile-card' ),
            'label_off'    => esc_html__( 'Hide', 'staff-profile-card' ),
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );

        $this->end_controls_section();

        /* ---- Style Tab: Card ---- */
        $this->start_controls_section( 'section_style_card', [
            'label' => esc_html__( 'Card', 'staff-profile-card' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );

        $this->add_control( 'accent_color', [
            'label'     => esc_html__( 'Accent Color', 'staff-profile-card' ),
            'type'      => Controls_Manager::COLOR,
            'default'   => '#E8590C',
            'selectors' => [
                '{{WRAPPER}} .spc-card__name a'         => 'color: {{VALUE}};',
                '{{WRAPPER}} .spc-card__view-profile'   => 'color: {{VALUE}};',
            ],
        ] );

        $this->add_responsive_control( 'card_padding', [
            'label'      => esc_html__( 'Padding', 'staff-profile-card' ),
            'type'       => Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'default'    => [
                'top'    => '16',
                'right'  => '20',
                'bottom' => '16',
                'left'   => '20',
                'unit'   => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .spc-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'card_gap', [
            'label'      => esc_html__( 'Gap Between Cards', 'staff-profile-card' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [
                'px' => [
                    'min' => 0,
                    'max' => 40,
                ],
            ],
            'default'    => [
                'size' => 12,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .spc-directory__list' => 'gap: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();

        /* ---- Style Tab: Image ---- */
        $this->start_controls_section( 'section_style_image', [
            'label'     => esc_html__( 'Image', 'staff-profile-card' ),
            'tab'       => Controls_Manager::TAB_STYLE,
            'condition' => [
                'show_image' => 'yes',
            ],
        ] );

        $this->add_responsive_control( 'image_width', [
            'label'      => esc_html__( 'Width', 'staff-profile-card' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [
                'px' => [
                    'min' => 40,
                    'max' => 200,
                ],
            ],
            'default'    => [
                'size' => 80,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .spc-card__image' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->add_responsive_control( 'image_height', [
            'label'      => esc_html__( 'Height', 'staff-profile-card' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [
                'px' => [
                    'min' => 40,
                    'max' => 200,
                ],
            ],
            'default'    => [
                'size' => 90,
                'unit' => 'px',
            ],
            'selectors'  => [
                '{{WRAPPER}} .spc-card__image' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ] );

        $this->end_controls_section();
    }

    /* -------------------------------------------------------------- */
    /*  Render — frontend                                             */
    /* -------------------------------------------------------------- */

    protected function render() {
        $settings   = $this->get_settings_for_display();
        $staff_list = ! empty( $settings['staff_list'] ) ? $settings['staff_list'] : [];

        ?>
        <div class="spc-directory">


            <?php if ( empty( $staff_list ) ) : ?>
                <div class="spc-directory__fallback">
                    <p class="spc-directory__fallback-message">
                        <?php esc_html_e( 'Add staff members using their API Profile IDs.', 'staff-profile-card' ); ?>
                    </p>
                </div>
            <?php else : ?>
                <div class="spc-directory__list">
                    <?php foreach ( $staff_list as $item ) :
                        $api_profile_id = ! empty( $item['api_profile_id'] )
                            ? strtoupper( trim( (string) $item['api_profile_id'] ) )
                            : '';

                        if ( ! preg_match( '/\ASPC-[A-Z0-9]{8}\z/', $api_profile_id ) ) {
                            $this->render_card_fallback( __( 'Enter a valid API Profile ID.', 'staff-profile-card' ) );
                            continue;
                        }

                        $data = spc_fetch_profile( $api_profile_id );

                        if ( is_wp_error( $data ) ) {
                            continue;
                        }

                        $name           = ! empty( $data['name'] ) ? $data['name'] : '';
                        $designation    = ! empty( $data['designation'] ) ? $data['designation'] : '';
                        $qualifications = ! empty( $data['qualifications'] ) ? $data['qualifications'] : '';
                        $image_url      = ! empty( $data['profile_image_link'] ) ? $data['profile_image_link'] : '';
                        $profile_url    = ! empty( $data['public_profile_url'] ) ? $data['public_profile_url'] : '';

                        if ( empty( $name ) ) {
                            continue;
                        }

                        $this->render_card( $settings, $name, $designation, $qualifications, $image_url, $profile_url );
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a single staff card.
     */
    private function render_card( $settings, $name, $designation, $qualifications, $image_url, $profile_url ) {
        ?>
        <div class="spc-card">
            <?php if ( 'yes' === $settings['show_image'] && ! empty( $image_url ) ) : ?>
                <div class="spc-card__image">
                    <img
                        src="<?php echo esc_url( $image_url ); ?>"
                        alt="<?php echo esc_attr( $name ); ?>"
                        loading="lazy"
                    />
                </div>
            <?php endif; ?>

            <div class="spc-card__body">
                <h3 class="spc-card__name">
                    <?php if ( ! empty( $profile_url ) ) : ?>
                        <a href="<?php echo esc_url( $profile_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $name ); ?></a>
                    <?php else : ?>
                        <?php echo esc_html( $name ); ?>
                    <?php endif; ?>
                </h3>

                <?php if ( 'yes' === $settings['show_designation'] && ! empty( $designation ) ) : ?>
                    <p class="spc-card__designation"><?php echo esc_html( $designation ); ?></p>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_qualifications'] && ! empty( $qualifications ) ) : ?>
                    <p class="spc-card__qualifications"><?php echo esc_html( $qualifications ); ?></p>
                <?php endif; ?>
            </div>

            <?php if ( 'yes' === $settings['show_view_profile'] && ! empty( $profile_url ) ) : ?>
                <div class="spc-card__actions">
                    <a class="spc-card__view-profile" href="<?php echo esc_url( $profile_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'View Profile', 'staff-profile-card' ); ?>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render a fallback card for errors.
     *
     * @param string $message Error message.
     */
    private function render_card_fallback( $message ) {
        ?>
        <div class="spc-card spc-card--fallback">
            <p class="spc-card__fallback-message"><?php echo esc_html( $message ); ?></p>
        </div>
        <?php
    }

    /* -------------------------------------------------------------- */
    /*  Content template — Elementor editor live preview               */
    /* -------------------------------------------------------------- */

    protected function content_template() {
        ?>
        <#
        var staffList = settings.staff_list || [];
        #>
        <div class="spc-directory">

            <# if ( staffList.length === 0 ) { #>
                <div class="spc-directory__fallback">
                    <p class="spc-directory__fallback-message">
                        <?php echo esc_html__( 'Add staff members using their API Profile IDs.', 'staff-profile-card' ); ?>
                    </p>
                </div>
            <# } else {
                // Gather valid IDs for a single batch request.
                var validIds = [];
                var idPattern = /^SPC-[A-Z0-9]{8}$/;
                _.each( staffList, function( item ) {
                    var pid = item.api_profile_id ? String( item.api_profile_id ).trim().toUpperCase() : '';
                    if ( idPattern.test( pid ) ) {
                        validIds.push( pid );
                    }
                } );

                var containerId = 'spc-preview-list-' + view.getID();
            #>
                <div id="{{{ containerId }}}" class="spc-directory__list">
                    <# _.each( staffList, function( item ) {
                        var pid = item.api_profile_id ? String( item.api_profile_id ).trim().toUpperCase() : '';
                        if ( ! idPattern.test( pid ) ) {
                    #>
                        <div class="spc-card spc-card--fallback">
                            <p class="spc-card__fallback-message">
                                <?php echo esc_html__( 'Enter a valid API Profile ID.', 'staff-profile-card' ); ?>
                            </p>
                        </div>
                    <#  } else { #>
                        <div class="spc-card" data-spc-id="{{{ pid }}}">
                            <p class="spc-card__fallback-message">
                                <?php echo esc_html__( 'Loading…', 'staff-profile-card' ); ?>
                            </p>
                        </div>
                    <#  }
                    } ); #>
                </div>
            <#
                // Capture settings as JS variables BEFORE the async fetch.
                var imgOn   = ( settings.show_image === 'yes' );
                var desigOn = ( settings.show_designation === 'yes' );
                var qualsOn = ( settings.show_qualifications === 'yes' );
                var linkOn  = ( settings.show_view_profile === 'yes' );

                // Fetch all valid profiles in one batch request.
                if ( validIds.length > 0 ) {
                    ( function( imgOn, desigOn, qualsOn, linkOn ) {
                        var container = document.getElementById( containerId );
                        if ( ! container ) return;

                        var url = '<?php echo esc_url( rest_url( 'staff-profile-card/v1/preview-multi' ) ); ?>'
                            + '?ids=' + encodeURIComponent( validIds.join( ',' ) )
                            + '&_wpnonce=<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>';

                        function esc( v ) {
                            var d = document.createElement( 'div' );
                            d.textContent = v == null ? '' : String( v );
                            return d.innerHTML;
                        }
                        function escAttr( v ) {
                            return esc( v ).replace( /"/g, '&quot;' );
                        }

                        fetch( url, { credentials: 'same-origin' } )
                            .then( function( res ) { return res.json(); } )
                            .then( function( profiles ) {
                                if ( ! Array.isArray( profiles ) ) return;

                                // Build a lookup by profile_id.
                                var lookup = {};
                                profiles.forEach( function( p ) {
                                    if ( p.profile_id ) lookup[ p.profile_id ] = p;
                                } );

                                // Update each card placeholder.
                                var cards = container.querySelectorAll( '.spc-card[data-spc-id]' );
                                cards.forEach( function( card ) {
                                    var pid  = card.getAttribute( 'data-spc-id' );
                                    var data = lookup[ pid ];

                                    if ( ! data || data.error || ! data.name ) {
                                        if ( card.parentNode ) {
                                            card.parentNode.removeChild( card );
                                        }
                                        return;
                                    }

                                    var html = '';

                                    if ( imgOn && data.profile_image_link ) {
                                        html += '<div class="spc-card__image">';
                                        html += '<img src="' + escAttr( data.profile_image_link ) + '" alt="' + escAttr( data.name ) + '" />';
                                        html += '</div>';
                                    }

                                    html += '<div class="spc-card__body">';
                                    html += '<h3 class="spc-card__name">';
                                    if ( data.public_profile_url ) {
                                        html += '<a href="' + escAttr( data.public_profile_url ) + '" target="_blank" rel="noopener noreferrer">' + esc( data.name ) + '</a>';
                                    } else {
                                        html += esc( data.name );
                                    }
                                    html += '</h3>';

                                    if ( desigOn && data.designation ) {
                                        html += '<p class="spc-card__designation">' + esc( data.designation ) + '</p>';
                                    }

                                    if ( qualsOn && data.qualifications ) {
                                        html += '<p class="spc-card__qualifications">' + esc( data.qualifications ) + '</p>';
                                    }
                                    html += '</div>';

                                    if ( linkOn && data.public_profile_url ) {
                                        html += '<div class="spc-card__actions">';
                                        html += '<a class="spc-card__view-profile" href="' + escAttr( data.public_profile_url ) + '" target="_blank" rel="noopener noreferrer">';
                                        html += '<?php echo esc_js( __( 'View Profile', 'staff-profile-card' ) ); ?>';
                                        html += ' <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" width="14" height="14"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>';
                                        html += '</a>';
                                        html += '</div>';
                                    }

                                    card.innerHTML = html;
                                } );
                            } )
                            .catch( function() {
                                var cards = container.querySelectorAll( '.spc-card[data-spc-id]' );
                                cards.forEach( function( card ) {
                                    card.classList.add( 'spc-card--fallback' );
                                    card.innerHTML = '<p class="spc-card__fallback-message">' +
                                        '<?php echo esc_js( __( 'Could not load profile.', 'staff-profile-card' ) ); ?>' +
                                        '</p>';
                                } );
                            } );
                    } )( imgOn, desigOn, qualsOn, linkOn );
                }
            #>
            <# } #>
        </div>
        <?php
    }
}
