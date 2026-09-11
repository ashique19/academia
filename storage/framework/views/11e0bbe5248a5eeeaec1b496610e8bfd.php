<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Corporate &amp; In-Company Training in Europe | Academia','description' => 'Customised corporate training delivered at your offices, live online or in a private classroom anywhere in Europe. Fixed-price proposal within two working days.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Corporate &amp; In-Company Training in Europe | Academia','description' => 'Customised corporate training delivered at your offices, live online or in a private classroom anywhere in Europe. Fixed-price proposal within two working days.']); ?>

    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap grid gap-10 lg:grid-cols-[1.1fr_.9fr]">
            <div>
                <p class="eyebrow !text-gold-400">Corporate &amp; in-company training</p>
                <h1 class="mt-3 text-white">Develop your workforce with practical, industry-relevant training</h1>
                <p class="lede mt-4 text-white/80">
                    From a single team workshop to a multi-country capability programme. We assess the
                    gap, design the curriculum around your systems and sector, deliver it wherever your
                    people are, and report on what changed.
                </p>
                <ul class="mt-6 space-y-2.5 text-sm text-white/85">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        'Proposal within two working days, fixed price, no surprises',
                        'Delivered at your offices, live online, or in a private classroom in any European city',
                        'Delivered in English, with materials and case studies localised to your market',
                        'One invoice, one point of contact, one capability report',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span><?php echo e($point); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
                <a href="#proposal" class="btn-gold btn-lg mt-8">Request a proposal</a>
            </div>

            <div class="rounded-card border border-white/15 bg-white/5 p-8">
                <p class="font-display text-4xl font-semibold text-gold-400">2 days</p>
                <p class="mt-1 text-sm text-white/70">Average proposal turnaround</p>
                <hr class="my-6 border-white/10">
                <p class="font-display text-4xl font-semibold text-gold-400">2 hours</p>
                <p class="mt-1 text-sm text-white/70">
                    First response, in business hours. We measure it, and the dashboard shows a
                    breach in red.
                </p>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">What we deliver for organisations</p>
            <h2 class="mt-3">Built around your capability gap, not our catalogue</h2>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    ['Customised training programmes', 'We rewrite cases, data and exercises around your sector, your systems and your policies — so nothing has to be translated back into your context.'],
                    ['Employee skills assessment', 'Diagnostic before, measurement after. You get a heat map of capability by team and a defensible baseline for the training budget.'],
                    ['Onsite workshops', 'One to five days at your premises anywhere in Europe. Cost-effective from six participants and far easier to schedule.'],
                    ['Leadership programmes', 'Modular development journeys for first-time managers through to executive teams, with coaching between modules.'],
                    ['Technical &amp; systems training', 'SAP, Power BI, Excel, Python, SQL and cloud — taught in a sandbox that mirrors your own environment.'],
                    ['Digital transformation', 'Change-ready programmes for ERP rollouts, AI adoption and process automation. Trains the behaviour, not just the button clicks.'],
                    ['Team development', 'Facilitated sessions on collaboration, communication and psychological safety — for teams that need to work differently, not know more.'],
                    ['Certification pathways', 'Independent exam preparation for the major schemes, with the exam booked directly with the scheme owner.'],
                    ['Group rates', 'Three or more people on the same course saves 15%, rising to 25% at ten. Applied automatically — no negotiation needed.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$heading, $body]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="card card-hover p-6">
                        <h3 class="text-base"><?php echo $heading; ?></h3>
                        <p class="mt-2 text-sm text-sand-600"><?php echo $body; ?></p>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    <section id="proposal" class="scroll-mt-24 bg-brand-cream py-20">
        <div class="wrap grid gap-12 lg:grid-cols-[1fr_1.15fr]">
            <div>
                <p class="eyebrow">Request a proposal</p>
                <h2 class="mt-3">Tell us the gap. We will send a fixed price.</h2>
                <p class="lede mt-4">
                    Three short steps. A training advisor replies within two working hours, and you
                    get a written proposal within two working days.
                </p>

                <div class="card mt-8 p-6">
                    <h3 class="text-base">What happens next</h3>
                    <ol class="mt-4 space-y-4 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                            ['Within 2 working hours', 'A training advisor calls or emails to confirm the detail we need.'],
                            ['Within 2 working days', 'A written proposal: curriculum outline, delivery plan, fixed price.'],
                            ['Before you commit', 'We will tell you if an in-company group is more expensive than public seats. Usually it is not, from six people.'],
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => [$when, $what]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex gap-3">
                                <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-orange-500 text-xs font-bold text-white"><?php echo e($index + 1); ?></span>
                                <span><strong class="block"><?php echo e($when); ?></strong><span class="text-sand-600"><?php echo e($what); ?></span></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ol>
                </div>
            </div>

            <div class="card p-7">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public.corporate-inquiry-form', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1918659767-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key);

echo $__html;

unset($__html);
unset($__key);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($faqs->isNotEmpty()): ?>
        <section class="py-20">
            <div class="wrap max-w-[820px]">
                <h2>Procurement questions</h2>
                <div class="mt-8 divide-y divide-sand-200 overflow-hidden rounded-card border border-sand-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <details class="group">
                            <summary class="flex cursor-pointer items-center gap-3 p-5 font-semibold hover:bg-sand-50">
                                <span class="flex-1"><?php echo e($faq->question); ?></span>
                                <svg class="h-4 w-4 text-sand-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <p class="border-t border-sand-100 bg-sand-50/50 p-5 text-sm text-sand-600"><?php echo e($faq->answer); ?></p>
                        </details>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/pages/corporate.blade.php ENDPATH**/ ?>