<?php 
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;
use User\Authentication\AuthAdapter;
use User\Authentication\Factory\AuthAdapterFactory;
use User\Form\UserForm;
use User\Form\Factory\UserFormFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use User\Service\Factory\UserModelAdapterFactory;

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
                    'login' => [
                        'type' => Literal::class,
                        'priority' => 10,
                        'options' => [
                            'route' => '/login',
                            'defaults' => [
                                'controller' => User\Controller\AuthController::class,
                                'action' => 'login',
                            ],
                        ],
                    ],
                    'logout' => [
                        'type' => Literal::class,
                        'priority' => 10,
                        'options' => [
                            'route' => '/logout',
                            'defaults' => [
                                'controller' => User\Controller\AuthController::class,
                                'action' => 'logout',
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
            User\Controller\AuthController::class => User\Controller\Factory\AuthControllerFactory::class,
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
        'user' => [
            [
                'label' => 'Welcome',
                'route' => 'user',
                'pages' => [
                    [
                        'label' => 'Logout',
                        'route' => 'user/logout',
                        'controller' => 'auth',
                        'action' => 'logout',
                    ],
                    [
                        'label' => 'Change Password',
                        'route' => 'user/default',
                        'controller' => 'user',
                        'action' => 'changepw',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'user-model-adapter-config' => 'model-adapter-config',
            AuthenticationService::class => 'auth-service',
        ],
        'factories' => [
            'user-model-adapter' => UserModelAdapterFactory::class,
            AuthAdapter::class => AuthAdapterFactory::class,
            'auth-service' => AuthenticationServiceFactory::class,
        ],
    ],
    'session_config' => [
        'cookie_lifetime' => 3600,
        'gc_maxlifetime'     => 2592000,
    ],
    'session_manager' => [
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'view_helpers' => [
        'factories' => [
            User\View\Helper\CurrentUser::class => User\View\Helper\Factory\CurrentUserFactory::class,
        ],
        'aliases' => [
            'currentUser' => User\View\Helper\CurrentUser::class,
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