<?php
/**
* DotBoost Technologies Inc.
* DotKernel Application Framework
*
* @category   DotKernel
* @package    DotLibrary
* @copyright  Copyright (c) 2009-2011 DotBoost Technologies Inc. Canada (http://www.dotboost.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
* @version    $Id$
*/

/**
* Control user access in DotKernel application
* access control list (ACL)
* @category   DotKernel
* @package    DotLibrary
* @author     DotKernel Team <team@dotkernel.com>
*/

class Dot_Acl
{
	/**
	 * Dot_Acl constructor.
	 * Make the proper initializations, set roles, resources and permisssions
	 * using Zend_Acl
	 * @access public
	 * @return Dot_Acl
	 */
	public function __construct()
	{
		$router = new Zend_Config_Xml(CONFIGURATION_PATH.'/router.xml');
		$role = new Zend_Config_Xml(CONFIGURATION_PATH.'/acl/role.xml');

		$this->requestModule = Zend_Registry::get('requestModule');
		$this->requestController = Zend_Registry::get('requestController');
		$this->requestControllerProcessed = Zend_Registry::get('requestControllerProcessed');
		$this->requestAction = Zend_Registry::get('requestAction');

		// instantiate Zend_Acl
		$this->acl = new Zend_Acl();
		//get resource(controllers) only for the curent module
		$this->_resource = $router->controllers->{$this->requestModule};
		//get permission only for current module
		$this->_permission = $role->permission->{$this->requestModule};
		$this->_role = $role->type;

		$this->_addRoles();
		$this->_addResources();
	}
	/**
	 * Add roles to ACL
	 * @access private
	 * @return void
	 */
	private function _addRoles()
	{
		// prepare roles from the xml to an array
		$roles = array();
		foreach ($this->_role->toArray()as $parent => $v)
		{
			if(is_string($v))
			{
				if(isset($roles[$v]))
				{
					$roles[$v] = array_merge($roles[$v], array($parent));
				}
				else
				{
					$roles[$v] = ('null' == $parent) ? null : array($parent);
				}
			}
			else
			{
				foreach ($v as $child)
				{
					if(isset($roles[$child]))
					{
						$roles[$child] = array_merge($roles[$child], array($parent));
					}
					else
					{
						$roles[$child] = array($parent);
					}
				}
			}
		}
		// add roles to ACL
		foreach($roles as $name => $parents)
		{
			if(!$this->acl->hasRole($name))
			{
                if(empty($parents))
				{
                    $parents = array();
				}
                $this->acl->addRole(new Zend_Acl_Role($name), $parents);
            }
		}
	}
	/**
	 * Add resources and permissions to ACL
	 * @access private
	 * @return void
	 */
	private function _addResources()
	{
		// add resource to ACL
		if(is_string($this->_resource))
		{
			$this->_resource = new Zend_Config(array(0=>$this->_resource));
		}
		foreach ($this->_resource->toArray() as $resource)
		{
			if(!$this->acl->has($resource))
			{
            	$this->acl->add(new Zend_Acl_Resource($resource));
        	}
		}
		// prepare permission and add them to ACL
		foreach ($this->_permission->toArray() as $permission => $value)
		{
			foreach ($value as $role => $allControllers)
			{
				if('all' == $allControllers)
				{
					if('allow' == $permission)
					{
						 $this->acl->allow($role);
					}
					if('deny' == $permission)
					{
						 $this->acl->deny($role);
					}
				}
				elseif(is_array($allControllers))
				{
					foreach ($allControllers as $controller => $action)
					{
						if('all' == $action)
						{
	                        $action = null;
	                    }
						if('allow' == $permission)
						{
							 $this->acl->allow($role, $controller, $action);
						}
						if('deny' == $permission)
						{
							 $this->acl->deny($role, $controller, $action);
						}
					}
				}
			}
		}
	}
	/**
	 * Get ACL roles
	 * @access public
	 * @return array
	 */
	public function getRoles()
	{
		return $this->acl->getRoles();
	}
	/**
	 * Check if role is allowed to a resource(controller, action)
	 * @access public
	 * @param string $role
	 * @return bool
	 */
	public function isAllowed($role)
	{
		$resource = $this->requestControllerProcessed;
		$privillege = $this->requestAction;
		if(!$this->acl->has($resource))
		{
			return FALSE;
		}
		else
		{
			return $this->acl->isAllowed($role, $resource, $privillege);
		}
	}
}