<?php

	class mod_time extends command_base
	{
		function __construct()
		{
			$this->year  = date( 'Y' );
			$this->month = date( 'n' );
			$this->day   = date( 'j' );
			$this->hour  = date( 'G' );
			$this->min   = date( 'i' );
			$this->sec   = date( 's' );
		}

		function date( &$_gm , $_rec , $_args )
		{
			$format = array_shift( $_args );
			$format = str_replace( '&CODE001;' , ' ' , $format );

			foreach( $_args as $method )
				$this->{$method}( $_gm , $_rec , $_args );
			
			$this->addBuffer( date( $format , $this->getTime() ) );
		}

		function format( &$_gm , $_rec , $_args )
		{
			$format = array_shift( $_args );
			$format = str_replace( '&CODE001;' , ' ' , $format );

			$this->addBuffer( date( $format , $_args[ 0 ] ) );
		}

		function timestamp( &$_gm , $_rec , $_args )
		{
			foreach( $_args as $method )
				$this->{$method}( $_gm , $_rec , $_args );

			$this->addBuffer( $this->getTime() );
		}

		function thisYear( &$_gm , $_rec , $_args )
		{
			$this->year = date( 'Y' );
			return $this->year;
		}

		function thisMonth( &$_gm , $_rec , $_args )
		{
			$this->month = date( 'n' );
			return $this->month;
		}

		function thisDay( &$_gm , $_rec , $_args )
		{
			$this->day = date( 'j' );
			return $this->day;
		}

		function nextYear( &$_gm , $_rec , $_args )
		{
			$this->year++;
			return $this->year;
		}

		function nextMonth( &$_gm , $_rec , $_args )
		{
			$this->month++;
			return $this->month;
		}

		function nextDay( &$_gm , $_rec , $_args )
		{
			$this->day++;
			return $this->day;
		}

		function previousYear( &$_gm , $_rec , $_args )
		{
			$this->year--;
			return $this->year;
		}

		function previousMonth( &$_gm , $_rec , $_args )
		{
			$this->month--;
			return $this->month;
		}

		function previousDay( &$_gm , $_rec , $_args )
		{
			$this->day--;
			return $this->day;
		}

		function firstDay( &$_gm , $_rec , $_args )
		{
			$this->day = 1;
			return 1;
		}

		function lastDay( &$_gm , $_rec , $_args )
		{
			$time = mktime( 0 , 0 , 0 , $this->month + 1 , 1 , $this->year ) - 1;
			$this->day = date( 'j' , $time );
			return $this->day;
		}

		private function getTime()
		{
			return mktime( $this->hour , $this->min , $this->sec , $this->month , $this->day , $this->year );
		}
	}

?>