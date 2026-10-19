<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Registration;
use App\Models\Speaker;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@kuliahilkom.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone_number' => '081234567890',
                'institution' => 'KuliahIlkom Admin Team',
                'email_verified_at' => now(),
            ]
        );

        $participant = User::firstOrCreate(
            ['email' => 'peserta@gmail.com'],
            [
                'name' => 'Harlen Simanullang',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'phone_number' => '082198765432',
                'institution' => 'Universitas Indonesia',
                'email_verified_at' => now(),
            ]
        );

        $participant2 = User::firstOrCreate(
            ['email' => 'budi.santoso@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'participant',
                'phone_number' => '081345678912',
                'institution' => 'PT Solusi Teknologi Nusantara',
                'email_verified_at' => now(),
            ]
        );

        // 2. Categories
        $categoriesData = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Materi modern framework, Full-Stack PHP Laravel, React, Vue, dan integrasi API.',
                'icon' => 'code-bracket',
            ],
            [
                'name' => 'AI & Data Science',
                'slug' => 'ai-data-science',
                'description' => 'Eksplorasi LLM, Machine Learning, Deep Learning, dan AI Agents.',
                'icon' => 'cpu-chip',
            ],
            [
                'name' => 'Cloud & DevOps',
                'slug' => 'cloud-devops',
                'description' => 'Arsitektur Cloud AWS/GCP, Docker container, Kubernetes, dan CI/CD automation.',
                'icon' => 'cloud',
            ],
            [
                'name' => 'Cyber Security',
                'slug' => 'cyber-security',
                'description' => 'Keamanan sistem web, OWASP Top 10, penetration testing, dan digital forensics.',
                'icon' => 'shield-check',
            ],
            [
                'name' => 'UI/UX & Product Design',
                'slug' => 'ui-ux-product-design',
                'description' => 'Perancangan desain sistem, user research, wireframing, dan prototipe di Figma.',
                'icon' => 'sparkles',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['slug']] = Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 3. Speakers
        $speakersData = [
            [
                'name' => 'Sandhika Galih',
                'title_role' => 'Dosen & Founder Web Programming UNPAS',
                'institution' => 'WPU Media & Tech Educator',
                'bio' => 'Praktisi web development dan edukator teknologi terkemuka di Indonesia dengan lebih dari 10 tahun pengalaman mendidik ratusan ribu calon developer.',
                'photo_url' => 'speakers/photos/sandhika-galih.jpg',
                'linkedin_url' => 'https://linkedin.com',
            ],
            [
                'name' => 'Eko Kurniawan Khannedy',
                'title_role' => 'Technical Architect & Founder PZN',
                'institution' => 'Blibli.com / Programmer Zaman Now',
                'bio' => 'Senior Technical Architect dengan spesialisasi distributed systems, high concurrency microservices, dan software engineering best practices.',
                'photo_url' => 'speakers/photos/eko-khannedy.jpg',
                'linkedin_url' => 'https://linkedin.com',
            ],
            [
                'name' => 'Rahmat Hidayat, M.Kom',
                'title_role' => 'Lead AI Research Engineer',
                'institution' => 'GoTo Financial & AI Lab',
                'bio' => 'Peneliti dan engineer di bidang Natural Language Processing, Autonomous Agents, dan penerapan Generative AI di sektor perbankan dan fintech.',
                'photo_url' => 'speakers/photos/rahmat-hidayat.jpg',
                'linkedin_url' => 'https://linkedin.com',
            ],
            [
                'name' => 'Fathur Rahman',
                'title_role' => 'Principal Cloud & DevOps Architect',
                'institution' => 'AWS Community Hero Indonesia',
                'bio' => 'Spesialis infrastruktur cloud berskala besar, multi-cloud management, Kubernetes orchestration, dan observability.',
                'photo_url' => 'speakers/photos/fathur-rahman.jpg',
                'linkedin_url' => 'https://linkedin.com',
            ],
        ];

        $speakers = [];
        foreach ($speakersData as $spk) {
            $speakers[] = Speaker::firstOrCreate(['name' => $spk['name']], $spk);
        }

        // 4. Workshops
        $workshopsData = [
            [
                'title' => 'Mastering Laravel 12 & Livewire 3: Bangun Web App Interaktif Skala Enterprise',
                'slug' => 'mastering-laravel-12-livewire-3',
                'category_id' => $categories['web-development']->id,
                'speaker_id' => $speakers[0]->id,
                'type' => 'workshop',
                'short_description' => 'Pelajari arsitektur SPA modern tanpa JavaScript framework kompleks menggunakan TALL Stack (Tailwind, Alpine, Laravel, Livewire 3).',
                'description' => "## Tentang Workshop Ini\n\nDalam workshop hands-on intensif ini, Anda akan dibimbing langsung oleh instruktur berpengalaman untuk membangun aplikasi web reaktif dari nol.\n\n### Yang Akan Anda Pelajari:\n- Komponen Reaktif Livewire 3: Deep Dive Lifecycle & Alpine Integration\n- Arsitektur Database MySQL yang scalable dengan Eloquent Relations & Indexing\n- Integrasi Pembayaran Midtrans Snap & Webhook Security\n- State Management, File Uploads, Form Validation, dan Real-time Event Dispatcher\n- Best Practices Deployment ke Production\n\n### Benefit Peserta:\n- E-Sertifikat Resmi ber-QR Code Verifikasi\n- Rekaman Full HD & Bahan Tayang Lengkap\n- Full Source Code Project Siap Pakai\n- Akses Grup Diskusi Eksklusif di Telegram",
                'banner_path' => 'workshops/banners/laravel-workshop.jpg',
                'start_date' => now()->addDays(5)->setHour(19)->setMinute(0),
                'end_date' => now()->addDays(5)->setHour(22)->setMinute(0),
                'timezone' => 'WIB',
                'platform' => 'zoom',
                'meeting_url' => 'https://zoom.us/j/84291823901?pwd=KuliahIlkomWorkshop2026',
                'meeting_password' => 'Ilkom2026',
                'is_paid' => true,
                'price' => 149000,
                'original_price' => 299000,
                'quota' => 50,
                'remaining_quota' => 42,
                'status' => 'published',
                'certificate_enabled' => true,
            ],
            [
                'title' => 'Deep Dive AI & LLM Agents: Membangun Autonomous Workflow untuk Developer',
                'slug' => 'deep-dive-ai-llm-agents-autonomous-workflow',
                'category_id' => $categories['ai-data-science']->id,
                'speaker_id' => $speakers[2]->id,
                'type' => 'webinar',
                'short_description' => 'Bongkar arsitektur AI Agents, Function Calling, dan integrasi Large Language Models ke dalam ekosistem aplikasi bisnis.',
                'description' => "## Eksplorasi Masa Depan Rekayasa Perangkat Lunak dengan AI\n\nWebinar komprehensif ini mengupas tuntas cara memanfaatkan LLM API untuk mengotomatisasi tugas-tugas kompleks dalam sistem backend.\n\n### Materi Utama:\n1. Pengantar Agen Otonom (Autonomous Agent Frameworks)\n2. Tool Calling, RAG (Retrieval-Augmented Generation) & Vector Database\n3. Studi Kasus Nyata: Pembuatan Asisten Analis Data Otomatis\n4. Praktik Terbaik Mengontrol Token Cost dan Latency pada Skala Produksi",
                'banner_path' => 'workshops/banners/ai-webinar.jpg',
                'start_date' => now()->addDays(2)->setHour(14)->setMinute(0),
                'end_date' => now()->addDays(2)->setHour(16)->setMinute(30),
                'timezone' => 'WIB',
                'platform' => 'zoom',
                'meeting_url' => 'https://zoom.us/j/91238472910?pwd=WebinarAiGratis2026',
                'meeting_password' => 'AIAgents2026',
                'is_paid' => false,
                'price' => 0,
                'original_price' => null,
                'quota' => 250,
                'remaining_quota' => 189,
                'status' => 'published',
                'certificate_enabled' => true,
            ],
            [
                'title' => 'DevOps Modern: Automasi CI/CD Pipeline dengan Docker & Kubernetes',
                'slug' => 'devops-modern-docker-kubernetes-cicd',
                'category_id' => $categories['cloud-devops']->id,
                'speaker_id' => $speakers[3]->id,
                'type' => 'bootcamp',
                'short_description' => 'Kuasai standar industri deployment containerized application dari staging hingga zero-downtime production release.',
                'description' => "## Bootcamp Intensif Cloud & DevOps Engineering\n\nSiapkan keahlian Anda untuk standard industri tech company masa kini. Bootcamp 3 hari berfokus pada hands-on praktik menggunakan platform cloud terkini.",
                'banner_path' => 'workshops/banners/devops-bootcamp.jpg',
                'start_date' => now()->addDays(10)->setHour(9)->setMinute(0),
                'end_date' => now()->addDays(12)->setHour(17)->setMinute(0),
                'timezone' => 'WIB',
                'platform' => 'gmeet',
                'meeting_url' => 'https://meet.google.com/xyz-devops-bootcamp',
                'meeting_password' => null,
                'is_paid' => true,
                'price' => 299000,
                'original_price' => 599000,
                'quota' => 30,
                'remaining_quota' => 18,
                'status' => 'published',
                'certificate_enabled' => true,
            ],
            [
                'title' => 'Cyber Security Fundamentals: OWASP Top 10 & Web Penetration Testing',
                'slug' => 'cyber-security-fundamentals-owasp-top-10',
                'category_id' => $categories['cyber-security']->id,
                'speaker_id' => $speakers[1]->id,
                'type' => 'webinar',
                'short_description' => 'Mengenal teknik eksploitasi celah keamanan web umum seperti SQLi, XSS, CSRF, dan cara mitigasinya di backend modern.',
                'description' => "## Tingkatkan Pertahanan Aplikasi Anda\n\nMemahami bagaimana hacker menyerang adalah langkah pertama untuk membangun sistem yang aman.",
                'banner_path' => 'workshops/banners/cybersecurity-webinar.jpg',
                'start_date' => now()->addDays(14)->setHour(19)->setMinute(30),
                'end_date' => now()->addDays(14)->setHour(21)->setMinute(30),
                'timezone' => 'WIB',
                'platform' => 'zoom',
                'meeting_url' => 'https://zoom.us/j/9922334455?pwd=Security2026',
                'meeting_password' => 'SecurePass',
                'is_paid' => true,
                'price' => 75000,
                'original_price' => 150000,
                'quota' => 100,
                'remaining_quota' => 84,
                'status' => 'published',
                'certificate_enabled' => true,
            ],
        ];

        $workshops = [];
        foreach ($workshopsData as $wData) {
            $workshops[] = Workshop::firstOrCreate(['slug' => $wData['slug']], $wData);
        }

        // 5. Create Sample Order & Registration for test participant (peserta@gmail.com)
        $sampleWorkshop = $workshops[0];
        $order = Order::firstOrCreate(
            ['order_code' => 'INV-WK-20261019-00101'],
            [
                'user_id' => $participant->id,
                'workshop_id' => $sampleWorkshop->id,
                'amount' => $sampleWorkshop->price,
                'status' => 'paid',
                'payment_type' => 'qris',
                'snap_token' => 'dummy-snap-token-12345',
                'midtrans_transaction_id' => 'mid-txn-'.Str::random(12),
                'paid_at' => now()->subDay(),
            ]
        );

        Registration::firstOrCreate(
            ['user_id' => $participant->id, 'workshop_id' => $sampleWorkshop->id],
            [
                'order_id' => $order->id,
                'ticket_code' => 'TKT-2610-LARAVEL12',
                'attendance_status' => 'not_attended',
                'notes' => 'Pendaftaran via simulasi awal',
            ]
        );

        // Also enroll participant to the Free AI webinar
        $freeWorkshop = $workshops[1];
        Registration::firstOrCreate(
            ['user_id' => $participant->id, 'workshop_id' => $freeWorkshop->id],
            [
                'order_id' => null,
                'ticket_code' => 'TKT-2610-AIFREE2026',
                'attendance_status' => 'not_attended',
                'notes' => 'Pendaftaran webinar gratis langsung aktif',
            ]
        );
    }
}
