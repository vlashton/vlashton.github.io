<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$> <$PSSeparator$> <$PSCurrentAlbumTitle$></title>
	
	<link rel="Start" title="Home" href="<$PSNavigationMain$>" />
	<link rel="Index" title="Index" href="<$PSNavigationListing$>" />
	
	<link rel="Next" title="Next" href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSNavigationNext$>" />
	<link rel="Prev" title="Prev" href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSNavigationPrevious$>" />
	
	<link rel="First" title="First" href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=1" />
	<link rel="Last" title="Last" href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSNavigationLast$>" />
	
	<PSAlbums>
	<link rel="Section" title="<$PSAlbumTitle$>" href="<$PSNavigationListing$>?album=<$PSAlbumFolder$>" />
	</PSAlbums>
	<PSPhotos>
	<link rel="Photographs" title="<$PSPhotoDescription$>" href="<$PSPhotoURL$>" />
	</PSPhotos>
	
	<meta name="copyright" content="<$PSNameAuthor$>" />
	<meta name="description" content="<$PSNamePhotos$>" />
	
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<$PSCurrentAlbumURL$>&#038;format=rss" />

	<style type="text/css" media="screen">
		/* <![CDATA[ */
		body
		{
			margin: 0px auto 40px auto;
			text-align: center;
			font-family: Georgia, Times, Serif;
		}
		
		a:link
		{
			color: black;
			text-decoration: none;
		}
		
		a:hover { text-decoration: underline; }
		a:visited { color: gray; }
		
		img
		{
			border: 0;
			margin: 0;
			padding: 0;
		}
		h2
		{
			float: left;
			margin: 0px 0px 20px 0px;
			padding: 0px;
			text-align: center;
			font-size: 17px;
			font-weight: normal;
			width: 100%;
		}
		
		h4
		{
			font-size: 10px;
			font-weight: normal;
			margin: 0px 0px 15px 0px;
			padding: 2px;
		}
		
		p
		{
			text-align: left;
			margin: 0;
			padding: 0;
		}
		
		#holder
		{
			margin: 0px auto 0px auto;
			padding: 50px 0px 0px 0px;
			width: <PSPhotos num="1"><$PSPhotoWidth$></PSPhotos>px;
		}
		
		ul
		{
			width: 100%;
			list-style: none;
			list-style-position: inside;
			margin: 0px;
			padding: 0px;	
		}
		
		ul li
		{
			float: left;
			display: block;
			padding: 0px;
			margin: 0px;
			height: 50px;
			width: 50px;
		}
		
		#photo
		{
			float: left;
			width: 100%;
			text-align: center;
			margin: 20px 0px 50px 0px;
		}
		
		.navigation
		{
			float: left;
			width: 100%;
			padding: 0px;
			padding-bottom: 20px;
			text-align: center;
		}
		
		.navigation select
		{
			padding: 0px;
			margin: 0px;
		}
		
		/* ]]> */
	</style>

	<script type="text/javascript">
		// <![CDATA[
		function MM_jumpMenu(targ,selObj,restore){ //v3.0
  		eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  		if (restore) selObj.selectedIndex=0;
		}
		// ]]>
	</script>
</head>
<body>
<div id="holder">
	<ul><PSPhotos><li><a href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSPhotoNumber$>" title="<$PSPhotoDescription$> - <$PSPhotoDate$>"><img 
					src="<$PSPhotoThumbPath$>" 
					width="<$PSPhotoThumbWidth$>" 
					height="<$PSPhotoThumbHeight$>" 
					alt="<$PSPhotoDescription$>" /></a></li></PSPhotos></ul>

	<p id="photo">
		<a href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSNavigationNext$>" title="<$PSCurrentPhotoDescription$> - <$PSCurrentPhotoDate$>">
			<img 
				src="<$PSCurrentPhotoPath$>"
				height="<$PSCurrentPhotoHeight$>" 
				width="<$PSCurrentPhotoWidth$>" 
				alt="<$PSCurrentPhotoDescription$> - <$PSCurrentPhotoDate$>" />
		</a>
	</p>
	
	<div class="navigation">	
		<select onchange="MM_jumpMenu('parent',this,0)">
			<option value="<$PSNavigationListing$>">[Index Listing]</option>
			<option value="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;format=rss">[RSS Feed for <$PSCurrentAlbumTitle$>]</option>

<option value="<$PSNavigationListing$>?search=DISPLAY_NONE">[Search]</option>
			<PSAlbums>
			<option value="<$PSNavigationListing$>?album=<$PSAlbumFolder$>"<$PSAlbumSelected$>><$PSAlbumTitle$></option>
			</PSAlbums>
		</select>
	</div>
</div>
</body>
</html>