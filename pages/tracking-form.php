<ol class="tracking-list">
    <?php foreach($parcel_links as $id => $track) : ?>
    <li>
        <a href="<?php echo $track['url']; ?>">
            <?php echo $track['company']; ?>
        </a>
        - <a class="remove" href="#" onclick="remove_tracking_parcel('<?php echo $id; ?>', this)">
            <?php _e('Remove', 'woocommerce-orders-parcel'); ?>
        </a>
    </li>
    <?php endforeach; ?>
</ol>
<input type="hidden" name="tracking_links_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">
<input type="hidden" name="order_id" value="<?php echo $post->ID; ?>" />
<label>
    <?php _e('Freight Company', 'woocommerce-orders-parcel'); ?>:
    <input type="text" style="width:250px;" name="shipping_company" value="<?php _e('Track Parcel', 'woocommerce-orders-parcel'); ?>">
</label>
<label>
    <?php _e('Link', 'woocommerce-orders-parcel'); ?>:
    <input type="text" style="width:250px;" name="tracking_link">
</label>
<label>
    <?php _e('Email Customer?', 'woocommerce-orders-parcel'); ?>
    <input type="checkbox" name="notify" checked/>
</label>
<input type="button" class="button" name="tracking_submit" value="<?php _e('Dispatch', 'woocommerce-orders-parcel'); ?>" />
<div id="parcel_overlay" style="display: none;">
    <div id="parcel_preloader">
        <img src="<?php echo $plugin_dir; ?>img/preloader.gif" />
        <br>
        Processing...
    </div>
</div>