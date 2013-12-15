<?php

class Twig extends \Twig_Extension
{
    public function getName()
    {
        return 'bakery';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('style', array($this, 'style')),
            new \Twig_SimpleFunction('url', array($this, 'url')),
            new \Twig_SimpleFunction('image', array($this, 'image')),
            new \Twig_SimpleFunction('less', array($this, 'less')),
            new \Twig_SimpleFunction('asset', array($this, 'asset')),
            new \Twig_SimpleFunction('javascript', array($this, 'javascript')),
        );
    }

    function url( $url, $args = NULL){
        echo \Bakery::$module['urlgenerator']->generate($url, $args);
    }


    /**
     * Provides the ability to get constants from instances as well as class/global constants.
     *
     * @param string      $constant The name of the constant
     * @param null|object $object   The object to get the constant from
     *
     * @return string
     */
    function style($stylename, $dir = NULL)
    {  

        return "/assets/css/$dir/$stylename";

    }

    function less($less, $dir = NULL)
    {

        return "/assets/less/$dir/$less";

    }

    /**
     * Provides the ability to get constants from instances as well as class/global constants.
     *
     * @param string      $constant The name of the constant
     * @param null|object $object   The object to get the constant from
     *
     * @return string
     */
    function image($image, $dir = NULL)
    {

        return "/assets/image/$dir/$image";

    }


    /**
     * Provides the ability to get constants from instances as well as class/global constants.
     *
     * @param string      $constant The name of the constant
     * @param null|object $object   The object to get the constant from
     *
     * @return string
     */
    function asset($asset, $file, $dir = NULL)
    {
     
        return "/assets/$asset/$dir/$file";
    }


    /**
     * Provides the ability to get constants from instances as well as class/global constants.
     *
     * @param string      $constant The name of the constant
     * @param null|object $object   The object to get the constant from
     *
     * @return string
     */
    function javascript($js, $dir = NULL)
    {
     
        return "/assets/js/$dir/$js";
    }



}