#!/bin/bash
{% for cronjob in root_cronjobs %}
{{ cronjob }}
{% endfor %}
