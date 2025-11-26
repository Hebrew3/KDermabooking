<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Deep Cleansing Facial',
                'description' => 'A comprehensive facial treatment that deeply cleanses, exfoliates, and nourishes the skin. This treatment includes steam, extraction, massage, and a hydrating mask to leave your skin feeling refreshed and rejuvenated.',
                'short_description' => 'Deep cleansing facial with hydrating mask and massage',
                'price' => 2500.00,
                'duration' => 60,
                'category' => 'facial',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'tags' => ['facial', 'cleansing', 'hydrating', 'skincare'],
                'meta_title' => 'Deep Cleansing Facial Treatment - Professional Skincare',
                'meta_description' => 'Professional deep cleansing facial treatment for healthy, glowing skin. Book your appointment today.',
            ],
            [
                'name' => 'Skin Consultation',
                'description' => 'Professional skin analysis and personalized treatment plan. Our expert dermatologists will assess your skin condition and recommend the best treatments and skincare routine for your specific needs.',
                'short_description' => 'Professional skin analysis and personalized treatment plan',
                'price' => 1500.00,
                'duration' => 30,
                'category' => 'consultation',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
                'tags' => ['consultation', 'skin analysis', 'dermatology', 'assessment'],
                'meta_title' => 'Professional Skin Consultation - Dermatology Assessment',
                'meta_description' => 'Get a professional skin consultation and personalized treatment plan from our expert dermatologists.',
            ],
            [
                'name' => 'Laser Hair Removal',
                'description' => 'Advanced laser technology for permanent hair reduction. Safe and effective treatment for all skin types. Multiple sessions required for optimal results.',
                'short_description' => 'Advanced laser therapy for permanent hair reduction',
                'price' => 3500.00,
                'duration' => 45,
                'category' => 'laser',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'tags' => ['laser', 'hair removal', 'permanent', 'advanced'],
                'meta_title' => 'Laser Hair Removal - Permanent Hair Reduction Treatment',
                'meta_description' => 'Safe and effective laser hair removal treatment for permanent hair reduction. Book your session today.',
            ],
            [
                'name' => 'Chemical Peel',
                'description' => 'Professional chemical peel treatment for skin renewal and rejuvenation. Removes dead skin cells, reduces fine lines, and improves skin texture and tone.',
                'short_description' => 'Professional chemical peel for skin renewal',
                'price' => 3000.00,
                'duration' => 50,
                'category' => 'peel',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
                'tags' => ['chemical peel', 'skin renewal', 'rejuvenation', 'exfoliation'],
                'meta_title' => 'Chemical Peel Treatment - Skin Renewal & Rejuvenation',
                'meta_description' => 'Professional chemical peel treatment for skin renewal, reducing fine lines and improving texture.',
            ],
            [
                'name' => 'Microdermabrasion',
                'description' => 'Gentle exfoliation treatment that removes dead skin cells and promotes cell renewal. Perfect for improving skin texture, reducing fine lines, and achieving smoother, more radiant skin.',
                'short_description' => 'Gentle exfoliation for smoother, radiant skin',
                'price' => 2000.00,
                'duration' => 40,
                'category' => 'facial',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'tags' => ['microdermabrasion', 'exfoliation', 'radiant skin', 'gentle'],
                'meta_title' => 'Microdermabrasion Treatment - Gentle Skin Exfoliation',
                'meta_description' => 'Gentle microdermabrasion treatment for smoother, more radiant skin. Book your appointment now.',
            ],
            [
                'name' => 'Botox Treatment',
                'description' => 'Anti-aging botox injection treatment to reduce wrinkles and fine lines. Safe and effective treatment performed by certified professionals.',
                'short_description' => 'Anti-aging botox injection treatment',
                'price' => 8000.00,
                'duration' => 30,
                'category' => 'injection',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 6,
                'tags' => ['botox', 'anti-aging', 'injection', 'wrinkles'],
                'meta_title' => 'Botox Treatment - Anti-Aging Injection Therapy',
                'meta_description' => 'Professional botox treatment for reducing wrinkles and fine lines. Safe and effective anti-aging solution.',
            ],
            [
                'name' => 'HydraFacial Treatment',
                'description' => 'Advanced hydra facial treatment that cleanses, extracts, and hydrates the skin. Uses patented Vortex-Fusion technology for immediate results.',
                'short_description' => 'Advanced hydra facial with immediate results',
                'price' => 4000.00,
                'duration' => 45,
                'category' => 'facial',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 7,
                'tags' => ['hydrafacial', 'advanced', 'hydrating', 'immediate results'],
                'meta_title' => 'HydraFacial Treatment - Advanced Skin Hydration',
                'meta_description' => 'Advanced hydra facial treatment for immediate skin hydration and rejuvenation. Book your session today.',
            ],
            [
                'name' => 'Dermaplaning',
                'description' => 'Gentle exfoliation treatment that removes dead skin cells and fine facial hair using a surgical blade. Results in smoother, brighter skin.',
                'short_description' => 'Gentle exfoliation with surgical blade for smooth skin',
                'price' => 2200.00,
                'duration' => 35,
                'category' => 'facial',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
                'tags' => ['dermaplaning', 'exfoliation', 'smooth skin', 'brightening'],
                'meta_title' => 'Dermaplaning Treatment - Gentle Skin Exfoliation',
                'meta_description' => 'Professional dermaplaning treatment for smoother, brighter skin. Safe and effective exfoliation.',
            ],
            [
                'name' => 'Gluta',
                'description' => 'Glutathione injection treatment for skin whitening and brightening. Safe and effective treatment for achieving a more even skin tone.',
                'short_description' => 'Glutathione injection for skin whitening and brightening',
                'price' => 3000.00,
                'duration' => 30,
                'category' => 'injection',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 9,
                'tags' => ['gluta', 'glutathione', 'whitening', 'brightening', 'injection'],
                'meta_title' => 'Glutathione Injection - Skin Whitening Treatment',
                'meta_description' => 'Professional glutathione injection treatment for skin whitening and brightening. Safe and effective.',
            ],
        ];

        foreach ($services as $serviceData) {
            Service::create($serviceData);
        }
    }
}
