<?php

namespace LivewireAutocomplete;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public $name = 'livewire-autocomplete';
    public $namespace = 'lwa';

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/' . $this->name . '.php',
            $this->name
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/livewire-autocomplete.php');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/' . $this->name . '.php' => config_path($this->name . '.php'),
        ], [$this->name, $this->name . '-config']);

        $this->loadViews(__DIR__ . '/../resources/views', $this->namespace, config($this->name . '.use_global_namespace', true));

        if (config($this->name . '.use_global_namespace', true)) {
            Blade::anonymousComponentNamespace(__DIR__ . '/../resources/views/components');
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
}
