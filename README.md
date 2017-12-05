# Advanced URL Field

*Advanced URL Field* is a Craft CMS plugin that adds a field type for entering a URL. It uses the standard plain text field, but provides validation to ensure that the value is a properly-formatted URL. The field can also be configured to only accept certain URL types such as absolute, relative, mailto, or tel.

This plugin can help prevent incorrect URLs such as `http://example.com/www.google.com/`, which happen when a user intends to enter an absolute URL, but omits the protocol, causing browser to interpret it as a relative URL.