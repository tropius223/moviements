<?php

	class Section
	{
		private $contents = Array();
		private $child    = Array();

		/*		�Z�N�V������������擾����		*/
		function getContentsString()
		{
			if( is_array( $this->contents ) )
				return implode( "\n" , $this->contents );
			else
				return null;
		}

		/*		�Z�N�V�����z����擾����		*/
		function getContentsArray()
		{
			return $this->contents;
		}

		/*		�Z�N�V������P��ɕ������Ď擾����		*/
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

		/*		�q�Z�N�V�����z����擾����		*/
		function getChilds()
		{
			return $this->child;
		}

		/*		�Z�N�V�������e��ǉ�		*/
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

		/*		�Z�N�V�������󂩂ǂ����m�F		*/
		function isNull()
		{
			if( !$this->contents && !$this->child )
				return true;
			else
				return false;
		}

		/*		�q�Z�N�V������ǉ�		*/
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
