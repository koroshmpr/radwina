<div class="row p-2 pb-1 mt-3 mt-lg-0 justify-content-end" id="category-dropdown">
    <?php
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'orderby' => 'name',
        'posts_per_page' => -1,
        'parent' => 0, // Get only parent categories
        'hide_empty' => true,
    ));

    $current_category_id = get_queried_object_id(); // Get the current category ID

    if ($categories) :
        foreach ($categories as $index => $category) :
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $category_id = 'category-' . $category->term_id; // Generate a unique ID for each category
            $category_class = ($category->term_id === $current_category_id) ? 'bg-primary bg-opacity-10' : ''; // Add class for the current category

            // Check if the current category is a child of this category
            $children_ids = get_term_children($category->term_id, 'product_cat');
            $is_current_or_child = in_array($current_category_id, $children_ids) || $category->term_id === $current_category_id;
            ?>
            <div class="accordion-item <?= $category_class; ?> d-flex align-items-center">
                <a href="<?= esc_url(get_term_link($category, $category->taxonomy)); ?>"
                   class="border-0 col d-flex justify-content-between align-items-center p-3 border border-1 overflow-hidden rounded-4">
                    <h6 class="category-title fw-bold mb-0 fs-6"><?= $category->name; ?></h6>
                    <p class="mb-0 text-primary small fw-bold pe-2 ms-auto">
                        <?= $category->count; ?>
                        <span class="ps-1">کالا</span>
                    </p>
                </a>
                <svg type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index; ?>"
                     aria-expanded="true" aria-controls="collapse<?= $index; ?>" width="20" height="20"
                     fill="currentColor" class="bi bi-plus-lg mx-2 text-primary " viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                          d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
                </svg>
            </div>
            <?php
            // Get child categories
            $children = get_terms(array(
                'taxonomy' => 'product_cat',
                'orderby' => 'name',
                'parent' => $category->term_id, // Get child categories for the current parent
                'hide_empty' => false,
            ));

            if ($children) : ?>
                <div id="collapse<?= $index; ?>" class="accordion-collapse collapse <?= $is_current_or_child ? 'show' : '';
                echo is_shop() && $index == 1 ? 'show' : ''; ?>" data-bs-parent="#category-dropdown">
                <?php
                foreach ($children as $subcat) {
                    $thumbnail_id = get_term_meta($subcat->term_id, 'thumbnail_id', true);
                    $subcat_id = 'subcat-' . $subcat->term_id; // Generate a unique ID for each subcategory
                    $subcat_class = ($subcat->term_id === $current_category_id) ? 'bg-primary bg-opacity-10' : 'bg-info bg-opacity-25'; // Add class for the current subcategory
                    ?>

                    <a href="<?= esc_url(get_term_link($subcat, $subcat->taxonomy)); ?>"
                       class="my-1 col-12 <?= $subcat_class; ?> border-info d-flex justify-content-between align-items-center p-3 border border-1 overflow-hidden rounded-4">
                        <h6 class="category-title fw-bold mb-0 fs-6"><?= $subcat->name; ?></h6>
                        <p class="mb-0 text-primary small fw-bold pe-2 ms-auto">
                            <?= $subcat->count; ?>
                            <span class="ps-1">کالا</span>
                        </p>
                    </a>

                    <?php
                    wp_reset_postdata(); // Reset Query for child categories
                }
            endif; ?>
            </div>
        <?php endforeach;
    endif;
    ?>
</div>