<?php

require(__DIR__ . "/../vendor/autoload.php");

$ymlParse = new \Symfony\Component\Yaml\Yaml();
$yml = $ymlParse->parse(__DIR__ . "/../app/config/parameters.yml");
$host = $yml['parameters']['beanstalkd_host'];
$port = $yml['parameters']['beanstalkd_port'];
$tube = $yml['parameters']['beanstalkd_tube'];
$timeout = 10;

$queue = new \Pheanstalk\Pheanstalk($host . ":" . $port, $timeout);

$console = realpath(__DIR__ . '/../app/console');
