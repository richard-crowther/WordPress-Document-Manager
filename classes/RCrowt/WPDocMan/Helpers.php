<?php
/**
 * Created by PhpStorm.
 * User: Richard
 * Date: 26/07/2015
 * Time: 11:13
 */

namespace RCrowt\WPDocMan;


class Helpers
{

    /**
     * Get the full path to any file in the plugin directory relative to the plugin directory.
     * @param $path string
     * @return string
     */
    public static function getPluginPath($any_plugin_file, $path)
    {
        return WP_PLUGIN_DIR . '/' . self::getPluginName($any_plugin_file) . '/' . ltrim($path, '/\\');
    }

    /**
     * Get the full URL to any file in the plugin directory relative to the plugin directory.
     * @param $path string
     * @return string
     */
    public static function getPluginUrl($any_plugin_file, $path = '')
    {
        return plugins_url(self::getPluginName($any_plugin_file)) . '/' . ltrim($path, '/\\');
    }

    /**
     * Get the current plugin name.
     * @return string
     */
    public static function getPluginName($any_plugin_file)
    {
        $basename = plugin_basename($any_plugin_file);
        return substr($basename, 0, strpos($basename . '/', '/'));
    }

}