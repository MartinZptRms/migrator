<?php

namespace App\View\Components;

use Closure;
use Exception;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Pluralizer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;

class Toolbar extends Component
{
    private $breadcrumbs = [
        [ 'module' => 'dashboard', 'route' => 'dashboard', 'plural' => 'Dashboard', 'single' => 'Dashboard', 'title' => 'name'],
        [ 'module' => 'users', 'route' => 'users', 'plural' => 'Usuarios', 'single' => 'Usuario', 'title' => 'name'],
        [ 'module' => 'connections', 'route' => 'connections', 'plural' => 'Conexiones', 'single' => 'Conexión', 'title' => 'name'],
        [ 'module' => 'databases', 'route' => 'databases', 'plural' => 'Bases de Datos', 'single' => 'Base de Datos', 'title' => 'name'],
        [ 'module' => 'tables', 'route' => 'databases.tables', 'plural' => 'Tablas', 'single' => 'Tabla', 'title' => 'name'],
        [ 'module' => 'columns', 'route' => 'databases.tables.columns', 'plural' => 'Columnas', 'single' => 'Columna', 'title' => 'name'],
        [ 'module' => 'services', 'route' => 'services', 'plural' => 'Servicios', 'single' => 'Servicio', 'title' => 'name'],
    ];

    private $exceptions = [
        'dashboard' => "index",
        'profile' => "show",
    ];

    private $actions = [
        'index' => 'Listado de',
        'create' => 'Crear',
        'edit' => 'Editar',
        'show' => 'Ver',
        'build' => 'Editar',
        'setting' => 'Configurar',
    ];

    protected $pathModel = 'App\\Models\\';

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $nameRoutes = Collection::make(Route::getRoutes()->getRoutesByName());
        $this->breadcrumbs = Collection::make($this->breadcrumbs);
        $currentRouteName = Route::currentRouteName();
        [$breadcrumbs, $action] = $this->getModulesName($currentRouteName);

        $this->getRoutes($breadcrumbs, $action);
        $module = $this->getModuleName($breadcrumbs, $action);
        $this->getItemParent($breadcrumbs);

        return view('components.toolbar', compact('breadcrumbs', 'module'));
    }

    private function getModulesName($currentRouteName)
    {
        $modules = explode('.',$currentRouteName);
        $action = array_pop($modules);

        $breadcrumbs = $this->breadcrumbs->whereIn('module', $modules); //if it any time this faill, change for ->where and in breadcrums add module.submodule

        return [$breadcrumbs, $action];
    }

    private function getItemParent(&$breadcrumbs){
        $breadcrumbs = $breadcrumbs->values();
        $originalParams = Request::route()->originalParameters();
        for($i = 0; $i < $breadcrumbs->count(); $i++){
            if($i%2 == 1){
                $module = $breadcrumbs[$i-1];
                $singular =  Str::singular($module['module']);

                // $params = explode('.',$breadcrumbs[$i]['route']);
                $params = explode('.',$module['route']);
                array_pop($params);
                $routeParams = [];
                foreach($params as $p){
                    $routeParams[Str::singular($p)] = $originalParams[Str::singular($p)];
                }

                $breadcrumbs->splice($i, 0,[
                    [ 'plural' => request()->{$singular}->{$module['title']}, 'routeIndex' => URL::route($module['route'].'.index', $routeParams)  ,'type' => 'parent' ]
                ]);
            }
        }
    }

    private function getModuleName($breadcrumbs, $action)
    {
        $module = $breadcrumbs->last();
        $module = $this->breadcrumbs->firstWhere('module', $module['module']);

        $module['nameModule'] = $this->getNameModule($action, $module);

        return $module;
    }

    private function getRoutes($breadcrumbs, $action){
        $bc = Collection::make($breadcrumbs->toArray());
        $params = Request::route()->originalParameters();
        $bc->reverse()->map(function($b, int $key) use(&$breadcrumbs, $bc, &$params, $action){
            $br = $breadcrumbs->where('module', $b['module'])->first();
            $method = "index";
            $routeException = array_search($bc->implode('module','.'), array_keys($this->exceptions));

            if($routeException !== false) $method = $this->exceptions[array_keys($this->exceptions)[$routeException]];

            if($action == 'index' && array_search($bc->implode('module','.'), array_keys($this->exceptions)) === false) $br['routeCreate'] = route( $bc->implode('module','.').'.create', $params);

            $br['routeIndex'] = route( $bc->implode('module','.').".$method", $params);

            $breadcrumbs = $breadcrumbs->put($key,$br);
            $bc->pop();
            array_pop($params);
        });
    }

    private function getNameModule($action, $module){
        if($action == 'index'){
            return $this->actions[$action].' '.$module['plural'];
        }
        return $this->actions[$action].' '.$module['single'];
    }
}
