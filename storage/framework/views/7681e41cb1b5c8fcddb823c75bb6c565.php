<?php $locale = session('locale', config('app.locale')); ?>
<!doctype html>
<html lang="<?php echo e($locale); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="format-detection" content="telephone=no">
    <title>PixelRental</title>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/materialize.css')); ?>">
    <link href="<?php echo e(asset('css/material-icons.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    <link rel="icon" href="<?php echo e(asset('favicon-32x32.png')); ?>" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="<?php echo e(asset('apple-touch-icon.png')); ?>">
</head>
<body>
<?php echo $__env->make('navigation.sidemenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="main grey darken-4">
    <div class="row no-padding main-row">
        <div class="col s12 no-padding">
            <?php echo $__env->make('navigation.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col s12 no-padding">
            <?php echo $__env->make('navigation.breadcrumbs', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class="col s12 no-padding" id="main-content-hernya">
            <div class="container">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
        <div class="col s12 no-padding">
            <?php echo $__env->make('navigation.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
<script src="<?php echo e(asset('js/script.js')); ?>"></script>
<script src="<?php echo e(asset('js/dropdown.js')); ?>"></script>
<script src="<?php echo e(asset('js/materialize.js')); ?>"></script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/app.blade.php ENDPATH**/ ?>