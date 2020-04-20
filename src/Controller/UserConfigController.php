<?php 
namespace User\Controller;

use Components\Controller\AbstractConfigController;
use Laminas\Db\Adapter\AdapterAwareTrait;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Sql\Ddl\CreateTable;
use Laminas\Db\Sql\Ddl\DropTable;
use Laminas\Db\Sql\Ddl\Column\Datetime;
use Laminas\Db\Sql\Ddl\Column\Integer;
use Laminas\Db\Sql\Ddl\Column\Varchar;
use Laminas\Db\Sql\Ddl\Constraint\PrimaryKey;
use Laminas\View\Model\ViewModel;
use User\Model\UserModel;
use User\Model\RoleModel;
use Settings\Model\SettingsModel;

class UserConfigController extends AbstractConfigController
{
    use AdapterAwareTrait;
    
    public function __construct()
    {
        $this->setRoute('user/config');
    }
    
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        
        $view->setTemplate('base/config');
        
        return $view;
    }
    
    public function clearDatabase()
    {
        $sql = new Sql($this->adapter);
        $ddl = [];
        
        $ddl[] = new DropTable('users');
        $ddl[] = new DropTable('roles');
        $ddl[] = new DropTable('user_roles');
        
        foreach ($ddl as $obj) {
            $this->adapter->query($sql->buildSqlString($obj), $this->adapter::QUERY_MODE_EXECUTE);
        }
        
        $this->clearSettings('USER');
    }
    
    public function createDatabase()
    {
        $sql = new Sql($this->adapter);
        
        /******************************
         * USERS
         ******************************/
        $ddl = new CreateTable('users');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('USERNAME', 100));
        $ddl->addColumn(new Varchar('FNAME', 100, TRUE));
        $ddl->addColumn(new Varchar('LNAME', 100, TRUE));
        $ddl->addColumn(new Varchar('ADDR1', 100, TRUE));
        $ddl->addColumn(new Varchar('ADDR2', 100, TRUE));
        $ddl->addColumn(new Varchar('CITY', 100, TRUE));
        $ddl->addColumn(new Varchar('STATE', 2, TRUE));
        $ddl->addColumn(new Varchar('ZIP', 9, TRUE));
        $ddl->addColumn(new Varchar('PHONE', 10, TRUE));
        $ddl->addColumn(new Varchar('EMAIL', 64, TRUE));
        $ddl->addColumn(new Varchar('PASSWORD', 64, TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * ROLES
         ******************************/
        $ddl = new CreateTable('roles');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        $ddl->addColumn(new Integer('STATUS', TRUE));
        $ddl->addColumn(new Datetime('DATE_CREATED', TRUE));
        $ddl->addColumn(new Datetime('DATE_MODIFIED', TRUE));
        
        $ddl->addColumn(new Varchar('ROLENAME', 100, TRUE));
        $ddl->addColumn(new Varchar('PARENT', 36, TRUE));
        $ddl->addColumn(new Integer('PRIORITY', TRUE));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * USER_ROLES
         ******************************/
        $ddl = new CreateTable('user_roles');
        
        $ddl->addColumn(new Varchar('UUID', 36));
        
        $ddl->addColumn(new Varchar('USER', 36));
        $ddl->addColumn(new Varchar('ROLE', 36));
        
        $ddl->addConstraint(new PrimaryKey('UUID'));
        
        $this->adapter->query($sql->buildSqlString($ddl), $this->adapter::QUERY_MODE_EXECUTE);
        unset($ddl);
        
        /******************************
         * Create Default Roles
         ******************************/
        $role = new RoleModel($this->adapter);
        $role->UUID = $role->generate_uuid();
        $role->ROLENAME = 'EVERYONE';
        $role->create();
        
        $role->UUID = $role->generate_uuid();
        $role_admin = $role->UUID;
        $role->ROLENAME = 'admin';
        $role->create();
        
        /******************************
         * Create Default Users
         ******************************/
        $user = new UserModel($this->adapter);
        $user->FNAME = 'Administrator';
        $user->USERNAME = 'Admin';
        $user->PASSWORD = 'admin';
        $user->STATUS = $user::ACTIVE_STATUS;
        $user->create();
        
        $user->assignRole([
            'UUID' => $user->generate_uuid(),
            'USER' => $user->UUID,
            'ROLE' => $role_admin,
        ]);
        
        $user = new UserModel($this->adapter);
        $user->UUID = 'SYSTEM';
        $user->FNAME = 'SYSTEM';
        $user->USERNAME = 'SYSTEM';
        $user->PASSWORD = '5Y5T3M';
        $user->STATUS = $user::ACTIVE_STATUS;
        $user->create();
        
        $this->flashMessenger()->addSuccessMessage('Admin users created.');
        
        $this->createSettings('USER');
        $this->flashMessenger()->addSuccessMessage('User Settings created.');
    }
    
    public function createSettings($module)
    {
        parent::createSettings($module);
        $setting = new SettingsModel($this->adapter);
        $setting->MODULE = $module;
        
        $settings = [
            'LDAP_DOMAIN',
            'LDAP_SERVER',
            'LDAP_BASE_DN',
        ];
        
        foreach ($settings as $rec) {
            $setting->UUID = $setting->generate_uuid();
            $setting->SETTING = $rec;
            $setting->create();
        }
    }
}