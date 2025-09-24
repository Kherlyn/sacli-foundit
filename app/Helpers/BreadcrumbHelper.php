<?php

namespace App\Helpers;

class BreadcrumbHelper
{
  /**
   * Generate breadcrumb items based on current route
   */
  public static function generate(): array
  {
    $routeName = request()->route()->getName();
    $breadcrumbs = [];

    switch ($routeName) {
      case 'browse':
        $breadcrumbs[] = ['title' => 'Browse Items', 'url' => null];
        break;

      case 'search':
        $breadcrumbs[] = ['title' => 'Search Results', 'url' => null];
        break;

      case 'items.show':
        $breadcrumbs[] = ['title' => 'Browse Items', 'url' => route('browse')];
        $breadcrumbs[] = ['title' => 'Item Details', 'url' => null];
        break;

      case 'dashboard':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => null];
        break;

      case 'items.create':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('dashboard')];
        $breadcrumbs[] = ['title' => 'Report Item', 'url' => null];
        break;

      case 'items.my-items':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('dashboard')];
        $breadcrumbs[] = ['title' => 'My Items', 'url' => null];
        break;

      case 'items.edit':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('dashboard')];
        $breadcrumbs[] = ['title' => 'My Items', 'url' => route('items.my-items')];
        $breadcrumbs[] = ['title' => 'Edit Item', 'url' => null];
        break;

      case 'items.view':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('dashboard')];
        $breadcrumbs[] = ['title' => 'My Items', 'url' => route('items.my-items')];
        $breadcrumbs[] = ['title' => 'View Item', 'url' => null];
        break;

      case 'profile.edit':
        $breadcrumbs[] = ['title' => 'Dashboard', 'url' => route('dashboard')];
        $breadcrumbs[] = ['title' => 'Profile', 'url' => null];
        break;

      // Admin breadcrumbs
      case 'admin.dashboard':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => null];
        break;

      case 'admin.pending-items':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'Pending Items', 'url' => null];
        break;

      case 'admin.items':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'All Items', 'url' => null];
        break;

      case 'admin.items.show':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'All Items', 'url' => route('admin.items')];
        $breadcrumbs[] = ['title' => 'Item Details', 'url' => null];
        break;

      case 'admin.categories':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'Categories', 'url' => null];
        break;

      case 'admin.statistics':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'Statistics', 'url' => null];
        break;

      case 'admin.notifications':
        $breadcrumbs[] = ['title' => 'Admin', 'url' => route('admin.dashboard')];
        $breadcrumbs[] = ['title' => 'Notifications', 'url' => null];
        break;

      default:
        // No breadcrumbs for home page and other routes
        break;
    }

    return $breadcrumbs;
  }

  /**
   * Generate custom breadcrumbs
   */
  public static function custom(array $items): array
  {
    return $items;
  }
}
