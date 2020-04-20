<?php
namespace User\Authentication;

use Laminas\Authentication\Result;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Db\Adapter\AdapterAwareTrait;
use User\Model\RoleModel;
use Settings\Model\SettingsModel;

class LdapAdapter implements AdapterInterface
{
    use AdapterAwareTrait;
    
    private $username;
    private $password;
    
    public function authenticate()
    {
        if ($this->authenticate_local_user()) {
            return new Result(Result::SUCCESS, $this->username, ["Authenticated Successfully"]);
        }
        
        $settings = new SettingsModel($this->adapter);
        $settings->MODULE = 'USER';
        $user_settings = $settings->get_module_settings();
        
//         $domain = 'MIDNET\\';
//         $server = 'IT-DC001.midnet.cityofmiddletown.com';
//         $base_dn = "DC=MidNet,DC=CityOfMiddletown,DC=com";
        $domain = $user_settings['LDAP_DOMAIN'];
        $server = $user_settings['LDAP_SERVER'];
        $base_dn = $user_settings['LDAP_BASE_DN'];
        
        
        $url = 'ldap://' . $server;
        $bind_rdn = $domain . $this->username;
        $bind_password = $this->password;
        
        $link_identifier = ldap_connect($url);
        ldap_set_option($link_identifier, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($link_identifier, LDAP_OPT_REFERRALS, 0);
        $bind = @ldap_bind($link_identifier, $bind_rdn, $bind_password);
        
        if ($bind) {
            $filter = "(samAccountName=" . $this->username . ")";
            $result_identifier = ldap_search($link_identifier, $base_dn, $filter);
            $info = ldap_get_entries($link_identifier, $result_identifier);
            $ary_ldap_groups = [];
            $matches = [];
            foreach ($info[0]['memberof'] as $id => $ldap_group) {
                preg_match('/CN=([a-zA-Z\s-]+)/', $ldap_group, $matches);
                if ($matches[1]) {
                    $ary_ldap_groups[] = $matches[1];
                }
            }
            
            /**
             * If user is a member of a role in this database, synchronize user info
             */
            $role = new RoleModel($this->adapter);
            $roles = $role->fetchAll();
            $ary_roles = [];
            foreach ($roles as $id => $ary) {
                $ary_roles[] = $ary['ROLENAME'];
            }
            
            $matching_roles = array_intersect($ary_roles, $ary_ldap_groups);
            
            if (sizeof($matching_roles)) {
                return new Result(Result::SUCCESS, $this->username, ["Authenticated Successfully"]);
            } else {
                return new Result(Result::FAILURE_UNCATEGORIZED, $this->username, ["Not allowed use of application"]);
            }
            
            
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, $this->username, ['Invalid Credentials']);
        }
    }
    
    public function authenticate_local_user()
    {
        $local_adapter = new AuthAdapter();
        
        $local_adapter->setDbAdapter($this->adapter);
        $local_adapter->setUsername($this->username);
        $local_adapter->setPassword($this->password);
        $result = $local_adapter->authenticate();
        
        switch ($result->getCode()) {
            case Result::SUCCESS:
                return TRUE;
                break;
                
            default:
                return FALSE;
                break;
        }
    }
    
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

}