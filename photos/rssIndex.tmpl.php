<?php header("Content-Type: application/rss+xml"); ?>
<rss version="2.0" 
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:admin="http://webns.net/mvcb/"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
	<channel>
    	<title><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$></title>
    	<link><$PSNavigationListing$></link>
		<description><$PSNameMain$> <$PSSeparator$> <$PSNamePhotos$></description>
		<PSAlbums>
		<item>
			<title><$PSAlbumTitle$> - <$PSAlbumDate$></title>
			<link><$PSNavigationListing$>?album=<$PSAlbumFolder$></link>
			<description>
			<![CDATA[
			<p>
				<a
					href="<$PSNavigationListing$>?album=<$PSAlbumFolder$>">
				<img 
					src="<PSPhotos num="1"><$PSPhotoThumbPath$></PSPhotos>"
					alt="<$PSAlbumTitle$>"
					width="50"
					height="50" /><$PSAlbumTitle$> - <$PSAlbumDate$>
				</a>
			</p>
			<p>
				<$PSAlbumCount$> Photographs in this album.
			</p>
			]]></description>
			<guid isPermaLink="true"><$PSNavigationListing$>?album=<$PSAlbumFolder$></guid>
		</item>
		</PSAlbums>
	</channel>
</rss> 
		