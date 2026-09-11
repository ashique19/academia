<footer class="mt-24 bg-sand-900 text-sand-300">
    <div class="wrap grid gap-10 py-14 md:grid-cols-2 lg:grid-cols-5">

        <div class="lg:col-span-1">
            <p class="font-display text-xl font-semibold text-white">Academia</p>
            <p class="text-[9.5px] font-bold uppercase tracking-[0.19em] text-sand-400">Training Solutions</p>
            <p class="mt-4 max-w-[34ch] text-sm">
                Practical, expert-led professional training delivered online, onsite and in
                classrooms across Europe.
            </p>
            <p class="mt-3 text-xs font-bold text-gold-400">
                A trade name of <?php echo e(str_replace(' B.V.', '', config('academia.legal_entity'))); ?>

            </p>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Training areas</h5>
            <ul class="space-y-2 text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Domain\Catalogue\Models\CourseCategory::active()->ordered()->take(6)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><a href="<?php echo e(route('courses.category', $category)); ?>" class="hover:text-white" wire:navigate><?php echo e($category->name); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <li><a href="<?php echo e(route('courses.index')); ?>" class="font-semibold text-gold-400 hover:text-gold-300" wire:navigate>All courses →</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Delivery</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="<?php echo e(route('online')); ?>" class="hover:text-white" wire:navigate>Live online training</a></li>
                <li><a href="<?php echo e(route('corporate')); ?>" class="hover:text-white" wire:navigate>Onsite corporate training</a></li>
                <li><a href="<?php echo e(route('locations')); ?>" class="hover:text-white" wire:navigate>Public classroom courses</a></li>
                <li><a href="<?php echo e(route('schedule')); ?>" class="hover:text-white" wire:navigate>Training schedule</a></li>
                <li><a href="<?php echo e(route('offers')); ?>" class="hover:text-white" wire:navigate>Offers &amp; discounts</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Popular cities</h5>
            <ul class="space-y-2 text-sm">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Domain\Shared\Models\City::active()->hasUpcomingSessions()->with('country')->orderBy('name')->take(5)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(route('locations.city', [$city->country, $city])); ?>" class="hover:text-white" wire:navigate>
                            Training in <?php echo e($city->name); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <li><a href="<?php echo e(route('locations')); ?>" class="font-semibold text-gold-400 hover:text-gold-300" wire:navigate>All locations →</a></li>
            </ul>
        </div>

        <div>
            <h5 class="mb-3 text-xs font-bold uppercase tracking-widest text-white">Company</h5>
            <ul class="space-y-2 text-sm">
                <li><a href="<?php echo e(route('about')); ?>" class="hover:text-white" wire:navigate>About us</a></li>
                <li><a href="<?php echo e(route('glossary')); ?>" class="hover:text-white" wire:navigate>Glossary</a></li>
                <li><a href="<?php echo e(route('faq')); ?>" class="hover:text-white" wire:navigate>FAQ</a></li>
                <li><a href="mailto:<?php echo e(config('academia.email')); ?>" class="hover:text-white">Contact</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="wrap flex flex-wrap items-center justify-between gap-3 py-5 text-xs">
            <span>
                © <?php echo e(date('Y')); ?> <?php echo e(config('academia.trade_name')); ?>, a trade name of
                <strong class="text-gold-400"><?php echo e(config('academia.legal_entity')); ?></strong>
                · KvK 00000000 · VAT NL000000000B01
            </span>
            
            <span class="flex flex-wrap gap-4">
                <a href="<?php echo e(route('legal', 'privacy')); ?>" class="hover:text-white" wire:navigate>Privacy &amp; GDPR</a>
                <a href="<?php echo e(route('legal', 'terms')); ?>" class="hover:text-white" wire:navigate>Terms</a>
                <a href="<?php echo e(route('legal', 'cancellation-policy')); ?>" class="hover:text-white" wire:navigate>Cancellation policy</a>
                <a href="<?php echo e(route('legal', 'cookie-settings')); ?>" class="hover:text-white" wire:navigate>Cookie settings</a>
            </span>
        </div>
    </div>
</footer>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/components/site-footer.blade.php ENDPATH**/ ?>