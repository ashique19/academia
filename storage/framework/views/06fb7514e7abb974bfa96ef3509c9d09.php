<form wire:submit="search"
      class="flex flex-col gap-2 rounded-card bg-white p-2 shadow-lg sm:flex-row sm:items-center">

    <label class="sr-only" for="hero-q">What do you want to learn?</label>
    <input id="hero-q" type="search" wire:model="query"
           placeholder="What do you want to learn? e.g. Power BI, IFRS, leadership…"
           class="min-w-0 flex-1 rounded-lg border-0 px-3 py-2.5 text-sm text-sand-900 placeholder:text-sand-400 focus:ring-0">

    <label class="sr-only" for="hero-cat">Subject area</label>
    <select id="hero-cat" wire:model="category"
            class="rounded-lg border-0 bg-sand-50 px-3 py-2.5 text-sm text-sand-900 focus:ring-0">
        <option value="">All subjects</option>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->slug); ?>"><?php echo e($category->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </select>

    <label class="sr-only" for="hero-city">City</label>
    <select id="hero-city" wire:model="city"
            class="rounded-lg border-0 bg-sand-50 px-3 py-2.5 text-sm text-sand-900 focus:ring-0">
        <option value="">Anywhere</option>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($city->slug); ?>"><?php echo e($city->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </select>

    <button type="submit" class="btn-primary shrink-0">
        <span wire:loading.remove wire:target="search">Find training</span>
        <span wire:loading wire:target="search">Searching…</span>
    </button>
</form>
<?php /**PATH C:\Users\Ashique\Desktop\Workplace\academia-laravel\resources\views/livewire/public/course-search.blade.php ENDPATH**/ ?>