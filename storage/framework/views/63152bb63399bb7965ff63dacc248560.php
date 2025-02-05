<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>pixelrental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
</head>
<body>
<?php echo $__env->make('auth.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="container valign-wrapper auth-container">
    <div class="valign-wrapper">
        <div class="main-color center auth-inner">
            <a href="/" class="back-auth-button"><i class="material-icons orange-text text-lighten-3">cancel</i></a>
            <h5 class="white-text"><?php echo e(__('translations.Log into your account')); ?></h5>
            <div class="row">
                <form class="col s12" method="POST" action="">
                    <?php echo e(csrf_field()); ?>

                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix white-text">phone</i>
                            <input name="phone" type="tel" placeholder="<?php echo e(__('translations.Phone')); ?>" class="white-text">
                        </div>
                        <div class="input-field col s12">
                            <i class="material-icons prefix white-text">lock</i>
                            <input name="password" type="password" placeholder="<?php echo e(__('translations.Password')); ?>" class="white-text">
                        </div>
                        <div class="input-field col s12">
                            <button type="submit" class="btn orange darken-4 authorization-link">
                                <?php echo e(__('translations.Log in')); ?>

                            </button>
                        </div>
                        <?php if($errors->any()): ?>
                            <div class="col s12">
                                <ul class="red-text">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr>
                </form>
            </div>
            <span class="white-text"><?php echo e(__('translations.Still do not have an account?')); ?> <a href="<?php echo e(route('register')); ?>" class="orange-text"><u><?php echo e(__('translations.Register')); ?></u></a></span>
            <br>
            <a href="<?php echo e(route('forgotPassword')); ?>" class="orange-text" style="margin-right: auto"><u><?php echo e(__('translations.Forgot password')); ?></u></a>
        </div>
    </div>
</div>
</body>
</html>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/auth/login.blade.php ENDPATH**/ ?>