<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\OfficialBook;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // --- Écoles ---
        $school1 = School::create([
            'name' => 'Groupe Scolaire Les Étoiles',
            'city' => 'Abidjan',
            'district' => 'Cocody',
            'is_active' => true,
        ]);

        $school2 = School::create([
            'name' => 'Institution Sainte-Marie',
            'city' => 'Abidjan',
            'district' => 'Marcory',
            'is_active' => true,
        ]);

        // --- Niveaux (Grades) ---
        $grade6A = Grade::create([
            'school_id' => $school1->id,
            'name' => '6ème A',
            'level' => '6ème',
            'academic_year' => '2025-2026',
        ]);

        $grade5B = Grade::create([
            'school_id' => $school1->id,
            'name' => '5ème B',
            'level' => '5ème',
            'academic_year' => '2025-2026',
        ]);

        $grade6SM = Grade::create([
            'school_id' => $school2->id,
            'name' => '6ème',
            'level' => '6ème',
            'academic_year' => '2025-2026',
        ]);

        // --- Matières (Subjects) ---
        $maths = Subject::create(['name' => 'Mathématiques']);
        $francais = Subject::create(['name' => 'Français']);
        $anglais = Subject::create(['name' => 'Anglais']);
        $svt = Subject::create(['name' => 'Sciences de la Vie et de la Terre']);
        $histoire = Subject::create(['name' => 'Histoire-Géographie']);

        // --- Livres officiels ---
        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 6ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $francais->id,
            'title' => 'Lecture et Expression 6ème',
            'author' => 'B. Koné',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $anglais->id,
            'title' => 'English for Africa 6ème',
            'author' => 'J. Adu',
            'publisher' => 'Macmillan',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $svt->id,
            'title' => 'SVT 6ème - Collection AREX',
            'author' => 'A. Touré',
            'publisher' => 'AREX',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6A->id,
            'subject_id' => $histoire->id,
            'title' => 'Histoire-Géo 6ème',
            'author' => 'M. Diallo',
            'publisher' => 'Hatier CI',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade5B->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 5ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade5B->id,
            'subject_id' => $francais->id,
            'title' => 'Lecture et Expression 5ème',
            'author' => 'B. Koné',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $maths->id,
            'title' => 'CIAM Mathématiques 6ème',
            'author' => 'Collection CIAM',
            'publisher' => 'EDICEF',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $francais->id,
            'title' => 'Français 6ème - Sainte-Marie',
            'author' => 'S. Bamba',
            'publisher' => 'NEI-CEDA',
            'is_active' => true,
        ]);

        OfficialBook::create([
            'grade_id' => $grade6SM->id,
            'subject_id' => $anglais->id,
            'title' => 'Go for English 6ème',
            'author' => 'P. Williams',
            'publisher' => 'Macmillan',
            'is_active' => true,
        ]);
    }
}
