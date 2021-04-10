<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Token extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'ip', 'browser', 'token', 'ending_at'];


    public static function getUuid() {
        $token = (string) Str::uuid();
        return (self::where('token', $token)->count() ? self::getUuid() : $token);
    }

    public static function addToken(Request $request, User $user, $stay) {
        $token = self::getUuid();
        $ending = ($stay ? Carbon::now()->addMonths(6) : Carbon::now()->addHours(8));

        $data = [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'browser' => $request->userAgent(),
            'token' => $token,
            'ending_at' => $ending
        ];

        self::create($data);

        return $token;
    }

    public static function securityToken(Request $request) {
        return self::where([
            ['token', '=', $request->bearerToken()],
            ['browser', '=', $request->userAgent()],
            ['ip', '=', $request->ip()],
            ['ending_at', '>',  Carbon::now()],
        ])->first();
        //return ($security ? true : false);
    }
}
