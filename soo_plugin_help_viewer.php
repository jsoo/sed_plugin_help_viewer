<?php

$plugin['version'] = '1.0.0-beta';
$plugin['author'] = 'Jeff Soo';
$plugin['author_uri'] = 'http://ipsedixit.net/txp/';
$plugin['description'] = 'Preview formatted help text from files in the plugin cache dirctory.';
$plugin['type'] = 1;
$plugin['allow_html_help'] = 1;

if (! defined('txpinterface')) {
    global $compiler_cfg;
    @include_once('config.php');
    include_once($compiler_cfg['path']);
}

# --- BEGIN PLUGIN CODE ---
if(@txpinterface == 'admin') {
    add_privs('soo_plugin_help_viewer','1,2');
    register_tab('extensions', 'soo_plugin_help_viewer', 'Help Viewer');
    register_callback('soo_plugin_help_viewer', 'soo_plugin_help_viewer');
}

define('PLUGIN_CACHE_DIR', rtrim($GLOBALS['prefs']['plugin_cache_dir'], DS).DS);
define('SOO_HELP_DIR', PLUGIN_CACHE_DIR.'readme'.DS);

function soo_plugin_help_viewer ($event, $step) 
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
    
    if (! $filename) {
        echo 'Help not accessible from that file.';
        return;
    }

    $content = file($filename);
    $formats = array(
        'zem_help' => 'ZEM Template',
        'ied_help' => 'IED Template',
        'textile'  => 'Textile',
        'markdown' => 'Markdown',
    );
    $format = 'unknown';

    for ($i = 0; $i < count($content); $i++) {
        $content[$i] = rtrim($content[$i]);
    }

    if ($plugin['help'] = _zem_extract_section($content, 'HELP')) {
        $format = 'zem_help';
    } elseif ($plugin['help'] = _ied_extract_section($content, 'HELP')) {
        $format = 'ied_help';
    } elseif ($syntax = _soo_plugin_list_syntax(ltrim(strrchr($filename, '.'), '.'))) {
        if ($plugin['help'] = soo_plugin_help_parse($content, $syntax)) {
            $format = $syntax;
        }
    }

    echo startTable('edit');
    $table_rows = array();
    if ($format != 'unknown') {
        $table_rows[] = tr(tda( '<p>Help text extracted from <strong>'.$formats[$format].'</strong> file.</p>', ' width="600"'));
    }
    switch( $format ) {
        case 'unknown':        
            $table_rows[] =  tr(tda( '<p><strong>Unknown format or empty help section.</strong></p><hr>', ' width="600"'));
            break;
        case 'zem_help':
            $plugin['css']  = _zem_extract_section($content, 'CSS');
            if (empty($plugin['allow_html_help'])) {
                include_once txpath.'/lib/classTextile.php';
                if (class_exists('Textile')) {
                    $textile = new Textile();
                    $plugin['help'] = $plugin['css'].n.$textile->TextileThis($plugin['help']);
                }
            } else {
                $plugin['help'] = $plugin['css'].n.$plugin['help_raw'];
            }
        default:
            $table_rows[] =  tr(tda($plugin['help'], ' width="600"'));
            break;
    }
    echo implode("\n", $table_rows);
    echo endTable();
        
}

function soo_plugin_help_parse($content, $syntax)
{    
    if (is_array($content)) {
        $content = trim(join("\n", $content));
    }
    
    if ($syntax === 'textile' && class_exists('Netcarver\\Textile\\Parser')) {
        $parser = new \Netcarver\Textile\Parser('html5');
        return $parser->TextileThis($content);
        
    } elseif ($syntax === 'markdown') {
        if (class_exists('\Textpattern\Loader')) {
            Txp::get('\Textpattern\Loader', txpath.'/vendors/parsedown')->register();
        }
        if (class_exists('Parsedown')) {
            $parser = new Parsedown();
            return $parser->text($content);
        }
    }
    
    return '';
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
    $exts = array_keys(_soo_plugin_list_syntax());
    
    pagetop(gTxt('edit_plugins'),$message);
    echo startTable('list');

    $files = array();

    if (is_dir(PLUGIN_CACHE_DIR)) {
        if (is_dir(SOO_HELP_DIR)) {
            $helpDirs = glob(SOO_HELP_DIR.'*', GLOB_ONLYDIR);
            foreach ($helpDirs as $d) {
                foreach (glob($d.DS.'README.{'.implode(',', $exts).'}', GLOB_BRACE) as $helpFile) {
                    $files[str_replace(SOO_HELP_DIR, '', $helpFile)] = $helpFile;
                }
            }
        }
        foreach (glob(PLUGIN_CACHE_DIR.'*.php') as $plugin) {
            $files[substr($plugin, strrpos($plugin, DS) + 1)] = $plugin;
        }
    }
   
    echo tr(
    tda(
    tag('README and plugin files found in the cache:','h1')
    ,' colspan="1" style="border:0;height:50px;text-align:left"')
    );

    echo assHead('plugin');

    if (count( $files ) > 0) {
        foreach($files as $n => $f) {
            $fileext = ltrim(strrchr($f, '.'), '.');
            if (in_array($fileext, $exts) || $fileext == 'php') {
                $elink = '<a href="?event=soo_plugin_help_viewer&#38;step=view_help&#38;filename='.$f.'">'.$n.'</a>';
                echo tr(td(strong($elink)));
            }
        }
    }

    echo endTable();
}

function _soo_plugin_list_syntax($ext = null)
{
    $types = array(
        'md' => 'markdown',
        'markdown' => 'markdown',
        'textile' => 'textile'
    );
    return $ext && isset($types[$ext]) ? $types[$ext] : $types;
}

# --- END PLUGIN CODE ---

?>
