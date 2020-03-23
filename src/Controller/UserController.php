<?php
namespace User\Controller;

use Components\Controller\AbstractBaseController;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractBaseController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
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
        
        $view->setVariable('header', $header);
        $view->setVariable('data', $data);
        
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
        return $view;
    }
}