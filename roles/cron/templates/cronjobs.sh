#!/bin/bash
{% for cronjob in cronjobs %}
{{ cronjob }}
{% endfor %}
