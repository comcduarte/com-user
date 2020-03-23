<?php 

use User\Form\Factory\UserFormFactory;

return [
    'router' => [
        'routes' => [
            'user' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/user',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => User\Controller\UserController::class,
                    ],
                ],
                'may_terminate' => TRUE,
                'child_routes' => [
                    'config' => [
                        'type' => Segment::class,
                        'priority' => 100,
                        'options' => [
                            'route' => '/config[/:action]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => User\Controller\UserConfigController::class,
                            ],
                        ],
                    ],
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => User\Controller\UserController::class,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            User\Controller\UserController::class => User\Controller\Factory\UserControllerFactory::class,
            User\Controller\UserConfigController::class => User\Controller\Factory\UserConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            UserForm::class => UserFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            'user' => [
                'label' => 'User',
                'route' => 'user',
                'class' => 'dropdown',
                'pages' => [
                    [
                        'label' => 'Add New User',
                        'route' => 'user/default',
                        'action' => 'create',
                    ],
                    [
                        'label' => 'List Users',
                        'route' => 'user/default',
                    ],
                ],
            ],
            'settings' => [
                'label' => 'Settings',
                'route' => 'home',
                'class' => 'dropdown',
                'order' => 100,
                'pages' => [
                    'user' => [
                        'label'  => 'User Settings',
                        'route'  => 'user/config',
                        'action' => 'index',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'user-model-adapter-config' => 'model-adapter-config',
        ],
        'factories' => [
            'user-model-adapter' => User\Service\Factory\UserModelAdapterFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'user/config' => __DIR__ . '/../view/user/config/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];