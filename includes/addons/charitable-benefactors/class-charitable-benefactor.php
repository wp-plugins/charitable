<?php
/**
 * The model for Benefactor relationships between Charitable campaigns and products in 3rd party extensions (EDD, WooCommerce, etc).
 *
 * @package		Charitable/Classes/Charitable_Benefactor
 * @version 	1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2014, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Charitable_Benefactor' ) ) : 

/**
 * Charitable_Benefactor
 *
 * @since 		1.0.0
 */
abstract class Charitable_Benefactor {

	/**
	 * Core benefactor record. 
	 *
	 * @var 	Object
	 * @access  protected
	 */
	protected $benefactor;

	/**
	 * Create class object.
	 * 
	 * @param 	mixed 	$benefactor
	 * @access 	public
	 * @since	1.0.0
	 */
	public function __construct( $benefactor ) {
		if ( ! is_object( $benefactor ) ) {
			$this->benefactor = charitable_get_table( 'benefactors' )->get( $benefactor );
		}
	}

	/**
	 * Return an object of type Charitable_Benefactor, given a benefactor record and an extension.	
	 *
	 * @param 	Object 	$benefactor
	 * @param 	string  $extension
	 * @return  Charitable_Benefactor
	 * @access  public
	 * @static
	 * @since   1.0.0
	 */
	public function get_object( $benefactor, $extension ) {
		$class = apply_filters( 'charitable_benefactor_class_' . $extension, false );

		if ( ! class_exists( $class ) ) {
			_doing_it_wrong( __METHOD__, __( 'Benefactor class does not exist for given extension.', '1.0.0' ) );
		}

		return new $class( $benefactor );
	}

	/**
	 * Display a short one-line summary of a benefactor (how much is contributed and from where).	
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function __toString() {
		$summary = apply_filters( 'charitable_benefactor_summary', sprintf( "%s %s.", $this->get_contribution_amount(), $this->get_contribution_type() ), $this );
		return $summary;
	}

	/**
	 * Magic getter method. 
	 *
	 * @param 	$key
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function __get( $key ) {
		return isset( $this->benefactor->$key ) ? $this->benefactor->$key : null;
	}

	/**
	 * Return the details of the benefactor (i.e. the 3rd party extension). 
	 *
	 * @return 	Object
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_benefactor() {
		return $this->benefactor;
	}

	/**
	 * Return the contribution as a nicely formatted amount. 
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_contribution_amount() {
		if ( $this->benefactor->contribution_amount_is_percentage ) {
			$amount = apply_filters( 'charitable_benefactor_contribution_amount_percentage', $this->benefactor->contribution_amount . '%', $this->benefactor->contribution_amount, $this );
		}
		else {
			$amount = apply_filters( 'charitable_benefactor_contribution_amount_fixed', charitable()->get_currency_helper()->get_monetary_amount( $this->benefactor->contribution_amount ), $this->benefactor->contribution_amount, $this );
		}

		return $amount;
	}

	/**
	 * Return the type of the contribution. Either per purchase or per  
	 *
	 * @return 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_contribution_type() {
		$type = $this->benefactor->contribution_amount_is_per_item ? __( 'per item', 'charitable' ) : __( 'per purchase', 'charitable' );
		return apply_filters( 'charitable_benefactor_contribution_type', $type, $this->benefactor->contribution_amount_is_per_item, $this );
	}

    /**
     * Return the benefit amount of a product based on the price, quantity and percent going to benefit. 
     *
     * @param   float 	$price
     * @param   int   	$quantity
     * @return  float
     * @access  protected
     * @since   1.0.0
     */
    protected function calculate_line_item_percent_contribution( $price, $quantity ) {
        return $price * $quantity * ( $this->benefactor->contribution_amount / 100 );
    }

    /**
     * Return the benefit amount of a product based on the quantity and fixed amount going per item.
     *
     * @param   int   	$quantity
     * @return  float
     * @access  protected
     * @since   1.0.0
     */
    protected function calculate_line_item_fixed_contribution( $quantity = 1 ) {
        return $quantity * $this->benefactor->contribution_amount;
    }    

    /**
     * Returns whether the benefactor's benefit is applied once per cart.
     *
     * False means that more benefit is added for every applicable item in the cart.
     *
     * @return  boolean
     * @access  protected
     * @since   1.0.0
     */
    protected function benefit_is_per_cart() {
    	return false === ( $this->benefactor->contribution_amount_is_per_item || $this->benefactor->contribution_amount_is_percentage );
    }	
}

endif; // End class_exists check