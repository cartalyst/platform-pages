<?php namespace Platform\Pages\Menus;
/**
 * Part of the Platform application.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Platform
 * @version    2.0.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011 - 2013, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Platform\Menus\BaseType;
use Platform\Menus\Models\Menu;
use Platform\Menus\TypeInterface;

class StaticType extends BaseType implements TypeInterface {

    /**
     * Get the type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'page';
    }

    /**
     * Get a human friendly name for the type.
     *
     * @return string
     */
    public function getName()
    {
        return 'Page';
    }

    /**
     * Get the name for the menu child.
     *
     * @param  Platform\Menus\Models\Menu  $child
     * @return string
     */
    public function getChildName(Menu $child)
    {
        return $child->name;
    }

    /**
     * Get the URL for the menu child.
     *
     * @param  Platform\Menus\Models\Menu  $child
     * @param  array  $options
     * @return string
     */
    public function getChildUrl(Menu $child, array $options = array())
    {
        if ($uri = $child->uri)
        {
            if (isset($options['before_uri']))
            {
                $uri = $options['before_uri'].'/'.$uri;
            }

            return $this->url->to($uri);
        }
    }

    /**
     * Called after a menu child is saved. Attach any links
     * and relationships.
     *
     * @param  Platform\Menus\Models\Menu  $child
     * @return void
     */
    public function afterSave(Menu $child)
    {
        $data = $child->getTypeData();
        $save = false;

        if (isset($data['uri']))
        {
            $child->uri = $data->uri;
            $save = true;
        }
        if (isset($data['name']))
        {
            $child->Name = $data->name;
            $save = true;
        }

        if ($save) $child->save();
    }

    /**
     * Called before a child is deleted. Detach any links
     * and relationships.
     *
     * @param  Platform\Menus\Models\Menu  $child
     * @return void
     */
    public function beforeDelete(Menu $child) {}

    protected function getRelation()
    {

    }

}
