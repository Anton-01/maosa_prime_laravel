<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'can_view_price_table', 'is_approved', 'user_type',
        'id_socio', 'id_estacion', 'permiso_precios_pemex',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'can_view_price_table' => 'boolean',
        'permiso_precios_pemex' => 'boolean',
        'id_socio' => 'integer',
        'id_estacion' => 'integer',
    ];

    /**
     * Tabla pivote usuario <-> estación del catálogo nacional (REQ-01).
     * Creada fuera de las migraciones de la aplicación.
     */
    public const PIVOT_USUARIO_ESTACION = 'usuario_estacion';

    /** Columna de la pivote que apunta al usuario. */
    public const PIVOT_USUARIO_KEY = 'id_usuario';

    /** Columna de la pivote que apunta a la estación del catálogo. */
    public const PIVOT_ESTACION_KEY = 'id_estacion';

    /**
     * Get the sessions for the user.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the page visits for the user.
     */
    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }

    /**
     * Get the activities for the user.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the navigation flows for the user.
     */
    public function navigationFlows(): HasMany
    {
        return $this->hasMany(UserNavigationFlow::class);
    }

    /**
     * Estaciones del catálogo nacional asignadas al usuario (REQ-02),
     * a través de la tabla pivote `usuario_estacion`.
     */
    public function estacionesAsignadas(): BelongsToMany
    {
        return $this->belongsToMany(
            EstacionNacional::class,
            self::PIVOT_USUARIO_ESTACION,
            self::PIVOT_USUARIO_KEY,
            self::PIVOT_ESTACION_KEY,
            'id',
            'id_estacion',
        );
    }

    /**
     * Ids de las estaciones asignadas leídos directamente de la pivote, sin
     * tocar la foreign table del catálogo (que puede no estar disponible).
     *
     * @return array<int, int>
     */
    public function estacionesAsignadasIds(): array
    {
        return DB::table(self::PIVOT_USUARIO_ESTACION)
            ->where(self::PIVOT_USUARIO_KEY, $this->id)
            ->pluck(self::PIVOT_ESTACION_KEY)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Permiso de acceso al submódulo "Precios PEMEX" (REQ-02 / REQ-04).
     */
    public function tienePermisoPreciosPemex(): bool
    {
        return (bool) $this->permiso_precios_pemex;
    }

    /**
     * ¿La estación pertenece a las asignadas al usuario?
     */
    public function tieneEstacionAsignada(int $idEstacion): bool
    {
        return in_array($idEstacion, $this->estacionesAsignadasIds(), true);
    }

    /**
     * Check if user can view price table.
     */
    public function canViewPriceTable(): bool
    {
        return (bool) $this->can_view_price_table;
    }

    /**
     * Join the user's most recent session as "last_session"
     * (last_session_at, city, region, country).
     */
    public function scopeWithLastSession(Builder $query): Builder
    {
        $lastSession = DB::table('user_sessions')
            ->select('user_id', 'created_at as last_session_at', 'city', 'region', 'country')
            ->distinct('user_id')
            ->orderBy('user_id')
            ->orderByDesc('created_at');

        return $query->leftJoinSub($lastSession, 'last_session', 'last_session.user_id', '=', 'users.id');
    }

    /**
     * Users without activity since the given cutoff: their last session is
     * older than the cutoff, or they never had a session and were created
     * before the cutoff. Requires scopeWithLastSession().
     */
    public function scopeInactiveSince(Builder $query, $cutoff): Builder
    {
        return $query->where(function (Builder $q) use ($cutoff) {
            $q->where('last_session.last_session_at', '<', $cutoff)
                ->orWhere(function (Builder $q2) use ($cutoff) {
                    $q2->whereNull('last_session.last_session_at')
                        ->where('users.created_at', '<', $cutoff);
                });
        });
    }
}
