<?php

namespace rsanchez\Deep\App\Laravel;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Foundation\AliasLoader;
use rsanchez\Deep\Deep;
use rsanchez\Deep\Model\Model;
use rsanchez\Deep\Validation\Validator;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($connection = $this->app['config']->get('database.deep.connection')) {
            Model::setGlobalConnection($connection);
        }

        $this->app->singleton('deep', function ($app) {
            $deep = new Deep();

            // use Laravel's Validator
            $deep->extend('ValidatorFactory', function($deep) use ($app) {
                $validatorFactory = new ValidatorFactory($app['validator']->getTranslator(), $app);

                $validatorFactory->setPresenceVerifier($deep->make('ValidationPresenceVerifier'));

                $validatorFactory->resolver(function ($translator, $data, $rules, $messages) {
                    return new Validator($translator, $data, $rules, $messages);
                });

                return $validatorFactory;
            });

            return $deep;
        });

        $this->app->singleton('deep.entry', function ($app) {
            return $app->make('deep')->make('Entry');
        });

        $this->app->singleton('deep.title', function ($app) {
            return $app->make('deep')->make('Title');
        });

        $this->app->singleton('deep.category', function ($app) {
            return $app->make('deep')->make('Category');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // register our Facade aliases
        $loader = AliasLoader::getInstance();
        $loader->alias('Entries', 'rsanchez\Deep\App\Laravel\Facade\Entries');
        $loader->alias('Titles', 'rsanchez\Deep\App\Laravel\Facade\Titles');
        $loader->alias('Categories', 'rsanchez\Deep\App\Laravel\Facade\Categories');
    }
}
