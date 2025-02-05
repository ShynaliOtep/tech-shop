<ul id="slide-out" class="sidenav sidenav-fixed main-color">
    <div class="logo-wrapper center">
        <a href="/"><img src="<?php echo e(asset('img/logo.jpg')); ?>" class="logo"/></a>
    </div>
    <div class="container">
        <hr>
        <li class="menu-item center">
            <div class="language-wrapper">
                <a href="<?php echo e(route('changeLang', 'en')); ?>" class="btn language-btn white-text <?php if(session()->get('locale') === 'en'): ?> orange darken-4 <?php else: ?> grey darken-2 <?php endif; ?>"><?php echo e(__('translations.EN')); ?></a>
                <a href="<?php echo e(route('changeLang', 'ru')); ?>" class="btn language-btn white-text <?php if(session()->get('locale') === 'ru'): ?> orange darken-4 <?php else: ?> grey darken-2 <?php endif; ?>"><?php echo e(__('translations.RU')); ?></a>
            </div>
            <hr>
            <div class="hide-on-med-and-up search-wrapper valign-wrapper hide-on-med-and-up input-field">
                <input id="search" type="text"
                       class="validate browser-default text-white center-align autocomplete"
                       placeholder="<?php echo e(__('translations.Search')); ?>">
                <i class="material-icons white-text">
                    search
                </i>
            </div>
            <hr class="hide-on-med-and-up">
        </li>
        <?php $__currentLoopData = $goodTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goodType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="menu-item">
                <a href="<?php echo e(route('goodList', $goodType->code, false)); ?>"
                   class="white-text waves-effect waves-light menu-item-link waves-ripple">
                    <span class="menu-item-content">
                        <?php if(Request::is('category/' . $goodType->code)): ?>
                            <span class="btn-medium btn-floating orange darken-4">
                                <i class="material-icons"><?php echo e($goodType->icon); ?></i>
                            </span>
                            <span class="orange-text"><?php echo e(__( 'translations.'. $goodType->code)); ?></span>
                        <?php else: ?>
                            <span class="btn-medium btn-floating grey darken-4">
                                <i class="material-icons"><?php echo e($goodType->icon); ?></i>
                            </span>
                            <span class=""><?php echo e(__( 'translations.'. $goodType->code)); ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</ul>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/navigation/sidemenu.blade.php ENDPATH**/ ?>