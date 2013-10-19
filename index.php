<?php
/**
 * Plugin Name: Woocommerce Product Payments
 * Plugin URI: www.dreamfox.nl 
 * Version: 1.0.4
 * Author: Marco van Loghum
 * Author URI: www.dreamfox.nl 
 * Description: Extend Woocommerce plugin to add payments methods to a product
 * Requires at least: 3.7
 * Tested up to: 3.5
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
	
	$productIds = get_option('woocommerce_product_apply');
	
	if($productIds)
		foreach($productIds as $key=>$product)
			if(!wp_get_single_post($product))
				unset($productIds[$key]);
	update_option('woocommerce_product_apply', $productIds);
	
	if(count($productIds)>=10&&!in_array($post->ID, $productIds)){
		echo 'Limit reached Please download full version package at www.dreamfox.nl!';
		return;
	}
	
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
	if($_POST['post_type']=='product'){
	
	
	$productIds = get_option('woocommerce_product_apply');
	if(!in_array($post_id, $productIds)&&count($productIds)<=10){
		$productIds[] = $post_id;
		update_option('woocommerce_product_apply', $productIds);
	}

	delete_post_meta($post_id, 'payments');	 
	if($_POST['pays'])
		foreach($_POST['pays'] as $pay)
    		add_post_meta($post_id, 'payments', $pay); 
			
	}
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
