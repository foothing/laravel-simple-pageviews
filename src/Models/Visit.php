<?php namespace Foothing\Laravel\Visits\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model {
    protected $table = "visits";
    protected $fillable = ["session", "ip", "url", "date", "count"];
    public $timestamps = false;
}
