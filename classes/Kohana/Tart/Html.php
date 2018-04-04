<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Html definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Html {

	/**
	 * Render the tab attributes for Bootstrap tab-content
	 * @param  string $current 
	 * @param  string $id      
	 * @return string          
	 */
	public static function tab($current, $id)
	{
		return HTML::attributes(array(
			'class' => 'tab-pane fade '.(($current == $id) ? 'active in' : ''),
			'id' => $id,
			'disabled' => ($current == $id) ? NULL : 'disabled',
		));
	}

	/**
	 * Render a html navigation based on the given array. Can be one level deep nested
	 * If not provided uses jam-tart.navigation for navigation array, currently logged user as user and current controller as controller
	 * 
	 * @param  array $navigation         
	 * @param  Model_User $user               
	 * @param  string $current_controller 
	 * @return string                     
	 */
	public static function navigation(array $navigation = NULL, $user = NULL, $current_controller = NULL)
	{
		$navigation = $navigation ?: Kohana::$config->load('jam-tart.navigation');
		$user = $user ?: Auth::instance()->get_user();

		if ( ! $current_controller AND Request::initial())
		{
			$current_controller = Request::initial()->controller();
		}

		return Tart::html(NULL, function($h) use ($navigation, $user, $current_controller) {
			$h('div.navbar.navbar-inverse.navbar-fixed-top', function($h) use ($navigation, $user, $current_controller) {
				$h('div.navbar-inner', function($h) use ($navigation, $user, $current_controller) {
					$h('div.container-fluid', function($h) use ($navigation, $user, $current_controller) {
						$h('button', array('class' => 'btn btn-navbar collapsed', 'data-toggle' => 'collapse', 'data-target' => '.nav-collapse'), function($h){
							$h('span.icon-bar', ' ');
							$h('span.icon-bar', ' ');
							$h('span.icon-bar', ' ');
						});

						$h('a', array('class' => 'brand', 'href' => Tart::uri()), 'Admin');
						$h('div.nav-collapse.collapse', function($h) use ($navigation, $user, $current_controller) {
							$h('ul.nav', function($h) use ($navigation, $user, $current_controller) {
								foreach ($navigation as $controller => $name) 
								{
									if (is_array($name))
									{
										if (array_filter(array_map(function($controller) use ($user) { return Tart::user_allowed(Tart::uri($controller), $user);}, array_keys($name))))
										{
											$h('li', array('class' => 'dropdown '.(array_key_exists($current_controller, $name) ? 'active' : '')), function($h) use ($controller, $current_controller, $user, $name) {
												$h('a', array('href' => '#', 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'), $controller.' <b class="caret"></b>');

												$h('ul.dropdown-menu', function($h) use ($controller, $name, $user, $current_controller) {
													foreach ($name as $controller => $name) 
													{
														if (Tart::user_allowed(Tart::uri($controller), $user))
														{
															$h('li', array('class' => ($current_controller === $controller) ? 'active' : NULL), function($h) use ($controller, $name) {
																$h('a', array('href' => Tart::uri($controller)), $name);
															});
														}
													}
												});
											});
										}
									}
									elseif (Tart::user_allowed(Tart::uri($controller), $user))
									{
										$h('li', array('class' => ($current_controller === $controller) ? 'active' : NULL), function($h) use ($controller, $name) {
											$h('a', array('href' => Tart::uri($controller)), $name);
										});
									}
								}
							});
							if ($user)
							{
								$h('div.pull-right', function($h) use ($user) {
									$h('ul.nav.pull-right', function($h) use ($user) {
										$h('li.dropdown', function($h) use ($user) {
											$h('a', array('href' => '#', 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown'), $user->name().' <b class="caret"></b>');

											$h('ul.dropdown-menu', function($h) use ($user) {
												$h('li', function($h) use ($user) {
													$h('a', array('href' => Tart::uri($user)), '<i class="icon-cog"></i> Preferences');
												});
												$h('li.divider');
												$h('li', function($h) {
													$h('a', array('href' => Tart::uri('session', 'destroy')), '<i class="icon-off"></i> Logout');
												});
											});
										});
									});
								});
							}
						});
					});
				});
			});
		})->render();
	}

	public static function accordion($collection, $view, $id, $link = NULL, $title = NULL, $limit = 10)
	{
		$count = $collection->count_all();

		if ( ! $collection->count_all())
			return NULL;

		$body_id = 'accordion-'.$collection->meta()->model();
		$title = $title ?: ucfirst(Inflector::humanize(Inflector::plural($collection->meta()->model())));

		$options = compact('title', 'collection', 'view', 'limit', 'count', 'link', 'id', 'body_id');

		return Tart::html($options, function($h) {
			$h('div.accordion-group', function($h, $options) {
				$h('div.accordion-heading', function($h, $options) {
					if ($options['link'])
					{
						$h('a', array('href' => $options['link'], 'class' => 'accordion-toggle pull-right'), 'List <i class="icon-list"></i>');
					}
					$h('a', array('class' => 'accordion-toggle', 'data-toggle' => 'collapse', 'data-parent' => $options['id'], 'href' => '#'.$options['body_id']), function($h, $options) {
						$h->add($options['title']);
						$h('span.muted', '('.$options['count'].')');
					});
				});
				$h('div', array('class' => 'accordion-body collapse', 'id' => $options['body_id']), function($h) {
					$h('ul.thumbnails.accordion-inner', function($h, $options) {
						foreach ($options['collection']->limit($options['limit']) as $item) 
						{
							$h->add(View::factory($options['view'], array('item' => $item)));
						}
					});
				});
			});
		})->render();
	}

	/**
	 * Display a pills submenu 
	 * 
	 * @param  string $controller 
	 * @param  string $current    
	 * @param  array  $items      
	 * @return string             
	 */
	public static function submenu($controller, $current,  array $items = array())
	{
		return Tart::html(NULL, function($h) use ($controller, $current, $items) {
			$h('ul.nav.nav-pills', function($h) use ($controller, $current, $items) {
				foreach ($items as $item => $title) 
				{
					$h('li', array('class' => ($current == $item) ? 'active' : NULL), function($h) use ($title, $item, $controller) {
						$h('a', array('href' => Tart::uri($controller, array('category' => $item))), $title);
					});
				}
			});
		});
	}

	public static function anchor($anchor, $title = NULL, array $attributes = array(), $user = NULL)
	{
		if ( ! $anchor)
			return NULL;

		$user = $user ?: Auth::instance()->get_user();
		$options = Arr::extract($attributes, array('external', 'limit', 'if', 'unless'));
		$attributes = array_diff_key($attributes, $options);

		if ($title === NULL)
		{
			$title = $anchor;
		}

		if ($options['limit'] !== NULL)
		{
			$title = Text::limit_chars($title, $options['limit']);
		}

		if ($options['external'] !== NULL)
		{
			$title .= ' <i class="icon-share"></i>';
		}

		if ( ! $options['external'] AND ! Tart::user_allowed($anchor, $user))
		{
			return NULL;
		}

		if (($options['if'] !== NULL AND ! $options['if']) OR ($options['unless'] !== NULL AND $options['unless']))
		{
			return "<span".HTML::attributes($attributes).">{$title}</span>";
		}

		return HTML::anchor($anchor, $title, $attributes);
	}

	public static function date_span($timestamp, $local_timestamp = NULL)
	{
		$local_timestamp = ($local_timestamp === NULL) ? Jam_Timezone::instance()->convert(time(), Jam_Timezone::DEFAULT_TIMEZONE, Jam_Timezone::USER_TIMEZONE) : (int) $local_timestamp;

		// Determine the difference in seconds
		$offset = abs($local_timestamp - $timestamp);

		$timeranges = array(
			Date::MINUTE => array(1, 'second'),
			Date::HOUR => array(Date::MINUTE, 'minute'),
			Date::DAY => array(Date::HOUR, 'hour'),
			Date::WEEK*2 => array(Date::DAY, 'day'),
			Date::MONTH => array(Date::WEEK, 'week'),
			Date::YEAR => array(Date::MONTH, 'month'),
			PHP_INT_MAX => array(Date::YEAR, 'year'),
		);

		foreach ($timeranges as $renge => $display) 
		{
			if ($offset <= $renge)
			{
				$span = floor($offset / $display[0]);
				$span = $span.' '.(($span == 1) ? $display[1] : Inflector::plural($display[1]));
				break;
			}
		}

		if ($timestamp <= $local_timestamp)
		{
			// This is in the past
			return $span.' ago';
		}
		else
		{
			// This in the future
			return 'in '.$span;
		}	
	}

	public static function notifications()
	{
		if ($notifications = Session::instance()->get_once('tart.notifications'))
		{
			return Tart::html(NULL, function($h) use ($notifications) {
				foreach ($notifications as $notification) 
				{
					$h('div', array('class' => 'alert alert-'.$notification['label']), function($h) use ($notification) {
						$h('button', array('type' => 'button', 'class' => 'close', 'data-dismiss' => 'alert'), '&times;');
						$h('strong', ucfirst($notification['label']));
						$h->add($notification['message']);
					});
				}	
			});
		}
	}
}
