<?php

#	Copyright (C)Tomoya Koyanagi.
#	Last Update : 0:52 2010/08/25

#******************************************************************************************************************
#	インポート・エクスポート
class base_plugins_PicklesCrawler_resources_io{

	var $pcconf;
	var $conf;
	var $dbh;
	var $errors;

	#----------------------------------------------------------------------------
	#	コンストラクタ
	function base_plugins_PicklesCrawler_resources_io( &$pcconf ){
		$this->pcconf = &$pcconf;
		$this->conf = &$pcconf->get_basicobj_conf();
		$this->errors = &$pcconf->get_basicobj_errors();
		$this->dbh = &$pcconf->get_basicobj_dbh();
	}

	#----------------------------------------------------------------------------
	#	エクスポートファイルを作成する
	function mk_export_file( $ziptype , $options = array() ){

		#	エクスポートを実行
		if( !$this->local_export( $options ) ){
			return false;
		}

		$path_export_dir = $this->pcconf->get_home_dir().'/_export/';

		$download_content_path = $path_export_dir.'tmp/';
		$download_zipto_path = $path_export_dir.'PxCrawer_export_'.date('Ymd_His');
		if( !is_dir( $download_content_path ) ){
			return false;//←圧縮対象が存在しません。
		}

		if( strtolower($ziptype) == 'tgz' && strlen( $this->conf->path_commands['tar'] ) ){
			#	tarコマンドが使えたら(UNIXのみ)
			$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/resources/tgz.php' );
			if( !$className ){
				$this->errors->error_log( 'tgzライブラリのロードに失敗しました。' , __FILE__ , __LINE__ );
				return false;
			}
			$obj_tgz = new $className( &$this->conf , &$this->dbh , &$this->errors );

			if( !$obj_tgz->zip( $download_content_path , $download_zipto_path.'.tgz' ) ){
				return false;//圧縮に失敗しました。
			}

			if( !is_file( $download_zipto_path.'.tgz' ) ){
				return false;//圧縮されたアーカイブファイルは現在は存在しません。
			}

			$download_zipto_path = $download_zipto_path.'.tgz';

		}elseif( strtolower($ziptype) == 'zip' && class_exists( 'ZipArchive' ) ){
			#	ZIP関数が有効だったら
			$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/resources/zip.php' );
			if( !$className ){
				$this->errors->error_log( 'zipライブラリのロードに失敗しました。' , __FILE__ , __LINE__ );
				return false;
			}
			$obj_zip = new $className( &$this->conf , &$this->dbh , &$this->errors );

			if( !$obj_zip->zip( $download_content_path , $download_zipto_path.'.zip' ) ){
				return false;//圧縮に失敗しました。
			}

			if( !is_file( $download_zipto_path.'.zip' ) ){
				return false;//圧縮されたアーカイブファイルは現在は存在しません。
			}

			$download_zipto_path = $download_zipto_path.'.zip';

		}

		if( is_file( $download_zipto_path ) ){
			return $download_zipto_path;
		}
		return false;
	}// mk_export_file()

	#--------------------------------------
	#	エクスポートデータを作成
	function local_export( $options = array() ){
		$path_export_dir = $this->pcconf->get_home_dir().'/_export/';

		$this->dbh->rmdir( $path_export_dir );
		$this->dbh->mkdirall( $path_export_dir );
		$this->dbh->mkdirall( $path_export_dir.'tmp/' );

		$projList = $this->dbh->getfilelist( $this->pcconf->get_home_dir().'/proj/' );
		foreach( $projList as $project_id ){
			if( @count( $options['project'] ) && !$options['project'][$project_id] ){
				continue;
			}
			$this->dbh->mkdirall( $path_export_dir.'tmp/'.$project_id.'/' );
			$this->local_export_project(
				$this->pcconf->get_home_dir().'/proj/'.$project_id.'/' ,
				$path_export_dir.'tmp/'.$project_id.'/'
			);
		}

		return true;
	}//local_export()

	#--------------------------------------
	#	プロジェクトをエクスポートフォルダにコピーする
	function local_export_project( $from , $to ){
		$projFileList = $this->dbh->getfilelist( $from );
		foreach( $projFileList as $project_filename ){
			$tmp_path = $from.$project_filename;
			if( is_dir( $tmp_path ) ){
				$this->dbh->mkdirall( $to.$project_filename.'/' );
				if( $project_filename == 'prg' ){
					$projPrgList = $this->dbh->getfilelist( $from.$project_filename.'/' );
					foreach( $projPrgList as $program_id ){
						$this->dbh->mkdirall( $to.$project_filename.'/'.$program_id.'/' );
						$result = $this->local_export_program(
							$from.$project_filename.'/'.$program_id.'/' ,
							$to.$project_filename.'/'.$program_id.'/'
						);
					}
				}
			}elseif( is_file( $tmp_path ) ){
				$this->dbh->copy(
					$tmp_path ,
					$to.$project_filename
				);
			}
		}
		return true;
	}// local_export_project()

	#--------------------------------------
	#	プログラムをエクスポートフォルダにコピーする
	function local_export_program( $from , $to ){
		if( !is_dir( $from ) ){ return false; }
		$from = $this->dbh->get_realpath( $from ).'/';
		if( !is_dir( $to ) ){ return false; }
		$to = $this->dbh->get_realpath( $to ).'/';

		$prgFileList = $this->dbh->getfilelist( $from );
		foreach( $prgFileList as $prgFile ){
			if( is_dir( $from.$prgFile ) ){
				$this->dbh->mkdir($to.$prgFile);
			}elseif( is_file( $from.$prgFile ) ){
				$this->dbh->copy(
					$from.$prgFile ,
					$to.$prgFile
				);
			}
		}

		return true;
	}// local_export_program()

}
?>