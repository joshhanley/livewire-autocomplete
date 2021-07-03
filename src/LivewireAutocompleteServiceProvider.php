<?php

namespace LivewireAutocomplete;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use LivewireAutocomplete\Components\Autocomplete;

class LivewireAutocompleteServiceProvider extends ServiceProvider
{
    public $name = 'autocomplete';
    public $namespace = 'lwc';

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/' . $this->name . '.php',
            $this->name
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/' . $this->name . '.php' => config_path($this->name . '.php'),
        ], [$this->name, $this->name . '-config']);

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/' . $this->name),
        ], [$this->name, $this->name . '-views']);

        $this->loadViews(__DIR__ . '/../resources/views', $this->namespace, config($this->name . '.use_global_namespace', false));

        if (config($this->name . '.use_global_namespace', false)) {
            Blade::component(Autocomplete::class, null);
        } else {
            Blade::componentNamespace('LivewireAutocomplete\\Components', $this->namespace);
        }
    }

    protected function loadViews($path, $namespace, $useGlobalNamespace = false)
    {
        $this->callAfterResolving('view', function ($view) use ($path, $namespace, $useGlobalNamespace) {
            if (isset($this->app->config['view']['paths'])
                && is_array($this->app->config['view']['paths'])) {
                foreach ($this->app->config['view']['paths'] as $viewPath) {
                    if (is_dir($appPath = $viewPath . '/vendor/' . $this->name)) {
                        $useGlobalNamespace
                            ? $view->addLocation($appPath)
                            : $view->addNamespace($namespace, $appPath);
                    }
                }
            }

            $useGlobalNamespace
                ? $view->addLocation($path)
                : $view->addNamespace($namespace, $path);
        });
    }

    /**
     * Override the defalt mergeConfigFrom and make use of array_replace_recursive instead
     * for making sure nested arrays are merged correctly.
     *
     * @param string $path
     * @param string $key
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, array_replace_recursive(
                require $path,
                $config->get($key, [])
            ));
        }
    }
}
