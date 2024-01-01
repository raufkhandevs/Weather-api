<?php

namespace App\Http\Requests;

use App\Models\State;
use App\Traits\CacheAble;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class WeatherRequest extends FormRequest
{
    use CacheAble;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $statesCodeAndNames = $this->supportedStates();

        return [
            'city'  => 'required|string',
            'state' => 'required|string|in:' . implode(',', $statesCodeAndNames),
        ];
    }

    /**
     * cache supported states
     *
     * @return array
     */
    private function supportedStates(): array
    {
        // Cache for 24 hours (1440 minutes)
        $minutes = 24 * 60;
        $supportedStates = self::remember('supported_states', function () {
            return State::select('name', 'code')
                ->whereHas('country', function ($query) {
                    $query->where('is_supported', true);
                })
                ->get();
        }, $minutes);

        // combine state name and code - e.g., CA or California
        return $supportedStates->pluck('name')->concat($supportedStates->pluck('code'))->toArray();
    }
}
