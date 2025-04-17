<?php

	class Preset
	{
		static private $Label = Array
		(
			'index'          => 'TOP_PAGE_DESIGN' ,
			'head'           => 'HEAD_DESIGN' ,
			'foot'           => 'FOOT_DESIGN' ,
			'login'          => 'LOGIN_PAGE_DESIGN' ,
			'loginFaled'     => 'LOGIN_FALED_DESIGN' ,
			'activate'       => 'ACTIVATE_DESIGN_HTML' ,
			'activateFaled'  => 'ACTIVATE_FALED_DESIGN_HTML' ,
			'pageChange'     => 'SEARCH_PAGE_CHANGE_DESIGN' ,
			'registForm'     => 'REGIST_FORM_PAGE_DESIGN' ,
			'registCheck'    => 'REGIST_CHECK_PAGE_DESIGN' ,
			'registComp'     => 'REGIST_COMP_PAGE_DESIGN' ,
			'userRegistComp' => 'REGIST_COMP_PAGE_DESIGN' ,
			'registFaled'    => 'REGIST_ERROR_DESIGN' ,
			'editForm'       => 'EDIT_FORM_PAGE_DESIGN' ,
			'editCheck'      => 'EDIT_CHECK_PAGE_DESIGN' ,
			'editComp'       => 'EDIT_COMP_PAGE_DESIGN' ,
			'deleteCheck'    => 'DELETE_CHECK_PAGE_DESIGN' ,
			'deleteComp'     => 'DELETE_COMP_PAGE_DESIGN' ,
			'userDeleteComp' => 'DELETE_COMP_PAGE_DESIGN' ,
			'searchForm'     => 'SEARCH_FORM_PAGE_DESIGN' ,
			'searchResult'   => 'SEARCH_RESULT_DESIGN' ,
			'searchFaled'    => 'SEARCH_NOT_FOUND_DESIGN' ,
			'searchList'     => 'SEARCH_LIST_PAGE_DESIGN' ,
			'info'           => 'INFO_PAGE_DESIGN' ,
			'include'        => 'INCLUDE_DESIGN' ,
			'other'          => 'OTHER_PAGE_DESIGN'
		);

		static private $FileName = Array
		(
			'index'          => 'Index' ,
			'head'           => 'Head' ,
			'foot'           => 'Foot' ,
			'login'          => 'Login' ,
			'loginFaled'     => 'LoginFaled' ,
			'activate'       => 'Activate' ,
			'activateFaled'  => 'ActivateFaled' ,
			'pageChange'     => 'SearchPageChange' ,
			'registForm'     => 'Regist' ,
			'registCheck'    => 'RegistCheck' ,
			'registComp'     => 'RegistComp' ,
			'userRegistComp' => 'UserRegistComp' ,
			'registFaled'    => 'RegistFaled' ,
			'editForm'       => 'Edit' ,
			'editCheck'      => 'EditCheck' ,
			'editComp'       => 'EditComp' ,
			'deleteCheck'    => 'DeleteCheck' ,
			'deleteComp'     => 'DeleteComp' ,
			'userDeleteComp' => 'UserDeleteComp' ,
			'searchForm'     => 'Search' ,
			'searchResult'   => 'SearchResult' ,
			'searchFaled'    => 'SearchNotFound' ,
			'searchList'     => 'List' ,
			'info'           => 'Info'
		);

		static function GetFileName( $_key , $_target )
		{
			if( 'include' == $_key || 'other' == $_key )
				return ucfirst( $_target );
			else if( isset( Preset::$FileName[ $_key ] ) )
				return Preset::$FileName[ $_key ];
			else
				return 'unknown';
		}

		static function GetDirectory( $_key , $_user )
		{
			if( 'registComp' == $_key || 'editComp' == $_key || 'userRegistComp' == $_key || 'userEditComp' == $_key || 'deleteComp' == $_key || 'userDeleteComp' == $_key )
				return 'base/';
			else if( 'include' == $_key )
				return 'include/';
			else if( 'other' == $_key )
				return 'other/';
			else if( 'pageChange' == $_key )
				return 'base/';
			else if( $_user )
				return $_user . '/';
			else
				return 'unknown/';
		}

		static function GetLabel( $_key )
		{
			if( isset( Preset::$Label[ $_key ] ) )
				return Preset::$Label[ $_key ];
			else
				return $_key;
		}

		static function GetOwner( $_key )
		{
			switch( $_key )
			{
				case 'editSelf' :
				case 'deleteSelf' :
				case 'infoSelf' :
					return 1;

				case 'editOther' :
				case 'deleteOther' :
				case 'infoOther' :
					return 2;

				default :
					return 3;
			}
		}

		static function GetFormatKeys( $_method , $_key , $_target )
		{
			$define = DefineSection::GetDefines();

			if( array_key_exists( $_target , $define ) )
				$userData = ( 'user' == $define[ $_target ][ 'defineType' ] ? true : false );
			else
				$userData = false;

			switch( $_method )
			{
				case 'regist' :
					if( '*' == $_key )
					{
						if( $userData )
							return Array( 'registForm' , 'registCheck' , 'userRegistComp' , 'registFaled' );
						else
							return Array( 'registForm' , 'registCheck' , 'registComp' , 'registFaled' );
					}
					else
					{
						if( $userData && 'comp' == $_key )
							return Array( 'userRegist' . ucfirst( $_key ) );
						else
							return Array( 'regist' . ucfirst( $_key ) );
					}

				case 'edit' :
				case 'editSelf' :
				case 'editOther' :
					if( '*' == $_key )
						return Array( 'editForm' , 'editCheck' , 'editComp' , 'registFaled' );
					else if( 'faled' == $_key )
						return Array( 'registFaled' );
					else
						return Array( 'edit' . ucfirst( $_key ) );

				case 'delete' :
				case 'deleteSelf' :
				case 'deleteOther' :
					if( '*' == $_key )
					{
						if( $userData )
							return Array( 'deleteCheck' , 'userDeleteComp' );
						else
							return Array( 'deleteCheck' , 'deleteComp' );
					}
					else
					{
						if( $userData && 'comp' == $_key )
							return Array( 'userDelete' . ucfirst( $_key ) );
						else
							return Array( 'delete' . ucfirst( $_key ) );
					}

				case 'search' :
					if( '*' == $_key )
						return Array( 'searchForm' , 'searchResult' , 'searchFaled' , 'searchList' );
					else
						return Array( 'search' . ucfirst( $_key ) );

				case 'info' :
				case 'infoSelf' :
				case 'infoOther' :
					return Array( 'info' );

				default :
					return Array( 'unknown' );
			}
		}
	}

?>
