<?php
// database/seeders/PageSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::firstOrCreate(
            ['slug' => 'contact'],
            ['title' => 'Contact', 'content' => '<p>Nos coordonnées / horaires / plan d’accès.</p>']
        );
        Page::firstOrCreate(
            ['slug' => 'mentions-legales'],
            ['title' => 'Mentions légales', 'content' => '<p>Raison sociale, hébergeur, responsable de publication, CNIL/RGPD…</p>']
        );
    }
}
