<div>
    
    <?php $__env->startPush('schema'); ?>
        <meta name="robots" content="<?php echo e($this->robots); ?>">
    <?php $__env->stopPush(); ?>

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="<?php echo e(route('home')); ?>" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span><span>Training catalogue</span>
            </nav>
            <h1 class="mt-3 text-white">Training catalogue</h1>
            <p class="lede mt-3 max-w-[70ch] text-white/75">
                Filter by subject, delivery method, level or city — every course can be delivered
                online, onsite or in a classroom.
            </p>
        </div>
    </section>

    <div class="wrap grid gap-8 py-10 lg:grid-cols-[280px_1fr]">

        
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">Filters</h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeFilters): ?>
                    <button type="button" wire:click="resetFilters"
                            class="text-sm font-semibold text-orange-600 hover:underline">Reset</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-4 space-y-6">
                <div>
                    <label class="field-label" for="facet-search">Search</label>
                    <input id="facet-search" type="search" class="input"
                           wire:model.live.debounce.300ms="search" placeholder="Course, skill or system">
                </div>

                <fieldset>
                    <legend class="field-label">Subject area</legend>
                    <select class="select" wire:model.live="category">
                        <option value="">All subjects</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->slug); ?>">
                                <?php echo e($category->name); ?> (<?php echo e($category->published_courses_count); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </fieldset>

                <fieldset>
                    <legend class="field-label">Level</legend>
                    <div class="space-y-1.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Domain\Catalogue\Enums\CourseLevel::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $count = $this->facetCounts['levels'][$level->value] ?? 0; ?>
                            <label class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'flex items-center gap-2 text-sm',
                                'text-sand-400' => $count === 0,
                            ]); ?>">
                                <input type="checkbox" value="<?php echo e($level->value); ?>"
                                       wire:click="toggleFacet('levels', '<?php echo e($level->value); ?>')"
                                       <?php if(in_array($level->value, $levels, true)): echo 'checked'; endif; ?>
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span class="flex-1"><?php echo e($level->label()); ?></span>
                                
                                <span class="text-xs text-sand-500"><?php echo e($count); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="field-label">Delivery method</legend>
                    <div class="space-y-1.5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Domain\Catalogue\Models\DeliveryMode::orderBy('sort_order')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $count = $this->facetCounts['modes'][$mode->slug] ?? 0; ?>
                            <label class="<?php echo \Illuminate\Support\Arr::toCssClasses(['flex items-center gap-2 text-sm', 'text-sand-400' => $count === 0]); ?>">
                                <input type="checkbox" value="<?php echo e($mode->slug); ?>"
                                       wire:click="toggleFacet('modes', '<?php echo e($mode->slug); ?>')"
                                       <?php if(in_array($mode->slug, $modes, true)): echo 'checked'; endif; ?>
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span class="flex-1"><?php echo e($mode->name); ?></span>
                                <span class="text-xs text-sand-500"><?php echo e($count); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="field-label">City</legend>
                    <div class="max-h-64 space-y-1.5 overflow-y-auto pr-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->cityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" value="<?php echo e($city->slug); ?>"
                                       wire:click="toggleFacet('cities', '<?php echo e($city->slug); ?>')"
                                       <?php if(in_array($city->slug, $cities, true)): echo 'checked'; endif; ?>
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span><?php echo e($city->name); ?></span>
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </fieldset>

                <div class="card p-5">
                    <h3 class="text-sm font-semibold">Cannot find it?</h3>
                    <p class="mt-1.5 text-xs text-sand-600">
                        We build bespoke programmes on request — usually within three weeks.
                    </p>
                    <a href="<?php echo e(route('corporate')); ?>#proposal" wire:navigate
                       class="btn-green mt-3 w-full">Request a course</a>
                </div>
            </div>
        </aside>

        
        <div>
            <div class="flex flex-wrap items-center gap-3 border-b border-sand-200 pb-4">
                <p class="text-sm">
                    <strong><?php echo e(number_format($this->courses->total())); ?></strong>
                    <?php echo e(\Illuminate\Support\Str::plural('course', $this->courses->total())); ?>

                </p>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->activeFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facet => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = (array) $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $single): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                                wire:click="clearFacet('<?php echo e($facet); ?>', <?php echo \Illuminate\Support\Js::from(is_array($value) ? $single : null)->toHtml() ?>)"
                                class="chip-orange hover:bg-orange-100">
                            <?php echo e(is_array($value) ? $single : $single); ?> ✕
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <label class="ml-auto flex items-center gap-2 text-sm">
                    <span class="text-sand-500">Sort</span>
                    <select wire:model.live="sort" class="select w-auto py-1.5">
                        <option value="popular">Most booked</option>
                        <option value="soonest">Starting soonest</option>
                        <option value="price-asc">Price: low to high</option>
                        <option value="price-desc">Price: high to low</option>
                        <option value="newest">Newest</option>
                        <option value="az">A–Z</option>
                    </select>
                </label>
            </div>

            <div wire:loading.class="opacity-50" class="transition-opacity">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->courses->isEmpty()): ?>
                    <div class="card mt-8 p-10 text-center">
                        <h3>No courses match those filters</h3>
                        <p class="mt-2 text-sand-600">
                            Try removing a filter — or tell us what you are looking for and we will build it.
                        </p>
                        <a href="<?php echo e(route('corporate')); ?>#proposal" wire:navigate class="btn-green mt-5">
                            Request a custom programme
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if (isset($component)) { $__componentOriginal0a1b9827ce04f2b2ad6eeae95024b702 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0a1b9827ce04f2b2ad6eeae95024b702 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course-card','data' => ['course' => $course,'wire:key' => 'course-'.e($course->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['course' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course),'wire:key' => 'course-'.e($course->id).'']); ?>
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

                    <div class="mt-10"><?php echo e($this->courses->links()); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/livewire/public/course-catalogue.blade.php ENDPATH**/ ?>