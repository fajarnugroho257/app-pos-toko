<?php

namespace App\Providers;

use App\Models\TokoPusat;
use App\Models\User;
use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFour();
        //
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = User::where('user_id', Auth::user()->user_id)->first();
                // check
                $user_image = 'image/profil/default.jpg';
                if ($user->role_id == 'R0001') {
                    $user_image = 'image/profil/default.jpg';
                } else if ($user->role_id == 'R0004') {
                    // toko pusat
                    $tokoPusat = TokoPusat::with('toko_pusat_user')->whereRelation('toko_pusat_user', 'user_id', $user->user_id)->first();
                    $user_image = 'image/profil/' . $tokoPusat->user_image ?? '';
                } else if ($user->role_id == 'R0006') {
                    $user_image = 'image/profil/default.jpg';
                }
                $data['user_image'] = $user_image;
                // dd($data);
                $view->with('headerData', $data);
                # code...
            }
        });
    }
}
