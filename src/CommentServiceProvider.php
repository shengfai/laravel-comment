<?php

namespace Shengfai\LaravelComment;

use Illuminate\Support\ServiceProvider;

class CommentServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/comments.php' => config_path('comments.php')
        ], 'config');
        
        if (!class_exists('CreateCommentsTable')) {
            $timestamp = date('Y_m_d_His', time());
            
            $this->publishes([
                __DIR__ . '/../database/migrations/create_comments_table.php' => database_path("/migrations/{$timestamp}_create_comments_table.php")
            ], 'migrations');
        }
        
        // $this->loadRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/comments.php', 'comments');
    }

    /**
     * If routes are enabled (by default they are),
     * then load the routes, otherwise don't load
     * the routes.
     */
    protected function loadRoutes()
    {
        if (config('comments.routes') === true) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
    }
}