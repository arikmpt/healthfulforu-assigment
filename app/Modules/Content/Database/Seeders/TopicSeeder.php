<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Content\Models\Topic;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Nutrition',
                'slug' => 'nutrition',
                'description' => 'Explore healthy eating habits, meal planning, and nutritional information',
                'type' => 'category',
                'icon_url' => 'https://via.placeholder.com/100/4CAF50/FFFFFF?text=N',
                'sort_order' => 1,
                'topics' => [
                    ['name' => 'Healthy Eating', 'description' => 'Guidelines for balanced diet and nutrition'],
                    ['name' => 'Meal Planning', 'description' => 'Tips for planning nutritious meals'],
                    ['name' => 'Vitamins & Minerals', 'description' => 'Essential nutrients and their benefits'],
                    ['name' => 'Weight Management', 'description' => 'Healthy approaches to weight control'],
                ],
            ],
            [
                'name' => 'Fitness',
                'slug' => 'fitness',
                'description' => 'Physical activity, exercise routines, and fitness tips',
                'type' => 'category',
                'icon_url' => 'https://via.placeholder.com/100/2196F3/FFFFFF?text=F',
                'sort_order' => 2,
                'topics' => [
                    ['name' => 'Cardio Exercises', 'description' => 'Heart-healthy aerobic activities'],
                    ['name' => 'Strength Training', 'description' => 'Building muscle and bone strength'],
                    ['name' => 'Yoga & Flexibility', 'description' => 'Mind-body exercises and stretching'],
                    ['name' => 'Sports & Recreation', 'description' => 'Fun physical activities and sports'],
                ],
            ],
            [
                'name' => 'Mental Health',
                'slug' => 'mental-health',
                'description' => 'Emotional well-being, stress management, and mental wellness',
                'type' => 'category',
                'icon_url' => 'https://via.placeholder.com/100/9C27B0/FFFFFF?text=M',
                'sort_order' => 3,
                'topics' => [
                    ['name' => 'Stress Management', 'description' => 'Techniques to reduce and cope with stress'],
                    ['name' => 'Mindfulness & Meditation', 'description' => 'Practices for mental clarity and peace'],
                    ['name' => 'Sleep Hygiene', 'description' => 'Healthy sleep habits and routines'],
                    ['name' => 'Emotional Wellness', 'description' => 'Understanding and managing emotions'],
                ],
            ],
            [
                'name' => 'Disease Prevention',
                'slug' => 'disease-prevention',
                'description' => 'Preventive health measures and disease awareness',
                'type' => 'category',
                'icon_url' => 'https://via.placeholder.com/100/FF5722/FFFFFF?text=D',
                'sort_order' => 4,
                'topics' => [
                    ['name' => 'Vaccinations', 'description' => 'Immunization schedules and benefits'],
                    ['name' => 'Regular Check-ups', 'description' => 'Importance of preventive health screenings'],
                    ['name' => 'Chronic Disease Management', 'description' => 'Living with and managing chronic conditions'],
                    ['name' => 'Lifestyle Diseases', 'description' => 'Preventing lifestyle-related health issues'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $topics = $categoryData['topics'];
            unset($categoryData['topics']);

            $category = Topic::create($categoryData);

            foreach ($topics as $index => $topicData) {
                Topic::create([
                    'name' => $topicData['name'],
                    'slug' => \Illuminate\Support\Str::slug($topicData['name']),
                    'description' => $topicData['description'],
                    'type' => 'topic',
                    'parent_id' => $category->id,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }

        // Create health conditions
        $conditions = [
            ['name' => 'Diabetes', 'description' => 'Managing blood sugar and diabetes care'],
            ['name' => 'Hypertension', 'description' => 'Understanding and controlling high blood pressure'],
            ['name' => 'Obesity', 'description' => 'Addressing weight-related health concerns'],
            ['name' => 'Heart Disease', 'description' => 'Cardiovascular health and prevention'],
            ['name' => 'Asthma', 'description' => 'Managing respiratory conditions'],
        ];

        foreach ($conditions as $index => $conditionData) {
            Topic::create([
                'name' => $conditionData['name'],
                'slug' => \Illuminate\Support\Str::slug($conditionData['name']),
                'description' => $conditionData['description'],
                'type' => 'condition',
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }
    }
}
