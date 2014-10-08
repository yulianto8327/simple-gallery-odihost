/************************ Big Image Slider ***************************/

$j = jQuery.noConflict();
$j(document).ready(function() {
	$j('.mycarousel').jcarousel({
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

	$j('.mycarousel-next').bind('click', function() {
		carousel.next();
		return false;
	});

	$j('.mycarousel-prev').bind('click', function() {
		carousel.prev();
		return false;
	});
};

/************************ Thumbnail Slider *****************************/

/*
function changeimage(counter)
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
		document.getElementsByClassName("big-photo")[0].innerHTML ='<div class="single-thumbnail" style="width:'+ width +'px; height:'+ height +'px;">\
																		<img src="' + src[counter] + '" alt="photo"/> \
																		<div class="image-caption" style="display:none; margin-top: 0px; width:'+ width +'px; height:'+ plus(height,6) +'px;"> \
																			<div class="info" style="width:'+ width +'px;"> \
																				<p style="max-height:'+ height +'px;">'+ caption[counter] +'</p> \
																			</div> \
																		</div> \
																	</div>';
	}else{
		document.getElementsByClassName("big-photo")[0].innerHTML ='<div class="single-thumbnail" style="width:'+ width +'px; height:'+ height +'px;">\
																		<img src="' + src[counter] + '" alt="photo"/> \
																	</div>';

	}
}
*/
function plus(val1, val2)
{
	var result = parseInt(val1) + parseInt(val2);
	return result;
}
