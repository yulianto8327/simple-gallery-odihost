<?php

$id=$_GET['gallery_id'];

function easy_gallery_scripts_method() {
    wp_enqueue_script( 'prototype' );
    wp_enqueue_script( 'scriptaculous' );
}

add_action( 'wp_enqueue_scripts', 'easy_gallery_scripts_method' ); // wp_enqueue_scripts action hook to link only on the front-end


function show_gallery($id){
    
    //$gallery_id=$_GET['gallery_id'];
    
    if($_GET[ 'gallery_id' ] ){
       display_easy_gallery($id);
       echo do_shortcode("[easygallery id=$_GET[gallery_id]]");
    }
    else
       
      display_gallery_list($id);
}


function display_easy_gallery($id)
{
	global $wpdb;
	$gallery =$wpdb->get_row("select * from easy_gallery where id = " .$id);
	
	switch($gallery->type)
	{
		case 1:
			display_easy_gallery_thumbnail($gallery);
			break;
		case 2:
			display_easy_gallery_big_image_slider($gallery); //display_easy_gallery_big_image_slider($gallery);
			break;
		case 3:
			display_easy_gallery_thumbnail_slider($gallery);
			break;
		case 4:
			display_easy_gallery_video($gallery);
			break;
	}
}

function display_easy_gallery_thumbnail($gallery)
{
	global $wpdb;
	$gallery_lines = $wpdb->get_results("select * from easy_gallery_line where gallery_id = " .$gallery->id." and file_name != '' order by order_no");
?>
	<!--
	<link rel='stylesheet' href='<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/gallery.css' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/lightbox.css' type='text/css' media='all' />
	-->
	<div class="gallery-wrapper">
	<input type="hidden" name="root" id="root" value="<?php echo get_option("siteurl");?>">
	<?php // if(get_option('lightbox_theme') == 'on') echo "<script src='".get_option("siteurl")."/wp-content/plugins/simple-gallery-odihost/js/lightbox.js' type='text/javascript'></script>"; ?>	
	<div class="gallery_thumbnail">
<?php
	foreach($gallery_lines as $gallery_line)
	{
		?>
		<div class="single-thumbnail" style="<?php echo "width:".$gallery->thumb_width."px;height:".$gallery->thumb_height."px;"; ?>">
			<a href="<?php echo get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name ;?>" rel="lightbox[<?php echo $gallery->id;?>]" class="image_thumb">
				<img src="<?php echo get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->thumb_width.'&h='.$gallery->thumb_height.'&zc=1';?>" />
				<?php if($gallery_line->caption != ""): ?>
					<div class="image-caption" style="display:none; margin-top:<?php echo ($gallery->thumb_height + 6) * 0 . "px;"; ?> width:<?php echo $gallery->thumb_width."px;"; ?> height:<?php echo ($gallery->thumb_height + 6) ."px;"; ?>">
						<div class="info" style="width:<?php echo $gallery->thumb_width."px;"; ?>">
							<p style="<?php echo "max-height:". ($gallery->thumb_height * 1) ."px;"; ?>"><?php echo $gallery_line->caption; ?></p>
						</div>
					</div>
				<?php endif; ?>
			</a>
		</div>
		<?php
	}
?>
	</div>
	<div class="clearThis"></div>
	</div>
<?php
	//caption_script();
}

function display_easy_gallery_big_image_slider($gallery)
{
	global $wpdb;
	$gallery_lines = $wpdb->get_results("select * from easy_gallery_line where gallery_id = " .$gallery->id."  and file_name != '' order by order_no");
?>
	<!-- 
	<link rel='stylesheet' href='<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/gallery.css' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php// echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/carousel.css' type='text/css' media='all' />
	<script type="text/javascript" src="<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/jquery.jcarousel.min.js"></script>
	-->
	<style>
	
	.jcarousel-container-horizontal,.jcarousel-clip-horizontal,	.jcarousel-item 
	{
		width:<?php echo $gallery->full_size_width;?>px !important;
		height:<?php echo $gallery->full_size_height;?>px !important;
	}
	.jcarousel-skin-tango
	{
		width:<?php echo $gallery->full_size_width+80;?>px !important;
		height:<?php echo $gallery->full_size_height;?>px !important;
	}
	</style>
	<div class="gallery-wrapper">
	<ul id="mycarousel" class="mycarousel jcarousel-skin-tango">
<?php
	foreach($gallery_lines as $gallery_line)
	{
		?>
		<li>
			<div class="single-thumbnail" style="<?php echo "width:".$gallery->full_size_width."px;height:".$gallery->full_size_height."px;"; ?>">
				<img src="<?php echo get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->full_size_width.'&h='.$gallery->full_size_height.'&zc=1';?>">
				<?php if($gallery_line->caption != ""): ?>
					<div class="image-caption" style="display:none; margin-top:<?php echo ($gallery->full_size_height + 6) * 0 . "px;"; ?> width:<?php echo $gallery->full_size_width."px;"; ?> height:<?php echo ($gallery->full_size_height + 6) ."px;"; ?>">
						<div class="info" style="width:<?php echo $gallery->full_size_width."px;"; ?>">
							<p style="<?php echo "max-height:". ($gallery->full_size_height * 1) ."px;"; ?>"><?php echo $gallery_line->caption; ?></p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</li>
		<?php
	}
?>
	</ul>
	<br />
	<hr class="hr1">
	<div id="thumbnav"  class="jcarousel-control">Slides:
	<?php
	$page =1;
	foreach($gallery_lines as $gallery_line)
	{
	?><a href=""><?php echo $page;?></a>
	<?php
		$page++;
	}
	?>
	</div>
	<div class="clearThis"></div>
	</div>
	<!-- <script>
		$j = jQuery.noConflict();
		$j(document).ready(function() {
				$j('#mycarousel').jcarousel({
					auto: 0,
					scroll: 1,
					wrap: 'last',
					initCallback: mycarousel_initCallback
				});
			});
		function mycarousel_initCallback(carousel) {

			$j('.jcarousel-control a').bind('click', function() {
				carousel.scroll($j.jcarousel.intval($j(this).text()));
				return false;
			});

			$j('.jcarousel-scroll select').bind('change', function() {
				carousel.options.scroll = $j.jcarousel.intval(this.options[this.selectedIndex].value);
				return false;
			});

			$j('#mycarousel-next').bind('click', function() {
				carousel.next();
				return false;
			});

			$j('#mycarousel-prev').bind('click', function() {
				carousel.prev();
				return false;
			});
		};

	</script>
	-->
<?php
	//caption_script();
}
function display_easy_gallery_thumbnail_slider($gallery)
{
	global $wpdb;
	$gallery_lines = $wpdb->get_results("select * from easy_gallery_line where gallery_id = " .$gallery->id."  and file_name != '' order by order_no");
	$gallery_line = $wpdb->get_row("select * from easy_gallery_line where gallery_id = " .$gallery->id." order by order_no limit 0,1");
	$big_photo = get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->full_size_width.'&h='.$gallery->full_size_height.'&zc=1';
?>
	<!-- 
	<link rel='stylesheet' href='<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/gallery.css' type='text/css' media='all' />
	<link rel='stylesheet' href='<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/carousel.css' type='text/css' media='all' />
	<script src="<?php //echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/js/simple_carousel.js" type="text/javascript"></script>
	-->
	
	<style>
	#scroll-content
	{
		width:<?php echo $gallery->thumb_width*count($gallery_lines);?>px;
		height:<?php echo $gallery->thumb_height;?>px;
	}
	#scroll-container
	{
		/* width:<?php echo $gallery->thumb_width*4;?>px; */
		width: <?php echo $gallery->full_size_width; ?>px;
		height:<?php echo $gallery->thumb_height;?>px;
	
	}
	.big-photo
	{
		width:<?php echo $gallery->full_size_width;?>px;
		height:<?php echo $gallery->full_size_height;?>px;
		margin-bottom:10px;
	}
	#scroll-controls a.right-arrow {
		left:<?php echo $gallery->full_size_width-20;?>px;
	}	
	</style>
	<script>
	function changeimage_<?php echo $gallery->id ?>(counter, event)
	{
		var src		= new Array();
		var caption	= new Array();
		var width	= "<?php echo $gallery->full_size_width; ?>";
		var height	= "<?php echo $gallery->full_size_height; ?>";
		<?php
		$counter =0;
		foreach($gallery_lines as $gallery_line)
		{
			?>
			src[<?php echo $counter;?>] = "<?php echo get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->full_size_width.'&h='.$gallery->full_size_height.'&zc=1';?>";
			caption[<?php echo $counter;?>] = "<?php echo $gallery_line->caption; ?>";
			<?php
			if($counter == 0)
			{
				$first_caption = $gallery_line->caption;
			}	
			$counter++;
		}
		?>
		//$j = jQuery.noConflict();
		
		if(caption[counter] != ""){
			
			document.getElementsByClassName("big-photo-<?php echo $gallery->id ?>")[0].innerHTML ='<div class="single-thumbnail" style="width:'+ width +'px; height:'+ height +'px;">\
																			<img src="' + src[counter] + '" alt="photo"/> \
																			<div class="image-caption" style="display:none; margin-top: 0px; width:'+ width +'px; height:'+ plus(height,6) +'px;"> \
																				<div class="info" style="width:'+ width +'px;"> \
																					<p style="max-height:'+ height +'px;">'+ caption[counter] +'</p> \
																				</div> \
																			</div> \
																		</div>';
		}else{
			document.getElementsByClassName("big-photo-<?php echo $gallery->id ?>")[0].innerHTML ='<div class="single-thumbnail" style="width:'+ width +'px; height:'+ height +'px;">\
																			<img src="' + src[counter] + '" alt="photo"/> \
																		</div>';

		}
	}
	/*
	function plus(val1, val2)
	{
		var result = parseInt(val1) + parseInt(val2);
		return result;
	}
	*/
	</script>
	<div class="gallery-wrapper">
	<div class="big-photo-<?php echo $gallery->id ?>">
		<div class="single-thumbnail" style="<?php echo "width:".$gallery->full_size_width."px;height:".$gallery->full_size_height."px;"; ?>">
			<img src="<?php echo $big_photo;?>" alt="photo"/>
			<?php if($first_caption != ""): ?>
				<div class="image-caption" style="display:none; margin-top:<?php echo ($gallery->full_size_height + 6) * 0 . "px;"; ?> width:<?php echo $gallery->full_size_width."px;"; ?> height:<?php echo ($gallery->full_size_height + 6) ."px;"; ?>">
					<div class="info" style="width:<?php echo $gallery->full_size_width."px;"; ?>">
						<p style="<?php echo "max-height:". ($gallery->full_size_height * 1) ."px;"; ?>"><?php echo $first_caption; ?></p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<div id="scroll-container">
		<div id="scroll-content">
		<?php
			$counter=0;
			foreach($gallery_lines as $gallery_line)
			{
				?>
				<a href="javascript:changeimage_<?php echo $gallery->id ?>('<?php echo $counter;?>', this);"><img src="<?php echo get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->thumb_width.'&h='.$gallery->thumb_height.'&zc=1';?>" alt="photo"/></a>
				<?php
				$counter++;
			}
		?>
		</div>
		<div id="scroll-controls"><a href="#" class="left-arrow"></a><a href="#" class="right-arrow"></a>
		</div>
	</div>
	<?php
	foreach($gallery_lines as $gallery_line)
	{
		?><img src="<?php echo get_option("siteurl") .'/wp-content/plugins/simple-gallery-odihost/includes/thumb.php?src=' .get_option('siteurl').'/wp-content/uploads/easy-gallery/'.$gallery->id."/".$gallery_line->file_name . '&w='.$gallery->full_size_width.'&h='.$gallery->full_size_height.'&zc=1';?>" class="hide"><?php
		$counter++;
	}
	?>

	<div class="clearThis"></div>
	</div>
<?php
	//caption_script();
}


function display_easy_gallery_video($gallery)
{
	global $wpdb;
	
	$videos = $wpdb->get_results("SELECT * FROM easy_gallery_line WHERE gallery_id = '".$gallery->id."' AND video_url != '' AND video_url is not null");
		
	?>
	<!--
	<link rel='stylesheet' href='<?php echo get_option("siteurl");?>/wp-content/plugins/simple-gallery-odihost/css/gallery.css' type='text/css' media='all' />
	-->
	<div id="video_wrapper">
	<?php
	foreach($videos as $video)
	{
		?>
			<div class='youtube-video'>
				<!-- <iframe width="<?php //echo $gallery->video_width; ?>" height="<?php //echo $gallery->video_height; ?>" src="<?php //echo $video->video_url; ?>" frameborder="0" allowfullscreen></iframe> -->
				<object width="<?php echo $gallery->video_width; ?>" height="<?php echo $gallery->video_height; ?>" data="http://www.youtube.com/v/<?php echo $video->video_url; ?>" type="application/x-shockwave-flash">
					<param name="src" value="http://www.youtube.com/v/<?php echo $video->video_url; ?>" />
					<param
						name="allowFullScreen"
						value="true">
					</param>
					<param
						name="allowscriptaccess"
						value="always">
					</param>
				</object>
			</div>			
		<?php
	}
	?>
	<div class="clearThis"></div>
	</div>	
	<?php
}
function youtube_code($url)
{
	parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
	return $my_array_of_vars['v'];
}
function caption_script()
{
	?>
	<!-- <script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.10.1.min.js"></script> -->
	<script type="text/javascript">
		var jq = $.noConflict();
		jq(".single-thumbnail").mouseover(function() { jq(".image-caption", this).show(); });
		jq(".single-thumbnail").mouseleave(function() { jq(".image-caption", this).hide(); });
	</script>
	<?php
}


function display_gallery_list($id)
{
 
 
 global $wpdb;
 $gallery_list = $wpdb->get_results("SELECT * FROM easy_gallery , easy_gallery_line WHERE easy_gallery.id=easy_gallery_line.gallery_id GROUP BY gallery_id ORDER BY easy_gallery_line.gallery_id DESC"); //AND easy_gallery_line.gallery_id = '".$gallery->id."' ");
  
   ?>
   
   <div class="image-gallery">
   <?php 
    //$results=$gallery_list;
    foreach( $gallery_list as $results )
    //for($i=0; $i<count($results); $i++)
	{
	   
		?>
	
   <!-- <a href="Javascript:loadgallery('<?php // echo $results->gallery_id; ?>')">-->
   
    <a href="<?php echo get_option(siteurl) .'/gallery/?gallery_id='.$results->gallery_id; ?>">
    <img src="<?php echo get_option(siteurl) .'/wp-content/uploads/easy-gallery/'.$results->gallery_id.'/'.$results->file_name; ?>" title="<?php echo "Gallery ".$results->gallery_name; ?>" width="150px" height="150px" > <!--</span> width="100px" height="100px"-->	
	</a>
    
    	<?php
	}
    ?>

</div>
<?php  

}


?>
