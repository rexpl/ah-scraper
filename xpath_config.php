<?php

return [


    /**
     * Get all categories.
     * 
     * @var string
     */
    'all_categories' => '//*[@id="start-of-content"]/div[1]/div/div/div/div/div/div/a',


    /**
     * Get the product out in the procut list.
     * 
     * @var string
     */
    'category_product_list' => '/html/body/div[3]/div[2]/div[2]/div[2]/div/article/div[2]/div[2]/a',


    /**
     * Get the possible result count.
     * 
     * @var string
     */
    'category_result_count' => '//*[@id="start-of-content"]/div[3]/span',


    /**
     * If the product is avialble online.
     * 
     * @var string
     */
    'product_available' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/p/span',


    /**
     * If the product is marked as "nieuw".
     * 
     * @var string
     */
    'product_new' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[2]/span',
    'product_size' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[1]/div/text()',


    /**
     * The product name.
     * 
     * @var string
     */
    'product_name' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[1]/h1/span',


    /**
     * The product price if not new.
     * 
     * @var string
     */
    'product_price' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[2]/div[1]/div',
    'product_price_before' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[2]/div[1]/div[1]/div',


    /**
     * Price if the product if it is new.
     * 
     * @var string
     */
    'product_price_new' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[3]/div[1]/div',


    /**
     * Price if the product is in promotion.
     * 
     * @var string
     */
    'product_price_promo' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[2]/div[2]/div[1]/div[2]',


    /**
     * Sticker "2e Gratis" on image.
     * 
     * @var string
     */
    'product_second_free' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[1]/div/div',


    /**
     * The product brand.
     * 
     * @var string
     */
    'product_brand' => '//*[@id="app"]/main/div/div/div/div[2]/a',


    /**
     * The product image url.
     * 
     * @var string
     */
    'product_image' => '//*[@id="start-of-content"]/div[1]/div/div/div/article/div/div/div[1]/div/figure/div/img',


];