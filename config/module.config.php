<?php 
use Laminas\Authentication\AuthenticationService;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;
use User\Authentication\AuthAdapter;
use User\Authentication\LdapAdapter;
use User\Authentication\Factory\AuthAdapterFactory;
use User\Authentication\Factory\LdapAdapterFactory;
use User\Form\UserForm;
use User\Form\UserRolesForm;
use User\Form\Factory\UserFormFactory;
use User\Form\Factory\UserRolesFormFactory;
use User\Service\Factory\AuthenticationServiceFactory;
use User\Service\Factory\UserModelAdapterFactory;
use User\Form\RoleForm;
use User\Form\Factory\RoleFormFactory;

return [
    'router' => [
        'routes' => [
            'role' => [
                'type' => Literal::class,
                'priority' => 1,
                'options' => [
                    'route' => '/role',
                    'defaults' => [
                        'action' => 'index',
                        'controller' => User\Controller\RoleController::class,
                    ],
                ],
                'may_terminate' => TRUE,
                'child_routes' => [
                    'default' => [
                        'type' => Segment::class,
                        'priority' => -100,
                        'options' => [
                            'route' => '/[:action[/:uuid]]',
                            'defaults' => [
                                'action' => 'index',
                                'controller' => User\Controller\RoleController::class,
                            ],
                        ],
                    ],
                ],
            ],
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
    'acl' => [
        'EVERYONE' => [
            'user/config' => ['create','clear','index'],
            'user/login' => ['login'],
            'user/logout' => ['logout'],
        ],
        'admin' => [
            'user/config' => [],
            'user/default' => [],
            'role/default' => [],
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            User\Controller\Plugin\CurrentUser::class => User\Controller\Plugin\Factory\CurrentUserFactory::class,
        ],
        'aliases' => [
            'currentUser' => User\Controller\Plugin\CurrentUser::class,
        ],
        
    ],
    'controllers' => [
        'factories' => [
            User\Controller\AuthController::class => User\Controller\Factory\AuthControllerFactory::class,
            User\Controller\RoleController::class => User\Controller\Factory\RoleControllerFactory::class,
            User\Controller\UserController::class => User\Controller\Factory\UserControllerFactory::class,
            User\Controller\UserConfigController::class => User\Controller\Factory\UserConfigControllerFactory::class,
        ],
    ],
    'form_elements' => [
        'factories' => [
            RoleForm::class => RoleFormFactory::class,
            UserForm::class => UserFormFactory::class,
            UserRolesForm::class => UserRolesFormFactory::class,
        ],
    ],
    'navigation' => [
        'default' => [
            'role' => [
                'label' => 'Role',
                'route' => 'role',
                'class' => 'dropdown',
                'order' => 80,
                'resource' => 'role/default',
                'privilege' => 'index',
                'pages' => [
                    [
                        'label' => 'Add New Role',
                        'route' => 'role/default',
                        'action' => 'create',
                    ],
                    [
                        'label' => 'List Roles',
                        'route' => 'role/default',
                    ],
                ],
            ],
            'user' => [
                'label' => 'User',
                'route' => 'user',
                'class' => 'dropdown',
                'order' => 90,
                'resource' => 'user/default',
                'privilege' => 'index',
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
                        'resource' => 'user/config',
                        'privilege' => 'index',
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
            'auth-service' => AuthenticationService::class,
            'auth-adapter' => LdapAdapter::class,
        ],
        'factories' => [
            'user-model-adapter' => UserModelAdapterFactory::class,
            AuthAdapter::class => AuthAdapterFactory::class,
            AuthenticationService::class => AuthenticationServiceFactory::class,
            LdapAdapter::class => LdapAdapterFactory::class,
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
            'user/update' => __DIR__ . '/../view/user/user/update.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];