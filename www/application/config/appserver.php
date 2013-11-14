<?php

/**
 * 
 */
$config['http-version-header'] = 'X-app-version';
$config['require-app-version'] = 1;
$config['latest-app-version'] = 1;

/**
 * 
 */
$config['languages']['default'] = 'en';
$config['languages']['en'] = 'english';

/**
 * 
 */
$config['auth-library'] = array(
    'library'         => 'flexi_auth_lite',
    'params'        => FALSE,
    'object_name'   => 'flexi_auth'
);

/**
 * 
 */
$config['contacts']['notify']['subject'] = 'Admin Notification';
$config['contacts']['notify']['email']   = 'your@domain-dot-com';
$config['contacts']['sender']['name']    = 'My App Server';
$config['contacts']['sender']['email']   = 'your@domain-dot-com';
