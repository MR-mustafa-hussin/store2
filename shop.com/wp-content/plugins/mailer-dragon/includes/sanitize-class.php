<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages scheduled_disc settings
 *
 * Here all scheduled_disc settings are defined and managed.
 *
 * @version		1.0.0
 * @package		mailer-dragon/includes
 * @author 		Norbert Dreszer
 */
class ic_mailer_sanitize {

	public function sanitize( $filter, $number = false ) {
		if ( is_array( $filter ) ) {
			if ( $number ) {
				$sanitized = $this->number( $filter );
			} else {
				$sanitized = $this->text( $filter );
			}
			if ( !empty( $sanitized ) ) {
				return array_filter( $sanitized );
			}
		}
		return '';
	}

	public function number( $number ) {
		if ( is_array( $number ) ) {
			return array_filter( array_map( array( $this, 'number' ), $number ) );
		} else {
			$number = intval( $number );
			if ( !empty( $number ) ) {
				return $number;
			}
		}
		return '';
	}

	public function text( $text ) {
		if ( is_array( $text ) ) {
			return array_filter( array_map( array( $this, 'text' ), $text ) );
		} else {
			return sanitize_text_field( $text );
		}
		return '';
	}

	public function text_number( $input ) {
		if ( is_array( $input ) ) {
			return array_map( array( $this, 'text_number' ), $input );
		} else {
			if ( is_int( $input ) ) {
				return intval( $input );
			} else {
				return sanitize_text_field( $input );
			}
		}
	}

}
