<div id="auth-modal" class="modal">
    <div class="modal-content container center">
        <h1 class="btn-floating btn-large orange darken-4"><i
                class="large material-icons text-accent-4 white-text "><?php echo e($icon); ?></i></h1>
        <h4><?php echo e($title); ?></h4>
        <p><?php echo e($content); ?></p>
    </div>
    <div class="divider"></div>
    <div class="modal-footer">
        <div class="row">
            <div class="col s12 center">
                <a href="<?php echo e(route('login')); ?>" class="btn-large nav-link orange darken-4 auth-link ">
                    <?php echo e(__('translations.Log in')); ?>

                </a>
            </div>
            <div class="col s12 center">
                <a href="<?php echo e(route('register')); ?>" class="btn-large modal-btn grey black white-text register-link"><?php echo e(__('translations.Register')); ?></a>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/auth/modal.blade.php ENDPATH**/ ?>