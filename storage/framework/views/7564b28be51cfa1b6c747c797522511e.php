<a href="/<?php echo e($good->id); ?>">
    <div class="card good-card hoverable z-depth-5">
        <div class="card-image">
            <?php if($good->attachment()?->first()?->url()): ?>
                <img loading="lazy" src="<?php echo e($good->attachment()?->first()?->url()); ?>" class="card-presenter-image">
            <?php else: ?>
                <img loading="lazy" src="<?php echo e(asset('img/no-image.jpg')); ?>" class="card-presenter-image">
            <?php endif; ?>
            <?php if(auth()->guard('clients')->check()): ?>
                <?php if(in_array($good->id, App\Models\Client::query()->find(Auth::guard('clients')->id())->favorites()->pluck('good_id')->toArray())): ?>
                    <a class="btn-floating remove-from-favorites-btn btn-large halfway-fab waves-effect waves-light orange darken-4" data-product-id="<?php echo e($good->id); ?>">
                        <i class="large material-icons">
                            favorite
                        </i>
                    </a>
                <?php else: ?>
                    <a class="btn-floating add-to-favorites-btn btn-large halfway-fab waves-effect waves-light orange darken-4" data-product-id="<?php echo e($good->id); ?>">
                        <i class="large material-icons">
                            favorite_border
                        </i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->guard('clients')->guest()): ?>
                <a class="btn-floating add-to-favorites-btn btn-large halfway-fab waves-effect waves-light orange darken-4 modal-trigger"
                   href="#auth-modal">
                    <i class="large material-icons">
                        favorite_border
                    </i>
                </a>
            <?php endif; ?>
            <a class="btn-floating add-to-cart-btn btn-large halfway-fab waves-effect waves-light orange darken-4"
               data-product-id="<?php echo e($good->id); ?>">
                <i class="large material-icons">add_shopping_cart</i>
            </a>
        </div>
        <a href="<?php echo e(route('viewGood', $good)); ?>">
        <div class="card-content">
            <span class="card-title black-text">
                <?php echo e($good['name_' . session()->get('locale', 'ru')]); ?>

            </span>
            <?php if($good->discount_cost): ?>
                <span class="cost-label">
                    <span class="chip small">
                        <s><?php echo e($good->cost); ?></s>
                    </span>
                    <span class="chip red white-text large">
                        <b><?php echo e($good->discount_cost); ?></b>
                    </span>
                <?php echo e(__('translations.Tenge per day')); ?>

                </span>
            <?php else: ?>
                <span class="cost-label black-text">
                    <span class="chip">
                        <b><?php echo e($good->cost); ?></b>
                    </span>
                <?php echo e(__('translations.Tenge per day')); ?>

                </span>
            <?php endif; ?>
            <?php if($good->discount_cost): ?>
                <a class="btn-floating discount-btn waves-effect waves-light red darken-4">
                    <i class="medium material-icons white-text">money_off</i>
                </a>
            <?php endif; ?>
        </div>
        </a>
    </div>
</a>
<?php /**PATH /Users/shynaliotep/works/tech-shop/resources/views/goodCard.blade.php ENDPATH**/ ?>