<?php 
namespace User\Model;

use Components\Model\AbstractBaseModel;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Validator\Identical;

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
}