<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\BlogCategory;
use App\Domain\Content\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Sample published posts so the admin Blog Posts resource and any future
 * public blog surface are not empty after seed.
 */
class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'content@academiatraining.eu')->first()
            ?? User::query()->first();

        $posts = [
            [
                'category' => 'Corporate Training',
                'title' => 'How to brief an in-company training provider',
                'excerpt' => 'The five pieces of context that turn a generic proposal into a curriculum your managers will actually use.',
                'body' => <<<'MD'
A useful training brief is short. It names the audience, the systems they work in, the outcome you need within ninety days, and the constraints you will not bend — language, locations, and maximum time away from the desk.

Send that before you ask for a price. Providers who cannot respond to those four points with a fixed curriculum outline are not ready to deliver; providers who can will usually send a draft agenda inside two working days.
MD,
            ],
            [
                'category' => 'Data Analytics',
                'title' => 'Why classroom BI courses still beat self-paced video',
                'excerpt' => 'Self-paced libraries are excellent for syntax. Cohort classrooms are where teams leave with a model still running on their own data.',
                'body' => <<<'MD'
Video libraries win on convenience. They lose when the exercise dataset is fictional, the instructor cannot answer a question about your warehouse, and nobody is waiting for your dashboard on Friday.

Our classroom and live-online BI courses use a shared lab and, for in-company cohorts, a scrubbed extract of your own data. Participants leave with something that survives the commute home — which is the only metric that matters after the certificate is printed.
MD,
            ],
            [
                'category' => 'Leadership',
                'title' => 'Onboarding new managers without a six-month fog',
                'excerpt' => 'A practical first-ninety-days syllabus for people who were excellent individual contributors last quarter.',
                'body' => <<<'MD'
New managers inherit calendars, not playbooks. The gap is rarely motivation; it is rehearsal — running a one-to-one, giving feedback that changes behaviour, and protecting delivery while someone is learning the role.

A three-day essentials programme will not replace mentoring, but it gives the cohort a shared language and a set of practised conversations before the first difficult week arrives.
MD,
            ],
            [
                'category' => 'Learning Tips',
                'title' => 'Getting value from a public course seat',
                'excerpt' => 'Arrive with a live problem, book the seat early enough for early-bird pricing, and block the afternoon after day three.',
                'body' => <<<'MD'
Public courses work when you treat them as applied workshops. Bring a concrete problem from your desk. Sit near people from adjacent industries — the hallway conversations are half the fee.

Block ninety minutes the day after the final session to apply one technique before inbox gravity returns. That single calendar hold is what separates a certificate from a capability change.
MD,
            ],
            [
                'category' => 'Finance & Accounting',
                'title' => 'Omnibus pricing and why we publish every promotion end date',
                'excerpt' => 'EU consumer rules require discount claims to reference a real prior price. Here is how Academia applies that on every course page.',
                'body' => <<<'MD'
Directive (EU) 2019/2161 requires announced discounts to reference the lowest price in the previous thirty days. We store that prior price on every course row and never raise a list price to manufacture a sale.

Every promotion also carries a stated reason and an end date in the database. If either is missing, the row cannot be saved — which is deliberate. An evergreen “sale” is a pricing policy dressed as a campaign.
MD,
            ],
            [
                'category' => 'Digital Transformation',
                'title' => 'Rolling the same programme across six countries',
                'excerpt' => 'One curriculum, localised examples, and a single capability report — how multi-site corporate deliveries stay coherent.',
                'body' => <<<'MD'
Multi-country programmes fail when each site invents its own version of “the same course”. We keep one master curriculum, swap regulatory and systems examples per market, and report capability gaps on one scorecard.

That approach is slower to design and faster to scale. It also means a participant who transfers from Madrid to Amsterdam mid-programme does not start again from zero.
MD,
            ],
        ];

        foreach ($posts as $order => $data) {
            $category = BlogCategory::query()->where('name', $data['category'])->first();

            if ($category === null) {
                continue;
            }

            BlogPost::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'blog_category_id' => $category->id,
                    'author_id' => $author?->id,
                    'title' => $data['title'],
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'reading_minutes' => max(2, (int) ceil(str_word_count($data['body']) / 220)),
                    'status' => 'published',
                    'published_at' => now()->subDays(count($posts) - $order),
                ]
            );
        }
    }
}
