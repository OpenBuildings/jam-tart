<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package    Jam
 * @category   Behavior
 * @author     Yasen Yanev
 * @copyright  (c) 2014 Despark Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Kohana_Jam_Behavior_User_Permissions extends Jam_Behavior {

    /**
     * Get all permisions for all user roles.
     * @param  $user permissions for the specific user or for the currently loaded one
     * @return array - key contains permission name, value contains permission description
     */
    public function model_call_permissions(Jam_Model $user, Jam_Event_Data $data)
    {
        $cache_key = $user->id();

        if ( ! isset($this->_permissions[$cache_key]) OR $this->_permissions[$cache_key] === NULL)
        {
            $res = DB::select(
                    array('p.name', 'permission'),
                    array('p.description', 'description')
                )
                ->from(array('roles_users', 'ru'))
                ->join(array('permissions_roles', 'pr'), 'INNER')->on('pr.role_id', '=', 'ru.role_id')
                ->join(array('permissions', 'p'), 'INNER')->on('p.id', '=', 'pr.permission_id')
                ->where('ru.user_id', '=', $user->id());

            $this->_permissions[$cache_key] = $res->execute()->as_array('permission', 'description');
        }

        $data->return = $this->_permissions[$cache_key];
        $data->stop = TRUE;
    }


    public function model_call_is_superadmin(Jam_Model $user, Jam_Event_Data $data)
    {
        $data->return = $user->roles->has('superadmin');
        $data->stop = TRUE;
    }

    public function model_call_is_admin(Jam_Model $user, Jam_Event_Data $data)
    {
        $data->return = $user->roles->has('admin');
        $data->stop = TRUE;
    }


    /**
     * Checks if user has permission.
     *
     * @param   mixed    $permission Permission name string, perission Jam object, permission id
     * @return  boolean
     */
    public function model_call_has_permission(Jam_Model $user, Jam_Event_Data $data, $permission)
    {
        if ($permission instanceof Model_Permission)
        {
            $permission = $permission->name();
        }
        elseif (is_numeric($permission))
        {
            $permission = Jam::factory('permission', $permission)->name();
        }

        if ($user->is_superadmin())
        {
            $data->return = TRUE;
            $data->stop = TRUE;
        }

        $data->return = array_key_exists($permission, $user->permissions());
        $data->stop = TRUE;
    }
}
