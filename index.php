<?php
/**
 * Plugin Name: Woocommerce Payment Gateway per Product
 * Plugin URI: http://www.dreamfox.nl
 * Description: This plugin for woocommerce lets you select the available payment gateways for each individual product.
 * Version: 1.0.1
 * Author: Marco van Loghum Slaterus
 * Author URI: http://www.dreamfox.nl
 * Requires at least: 3.0
 * Tested up to: 3.5
 *
 * Text Domain: woocommerce
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce
 * @category Core
 * @author WooThemes
 */
require_once ABSPATH . WPINC . '/pluggable.php';;
require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-payment-gateways.php';
require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-cart.php';



add_action( 'add_meta_boxes', 'cd_meta_box_add' );  
function cd_meta_box_add()  
{  
    add_meta_box( 'payments', 'Payments', 'payments_form', 'product', 'side', 'core' ); 
}

function payments_form()  
{
	global $post, $woo;
	$postPayments = get_metadata('post', $post->ID, 'payments', false) ;
	$woo = new WC_Payment_Gateways();
	$payments = $woo->get_available_payment_gateways();
	foreach($payments as $pay){
	$checked = '';
	if(in_array($pay->id, $postPayments)) $checked = ' checked="yes" ';
    ?>  
    <input type="checkbox" <?php echo $checked; ?> value="<?php echo $pay->id; ?>" name="pays[]" id="payments" />
    <label for="my_meta_box_text"><?php echo $pay->title; ?></label>  
    <br />  
    <?php }      
} 

add_action('save_post', 'cd_meta_box_save', 10, 2 );
function cd_meta_box_save( $post_id )  
{   
	delete_post_meta($post_id, 'payments');	 
	if($_POST['pays'])
		foreach($_POST['pays'] as $pay)
    		add_post_meta($post_id, 'payments', $pay); 
}



function payment_gateway_disable_country( $available_gateways ) {
	global $woocommerce;
	$arrayKeys = array_keys($available_gateways);
	$items = $woocommerce->cart->cart_contents;
	$itemsPays = '';
	if($items)
		foreach($items as $item)
		$itemsPays[] = get_metadata('post', $item['product_id'], 'payments', false) ;
	if($itemsPays)
		foreach($itemsPays as $objs)
		if(count($objs))
			foreach($arrayKeys as $key)
				if(!in_array($key, $objs))
					unset($available_gateways[$key]);					
	return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'payment_gateway_disable_country' );





