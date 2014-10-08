<?php
	add_action('admin_menu', 'easy_gallery_menu');
	add_action( 'admin_init', 'easy_gallery_admin_init' );

	function easy_gallery_admin_init()
	{
		wp_enqueue_script( 'jquery-form');
	}
	
	function easy_gallery_menu()
	{
		add_menu_page ('Easy Gallery', "Easy Gallery", 'manage_options','easy_gallery', 'easy_gallery',get_option("siteurl")."/wp-content/plugins/simple-gallery-odihost/images/gallery.png"); 
		add_submenu_page('easy_gallery', 'Easy Gallery', 'Easy Gallery', 'manage_options', 'easy_gallery', 'easy_gallery',get_option("siteurl")."/wp-content/plugins/simple-gallery-odihost/images/gallery.png");
		add_submenu_page('easy_gallery', 'Easy Gallery Settings', 'Settings', 'manage_options', 'easy_gallery_settings', 'easy_gallery_settings',get_option("siteurl")."/wp-content/plugins/simple-gallery-odihost/images/gallery.png");
	}
	
	function easy_gallery_settings()
	{
		if(!empty($_POST) && isset($_POST['lightbox']))
		{
			if($_POST['lightbox'] == "on")
			{
				update_option('lightbox_theme','on');
				$message = "<strong>Lightbox theme on</strong>";
			}
			else
			{
				update_option('lightbox_theme','off');
				$message = "<strong>Lightbox theme off</strong>";
			}
			echo "<div class='admin-message update-nag'>$message</div>";	
		}
		?>
			<link rel='stylesheet' href='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/admin.css' type='text/css' media='all' />
			<div class="wrap">
				<h2>Settings</h2>
				<br />				
				<form method="post">
					<input type="hidden" name="lightbox" value="off" />
					<input type="checkbox" name="lightbox" title="Check this to enable lightbox theme on image thumbnail gallery" value="on" <?php if(get_option('lightbox_theme') == "on") echo "checked"; ?>/>&nbsp;<label>Use Lightbox Theme</label> <p class="explanation">- Check to enable lightbox theme on thumbnail gallery only</p>	
					<br />
					<br />
					<input type="submit" title="Click to save settings" value="Save" class="button button-primary">
				</form>
			</div>
		<?php
	}
	function easy_gallery()
	{
		?>
		<div class='admin-message update-nag'>If you have any problem with the plugin or need customization please contact us <a href='http://odihost.com/contact-us'>here</a>. </div>
		<link rel='stylesheet' href='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/admin.css' type='text/css' media='all' />
		<?php
		if($_REQUEST["task"] == "add")
		{			
			edit_gallery(0);
		}
		else if($_REQUEST["task"] == "duplicate_gallery")
		{
			duplicate_gallery($_REQUEST["id"]);
			echo "<div class='admin-message update-nag'><b>Gallery duplicated</b></div>";
			show_gallery_list();
		}
		else if($_REQUEST["task"] == "delete_gallery")
		{
			delete_gallery($_REQUEST["id"]);
			echo "<div class='admin-message update-nag'><b>Gallery deleted</b></div>";
			show_gallery_list();
		}
		else if($_REQUEST["task"] == "edit_gallery" && !isset($_POST['lightbox']))
		{
			edit_gallery($_REQUEST["id"]);
		}
		else if($_REQUEST["task"] == "save_gallery")
		{
			save_gallery();
			echo "<div class='admin-message update-nag'><b>Gallery updated</b></div>";
			show_gallery_list();
		}
		else
		{
			show_gallery_list();
		}
	}
	
	function duplicate_gallery($id)
	{
		global $wpdb;
		
		$wpdb->query("insert into easy_gallery(gallery_name, thumb_width, thumb_height, full_size_width, full_size_height, video_width, video_height, type, custom_css) select gallery_name, thumb_width, thumb_height, full_size_width, full_size_height, video_width, video_height, type, custom_css from easy_gallery where id = $id");
		
		$max_id = $wpdb->get_var("select max(id) from easy_gallery");		
		$wpdb->query("insert into easy_gallery_line(gallery_id, file_name, video_url, order_no) select ". $max_id.", file_name, video_url, order_no from easy_gallery_line where gallery_id = ". $id ."");
		
		/*copy files*/
		$oldpath = dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id/";
		$newpath = dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$max_id/";
		if(!is_dir($newpath)) mkdir($newpath,0777,true);
		
		$filenames = $wpdb->get_results("select file_name from easy_gallery_line where gallery_id = $max_id and file_name is not null order by order_no");
		foreach ($filenames as $filename)
		{
			copy($oldpath . $filename->file_name, $newpath . $filename->file_name);
		}
		/* end copy files*/	
	}
	
	function delete_gallery($id)
	{
		global $wpdb;
		$wpdb->query("delete from easy_gallery where id = " .$id);
		$wpdb->query("delete from easy_gallery_line where gallery_id = " .$id);
		
		$path = dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id/";
		
		if(is_dir($path)) rmdir_recursive($path);		 
	}
	
	function rmdir_recursive($dir) {
		foreach(scandir($dir) as $file) {
			if ('.' === $file || '..' === $file) continue;
			if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
			else unlink("$dir/$file");
		}
		rmdir($dir);
	}
	
	function save_gallery()
	{

		global $wpdb;
		$counter=0;
		
		if($_REQUEST["id"] =="")
		{
			$wpdb->query("insert into easy_gallery (gallery_name, type,custom_css,thumb_width,thumb_height,full_size_width,full_size_height,video_width,video_height) values('".$_REQUEST["gallery_name"]."','".$_REQUEST["type"]."','".$_REQUEST["custom_css"]."','".$_REQUEST["thumb_width"]."','".$_REQUEST["thumb_height"]."','".$_REQUEST["full_size_width"]."','".$_REQUEST["full_size_height"]."','".$_REQUEST["video_width"]."','".$_REQUEST["video_height"]."')");
			$id = $wpdb->get_var("select max(id) from easy_gallery");
			$type = $wpdb->get_var("select type from easy_gallery where id='".$id."'");
			
			if($type == 1 or $type == 2 or $type == 3)
			{
				$count = count($_FILES["imagefile"]["name"]);
				
				/* start images upload source code*/
				if(!is_dir(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/")) mkdir(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/",0777,true);

				$uploadpath = dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id";
				for($i=0;$i<$count;$i++)
				{
					$tempfile = $_FILES["imagefile"]["tmp_name"][$i];
					$filename = $_FILES["imagefile"]["name"][$i];
					$new_caption = $_REQUEST["new_caption"][$i];
				
					if(strpos(strtoupper($filename), ".JPG") || strpos(strtoupper($filename), ".GIF") || strpos(strtoupper($filename), ".PNG"))
					{
						//$helper->upload_file($_FILES[
						$filename = upload_file($tempfile, $filename,$uploadpath);
						$wpdb->query("insert into easy_gallery_line (gallery_id , file_name, caption, order_no) values(".$id.",'".$filename."', '$new_caption',$counter)");
						$counter++;
					}
				}
				/* end images upload source code*/
			}
			else if($type == 4)
			{
				$count = count($_REQUEST["video_url"]);
				
				/* start videos url upload source code*/
				for($i=0;$i<$count;$i++)
				{
					$url = $_REQUEST["video_url"][$i];
					$wpdb->query("insert into easy_gallery_line (gallery_id , video_url, order_no) values(".$id.",'".$url."',$counter)");
					$counter++;
				}
				/* end videos url upload source code*/
			}
		}
		else
		{	
			$type = $_REQUEST["type"];
			
			$id = $_REQUEST["id"];
			if(!is_dir(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/")) mkdir(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/",0777, true);
			$wpdb->query("update easy_gallery set gallery_name= '".$_REQUEST["gallery_name"]."', type='".$_REQUEST["type"]."',custom_css='".$_REQUEST["custom_css"]."',thumb_width= '".$_REQUEST["thumb_width"]."',thumb_height= '".$_REQUEST["thumb_height"]."',full_size_width= '".$_REQUEST["full_size_width"]."',full_size_height= '".$_REQUEST["full_size_height"]."', video_width = '".$_REQUEST["video_width"]."', video_height = '".$_REQUEST["video_height"]."' where id = ".$id);
			
			if($type == 1 or $type == 2 or $type == 3)
			{
				$count = count($_FILES["imagefile"]["name"]);				
				
				//delete images
				$files = explode(",",$_REQUEST["deletefiles"]);
				foreach($files as $file)
				{
					if($file!='')
					{
						$wpdb->query("delete from easy_gallery_line where gallery_id = " .$id." and file_name ='".$file."'");
						if(file_exists(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id/$file")) 
							unlink(dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id/$file");
					}
				}
				
				$uploadpath = dirname(dirname(dirname(dirname(__FILE__))))."/uploads/easy-gallery/$id";
				for($i=0;$i<$count;$i++)
				{
					$tempfile = $_FILES["imagefile"]["tmp_name"][$i];
					$filename = $_FILES["imagefile"]["name"][$i];
					$new_caption = $_REQUEST["new_caption"][$i];
				
					if(strpos(strtoupper($filename), ".JPG") || strpos(strtoupper($filename), ".GIF") || strpos(strtoupper($filename), ".PNG"))
					{
						//$helper->upload_file($_FILES[
						$filename = upload_file($tempfile, $filename,$uploadpath);
						$wpdb->query("insert into easy_gallery_line (gallery_id , file_name, caption, order_no) values(".$id.",'".$filename."', '$new_caption',$counter)");
						$counter++;
					}
				}
				
				//update image caption
				$captions = $_REQUEST["caption"];
				$caption_id = $_REQUEST["caption_id"];
				$index = 0;
				foreach($captions as $caption)
				{
					$wpdb->query("UPDATE easy_gallery_line SET caption = '".$caption."' WHERE id=".$caption_id[$index]."");
					$index++;
				}
			}
			else if($type == 4)
			{
				$count = count($_REQUEST["video_url"]);
				
				$result = $wpdb->query("delete from easy_gallery_line where gallery_id = '".$id."' and (video_url is not null and file_name is null)");
				/* start videos url upload source code*/
				if(is_numeric($result))
				{
					for($i=0;$i<$count;$i++)
					{
						$url = $_REQUEST["video_url"][$i];
						$wpdb->query("insert into easy_gallery_line (gallery_id , video_url, order_no) values(".$id.",'".$url."',$counter)");
						$counter++;
					}
				}				
				/* end videos url upload source code*/
			}
		}
	}
	
	function edit_gallery($id)
	{
		global $wpdb;
		$gallery = $wpdb->get_row("select * from easy_gallery where id =".$id); 
		$gallery_lines = $wpdb->get_results("select * from easy_gallery_line where gallery_id =".$id."  and file_name is not null order by order_no"); 
		$gallery_lines_url = $wpdb->get_results("select * from easy_gallery_line where gallery_id =".$id." and video_url != '' and video_url is not null order by order_no"); 
		$id_counter = count($gallery_lines_url) - 1;
	
		$counter =0;
		
		?>
		<script type="text/javascript">
			// window.onload = type_changed("type1");			 
			window.onload = function(){type_changed(<?php if($gallery->type > 0) echo $gallery->type; else echo "1"; ?>);};
		</script>
		<div class="wrap">
		<form method="post" enctype="multipart/form-data" id="gallery_form">
		<script src='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/jquery.MetaData.js' type="text/javascript" language="javascript"></script>
		<script src='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/jquery.MultiFile.js' type="text/javascript" language="javascript"></script>
		<script src='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/jquery.blockUI.js' type="text/javascript" language="javascript"></script>
		<script src="<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/jquery.tablednd_0_5.js" type="text/javascript" language="javascript"></script>
		<!-- <script src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js" type="text/javascript" language="javascript"></script> -->

		 <script type="text/javascript">
	 
		 jQuery(document).ready(function(){

			jQuery("#imagetable").tableDnD();

			});
					
			
			function check()
			{
				var files = "";
					jQuery('#imagetable tr').each(function() {
					 files += jQuery(this).find("td").eq(0).html() +",";    
				});

				jQuery("#orderedfiles").val(files);
			}
			function deletefile(str, counter)
			{
				jQuery("#file"+counter).hide();
				jQuery("#deletefiles").val(jQuery("#deletefiles").val() + str +",");
			}
		
			function type_changed(type)
			{
				if(type==1)
				{
					jQuery(".type1").show();
					jQuery(".type2").hide();
					jQuery(".type3").hide();
					jQuery("#image_lbl").show();
					jQuery("#imagefile").show();
					jQuery("#video_lbl").hide();
					jQuery(".video_url").hide();
					jQuery("#imagetable").show();
					jQuery("#input_video").hide();
					jQuery("#type_1").attr('checked', 'checked');
					jQuery("#imagefile_wrap").show();					
				}
				else if(type==2)
				{
					jQuery(".type2").show();
					jQuery(".type1").hide();
					jQuery(".type3").hide();
					jQuery("#image_lbl").show();
					jQuery("#imagefile").show();
					jQuery("#video_lbl").hide();
					jQuery(".video_url").hide();
					jQuery("#imagetable").show();
					jQuery("#input_video").hide();
					jQuery("#type_2").attr('checked', 'checked');
					jQuery("#imagefile_wrap").show();
				}
				else if(type==3)
				{
					jQuery(".type2").show();
					jQuery(".type1").show();
					jQuery(".type3").hide();
					jQuery("#image_lbl").show();
					jQuery("#imagefile").show();
					jQuery("#video_lbl").hide();
					jQuery(".video_url").hide();
					jQuery("#imagetable").show();
					jQuery("#input_video").hide();
					jQuery("#type_3").attr('checked', 'checked');
					jQuery("#imagefile_wrap").show();
				}
				else if(type==4)
				{
					jQuery(".type2").hide();
					jQuery(".type1").hide();
					jQuery(".type3").show();
					jQuery("#image_lbl").hide();
					jQuery("#imagefile").hide();
					jQuery("#video_lbl").show();
					jQuery(".video_url").show();
					jQuery("#imagetable").hide();
					jQuery("#input_video").show();
					jQuery("#type_4").attr('checked', 'checked');
					jQuery("#imagefile_wrap").hide();
				}
				
				 document.getElementById("hide").style.visibility="visible";
				 document.getElementById("loading").style.display="none";
			}			
		</script>		
		<script type="text/javascript">
			var id_counter = <?php echo $id_counter; ?>;
						
			function addRow(tableID) {
				id_counter++;
				var table = document.getElementById(tableID);
	 
				var rowCount = table.rows.length;
				var row = table.insertRow(rowCount);
				row.id = "url" + id_counter;
	 
				var cell1 = row.insertCell(0);
				var element1 = document.createElement("label");
				element1.className ="video_lbl"
				element1.innerHTML="Embedded Code:";
				cell1.appendChild(element1);
	 
				var cell2 = row.insertCell(1);
				var element2 = document.createElement("input");
				element2.type = "text";
				element2.name = "video_url[]";
				//element2.style= "width:300px;";
				element2.title = "Type your unique youtube code here";
				element2.className ="video_url"
				cell2.appendChild(element2); 
	 
				var cell3 = row.insertCell(2);
				var element3 = document.createElement("a");
				element3.href = "javascript:deleteRow('input_url','url"+id_counter+"')";
				element3.innerHTML = "[delete]";
				cell3.appendChild(element3); 
			}
			function deleteRow(tableID,rowID) {
				try {
				var table = document.getElementById(tableID);
				var rowCount = table.rows.length;
			
				for(var i=0; i<rowCount; i++) {
					if(table.rows[i].id == rowID)
					{
						table.deleteRow(i);
						break;
					}	
				}
				}catch(e) {
					alert(e);
				}
			}
		</script>
		<h2>Gallery</h2>
		<p id='loading'><em>Please wait...</em></p>
		<div id='hide' style='visibility:hidden;'>		
		<?php
		if($id > 0)
		{
			?>	<div class="row"><label>Shortcode:</label><input readonly type="text" name="" id="" title="Copy this shortcode into your post/page to show gallery" value="[easygallery id=<?php echo $id;?>]"></div>
			<?php
		}
		?>
		<div class="row"><label>Name:</label><input type="text" name="gallery_name" id="gallery_name" title="Type your gallery name here" value="<?php echo $gallery->gallery_name;?>"></div>
		<div class="row"><label>Type:</label>
			<div class="field">
				<input type="radio" name="type" id="type_1" onclick="type_changed(1);" title="Create image thumbnail gallery" value="1" <?php if($gallery->type==1) echo "checked";?>>Thumbnail&nbsp;<input type="radio" name="type" id="type_2" onclick="type_changed(2);" title="Create image gallery with slider" value="2" <?php if($gallery->type==2) echo "checked";?>>Big Image Slider&nbsp;<input type="radio" name="type" id="type_3" onclick="type_changed(3);" title="Create image thumbnail gallery with slider and large image as viewer" value="3" <?php if($gallery->type==3) echo "checked";?>>Thumbnail Slider&nbsp;<input type="radio" name="type" id="type_4" onclick="type_changed(4);" title="Create youtube video gallery" value="4" <?php if($gallery->type==4) echo "checked";?>>Youtube
				<div class="type1 type" <?php if($gallery->type == 2) echo "style='display:none'";?>>
					<label>Thumb Width:</label><input type="text" id="thumb_width" name="thumb_width" title="Thumbnail image width value" value="<?php echo $gallery->thumb_width;?>"><br/>
					<label>Thumb Height:</label><input type="text" id="thumb_height" name="thumb_height" title="Thumbnail image height value" value="<?php echo $gallery->thumb_height;?>">
				</div>
				<div class="type2 type" <?php if($gallery->type == 1) echo "style='display:none'";?>>
					<label>Full Size Width:</label><input type="text" id="full_size_width" name="full_size_width" title="Max image width value" value="<?php echo $gallery->full_size_width;?>"><br/>
					<label>Full Size Height:</label><input type="text" id="full_size_height" name="full_size_height" title="Max image height value" value="<?php echo $gallery->full_size_height;?>">
				</div>
				<div class="type3 type" <?php if($gallery->type == 4) echo "style='display:none'";?>>
					<label>Video Width:</label><input type="text" id="video_width" name="video_width" title="Video width value" value="<?php echo $gallery->video_width;?>"><br/>
					<label>Video Height:</label><input type="text" id="video_height" name="video_height" title="Video height value" value="<?php echo $gallery->video_height;?>">
				</div>
			</div>
		</div>
		<div class="row">
			<label id="image_lbl">Images:</label>				
			<div class="field">
				<input type="file" class="multi" name="imagefile[]" id="imagefile" multiple="multiple" title="Click here to upload your images" />				
		<?php					
			echo '<table width="400px" id="imagetable" cellpadding="5px" cellspacing="5px">';
			foreach($gallery_lines as $gallery_line)
			{
				if($gallery_line->file_name != "")
				{
					$imagefile = get_option("siteurl").'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$id."/".$gallery_line->file_name . '&w=182&h=137&zc=1';
					
					echo "<tr id='file$counter'><td>";
					echo "<div ><a href=\"javascript:deletefile('". $gallery_line->file_name ."', ".$counter.");\"><img src='". str_replace("admin/","",plugins_url("images/delete.png", __FILE__)) ."' alt='delete image' /></a><img src='$imagefile'></div>";?>
					</td>
					<?php echo "
						<td colspan='2'>
							<label class='caption_label'>Caption</label><textarea name='caption[]' title='Type your image caption here'>$gallery_line->caption</textarea>
							<input type='hidden' name='caption_id[]' title='caption id' value='$gallery_line->id' />
						</td>
					</tr>";
					
					$counter++;
				}
				
			}
			echo '</table>';
		?>			
			</div>
			<!-- Show videos input url -->
			<div id="input_video">
				<table id="input_url">
				<?php
					$id_counter = 0;
					foreach($gallery_lines_url as $gallery_line)
					{
						echo "
						<tr id='url".$id_counter."' >
							<td class='first_colomn'>
								<label class='video_lbl'>Embedded Code:</label>
							</td>
							<td>
								<input type='text' name='video_url[]' class='video_url' title='Type your unique youtube code here' value='". $gallery_line->video_url ."' />
							</td>
							<td>
								<a href='javascript:deleteRow(".'"input_url","url'.$id_counter.'"'.")'>[delete]</a>
							</td>
						</tr>";
						$id_counter++;
					}					
				?>					
				</table>
				<a id="add_video" onclick="addRow('input_url')">Add video</a>
			</div>
		</div>
		<input type="hidden" id="orderedfiles" name="orderedfiles">
		<input type="hidden" id="deletefiles" name="deletefiles">
		<input type="hidden" id="deleteurl" name="deleteurl">
		<input type="hidden" id="task" name="task" value="save_gallery">
		<input type="hidden" id="id" name="id" value="<?php echo $_REQUEST['id'];?>">
		<input type="hidden" id="id_counter" name="id_counter" value="<?php echo $id_counter; ?>">			
		<br /><input class="button button-primary button-large" type="submit" title="Click here to save gallery" value="Save">
		</form>	
		</div>
		</div>
		<?php
	}
	
	function show_gallery_list()
	{
		global $wpdb;
		$galleries = $wpdb->get_results("select * from easy_gallery");
		
		?>
		<div class="wrap">		
		<h2>Gallery List<a class='add-new-h2' href='<?php echo get_option("siteurl");?>/wp-admin/admin.php?page=easy_gallery&task=add'>Add New</a></h2>
		<?php		
		?>
		<table class="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr><th>Name</th><th>Shortcode</th><th>Actions</th></tr>
			</thead>
			<tbody>
			<?php
			if(count($galleries) > 0)
			{
				foreach($galleries as $gallery)
				{
				?>
					<tr>
						<td><?php echo $gallery->gallery_name;?></td>
						<td>[easygallery id=<?php echo $gallery->id;?>]</td>
						<td><a href="<?php echo get_option("siteurl");?>/wp-admin/admin.php?page=easy_gallery&task=duplicate_gallery&id=<?php echo $gallery->id;?>">Duplicate</a> | <a href="<?php echo get_option("siteurl");?>/wp-admin/admin.php?page=easy_gallery&task=edit_gallery&id=<?php echo $gallery->id;?>">Edit</a> | <a href="<?php echo get_option("siteurl");?>/wp-admin/admin.php?page=easy_gallery&task=delete_gallery&id=<?php echo $gallery->id;?>">Delete</a></td>
					</tr>
				<?php
				}				
			}
			else
				echo "<tr><td colspan='3'>No gallery</td></tr>";
			?>		
			</tbody>
		</table>
        
        <p>&nbsp</p>
        * Or, use the shortcode [easygallerylist] to list galleries on the page / post.
		</div>
		<?php	
	}
?>