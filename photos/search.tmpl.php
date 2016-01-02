<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><$PSNameMain$> <$PSSeparator$> Search</title>
	
	<link rel="Start" title="Home" href="<$PSNavigationMain$>" />
	<link rel="Index" title="Index" href="<$PSNavigationListing$>" />
	
	<style type="text/css" media="screen">
		/* <![CDATA[ */
		body
		{
			margin: 0px auto 0px auto;
			text-align: center;
			font-family: Georgia, Times, Serif;
		}
		
		a, a:link, a:visited
		{
			color: black;
			text-decoration: none;
		} 
		
		a:hover { text-decoration: underline; }
		
		img
		{
			border: 0;
			margin: 0;
			padding: 0;
		}
				
		h1 {
			font-size: 20px;
			font-weight: normal;
			margin: 0px 0px 30px 0px;
			padding: 0px;
		}
		h2 {
			font-size: 16px;
			margin: 0px 0px 30px 0px;
			padding: 0px;
		}
				
		p

		{
			text-align: left;
			margin: 0;
			padding: 0;
			font-size: 17px;
		}
		
		#holder
		{
			margin: 0px auto 0px auto;
			padding: 50px 0px 0px 0px;
			text-align: left;
			width: 370px;
		}
		ul li {
		margin-bottom: 10px;
		}
		
		ul li ul {
		margin-top: 5px;
		margin-bottom: 10px;
		}
		
		strong {
		background-color: #FFC;
		}
		
		/* ]]> */
	</style>
</head>
<body>
<div id="holder">
<h1><a href="<$PSNavigationMain$>"><$PSNameMain$></a> <$PSSeparator$> <a href="<$PSNavigationListing$>"><$PSNamePhotos$></a> <$PSSeparator$> Search</h1>

<form action="./" method="get">
<p><input type="text" name="search" size="20" /><input type="submit" name="submit" /></p>
</form>
<?php
//This page has VERY messy code, please excuse.

 if($_GET['search'] != 'DISPLAY_NONE') { ?>
<PSAlbums>
<?php $matches = 'no';

$result = '';?>
<PSPhotos>
<?php
$entry = "<$PSPhotoDescription$> <$PSPhotoDate$>";

if(preg_match('/^.*'.$_GET['search'].'.*$/i',$entry)) {
	$result .= "<li><a href=\"<$PSNavigationListing$>?album=<$PSAlbumFolder$>&#038;img=<$PSPhotoNumber$>\">".preg_replace('/(.*)('.$_GET['search'].')(.*)/i', '$1<strong>$2</strong>$3', "<$PSPhotoDescription$>")."</a></li>
";
	$matches = 'yes';
}
?></PSPhotos><?php
if ($matches == 'yes') {
	$results[] = "<li><a href=\"<$PSNavigationListing$>?album=<$PSAlbumFolder$>\"><$PSAlbumTitle$>: <$PSAlbumCount$> Photos</a>
	<ul>".$result."</ul>
	</li>";
} elseif(preg_match('/^.*'.$_GET['search'].'.*$/i',"<$PSAlbumTitle$>")) {
	$results[] = "<li><a href=\"<$PSNavigationListing$>?album=<$PSAlbumFolder$>\">".preg_replace('/(.*)('.$_GET['search'].')(.*)/i', '$1<strong>$2</strong>$3', "<$PSAlbumTitle$>").": <$PSAlbumCount$> Photos</a></li>
";
}?>
</PSAlbums>

<?php
if($results) {
	echo '<h2>Results for &#8220;'.$_GET['search'].'&#8221;:</h2>
	<ul>';
	foreach ($results as $key => $value) {
	echo $value;
	}
	echo '</ul>';
	
} else {
	echo '<h2>No Matches Found</h2>';
}
}
?>
</div>
</body>
</html>
