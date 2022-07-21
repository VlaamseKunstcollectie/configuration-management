<?php

$allowedSources = [
{% for allowed_source in culturize.allowed_sources %}
  {{ allowed_source }}{{ "," if not loop.last else "" }}
{% endfor %}
];
$allowedDestinations = [
{% for allowed_destination in culturize.allowed_destinations %}
  {{ allowed_destination }}{{ "," if not loop.last else "" }}
{% endfor %}
];

$valid = true;

$handle = fopen('{{ culturize.github_file }}', 'r');
$errorHandle = null;
if ($handle) {
    while (($line = fgets($handle)) !== false) {

    	if($line !== '') {
			$thisValid = false;
			$message = '';
			foreach($allowedSources as $allowedSource) {
				foreach($allowedDestination as $allowedDestination) {
					if(preg_match('/^rewrite ' $allowedSource . '[a-zA-Z0-9\-_]+' . '$ https?://' . $allowedDestination . '/?[^ ]* redirect ;', $line)) {
						if (filter_var(strstr($line, 'http'), FILTER_VALIDATE_URL)) {
							$thisValid = true;
							break;
						}
					}
				}
				if($thisValid) {
					break;
				}
			}

			if(!$thisValid) {
				$valid = false;
		    	if($errorHandle === null) {
			    	$errorHandle = fopen('{{ culturize.dir }}/error.log', 'w');
			    }
		    	if($errorHandle) {
			    	fwrite($errorHandle, $line);
			    }
			}
		}
    }

    if($errorHandle) {
	    fclose($errorHandle);
	}
    if($handle) {
	    fclose($handle);
	}
}

if($valid) {
	copy('{{ culturize.dir }}/nginx_redirect.conf', '/etc/nginx/{{ vhost.name }}/nginx_redirect.conf');
}
