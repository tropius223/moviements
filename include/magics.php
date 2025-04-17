<?php

	//★クラス //

	/**
		@brief   本基底クラス。
		@details エラーチェックに関するマジックメソッドを一括で実装します。\n
		         このクラスを継承することで未定義のメンバ参照/メソッド呼び出しを例外として扱えるようになります。
		@ingroup SystemComponent
	*/
	class magics //
	{
		//■マジック //

		/**
			@brief     未定義のメソッド呼び出しをフックする。
			@details   クラスが実装していないメソッドを呼び出そうとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iMethodName_ メソッド名。
			@param[in] $iArgs_       引数配列。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		function __call( $iMethodName_ , $iArgs_ ) //
			{ throw new LogicException( '次のメソッドは定義されていません[' . $iMethodName_ . ']' ); }

		/**
			@brief     未定義の静的メソッド呼び出しをフックする。
			@details   クラスが実装していない静的メソッドを呼び出そうとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iMethodName_ メソッド名。
			@param[in] $iArgs_       引数配列。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		static function __callstatic( $iMethodName_ , $iArgs_ ) //
			{ throw new LogicException( '次のメソッドは定義されていません[' . $iMethodName_ . ']' ); }

		/**
			@brief     アクセス不能メンバへの参照をフックする。
			@details   クラスが実装していないメンバの値を取得しようとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iName_ メンバ名。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		function __get( $iName_ ) //
			{ throw new LogicException( '次のメンバは定義されていないか、またはprivateです[' . $iName_ . ']' ); }

		/**
			@brief     アクセス不能メンバへの参照をフックする。
			@details   クラスが実装していないメンバに対してissetを呼び出そうとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iName_ メンバ名。
			@attention PHP5.1.0未満では機能しません。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		function __isset( $iName_ ) //
			{ throw new LogicException( '次のメンバは定義されていないか、またはprivateです[' . $iName_ . ']' ); }

		/**
			@brief     アクセス不能メンバへの参照をフックする。
			@details   クラスが実装していないメンバに値を代入しようとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iName_  メンバ名。
			@param[in] $iValue_ 代入値。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		function __set( $iName_ , $iValue_ ) //
			{ throw new LogicException( '次のメンバは定義されていないか、またはprivateです[' . $iName_ . ']' ); }

		/**
			@brief     アクセス不能メンバへの参照をフックする。
			@details   クラスが実装していないメンバに対してunsetを呼び出そうとした場合に実行されます。
			@exception LogicException 常に。
			@param[in] $iName_ メンバ名。
			@attention PHP5.1.0未満では機能しません。
			@attention 対応できない場合は必ず例外を投げなければいけません。
		*/
		function __unset( $iName_ ) //
			{ throw new LogicException( '次のメンバは定義されていないか、またはprivateです[' . $iName_ . ']' ); }
	}
