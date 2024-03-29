<?php 
namespace User\Controller;

use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\UserLoginForm;
use Laminas\Log\LoggerAwareTrait;
use Laminas\Authentication\Result;

class AuthController extends AbstractActionController
{
    use AdapterAwareTrait;
    use LoggerAwareTrait;
    
    private $authentication_adapter;
    private $authentication_service;
    private $authentication_form;
    
    public function loginAction()
    {
        $view = new ViewModel();
        
        $request = $this->getRequest();
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referring_url = $_SERVER['HTTP_REFERER'];
        } else {
            $referring_url = $this->url()->fromRoute('home');
        }
        
        
        /**
         * @var UserLoginForm $form
         */
        $form = $this->getAuthentication_form();
        $form->get('REFERRING_URL')->setValue($referring_url);
        
        if ($request->isPost()) {
            /**
             * @ TODO: $form->bind(new UserModel()); Breaks when upgrading from zend-form 2.12.0 to 2.14.0
             */
            //             $form->bind(new UserModel());
            $form->setData($request->getPost());
            if (!$form->isValid()) {
                $this->flashMessenger()->addErrorMessage('Form Invalid.');
                $this->redirect()->toRoute('user/login');
            } else {
                $data = $form->getData();
                $this->setAuthentication_adapter($this->authentication_service->getAdapter());
                $this->authentication_adapter->setUsername($data['USERNAME']);
                $this->authentication_adapter->setPassword($data['PASSWORD']);
                $result = $this->authentication_adapter->authenticate();
                
                /**
                 * @var Result $result
                 */
                if ($result->isValid()) {
                    $storage = $this->authentication_service->getStorage();
                    $storage->write($data['USERNAME']);
                    
                    $this->flashMessenger()->addSuccessMessage($result->getMessages());
                    $this->logger->info(sprintf('Code:%s Identity:%s Message:%s', $result->getCode(), $result->getIdentity(), json_encode($result->getMessages())));
                    return $this->redirect()->toUrl($request->getPost('REFERRING_URL'));
                } else {
                    $this->flashMessenger()->addErrorMessage($result->getMessages());
                    $this->logger->info(sprintf('Code:%s Identity:%s Message:%s', $result->getCode(), $result->getIdentity(), json_encode($result->getMessages())));
                    $this->redirect()->toRoute('user', ['controller' => 'auth','action' => 'login']);
                }
            }
        }
        
        $view->setVariable('form', $form);
        return ($view);
    }
    
    public function logoutAction()
    {
        $this->authentication_service->clearIdentity();
        return $this->redirect()->toRoute('home');
    }
    
    public function getAuthentication_adapter()
    {
        return $this->authentication_adapter;
    }

    public function setAuthentication_adapter($authentication_adapter)
    {
        $this->authentication_adapter = $authentication_adapter;
    }

    public function getAuthentication_service()
    {
        return $this->authentication_service;
    }

    public function setAuthentication_service($authentication_service)
    {
        $this->authentication_service = $authentication_service;
    }
    
    public function getAuthentication_form()
    {
        return $this->authentication_form;
    }

    public function setAuthentication_form($authentication_form)
    {
        $this->authentication_form = $authentication_form;
    }


    
    
}