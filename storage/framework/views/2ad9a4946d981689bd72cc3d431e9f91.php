<?php
    // Renders nothing when no campaign is running. PromotionService::active()
    // returns null the moment ends_at passes, so an expired campaign
    // disappears from here, the cards, the offers page and the schema at the
    // same instant — there is no evergreen sale to forget to switch off.
    $promotion = app(\App\Domain\Catalogue\Services\PromotionService::class)->active();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($promotion): ?>
    <div class="bg-gradient-to-r from-orange-600 via-orange-400 to-gold-400 text-white">
        <div class="wrap flex flex-wrap items-center justify-center gap-x-3 gap-y-1 py-2.5 text-center text-sm font-semibold">
            <span>
                <strong class="rounded-pill bg-black/20 px-2.5 py-0.5">
                    <?php echo e($promotion->name); ?> — <?php echo e($promotion->percentage); ?>% off
                </strong>
                <span class="ml-2 font-normal"><?php echo e($promotion->blurb); ?></span>
            </span>
            <span class="font-normal">
                Code <strong class="rounded-pill bg-black/20 px-2 py-0.5"><?php echo e($promotion->code); ?></strong>,
                ends <?php echo e($promotion->ends_at->format('j F Y')); ?>.
            </span>
            <a href="<?php echo e(route('offers')); ?>" class="underline underline-offset-4 hover:no-underline">See all offers</a>
        </div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/components/promo-bar.blade.php ENDPATH**/ ?>