<?php header("Content-Type: application/rss+xml"); ?>
<rss version="2.0" 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:admin="http://webns.net/mvcb/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	<channel>
    	<title><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$> <$PSSeparator$> <$PSCurrentAlbumTitle$></title>
    	<link><$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$></link>
		<description><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$></description>
		<PSPhotos>
		<item>
			<title><$PSPhotoDescription$> - <$PSPhotoDate$></title>
			<link><$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSPhotoNumber$></link>
			<description>
			<![CDATA[
				<a
					href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSPhotoNumber$>" 
					title="<$PSPhotoDescription$> - <$PSPhotoDate$>">
					
				<img 
					src="<$PSPhotoPath$>" 
					alt="<$PSPhotoDescription$>" 
					width="<$PSPhotoWidth$>"
					height="<$PSPhotoHeight$>" />
				</a>
			]]>
			</description>
			<guid isPermaLink="true"><$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&#038;img=<$PSPhotoNumber$></guid>
		</item>
		</PSPhotos>
	</channel>
</rss>		