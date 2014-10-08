<?php
function checklogin()
{
	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) {
		// Not logged in.
		echo "Please login first <a href='".get_option("siteurl")."/wp-login.php'>here</a> to access this page";
		return false;
	} else {
		// Logged in.
		return true;
	}
}

function clean_name($name)
{
	return preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $name);
}
function upload_file($temp,$filename,$uploadpath)
{
	if($filename != "")
	{
		if(!is_dir($uploadpath)) mkdir($uploadpath,0777,true);
		
		$filename = str_replace("\'","",$filename);
		$filename = date("Ymhis") . "-".str_replace(",","",$filename);

		$upload_filename = $uploadpath."/".$filename;			
		if (move_uploaded_file($temp, $upload_filename)) {
			return  $filename;
		} else {
			return "";
		}
	}

	return $name;
}

function get_url($url)
{
	$url = str_replace("http://","",$url);
	$url = str_replace("www.","",$url);

	$arr = preg_split("/\./",$url);

	if($arr[count($arr)-1]=="au"||$arr[count($arr)-1]=="au/")
	{
		if(count($arr) <4)
			$url = "http://www.".$url;
		else
			$url = "http://".$url;
		
	}
	else if(count($arr) <3)
		$url = "http://www.".$url;
	else
		$url = "http://".$url;
		
	return $url;
}

function get_link_html($url)
{
	if(strpos($url,"http://" ) > -1 || strpos($url,"https://") > -1)
	{
		echo "<a href='".$url."' target='_blank'>".get_short_url($url)."</a>";
	}
	else if(strpos($url,"www") > -1)
	{
		echo "<a href='http://".$url."' target='_blank'>".get_short_url($url)."</a>";
	}
	else
	{
		 echo $url;
	}
}

function get_short_url($str)
{
	$str = str_replace("http://","",$str);
	$str = str_replace("www.","",$str);	
	return $str;
}

?>