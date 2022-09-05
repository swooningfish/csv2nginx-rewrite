<?php

/*
	This one reads redirects from CSV list
	and echoes nginx server location rules
	
	Suggest to put in a seperate include file such in the server section of the config file
	
	server {
		...
		 include /etc/nginx/redirects_custom.conf;
		...
	}
	
	# Example nginx rules would look like the following
	
	# location = /content/unique-page-name {
	#    return 301 http://sitedomain.co.uk/new-name/unique-page-name;
	# }

	#
	# location /product {
	#     rewrite ^/product(.*)$ https://www.newsite.com/$1 redirect;
	# }


*/

$i = 0;

$startingUrl = 'https://www.example.com';
$dataFile = 'data/input.csv';

echo 'Reading csv<br />';
if (file_exists($dataFile)) {

	$file = @fopen($dataFile, "r") ;
	// while there is another line to read in the file
	echo '<pre>';
	while (!feof($file)){
		// Get the current line that the file is reading
		$currentLine = fgets($file) ;
		$currentLine = explode(',',$currentLine) ;

		$req = substr($currentLine[0], strlen($startingUrl));
		echo 'location '.str_replace('.', '\.', $req).' {<br/>';
		echo '    return 301 '.trim($currentLine[1]).';<br/>';
		//echo '    rewrite ^'.str_replace('.', '\.', $req).'(.*)$ '.trim($currentLine[1]).' redirect;<br/>';
		echo '}<br/><br/>';
		
		$i++;
	}
	echo '</pre>';

	fclose($file) ;

}

echo $i.' lines read';

?>