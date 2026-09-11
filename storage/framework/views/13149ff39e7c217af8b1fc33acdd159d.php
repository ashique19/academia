<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    
    <section class="relative overflow-hidden bg-sand-900 text-white">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0
             bg-[radial-gradient(1100px_620px_at_78%_8%,rgba(224,165,38,.24),transparent_62%),radial-gradient(880px_560px_at_8%_92%,rgba(177,71,5,.34),transparent_60%),radial-gradient(700px_500px_at_52%_118%,rgba(46,107,79,.30),transparent_62%)]"></div>

        <div class="wrap relative grid items-center gap-12 py-16 lg:grid-cols-[1.06fr_.94fr] lg:py-24">
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promotion): ?>
                    <span class="chip mb-5 bg-white/10 text-white">
                        <?php echo e($promotion->name); ?> — <?php echo e($promotion->percentage); ?>% off
                    </span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <h1 class="text-white">
                    Transform Skills. Empower Careers.
                    <em class="not-italic text-gold-400">Build Future-Ready Teams.</em>
                </h1>

                <p class="lede mt-5 max-w-[53ch] text-white/80">
                    Professional online, onsite and classroom training delivered by experienced
                    practitioners across Europe. Over <?php echo e($stats['courses']); ?> courses in business,
                    finance, technology, supply chain, HR and compliance.
                </p>

                
                <div class="mt-8">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public.course-search', []);

$__key = null;

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-1978565186-0', $__key);

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

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="<?php echo e(route('courses.index')); ?>" class="btn-primary btn-lg" wire:navigate>
                        Browse <?php echo e($stats['courses']); ?>+ Courses
                    </a>
                    <a href="<?php echo e(route('corporate')); ?>#proposal" class="btn-gold btn-lg" wire:navigate>
                        Request Corporate Training
                    </a>
                    <a href="<?php echo e(route('schedule')); ?>" class="btn-light btn-lg" wire:navigate>
                        View Training Schedule
                    </a>
                </div>
            </div>

            <div class="hidden lg:block">
                <?php if (isset($component)) { $__componentOriginal8423e086faa18aab3daf84debcba334c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8423e086faa18aab3daf84debcba334c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hero-art','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('hero-art'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8423e086faa18aab3daf84debcba334c)): ?>
<?php $attributes = $__attributesOriginal8423e086faa18aab3daf84debcba334c; ?>
<?php unset($__attributesOriginal8423e086faa18aab3daf84debcba334c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8423e086faa18aab3daf84debcba334c)): ?>
<?php $component = $__componentOriginal8423e086faa18aab3daf84debcba334c; ?>
<?php unset($__componentOriginal8423e086faa18aab3daf84debcba334c); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="relative border-t border-white/10 bg-black/20">
            <div class="wrap grid grid-cols-2 gap-6 py-6 md:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    [$stats['courses'], 'Courses, all deliverable'],
                    ['Max 14', 'Per classroom · 12 online'],
                    ['10+ yrs', 'Every expert, still practising'],
                    [$stats['cities'], 'European cities'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$figure, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <p class="font-display text-3xl font-semibold text-white"><?php echo e($figure); ?></p>
                        <p class="mt-0.5 text-[11px] font-bold uppercase tracking-wider text-white/60"><?php echo e($label); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">How you learn with us</p>
            <h2 class="mt-3 max-w-[22ch]">Three ways to train. One standard of quality.</h2>
            <p class="lede mt-4 max-w-[62ch]">
                Every course in our catalogue can be delivered live online, at your own offices,
                or in a public classroom in a European city near you. Same expert, same materials,
                same certificate.
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    ['Online Training', 'online', 'Live online', 'Instructor-led virtual classrooms and self-paced modules. Join from anywhere in Europe with no travel cost.', ['Live interaction and breakout rooms', 'Sessions recorded for 90 days', 'Digital certificate on completion'], 'green'],
                    ['Onsite Corporate Training', 'corporate', 'In-company', 'Your expert travels to you. Content built around your systems, your data and your team\'s real work.', ['Fully customised curriculum', 'Pre-training skills assessment', 'Cost-effective from six participants'], 'orange'],
                    ['Classroom Training', 'locations', 'Public courses', 'Scheduled public courses in European cities. Learn alongside professionals from other organisations.', ['Central, well-connected venues', 'Small groups, maximum 14 people', 'Lunch and materials included'], 'gold'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$heading, $routeName, $chip, $body, $points, $tone]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="card card-hover flex flex-col p-6">
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'chip w-fit',
                            'chip-green'  => $tone === 'green',
                            'chip-orange' => $tone === 'orange',
                            'chip-gold'   => $tone === 'gold',
                        ]); ?>"><?php echo e($chip); ?></span>

                        <h3 class="mt-4"><?php echo e($heading); ?></h3>
                        <p class="mt-2 text-sm text-sand-600"><?php echo e($body); ?></p>

                        <ul class="mt-4 space-y-2 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $points; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex gap-2">
                                    <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span><?php echo e($point); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>

                        <a href="<?php echo e(route($routeName)); ?>" wire:navigate
                           class="mt-5 text-sm font-semibold text-orange-600 hover:underline">
                            Explore <?php echo e(strtolower($heading)); ?> →
                        </a>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="bg-brand-cream py-20">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow"><?php echo e($stats['courses']); ?> training topics</p>
                    <h2 class="mt-3">Find training by subject area</h2>
                </div>
                <a href="<?php echo e(route('courses.index')); ?>" class="btn-ghost" wire:navigate>View the full catalogue</a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('courses.category', $category)); ?>" wire:navigate
                       class="card card-hover flex flex-col p-6">
                        <h3 class="text-lg"><?php echo e($category->name); ?></h3>
                        <p class="mt-2 line-clamp-2 text-sm text-sand-600"><?php echo e($category->summary); ?></p>
                        <span class="mt-4 text-sm font-semibold text-orange-600">
                            <?php echo e($category->published_courses_count); ?> courses →
                        </span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">Booking fast</p>
            <h2 class="mt-3">Courses professionals are booking this quarter</h2>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ($featured->isNotEmpty() ? $featured : $popular); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal0a1b9827ce04f2b2ad6eeae95024b702 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0a1b9827ce04f2b2ad6eeae95024b702 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course-card','data' => ['course' => $course]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0a1b9827ce04f2b2ad6eeae95024b702)): ?>
<?php $attributes = $__attributesOriginal0a1b9827ce04f2b2ad6eeae95024b702; ?>
<?php unset($__attributesOriginal0a1b9827ce04f2b2ad6eeae95024b702); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0a1b9827ce04f2b2ad6eeae95024b702)): ?>
<?php $component = $__componentOriginal0a1b9827ce04f2b2ad6eeae95024b702; ?>
<?php unset($__componentOriginal0a1b9827ce04f2b2ad6eeae95024b702); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="bg-sand-50 py-20">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Confirmed dates</p>
                    <h2 class="mt-3">Starting soon</h2>
                </div>
                <a href="<?php echo e(route('schedule')); ?>" class="btn-ghost" wire:navigate>See the full schedule</a>
            </div>

            <div class="mt-8 grid gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal3972dab074043012adc9f816b2f97b40 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3972dab074043012adc9f816b2f97b40 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.session-row','data' => ['session' => $session]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('session-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['session' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($session)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3972dab074043012adc9f816b2f97b40)): ?>
<?php $attributes = $__attributesOriginal3972dab074043012adc9f816b2f97b40; ?>
<?php unset($__attributesOriginal3972dab074043012adc9f816b2f97b40); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3972dab074043012adc9f816b2f97b40)): ?>
<?php $component = $__componentOriginal3972dab074043012adc9f816b2f97b40; ?>
<?php unset($__componentOriginal3972dab074043012adc9f816b2f97b40); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <section class="bg-sand-900 py-20 text-white">
        <div class="wrap grid items-center gap-10 lg:grid-cols-[1.2fr_.8fr]">
            <div>
                <p class="eyebrow !text-gold-400">Corporate &amp; in-company</p>
                <h2 class="mt-3 text-white">Develop your workforce with practical, industry-relevant training</h2>
                <p class="lede mt-4 text-white/75">
                    From a single team workshop to a multi-country capability programme. We assess
                    the gap, design the curriculum around your systems and sector, deliver it
                    wherever your people are, and report on what changed.
                </p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="<?php echo e(route('corporate')); ?>#proposal" class="btn-gold btn-lg" wire:navigate>
                        Request a proposal
                    </a>
                    <a href="<?php echo e(route('offers')); ?>" class="btn-light btn-lg" wire:navigate>
                        See group rates
                    </a>
                </div>
            </div>

            <div class="rounded-card border border-white/15 bg-white/5 p-6">
                <ul class="space-y-3 text-sm text-white/85">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                        'Proposal within two working days, fixed price',
                        'Delivered at your offices, live online, or in a private classroom',
                        'Delivered in English, localised to your market',
                        'One invoice, one point of contact, one capability report',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span><?php echo e($point); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        </div>
    </section>

    
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">Classroom training across Europe</p>
            <h2 class="mt-3">Public courses in <?php echo e($stats['cities']); ?> European cities</h2>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('locations.city', [$city->country, $city])); ?>" wire:navigate
                       class="card card-hover p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-sand-500"><?php echo e($city->country->name); ?></p>
                        <h3 class="mt-1 text-lg"><?php echo e($city->name); ?></h3>
                        <p class="mt-2 text-sm text-sand-600">
                            <?php echo e($city->upcoming_sessions_count); ?> upcoming
                            <?php echo e(\Illuminate\Support\Str::plural('date', $city->upcoming_sessions_count)); ?>

                        </p>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($testimonials->isNotEmpty()): ?>
        <section class="bg-brand-cream py-20">
            <div class="wrap">
                <p class="eyebrow">What participants tell us</p>
                <h2 class="mt-3">The feedback we design for</h2>
                <p class="lede mt-4 max-w-[70ch]">
                    We do not publish attributed quotes, because we will not put a name and a face to
                    words a participant did not write. What we can publish is the standard every
                    programme is built to meet — and an independent review feed once participants
                    have submitted their own.
                </p>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <figure class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'card p-6',
                            'border-dashed' => $testimonial->is_illustrative,
                        ]); ?>">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($testimonial->is_illustrative): ?>
                                
                                <span class="sample-flag">Illustrative</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <blockquote class="mt-3 text-sm leading-relaxed">
                                “<?php echo e($testimonial->quote); ?>”
                            </blockquote>

                            <figcaption class="mt-4 text-xs text-sand-500">
                                <?php echo e($testimonial->attribution); ?>

                            </figcaption>
                        </figure>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <p class="mt-6 max-w-[80ch] text-xs text-sand-500">
                    Academia publishes no rating of its own. When an independent review platform is
                    connected, verified reviews and a verified score appear here — and only then does
                    <code>aggregateRating</code> schema begin to emit.
                </p>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalb9eda212fea7921b4a03feb1b01f47d7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.cta-band','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('cta-band'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7)): ?>
<?php $attributes = $__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7; ?>
<?php unset($__attributesOriginalb9eda212fea7921b4a03feb1b01f47d7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb9eda212fea7921b4a03feb1b01f47d7)): ?>
<?php $component = $__componentOriginalb9eda212fea7921b4a03feb1b01f47d7; ?>
<?php unset($__componentOriginalb9eda212fea7921b4a03feb1b01f47d7); ?>
<?php endif; ?>

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
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/pages/home.blade.php ENDPATH**/ ?>