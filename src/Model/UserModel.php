<?php 
namespace User\Model;

use Components\Model\AbstractBaseModel;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Validator\Identical;
use User\Form\UserRolesForm;
use Exception;


class UserModel extends AbstractBaseModel
{
    public $USERNAME;
    public $FNAME;
    public $LNAME;
    public $ADDR1;
    public $ADDR2;
    public $CITY;
    public $STATE;
    public $ZIP;
    public $PHONE;
    public $EMAIL;
    public $PASSWORD;
    
    public function __construct($adapter = NULL)
    {
        parent::__construct($adapter);
        $this->setTableName('users');
    }
    
    public function create()
    {
        $bcrypt = new Bcrypt();
        $this->PASSWORD = $bcrypt->create($this->PASSWORD);
        
        parent::create();
    }
    
    public function getInputFilter()
    {
        $this->inputFilter = parent::getInputFilter();
        
        $this->inputFilter->add([
            'name' => 'CONFIRM_PASSWORD',
            'validators' => [
                [
                    'name' => Identical::class,
                    'options' => [
                        'token' => 'PASSWORD',
                        'messages' => [
                            Identical::NOT_SAME => 'Passwords do not match.',
                            Identical::MISSING_TOKEN => 'Password or Confirmation missing.',
                        ],
                    ],
                ],
            ],
        ]);
        
        return $this->inputFilter;
    }
    
    public function changePassword($password)
    {
        $bcrypt = new Bcrypt();
        $this->PASSWORD = $bcrypt->create($password);
        $this->update();
        return $this;
    }
    
    public function assignRole($data)
    {
        $sql = new Sql($this->adapter);
        $columns = [
            'UUID',
            'USER',
            'ROLE',
        ];
        $values = [
            $data['UUID'],
            $data['USER'],
            $data['ROLE'],
        ];
        
        $insert = new Insert();
        $insert->into('user_roles');
        $insert->columns($columns);
        $insert->values($values);
        
        $statement = $sql->prepareStatementForSqlObject($insert);
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return $e;
        }
        return $this;
    }
    
    public function assignAction()
    {
        $uuid = $this->params()->fromRoute('uuid', 0);
        
        //-- Create User Model from Record --//
        $this->model->read(['UUID' => $uuid]);
        
        //-- Create UserRolesForm --//
        $form = new UserRolesForm();
        $form->setDbAdapter($this->adapter);
        $form->setUser(['UUID' => $uuid]);
        $form->init();
        $form->get('SUBMIT')->setAttribute('value', 'Add');
        
        
        
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            //-- Capture POST --//
            $form->setInputFilter($model->getInputFilter());
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
                $this->model->assignRole($form->getData('ROLE'));
                $this->redirect()->toRoute('user/default');
            }
        }
        
        //-- BEGIN: Retrieve currently assigned roles --//
        $sql = new Sql($this->adapter);
        
        $select = new Select();
        $select->from('user_roles');
        $select->where(['USER' => $uuid]);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        
        try {
            $resultSet = $statement->execute();
        } catch (Exception $e) {
            return $e;
        }
        
        $roles = [];
        foreach ($resultSet as $role) {
            $rolemodel = new RoleModel($this->adapter);
            $rolemodel->read(['UUID' => $role['ROLE']]);
            $roles[] = [
                'ROLENAME' => $rolemodel->ROLENAME,
                'ROLEUUID' => $rolemodel->UUID,
                'UUID' => $role['UUID'],
            ];
        }
        //-- END: Retrieve currently assigned roles --//
        
        
        return ([
            'form' => $form,
            'username' => $model->USERNAME,
            'user-uuid' => $model->UUID,
            'roles' => $roles,
        ]);
    }
    
    public function unassignRole($data)
    {
        $sql = new Sql($this->adapter);
        
        $delete = new Delete();
        $delete->from('user_roles')->where($data);
        $statement = $sql->prepareStatementForSqlObject($delete);
        
        try {
            $statement->execute();
        } catch (Exception $e) {
            return $e;
        }
        return true;
    }
}