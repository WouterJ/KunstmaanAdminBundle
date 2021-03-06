<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * SettingsMenuAdaptor to add the Settings MenuItem to the top menu and build the Settings tree
 */
class SettingsMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The MenuBuilder
     * @param MenuItem[]  &$children The current children
     * @param MenuItem    $parent    The parent Menu item
     * @param Request     $request   The Request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('KunstmaanAdminBundle_settings')
                ->setInternalName('Settings')
                ->setParent($parent)
                ->setRole("settings");
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } elseif ('KunstmaanAdminBundle_settings' == $parent->getRoute()) {
            if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanAdminBundle_settings_users')
                    ->setInternalName('Users')
                    ->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanAdminBundle_settings_groups')
                    ->setInternalName('Groups')
                    ->setParent($parent);

                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;

                $menuItem = new MenuItem($menu);
                $menuItem->setRoute('KunstmaanAdminBundle_settings_roles')
                    ->setInternalName('Roles')
                    ->setParent($parent);
                if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                    $menuItem->setActive(true);
                }
                $children[] = $menuItem;

                // Only admins should be able to see this
                if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                    if ($this->container->getParameter('version_checker.enabled')) {
                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanAdminBundle_settings_bundle_version')
                            ->setInternalName('Bundle versions')
                            ->setParent($parent);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;
                    }
                }
            }
        } else {
            if ('KunstmaanAdminBundle_settings_users' == $parent->getRoute()) {
                if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanAdminBundle_settings_users_add')
                        ->setInternalName('Add user')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;

                    $menuItem = new MenuItem($menu);
                    $menuItem->setRoute('KunstmaanAdminBundle_settings_users_edit')
                        ->setInternalName('Edit user')
                        ->setParent($parent)
                        ->setAppearInNavigation(false);
                    if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $menuItem;
                }
            } else {
                if ('KunstmaanAdminBundle_settings_groups' == $parent->getRoute()) {
                    if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanAdminBundle_settings_groups_add')
                            ->setInternalName('Add group')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;

                        $menuItem = new MenuItem($menu);
                        $menuItem->setRoute('KunstmaanAdminBundle_settings_groups_edit')
                            ->setInternalName('Edit group')
                            ->setParent($parent)
                            ->setAppearInNavigation(false);
                        if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                            $menuItem->setActive(true);
                        }
                        $children[] = $menuItem;
                    }
                } else {
                    if ('KunstmaanAdminBundle_settings_roles' == $parent->getRoute()) {
                        if ($this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
                            $menuItem = new MenuItem($menu);
                            $menuItem->setRoute('KunstmaanAdminBundle_settings_roles_add')
                                ->setInternalName('Add role')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
                            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                                $menuItem->setActive(true);
                            }
                            $children[] = $menuItem;

                            $menuItem = new MenuItem($menu);
                            $menuItem->setRoute('KunstmaanAdminBundle_settings_roles_edit')
                                ->setInternalName('Edit role')
                                ->setParent($parent)
                                ->setAppearInNavigation(false);
                            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                                $menuItem->setActive(true);
                            }
                            $children[] = $menuItem;
                        }
                    }
                }
            }
        }
    }

}
