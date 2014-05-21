<?php
add_action('admin_menu','Readd_admin_menu');
function Readd_admin_menu(){
	add_theme_page('主题设置','主题设置','edit_themes',basename(__FILE__),'Readd_setting_page');
	add_action('admin_init','Readd_setting');
}

function Readd_setting(){
	register_setting('Readd_setting_group','Readd_options');
}

function Readd_setting_page(){
	if ( isset($_REQUEST['settings-updated']) )
		echo '<div id="message" class="updated fade"><p><strong>主题设置已保存!</strong></p></div>';
	if ( 'reset' == isset($_REQUEST['reset']) ){
		delete_option('Readd_options');
		echo '<div id="message" class="updated fade"><p><strong>主题设置已重置!</strong></p></div>';
	}
	?>
	<div class="wrap" style="width:600px;margin:100px auto;padding:40px;border:1px solid #ededed;background:#fff;">
		<div id="icon-options-general" class="icon32"><br></div><h2>主题设置</h2><br>
		<form method="post" action="options.php">
			<?php settings_fields('Readd_setting_group'); ?>
			<?php $options = get_option('Readd_options'); ?>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label>公告：</label></th>
						<td>
							<p>显示在分类菜单下方的通知</p>
							<p><textarea type="textarea" name="Readd_options[news]" class="large-text"><?php echo $options['news']; ?></textarea></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>主菜单下方图片地址（600*195）：</label></th>
						<td>
							<p><input type="text" name="Readd_options[face]" class="large-text" value="<?php echo $options['face']; ?>" /></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>网站背景图片地址：</label></th>
						<td>
							<p><input type="text" name="Readd_options[site_bg]" class="large-text" value="<?php echo $options['site_bg']; ?>" /></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label>幻灯地址：</label></th>
						<td>
							<p>随机文章幻灯片的背景图片地址（600*195大小）</p>
							<p><input type="text" name="Readd_options[bg1]" class="bg-text large-text" value="<?php echo $options['bg1']; ?>" /></p>
							<p><input type="text" name="Readd_options[bg2]" class="bg-text large-text" value="<?php echo $options['bg2']; ?>" /></p>
							<p><input type="text" name="Readd_options[bg3]" class="bg-text large-text" value="<?php echo $options['bg3']; ?>" /></p>
							<p><input type="text" name="Readd_options[bg4]" class="bg-text large-text" value="<?php echo $options['bg4']; ?>" /></p>
							<p><input type="text" name="Readd_options[bg5]" class="bg-text large-text" value="<?php echo $options['bg5']; ?>" /></p>
							<p><input type="text" name="Readd_options[bg6]" class="bg-text large-text" value="<?php echo $options['bg6']; ?>" /></p>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="Readd_submit_form">
				<input type="submit" name="save" class="button-primary Readd-submit-btm" value="<?php _e('Save Changes'); ?>" />
			</div>
		</form>
		<form method="post" style="margin-top:10px;">
			<div class="Readd_reset_from">
				<input type="submit" name="reset" value="<?php _e('Reset') ?>" class="button-primary Readd-reset-btn" />
				<input type="hidden" name="reset" value="reset" />
			</div>
		</form>
		<p>有问题请发邮件咨询：mengjianzhizi@gmail.com，或访问www.dearzd.com留言。<p>
	</div>
	<?php
}
?>