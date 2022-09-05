<?php

/*
	This one reads redirects from CSV list
	and echoes nginx server location rules
	
	Suggest to put in a separate include file such in the server section of the config file
	
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
$redirectType = (key_exists('redirect_type',$_GET) && $_GET['redirect_type'] == '302') ? 'redirect' : 'permanent';
$redirectRegex = key_exists('use_regex',$_GET) && $_GET['use_regex'] == '1';
$startingUrl = key_exists('starting_url',$_GET) && $_GET['starting_url'] ? trim($_GET['starting_url']) : 'https://www.example.com';
$dataFile = 'data/input.csv';
echo <<<EOF

<form method="get" action="">
<label for="redirect_type">Redirect Type
<select name="redirect_type" id="redirect_type">
    <option value="301">Redirect (301) Permanent</option>
    <option value="302">Redirect (302) Temporary</option>
</select>
</label>
<br/>
<label for="use_regex">Use Regex<input type="checkbox" value="1" name="use_regex" id="use_regex"/></label>
<br/>
<label for="starting_url">Starting Url<input type="input" name="starting_url" id="starting_url" value="{$startingUrl}"></label>
<br/>
<input type="submit"/>
</form>
EOF;

echo 'Reading csv<br />';
if (file_exists($dataFile)) {

	$file = @fopen($dataFile, "r") ;
    $rewriteRules = '';
	// while there is another line to read in the file
	while (!feof($file)){
		// Get the current line that the file is reading
		$currentLine = fgets($file) ;
		$currentLine = explode(',',$currentLine) ;
		$req = substr($currentLine[0], strlen($startingUrl)-1);
        $rewriteRules .= 'location '.str_replace('.', '\.', $req).' {'.PHP_EOL; // need to check if the
        if ($redirectRegex) {
            $rewriteRules .= '    rewrite ^'.str_replace('.', '\.', $req).'(.*)$ '.trim($currentLine[1]).' '.$redirectType.';'.PHP_EOL;
        } else {
            $rewriteRules .= '    return 301 '.trim($currentLine[1]).';'.PHP_EOL;
        }
        $rewriteRules .= '}'.PHP_EOL.PHP_EOL;
		$i++;
	}
	fclose($file) ;
    echo '<textarea cols="250" rows="40">'.$rewriteRules.'</textarea><hr/>';

}

echo $i.' lines read';

?>