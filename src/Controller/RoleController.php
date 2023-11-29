<?php 
namespace User\Controller;

use Components\Controller\AbstractBaseController;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Sql;
use Laminas\View\Model\ViewModel;

class RoleController extends AbstractBaseController
{
    public function indexAction()
    {
        $view = new ViewModel();
        $view = parent::indexAction();
        $view->setTemplate('base/subtable');
        
        $sql = new Sql($this->adapter);
        $select = new Select();
        $select->from($this->model->getTableName());
        $select->columns([
            'UUID' => 'UUID',
            'Role' => 'ROLENAME',
            'Parents' => 'PARENT',
            'Priority' => 'PRIORITY',
        ]);
        $select->where(['roles.STATUS' => $this->model::ACTIVE_STATUS]);
        $select->order(['PRIORITY']);
        
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
                'route' => 'role/default',
                'action' => 'update',
                'key' => 'UUID',
                'label' => 'Update',
            ],
            [
                'route' => 'role/default',
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
            'title' => 'Roles',
        ]);
        
        return $view;
    }
}