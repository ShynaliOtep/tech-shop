<?php $__env->startSection('content'); ?>
    <?php if(isset($viewedGoodTypes)): ?>
        <?php $__currentLoopData = $viewedGoodTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goodType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row">
            <h5 class="white-text page-presenter-header"><?php echo e(__('translations.' . $goodType->code)); ?></h5>
            <?php if(count($goodType->goods) != 0): ?>
                <?php $__currentLoopData = $goodType->goods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $good): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col s6 m4 l3">
                        <?php echo $__env->make('goodCard', ['good' => $good], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <h5 class="white-text center"><?php echo e(__('translations.There is nothing here yet')); ?> :(</h5>
            <?php endif; ?>
    </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/favoriteActions.js')); ?>"></script>
        <script src="<?php echo e(asset('js/cart.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
    <?php echo $__env->make('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/good.blade.php ENDPATH**/ ?>