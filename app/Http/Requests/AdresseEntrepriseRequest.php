<?php

namespace App\Http\Requests;

use App\Enum\AdresseEntrepriseTypeEnum;
use App\Models\AdresseEntreprise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AdresseEntrepriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $datas = $validator->getData();
            if (isset($datas['type'])) {
                if ($this->hasAdresseOfType($datas['type'])) {
                    if (is_null($this->adress)) {
                        $validator->errors()->add('type', "L'entreprise a déjà une adresse de ce type");
                    }
                }
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'email' => 'required|email',
            'nom' => 'required|string',
            'adresse' => 'required|string',
            'code_postal' => 'required|string',
            'ville' => 'required|string',
            'tva' => 'required|string',
        ];

        if (is_null($this->adress)) {
            $rules['type'] = 'required|in:'.AdresseEntrepriseTypeEnum::PHYSIQUE->value.','.AdresseEntrepriseTypeEnum::FACTURATION->value;
        }

        return $rules;
    }

    /**
     * @param int $type
     * @return bool
     */
    private function hasAdresseOfType(int $type): bool
    {
        $res = AdresseEntreprise::where('entreprise_id', $this->entreprise->id)->where('type', $type)->count();
        return $res >= 1;
    }
}
