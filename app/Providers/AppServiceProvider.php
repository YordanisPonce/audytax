<?php

namespace App\Providers;

use Doctrine\DBAL\Schema\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\AuditoryType;
use App\Models\Comment;
use App\Models\Document;
use App\Models\Fase;
use App\Models\QualityControl;
use App\Observers\AuditoryTypeObserver;
use App\Observers\CommentObserver;
use App\Observers\DocumentObserver;
use App\Observers\FaseObserver;
use App\Observers\QualityControlObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::shouldBeStrict(!$this->app->isProduction());
        $roles = ['admin' => 'Administrador', 'consultant' => 'Consultor', 'client' => 'Cliente'];
        view()->share('allRoles', $roles);

        //Observables
        AuditoryType::observe(AuditoryTypeObserver::class);
        Fase::observe(FaseObserver::class);
        QualityControl::observe(QualityControlObserver::class);
        Document::observe(DocumentObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
