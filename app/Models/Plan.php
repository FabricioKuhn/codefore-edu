namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    // Liberamos quais campos podem ser salvos direto no banco
    protected $fillable = [
        'name', 
        'monthly_price', 
        'annual_price',
        'teacher_limit', 
        'classroom_limit', 
        'student_limit', 
        'task_limit',
        'is_active'
    ];

    // Relacionamento: 1 Plano tem Várias Assinaturas
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}