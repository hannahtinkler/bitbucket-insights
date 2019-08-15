<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    const LAST_REFRESH = 'last_refresh';
    const CURRENTLY_REFRESHING = 'currently_refreshing';

    /**
     * @var array
     */
    protected $guarded = [];

    private $formats = [
        self::LAST_REFRESH => 'calendar',
        self::CURRENTLY_REFRESHING => 'boolean',
    ];

    public function set(string $name, $value)
    {
        return $this->updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }

    public function value(string $name)
    {
        $setting = optional($this->whereName($name)->first());

        return $this->parseValue($name, $setting->value);
    }

    public function values()
    {
        return $this->all()->mapWithKeys(function ($setting) {
            return [$setting->name => $this->parseValue($setting->name, $setting->value)];
        });
    }

    private function parseValue(string $name, $value)
    {
        if ($format = $this->formats[$name] ?? null) {
            switch ($format) {
                case 'calendar':
                    $value = strtolower(Carbon::parse($value)->setTimezone('Europe/London')->calendar());
                    break;
                case 'boolean':
                    $value = (bool) $value;
                    break;
            }
        }

        return $value;
    }
}
