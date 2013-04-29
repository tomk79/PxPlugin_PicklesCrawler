<?php

#	Copyright (C)Tomoya Koyanagi.
#	Last Update : 6:44 2009/08/27

/**
 * 機能設定
 */
class pxplugin_PicklesCrawler_config{

	var $conf;
	var $errors;
	var $dbh;
	var $req;
	var $user;
	var $site;
	var $theme;
	var $custom;

	#--------------------------------------
	#	設定項目
	var $path_home_dir = null;
		#	PicklesCrawlerのホームディレクトリ設定

	var $localpath_proj_dir = '/proj';		#	プロジェクトディレクトリ
	var $localpath_log_dir = '/logs';		#	ログディレクトリ
	var $localpath_proc_dir = '/proc';		#	プロセス記憶ディレクトリ

	var $pid = array(
		'crawlctrl'=>'crawlctrl',	#	クロールコントローラのページID
	);


	var $conf_crawl_max_url_number = 10000000;
		#	1回のクロールで処理できる最大URL数。
		#	URLなので、画像などのリソースファイルも含まれる。
		#		6:44 2009/08/27 : 100000 から 10000000 に変更

	var $conf_dl_datetime_in_filename = true;
		#	クロール結果を管理画面からダウンロードするときに、
		#	ファイル名にクロール日時を含めるか否か。

	var $conf_download_list_csv_charset = 'Shift_JIS';
		#	ダウンロードリストCSVの文字コード。
		#	null を指定すると、mb_internal_encoding() になる。

	#	/ 設定項目
	#--------------------------------------

	/**
	 * コンストラクタ
	 */
	public function __construct( &$conf , &$errors , &$dbh , &$req , &$user , &$site , &$theme , &$custom ){
		$this->conf = &$conf;
		$this->errors = &$errors;
		$this->dbh = &$dbh;
		$this->req = &$req;
		$this->user = &$user;
		$this->site = &$site;
		$this->theme = &$theme;
		$this->custom = &$custom;
	}

	#--------------------------------------
	#	設定値を取得
	function get_value( $key ){
		if( !preg_match( '/^[a-zA-Z][a-zA-Z0-9_]*$/' , $key ) ){ return false; }
		$RTN = @eval( 'return $this->conf_'.strtolower( $key ).';' );
		return	$RTN;
	}

	#--------------------------------------
	#	値を設定
	function set_value( $key , $val ){
		if( !preg_match( '/^[a-zA-Z][a-zA-Z0-9_]*$/' , $key ) ){ return false; }
		@eval( '$this->conf_'.strtolower( $key ).' = '.text::data2text( $val ).';' );
		return	true;
	}


	#--------------------------------------
	#	ホームディレクトリの設定/取得
	function set_home_dir( $path ){
		if( !strlen( $path ) ){ return false; }
		$path = $this->dbh->get_realpath( $path );
		if( !$this->dbh->is_writable( $path ) ){
			return	false;
		}

		$this->path_home_dir = $path;
		return	true;
	}
	function get_home_dir(){
		return	$this->path_home_dir;
	}

	#--------------------------------------
	#	プロジェクトディレクトリの取得
	function get_proj_dir( $project_id = null ){
		if( !is_dir( $this->get_home_dir().$this->localpath_proj_dir ) ){
			if( !$this->dbh->mkdirall( $this->get_home_dir().$this->localpath_proj_dir ) ){
				return	false;
			}
		}
		if( strlen( $project_id ) ){
			return	$this->get_home_dir().$this->localpath_proj_dir.'/'.urlencode( $project_id );
		}
		return	$this->get_home_dir().$this->localpath_proj_dir;
	}
	#--------------------------------------
	#	プログラムディレクトリの取得
	function get_program_home_dir( $project_id , $program_id = null ){
		if( !strlen( $project_id ) ){ return false; }
		$proj_dir = $this->get_proj_dir( $project_id );
		if( !is_dir( $proj_dir ) ){
			return	false;
		}
		if( !is_dir( $proj_dir.'/prg' ) ){
			if( !$this->dbh->mkdir( $proj_dir.'/prg' ) ){
				return	false;
			}
		}
		if( strlen( $program_id ) ){
			return	$proj_dir.'/prg/'.urlencode( $program_id );
		}
		return	$proj_dir.'/prg';
	}
	#--------------------------------------
	#	ログディレクトリの取得
	function get_log_dir( $project_id = null ){
		if( !is_dir( $this->get_home_dir().$this->localpath_log_dir ) ){
			if( !$this->dbh->mkdir( $this->get_home_dir().$this->localpath_log_dir ) ){
				return	false;
			}
		}
		if( strlen( $project_id ) ){
			return	$this->get_home_dir().$this->localpath_log_dir.'/'.urlencode( $project_id );
		}
		return	$this->get_home_dir().$this->localpath_log_dir;
	}
	#--------------------------------------
	#	プロセス記憶ディレクトリの取得
	function get_proc_dir(){
		if( !is_dir( $this->get_home_dir().$this->localpath_proc_dir ) ){
			if( !$this->dbh->mkdir( $this->get_home_dir().$this->localpath_proc_dir ) ){
				return	false;
			}
		}
		return	$this->get_home_dir().$this->localpath_proc_dir;
	}



	#--------------------------------------
	#	ファクトリ：プロジェクトモデル
	function &factory_model_project(){
		$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/model/project.php' );
		if( !$className ){
			return	false;
		}
		$obj = new $className( &$this->conf , &$this , &$this->errors , &$this->dbh );
		return	$obj;
	}



	#--------------------------------------
	#	ファクトリ：管理画面インスタンスを取得
	function &factory_admin(){
		$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/admin.php' );
		if( !$className ){
			$this->errors->error_log( 'PicklesCrawlerプラグイン「管理画面」の読み込みに失敗しました。' , __FILE__ , __LINE__ );
			return	false;
		}
		$obj = new $className( &$this );
		return	$obj;
	}


	#--------------------------------------
	#	ファクトリ：クローラインスタンスを取得
	function &factory_crawlctrl(){
		$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/crawlctrl.php' );
		if( !$className ){
			$this->errors->error_log( 'PicklesCrawlerプラグイン「クロールコントローラ」の読み込みに失敗しました。' , __FILE__ , __LINE__ );
			return	false;
		}
		$obj = new $className( &$this );
		return	$obj;
	}

	#--------------------------------------
	#	基本オブジェクトを取り出す
	function &get_basicobj_conf()		{ return $this->conf; }
	function &get_basicobj_errors()		{ return $this->errors; }
	function &get_basicobj_dbh()		{ return $this->dbh; }
	function &get_basicobj_req()		{ return $this->req; }
	function &get_basicobj_user()		{ return $this->user; }
	function &get_basicobj_site()		{ return $this->site; }
	function &get_basicobj_theme()		{ return $this->theme; }
	function &get_basicobj_custom()		{ return $this->custom; }

}

?>