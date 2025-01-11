<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Academic Studies',
                'description' => 'Expert tutoring in Mathematics, Sciences, Literature, and preparation for official exams (BEPC, CAP, Probatoire, BAC, GCE O/A Levels). Get help with homework, exam preparation, and understanding complex topics.',
            ],
            [
                'name' => 'Language Learning',
                'description' => 'Master French, English, German, Chinese, and other languages. Get preparation for IELTS/TOEFL, conversation practice, and writing skills. Perfect for both beginners and advanced learners.',
            ],
            [
                'name' => 'Professional Development',
                'description' => 'Enhance your career with business skills, entrepreneurship training, professional certifications, and career development coaching. Learn from experienced professionals in your field.',
            ],
            [
                'name' => 'Technology & Digital Skills',
                'description' => 'Learn programming, web development, digital marketing, graphic design, and other tech skills. From basic computer skills to advanced software development - stay competitive in the digital age.',
            ],
            [
                'name' => 'Competitive Exam Prep',
                'description' => 'Specialized preparation for entrance exams including ENAM, IRIC, ENS, ENSET, Medical School, and other competitive examinations. Get guidance from tutors who understand the system.',
            ],
            [
                'name' => 'Music & Performing Arts',
                'description' => 'Learn musical instruments (piano, guitar, drums), vocal training, dance (traditional and modern), and performing arts. Develop your artistic talents with experienced instructors.',
            ],
            [
                'name' => 'Beauty & Fashion Skills',
                'description' => 'Learn professional hairdressing, makeup artistry, fashion design, and beauty therapy. Perfect for those wanting to enter the beauty and fashion industry or develop professional skills.',
            ],
            [
                'name' => 'Sports & Fitness Training',
                'description' => 'Get professional training in football, fitness, athletics, and traditional sports. Whether you are a beginner or looking to improve your skills, find the right coach for your goals.',
            ],
            [
                'name' => 'Creative Arts & Crafts',
                'description' => 'Explore painting, drawing, sculpture, photography, and traditional crafts. Learn from skilled artists and develop your creative abilities in various artistic mediums.',
            ],
            [
                'name' => 'University & Advanced Studies',
                'description' => 'Get help with university courses, research projects, and advanced academic subjects. Perfect for university students seeking support in their studies or advanced learners pursuing higher education.',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}