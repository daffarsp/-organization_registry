<?php

namespace App\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRegistrationRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other'])],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'school' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:1000'],
            'division_id' => [
                'required',
                'integer',
                Rule::exists('divisions', 'id')->where(fn (Builder $query): Builder => $query->where('is_active', true)),
            ],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'organization_experience' => ['nullable', 'string', 'max:2000'],
            'instagram' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9._]+$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'email',
            'phone' => 'nomor WhatsApp',
            'gender' => 'jenis kelamin',
            'birth_date' => 'tanggal lahir',
            'school' => 'asal sekolah/kampus',
            'address' => 'alamat',
            'division_id' => 'divisi',
            'reason' => 'alasan bergabung',
            'organization_experience' => 'pengalaman organisasi',
            'instagram' => 'Instagram',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda plus, tanda minus, titik, dan kurung.',
            'instagram.regex' => 'Username Instagram hanya boleh berisi huruf, angka, titik, dan underscore.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->cleanString('name'),
            'email' => str($this->input('email', ''))->trim()->lower()->toString(),
            'phone' => $this->cleanString('phone'),
            'school' => $this->cleanString('school'),
            'address' => $this->cleanString('address'),
            'reason' => $this->cleanString('reason'),
            'organization_experience' => $this->cleanString('organization_experience'),
            'instagram' => str($this->input('instagram', ''))->trim()->ltrim('@')->toString() ?: null,
        ]);
    }

    private function cleanString(string $key): ?string
    {
        $value = $this->input($key);

        if ($value === null) {
            return null;
        }

        $cleaned = str((string) $value)
            ->replaceMatches('/\s+/', ' ')
            ->trim()
            ->toString();

        return $cleaned === '' ? null : $cleaned;
    }
}
