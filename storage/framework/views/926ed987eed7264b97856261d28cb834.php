<!DOCTYPE html>
<html lang="en" class="scroll-pt-24">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo e($title ?? 'Academia Training Solutions — Professional Training Across Europe'); ?></title>

    <meta name="description" content="<?php echo e($description ?? 'Practical, expert-led professional training delivered online, onsite and in classrooms across Europe. Over 500 courses in business, finance, technology, supply chain, HR and compliance.'); ?>">
    <meta name="robots" content="<?php echo e($robots ?? 'index,follow'); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($canonical)): ?><link rel="canonical" href="<?php echo e($canonical); ?>"><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <meta property="og:site_name" content="Academia Training Solutions">
    <meta property="og:type" content="<?php echo e($ogType ?? 'website'); ?>">
    <meta property="og:title" content="<?php echo e($title ?? 'Academia Training Solutions'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <?php echo $__env->yieldPushContent('schema'); ?>
</head>
<body class="min-h-screen bg-white">

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-3 focus:rounded-lg focus:bg-sand-900 focus:px-4 focus:py-2 focus:text-white">
        Skip to content
    </a>

    <?php if (isset($component)) { $__componentOriginalb7bc86379492162b1d8e139381111353 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb7bc86379492162b1d8e139381111353 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.promo-bar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('promo-bar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb7bc86379492162b1d8e139381111353)): ?>
<?php $attributes = $__attributesOriginalb7bc86379492162b1d8e139381111353; ?>
<?php unset($__attributesOriginalb7bc86379492162b1d8e139381111353); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb7bc86379492162b1d8e139381111353)): ?>
<?php $component = $__componentOriginalb7bc86379492162b1d8e139381111353; ?>
<?php unset($__componentOriginalb7bc86379492162b1d8e139381111353); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginalfdc8967a87956c0a7185abbef03fae20 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfdc8967a87956c0a7185abbef03fae20 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-header','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfdc8967a87956c0a7185abbef03fae20)): ?>
<?php $attributes = $__attributesOriginalfdc8967a87956c0a7185abbef03fae20; ?>
<?php unset($__attributesOriginalfdc8967a87956c0a7185abbef03fae20); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfdc8967a87956c0a7185abbef03fae20)): ?>
<?php $component = $__componentOriginalfdc8967a87956c0a7185abbef03fae20; ?>
<?php unset($__componentOriginalfdc8967a87956c0a7185abbef03fae20); ?>
<?php endif; ?>

    <main id="main">
        <?php echo e($slot); ?>

    </main>

    <?php if (isset($component)) { $__componentOriginal222c87a019257fb1d70ae0ff46ab02e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.site-footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1)): ?>
<?php $attributes = $__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1; ?>
<?php unset($__attributesOriginal222c87a019257fb1d70ae0ff46ab02e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal222c87a019257fb1d70ae0ff46ab02e1)): ?>
<?php $component = $__componentOriginal222c87a019257fb1d70ae0ff46ab02e1; ?>
<?php unset($__componentOriginal222c87a019257fb1d70ae0ff46ab02e1); ?>
<?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>