<?php

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Models\Division;
use App\Models\Registration;
use App\Models\RegistrationScreeningAnswer;
use App\Models\ScreeningQuestion;
use Database\Seeders\ScreeningQuestionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_active_divisions(): void
    {
        $activeDivision = Division::factory()->create(['name' => 'Divisi Media', 'is_active' => true]);
        $inactiveDivision = Division::factory()->create(['name' => 'Divisi Lama', 'is_active' => false]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Divisi Media');
        $response->assertDontSee('Divisi Lama');
    }

    public function test_registration_form_displays_active_divisions(): void
    {
        $division = Division::factory()->create(['name' => 'Divisi Humas', 'is_active' => true]);

        $response = $this->get(route('registrations.create'));

        $response->assertStatus(200);
        $response->assertSee('Divisi Humas');
    }

    public function test_user_can_submit_registration_successfully(): void
    {
        $division = Division::factory()->create(['is_active' => true]);

        $payload = [
            'name' => 'Budi Santoso',
            'email' => 'BUDI.santoso@EXAMPLE.com ',
            'phone' => '08123456789',
            'gender' => 'male',
            'birth_date' => '2002-05-15',
            'school' => ' Universitas Indonesia ',
            'address' => 'Jl. Merdeka No. 10',
            'division_id' => $division->id,
            'reason' => 'Ingin menambah pengalaman organisasi dan belajar kepemimpinan.',
            'organization_experience' => 'Ketua OSIS SMA N 1',
            'instagram' => '@budi_santoso',
        ];

        $response = $this->post(route('registrations.store'), $payload);

        $registration = Registration::first();

        $this->assertNotNull($registration);
        $this->assertStringStartsWith('REG-'.now()->year.'-', $registration->registration_number);
        $this->assertEquals('Budi Santoso', $registration->name);
        $this->assertEquals('budi.santoso@example.com', $registration->email);
        $this->assertEquals('08123456789', $registration->phone);
        $this->assertEquals('Universitas Indonesia', $registration->school);
        $this->assertEquals('budi_santoso', $registration->instagram);
        $response->assertRedirect(route('registrations.basic-question', $registration));
    }

    public function test_screening_question_seeder_creates_common_and_division_questions(): void
    {
        $division = Division::factory()->create(['name' => 'Divisi Teknologi', 'is_active' => true]);

        $this->seed(ScreeningQuestionSeeder::class);

        $questions = ScreeningQuestion::query()
            ->where(fn ($query) => $query->whereNull('division_id')->orWhere('division_id', $division->id))
            ->orderBy('sort_order')
            ->pluck('question_text')
            ->all();

        $this->assertCount(5, $questions);
        $this->assertContains('Dari mana kamu mengetahui organisasi ini?', $questions);
        $this->assertContains('Kenapa kamu ingin bergabung dengan organisasi ini?', $questions);
        $this->assertContains('Kenapa kamu layak menjadi bagian dari organisasi ini?', $questions);
        $this->assertContains('Apa yang kamu ketahui tentang tugas utama Divisi Teknologi?', $questions);
        $this->assertContains('Kegiatan sederhana apa yang ingin kamu bantu lakukan di Divisi Teknologi?', $questions);
    }

    public function test_new_division_gets_default_division_screening_questions(): void
    {
        $division = Division::factory()->create(['name' => 'Kreatif', 'is_active' => true]);

        $questions = ScreeningQuestion::query()
            ->where('division_id', $division->id)
            ->orderBy('sort_order')
            ->pluck('question_text')
            ->all();

        $this->assertSame([
            'Apa yang kamu ketahui tentang tugas utama Divisi Kreatif?',
            'Kegiatan sederhana apa yang ingin kamu bantu lakukan di Divisi Kreatif?',
        ], $questions);
    }

    public function test_renamed_division_updates_default_division_screening_questions(): void
    {
        $division = Division::factory()->create(['name' => 'Kreatif', 'is_active' => true]);

        $division->update(['name' => 'Media']);

        $questions = ScreeningQuestion::query()
            ->where('division_id', $division->id)
            ->orderBy('sort_order')
            ->pluck('question_text')
            ->all();

        $this->assertSame([
            'Apa yang kamu ketahui tentang tugas utama Divisi Media?',
            'Kegiatan sederhana apa yang ingin kamu bantu lakukan di Divisi Media?',
        ], $questions);
    }

    public function test_user_can_answer_basic_screening_questions(): void
    {
        $division = Division::factory()->create(['name' => 'IT', 'is_active' => true]);
        $registration = Registration::factory()->create(['division_id' => $division->id]);

        $this->seed(ScreeningQuestionSeeder::class);

        $questions = ScreeningQuestion::query()
            ->where(fn ($query) => $query->whereNull('division_id')->orWhere('division_id', $division->id))
            ->orderBy('sort_order')
            ->get();

        $response = $this->get(route('registrations.basic-question', $registration));

        $response->assertStatus(200);
        $questions->each(fn (ScreeningQuestion $question) => $response->assertSee($question->question_text));

        $payload = [
            'answers' => $questions
                ->mapWithKeys(fn (ScreeningQuestion $question): array => [
                    $question->id => 'Jawaban singkat untuk '.$question->id,
                ])
                ->all(),
        ];

        $response = $this->post(route('registrations.basic-question.submit', $registration), $payload);

        $response->assertRedirect(route('registrations.status', [
            'registration_number' => $registration->registration_number,
            'email' => $registration->email,
        ]));

        $registration->refresh();

        $this->assertTrue($registration->hasCompletedBasicQuestion());
        $this->assertEquals(5, RegistrationScreeningAnswer::query()->where('registration_id', $registration->id)->count());
        $this->assertStringContainsString('Dari mana kamu mengetahui organisasi ini?', $registration->basic_question_answer);
    }

    public function test_registration_number_sequence_increments(): void
    {
        $division = Division::factory()->create(['is_active' => true]);
        $year = now()->year;

        $reg1 = Registration::factory()->create(['division_id' => $division->id]);
        $reg2 = Registration::factory()->create(['division_id' => $division->id]);

        $this->assertEquals("REG-{$year}-0001", $reg1->registration_number);
        $this->assertEquals("REG-{$year}-0002", $reg2->registration_number);
    }

    public function test_registration_fails_validation_with_missing_or_invalid_fields(): void
    {
        $inactiveDivision = Division::factory()->create(['is_active' => false]);

        $response = $this->post(route('registrations.store'), [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => 'abc-not-phone',
            'division_id' => $inactiveDivision->id,
            'reason' => 'Pendek',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'phone',
            'division_id',
            'reason',
        ]);
    }

    public function test_registration_success_page_displays_details(): void
    {
        $division = Division::factory()->create(['name' => 'Divisi IT']);
        $registration = Registration::factory()->create([
            'division_id' => $division->id,
            'name' => 'Siti Rahma',
        ]);

        $response = $this->get(route('registrations.success', $registration));

        $response->assertStatus(200);
        $response->assertSee($registration->registration_number);
        $response->assertSee('Siti Rahma');
        $response->assertSee('Divisi IT');
    }
}
