<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper grey darken-3">
            <div class="nav-inner-wrapper">
                <a href="#" data-target="slide-out" class="sidenav-trigger hide-on-med-and-up"><i
                        class="material-icons">menu</i></a>
                <div class="brand-logo">
                    <div class="search-wrapper valign-wrapper hide-on-small-only input-field">
                        <input id="search" type="text"
                               class="validate browser-default text-white center-align autocomplete"
                               placeholder="<?php echo e(__('translations.Search')); ?>">
                        <i class="material-icons">
                            search
                        </i>
                    </div>
                </div>
                <ul class="right nav-buttons">
                    <li class="nav-element center">
                        <a href="<?php echo e(route('cart')); ?>" class="nav-link cart-link">
                            <i class="material-icons left navbar-icon">
                                shopping_cart
                            </i>
                            <span class="hide-on-med-and-down">
                                <?php echo e(__('translations.Cart')); ?>

                            </span>
                            <?php if(isset($cartCount)): ?>
                                <span class="cart-counter-badge badge red white-text">
                                    <span class="in-cart-item-counter">
                                        <?php echo e($cartCount); ?>

                                    </span>
                            </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if(auth()->guard('clients')->check()): ?>
                        <li class="nav-element center">
                            <a href="<?php echo e(route('getFavorites')); ?>" class="nav-link white-text">
                                <i class="material-icons left navbar-icon">
                                    favorite_border
                                </i>
                                <span class="hide-on-med-and-down">
                                <?php echo e(__('translations.Favorites')); ?>

                            </span>
                            </a>
                        </li>
                        <li class="nav-element center">
                            <a href="#" class="nav-link dropdown-trigger white-text" data-target="profile-options">
                                <i class="material-icons left navbar-icon">
                                    account_circle
                                </i>
                                <span class="hide-on-med-and-down">
                                <?php echo e(__('translations.Profile')); ?>

                            </span>
                            </a>

                            <ul id='profile-options' class='dropdown-content main-color white-text'>
                                <li><a href="<?php echo e(route('getMyOrders')); ?>" class="profile-dropdown-link white-text"><?php echo e(__('translations.My orders')); ?></a></li>
                                <li><a href="<?php echo e(route('viewProfile')); ?>" class="profile-dropdown-link white-text"><?php echo e(__('translations.Check profile')); ?></a></li>
                                <li class="divider" tabindex="-1"></li>
                                <li><a href="<?php echo e(route('logout')); ?>" class="white-text profile-dropdown-link"><i
                                            class="material-icons orange-text">cancel</i><?php echo e(__('translations.Logout')); ?></a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if(auth()->guard('clients')->guest()): ?>
                        <li class="nav-element center">
                            <a href="<?php echo e(route('login')); ?>" class="nav-link orange darken-4 auth-link z-depth-3">
                                <?php echo e(__('translations.Log in')); ?>

                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <?php $__env->startPush('scripts'); ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var elems = document.querySelectorAll('.autocomplete');
                    var data = {
                        <?php $__currentLoopData = $goodOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goodOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        "<?php echo e($goodOption['name']); ?>": "<?php echo e($goodOption['url']); ?>",
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    };
                    var instances = M.Autocomplete.init(elems, {
                        data: data,
                        limit: 5,
                        onAutocomplete: (item) => {
                            window.location.href = '/autofill/' + item
                        }
                    });
                });
            </script>
        <?php $__env->stopPush(); ?>
    </nav>
</div>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/navigation/navbar.blade.php ENDPATH**/ ?>