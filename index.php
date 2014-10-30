<?php
/**
 * Plugin Name: Woocommerce Payment Gateway per Product
 * Plugin URI: www.dreamfoxmedia.nl 
 * Version: 1.1.7
 * Author: Marco van Loghum
 * Author URI: www.dreamfoxmedia.nl 
 * Description: Extend Woocommerce plugin to add payments methods to a product
 * Requires at least: 3.7
 * Tested up to: 4.0
 */
//require_once ABSPATH . WPINC . '/pluggable.php';;
//require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-payment-gateways.php';
//require_once dirname(dirname(__FILE__)).'/woocommerce/classes/class-wc-cart.php';

add_action( 'add_meta_boxes', 'wpp_meta_box_add' );  
function wpp_meta_box_add()  
{  
    add_meta_box( 'payments', 'Payments', 'wpp_payments_form', 'product', 'side', 'core' ); 
}

function wpp_payments_form()  
{
	global $post, $woo;
	
	$productIds = get_option('woocommerce_product_apply');
	if( is_array( $productIds ) ){
		foreach($productIds as $key=>$product){
			if(!get_post($product) || !count(get_post_meta($product, 'payments', true)) ){
				unset($productIds[$key]);
			}
		}
	}
	update_option('woocommerce_product_apply', $productIds);
	
	$postPayments = count ( get_post_meta($post->ID, 'payments', true) ) ? get_post_meta($post->ID, 'payments', true) : array() ;
	if( count( $productIds )>=10 && !count( $postPayments ) ){
		echo 'Limit reached Please download full version package at www.dreamfox.nl!';
		return;
	}
	
	$woo = new WC_Payment_Gateways();
	$payments = $woo->payment_gateways;
	foreach($payments as $pay){
		$checked = '';
		if( is_array( $postPayments ) && in_array($pay->id, $postPayments)) $checked = ' checked="yes" '; ?>  
		<input type="checkbox" <?php echo $checked; ?> value="<?php echo $pay->id; ?>" name="pays[]" id="payment_<?php echo $pay->id; ?>" />
		<label for="payment_<?php echo $pay->id; ?>"><?php echo $pay->title; ?></label>  
		<br />  
		<?php
	}      
} 

add_action('save_post', 'wpp_meta_box_save', 10, 2 );
function wpp_meta_box_save( $post_id, $post )  
{   
	// Restrict to save for autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	 	return $post_id;

	// Restrict to save for revisions
   	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
      return $post_id;
		
	if($_POST['post_type']=='product'){
	
		$productIds = get_option('woocommerce_product_apply');
		if( !in_array( $post_id, $productIds ) && count( $productIds ) <= 10 ){
			$productIds[] = $post_id;
			update_option('woocommerce_product_apply', $productIds);
		}
	
		//delete_post_meta($post_id, 'payments');	 
		$payments = array();
		if($_POST['pays']){
			foreach($_POST['pays'] as $pay)
				$payments[] = $pay;
		}		
		update_post_meta($post_id, 'payments', $payments); 
	}
}

function wpppayment_gateway_disable_country( $available_gateways ) {
	global $woocommerce;
	$arrayKeys = array_keys($available_gateways);
	if( count(  $woocommerce->cart ) ){
		$items = $woocommerce->cart->cart_contents;
		$itemsPays = '';
		if(is_array($items)){
			foreach($items as $item){
				$itemsPays = get_post_meta($item['product_id'], 'payments', true) ;
				if( is_array( $itemsPays ) && count($itemsPays) ){
					foreach( $arrayKeys as $key ){
                                        if(array_key_exists( $key, $available_gateways ) && !in_array( $available_gateways[$key]->id ,$itemsPays) ){
							unset($available_gateways[$key]);
					    }
					}
				}
			}
		}
	}
	return $available_gateways;
}
add_filter( 'woocommerce_available_payment_gateways', 'wpppayment_gateway_disable_country' );
?>