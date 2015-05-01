<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Widget_Themico_Base extends WP_Widget
{
    protected $_frontend_scripts = false;
    protected $_frontend_styles = false;
    protected $_admin_scripts = false;
    protected $_admin_styles = false;

    public function frontendScripts()
    {
    }

    public function frontendStyles()
    {
    }

    public function adminScripts()
    {
    }

    public function adminStyles()
    {
    }

    protected function getWidgetClass()
    {
        return $this->widget_options['classname'];
    }

    protected function toUnixPath($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function __construct($id_base = false, $name, $widget_options = array(), $control_options = array())
    {

        if (is_admin()) {

            if ($this->_admin_scripts) {
                add_action('admin_enqueue_scripts', array($this, 'adminScripts'));
            }

            if ($this->_admin_styles) {
                add_action('admin_enqueue_scripts', array($this, 'adminStyles'));
            }

        } else {

            if ($this->_frontend_scripts) {
                add_action('wp_enqueue_scripts', array($this, 'frontendScripts'));
            }

            if ($this->frontendStyles()) {
                add_action('wp_enqueue_scripts', array($this, 'frontendStyles'));
            }

        }

        parent::__construct($id_base, $name, $widget_options, $control_options);
    }

    public function getFullUrlByRel($rel = '')
    {
        $url = '';

        $reflection = new ReflectionClass($this);
        $widget_rel_path = str_replace(str_replace('\\', '/', get_template_directory()), '', str_replace('\\', '/', dirname($reflection->getFileName())));
        $template_directory_uri = get_template_directory_uri();

        if ($rel) {
            $url = $template_directory_uri . path_join($widget_rel_path , $rel);
        } else {
            $url = $template_directory_uri .  $widget_rel_path;
        }

        return untrailingslashit($url);
    }



}

?>
