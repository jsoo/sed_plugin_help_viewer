# sed_plugin_help_viewer

Plugin Help Section Viewer

A plugin for Textpattern [http://textpattern.com](http://textpattern.com)

Allows you to view the help section of any plugin in your cache directory.

If the file matches ZEM's template then the help section will get run through the textile formatter before display, otherwise it will be treated as straight HTML.

## Version History

### v0.4.1 (March 12th, 2017)

(Forked to https://github.com/jsoo/sed_plugin_help_viewer)

* Fixed "passed by reference" error notice when running in PHP strict mode

### v0.4 (September 6th, 2008)

* Update to get rid of the tiny "help" click target and replace it with the entire filename.

### v0.3 (April 2nd, 2007)

* Updated to allow processing of TxP 4.0.4 plugins marked as @allow_html_help@.

### v0.2

* Fixed undefined variable error when cache directory is empty. (Thanks Rigel.)

### v0.1 (July 20th, 2006)

Implements the following features&#8230;

* Scanning the cache directory for plugins.
  * When clicked, pull out the help section, Textile it if needed, display it.

## Credits

Sections of this plugin use code from **Alex (a.k.a. Zem)** and **Yura (a.k.a Inspired)** with permission.
Many thanks for helping the community guys!

## License

Released under the GPLv2