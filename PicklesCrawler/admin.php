<?php

#	Copyright (C)Tomoya Koyanagi.
#	Last Update: 12:54 2011/08/28

/**
 * プロジェクト管理機能
 */
class pxplugin_PicklesCrawler_admin{

	private $px;
	private $pcconf;
	private $cmd;

	private $local_sitemap = array();// ページ名等を定義する

	/**
	 * コンストラクタ
	 */
	public function __construct( &$px, &$pcconf, $cmd ){
		$this->px = &$px;
		$this->pcconf = &$pcconf;
		$this->cmd = &$cmd;

		$this->set_sitemap();
	}

	/**
	 * config:設定値を取得
	 */
	public function get_conf( $key ){
		return	$this->pcconf->get_value( $key );
	}

	/**
	 * config:値を設定
	 */
	public function set_conf( $key , $val ){
		return	$this->pcconf->set_value( $key , $val );
	}


	/**
	 * 処理の開始
	 */
	public function start(){
		if( $this->cmd[0] == 'detail' ){
			#	プロジェクト詳細
			return	$this->page_project_detail();
		}elseif( $this->cmd[0] == 'create_proj' || $this->cmd[0] == 'edit_proj' ){
			#	プロジェクト作成/編集
			return	$this->start_edit_proj();
		}elseif( $this->cmd[0] == 'edit_param_define' ){
			#	プロジェクトのパラメータ定義を編集
			return	$this->start_edit_param_define();
		}elseif( $this->cmd[0] == 'edit_localfilename_rewriterules' ){
			#	保存ファイル名のリライトルール編集
			return	$this->start_edit_localfilename_rewriterules();
		}elseif( $this->cmd[0] == 'edit_charset' ){
			#	文字コード・改行コード変換設定編集
			return	$this->start_edit_charset();
		}elseif( $this->cmd[0] == 'edit_preg_replace' ){
			#	一括置換設定編集
			return	$this->start_edit_preg_replace();
		}elseif( $this->cmd[0] == 'create_program' || $this->cmd[0] == 'edit_program' ){
			#	プログラム作成/編集
			return	$this->start_edit_program();
		}elseif( $this->cmd[0] == 'execute_program' ){
			#	プログラムを実行
			return	$this->start_execute_program();
		}elseif( $this->cmd[0] == 'delete_program_content' ){
			#	プログラムが書き出したコンテンツを削除する
			return	$this->start_delete_program_content();
		}elseif( $this->cmd[0] == 'delete_program' ){
			#	プログラムを削除
			return	$this->start_delete_program();
		}elseif( $this->cmd[0] == 'delete_proj' ){
			#	プロジェクトを削除
			return	$this->start_delete_proj();
		}elseif( $this->cmd[0] == 'configcheck' ){
			#	設定項目の確認
			return	$this->page_configcheck();
		}elseif( $this->cmd[0] == 'export' ){
			#	エクスポート
			return	$this->start_export();
		}
		return	$this->page_start();
	}


	/**
	 * コンテンツ内へのリンク先を調整する。
	 */
	private function href( $linkto = null ){
		if(is_null($linkto) || $linkto == ':'){
			return '?PX=plugins.PicklesCrawler';
		}
		$rtn = preg_replace('/^\:/','?PX=plugins.PicklesCrawler.',$linkto);

		$rtn = $this->px->theme()->href( $rtn );
		return $rtn;
	}

	/**
	 * コンテンツ内へのリンクを生成する。
	 */
	private function mk_link( $linkto , $options = array() ){
		if( !strlen($options['label']) ){
			if( $this->local_sitemap[$linkto] ){
				$options['label'] = $this->local_sitemap[$linkto]['title'];
			}
		}
		$rtn = $this->href($linkto);

		$rtn = $this->px->theme()->mk_link( $rtn , $options );
		return $rtn;
	}

	/**
	 * このコンテンツ内でのサイトマップを登録する
	 */
	private function set_sitemap(){

		$this->local_sitemap[ ':create_proj'                                      ] = array( 'title'=>'新規プロジェクト作成'               );
		$this->local_sitemap[ ':configcheck'                                      ] = array( 'title'=>'設定の確認'                         );
		$this->local_sitemap[ ':export'                                           ] = array( 'title'=>'設定をエクスポート'                 );
		$this->local_sitemap[ ':detail.'.$this->cmd[1]                            ] = array( 'title'=>'プロジェクト詳細'                   );
		$this->local_sitemap[ ':edit_proj.'.$this->cmd[1]                         ] = array( 'title'=>'プロジェクト編集'                   );
		$this->local_sitemap[ ':edit_param_define.'.$this->cmd[1]                 ] = array( 'title'=>'パラメータ定義の編集'               );
		$this->local_sitemap[ ':edit_localfilename_rewriterules.'.$this->cmd[1]   ] = array( 'title'=>'保存ファイル名のリライトルール編集' );
		$this->local_sitemap[ ':create_program.'.$this->cmd[1]                    ] = array( 'title'=>'新規プログラム作成'                 );
		$this->local_sitemap[ ':edit_program.'.$this->cmd[1].'.'.$this->cmd[2]    ] = array( 'title'=>'プログラム編集'                     );
		$this->local_sitemap[ ':execute_program.'.$this->cmd[1].'.'.$this->cmd[2] ] = array( 'title'=>'プログラム実行'                     );
		$this->local_sitemap[ ':delete_program.'.$this->cmd[1].'.'.$this->cmd[2]  ] = array( 'title'=>'プログラム削除'                     );
		$this->local_sitemap[ ':edit_charset.'.$this->cmd[1]                      ] = array( 'title'=>'文字コード・改行コード変換設定'     );
		$this->local_sitemap[ ':edit_preg_replace.'.$this->cmd[1]                 ] = array( 'title'=>'一括置換設定'                       );
		$this->local_sitemap[ ':delete_proj.'.$this->cmd[1]                       ] = array( 'title'=>'プロジェクトを削除'                 );
		$this->local_sitemap[ ':delete_program_content.'.$this->cmd[1]            ] = array( 'title'=>'プログラムコンテンツの削除'         );

		return true;
	}


	/**
	 * スタートページ
	 */
	private function page_start(){

		$RTN = '';
		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	この機能は、ウェブアクセスにより、ネットワーク上のウェブサイトを巡回し保存します。<br />'."\n";
		$RTN .= '</p>'."\n";

		$project_model = &$this->pcconf->factory_model_project();
		$project_list = $project_model->get_project_list();
		if( !is_array($project_list) || !count($project_list) ){
			$RTN .= '<p class="ttr">現在プロジェクトは登録されていません。</p>'."\n";
		}else{
			$RTN .= '<table class="deftable" width="100%">'."\n";
			$RTN .= '	<thead>'."\n";
			$RTN .= '		<tr>'."\n";
			$RTN .= '			<th>プロジェクト名</div></th>'."\n";
			$RTN .= '			<th>プロジェクトID</div></th>'."\n";
			$RTN .= '			<th>トップページURL</div></th>'."\n";
			$RTN .= '			<th>&nbsp;</div></th>'."\n";
			$RTN .= '		</tr>'."\n";
			$RTN .= '	</thead>'."\n";
			foreach( $project_list as $Line ){
				$RTN .= '	<tr>'."\n";
				$RTN .= '		<th class="left">'.$this->px->theme()->mk_link(':detail.'.$Line['id'],array('label'=>$Line['name'],'style'=>'inside')).'</th>'."\n";
				$RTN .= '		<td class="left">'.htmlspecialchars( $Line['id'] ).'</td>'."\n";
				$RTN .= '		<td class="left">'.htmlspecialchars( $Line['url_docroot'] ).'</td>'."\n";
				$RTN .= '		<td class="left">'."\n";
				$RTN .= '			'.$this->mk_link(':detail.'.$Line['id'],array('label'=>'詳細','style'=>'inside'))."\n";
//				$RTN .= '			'.$this->mk_link(':edit_proj.'.$Line['id'],array('label'=>'編集','style'=>'inside'))."\n";
//				$RTN .= '			'.$this->mk_link(':delete_proj.'.$Line['id'],array('label'=>'削除','style'=>'inside')).''."\n";
				$RTN .= '		</td>'."\n";
				$RTN .= '	</tr>'."\n";
			}
			$RTN .= '</table>'."\n";
		}

		$RTN .= '<hr />'."\n";
		$RTN .= '<ul>'."\n";
		$RTN .= '	<li>'.$this->mk_link(':create_proj',array('style'=>'inside')).'</li>'."\n";
		$RTN .= '	<li>'.$this->mk_link(':export',array('style'=>'inside')).'</li>'."\n";
		$RTN .= '	<li>'.$this->mk_link(':configcheck',array('style'=>'inside')).'</li>'."\n";
		$RTN .= '</ul>'."\n";
		return	$RTN;
	}

	#--------------------------------------
	#	プロジェクトの詳細画面
	function page_project_detail(){

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );

		$this->site->setpageinfo( $this->req->p() , 'title' , 'プロジェクト『'.htmlspecialchars( $project_model->get_project_name() ).'』の詳細情報' );

		$RTN = '';

		#======================================
		$RTN .= ''.$this->theme->mk_hx( '基本情報' ).''."\n";
		$RTN .= '<table class="deftable" width="100%">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プロジェクト名 (プロジェクトID)</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr"><strong>'.htmlspecialchars( $project_model->get_project_name() ).'</strong> ('.htmlspecialchars( $this->cmd[1] ).')</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ドキュメントルートURL</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_url_docroot() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">スタートページURL</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_url_startpage() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">デフォルトのファイル名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_default_filename() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">URL変換時に省略するファイル名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( implode( ', ' , $project_model->get_omit_filename() ) ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象外URLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$urlList = $project_model->get_urllist_outofsite();
		if( count( $urlList ) ){
			$RTN .= '			<ul>'."\n";
			foreach( $urlList as $url ){
				$RTN .= '				<li class="ttr">'.htmlspecialchars( $url ).'</li>'."\n";
			}
			$RTN .= '			</ul>'."\n";
		}else{
			$RTN .= '			<div class="ttr">指定はありません。</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">追加スタートページURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$urlList = $project_model->get_urllist_startpages();
		if( count( $urlList ) ){
			$RTN .= '			<ul>'."\n";
			foreach( $urlList as $url ){
				$RTN .= '				<li class="ttr">'.htmlspecialchars( $url ).'</li>'."\n";
			}
			$RTN .= '			</ul>'."\n";
		}else{
			$RTN .= '			<div class="ttr">指定はありません。</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">認証</div></th>'."\n";
		if( !$project_model->isset_basic_authentication_info() ){
			$RTN .= '		<td style="width:70%;"><div class="ttr">設定なし(または無効)</div></td>'."\n";
		}else{
			$RTN .= '		<td style="width:70%;">'."\n";
			$label = array( ''=>'自動選択', 'basic'=>'ベーシック認証', 'digest'=>'ダイジェスト認証' );
			$RTN .= '			<div class="ttr">認証タイプ: '.htmlspecialchars( $label[$project_model->get_authentication_type()] ).'</div>'."\n";
			$RTN .= '			<div class="ttr">ID: '.htmlspecialchars( $project_model->get_basic_authentication_id() ).'</div>'."\n";
			$RTN .= '			<div class="ttr">PW: ********</div>'."\n";
			$RTN .= '		</td>'."\n";
		}
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">パス指定変換</div></th>'."\n";
		$label = array( 'relative'=>'相対パス','absolute'=>'絶対パス','url'=>'URL','none'=>'変換しない' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[$project_model->get_path_conv_method()] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">サイト外指定パスの変換</div></th>'."\n";
		$label = array( '0'=>'パス指定変換設定に従う','1'=>'URLに変換する' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[intval($project_model->get_outofsite2url_flg())] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">未定義のパラメータ</div></th>'."\n";
		$label = array( '0'=>'送信しない','1'=>'送信する' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[intval($project_model->get_send_unknown_params_flg())] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">フォーム送信可否</div></th>'."\n";
		$label = array( '0'=>'送信しない','1'=>'送信する' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[intval($project_model->get_send_form_flg())] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">HTML内のJavaScript</div></th>'."\n";
		$label = array( '0'=>'解析しない','1'=>'解析する' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[intval($project_model->get_parse_jsinhtml_flg())] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">Not Found ページ収集</div></th>'."\n";
		$label = array( '0'=>'収集しない','1'=>'収集する' );
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $label[intval($project_model->get_save404_flg())] ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">複製先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_path_copyto() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':edit_proj.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="プロジェクト情報を編集する" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':edit_proj.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		#======================================
		$RTN .= ''.$this->theme->mk_hx( 'プログラム一覧' ).''."\n";

		$program_list = $project_model->get_program_list();
		$CSS = '';
		$CSS .= '#content .cont_unit_program{'."\n";
		$CSS .= '	border:2px solid #ff9999;'."\n";
		$CSS .= '}'."\n";
		$this->theme->putsrc($CSS,'css');
		$RTN .= '<div class="unit cont_unit_program">'."\n";
		if( !is_array( $program_list ) || !count( $program_list ) ){
			$RTN .= '<p class="ttr">現在、プログラムは登録されていません。</p>'."\n";
		}else{
			$RTN .= '<table class="deftable" width="100%">'."\n";
			$RTN .= '	<thead>'."\n";
			$RTN .= '		<tr>'."\n";
			$RTN .= '			<th><div class="ttr" style="overflow:hidden;">プログラム名</div></th>'."\n";
			$RTN .= '			<th><div class="ttr" style="overflow:hidden;">プログラムID</div></th>'."\n";
			$RTN .= '			<th><div class="ttr" style="overflow:hidden;">パラメータ</div></th>'."\n";
			$RTN .= '			<th><div class="ttr" style="overflow:hidden;">HTTP_USER_AGENT</div></th>'."\n";
			$RTN .= '			<th><div class="ttr">&nbsp;</div></th>'."\n";
			$RTN .= '			<th><div class="ttr">&nbsp;</div></th>'."\n";
			$RTN .= '			<th><div class="ttr">&nbsp;</div></th>'."\n";
			$RTN .= '		</tr>'."\n";
			$RTN .= '	</thead>'."\n";
			foreach( $program_list as $program_id ){
				$program_model = &$project_model->factory_program( $program_id );
				$RTN .= '	<tr>'."\n";
				$RTN .= '		<th><div class="ttr" style="overflow:hidden;">'.$this->theme->mk_link(':execute_program.'.$this->cmd[1].'.'.$program_model->get_program_id(),array('label'=>$program_model->get_program_name(),'style'=>'inside')).'</div></th>'."\n";
				$RTN .= '		<td><div class="ttr" style="overflow:hidden;">'.htmlspecialchars( $program_model->get_program_id() ).'</div></td>'."\n";
				$RTN .= '		<td><div class="ttr" style="overflow:hidden;">'.htmlspecialchars( $program_model->get_program_param() ).'</div></td>'."\n";
				$RTN .= '		<td><div class="ttr" style="overflow:hidden;">'.htmlspecialchars( $program_model->get_program_useragent() ).'</div></td>'."\n";
				$RTN .= '		<td><div class="ttr AlignC">'.$this->theme->mk_link(':edit_program.'.$this->cmd[1].'.'.$program_model->get_program_id(),array('label'=>'編集')).'</div></td>'."\n";
				$RTN .= '		<td><div class="ttr AlignC">'.$this->theme->mk_link(':execute_program.'.$this->cmd[1].'.'.$program_model->get_program_id(),array('label'=>'実行')).'</div></td>'."\n";
				$RTN .= '		<td><div class="ttr AlignC">'.$this->theme->mk_link(':delete_program.'.$this->cmd[1].'.'.$program_model->get_program_id(),array('label'=>'削除')).'</div></td>'."\n";
				$RTN .= '	</tr>'."\n";
			}
			$RTN .= '</table>'."\n";
		}
		$RTN .= '</div><!-- / .cont_unit_program -->'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':create_program.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="新規プログラムを追加する" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':create_program.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		#======================================
		$RTN .= ''.$this->theme->mk_hx( 'URLパラメータ定義' ).''."\n";

		$param_def_list = $project_model->get_param_define_list();
		if( is_array( $param_def_list ) && count( $param_def_list ) ){
			$RTN .= '	<table class="deftable" width="100%">'."\n";
			$RTN .= '	<thead>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th class="ttr">物理名</th>'."\n";
			$RTN .= '		<th class="ttr">論理名</th>'."\n";
			$RTN .= '		<th class="ttr">リクエストに含めるか</th>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	</thead>'."\n";
			foreach( $param_def_list as $Line ){
				$RTN .= '	<tr>'."\n";
				$RTN .= '		<th class="ttr">'.htmlspecialchars( $Line ).'</th>'."\n";
				$RTN .= '		<td class="ttr">'.htmlspecialchars( $project_model->get_param_define( $Line , 'name' ) ).'</td>'."\n";
				if( $project_model->get_param_define( $Line , 'request' ) ){
					$RTN .= '		<td class="ttr">含める</td>'."\n";
				}else{
					$RTN .= '		<td class="ttr">含めない</td>'."\n";
				}
				$RTN .= '	</tr>'."\n";
			}
			$RTN .= '	</table>'."\n";
		}else{
			$RTN .= '<p class="ttr">登録されていません。</p>'."\n";
		}
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':edit_param_define.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="パラメータ定義を編集する" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':edit_param_define.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		#======================================
		$RTN .= ''.$this->theme->mk_hx( 'URLのリライトルール' ).''."\n";

		$rule_list = $project_model->get_localfilename_rewriterules();
		if( is_array( $rule_list ) && count( $rule_list ) ){
			$RTN .= '	<table class="deftable" width="100%">'."\n";
			$RTN .= '		<thead>'."\n";
			$RTN .= '			<tr>'."\n";
			$RTN .= '				<th class="ttr"></th>'."\n";
			$RTN .= '				<th class="ttr">元のパス</th>'."\n";
			$RTN .= '				<th class="ttr">変換後の保存先パス</th>'."\n";
			$RTN .= '				<th class="ttr">必須URLパラメータ</th>'."\n";
			$RTN .= '			</tr>'."\n";
			$RTN .= '		</thead>'."\n";
			foreach( $rule_list as $line ){
				$RTN .= '		<tr>'."\n";
				$RTN .= '			<th class="ttr">'.htmlspecialchars( $line['priority'] ).'</th>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['before'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['after'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['requiredparam'] ).'</td>'."\n";
				$RTN .= '		</tr>'."\n";
			}
			$RTN .= '	</table>'."\n";
		}else{
			$RTN .= '<p class="ttr">条件は設定されていません。</p>'."\n";
		}
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':edit_localfilename_rewriterules.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="保存ファイル名のリライトルールを編集" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':edit_localfilename_rewriterules.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		#======================================
		$RTN .= ''.$this->theme->mk_hx( '文字コード・改行コード変換設定' ).''."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">文字コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_charset_charset() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">改行コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_charset_crlf() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
		$RTN .= '		<td style="width:70%;"><div class="ttr">'.htmlspecialchars( $project_model->get_charset_ext() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':edit_charset.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="文字コード・改行コード変換設定を編集" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':edit_charset.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		#======================================
		$RTN .= ''.$this->theme->mk_hx( '一括置換設定' ).''."\n";
		$rule_list = $project_model->get_preg_replace_rules();
		if( is_array( $rule_list ) && count( $rule_list ) ){
			$RTN .= '	<table class="deftable" width="100%">'."\n";
			$RTN .= '		<thead>'."\n";
			$RTN .= '			<tr>'."\n";
			$RTN .= '				<th class="ttr"></th>'."\n";
			$RTN .= '				<th class="ttr">pregパターン</th>'."\n";
			$RTN .= '				<th class="ttr">置換後の文字列</th>'."\n";
			$RTN .= '				<th class="ttr">対象のパス</th>'."\n";
			$RTN .= '				<th class="ttr">ディレクトリを再帰的に置換</th>'."\n";
			$RTN .= '				<th class="ttr">対象とする拡張子</th>'."\n";
			$RTN .= '			</tr>'."\n";
			$RTN .= '		</thead>'."\n";
			foreach( $rule_list as $line ){
				$RTN .= '		<tr>'."\n";
				$RTN .= '			<th class="ttr">'.htmlspecialchars( $line['priority'] ).'</th>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['pregpattern'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['replaceto'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['path'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['dirflg'] ).'</td>'."\n";
				$RTN .= '			<td class="ttr">'.htmlspecialchars( $line['ext'] ).'</td>'."\n";
				$RTN .= '		</tr>'."\n";
			}
			$RTN .= '	</table>'."\n";
		}else{
			$RTN .= '<p class="ttr">条件は設定されていません。</p>'."\n";
		}
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':edit_preg_replace.'.$this->cmd[1] ) ).'">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="一括置換設定を編集" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':edit_preg_replace.'.$this->cmd[1] ).''."\n";
		$RTN .= '</form>'."\n";

		$RTN .= $this->theme->hr()."\n";
		$RTN .= '<ul class="horizontal">'."\n";
		$RTN .= '	<li>'.$this->theme->mk_link(':delete_proj.'.$this->cmd[1],array('label'=>'このプロジェクトを削除','style'=>'inside')).'</li>'."\n";
		$RTN .= '</ul>'."\n";

		return	$RTN;
	}



	###################################################################################################################
	#	新規プロジェクト作成/編集
	function start_edit_proj(){
		$error = $this->check_edit_proj_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_proj_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_proj_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_proj_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			if( $this->cmd[0] == 'edit_proj' ){
				$project_model = &$this->pcconf->factory_model_project();
				$project_model->load_project( $this->cmd[1] );
				$this->req->setin( 'project_id' , $this->cmd[1] );
				$this->req->setin( 'project_name' , $project_model->get_project_name() );
				$this->req->setin( 'url_stargpage' , $project_model->get_url_startpage() );
				$this->req->setin( 'url_docroot' , $project_model->get_url_docroot() );
				$this->req->setin( 'default_filename' , $project_model->get_default_filename() );
				$this->req->setin( 'omit_filename' , implode( ',' , $project_model->get_omit_filename() ) );
				$this->req->setin( 'outofsite2url_flg' , $project_model->get_outofsite2url_flg() );
				$this->req->setin( 'send_unknown_params_flg' , intval( $project_model->get_send_unknown_params_flg() ) );
				$this->req->setin( 'send_form_flg' , intval( $project_model->get_send_form_flg() ) );
				$this->req->setin( 'parse_jsinhtml_flg' , intval( $project_model->get_parse_jsinhtml_flg() ) );
				$this->req->setin( 'save404_flg' , intval( $project_model->get_save404_flg() ) );
				$this->req->setin( 'path_copyto' , $project_model->get_path_copyto() );
				$urllist_outofsite = $project_model->get_urllist_outofsite();
				$str_urllist = '';
				foreach( $urllist_outofsite as $url ){
					$str_urllist .= $url."\n";
				}
				$this->req->setin( 'urllist_outofsite' , $str_urllist );

				$urllist_startpages = $project_model->get_urllist_startpages();
				$str_urllist = '';
				foreach( $urllist_startpages as $url ){
					$str_urllist .= $url."\n";
				}
				$this->req->setin( 'urllist_startpages' , $str_urllist );

				$this->req->setin( 'authentication_type' , $project_model->get_authentication_type() );
				$this->req->setin( 'basic_authentication_id' , $project_model->get_basic_authentication_id() );
				$this->req->setin( 'basic_authentication_pw' , $project_model->get_basic_authentication_pw() );

				$this->req->setin( 'path_conv_method' , $project_model->get_path_conv_method() );

#				$this->req->setin( 'conf_param2filename_type' , '' );
#				$this->req->setin( 'conf_param2filename_ptn' , '' );
			}else{
				#	新規作成のデフォルト値
				$this->req->setin('default_filename','index.html');
			}
		}
		return	$this->page_edit_proj_input( $error );
	}
	#--------------------------------------
	#	新規プロジェクト作成/編集：入力
	function page_edit_proj_input( $error ){
		$RTN = ''."\n";

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	プロジェクトの情報を入力して、「確認する」ボタンをクリックしてください。<span class="must">*</span>印の項目は必ず入力してください。<br />'."\n";
		$RTN .= '</p>'."\n";
		if( is_array( $error ) && count( $error ) ){
			$RTN .= '<p class="ttr error">'."\n";
			$RTN .= '	入力エラーを検出しました。画面の指示に従って修正してください。<br />'."\n";
			$RTN .= '</p>'."\n";
		}
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プロジェクトID <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		if( $this->cmd[0] == 'edit_proj' ){
			#	編集
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('project_id') ).'<input type="hidden" name="project_id" value="'.htmlspecialchars( $this->req->in('project_id') ).'" /></div>'."\n";
		}else{
			#	新規
			$RTN .= '			<div class="ttr"><input type="text" name="project_id" value="'.htmlspecialchars( $this->req->in('project_id') ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['project_id'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['project_id'].'</div>'."\n";
			}
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プロジェクト名 <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="project_name" value="'.htmlspecialchars( $this->req->in('project_name') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['project_name'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['project_name'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ドキュメントルートのURL <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="url_docroot" value="'.htmlspecialchars( $this->req->in('url_docroot') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['url_docroot'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['url_docroot'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">スタートページのURL <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="url_stargpage" value="'.htmlspecialchars( $this->req->in('url_stargpage') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['url_stargpage'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['url_stargpage'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">デフォルトのファイル名 <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="default_filename" value="'.htmlspecialchars( $this->req->in('default_filename') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['default_filename'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['default_filename'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">URL変換時に省略するファイル名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="omit_filename" value="'.htmlspecialchars( $this->req->in('omit_filename') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['omit_filename'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['omit_filename'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※ファイル名は完全一致で評価されます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※カンマ区切りで複数登録することができます。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象外URLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><textarea name="urllist_outofsite" class="inputitems" rows="9">'.htmlspecialchars( $this->req->in('urllist_outofsite') ).'</textarea></div>'."\n";
		if( strlen( $error['urllist_outofsite'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['urllist_outofsite'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※プロトコル部(http://またはhttps://)から始まる完全なURLで指定してください。</li>'."\n";
		$RTN .= '				<li class="ttrs">※改行区切りで複数登録することができます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※アスタリスク(*)記号でワイルドカードを表現できます。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">追加スタートページURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><textarea name="urllist_startpages" class="inputitems" rows="9">'.htmlspecialchars( $this->req->in('urllist_startpages') ).'</textarea></div>'."\n";
		if( strlen( $error['urllist_startpages'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['urllist_startpages'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※プロトコル部(http://またはhttps://)から始まる完全なURLで指定してください。</li>'."\n";
		$RTN .= '				<li class="ttrs">※改行区切りで複数登録することができます。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">認証</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">認証タイプ : '."\n";
		$c = array( $this->req->in('authentication_type')=>' selected="selected"' );
		$RTN .= '				<select name="authentication_type">'."\n";
		$RTN .= '					<option value=""'.$c[''].'>自動選択</option>'."\n";
		$RTN .= '					<option value="basic"'.$c['basic'].'>ベーシック認証</option>'."\n";
		$RTN .= '					<option value="digest"'.$c['digest'].'>ダイジェスト認証</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		$RTN .= '			<div class="ttr">ID : <input type="text" name="basic_authentication_id" value="'.htmlspecialchars( $this->req->in('basic_authentication_id') ).'" /></div>'."\n";
		if( strlen( $error['basic_authentication_id'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['basic_authentication_id'].'</div>'."\n";
		}
		$RTN .= '			<div class="ttr">PW : <input type="text" name="basic_authentication_pw" value="'.htmlspecialchars( $this->req->in('basic_authentication_pw') ).'" /></div>'."\n";
		if( strlen( $error['basic_authentication_pw'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['basic_authentication_pw'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">パス指定変換</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('path_conv_method')=>' selected="selected"' );
		$RTN .= '				<select name="path_conv_method">'."\n";
		$RTN .= '					<option value="relative"'.$c['relative'].'>相対パス</option>'."\n";
		$RTN .= '					<option value="absolute"'.$c['absolute'].'>絶対パス</option>'."\n";
		$RTN .= '					<option value="url"'.$c['url'].'>URL</option>'."\n";
		$RTN .= '					<option value="none"'.$c['none'].'>変換しない</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['path_conv_method'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['path_conv_method'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">サイト外指定パスの変換</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('outofsite2url_flg')=>' selected="selected"' );
		$RTN .= '				<select name="outofsite2url_flg">'."\n";
		$RTN .= '					<option value="0"'.$c['0'].'>パス指定変換設定に従う</option>'."\n";
		$RTN .= '					<option value="1"'.$c['1'].'>URLに変換する</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['outofsite2url_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['outofsite2url_flg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">未定義のパラメータ</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('send_unknown_params_flg')=>' selected="selected"' );
		$RTN .= '				<select name="send_unknown_params_flg">'."\n";
		$RTN .= '					<option value="0"'.$c['0'].'>送信しない</option>'."\n";
		$RTN .= '					<option value="1"'.$c['1'].'>送信する</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['send_unknown_params_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['send_unknown_params_flg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">フォーム送信可否</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('send_form_flg')=>' selected="selected"' );
		$RTN .= '				<select name="send_form_flg">'."\n";
		$RTN .= '					<option value="0"'.$c['0'].'>送信しない</option>'."\n";
		$RTN .= '					<option value="1"'.$c['1'].'>送信する</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['send_form_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['send_form_flg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">HTML内のJavaScript</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('parse_jsinhtml_flg')=>' selected="selected"' );
		$RTN .= '				<select name="parse_jsinhtml_flg">'."\n";
		$RTN .= '					<option value="0"'.$c['0'].'>解析しない</option>'."\n";
		$RTN .= '					<option value="1"'.$c['1'].'>解析する</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['parse_jsinhtml_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['parse_jsinhtml_flg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">Not Found ページ収集</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('save404_flg')=>' selected="selected"' );
		$RTN .= '				<select name="save404_flg">'."\n";
		$RTN .= '					<option value="0"'.$c['0'].'>収集しない</option>'."\n";
		$RTN .= '					<option value="1"'.$c['1'].'>収集する</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['save404_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['save404_flg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">複製先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="path_copyto" value="'.htmlspecialchars( $this->req->in('path_copyto') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['path_copyto'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['path_copyto'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※収集完了後に、収集したコンテンツを複製することができます。複製しない場合は、空白に設定してください。</li>'."\n";
		$RTN .= '				<li class="ttrs">※複製先パスは、既に存在するパスである必要があります。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '	<div align="center"><input type="submit" value="確認する" /></div>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	新規プロジェクト作成/編集：確認
	function page_edit_proj_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	入力した内容を確認してください。<br />'."\n";
		$RTN .= '</p>'."\n";

		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プロジェクトID</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('project_id') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="project_id" value="'.htmlspecialchars( $this->req->in('project_id') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プロジェクト名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('project_name') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="project_name" value="'.htmlspecialchars( $this->req->in('project_name') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ドキュメントルートのURL</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('url_docroot') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="url_docroot" value="'.htmlspecialchars( $this->req->in('url_docroot') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">スタートページのURL</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('url_stargpage') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="url_stargpage" value="'.htmlspecialchars( $this->req->in('url_stargpage') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">デフォルトのファイル名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.text::text2html( $this->req->in('default_filename') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="default_filename" value="'.htmlspecialchars( $this->req->in('default_filename') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">URL変換時に省略するファイル名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.text::text2html( $this->req->in('omit_filename') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="omit_filename" value="'.htmlspecialchars( $this->req->in('omit_filename') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象外URLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.text::text2html( $this->req->in('urllist_outofsite') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="urllist_outofsite" value="'.htmlspecialchars( $this->req->in('urllist_outofsite') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">追加スタートページURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.text::text2html( $this->req->in('urllist_startpages') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="urllist_startpages" value="'.htmlspecialchars( $this->req->in('urllist_startpages') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">認証</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( ''=>'自動選択', 'basic'=>'ベーシック認証', 'digest'=>'ダイジェスト認証' );
		$RTN .= '			<div class="ttr">認証タイプ： '.htmlspecialchars( $label[$this->req->in('authentication_type')] ).'</div>'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('basic_authentication_id') ).' : '.htmlspecialchars( $this->req->in('basic_authentication_pw') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="authentication_type" value="'.htmlspecialchars( $this->req->in('authentication_type') ).'" />';
		$HIDDEN .= '<input type="hidden" name="basic_authentication_id" value="'.htmlspecialchars( $this->req->in('basic_authentication_id') ).'" />';
		$HIDDEN .= '<input type="hidden" name="basic_authentication_pw" value="'.htmlspecialchars( $this->req->in('basic_authentication_pw') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">パス指定変換</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( 'relative'=>'相対パス','absolute'=>'絶対パス','url'=>'URL','none'=>'変換しない' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('path_conv_method')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="path_conv_method" value="'.htmlspecialchars( $this->req->in('path_conv_method') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">サイト外指定パスの変換</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( '0'=>'パス指定変換設定に従う','1'=>'URLに変換する' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('outofsite2url_flg')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="outofsite2url_flg" value="'.htmlspecialchars( $this->req->in('outofsite2url_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">未定義のパラメータ</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( '0'=>'送信しない','1'=>'送信する' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('send_unknown_params_flg')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="send_unknown_params_flg" value="'.htmlspecialchars( $this->req->in('send_unknown_params_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">フォーム送信可否</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( '0'=>'送信しない','1'=>'送信する' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('send_form_flg')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="send_form_flg" value="'.htmlspecialchars( $this->req->in('send_form_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">HTML内のJavaScript</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( '0'=>'解析しない','1'=>'解析する' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('parse_jsinhtml_flg')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="parse_jsinhtml_flg" value="'.htmlspecialchars( $this->req->in('parse_jsinhtml_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">Not Found ページ収集</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$label = array( '0'=>'収集しない','1'=>'収集する' );
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $label[$this->req->in('save404_flg')] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="save404_flg" value="'.htmlspecialchars( $this->req->in('save404_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">複製先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('path_copyto') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="path_copyto" value="'.htmlspecialchars( $this->req->in('path_copyto') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '<div class="p AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $this->req->po() ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	新規プロジェクト作成/編集：チェック
	function check_edit_proj_check(){
		$RTN = array();
		if( !strlen( $this->req->in('project_id') ) ){
			$RTN['project_id'] = 'プロジェクトIDは必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('project_id') ) ){
			$RTN['project_id'] = 'プロジェクトIDに改行を含めることはできません。';
		}elseif( strlen( $this->req->in('project_id') ) > 64 ){
			$RTN['project_id'] = 'プロジェクトIDが長すぎます。';
		}elseif( !preg_match( '/^[a-z0-9\_\-\.\@]+$/' , $this->req->in('project_id') ) ){
			$RTN['project_id'] = 'プロジェクトIDに使用できない文字が含まれています。';
		}
		if( !strlen( $this->req->in('project_name') ) ){
			$RTN['project_name'] = 'プロジェクト名は必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('project_name') ) ){
			$RTN['project_name'] = 'プロジェクト名に改行を含めることはできません。';
		}elseif( strlen( $this->req->in('project_name') ) > 256 ){
			$RTN['project_name'] = 'プロジェクト名が長すぎます。';
		}

		if( !strlen( $this->req->in('default_filename') ) ){
			$RTN['default_filename'] = 'デフォルトのファイル名は必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('default_filename') ) ){
			$RTN['default_filename'] = 'デフォルトのファイル名に改行を含めることはできません。';
		}

		if( preg_match( '/\r\n|\r|\n/' , $this->req->in('omit_filename') ) ){
			$RTN['omit_filename'] = 'URL変換時に省略するファイル名に改行を含めることはできません。';
		}

		if( !strlen( $this->req->in('url_docroot') ) ){
			$RTN['url_docroot'] = 'ドキュメントルートURLは必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('url_docroot') ) ){
			$RTN['url_docroot'] = 'ドキュメントルートURLはに改行を含めることはできません。';
		}elseif( !text::is_url( $this->req->in('url_docroot') ) ){
			$RTN['url_docroot'] = 'ドキュメントルートURLの形式が不正です。';
		}
		if( !strlen( $this->req->in('url_stargpage') ) ){
			$RTN['url_stargpage'] = 'スタートページURLは必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('url_stargpage') ) ){
			$RTN['url_stargpage'] = 'ドキュメントルートURLはに改行を含めることはできません。';
		}elseif( !text::is_url( $this->req->in('url_stargpage') ) ){
			$RTN['url_stargpage'] = 'スタートページURLの形式が不正です。';
		}
		switch( $this->req->in('path_conv_method') ){
			case 'relative':
			case 'absolute':
			case 'url':
			case 'none':
				break;
			default:
				$RTN['path_conv_method'] = '選択できない値を選びました。';
				break;
		}
		if( strlen( $this->req->in('path_copyto') ) ){
			if( !is_dir( $this->req->in('path_copyto') ) ){
				$RTN['path_copyto'] = '複製先パスには、ディレクトリが存在している必要があります。';
			}elseif( !$this->dbh->check_rootdir( $this->req->in('path_copyto') ) ){
				$RTN['path_copyto'] = '複製先パスが、フレームワークの管理外のパスを指しています。';
			}
		}
		return	$RTN;
	}
	#--------------------------------------
	#	新規プロジェクト作成/編集：実行
	function execute_edit_proj_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return $this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();

		if( $this->cmd[0] == 'edit_proj' ){
			#	既存プロジェクトの編集
			$project_model->load_project( $this->cmd[1] );
		}elseif( $this->cmd[0] == 'create_proj' ){
			#	新規プロジェクト作成
			if( !$project_model->create_new_project( $this->req->in('project_id') ) ){
				return	'<p class="ttr error">新規プロジェクトの作成に失敗しました。</p>';
			}
		}

		$project_model->set_project_name( $this->req->in('project_name') );
		$project_model->set_url_startpage( $this->req->in('url_stargpage') );
		$project_model->set_url_docroot( $this->req->in('url_docroot') );
		$project_model->set_default_filename( $this->req->in('default_filename') );
		$project_model->set_omit_filename( $this->req->in('omit_filename') );
		$project_model->set_urllist_outofsite( $this->req->in('urllist_outofsite') );
		$project_model->set_urllist_startpages( $this->req->in('urllist_startpages') );
		$project_model->set_authentication_type( $this->req->in('authentication_type') );
		$project_model->set_basic_authentication_id( $this->req->in('basic_authentication_id') );
		$project_model->set_basic_authentication_pw( $this->req->in('basic_authentication_pw') );
		$project_model->set_path_conv_method( $this->req->in('path_conv_method') );
		$project_model->set_outofsite2url_flg( $this->req->in('outofsite2url_flg') );
		$project_model->set_send_unknown_params_flg( $this->req->in('send_unknown_params_flg') );
		$project_model->set_send_form_flg( $this->req->in('send_form_flg') );
		$project_model->set_parse_jsinhtml_flg( $this->req->in('parse_jsinhtml_flg') );
		$project_model->set_save404_flg( $this->req->in('save404_flg') );
		$project_model->set_path_copyto( $this->req->in('path_copyto') );

		#	出来上がったプロジェクトを保存
		if( !$project_model->save_project() ){
			return	'<p class="ttr error">プロジェクトの保存に失敗しました。</p>';
		}

		return $this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	新規プロジェクト作成/編集：完了
	function page_edit_proj_thanks(){
		$RTN = ''."\n";
		if( $this->cmd[0] == 'edit_proj' ){
			$RTN .= '<p class="ttr">プロジェクト編集処理を完了しました。</p>'."\n";
			$backTo = ':detail.'.$this->cmd[1];
		}else{
			$RTN .= '<p class="ttr">新規プロジェクトを作成しました。</p>'."\n";
			$backTo = ':';
		}
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $backTo ) ).'" method="post">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( $backTo )."\n";
		$RTN .= '	<p class="ttr"><input type="submit" value="戻る" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	プロジェクトをエクスポート
	function start_export(){
		$error = $this->check_export_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_export_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_export_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_export_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			$project_model = &$this->pcconf->factory_model_project();
			if( !count( $this->req->setin( 'project' ) ) ){
				$project_list = $project_model->get_project_list();
				$tmpAry = array();
				foreach( $project_list as $Line ){
					$tmpAry[$Line['id']] = 1;
				}
				$this->req->setin( 'project' , $tmpAry );
			}
		}
		return	$this->page_export_input( $error );
	}
	#--------------------------------------
	#	プロジェクトをエクスポート：入力
	function page_export_input( $error ){
		$project_model = &$this->pcconf->factory_model_project();
		$RTN = '';

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	必要事項を入力して、「確認する」ボタンをクリックしてください。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	<span class="must">*必須</span> が付いている項目は必ず入力してください。<br />'."\n";
		$RTN .= '</p>'."\n";

		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th width="30%"><div class="ttr">対象プロジェクト<span class="must">*必須</span></div></th>'."\n";
		$RTN .= '		<td width="70%">'."\n";
		$RTN .= '			<ul>'."\n";
		$project_list = $project_model->get_project_list();
		foreach( $project_list as $Line ){
			$in_project = $this->req->in('project');
			$c = array( 1=>' checked="checked"' );
			$RTN .= '				<li><label><input type="checkbox" name="project['.htmlspecialchars($Line['id']).']" value="1"'.$c[$in_project[$Line['id']]].' /> '.htmlspecialchars($Line['name']).' ('.htmlspecialchars($Line['id']).')</label></li>'."\n";
		}
		$RTN .= '			</ul>'."\n";
		if( strlen( $error['project'] ) ){
			$RTN .= '<div class="ttr error">'.$error['project'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th width="30%"><div class="ttr">圧縮形式<span class="must">*必須</span></div></th>'."\n";
		$RTN .= '		<td width="70%">'."\n";
		$is_zip = array();
		if( class_exists( 'ZipArchive' ) ){
			$is_zip['zip'] = true;
		}
		if( strlen( $this->conf->path_commands['tar'] ) ){
			$is_zip['tgz'] = true;
		}
		if( count( $is_zip ) ){
			$RTN .= '<ul class="none mt0 mb0">'."\n";
			$c = array( $this->req->in('ziptype').''=>' checked="checked"' );
			foreach( array_keys( $is_zip ) as $type ){
				$RTN .= '	<li class="ttr"><label><input type="radio" name="ziptype" value="'.htmlspecialchars( strtolower($type) ).'"'.$c[$type].' /> '.strtoupper($type).'形式</label></li>'."\n";
			}
			$RTN .= '</ul>'."\n";
			if( strlen( $error['ziptype'] ) ){
				$RTN .= '<div class="ttr error">'.$error['ziptype'].'</div>'."\n";
			}
		}else{
			#	圧縮解凍系機能が利用できなかったら
			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	<span class="error">圧縮機能がセットアップされていません</span>。<code>$conf->path_commands[\'tar\']</code>に、tarコマンドのパスを設定するか、PHPにZIPサポートをインストールしてください。。<br />'."\n";
			$RTN .= '</p>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="確認する" /></p>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $this->site->get_parent( $this->req->p() ) ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( $this->site->get_parent( $this->req->p() ) )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトをエクスポート：確認
	function page_export_confirm(){
		$project_model = &$this->pcconf->factory_model_project();

		$RTN = '';
		$HIDDEN = '';

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	入力内容に間違いがないことをご確認ください。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	よろしければ、「エクスポートする」ボタンをクリックしてください。<br />'."\n";
		$RTN .= '</p>'."\n";

		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th width="30%"><div class="ttr">対象プロジェクト</div></th>'."\n";
		$RTN .= '		<td width="70%">'."\n";
		$RTN .= '			<ul>'."\n";
		$project_list = $project_model->get_project_list();
		foreach( $project_list as $Line ){
			$in_project = $this->req->in('project');
			if( !$in_project[$Line['id']] ){ continue; }
			$RTN .= '				<li>'.htmlspecialchars($Line['name']).' ('.htmlspecialchars($Line['id']).')</li>'."\n";
			$HIDDEN .= '<input type="hidden" name="project['.htmlspecialchars($Line['id']).']" value="1" />';
		}
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th width="30%"><div class="ttr">圧縮形式</div></th>'."\n";
		$RTN .= '		<td width="70%">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( strtoupper( $this->req->in('ziptype') ) ).' 形式</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="ziptype" value="'.htmlspecialchars( $this->req->in('ziptype') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '<div class="AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="エクスポートする" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $this->site->get_parent( $this->req->p() ) ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( $this->site->get_parent( $this->req->p() ) )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトをエクスポート：チェック
	function check_export_check(){
		$RTN = array();
		if( !count( $this->req->in('project') ) ){
			$RTN['project'] = '対象プロジェクトを選択してください。';
		}
		if( !strlen( $this->req->in('ziptype') ) ){
			$RTN['ziptype'] = '圧縮形式を選択してください。';
		}else{
			$is_zip = array();
			if( class_exists( 'ZipArchive' ) ){
				$is_zip['zip'] = true;
			}
			if( strlen( $this->conf->path_commands['tar'] ) ){
				$is_zip['tgz'] = true;
			}
			if( !count( $is_zip ) ){
				$RTN['ziptype'] = '圧縮形式が選択できません。システムにインストールしてください。';
			}elseif( !$is_zip[$this->req->in('ziptype')] ){
				$RTN['ziptype'] = '対応していない圧縮形式です。';
			}
		}
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトをエクスポート：実行
	function execute_export_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$className = $this->dbh->require_lib('/plugins/PicklesCrawler/resources/io.php');
		if( !$className ){ return $this->theme->errorend('I/Oモジュールをロードできません。',__FILE__,__LINE__); }
		$io = new $className( $this->pcconf );

		if( !$this->dbh->lock() ){
			return $theme->errorend('アプリケーションがロックされています。しばらく時間をおいてから、もう一度操作してみてください。');
		}

		$path_export_archive = $io->mk_export_file( $this->req->in('ziptype') , array( 'project'=>$this->req->in('project') ) );
		if( $path_export_archive === false ){
			$this->dbh->unlock();
			$this->errors->error_log( 'アーカイブの作成に失敗しました。' , __FILE__ , __LINE__ );
			return	'<p class="ttr error">アーカイブの作成に失敗しました。</p>';
		}

		$this->dbh->unlock();

		$result = $this->theme->flush_file( $path_export_archive , array( 'filename'=>basename($path_export_archive) , 'delete'=>true ) );

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	プロジェクトをエクスポート：完了
	function page_export_thanks(){
		$RTN = '';
		$RTN .= '<p class="ttr">プロジェクトをエクスポート処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $this->site->get_parent( $this->req->p() ) ) ).'" method="post">'."\n";
		$RTN .= '	<p class="ttr"><input type="submit" value="戻る" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( $this->site->get_parent( $this->req->p() ) )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	プロジェクトの削除
	function start_delete_proj(){
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_delete_proj_thanks();
		}elseif( $this->req->in('mode') == 'execute' ){
			return	$this->execute_delete_proj_execute();
		}
		return	$this->page_delete_proj_confirm();
	}
	#--------------------------------------
	#	プロジェクトの削除：確認
	function page_delete_proj_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		$RTN .= '<p class="ttr">プロジェクトを削除します。</p>'."\n";
		$RTN .= '<p class="ttr">よろしいですか？</p>'."\n";

		$RTN .= '<div class="p AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="削除する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr" align="center"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトの削除：実行
	function execute_delete_proj_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		if( !strlen( $this->cmd[1] ) ){
			return	'<p class="ttr error">プロジェクトが選択されていません。</p>';
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );

		$result = $project_model->destroy_project();

		if( !$result ){
			return	'<p class="ttr error">プロジェクトの削除に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	プロジェクトの削除：完了
	function page_delete_proj_thanks(){
		$RTN = ''."\n";
		$RTN .= '<p class="ttr">プロジェクトの削除処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':' ) ).'" method="post">'."\n";
		$RTN .= '	<p class="ttr"><input type="submit" value="戻る" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':' )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}




	###################################################################################################################
	#	新規プログラム作成/編集
	function start_edit_program(){
		if( $this->cmd[0] == 'edit_program' ){
			if( !strlen( $this->cmd[1] ) || !strlen( $this->cmd[2] ) ){
				return $this->theme->errorend('編集対象のプログラムが指定されていません。');
			}
		}
		$error = $this->check_edit_program_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_program_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_program_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_program_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			if( $this->cmd[0] == 'edit_program' ){
				$project_model = &$this->pcconf->factory_model_project();
				$project_model->load_project( $this->cmd[1] );
				$program_model = &$project_model->factory_program( $this->cmd[2] );
				$this->req->setin( 'program_name' , $program_model->get_program_name() );
				$this->req->setin( 'program_param' , $program_model->get_program_param() );
				$this->req->setin( 'program_type' , $program_model->get_program_type() );
				$this->req->setin( 'program_useragent' , $program_model->get_program_useragent() );
				$this->req->setin( 'path_copyto' , $program_model->get_path_copyto() );//10:54 2009/08/27 追加
				$this->req->setin( 'copyto_apply_deletedfile_flg' , $program_model->get_copyto_apply_deletedfile_flg() );//10:54 2009/08/27 追加

				$urllist_scope = $program_model->get_urllist_scope();
				$str_urllist = '';
				foreach( $urllist_scope as $url ){
					$str_urllist .= $url."\n";
				}
				$this->req->setin( 'urllist_scope' , $str_urllist );

				$urllist_nodownload = $program_model->get_urllist_nodownload();
				$str_urllist = '';
				foreach( $urllist_nodownload as $url ){
					$str_urllist .= $url."\n";
				}
				$this->req->setin( 'urllist_nodownload' , $str_urllist );
			}else{
				#	デフォルト値を設定
				$this->req->setin( 'program_name' , 'New Program' );
				$this->req->setin( 'program_useragent' , 'PicklesCrawler' );
			}
		}
		return	$this->page_edit_program_input( $error );
	}
	#--------------------------------------
	#	新規プログラム作成/編集：入力
	function page_edit_program_input( $error ){
		$RTN = ''."\n";

		$RTN .= '<p class="ttr">プログラムの設定情報を入力して、「確認する」をクリックしてください。<span class="must">*</span>印がついている項目は必ず入力してください。</p>'."\n";

		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プログラム名 <span class="must">*</span></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="program_name" value="'.htmlspecialchars( $this->req->in('program_name') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['program_name'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['program_name'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
/*
		#	2:10 2007/12/31
		#	プログラムタイプは、snapshot以外は実装しない方針にしました。よって、選択もできなくなりました。
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プログラムタイプ</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$c = array( $this->req->in('program_type') => ' selected="selected"' );
		$RTN .= '			<div class="ttr">'."\n";
		$RTN .= '				<select name="program_type">'."\n";
		$RTN .= '					<option value=""'.$c[''].'>選択してください。</option>'."\n";
		$RTN .= '					<option value="snapshot"'.$c['snapshot'].'>スナップショット</option>'."\n";
		$RTN .= '					<option value="searchindex"'.$c['searchindex'].'>検索用インデックス生成</option>'."\n";
		$RTN .= '					<option value="import2picklesframework"'.$c['import2picklesframework'].'>PicklesFrameworkへのインポート</option>'."\n";
		$RTN .= '					<option value="cratepdf"'.$c['cratepdf'].'>PDFドキュメント生成</option>'."\n";
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error{'program_type'} ) ){
			$RTN .= '			<div class="ttr error">'.$error{'program_type'}.'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
*/
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">常に送信するパラメータ</div></th>'."\n";//PicklesCrawler 0.3.0 追加
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="program_param" value="'.htmlspecialchars( $this->req->in('program_param') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['program_param'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['program_param'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">HTTP_USER_AGENT</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="program_useragent" value="'.htmlspecialchars( $this->req->in('program_useragent') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['program_useragent'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['program_useragent'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象範囲とするURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><textarea name="urllist_scope" rows="7" class="inputitems">'.htmlspecialchars( $this->req->in('urllist_scope') ).'</textarea></div>'."\n";
		if( strlen( $error['urllist_scope'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['urllist_scope'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※プロトコル部(http://またはhttps://)から始まる完全なURLで指定してください。</li>'."\n";
		$RTN .= '				<li class="ttrs">※改行区切りで複数登録することができます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※アスタリスク(*)記号でワイルドカードを表現できます。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ダウンロードしないURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><textarea name="urllist_nodownload" rows="7" class="inputitems">'.htmlspecialchars( $this->req->in('urllist_nodownload') ).'</textarea></div>'."\n";
		if( strlen( $error['urllist_nodownload'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['urllist_nodownload'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※プロトコル部(http://またはhttps://)から始まる完全なURLで指定してください。</li>'."\n";
		$RTN .= '				<li class="ttrs">※改行区切りで複数登録することができます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※アスタリスク(*)記号でワイルドカードを表現できます。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">複製先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="path_copyto" value="'.htmlspecialchars( $this->req->in('path_copyto') ).'" class="inputitems" /></div>'."\n";
		if( strlen( $error['path_copyto'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['path_copyto'].'</div>'."\n";
		}
		$c = array( '1'=>' checked="checked"' );
		$RTN .= '			<div class="ttr"><label><input type="checkbox" name="copyto_apply_deletedfile_flg" value="1"'.htmlspecialchars( $c[$this->req->in('copyto_apply_deletedfile_flg')] ).' /> 削除されたファイルを反映する</label></div>'."\n";
		if( strlen( $error['copyto_apply_deletedfile_flg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['copyto_apply_deletedfile_flg'].'</div>'."\n";
		}
		$RTN .= '			<ul class="annotation">'."\n";
		$RTN .= '				<li class="ttrs">※プロジェクトに設定された「複製先パス」を上書きします。ここで空白を設定すると、プロジェクトの「複製先パス」が採用されます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※複製先パスは、既に存在するパスである必要があります。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="確認する" /></p>'."\n";
		$RTN .= '	<input type="hidden" name="program_type" value="snapshot" />'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	新規プログラム作成/編集：確認
	function page_edit_program_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		$RTN .= '<p class="ttr">入力したプログラムの設定情報を確認してください。</p>'."\n";

		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プログラム名</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('program_name') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="program_name" value="'.htmlspecialchars( $this->req->in('program_name') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">常に送信するパラメータ</div></th>'."\n";//PicklesCrawler 0.3.0
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('program_param') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="program_param" value="'.htmlspecialchars( $this->req->in('program_param') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
/*
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">プログラムタイプ</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('program_type') ).'</div>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
*/
		$HIDDEN .= '<input type="hidden" name="program_type" value="'.htmlspecialchars( $this->req->in('program_type') ).'" />';
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">HTTP_USER_AGENT</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('program_useragent') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="program_useragent" value="'.htmlspecialchars( $this->req->in('program_useragent') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象範囲とするURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$inputvalueList = preg_split( '/\r\n|\r|\n/' , $this->req->in('urllist_scope') );
		foreach( $inputvalueList as $key=>$val ){
			$val = trim($val);
			if( !strlen( $val ) ){
				unset( $inputvalueList[$key] ); continue;
			}
		}
		if( count( $inputvalueList ) ){
			$RTN .= '			<ul>'."\n";
			foreach( $inputvalueList as $val ){
				$RTN .= '				<li class="ttr">'.htmlspecialchars( $val ).'</li>'."\n";
			}
			$RTN .= '			</ul>'."\n";
		}else{
			$RTN .= '			<div class="ttrs">指定されていません。</div>'."\n";
		}
		$HIDDEN .= '<input type="hidden" name="urllist_scope" value="'.htmlspecialchars( $this->req->in('urllist_scope') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ダウンロードしないURLリスト</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$inputvalueList = preg_split( '/\r\n|\r|\n/' , $this->req->in('urllist_nodownload') );
		foreach( $inputvalueList as $key=>$val ){
			$val = trim($val);
			if( !strlen( $val ) ){
				unset( $inputvalueList[$key] ); continue;
			}
		}
		if( count( $inputvalueList ) ){
			$RTN .= '			<ul>'."\n";
			foreach( $inputvalueList as $val ){
				$RTN .= '				<li class="ttr">'.htmlspecialchars( $val ).'</li>'."\n";
			}
			$RTN .= '			</ul>'."\n";
		}else{
			$RTN .= '			<div class="ttrs">指定されていません。</div>'."\n";
		}
		$HIDDEN .= '<input type="hidden" name="urllist_nodownload" value="'.htmlspecialchars( $this->req->in('urllist_nodownload') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">複製先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		if( strlen( $this->req->in('path_copyto') ) ){
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('path_copyto') ).'</div>'."\n";
		}else{
			$RTN .= '			<div class="ttr">---</div>'."\n";
		}
		$HIDDEN .= '<input type="hidden" name="path_copyto" value="'.htmlspecialchars( $this->req->in('path_copyto') ).'" />';
		$c = array( '1'=>'する' , '0'=>'しない' );
		$RTN .= '			<div class="ttr">削除されたファイルを反映'.htmlspecialchars( $c[intval($this->req->in('copyto_apply_deletedfile_flg'))] ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="copyto_apply_deletedfile_flg" value="'.htmlspecialchars( $this->req->in('copyto_apply_deletedfile_flg') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '<div class="p AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	新規プログラム作成/編集：チェック
	function check_edit_program_check(){
		$RTN = array();
		if( !strlen( $this->req->in('program_name') ) ){
			$RTN['program_name'] = 'プログラム名は必須項目です。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('program_name') ) ){
			$RTN['program_name'] = 'プログラム名に改行を含むことはできません。';
		}
		if( preg_match( '/\r\n|\r|\n/' , $this->req->in('program_useragent') ) ){
			$RTN['program_useragent'] = 'ユーザエージェントに改行を含むことはできません。';
		}
		if( !strlen( $this->req->in('program_type') ) ){
			$RTN['program_type'] = 'タイプを選択してください。';
		}elseif( preg_match( '/\r\n|\r|\n/' , $this->req->in('program_type') ) ){
			$RTN['program_type'] = 'タイプに改行を含むことはできません。';
		}
		if( strlen( $this->req->in('path_copyto') ) ){
			if( !is_dir( $this->req->in('path_copyto') ) ){
				$RTN['path_copyto'] = '複製先パスには、ディレクトリが存在している必要があります。';
			}elseif( !$this->dbh->check_rootdir( $this->req->in('path_copyto') ) ){
				$RTN['path_copyto'] = '複製先パスが、フレームワークの管理外のパスを指しています。';
			}
		}
		return	$RTN;
	}
	#--------------------------------------
	#	新規プログラム作成/編集：実行
	function execute_edit_program_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );

		if( $this->cmd[0] == 'edit_program' ){
			$program_model = &$project_model->factory_program( $this->cmd[2] );
		}else{
			$program_model = &$project_model->factory_program();
		}

		$program_model->set_program_name( $this->req->in('program_name') );
		$program_model->set_program_param( $this->req->in('program_param') );//PicklesCrawler 0.3.0
		$program_model->set_program_type( $this->req->in('program_type') );
		$program_model->set_program_useragent( $this->req->in('program_useragent') );
		$program_model->set_path_copyto( $this->req->in('path_copyto') );//10:56 2009/08/27 PicklesCrawler 0.3.3 追加
		$program_model->set_copyto_apply_deletedfile_flg( $this->req->in('copyto_apply_deletedfile_flg') );//10:56 2009/08/27 PicklesCrawler 0.3.3 追加
		$program_model->set_urllist_scope( $this->req->in('urllist_scope') );
		$program_model->set_urllist_nodownload( $this->req->in('urllist_nodownload') );


		#	出来上がったプログラムを保存
		if( !$program_model->save_program() ){
			return	'<p class="ttr error">プログラムの保存に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks&program_id='.urlencode($program_model->get_program_id()) );
	}
	#--------------------------------------
	#	新規プログラム作成/編集：完了
	function page_edit_program_thanks(){
		$RTN = ''."\n";
		if( $this->cmd[0] == 'edit_program' ){
			$RTN .= '<p class="ttr">プログラム '.htmlspecialchars( $this->req->in('program_id') ).' の編集処理を保存しました。</p>';
		}else{
			$RTN .= '<p class="ttr">新規プログラム '.htmlspecialchars( $this->req->in('program_id') ).' を作成しました。</p>';
		}
		$RTN .= '<div class="p">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':execute_program.'.$this->cmd[1].'.'.urlencode( $this->req->in('program_id') ) ) ).'" method="post">'."\n";
		$RTN .= '	<div class="inline">'."\n";
		$RTN .= '		<input type="submit" value="実行する" />'."\n";
		$RTN .= '		'.$this->theme->mk_form_defvalues( ':execute_program.'.$this->cmd[1].'.'.urlencode( $this->req->in('program_id') ) )."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<div class="inline">'."\n";
		$RTN .= '		<input type="submit" value="戻る" />'."\n";
		$RTN .= '		'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		return	$RTN;
	}



	###################################################################################################################
	#	プロジェクトのパラメータ定義を編集
	function start_edit_param_define(){

		$in = $this->req->in();

		$param_list = array();
		foreach( $in as $key=>$val ){
			if( !preg_match( '/^param:(.+?):(.+)$/' , $key , $matches ) ){
				continue;
			}
			if( !strlen( $this->req->in( 'param:'.$matches[1].':key' ) ) ){
				#	パラメータの削除処理
				continue;
			}
			if( $matches[1] != $this->req->in( 'param:'.$matches[1].':key' ) ){

				#	パラメータキーの変更処理
				$new_key = $this->req->in( 'param:'.$matches[1].':key' );
				$this->req->setin( 'param:'.$new_key.':key' , $new_key );
				$this->req->setin( 'param:'.$new_key.':name' , $this->req->in( 'param:'.$matches[1].':name' ) );
				$this->req->setin( 'param:'.$new_key.':request' , $this->req->in( 'param:'.$matches[1].':request' ) );
				$this->req->setin( 'param:'.$matches[1].':key' , '' );
				$this->req->setin( 'param:'.$matches[1].':name' , '' );
				$this->req->setin( 'param:'.$matches[1].':request' , '' );
				$matches[1] = $new_key;
			}

			$param_list[$matches[1]] = true;
		}


		if( strlen( $this->req->in('newparam:key') ) ){
			#	新しいパラメータの追加処理
			$this->req->setin( 'param:'.$this->req->in('newparam:key').':key' , $this->req->in('newparam:key') );
			$this->req->setin( 'param:'.$this->req->in('newparam:key').':name' , $this->req->in('newparam:name') );
			$this->req->setin( 'param:'.$this->req->in('newparam:key').':request' , $this->req->in('newparam:request') );
			$param_list[$this->req->in('newparam:key')] = true;
		}

		$param_list = array_keys( $param_list );
		sort( $param_list );

		$error = $this->check_edit_param_define_check( $param_list );
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_param_define_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_param_define_confirm( $param_list );
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_param_define_execute( $param_list );
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			$project_model = &$this->pcconf->factory_model_project();
			$project_model->load_project( $this->cmd[1] );
			$param_list = $project_model->get_param_define_list( $this->cmd[2] );
			if( is_array( $param_list ) && count( $param_list ) ){
				foreach( $param_list as $param_name ){
					$param_info = $project_model->get_param_define( $param_name );
					$this->req->setin( 'param:'.$param_name.':key' , $param_name );
					foreach( $param_info as $info_key=>$info_val ){
						$this->req->setin( 'param:'.$param_name.':'.$info_key , $info_val );
					}
				}
			}

			$in = $this->req->in();

			$param_list = array();
			foreach( $in as $key=>$val ){
				if( !preg_match( '/^param:(.+?):(.+)$/' , $key , $matches ) ){
					continue;
				}
				$param_list[$matches[1]] = true;
			}
			$param_list = array_keys( $param_list );


		}
		return	$this->page_edit_param_define_input( $error , $param_list );
	}
	#--------------------------------------
	#	プロジェクトのパラメータ定義を編集：入力
	function page_edit_param_define_input( $error , $param_list ){

		$RTN = ''."\n";
		$HIDDEN = ''."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		if( is_array( $param_list ) && count( $param_list ) ){
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<thead>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;">'."\n";
			$RTN .= '			<div class="ttr">物理名</div>'."\n";
			$RTN .= '		</th>'."\n";
			$RTN .= '		<th width="70%">'."\n";
			$RTN .= '			<div class="ttr">論理名/送信設定</div>'."\n";
			$RTN .= '		</th>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	</thead>'."\n";
			foreach( $param_list as $param_name ){
				$RTN .= '	<tr>'."\n";
				$RTN .= '		<th style="width:30%;">'."\n";
				$RTN .= '			<div class="ttr"><input type="text" name="param:'.$param_name.':key" value="'.htmlspecialchars( $this->req->in('param:'.$param_name.':key') ).'" class="inputitems" /></div>'."\n";
				$RTN .= '		</th>'."\n";
				$RTN .= '		<td style="width:70%;">'."\n";
				$RTN .= '			<div class="ttr"><input type="text" name="param:'.$param_name.':name" value="'.htmlspecialchars( $this->req->in('param:'.$param_name.':name') ).'" class="inputitems" /></div>'."\n";
				$check = array( 1=>' checked="checked"' );
				$RTN .= '			<div class="ttr"><input type="checkbox" name="param:'.$param_name.':request" id="param:'.$param_name.':request" value="1"'.$check[$this->req->in('param:'.$param_name.':request')].' /><label for="param:'.$param_name.':request">リクエストに含める</label></div>'."\n";
				if( strlen( $error{'param:'.$param_name} ) ){
					$RTN .= '			<div class="ttr error">'.$error{'param:'.$param_name}.'</div>'."\n";
				}
				$RTN .= '		</td>'."\n";
				$RTN .= '	</tr>'."\n";

			}
			$RTN .= '</table>'."\n";
		}

		$RTN .= '<p class="ttr">新しいパラメータを作成する場合は、ここに記入してください。</p>'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<thead>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;">'."\n";
		$RTN .= '			<div class="ttr">物理名</div>'."\n";
		$RTN .= '		</th>'."\n";
		$RTN .= '		<th width="70%">'."\n";
		$RTN .= '			<div class="ttr">論理名/送信設定</div>'."\n";
		$RTN .= '		</th>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	</thead>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr"><input type="text" name="newparam:key" value="" class="inputitems" /></div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="newparam:name" value="" class="inputitems" /></div>'."\n";
		$RTN .= '			<div class="ttr"><input type="checkbox" name="newparam:request" id="newparam:request" value="1" /><label for="newparam:request">リクエストに含める</label></div>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '	<p class="ttr">これでいい場合は「確認する」を、さらに追加する場合は「画面を更新」をクリックしてください。</p>'."\n";
		$RTN .= '	<div class="AlignC">'."\n";
		$RTN .= '		<input type="submit" value="確認する" />'."\n";
		$RTN .= '		<input type="submit" value="画面を更新" onclick="document.getElementById(\'pc_document_form_mode\').value=\'input\'; return true;" />'."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" id="pc_document_form_mode" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトのパラメータ定義を編集：確認
	function page_edit_param_define_confirm( $param_list ){

		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		if( is_array( $param_list ) && count( $param_list ) ){
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<thead>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;">'."\n";
			$RTN .= '			<div class="ttr">物理名</div>'."\n";
			$RTN .= '		</th>'."\n";
			$RTN .= '		<th width="70%">'."\n";
			$RTN .= '			<div class="ttr">論理名/送信設定</div>'."\n";
			$RTN .= '		</th>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	</thead>'."\n";
			foreach( $param_list as $param_name ){
				$RTN .= '	<tr>'."\n";
				$RTN .= '		<th style="width:30%;"><div class="ttr">'.htmlspecialchars( $this->req->in('param:'.$param_name.':key') ).'</div></th>'."\n";
				$HIDDEN .= '<input type="hidden" name="param:'.$param_name.':key" value="'.htmlspecialchars( $this->req->in('param:'.$param_name.':key') ).'" />';
				$RTN .= '		<td style="width:70%;">'."\n";
				$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('param:'.$param_name.':name') ).'</div>'."\n";
				$HIDDEN .= '<input type="hidden" name="'.htmlspecialchars( 'param:'.$param_name.':name' ).'" value="'.htmlspecialchars( $this->req->in('param:'.$param_name.':name') ).'" />';
				if( $this->req->in('param:'.$param_name.':request') ){
					$RTN .= '			<div class="ttr">リクエストに含める</div>'."\n";
				}else{
					$RTN .= '			<div class="ttr">リクエストに含めない</div>'."\n";
				}
				$HIDDEN .= '<input type="hidden" name="'.htmlspecialchars( 'param:'.$param_name.':request' ).'" value="'.htmlspecialchars( $this->req->in('param:'.$param_name.':request') ).'" />';
				$RTN .= '		</td>'."\n";
				$RTN .= '	</tr>'."\n";
			}
			$RTN .= '</table>'."\n";
		}else{
			$RTN .= '<p class="ttr">パラメータを定義しない。</p>'."\n";
		}

		$RTN .= '<p class="ttr">この設定でよろしければ、「保存する」をクリックしてください。</p>'."\n";

		$RTN .= '<div class="AlignC p">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトのパラメータ定義を編集：チェック
	function check_edit_param_define_check( $param_list ){
		$RTN = array();
		return	$RTN;
	}
	#--------------------------------------
	#	プロジェクトのパラメータ定義を編集：実行
	function execute_edit_param_define_execute( $param_list ){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$project_model->clear_param_define();

		foreach( $param_list as $param_key ){
			$project_model->set_param_define( $param_key , 'name' , $this->req->in('param:'.$param_key.':name') );
			$project_model->set_param_define( $param_key , 'request' , $this->req->in('param:'.$param_key.':request') );
		}

		$result = $project_model->save_project();
		if( !$result ){
			return	'<p class="ttr error">保存に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	プロジェクトのパラメータ定義を編集：完了
	function page_edit_param_define_thanks(){
		$RTN = ''."\n";
		$RTN .= '<p class="ttr">プロジェクトのパラメータ定義を編集処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}



	###################################################################################################################
	#	保存ファイル名のリライトルール編集
	function start_edit_localfilename_rewriterules(){
		if( strlen( $this->req->in('add:before') ) ){
			for( $i = 1; strlen( $this->req->in('p'.$i.':after') ); $i ++ ){;}
			$this->req->setin( 'p'.$i.':priority' , $i );
			$this->req->setin( 'p'.$i.':before' , $this->req->in('add:before') );
			$this->req->setin( 'p'.$i.':requiredparam' , $this->req->in('add:requiredparam') );
			$this->req->setin( 'p'.$i.':after' , $this->req->in('add:after') );
		}

		$error = $this->check_edit_localfilename_rewriterules_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_localfilename_rewriterules_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_localfilename_rewriterules_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_localfilename_rewriterules_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			$project_model = &$this->pcconf->factory_model_project();
			$project_model->load_project( $this->cmd[1] );
			$rule_list = $project_model->get_localfilename_rewriterules();
			if( is_array( $rule_list ) && count( $rule_list ) ){
				$i = 0;
				foreach( $rule_list as $Line ){
					$i ++;
					$this->req->setin( 'p'.$i.':priority' , $Line['priority'] );
					$this->req->setin( 'p'.$i.':before' , $Line['before'] );
					$this->req->setin( 'p'.$i.':requiredparam' , $Line['requiredparam'] );
					$this->req->setin( 'p'.$i.':after' , $Line['after'] );
				}
			}
		}
		return	$this->page_edit_localfilename_rewriterules_input( $error );
	}
	#--------------------------------------
	#	保存ファイル名のリライトルール編集：入力
	function page_edit_localfilename_rewriterules_input( $error ){
		$RTN = ''."\n";

		$RTN .= '<script type="text/javascript">'."\n";
		$RTN .= '	function up_item(num){'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').operation_up.value=num;'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').mode.value=\'input\';'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').submit();'."\n";
		$RTN .= '	}'."\n";
		$RTN .= '	function down_item(num){'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').operation_down.value=num;'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').mode.value=\'input\';'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').submit();'."\n";
		$RTN .= '	}'."\n";
		$RTN .= '</script>'."\n";

		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post" id="cont_op_document_form">'."\n";
		$RTN .= '<div class="unit_pane2">'."\n";
		$RTN .= '	<div class="pane2L">'."\n";
		$RTN .= '		<p class="ttr">'."\n";
		$RTN .= '			保存ファイル名の変換ルールを設定してください。<br />'."\n";
		$RTN .= '		</p>'."\n";

		$entry_list = array();
		for( $i = 1; strlen( $this->req->in('p'.$i.':after') ); $i ++ ){
			$MEMO = array();
			$MEMO['priority']		= $i;
			$MEMO['before']			= $this->req->in( 'p'.$i.':before' );
			$MEMO['requiredparam']	= $this->req->in( 'p'.$i.':requiredparam' );
			$MEMO['after']			= $this->req->in( 'p'.$i.':after' );
			array_push( $entry_list , $MEMO );
		}

		if( strlen( $this->req->in('operation_up') ) && $this->req->in('operation_up') > 1 ){
			foreach( $entry_list as $key=>$line ){
				if( $line['priority'] == intval( $this->req->in('operation_up') ) ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_up') )-1;
					continue;
				}elseif( $line['priority'] == intval($this->req->in('operation_up'))-1 ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_up') );
					continue;
				}
			}
		}elseif( strlen( $this->req->in('operation_down') ) && $this->req->in('operation_down') < count( $entry_list ) ){
			foreach( $entry_list as $key=>$line ){
				if( $line['priority'] == intval( $this->req->in('operation_down') ) ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_down') )+1;
					continue;
				}elseif( $line['priority'] == intval($this->req->in('operation_down'))+1 ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_down') );
					continue;
				}
			}
		}

		usort( $entry_list , create_function( '$a,$b' , 'if( $a[\'priority\'] > $b[\'priority\'] ){ return 1; } if( $a[\'priority\'] < $b[\'priority\'] ){ return -1; } return 0;' ) );

		foreach( $entry_list as $line ){
			$btn_operation_up = '<a href="javascript:up_item('.text::data2text( $line['priority'] ).');">上へ</a>';
			$btn_operation_down = '<a href="javascript:down_item('.text::data2text( $line['priority'] ).');">下へ</a>';

			$RTN .= ''.$this->theme->mk_hx( '優先度['.$line['priority'].'] <span style="font-weight:normal;">'.$btn_operation_up.' '.$btn_operation_down.'</span>' , null , array( 'allow_html'=>true ) ).''."\n";
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">元のパス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':before" value="'.htmlspecialchars( $line['before'] ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':before'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':before'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">変換後の保存先パス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':after" value="'.htmlspecialchars( $line['after'] ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':after'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':after'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">必須URLパラメータ</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':requiredparam" value="'.htmlspecialchars( $line['requiredparam'] ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':requiredparam'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':requiredparam'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '</table>'."\n";
		}

		$RTN .= $this->theme->mk_hr()."\n";

		$RTN .= ''.$this->theme->mk_hx( '条件を追加' ).''."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">元のパス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:before" value="" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		if( strlen( $error['add:before'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:before'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">変換後の保存先パス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:after" value="" class="inputitems" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		if( strlen( $error{'add:after'} ) ){
			$RTN .= '			<div class="ttr error">'.$error{'add:after'}.'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">必須URLパラメータ</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:requiredparam" value="" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		if( strlen( $error{'add:requiredparam'} ) ){
			$RTN .= '			<div class="ttr error">'.$error{'add:requiredparam'}.'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '	</div>'."\n";
		$RTN .= '	<div class="pane2R">'."\n";
		$RTN .= '		<ul>'."\n";
		$RTN .= '			<li class="ttr">'."\n";
		$RTN .= '				元のパス、変換後の保存先パスは、スラッシュから始まる絶対パスで指定してください。先頭のスラッシュは、常にドメイン名の直後の階層に当たります。<br />'."\n";
		$RTN .= '			</li>'."\n";
		$RTN .= '			<li class="ttr">'."\n";
		$RTN .= '				元のパスでは、アスタリスク(*)記号でワイルドカードを表現できます。<br />'."\n";
		$RTN .= '			</li>'."\n";
		$RTN .= '			<li>'."\n";
		$RTN .= '				<p class="ttr">'."\n";
		$RTN .= '					変換後の保存先パスでは、次の特殊変数を利用できます。<br />'."\n";
		$RTN .= '				</p>'."\n";
		$RTN .= '				<dl>'."\n";
		$RTN .= '					<dt class="ttr">{$param.XXXXX}</dt>'."\n";
		$RTN .= '						<dd class="ttr">URLパラメータ($_POST/$_GET)から、キー「XXXXX」で得られた値。</dd>'."\n";
		$RTN .= '					<dt class="ttr">{$dirname}</dt>'."\n";
		$RTN .= '						<dd class="ttr">本来のパスから、ファイル名を取り除いた値。</dd>'."\n";
		$RTN .= '					<dt class="ttr">{$basename}</dt>'."\n";
		$RTN .= '						<dd class="ttr">本来のパスから、ファイル名だけを取り出した値。</dd>'."\n";
		$RTN .= '					<dt class="ttr">{$extension}</dt>'."\n";
		$RTN .= '						<dd class="ttr">本来のパスから、拡張子部分だけを取り出した値。</dd>'."\n";
		$RTN .= '					<dt class="ttr">{$basename_body}</dt>'."\n";
		$RTN .= '						<dd class="ttr">{$basename}から、拡張子部分を取り除いた値。</dd>'."\n";
		$RTN .= '					<dt class="ttr">{$wildcard.XXXXX}</dt>'."\n";
		$RTN .= '						<dd class="ttr">ワイルドカード「*(アスタリスク)」を指定したうち、キー「XXXXX」番目(1から数える)にマッチした値。</dd>'."\n";
		$RTN .= '				</dl>'."\n";
		$RTN .= '			</li>'."\n";
		$RTN .= '	</ul>'."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '</div>'."\n";

		$RTN .= '	<p class="ttr">これでいい場合は「確認する」を、さらに追加する場合は「画面を更新」をクリックしてください。</p>'."\n";
		$RTN .= '	<div class="AlignC">'."\n";
		$RTN .= '		<input type="submit" value="確認する" />'."\n";
		$RTN .= '		<input type="submit" value="画面を更新" onclick="document.getElementById(\'cont_op_document_form\').mode.value=\'input\';return true;" />'."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	<input type="hidden" name="operation_up" value="" />'."\n";
		$RTN .= '	<input type="hidden" name="operation_down" value="" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	保存ファイル名のリライトルール編集：確認
	function page_edit_localfilename_rewriterules_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		for( $i = 1; strlen( $this->req->in('p'.$i.':after') ); $i ++ ){
			$RTN .= ''.$this->theme->mk_hx('優先度['.$i.']').''."\n";
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">元のパス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':before') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':before" value="'.htmlspecialchars( $this->req->in('p'.$i.':before') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">変換後の保存先パス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':after') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':after" value="'.htmlspecialchars( $this->req->in('p'.$i.':after') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">必須URLパラメータ</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':requiredparam') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':requiredparam" value="'.htmlspecialchars( $this->req->in('p'.$i.':requiredparam') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '</table>'."\n";
		}

		$RTN .= '<div class="p AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	保存ファイル名のリライトルール編集：チェック
	function check_edit_localfilename_rewriterules_check(){
		$RTN = array();
		return	$RTN;
	}
	#--------------------------------------
	#	保存ファイル名のリライトルール編集：実行
	function execute_edit_localfilename_rewriterules_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$project_model->clear_localfilename_rewriterules();

		$rules = array();
		for( $i = 1; strlen( $this->req->in('p'.$i.':after') ); $i ++ ){
			$MEMO = array();
			$MEMO['priority'] = $i;
			$MEMO['before'] = $this->req->in('p'.$i.':before');
			$MEMO['requiredparam'] = $this->req->in('p'.$i.':requiredparam');
			$MEMO['after'] = $this->req->in('p'.$i.':after');
			array_push( $rules , $MEMO );
			unset( $MEMO );
		}
		$project_model->set_localfilename_rewriterules( $rules );

		$result = $project_model->save_project();
		if( !$result ){
			return	'<p class="ttr error">プロジェクト情報の保存に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	保存ファイル名のリライトルール編集：完了
	function page_edit_localfilename_rewriterules_thanks(){
		$RTN = ''."\n";
		$RTN .= '<p class="ttr">保存ファイル名のリライトルール編集処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	文字コード・改行コード変換設定編集
	function start_edit_charset(){
		$error = $this->check_edit_charset_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_charset_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_charset_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_charset_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			$project_model = &$this->pcconf->factory_model_project();
			$project_model->load_project( $this->cmd[1] );

			$this->req->setin( 'charset' ,$project_model->get_charset_charset() );
			$this->req->setin( 'crlf' , $project_model->get_charset_crlf() );
			$this->req->setin( 'ext' , $project_model->get_charset_ext() );

		}
		return	$this->page_edit_charset_input( $error );
	}
	#--------------------------------------
	#	文字コード・改行コード変換設定編集：入力
	function page_edit_charset_input( $error ){
		$charsetList = array( 'UTF-8' , 'Shift_JIS' , 'EUC-JP' , 'JIS' );
		$crlfList = array( 'CRLF' , 'CR' , 'LF' );
		$RTN = '';

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	文字コードの変換設定を編集します。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	この設定により、収集したファイルの文字コードと改行コードを一律整形することができます。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">文字コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('charset')=>' selected="selected"' );
		$RTN .= '				<select name="charset">'."\n";
		$RTN .= '					<option value=""'.$c[''].'>変換しない</option>'."\n";
		foreach( $charsetList as $charset ){
			$RTN .= '					<option value="'.htmlspecialchars( $charset ).'"'.$c[$charset].'>'.htmlspecialchars( $charset ).'</option>'."\n";
		}
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['charset'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['charset'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">改行コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'."\n";
		$c = array( $this->req->in('crlf')=>' selected="selected"' );
		$RTN .= '				<select name="crlf">'."\n";
		$RTN .= '					<option value=""'.$c[''].'>変換しない</option>'."\n";
		foreach( $crlfList as $crlf ){
			$RTN .= '					<option value="'.htmlspecialchars( $crlf ).'"'.$c[$crlf].'>'.htmlspecialchars( $crlf ).'</option>'."\n";
		}
		$RTN .= '				</select>'."\n";
		$RTN .= '			</div>'."\n";
		if( strlen( $error['crlf'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['crlf'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="ext" value="'.htmlspecialchars( $this->req->in('ext') ).'" class="inputitems" /></div>'."\n";
		$RTN .= '			<ul class="annotation mt0">'."\n";
		$RTN .= '				<li class="ttrs">※セミコロン区切りで複数指定できます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※例：<code>html;htm;css;js</code></li>'."\n";
		$RTN .= '			</ul>'."\n";
		if( strlen( $error['ext'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['ext'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="確認する" /></p>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	文字コード・改行コード変換設定編集：確認
	function page_edit_charset_confirm(){
		$RTN = '';
		$HIDDEN = '';

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	文字コード・改行コード変換設定を確認してください。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">文字コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('charset') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="charset" value="'.htmlspecialchars( $this->req->in('charset') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">改行コード</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('crlf') ).'</div>'."\n";
		$HIDDEN .= '<input type="hidden" name="crlf" value="'.htmlspecialchars( $this->req->in('crlf') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$extlist = explode( ';' , $this->req->in('ext') );
		$MEMO = '';
		foreach( $extlist as $ext ){
			$ext = trim($ext);
			if( !strlen( $ext ) ){ continue; }
			$MEMO .= '	<li class="ttr">'.htmlspecialchars( $ext ).'</li>'."\n";
		}
		if( strlen( $MEMO ) ){
			$RTN .= '			<ul>'."\n";
			$RTN .= $MEMO;
			$RTN .= '			</ul>'."\n";
		}else{
			$RTN .= '			<div class="ttr">拡張子は登録されません。</div>'."\n";
		}
		$HIDDEN .= '<input type="hidden" name="ext" value="'.htmlspecialchars( $this->req->in('ext') ).'" />';
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '<div class="AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	文字コード・改行コード変換設定編集：チェック
	function check_edit_charset_check(){
		$charsetList = array( 'UTF-8' , 'Shift_JIS' , 'EUC-JP' , 'JIS' );
		$RTN = array();
		if( strlen( $this->req->in('charset') ) ){
			$is_hit = false;
			foreach( $charsetList as $charset ){
				if( $charset == $this->req->in('charset') ){
					$is_hit = true;
					break;
				}
			}
			if( !$is_hit ){
				$RTN['charset'] = '選択できない文字コードが指定されました。';
			}
		}

		$crlfList = array( 'CRLF' , 'CR' , 'LF' );
		$RTN = array();
		if( strlen( $this->req->in('crlf') ) ){
			$is_hit = false;
			foreach( $crlfList as $crlf ){
				if( $crlf == $this->req->in('crlf') ){
					$is_hit = true;
					break;
				}
			}
			if( !$is_hit ){
				$RTN['crlf'] = '選択できない改行コードが指定されました。';
			}
		}
		return	$RTN;
	}
	#--------------------------------------
	#	文字コード・改行コード変換設定編集：実行
	function execute_edit_charset_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );

		$project_model->set_charset_charset( $this->req->in('charset') );
		$project_model->set_charset_crlf( $this->req->in('crlf') );
		$project_model->set_charset_ext( $this->req->in('ext') );

		$result = $project_model->save_project();
		if( !$result ){
			return	'<p class="ttr error">プロジェクト情報の保存に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	文字コード・改行コード変換設定編集：完了
	function page_edit_charset_thanks(){
		$RTN = '';
		$RTN .= '<p class="ttr">文字コード・改行コード変換設定を保存しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	一括置換設定編集
	function start_edit_preg_replace(){
		if( strlen( $this->req->in('add:pregpattern') ) ){
			for( $i = 1; strlen( $this->req->in('p'.$i.':pregpattern') ); $i ++ ){;}
			$this->req->setin( 'p'.$i.':priority' , $i );
			$this->req->setin( 'p'.$i.':pregpattern' , $this->req->in('add:pregpattern') );
			$this->req->setin( 'p'.$i.':replaceto' , $this->req->in('add:replaceto') );
			$this->req->setin( 'p'.$i.':path' , $this->req->in('add:path') );
			$this->req->setin( 'p'.$i.':dirflg' , $this->req->in('add:dirflg') );
			$this->req->setin( 'p'.$i.':ext' , $this->req->in('add:ext') );
		}

		$error = $this->check_edit_preg_replace_check();
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_edit_preg_replace_thanks();
		}elseif( $this->req->in('mode') == 'confirm' && !count( $error ) ){
			return	$this->page_edit_preg_replace_confirm();
		}elseif( $this->req->in('mode') == 'execute' && !count( $error ) ){
			return	$this->execute_edit_preg_replace_execute();
		}elseif( !strlen( $this->req->in('mode') ) ){
			$error = array();
			$project_model = &$this->pcconf->factory_model_project();
			$project_model->load_project( $this->cmd[1] );
			$rule_list = $project_model->get_preg_replace_rules();
			if( is_array( $rule_list ) && count( $rule_list ) ){
				$i = 0;
				foreach( $rule_list as $Line ){
					$i ++;
					$this->req->setin( 'p'.$i.':priority' , $Line['priority'] );
					$this->req->setin( 'p'.$i.':pregpattern' , $Line['pregpattern'] );//検索パターン
					$this->req->setin( 'p'.$i.':replaceto' , $Line['replaceto'] );//置換文字列
					$this->req->setin( 'p'.$i.':path' , $Line['path'] );//対象ファイル/ディレクトリのパス
					$this->req->setin( 'p'.$i.':dirflg' , $Line['dirflg'] );//ディレクトリを再帰的に処理するフラグ
					$this->req->setin( 'p'.$i.':ext' , $Line['ext'] );//対象拡張子
				}
			}
		}
		return	$this->page_edit_preg_replace_input( $error );
	}
	#--------------------------------------
	#	一括置換設定編集：入力
	function page_edit_preg_replace_input( $error ){
		$RTN = '';

		$RTN .= '<script type="text/javascript">'."\n";
		$RTN .= '	function up_item(num){'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').operation_up.value=num;'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').mode.value=\'input\';'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').submit();'."\n";
		$RTN .= '	}'."\n";
		$RTN .= '	function down_item(num){'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').operation_down.value=num;'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').mode.value=\'input\';'."\n";
		$RTN .= '		document.getElementById(\'cont_op_document_form\').submit();'."\n";
		$RTN .= '	}'."\n";
		$RTN .= '</script>'."\n";

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	一括置換設定を編集してください。<br />'."\n";
		$RTN .= '</p>'."\n";

		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post" id="cont_op_document_form">'."\n";

		$entry_list = array();
		for( $i = 1; strlen( $this->req->in('p'.$i.':pregpattern') ); $i ++ ){
			$MEMO = array();
			$MEMO['priority']		= $i;
			$MEMO['pregpattern']	= $this->req->in( 'p'.$i.':pregpattern' );
			$MEMO['replaceto']		= $this->req->in( 'p'.$i.':replaceto' );
			$MEMO['path']			= $this->req->in( 'p'.$i.':path' );
			$MEMO['dirflg']			= $this->req->in( 'p'.$i.':dirflg' );
			$MEMO['ext']			= $this->req->in( 'p'.$i.':ext' );
			array_push( $entry_list , $MEMO );
		}

		if( strlen( $this->req->in('operation_up') ) && $this->req->in('operation_up') > 1 ){
			foreach( $entry_list as $key=>$line ){
				if( $line['priority'] == intval( $this->req->in('operation_up') ) ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_up') )-1;
					continue;
				}elseif( $line['priority'] == intval($this->req->in('operation_up'))-1 ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_up') );
					continue;
				}
			}
		}elseif( strlen( $this->req->in('operation_down') ) && $this->req->in('operation_down') < count( $entry_list ) ){
			foreach( $entry_list as $key=>$line ){
				if( $line['priority'] == intval( $this->req->in('operation_down') ) ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_down') )+1;
					continue;
				}elseif( $line['priority'] == intval($this->req->in('operation_down'))+1 ){
					$entry_list[$key]['priority'] = intval( $this->req->in('operation_down') );
					continue;
				}
			}
		}

		usort( $entry_list , create_function( '$a,$b' , 'if( $a[\'priority\'] > $b[\'priority\'] ){ return 1; } if( $a[\'priority\'] < $b[\'priority\'] ){ return -1; } return 0;' ) );

		foreach( $entry_list as $line ){
			$btn_operation_up = '<a href="javascript:up_item('.text::data2text( $line['priority'] ).');">上へ</a>';
			$btn_operation_down = '<a href="javascript:down_item('.text::data2text( $line['priority'] ).');">下へ</a>';

			$RTN .= ''.$this->theme->mk_hx( '実行順序['.$line['priority'].'] <span style="font-weight:normal;">'.$btn_operation_up.' '.$btn_operation_down.'</span>' , null , array( 'allow_html'=>true ) ).''."\n";
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">pregパターン</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':pregpattern" value="'.htmlspecialchars( $line['pregpattern'] ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':pregpattern'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':pregpattern'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">置換後の文字列</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':replaceto" value="'.htmlspecialchars( $line['replaceto'] ).'" class="inputitems" /></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':replaceto'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':replaceto'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">対象のパス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':path" value="'.htmlspecialchars( $line['path'] ).'" class="inputitems" /></div>'."\n";
			$RTN .= '			<ul class="annotation mt0">'."\n";
			$RTN .= '				<li class="ttrs">※リライトルール適用後のパスで指定してください。</li>'."\n";
			$RTN .= '			</ul>'."\n";
			if( strlen( $error['p'.$line['priority'].':path'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':path'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">ディレクトリを再帰的に置換</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$c = array( '1'=>' checked="checked"' );
			$RTN .= '			<div class="ttr"><label><input type="checkbox" name="p'.$line['priority'].':dirflg" value="1"'.$c[$line['dirflg']].' />再帰的に置換する</label></div>'."\n";
			if( strlen( $error['p'.$line['priority'].':dirflg'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':dirflg'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr"><input type="text" name="p'.$line['priority'].':ext" value="'.htmlspecialchars( $line['ext'] ).'" class="inputitems" /></div>'."\n";
			$RTN .= '			<ul class="annotation mt0">'."\n";
			$RTN .= '				<li class="ttrs">※セミコロン区切りで複数指定できます。</li>'."\n";
			$RTN .= '				<li class="ttrs">※例：<code>html;htm;css;js</code></li>'."\n";
			$RTN .= '			</ul>'."\n";
			if( strlen( $error['p'.$line['priority'].':ext'] ) ){
				$RTN .= '			<div class="ttr error">'.$error['p'.$line['priority'].':ext'].'</div>'."\n";
			}
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '</table>'."\n";
		}

		$RTN .= $this->theme->mk_hr()."\n";

		$RTN .= ''.$this->theme->mk_hx( '一括置換設定を追加' ).''."\n";
		$RTN .= '<table width="100%" class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">pregパターン</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:pregpattern" value="" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		if( strlen( $error['add:pregpattern'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:pregpattern'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">置換後の文字列</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:replaceto" value="" class="inputitems" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		if( strlen( $error['add:replaceto'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:replaceto'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象のパス</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:path" value="/" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		$RTN .= '			<ul class="annotation mt0">'."\n";
		$RTN .= '				<li class="ttrs">※リライトルール適用後のパスで指定してください。</li>'."\n";
		$RTN .= '			</ul>'."\n";
		if( strlen( $error['add:path'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:path'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">ディレクトリを再帰的に置換</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><label><input type="checkbox" name="add:dirflg" value="1" checked="checked" />再帰的に置換する</label></div>'."\n";
		if( strlen( $error['add:dirflg'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:dirflg'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
		$RTN .= '		<td style="width:70%;">'."\n";
		$RTN .= '			<div class="ttr"><input type="text" name="add:ext" value="html;htm;css;js" class="inputitems" style="font-family:\'ＭＳ ゴシック\';" /></div>'."\n";
		$RTN .= '			<ul class="annotation mt0">'."\n";
		$RTN .= '				<li class="ttrs">※セミコロン区切りで複数指定できます。</li>'."\n";
		$RTN .= '				<li class="ttrs">※例：<code>html;htm;css;js</code></li>'."\n";
		$RTN .= '			</ul>'."\n";
		if( strlen( $error['add:ext'] ) ){
			$RTN .= '			<div class="ttr error">'.$error['add:ext'].'</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";

		$RTN .= '	<p class="ttr">これでいい場合は「確認する」を、さらに追加する場合は「画面を更新」をクリックしてください。</p>'."\n";
		$RTN .= '	<div class="AlignC">'."\n";
		$RTN .= '		<input type="submit" value="確認する" />'."\n";
		$RTN .= '		<input type="submit" value="画面を更新" onclick="document.getElementById(\'cont_op_document_form\').mode.value=\'input\';return true;" />'."\n";
		$RTN .= '	</div>'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="confirm" />'."\n";
		$RTN .= '	<input type="hidden" name="operation_up" value="" />'."\n";
		$RTN .= '	<input type="hidden" name="operation_down" value="" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '</form>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	一括置換設定編集：確認
	function page_edit_preg_replace_confirm(){
		$RTN = '';
		$HIDDEN = '';

		for( $i = 1; strlen( $this->req->in('p'.$i.':pregpattern') ); $i ++ ){
			$RTN .= ''.$this->theme->mk_hx('実行順序['.$i.']').''."\n";
			$RTN .= '<table width="100%" class="deftable">'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">pregパターン</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':pregpattern') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':pregpattern" value="'.htmlspecialchars( $this->req->in('p'.$i.':pregpattern') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">置換後の文字列</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':replaceto') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':replaceto" value="'.htmlspecialchars( $this->req->in('p'.$i.':replaceto') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">対象のパス</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':path') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':path" value="'.htmlspecialchars( $this->req->in('p'.$i.':path') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">ディレクトリを再帰的に置換</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':dirflg') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':dirflg" value="'.htmlspecialchars( $this->req->in('p'.$i.':dirflg') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '	<tr>'."\n";
			$RTN .= '		<th style="width:30%;"><div class="ttr">対象とする拡張子</div></th>'."\n";
			$RTN .= '		<td style="width:70%;">'."\n";
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $this->req->in('p'.$i.':ext') ).'</div>'."\n";
			$HIDDEN .= '<input type="hidden" name="p'.$i.':ext" value="'.htmlspecialchars( $this->req->in('p'.$i.':ext') ).'" />';
			$RTN .= '		</td>'."\n";
			$RTN .= '	</tr>'."\n";
			$RTN .= '</table>'."\n";
		}

		$RTN .= '<div class="AlignC">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="保存する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="input" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="訂正する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<div class="ttr AlignC"><input type="submit" value="キャンセル" /></div>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	一括置換設定編集：チェック
	function check_edit_preg_replace_check(){
		$RTN = array();
/*
		if( !strlen( $this->req->in('field_id') ) ){
			$RTN['field_id'] = 'フィールド名は必ず入力してください。';
		}elseif( strlen( $this->req->in('field_id') ) < 10 ){
			$RTN['field_id'] = 'フィールド名は10バイト以上入力してください。';
		}elseif( strlen( $this->req->in('field_id') ) > 100 ){
			$RTN['field_id'] = 'フィールド名は100バイト以内で入力してください。';
		}
*/
		return	$RTN;
	}
	#--------------------------------------
	#	一括置換設定編集：実行
	function execute_edit_preg_replace_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$project_model->clear_preg_replace_rules();

		$rules = array();
		for( $i = 1; strlen( $this->req->in('p'.$i.':pregpattern') ); $i ++ ){
			$MEMO = array();
			$MEMO['priority'] = $i;
			$MEMO['pregpattern'] = $this->req->in('p'.$i.':pregpattern');
			$MEMO['replaceto'] = $this->req->in('p'.$i.':replaceto');
			$MEMO['path'] = $this->req->in('p'.$i.':path');
			$MEMO['dirflg'] = $this->req->in('p'.$i.':dirflg');
			$MEMO['ext'] = $this->req->in('p'.$i.':ext');
			array_push( $rules , $MEMO );
			unset( $MEMO );
		}
		$project_model->set_preg_replace_rules( $rules );

		$result = $project_model->save_project();
		if( !$result ){
			return	'<p class="ttr error">プロジェクト情報の保存に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	一括置換設定編集：完了
	function page_edit_preg_replace_thanks(){
		$RTN = '';
		$RTN .= '<p class="ttr">一括置換設定編集処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	プログラムを実行
	function start_execute_program(){
		if( $this->req->in('mode') == 'download' ){
			#	ダウンロードする場合
			return	$this->download_program_content();
		}

		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$program_model = &$project_model->factory_program( $this->cmd[2] );

		$pid_crawlctrl = $this->pcconf->pid['crawlctrl'];
		$exec_page_id = $pid_crawlctrl.'.'.$this->cmd[1].'.'.$this->cmd[2];

		$RTN = ''."\n";
		$RTN .= '<div class="unit_pane2">'."\n";
		$RTN .= '	<div class="pane2L" style="width:70%;">'."\n";

		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	プロジェクト『<strong>'.htmlspecialchars( $project_model->get_project_name() ).'</strong>('.htmlspecialchars( $project_model->get_project_id() ).')』のプログラム『<strong>'.htmlspecialchars( $program_model->get_program_name() ).'</strong>('.htmlspecialchars( $program_model->get_program_id() ).')』を実行します。設定を確認してください。<br />'."\n";
		$RTN .= '</p>'."\n";

		$RTN .= $this->theme->mk_hx('このプログラムの情報')."\n";
		$RTN .= '<table class="deftable">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">プロジェクト名 (プロジェクトID)</th>'."\n";
		$RTN .= '		<td class="ttr">'.htmlspecialchars( $project_model->get_project_name() ).' ('.htmlspecialchars( $project_model->get_project_id() ).')</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">プログラム名 (プログラムID)</th>'."\n";
		$RTN .= '		<td class="ttr">'.htmlspecialchars( $program_model->get_program_name() ).' ('.htmlspecialchars( $program_model->get_program_id() ).')</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">常に送信するパラメータ</th>'."\n";
		$RTN .= '		<td class="ttr"><div style="overflow:hidden;">'.htmlspecialchars( $program_model->get_program_param() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">HTTP_USER_AGENT</th>'."\n";
		$RTN .= '		<td class="ttr"><div style="overflow:hidden;">'.htmlspecialchars( $program_model->get_program_useragent() ).'</div></td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">対象範囲とするURL</th>'."\n";
		$RTN .= '		<td>'."\n";
		$RTN .= '			<div style="overflow:hidden;">'."\n";
		$urllist_scope = $program_model->get_urllist_scope();
		if( count( $urllist_scope ) ){
			$RTN .= '<ul>'."\n";
			foreach( $urllist_scope as $url ){
				$RTN .= '<li class="ttr">'.htmlspecialchars($url).'</li>'."\n";
			}
			$RTN .= '</ul>'."\n";
		}else{
			$RTN .= '<div class="ttr">全てのURLが対象です。</div>'."\n";
		}
		$RTN .= '			</div>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">ダウンロード対象外のURL</th>'."\n";
		$RTN .= '		<td>'."\n";
		$RTN .= '			<div style="overflow:hidden;">'."\n";
		$urllist_nodownload = $program_model->get_urllist_nodownload();
		if( count( $urllist_nodownload ) ){
			$RTN .= '<ul>'."\n";
			foreach( $urllist_nodownload as $url ){
				$RTN .= '<li class="ttr">'.htmlspecialchars($url).'</li>'."\n";
			}
			$RTN .= '</ul>'."\n";
		}else{
			$RTN .= '<div class="ttr">ダウンロード対象外に指定されたURLはありません。</div>'."\n";
		}
		$RTN .= '			</div>'."\n";
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr">複製先パス</th>'."\n";
		$RTN .= '		<td>'."\n";
		$path_copyto = $project_model->get_path_copyto();
		if( strlen( $program_model->get_path_copyto() ) ){
			$path_copyto = $program_model->get_path_copyto();
		}
		if( strlen( $path_copyto ) ){
			$RTN .= '			<div class="ttr">'.htmlspecialchars( $path_copyto ).'</div>'."\n";
			if( !is_dir( $path_copyto ) ){
				$RTN .= '			<div class="ttr error">ディレクトリが存在しません。</div>'."\n";
			}elseif( !is_writable( $path_copyto ) ){
				$RTN .= '			<div class="ttr error">ディレクトリに書き込みできません。</div>'."\n";
			}elseif( !$this->dbh->check_rootdir( $path_copyto ) ){
				$RTN .= '			<div class="ttr error">ディレクトリが管理外のパスです。</div>'."\n";
			}
		}else{
			$RTN .= '			<div class="ttr">---</div>'."\n";
		}
		if( $program_model->get_copyto_apply_deletedfile_flg() ){
			$RTN .= '			<div class="ttr">削除されたファイルを反映する</div>'."\n";
		}else{
			$RTN .= '			<div class="ttr">削除されたファイルを反映しない</div>'."\n";
		}
		$RTN .= '		</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '<p class="ttr">'.$this->theme->mk_link(':edit_program.'.$this->cmd[1].'.'.$this->cmd[2],array('label'=>'このプログラムを編集する','style'=>'inside')).'</p>'."\n";

		if( $this->dbh->is_unix() ){
			#--------------------------------------
			#	UNIXの場合→コマンドラインでの実行方法を案内。
			$RTN .= $this->theme->mk_hx('このプログラムの実行')."\n";
			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	この操作は、次のコマンドラインからも実行することができます。<br />'."\n";
			$RTN .= '</p>'."\n";
			$RTN .= '<blockquote><div class="ttr">';
			$RTN .= htmlspecialchars( ''.escapeshellcmd( $this->conf->path_phpcommand ).' '.escapeshellarg( realpath( $this->conf->path_docroot.'/index.php' ) ).' '.escapeshellarg( urlencode( $this->req->pkey() ).'='.urlencode( $exec_page_id ).'&'.'output_encoding='.urlencode( $this->conf->fs_encoding).''.preg_replace( '/&/' , '&' , $this->req->gene() ) ) );
			$RTN .= '</div></blockquote>'."\n";

			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	このコマンドを、ウェブから起動するには、次の「実行する」ボタンをクリックします。<br />'."\n";
			$RTN .= '</p>'."\n";
		}else{
			#--------------------------------------
			#	Windowsの場合→コマンドラインで実行できない・・・。
			$RTN .= $this->theme->mk_hx('このプログラムの実行')."\n";
			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	プログラムを実行するには、次の「実行する」ボタンをクリックしてください。<br />'."\n";
			$RTN .= '</p>'."\n";
		}

		if( strlen( $pid_crawlctrl ) && $pid_crawlctrl == $this->site->getpageinfo( $pid_crawlctrl , 'id' ) ){
			$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( $exec_page_id ) ).'" method="post" target="_blank">'."\n";
			$RTN .= '	<p class="ttr AlignC"><input type="submit" value="実行する" /></p>'."\n";
			$RTN .= '	'.$this->theme->mk_form_defvalues( $exec_page_id )."\n";
			$RTN .= '</form>'."\n";
		}else{
			$RTN .= '<p class="ttr error">実行コンテンツが登録されていないか、無効なため実行できません。</p>'."\n";
		}


		$RTN .= '	</div>'."\n";
		$RTN .= '	<div class="pane2R" style="width:25%;">'."\n";

		$RTN .= $this->theme->mk_hx('書き出したデータのダウンロード')."\n";
		$is_zip = array();
		if( class_exists( 'ZipArchive' ) ){
			$is_zip['zip'] = true;
		}
		if( strlen( $this->conf->path_commands['tar'] ) ){
			$is_zip['tgz'] = true;
		}
		if( count( $is_zip ) ){
			#	tarコマンドが使えたら(UNIXのみ)
			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	書き出したデータを';
			$RTN .= implode( ', ' , array_keys( $is_zip ) );
			$RTN .= '形式でダウンロードすることができます。';
			$RTN .= '<br />'."\n";
			$RTN .= '</p>'."\n";
			$RTN .= '<ul class="none">'."\n";
			foreach( array_keys( $is_zip ) as $type ){
				$RTN .= '	<li class="ttr">'.$this->theme->mk_link( $this->req->p() , array('label'=>strtoupper($type).'形式でダウンロード','active'=>false,'style'=>'inside','additionalquery'=>'mode=download&ext='.strtolower($type)) ).'</li>'."\n";
			}
			$RTN .= '</ul>'."\n";
		}else{
			#	圧縮解凍系機能が利用できなかったら
			$RTN .= '<p class="ttr">'."\n";
			$RTN .= '	<span class="error">tarコマンドのパスがセットされていません</span>。<code>$conf->path_commands[\'tar\']</code>に、tarコマンドのパスを設定してください。<br />'."\n";
			$RTN .= '</p>'."\n";
		}

		$RTN .= $this->theme->mk_hx('書き出したデータの削除')."\n";
		$RTN .= '<p class="ttr">'."\n";
		$RTN .= '	書き出したデータを削除します。<br />'."\n";
		$RTN .= '</p>'."\n";
		$RTN .= '<ul class="none">'."\n";
		$RTN .= '	<li class="ttr">'.$this->theme->mk_link( ':delete_program_content.'.$this->cmd[1].'.'.$this->cmd[2] , array('label'=>'削除する','active'=>false,'style'=>'inside') ).'</li>'."\n";
		$RTN .= '</ul>'."\n";

		$RTN .= '	</div>'."\n";
		$RTN .= '</div>'."\n";

		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="戻る" /></p>'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}


	###################################################################################################################
	#	プログラムが書き出したコンテンツのダウンロード
	function download_program_content(){
		$download_content_path = $this->pcconf->get_program_home_dir( $this->cmd[1] , $this->cmd[2] ).'/dl';
		$download_zipto_path = dirname($download_content_path).'/tmp_download_content';
		if( !is_dir( $download_content_path ) ){
			return	'<p class="ttr error">ディレクトリが存在しません。</p>';
		}

		if( $this->req->in('ext') == 'tgz' && strlen( $this->conf->path_commands['tar'] ) ){
			#	tarコマンドが使えたら(UNIXのみ)
			$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/resources/tgz.php' );
			if( !$className ){
				$this->errors->error_log( 'tgzライブラリのロードに失敗しました。' , __FILE__ , __LINE__ );
				return	'<p class="ttr error">tgzライブラリのロードに失敗しました。</p>';
			}
			$obj_tgz = new $className( $this->conf , $this->dbh , $this->errors );

			if( !$obj_tgz->zip( $download_content_path , $download_zipto_path.'.tgz' ) ){
				return	'<p class="ttr error">圧縮に失敗しました。</p>';
			}

			if( !is_file( $download_zipto_path.'.tgz' ) ){
				return	'<p class="ttr error">圧縮されたアーカイブファイルは現在は存在しません。</p>';
			}

			$dl_filename = $this->cmd[1].'_'.$this->cmd[2].'.tgz';
			if( $this->pcconf->conf_dl_datetime_in_filename ){
				$CONTENT = $this->dbh->file_get_contents( $download_content_path.'/__LOGS__/datetime.txt' );
				list( $start_datetime , $end_datetime ) = explode(' --- ',$CONTENT);
				if( !strlen( $end_datetime ) ){
					$end_datetime = date('Y-m-d H:i:s');
				}
				$dl_filename = $this->cmd[1].'_'.date('Ymd_Hi',time::datetime2int($end_datetime)).'_'.$this->cmd[2].'.tgz';
			}
			$download_zipto_path = $download_zipto_path.'.tgz';

		}elseif( $this->req->in('ext') == 'zip' && class_exists( 'ZipArchive' ) ){
			#	ZIP関数が有効だったら
			$className = $this->dbh->require_lib( '/plugins/PicklesCrawler/resources/zip.php' );
			if( !$className ){
				$this->errors->error_log( 'zipライブラリのロードに失敗しました。' , __FILE__ , __LINE__ );
				return	'<p class="ttr error">zipライブラリのロードに失敗しました。</p>';
			}
			$obj_zip = new $className( $this->conf , $this->dbh , $this->errors );

			if( !$obj_zip->zip( $download_content_path , $download_zipto_path.'.zip' ) ){
				return	'<p class="ttr error">圧縮に失敗しました。</p>';
			}

			if( !is_file( $download_zipto_path.'.zip' ) ){
				return	'<p class="ttr error">圧縮されたアーカイブファイルは現在は存在しません。</p>';
			}

			$dl_filename = $this->cmd[1].'_'.$this->cmd[2].'.zip';
			if( $this->pcconf->conf_dl_datetime_in_filename ){
				$CONTENT = $this->dbh->file_get_contents( $download_content_path.'/__LOGS__/datetime.txt' );
				list( $start_datetime , $end_datetime ) = explode(' --- ',$CONTENT);
				if( !strlen( $end_datetime ) ){
					$end_datetime = date('Y-m-d H:i:s');
				}
				$dl_filename = $this->cmd[1].'_'.date('Ymd_Hi',time::datetime2int($end_datetime)).'_'.$this->cmd[2].'.zip';
			}
			$download_zipto_path = $download_zipto_path.'.zip';

		}

		$result = $this->theme->flush_file( $download_zipto_path , array( 'filename'=>$dl_filename , 'delete'=>true ) );
		if( $result === false ){
			return	'<p class="ttr error">作成されたアーカイブのダウンロードに失敗しました。</p>';
		}
		return	$result;
	}


	###################################################################################################################
	#	プログラムの削除
	function start_delete_program_content(){
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_delete_program_content_thanks();
		}elseif( $this->req->in('mode') == 'execute' ){
			return	$this->execute_delete_program_content_execute();
		}
		return	$this->page_delete_program_content_confirm();
	}
	#--------------------------------------
	#	プログラムの削除：確認
	function page_delete_program_content_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		$RTN .= '<p class="ttr">プログラムが書き出したコンテンツを削除します。</p>'."\n";
		$RTN .= '<p class="ttr">よろしいですか？</p>'."\n";

		$RTN .= '<div class="AlignC p">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="削除する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':execute_program.'.$this->cmd[1].'.'.$this->cmd[2] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':execute_program.'.$this->cmd[1].'.'.$this->cmd[2] )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プログラムの削除：実行
	function execute_delete_program_content_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		if( !strlen( $this->cmd[1] ) ){
			return	'<p class="ttr error">プロジェクトが選択されていません。</p>';
		}
		if( !strlen( $this->cmd[2] ) ){
			return	'<p class="ttr error">プログラムが選択されていません。</p>';
		}


		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$program_model = &$project_model->factory_program( $this->cmd[2] );
		$result = $program_model->delete_program_content();

		if( !$result ){
			return	'<p class="ttr error">プログラムコンテンツの削除に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	プログラムの削除：完了
	function page_delete_program_content_thanks(){
		$RTN = ''."\n";
		$RTN .= '<p class="ttr">プログラムコンテンツの削除処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':execute_program.'.$this->cmd[1].'.'.$this->cmd[2] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':execute_program.'.$this->cmd[1].'.'.$this->cmd[2] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}






	###################################################################################################################
	#	プログラムの削除
	function start_delete_program(){
		if( $this->req->in('mode') == 'thanks' ){
			return	$this->page_delete_program_thanks();
		}elseif( $this->req->in('mode') == 'execute' ){
			return	$this->execute_delete_program_execute();
		}
		return	$this->page_delete_program_confirm();
	}
	#--------------------------------------
	#	プログラムの削除：確認
	function page_delete_program_confirm(){
		$RTN = ''."\n";
		$HIDDEN = ''."\n";

		$RTN .= '<p class="ttr">プログラムを削除します。</p>'."\n";
		$RTN .= '<p class="ttr">よろしいですか？</p>'."\n";

		$RTN .= '<div class="AlignC p">'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act() ).'" method="post">'."\n";
		$RTN .= '	<input type="hidden" name="mode" value="execute" />'."\n";
		$RTN .= $HIDDEN;
		$RTN .= '	'.$this->theme->mk_form_defvalues()."\n";
		$RTN .= '	<input type="submit" value="削除する" />'."\n";
		$RTN .= '</form>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= $this->theme->mk_hr()."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="get">'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '	<p class="ttr AlignC"><input type="submit" value="キャンセル" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}
	#--------------------------------------
	#	プログラムの削除：実行
	function execute_delete_program_execute(){
		if( !$this->user->save_t_lastaction() ){
			#	2重書き込み防止
			return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
		}

		if( !strlen( $this->cmd[1] ) ){
			return	'<p class="ttr error">プロジェクトが選択されていません。</p>';
		}
		if( !strlen( $this->cmd[2] ) ){
			return	'<p class="ttr error">プログラムが選択されていません。</p>';
		}


		$project_model = &$this->pcconf->factory_model_project();
		$project_model->load_project( $this->cmd[1] );
		$program_model = &$project_model->factory_program( $this->cmd[2] );
		$result = $program_model->destroy_program();

		if( !$result ){
			return	'<p class="ttr error">プログラムの削除に失敗しました。</p>';
		}

		return	$this->theme->redirect( $this->req->p() , 'mode=thanks' );
	}
	#--------------------------------------
	#	プログラムの削除：完了
	function page_delete_program_thanks(){
		$RTN = ''."\n";
		$RTN .= '<p class="ttr">プログラムの削除処理を完了しました。</p>';
		$RTN .= '<form action="'.htmlspecialchars( $this->theme->act( ':detail.'.$this->cmd[1] ) ).'" method="post">'."\n";
		$RTN .= '	<input type="submit" value="戻る" />'."\n";
		$RTN .= '	'.$this->theme->mk_form_defvalues( ':detail.'.$this->cmd[1] )."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}




	/**
	 * 設定項目の確認
	 */
	private function page_configcheck(){
		$RTN = ''."\n";
		$RTN .= '<div class="unit">'."\n";
		$RTN .= '<table class="def">'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr" style="width:30%;">作業ディレクトリ</th>'."\n";
		$path = $this->pcconf->get_home_dir();
		if( is_dir( $path ) ){
			$path = realpath( $path );
		}
		$RTN .= '		<td class="ttr" style="width:70%;">'.htmlspecialchars( $path ).'</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr" style="width:30%;">収集数の上限</th>'."\n";
		$RTN .= '		<td class="ttr" style="width:70%;">'.htmlspecialchars( $this->pcconf->get_value('crawl_max_url_number') ).'</td>'."\n";
		$RTN .= '	</tr>'."\n";
		$RTN .= '	<tr>'."\n";
		$RTN .= '		<th class="ttr" style="width:30%;">tarのパス</th>'."\n";
		$RTN .= '		<td class="ttr" style="width:70%;">'.htmlspecialchars( $this->conf->path_commands['tar'] ).'</td>'."\n";
		$RTN .= '	</tr>'."\n";
		// $RTN .= '	<tr>'."\n";
		// $RTN .= '		<th class="ttr" style="width:30%;">crawlctrl のページID</th>'."\n";
		// $RTN .= '		<td class="ttr" style="width:70%;">'.htmlspecialchars( $this->pcconf->pid['crawlctrl'] ).'</td>'."\n";
		// $RTN .= '	</tr>'."\n";
		$RTN .= '</table>'."\n";
		$RTN .= '</div>'."\n";
		$RTN .= '<form action="'.htmlspecialchars( $this->href(':') ).'" method="post">'."\n";
		$RTN .= '	<p class="center"><input type="submit" value="戻る" /></p>'."\n";
		$RTN .= '</form>'."\n";
		return	$RTN;
	}

}

?>�ル編集' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.create_program.'.$this->req->pvelm(1) , array( 'title'=>'新規プログラム作成' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.edit_program.'.$this->req->pvelm(1).'.'.$this->req->pvelm(2) , array( 'title'=>'プログラム編集' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.execute_program.'.$this->req->pvelm(1).'.'.$this->req->pvelm(2) , array( 'title'=>'プログラム実行' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.delete_program.'.$this->req->pvelm(1).'.'.$this->req->pvelm(2) , array( 'title'=>'プログラム削除' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.edit_charset.'.$this->req->pvelm(1) , array( 'title'=>'文字コード・改行コード変換設定' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.edit_preg_replace.'.$this->req->pvelm(1) , array( 'title'=>'一括置換設定' , 'path'=>$path ) );
		$this->site->setpageinfoall( $this->req->po().'.delete_proj.'.$this->req->pvelm(1) , array( 'title'=>'プロジェクトを削除' , 'path'=>$path ) );

		$path = $path.'/'.$this->req->po().'.execute_program.'.$this->req->pvelm(1).'.'.$this->req->pvelm(2);
		$this->site->setpageinfoall( $this->req->po().'.delete_program_content.'.$this->req->pvelm(1) , array( 'title'=>'プログラムコンテンツの削除' , 'path'=>$path ) );
		return true;
	}

}

?>