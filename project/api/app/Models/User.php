<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];
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
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_approved' => 'boolean',
            'is_blocked' => 'boolean',
            'info' => 'array',
        ];
    }

    // Relationships
    public function device(): HasOne
    {
        return $this->hasOne(Device::class, 'user_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'user_id');
    }

    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class, 'user_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function createdCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'created_by');
    }

    public function createdAddresses(): HasMany
    {
        return $this->hasMany(Address::class, 'created_by');
    }

    public function createdCustomerGroups(): HasMany
    {
        return $this->hasMany(CustomerGroup::class, 'created_by');
    }

    public function createdInterestedTypes(): HasMany
    {
        return $this->hasMany(InterestedType::class, 'created_by');
    }

    public function createdCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function createdLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'created_by');
    }

    public function createdCustomerNotes(): HasMany
    {
        return $this->hasMany(CustomerNote::class, 'created_by');
    }

    public function createdFollowUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'created_by');
    }

    public function createdSmsTemplates(): HasMany
    {
        return $this->hasMany(SmsTemplate::class, 'created_by');
    }

    public function createdSmsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'created_by');
    }

    public function createdCallLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'created_by');
    }

    public function createdBackups(): HasMany
    {
        return $this->hasMany(Backup::class, 'created_by');
    }

    public function createdSavedFilters(): HasMany
    {
        return $this->hasMany(SavedFilter::class, 'created_by');
    }

    public function createdReports(): HasMany
    {
        return $this->hasMany(Report::class, 'created_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function customerAssignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class, 'employee_id');
    }

    public function assignedCustomerAssignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class, 'assigned_by');
    }

    public function callTargets(): HasMany
    {
        return $this->hasMany(CallTarget::class, 'user_id');
    }

    public function setCallTargets(): HasMany
    {
        return $this->hasMany(CallTarget::class, 'set_by');
    }
}
