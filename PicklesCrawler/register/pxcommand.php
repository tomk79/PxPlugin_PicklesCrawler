<?php
$this->load_px_class('/bases/pxcommand.php');

/**
 * PX Plugin "PicklesCrawler"
 */
class pxplugin_PicklesCrawler_register_pxcommand extends px_bases_pxcommand{

	/**
	 * コンストラクタ
	 * @param $command = PXコマンド配列
	 * @param $px = PxFWコアオブジェクト
	 */
	public function __construct( $command , $px ){
		parent::__construct( $command , $px );
		$this->px = $px;

		$this->homepage();
	}

	/**
	 * ホームページを表示する。
	 */
	private function homepage(){
		$command = $this->get_command();


/*
		#--------------------------------------
		#    PicklesCrawler コンフィグを生成
		$className = $dbh->require_lib( '/plugins/PicklesCrawler/config.php' );
		$pcconf = new $className( &$conf , &$errors , &$dbh , &$req , &$user , &$site , &$theme , &$custom );

		#--------------------------------------
		#    設定

		# クローラのページIDを設定。
		# ここでは、ページID crawlctrl を指定する。
		$pcconf->pid = array(
		    'crawlctrl'=>'crawlctrl',
		);

		# PicklesCrawlerに付与するホームディレクトリを設定。
		# RAMデータディレクトリ内に専用の領域を付与している。
		$pcconf->set_home_dir( $conf->path_ramdata_dir.'/plugins/PicklesCrawler' );

		#    / 設定
		#--------------------------------------


		$obj = &$pcconf->factory_admin();
		return    $obj->start();

*/

		$src = '';
		$src .= '<p>サンプル</p>'."\n";

		print $this->html_template($src);
		exit;
	}

}

?>
