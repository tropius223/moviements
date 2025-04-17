<?php

	class Section
	{
		private $contents = Array();
		private $child    = Array();

		/*		セクション文字列を取得する		*/
		function getContentsString()
		{
			if( is_array( $this->contents ) )
				return implode( "\n" , $this->contents );
			else
				return null;
		}

		/*		セクション配列を取得する		*/
		function getContentsArray()
		{
			return $this->contents;
		}

		/*		セクションを単語に分割して取得する		*/
		function getContentsWords()
		{
			if( is_array( $this->contents ) )
			{
				for( $i = 0 ; $i < count( $this->contents ) ; $i++ )
				{
					foreach( preg_split( '/[\\t\\s]+/' , $this->contents[ $i ] ) as $word )
						$result[] = $word;
				}

				return $result;
			}
			else
				return null;
		}

		/*		子セクション配列を取得する		*/
		function getChilds()
		{
			return $this->child;
		}

		/*		セクション内容を追加		*/
		function addContents( $_contents )
		{
			if( is_array( $_contents ) )
			{
				for( $i = 0 ; $i < count( $_contents ) ; $i++ )
				{
					if( $_contents[ $i ] )
						$this->contents[] = $_contents[ $i ];
				}
			}
			else if( $_contents )
				$this->contents[] = $_contents;
		}

		/*		セクションが空かどうか確認		*/
		function isNull()
		{
			if( !$this->contents && !$this->child )
				return true;
			else
				return false;
		}

		/*		子セクションを追加		*/
		function addChilds( $_child )
		{
			if( is_array( $_child ) )
			{
				foreach( $_child as $value )
				{
					if( !$value->isNull() )
					{
						$this->child[] = $value;
						$value->parent = $this;
					}
				}
			}
			else if( !$_child->isNull() )
			{
				$this->child[]  = $_child;
				$_child->parent = $this;
			}
		}
	}
?>
