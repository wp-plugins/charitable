<?php
/** 
 * Class used to provide information about the current request.
 *  
 * @version		1.0.0
 * @package		Charitable/Classes/Charitable_Request
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'Charitable_Request' ) ) :

/** 
 * Charitable_Request. 
 *  
 * @since		1.0.0
 * @final
 */
final class Charitable_Request extends Charitable_Start_Object {

	/**
	 * @var 	Charitable_Campaign
	 * @access 	private
	 */
	private $campaign;

	/**
	 * @var 	Charitable_Donor
	 * @access 	private
	 */
	private $donor;

	/**
	 * @var 	Charitable_Donation
	 * @access 	private
	 */
	private $donation; 

	/**
	 * Set up the class. 
	 * 
	 * Note that the only way to instantiate an object is with the on_start method, 
	 * which can only be called during the start phase. In other words, don't try 
	 * to instantiate this object. 
	 *
	 * @param 	Charitable 		$charitable
	 * @access 	protected
	 * @since 	1.0.0
	 */
	protected function __construct() {	
		add_action( 'the_post', array( $this, 'set_current_campaign' ) );
	}

	/**
	 * When the_post is set, sets the current campaign to the current post if it is a campaign.
	 *
	 * @param 	array 		$args
	 * @return 	void
	 * @access  public
	 * @since 	1.0.0
	 */
	public function set_current_campaign( $post ) {
		if ( 'campaign' == $post->post_type ) {

			$this->campaign = new Charitable_Campaign( $post );

		}
		else {

			unset( $this->campaign, $this->campaign_id );

		}
	}

	/** 
	 * Returns the current campaign. If there is no current campaign, return false. 
	 *
	 * @return 	Charitable_Campaign if we're viewing a campaign within a loop. False otherwise. 
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function get_current_campaign() {
		if ( ! isset( $this->campaign ) ) {

			if ( $this->get_current_campaign_id() > 0 ) {

				$this->campaign = new Charitable_Campaign( $this->get_current_campaign_id() );

			}
			else {

				$this->campaign = false;

			}
		}

		return $this->campaign;
	}

	/**
	 * Returns the current campaign ID. If there is no current campaign, return 0. 
	 *
	 * @return 	int
	 * @access  public
	 * @since 	1.0.0
	 */
	public function get_current_campaign_id() {
		if ( isset( $this->campaign ) && $this->campaign ) {

			$this->campaign_id = $this->campaign->ID;

		}
		else {

			$this->campaign_id = 0;

			if ( get_post_type() == Charitable::CAMPAIGN_POST_TYPE ) {

				$this->campaign_id = get_the_ID();

			}
			elseif ( get_query_var( 'donate', false ) ) {

				$session_donation = charitable_get_session()->get( 'donation' );

				if ( false !== $session_donation ) {

					$this->campaign_id = $session_donation->get( 'campaign_id' );

				}
			}
		}

		if ( ! $this->campaign_id ) {

			$this->campaign_id = $this->get_campaign_id_from_submission();

		}

		return $this->campaign_id;
	}

	/**
	 * Returns the campaign ID from a form submission.  
	 *
	 * @return  int
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_campaign_id_from_submission() {
		if ( ! isset( $_POST['campaign_id'] ) ) {
            return 0;
        }

        $campaign_id = absint( $_POST['campaign_id'] );

        if ( Charitable::CAMPAIGN_POST_TYPE !== get_post_type( $campaign_id ) ) {
            return 0;
        } 

        return $campaign_id;
	}

	/**
	 * Returns the current donation object. If there is no current donation, return false. 
	 *
	 * @return  Charitable_Donation|false
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_current_donation() {
		if ( ! isset( $this->donation ) ) {

			$donation_id = $this->get_current_donation_id();
			$this->donation = $donation_id ? new Charitable_Donation( $donation_id ) : false;

		}
		
		return $this->donation;
	}

	/**
	 * Returns the current donation ID. If there is no current donation, return 0. 
	 *
	 * @return  int
	 * @access  public
	 * @since   1.0.0
	 */
	public function get_current_donation_id() {
		return get_query_var( 'donation_id', 0 );
	}
}

endif; // End class_exists check 