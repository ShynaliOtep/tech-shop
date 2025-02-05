<div class="breadcrumbs-section">
    <div class="col s12">
        <a href="/" class="breadcrumb">
            <span class="breadcrumb-item chip orange darken-4 white-text">
                <b>
                <?php echo e(__('translations.Main')); ?>

                </b>
            </span>
        </a>
        <?php if(Route::is('goodList') && isset($goodType)): ?>
            <a href="category/<?php echo e($goodType->code); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.'. $goodType->code)); ?>

                    </b>
                </span>
            </a>
        <?php endif; ?>
        <?php if(Route::is('viewProfile')): ?>
            <a href="#" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.Profile')); ?>

                    </b>
                </span>
            </a>
        <?php endif; ?>
        <?php if(Route::is('editProfile')): ?>
            <a href="<?php echo e(route('viewProfile')); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.Profile')); ?>

                    </b>
                </span>
            </a>
            <a href="#" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.Editing')); ?>

                    </b>
                </span>
            </a>
        <?php endif; ?>
        <?php if(Route::is('viewGood') && isset($good)): ?>
            <a href="category/<?php echo e($good->goodType->code); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.'. $good->goodType->code)); ?>

                    </b>
                </span>
            </a>
            <a href="<?php echo e(route('viewGood', $good)); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e($good['name_' . session()->get('locale', 'ru')]); ?>

                    </b>
                </span>
            </a>
        <?php endif; ?>
        <?php if(Route::is('getFavorites')): ?>
            <a href="<?php echo e(route('getFavorites')); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        <?php echo e(__( 'translations.Favorites')); ?>

                    </b>
                </span>
            </a>
        <?php endif; ?>
        <?php if(Route::is('cart')): ?>
            <a href="<?php echo e(route('cart')); ?>" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                <b>
                    <?php echo e(__( 'translations.Cart')); ?>

                </b>
                </span>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/navigation/breadcrumbs.blade.php ENDPATH**/ ?>