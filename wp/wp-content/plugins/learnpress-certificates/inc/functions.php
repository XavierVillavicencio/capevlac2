<?php
/**
 * @param string $template_name
 * @param array  $args
 * @deprecated 4.0.4
 */
function learn_press_certificate_get_template( $template_name, $args = array() ) {
	_deprecated_function( __FUNCTION__, '4.0.4' );
	learn_press_get_template( $template_name, $args, learn_press_template_path() . DIRECTORY_SEPARATOR . 'addons' . DIRECTORY_SEPARATOR . 'certificates' . DIRECTORY_SEPARATOR, LP_ADDON_CERTIFICATES_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR );
}

/**
 * @param string $template_name
 * @param array  $args
 *
 * @return string
 * @deprecated 4.0.4
 */
function learn_press_certificate_locate_template( $template_name ) {
	_deprecated_function( __FUNCTION__, '4.0.4' );
	return learn_press_locate_template( $template_name, learn_press_template_path() . '/addons/certificates/', LP_ADDON_CERTIFICATES_PATH . '/templates/' );
}

/**
 * @deprecated 4.0.4
 */
function learn_press_certificates_button_download( $certificate ) {
	_deprecated_function( __FUNCTION__, '4.0.4' );
	learn_press_certificate_get_template( 'buttons/download.php', array( 'certificate' => $certificate ) );
}

/**
 * @param LP_User_Certificate $certificate
 */
add_action( 'learn-press/certificates/after-certificate-content', 'learn_press_certificates_buttons', 10 );
function learn_press_certificates_buttons( $certificate ) {
	$twitter  = LearnPress::instance()->settings()->get( 'certificates.socials_twitter' );
	$facebook = LearnPress::instance()->settings()->get( 'certificates.socials_facebook' );
	$socials  = array();

	if ( $twitter || $facebook ) {
		if ( $facebook === 'yes' ) {
			$link      = 'https://www.facebook.com/sharer/sharer.php?u=';
			$socials[] = sprintf(
				'<a href="%s" class="social-fb-svg social-cert" target="_blank"><img src="%s" alt="share-certificate-facebook"></a>',
				$link,
				LP_Addon_Certificates_Preload::$addon->get_plugin_url( 'assets/images/facebook.svg' )
			);
		}

		if ( $twitter === 'yes' ) {
			$link      = 'https://twitter.com/intent/tweet?text=';
			$socials[] = sprintf(
				'<a href="%s" class="social-twitter-svg social-cert" target="_blank"><img src="%s" alt="share-certificate-twitter"></a>',
				$link,
				LP_Addon_Certificates_Preload::$addon->get_plugin_url( 'assets/images/twitter.svg' )
			);
		}
	}
	$socials = apply_filters( 'learn-press/certificates/socials-share', $socials, $certificate );
	LP_Addon_Certificates_Preload::$addon->get_template(
		'buttons-action.php',
		compact( 'socials', 'certificate' )
	);
}

if ( ! function_exists( 'learn_press_certificate_buy_button' ) ) {
	function learn_press_certificate_buy_button( $course ) {
		$course_id = $course->get_id();

		if ( $course_id ) {
			$lp_woo_payment_enable = 'no';

			if ( is_plugin_active( 'learnpress-woo-payment/learnpress-woo-payment.php' ) && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				$lp_woo_payment_enable = LearnPress::instance()->settings()->get( 'woo-payment.enable', 'no' );
			}

			if ( class_exists( 'WooCommerce' ) && $lp_woo_payment_enable == 'yes' ) {
				$wc_cart = WC()->cart;
				if ( ! $wc_cart ) {
					include_once WC_ABSPATH . 'includes/wc-cart-functions.php';
					include_once WC_ABSPATH . 'includes/class-wc-cart.php';
					wc_load_cart();
					$wc_cart = WC()->cart;
				}

				$cert_id_assign_of_course = get_post_meta( $course_id, '_lp_cert', true );
				$flag_found               = false;

				// Check certificate added to cart
				foreach ( $wc_cart->get_cart() as $cart_item ) {
					if ( isset( $cart_item['lp_cert_id'] )
						&& $cart_item['lp_cert_id'] == $cert_id_assign_of_course
						&& $cart_item['course_id'] == $course_id ) {
						$flag_found = true;
					}
				}

				if ( $flag_found ) {
					echo '<a class="btn-lp-cert-view-cart" href="' . wc_get_cart_url() . '"><button class="lp-button">' . __( 'View cart certificate', 'learnpress-certificates' ) . '</button></a>';
				} else {
					LP_Addon_Certificates_Preload::$addon->get_template( 'button-woo-certificate-add-to-cart.php', compact( 'course' ) );
				}
			} else {
				LP_Addon_Certificates_Preload::$addon->get_template( 'button-purchase-certificate.php', compact( 'course' ) );
			}
		}
	}
}
