<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_cart' ); ?>

<section class="py-5">
    <div class="container-xl">

        <div class="row g-4">
            <div class="col-lg-8">

                <?php do_action( 'woocommerce_before_cart' ); ?>

                <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
                    <?php do_action( 'woocommerce_before_cart_table' ); ?>

                    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="product-thumbnail">&nbsp;</th>
                            <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                            <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                            <th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
                            <th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
                            <th class="product-remove">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

                        <?php
                        $allowed_category_terms = get_field('cat_step', 'option');
                        $allowed_category_ids = array();

                        if ($allowed_category_terms && is_array($allowed_category_terms)) {
                            // Loop through the category terms
                            foreach ($allowed_category_terms as $term) {
                                // Check if the term object has a term_id property
                                if (property_exists($term, 'term_id')) {
                                    // Add the term ID to the array
                                    $allowed_category_ids[] = $term->term_id;
                                }
                            }
                        }
                        $stepSize = get_field('cat_step_number', 'option');
                        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                            $product_id = $cart_item['product_id'];
                            $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));

                            // Check if there is an intersection between allowed and product categories
                            $minValue = count(array_intersect($allowed_category_ids, $product_categories)) > 0 ? $stepSize : 1;
                            $titleAllow = count(array_intersect($allowed_category_ids, $product_categories)) > 0 ? 'متر' : '';

                            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                                ?>
                                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                                    <td class="product-thumbnail">
                                        <?php
                                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                                        if ( ! $product_permalink ) {
                                            echo $thumbnail; // PHPCS: XSS ok.
                                        } else {
                                            printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
                                        }
                                        ?>
                                    </td>

                                    <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
                                        <?php
                                        if ( ! $product_permalink ) {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                                        } else {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a class="text-dark" href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                                        }

                                        do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                                        // Meta data.
                                        echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

                                        // Backorder notification.
                                        if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
                                        }
                                        ?>
                                    </td>

                                    <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
                                        <?php
                                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                        ?>
                                    </td>

                                    <td class="product-quantity d-flex align-items-center" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
                                        <?php
                                        if ( $_product->is_sold_individually() ) {
                                            $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                                        } else {
                                            $product_quantity = woocommerce_quantity_input(
                                                array(
                                                    'input_name'   => "cart[{$cart_item_key}][qty]",
                                                    'input_value'  => $cart_item['quantity'],
                                                    'max_value'    => $_product->get_max_purchase_quantity(),
                                                    'min_value'    => $minValue,
                                                    'product_name' => $_product->get_name(),
                                                    'input_id'     => 'cart_quantity_input', // Specify the ID of your quantity input field
                                                    'step' => $minValue
                                                ),
                                                $_product,
                                                false
                                            );
                                        }

                                        echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
                                        ?>
                                        <span class="ms-2 fs-6"><?= $titleAllow; ?></span>
                                    </td>

                                    <td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
                                        <?php
                                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
                                        ?>
                                    </td>

                                    <td class="product-remove">
                                        <?php
                                        echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                            'woocommerce_cart_item_remove_link',
                                            sprintf(
                                                '<a href="%s" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="bi bi-trash fs-5 text-primary"></i></a>',
                                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                                esc_html__( 'Remove this item', 'woocommerce' ),
                                                esc_attr( $product_id ),
                                                esc_attr( $_product->get_sku() )
                                            ),
                                            $cart_item_key
                                        );
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>

                        <tr>
                            <td colspan="6" class="actions">

                                <?php if ( wc_coupons_enabled() ) { ?>
                                    <div class="coupon d-flex">
                                        <label for="coupon_code"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="form-control bg-white" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" />
                                        <button type="submit" class="button bg-primary text-white ms-3 rounded"
                                                name="apply_coupon"
                                                value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?></button>
                                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="button bg-primary text-white rounded mt-2"
                                        name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

                                <?php do_action( 'woocommerce_cart_actions' ); ?>

                                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                            </td>
                        </tr>

                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                        </tbody>
                    </table>

                    <?php do_action( 'woocommerce_after_cart_table' ); ?>
                </form>
            </div>


            <div class="col-lg-4">

                <div class="card bg-white">
                    <div class="card-body vstack gap-4">
                        <p class="fs-4 m-0">مجموع سبد خرید</p>

                        <div class="fs-5 fw-light small-sm-down">
                            <div class="hstack justify-content-between">
                                <span>مجموع خرید</span>
                                <span class="text-end"><?php wc_cart_totals_subtotal_html(); ?></span>

                            </div>

                            <hr>
                            <div class="hstack justify-content-between">
                                <span>هزینه ارسال</span>
                                <span class="text-end"><?php wc_cart_totals_shipping_html(); ?></span>
                            </div>

                            <hr>

                            <div class="hstack justify-content-between">
                                <span class="fw-bold">جمع کل</span>
                                <span class="text-end"><?php wc_cart_totals_order_total_html(); ?></span>
                            </div>
                        </div>

                        <div>
                            <label for="discountFormInput" class="form-label fs-4 fw-normal small-sm-down">کد تخفیف دارید؟</label>
                            <input type="text" placeholder="کد تخفیف خود را وارد کنید" class="form-control bg-transparent" id="discountFormInput">
                        </div>

                        <div>
                            <a class="btn btn-primary btn-lg w-100" href="/checkout">ثبت سفارش</a>
                        </div>
                    </div>
                </div>

                <?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
                <?php do_action( 'woocommerce_after_cart' ); ?>
            </div>
        </div>
    </div>
</section>
