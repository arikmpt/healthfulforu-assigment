<?php

namespace Modules\Content\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Content\Models\Content;
use Modules\Content\Models\Topic;
use Modules\Auth\Models\User;
use Carbon\Carbon;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Get an admin or editor user as author
        $author = User::role(['admin', 'editor'])->first();
        if (!$author) {
            $author = User::first();
        }

        if (!$author) {
            $this->command->warn('No users found. Please seed users first.');
            return;
        }

        // Get topics for content association
        $topics = Topic::where('type', 'topic')->get();
        $conditions = Topic::where('type', 'condition')->get();

        // Create articles
        $articles = [
            [
                'title' => '10 Simple Ways to Improve Your Diet Today',
                'summary' => 'Discover easy-to-implement dietary changes that can transform your health.',
                'body' => $this->getArticleBody('nutrition'),
                'type' => 'article',
                'access_level' => 'free',
                'read_time_minutes' => 5,
                'topic_ids' => $topics->where('name', 'Healthy Eating')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Understanding Macronutrients: Carbs, Proteins, and Fats',
                'summary' => 'A comprehensive guide to the three essential macronutrients and their role in your body.',
                'body' => $this->getArticleBody('nutrition'),
                'type' => 'article',
                'access_level' => 'premium',
                'read_time_minutes' => 8,
                'topic_ids' => $topics->where('name', 'Healthy Eating')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Beginner\'s Guide to Cardio Workouts',
                'summary' => 'Start your fitness journey with these simple cardio exercises.',
                'body' => $this->getArticleBody('fitness'),
                'type' => 'article',
                'access_level' => 'free',
                'read_time_minutes' => 6,
                'topic_ids' => $topics->where('name', 'Cardio Exercises')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Advanced Strength Training Techniques',
                'summary' => 'Take your strength training to the next level with these expert techniques.',
                'body' => $this->getArticleBody('fitness'),
                'type' => 'article',
                'access_level' => 'premium',
                'read_time_minutes' => 10,
                'topic_ids' => $topics->where('name', 'Strength Training')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Stress Management: Practical Strategies for Daily Life',
                'summary' => 'Learn effective techniques to reduce stress and improve your mental well-being.',
                'body' => $this->getArticleBody('mental-health'),
                'type' => 'article',
                'access_level' => 'free',
                'read_time_minutes' => 7,
                'topic_ids' => $topics->where('name', 'Stress Management')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Deep Dive: The Science of Mindfulness Meditation',
                'summary' => 'Explore the scientific research behind mindfulness and its profound effects on the brain.',
                'body' => $this->getArticleBody('mental-health'),
                'type' => 'article',
                'access_level' => 'premium',
                'read_time_minutes' => 12,
                'topic_ids' => $topics->where('name', 'Mindfulness & Meditation')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Living with Diabetes: A Complete Guide',
                'summary' => 'Everything you need to know about managing diabetes effectively.',
                'body' => $this->getArticleBody('diabetes'),
                'type' => 'article',
                'access_level' => 'premium',
                'read_time_minutes' => 15,
                'topic_ids' => $conditions->where('name', 'Diabetes')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Heart Health: Prevention Tips Everyone Should Know',
                'summary' => 'Simple lifestyle changes to protect your cardiovascular health.',
                'body' => $this->getArticleBody('heart'),
                'type' => 'article',
                'access_level' => 'free',
                'read_time_minutes' => 8,
                'topic_ids' => $conditions->where('name', 'Heart Disease')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Sleep Better: A Guide to Improving Your Sleep Quality',
                'summary' => 'Discover proven methods to enhance your sleep and wake up refreshed.',
                'body' => $this->getArticleBody('sleep'),
                'type' => 'article',
                'access_level' => 'free',
                'read_time_minutes' => 6,
                'topic_ids' => $topics->where('name', 'Sleep Hygiene')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Meal Prep Masterclass: Save Time and Eat Healthy',
                'summary' => 'Learn how to prepare nutritious meals for the entire week in just a few hours.',
                'body' => $this->getArticleBody('nutrition'),
                'type' => 'article',
                'access_level' => 'premium',
                'read_time_minutes' => 10,
                'topic_ids' => $topics->where('name', 'Meal Planning')->pluck('id')->toArray(),
            ],
        ];

        // Create videos
        $videos = [
            [
                'title' => '15-Minute Morning Yoga Routine',
                'summary' => 'Start your day with this energizing yoga sequence for beginners.',
                'video_url' => 'https://www.youtube.com/watch?v=sample1',
                'type' => 'video',
                'access_level' => 'free',
                'duration_minutes' => 15,
                'topic_ids' => $topics->where('name', 'Yoga & Flexibility')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Advanced HIIT Workout for Fat Loss',
                'summary' => 'Intense interval training to maximize calorie burn and improve fitness.',
                'video_url' => 'https://www.youtube.com/watch?v=sample2',
                'type' => 'video',
                'access_level' => 'premium',
                'duration_minutes' => 30,
                'topic_ids' => $topics->where('name', 'Cardio Exercises')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Guided Meditation for Better Sleep',
                'summary' => 'Relax your mind and body with this calming bedtime meditation.',
                'video_url' => 'https://www.youtube.com/watch?v=sample3',
                'type' => 'video',
                'access_level' => 'free',
                'duration_minutes' => 20,
                'topic_ids' => $topics->where('name', 'Mindfulness & Meditation')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Full Body Strength Training Workout',
                'summary' => 'Complete strength training routine targeting all major muscle groups.',
                'video_url' => 'https://www.youtube.com/watch?v=sample4',
                'type' => 'video',
                'access_level' => 'premium',
                'duration_minutes' => 45,
                'topic_ids' => $topics->where('name', 'Strength Training')->pluck('id')->toArray(),
            ],
            [
                'title' => 'Healthy Meal Prep Tutorial: 5 Recipes',
                'summary' => 'Follow along as we prepare five nutritious meals for the week ahead.',
                'video_url' => 'https://www.youtube.com/watch?v=sample5',
                'type' => 'video',
                'access_level' => 'premium',
                'duration_minutes' => 25,
                'topic_ids' => $topics->where('name', 'Meal Planning')->pluck('id')->toArray(),
            ],
        ];

        // Create article content
        foreach ($articles as $index => $articleData) {
            $topicIds = $articleData['topic_ids'];
            unset($articleData['topic_ids']);

            $content = Content::create([
                ...$articleData,
                'author_id' => $author->id,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'thumbnail_url' => 'https://via.placeholder.com/800x450/4CAF50/FFFFFF?text=Article+' . ($index + 1),
                'views_count' => rand(100, 5000),
                'likes_count' => rand(10, 500),
                'shares_count' => rand(5, 100),
                'bookmarks_count' => rand(20, 300),
            ]);

            // Attach topics
            if (!empty($topicIds)) {
                $content->topics()->attach($topicIds[0], ['is_primary' => true]);
            }
        }

        // Create video content
        foreach ($videos as $index => $videoData) {
            $topicIds = $videoData['topic_ids'];
            unset($videoData['topic_ids']);

            $content = Content::create([
                ...$videoData,
                'author_id' => $author->id,
                'status' => 'published',
                'published_at' => Carbon::now()->subDays(rand(1, 30)),
                'thumbnail_url' => 'https://via.placeholder.com/800x450/2196F3/FFFFFF?text=Video+' . ($index + 1),
                'views_count' => rand(200, 10000),
                'likes_count' => rand(20, 800),
                'shares_count' => rand(10, 200),
                'bookmarks_count' => rand(30, 400),
            ]);

            // Attach topics
            if (!empty($topicIds)) {
                $content->topics()->attach($topicIds[0], ['is_primary' => true]);
            }
        }

        $this->command->info('Content seeded successfully: ' . (count($articles) + count($videos)) . ' items created.');
    }

    private function getArticleBody(string $type): string
    {
        $bodies = [
            'nutrition' => "# Introduction\n\nProper nutrition is the foundation of good health. In this comprehensive guide, we'll explore the essential principles of healthy eating and how to apply them in your daily life.\n\n## The Basics of Nutrition\n\nA balanced diet includes a variety of foods from all food groups. Understanding macronutrients and micronutrients is crucial for making informed dietary choices.\n\n## Practical Tips\n\n1. **Eat a rainbow of colors**: Different colored fruits and vegetables provide different nutrients.\n2. **Stay hydrated**: Drink plenty of water throughout the day.\n3. **Watch portion sizes**: Even healthy foods can lead to weight gain if consumed in excess.\n4. **Plan ahead**: Meal planning helps you make healthier choices and save time.\n\n## Conclusion\n\nSmall, consistent changes in your diet can lead to significant improvements in your overall health and well-being.",

            'fitness' => "# Getting Started with Fitness\n\nRegular physical activity is one of the most important things you can do for your health. This guide will help you begin your fitness journey safely and effectively.\n\n## Benefits of Regular Exercise\n\n- Improved cardiovascular health\n- Stronger muscles and bones\n- Better mental health\n- Weight management\n- Increased energy levels\n\n## Creating Your Workout Plan\n\nA well-rounded fitness program includes:\n1. Cardiovascular exercise\n2. Strength training\n3. Flexibility work\n4. Rest and recovery\n\n## Safety First\n\nAlways warm up before exercising and cool down afterwards. Listen to your body and don't push through pain.",

            'mental-health' => "# Understanding Mental Wellness\n\nMental health is just as important as physical health. This article explores practical strategies for maintaining emotional well-being.\n\n## The Importance of Mental Health\n\nYour mental health affects how you think, feel, and act. It influences how you handle stress, relate to others, and make choices.\n\n## Daily Practices for Mental Wellness\n\n- Practice mindfulness and meditation\n- Maintain social connections\n- Get regular physical exercise\n- Prioritize quality sleep\n- Seek professional help when needed\n\n## Building Resilience\n\nDeveloping emotional resilience helps you cope with life's challenges more effectively.",

            'diabetes' => "# Living Well with Diabetes\n\nDiabetes is a chronic condition that requires ongoing management, but with the right approach, you can live a full and healthy life.\n\n## Understanding Diabetes\n\nDiabetes affects how your body processes blood sugar (glucose). There are two main types: Type 1 and Type 2 diabetes.\n\n## Management Strategies\n\n1. **Blood sugar monitoring**: Regular checking helps you understand how food, activity, and medication affect your levels.\n2. **Healthy eating**: Focus on balanced meals with controlled carbohydrate intake.\n3. **Physical activity**: Regular exercise helps control blood sugar levels.\n4. **Medication adherence**: Take prescribed medications as directed.\n\n## Working with Your Healthcare Team\n\nRegular check-ups and open communication with your doctor are essential for optimal diabetes management.",

            'heart' => "# Protecting Your Heart Health\n\nCardiovascular disease is a leading cause of death, but many cases are preventable through lifestyle modifications.\n\n## Risk Factors\n\n- High blood pressure\n- High cholesterol\n- Smoking\n- Obesity\n- Physical inactivity\n- Poor diet\n\n## Prevention Strategies\n\n1. **Eat a heart-healthy diet**: Focus on fruits, vegetables, whole grains, and lean proteins.\n2. **Exercise regularly**: Aim for at least 150 minutes of moderate activity per week.\n3. **Maintain a healthy weight**: Even modest weight loss can reduce heart disease risk.\n4. **Don't smoke**: Smoking is one of the most significant risk factors for heart disease.\n5. **Manage stress**: Chronic stress can contribute to heart disease.\n\n## Regular Screenings\n\nGet your blood pressure, cholesterol, and blood sugar checked regularly.",

            'sleep' => "# The Science of Better Sleep\n\nQuality sleep is essential for physical and mental health, yet many people struggle to get enough rest.\n\n## Why Sleep Matters\n\nDuring sleep, your body:\n- Repairs tissues\n- Consolidates memories\n- Regulates hormones\n- Strengthens the immune system\n\n## Sleep Hygiene Tips\n\n1. **Stick to a schedule**: Go to bed and wake up at the same time every day.\n2. **Create a bedtime routine**: Wind down with relaxing activities.\n3. **Optimize your environment**: Keep your bedroom cool, dark, and quiet.\n4. **Limit screen time**: Avoid electronic devices before bed.\n5. **Watch what you eat and drink**: Avoid caffeine and heavy meals close to bedtime.\n\n## When to Seek Help\n\nIf you consistently have trouble sleeping, consult a healthcare provider to rule out sleep disorders.",
        ];

        return $bodies[$type] ?? $bodies['nutrition'];
    }
}
