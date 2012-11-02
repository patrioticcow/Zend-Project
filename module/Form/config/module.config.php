<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Form\Controller\Index' => 'Form\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'form' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/form',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Form\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                	'create' => array(
                		'type' => 'Literal',
                		'options' => array(
                			'route' => '/create',
                			'defaults' => array(
                				'controller' => 'Index',
                				'action'     => 'create',
                			),
                		),
                	),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Form' => __DIR__ . '/../view',
        ),
    ),
);
