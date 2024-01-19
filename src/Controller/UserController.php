<?php
namespace User\Controller;

use Components\Controller\AbstractBaseController;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Predicate\Like;
use Laminas\View\Model\ViewModel;
use User\Form\UserChangePasswordForm;

class UserController extends AbstractBaseController
{
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
        $select->where(['users.STATUS' => $this->model::ACTIVE_STATUS]);
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
                } else {
                    $this->flashmessenger()->addErrorMessage('Unable to change password');
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
}