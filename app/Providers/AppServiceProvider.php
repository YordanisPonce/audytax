<?php

namespace App\Providers;

// use Doctrine\DBAL\Schema\View;
use Illuminate\Support\Facades\View;
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
        // =====================================================================================
    // View Composer: inyecta las últimas entradas de History a “components.dashboard-header”
    // =====================================================================================
    View::composer('components.dashboard-header', function($view) {
        // Obtenemos las últimas 5 entradas de History (puedes cambiar “take(5)” si deseas otra cantidad).
        $latestHistories = \App\Models\History::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Con esto, en el Blade 'dashboard-header' estará disponible $notifications
        $view->with('notifications', $latestHistories);
    });
}

}
