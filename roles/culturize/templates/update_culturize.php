<?php

$allowedSources = [
{% for allowed_source in culturize.allowed_sources %}
  '{{ allowed_source | replace("/", "\/") | replace(".", "\.") }}'{{ "," if not loop.last else "" }}
{% endfor %}
];
$allowedDestinations = [
{% for allowed_destination in culturize.allowed_destinations %}
  '{{ allowed_destination | replace("/", "\/") | replace(".", "\.") }}'{{ "," if not loop.last else "" }}
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
				foreach($allowedDestinations as $allowedDestination) {
					if(preg_match('/^rewrite ' . $allowedSource . '[a-zA-Z0-9\-_]+\$ https?://' . $allowedDestination . '\/?[^ ]* redirect ;$/', $line)) {
						$substr = 'http' . strstr($line, '$ http');
						$substr = substr($substr, 0, -11);
						if (filter_var($substr, FILTER_VALIDATE_URL)) {
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
	copy('{{ culturize.dir }}/nginx_redirect.conf', '/etc/nginx/{{ culturize.nginx.name }}/nginx_redirect.conf');
}
