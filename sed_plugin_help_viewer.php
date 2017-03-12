<?php

$plugin['version'] = '0.4.1';
$plugin['author'] = 'Netcarver';
$plugin['author_uri'] = 'http://txp-plugins.netcarving.com';
$plugin['description'] = 'Quickly check your plugin\'s help section from the plugin cache dirctory.';
$plugin['type'] = 1;
$plugin['allow_html_help'] = 1;

if (! defined('txpinterface')) {
    global $compiler_cfg;
    @include_once('config.php');
    @include_once($compiler_cfg['path']);
}

# --- BEGIN PLUGIN CODE ---
if(@txpinterface == 'admin') {
    add_privs('sed_plugin_help_viewer','1,2');
    register_tab('extensions', 'sed_plugin_help_viewer', 'Help Viewer');
    register_callback('sed_plugin_help_viewer', 'sed_plugin_help_viewer');
}

function sed_plugin_help_viewer ($event, $step) 
{
    if (!$step or !in_array($step , array('view_help'))) {
        _sed_list_plugins_from_cache();
    } else {
        $step();
    }
}

function view_help($message='') 
{
    pagetop(gTxt('edit_plugins'), $message);

    $filename = gps('filename');
    $plugin = array();

    if (!empty($filename)) {
        $content = file($filename);
        $source_lines = count($content);
        $format = 'none';

        for ($i=0; $i < $source_lines; $i++) {
            $content[$i] = rtrim($content[$i]);
        }

        $format = 'unknown';

        //  Check for ZEM plugin...
        $plugin['help'] = _zem_extract_section($content, 'HELP');
        if ( '' != $plugin['help']) {
            $format = 'zem_help';
        } else {
            //  check for ied style help section...
            $plugin['help'] = _ied_extract_section($content, 'HELP');
            if ('' != $plugin['help']) {
                $format = 'ied_help';
            }
        }

        echo startTable('edit');
        
        switch( $format ) {
            case 'zem_help':
                echo tr(tda( '<p>Plugin is in zem template format.</p>', ' width="600"'));
                if (!isset($plugin['allow_html_help']) or (0 === $plugin['allow_html_help'])) {
                    #   Textile...
                    $plugin['css']  = _zem_extract_section($content, 'CSS');
                    include_once txpath.'/lib/classTextile.php';
                    if (class_exists('Textile')) {
                        $textile = new Textile();
                        $plugin['help'] = $plugin['css'].n.$textile->TextileThis($plugin['help']);
                        echo tr(tda( '<p>Extracted and Textile processed help section follows&#8230;</p><hr>', ' width="600"'));
                    } else {
                        echo tr(tda('<p>Extracted help section follows, <strong>Textile Processing Failed</strong>&#8230;</p><hr>', ' width="600"'));
                    }
                } else {
                    # (x)html...
                    $plugin['css']  = _zem_extract_section($content, 'CSS' );
                    $plugin['help'] = $plugin['css'].n.$plugin['help_raw'];
                }
                echo tr(tda($plugin['help'], ' width="600"'));
                break;
            case 'ied_help':
                echo tr(tda('<p>Plugin is in ied template format.</p>', ' width="600"'));
                echo tr(tda('<p>Extracted raw help section follows&#8230;</p><hr>', ' width="600"'));
                echo tr(tda($plugin['help'], ' width="600"'));
                break;
            default:        
                echo tr(tda( '<p><strong>Unknown plugin file format or empty help section.</strong></p><hr>', ' width="600"'));
                break;
        }

        echo endTable();
        
    } else {
        echo 'Help not accessible from that file.';
    }
}

function _zem_extract_section($lines, $section) 
{
    $start_delim = "# --- BEGIN PLUGIN $section ---";
    $end_delim = "# --- END PLUGIN $section ---";

    $start = array_search($start_delim, $lines);
    if (false === $start) {
        return '';
    } else {
        $start += 1;
    }
    
    $end = array_search($end_delim, $lines);
    $content = array_slice($lines, $start, $end-$start);

    return join(n, $content);
}

function _ied_extract_section($lines, $section) 
{
    #$meta_delim = '--- PLUGIN METADATA ---';
    $help_delim = '--- BEGIN PLUGIN HELP ---';
    $end_delim  = '--- END PLUGIN HELP & METADATA ---';

    #$code_start = 1;
    $help_line = array_search($help_delim, $lines);
    if (false === $help_line) {
        return '';  // This is not an ied file.
    }
    $help_line += 1;
    $end_line = array_search($end_delim, $lines);
    $content = array_slice($lines, $help_line, $end_line-$help_line);

    return join(n, $content);
}

function _sed_list_plugins_from_cache($message='') 
{
    pagetop(gTxt('edit_plugins'),$message);
    echo startTable('list');

    $filenames = array();

    if (!empty($GLOBALS['prefs']['plugin_cache_dir'])) {
        $dir = dir($GLOBALS['prefs']['plugin_cache_dir']);
        while ($file = $dir->read()) {
            if($file != '.' && $file != '..') {
                $fileaddr = $GLOBALS['prefs']['plugin_cache_dir'].DS.$file;

                if (!is_dir($fileaddr)) {
                    $filenames[]=$fileaddr;
                }
            }
        }
        $dir->close();
        ($filenames and (count($filenames) > 0) ) ? natcasesort($filenames) : '';
    }

    echo tr(
    tda(
    tag('Plugins found in the plugin cache directory: '.$GLOBALS['prefs']['plugin_cache_dir'],'h1')
    ,' colspan="1" style="border:0;height:50px;text-align:left"')
    );

    echo assHead('plugin');

    if (count( $filenames ) > 0) {
        foreach($filenames as $filename) {
            $fileext= ltrim(strrchr($filename, '.'), '.');
            if ($fileext==='php') {
                $elink = '<a href="?event=sed_plugin_help_viewer&#38;step=view_help&#38;filename='.$filename.'">'.(isset($plugin['name']) ? $plugin['name'] : $filename).'</a>';
                echo tr(td(strong($elink)));
            }
        }
    }

    echo endTable();
}

# --- END PLUGIN CODE ---

?>
