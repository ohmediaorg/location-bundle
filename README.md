# Installation

Update `composer.json` by adding this to the `repositories` array:

```json
{
    "type": "vcs",
    "url": "https://github.com/ohmediaorg/contact-bundle"
}
```

Then run `composer require ohmediaorg/contact-bundle:dev-main`.

Import the routes in `config/routes.yaml`:

```yaml
oh_media_contact:
    resource: '@OHMediaContactBundle/config/routes.yaml'
```

Run `php bin/console make:migration` then run the subsequent migration.

# Locations

Information for multiple Locations can be managed, include name, contact info,
and hours.

A Location can be marked as primary, making it available via the `location_primary()`
Twig function.

All Locations can be retrieved using the `locations()` Twig function.

## Schema

All Locations schema can be output via `{{ locations_schema() }}`.

A single Location's schema can be output via `{{ location_schema(location) }}`.

A particular Location's schema will not be output more than once.

## Location Data

The Location has the following properties available in the template:

```twig
{{ location.name }}
{{ location.address }}
{{ location.city }}
{{ location.province }} {# 2 letter code if Canada or US #}
{{ location.country }} {# 3 letter code #}
{{ location.postalCode }}
{{ location.email }}
{{ location.phone }}
{{ location.fax }}
{{ location.primary }} {# true|false indicating if this location is primary #}
```

The only values that can be blank are `email`, `phone`, and `fax`.

### Displaying Hours

```twig
{% for day, hours in location.hoursFormatted %}
<p><b>{{ day }}:</b> {{ hours }}</p>
{% endfor %}
```

### Displaying Today's Hours

If a client wants to display the current hours for the main location in the
website header, that would look something like this:

```twig
{% set primary_location = location_primary() %}

{% if primary_location %}
  {% set hours_formatted = primary_location.hoursFormatted %}
  {% set today = "now"|datetime('l') %}
  <p><b>Today's hours:</b> {{ hours_formatted[today] }}</p>
{% endif %}
```
