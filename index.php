<?php
	class episode {
	    var $title;
	    var $duration; 
	    var $description; 
	    var $pubDate;  
	    var $filename;
	    
	    function episode ($ep) {
	        foreach ($ep as $key => $val)
	            $this -> $key = $ep[$key];
	    }
	}

	function readDatabase() {
		

		$data = implode("", file("feed.xml"));
		$data = str_replace("itunes:", "", $data);
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    	xml_parse_into_struct($parser, $data, $values, $tags);
    	xml_parser_free($parser);
		
		foreach ($tags as $key=>$val) {
	        if ($key == "item") {
	            $molranges = $val;
	            // each contiguous pair of array entries are the 
	            // lower and upper range for each molecule definition
	            for ($i=0; $i < count($molranges); $i+=2) {
	                $offset = $molranges[$i] + 1;
	                $len = $molranges[$i + 1] - $offset;
	                $tdb[] = parseEpisodes(array_slice($values, $offset, $len));
	            }
	        } else {
	            continue;
	        }
	    }

	    return $tdb;
	}
	function parseEpisodes($mvalues) 
	{
	    for ($i=0; $i < count($mvalues); $i++) {
	        $mol[$mvalues[$i]["tag"]] = $mvalues[$i]["value"];
	    }
	    return new episode($mol);
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>This Is My Son</title>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="Style me!">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="cathy.css">
	</head>

	<style>
		a {
			color: rgb(44, 92, 213);
		}
		a:hover {
			color: #2796FF;
		}
		body {
			background-color: #ddfff2;
			font-weight: 300;
		}
		article {
			    padding: 1rem .75rem;
		}
		article > header {
			text-align: center;
			padding: 2em;
			position: relative;
		}
		nav h2 {
			margin-top: 0;
		}
		nav h2,
		.expand-control {
			font-size: .9em;
			font-weight: 500;
			margin-bottom: 1em;
			padding: .25em;
			
		}
		.expand-control {
			text-align: center;
			font-size: .8em;
			font-weight: normal;
		}
			.expand-control:hover {
				cursor: pointer;
				background-color: rgba(44, 92, 213, .05);
				text-decoration: none;
				color: rgb(44, 92, 213);
			}
		nav a {
			font-size: .8em;
			margin-bottom: .5em;
			font-weight: 400;
			padding: .25em;
		}
		article {
			background-color: rgb(242, 48, 119);
			color: white;
		}
			article h1,
			article h2,
			article h3 {
				color: rgba(255,255,255, .75);
			}
		.episode {
			background-color: rgba(0,0,0,.15); 
			padding: 1.5em;
			margin-bottom: 3em;
		}
			.episode__header__title {
				font-size: 1.5em;
				color: rgb(171, 255, 223);
				font-weight: 500;
				text-shadow: -2px -3px 0px rgb(5, 67, 221);
				margin: 0 0 1em;
			}
			.episode__header__date {
				font-size: .8em;
				opacity: .8;
				margin: 0 0 .75em;
			}
			.episode__player {
				width: 100%;
				margin-bottom: 1em;
			}
			.episode__description {
				font-size: .9em;
				line-height: 1.5;
				font-weight: 300;
			}
		.button-group {
			position: absolute;
			top: 0;
			right: 0;
		}
		.button,
		a.button {
			background-color: rgba(171, 255, 223, .1);
			display: inline-block;
			padding: .5em 1em;
			border-radius: 3px;
			border: 1px solid rgba(255,255,255,.5);
			font-size: .8em;
			color: white;
			transition: all .2s ease;
		}
		.button:hover,
		a.button:hover {
			text-decoration: none;
			/*background-color: rgb(171, 255, 223);
			color: rgb(5, 67, 221);*/
			background-color: rgba(0,0,0,.1);
		}
		.expand-content {
			display: none;
		}
		@media (min-width: 900px) {
			article {
				padding: 1rem 2rem;
			}
		}
	</style>
	
	<body>
		<nav>
			<h2>Recent Episodes</h2>
		<?php
			$db = readDatabase();

			foreach ($db as $key => $episode) {
				$title = $episode->title;
				if ($key == 5) {
					echo "<a class='expand-control' onclick='expandContent(this);'>Show All</a><div  class='expand-content'>";
				}
				echo "<a href='#episode--$key'>$title</a>";
			}
			if ($key > 4) echo "</div>";
		?>

		</nav>
		<main>
			<article>
				<header>
					<img src='logo.png' alt="This Is My Son" style="width: 300px;">
					<h1 style="font-size: 1px; color: transparent;">This Is My Son</h1>
					<div class="button-group">
						<a target="_blank" href="feed.xml" class="button">Feed</a>
						<a target="_blank" href="https://itunes.apple.com/us/podcast/this-is-my-son/id1082165827?mt=2" class="button">iTunes</a>
						<a target="_blank" href="http://www.twitter.com/thisismyson" class="button">Twitter</a>
				</header>
				
				<?php

					foreach ($db as $key => $episode) {
						//print_r($episode);
						$title = $episode->title;
						$description = $episode->description;
						$file = $episode->filename;
						$timestamp = strtotime($episode->pubDate);
						$date = date("F j, Y", $timestamp);
						$dateFormatted = $date;
					 	echo "
					 	<section class='episode' id='episode--$key'>
					 		<header class='episode__header'>
					 			<div class='episode__header__date'>$dateFormatted</div>
								<h2 class='episode__header__title'>$title</h2>
								
					 		</header>
					 		<audio class='episode__player' controls>
							  <source src='$file' type='audio/mpeg'>
							  Your browser does not support the audio tag.
							</audio>
							<p class='episode__description'>$description</p>
						</section>
					 	";
					}

				?>


				<footer>
					<address style="text-align: center; font-size: .7em;">
						Copyright The Flavor Boys 2016
					</address>
				</footer>
			</article>
		</main>
	</body>

	<script>

		function toggleDialog(dialogID) {
			var dialog = document.getElementById(dialogID);
			if (dialog.open) {
				dialog.open = false;
			} else {
				dialog.open = true;
			}
		}

		function expandContent(expander) {
			var content = expander.nextSibling;
			if ( !isVisible(content) ) {
				content.style.display = "block";
			} 
			expander.style.display = "none";
		}
		function isVisible(element) {
			if (element.offsetWidth > 0 && element.offsetHeight > 0) return true;
			return false;
		}
	</script>
	
</html>