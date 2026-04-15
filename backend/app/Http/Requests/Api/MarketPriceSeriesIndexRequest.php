<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 集計済み相場データ一覧取得APIのリクエスト
 */
class MarketPriceSeriesIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前に入力値を補完する
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'floor_area_band' => $this->input('floor_area_band', 'all'),
            'built_year_band' => $this->input('built_year_band', 'all'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'station_id' => ['required', 'integer', 'exists:stations,id'],
            'property_type' => ['required', 'string', Rule::in(['mansion', 'house'])],
            'floor_area_band' => ['required', 'string', Rule::in(['all'])],
            'built_year_band' => ['required', 'string', Rule::in(['all'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'station_id' => '駅ID',
            'property_type' => '物件種別',
            'floor_area_band' => '面積帯',
            'built_year_band' => '築年帯',
        ];
    }
}
