<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadSource;
use App\Models\User;

class LeadSourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user or create a default user for seeding
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('No user found. Please create a user first or run UserSeeder.');
            return;
        }

        $leadSources = [
            [
                'title' => 'Facebook',
                'description' => 'Leads from Facebook page, posts, and direct messages',
                'is_active' => true,
            ],
            [
                'title' => 'Facebook Ads',
                'description' => 'Leads from Facebook advertising campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Instagram',
                'description' => 'Leads from Instagram posts, stories, and direct messages',
                'is_active' => true,
            ],
            [
                'title' => 'Instagram Ads',
                'description' => 'Leads from Instagram advertising campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'WhatsApp',
                'description' => 'Leads from WhatsApp messages and business account',
                'is_active' => true,
            ],
            [
                'title' => 'IMO',
                'description' => 'Leads from IMO messages and calls',
                'is_active' => true,
            ],
            [
                'title' => 'LinkedIn',
                'description' => 'Leads from LinkedIn posts, messages, and connections',
                'is_active' => true,
            ],
            [
                'title' => 'LinkedIn Ads',
                'description' => 'Leads from LinkedIn advertising campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Twitter/X',
                'description' => 'Leads from Twitter/X posts and direct messages',
                'is_active' => true,
            ],
            [
                'title' => 'YouTube',
                'description' => 'Leads from YouTube videos, comments, and channel',
                'is_active' => true,
            ],
            [
                'title' => 'YouTube Ads',
                'description' => 'Leads from YouTube advertising campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'TikTok',
                'description' => 'Leads from TikTok videos and direct messages',
                'is_active' => true,
            ],
            [
                'title' => 'TikTok Ads',
                'description' => 'Leads from TikTok advertising campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Telegram',
                'description' => 'Leads from Telegram messages and channels',
                'is_active' => true,
            ],
            [
                'title' => 'Snapchat',
                'description' => 'Leads from Snapchat messages and stories',
                'is_active' => true,
            ],
            [
                'title' => 'Pinterest',
                'description' => 'Leads from Pinterest pins and boards',
                'is_active' => true,
            ],
            [
                'title' => 'Google Search',
                'description' => 'Leads from Google search results and Google Ads',
                'is_active' => true,
            ],
            [
                'title' => 'Website',
                'description' => 'Leads from company website contact forms and inquiries',
                'is_active' => true,
            ],
            [
                'title' => 'Referral',
                'description' => 'Leads referred by existing customers or partners',
                'is_active' => true,
            ],
            [
                'title' => 'Phone Call',
                'description' => 'Direct phone inquiries and cold calls',
                'is_active' => true,
            ],
            [
                'title' => 'Walk-in',
                'description' => 'Customers who visit the office or showroom directly',
                'is_active' => true,
            ],
            [
                'title' => 'Email',
                'description' => 'Leads from email inquiries and campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Advertisement',
                'description' => 'Leads from print, TV, radio, or online advertisements',
                'is_active' => true,
            ],
            [
                'title' => 'Partner',
                'description' => 'Leads from business partners and affiliates',
                'is_active' => true,
            ],
            [
                'title' => 'Event',
                'description' => 'Leads from trade shows, exhibitions, and events',
                'is_active' => true,
            ],
            [
                'title' => 'Online Listing',
                'description' => 'Leads from property listing websites and portals',
                'is_active' => true,
            ],
            [
                'title' => 'SMS Campaign',
                'description' => 'Leads from SMS marketing campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Newspaper',
                'description' => 'Leads from newspaper advertisements',
                'is_active' => true,
            ],
            [
                'title' => 'Billboard',
                'description' => 'Leads from outdoor billboard advertisements',
                'is_active' => true,
            ],
            [
                'title' => 'Employee Referral',
                'description' => 'Leads referred by company employees',
                'is_active' => true,
            ],
            [
                'title' => 'Repeat Customer',
                'description' => 'Existing customers returning for new projects',
                'is_active' => true,
            ],
            [
                'title' => 'Cold Call',
                'description' => 'Outbound cold calling campaigns',
                'is_active' => true,
            ],
            [
                'title' => 'Other',
                'description' => 'Other sources not listed above',
                'is_active' => true,
            ],
        ];

        foreach ($leadSources as $source) {
            LeadSource::updateOrCreate(
                ['title' => $source['title']],
                [
                    'description' => $source['description'],
                    'is_active' => $source['is_active'],
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]
            );
        }

        $this->command->info('Lead sources seeded successfully!');
    }
}
