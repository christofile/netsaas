<?php 
/* wppa-upload.php
* Package: wp-photo-album-plus
*
* Contains all the upload/import pages and functions
* Version 5.0.10
*
*/

function _wppa_page_upload() {
global $target;
global $wppa_opt;
global $wppa_revno;

	// upload images admin page

    // sanitize system
	$user = wppa_get_user();
	wppa_sanitize_files();

	if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) {
		if ( isset( $_POST['wppa-watermark-file'] ) ) update_option('wppa_watermark_file_'.$user, $_POST['wppa-watermark-file']);
		if ( isset( $_POST['wppa-watermark-pos'] ) ) update_option('wppa_watermark_pos_'.$user, $_POST['wppa-watermark-pos']);
	}
	
	// If from album admin set the last album
	if ( isset( $_REQUEST['wppa-set-album'] ) ) wppa_set_last_album($_REQUEST['wppa-set-album']);
	
	// Do the upload if requested
	if ( isset( $_POST['wppa-upload-multiple'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		wppa_upload_multiple();
		if ( isset( $_POST['wppa-go-edit-multiple'] ) ) {
			if ( current_user_can('wppa_admin') ) {
				wppa_ok_message(__('Connecting to edit album...', 'wppa')); ?>
				<script type="text/javascript">document.location = '<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$_POST['wppa-album'], 'js')) ?>';</script>
			<?php }
			else {
				wppa_ok_message(__('Connecting to edit photos...', 'wppa')); ?>
				<script type="text/javascript">document.location = '<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_edit_photo', 'js')) ?>';</script>
			<?php }
		}
	}
	if ( isset( $_POST['wppa-upload'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		wppa_upload_photos();
		if ( isset( $_POST['wppa-go-edit-single'] ) ) {
			if ( current_user_can('wppa_admin') ) {
				wppa_ok_message(__('Connecting to edit album...', 'wppa')); ?>
				<script type="text/javascript">document.location = '<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_admin_menu&tab=edit&edit_id='.$_POST['wppa-album'], 'js')) ?>';</script>
			<?php }
			else {
				wppa_ok_message(__('Connecting to edit photos...', 'wppa')); ?>
				<script type="text/javascript">document.location = '<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_edit_photo', 'js')) ?>';</script>
			<?php }
		}
	} 
	if ( isset( $_POST['wppa-upload-zip'] ) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		$err = wppa_upload_zip();
		if ( isset( $_POST['wppa-go-import'] ) && $err == '0' ) { 
			wppa_ok_message(__('Connecting to your depot...', 'wppa'));
			update_option('wppa_import_source_'.$user, WPPA_DEPOT); ?>
			<script type="text/javascript">document.location = '<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_import_photos&zip='.$target, 'js')) ?>';</script>
		<?php }
	} 
	
	// sanitize system again
	wppa_sanitize_files();
	
	// Check database
	// if ( get_option('wppa_revision') != $wppa_revno ) 
	wppa_check_database(true);

	?>
	
	<div class="wrap">
		<?php $iconurl = WPPA_URL.'/images/camera32.png'; ?>
		<div id="icon-camera" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
		</div>
		<?php $iconurl = WPPA_URL.'/images/arrow32.png'; ?>
		<div id="icon-arrow" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
		</div>
		<?php $iconurl = WPPA_URL.'/images/album32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat">
		<br />
		</div>
		<h2><?php _e('Upload Photos', 'wppa'); ?></h2>

		<?php	
		$max_files = ini_get('max_file_uploads');
		$max_files_txt = $max_files;
		if ($max_files < '1') {
			$max_files_txt = __('unknown', 'wppa');
			$max_files = '15';
		}
		$max_size = ini_get('upload_max_filesize');
		$max_time = ini_get('max_input_time');	
		if ($max_time < '1') $max_time = __('unknown', 'wppa');
		// chek if albums exist before allowing upload
		if ( wppa_has_albums() ) {
			if ( wppa_switch('wppa_upload_one_only') && ! current_user_can('administrator') ) {
				/* One only */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php _e('Upload a single photo', 'wppa'); ?></h3>
					<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_upload_photos')) ?>" method="post">
					<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); ?>
						<input id="my_files" type="file" name="my_files[]" />
						<p>
							<label for="wppa-album"><?php _e('Album:', 'wppa'); ?> </label>
							<select name="wppa-album" id="wppa-album-s">
								<?php echo wppa_album_select_a(array('path' => wppa_switch('wppa_hier_albsel'),'addpleaseselect' => true, 'checkaccess' => true, 'checkupload' => true));//echo(wppa_album_select('', '', false, false, false, false, false, true)); ?>
							</select>
						</p>
						<?php if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) { ?>		
							<p>		
								<?php _e('Apply watermark file:', 'wppa') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo(wppa_watermark_file_select()) ?>
								</select>

								<?php _e('Position:', 'wppa') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo(wppa_watermark_pos_select()) ?>
								</select>
							</p>
						<?php } ?>
						<input type="submit" class="button-primary" name="wppa-upload-multiple" value="<?php _e('Upload Photo', 'wppa') ?>" onclick="if ( document.getElementById('wppa-album-s').value == 0 ) { alert('<?php _e('Please select an album', 'wppa') ?>'); return false; }" />
						<input type="checkbox" id="wppa-go-edit-multiple" name="wppa-go-edit-multiple" style="display:none" checked="checked" />&nbsp;
					</form>
				</div>
<?php		}
			else { ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px; background-color:#fffbcc; border-color:#e6db55;">
				<?php echo(sprintf(__('<b>Notice:</b> your server allows you to upload <b>%s</b> files of maximum total <b>%s</b> bytes and allows <b>%s</b> seconds to complete.', 'wppa'), $max_files_txt, $max_size, $max_time)) ?>
				<?php _e('If your request exceeds these limitations, it will fail, probably without an errormessage.', 'wppa') ?>
				<?php _e('Additionally your hosting provider may have set other limitations on uploading files.', 'wppa') ?>
				<?php echo '<br />'.wppa_check_memory_limit() ?>
				</div>
				<?php /* Multple photos */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php _e('Box A:', 'wppa'); echo ' ';_e('Multiple Photos in one selection', 'wppa'); ?></h3>
					<?php echo sprintf(__('You can select up to %s photos in one selection and upload them.', 'wppa'), $max_files_txt); ?>
					<br /><small style="color:blue" ><?php _e('You need a modern browser that supports HTML-5 to select multiple files', 'wppa') ?></small>
					<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_upload_photos')) ?>" method="post">
					<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); ?>
						<input id="my_files" type="file" multiple="multiple" name="my_files[]" onchange="showit()" />
						<div id="files_list2">
							<h3><?php _e('Selected Files:', 'wppa'); ?></h3>
							
						</div>
						<script type="text/javascript">
							function showit() {
								var maxsize = parseInt('<?php echo $max_size ?>') * 1024 * 1024;
								var maxcount = parseInt('<?php echo $max_files_txt ?>');
								var totsize = 0;
								var files = document.getElementById('my_files').files;
								var tekst = '<h3><?php _e('Selected Files:', 'wppa') ?></h3>';
								tekst += '<table><thead><tr>';
										tekst += '<td><?php _e('Name', 'wppa') ?></td><td><?php _e('Size', 'wppa') ?></td><td><?php _e('Type', 'wppa') ?></td>';
									tekst += '</tr></thead>';
									tekst += '<tbody>';
										tekst += '<tr><td><hr /></td><td><hr /></td><td><hr /></td></tr>';
										for (var i=0;i<files.length;i++) {
											tekst += '<tr>';
												tekst += '<td>' + files[i].name + '</td>';
												tekst += '<td>' + files[i].size + '</td>';
												totsize += files[i].size;
												tekst += '<td>' + files[i].type + '</td>';
											tekst += '</tr>';
										}
										tekst += '<tr><td><hr /></td><td><hr /></td><td><hr /></td></tr>';
									var style1 = '';
									var style2 = '';
									var style3 = '';
									var warn1 = '';
									var warn2 = '';
									var warn3 = '';
									if ( maxcount > 0 && files.length > maxcount ) {
										style1 = 'color:red';
										warn1 = '<?php _e('Too many!', 'wppa') ?>';
									}
									if ( maxsize > 0 && totsize > maxsize ) {
										style2 = 'color:red';
										warn2 = '<?php _e('Too big!', 'wppa') ?>';
									}
									if ( warn1 || warn2 ) {
										style3 = 'color:green';
										warn3 = '<?php _e('Try again!', 'wppa') ?>';
									}
									tekst += '<tr><td style="'+style1+'" ><?php _e('Total', 'wppa') ?>: '+files.length+' '+warn1+'</td><td style="'+style2+'" >'+totsize+' '+warn2+'</td><td style="'+style3+'" >'+warn3+'</td></tr>';
									tekst += '</tbody>';
								tekst += '</table>';
								jQuery('#files_list2').html(tekst); 
							}
						</script>
						<p>
							<label for="wppa-album"><?php _e('Album:', 'wppa'); ?> </label>
							<select name="wppa-album" id="wppa-album-s">
								<?php echo wppa_album_select_a(array('path' => wppa_switch('wppa_hier_albsel'),'addpleaseselect' => true, 'checkaccess' => true, 'checkupload' => true));//echo(wppa_album_select('', '', false, false, false, false, false, true)); ?>
							</select>
						</p>
						<?php if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) { ?>		
							<p>		
								<?php _e('Apply watermark file:', 'wppa') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo(wppa_watermark_file_select()) ?>
								</select>

								<?php _e('Position:', 'wppa') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo(wppa_watermark_pos_select()) ?>
								</select>
							</p>
						<?php } ?>
						<input  type="submit" class="button-primary" name="wppa-upload-multiple" value="<?php _e('Upload Multiple Photos', 'wppa') ?>" onclick="if ( document.getElementById('wppa-album-s').value == 0 ) { alert('<?php _e('Please select an album', 'wppa') ?>'); return false; }" />
						<input type="checkbox" id="wppa-go-edit-multiple" name="wppa-go-edit-multiple" onchange="wppaCookieCheckbox(this, 'wppa-go-edit-multiple')" />&nbsp;
						<script type="text/javascript" >
							if ( wppa_getCookie('wppa-go-edit-multiple') == 'on' ) document.getElementById('wppa-go-edit-multiple').checked = 'checked';
						</script>
						<?php 
						if ( current_user_can('wppa_admin') ) { 
							_e('After upload: Go to the <b>Edit Album</b> page.', 'wppa');
						} 
						else {
							_e('After upload: Go to the <b>Edit Photos</b> page.', 'wppa');
						}
						?>
					</form>
				</div>
				<?php /* End multiple */ ?>

				<?php /* Single photos */ ?>
				<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; width: 600px;">
					<h3 style="margin-top:0px;"><?php  _e('Box B:', 'wppa'); echo ' ';_e('Single Photos in multiple selections', 'wppa'); ?></h3>
					<?php echo sprintf(__('You can select up to %s photos one by one and upload them at once.', 'wppa'), $max_files_txt); ?>
					<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_upload_photos')) ?>" method="post">
					<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); ?>
						<input id="my_file_element" type="file" name="file_1" />
						<div id="files_list">
							<h3><?php _e('Selected Files:', 'wppa'); ?></h3>
							
						</div>
						<p>
							<label for="wppa-album"><?php _e('Album:', 'wppa'); ?> </label>
							<select name="wppa-album" id="wppa-album-m">
								<?php echo wppa_album_select_a(array('path' => wppa_switch('wppa_hier_albsel'), 'addpleaseselect' => true, 'checkaccess' => true, 'checkupload' => true));//('', '', false, false, false, false, false, true)); ?>
							</select>
						</p>
						<?php if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) { ?>		
							<p>		
								<?php _e('Apply watermark file:', 'wppa') ?>
								<select name="wppa-watermark-file" id="wppa-watermark-file">
									<?php echo(wppa_watermark_file_select()) ?>
								</select>

								<?php _e('Position:', 'wppa') ?>
								<select name="wppa-watermark-pos" id="wppa-watermark-pos">
									<?php echo(wppa_watermark_pos_select()) ?>
								</select>
							</p>
						<?php } ?>
						<input type="submit" class="button-primary" name="wppa-upload" value="<?php _e('Upload Single Photos', 'wppa') ?>" onclick="if ( document.getElementById('wppa-album-m').value == 0 ) { alert('<?php _e('Please select an album', 'wppa') ?>'); return false; }" />
						<input type="checkbox" id="wppa-go-edit-single" name="wppa-go-edit-single" onchange="wppaCookieCheckbox(this, 'wppa-go-edit-single')" />&nbsp;
						<script type="text/javascript" >
							if ( wppa_getCookie('wppa-go-edit-single') == 'on' ) document.getElementById('wppa-go-edit-single').checked = 'checked';
						</script>
						<?php 
						if ( current_user_can('wppa_admin') ) {
							_e('After upload: Go to the <b>Edit Album</b> page.', 'wppa');
						}
						else {
							_e('After upload: Go to the <b>Edit Photos</b> page.', 'wppa');
						} 
						?>
					</form>
					<script type="text/javascript">
					<!-- Create an instance of the multiSelector class, pass it the output target and the max number of files -->
						var multi_selector = new MultiSelector( document.getElementById( 'files_list' ), <?php echo($max_files) ?> );
					<!-- Pass in the file element -->
						multi_selector.addElement( document.getElementById( 'my_file_element' ) );
					</script>
				</div>
				<?php /* End single photos */ ?>

				<?php /* Single zips */ ?>
				<?php if ( current_user_can('wppa_import') ) { ?>
					<?php if (PHP_VERSION_ID >= 50207) { ?>
						<div style="border:1px solid #ccc; padding:10px; width: 600px;">
							<h3 style="margin-top:0px;"><?php  _e('Box C:', 'wppa'); echo ' ';_e('Zipped Photos in one selection', 'wppa'); ?></h3>
							<?php echo sprintf(__('You can upload one zipfile. It will be placed in your personal wppa-depot: <b>.../%s</b><br/>Once uploaded, use <b>Import Photos</b> to unzip the file and place the photos in any album.', 'wppa'), WPPA_DEPOT) ?>
							<form enctype="multipart/form-data" action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_upload_photos')) ?>" method="post">
							<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); ?>
								<input id="my_zipfile_element" type="file" name="file_zip" /><br/><br/>
								<input type="submit" class="button-primary" name="wppa-upload-zip" value="<?php _e('Upload Zipped Photos', 'wppa') ?>" />
								<input type="checkbox" id="wppa-go-import" name="wppa-go-import" onchange="wppaCookieCheckbox(this, 'wppa-go-import')" />&nbsp;
								<script type="text/javascript" >
									if ( wppa_getCookie('wppa-go-import') == 'on' ) document.getElementById('wppa-go-import').checked = 'checked';
								</script>
								<?php _e('After upload: Go to the <b>Import Photos</b> page.', 'wppa') ?>
							</form>
						</div>
					<?php }
					else { ?>
						<div style="border:1px solid #ccc; padding:10px; width: 600px;">
						<?php _e('<small>Ask your administrator to upgrade php to version 5.2.7 or later. This will enable you to upload zipped photos.</small>', 'wppa') ?>
						</div>
					<?php }
				} 
			}
		}
	else { ?>
			<?php $url = wppa_dbg_url(get_admin_url().'admin.php?page=wppa_admin_menu'); ?>
			<p><?php _e('No albums exist. You must', 'wppa'); ?> <a href="<?php echo($url) ?>"><?php _e('create one', 'wppa'); ?></a> <?php _e('beofre you can upload your photos.', 'wppa'); ?></p>
<?php } ?>
	</div>
<?php
}

// import images admin page
function _wppa_page_import() {
global $wppa_opt;
global $wppa_revno;
global $wppa;
//ini_set('max_execution_time', '15');
	// Check database
//	if ( get_option('wppa_revision') != $wppa_revno ) 
	wppa_check_database(true);

	// Sanitize system
	$user = wppa_get_user();
	$count = wppa_sanitize_files();
	if ($count) wppa_error_message($count.' '.__('illegal files deleted.', 'wppa'));

	if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) {
		if ( isset( $_POST['wppa-watermark-file'] ) ) update_option('wppa_watermark_file_'.$user, $_POST['wppa-watermark-file']);
		if ( isset( $_POST['wppa-watermark-pos'] ) ) update_option('wppa_watermark_pos_'.$user, $_POST['wppa-watermark-pos']);
	}
	
	// Do the dirty work
	if (isset($_GET['zip'])) {
	//	check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		wppa_extract($_GET['zip'], true);
	}
	if (isset($_POST['wppa-import-set-source'])) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
		update_option('wppa_import_source_'.$user, $_POST['wppa-source']);
	}
	elseif ( isset($_POST['wppa-import-submit']) ) {
		check_admin_referer( '$wppa_nonce', WPPA_NONCE );
        if (isset($_POST['del-after-p'])) $delp = true; else $delp = false;
		if (isset($_POST['del-after-a'])) $dela = true; else $dela = false;	
		if (isset($_POST['del-after-z'])) $delz = true; else $delz = false;
//		if (isset($_POST['del-after-d'])) $deld = true; else $deld = false;
		wppa_import_photos($delp, $dela, $delz);
	} 
	elseif ( isset($_GET['continue']) ) {
		if ( wp_verify_nonce( $_GET['nonce'], 'dirimport' ) ) wppa_import_photos();
	}
	// Sanitize again
	$count = wppa_sanitize_files();
	if ($count) wppa_error_message($count.' '.__('illegal files deleted.', 'wppa'));
?>
	
	<div class="wrap">
		<?php $iconurl = WPPA_URL.'/images/camera32.png'; ?>
		<div id="icon-camera" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat"></div>
		<?php $iconurl = WPPA_URL.'/images/arrow32.png'; ?>
		<div id="icon-arrow" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat"></div>
		<?php $iconurl = WPPA_URL.'/images/album32.png'; ?>
		<div id="icon-album" class="icon32" style="background: transparent url(<?php echo($iconurl); ?>) no-repeat"><br /></div>
		
		<h2><?php _e('Import Photos', 'wppa'); ?></h2><br />
<?php		
		// Get this users current source directory setting
		$source      = get_option('wppa_import_source_'.$user, WPPA_DEPOT);
		if ( ! $source || ! is_dir(ABSPATH . $source) ) {
			$source = WPPA_DEPOT;
			update_option('wppa_import_source_'.$user, WPPA_DEPOT);
		}
		$source_path = ABSPATH . $source;
		$source_url  = get_bloginfo('url') . '/' . $source;

		// See if the current source is the 'home' directory
		$is_depot 	= ( $source == WPPA_DEPOT );
		// See if the current souce is a wp upload location or a wppa+ sourcefile location ( if so: no delete checkbox )
		$is_sub_depot = ( substr($source, 0, strlen(WPPA_DEPOT) ) == WPPA_DEPOT ) && ( substr(ABSPATH.$source, 0, strlen($wppa_opt['wppa_source_dir'])) != $wppa_opt['wppa_source_dir'] );

		// See what's in there
		$files 		= glob($source_path . '/*');
		$zipcount 	= wppa_get_zipcount($files);
		$albumcount = wppa_get_albumcount($files);
		$photocount = wppa_get_photocount($files);
		$dircount	= wppa_get_dircount($files);

?>		
		<form action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_import_photos')) ?>" method="post">
		<?php if ( current_user_can('administrator') || $wppa_opt['wppa_chgsrc_is_restricted'] == 'no' ) { ?>
<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
		<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); ?>
		<?php _e('Import photos from:', 'wppa'); ?>
			<select name="wppa-source">
				<!--<option value="" ><?php _e('Please select a location', 'wppa') ?></option>-->
				<option value="<?php echo(WPPA_DEPOT) ?>" <?php if ($is_depot) echo('selected="selected"') ?>><?php _e('--- My depot ---', 'wppa') ?></option>
				<?php wppa_walktree(WPPA_DEPOT, $source, true, true); /* Allow the name 'wppa', subdirs only */ ?>
				<?php wppa_walktree(WPPA_UPLOAD, $source, false, false); /* Do NOT allow the name 'wppa', include topdir */ ?>	
			</select>
			<input type="submit" class="button-secundary" name="wppa-import-set-source" value="<?php _e('Set source directory', 'wppa'); ?>" />
</div>
		<?php } ?>
		</form>

<?php
		
		// check if albums exist or will be made before allowing upload
		if ( wppa_has_albums() || $albumcount > '0' || $zipcount >'0' || $dircount > '0' ) { 
	
			if ( $photocount > '0' || $albumcount > '0' || $zipcount >'0' || $dircount > '0') { ?>
			
				<form action="<?php echo(wppa_dbg_url(get_admin_url().'admin.php?page=wppa_import_photos')) ?>" method="post">
				<?php wp_nonce_field('$wppa_nonce', WPPA_NONCE); 
				
				// Display the zips
				if (PHP_VERSION_ID >= 50207 && $zipcount > '0') { ?>	
<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
				<p><b>
					<?php _e('There are', 'wppa'); echo(' '.$zipcount.' '); _e('zipfiles in the depot.', 'wppa') ?><br/>
				</b></p>
				<table class="form-table albumtable widefat" style="margin-bottom:0;" >
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="all-zip" checked="checked" onchange="checkAll('all-zip', '.wppa-zip')" /><b>&nbsp;&nbsp;<?php _e('Check/uncheck all', 'wppa') ?></b>
							</th>
							<?php if ($is_sub_depot) { ?>
								<th>
									<input type="checkbox" name="del-after-z" checked="checked" /><b>&nbsp;&nbsp;<?php _e('Delete after successful extraction.', 'wppa'); ?></b>
								</th>
							<?php } ?>
						</tr>
					</thead>
				</table>
				<table class="form-table albumtable" style="margin-top:0;" >
					<tr>
						<?php
						$ct = 0;
						$idx = '0';
						foreach ($files as $file) {
				
							$ext = strtolower(substr(strrchr($file, "."), 1));
							if ($ext == 'zip') { ?>
								<td>
									<input type="checkbox" name="file-<?php echo($idx) ?>" class="wppa-zip" checked="checked" />&nbsp;&nbsp;<?php echo(basename($file)); ?>
								</td>
								<?php if ($ct == 3) {
									echo('</tr><tr>'); 
									$ct = 0;
								}
								else {
									$ct++;
								}
							}
							$idx++;
						} ?>
					</tr>
				</table>
</div>
				<?php }
				
				// Dispay the albums ( .amf files )
				if ($albumcount > '0') { ?>
<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
				<p><b>
					<?php _e('There are', 'wppa'); echo(' '.$albumcount.' '); _e('albumdefinitions in the depot.', 'wppa') ?><br/>
				</b></p>
				<table class="form-table albumtable widefat" style="margin-bottom:0;" >
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="all-amf" checked="checked" onchange="checkAll('all-amf', '.wppa-amf')" /><b>&nbsp;&nbsp;<?php _e('Check/uncheck all', 'wppa') ?></b>
							</th>
							<?php if ($is_sub_depot) { ?>
								<th>
									<input type="checkbox" name="del-after-a" checked="checked" /><b>&nbsp;&nbsp;<?php _e('Delete after successful import, or if the album already exits.', 'wppa'); ?></b>
								</th>
							<?php } ?>
						</tr>
					</thead>
				</table>
				<table class="form-table albumtable"  style="margin-top:0;" >
					<tr>
						<?php
						$ct = 0;
						$idx = '0';
						foreach ($files as $file) {
							$ext = strtolower(substr(strrchr($file, "."), 1));
							if ($ext == 'amf') { ?>
								<td>
									<input type="checkbox" name="file-<?php echo($idx) ?>" class="wppa-amf" checked="checked" />&nbsp;&nbsp;<?php echo(basename($file)); ?>&nbsp;<?php echo(stripslashes(wppa_get_meta_name($file, '('))) ?>
								</td>
								<?php if ($ct == 3) {
									echo('</tr><tr>'); 
									$ct = 0;
								}
								else {
									$ct++;
								}
							}
							$idx++;
						} ?>
					</tr>
				</table>
</div>
				<?php }
				
				// Display the single photos
				if ($photocount > '0') { ?>
<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
				<p><b>
					<?php _e('There are', 'wppa'); echo(' '.$photocount.' '); _e('photos in the depot.', 'wppa'); if ( $wppa_opt['wppa_resize_on_upload'] == 'yes' ) { echo(' '); _e('Photos will be downsized during import.', 'wppa'); } ?><br/>
				</b></p>
				<p class="hideifupdate" >
					<?php _e('Default album for import:', 'wppa') ?>
					<select name="wppa-album" id="wppa-album">
						<option value=""><?php _e('- select an album -', 'wppa') ?></option>
						<?php echo(wppa_album_select('', '', false, false, false, false, false, true)) ?>
					</select>
					<?php _e('Photos that have (<em>name</em>)[<em>album</em>] will be imported by that <em>name</em> in that <em>album</em>.', 'wppa') ?>
				</p>
		<?php if ( $wppa_opt['wppa_watermark_on'] == 'yes' && ( $wppa_opt['wppa_watermark_user'] == 'yes' || current_user_can('wppa_settings') ) ) { ?>
				<p>
					<?php _e('Apply watermark file:', 'wppa') ?>
					<select name="wppa-watermark-file" id="wppa-watermark-file">
						<?php echo(wppa_watermark_file_select()) ?>
					</select>
					<?php _e('Position:', 'wppa') ?>
					<select name="wppa-watermark-pos" id="wppa-watermark-pos">
						<?php echo(wppa_watermark_pos_select()) ?>
					</select>
				</p>
		<?php } ?>
				<table class="form-table albumtable widefat" style="margin-bottom:0;" >
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="all-pho" checked="checked" onchange="checkAll('all-pho', '.wppa-pho')" /><b>&nbsp;&nbsp;<?php _e('Check/uncheck all', 'wppa') ?></b>
							</th>
							<?php if ($is_sub_depot) { ?>
								<th>
									<input type="checkbox" name="del-after-p" checked="checked" /><b>&nbsp;&nbsp;<?php _e('Delete after successful import.', 'wppa'); ?></b>
								</th>
							<?php } ?>
							<th>
								<input type="checkbox" id="wppa-upd" onchange="impUpd(this, '#submit')" name="wppa-update"><b>&nbsp;&nbsp;<?php _e('Update existing photos', 'wppa') ?></b>
							</th>
							<th>
								<input type="checkbox" id="wppa-nodups" name="wppa-nodups" checked="checked" ><b>&nbsp;&nbsp;<?php _e('Do not create duplicates', 'wppa') ?></b>
							</th>
						</tr>
					</thead>
				</table>				
				<table class="form-table albumtable" style="margin-top:0;" >
					<tr> 
						<?php
						$ct = 0;
						$idx = '0';
						foreach ($files as $file) {
							$ext = strtolower(substr(strrchr($file, "."), 1));
							$meta =	substr($file, 0, strlen($file)-3).'pmf';
							if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') { ?>
								<td>
									<input type="checkbox" name="file-<?php echo($idx) ?>" class= "wppa-pho" <?php if ($is_sub_depot) echo('checked="checked"') ?> />&nbsp;&nbsp;<?php echo(basename($file)); ?>&nbsp;<?php echo(stripslashes(wppa_get_meta_name($meta, '('))) ?><?php echo(stripslashes(wppa_get_meta_album($meta, '['))) ?>
								</td>
								<?php if ($ct == 3) {
									echo('</tr><tr>'); 
									$ct = 0;
								}
								else {
									$ct++;
								}
							}
							$idx++;
						} ?>
					</tr>
				</table>
</div>
				<?php } 
				// Display the directories to be imported as albums. Do this in the depot only!!
				if ( $is_depot && $dircount > '0' ) { ?>
<div style="border:1px solid gray; padding:4px; margin: 3px 0;" >
				<p><b>
					<?php _e('There are', 'wppa'); echo(' '.$dircount.' '); _e('albumdirectories in the depot.', 'wppa') ?><br/>
				</b></p>
				<table class="form-table albumtable widefat" style="margin-bottom:0;" >
					<thead>
						<tr>
							<th>
								<input type="checkbox" id="all-dir" checked="checked" onchange="checkAll('all-dir', '.wppa-dir')" /><b>&nbsp;&nbsp;<?php _e('Check/uncheck all', 'wppa') ?></b>
							</th>
<!--							<th>
								<input type="checkbox" id="wppa-crepag" name="wppa-crepag" checked="checked" ><b>&nbsp;&nbsp;<?php _e('Create WP page for album to be displayed.', 'wppa') ?></b>
							</th>
-->
						</tr>
					</thead>
				</table>				
				<table class="form-table albumtable" style="margin-top:0;" >
				<?php 
				$ct = 0; 
				$idx = '0';
		//		$subdircount = '0';
				foreach( $files as $dir ) { 
					if ( basename($dir) == '.' ) {}
					elseif ( basename($dir) == '..' ) {}
					elseif ( is_dir($dir) ) { ?>
						<tr>
							<td>
								<input type="checkbox" name="file-<?php echo($idx) ?>" class= "wppa-dir" checked="checked" />&nbsp;&nbsp;<b><?php echo(basename($dir)) ?></b>
								<?php
									$subfiles = glob($dir.'/*');
									$subdircount = '0';
									if ( $subfiles ) foreach ( $subfiles as $subfile ) if ( is_dir($subfile) && basename($subfile) != '.' && basename($subfile) != '..' ) $subdircount++;
									$sfcount = empty($subfiles) ? '0' : wppa_get_photocount($subfiles);
									echo ' Contains '.$sfcount.' files';
									if ( $subdircount ) echo ' and '.$subdircount.' sub directories.';
								?>
							</td>
						</tr>
				<?php 
					}
					$idx++;
				}?>
				</table>
</div>				
			<?php } ?>
				<?php
				// The submit button
				?>
				<p>
					<input type="submit" class="button-primary" id="submit" name="wppa-import-submit" value="<?php _e('Import', 'wppa'); ?>" />
				</p>
				</form>

		<?php }
		else {
			if (PHP_VERSION_ID >= 50207) {
				wppa_ok_message(__('There are no archives, albums or photos in directory:', 'wppa').' '.$source_url);
			}
			else {
				wppa_ok_message(__('There are no albums or photos in directory:', 'wppa').' '.$source_url);
			}
		}
	}
	else { ?>
		<?php $url = wppa_dbg_url(get_admin_url().'admin.php?page=wppa_admin_menu'); ?>
		<p><?php _e('No albums exist. You must', 'wppa'); ?> <a href="<?php echo($url) ?>"><?php _e('create one', 'wppa'); ?></a> <?php _e('beofre you can upload your photos.', 'wppa'); ?></p>
<?php } 
	if ( $wppa['continue'] ) {
		wppa_warning_message(__('Trying to continue...', 'wppa'));
		echo '<script type="text/javascript">document.location=\''.get_admin_url().'admin.php?page=wppa_import_photos&continue&nonce='.wp_create_nonce('dirimport').'\';</script>';
	}
?>
	</div>
<?php
}

// Upload multiple photos
function wppa_upload_multiple() {
	global $wpdb;
	global $warning_given;

	$warning_given = false;
	$uploaded_a_file = false;
	
	$count = '0';
	foreach ($_FILES as $file) {
		if ( is_array($file['error']) ) {
			for ($i = '0'; $i < count($file['error']); $i++) {
				if ( wppa_is_time_up() ) {
					wppa_error_message(sprintf(__('Time is up. %s photos uploaded in album nr %s.', 'wppa'), $count, $_POST['wppa-album']));
					wppa_set_last_album($_POST['wppa-album']);
					return;
				}
				if ( ! $file['error'][$i] ) {
					if (wppa_insert_photo($file['tmp_name'][$i], $_POST['wppa-album'], $file['name'][$i])) {
						$uploaded_a_file = true;
						$count++;
					}
					else {
						wppa_error_message(__('Error inserting photo', 'wppa') . ' ' . basename($file['name'][$i]) . '.');
						return;
					}
				}
			}
		}
	}
	
	if ($uploaded_a_file) { 
		wppa_update_message($count.' '.__('Photos Uploaded in album nr', 'wppa') . ' ' . $_POST['wppa-album']);
		wppa_set_last_album($_POST['wppa-album']);
    }
}

// Upload single photos 
function wppa_upload_photos() {
	global $wpdb;
	global $warning_given;

	$warning_given = false;
	$uploaded_a_file = false;
	
	$count = '0';
	foreach ($_FILES as $file) {
		if ($file['tmp_name'] != '') {
			if (wppa_insert_photo($file['tmp_name'], $_POST['wppa-album'], $file['name'])) {
				$uploaded_a_file = true;
				$count++;
			}
			else {
				wppa_error_message(__('Error inserting photo', 'wppa') . ' ' . basename($file['name']) . '.');
				return;
			}
		}
	}
	
	if ($uploaded_a_file) { 
		wppa_update_message($count.' '.__('Photos Uploaded in album nr', 'wppa') . ' ' . $_POST['wppa-album']);
		wppa_set_last_album($_POST['wppa-album']);
    }
}

function wppa_upload_zip() {
global $target;

	$file = $_FILES['file_zip'];
	$name = $file['name'];
	$type = $file['type'];
	$error = $file['error'];
	$size = $file['size'];
	$temp = $file['tmp_name'];
	$target = WPPA_DEPOT_PATH.'/'.$name;
	
	copy($temp, $target);
	
	if ($error == '0') wppa_ok_message(__('Zipfile', 'wppa').' '.$name.' '.__('sucessfully uploaded.', 'wppa'));
	else wppa_error_message(__('Error', 'wppa').' '.$error.' '.__('during upload.', 'wppa'));
	
	return $error;
}

function wppa_import_photos($delp = false, $dela = false, $delz = false) {
global $wpdb;
global $warning_given;
global $wppa;

	$warning_given = false;
	
	// Get this users current source directory setting
	$user = wppa_get_user();
	$source = get_option('wppa_import_source_'.$user, WPPA_DEPOT); // removed /$user

	$depot = ABSPATH . $source;	// Filesystem
	$depoturl = get_bloginfo('wpurl').'/'.$source;	// url

	// See what's in there
	$files = glob($depot.'/*');

	// First extract zips if our php version is ok
	$idx='0';
	$zcount = 0;
	if (PHP_VERSION_ID >= 50207) {
		foreach($files as $zipfile) {
			if (isset($_POST['file-'.$idx])) {
				$ext = strtolower(substr(strrchr($zipfile, "."), 1));
				
				if ($ext == 'zip') {
					$err = wppa_extract($zipfile, $delz);
					if ($err == '0') $zcount++;
				} // if ext = zip			
			} // if isset
			$idx++;
		} // foreach
	}
	
	// Now see if albums must be created
	$idx='0';
	$acount = 0;
	foreach($files as $album) {
		if (isset($_POST['file-'.$idx])) {
			$ext = strtolower(substr(strrchr($album, "."), 1));
			if ($ext == 'amf') {
				$name = '';
				$desc = '';
				$aord = '0';
				$parent = '0';
				$porder = '0';
				$owner = '';
				$handle = fopen($album, "r");
				if ($handle) {
					$buffer = fgets($handle, 4096);
					while (!feof($handle)) {
						$tag = substr($buffer, 0, 5);
						$len = strlen($buffer) - 6;	// substract 5 for label and one for eol
						$data = substr($buffer, 5, $len);
						switch($tag) {
							case 'name=':
								$name = $data;
								break;
							case 'desc=':
								$desc = wppa_txt_to_nl($data);
								break;
							case 'aord=':
								if (is_numeric($data)) $aord = $data;
								break;
							case 'prnt=':
								if ($data == __('--- none ---', 'wppa')) $parent = '0';
								elseif ($data == __('--- separate ---', 'wppa')) $parent = '-1';
								else {
									$prnt = wppa_get_album_id($data);
									if ($prnt != '') {
										$parent = $prnt;
									}
									else {
										$parent = '0';
										wppa_warning_message(__('Unknown parent album:', 'wppa').' '.$data.' '.__('--- none --- used.', 'wppa'));
									}
								}
								break;
							case 'pord=':
								if (is_numeric($data)) $porder = $data;
								break;
							case 'ownr=':
								$owner = $data;
								break;
						}
						$buffer = fgets($handle, 4096);
					} // while !foef
					fclose($handle);
					if (wppa_get_album_id($name) != '') {
						wppa_warning_message('Album already exists '.stripslashes($name));
						if ($dela) unlink($album);
					}
					else {
						$id = basename($album);
						$id = substr($id, 0, strpos($id, '.'));
						if (!wppa_is_id_free('album', $id)) $id = wppa_nextkey(WPPA_ALBUMS);
						$query = $wpdb->prepare( "INSERT INTO `" . WPPA_ALBUMS . "` (`id`, `name`, `description`, `a_order`, `a_parent`, `p_order_by`, `main_photo`, `cover_linktype`, `cover_linkpage`, `owner`, `timestamp`, `default_tags`, `cover_type`, `suba_order_by`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '', '', '')", $id, stripslashes($name), stripslashes($desc), $aord, $parent, $porder, '0', 'content', '0', $owner, time());
						$iret = $wpdb->query($query);

						if ($iret === FALSE) wppa_error_message(__('Could not create album.', 'wppa'));
						else {
							//$id = wppa_get_album_id($name);
							wppa_set_last_album($id);
							wppa_index_add('album', $id);
							wppa_ok_message(__('Album #', 'wppa') . ' ' . $id . ': '.stripslashes($name).' ' . __('Added.', 'wppa'));
							if ($dela) unlink($album);
							$acount++;
							wppa_clear_cache();
							wppa_flush_treecounts($id);
						} // album added
					} // album did not exist
				} // if handle (file open)
			} // if its an album
		} // if isset
		$idx++;
	} // foreach file
	
	// Now the photos
	$idx='0';
	$pcount = '0';
	$totpcount = '0';
	if (isset($_POST['wppa-album'])) $album = $_POST['wppa-album']; else $album = '0';

	wppa_ok_message(__('Processing files, please wait...', 'wppa').' '.__('If the line of dots stops growing or your browser reports Ready, your server has given up. In that case: try again', 'wppa').' <a href="'.wppa_dbg_url(get_admin_url().'admin.php?page=wppa_import_photos').'">'.__('here.', 'wppa').'</a>');
	foreach ($files as $file) {

		if (isset($_POST['file-'.$idx])) {
			$ext = strtolower(substr(strrchr($file, "."), 1));
			if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') {
				// See if a metafile exists
				$meta = substr($file, 0, strlen($file) - 3).'pmf';
				// find all data: name, desc, porder form metafile
				if (is_file($meta)) {
					$alb = wppa_get_album_id(wppa_get_meta_album($meta));
					$name = wppa_get_meta_name($meta);
					$desc = wppa_txt_to_nl(wppa_get_meta_desc($meta));
					$porder = wppa_get_meta_porder($meta);
					$linkurl = wppa_get_meta_linkurl($meta);
					$linktitle = wppa_get_meta_linktitle($meta);
				}
				else {
					$alb = $album;	// default album
					$name = '';		// default name
					$desc = '';		// default description
					$porder = '0';	// default p_order
					$linkurl = '';
					$linktitle = '';
				}
				// Update the photo ?
				if (isset($_POST['wppa-update'])) { 
					$iret = wppa_update_photo($file, $name);
					if ( $iret ) {
						$pcount++;
						$totpcount += $iret;
						if ($delp) {
							unlink($file);
						}
					}
				} 
				// Insert the photo
				else { 
					if (is_numeric($alb) && $alb != '0') {
						$id = basename($file);
						$id = substr($id, 0, strpos($id, '.'));
						if (!is_numeric($id) || !wppa_is_id_free('photo', $id)) $id = 0;
						if (wppa_insert_photo($file, $alb, stripslashes($name), stripslashes($desc), $porder, $id, stripslashes($linkurl), stripslashes($linktitle))) {

							$pcount++;
							if ($delp) {
								unlink($file);
								if (is_file($meta)) unlink($meta);
							}
						}
						else {
							wppa_error_message(__('Error inserting photo', 'wppa') . ' ' . basename($file) . '.');
						}
					}
					else {
						wppa_error_message(sprintf(__('Error inserting photo %s, unknown or non existent album.', 'wppa'), basename($file)));
					} 
				} // Insert
			}
		}
		$idx++;
		if ( wppa_is_time_up() ) {
			wppa_error_message(sprintf(__('Time is up. %s photos imported. Please restart this operation.', 'wppa'), $pcount));
			wppa_set_last_album($album);
			return;
		}
	} // foreach $files
	
	// Now the dirs to album imports
	
	$idx='0';
	$dircount = '0';
	global $photocount;
	$photocount = '0';
	$iret = true;
//	$files = glob($depot.'/*');

	foreach ($files as $file) {
		if ( basename($file) != '.' &&  basename($file) != '..' && ( isset($_POST['file-'.$idx]) || isset($_GET['continue']) ) ) {
			if ( is_dir($file) ) {
				$iret = wppa_import_dir_to_album($file, '0');
				if ( wppa_is_time_up() && wppa_switch('wppa_auto_continue') ) {
					$wppa['continue'] = 'continue';
				}
				$dircount++;
			}
		}
		$idx++;
		if ( $iret == false ) break;	// Time up
	}	
	
	
	wppa_ok_message(__('Done processing files.', 'wppa'));
	
	if ($pcount == '0' && $acount == '0' && $zcount == '0' && $dircount == '0' && $photocount == '0') {
		wppa_error_message(__('No files to import.', 'wppa'));
	}
	else {
		$msg = '';
		if ($zcount) $msg .= $zcount.' '.__('Zipfiles extracted.', 'wppa').' ';
		if ($acount) $msg .= $acount.' '.__('Albums created.', 'wppa').' ';
		if ($dircount) $msg .= $dircount.' '.__('Directory to album imports.', 'wppa').' ';
		if ($photocount) $msg .= ' '.sprintf(__('With total %s photos.','wppa'), $photocount).' ';
		if ($pcount) {
			if (isset($_POST['wppa-update'])) {
				$msg .= $pcount.' '.__('Photos updated', 'wppa');
				if ( $totpcount != $pcount ) {
					$msg .= ' '.sprintf(__('to %s locations', 'wppa'), $totpcount);
				}
				$msg .= '.';
			}
			else $msg .= $pcount.' '.__('single photos imported.', 'wppa').' '; 
		}
		wppa_ok_message($msg); 
		wppa_set_last_album($album);
	}
}

function wppa_get_zipcount($files) {
	$result = 0;
	if ($files) {
		foreach ($files as $file) {
			$ext = strtolower(substr(strrchr($file, "."), 1));
			if ($ext == 'zip') $result++;
		}
	}
	return $result;
}

function wppa_get_albumcount($files) {
	$result = 0;
	if ($files) {
		foreach ($files as $file) {
			$ext = strtolower(substr(strrchr($file, "."), 1));
			if ($ext == 'amf') $result++;
		}
	}
	return $result;
}

function wppa_get_photocount($files) {
	$result = 0;
	if ($files) {
		foreach ($files as $file) {
			$ext = strtolower(substr(strrchr($file, "."), 1));
			if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') $result++;
		}
	}
	return $result;
}

// Find dir is new album candidates
function wppa_get_dircount($files) {
	$result = 0;
	if ( $files ) {
		foreach ( $files as $file ) {
			if ( basename($file) == '.' ) {}
			elseif ( basename($file) == '..' ) {}
			elseif ( is_dir($file) ) $result++;
		}
	}
	return $result;
}

function wppa_get_meta_name($file, $opt = '') {
	return wppa_get_meta_data($file, 'name', $opt);
}
function wppa_get_meta_album($file, $opt = '') {
	return wppa_get_meta_data($file, 'albm', $opt);
}
function wppa_get_meta_desc($file, $opt = '') {
	return wppa_get_meta_data($file, 'desc', $opt);
}
function wppa_get_meta_porder($file, $opt = '') {
	return wppa_get_meta_data($file, 'pord', $opt);
}
function wppa_get_meta_linkurl($file, $opt = '') {
	return wppa_get_meta_data($file, 'lnku', $opt);
}
function wppa_get_meta_linktitle($file, $opt = '') {
	return wppa_get_meta_data($file, 'lnkt', $opt);
}

function wppa_get_meta_data($file, $item, $opt) {
	$result = '';
	$opt2 = '';
	if ($opt == '(') $opt2 = ')';
	if ($opt == '{') $opt2 = '}';
	if ($opt == '[') $opt2 = ']';
	if (is_file($file)) {
		$handle = fopen($file, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				if (substr($buffer, 0, 5) == $item.'=') {
					if ($opt == '') $result = substr($buffer, 5, strlen($buffer)-6);
					else $result = $opt.wppa_qtrans(substr($buffer, 5, strlen($buffer)-6)).$opt2;		// Translate for display purposes only
				}
			}
			if (!feof($handle)) {
				_e('Error: unexpected fgets() fail in wppa_get_meta_data().', 'wppa');
			}
			fclose($handle);
		}
	}
	return $result;
}


function wppa_extract($path, $delz) {
// There are two reasons that we do not allow the directory structure from the zipfile to be restored.
// 1. we may have no create dir access rights.
// 2. we can not reach the pictures as we only glob the users depot and not lower.
// We extract all files to the users depot. 
// The illegal files will be deleted there by the wppa_sanitize_files routine, 
// so there is no chance a depot/subdir/destroy.php or the like will get a chance to be created.
// dus...

	$err = '0';
	if (!class_exists('ZipArchive')) {
		$err = '3';
		wppa_error_message(__('Class ZipArchive does not exist! Check your php configuration', 'wppa'));
	}
	else {
		$ext = strtolower(substr(strrchr($path, "."), 1));
		if ($ext == 'zip') {
			$zip = new ZipArchive;
			if ($zip->open($path) === true) {
				$zip->extractTo(WPPA_DEPOT_PATH);
				$zip->close();
				wppa_ok_message(__('Zipfile', 'wppa').' '.basename($path).' '.__('extracted.', 'wppa'));
				if ($delz) unlink($path);
			} else {
				wppa_error_message(__('Failed to extract', 'wppa').' '.$path);
				$err = '1';
			}
		}
		else $err = '2';
	}
	return $err;
}

function wppa_import_dir_to_album($file, $parent) {
global $photocount;
global $wpdb;
global $wppa_opt;
	
	// see if album exists
	if ( is_dir($file) ) {
		$alb = wppa_get_album_id(basename($file));
		if ( !$alb ) {	// Album must be created
			$alb 	= wppa_nextkey(WPPA_ALBUMS);
			$id 	= $alb;
			$name	= basename($file);
			$uplim	= $wppa_opt['wppa_upload_limit_count'].'/'.$wppa_opt['wppa_upload_limit_time'];
			$query = $wpdb->prepare("INSERT INTO `" . WPPA_ALBUMS . "` (`id`, `name`, `description`, `a_order`, `a_parent`, `p_order_by`, `main_photo`, `cover_linktype`, `cover_linkpage`, `owner`, `timestamp`, `upload_limit`, `alt_thumbsize`, `default_tags`, `cover_type`, `suba_order_by`) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '', '', '')", $id, $name, '', '0', $parent, '0', '0', 'content', '0', wppa_get_user(), time(), $uplim, '0');
			$iret = $wpdb->query($query);
			if ($iret === FALSE) {
				wppa_error_message(__('Could not create album.', 'wppa').'<br/>Query = '.$query);
				wp_die('Sorry, cannot continue');
			}
			else {
				$edit_id = $id;
				wppa_set_last_album($edit_id);
				wppa_flush_treecounts($edit_id);
				wppa_index_add('album', $id);
				wppa_ok_message(__('Album #', 'wppa') . ' ' . $edit_id . ' ('.$name.') ' . __('Added.', 'wppa'));
				if ( $wppa_opt['wppa_newpag_create'] == 'yes' /*isset($_POST['wppa-crepag']) */ && $parent <= '0' ) {
				
					// Create post object
					$my_post = array(
					  'post_title'    => $name,
					  'post_content'  => str_replace('w#album', $edit_id, $wppa_opt['wppa_newpag_content']),
					  'post_status'   => $wppa_opt['wppa_newpag_status'],
					  'post_type'	  => $wppa_opt['wppa_newpag_type']
					);

					// Insert the post into the database
					$pagid = wp_insert_post( $my_post );
					if ( $pagid ) {
						wppa_ok_message(sprintf(__('Page <a href="%s" target="_blank" >%s</a> created.', 'wppa'), home_url().'?page_id='.$pagid, $name));
						$wpdb->query($wpdb->prepare("UPDATE `".WPPA_ALBUMS."` SET `cover_linkpage` = %s WHERE `id` = %s", $pagid, $alb));
					}
					else {
						wppa_error_message(__('Could not create page.', 'wppa'));
					}
				}
			}
		}
		
		// Now import the files
		$photofiles = glob($file.'/*');
		if ( $photofiles ) foreach ( $photofiles as $photofile ) {
			if ( ! is_dir($photofile) ) {
				if ( wppa_albumphoto_exists($alb, basename($photofile)) ) {
					wppa_error_message('Photo '.basename($photofile).' already exists in album '.$alb.'. Removed.');
					unlink($photofile);
				}
				else {
					$bret = wppa_insert_photo($photofile, $alb, basename($photofile));
					if ( ! $bret ) return false;	// Time up
					unlink($photofile);
					$photocount++;
				}
				if ( wppa_is_time_up($photocount) ) return false;
			}
		}
		
		// Now go deeper, process the subdirs
		$subdirs = glob($file.'/*');
		if ( $subdirs ) foreach ( $subdirs as $subdir ) {
			if ( is_dir($subdir) ) {
				if ( basename($subdir) != '.' && basename($subdir) != '..' ) {
					$bret = wppa_import_dir_to_album($subdir, $alb);
					if ( ! $bret ) return false;	// Time up
				}
			}
		}
		@ rmdir($file);	// Try to remove dir, ignore error
	}
	else {
		wppa_dbg_msg('Invalid file in wppa_import_dir_to_album(): '.$file);
		return false;
	}
	return true;
}