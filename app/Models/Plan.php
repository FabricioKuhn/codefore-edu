<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'price_monthly', 
        'price_yearly', 
        'is_free',
        'limit_classes', 
        'limit_students_per_class', 
        'limit_tasks_per_class', 
        'is_active'
    ];


    // Relacionamento: 1 Plano tem Várias Assinaturas
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // app/Models/Plan.php

public function institutions()
{
    return $this->hasMany(Institution::class);
}
}