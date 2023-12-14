<?php 
namespace User\Model;

use Components\Model\AbstractBaseModel;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Delete;
use Laminas\Db\Sql\Insert;
use Laminas\Db\Sql\Join;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\Validator\Identical;
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
        if (!$this->inputFilter) {
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
        }
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
    
    /**
     * Return array for role membership
     */
    public function memberOf(bool $asList = FALSE)
    {
        $role = new RoleModel($this->adapter);
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from('user_roles')->where(['USER' => $this->UUID]);
        $select->join($role->getTableName(), 'user_roles.ROLE = roles.UUID', ['ROLENAME'], Join::JOIN_INNER);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        try {
            $results = $statement->execute();
            $resultSet->initialize($results);
        } catch (Exception $e) {
            return FALSE;
        }
        
        $retval = $resultSet->toArray();
        
        if ($asList) {
            $list = [];
            foreach ($retval as $role) {
                $list[] = $role['ROLENAME'];
            }
            $retval = $list;
        }
        
        return $retval;
    }
}