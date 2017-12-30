<?php

namespace Rbs\Bundle\CoreBundle\Twig\Extension;

class JsonDecodeExtension extends \Twig_Extension
{
    protected $container;

    public function getName()
    {
        return 'twig_decode';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('json_decode', array($this, 'jsonDecode')),
        );
    }

    public function jsonDecode($str) {
        return json_decode($str, true);
    }
}
