namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 
        'plan_id', 
        'billing_cycle',
        'price', 
        'status', 
        'expires_at'
    ];

    // Transforma a coluna de data do banco em um objeto de data do PHP (Carbon) automaticamente
    protected $casts = [
        'expires_at' => 'date',
    ];

    // Relacionamento: 1 Assinatura pertence a 1 Plano
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // Relacionamento: 1 Assinatura pertence a 1 Instituição (Escola)
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}