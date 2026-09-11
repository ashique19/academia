<div>
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <h1 class="text-white">Training schedule</h1>
            <p class="lede mt-3 max-w-[70ch] text-white/75">
                Confirmed dates with real seat counts, shown before you enquire. Filter by subject,
                city, delivery method or date.
            </p>
        </div>
    </section>

    <div class="wrap py-10">
        <div class="flex flex-wrap items-center gap-3 border-b border-sand-200 pb-5">
            <div class="inline-flex rounded-lg border border-sand-300 p-0.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['list' => 'List', 'calendar' => 'Calendar']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" wire:click="setView('<?php echo e($value); ?>')"
                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'rounded-md px-3.5 py-1.5 text-sm font-semibold transition',
                                'bg-sand-900 text-white' => $viewMode === $value,
                                'text-sand-600 hover:bg-sand-50' => $viewMode !== $value,
                            ]); ?>"><?php echo e($label); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <select wire:model.live="category" class="select w-auto">
                <option value="">All subjects</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($category->slug); ?>"><?php echo e($category->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>

            <select wire:model.live="mode" class="select w-auto">
                <option value="">Any delivery method</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->modes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $deliveryMode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($deliveryMode->slug); ?>"><?php echo e($deliveryMode->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>

            <select wire:model.live="country" class="select w-auto">
                <option value="">Any country</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $countryOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($countryOption->slug); ?>"><?php echo e($countryOption->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>

            <select wire:model.live="city" class="select w-auto">
                <option value="">Any city</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cityOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cityOption->slug); ?>"><?php echo e($cityOption->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="availableOnly"
                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                Seats available only
            </label>

            <button type="button" wire:click="resetFilters"
                    class="ml-auto text-sm font-semibold text-orange-600 hover:underline">Reset</button>
        </div>

        <div wire:loading.class="opacity-50" class="mt-6 transition-opacity">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewMode === 'calendar'): ?>
                <?php $calendar = $this->calendar; ?>

                <div class="flex items-center justify-between">
                    <h2 class="text-xl"><?php echo e($calendar['month']->format('F Y')); ?></h2>
                    <div class="flex gap-2">
                        <button type="button" wire:click="shiftMonth(-1)" class="btn-ghost">← Previous</button>
                        <button type="button" wire:click="shiftMonth(1)" class="btn-ghost">Next →</button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-7 gap-px overflow-hidden rounded-card border border-sand-200 bg-sand-200 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-sand-50 p-2 text-center text-xs font-bold uppercase text-sand-500"><?php echo e($dayName); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $calendar['days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'min-h-[110px] bg-white p-1.5',
                            'bg-sand-50/60 text-sand-400' => ! $day['inMonth'],
                            'ring-2 ring-inset ring-orange-500' => $day['isToday'],
                        ]); ?>">
                            <span class="text-xs font-semibold"><?php echo e($day['date']->format('j')); ?></span>
                            <div class="mt-1 space-y-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $day['sessions']->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('courses.show', $session->course)); ?>" wire:navigate
                                       wire:key="cal-<?php echo e($session->id); ?>"
                                       class="block truncate rounded bg-orange-50 px-1.5 py-0.5 text-[11px] text-orange-700 hover:bg-orange-100"
                                       title="<?php echo e($session->course->display_title); ?>">
                                        <?php echo e($session->course->display_title); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($day['sessions']->count() > 3): ?>
                                    <span class="block px-1.5 text-[11px] text-sand-500">
                                        +<?php echo e($day['sessions']->count() - 3); ?> more
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-sm text-sand-500">
                    Showing <?php echo e(number_format($this->sessions->total())); ?> sessions
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $from && ! $to): ?>
                        in the next <?php echo e(config('academia.schedule.default_window_days')); ?> days
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>

                <div class="mt-4 grid gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->sessions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $session): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if (isset($component)) { $__componentOriginal3972dab074043012adc9f816b2f97b40 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3972dab074043012adc9f816b2f97b40 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.session-row','data' => ['session' => $session,'wire:key' => 'row-'.e($session->id).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('session-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['session' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($session),'wire:key' => 'row-'.e($session->id).'']); ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="card p-10 text-center">
                            <h3>No sessions match those filters</h3>
                            <p class="mt-2 text-sand-600">Try widening the date range or removing a filter.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="mt-8"><?php echo e($this->sessions->links()); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/livewire/public/schedule-browser.blade.php ENDPATH**/ ?>