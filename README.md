# soo_plugin_help_viewer

## Overview

A plugin for [Textpattern](http://textpattern.com). Extended from the original [sed_plugin_help_viewer](https://github.com/netcarver/sed_plugin_help_viewer) by [netcarver](https://github.com/netcarver). Many thanks to netcarver for doing the heavy lifting.

Just as the Textpattern plugin cache speeds code development, **soo_plugin_help_viewer** speeds help-text editing. If you use the ZEM or IED plugin template, **soo_plugin_help_viewer** gives a formatted preview of the help section. That's how netcarver's original version worked; the purpose of **soo_plugin_help_viewer** is to extend that functionality to standalone Textile and Markdown README files.

* [Support forum topic](https://forum.textpattern.io/viewtopic.php?id=47690)
* [Author site](http://ipsedixit.net/txp/)
* [GitHub](https://github.com/jsoo/soo_plugin_help_viewer)

## Configuration

Install the plugin [in the usual manner](https://docs.textpattern.io/administration/plugins-panel). (It is an unobtrusive plugin and does not install any extras, neither in the database nor elsewhere.)

Enable the Textpattern plugin cache: in the [Textpattern preferences panel](https://docs.textpattern.io/administration/preferences-panel), under **Admin preferences**, enter the file path for the directory you want to use. (NB: do not use the Temporary directory for this.) For plugin help text in the old ZEM or IED template format, add your plugin source files to this directory: this is all you need to do to use the Help Viewer. 

NB: the main purpose of this cache is to allow you to load plugins without installing them, which is a great help for plugin development, but generally a bad idea in a production environment. The cache has precedence: if a plugin is installed and also in the cache, the cached version is the one that actually loads.

**soo_plugin_help_viewer** allows separating help text from the plugin code source file into its own README file, either in Textile or Markdown format. This allows you to use a single file as a standard README (such as you'll want to use on, e.g., GitHub, or if you otherwise distribute your plugin as uncompiled source files) and for the help text of your installed plugin. Of course if you use separate code and help files in this way, you'll need something other than the [default plugin template and compiler](https://github.com/textpattern/textpattern-plugin-template). The version at [https://github.com/jsoo/textpattern-plugin-template](https://github.com/jsoo/textpattern-plugin-template) is designed for this (make sure it is the `dev` branch).

### To enable the new features:

Add a directory named `readme` to your plugin cache directory. For each of your plugins, add a directory containing the README file to the `soo_plugin_help` directory. The file must be named `README.md`, `README.markdown`, or `README.textile`.

For Textpattern 4.6 and later, you are now ready to preview `README.textile` files. For Markdown parsing, you will have to install [parsedown](https://github.com/erusev/parsedown). Copy the parsedown directory (or a symbolic link pointing to it) to `textpattern/vendors`.

You may prefer to use symbolic links (i.e., aliases) from your plugin repos to the above directories.

## Usage

The Help Viewer is accessible under [Extensions](https://docs.textpattern.io/administration/extensions-region). (NB: you cannot access the Extensions region from the Plugins panel.) The Help Viewer landing page lists all `.php` files in the plugin cache and all `README.md` (or `.markdown` or `.textile`) files in directories within the `readme` directory. Click on an item in the list to view the formatted help text.

## Version History

### v1.0.0-beta (March 14th, 2017)

* Renamed to **soo_plugin_help_viewer**. 
  * (NB: help file cache directory renamed to `readme`). 
* Some UI improvements.

### v1.0.0-alpha (March 12th, 2017)

* **sed_plugin_help_viewer Mark II**, capable of previewing standalone Textile and Markdown README files.

### v0.4.1 (March 12th, 2017)

(Forked to https://github.com/jsoo/sed_plugin_help_viewer)

* Fixed "passed by reference" error notice when running in PHP strict mode.

### v0.4 (September 6th, 2008)

* Update to get rid of the tiny "help" click target and replace it with the entire filename.

### v0.3 (April 2nd, 2007)

* Updated to allow processing of TxP 4.0.4 plugins marked as @allow_html_help@.

### v0.2

* Fixed undefined variable error when cache directory is empty. (Thanks Rigel.)

### v0.1 (July 20th, 2006)

* Scanning the cache directory for plugins.
  * When clicked, pull out the help section, Textile it if needed, display it.

## Credits

Sections of this plugin use code from **Alex (a.k.a. Zem)** and **Yura (a.k.a Inspired)** with permission.
Many thanks for helping the community guys!

## License

Released under the GPLv2