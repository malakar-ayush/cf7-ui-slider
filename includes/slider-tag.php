<?php
if ( ! class_exists( 'SLIDER_UI_TAG' ) ) {

	class SLIDER_UI_TAG {
		private static $instance;

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_ui_scripts_types' ) );
			add_action( 'wpcf7_init', array( $this, 'add_ui_slider' ) );
			add_action( 'wpcf7_admin_init', array( $this, 'wpcf7_add_tag_generator_slider' ), 99 );

		}

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		public function enqueue_ui_scripts_types() {
			/*To ensure that only a single of these are loaded*/
			if ( ! wp_script_is( 'jquery-touch-punch', 'enqueued ' ) ) {
				wp_enqueue_script( 'jquery-touch-punch' );
			}
			$handle = 'jquery-ui-slider'; // checks if jquery ui slider has been enqueded or not
			if ( ! wp_script_is( $handle ) ) {
				wp_enqueue_script( 'jquery-ui-slider' );
			}
			if ( ! wp_script_is( 'jquery-ui-css', 'registered ' ) ) {
				wp_register_style( 'jquery-ui-css', plugins_url( '../css/jquery-ui.css', __FILE__ ) );
			}
			if ( ! wp_script_is( 'ui-slider-style-one', 'registered ' ) ) {
				wp_register_style( 'ui-slider-style-one', plugins_url( '../css/style-one.css', __FILE__ ) );
			}
			if ( ! wp_script_is( 'jquery-ui-css', 'enqueued ' ) ) {
				wp_enqueue_style( 'jquery-ui-css' );
			}
			if ( ! wp_script_is( 'ui-slider-style-one', 'enqueued ' ) ) {
				wp_enqueue_style( 'ui-slider-style-one' );
			}
		}

		public function get_cf7_ui_slider( $tag ) {
			$tag                = new WPCF7_FormTag ( $tag );
			$class              = wpcf7_form_controls_class( $tag->type, 'wpcf7-text' );
			$atts               = array();
			$atts['min']        = $tag->get_option( 'min', '', true );
			$atts['ui_default'] = $tag->get_option( 'ui_default', '', true );
			$atts['max']        = $tag->get_option( 'max', '', true );
			$atts['class']      = $tag->get_class_option( $class );
			$atts['id']         = $tag->get_id_option();
			$atts['type']       = 'hidden';
			$atts['connect']    = $tag->get_option( 'connect', 'class', true );
			$atts['prefix']     = $tag->get_option( 'prefix', '', true );
			$atts['step']       = $tag->get_option( 'step', '', true );
			//$value             = (string) reset( $tag->values );
			/*atts for hidden field */
			$hidden_field_atts          = array();
			$hidden_field_atts['type']  = 'hidden';
			$hidden_field_atts['value'] = ! empty( $atts['ui_default'] ) ? $atts['prefix'] . $atts['ui_default'] : $atts['prefix'] . $atts['min'];
			$hidden_field_atts['name']  = $tag->name;
			$hidden_field_atts['id']    = $atts['id'] . '-single-slider';


			ob_start();
			$default_value = ! empty( $atts['ui_default'] ) ? $atts['ui_default'] : $atts['min'];
			$connect       = ! empty( $atts['connect'] ) ? $atts['connect'] : $hidden_field_atts['id'];

			?>


            <div id="<?php echo $atts['id']; ?>"></div>
            <script>
                jQuery(function ($) {
                    $(window).ready(function () {
						<?php if ( ! empty( $atts['connect'] ) ): ?>
                        $("#<?php echo $atts['connect'] ?>").css('display', 'none');
						<?php endif; ?>
                        var tooltip = $('<div class="slider-tooltip" />').css({
                            position: 'absolute'
                        });

                        tooltip.text('<?php echo $atts['prefix'] ?><?php  echo $default_value ?>');

                        $("#<?php echo $atts['id']; ?>").slider({
                            range: "min",
                            min: <?php echo $atts['min']; ?>,
                            max: <?php echo $atts['max'];?>,
							<?php  echo( $atts['step'] > 0 ? "step:" . $atts['step'] . "," : '' ); ?>
                            value: <?php    echo $default_value ?>,
                            slide: function (event, ui) {
                                $("#<?php echo $connect ?>").val('<?php echo $atts['prefix'] ?>' + ui.value);
                                tooltip.text('<?php echo $atts['prefix'] ?>' + ui.value);
                            },
                            change: function (event, ui) {
                            }
                        }).find(".ui-slider-handle").append(tooltip);
//                        $("#<?php //echo $connect  ?>//").val(<?php //echo $default_value ?>//);
                    });
                });
            </script>

			<?php
			if ( empty( $atts['connect'] ) ) {
				$hidden_field_atts = wpcf7_format_atts( $hidden_field_atts );
				echo sprintf( '<input %s />', $hidden_field_atts );
			}

			return ob_get_clean();


		}


		public function add_ui_slider() {
			wpcf7_add_form_tag( 'cf7_ui_slider', array( $this, 'get_cf7_ui_slider' ), array(
				'name-attr' => true,

			) );
		}

		function wpcf7_add_tag_generator_slider() {

			$tag_generator = WPCF7_TagGenerator::get_instance();
			$tag_generator->add( 'cf7_ui_slider', __( 'UI Slider', 'contact-form-7' ), array(
				$this,
				'wpcf7_tag_generator_slider',
			) );


		}

		function wpcf7_tag_generator_slider( $contact_form, $args = '' ) {
			$args = wp_parse_args( $args, array() );
			$type = $args['id'];

			if ( ! in_array( $type, array( 'min', 'max', 'connect_id', 'ui_default' ) ) ) {
				$type = 'cf7_ui_slider';
			}
			$description = __( "When using default value, please put the value in between the max and min range in order to avoid any unwanted issues ", 'contact-form-7' );
			$desc_link   = '';
			?>
            <div class="control-box">
                <fieldset>
                    <legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="name" class="tg-name oneline"
                                       id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="id" class="idvalue oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-id' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-min' ); ?>"><?php echo esc_html( __( 'Minimum Value', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="min" class=" oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-min' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-ui_default' ); ?>"><?php echo esc_html( __( 'Default Value', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="ui_default" class=" oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-ui_default' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-max' ); ?>"><?php echo esc_html( __( 'Maxium Value', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="max" class=" oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-max' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-prefix' ); ?>"><?php echo esc_html( __( 'Add Prefix ', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="prefix" class=" oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-prefix' ); ?>"/></td>
                        </tr>
                        <tr>
                            <th scope="row"><label
                                        for="<?php echo esc_attr( $args['content'] . '-step' ); ?>"><?php echo esc_html( __( 'Add step increment ', 'contact-form-7' ) ); ?></label>
                            </th>
                            <td><input type="text" name="step" class="oneline option"
                                       id="<?php echo esc_attr( $args['content'] . '-step' ); ?>"/></td>
                        </tr>


                        </tbody>
                    </table>
                </fieldset>
            </div>

            <div class="insert-box">
                <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly"
                       onfocus="this.select()"/>

                <div class="submitbox">
                    <input type="button" class="button button-primary insert-tag"
                           value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>"/>
                </div>

                <br class="clear"/>

                <p class="description mail-tag"><label
                            for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?>
                        <input type="text" class="mail-tag code hidden" readonly="readonly"
                               id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/></label></p>
            </div>
			<?php
		}

	}

}

