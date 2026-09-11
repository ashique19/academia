<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Live Online Professional Training | Academia','description' => 'Instructor-led virtual classrooms, maximum 12 participants. Join from anywhere in Europe with no travel cost.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Live Online Professional Training | Academia','description' => 'Instructor-led virtual classrooms, maximum 12 participants. Join from anywhere in Europe with no travel cost.']); ?>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[760px]">
            <p class="eyebrow !text-gold-400">Live online training</p>
            <h1 class="mt-3 text-white">Learn from anywhere. Nothing watered down.</h1>
            <p class="lede mt-4 text-white/80">
                Instructor-led virtual classrooms with a maximum of 12 participants, breakout rooms,
                and the same expert, materials and certificate as the classroom version.
            </p>
        </div>
    </section>

    <div class="wrap py-14">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['Maximum 12 participants', 'Small enough that everyone is asked a direct question and gets feedback on their own work.'],
                ['Sessions recorded', 'Available for 90 days where the trainer and participants consent — recording a session in which people discuss their own workplace has GDPR implications.'],
                ['Your own time zone', 'Every start time is shown in CET and converted to your local time automatically.'],
                ['Same certificate', 'Identical to the classroom version. The delivery mode changes; the standard does not.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$heading, $body]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card p-5">
                    <h2 class="text-base"><?php echo e($heading); ?></h2>
                    <p class="mt-2 text-sm text-sand-600"><?php echo e($body); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($courses->isNotEmpty()): ?>
            <h2 class="mt-14">Popular online courses</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
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
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/pages/online.blade.php ENDPATH**/ ?>