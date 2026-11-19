<?php

    namespace App\Models;

    // use Illuminate\Contracts\Auth\MustVerifyEmail;
    use App\Models\Cart;
    use App\Models\Wishlist;
    use Laravel\Sanctum\HasApiTokens;
    use Database\Factories\UserFactory;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class User extends Authenticatable
    {
        /** @use HasFactory<UserFactory> */
        use HasFactory, HasApiTokens, Notifiable, SoftDeletes;

        /**
         * The attributes that are mass assignable.
         *
         * @var list<string>
         */
        protected $fillable = [
            'name',
            'email',
            'password',
            'phone',
            'status',
            'plain_password',
            'otp',
            'otp_expires_at',
            'auth_type',
            'role',
            'role_id', // Add role_id
            'is_phone_verified',
            'google_id',
            'avatar',
            'email_verified_at',
            'remember_token',
        ];

        /**
         * The attributes that should be hidden for serialization.
         *
         * @var list<string>
         */
        protected $hidden = [
            'password',
            'remember_token',
        ];

        /**
         * Get the attributes that should be cast.
         *
         * @return array<string, string>
         */
        protected function casts(): array
        {
            return [
                'email_verified_at' => 'datetime',
                'otp_expires_at' => 'datetime',
                'password' => 'hashed',
                'is_phone_verified' => 'boolean',
                'status' => 'integer',
                'deleted_at' => 'datetime',
            ];
        }

        /**
         * Get a usable profile avatar URL (full URL, prefixed path, or default).
         */
        public function getAvatarUrlAttribute(): string
        {
            $default = asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg');
            $avatar = trim((string) ($this->attributes['avatar'] ?? ''));

            if ($avatar === '') {
                return $default;
            }

            if (preg_match('#^https?://#i', $avatar)) {
                return $avatar;
            }

            $avatar = ltrim(str_replace('\\', '/', $avatar), '/');

            if (
                str_starts_with($avatar, 'backend/') ||
                str_starts_with($avatar, 'frontend/') ||
                str_starts_with($avatar, 'storage/') ||
                str_starts_with($avatar, 'uploads/')
            ) {
                return asset($avatar);
            }

            return asset('backend/assets/images/users/' . $avatar);
        }

        /**
         * Relationships
         */
        public function addresses()
        {
            return $this->hasMany(Address::class);
        }

        public function cartItems()
        {
            return $this->hasMany(Cart::class);
        }

        public function wishlistItems()
        {
            return $this->hasMany(Wishlist::class);
        }

        public function orders()
        {
            return $this->hasMany(Order::class);
        }

        /**
         * Get the user's role relationship
         */
        public function role()
        {
            return $this->belongsTo(Role::class);
        }

        /**
         * Get role name attribute (backward compatibility for existing code)
         */
        public function getRoleAttribute()
        {
            return $this->role()->first()?->name ?? 'user';
        }

        /**
         * Get role display name
         */
        public function getRoleDisplayNameAttribute()
        {
            return $this->role()->first()?->display_name ?? 'User';
        }

        /**
         * Get role color for badges
         */
        public function getRoleColorAttribute()
        {
            $role = $this->role()->first();
            return $role?->color ?? 'secondary';
        }

        /**
         * Get role icon
         */
        public function getRoleIconAttribute()
        {
            $role = $this->role()->first();
            return $role?->icon ?? 'ti-user';
        }

        /**
         * Get role permissions
         */
        public function getRolePermissionsAttribute()
        {
            $role = $this->role()->first();
            return $role?->permissions ?? [];
        }

        /**
         * Role Checking Methods
         */
        
        /**
         * Check if user has a specific role
         */
        public function hasRole($roleName)
        {
            $role = $this->role()->first();
            return $role && $role->name === $roleName;
        }

        /**
         * Check if user has any of the given roles
         */
        public function hasAnyRole($roles)
        {
            if (is_string($roles)) {
                $roles = explode(',', $roles);
            }

            $role = $this->role()->first();
            return $role && in_array($role->name, (array) $roles);
        }

        /**
         * Check if user has all of the given roles
         */
        public function hasAllRoles($roles)
        {
            if (is_string($roles)) {
                $roles = explode(',', $roles);
            }

            $role = $this->role()->first();
            if (!$role) {
                return false;
            }

            foreach ((array) $roles as $roleName) {
                if ($role->name !== $roleName) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Specific role checks
         */
        public function isAdmin()
        {
            return $this->hasRole('admin');
        }

        public function isWebDesigner()
        {
            return $this->hasRole('web_designer');
        }

        public function isOrderManager()
        {
            return $this->hasRole('order_manager');
        }

        public function isManager()
        {
            return $this->hasRole('manager');
        }

        public function isUser()
        {
            return $this->hasRole('user');
        }

        /**
         * Permission Checking Methods
         */
        
        /**
         * Check if user has a specific permission
         */
        public function hasPermission($permission)
        {
            // Admin has all permissions
            if ($this->isAdmin()) {
                return true;
            }

            $role = $this->role()->first();
            if (!$role || !$role->permissions) {
                return false;
            }

            return in_array($permission, $role->permissions) || in_array('all', $role->permissions);
        }

        /**
         * Check if user has any of the given permissions
         */
        public function hasAnyPermission($permissions)
        {
            if (is_string($permissions)) {
                $permissions = explode(',', $permissions);
            }

            foreach ((array) $permissions as $permission) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Check if user has all of the given permissions
         */
        public function hasAllPermissions($permissions)
        {
            if (is_string($permissions)) {
                $permissions = explode(',', $permissions);
            }

            foreach ((array) $permissions as $permission) {
                if (!$this->hasPermission($permission)) {
                    return false;
                }
            }
            return true;
        }

        /**
         * Scope Methods
         */
        
        /**
         * Scope to get users by role
         */
        public function scopeByRole($query, $roleName)
        {
            return $query->whereHas('role', function($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        /**
         * Scope to get active users
         */
        public function scopeActive($query)
        {
            return $query->where('status', 1);
        }

        /**
         * Scope to get inactive users
         */
        public function scopeInactive($query)
        {
            return $query->where('status', 0);
        }

        /**
         * Scope to get verified users
         */
        public function scopeVerified($query)
        {
            return $query->whereNotNull('email_verified_at');
        }

        /**
         * Scope to get users with phone verified
         */
        public function scopePhoneVerified($query)
        {
            return $query->where('is_phone_verified', 1);
        }

        /**
         * Helper Methods
         */
        
        /**
         * Check if user is active
         */
        public function isActive()
        {
            return $this->status === 1;
        }

        /**
         * Check if user is verified
         */
        public function isVerified()
        {
            return !is_null($this->email_verified_at);
        }

        /**
         * Check if user is phone verified
         */
        public function isPhoneVerified()
        {
            return $this->is_phone_verified === 1;
        }

        /**
         * Get user full name
         */
        public function getFullNameAttribute()
        {
            return $this->name;
        }

        /**
         * Get user initials
         */
        public function getInitialsAttribute()
        {
            $words = explode(' ', $this->name);
            $initials = '';
            foreach ($words as $word) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
            return substr($initials, 0, 2);
        }

        /**
         * Get total orders count
         */
        public function getTotalOrdersCountAttribute()
        {
            return $this->orders()->count();
        }

        /**
         * Get total spent amount
         */
        public function getTotalSpentAttribute()
        {
            return $this->orders()->sum('grand_total');
        }

        /**
         * Get pending orders count
         */
        public function getPendingOrdersCountAttribute()
        {
            return $this->orders()->where('order_status', 'pending')->count();
        }

        /**
         * Get completed orders count
         */
        public function getCompletedOrdersCountAttribute()
        {
            return $this->orders()->where('order_status', 'delivered')->count();
        }

        /**
         * Get wishlist count
         */
        public function getWishlistCountAttribute()
        {
            return $this->wishlistItems()->count();
        }

        /**
         * Get cart items count
         */
        public function getCartCountAttribute()
        {
            return $this->cartItems()->sum('quantity');
        }

        /**
         * Permissions that grant access to the admin panel
         */
        public static function adminPanelPermissions(): array
        {
            return [
                'dashboard', 'orders', 'transactions', 'refunds', 'products',
                'categories', 'subcategories', 'reviews', 'cms', 'sliders',
                'blogs', 'banners', 'coupons', 'customers', 'users', 'roles',
                'reports', 'settings', 'profile', 'all',
            ];
        }

        /**
         * Check if user can access admin panel
         */
        public function canAccessAdmin()
        {
            if ($this->hasAnyRole(['admin', 'web_designer', 'order_manager', 'manager'])) {
                return true;
            }

            return $this->hasAnyPermission(self::adminPanelPermissions());
        }

        /**
         * Get the best admin landing route for this user
         */
        public function getAdminHomeRoute(): string
        {
            if ($this->hasPermission('dashboard') || $this->hasAnyRole(['admin', 'web_designer', 'order_manager', 'manager'])) {
                return route('admin.dashboard');
            }

            $permissionRoutes = [
                'orders' => 'order.index',
                'transactions' => 'transactions.index',
                'products' => 'product.index',
                'categories' => 'category.index',
                'subcategories' => 'subcategory.index',
                'reviews' => 'review.index',
                'sliders' => 'slider.index',
                'blogs' => 'blog.index',
                'banners' => 'home-banner.index',
                'coupons' => 'coupon.index',
                'customers' => 'customer.index',
                'users' => 'user.index',
                'roles' => 'roles.index',
                'reports' => 'reports.index',
            ];

            foreach ($permissionRoutes as $permission => $routeName) {
                if ($this->hasPermission($permission)) {
                    return route($routeName);
                }
            }

            return route('admin.dashboard');
        }

        /**
         * Get user permissions for sidebar
         */
        public function getSidebarPermissionsAttribute()
        {
            return [
                'dashboard' => $this->hasPermission('dashboard') || $this->hasAnyRole(['admin', 'web_designer', 'order_manager', 'manager']),
                'orders' => $this->hasAnyPermission(['orders', 'all']) || $this->isAdmin(),
                'transactions' => $this->hasAnyPermission(['transactions', 'all']) || $this->isAdmin(),
                'products' => $this->hasAnyPermission(['products', 'all']) || $this->isAdmin(),
                'categories' => $this->hasAnyPermission(['categories', 'all']) || $this->isAdmin(),
                'subcategories' => $this->hasAnyPermission(['subcategories', 'all']) || $this->isAdmin(),
                'reviews' => $this->hasAnyPermission(['reviews', 'all']) || $this->isAdmin(),
                'cms' => $this->hasAnyPermission(['cms', 'all']) || $this->isAdmin(),
                'coupons' => $this->hasAnyPermission(['coupons', 'all']) || $this->isAdmin(),
                'customers' => $this->hasAnyPermission(['customers', 'all']) || $this->isAdmin(),
                'users' => $this->hasAnyPermission(['users', 'all']) || $this->isAdmin(),
                'roles' => $this->hasAnyPermission(['roles', 'all']) || $this->isAdmin(),
                'reports' => $this->hasAnyPermission(['reports', 'all']) || $this->isAdmin(),
                'settings' => $this->hasAnyPermission(['settings', 'all']) || $this->isAdmin(),
            ];
        }

        /**
         * Determine if the user is a staff member
         */
        public function isStaff()
        {
            return $this->canAccessAdmin();
        }

        /**
         * Get users count by role
         */
        public static function getCountByRole($roleName)
        {
            return self::byRole($roleName)->count();
        }

        /**
         * Get all roles with user counts
         */
        public static function getRolesWithCount()
        {
            return Role::withCount('users')->get();
        }

        /**
         * Check if the user can manage a specific module
         */
        public function canManage($module)
        {
            return $this->hasPermission($module) || $this->isAdmin();
        }
    }