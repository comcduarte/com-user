<?php
namespace User\Controller;

use Components\Controller\AbstractBaseController;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\Log\LoggerAwareTrait;
use Laminas\Mail\Protocol\Smtp as SmtpProtocol;
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mime\Mime;
use Laminas\Validator\Db\NoRecordExists;
use Laminas\View\Model\ViewModel;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\AggregateResolver;
use Settings\Model\SettingsModel;
use User\Form\UserChangePasswordForm;
use User\Form\UserRegisterForm;
use User\Model\UserModel;
use User\Model\RoleModel;
use Laminas\InputFilter\InputFilter;

class UserController extends AbstractBaseController
{
    use LoggerAwareTrait;
    
    public $user_roles_form;
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        $view->setTemplate('base/subtable');
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from('users');
        $select->columns([
            'UUID' => 'UUID',
            'Username' => 'USERNAME',
            'First Name' => 'FNAME',
            'Last Name' => 'LNAME',
        ]);
        $select->order(['USERNAME']);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $data = $resultSet->toArray();
        
        $header = [];
        if (!empty($data)) {
            $header = array_keys($data[0]);
        }
        
        $params = [
            [
                'route' => 'user/default',
                'action' => 'update',
                'key' => 'UUID',
                'label' => 'Update',
            ],
            [
                'route' => 'user/default',
                'action' => 'delete',
                'key' => 'UUID',
                'label' => 'Delete',
            ],
        ];
        
        $view->setvariables ([
            'data' => $data,
            'header' => $header,
            'primary_key' => $this->model->getPrimaryKey(),
            'params' => $params,
            'search' => true,
            'title' => 'Users',
        ]);
        
        return $view;
    }
    
    public function createAction()
    {
        $view = new ViewModel();
        
        $bcrypt = new Bcrypt();
        $this->model->PASSWORD = $bcrypt->create($this->model->PASSWORD);
        
        /**
         * 
         * @var InputFilter $inputfilter
         */
        $inputfilter = $this->model->getInputFilter();
        $inputfilter->add([
            'name' => 'USERNAME',
            'validators' => [
                [
                    'name' => NoRecordExists::class,
                    'options' => [
                        'table' => UserModel::getTableName(),
                        'field' => 'USERNAME',
                        'adapter' => $this->adapter,
                    ],
                ],
            ],
        ]);
        $this->model->setInputFilter($inputfilter);
        
        $view = parent::createAction();
        
        return $view;
    }
    
    public function updateAction()
    {
        $view = new ViewModel();
        $this->form->remove('PASSWORD');
        $this->form->remove('CONFIRM_PASSWORD');
        
        $view = parent::updateAction();
        
        $view->setTemplate('user/update');
        
        /****************************************
         * SUBTABLE
         ****************************************/
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->columns(['UUID']) 
        ->from('user_roles') 
        ->join('roles', 'user_roles.ROLE = roles.UUID', ['UUID_ROLE' => 'UUID', 'Role' => 'ROLENAME'], Join::JOIN_INNER)
        ->where([new Like('USER', $this->model->UUID)]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        $results = $statement->execute();
        $resultSet = new ResultSet($results);
        $resultSet->initialize($results);
        $roles = $resultSet->toArray();
        
        
        $view->setVariable('user_roles_form', $this->user_roles_form);
        $view->setVariable('uuid', $this->model->UUID);
        $view->setVariable('roles', $roles);
        /****************************************
         * END SUBTABLE
         ****************************************/
        
        
        return $view;
    }
    
    public function changepwAction()
    {
        $uuid = $this->params()->fromRoute('uuid', 0);
        if (!$uuid) {
            $this->flashmessenger()->addErrorMessage('Did not pass identifier.');
            return $this->redirect()->toRoute('user/default');
        }
        
        $this->model->read(['UUID' => $uuid]);
        
        $form = new UserChangePasswordForm();
        $form->init();
        $form->setInputFilter($this->model->getInputFilter());
        
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($this->model->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $result = $this->model->changePassword($data['PASSWORD']);
                
                if ($result) {
                    $this->flashmessenger()->addSuccessMessage('Password Change Successful');
                    $this->logger->info(sprintf('Code:%s Identity:%s Message:%s', '0', $this->model->USERNAME, 'Password Change Successful'));
                } else {
                    $this->flashmessenger()->addErrorMessage('Unable to change password');
                    $this->logger->info(sprintf('Code:%s Identity:%s Message:%s', '-1', $this->model->USERNAME, 'Unable to change password'));
                }
                
                return $this->redirect()->toRoute('user/default');
            }
            
        }
        
        return ([
            'form' => $form,
            'uuid' => $uuid,
        ]);
        
    }
    
    public function assignAction()
    {
        $form = $this->user_roles_form;
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $this->model->assignRole($form->getData());
            } else {
                $this->flashmessenger()->addErrorMessage('Form is Invalid');
            }
        }
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }
    
    public function unassignAction()
    {
        $uuid = $this->params()->fromRoute('uuid', 0);
        if (!$uuid) {
            return $this->redirect()->toRoute('user/default');
        }
        
        $this->model->unassignRole(['UUID' => $uuid]);
        
        $url = $this->getRequest()->getHeader('Referer')->getUri();
        return $this->redirect()->toUrl($url);
    }

    public function registerAction()
    {
        /**
         * 
         * @var \Laminas\View\Model\ViewModel $view
         * @var UserModel $model;
         */
        $view = new ViewModel();
        
        $model = $this->model;
        $view->setTemplate('base/create');
        
        $route = $this->getEvent()->getRouteMatch()->getMatchedRouteName();
        $params = array_merge(
            $this->getEvent()->getRouteMatch()->getParams(),
            ['action' => 'confirm']
            );
        
        
        $form = new UserRegisterForm();
        $form->init();
        
        $form->bind($model);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
                );
            
            $form->setData($post);
            
            /**
             * @var UserModel $model
             */
            if ($form->isValid()) {
                $model->STATUS = UserModel::INACTIVE_STATUS;
                $model->create();
                
                $role = new RoleModel($this->adapter);
                $role->read(['ROLENAME' => 'EVERYONE']);
                
                /**
                 * Add to EVERYONE Group
                 * @var array $data
                 */
                $data = [
                    'UUID' => $model->generate_uuid(),
                    'USER' => $model->UUID,
                    'ROLE' => $role->UUID
                ];
                $model->assignRole($data);
                unset($role);
                
                $this->sendConfirmationEmail($model->EMAIL, $model->UUID);
                $this->logger->info(sprintf('User Registered [%s]',$model->USERNAME));
                return $this->redirect()->toRoute($route, $params);
            } else {
                $this->logger->err('Form is invalid', $form->getMessages());
            }
        }
        
        $view->setVariables([
            'form' => $form,
            'title' => 'Add New Record',
        ]);
        
        return $view;
    }

    public function confirmAction()
    {
        $view = new ViewModel();
        if (! $this->params()->fromRoute('uuid', 0)) {
            $view->setTemplate('user/user/confirm');
            return $view;
        } else {
            $uuid = $this->params()->fromRoute('uuid', 0);
            
            /**
             * 
             * @var UserModel $model
             */
            $model = $this->model;
            $model->read(['UUID' => $uuid]);
            
            $model->STATUS = UserModel::REGISTERED_STATUS;
            $model->update();
            
            return $this->redirect()->toRoute('user/login');
        }
    }

    private function sendConfirmationEmail(string $to, $uuid)
    {
        /****************************************
         * Confirmation Email
         ****************************************/
        $view = new PhpRenderer();
        
        $settings = new SettingsModel($this->adapter);
        
        $resolver = new AggregateResolver();
        $view->setResolver($resolver);
        
        $map = new \Laminas\View\Resolver\TemplateMapResolver([
            'notifications/registration-confirmation' => __DIR__ . '/../../view/user/notifications/registration-confirmation.phtml',
        ]);
        $resolver->attach($map);
        
        $link = $_SERVER['HTTP_ORIGIN'] . $this->url()->fromRoute('user/default', ['action' => 'confirm', 'uuid' => $uuid]);
        
        $viewModel = new ViewModel();
        $viewModel->setTemplate('notifications/registration-confirmation')->setVariable('link', $link);
        $view->viewModel()->setRoot($viewModel);
        
        $message = new \Laminas\Mail\Message();
        $body = new \Laminas\Mime\Message();
        
        $html = $view->render($viewModel);
        $part = new \Laminas\Mime\Part($html);
        $part->type = Mime::TYPE_HTML;
        
        $settings->read(['MODULE' => 'USER', 'SETTING' => 'FROM']);
        $message->setFrom($settings->VALUE);
        $message->setTo($to);
        $message->setSubject('Registration Confirmation');
        
        $body->addPart($part);
        
        $message->setBody($body);
        
        try {
            $settings->read(['MODULE' => 'USER', 'SETTING' => 'SERVER']);
            $protocol = new SmtpProtocol($settings->VALUE);
            $protocol->connect();
            $settings->read(['MODULE' => 'USER', 'SETTING' => 'HELO']);
            $protocol->helo($settings->VALUE);
            
            $transport = new SmtpTransport();
            $transport->setConnection($protocol);
            $protocol->rset();
            $transport->send($message);
        } catch (\Exception $e) {
            /**
             * @var \Laminas\Log\Logger $logger
             */
            $logger = $this->logger;
            $logger->err($e->getMessage());
            $logger->info("Error sending email:" . $to);
        }
        
        $protocol->disconnect();
    }
}