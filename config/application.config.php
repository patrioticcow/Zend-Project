<?php
return array(
    'modules' => array(
        'ZfcBase',
        'ZfcUser',
        'BjyAuthorize',
        'RoleUserBridge',
    	'ZendDeveloperTools',
    	'BjyProfiler',
    	'ZFTool',
        'Application',
        'Admin',
    	'Album',
        'Form',
        
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
