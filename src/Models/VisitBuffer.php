<?php namespace Foothing\Laravel\Visits\Models;

use Illuminate\Database\Eloquent\Model;

class VisitBuffer extends Model {
    protected $table = "visits_buffer";
    protected $fillable = ["session", "ip", "url", "date", "count"];
    public $timestamps = false;
}
