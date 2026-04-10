<?php
/**
 * Expects: $shop_variation_list (array), $home_pick_query (string)
 *
 * Optional:
 * - $color_variation_band_featured (bool) — "Variation" heading + featured-style product row (paired flow)
 */
if (empty($shop_variation_list)) {
    return;
}
$band_featured = !empty($color_variation_band_featured);
?>
<section id="color-variation" class="<?php echo $band_featured ? 'my-5 pb-5 variation-featured-band' : 'my-5 pb5'; ?>">
    <?php if ($band_featured): ?>
    <div class="container text-center mt-4 py-3">
        <h3>Variation</h3>
        <hr>
        <p>Here you can check out more from our shop</p>
    </div>
    <div class="row mx-auto container-fluid">
        <?php foreach ($shop_variation_list as $sv) { ?>
        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($sv['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="p-price">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <h5 class="p-name"><?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
            <h4 class="p-price">Pkr: <?php echo htmlspecialchars((string) $sv['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <a href="<?php echo 'single_product.php?product_id=' . (int) $sv['product_id'] . $home_pick_query; ?>">
                <button type="button" class="buy-btn">Buy now</button>
            </a>
        </div>
        <?php } ?>
    </div>
    <?php else: ?>
    <div class="container text-center mt-5 py-5">
        <h3>Color variation</h3>
        <hr>
        <p>More picks from our shop collection</p>
    </div>
    <div class="row mx-auto container-fluid">
        <?php foreach ($shop_variation_list as $sv) { ?>
        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <a href="<?php echo 'single_product.php?product_id=' . (int) $sv['product_id'] . $home_pick_query; ?>" class="text-decoration-none text-dark d-block">
                <img class="img-fluid mb-3" src="./<?php echo htmlspecialchars(product_image_url($sv['product_image']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?>">
                <h5 class="p-name"><?php echo htmlspecialchars($sv['product_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                <h4 class="p-price">Pkr: <?php echo htmlspecialchars((string) $sv['product_price'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <span class="buy-btn d-inline-block">View</span>
            </a>
        </div>
        <?php } ?>
    </div>
    <?php endif; ?>
</section>
