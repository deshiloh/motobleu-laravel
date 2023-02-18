<?php

// API PASSAGER
use App\Models\AdresseReservation;
use App\Models\CostCenter;
use App\Models\Entreprise;
use App\Models\Localisation;
use App\Models\Passager;
use App\Models\Pilote;
use App\Models\TypeFacturation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

Route::get('/passagers', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');
    $user = $request->input('user');

    $result = Passager::query()
        ->select('id', 'nom', 'email');

    if (!empty($user)) {
        $result->where('user_id', '=', (int)$user);
    }

    $result->when($search, function (Builder $query, $search) {
        $query->where('nom', 'like', "%$search%");
    })->when(
        $selected,
        function (Builder $query, $selected) {
            $query->whereIn('id', $selected);
        }
    );

    return $result->get();
})->name('api.passagers');

// API LOCALISATION
Route::get('/pickup_place', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');

    return Localisation::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when($search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%$search%");
        })
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            }
        )
        ->get();
})->name('api.pickupplace');

// API ADRESSE RESERVATION
Route::get('/adresses', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');
    $user = $request->input('user', false);

    return AdresseReservation::when($user, function(Builder $query, $search) {
            $query->where('user_id', $search);
        })
        ->when($search, function (Builder $query, $search) {
            $query->where('adresse', 'like', "%$search%");
        })
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            }
        )
        ->orderBy('adresse')
        ->get();
})->name('api.adresses');

// API USER ENTREPRISE
Route::get('/user-entreprise', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');
    $notIn = $request->input('notIn', false);

    return User::query()
        ->select('users.*')
        ->orderBy('users.nom')
        ->when($search, function (Builder $query, $search) {
            $query->where('users.nom', 'like', "%$search%");
        })
        ->when($notIn, function (Builder $query, $notIn) {
            $query->whereNotIn('id', $notIn);
        })
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            }
        )
        ->get();
})->name('api.user_in_entreprise');

Route::get('/entreprises_users', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');
    $userId = $request->input('userId');

    return Entreprise::query()
        ->select('entreprises.id', 'entreprises.nom')
        ->join('entreprise_user', 'entreprise_id', '=', 'entreprises.id')
        ->where('entreprise_user.user_id', $userId)
        ->where('is_actif', true)
        ->orderBy('entreprises.nom')
        ->when(
            $search, function (Builder $query, $search) {
                $query->where('entreprises.nom', 'like', "%$search%");
            }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('entreprises.id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.entreprises_users');

Route::get('/users', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return User::query()
        ->select('id', 'nom', 'email', 'prenom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query
                ->where('nom', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('prenom', 'like', "%$search%")
            ;
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.users');

Route::get('/cost-center', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return CostCenter::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%$search%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.cost_center');

Route::get('/type-facturation', function (Request $request){
    $search = $request->input('search');
    $selected = $request->input('selected');

    return TypeFacturation::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%$search%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.type_facturation');

Route::get('/pilotes', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');

    return Pilote::query()
        ->select('id', 'nom', 'email', 'prenom', 'is_actif')
        ->orderBy('nom')
        ->when($search, function (Builder $query, $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('nom', 'like', "%$search%");
                    $query->orWhere('prenom', 'like', "%$search%");
                });
            }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->where('is_actif', true)
        ->get();
})->name('api.pilotes');

Route::get('/entreprises', function (Request $request) {
    $search = $request->input('search');
    $selected = $request->input('selected');
    $without = $request->input('exclude');

    return Entreprise::query()
        ->select('id', 'nom')
        ->orderBy('nom')
        ->where('is_actif', true)
        ->when($without, function (Builder $query, $excludes) {
            $query->whereNotIn('id', $excludes);
        })
        ->when(
            $search, function (Builder $query, $search) {
            $query->where('nom', 'like', "%$search%");
        }
        )
        ->when(
            $selected,
            function (Builder $query, $selected) {
                $query->whereIn('id', $selected);
            },
            function (Builder $query) {
                $query->limit(10);
            }
        )
        ->get();
})->name('api.entreprises');

