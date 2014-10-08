/**
function loadgallery(id){
   // alert(id);
$.ajax({ 
type	: 'POST',
url : object_names.GalleryListajaxurl+'?gallery_id='+id,	
data	: '', 
success: function(data){

$("#gallerylist").html(data);
$("#gallerylisthide").hide();
}
}); 
}

*/