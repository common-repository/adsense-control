<?php
	add_action( 'add_meta_boxes', 'ac_add_custom_box' );
	function ac_add_custom_box() {
	add_meta_box(
	  'ac_meta_box',
	  'Adsense',
	  'ac_meta_box',
	  'post',
	  'side',
	  'default'
	);
	}
	function ac_meta_box() {
		global $post;
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="eventmeta_noncename" id="eventmeta_noncename" value="'.wp_create_nonce(plugin_basename(__FILE__) ).'" />';

		$value = get_post_meta($post->ID, 'ac_meta_box_active', true);
		
		$checked_3 = ($value=='3') ? " checked='checked' " : "" ;
		$checked_2 = ($value=='2') ? " checked='checked' " : "" ;
		$checked_1 = ($value=='1') ? " checked='checked' " : "" ;
		$checked_0 = ($value=='0') ? " checked='checked' " : "" ;
		if(empty($checked_3) && empty($checked_2) && empty($checked_1) && empty($checked_0)){
			$checked_2 = " checked='checked' ";
		}

		echo "	<p><ul>";
		echo "		<li><input type=\"radio\" ".$checked_3." name=\"ac_meta_box_active\" value=\"3\" /> Ativar somente para o post</li>";
		echo "		<li><input type=\"radio\" ".$checked_2." name=\"ac_meta_box_active\" value=\"2\" /> Ativar somente para o Google</li>";
		echo "		<li><input type=\"radio\" ".$checked_1." name=\"ac_meta_box_active\" value=\"1\" /> Ativar para ambos</li>";
		echo "		<li><input type=\"radio\" ".$checked_0." name=\"ac_meta_box_active\" value=\"0\" /> Desativar</li>";
		echo "	</ul></p>";
	}

	function ac_meta_box_save($post_id, $post) {
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if ( !wp_verify_nonce( $_POST['eventmeta_noncename'], plugin_basename(__FILE__) )) {
	  return $post->ID;
	}

	// Is the user allowed to edit the post or page?
	if ( !current_user_can( 'edit_post', $post->ID ))
	  return $post->ID;

	// OK, we're authenticated: we need to find and save the data
	// We'll put it into an array to make it easier to loop though.
	$events_meta['ac_meta_box_active'] = $_POST['ac_meta_box_active'];

	// Add values of $events_meta as custom fields
	foreach ($events_meta as $key => $value) { // Cycle through the $events_meta array!
	  if( $post->post_type == 'revision' ) return; // Don't store custom data twice
	  $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
	  if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
		update_post_meta($post->ID, $key, $value);
	  } else { // If the custom field doesn't have a value
		add_post_meta($post->ID, $key, $value);
	  }
	  if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
	}
	add_action('save_post', 'ac_meta_box_save', 1, 2);
?>