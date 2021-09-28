#!/bin/bash
{% for command in cronjobs %}
{{ command }}
{% endfor %}
