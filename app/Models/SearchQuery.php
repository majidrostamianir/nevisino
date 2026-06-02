<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchQuery extends Model
{
    protected $fillable = ['query' , 'ip' , 'user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function querySearch($q, $limit = 300)
    {
        $q = self::normalize($q);
        return SearchQuery::query()->where('query', 'LIKE', '%' . $q . '%')->orderBy('created_at', 'desc')->limit($limit)->get();
    }
    public static function ipSearch($q, $limit = 300)
    {
        $q = self::normalize($q);
        return SearchQuery::query()->where('ip', 'LIKE', '%' . $q . '%')->orderBy('created_at', 'desc')->limit($limit)->get();
    }
    public static function userSearch($q, $limit = 300)
    {
        $q = self::normalize($q);

        return SearchQuery::query()
            ->whereHas('user', function($query) use ($q) {
                $query->where('name', 'LIKE', '%' . $q . '%');
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
    public static function normalize($query)
    {
        $query = trim($query);

        // تبدیل اعداد فارسی به انگلیسی
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $query = str_replace($persian, $english, $query);

        // حذف فاصله‌های اضافه
        return preg_replace('/\s+/', ' ', $query);
    }
}
