<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$></title>
	
	<link rel="Start" title="Home" href="<$PSNavigationMain$>" />
	<link rel="Index" title="Index" href="<$PSNavigationListing$>" />
	
	<PSAlbums>
	<link rel="Section" title="<$PSAlbumTitle$>" href="<$PSNavigationListing$>?album=<$PSAlbumFolder$>" />
	</PSAlbums>
	
	<meta name="copyright" content="<$PSNameAuthor$>" />
	<meta name="description" content="<$PSNamePhotos$>" />
	
	<link rel="alternate" type="application/rss+xml" title="RSS" href="<$PSNavigationListing$>?format=rss" />
	
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
			font-size: 17px;
		}
		
		#holder
		{
			margin: 0px auto 0px auto;
			padding: 50px 0px 0px 0px;
			text-align: left;
			width: 370px;
		}
		
		ul.listing
		{
			list-style: none;
			list-style-position: inside;
			margin: 0px auto 0px auto;
			padding: 0px;
			width: 370px;
		}
		
		ul.listing li
		{
			list-style: none;
			padding: 0px 0px 20px 0px;
			margin: 0px;
		}
		
		ul.listing li p a img { margin-right: 10px; }
		
		ul.listing li small
		{
			font-family: Verdana, Trebuchet, Sans;
			font-size: 10px;
			letter-spacing: .1em;
			color: gray;
			text-transform: uppercase;
		}
		
		/* ]]> */
	</style>
</head>
<body>
<div id="holder">
<h1><a href="<$PSNavigationMain$>"><$PSNameMain$></a> <$PSSeparator$> <a href="<$PSNavigationListing$>"><$PSNamePhotos$></a></h1>

<form action="./" method="get">
<p><input type="text" name="search" size="20" accesskey="4" /> <input type="submit" name="submit" value="Search" /></p>
</form>
	<ul class="listing">
		<PSAlbums><li>
			<p>
				<a href="<$PSNavigationListing$>?album=<$PSAlbumFolder$>">
				<PSPhotos num="1">				
				<img 
					src="<$PSPhotoThumbPath$>" 
					alt="<$PSAlbumTitle$>" 
					width="<$PSPhotoThumbWidth$>" 
					height="<$PSPhotoThumbHeight$>" /></PSPhotos></a>
				<a href="<$PSNavigationListing$>?album=<$PSAlbumFolder$>"><$PSAlbumTitle$><small>&nbsp;&nbsp;<$PSAlbumDate$></small></a>
			</p>
		</li></PSAlbums>
	</ul>
	
	<h4>[<a href="?format=rss" title="RSS 0.92">RSS</a>] [<a href="http://photostack.org/" title="Find Out More">Organized by PhotoStack</a>]</h4>
</div>
</body>
</html>