<?php
/*
                                                     ___
                     ( )_ ____                ___.--'   `--(  )
                     //      \      __.------'              ||
                    //        `----'                        ||
                   //    		PhotoStack 2.0b13           ||
                  //                                        ||
                 //           by: Noel D. Jackson           ||
   ,,,,,        //                                  ,,,,,   ||
  ;;;;;\      ,/(_/     http://photostack.org      ;;;;;\   ||
  ';;C '\   .'//     ____                    /--'-'';;C '\-\|-:
   );  _) .' //\____'    `----.____.--`\----'       );  _)  / :
 .'=. (  '/|//                                    .'=. (   / /
|   )`-\.' __/                                    |   )`-\/ /|
\   \ ,'\///                                      \   \ /  /||
 ;.  '  ///                                        ;.  '  / ||
 | `._,'//                                         | `._,'|-||:
 \     //                                          \      )-||:
  )===//]                                           )=====] ||
 /   // \                                          /       \||
 \_ (/   |                                         \_      )||
  \      |\                                        \\      |
  \      | \                                        \\     |
  |      |  )                                       ||     /
  |     /  /                                        ||    |
   \    | /                                         \\    |
   |    |/                                          ||    |
  /|    |                                           ||    |
 /\|    |                                           ||    |
/`.|    |                                           ||    |
`=.[____)                                           |[___/
    )  '`--.                                        ))  '`--.
    `='===='                                        ``='===='


This is PhotoStack 2.0b13
http://photostack.org/

It was created by Noel D. Jackson (noel@noeljackson.com) 31 Mar 2003.
Last updated 24 Mar 2004.

PhotoStack was influenced by Textism Photos (textism.com/photos/).
Thanks for letting me use your layout Dean.

This work is licensed under the Creative Commons Attribution-NoDerivs-NonCommercial License. 
To view a copy of this license, visit http://creativecommons.org/licenses/by-nd-nc/1.0/ 
or send a letter to Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.

You can't create derivative works. Sorry.

This script uses http://www.offsky.com/software/exif/index.php for image dates.
We are not taking credit for the exif class below. Thank you! (Exifer 1.0)
*/


# This function takes care of loading the correct module: photostack or organize.
# It's a ghetto approach, but hey, aren't we all a little ghetto?
# "Ghetto fresh," says I.

function load ($config,$mode='photostack') {
	if(strpos($config, 'config.php') === false) {
		echo 'You are trying to load something other than a configuration file!';
		return false;
		exit;
	} else {
		if(include $config) {
			define('config', 'yes');
			
			#Stripslashes from input if needed.
			$_GET = strip($_GET);
			$_POST = strip($_POST);

			#define
			define('siteName',$siteName);
			define('author',$author);		
			define('separator',$separator);		
			define('photosName',$photosName);
			define('dateFormat',$dateFormat);
			
			define('webDir', preg_replace('/(.*)\/$/', '$1',$webDir));
			define('dirRoot', dirname($dirRoot));
			define('storageDir',$storageDir);
			define('pageName',$pageName);
			
			define('photostackUsername',$photostackUsername);
			define('photostackPassword',$photostackPassword);
			define('ftpUsername',$ftpUsername);
			define('ftpPassword',$ftpPassword);
			define('ftpHost',$ftpHost);
			define('ftpDir',$ftpDir);
			define('webConfigURL',$webConfigURL);
			
			define('templateIndex',$templateIndex);
			define('templateDisplay',$templateDisplay);
			define('templateSyndicateIndex',$templateSyndicateIndex);
			define('templateSyndicateDisplay',$templateSyndicateDisplay);
			define('templateSearch',$templateSearch);
			
			define('albumsSortNatural',$albumsSortNatural);
			define('photosSortNatural',$photosSortNatural);
			define('photosSortReverse',$photosSortReverse);
			define('photosSortByDesc', $photosSortByDesc);
			define('photosSortByDate', $photosSortByDate);
			define('skipIfContains',$skipIfContains);
			
			define('imageScale',$imageScale);
			define('thumbHeight',$thumbHeight);
			define('thumbWidth',$thumbWidth);
			define('fullSize',$fullSize);
			
			define('caching',$caching);
			define('thumbnails',$thumbnails);
			define('syndicationType', $syndicationType);	
			define('timeLimit',$timeLimit);
			define('gd2',$gd2);
			define('albumListing',$albumListing);
			define('photoDesc',$photoDesc);
			define('imageDir',$imageDir);
			define('tagName',$tagName);	
			
			$photostack = new $mode;
		}
		return true;
	}
}
 
function strip ($array) {
	if(get_magic_quotes_gpc()) {
		return is_array($array)? array_map('strip',$array) : stripslashes($array);
	} else {
		return $array;
	}
}

# The class that takes care of most everything the browser sees.
class photostack {
 
	function photostack () {
		
		if(!defined('config')) {
			echo '<p><strong>You have not loaded a configuration file!</strong></p>
				<p>An example index document:</p>
					<p><code>&lt;?php<br /># Pull in the photostack.php program.<br />
					require("photostack.php");<br /><br />
					# Load your configuration file.<br />
					load(\'config.php\');<br /><br /># Run photostack.<br />
					$photopal = new photostack;<br />?&gt;</code></p>';
			exit;
		}
		
		# Set time limit.
		if(timeLimit != 0) {
		(timeLimit)?
			set_time_limit(timeLimit):
			set_time_limit(90);
		}
		
		# Hither, like the wind...
		
		# Check for a storage directory.
		if(!file_exists(dirRoot.'/'.storageDir) or
			!is_writeable(dirRoot.'/'.storageDir)) {
			echo dirRoot.'/'.storageDir;
			echo 'The "'.storageDir.'" storage directory is not set up properly.';
			exit;
		}

		#Cache, bling, bling.
		if (caching == 'yes') {

			# Files to check so you can modify the cache...
			$files = array(
				dirRoot.'/'.albumListing,
				dirRoot.'/'.templateIndex,
				dirRoot.'/'.templateDisplay,
				dirRoot.'/'.templateSyndicateIndex,
				dirRoot.'/'.templateSyndicateDisplay,
				dirRoot.'/'.templateSearch,
				dirRoot.'/'.$_GET['album'].'/'.imageDir,
				dirRoot.'/'.$_GET['album'].'/',
				dirRoot.'/'.$_GET['album'].'/'.photoDesc,
				dirRoot.'/photostack.php',
				dirRoot.'/config.php',
				dirRoot.'/'
			);
			
			# Name the cache file. 
			if($_GET['album']) {
				($_GET['img'])?
					$cachefile = dirRoot.'/'
							.storageDir
							.'/cache-'.$_GET['album'].'-'.$_GET['img'].'.html'
				:
					$cachefile = dirRoot.'/'
								.storageDir
								.'/cache-'.$_GET['album'].'.html'
				;
							
				if($_GET['format'] == syndicationType) {
					$cachefile = dirRoot.'/'
								.storageDir
								.'/cache-'.$_GET['album'].'.xml';
				}			
			} else {
				$cachefile = dirRoot.'/'.storageDir.'/cache-index.html';
				if($_GET['format'] == syndicationType) {
					$cachefile = dirRoot.'/'.storageDir.'/cache-index.xml';
				}
			}		
			
			foreach($files as $key) {
				if (file_exists($key)) {
					# If the modified time of any of $files is newer (read: greater) than the cached page then flag it to make a new one.
					if (!file_exists($cachefile) or filemtime($key) == false or filemtime($key) > filemtime($cachefile)) {						
						if(file_exists(dirRoot.'/'.$_GET['album'].'/'.imageDir)) {
							$create = 'yes';
						} else {
							exit;
						}
					}
					
				}
			}

			
			#If you don't want to flush the cache or make a new cache file then display the old one.
			if ($create != 'yes' && $_GET['flush'] != 'yes' && !$_GET['template'] && !$_GET['search']) {
				readfile($cachefile);
    			//For debug...
    			//echo "<!-- Cached copy, generated ".date('H:i:s', filemtime($cachefile))." -->\n";
    			exit;	
			}
			
		}
		
		//If you get past the above, display your data, put it in a variable and then...
		ob_start(); // Start the output buffer		
		$disp = new displayData;// Display Data
		$disp->photoDisplay();
		$data = ob_get_contents(); //Save to variable.
		ob_end_flush(); // Send the output to the browser	

		//Write this output to the cache.
		if (caching == 'yes' && !$_GET['template'] && !$_GET['search'] && $_GET['img'] <= count($disp->getData->photos)) {
			$fp = fopen($cachefile, 'w');
			fwrite($fp, $data);
			fclose($fp);
			clearstatcache();
			// for debug echo 'written';
		}	
	}
}



# This class displays templates.
class displayData {
	var $getData;
	
	function displayData() {
		# Load the photo and album data.
		$this->getData = new getData;
	}
	
	function photoDisplay() {
		
		if($_GET['search']) {
			$template = dirRoot.'/'.templateSearch;
		} else {
		
			if($_GET['album']) {
				($_GET['format'] && $_GET['format'] == syndicationType) ? $template = dirRoot.'/'.templateSyndicateDisplay : $template = dirRoot.'/'.templateDisplay;
			} else {
			
				($_GET['format'] && $_GET['format'] == syndicationType)? $template = dirRoot.'/'.templateSyndicateIndex : $template = dirRoot.'/'.templateIndex;
				
			}
		}
		
		if ($_GET['template'] && !file_exists(dirRoot.'/'.$_GET['template'])) {
			echo 'This template does not exist';
		} elseif($_GET['template']) {
			$template = dirRoot.'/'.$_GET['template'];
		}
			
		//Template it baby.
		$this->template($template);
		
	}


	#The Meat and Bones baby. Err, something like that.
	function template($template) {
	
		# Open $template and put it into $page. Then buffer it.
		$openTemplate = fopen($template, 'r');
		
		while(!feof ($openTemplate) ) {
			$line = fgets($openTemplate, 4096);
			$page .= $line;
		}
		fclose($openTemplate);
		$this->rawTemplate($page);
	}
	
	function rawTemplate($page) {
		# start a buffer grab the content evaluate it and save it,
		# then, don't display a damn thing
		
			
		//Fix template tags.
		
		# Set current Image
		!$_GET['img'] ? ($n = 1) : ($n = $_GET['img']);
		
		
		//Album Container Tag
		$page = preg_replace_callback(
				'/<'.tagName.'Albums.*?'.'>([\w\W]*?)<\/'.tagName.'Albums>/', array($this, 'albumList'),$page);
				
		//Images Container Tag
		$page = preg_replace_callback(
				'/<'.tagName.'Photos.*?'.'>([\W\w]*?)<\/'.tagName.'Photos>/', array($this, 'imageList'),$page);
		
		// Miscellaneous		
		$page = str_replace(
				'<$'.tagName.'Separator'.'$>',
				separator, $page);
		
		// Names
		$page = str_replace(
				'<$'.tagName.'NameMain'.'$>',
				siteName, $page);
				
		$page = str_replace(
				'<$'.tagName.'NameAuthor'.'$>',
				author, $page);
				
		$page = str_replace(
				'<$'.tagName.'NamePhotos'.'$>',
				photosName, $page);
		
		// Current Album
				
		$page = str_replace(
				'<$'.tagName.'CurrentAlbumTitle'.'$>',
				$this->getData->albums[$_GET['album']]['title'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentAlbumURL'.'$>',
				webDir.'/'.pageName.'?album='.$_GET['album'], $page);
		
		# Fix counter for photos.
		($this->getData->photos)? $count = count($this->getData->photos) :$count = 0;
		
		$page = str_replace(
				'<$'.tagName.'CurrentAlbumCount'.'$>',
				$count, $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentAlbumFolder'.'$>',
				$_GET['album'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentAlbumDate'.'$>',
				$this->getData->albums[$_GET['album']]['date'], $page);
		
		$page = str_replace('<$'.tagName.'CurrentPhotoPath'.'$>', 
				webDir.'/'
				.$_GET['album']."/".imageDir
				.$this->getData->photos[$n]['name'], $page);
				
		$page = str_replace('<$'.tagName.'CurrentPhotoName'.'$>', 
				$this->getData->photos[$n]['name'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoHeight'.'$>',
				$this->getData->photos[$n]['height'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoWidth'.'$>',
				$this->getData->photos[$n]['width'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoDescription'.'$>',
				$this->getData->photos[$n]['desc'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoDate'.'$>',
				date(dateFormat, $this->getData->photos[$n]['date']), $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoURL'.'$>',
				webDir.'/?album='.$_GET['album']."&#038;img=".$n, $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoFilename'.'$>',
				$this->getData->photos[$n]['name'], $page);
				
		$page = str_replace(
				'<$'.tagName.'CurrentPhotoNumber'.'$>',
				$n, $page);
		
		// Navigation
		

		// "Next album" logic.
		foreach ($this->getData->albums as $key => $value) {
			$albumList[] = $value['id'];
		}
	
		foreach ($albumList as $key => $value) { 
			if($_GET['album'] == $value) {
				($albumList[$key + 1])?
				$nextalbum = $albumList[$key + 1]:
				$nextalbum = $albumList[0];
				
				($albumList[$key - 1])?
				$prevalbum = $albumList[$key - 1]:
				$prevalbum = $albumList[count($albumList) - 1];
			}
		}
		
				
		$page = str_replace(
				'<$'.tagName.'NavigationNextAlbum'.'$>',
				webDir.'/'.pageName.'?album='.$nextalbum, $page);
		
		
		
		$page = str_replace(
				'<$'.tagName.'NavigationPreviousAlbum'.'$>',
				webDir.'/'.pageName.'?album='.$prevalbum, $page);
		

		($n < $count)? $next = $n + 1 : $next = 1;
		
		$page = str_replace(
				'<$'.tagName.'NavigationNext'.'$>',
				$next, $page);
		
		($n <= $count && $n != 1)? $prev = $n - 1 : $prev = $count;
		
		$page = str_replace(
				'<$'.tagName.'NavigationPrevious'.'$>',
				$prev, $page);
				
		$page = str_replace(
				'<$'.tagName.'NavigationFirst'.'$>',
				'1', $page);
				
		$page = str_replace(
				'<$'.tagName.'NavigationLast'.'$>',
				$count, $page);
				
		$page = str_replace(
				'<$'.tagName.'NavigationListing'.'$>',
				webDir.'/'.pageName, $page);
				
		$page = str_replace(
				'<$'.tagName.'NavigationMain'.'$>',
				'http://'.$_SERVER['HTTP_HOST'], $page);
		
		eval(' ?'.'>'.$page.'<'.'? ');
		// Just an echo function for ALL of this... seems stupid.
		
	}
	
	function albumList ($matches) {
		$data = rtrim($matches[1]);
	
		if($this->getData->albums) {

			$tags = array(
				'<$'.tagName.'AlbumTitle'.'$>',
				'<$'.tagName.'AlbumFolder'.'$>',
				'<$'.tagName.'AlbumDate'.'$>',
				'<$'.tagName.'AlbumSelected'.'$>',
				'<$'.tagName.'AlbumCount'.'$>'	
			);
			
			(preg_match('/<'.tagName.'Albums .*num="[0-9]+".*>[\w\W]*?<\/'
						.tagName.'Albums>/', $matches[0])) ?
				($number = preg_replace('/<'.tagName.'Albums .*num="([0-9]+)".*>[\w\W]*?<\/'
						.tagName.'Albums>/',"\\1", $matches[0])) :
				($number = count($this->getData->albums));
				
			foreach($this->getData->albums as $albumid) {
				if($counter < $number) {
				
				//If necessary get picture info.
				if(
					strstr($data,'<$'.tagName.'AlbumCount'.'$>') or 
					strstr($data,'<$'.tagName.'AlbumDate'.'$>') or
					strstr($data,'<$'.tagName.'AlbumFirstImagePath'.'$>') or
					preg_match('/<'.tagName.'Photos.*?'.'>([\w\W]*?)<\/'.tagName.'Photos>/', $matches[1]) && !$_GET['album']) {
					
					unset($this->getData->photos);
					
					//NUMBER THE GOD DAMN IMAGES BEFORE ASSIGNING ANYTHING!!!					
					$this->getData->numImages($albumid['id']);
					//NUMBER THE GOD DAMN IMAGES BEFORE ASSIGNING ANYTHING!!!	
					$this->getData->makeThumbs($albumid['id']);
					
					if(preg_match('<\$'.tagName.'Photo.*Height'.'\$>', $matches[1])) { 
						$this->getData->getSizes($albumid['id']);
					}
					
					if(preg_match('<\$'.tagName.'Photo.*Date'.'\$>', $matches[1])) { 
						$this->getData->getPhotoDates($albumid['id']);
					}
					
					if(preg_match('<\$'.tagName.'PhotoDescription'.'\$>', $matches[1])) { 
						$this->getData->getPhotoDesc($albumid['id']);
					}
										
					$this->getData->getPhotoDates($albumid['id']);
					$this->getData->sortPhotos();
				}
				
				if($this->getData->photos != '' && $this->getData->photos) {
					$count = count($this->getData->photos);
				} else {
					$count = 0;
				}
				
				$randval = 1;
				if($_GET['album'] == $albumid['id']) {
					$selected = ' selected="selected"';
				} else {
					$selected = '';
				}
				
				
				
				//SETS DATE FOR ALBUMS WHEN DISPLAYED
				($albumid['date'])? $date = $albumid['date']:$date = $this->getData->photos[$randval]['date'];
				
								
				$replacements = array(
					$albumid['title'],
					$albumid['id'],
					date(dateFormat, $date),
					$selected,
					$count
				);
				
				
				$albumListing = str_replace($tags, $replacements, $data);
				
				//Save it
				$album = $_GET['album'];
				//Change it
				$_GET['album'] = $albumid['id'];
				
				//Get sub image listing. :-)
				$imageListing = preg_replace_callback('/<'.tagName.'Photos.*?'.'>([\w\W]*?)<\/'.
								tagName.'Photos>/',array($this, 'imageList'), $albumListing);
				
				//Replenish it.
				$_GET['album'] = $album;
				
				$output .= $imageListing;
				$counter++;
				}
			}
			
			return $output;
		}	
	}
	
	function imageList ($matches) {
		
		$data = rtrim($matches[1]);
		
		if ($_GET['album']) {
			$albumGet = $_GET['album'];
		}
		
		if($_GET['album']) {
		
			if(file_exists(dirRoot."/".$_GET['album']."/")) {
			
				$tags = array(
					'<$'.tagName.'PhotoNumber'.'$>',
					'<$'.tagName.'PhotoDate'.'$>',
					'<$'.tagName.'PhotoDescription'.'$>',
					'<$'.tagName.'PhotoURL'.'$>',
					'<$'.tagName.'PhotoPath'.'$>',
					'<$'.tagName.'PhotoThumbPath'.'$>',
					'<$'.tagName.'PhotoHeight'.'$>',
					'<$'.tagName.'PhotoWidth'.'$>',
					'<$'.tagName.'PhotoFilename'.'$>',
					'<$'.tagName.'PhotoThumbHeight'.'$>',
					'<$'.tagName.'PhotoThumbWidth'.'$>',
				);
			
				(preg_match('/<'.tagName.'Photos .*num="[0-9]+".*>[\w\W]*?<\/'.
							tagName.'Photos>/', $matches[0])) ?
					($count = preg_replace('/<'.tagName.'Photos .*num="([0-9]+)".*>[\w\W]*?<\/'.
											tagName.'Photos>/' ,"\\1", $matches[0])) : 
					($count = count($this->getData->photos));
					
				(preg_match('/<'.tagName.'Photos .*offset="[0-9]+".*>([\w\W]*?)<\/'.
							tagName.'Photos>/', $matches[0])) ? 
					($iCount = preg_replace('/<'.tagName.'Photos .*offset="([0-9]+)".*>[\w\W]*?<\/'.
											tagName.'Photos>/', "\\1", $matches[0])) : 
					($iCount = 0);
				
				$count = round($count);
				$iCount = round($iCount);
				
				if($this->getData->photos) {
				
					foreach($this->getData->photos as $i => $value) {
						
						($i + $iCount > count($this->getData->photos))?
							$i = count($this->getData->photos) - $counter:
							$i = $i + $iCount;
						

						if($counter < $count) {
							$replacements = array(
								$i,
								date(dateFormat, $this->getData->photos[$i]['date']),
								$this->getData->photos[$i]['desc'],
								webDir.'/'.pageName.'?album='.$_GET['album'].'&#038;img='.$i,
								webDir.'/'.$_GET['album'].'/'.
									imageDir.$this->getData->photos[$i]['name'],
								webDir.'/'.storageDir.'/'.
									'thumb-'.$_GET['album'].'-'.$this->getData->photos[$i]['name'].'.png',
								$this->getData->photos[$i]['height'],
								$this->getData->photos[$i]['width'],
								$this->getData->photos[$i]['name'],
								$this->getData->photos[$i]['thumbHeight'],
								$this->getData->photos[$i]['thumbWidth'],
							);
			
							$output .= str_replace($tags, $replacements, $data);						
							$counter++;
						}
					}
				}
			}
			
			return $output;
		}
	}
	
	
}


# This class gathers all the information.
class getData {
	var $photos;
	var $albums;
	
	function getData() {
		//Get Album Names
	
		$this->getAlbumListing();
			
			
		if($_GET['album']) {
			
			//Give album a variable. Why? Cause.
			$selectedAlbum = $_GET['album'];
			
			//LET IT^ NUMBER THE DAMN IMAGES!!!
			$this->numImages($selectedAlbum);	
			//LET IT^ NUMBER THE DAMN IMAGES!!!
						
			//Make the thumbnails / take care of flush
			$this->makeThumbs($selectedAlbum);
			
			//Get Photo Descriptions
			$this->getPhotoDesc($selectedAlbum);
			
			//Get Photo Dimensions
			$this->getSizes($selectedAlbum);
			
			//Get Photo Dates/Modified Times
			$this->getPhotoDates($selectedAlbum);
			
			$this->sortPhotos();
		}

	}
	
	function getAlbumListing() {
	
		if(file_exists(dirRoot."/".albumListing)) {
			$albumLines = fopen (dirRoot."/".albumListing, 'r');
	
			while(!feof ($albumLines)) {
					$line = fgets($albumLines, 4096);
					
				//If the line is empty fuck it.
				if(trim($line) != '') {
				
					// If there is a date, preg it.
					if (preg_match('/^(.+):(.+):(.+)$/',$line)) {
						$albumName = trim(preg_replace('/^(.+):(.+):(.+)$/',"\$1",$line));
						$albumDesc = trim(preg_replace('/^(.+):(.+):(.+)$/',"\$2",$line));
						$albumDate = trim(preg_replace('/^(.+):(.+):(.+)$/',"\$3",$line));
						$this->albums[$albumName]['date'] = date(dateFormat, strtotime($albumDate));
					} else {
						$albumName = trim(preg_replace('/^(.+):(.+)$/',"\$1",$line));
						$albumDesc = trim(preg_replace('/^(.+):(.+)$/',"\$2",$line));
					}	
					
								
					$this->albums[$albumName]['title'] = str_replace("'","&#39;", str_replace('"',"&#34;", $albumDesc));
					$this->albums[$albumName]['id'] = $albumName;
				}
			}

			fclose($albumLines);
			if(albumsSortNatural == 'yes') {
				uasort($this->albums,array($this,'sortByTitle'));
			}
		} else {
			echo "<p>You don't have an album description file. Please fix this.</p>";
		}
	}
	
	function sortByTitle($a,$b) {
		return strnatcasecmp($a['title'],$b['title']);
	}

	// Store images in photo Array
	function numImages($albumGet) {
		unset($this->photos);
	
		if (file_exists(dirRoot."/".$albumGet.'/'.imageDir)) {
			$dirimages = opendir(dirRoot."/".$albumGet."/".imageDir) 
			or print("We can't open the correct directory!");

			$photocount = 0;

			while ($files = readdir($dirimages) ) {
				if (preg_match('/.*\.(jpg|png|gif|JPG|PNG|GIF)/',$files) && !@strstr($files,skipIfContains)) {
					$photocount = $photocount + 1;
					$this->photos[$photocount]['name'] = $files;
				}
			}		
		} else {
			echo "<p><strong>The '$albumGet' folder does not exist or is not setup properly.</strong></p>";
		}
		
		return count($this->photos);
	}
	
	function getSizes ($albumGet) {
		if($this->photos) {
			foreach($this->photos as $i => $value) {
				$this->getPhotoSize($i,$albumGet);
			}
		}
	}


	function getPhotoSize ($integer,$albumGet) {
		$size = @getimagesize(dirRoot."/".
				$albumGet."/".imageDir.$this->photos[$integer]['name']);
				
		$this->photos[$integer]['height'] = $size[1];
		$this->photos[$integer]['width'] = $size[0];

		$size = @getimagesize(dirRoot."/".
				storageDir."/".'thumb-'.$albumGet.'-'.$this->photos[$integer]['name'].'.png');
				
		$this->photos[$integer]['thumbHeight'] = $size[1];
		$this->photos[$integer]['thumbWidth'] = $size[0];
	}

	function getPhotoDesc($albumGet) {
		$descPath = dirRoot.'/'.$albumGet.'/'.photoDesc;
		
		if($this->photos) {
			if(file_exists($descPath)) {
			
				$descLines = fopen ($descPath, 'r');
		
				while(!feof($descLines)) {
					
					$line = fgets($descLines, 4096);
					$line = str_replace('http://','http;//',$line); //fix for urls
					$line = str_replace('"','"',$line); //fix for quotes or something?
					$corPhoto = trim(preg_replace('/^(.+):(.+)$/', '$1',$line)); // filename
					$desc = trim(preg_replace('/^(.+):(.+)$/', '$2',$line)); // description
					$desc = str_replace('http;//','http://',$desc); //fix urls
					$desc = str_replace("'","&#39;", $desc); //fix apostrophe
					$desc = str_replace('"',"&#34;", $desc); //fix quote mark
					
					$desc = str_replace(' ',' ',$desc);
					$desc = str_replace('(','%28',$desc);
					$desc = str_replace(')','%29',$desc);

					
					$descriptions[$corPhoto] = $desc; //put into array
					$this->files[] = $corPhoto;
				}
				
				fclose($descLines);
			}

			foreach($this->photos as $i => $value) {
			
				if ($this->files) {
					foreach($descriptions as $key => $desc) {
						if ($key == $this->photos[$i]['name']) {
	 						$this->photos[$i]['desc'] = $desc;
	 					}
	 				}
	 			} 
	 			
	 			if (!$this->photos[$i]['desc']) {
	 				$this->photos[$i]['desc'] = $this->photos[$i]['name'];
	 			}
			}		
		}
		
	}
	
	function sortPhotos () {
	
		if(photosSortNatural != 'no' && $this->photos) {
			usort($this->photos,array($this,'sortByName'));
			$this->$sort = true;
		}
		if(photosSortByDesc != 'no' && $this->photos && $this->files && file_exists(dirRoot.'/'.$_GET['album'].'/'.photoDesc)) {
			//@usort($this->photos,array($this,'sortByName'));	
			usort($this->photos,array($this,"descSort"));
			$this->$sort = true;				
		}
		if(photosSortByDate != 'no' && $this->photos) {
			usort($this->photos,array($this,"dateSort"));
			$this->$sort = true;			
		}	
		if(photosSortReverse != 'no' && $this->photos) {
			$this->photos = array_reverse($this->photos);
			$this->$sort = true;
		}
		if($this->$sort == true) {
			foreach($this->photos as $i => $value) { $this->photos[$i + 1] = $value; }	
			unset($this->photos[0]);
		}
	}
	
	
	function sortByName($a,$b) {
		return strnatcasecmp($a['name'],$b['name']);
	}
	
	
	function dateSort ($a,$b) {
		return strnatcasecmp($a['date'],$b['date']);
	}
	
	function descSort ($a,$b) {
			foreach($this->files as $key => $value) {
				if($a['name'] == $value) { return 0; }
				if($b['name'] == $value) { return 1; }
			}
	}
	
	//end sort functions

	function getPhotoDates($albumGet) {
		if($this->photos) {		
			foreach($this->photos as $i => $value) {
				$this->assignDate($i,$albumGet);
			}
		}
	}

	function assignDate ($integer,$albumGet) {
		if($this->photos) {
			$exif = new exif;
		
			$result = @$exif->read_exif_data_raw(dirRoot.'/'.
											$albumGet.'/'.imageDir.
											$this->photos[$integer]['name'],"yes");

			$time = $result['SubIFD']['DateTimeOriginal'];

		
			$hour = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$4', $time);
			$minute = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$5', $time);
			$second = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$6', $time);
			$month = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$2', $time);
			$day = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$3', $time);
			$year = preg_replace('/([0-9]+):([0-9]+):([0-9]+) ([0-9]+):([0-9]+):([0-9]+)/', '$1', $time);

			$timestamp = mktime($hour,$minute,$second,$month,$day,$year);
	/*temporary*/
			#if ($time == '' or $year == 0000) {
				$timestamp = @filemtime(dirRoot.'/'.$albumGet.'/'.imageDir.$this->photos[$integer]['name']);
			#}
		
		
			$this->photos[$integer]['date'] = $timestamp;
			if($integer = 1 && !isset($this->albums[$albumGet]['date'])) {
				$this->albums[$albumGet]['date'] = $this->photos[$integer]['date'];
			}
		}
	}

	function makeThumbs ($albumGet) {
		if($this->photos && thumbnails != 'no') {
			if(file_exists(dirRoot.'/'.$albumGet.'/'.imageDir)) {
				foreach($this->photos as $i => $value) {
					$this->makeThumb($i,$albumGet);
				}
			}
		}
	}

	function makeThumb ($integer,$albumGet) {

		$name = $this->photos[$integer]['name'];

		$this->flushThumb($integer,$albumGet);

		if (!file_exists(dirRoot."/".storageDir."/".'thumb-'.$albumGet.'-'.$name.'.png') && thumbnails != 'no') {
		
			if(!is_writeable(dirRoot.'/'.storageDir.'/')) {
				echo 'The "'.storageDir.'" directory is not writeable, PhotoStack can not create your thumbnails.';
			} else {
				
				// Image to make thumb after.
				
				$originalPath = dirRoot."/".$albumGet."/".imageDir.$name;
				$thumbPath = dirRoot."/".storageDir."/".'thumb-'.$albumGet.'-'.$name.'.png';
				
				//Create a canvas from the image.
				$srcImg = imagecreatefromjpeg($originalPath);
				
				//Get the image size into the $srcSize Array
				$srcSize = getimagesize($originalPath); //[0] is width [1] is height
				
				
				//Set the size of the new smaller thumbnail
				
				if (fullSize != 'yes') {
					$dstW = $srcSize[0] * imageScale;	
					$dstH = $srcSize[1] * imageScale;
				} elseif(fullSize == 'yes' && thumbHeight or thumbWidth > 0)  {
					(thumbHeight > thumbWidth)? ($magnify = $srcSize[1] / thumbHeight) : ($magnify = $srcSize[0] / thumbWidth);
									
					$dstW = $srcSize[0] / $magnify;
					$dstH = $srcSize[1] / $magnify;
					
				} else {
					echo 'Your thumbnail options are set incorrectly.';
					exit;
				}
				
				//Create a blank canvas
				$dstImg = $this->createimage($dstW,$dstH);

				//Resize the $srcImg image onto the $dstImg
				$this->copyimage($dstImg, $srcImg,		//dst_im, src_im
								0, 0,						//int dstX, int dstY
					  			0, 0,						//int srcX, srcY
					  			$dstW, $dstH,				//int dstW, int dstH
        		    		    $srcSize[0], $srcSize[1]);	//int srcW, int srcH
        		
				# Write the full size thumbnail to the thumb path.
        		imagepng($dstImg, $thumbPath);
        		
        		imagedestroy($srcImg);
        		imagedestroy($dstImg);
				
				if (fullSize != 'yes') {
					$srcImg = imagecreatefrompng($thumbPath);
        		       		
        			$srcX = ($dstW / 2) - (thumbWidth / 2);
					$srcY = ($dstH / 2) - (thumbHeight / 2);
					
					//Create the thumbnail canvas.
					$thumbImg = $this->createimage(thumbWidth,thumbHeight);
					
					#copy the 
                	$this->copyimage($thumbImg, $srcImg,	//dst_im, src_im
									0, 0,				//int dstX, int dstY
						  			$srcX, $srcY,		//int srcX, srcY
						  			$dstW, $dstH,		//int dstW, int dstH
        			       			$dstW, $dstH);		//int srcW, int srcH
        		       			
        			# Write the $thumbImg resource img to file.
					imagepng($thumbImg, dirRoot."/".storageDir."/".'thumb-'.$albumGet.'-'.$name.'.png');
					
					imagedestroy($srcImg);
					imagedestroy($thumbImg);
				}
			}
		} 		
	}
	
	function copyimage($dst,$src,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH) {
				
		(gd2 != 'no')?
			($result = imagecopyresampled($dst,$src,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH))
		:
			($result =  imagecopyresized($dst,$src,$dstX,$dstY,$srcX,$srcY,$dstW,$dstH,$srcW,$srcH));
		
		return $result;
		
	}
	
	function createimage($dstW,$dstH) {
		
		(gd2 != 'no')?
			($result = ImageCreateTrueColor($dstW,$dstH))
		:
			($result = ImageCreate($dstW,$dstH));
		
		return $result;
	}
	
	
	function flushThumb ($integer,$albumGet) {			
		if (@filemtime(dirRoot."/".
				storageDir."/".
				'thumb-'.$albumGet.'-'.$this->photos[$integer]['name'].'.png') < 	
			@filemtime(dirRoot."/"
				.$albumGet.'/'.imageDir.$this->photos[$integer]['name'])
			or $_GET['flush'] == 'yes' && file_exists(
				dirRoot."/".storageDir."/".
				'thumb-'.$albumGet.'-'.$this->photos[$integer]['name'].'.png')
			) {
			
				@unlink(dirRoot."/".
					storageDir."/".
					'thumb-'.$albumGet.'-'.$this->photos[$integer]['name'].'.png');
		}
	}
}

# The class used in conjunction with photostack.
class organize extends photostack {
	var $displayData;
	
	function organize () {
		if(!defined('config')) {
			echo '<p><strong>You have not loaded a configuration file!</strong></p>
				<p>An example index document:</p>
					<p><code>&lt;?php<br /># Pull in the photostack.php program.<br />
					require("photostack.php");<br /><br />
					# Load your configuration file.<br />
					load(\'../config.php\');<br /><br /># Run photostack.<br />?&gt;</code></p>';
			exit;
		}
		
		ob_start();
		$this->displayData = new displayData;

		$this->head();
		
		$md5 = md5('PhotoStack'.$_SERVER['SERVER_ADDR'].photostackUsername.photostackPassword);

		if($_COOKIE['PhotoStack'] != $md5 or $_POST['username'] or $_POST['password'] or $_GET['logout']) {
		echo '<h2>Login</h2>';
				
				if($_GET['logout'] == 'yes') {
					setcookie('PhotoStack',
					  'nothing', 
					  time() - 3600,
					  '/',
					  '.'.$_SERVER['HTTP_HOST']);
					  
					echo '<h3 class="big">Logout Successful</h3>';
					
				}
				
				if($_POST['username'] == photostackUsername && $_POST['password'] == photostackPassword) {
					setcookie('PhotoStack',
					  md5('PhotoStack'.$_SERVER['SERVER_ADDR'].$_POST['username'].$_POST['password']), 
					  time()+60*60*24*30,
					  '/',
					  '.'.$_SERVER['HTTP_HOST']);
					
					header('Location: '.webConfigURL.'');
					  
				} elseif($_POST) {
					echo '<h3 class="big">Incorrect Authentication Information</h3>';
				}
				
				
				echo '
			<form action="'.webConfigURL.'" method="post">
			<p class="center">Username: <input type="text" name="username" size="20" /></p>
			<p class="center">Password: <input type="password" name="password" size="20" /></p>
			<p class="center"><input type="submit" name="submit" value="Login" /></p>
			</form>
			';
				
		
		
		
		} elseif(isset($_COOKIE['PhotoStack']) && $_COOKIE['PhotoStack'] == $md5) {

			if($_POST['__mode']) {
				$_GET['__mode'] = $_POST['__mode'];
			}
			
			if($_POST['album']) {
				$_GET['album'] = $_POST['album'];
			}
			
			switch ($_GET['__mode']) {
		
				case 'editAlbum':
					$this->editAlbum();
					break;
				case 'editDesc':
					$this->editDesc();
					break;
				
				case 'addPhotos':
					$this->addPhotos();
					break;
		
				case 'deletePhotos':
					$this->deletePhotos();
					break;
				
				case 'addAlbum':
					$this->addAlbum();
					break;
				
				case 'deleteAlbum':
					$this->deleteAlbum();
					break;
		
				default: 
					$this->defaultDisplay();		
			}
		
		}
		$this->foot();
		
		$page = ob_get_contents();
		ob_end_clean();
		
		echo $page;//str_replace('</h2>','</h2><div id="content">',$page);
	}
	
	function head () {
		echo '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       		 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<title>PhotoStack</title>
		<link rel="stylesheet" href="organize.css" media="screen" />
		</head>
		<body id="'.$_GET['__mode'].'">
		
			<h1><a href="'.webConfigURL.'">PhotoStack &#8250; Organize</a></h1>
			<div id="holder">';
				
				if($_COOKIE['PhotoStack'] && $_GET['logout'] != 'yes') {
				echo '
				<ul id="menu"><li><a href="'.webConfigURL.'">Edit Albums</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?__mode=addAlbum">Add Albums</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?__mode=deleteAlbum">Delete Albums</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?logout=yes">Logout</a></li></ul>';
				
				if(
			$_GET['__mode'] == 'deletePhotos' or
			$_GET['__mode'] == 'addPhotos' or 
			$_GET['__mode'] == 'editAlbum' or
			$_GET['__mode'] == 'editDesc' ) {
			$this->displayData->rawTemplate('
			
			<ul id="submenu">
				<li><strong>Album Tasks:</strong> <a href="'.webConfigURL.'?__mode=editAlbum&#038;album=<$PSCurrentAlbumFolder$>">Edit Album</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?__mode=editDesc&#038;album=<$PSCurrentAlbumFolder$>">Edit Descriptions</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?__mode=addPhotos&#038;album=<$PSCurrentAlbumFolder$>">Add Photos</a>&#8226;</li>
				<li><a href="'.webConfigURL.'?__mode=deletePhotos&#038;album=<$PSCurrentAlbumFolder$>">Delete Photos</a></li>
			</ul>
			');
		}
				} 
			echo '
			
			<script type="text/javascript">
			// <![CDATA[
			function MM_jumpMenu(targ,selObj,restore){ //v3.0
  				eval(targ+".location=\'"+selObj.options[selObj.selectedIndex].value+"\'");
  				if (restore) selObj.selectedIndex=0;
			}
			// ]]>
			</script>
			';
	}
	
	function foot () {
		echo '
		</div>
		</div></body>
		</html>';
	}

	function defaultDisplay() {
		$this->displayData->rawTemplate('
		<h2>Your Photo Albums</h2>
		<div class="rightBox">
		<h3>Tips</h3>
		<p>To the left are your current albums, click them to edit photos move the album to another folder, rename, the album, and edit photo descriptions.</p>
		
		<h3>Tasks</h3>
		
		<ul>
		<li><a href="<$PSNavigationListing$>?flush=yes">Flush All Thumbnails</a></li>
		<li><a href="<$PSNavigationListing$>">View Your Installation</a></li>
		</ul>
		
		</div>
		
		<ul class="plain">
		<PSAlbums>
		<PSPhotos num="1" offset="0">
		<li style="height:<$PSPhotoThumbHeight$>px;">
			<a href="'.webConfigURL.'?__mode=editAlbum&#038;album=<$PSAlbumFolder$>"><img 
				src="<$PSPhotoThumbPath$>" 
				alt="<$PSAlbumTitle$>" 
				width="<$PSPhotoThumbWidth$>" 
				height="<$PSPhotoThumbHeight$>" /></PSPhotos></a>
				
				<p><a href="'.webConfigURL.'?__mode=editAlbum&#038;album=<$PSAlbumFolder$>"><strong><$PSAlbumTitle$></strong><br />
				<$PSAlbumCount$> Photos</a></p>
		</li>
		</PSAlbums>
		
		</ul>
		');
	
	}
	
	function ftpConnect() {
			$ftp = ftp_connect(ftpHost);
			
			$login_result = ftp_login($ftp, ftpUsername, ftpPassword);

			// check connection
			if (!$ftp or !$login_result) { 
        		echo "FTP connection has failed!";
        		echo "Attempted to connect to $ftp_server for user $ftp_user_name"; 
        		exit; 
    		}
   			ftp_pasv($ftp, true);
   			ftp_chdir($ftp, ftpDir.'/');
   			
   			return $ftp;
	}
	
	#This function is so fucked up looking.
	function ftpUpload ($files, $ftp) {
		if($files) {
			$forshell = escapeshellcmd($_POST['albumFolder']);
			$print .= '<ol>';
			# Count how many files to try and upload
			$count = count($files['upload_file']['name']);
			
			for($i = 0; $i <= $count - 1; $i++) {
				# If the file is '' then don't upload it.
				if(eregi('.*\.zip$',$files['upload_file']['name'][$i])) {
					if(ftp_put($ftp, $files['upload_file']['name'][$i], $files['upload_file']['tmp_name'][$i], FTP_BINARY)) {					
						if(file_exists(dirRoot.'/'.$_POST['albumFolder'].'/'.$files['upload_file']['name'][$i])) {
							$perms = substr(sprintf("%o", fileperms(dirRoot.'/'.$_POST['albumFolder'].'/')), '2');
							
							if(ftp_site($ftp, 'CHMOD 777 '.ftpDir.'/'.$_POST['albumFolder'].'/')) {
								
								exec('cd '.dirRoot.'/'.$forshell.'/; pwd; '.
								'/usr/bin/unzip -o '.dirRoot.'/'.$forshell.'/'.$files['upload_file']['name'][$i]);
								ftp_site($ftp, 'CHMOD '.$perms.' '.ftpDir.'/'.$_POST['albumFolder'].'/');
								ftp_delete($ftp, ftpDir.'/'.$_POST['albumFolder'].'/'.$files['upload_file']['name'][$i]);
								$print .= '<li>'.$files['upload_file']['name'][$i].' was successfully unzipped.</li>';
							} else {
								$print .= '<li>An error occured. Your FTP server does not support the SITE set of commands.</li>';
							}
						}
						
					}
				} elseif ($files['upload_file']['name'][$i] != '' &&
						 preg_match('/.*\.(txt|jpg|png|gif|JPG|PNG|TXT|GIF)/',$files['upload_file']['name'][$i])) {
					if(ftp_put($ftp, $files['upload_file']['name'][$i], $files['upload_file']['tmp_name'][$i], FTP_BINARY)) {
						$print .= '<li>'.$files['upload_file']['name'][$i].' was uploaded successfully.</li>';
					}
				} elseif ($files['upload_file']['name'][$i] != '') {
					$print .= '<li>'.$files['upload_file']['name'][$i].' - An encountered file has an unsuitable extension for uploading.</li>';
				}
			
			}
			
			$print .= '</ol>';
			return $print;			
		}
	}
	
	function addAlbum() {	
		if($_POST['edit'] == 'yes' && $_POST['albumFolder'] != '' && $_POST['albumTitle'] != '') {
			/*$_POST['albumFolder'] = stripslashes($_POST['albumFolder']);
			$_POST['albumTitle'] = stripslashes($_POST['albumTitle']);*/
			
			echo '<h2>Creating New Album</h2>';
			
			$ftp = $this->ftpConnect();

 			# Create temp file and get old album data
 			/*$temp = tmpfile();
			if(@ftp_fget($ftp, $temp, albumListing, FTP_ASCII)) {
				rewind ($temp);
				$data = fread($temp, ftp_size($ftp, albumListing) );
			}
			fclose($temp);*/	
			
			
			#If the album does not exist make the directory for it and change to that directory
			if(@ftp_mkdir($ftp, ftpDir.'/'.$_POST['albumFolder'].'/')) {
				echo 'Album folder created';
			} else {
				echo 'Sorry but that album directory already exists.';
			}
				# Get old Album Listing (albums.txt or nonsense like that.)
				$listing = fopen(dirRoot.'/'.albumListing, 'r');
				$data = fread($listing, filesize(dirRoot.'/'.albumListing));
				fclose($listing);
			
				#append new album data to old album data
				$data = trim($data);
				if($_POST['albumFolder'] && $_POST['albumFolder'] != '') {
					$data = $_POST['albumFolder'].":".$_POST['albumTitle']."\n".$data;				
				}
			
			
				# Create temp file and write to server in ASCII
				$temp = tmpfile();
				fwrite($temp, $data);
				rewind($temp);
				$album = ftp_fput($ftp, albumListing, $temp, FTP_ASCII);
				fclose($temp);	
							
				if(@ftp_chdir($ftp, ftpDir.'/'.$_POST['albumFolder'].'/')) {
					$upload = $this->ftpUpload($_FILES, $ftp);
					
					if (!$album) { 
   	    				echo '<h3 class="big">Album creation has failed!</h3>';
   					} else {
       					echo '<h3 class="big">Album added!</h3>';
       					echo $upload;
    				}
				} else {
					echo 'There was an error creating your photo album.';
				}
			
			
			ftp_quit($ftp);
		} else {
			echo '<h2>Add a New Album</h2>';
			echo '
				<div class="rightBox">
					<h3>Tips</h3>
					<p><strong>Title:</strong><br />
					This is the title of this album of photos. For example "John At Work".</p>
					
					<p><strong>Directory Name:</strong><br />
					This is the directory that your album will be created in and should be unique to this album. For example "john".</p>
					
					<p><strong>Choose Photos:</strong><br />
					You can upload as many or as few photos when you create this album.</p>
					<p>To upload more photos select approximately how many you would like to 
					upload from the drop down menu. Upload more photos when you feel like it.</p>
					
					<p>The accepted file formats are JPG, PNG, and ZIP. A zip archive must 
					contain the files you would like extracted into this album.</p>
					
				</div>
				<form action="'.webConfigURL.'" method="post" enctype="multipart/form-data">
				<input type="hidden" name="__mode" value="addAlbum" />
				<input type="hidden" name="edit" value="yes" />
			<p>
			<strong>Title:</strong>
			<input type="text" name="albumTitle" size="35" value"'.$_GET['albumTitle'].'" />
			</p>
			
			<p><strong>Directory Name:</strong>
			<input type="text" name="albumFolder" size="25" value"'.$_GET['albumFolder'].'" />
			</p>';
			
			echo '<p><strong>Choose Photos:</strong></p>
			<p>Upload <select style="width: 100px;" name="number" onchange="MM_jumpMenu(\'parent\',this,0)">';
			
			for($i = 0; $i <= 40; $i) {
			$i = $i + 8;
				echo '<option value="'.webConfigURL.'?__mode=addAlbum&#038;n='.$i.'">'.$i.' Photos</option>';
			}
			echo '</select></p>';
			
			if(!$_GET['n']) { $n = 8; } else { $n = $_GET['n']; }
			echo '<ol>';
			for($i = 1; $i <= $n;$i++) {
				echo '<li><input type="file" name="upload_file[]" /></li>';
			}
			
			echo '
			</ol>
			<input type="submit" value="Create Album and Upload Photos" />
			</form>
			';
		}
	}
	
	
	function deleteAlbum() {
		if($_GET['edit'] == 'yes' && $_GET['delete']) {
			echo '<h2>Deleting Albums</h2>';
			$ftp = $this->ftpConnect();
			ftp_chdir($ftp, ftpDir.'/');
 			
 			$listing = fopen(dirRoot.'/'.albumListing, 'r');
			$data = fread($listing, filesize(dirRoot.'/'.albumListing));
			fclose($listing);
		
			$data = trim($data);
		
			for($i = 0; $i <= count($_GET['delete']) - 1;$i++) {
						
				@ftp_chdir($ftp, ftpDir.'/'.$_GET['delete'][$i].'/');
				
				if($contents = ftp_nlist($ftp, ftpDir.'/'.$_GET['delete'][$i])) {
					foreach($contents as $file) {
						ftp_delete($ftp, $file);
					}
				}
				
				if(@ftp_rmdir($ftp, ftpDir.'/'.$_GET['delete'][$i].'/')) {
					echo '<h3 class="big">The album "'.$_GET['albumTitle'][$_GET['delete'][$i]].'" was deleted from the server.</h3>';
				} else {
					echo '<p>'.$_GET['albumTitle'][$albumfolder].' album was unsuccessfully deleted from the server (it may not have had a directory).</p>';
				}
				
				
				$data = preg_replace('/^'.preg_quote($_GET['delete'][$i]).':'.preg_quote($_GET['albumTitle'][$_GET['delete'][$i]]).'.*$/m', '', $data);
					$data = preg_replace('/^\s*$/m', '', $data);
					
					$temp = tmpfile();
					fwrite($temp, $data);
					rewind($temp);
					$upload = ftp_fput($ftp, ftpDir.'/'.albumListing, $temp, FTP_ASCII);
					fclose($temp);
					echo '<h3 class="big">The album was deleted from '.albumListing.'</h3>';
				
			}
			
			ftp_quit($ftp); 			
		} else {
			echo '<h2>Delete Albums</h2>';
			$this->displayData->rawTemplate('<form action="'.webConfigURL.'" method="get">
			<input type="hidden" name="__mode" value="deleteAlbum" />
			<input type="hidden" name="edit" value="yes" />
			<ul class="centerPlain">
				<PSAlbums>
				
				<li style="height: 50px;">
					<p><strong><$PSAlbumTitle$></strong> - 
						<em><$PSAlbumCount$> Photos</em><br />
						<input type="checkbox" name="delete[]" value="<$PSAlbumFolder$>" /> Delete
						<input type="hidden" name="albumTitle[<$PSAlbumFolder$>]" value="<$PSAlbumTitle$>" />
					</p>
				</li>
				</PSAlbums>
			</ul>
			<p class="center"><input type="submit" value="Delete Checked Albums" /></p>
			</form>');
		}
	}
	
	function editAlbum() {
		$this->displayData->rawTemplate('<h2>Edit <$PSCurrentAlbumTitle$></h2>');

		if($_GET['edit'] == 'yes' && $_GET['albumFolder'] != '' or $_GET['albumTitle'] != '' or $_GET['albumDate']) {
			
			/*$_GET['albumFolder'] = stripslashes($_GET['albumFolder']);
			$_GET['albumTitle'] = stripslashes($_GET['albumTitle']);
			$_GET['albumDate'] = stripslashes($_GET['albumDate']);
			
			$_GET['oldFolder'] = stripslashes($_GET['oldFolder']);
			$_GET['oldTitle'] = stripslashes($_GET['oldTitle']);*/
			
			if($_GET['albumFolder'] == '') {
				$_GET['albumFolder'] = $_GET['oldFolder'];
			}
			
			if($_GET['albumTitle'] == '') {
				$_GET['albumTitle'] = $_GET['oldTitle'];
			}
			
			if($_GET['albumDate'] == '' or $_GET['albumDate'] == ' ') {
				$_GET['albumDate'] = '';
			} else {
				$_GET['albumDate'] = ':'.$_GET['albumDate'];
			}
			
			$ftp = $this->ftpConnect();
 			ftp_chdir($ftp, ftpDir.'/');
 			
 			 # Get old Album Listing (albums.txt or nonsense like that.)
			$listing = fopen(dirRoot.'/'.albumListing, 'r');
			$data = fread($listing, filesize(dirRoot.'/'.albumListing));
			fclose($listing);

			$data = trim($data);
			
			$pattern = "/".preg_quote($_GET['oldFolder']).":".preg_quote($_GET['oldTitle']).".*/";
			$replacement = $_GET['albumFolder'].":".$_GET['albumTitle'].$_GET['albumDate'];
			$data = preg_replace($pattern,$replacement,$data);
			
			# Create temp file and write to server in ASCII
			$temp = tmpfile();
			fwrite($temp, $data);
			rewind($temp);
			$album = ftp_fput($ftp, albumListing, $temp, FTP_ASCII);
			fclose($temp);
 			
			# Move if the directory has changed.
			if($_GET['albumFolder'] != $_GET['oldFolder']) {
				if(@ftp_chdir($ftp, ftpDir.'/'.$_GET['albumFolder'].'/') == 0) {
					ftp_mkdir($ftp, ftpDir.'/'.$_GET['albumFolder'].'/');
				} else {
					echo 'That album folder is already being used.<br />';
				}
				
				#rename this bitch
				ftp_rename($ftp, $_GET['oldFolder'], $_GET['albumFolder']);
			
				if(ftp_rmdir($ftp, ftpDir.'/'.$_GET['oldFolder'].'/')) {
					echo 'The album '.$_GET['albumFolder'].' was moved.<br />';
				}
			}	
			ftp_quit($ftp);
    		
    		header('Location: '.webConfigURL.'?__mode=editAlbum&edited=yes&album='.$_GET['albumFolder']);

		}
			if ($_GET['edited'] == 'yes') { 
   	    	
       			echo '<h3 class="big">Album Edited!</h3>';       			
    		}
			$this->displayData->rawTemplate('
			<div class="columnLeft">
			<h3>Album Info:</h3>
			<p><strong>Title:</strong> <$PSCurrentAlbumTitle$></p>
			
			<p><strong>Directory:</strong> <$PSCurrentAlbumFolder$></p>
			
			<p><strong>Date:</strong> <$PSCurrentAlbumDate$></p>
			
			<p><strong><a href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>">View</a></strong> | <strong><a href="<$PSNavigationListing$>?album=<$PSCurrentAlbumFolder$>&flush=yes">Flush</a></strong></p>
			</div>
			<div class="columnRight">
			<h3>Change Album Info:</h3>
			<form action="'.webConfigURL.'" method="get">
			<input type="hidden" name="__mode" value="editAlbum" />
			<input type="hidden" name="album" value="'.$_GET['album'].'" />
			<input type="hidden" name="edit" value="yes" />
			<input type="hidden" name="oldTitle" value="<$PSCurrentAlbumTitle$>" />
			<input type="hidden" name="oldFolder" value="<$PSCurrentAlbumFolder$>" />
			
			<p><strong>Title:</strong> <input type="text" name="albumTitle" size="20" /></p>
			<p><strong>Directory Name:</strong> <input type="text" name="albumFolder" size="20" /></p>
			
			<p><strong>Date:</strong> <input type="text" name="albumDate" size="20" /></p>
			
			<p><input type="submit" name="submit" value="Change Album Info" /></p>
			</form>
			</div>
			');
		
	}
	
	function editDesc() {
		$this->displayData->rawTemplate('<h2>Edit Descriptions</h2>');

		if($_GET['edit'] == 'yes') {
			
			$ftp = $this->ftpConnect();
 			ftp_chdir($ftp, ftpDir.'/'.$_GET['album'].'/');
					
			for($i = 0; $i <= count($_GET['photos']) - 1;$i++) {
				
				if($_GET['desc'][$i] && $_GET['desc'][$i] != '' && $_GET['name'][$i] != $_GET['desc'][$i]) {
					$page .= $_GET['photos'][$i].":".$_GET['desc'][$i]."\n";					
				}
			}
			
			$temp = tmpfile();
			fwrite($temp, $page);
			rewind($temp);
			$upload = ftp_fput($ftp, ftpDir.'/'.$_GET['album'].'/'.photoDesc, $temp, FTP_ASCII);
			
			fclose($temp);
		
			if (!$upload) { 
   	    		echo "FTP upload has failed!";
   			} else {
       			echo "Descriptions and files adjusted accordingly!";
    		}
    		
			// close the FTP stream 
			ftp_quit($ftp); 
		} else {
			$this->displayData->rawTemplate('
			<form action="'.webConfigURL.'" method="get">
			<input type="hidden" name="__mode" value="editDesc" />
			<input type="hidden" name="album" value="'.$_GET['album'].'" />
			<input type="hidden" name="edit" value="yes" />
			<ul class="centerPlain">
			<PSPhotos><li id="photo<$PSPhotoFilename$>" style="height:<$PSPhotoThumbHeight$>px"><a href="#photo<$PSPhotoFilename$>" onClick="window.open(\'<$PSPhotoPath$>\',\'mywindow\',\'width=<$PSPhotoWidth$>,height=<$PSPhotoHeight$>\')" ><img 
					src="<$PSPhotoThumbPath$>" 
					width="<$PSPhotoThumbWidth$>" 
					height="<$PSPhotoThumbHeight$>" 
					alt="<$PSPhotoFilename$>" /></a><p><strong><$PSPhotoFilename$></strong><br />
					<input type="hidden" name="photos[]" value="<$PSPhotoFilename$>" />
					<input type="text" name="desc[]" size="26" value="<$PSPhotoDescription$>" />
					<input type="hidden" name="name[]" value="<$PSPhotoFilename$>" />
					</p></li></PSPhotos>
			</ul>
			<p class="center">
			<input type="submit" value="Update Descriptions" />
			</p>
			</form>
			');
		}
	}
	
	function addPhotos() {
		$this->displayData->rawTemplate('<h2>Add Photos to &#8220;<$PSCurrentAlbumTitle$>&#8221;</h2>');
		if($_POST['edit'] == 'yes' && $_POST['albumFolder'] && $_FILES) {
			$ftp = $this->ftpConnect();
			/*$_POST['albumFolder'] = stripslashes($_POST['albumFolder']);*/
			
			#If the album does not exist make the directory for it and change to that directory
			if(@ftp_chdir($ftp, ftpDir.'/'.$_POST['albumFolder'].'/') ) {
				$upload = $this->ftpUpload($_FILES, $ftp);
						
				if (!$upload) { 
   	    			echo '<h3 class="big">Photo adddition has failed!</h3>';
   				} else {
       				echo '<h3 class="big">Photos added to album!</h3>';   
       				echo $upload;    			
    			}
			} else {
				echo 'That album does not exist.';
			}
			ftp_quit($ftp);
		} else {
			echo '<div class="rightBox">
			<h3>Tips</h3>
			
			<p><strong>Upload your files:</strong><br />
					You can upload as many or as few photos when you create this album.</p>
					<p>To upload more photos select approximately how many you would like to 
					upload from the drop down menu. Upload more photos when you feel like it.</p>
					
			<p><strong>File Types:</strong><br />
					JPG, PNG, GIF, and ZIP files are acceptable. ZIP files should be a single zipped file of JPGs.</p>
					
			</div>';
			echo '
				<form action="'.webConfigURL.'" method="post" enctype="multipart/form-data">
				<input type="hidden" name="__mode" value="addPhotos" />
				<input type="hidden" name="edit" value="yes" />
				<input type="hidden" name="album" value="'.$_GET['album'].'" />
				<input type="hidden" name="albumFolder" value="'.$_GET['album'].'" />';
			
			echo '<p><strong>Choose Photos:</strong></p>
			<p>Add<select name="number" onchange="MM_jumpMenu(\'parent\',this,0)">';
			echo '<option value="">Select</option>';
			for($i = 0; $i <= 40; $i) {
			$i = $i + 8;
				echo '<option value="'.webConfigURL.'?__mode=addPhotos&#038;album='.$_GET['album'].'&#038;n='.$i.'">'.$i.'</option>';
			}
			echo '</select> Photos</p>';
			
			if(!$_GET['n']) { $n = 8; } else { $n = $_GET['n']; }
			echo '<ol>';
			for($i = 1; $i <= $n;$i++) {
				echo '<li><input type="file" name="upload_file[]" /></li>';
				
				
			}
			
			echo '
			</ol>
			<p class="center"><input type="submit" value="Add Photos to Album" /></p>
			</form>
			';
		}
	}
	
	function deletePhotos() {
		$this->displayData->rawTemplate('<h2>Delete Photos from &#8220;<$PSCurrentAlbumTitle$>&#8221;</h2>');
		if($_GET['edit'] == 'yes' && $_GET['delete']) {
			$ftp = $this->ftpConnect();
			
 			ftp_chdir($ftp, ftpDir.'/'.$_GET['album'].'/');
 			echo '<h3 class="big">Photos Deleted Successfully</h3>
 			<ol>';
			foreach($_GET['delete'] as $key => $value) {
				if(file_exists(dirRoot.'/'.$_GET['album'].'/'.$value)) {
					ftp_delete($ftp, ftpDir.'/'.$_GET['album'].'/'.$value);
					echo '<li>'.$value.' was deleted from this album.</li>';
				}
			}
			
			echo '</ol>';		
			
			ftp_quit($ftp);
		} else {
			$this->displayData->rawTemplate('
			<form action="'.webConfigURL.'" method="get">
			<input type="hidden" name="__mode" value="deletePhotos" />
			<input type="hidden" name="album" value="'.$_GET['album'].'" />
			<input type="hidden" name="edit" value="yes" />
			<ol class="centerPlain">
			<PSPhotos><li style="height: <$PSPhotoThumbHeight$>px;">
				<a href="#photo<$PSPhotoFilename$>" onClick="window.open(\'<$PSPhotoPath$>\',\'mywindow\',\'width=<$PSPhotoWidth$>,height=<$PSPhotoHeight$>\')" ><img 
					src="<$PSPhotoThumbPath$>" 
					width="<$PSPhotoThumbWidth$>" 
					height="<$PSPhotoThumbHeight$>" 
					alt="<$PSPhotoFilename$>" /></a>
				<p>
					<strong><$PSPhotoFilename$></strong>
					<em><$PSPhotoDescription$></em><br />
					<input type="hidden" name="photo" value="<$PSPhotoFilename$>" />
					<input type="checkbox" name="delete[]" value="<$PSPhotoFilename$>" /> Delete
				</p>
			</li></PSPhotos>
			</ol>
			<p class="center" style="color: red;">
			<strong>There is no turning back!!!!</strong><br />
			
			<input type="submit" value="Delete Photos" />
			</p>
			</form>
			');
		}
	}
	
	
		
#End of Class...
}



class exif {
//================================================================================================
//================================================================================================
// Converts from Intel to Motorola endien.  Just reverses the bytes (assumes hex is passed in)
//================================================================================================
//================================================================================================
function intel2Moto($intel) {
	$len = strlen($intel);
	$moto="";
	for($i=0; $i<=$len; $i+=2) {
		$moto.=substr($intel,$len-$i,2);
	}
	return $moto;
}

//================================================================================================
//================================================================================================
// Looks up the name of the tag
//================================================================================================
//================================================================================================
function lookup_tag($tag) {
	switch($tag) {
	
		//used by IFD0
		case "010e": $tag = "ImageDescription";break;
		case "0132": $tag = "DateTime";break;
		case "8769": $tag = "ExifOffset";break;
		
		//used by Exif SubIFD
		case "829a": $tag = "ExposureTime";break;
		case "9000": $tag = "ExifVersion";break;
		case "9003": $tag = "DateTimeOriginal";break;
		case "9004": $tag = "DateTimedigitized";break;
		
		default: $tag = "unknown:".$tag;break;
	}
	return $tag;

}

//================================================================================================
//================================================================================================
// Looks up the datatype
//================================================================================================
//================================================================================================
function lookup_type(&$type,&$size) {
	switch($type) {
		case "0001": $type = "UBYTE";$size=1;break;
		case "0002": $type = "ASCII";$size=1;break;
		case "0003": $type = "USHORT";$size=2;break;
		case "0004": $type = "ULONG";$size=4;break;
		case "0005": $type = "URATIONAL";$size=8;break;
		case "0006": $type = "SBYTE";$size=1;break;
		case "0007": $type = "UNDEFINED";$size=1;break;
		case "0008": $type = "SSHORT";$size=2;break;
		case "0009": $type = "SLONG";$size=4;break;
		case "000a": $type = "SRATIONAL";$size=8;break;
		case "000b": $type = "FLOAT";$size=4;break;
		case "000c": $type = "DOUBLE";$size=8;break;
		default: $type = "error:".$type;$size=0;break;
	}
	return $type;
}

//================================================================================================
//================================================================================================
// Formats Data for the data type
//================================================================================================
//================================================================================================
function formatData($type,$tag,$intel,$data) {

	if($type=="ULONG") {
		$data = bin2hex($data);
		if($intel==1) $data = $this->intel2Moto($data);
		if($intel==0 && ($type=="USHORT" || $type=="SSHORT")) $data = substr($data,0,4);
		$data=hexdec($data);	
	}
	
	return $data;
}

//================================================================================================
//================================================================================================
// Reads one standard IFD entry
//================================================================================================
//================================================================================================
function read_entry(&$result,$in,$seek,$intel,$ifd_name,$globalOffset) {
	
	//2 byte tag
	$tag = bin2hex(fread( $in, 2 ));
	if($intel==1) $tag = $this->intel2Moto($tag);
	$tag_name = $this->lookup_tag($tag);
	
	//2 byte datatype
	$type = bin2hex(fread( $in, 2 ));
	if($intel==1) $type = $this->intel2Moto($type);
	$this->lookup_type($type,$size);
	
	//4 byte number of elements
	$count = bin2hex(fread( $in, 4 ));
	if($intel==1) $count = $this->intel2Moto($count);
	$bytesofdata = $size*hexdec($count);
	
	//4 byte value or pointer to value if larger than 4 bytes
	$value = fread( $in, 4 );
	
	if($bytesofdata<=4) { 	//if datatype is 4 bytes or less, its the value
		$data = $value;
	} else {				//otherwise its a pointer to the value, so lets go get it
		$value = bin2hex($value);
		if($intel==1) $value = $this->intel2Moto($value);
		$v = fseek($seek,$globalOffset+hexdec($value));  //offsets are from TIFF header which is 12 bytes from the start of the file
		if($v==0) {
			$data = fread($seek, $bytesofdata);
		} else if($v==-1) {
			$result['Errors'] = $result['Errors']++;
		}
	}
	
		//Format the data depending on the type and tag
		$formated_data = $this->formatData($type,$tag,$intel,$data);
		
		$result[$ifd_name][$tag_name] = $formated_data;
}

//================================================================================================
//================================================================================================
// Pass in a file and this reads the EXIF data
//
// Usefull resources
// http://www.ba.wakwak.com/~tsuruzoh/Computer/Digicams/exif-e.html
// http://www.w3.org/Graphics/JPEG/jfif.txt
// http://exif.org/
//================================================================================================
//================================================================================================
function read_exif_data_raw($path,$verbose) {
	
	if($path=='' or $path=='none') return;
	
	$in = fopen($path, "rb"); //the b is for windows machines to open in binary mode
	$seek = fopen($path, "rb"); //There may be an elegant way to do this with one file handle.
	
	$globalOffset = 0;
	
	if(!isset($verbose)) $verbose=0;
	
	$result['VerboseOutput'] = $verbose;
	$result['Errors'] = 0;
	
	//First 2 bytes of JPEG are 0xFFD8 
	$data = bin2hex(fread( $in, 2 ));
	if($data=="ffd8") {
		$result['ValidJpeg'] = 1;
	} else {
		$result['ValidJpeg'] = 0;
		fclose($in);
		fclose($seek);
		return $result;
	}	
	
	
	//Next 2 bytes are MARKER tag (0xFFE#)
	$data = bin2hex(fread( $in, 2 ));
	if($data=="ffe0") {
		$result['ValidJFIFData'] = 1;
		$size = bin2hex(fread( $in, 2 ));
		$result['JFIF']['Size'] = hexdec($size);
		$ident = fread( $in, 5 );
		$result['JFIF']['Identifier'] = $ident;
		$code = fread( $in, 1 );
		$result['JFIF']['ExtensionCode'] =  bin2hex($code);
		
		$data = fread( $in, hexdec($size)-8 );
		$result['JFIF']['Data'] = $data;
		$globalOffset+=hexdec($size)+2;
		
		$data = bin2hex(fread( $in, 2 ));
	} else {
		$result['ValidJFIFData'] = 0;
	}
	
	if($data=="ffe1") {
		$result['ValidEXIFData'] = 1;
	} else {
		$result['ValidEXIFData'] = 0;
		fclose($in);
		fclose($seek);
		return $result;
	}
	
	//Size of APP1 
	$size = bin2hex(fread( $in, 2 ));
	$result['APP1Size'] = hexdec($size);
	
	//Start of APP1 block starts with "Exif" header (6 bytes)
	$header = fread( $in, 6 );
	
	//Then theres a TIFF header with 2 bytes of endieness (II or MM) 
	$header = fread( $in, 2 );
	if($header==="II") {
		$intel=1;
		$result['Endien'] = "Intel";
	} else if($header==="MM") {
		$intel=0;
		$result['Endien'] = "Motorola";
	}
	
	//2 bytes of 0x002a
	$tag = bin2hex(fread( $in, 2 ));
	
	//Then 4 bytes of offset to IFD0 (usually 8 which includes all 8 bytes of TIFF header)
	$offset = bin2hex(fread( $in, 4 ));
	if($intel==1) $offset = $this->intel2Moto($offset);
	if(hexdec($offset)>8) $unknown = fread( $in, hexdec($offset)-8); //fixed this bug in 1.3
	
	//add 12 to the offset to account for TIFF header
	$globalOffset+=12;
	
	
	//===========================================================Start of IFD0
	$num = bin2hex(fread( $in, 2 ));
	if($intel==1) $num = $this->intel2Moto($num);
	$result['IFD0NumTags'] = hexdec($num);
	
	for($i=0;$i<hexdec($num);$i++) {
		$this->read_entry($result,$in,$seek,$intel,"IFD0",$globalOffset);
	}
	
	//store offset to IFD1
	$offset = bin2hex(fread( $in, 4 ));
	if($intel==1) $offset = $this->intel2Moto($offset);
	$result['IFD1Offset'] = hexdec($offset);
	
	//Check for SubIFD
	if(!isset($result['IFD0']['ExifOffset']) || $result['IFD0']['ExifOffset']==0) {
		fclose($in);
		fclose($seek);
		return $result;
	}
	
	//seek to SubIFD (Value of ExifOffset tag) above.
	$ExitOffset = $result['IFD0']['ExifOffset'];
	$v = fseek($in,$globalOffset+$ExitOffset);
	if($v==-1) {
		$result['Errors'] = $result['Errors']++;
	}
	
	//===========================================================Start of SubIFD
	$num = bin2hex(fread( $in, 2 ));
	if($intel==1) $num = $this->intel2Moto($num);
	$result['SubIFDNumTags'] = hexdec($num);
	
	for($i=0;$i<hexdec($num);$i++) {
		$this->read_entry($result,$in,$seek,$intel,"SubIFD",$globalOffset);
	}

	fclose($in);
	fclose($seek);
	return $result;
}	

}
?>