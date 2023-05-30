<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart {

    private const CSRF_SESSION_TOKEN_INDEX = 'tart_csrf_token';

    public static function convert_to_class($name)
    {
        return str_replace(' ', '_', ucwords(str_replace('_', ' ', $name)));
    }

    public static function to_sentence(array $arr)
    {
        if ( ! is_array($arr))
            return (string) $arr;

        $length = count($arr);
        switch ($length)
        {
            case 0:
                return '';
            case 1:
                return (string) $arr[0];
            case 2:
                return $arr[0].' and '.$arr[1];
            default:
                return join(', ', array_slice($arr, 0, -2)).', '.$arr[$length-2].' and '.$arr[$length-1];
        }
    }

    public static function filter(array $data, array $items = NULL)
    {
        return new Tart_Filter($data, $items);
    }

    public static function index(Jam_Query_Builder_Collection $collection, $offset = 0, array $items = array())
    {
        return new Tart_Index($collection, $offset, $items);
    }

    public static function table($collection = array(), array $items = array())
    {
        return new Tart_Table($collection, $items);
    }

    public static function pagination($total = 0, $offset = 0)
    {
        return new Tart_Pagination($total, $offset);
    }

    public static function date_range($start = NULL, $end = NULL, $period = NULL)
    {
        return new Tart_Date_Range($start, $end, $period);
    }

    public static function stats_widget($name, Tart_Date_Range $range = NULL)
    {
        $class = 'Stats_Widget_'.Tart::convert_to_class($name);
        return new $class($name, $range);
    }

    public static function form($context = NULL, $content = NULL)
    {
        return new Builder_Form(NULL, NULL, $content, $context);
    }

    public static function html($context = NULL, $content = NULL)
    {
        return new Builder_Html(NULL, NULL, $content, $context);
    }

    public static function entry($name, $params = NULL, $callback = NULL)
    {
        $class = 'Tart_Filter_Entry_'.Tart::convert_to_class($name);
        return new $class($params, $callback);
    }

    public static function column($name = NULL, $callback = NULL)
    {
        if (is_string($name) AND ! is_callable($name))
        {
            $class = 'Tart_Column_'.Tart::convert_to_class($name);
            return new $class($callback);
        }
        else
        {
            return new Tart_Column($name, $callback);
        }
    }

    public static function category($controller)
    {
        return Request::initial() ? Request::initial()->param('category') : NULL;
    }

    public static function allowed($url, array $allowed = array(), array $disallowed = array())
    {
        foreach (array_filter(array_diff($disallowed, $allowed)) as $rule)
        {
            if (preg_match("#{$rule}#", $url))
                return FALSE;
        }

        foreach (array_filter($allowed) as $rule)
        {
            if (preg_match("#{$rule}#", $url))
                return TRUE;
        }

        return FALSE;
    }

    /**
     * @deprecated Use Kohana_Tart_Layout::user_access_by_url instead.
     * @param  $url
     * @param  $user
     * @return bool
     */
    public static function user_allowed($url, $user = NULL)
    {
        if ( ! $user)
            return TRUE;

        $allowed = array();
        $disallowed = array();
        foreach ($user->roles as $role)
        {
            $allowed = Arr::merge($allowed, (array) $role->allowed);
            $disallowed = Arr::merge($disallowed, (array) $role->disallowed);
        }

        return Tart::allowed($url, $allowed, $disallowed);
    }

    public static function user_access_by_url($user = NULL, $permission = NULL, $url)
    {
        if ( ! $user)
            return TRUE;

        $allowed = array();
        $disallowed = array();
        foreach ($user->roles as $role)
        {
            $allowed = Arr::merge($allowed, (array) $role->allowed);
            $disallowed = Arr::merge($disallowed, (array) $role->disallowed);
        }

        return Tart::allowed($url, $allowed, $disallowed);
    }

    public static function user_access_by_permission($user = NULL, $permission, $url = NULL)
    {
        if ( ! $user)
            return TRUE;

        return $user->has_permission($permission);
    }

    public static function uri($item = NULL, $action = NULL)
    {
        $route_name = 'tart';

        if ($item instanceof Jam_Validated)
        {
            $params = array('controller' => Inflector::plural($item->meta()->model()));

            if ($item->loaded())
            {
                $params['id'] = $item->id();
            }

            if (is_array($action))
            {
                $params = Arr::merge($params, $action);
            }
            elseif ($action)
            {
                $params['action'] = $action;
            }

            if ( ! $action OR (is_array($action) AND ! isset($action['action'])))
            {
                $params['action'] = $item->loaded() ? 'edit' : 'new';
            }

            $params['category'] = Arr::get($params, 'category', Tart::category($params['controller']));

            if ($params['category'])
            {
                $route_name = 'tart_category';
            }

            return Route::url($route_name, $params);
        }
        else
        {
            $params = array('controller' => $item);

            if (is_array($action))
            {
                $params = Arr::merge($params, $action);
            }
            elseif ($action)
            {
                $params['action'] = $action;
            }

            $params['category'] = Arr::get($params, 'category', Tart::category($params['controller']));

            if ($params['category'])
            {
                $route_name = 'tart_category';
            }

            return Route::url($route_name, $params);
        }
    }

    public static function generate_csrf_token()
    {
        Session::instance()->set(self::CSRF_SESSION_TOKEN_INDEX, bin2hex(random_bytes(35)));
    }

    public static function get_csrf_token(): ?string
    {
        return Session::instance()->get(self::CSRF_SESSION_TOKEN_INDEX);
    }
}
